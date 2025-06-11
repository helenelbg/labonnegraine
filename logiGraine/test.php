<?php 
require 'application_top.php';

/** TEST SI MODULE AUTORISE POUR OPERATEUR **/
$testAutorisation = LBGModule::testModuleByOperateur(2, $operateur->id);
if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
{
    header('Location: /LogiGraine/accueil.php');
};

// Pdt 1.5kg 100 (6-001 > 6-999)
// Asperges 100 (7-600 > 7-999)
// Rosiers 400 (0-001 > 0-299)
// 2-900 > 3-999 5
// Fraisiers en racines nues 7-500 > 7-599 10

//$taille_caisse = array(100,200,400);
$taille_caisse = array(100,400);
if ( true )
{
    $liste_zone = array(
        1 => array(
            '0-', '1-', '2-', '3-', '4-', '20-000'
        ),
        2 => array(
            '5-'
        ),
        3 => array(
            '6-', '7-', '8-'
        ),
        4 => array(
            '9-', '10-', '11-', '13-'
        ),
        5 => array(
            '14-', '15-', 'M-', 'PACK45'
        )
    );
    $unite_zone = array(
        1 => 1,
        2 => 10,
        3 => 10,
        4 => 10,
        5 => 10
    );

    $cmdEC = array(array('product_id' => 3742, 'product_attribute_id' => 68328, 'product_reference' => '6-505', 'product_quantity' => 1));
    $tailleCmd = 0;
    $force = 0;
    foreach($cmdEC as $prodEC)
    {
        $tmpProd = new Product($prodEC['product_id']);
        $cpt = 0;
        $position = 0;

        foreach($tmpProd->getAttributesGroups(1) as $tmpAttr)
        {
            $cpt++;
            if ( $tmpAttr['id_attribute_group'] == 6 && $tmpAttr['id_product_attribute'] == $prodEC['product_attribute_id'] )
            {
                $position = $cpt;
            }
        }
        $prefixe = explode('-', $prodEC['product_reference'])[0].'-';
        $zoneEC = 0;
        foreach($liste_zone as $idz => $z)
        {
            if ( in_array($prefixe, $z) )
            {
                $zoneEC = $idz;
            }
        }
        
        if ( $zoneEC == 4 ) // Extérieur
        {   
            $req = new DbQuery();
            $req->select('fp.id_feature, fp.id_feature_value');
            $req->from('feature_product', 'fp');
            $req->where('fp.id_product = "'.$prodEC['product_id'].'" AND id_feature = 19');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

            if ( isset($resu[0]['id_feature']) && !empty($resu[0]['id_feature']) )
            {
                if ( $resu[0]['id_feature_value'] == 2266 ) // 7 cm
                {
                    $tailleCmd += 12 * $prodEC['product_quantity']; 
                }
                elseif ( $resu[0]['id_feature_value'] == 2267 || $resu[0]['id_feature_value'] == 2280 ) // 9 cm et 8 cm
                {
                    $tailleCmd += 16 * $prodEC['product_quantity']; 
                } 
                elseif ( $resu[0]['id_feature_value'] == 2281 ) // 2L
                {
                    $tailleCmd += 50 * $prodEC['product_quantity']; 
                } 
                elseif ( $resu[0]['id_feature_value'] == 6080 ) // Racines nues
                {
                    $tailleCmd += 50 * $prodEC['product_quantity']; 
                } 
            }
            else 
            {
                // on a pas la taille du produit, on force la caisse verte
                $tailleCmd += 200;
            }
        }
        elseif ( $zoneEC == 5 ) // Accessoires
        {   
            $req = new DbQuery();
            $req->select('lga.ref, lga.unite, lga.forcer');
            $req->from('LogiGraine_accessoires', 'lga');
            $req->where('lga.ref = "'.$prodEC['product_reference'].'"');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

            if ( isset($resu[0]['ref']) && !empty($resu[0]['ref']) )
            {
                $tailleCmd += $resu[0]['unite'] * $prodEC['product_quantity'];
                if ( $resu[0]['forcer'] > $force )
                {
                    $force = $resu[0]['forcer'];
                }
            }
            else 
            {
                // on a pas la taille de l'accessoire, on force la caisse noire
                $tailleCmd += 400;
            }
        }
        else 
        {
            // OK Asperges 100 (7-600 > 7-999)
            // OK Rosiers 400 (0-001 > 0-299)
            // OK 2-900 > 3-999 5
            // OK Fraisiers en racines nues 7-500 > 7-599 10
            // Pdt 1.5kg 100 (6-001 > 6-999)

            $refEC = ((str_replace('-', '', $prodEC['product_reference']))*1);
            if ( $refEC >= 7600 && $refEC <= 7999 )
            {
                $unite_zone[$zoneEC] = 100;
            }
            elseif ( $refEC >= 1 && $refEC <= 299 )
            {
                $unite_zone[$zoneEC] = 400;
            }
            elseif ( $refEC >= 2900 && $refEC <= 3999 )
            {
                $unite_zone[$zoneEC] = 5;
            }
            elseif ( $refEC >= 7500 && $refEC <= 7599 )
            {
                $unite_zone[$zoneEC] = 10;
            }
            elseif ( !isset($unite_zone[$zoneEC]) )
            {
                $unite_zone[$zoneEC] = 0;
            }
            $taille_prod = $unite_zone[$zoneEC] * $prodEC['product_quantity'];
            for ($i = 1; $i < $position; $i++)
            {
                $taille_prod = $taille_prod * 4;
            }
            $tailleCmd += $taille_prod;
        }
    }
    if ( !isset($force))
    {
        $force = 0;
    }
    if ( $force > $tailleCmd )
    {
        echo $force;
        die;
    }

    foreach($taille_caisse as $tc)
    {
        if ( $tailleCmd < $tc )
        {
            echo $tc . '('.$tailleCmd.')';
            die;
        }
    }
    echo 400;
    die;
}




