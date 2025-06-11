<?php

    $action = Tools::getValue('action');
    $id_lang = (int) Tools::getValue('id_lang');
    $newId = (int) Tools::getValue('gr_id');

    function categoryChildren(&$to_delete, $id_category)
    {
        if (!is_array($to_delete) || !$id_category)
        {
            return false;
        }
        $result = Db::getInstance()->executeS('
        SELECT `id_category`
        FROM `'._DB_PREFIX_.'category`
        WHERE `id_parent` = '.(int) $id_category);
        foreach ($result as $row)
        {
            $to_delete[] = (int) $row['id_category'];
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

    switch ($action){
        case 'move':
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
                    $sql = 'SELECT c.id_category, c.id_parent, c.position FROM '._DB_PREFIX_."category c
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
                    $k = $newpos = 0;
                    $done = false;
                    $todo = array();
                    $sql = 'SELECT c.id_category, c.id_parent, cs.position FROM '._DB_PREFIX_.'category c
                                LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop='.(int) SCI::getSelectedShop().")
                                WHERE c.id_parent=" .(int) $idNewParent . "
                                ORDER BY cs.position";
                    $res = Db::getInstance()->ExecuteS($sql);
                    foreach ($res as $row)
                    {
                        if ($row['id_category'] == $idNextBrother)
                        {
                            $sql2 = 'SELECT c.id_parent,cs.position
                                         FROM '._DB_PREFIX_.'category c
                                         LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (c.id_category=cs.id_category '.(SCI::getSelectedShop() > 0 ? 'AND cs.id_shop='.(int) SCI::getSelectedShop() : '').")
                                         WHERE c.id_category='".(int) $idCateg."'";
                            $categInfo = Db::getInstance()->getRow($sql2);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category SET id_parent='.(int) $idNewParent.',date_upd=NOW() WHERE id_category='.(int) $idCateg;
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg.' AND id_shop = '.(int) SCI::getSelectedShop();
                            $done = true;
                            $newpos = $k;
                            ++$k;
                        }
                        if ($row['id_category'] != $idCateg)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category SET position=position'.($done ? ',date_upd=NOW()' : '').' WHERE id_category='.(int) $row['id_category'];
                            $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $row['id_category'].' AND id_shop = '.(int) SCI::getSelectedShop();
                        }
                        ++$k;
                    }
                    addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $idCateg, $id_lang, _DB_PREFIX_.'category', 'Parent ID:'.(int) $idNewParent.' - Position:'.$newpos, (isset($categInfo) ? 'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int) $newpos : ''));
                    if (!$done)
                    { // Dnd to the end of a branch
                        $todo[] = 'UPDATE '._DB_PREFIX_."category SET id_parent=" .(int) $idNewParent . ",date_upd=NOW() WHERE id_category=".(int) $idCateg;
                        $todo[] = 'UPDATE '._DB_PREFIX_.'category_shop SET position='.(int) $k.' WHERE id_category='.(int) $idCateg.' AND id_shop = '.(int) SCI::getSelectedShop();
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
            break;
        case 'insert':
            $id_parent = (int) Tools::getValue('id_parent', 1);
            $name = str_replace('"', "'", (Tools::getValue('name', 'new')));
            $newcategory = new Category();
            $newcategory->id_parent = $id_parent;
            $newcategory->level_depth = $newcategory->calcLevelDepth();
            $newcategory->active = 0;
            if (SCMS && SCI::getSelectedShopActionList())
            {
                $newcategory->id_shop_list = SCI::getSelectedShopActionList();
                $newcategory->id_shop_default = SCI::getSelectedShop();
                $_POST['checkBoxShopAsso_category'] = array();
                foreach ($newcategory->id_shop_list as $id)
                {
                    $_POST['checkBoxShopAsso_category'][$id] = $id;
                }
            }
            foreach ($languages as $lang)
            {
                $newcategory->link_rewrite[$lang['id_lang']] = link_rewrite($name, (string) $lang['iso_code']);
                $newcategory->name[$lang['id_lang']] = $name;
            }
            $newcategory->add();
            if (!sc_in_array(1, $newcategory->getGroups(), 'catCategoryupdate_catgroups'.$newcategory->id))
            {
                $newcategory->addGroups(array(1));
            }
            SCMSCleanPositionsInAllShops($id_parent);
            fixLevelDepth();
            echo $newcategory->id;
            exit;
        break;
        case 'emptybin':
            if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
            {
                include_once SC_PS_PATH_DIR.'images.inc.php';
            }
            $id_category = $id_category_bin = (int) Tools::getValue('id_category', 0);
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

                        /* Delete products which have only BIN as id_category default in all shops and associated to category BIN*/
                        $only_one_bin = Db::getInstance()->ExecuteS('SELECT COUNT(DISTINCT(id_category_default)) AS nb_categ,id_product
                                                                        FROM '._DB_PREFIX_.'product_shop
                                                                        WHERE id_product IN (SELECT cp.id_product
                                                                                                    FROM '._DB_PREFIX_.'category_product cp
                                                                                                    WHERE cp.id_category = '.(int) $id_category_bin.'
                                                                                                    AND cp.id_product IN (SELECT id_product
                                                                                                                            FROM '._DB_PREFIX_.'product_shop
                                                                                                                            WHERE id_category_default = '.(int) $id_category_bin.'))
                                                                        GROUP BY id_product
                                                                        HAVING nb_categ = 1');
                        foreach ($only_one_bin as $p)
                        {
                            $product = new Product((int) $p['id_product']);
                            if (Validate::isLoadedObject($product))
                            {
                                $product->delete();
                            }
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
                ExtensionPMCM::clearFromIdsCategory($id_category);
            }
            exit;
        break;
        case 'changedefault':
                $action = 'updated';
            break;
        case 'sort_and_save':
            $id_category = (int) Tools::getValue('id_category');
            $children = (Tools::getValue('children'));
            if (!empty($children))
            {
                $child_cat = explode(',', $children);
                foreach ($child_cat as $key => $value)
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_."category SET date_upd=NOW(), position='".(int) $key."' WHERE id_category=".(int) $value);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_."category_shop SET position='".(int) $key."' WHERE id_category='".(int) $value."' AND id_shop IN (".pInSQL(SCI::getSelectedShopActionList(true)).')');
                    }
                }
            }
            $action = 'updated';
            SCI::hookExec('categoryUpdate', array('category' => new Category((int) $id_category)));
            ExtensionPMCM::clearFromIdsCategory($id_category);
            break;
        case 'enable':
                $enable = (int) Tools::getValue('enable');
                $id_category = (int) Tools::getValue('id_category');
                $sql = 'UPDATE '._DB_PREFIX_."category SET date_upd=NOW(),active=" .(int) $enable . " WHERE id_category=".(int) $id_category;
                Db::getInstance()->Execute($sql);
                $action = 'updated';
                SCI::hookExec('categoryUpdate', array('category' => new Category((int) $id_category)));
                ExtensionPMCM::clearFromIdsCategory($id_category);
            break;
    }

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<data>';
    echo "<action type='".$action."' sid='".$newId."' tid='".$newId."'/>";
    $debug = false;
    echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
    echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
    echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
    if ($debug && isset($todo))
    {
        echo '<sql><![CDATA[';
        print_r($todo);
        echo ']]></sql>';
    }
    echo '</data>';
