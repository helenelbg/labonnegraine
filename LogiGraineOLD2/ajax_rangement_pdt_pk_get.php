<?php
    require 'application_top.php';

    $req = 'SELECT * FROM ps_LogiGraine_rangement_pdt_pk WHERE emplacement = "'.$_POST['emplacement'].'";';
    foreach ( Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req) as $rangee )
    {
        $prodEC = new Produit($rangee['ean']);
        echo '<div class="ligne'.$rangee['id_rangement_pdt'].'"><b>'.$rangee['quantity'] . ' x </b>' . $prodEC->nom . ' / ' . $prodEC->declinaison.'&nbsp;<i class="fa-solid fa-trash" aria-hidden="true" idR="'.$rangee['id_rangement_pdt_pk'].'" idPA="'.$rangee['id_product_attribute'].'"></i><br /><br /></div>';
    }
?>