<?php
    require 'application_top.php';

    if ( isset($_POST['code_ean']) && !empty($_POST['code_ean']) && isset($_POST['emplacement']) && !empty($_POST['emplacement']) && isset($_POST['qte']) && $_POST['qte'] > 0 )
    {
        $produit = new Produit($_POST['code_ean']);

        if ( $produit->id > 0 )
        {
            $req = 'INSERT INTO ps_LogiGraine_rangement_pdt SET emplacement = "'.$_POST['emplacement'].'", id_product = "'.$produit->id.'", id_product_attribute = "'.$produit->id_declinaison.'", ean = "'.$_POST['code_ean'].'", quantity = "'.$_POST['qte'].'";';
            if ( Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req) )
            {
                $id_ins = Db::getInstance()->Insert_ID();
                echo '<div class="ligne'.$id_ins.'"><b>'.$_POST['qte'] . ' x </b>' . $produit->nom . ' / ' . $produit->declinaison.'&nbsp;<i class="fa-solid fa-trash" aria-hidden="true" idR="'.$id_ins.'" idPA="'.$produit->id_declinaison.'"></i><br /><br /></div>';
            }
        }
    }
?>