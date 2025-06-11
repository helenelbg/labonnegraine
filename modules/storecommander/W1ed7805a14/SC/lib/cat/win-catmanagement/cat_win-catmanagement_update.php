<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_category = (Tools::getValue('gr_id', 0));

    $id_shop = Tools::getValue('id_shop', 0);
    $in_all_shops = Tools::getValue('in_all_shops', 0);

    $action = Tools::getValue('action', '');

    $field = Tools::getValue('field', '');
    $value = Tools::getValue('value', '');

    $id_parent = (int) Tools::getValue('id_parent', 0);
    $name = Tools::getValue('name', 'new');
//    $position = Tools::getValue('position', '0');

    $id_categories = (Tools::getValue('id_categories', 0));
    $id_bin = (int) Tools::getValue('id_bin', '0');

    /*
     * FUNCTIONS
     */

    function categoryChildren(&$array, $id_category)
    {
        if (!is_array($array) || !$id_category)
        {
            return false;
        }
        $result = Db::getInstance()->executeS('
        SELECT `id_category`
        FROM `'._DB_PREFIX_.'category`
        WHERE `id_parent` = '.(int) $id_category);
        foreach ($result as $row)
        {
            $array[] = (int) $row['id_category'];
        }
    }

    function SCMSdeleteCategory($id_category, $binCategory)
    {
        $category = new Category($id_category);
        if (Validate::isLoadedObject($category) && !$category->isRootCategoryForAShop())
        {
            if ((int) $category->id === 0 || (int) $category->id === 1)
            {
                return false;
            }

            $children = array();
            categoryChildren($children, $category->id);
            foreach ($children as $id_cat)
            {
                $cat = new Category($id_cat);
                if ($cat->isRootCategoryForAShop())
                {
                    continue;
                }
                SCMSdeleteCategory($cat->id, $binCategory);
            }
            if ($id_category != $binCategory)
            {
                $category->id_shop_list = $category->getAssociatedShops();
                $category->deleteLite();
                $category->deleteImage(true);
                $category->cleanGroups();
                $category->cleanAssoProducts();
                // Delete associated restrictions on cart rules
                CartRule::cleanProductRuleIntegrity('categories', array($category->id));
                SCMSCleanPositionsInAllShops($category->id_parent);
                /* Delete Categories in GroupReduction */
                if (GroupReduction::getGroupsReductionByCategoryId((int) $category->id))
                {
                    GroupReduction::deleteCategory($category->id);
                }
                Hook::exec('actionCategoryDelete', array('category' => $category));
            }
            else
            {
                Category::regenerateEntireNtree();
            }
        }
    }

    $duplicated_ids = array();
    function duplicateCategories($id_category, $id_parent)
    {
        global $duplicated_ids;
        if (empty($id_category) || empty($id_parent))
        {
            return false;
        }

        $category_parent = new Category($id_parent);
        $last_position = (int) (Db::getInstance()->getValue('
                    SELECT MAX(`position`)
                    FROM `'._DB_PREFIX_.'category`
                    WHERE `id_parent` = '.(int) $id_parent) + 1);

        // INSERT IN ps_category
        $result = Db::getInstance()->executeS('
                    SELECT *
                    FROM `'._DB_PREFIX_.'category`
                    WHERE `id_category` = '.(int) $id_category);
        if (!empty($result[0]['id_category']))
        {
            $fields = $result[0];
            unset($fields['id_category']);
            unset($fields['nleft']);
            unset($fields['nright']);
            if (isset($fields['level_depth']))
            {
                $fields['level_depth'] = $category_parent->level_depth + 1;
            }
            if (isset($fields['date_add']))
            {
                $fields['date_add'] = date('Y-m-d H:i:s');
            }
            if (isset($fields['date_upd']))
            {
                $fields['date_upd'] = date('Y-m-d H:i:s');
            }
            if (isset($fields['id_parent']))
            {
                $fields['id_parent'] = $id_parent;
            }
            if (isset($fields['position']))
            {
                $fields['position'] = $last_position;
            }

            $names = array();
            $values = array();
            foreach ($fields as $key => $val)
            {
                $names[] = $key;
                $values[] = $val;
            }

            $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category` (`'.bqSQL(implode('`,`', $names)).'`)
                                VALUES ("'.implode('","', $values).'")';
            Db::getInstance()->execute($sql_insert);
            $new_id_cat = Db::getInstance()->Insert_ID();

            if (!empty($new_id_cat))
            {
                $duplicated_ids[$new_id_cat] = $new_id_cat;
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    // INSERT IN ps_category_shop
                    $results = Db::getInstance()->executeS('
                                SELECT *
                                FROM `'._DB_PREFIX_.'category_shop`
                                WHERE `id_category` = '.(int) $id_category);
                    foreach ($results as $result)
                    {
                        $fields = $result;
                        $fields['id_category'] = (int) $new_id_cat;
                        $fields['position'] = Category::getLastPosition($id_parent, (int) $fields['id_shop']);

                        $names = array();
                        $values = array();
                        foreach ($fields as $key => $val)
                        {
                            $names[] = $key;
                            $values[] = $val;
                        }

                        $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category_shop` (`'.bqSQL(implode('`,`', $names)).'`)
                                    VALUES ("'.implode('","', $values).'")';
                        Db::getInstance()->execute($sql_insert);
                    }
                }

                // INSERT IN ps_category_lang
                $results = Db::getInstance()->executeS('
                            SELECT *
                            FROM `'._DB_PREFIX_.'category_lang`
                            WHERE `id_category` = '.(int) $id_category);
                foreach ($results as $result)
                {
                    $fields = $result;
                    $fields['id_category'] = (int) $new_id_cat;

                    $names = array();
                    $values = array();
                    foreach ($fields as $key => $val)
                    {
                        $names[] = $key;
                        switch ($key){
                            case 'name':
                            case 'meta_title':
                            case 'meta_description':
                                $val = pSQL($val);
                                break;
                            case 'description':
                                $val = pSQL($val, true);
                                break;
                        }
                        $values[] = $val;
                    }

                    $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category_lang` (`'.bqSQL(implode('`,`', $names)).'`)
                                VALUES ("'.implode('","', $values).'")';
                    Db::getInstance()->execute($sql_insert);
                }

                // INSERT IN ps_category_group
                $results = Db::getInstance()->executeS('
                            SELECT *
                            FROM `'._DB_PREFIX_.'category_group`
                            WHERE `id_category` = '.(int) $id_category);
                foreach ($results as $result)
                {
                    $fields = $result;
                    $fields['id_category'] = (int) $new_id_cat;

                    $names = array();
                    $values = array();
                    foreach ($fields as $key => $val)
                    {
                        $names[] = $key;
                        $values[] = $val;
                    }

                    $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category_group` (`'.bqSQL(implode('`,`', $names)).'`)
                                VALUES ("'.implode('","', $values).'")';
                    Db::getInstance()->execute($sql_insert);
                }

                // ADD IMAGE
                $actual_image_name = _PS_CAT_IMG_DIR_.(int) $id_category.'.jpg';
                $new_image_name = _PS_CAT_IMG_DIR_.(int) $new_id_cat.'.jpg';
                if (file_exists($actual_image_name))
                {
                    if (copy($actual_image_name, $new_image_name))
                    {
                        $images_types = ImageType::getImagesTypes('categories');
                        foreach ($images_types as $k => $image_type)
                        {
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                ImageManager::resize(
                                    $new_image_name,
                                    _PS_CAT_IMG_DIR_.$new_id_cat.'-'.stripslashes($image_type['name']).'.jpg',
                                    (int) $image_type['width'], (int) $image_type['height']
                                );
                            }
                            else
                            {
                                imageResize($new_image_name, _PS_CAT_IMG_DIR_.$new_id_cat.'-'.stripslashes($image_type['name']).'.jpg', (int) ($image_type['width']), (int) ($image_type['height']));
                            }
                        }
                    }
                }

                // GET CHILDREN
                $result = Db::getInstance()->executeS('
                            SELECT `id_category`
                            FROM `'._DB_PREFIX_.'category`
                            WHERE `id_parent` = '.(int) $id_category);
                foreach ($result as $row)
                {
                    if (empty($duplicated_ids[$row['id_category']]))
                    {
                        duplicateCategories((int) $row['id_category'], (int) $new_id_cat);
                    }
                }
            }
        }
    }

    /*
     * ACTION
     */
    if (!empty($action) && $action == 'insert' && !empty($name))
    {
        $position = 0;
        $last_position = Db::getInstance()->executeS('
        SELECT MAX(c.`position`) as position
        FROM `'._DB_PREFIX_.'category` c
        WHERE c.`id_parent` = '.(int) $id_parent);
        if (!empty($last_position[0]['position']))
        {
            $position = $last_position[0]['position'] + 1;
        }

        $name = str_replace('"', "'", $name);
        $newcategory = new Category();
        $newcategory->id_parent = $id_parent;
        $newcategory->level_depth = $newcategory->calcLevelDepth();
        $newcategory->position = $position;
        $newcategory->active = 0;
        $id_shop = (int) Tools::getValue('id_shop');
        if (SCMS && SCI::getSelectedShopActionList())
        {
            $newcategory->id_shop_list = array($id_shop);
            $newcategory->id_shop_default = $id_shop;
            $_POST['checkBoxShopAsso_category'] = array();
            foreach ($newcategory->id_shop_list as $id)
            {
                $_POST['checkBoxShopAsso_category'][$id] = $id;
            }
        }
        foreach ($languages as $lang)
        {
            $newcategory->link_rewrite[$lang['id_lang']] = link_rewrite($name, $lang['iso_code']);
            $newcategory->name[$lang['id_lang']] = $name;
        }
        $newcategory->add();
        if (!sc_in_array(1, $newcategory->getGroups(), 'catWinCatManaUpdate_groups_'.$newcategory->id))
        {
            $newcategory->addGroups(array(1));
        }
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            if (SCMS)
            {
                Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'category_shop`
                WHERE `id_category` = '.(int) $newcategory->id);
            }
            $shops = Category::getShopsByCategory((int) $id_parent);
            foreach ($shops as $shop)
            {
                $position = Category::getLastPosition((int) $id_parent, $shop['id_shop']);
                if (!$position)
                {
                    $position = 1;
                }
                $newcategory->addPosition($position, $shop['id_shop']);
            }
        }
        SCMSCleanPositionsInAllShops($id_parent);
        fixLevelDepth();
        echo $newcategory->id;
    }
    if (!empty($action) && $action == 'update' && !empty($field))
    {
        if ($field == 'name' && !empty($value))
        {
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (empty($id_shop) || !empty($in_all_shops)))
            {
                $shops = Shop::getShops(false);
                foreach ($shops as $shop)
                {
                    $insert = false;

                    $exist = 'SELECT id_category FROM '._DB_PREFIX_."category_lang WHERE id_category=" .(int) $id_category . " AND id_lang=" .(int) $id_lang . " AND id_shop=" .(int) $shop['id_shop'];
                    $exist = Db::getInstance()->ExecuteS($exist);
                    if (empty($exist[0]['id_category']))
                    {
                        $insert = true;
                    }

                    if (!$insert)
                    {
                        $url_rewrite = '';
                        if (_s('CAT_SEO_CAT_NAME_TO_URL'))
                        {
                            $url_rewrite = ", `link_rewrite`='".pSQL(link_rewrite($value, Language::getIsoById($id_lang)))."'";
                        }
                        $sql = 'UPDATE '._DB_PREFIX_."category_lang SET name='".pSQL($value)."' ".$url_rewrite." WHERE id_category=" .(int) $id_category . " AND id_lang=" .(int) $id_lang . " AND id_shop=" .(int) $shop['id_shop'];
                        Db::getInstance()->Execute($sql);
                    }
                    else
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category, id_shop,id_lang,name,link_rewrite)
                                        VALUES ('".(int) $id_category."','".(int) $shop['id_shop']."','".(int) $id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value, Language::getIsoById($id_lang)))."')";
                        Db::getInstance()->Execute($sql);
                    }
                }
            }
            else
            {
                $insert = false;
                $exist = 'SELECT id_category FROM '._DB_PREFIX_."category_lang WHERE id_category=" .(int) $id_category . " AND id_lang=" .(int) $id_lang;
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
                {
                    $exist .= " AND id_shop=" .(int) $id_shop;
                }
                $exist = Db::getInstance()->ExecuteS($exist);
                if (empty($exist[0]['id_category']))
                {
                    $insert = true;
                }

                if (!$insert)
                {
                    $url_rewrite = '';
                    if (_s('CAT_SEO_CAT_NAME_TO_URL'))
                    {
                        $url_rewrite = ", `link_rewrite`='".pSQL(link_rewrite($value, Language::getIsoById($id_lang)))."'";
                    }

                    $sql = 'UPDATE '._DB_PREFIX_."category_lang SET name='".pSQL($value)."' ".$url_rewrite." WHERE id_category=" .(int) $id_category . " AND id_lang=" .(int) $id_lang;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
                    {
                        $sql .= " AND id_shop=" .(int) $id_shop;
                    }
                    Db::getInstance()->Execute($sql);
                }
                else
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category, id_shop,id_lang,name,link_rewrite)
                                VALUES ('".(int) $id_category."','".(int) $id_shop."','".(int) $id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value, Language::getIsoById($id_lang)))."')";
                        Db::getInstance()->Execute($sql);
                    }
                    else
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_."category_lang (id_category,id_lang,name,link_rewrite)
                                VALUES ('".(int) $id_category."','".(int) $id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value, Language::getIsoById($id_lang)))."')";
                        Db::getInstance()->Execute($sql);
                    }
                }
            }
            echo $id_category;
            exit;
        }

        if ($field == 'active')
        {
            if(!$value){
                $value = '1-c.active';
            } else {
                $value = (int) $value;
            }
            $sql = 'UPDATE '._DB_PREFIX_.'category c LEFT JOIN '._DB_PREFIX_.'category_lang cl ON c.id_category = cl.id_category SET c.active='.$value.' WHERE c.id_category IN (' .pInSQL($id_category).') AND cl.name != \'SC Recycle Bin\'';
            Db::getInstance()->Execute($sql);
            echo $id_category;
            exit;
        }

        if ($field == 'position' && !empty($id_category))
        {
            $category = new CategoryCore($id_category);
            $category->addPosition($value, (int) $id_shop);
            $category->save();
            addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $id_category, $id_lang, _DB_PREFIX_.'category', $value, '', (int) $id_shop);
            SCI::hookExec('categoryUpdate', array('category' => $category));
            SCMSCleanPositionsInAllShops($category->id_parent);
        }

        // PM Cache
        if (!empty($id_category))
        {
            ExtensionPMCM::clearFromIdsCategory($id_category);
        }

        SC_Ext::readCustomCategoriesGridConfigXML('onAfterUpdateSQL');
    }
    if (!empty($action) && $action == 'move')
    {
        $idCateg = (int) Tools::getValue('idCateg');
        $idNewParent = (int) Tools::getValue('idNewParent', 0);
        $idNextBrother = (int) Tools::getValue('idNextBrother');
        if ($idCateg != 0 && $idNewParent != 0)
        {
            $current_categ_id_parent = Db::getInstance()->getValue('SELECT id_parent FROM '._DB_PREFIX_.'category WHERE id_category = '.(int) $idCateg);
            if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
            {
                $k = $newpos = 0;
                $done = false;
                $todo = array();
                $sql = 'SELECT c.id_category, c.id_parent, c.position FROM'._DB_PREFIX_."category c
                                WHERE c.id_parent=" .(int) $idNewParent . "
                                ORDER BY c.position";
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    if ($row['id_category'] == $idNextBrother)
                    {
                        $sql2 = 'SELECT c.id_parent,c.position FROM '._DB_PREFIX_."category c WHERE c.id_category=" .(int) $idCateg;
                        $categInfo = Db::getInstance()->getRow($sql2);
                        $todo[] = 'UPDATE '._DB_PREFIX_.'category SET id_parent='.(int) $idNewParent.',position='.(int) $k.',date_upd=NOW() WHERE id_category='.(int) $idCateg;
                        $done = true;
                        $newpos = $k;
                        ++$k;
                    }
                    if ($row['id_category'] != $idCateg)
                    {
                        $todo[] = 'UPDATE '._DB_PREFIX_.'category SET position='.(int) $k.($done ? ',date_upd=NOW()' : '').' WHERE id_category='.(int) $row['id_category'];
                    }
                    ++$k;
                }
                addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $idCateg, $id_lang, _DB_PREFIX_.'category', 'Parent ID:'.(int) $idNewParent.' - Position:'.$newpos, (isset($categInfo) ? 'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int) $newpos : ''));
                if (!$done)
                { // Dnd to the end of a branch
                    $todo[] = 'UPDATE '._DB_PREFIX_."category SET id_parent=" .(int) $idNewParent . ",position=".(int) $k.',date_upd=NOW() WHERE id_category='.(int) $idCateg;
                }
            }
            else
            { // PS 1.5
                if (!empty($id_shop))
                {
                    $k = $newpos = 0;
                    $done = false;
                    $todo = array();
                    $sql = 'SELECT c.id_category, c.id_parent, cs.position FROM '._DB_PREFIX_.'category c
                                    LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop='.(int) $id_shop.")
                                    WHERE c.id_parent=" .(int) $idNewParent . "
                                    ORDER BY cs.position";
                    $res = Db::getInstance()->ExecuteS($sql);
                    foreach ($res as $row)
                    {
                        if ($row['id_category'] == $idNextBrother)
                        {
                            $sql2 = 'SELECT c.id_parent,cs.position
                                             FROM '._DB_PREFIX_.'category c
                                             LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop='.(int) $id_shop.")
                                             WHERE c.id_category='".(int) $idCateg."'";
                            $categInfo = Db::getInstance()->getRow($sql2);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category SET id_parent='.(int) $idNewParent.',date_upd=NOW() WHERE id_category='.(int) $idCateg;
                            if ($in_all_shops)
                            {
                                $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg;
                            }
                            else
                            {
                                $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg.' AND id_shop = '.(int) $id_shop;
                            }
                            $done = true;
                            $newpos = $k;
                            ++$k;
                        }
                        if ($row['id_category'] != $idCateg)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category SET position=position'.($done ? ',date_upd=NOW()' : '').' WHERE id_category='.(int) $row['id_category'];
                            if ($in_all_shops)
                            {
                                $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $row['id_category'];
                            }
                            else
                            {
                                $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $row['id_category'].' AND id_shop = '.(int) $id_shop;
                            }
                        }
                        ++$k;
                    }
                    addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $idCateg, $id_lang, _DB_PREFIX_.'category', 'Parent ID:'.(int) $idNewParent.' - Position:'.$newpos, (isset($categInfo) ? 'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int) $newpos : ''));
                    if (!$done)
                    { // Dnd to the end of a branch
                        $todo[] = 'UPDATE '._DB_PREFIX_."category SET id_parent=" .(int) $idNewParent . ",date_upd=NOW() WHERE id_category=".(int) $idCateg;
                        if ($in_all_shops)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg;
                        }
                        else
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg.' AND id_shop = '.(int) $id_shop;
                        }
                    }
                }
                else
                {
                    $k = $newpos = 0;
                    $done = false;
                    $todo = array();
                    $sql = 'SELECT c.id_category, c.id_parent, c.position FROM '._DB_PREFIX_."category c
                                WHERE c.id_parent=" .(int) $idNewParent . "
                                ORDER BY c.position";
                    $res = Db::getInstance()->ExecuteS($sql);
                    foreach ($res as $row)
                    {
                        if ($row['id_category'] == $idNextBrother)
                        {
                            $sql2 = 'SELECT c.id_parent,c.position
                                         FROM '._DB_PREFIX_."category c
                                         WHERE c.id_category='".(int) $idCateg."'";
                            $categInfo = Db::getInstance()->getRow($sql2);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category SET id_parent='.(int) $idNewParent.',position='.(int) $k.',date_upd=NOW() WHERE id_category='.(int) $idCateg;
                            if ($in_all_shops)
                            {
                                $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg;
                            }
                            $done = true;
                            $newpos = $k;
                            ++$k;
                        }
                        if ($row['id_category'] != $idCateg)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category SET position='.(int) $k.($done ? ',date_upd=NOW()' : '').' WHERE id_category='.(int) $row['id_category'];
                            if ($in_all_shops)
                            {
                                $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $row['id_category'];
                            }
                        }
                        ++$k;
                    }
                    addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $idCateg, $id_lang, _DB_PREFIX_.'category', 'Parent ID:'.(int) $idNewParent.' - Position:'.$newpos, (isset($categInfo) ? 'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int) $newpos : ''));
                    if (!$done)
                    { // Dnd to the end of a branch
                        $todo[] = 'UPDATE '._DB_PREFIX_."category SET id_parent=" .(int) $idNewParent . ",position=".(int) $k.',date_upd=NOW() WHERE id_category='.(int) $idCateg;
                        if ($in_all_shops)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg;
                        }
                    }
                }
                if (!empty($todo))
                {
                    foreach ($todo as $sqlTotal)
                    {
                        Db::getInstance()->Execute($sqlTotal);
                    }
                }
                SCMSCleanPositionsInAllShops($current_categ_id_parent);
                SCMSCleanPositionsInAllShops($idNewParent);
                fixLevelDepth();

                $sqlc = 'SELECT COUNT(*) AS nbc FROM '._DB_PREFIX_.'category';
                $nbCateg = Db::getInstance()->getValue($sqlc);
                if ($nbCateg <= 50)
                {
                    Category::regenerateEntireNtree();
                }

                ExtensionPMCM::clearFromIdsCategory($idCateg);
                SCI::hookExec('categoryUpdate', array('category' => new Category((int) $idCateg)));
            }
        }
    }
    if (!empty($action) && $action == 'emptybin')
    {
        if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
        {
            include_once SC_PS_PATH_DIR.'/images.inc.php';
        }
        if ($id_category > 1)
        {
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                Category::regenerateEntireNtree();
                $category = new Category($id_category);
                if (Validate::isLoadedObject($category) && !$category->isRootCategoryForAShop())
                {
                    if (SCMS)
                    {
                        SCMSdeleteCategory((int) $id_category, (int) $id_category);
                    }
                    else
                    {
                        $category->delete();
                    }

                    /* Delete products which were not in others categories */
                    $result = Db::getInstance()->ExecuteS('
                        SELECT `id_product`
                        FROM `'._DB_PREFIX_.'product`
                        WHERE `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`)');
                    foreach ($result as $p)
                    {
                        $product = new Product((int) $p['id_product']);
                        if (Validate::isLoadedObject($product))
                        {
                            $product->delete();
                        }
                    }
                    /* For products not deleted because of stock management or other... we place it in Recycle bin*/
                    $result = Db::getInstance()->ExecuteS('
                        SELECT `id_product`
                        FROM `'._DB_PREFIX_.'product`
                        WHERE `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`)');
                    foreach ($result as $p)
                    {
                        $product = new Product((int) $p['id_product']);
                        if (Validate::isLoadedObject($product))
                        {
                            $product->addToCategories($id_category);
                        }
                    }

                    /* Set category default to one category used where category no more exists */
                    $result = Db::getInstance()->Execute('
                        UPDATE `'._DB_PREFIX_.'product_shop` ps
                        SET ps.`id_category_default` = (SELECT cp.id_category FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.id_product=ps.id_product LIMIT 1)
                        WHERE `id_category_default` NOT IN (SELECT `id_category` FROM `'._DB_PREFIX_.'category`)');
                }
            }
            else
            {  // versions < 1.5
                $category = new Category($id_category);
                if (Validate::isLoadedObject($category))
                {
                    $category->delete();

                    /* Set category default to one category used where category no more exists */
                    $result = Db::getInstance()->Execute('
                        UPDATE `'._DB_PREFIX_.'product` ps
                        SET ps.`id_category_default` = (SELECT cp.id_category FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.id_product=ps.id_product LIMIT 1)
                        WHERE `id_category_default` NOT IN (SELECT `id_category` FROM `'._DB_PREFIX_.'category`)');
                }

                /* recreate SC Bin */
                $sql = 'INSERT INTO '._DB_PREFIX_.'category (id_category,id_parent,level_depth,active,date_upd,position) VALUES ('.(int) $category->id.",1,1,0,'".psql($category->date_upd)."',".(int) $category->position.')';
                Db::getInstance()->Execute($sql);
                $sql = 'INSERT INTO '._DB_PREFIX_.'category_group (id_category,id_group) VALUES ('.(int) $category->id.',1)';
                Db::getInstance()->Execute($sql);
                foreach ($languages as $lang)
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_.'category_lang (id_category,id_lang,name,link_rewrite) VALUES ('.(int) $category->id.','.(int) $lang['id_lang'].",'SC Recycle Bin','SC-Recycle-Bin')";
                    Db::getInstance()->Execute($sql);
                }
            }
            // PM Cache
            if (!empty($id_category))
            {
                ExtensionPMCM::clearFromIdsCategory($id_category);
            }
        }
    }
    if (!empty($action) && $action == 'active_products')
    {
        if (!empty($id_categories))
        {
            if (empty($id_shop) || $in_all_shops)
            {
                Db::getInstance()->Execute('
                UPDATE `'._DB_PREFIX_.'product`
                SET active = "'.pSQL($value).'"
                WHERE `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pInSQL($id_categories).'))');

                if (SCMS && $in_all_shops)
                {
                    Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'product_shop`
                    SET active = "'.pSQL($value).'"
                    WHERE `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pInSQL($id_categories).'))');
                }
                elseif (empty($id_shop) && version_compare(_PS_VERSION_, '1.5', '>='))
                {
                    Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'product_shop`
                    SET active = "'.pSQL($value).'"
                    WHERE `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pInSQL($id_categories).'))');
                }
            }
            elseif (SCMS && !empty($id_shop))
            {
                Db::getInstance()->Execute('
                    UPDATE `'._DB_PREFIX_.'product_shop`
                    SET active = "'.pSQL($value).'"
                    WHERE id_shop = "'.(int) $id_shop.'" AND `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pInSQL($id_categories).'))');
            }
            // PM Cache
            if (!empty($id_categories))
            {
                ExtensionPMCM::clearFromIdsCategory($id_categories);
            }
        }
    }
    if (!empty($action) && $action == 'paste_multiple' && !empty($id_category))
    {
        if (file_exists(SC_TOOLS_DIR.'lib/all/upload/upload-image.inc.php'))
        {
            require_once SC_TOOLS_DIR.'lib/all/upload/upload-image.inc.php';
        }
        else
        {
            require_once dirname(__FILE__).'/../../all/upload/upload-image.inc.php';
        }
        $id_parent = (int) Tools::getValue('id_parent', 0);
        if (!empty($id_parent))
        {
            duplicateCategories($id_category, $id_parent);

            $sqlc = 'SELECT COUNT(*) AS nbc FROM '._DB_PREFIX_.'category';
            $nbCateg = Db::getInstance()->getValue($sqlc);
            if ($nbCateg <= 50)
            {
                Category::regenerateEntireNtree();
            }

            // PM Cache
            if (!empty($id_category))
            {
                ExtensionPMCM::clearFromIdsCategory($id_category);
            }
        }
    }
