<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$reloadCatLeftTree = false;
$reloadCat = false;
$return_datas = array();
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action != 'insert')
    {
        if (_PS_MAGIC_QUOTES_GPC_)
        {
            $_POST['rows'] = Tools::getValue('rows');
        }
        $rows = json_decode($_POST['rows']);
    }
    else
    {
        $rows = array();
        $rows[0] = new stdClass();
        $rows[0]->name = Tools::getValue('act', '');
        $rows[0]->action = Tools::getValue('action', '');
        $rows[0]->row = Tools::getValue('gr_id', '');
        $rows[0]->callback = Tools::getValue('callback', '');
        $rows[0]->params = $_POST;
    }

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = '';

        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        $date = date('Y-m-d H:i:s');
        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params) ? $row->params : array()), (!empty($row->callback) ? $row->callback : null), $date);
            $log_ids[$num] = $id;
        }

        // Deuxième boucle pour effectuer les
        // actions les une après les autres
        foreach ($rows as $num => $row)
        {
            if (!empty($log_ids[$num]))
            {
                $gr_id = (int) $row->row;
                $id_category = $row->row;
                $action = $row->action;

                if (!empty($row->callback))
                {
                    $callbacks .= $row->callback.';';
                }

                if ($action != 'insert')
                {
                    $_POST = array();
                    $_POST = (array) json_decode($row->params);
                }

                if (!empty($action) && $action == 'update')
                {
                    $idlist = Tools::getValue('idlist', '');
                    $sub_action = Tools::getValue('sub_action', '');

                    if ($sub_action != '')
                    {
                        $tmp = explode(',', $idlist);
                        if (!empty($tmp) && count($tmp) == 1)
                        {
                            $updated_products[$tmp[0]] = $tmp[0];
                        }
                        elseif (!empty($tmp) && count($tmp) > 1)
                        {
                            $updated_products = array_merge($updated_products, $tmp);
                        }

                        switch ($sub_action)
                        {
                            case '1':
                                $sql = 'DELETE FROM '._DB_PREFIX_.'category_product WHERE id_product IN ('.pInSQL($idlist).') AND id_category = '.(int) $id_category;
                                Db::getInstance()->Execute($sql);
                                $sql = 'SELECT MAX(position) AS max FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int) $id_category;
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
                                    $sql = 'INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES '.psql($sql).';';
                                    Db::getInstance()->Execute($sql);
                                    $sql = 'UPDATE `'._DB_PREFIX_.'product` SET date_upd=NOW() WHERE id_product IN ('.pInSQL($idlist).');';
                                    Db::getInstance()->Execute($sql);
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    {
                                        $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product IN ('.pInSQL($idlist).') AND id_shop='.(int) SCI::getSelectedShop();
                                        Db::getInstance()->Execute($sql);
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

                                $eservices_id_project = Tools::getValue('eservices_id_project', '');
                                if (!empty($eservices_id_project))
                                {
                                    eServices_sendListItems($eservices_id_project);
                                }
                                break;
                            case '0':
                                $sql = 'DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_product` IN ('.pInSQL($idlist).') AND `id_category` = '.(int) $id_category.';';
                                Db::getInstance()->Execute($sql);
                                $sql = 'UPDATE `'._DB_PREFIX_.'product` SET date_upd=NOW() WHERE id_product IN ('.pInSQL($idlist).');';
                                Db::getInstance()->Execute($sql);
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product IN ('.pInSQL($idlist).') AND id_shop='.(int) SCI::getSelectedShop();
                                    Db::getInstance()->Execute($sql);
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

                                $eservices_id_project = Tools::getValue('eservices_id_project', '');
                                if (!empty($eservices_id_project))
                                {
                                    eServices_sendListItems($eservices_id_project);
                                }
                                break;
                            case 'default1':
                                $ids = explode(',', $idlist);
                                if (SCMS)
                                {
                                    $id_shop = (int) Tools::getValue('id_shop', '');
                                    $sql = 'UPDATE `'._DB_PREFIX_.'product`
                                            SET date_upd=NOW(),id_category_default='.(int) $id_category.'
                                            WHERE id_product IN ('.pInSql($idlist).')
                                            AND id_shop_default ='.(int) $id_shop;
                                    Db::getInstance()->Execute($sql);
                                    if (!empty($id_shop))
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
                                elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $idshops = SCI::getSelectedShopActionList();
                                    foreach ($idshops as $id_shop)
                                    {
                                        $sql = 'UPDATE `'._DB_PREFIX_.'product`
                                                SET date_upd=NOW(),id_category_default='.(int) $id_category.'
                                                WHERE id_product IN ('.pInSql($idlist).')
                                                AND id_shop_default ='.(int) $id_shop;
                                        Db::getInstance()->Execute($sql);
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
                                    $sql = 'UPDATE `'._DB_PREFIX_.'product`
                                            SET date_upd=NOW(),id_category_default='.(int) $id_category.'
                                            WHERE id_product IN ('.pInSQL($idlist).')';
                                    Db::getInstance()->Execute($sql);
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
                                    $reloadCat = true;
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
                                            if (!sc_in_array($idc.','.$idp, $alreadylinked, 'catCategorypanelUpdateQueue_alreadylinked_'.$idp))
                                            {
                                                if ($idp != 0 && $idc != 0)
                                                {
                                                    ++$posarray[$idc];
                                                    $sqlinsert .= '('.(int) $idp.','.(int) $idc.','.(int) $posarray[$idc].'),';
                                                }
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
                                    $reloadCat = true;
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
                                            if (!in_array($idc.','.$idp, $defcateg))
                                            {
                                                $sqldelete[] = 'id_product='.(int) $idp.' AND id_category='.(int) $idc;
                                            }
                                        }
                                    }
                                    if (!empty($sqldelete))
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

                        // update date_upd
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product=".(int) $idlist);
                        if (SCMS)
                        {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product=".(int) $idlist.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
                        }
                    }
                }

                $return_callback = '';
                if ($reloadCatLeftTree)
                {
                    $return_datas['reload_cat'] = '1';
                }
                if ($reloadCat)
                {
                    $return_datas['refresh_cat'] = '1';
                }
                foreach ($return_datas as $key => $val)
                {
                    if (!empty($key))
                    {
                        if (!empty($return_callback))
                        {
                            $return_callback .= ',';
                        }
                        $return_callback .= $key.":'".str_replace("'", "\'", $val)."'";
                    }
                }
                $return_callback = '{'.$return_callback.'}';
                $callbacks = str_replace('{data}', $return_callback, $callbacks);

                QueueLog::delete($log_ids[$num]);
            }
        }

        if (!empty($updated_products))
        {
            if (_s('CAT_APPLY_ALL_CART_RULES'))
            {
                SpecificPriceRule::applyAllRules($updated_products);
            }
            // PM Cache
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
