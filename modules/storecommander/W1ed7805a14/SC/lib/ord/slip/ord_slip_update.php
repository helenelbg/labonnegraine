<?php

    $act = Tools::getValue('act', 0);
    $action = Tools::getValue('action', 0);
    $col = Tools::getValue('col', 0);
    $val = Tools::getValue('val', 0);
    $id_order = (int) Tools::getValue('id_order', 0);
    $id_order_slip = (int) Tools::getValue('id_order_slip', 0);

    if ($act == 'ord_slip_update' && $action == 'insert' && $id_order)
    {
        $order = new Order($id_order);
        $amount = 0;
        $order_detail_list = array();
        $plist = $order->getProductsDetail();
        foreach ($plist as $row)
        {
            $plist_formated[$row['id_order_detail']] = 0;
        }
        foreach ($plist_formated as $id_order_detail => $amount_detail)
        {
            $order_detail_list[$id_order_detail] = array(
                            'quantity' => 1,
                            'id_order_detail' => (int) $id_order_detail,
                        );

            //$order_detail = new OrderDetail((int)$id_order_detail);
            $order_detail_list[$id_order_detail]['unit_price'] = 0;
            $order_detail_list[$id_order_detail]['amount'] = 0;
        }

        OrderSlip::create($order, $order_detail_list);
    }

    if ($act == 'ord_slip_update' && $action == 'update')
    {
        $fields = array('total_products_tax_excl', 'total_products_tax_incl', 'total_shipping_tax_excl', 'total_shipping_tax_incl', 'conversion_rate', 'shipping_cost', 'amount', 'shipping_cost_amount');
        $todo = array();
        foreach ($fields as $field)
        {
            if ($col == $field)
            {
                $todo[] = $field."='".psql($val)."'";
                //addToHistory('order_detail','modification',$field,(int) $id_order,$id_lang,_DB_PREFIX_."order_detail",psql(Tools::getValue($field)));
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'order_slip SET '.join(' , ', $todo).' WHERE id_order_slip='.(int) $id_order_slip;
            Db::getInstance()->Execute($sql);
        }
        $action = 'update';
    }

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo $sql;
