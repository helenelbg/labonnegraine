<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$updated_products = array();

// Récupération de toutes les modifications à effectuer
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
                $id_order_detail = $row->row;
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
                    $id_order = (int) Tools::getValue('id_order');
                    unset($_POST['id_order']);
                    $field = null;
                    foreach ($_POST as $field_name => $field_value)
                    {
                        $field = $field_name;
                        break;
                    }

                    $todo = array();
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && ($field == 'product_price' || $field == 'product_quantity'))
                    {
                        $order = new Order((int) $id_order);
                        $order_detail = new OrderDetail((int) $id_order_detail);

                        $tax_rate = Tools::ps_round((float) $order_detail->total_price_tax_incl / $order_detail->total_price_tax_excl, 2);

                        if ($field == 'product_quantity')
                        {
                            $product_quantity = (int) Tools::getValue($field);
                            $order_detail->product_quantity = $product_quantity;
                        }
                        else
                        {
                            $product_quantity = $order_detail->product_quantity;
                        }
                        if ($field == 'product_price')
                        {
                            $product_price_tax_excl = Tools::ps_round((float) Tools::getValue($field), 2);
                            $product_price_tax_incl = Tools::ps_round($product_price_tax_excl * $tax_rate, 2);
                        }
                        else
                        {
                            $product_price_tax_excl = $order_detail->unit_price_tax_excl;
                            $product_price_tax_incl = $order_detail->unit_price_tax_incl;
                        }
                        $total_products_tax_incl = $product_price_tax_incl * $product_quantity;
                        $total_products_tax_excl = $product_price_tax_excl * $product_quantity;

                        // Calculate differences of price (Before / After)
                        $diff_price_tax_incl = $total_products_tax_incl - $order_detail->total_price_tax_incl;
                        $diff_price_tax_excl = $total_products_tax_excl - $order_detail->total_price_tax_excl;

                        // Apply change on OrderInvoice
                        if ($order_detail->id_order_invoice)
                        {
                            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
                        }

                        if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0)
                        {
                            $order_detail->unit_price_tax_excl = $product_price_tax_excl;
                            $order_detail->unit_price_tax_incl = $product_price_tax_incl;

                            $order_detail->total_price_tax_incl += $diff_price_tax_incl;
                            $order_detail->total_price_tax_excl += $diff_price_tax_excl;

                            if (isset($order_invoice))
                            {
                                // Apply changes on OrderInvoice
                                $order_invoice->total_products += $diff_price_tax_excl;
                                $order_invoice->total_products_wt += $diff_price_tax_incl;

                                $order_invoice->total_paid_tax_excl += $diff_price_tax_excl;
                                $order_invoice->total_paid_tax_incl += $diff_price_tax_incl;
                            }

                            // Apply changes on Order
                            $order = new Order($order_detail->id_order);
                            $order->total_products += $diff_price_tax_excl;
                            $order->total_products_wt += $diff_price_tax_incl;

                            $order->total_paid += $diff_price_tax_incl;
                            $order->total_paid_tax_excl += $diff_price_tax_excl;
                            $order->total_paid_tax_incl += $diff_price_tax_incl;

                            $order->update();
                        }

                        // Save order detail
                        $order_detail->update();
                        // Save order invoice
                        if (isset($order_invoice))
                        {
                            $order_invoice->update();
                        }

                        addToHistory('order_detail', 'modification', $field, (int) $id_order, $id_lang, _DB_PREFIX_.'order_detail', psql(Tools::getValue($field)));
                    }
                    else
                    {
                        $todo[] = $field."='".psql(html_entity_decode(Tools::getValue($field)))."'";
                        addToHistory('order_detail', 'modification', $field, (int) $id_order, $id_lang, _DB_PREFIX_.'order_detail', psql(Tools::getValue($field)));
                    }
                    if (count($todo))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'order_detail SET '.join(' , ', $todo).' WHERE id_order_detail='.(int) $id_order_detail;
                        Db::getInstance()->Execute($sql);
                    }
                }
            }
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
