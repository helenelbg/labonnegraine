<?php 
require 'application_top.php';
if ( !isset($_GET['date']) )
{
    $_GET['date'] = date('Y-m-d');
}
//$req = 'SELECT id_order FROM ps_orders WHERE date_add LIKE "'.$_GET['date'].'%" LIMIT 0,100;';
$req = 'SELECT id_order FROM ps_orders WHERE date_add LIKE "'.$_GET['date'].'%";';
$resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
$cmds = array();
foreach($resu as $rangee)
{
    $cmds[] = $rangee['id_order'];
}

$groups = Commande::getGroups($cmds);
