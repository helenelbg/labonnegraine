<?php 
require 'application_top.php';

$req = 'SELECT id_groupe, id_order FROM ps_LogiGraine_groupes_commandes WHERE statut = "prise";';
$resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
$cmds = array();
foreach($resu as $rangee)
{  
    $reqUp = 'UPDATE ps_LogiGraine_groupes_commandes SET statut = "picking" WHERE id_groupe = "'.$rangee['id_groupe'].'";';
    $resuUp = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqUp);

    $listeCmd = explode('_', $rangee['id_order']);
    foreach ($listeCmd as $cmd)
    {
        $history = new OrderHistory();
        $history->id_order = $cmd;
        $history->id_order_state = 3;
        $history->add();
        $history->changeIdOrderState(3, $cmd);
    }
}