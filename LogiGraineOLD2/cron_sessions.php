<?php 
require 'application_top.php';

$cmd_rosiers = array();
$close_zone_rosiers = '(od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';
$sql_rosiers = 'SELECT id_order
        FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
        WHERE o.`current_state` = 2
        AND o.id_order IN (
            SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = 2 AND ('.$close_zone_rosiers.')
        )
        AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = 2 AND (od2.id_warehouse = 0 OR od2.id_warehouse <= '.date('W').'))
        ' . Shop::addSqlRestriction(false, 'o') . '
        ORDER BY invoice_date ASC';
$result_rosiers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_rosiers);

foreach($result_rosiers as $ros)
{
    $cmd_rosiers[] = $ros['id_order'];
}

if ( count($cmd_rosiers) > 0 )
{
    $req = 'SELECT o.id_order FROM ps_orders o LEFT JOIN ps_carrier c ON o.id_carrier = c.id_carrier WHERE o.current_state IN (2,47) AND c.id_reference <> 342 AND o.id_order NOT IN ('.implode(',', $cmd_rosiers).');';
}
else 
{
    $req = 'SELECT o.id_order FROM ps_orders o LEFT JOIN ps_carrier c ON o.id_carrier = c.id_carrier WHERE o.current_state IN (2,47) AND c.id_reference <> 342;';
}
$resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
$cmds = array();
foreach($resu as $rangee)
{  
    $req_v = 'SELECT id_groupe FROM ps_LogiGraine_groupes_commandes WHERE statut <> "disponible" AND (id_order LIKE "%_'.$rangee['id_order'].'_%" OR id_order LIKE "'.$rangee['id_order'].'_%" OR id_order LIKE "%_'.$rangee['id_order'].'" OR id_order = "'.$rangee['id_order'].'");';
    echo $req_v.'<br />';
    $resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);

    if ( !isset($resu_v[0]['id_groupe']) )
    {
        $cmds[] = $rangee['id_order'];
    }
}

print_r($cmds);

$groups = Commande::getGroups($cmds);