die;
$produitsEC = Commande::getProductsByOrderGroup2('245224_245169_245264_245259_245332');
                    $totalEC = 0;
                    foreach($produitsEC as $produitEC)
                    {
                        $totalEC += $produitEC['quantity_final'];
                    }
                    echo $totalEC;
die;

$liste_produits = Commande::getGroups(array('245224','245169','245264','245259','245332'), true);

foreach($liste_produits as $r)
{
    $groupeEC = '';
    foreach($r['trolley_layout'] as $etage)
    {
        foreach($etage['commandes'] as $idEC)
        {
        if ( !empty($groupeEC) )
        {
            $groupeEC .= '_';
        }
        $groupeEC .= $idEC;
        }
    }
    echo $groupeEC.'<br />';
}

echo '<pre>';
    print_r($liste_produits);
echo '</pre>';
die;


/*$produitsEC = Commande::getProductsByOrderGroup2('241591_241756_241765');
$totalEC = 0;
foreach($produitsEC as $produitEC)
{
    echo $produitEC['quantity_final'].' / '.$produitEC['product_name'].' / '.$produitEC['id_order'].'<br />';
    $totalEC += $produitEC['quantity_final'];
}
echo $totalEC;*/
$req = 'SELECT distinct id_order FROM ps_order_detail WHERE product_reference = "14-021" ORDER BY id_order DESC LIMIT 0,100;';
$resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
foreach($resu as $ord)
{
    echo $ord['id_order'].' : '.Caisse::getTailleCaisseByCommande($ord['id_order']).'<br />';
}
die;


$expl_multi = explode('_', '245157_245009_245342_245121_245353');

foreach($expl_multi as $cmdEC)
            {
                $listeArt = Commande::getProductsByOrder($cmdEC);
                $articles = array();
                foreach($listeArt as $artEC)
                {
                    $req_emp = 'SELECT etagere_plan FROM ps_LogiGraine_plan WHERE (REPLACE(debut_plan,"-","") * 1) <= "'.str_replace('-', '', $artEC['product_reference']).'" AND (REPLACE(fin_plan,"-","") * 1) >= "'.str_replace('-', '', $artEC['product_reference']).'" LIMIT 0,1;';
                    echo $req_emp.'<br />';
                    $resu_emp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_emp);
                    if ( !isset($resu_emp[0]['etagere_plan']) )
                    {
                        $resu_emp[0]['etagere_plan'] = '-';
                    }
                    $articles[] = array($artEC['product_ean13'], $artEC['quantity_final'], $resu_emp[0]['etagere_plan']);
                }
                $commandes[$cmdEC] = $articles;
            }
            echo '<pre>';
            print_r($commandes);
            echo '</pre>';

$liste_produits = Commande::getGroups($expl_multi, true);
echo '<pre>';
    print_r($liste_produits);
echo '</pre>';
$rows3 = '';
        $locationEC = '';
        $ordreEC = 0;
        foreach($liste_produits as $ordreProduits)
        {
            foreach($ordreProduits['sequence'] as $seq)
            {
                foreach($seq['items'] as $prod)
                {
                    if ( !empty($rows3) )
                    {
                        $rows3 .= ',';
                    }
                    if ( $locationEC != $prod['location'])
                    {
                        $ordreEC++;
                        $locationEC = $prod['location'];
                    }
                    $rows3 .= '('.$prod['order_id'].', "'.$prod['location'].'", "'.$prod['ean'].'", '.$prod['quantity'].', '.$ordreEC.')';
                }
            }
            
            $req3 = 'INSERT IGNORE INTO ps_LogiGraine_controle_produit_ordre
            (id_order, location, ean, quantity, ordre)
            VALUES
            '.$rows3.';';
            echo $req3;
            //Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req3);
        }
die;
$title = 'Scan commandes';
include('top.php'); 
include('header.php'); 
                     $orderEC = 234549;
