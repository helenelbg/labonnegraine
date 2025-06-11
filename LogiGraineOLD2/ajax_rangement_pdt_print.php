<?php
    require 'application_top.php';

    if ( isset($_POST['emplacement']) && !empty($_POST['emplacement']) )
    {
        $req = 'SELECT * FROM ps_LogiGraine_rangement_pdt WHERE emplacement = "'.$_POST['emplacement'].'" ORDER BY id_product, id_product_attribute;';
        $produitActuel = '';
        foreach ( Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req) as $rangee )
        {
            $prodEC = new Produit($rangee['ean']);
            if ( !empty($produitActuel) && $produitActuel != $prodEC->id )
            {
                echo '@';
            }
            if ( $produitActuel != $prodEC->id )
            {
                echo str_replace('Pomme de terre ', '', $prodEC->nom).'#'.str_replace('sac de ', '', str_replace(' (tubercules)', '', $prodEC->declinaison));
            }
            else
            {
                echo ' / '.str_replace('sac de ', '', str_replace(' (tubercules)', '', $prodEC->declinaison));
            } 
            $produitActuel = $prodEC->id;
        }
    }
    echo false;
?>