<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = (int) Tools::getValue('id_lang', '0');

$return = 'ERROR: Try again later';

// FUNCTIONS
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows'))
{
    if(_PS_MAGIC_QUOTES_GPC_)
        $_POST["rows"] = Tools::getValue('rows');
    $rows = json_decode($_POST["rows"]);

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
                $action = $row->action;

                if (!empty($row->callback))
                {
                    $callbacks .= $row->callback.';';
                }

                $_POST = array();
                $_POST = (array) json_decode($row->params);

                if (!empty($action) && $action == 'delete' && !empty($gr_id))
                {
                    $id_specific_price = $gr_id;
                    $id_specific_prices = explode(',', $id_specific_price);
                    foreach ($id_specific_prices as $id_specific_price)
                    {
                        if (!empty($id_specific_price))
                        {
                            $specificPrice = new SpecificPrice((int) ($id_specific_price));
                            $updated_products[$specificPrice->id_product] = (int) $specificPrice->id_product;
                            $specificPrice->delete();
                            addToHistory('cat_win-specificprice', 'delete', '', (int) $specificPrice->id, $id_lang, _DB_PREFIX_.'specific_price', null, null);
                        }
                    }
                }
                elseif (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $id_specific_price = $gr_id;
                    $id_product = (Tools::getValue('id_product', 0));
                    $id_shop = Tools::getValue('id_shop', 0);
                    $id_shop_group = Tools::getValue('id_shop_group', 0);
                    $id_currency = Tools::getValue('id_currency', 0);
                    $id_country = Tools::getValue('id_country', 0);
                    $id_group = Tools::getValue('id_group', 0);
                    $id_customer = Tools::getValue('id_customer', 0);
                    $reduction_tax = Tools::getValue('reduction_tax');
                    $from_quantity = Tools::getValue('from_quantity');

                    $id_shop_selected = Tools::getValue('id_shop_selected', 0);
                    if (!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $id_shop_selected = SCI::getSelectedShop();
                    }

                    $price = str_replace(',', '.', trim(Tools::getValue('price')));
                    $reduction_price = str_replace(',', '.', Tools::getValue('reduction_price'));
                    $reduction_percent = str_replace(',', '.', Tools::getValue('reduction_percent'));
                    $reduction_fixed_price = Tools::getValue('price');
                    $reduction_fixed_price = $reduction_fixed_price >= 0 ? str_replace(',', '.', Tools::getValue('price')) : $reduction_fixed_price;

                    $from = Tools::getValue('from');
                    $to = Tools::getValue('to');

                    $fields = array('price', 'from_quantity', 'id_shop', 'id_shop_group', 'id_group', 'id_country', 'id_currency', 'reduction_price', 'reduction_percent', 'from', 'to', 'reduction_tax');

                    $id_specific_prices = explode(',', $id_specific_price);
                    foreach ($id_specific_prices as $id_specific_price)
                    {
                        $specificPrice = new SpecificPrice((int) ($id_specific_price));
                        $updated_products[$specificPrice->id_product] = (int) $specificPrice->id_product;
                        foreach ($fields as $field)
                        {
                            if (isset($_POST[$field]))
                            {
                                if (isset($specificPrice->$field)) $before = $specificPrice->$field;
                                if ($field == 'reduction_price')
                                {
                                    $before = (float) $specificPrice->reduction;
                                    $tmp_red = str_replace(array(',', '%', '-'), array('.', '', ''), ${$field});
                                    $specificPrice->reduction = $after = (float) $tmp_red;
                                    $specificPrice->reduction_type = 'amount';
                                }
                                elseif ($field == 'reduction_percent')
                                {
                                    $before = ($specificPrice->reduction * 100).'%';
                                    $tmp_red = str_replace(array(',', '%', '-'), array('.', '', ''), ${$field});
                                    $specificPrice->reduction = $after = (float) ($tmp_red / 100);
                                    $specificPrice->reduction_type = 'percentage';
                                }
                                elseif ($field == 'price')
                                {
                                    $before = $specificPrice->price;
                                    if (${$field} >= 0)
                                    {
                                        $tmp_red = (float) str_replace(array(',', '%', '-'), array('.', '', ''), ${$field});
                                    }
                                    else
                                    {
                                        $tmp_red = ${$field};
                                    }
                                    $specificPrice->price = $tmp_red;
                                }
                                elseif ($field == 'from')
                                {
                                    $specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
                                }
                                elseif ($field == 'to')
                                {
                                    $specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
                                }
                                else
                                {
                                    $specificPrice->$field = (int) ${$field};
                                }
                                if (isset($specificPrice->$field)) $after = $specificPrice->$field;
                                if ($field == 'reduction_percent')
                                {
                                    $after = ($specificPrice->reduction * 100).'%';
                                }
                                addToHistory('cat_win-specificprice', 'modification', $field, (int) $specificPrice->id, $id_lang, _DB_PREFIX_.'specific_price', $after, $before);
                            }
                        }
                        $specificPrice->update();

                        sc_ext::readCustomWinSpePriceGridConfigXML('onAfterUpdateSQL');
                    }

                    if (isset($_POST['on_sale']))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_."product 
                                SET on_sale='".(int) Tools::getValue('on_sale')."'
                               WHERE id_product=" .(int) $id_product;
                        Db::getInstance()->Execute($sql);

                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop_selected))
                        {
                            $sql = 'UPDATE '._DB_PREFIX_."product_shop 
                              SET on_sale='".(int) Tools::getValue('on_sale')."'
                               WHERE id_product=" .(int) $id_product . " AND id_shop='".(int) $id_shop_selected."' ";
                            Db::getInstance()->Execute($sql);

                            $product = new Product((int) $id_product, false, null, (int) $id_shop_selected);
                        }
                        else
                        {
                            $product = new Product((int) $id_product);
                        }

                        SCI::hookExec('updateProduct', array('id_product' => (int) $id_product, 'product' => $product));
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
