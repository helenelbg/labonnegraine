<?php
    require 'application_top.php';

    if ( isset($_POST['id_product']) && !empty($_POST['id_product']) && isset($_POST['data_ajax']) && !empty($_POST['data_ajax']) )
    {
        $nouvelleBoite = Boite::getNextBoite();

        $req = 'INSERT INTO ps_LogiGraine_boite SET id_product = "'.$_POST['id_product'].'", code_boite = "'.$nouvelleBoite.'", reserve = 1;';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);

        $req_s = 'SELECT id_boite FROM ps_LogiGraine_boite WHERE code_boite = "'.$nouvelleBoite.'";';
        $resu_s = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_s);

        $exp_d = explode('#', $_POST['data_ajax']);
        foreach($exp_d as $single)
        {
            $exp_q = explode('_', $single);
            $req_q = 'INSERT INTO ps_LogiGraine_boite_decli SET id_boite = "'.$resu_s[0]['id_boite'].'", id_product_attribute = "'.$exp_q[0].'", quantity = "'.$exp_q[1].'";';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_q);
        }
        echo $nouvelleBoite;
    }
?>