<?php
    require 'application_top.php';

    if ( isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['id_product_attribute']) && !empty($_POST['id_product_attribute']) )
    {
        $req = 'DELETE FROM ps_LogiGraine_rangement_pdt WHERE id_rangement_pdt = "'.$_POST['id'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";';
        if ( Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req) )
        {
            echo $_POST['id'];
        }
    }
    echo false;
?>