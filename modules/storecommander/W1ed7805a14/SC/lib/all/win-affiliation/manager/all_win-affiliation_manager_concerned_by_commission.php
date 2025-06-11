<?php

$liste_order = array();
$liste_aff = array();
$listecom = array();
$listepaid = array();

if (Tools::getValue('ids'))
{
    $multiple = false;
    if (isset($_GET['ids']) && !empty($_GET['ids']) && strpos($_GET['ids'], ',') !== false)
    {
        $multiple = true;
    }

    $ids = (Tools::getValue('ids'));

    $sql = 'SELECT *
            FROM '._DB_PREFIX_.'scaff_commission
            WHERE id_commission IN ('.pInSQL($ids).')';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $commission)
    {
        if (!empty($commission['order_id']))
        {
            $liste_order[] = $commission['order_id'];

            $sql_order = 'SELECT *
                FROM '._DB_PREFIX_."orders
                WHERE id_order = '".(int) $commission['order_id']."'";
            $aff = Db::getInstance()->getRow($sql_order);
            if (!empty($aff['id_customer']))
            {
                $liste_aff[] = $aff['id_customer'];
            }
        }

        if (!$multiple)
        {
            $sql_paid = 'SELECT *
            FROM '._DB_PREFIX_."scaff_commission
            WHERE id_commission = '".(int) $commission['id_commission_paying']."'";
            $res_paid = Db::getInstance()->getRow($sql_paid);
            if (!empty($res_paid['id_commission']))
            {
                $listepaid[] = $res_paid['id_commission'];

                $sql_comm = 'SELECT *
                FROM '._DB_PREFIX_."scaff_commission
                WHERE id_commission_paying = '".(int) $res_paid['id_commission']."'";
                $res_comm = Db::getInstance()->ExecuteS($sql_comm);
                foreach ($res_comm as $comm)
                {
                    if (!empty($comm['id_commission']))
                    {
                        $listecom[] = $comm['id_commission'];
                    }
                }
            }
        }

        if (empty($listepaid) && count($listepaid) == 0)
        {
            $sql_comm = 'SELECT *
                    FROM '._DB_PREFIX_."scaff_commission
                    WHERE id_commission_paying = '".(int) $commission['id_commission']."'";
            $res_comm = Db::getInstance()->ExecuteS($sql_comm);
            foreach ($res_comm as $comm)
            {
                if (!empty($comm['id_commission']))
                {
                    $listecom[] = $comm['id_commission'];
                }
            }
            if (!empty($listecom) && count($listecom) > 0)
            {
                $listepaid[] = $commission['id_commission'];
            }
        }
    }
}

echo json_encode(array(
        'liste' => $liste_order,
        'listebis' => $liste_aff,
        'listecom' => $listecom,
        'listepaid' => $listepaid,
));