$commandeEC = new Order($orderEC);
                            echo '<select id="selected_device" style="visibility:hidden;" onchange=onDeviceSelected(this);></select>';
                            $adresseEC = new Address($commandeEC->id_address_delivery);
                            ?>
                            <script type="text/javascript" src="BrowserPrint-3.1.250.min.js"></script>
                            <script type="text/javascript">

                            var selected_device;
                            var devices = [];

                            function impAdr()
                            {
                                writeToSelectedPrinter('ï»¿CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT33,46^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->lastname); ?> <?php echo str_replace("'", "\'", $adresseEC->firstname); ?>^FS^CI27^FT33,141^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address1); ?>^FS^CI27^FT33,236^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address2); ?>^FS^CI27^FT33,331^A0N,34,33^FH\^CI28^FD<?php echo $adresseEC->postcode; ?>^FS^CI27^FT156,331^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->city); ?>^FS^CI27^PQ1,0,1,Y^XZ');
                                console.log('ï»¿CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT33,46^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->lastname); ?> <?php echo str_replace("'", "\'", $adresseEC->firstname); ?>^FS^CI27^FT33,141^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address1); ?>^FS^CI27^FT33,236^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address2); ?>^FS^CI27^FT33,331^A0N,34,33^FH\^CI28^FD<?php echo $adresseEC->postcode; ?>^FS^CI27^FT156,331^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->city); ?>^FS^CI27^PQ1,0,1,Y^XZ');
                                /*$.ajax({
                                    method: "POST",
                                    url: "/LogiGraine/valideTransport.php",
                                    data: {id_order: <?php echo $orderEC; ?>},
                                    success :function(data) {
                                        //location.reload();
                                        //console.log(data);
                                    }
                                });*/
                            }

                            function setup()
                            {
                                //Get the default device from the application as a first step. Discovery takes longer to complete.
                                BrowserPrint.getDefaultDevice("printer", function(device)
                                {
                            
                                    //Add device to list of devices and to html select element
                                    selected_device = device;
                                    devices.push(device);
                                    var html_select = document.getElementById("selected_device");
                                    var option = document.createElement("option");
                                    option.text = device.name;
                                    html_select.add(option);
                                    
                                    //Discover any other devices available to the application
                                    BrowserPrint.getLocalDevices(function(device_list){
                                        for(var i = 0; i < device_list.length; i++)
                                        {
                                            //Add device to list of devices and to html select element
                                            var device = device_list[i];
                                            if(!selected_device || device.uid != selected_device.uid)
                                            {
                                                devices.push(device);
                                                var option = document.createElement("option");
                                                option.text = device.name;
                                                option.value = device.uid;
                                                html_select.add(option);
                                            }
                                        }
                                        
                                    }, function(){alert("Error getting local devices")},"printer");
                                    
                                }, function(error){
                                    //alert('Imprimante non trouvée');
                                })
                            }
                            function getConfig(){
                                BrowserPrint.getApplicationConfiguration(function(config){
                                    alert(JSON.stringify(config))
                                }, function(error){
                                    alert(JSON.stringify(new BrowserPrint.ApplicationConfiguration()));
                                })
                            }
                            function writeToSelectedPrinter(dataToWrite)
                            {
                                selected_device.send(dataToWrite, undefined, errorCallback);
                            }
                            var readCallback = function(readData) {
                                if(readData === undefined || readData === null || readData === "")
                                {
                                    alert("No Response from Device");
                                }
                                else
                                {
                                    alert(readData);
                                }
                                
                            }
                            var errorCallback = function(errorMessage){
                                alert("Error: " + errorMessage);	
                            }
                            function readFromSelectedPrinter()
                            {

                                selected_device.read(readCallback, errorCallback);
                                
                            }
                            function getDeviceCallback(deviceList)
                            {
                                alert("Devices: \n" + JSON.stringify(deviceList, null, 4))
                            }

                            function sendImage(imageUrl)
                            {
                                url = window.location.href.substring(0, window.location.href.lastIndexOf("/"));
                                url = url + "/" + imageUrl;
                                selected_device.convertAndSendFile(url, undefined, errorCallback)
                            }
                            function sendFile(fileUrl){
                                url = window.location.href.substring(0, window.location.href.lastIndexOf("/"));
                                url = url + "/" + fileUrl;
                                selected_device.sendFile(url, undefined, errorCallback)
                            }
                            function onDeviceSelected(selected)
                            {
                                for(var i = 0; i < devices.length; ++i){
                                    if(selected.value == devices[i].uid)
                                    {
                                        selected_device = devices[i];
                                        return;
                                    }
                                }
                            }
                            window.onload = setup;
                            $( document ).ready(function() {
                                $('#myModalSucces .lien').attr('onclick', 'impAdr();document.location.href=\'/LogiGraine/modules/commandes.php\'');
                                $('#myModalSucces .modal-title').html('Commande complète');
                                $('#myModalSucces .modal-body').html('Impression de l\'étiquette adresse');
                                $('#myModalSucces').modal('show'); 
                            });
                            </script>
                        <?php include('footer.php'); ?>
  </body>
</html>