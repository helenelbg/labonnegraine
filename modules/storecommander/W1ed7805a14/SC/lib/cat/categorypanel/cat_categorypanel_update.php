<?php

$idlist = Tools::getValue('idlist', '');
$action = Tools::getValue('action', '');
$id_category = Tools::getValue('id_category', '0');

$reloadCat = false;

if ($action != '')
{
    switch ($action){
        case '1':
            $sql = 'DELETE FROM '._DB_PREFIX_.'category_product WHERE id_product IN ('.pInSQL($idlist).') AND id_category = '.(int)$id_category;
            Db::getInstance()->Execute($sql);
            $sql = 'SELECT MAX(position) AS max FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int)$id_category;
            $res = Db::getInstance()->getRow($sql);
            $max = (int) $res['max'];
            $id_product_src = explode(',', Tools::getValue('idlist', '0'));
            $sql = '';
            foreach ($id_product_src as $src)
            {
                ++$max;
                $sql .= '('.$src.','.(int) $id_category.','.$max.'),';
            }
            $sql = trim($sql, ',');
            if ($sql != '')
            {
                if (SCMS)
                {
                    $id_shop = SCI::getSelectedShop();
                    $category = new Category($id_category);
                    if (!$category->existsInShop($id_shop))
                    {
                        $category->addShop($id_shop);
                        $reloadCat = true;
                    }
                }
                $sql = 'INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES '.psql($sql);
                Db::getInstance()->execute($sql);
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET date_upd=NOW() WHERE id_product IN ('.pInSQL($idlist).')');
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product IN ('.pInSql($idlist).') AND id_shop='.(int) SCI::getSelectedShop());
                }
                if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                {
                    $ids = explode(',', $idlist);
                    foreach ($ids as $idproduct)
                    {
                        $product = new Product((int) $idproduct);
                        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                    }
                }
                elseif (_s('APP_COMPAT_EBAY'))
                {
                    $ids = explode(',', $idlist);
                    sort($ids);
                    Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $ids[0]));
                }
            }
            break;
        case '0':
            $sql = 'DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` IN ('.pInSql($idlist).') AND `id_category` = '.(int) $id_category.';';
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET date_upd=NOW() WHERE id_product IN ('.pInSql($idlist).')');
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product IN ('.pInSql($idlist).') AND id_shop='.(int) SCI::getSelectedShop());
            }
            if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
            {
                $ids = explode(',', $idlist);
                foreach ($ids as $idproduct)
                {
                    $product = new Product((int) $idproduct);
                    SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                }
            }
            elseif (_s('APP_COMPAT_EBAY'))
            {
                $ids = explode(',', $idlist);
                sort($ids);
                Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $ids[0]));
            }
            break;
        case 'default1':
            $sql = 'UPDATE `'._DB_PREFIX_.'product` SET date_upd=NOW(),id_category_default='.(int) $id_category.' WHERE id_product IN ('.pInSQL($idlist).')';
            Db::getInstance()->Execute($sql);
            $ids = explode(',', $idlist);
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $idshops = SCI::getSelectedShopActionList();
                foreach ($idshops as $id_shop)
                {
                    $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),id_category_default='.(int) $id_category.' WHERE id_product IN ('.pInSQL($idlist).') AND id_shop = '.(int) $id_shop;
                    Db::getInstance()->Execute($sql);

                    foreach ($ids as $idproduct)
                    {
                        $product = new Product((int) $idproduct, false, null, (int) $id_shop);
                        $product->setGroupReduction();
                    }
                }
            }
            else
            {
                foreach ($ids as $idproduct)
                {
                    $product = new Product((int) $idproduct, false);
                    $product->setGroupReduction();
                }
            }
            if (!_s('CAT_PROD_CAT_DEF_EXT'))
            {
                $sql = 'SELECT GROUP_CONCAT(id_product) FROM `'._DB_PREFIX_.'category_product` WHERE id_category='.(int) $id_category;
                $plist = Db::getInstance()->getValue($sql);
                $sql = 'SELECT MAX(position) AS max FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int) $id_category;
                $res = Db::getInstance()->getRow($sql);
                $max = (int) $res['max'];
                $id_product_src = explode(',', Tools::getValue('idlist', '0'));
                $id_product_alreadyin = explode(',', $plist);
                $id_product_src = array_diff($id_product_src, $id_product_alreadyin);
                $sql = '';
                foreach ($id_product_src as $src)
                {
                    if ($src != 0 && $id_category != 0)
                    {
                        ++$max;
                        $sql .= '('.$src.','.(int) $id_category.','.$max.'),';
                    }
                }
                if ($sql != '')
                {
                    $sql = trim($sql, ',');
                    $sql = 'INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES '.$sql;
                    Db::getInstance()->Execute($sql);
                }
            }
            if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
            {
                $ids = explode(',', $idlist);
                foreach ($ids as $idproduct)
                {
                    $product = new Product((int) $idproduct);
                    SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                }
            }
            elseif (_s('APP_COMPAT_EBAY'))
            {
                $ids = explode(',', $idlist);
                sort($ids);
                Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $ids[0]));
            }
            break;
        case 'multi_add':
            $idprod = Tools::getValue('idprod', '0');
            $idcateg = Tools::getValue('idcateg', '0');
            if ($idprod != '0' && $idcateg != '0')
            {
                $sql = 'SELECT distinct id_product,id_category FROM '._DB_PREFIX_.'category_product WHERE id_product IN ('.pInSQL($idprod).') AND id_category IN ('.pInSQL($idcateg).')';
                $res = Db::getInstance()->ExecuteS($sql);
                $alreadylinked = array();
                foreach ($res as $row)
                {
                    $alreadylinked[] = $row['id_category'].','.$row['id_product'];
                }
                $sqlinsert = '';
                $posarray = array();
                $listprod = explode(',', $idprod);
                $listcateg = explode(',', $idcateg);
                $id_shop = SCI::getSelectedShop();
                foreach ($listcateg as $idc)
                {
                    if (!sc_array_key_exists($idc, $posarray))
                    {
                        $sql = 'SELECT MAX(position) AS max FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int) $idc;
                        $res = Db::getInstance()->getRow($sql);
                        $posarray[$idc] = (int) $res['max'];
                    }
                    foreach ($listprod as $idp)
                    {
                        if (!sc_in_array($idc.','.$idp, $alreadylinked, 'catCategorypanelUpdate_alreadylinked'))
                        {
                            if ($idp != 0 && $idc != 0)
                            {
                                ++$posarray[$idc];
                                $sqlinsert .= '('.(int) $idp.','.(int) $idc.','.(int) $posarray[$idc].'),';
                            }
                        }
                    }

                    if (SCMS)
                    {
                        $category = new Category((int) $idc);
                        if (!$category->existsInShop($id_shop))
                        {
                            $category->addShop($id_shop);
                            $reloadCat = true;
                        }
                    }
                }
                $sqlinsert = trim($sqlinsert, ',');
                if ($sqlinsert != '')
                {
                    $sql = 'INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES '.psql($sqlinsert);
                    Db::getInstance()->Execute($sql);
                }
                if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                {
                    $ids = explode(',', $idprod);
                    foreach ($ids as $idproduct)
                    {
                        $product = new Product((int) $idproduct);
                        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                    }
                }
                elseif (_s('APP_COMPAT_EBAY'))
                {
                    $ids = explode(',', $idprod);
                    sort($ids);
                    Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $ids[0]));
                }
            }
            break;
        case 'multi_del':
            $idprod = Tools::getValue('idprod', '0');
            $idcateg = Tools::getValue('idcateg', '0');
            if ($idprod != '0' && $idcateg != '0')
            {
                $sql = 'SELECT id_product,id_category_default FROM '._DB_PREFIX_.'product WHERE id_product IN ('.pInSQL($idprod).')';
                $res = Db::getInstance()->ExecuteS($sql);
                $defcateg = array();
                foreach ($res as $row)
                {
                    $defcateg[] = $row['id_category_default'].','.$row['id_product'];
                }
                $sqldelete = array();
                $listprod = explode(',', $idprod);
                $listcateg = explode(',', $idcateg);
                foreach ($listcateg as $idc)
                {
                    foreach ($listprod as $idp)
                    {
                        if (!sc_in_array($idc.','.$idp, $defcateg, 'catCategorypanelUpdate_defcateg'))
                        {
                            $sqldelete[] = 'id_product='.(int) $idp.' AND id_category='.(int) $idc;
                        }
                    }
                }
                if (count($sqldelete))
                {
                    foreach ($sqldelete as $sqldel)
                    {
                        $sql = 'DELETE FROM `'._DB_PREFIX_.'category_product` WHERE '.psql($sqldel);
                        Db::getInstance()->Execute($sql);
                    }
                }
                if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                {
                    $ids = explode(',', $idprod);
                    foreach ($ids as $idproduct)
                    {
                        $product = new Product((int) $idproduct);
                        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
                    }
                }
                elseif (_s('APP_COMPAT_EBAY'))
                {
                    $ids = explode(',', $idprod);
                    sort($ids);
                    Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $ids[0]));
                }
            }
            break;
    }
}

if ($reloadCat)
{
    echo 'reload_cat';
}
