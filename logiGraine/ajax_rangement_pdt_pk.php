<?php
    require 'application_top.php';

    if ( isset($_POST['code_ean']) && !empty($_POST['code_ean']) && isset($_POST['emplacement']) && !empty($_POST['emplacement']) && isset($_POST['qte']) && $_POST['qte'] > 0 )
    {
        $produit = new Produit($_POST['code_ean']);

        if ( $produit->id > 0 )
        {
            $reqS = 'SELECT * FROM ps_LogiGraine_rangement_pdt_pk WHERE emplacement = "'.$_POST['emplacement'].'" AND id_product = "'.$produit->id.'" AND id_product_attribute = "'.$produit->id_declinaison.'";';
            $rangeeS = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($reqS);
       
            if ( isset($rangeeS[0]['id_rangement_pdt_pk']) && !empty($rangeeS[0]['id_rangement_pdt_pk']) )
            {
                if ( $rangeeS[0]['max'] == 0 )
                {
                    $req = 'UPDATE ps_LogiGraine_rangement_pdt_pk SET quantity = "'.$_POST['qte'].'", max = "'.$_POST['qte'].'" WHERE emplacement = "'.$_POST['emplacement'].'" AND id_product = "'.$produit->id.'" AND id_product_attribute = "'.$produit->id_declinaison.'";';
                }
                else 
                {
                    $req = 'UPDATE ps_LogiGraine_rangement_pdt_pk SET quantity = "'.$_POST['qte'].'" WHERE emplacement = "'.$_POST['emplacement'].'" AND id_product = "'.$produit->id.'" AND id_product_attribute = "'.$produit->id_declinaison.'";';
                }
            }
            else 
            {
                $req = 'INSERT INTO ps_LogiGraine_rangement_pdt_pk SET emplacement = "'.$_POST['emplacement'].'", id_product = "'.$produit->id.'", id_product_attribute = "'.$produit->id_declinaison.'", ean = "'.$_POST['code_ean'].'", quantity = "'.$_POST['qte'].'", max = "'.$_POST['qte'].'";';
            }
            if ( Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req) )
            {
                echo $_POST['emplacement'];
            }
        }
    }
?>