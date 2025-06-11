<?php

    $action = Tools::getValue('action');
    $id_lang = (int) Tools::getValue('id_lang');
    $newId = (int) Tools::getValue('gr_id');
    $bin = (int) Tools::getValue('id_cms_category');

    function CmsCategoryChildren(&$to_delete, $id_cms_category)
    {
        if (!is_array($to_delete) || !$id_cms_category)
        {
            return false;
        }
        $result = Db::getInstance()->executeS('
        SELECT `id_cms_category`
        FROM `'._DB_PREFIX_.'cms_category`
        WHERE `id_parent` = '.(int) $id_cms_category);
        foreach ($result as $row)
        {
            $to_delete[] = (int) $row['id_cms_category'];
        }
    }

    function SCMSdeleteCategory($id_cms_category, $binCategory)
    {
        $cms_category = new CMSCategory($id_cms_category);
        if (Validate::isLoadedObject($cms_category))
        {
            if ((int) $cms_category->id === 0 || (int) $cms_category->id === 1)
            {
                return false;
            }

            $children = array();
            CmsCategoryChildren($children, $cms_category->id);
            foreach ($children as $id_cat)
            {
                $cat_cms = new CMSCategory($id_cat);
                SCMSdeleteCategory($cat_cms->id, $binCategory);
            }
            if ($id_cms_category != $binCategory)
            {
                $cms_category->id_shop_list = $cms_category->getAssociatedShops();
                $cms_category->delete();
                CMSCategory::cleanPositions($cms_category->id_parent);
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
                if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                {
                    $k = 1;
                    $newpos = 0;
                    $done = false;
                    $todo = array();
                    $sql = 'SELECT c.id_cms_category, c.id_parent, c.position FROM '._DB_PREFIX_."cms_category c
                            WHERE c.id_parent='".(int) $idNewParent."'
                            ORDER BY c.position";
                    $res = Db::getInstance()->ExecuteS($sql);
                    foreach ($res as $row)
                    {
                        if ($row['id_cms_category'] == $idNextBrother)
                        {
                            $sql2 = 'SELECT c.id_parent,c.position FROM '._DB_PREFIX_."cms_category c WHERE c.id_cms_category='".(int) $idCateg."'";
                            $categInfo = Db::getInstance()->getRow($sql2);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'cms_category SET id_parent='.(int) $idNewParent.',position='.(int) $k.',date_upd=NOW() WHERE id_cms_category='.(int) $idCateg;
                            $done = true;
                            $newpos = $k;
                            ++$k;
                        }
                        if ($row['id_cms_category'] != $idCateg)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'cms_category SET position='.(int) $k.($done ? ',date_upd=NOW()' : '').' WHERE id_cms_category='.(int) $row['id_cms_category'];
                        }
                        ++$k;
                    }
                    addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $idCateg, $id_lang, _DB_PREFIX_.'cms_category', 'Parent ID:'.(int) $idNewParent.' - Position:'.$newpos, (isset($categInfo) ? 'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int) $newpos : ''));
                    if (!$done)
                    { // Dnd to the end of a branch
                        $todo[] = 'UPDATE '._DB_PREFIX_."cms_category SET id_parent='".(int) $idNewParent."',position=".(int) $k.',date_upd=NOW() WHERE id_cms_category='.(int) $idCateg;
                    }
                    foreach ($todo as $sqlTotal)
                    {
                        Db::getInstance()->Execute($sqlTotal);
                    }
                }
                else
                { // PS 1.5
                    $k = 1;
                    $newpos = 0;
                    $done = false;
                    $todo = array();
                    $sql = 'SELECT c.id_cms_category, c.id_parent, c.position FROM '._DB_PREFIX_."cms_category c
                        WHERE c.id_parent='".(int) $idNewParent."'
                        ORDER BY c.position";
                    $res = Db::getInstance()->ExecuteS($sql);
                    foreach ($res as $row)
                    {
                        if ($row['id_cms_category'] == $idNextBrother)
                        {
                            $sql2 = 'SELECT c.id_parent,c.position
                                 FROM '._DB_PREFIX_."cms_category c
                                 WHERE c.id_cms_category='".(int) $idCateg."'";
                            $categInfo = Db::getInstance()->getRow($sql2);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'cms_category SET id_parent='.(int) $idNewParent.',position='.(int) $k.',date_upd=NOW() WHERE id_cms_category='.(int) $idCateg;
                            $done = true;
                            $newpos = $k;
                            ++$k;
                        }
                        if ($row['id_cms_category'] != $idCateg)
                        {
                            $todo[] = 'UPDATE '._DB_PREFIX_.'cms_category SET position='.(int) $k.($done ? ',date_upd=NOW()' : '').' WHERE id_cms_category='.(int) $row['id_cms_category'];
                        }
                        ++$k;
                    }
                    addToHistory('catalog_tree', 'move_categ', 'id_parent', (int) $idCateg, $id_lang, _DB_PREFIX_.'cms_category', 'Parent ID:'.(int) $idNewParent.' - Position:'.$newpos, (isset($categInfo) ? 'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int) $newpos : ''));
                    if (!$done)
                    { // Dnd to the end of a branch
                        $todo[] = 'UPDATE '._DB_PREFIX_."cms_category SET id_parent='".(int) $idNewParent."',position=".(int) $k.',date_upd=NOW() WHERE id_cms_category='.(int) $idCateg;
                    }
                    foreach ($todo as $sqlTotal)
                    {
                        Db::getInstance()->Execute($sqlTotal);
                    }
                }

                $sqlc = 'SELECT COUNT(*) AS nbc FROM '._DB_PREFIX_.'cms_category';
                $nbCateg = Db::getInstance()->getValue($sqlc);
            }
        break;
        case 'insert':
            $id_parent = (int) Tools::getValue('id_parent', 1);
            $name = str_replace('"', "'", (Tools::getValue('name', 'new')));
            $newCmsCategory = new CMSCategory();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
            {
                $newCmsCategory->id_shop_list = array((int) $id_shop);
            }
            $newCmsCategory->id_parent = (int) $id_parent;
            $newCmsCategory->active = 0;
            foreach ($languages as $lang)
            {
                $newCmsCategory->link_rewrite[$lang['id_lang']] = link_rewrite($name, $lang['iso_code']);
                $newCmsCategory->name[$lang['id_lang']] = $name;
            }
            $newCmsCategory->add();
            echo $newCmsCategory->id;
            exit;
        break;
        case 'emptybin':
            if ($bin > 1)
            {
                $sql = 'SELECT id_cms_category
                        FROM '._DB_PREFIX_.'cms_category
                        WHERE id_parent = '.(int) $bin;
                $allCmsCategories = Db::getInstance()->ExecuteS($sql);

                $sql = 'SELECT id_shop
                                FROM '._DB_PREFIX_.'shop
                                AND deleted  = 0';
                $shops = Db::getInstance()->ExecuteS($sql2);

                $sql = 'SELECT id_cms
                        FROM '._DB_PREFIX_.'cms
                        WHERE id_cms_category = '.(int) $cmscategory['id_cms_category'];
                $allCms = Db::getInstance()->ExecuteS($sql);

                $shopsArray = array();
                foreach ($shops as $shop)
                {
                    $shopsArray[] = (int) $shop['id_shop'];
                }

                foreach ($allCmsCategories as $cmsCategory)
                {
                    $category = new CMSCategory((int) $cmsCategory['id_cms_category']);
                    if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
                    {
                        $category->id_shop_list = $shopsArray;
                    }
                    $category->delete();
                }

                foreach ($allCms as $cms)
                {
                    $cms = new CMS((int) $cms['id_cms']);
                    $cms->id_shop_list = $shopsArray;
                    $cms->delete();
                }
            }
            exit;
        break;
        case 'changedefault':
            $action = 'updated';
            break;
        case 'sort_and_save':
            $id_cms_category = (int) Tools::getValue('id_cms_category');
            $children = (Tools::getValue('children'));
            if (!empty($children))
            {
                $child_cat = explode(',', $children);
                foreach ($child_cat as $key => $value)
                {
                    $sql = 'UPDATE '._DB_PREFIX_."cms_category SET date_upd=NOW(), position='".(int) $key."' WHERE id_cms_category='".(int) $value."'";
                    Db::getInstance()->Execute($sql);
                }
            }
            $action = 'updated';
        break;
        case 'enable':
                $enable = (int) Tools::getValue('enable');
            $id_cms_category = (int) Tools::getValue('id_cms_category');
            $sql = 'UPDATE '._DB_PREFIX_."cms_category SET date_upd=NOW(),active='".(int) $enable."' WHERE id_cms_category=".(int) $id_cms_category;
            Db::getInstance()->Execute($sql);
            $action = 'updated';
        break;
    }
