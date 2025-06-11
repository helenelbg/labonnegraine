<?php

if (Tools::getValue('ids'))
{
    $ids = (Tools::getValue('ids'));

    if (!empty($ids))
    {
        $sql = 'SELECT DISTINCT(id_partner) as id_partner
                FROM '._DB_PREFIX_.'scaff_commission
                WHERE id_commission IN ('.pInSQL($ids).')';

        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $partner)
        {
            $total = 0;
            $com_id = '';
            $sql_com = 'SELECT *
                FROM '._DB_PREFIX_.'scaff_commission
                WHERE id_commission IN ('.pInSQL($ids).")
                    AND id_partner = '".(int) $partner['id_partner']."'
                    AND status = 'invoiced'
                    AND active = '1'";

            $res_com = Db::getInstance()->ExecuteS($sql_com);
            foreach ($res_com as $com)
            {
                if (!empty($com['price']))
                {
                    $total += $com['price'];
                    if (!empty($com_id))
                    {
                        $com_id .= ',';
                    }
                    $com_id .= $com['id_commission'];
                }
            }

            if (!empty($total))
            {
                $sql_insert = 'INSERT INTO '._DB_PREFIX_.'scaff_commission 
                (customer_id, id_partner, order_id, date_add, active, status, price, id_commission_paying) 
                VALUES (0,'.(int) $partner['id_partner'].",0,'".date('Y-m-d')."','1','paid', '".($total * -1)."','0')";
                Db::getInstance()->ExecuteS($sql_insert);
                $id_insert = Db::getInstance()->Insert_ID();
                if (!empty($id_insert))
                {
                    $sql_update = 'UPDATE '._DB_PREFIX_."scaff_commission SET id_commission_paying='".psql($id_insert)."', status='paid' WHERE id_commission IN (".pInSQL($com_id).')';
                    Db::getInstance()->ExecuteS($sql_update);
                }
            }
        }
    }
}
