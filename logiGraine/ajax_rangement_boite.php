<?php
    require 'application_top.php';

    if ( isset($_POST['code_boite']) && !empty($_POST['code_boite']) && isset($_POST['emplacement']) && !empty($_POST['emplacement']) )
    {
        $boite = new Boite($_POST['code_boite']);

        if ( $boite->id > 0 )
        {
            $req = 'INSERT INTO ps_LogiGraine_rangement_boite SET id_boite = "'.$boite->id.'", emplacement = "'.$_POST['emplacement'].'";';
            if ( Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req) )
            {
                echo $_POST['code_boite'];
            }
        }
    }
?>