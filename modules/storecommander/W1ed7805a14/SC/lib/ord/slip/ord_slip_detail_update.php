<?php

    $act = Tools::getValue('act', 0);
    $action = Tools::getValue('action', 0);
    $col = Tools::getValue('col', 0);
    $val = Tools::getValue('val', 0);
    $id_order_slip__id_order_detail = Tools::getValue('id_order_slip__id_order_detail', 0);
    $tmp = explode('__', $id_order_slip__id_order_detail);
    $id_order_slip = $tmp[0];
    $id_order_detail = $tmp[1];

    if ($act == 'ord_slip_detail_update' && $action == 'update')
    {
        $fields = array('product_quantity', 'unit_price_tax_excl', 'unit_price_tax_incl', 'total_price_tax_excl', 'total_price_tax_incl', 'amount_tax_excl', 'amount_tax_incl');
        $todo = array();
        foreach ($fields as $field)
        {
            if ($col == $field)
            {
                $todo[] = $field."='".psql($val)."'";
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'order_slip_detail SET '.join(' , ', $todo).' WHERE id_order_slip='.(int) $id_order_slip.' AND id_order_detail='.(int) $id_order_detail;
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
