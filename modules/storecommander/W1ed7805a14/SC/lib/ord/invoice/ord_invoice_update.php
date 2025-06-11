<?php

    $act = Tools::getValue('act', 0);
    $action = Tools::getValue('action', 0);
    $col = Tools::getValue('col', 0);
    $val = Tools::getValue('val', 0);
    $id_order = (int) Tools::getValue('id_order', 0);
    $id_order_invoice = (int) Tools::getValue('id_order_invoice', 0);

    if ($act == 'ord_invoice_update' && $action == 'update')
    {
        $fields = array('delivery_date', 'total_discount_tax_excl', 'total_discount_tax_incl', 'total_paid_tax_excl', 'total_paid_tax_incl', 'total_products', 'total_products_wt',
            'total_shipping_tax_excl', 'total_shipping_tax_incl', 'total_wrapping_tax_excl', 'total_wrapping_tax_incl', 'note', );
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
            $sql = 'UPDATE '._DB_PREFIX_.'order_invoice SET '.join(' , ', $todo).' WHERE id_order_invoice='.(int) $id_order_invoice;
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
