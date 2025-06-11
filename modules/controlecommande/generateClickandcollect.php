<?php
    include('../../config/config.inc.php');
    include('../../init.php');

    $chemin = dirname(__FILE__).'/../colissimo/documents/labels/';
    $cheminCN23Exist = dirname(__FILE__).'/../colissimo/documents/cn23/';
    $cheminCN23 = '/modules/colissimo/documents/cn23/';
    $etiquetteCN23 = '0';

    $dbQuery = new DbQuery();
    $dbQuery->select('o.`reference`, o.`date_add`, c.`firstname`, c.`lastname`')
            ->from('orders', 'o')
            ->leftJoin('customer', 'c', 'o.`id_customer` = c.`id_customer`');
    $dbQuery->where('o.id_order  = '.$_POST['id_order']);

    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)
                ->executeS($dbQuery);

    $date_achat = substr($results[0]['date_add'], 8, 2).'/'.substr($results[0]['date_add'], 5, 2).'/'.substr($results[0]['date_add'], 0, 4);

    if ( $_GET['poste'] == 'controle1' )
    {
        // Génération étiquette DATAMAX
        $donnees = 'O0110
f182
V4
L
A1
D11
SI
PI
H20
191100304910011 '.$results[0]['reference'].'
131200003460011 '.$date_achat.'
191100505320011 '.$results[0]['lastname'].' '.$results[0]['firstname'].'
Q0001
E

';
        $name_etiq = $results[0]['reference'].'.dpl';
    }
    else 
    {
        // Génération étiquette ZEBRA
        $donnees = 'CT~~CD,~CC^~CT~
^XA
~TA000
~JSN
^LT0
^MNW
^MTT
^PON
^PMN
^LH0,0
^JMA
^PR6,6
~SD15
^JUS
^LRN
^CI27
^PA0,1,1,0
^XZ
^XA
^MMT
^PW799
^LL1199
^LS0
^FT7,515^A0N,29,28^FB792,1,7,C^FH\^CI28^FD'.$results[0]['reference'].'\5C&^FS^CI27
^FT2,606^A0N,54,53^FB797,1,14,C^FH\^CI28^FD'.$results[0]['lastname'].' '.$results[0]['firstname'].'\5C&^FS^CI27
^FT11,675^A0N,29,63^FB788,1,7,C^FH\^CI28^FD'.$date_achat.'\5C&^FS^CI27
^PQ1,0,1,Y
^XZ
';
        $name_etiq = $results[0]['reference'].'.zpl';
    }

    $etiquette = $name_etiq;
    $chemin = dirname(__FILE__).'/etiq_cac/';

    if ( !file_exists($chemin.$etiquette) )
    {
        if (!$fp = fopen($chemin.$etiquette, 'x+')) {
                echo "Impossible d'ouvrir le fichier ($chemin.$etiquette)";
                exit;
        }

        // Ecrivons quelque chose dans notre fichier.
        if (fwrite($fp, $donnees) === FALSE) {
            echo "Impossible d'écrire dans le fichier ($chemin.$etiquette)";
            exit;
        }

        fclose($fp);
    }

    if ( file_exists($chemin.$etiquette) )
    {
        $base64 = base64_encode(file_get_contents($chemin.$etiquette));
        if ( $_GET['poste'] == 'controle1' )
        {
            echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=DATAMAX&adresseIp=&etiquette='.$base64;
        }
        else
        {
            echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
        }
    }
    else 
    {
        echo 'Erreur ClickAndCollect : '.$chemin.$etiquette;
    }

    echo '###0';
?>
