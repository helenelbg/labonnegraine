<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$return_datas = array();
$updated_products = array();

// Récupération de toutes les modifications à effectuer
function productDateUpdate($id_product, $ids_shop)
{

    //update date_upd
    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd = NOW() WHERE id_product='.(int) $id_product);
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shopConstraint = $ids_shop ? ' AND id_shop = '.(int)$ids_shop:'';
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd = NOW() WHERE id_product='.(int) $id_product.$shopConstraint);
    }
}

if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action != 'insert')
    {

        if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = Tools::getValue('rows');
        $rows = json_decode($_POST["rows"]);
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
                $id = $row->row;
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

                if (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    list($id_product, $id_shop) = explode('_', $id);
                    if (!empty($id_product) && !empty($id_shop))
                    {
                        $updated_products[$id_product] = $id_product;
                        $list_lang_fields = 'name,link_rewrite,available_now,available_later';
                        $list_shop_fields = 'active,visibility,on_sale,online_only,show_price,minimal_quantity,ecotax,id_tax_rules_group,price,wholesale_price,unity,unit_price_ratio,additional_shipping_cost,available_for_order,available_date,condition,available_date,online_only';
                        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                        {
                            $list_shop_fields .= ',show_condition';
                        }
                        $ecotaxrate = SCI::getEcotaxTaxRate();

                        // LANG
                        $fields = explode(',', $list_lang_fields);
                        $todo = array();
                        $link_rewrite = '';
                        foreach ($fields as $field)
                        {
                            if (isset($_POST[$field]))
                            {
                                $val = Tools::getValue($field);
                                $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode($val))."'";

                                if ($field == 'name' && _s('CAT_SEO_NAME_TO_URL'))
                                {
                                    $link_rewrite = "`link_rewrite`='".link_rewrite($val, Language::getIsoById($id_lang))."'";
                                }
                            }
                        }

                        if (!empty($link_rewrite))
                        {
                            $todo[] = $link_rewrite;
                        }

                        if (count($todo))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_lang SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product . " AND id_shop=" .(int) $id_shop . " AND id_lang=" .(int) $id_lang;
                            Db::getInstance()->Execute($sql);
                        }

                        // SHOP
                        $fields = explode(',', $list_shop_fields);
                        $todo = array();

                        foreach ($fields as $field)
                        {
                            if (isset($_POST[$field]))
                            {
                                $val = Tools::getValue($field);

                                if ($field == 'ecotax' && !empty($val))
                                {
                                    $val = $val / $ecotaxrate;
                                }

                                // si update taxe rule, on vérifie qu'elle est disponible pour le shop de la ligne
                                if (version_compare(_PS_VERSION_, '1.6.0.10', '>=')
                                    && $field == 'id_tax_rules_group'
                                    && $val != 0
                                ){

                                    // la taxe rule est pas disponible + nom tax rule
                                    $taxRuleAllowedForShopQuery = new DbQuery();
                                    $taxRuleAllowedForShopQuery->select('gs.id_tax_rules_group')
                                        ->from('shop','s')
                                        ->leftJoin('tax_rules_group_shop', 'gs', 's.id_shop = gs.id_shop AND gs.id_tax_rules_group = '.(int)$val)
                                        ->where('gs.id_shop = '.(int)$id_shop)
                                    ;
                                    $taxRuleAllowedForShop = Db::getInstance()->getRow($taxRuleAllowedForShopQuery->build());

                                    // construction du message
                                    if($taxRuleAllowedForShop){
                                        $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET id_tax_rules_group = '.(int)$val.' WHERE id_product='.(int) $id_product.' AND id_shop = '.(int)$id_shop;
                                        Db::getInstance()->execute($sql);
                                        productDateUpdate($id_product, $id_shop);
                                    } else {
                                        // sous requete pour recupérer le nom de la tax rule
                                        $taxName = Db::getInstance()->getValue((new DbQuery())->select('name')->from('tax_rules_group')->where('id_tax_rules_group = '.(int)$val));
                                        $shopName = Db::getInstance()->getValue((new DbQuery())->select('name')->from('shop')->where('id_shop = '.(int)$id_shop));

                                        $message = _l('Taxe rule `%s` cannot be applied on shop `%s`.', null,[$taxName, $shopName]);
                                        // modification action callback 'update' -> 'undo'
                                        $callbacks = str_replace($row->callback,'', $callbacks);
                                        $callbacks.=';displayErrorMessage("'.$message.'");';
                                    }

                                } else {
                                    $todo[] = '`'.bqSQL($field)."`='".pSQL(html_entity_decode($val))."'";
                                }

                            }
                        }
                        if (count($todo))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product_shop SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product . " AND id_shop=" .(int) $id_shop;
                            Db::getInstance()->Execute($sql);
                        }

                        // REF
                        $todo = array();
                        if (isset($_POST['reference']))
                        {
                            $val = Tools::getValue('reference');
                            $todo[] = "`reference`='".psql(html_entity_decode($val))."'";
                        }
                        if (isset($_POST['supplier_reference']))
                        {
                            $val = Tools::getValue('supplier_reference');
                            $todo[] = "`supplier_reference`='".psql(html_entity_decode($val))."'";

                            $product = new Product($id_product);
                            if (!empty($product->id_supplier))
                            {
                                $sql_supplier = 'SELECT * FROM '._DB_PREFIX_."product_supplier WHERE id_product=" .(int) $id_product . " AND id_supplier=" .(int) $product->id_supplier;
                                $actual_product_supplier = Db::getInstance()->getRow($sql_supplier);
                                if (!empty($actual_product_supplier['id_product_supplier']))
                                {
                                    $sql = 'UPDATE '._DB_PREFIX_."product_supplier SET `product_supplier_reference`='".psql(html_entity_decode($val))."' WHERE id_product_supplier=" .(int) $actual_product_supplier['id_product_supplier'];
                                    Db::getInstance()->Execute($sql);
                                }
                                else
                                {
                                    $sql = 'INSERT INTO '._DB_PREFIX_."product_supplier
                            (id_product, id_product_attribute, id_supplier, product_supplier_reference)
                            VALUES(" .(int) $id_product . ",'0','".$product->id_supplier."','".psql(html_entity_decode($val))."')";
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                        }
                        if (isset($_POST['ecotax']))
                        {
                            $ecotax = Tools::getValue('ecotax', 0) / $ecotaxrate;
                            $todo[] = "`ecotax`='".psql(html_entity_decode($ecotax))."'";
                        }
                        if (isset($_POST['ean13']))
                        {
                            $ean13 = Tools::getValue('ean13');
                            $todo[] = "`ean13`='".psql(($ean13))."'";
                        }
                        if (isset($_POST['upc']))
                        {
                            $upc = Tools::getValue('upc');
                            $todo[] = "`upc`='".psql(($upc))."'";
                        }
                        if (isset($_POST['isbn']))
                        {
                            $isbn = Tools::getValue('isbn');
                            $todo[] = "`isbn`='".psql(($isbn))."'";
                        }
                        if (isset($_POST['out_of_stock']))
                        {
                            $out_of_stock = Tools::getValue('out_of_stock');
                            $todo[] = "`out_of_stock`='".psql(($out_of_stock))."'";
                        }
                        if (isset($_POST['location']))
                        {
                            $location = Tools::getValue('location');
                            $todo[] = "`location`='".psql(($location))."'";
                        }
                        if (count($todo))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_.'product SET '.join(' , ', $todo)." WHERE id_product=" .(int) $id_product;
                            Db::getInstance()->Execute($sql);
                        }


                        productDateUpdate($id_product, $id_shop);

                        sc_ext::readCustomMsProductGridConfigXML('onAfterUpdateSQL');
                    }
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        // PM Cache
        if (!empty($updated_products))
        {
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
