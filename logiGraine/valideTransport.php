<?php
    session_start();

    include(dirname(__FILE__).'/../config/config.inc.php');
    require dirname(__FILE__).'/../init.php';

    if ( isset($_POST['id_order']) && !empty($_POST['id_order']) )
    {
        $req = 'UPDATE ps_LogiGraine_controle SET transport = 1 WHERE id_order = "'.$_POST['id_order'].'";';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
    }
?>