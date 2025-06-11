<?php
    include_once '../config/config.inc.php';
    include_once '../config/settings.inc.php';
    include_once '../init.php';
    
    try {
           $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
    } catch (exeption $ex) {
           die("probleme connexion serveur" . $ex->getMessage());
    }
    
    $tm[2425] = 1;
    $tm[2426] = 2;
    $tm[2427] = 3;
    $tm[2428] = 4;
    $tm[2429] = 5;
    $tm[2430] = 6;
    $tm[2431] = 7;
    $tm[2432] = 8;
    $tm[2433] = 9;
    $tm[2434] = 10;
    $tm[2435] = 11;
    $tm[2436] = 12;

    $tml[1] = 'janvier';
    $tml[2] = 'février';
    $tml[3] = 'mars';
    $tml[4] = 'avril';
    $tml[5] = 'mai';
    $tml[6] = 'juin';
    $tml[7] = 'juillet';
    $tml[8] = 'août';
    $tml[9] = 'septembre';
    $tml[10] = 'octobre';
    $tml[11] = 'novembre';
    $tml[12] = 'décembre';

    $req = 'SELECT p.id_product, p.reference, p.botanic_name, pa.ean13, c.*, al.name
            FROM ps_product p 
            LEFT JOIN awpf c ON p.id_product = c.id_product 
            LEFT JOIN ps_product_attribute pa ON p.id_product = pa.id_product 
            LEFT JOIN ps_product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute
            LEFT JOIN ps_attribute a ON pac.id_attribute = a.id_attribute
            LEFT JOIN ps_attribute_lang al ON a.id_attribute = al.id_attribute
            WHERE a.id_attribute_group = 6 AND al.id_lang = 1 AND p.reference = "4-091";';
    $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
    foreach($rangee as $product)
    {
        $reqb = 'SELECT * FROM `ps_feature_product` WHERE `id_product` = "'.$product['id_product'].'" AND id_feature = "21";';
        $rangeeb = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($reqb);
        $isBio = false;
        if ( $rangeeb[0]['id_feature_value'] == 2411 )
        {
            $isBio = true;
        }

        $reqs = 'SELECT * FROM `ps_feature_product` WHERE `id_product` = "'.$product['id_product'].'" AND id_feature = "27" ORDER BY id_feature_value DESC;';
        $rangees = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($reqb);
        $cpt = 0;
        $debut = 0;
        $fin = 0;

        $rangees = array(
            2436, 2435, 2434, 2426, 2425
        );

        foreach ( $rangees as $mois )
        {
            if ( $cpt == 0 )
            {
                $cpt++;
                $debut = $tm[$mois];
            }
            else
            {
                if ( ( $debut - $tm[$mois] ) == 1 )
                {
                    $debut = $tm[$mois];
                }
                else 
                {
                    $fin = $tm[$mois];
                    break;
                }
            }
        }
        echo $tml[$debut].' à '.$tml[$fin].'<br />';

        $rangees = array(
            2427, 2426, 2425
        );

        foreach ( $rangees as $mois )
        {
            if ( $cpt == 0 )
            {
                $cpt++;
                $debut = $tm[$mois];
            }
            else
            {
                if ( ( $debut - $tm[$mois] ) == 1 )
                {
                    $debut = $tm[$mois];
                }
                else 
                {
                    $fin = $tm[$mois];
                    break;
                }
            }
        }
        echo $tml[$debut].' à '.$tml[$fin].'<br />';
    }
?>