<?php
    require 'application_top.php';
    error_log('1');
    if ( isset($_POST['code_ean']) && !empty($_POST['code_ean']) && isset($_POST['qte']) && $_POST['qte'] > 0 )
    {
        error_log('2 : '.$_POST['code_ean']);
        $produit = new Produit($_POST['code_ean']);

        error_log(print_r($produit, true));
        if ( $produit->id > 0 )
        {
            error_log('3');
            $req = 'UPDATE ps_LogiGraine_rangement_pdt_pk SET quantity = quantity - '.$_POST['qte'].' WHERE id_product = "'.$produit->id.'" AND id_product_attribute = "'.$produit->id_declinaison.'";';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);

            $req2 = 'UPDATE ps_stock_available SET quantity = quantity - '.$_POST['qte'].',  physical_quantity = physical_quantity - '.$_POST['qte'].' WHERE id_product = "'.$produit->id.'" AND id_product_attribute = "'.$produit->id_declinaison.'";';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);

            $req2 = 'UPDATE ps_stock_available SET quantity = quantity - '.$_POST['qte'].',  physical_quantity = physical_quantity - '.$_POST['qte'].' WHERE id_product = "'.$produit->id.'" AND id_product_attribute = "0";';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);

            echo $_POST['qte'].'#'.$produit->nom .' ('.$produit->declinaison.')';
            error_log($_POST['qte'].'#'.$produit->nom .' ('.$produit->declinaison.')');
        }
    }
?>