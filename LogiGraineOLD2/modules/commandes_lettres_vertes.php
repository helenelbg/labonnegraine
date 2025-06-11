<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(10, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Lettres vertes';
    $liste_commandes = Commande::getCommandesLettresVertesByPda($_SESSION['pda']);
    
    if ( ( !isset($_POST['codeProduit']) || empty($_POST['codeProduit']) ) && ( !isset($_POST['codeCaisse']) || empty($_POST['codeCaisse']) ) )
    {
        include('../top.php');
        include('../header.php');
    ?>
    <div class="container">
        <?php
        if ( isset($_GET['valid']) && $_GET['valid'] == 'ok' )
        {
            ?>
            <script type="text/javascript">
                $( document ).ready(function() {
                    $('#myModalSucces .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                    $('#myModalSucces .modal-title').html('Commandes complètes');
                    $('#myModalSucces .modal-body').html('Vous allez passer aux commandes suivantes');
                    $('#myModalSucces').modal('show');
                });
            </script>
            <?php
        }
        elseif ( isset($_GET['caisse']) && $_GET['caisse'] == 'erreur' )
        {
            ?>
            <script type="text/javascript">
                $( document ).ready(function() {
                    $('#myModalErreur .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                    $('#myModalErreur .modal-title').html('Erreur');
                    $('#myModalErreur .modal-body').html("<?php echo $_GET['retour']; ?>");
                    $('#myModalErreur').modal('show');
                    $('#myModalErreur .btnFermer').attr('onclick', "$('#codeCaisse').focus();");

                    var audioElement = document.createElement("audio");
                    audioElement.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                    audioElement.setAttribute("autoplay:false", "autoplay");
                    audioElement.play();
                });
            </script>
            <?php
        }
        elseif ( isset($_GET['produit']) && $_GET['produit'] == 'erreur' )
        {
            ?>
            <script type="text/javascript">
                $( document ).ready(function() {
                    $('#myModalErreur .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                    $('#myModalErreur .modal-title').html('Erreur');
                    $('#myModalErreur .modal-body').html("<?php echo $_GET['retour']; ?>");
                    $('#myModalErreur').modal('show');
                    $('#myModalErreur .btnFermer').attr('onclick', "$('#codeProduit').focus();");

                    var audioElement2 = document.createElement("audio");
                    audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                    audioElement2.setAttribute("autoplay:false", "autoplay");
                    audioElement2.play();
                });
            </script>
            <?php
        }
        elseif ( ( isset($_GET['lettreverte']) && $_GET['lettreverte'] == 'ok' ) || ( isset($_POST['codeCaisseEtiquette']) && !empty($_POST['codeCaisseEtiquette']) ) )
        {           
            if ( isset($_POST['codeCaisseEtiquette']) && !empty($_POST['codeCaisseEtiquette']) ) 
            {
                $req_cc = 'SELECT c.id_order FROM ps_LogiGraine_controle c LEFT JOIN ps_LogiGraine_caisse c2 ON c.id_caisse = c2.id_caisse WHERE c.valide = 1 AND transport = 0 AND c2.code_caisse = "'.$_POST['codeCaisseEtiquette'].'";';
				$resu_cc = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_cc);
                //print_r($resu_cc);
                //die;
                if ( isset($resu_cc[0]['id_order']) && !empty($resu_cc[0]['id_order']) )
                {
                    $_GET['ctrl'] = $resu_cc[0]['id_order'];
                }
                else 
                {
                    header('Location:/LogiGraine/modules/commandes_lettres_vertes.php?caisse=erreur&retour=Commande non trouvée');
                }
            }
            echo '$_GET[ctrl] : '.$_GET['ctrl'];
            $controleECLV = new Controle($_GET['ctrl']);
            echo '<pre>';
            print_r($controleECLV );
            echo '</pre>';
            $commandeECLV = new Order($controleECLV->id_order);

            echo '$controleECLV->id_order : '.$controleECLV->id_order;
            $controleECLV->validateLettreVerte();

            echo '<select id="selected_device" style="visibility:hidden;" onchange=onDeviceSelected(this);></select>';
            $adresseEC = new Address($commandeECLV->id_address_delivery);
            ?>
            <script type="text/javascript" src="../BrowserPrint-3.1.250.min.js"></script>
            <script type="text/javascript">

            var selected_device;
            var devices = [];

            function impAdr()
            {
                writeToSelectedPrinter('ï»¿CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT33,46^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->lastname); ?> <?php echo str_replace("'", "\'", $adresseEC->firstname); ?>^FS^CI27^FT33,141^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address1); ?>^FS^CI27^FT33,236^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address2); ?>^FS^CI27^FT33,331^A0N,34,33^FH\^CI28^FD<?php echo $adresseEC->postcode; ?>^FS^CI27^FT156,331^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->city); ?>^FS^CI27^PQ1,0,1,Y^XZ');

                $.ajax({
                    method: "POST",
                    url: "/LogiGraine/valideTransport.php",
                    data: {id_order: <?php echo $controleECLV->id_order; ?>},
                    success :function(data) {
                        //location.reload();
                        //console.log(data);
                    }
                });
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
                /*$('#myModalSucces .lien').attr('onclick', 'impAdr();document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                $('#myModalSucces .modal-title').html('Commande complète');
                $('#myModalSucces .modal-body').html('Impression de l\'étiquette adresse');
                $('#myModalSucces').modal('show');*/
                impAdr();
                //console.log('la');
                document.location.href='/LogiGraine/modules/commandes_lettres_vertes.php';
            });
            </script>
            <?php
        }
    }

            // on regarde si on a des commandes lettres vertes valide = 1 et transport = 0 
            $liste_commandes_etiquettes = Commande::getCommandesLettresVertesByPdaForEtiquettes($_SESSION['pda']);
            if ( count($liste_commandes_etiquettes) > 0 )
            {
                echo '<div class="mireCaisse">';
                echo '<div class="choixCaisse">Impression étiquettes</div>';
                echo '<form class="form-caisse" action="/LogiGraine/modules/commandes_lettres_vertes.php" method="POST">';
                echo '<input type="text" id="codeCaisseEtiquette" onblur="focus();" name="codeCaisseEtiquette" class="form-control" placeholder="Scannez la caisse" required autofocus>';
                echo '</form>';
                echo '</div>';
            }
            elseif ( count($liste_commandes) > 0 )
            {
                if ( strpos($liste_commandes[0], '_') > 0 )
                {
                    // il s'agit d'un groupe de commande à traiter en même temps
                    // 1 - on vérifie si les boites sont attribuées
                    $splitCmd = explode('_', $liste_commandes[0]);
                    $groupCaisse = 0;
                    foreach($splitCmd as $uniqueCmd)
                    {
                        $controleEC = new Controle($uniqueCmd);
                        if ( $controleEC->id_caisse == 0 )
                        {
                            if ( isset($_POST['codeProduit']) && !empty($_POST['codeProduit']) )
                            {
                                unset($_POST['codeProduit']);
                            }
                        }
                        else
                        {
                            $groupCaisse++;
                        }
                    }

                    // 2 - on attribue la caisse scannée à la bonne commande
                    if ( isset($_POST['codeCaisse']) && !empty($_POST['codeCaisse']) )
                    {
                        $controleECTmp = new Controle($splitCmd[$groupCaisse]);
                        $tailleAPrendre = Caisse::getTailleCaisseByCommande($controleECTmp->id_order);
                        if ( ($retourCaisse = $controleECTmp->scanCaisse($_POST['codeCaisse'], $tailleAPrendre)) !== true )
                        {                            
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?caisse=erreur&retour='.$retourCaisse);
                            /*?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalErreur .modal-title').html('Erreur');
                                    $('#myModalErreur .modal-body').html("<?php echo $retourCaisse; ?>");
                                    $('#myModalErreur').modal('show');
                                    $('#myModalErreur .btnFermer').attr('onclick', "$('#codeCaisse').focus();");

                                    var audioElement = document.createElement("audio");
                                    audioElement.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                    audioElement.setAttribute("autoplay:false", "autoplay");
                                    audioElement.play();
                                });
                            </script>
                            <?php*/
                        }
                        else
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php');
                            $groupCaisse++;
                        }
                    }
                    else if ( isset($_POST['codeProduit']) && !empty($_POST['codeProduit']) && isset($_POST['orderEC']) && !empty($_POST['orderEC']) )
                    {
                        $req_dd = 'SELECT date_debut FROM ps_LogiGraine_controle WHERE id_order = "'.$_POST['orderEC'].'";';
				        $resu_dd = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_dd);
                        if ( $resu_dd[0]['date_debut'] == '0000-00-00 00:00:00' )
                        {
                            $req_dd2 = 'UPDATE ps_LogiGraine_controle SET date_debut = NOW() WHERE id_order = "'.$_POST['orderEC'].'";';
				            $resu_dd2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_dd2);
                        }

                        if ( ($retourProduit = $controleEC->scanProduitGroup($_POST['codeProduit'],$_POST['orderEC'])) !== true )
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?produit=erreur&retour='.$retourProduit);
                            /*?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalErreur .modal-title').html('Erreur');
                                    $('#myModalErreur .modal-body').html("<?php echo $retourProduit; ?>");
                                    $('#myModalErreur').modal('show');
                                    $('#myModalErreur .btnFermer').attr('onclick', "$('#codeProduit').focus();");

                                    var audioElement2 = document.createElement("audio");
                                    audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                    audioElement2.setAttribute("autoplay:false", "autoplay");
                                    audioElement2.play();
                                });
                            </script>
                            <?php*/
                        }
                        else 
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php');
                        }
                    }

                    // on vérifie que toutes les commandes sont bien à traiter
                    if ( $groupCaisse == 0 )
                    {
                        $exeOK = true;
                        foreach($splitCmd as $uniqueCmd2)
                        {
                            // Vérification que la commande est bien en statut en cours paiement accepté
                            $req_v = 'SELECT current_state FROM ps_orders WHERE id_order = "'.$uniqueCmd2.'";';
                            $resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);
                            if ( $resu_v[0]['current_state'] != 2 && $resu_v[0]['current_state'] != 18 )
                            {
                                $exeOK = false;
                            }
                        }
                        if ( $exeOK == false )
                        {
                            // on supprime le lot de commande du pda
                            $reqd1 = 'DELETE FROM ps_LogiGraine_pda_order WHERE id_order = "'.$liste_commandes[0].'";';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd1);
        
                            $reqd2 = 'DELETE FROM ps_LogiGraine_controle WHERE id_order IN ('.implode(',', $splitCmd).');';
			                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd2);

                            $reqd3 = 'DELETE FROM ps_LogiGraine_controle_produit_ordre WHERE id_order IN ('.implode(',', $splitCmd).');';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd3);
                            
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php');
                        }
                    }

                    $libelle_caisses = '';
                    $tableau_caisses = array();
                    foreach($splitCmd as $uniqueCmd)
                    {
                        $controleECTmp = new Controle($uniqueCmd);
                        if ( $controleECTmp->id_caisse != 0 )
                        {
                            $caisseECTmp = new Caisse($controleECTmp->id_caisse);
                            $tableau_caisses[] = $caisseECTmp->code;
                            if ( !empty($libelle_caisses) )
                            {
                                $libelle_caisses .= '_';
                            }
                            $libelle_caisses .= $caisseECTmp->code;
                        }
                    }
                    $produitsEC = Commande::getProductsByOrderGroup($liste_commandes[0]);
                    $totalEC = 0;
                    foreach($produitsEC as $produitEC)
                    {
                        $totalEC += $produitEC['quantity_final'];
                    }

                    echo '<div class="row topCmd">';
                    echo '<div class="col-xs-9 col-md-9 align-left" style="padding-right: 0;"><i class="fa-solid fa-globe"></i>'.implode(' ', explode('_', $liste_commandes[0])).'</div>';
                    echo '<div class="col-xs-3 col-md-3 align-right" style="padding-left: 0;"><span id="compteurProduits">'.Controle::getNbControleECGroup($liste_commandes[0]).'</span>/'.$totalEC.'<i class="fa-solid fa-cart-shopping"></i></div>';
                    echo '<div class="col-xs-4 col-md-4 align-left" style="padding-right: 0;"><i class="fa-solid fa-location-dot"></i>Graines</div>';
                    echo '<div class="col-xs-8 col-md-8 align-right" style="padding-left: 0;">'.implode(' ', explode('_', $libelle_caisses)).'<i class="fa-solid fa-box-archive"></i></div>';
                    echo '</div>';

                    if ( $groupCaisse != count($splitCmd) )
                    {
                        $tailleAPrendre = Caisse::getTailleCaisseByCommande($splitCmd[$groupCaisse]);
                        if ( $tailleAPrendre == 100 )
                        {
                            $valeurTaille = 'petite';
                            $couleurTaille = 'rouge';
                        }
                        elseif ( $tailleAPrendre == 200 )
                        {
                            $valeurTaille = 'moyenne';
                            $couleurTaille = 'verte';
                        }
                        elseif ( $tailleAPrendre == 400 )
                        {
                            $valeurTaille = 'grande';
                            $couleurTaille = 'noire';
                        }
                        echo '<div class="mireCaisse">';
                        echo '<div class="choixCaisse sur_'.$couleurTaille.'">Position '.($groupCaisse+1).' > Prenez une '.$valeurTaille.' caisse '.$couleurTaille.'</div>';
                        echo '<form class="form-caisse" method="POST">';
                        echo '<input type="text" id="codeCaisse" onblur="focus();" name="codeCaisse" class="form-control" placeholder="Scannez la caisse" required autofocus>';
                        //echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    else
                    {
                        $fin = 1;
                        foreach($produitsEC as $produitEC)
                        {
                            $quantite_scan = 0;
                            $cpEC = ControleProduit::checkGroup($produitEC['id_order'], $produitEC['product_id'], $produitEC['product_attribute_id']);
                            if ( $cpEC !== false )
                            {
                                // il y a un contrôle de débuté ou de terminé sur ce produit
                                $quantite_scan = $cpEC['quantite_prepare'];
                            }
                            if ( $quantite_scan == $produitEC['quantity_final'] )
                            {
                                continue;
                            }
                            $fin = 0;
                            echo '<div class="mireProduit">';
                            echo '<div class="product_name">'.$produitEC['location'].'</div>';
                            echo '<div class="product_ref" lbg="'.$produitEC['product_ean13'].'">'.$produitEC['product_reference'].'</div>';
                            echo '<div class="product_name">'.$produitEC['product_name_1'].'</div>';
                            $class_decli = 'sur_rouge';
                            if ( $produitEC['default_on'] == 1 )
                            {
                                $class_decli = 'noir';
                            }
                            echo '<div class="product_name '.$class_decli.'">'.$produitEC['product_name_2'].'</div>';
                            $class_qt = 'noir';
                            if ( $produitEC['quantity_final'] > 1 )
                            {
                                $class_qt = 'sur_rouge';
                            }
                            echo '<div class="product_qt '.$class_qt.'">'.$quantite_scan.'/'.$produitEC['quantity_final'].'</div>';
                            echo '<form class="form-produit" id="form-produit-multi" method="POST">';
                            echo '<input type="hidden" name="orderEC" value="'.$produitEC['id_order'].'" />';
                            echo '<input type="text" id="codeProduit" data-produit="'.$produitEC['product_ean13'].'" onblur="if ( this.value == \'\' ) focus();" name="codeProduit" class="form-control" placeholder="Scannez le produit" required autofocus>';
                            //echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';

                            $position_a_mettre = array_search($produitEC['id_order'], $splitCmd);
                            $caisse_attendue = $tableau_caisses[$position_a_mettre];

                            //echo $caisse_attendue;
                            //echo '<div class="product_ref">Caisse '.($position_a_mettre+1).' ('.$caisse_attendue.')</div>';
                            $array_bg_caisse = array('1' => '#8bc34a', '2' => '#03a9f4', '3' => '#ffeb3b', '4' => '#ff9800', '5' => '#9e9e9e');
                            $array_lettre_caisse = array('1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E');
                            if ( ($position_a_mettre+1) <= 6 )
                            {
                                $positionEtage = ($position_a_mettre+1);
                            }
                            elseif ( ($position_a_mettre+1) <= 12 )
                            {
                                $positionEtage = ($position_a_mettre+1)-6;
                            }
                            elseif ( ($position_a_mettre+1) <= 18 )
                            {
                                $positionEtage = ($position_a_mettre+1)-12;
                            }
                            elseif ( ($position_a_mettre+1) <= 24 )
                            {
                                $positionEtage = ($position_a_mettre+1)-18;
                            }
                            elseif ( ($position_a_mettre+1) <= 30 )
                            {
                                $positionEtage = ($position_a_mettre+1)-24;
                            }
                            echo '<div class="product_ref" style="background-color: '.$array_bg_caisse[ceil(($position_a_mettre+1)/6)].'">Caisse '.$array_lettre_caisse[ceil(($position_a_mettre+1)/6)].($position_a_mettre+1).'</div>';

                            echo '<input type="text" id="codeCaisseCheck" data-caisse="'.$caisse_attendue.'" name="codeCaisseCheck" class="form-control" placeholder="Scannez la caisse '.($position_a_mettre+1).'">';

                            echo '</form>';
                            echo '</div>';
                            break;
                        }
                        if ( $fin == 1 )
                        {
                            foreach($splitCmd as $uniqueCmd)
                            {
                              $controleECTmpV = new Controle($uniqueCmd);
                              $controleECTmpV->validate();
                            }
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?valid=ok');
                            /*$commandeEC = new Order($controleEC->id_order);
                            $carrierEC = new Carrier($commandeEC->id_carrier);
                            if ( $carrierEC->id_reference == 342 ) // Lettre Verte
                            {
                                $controleEC->validateLettreVerte();
                                error_log('LETTRE VERTE');
                                echo '<select id="selected_device" style="visibility:hidden;" onchange=onDeviceSelected(this);></select>';
                                $adresseEC = new Address($commandeEC->id_address_delivery);
                                ?>
                                <script type="text/javascript" src="../BrowserPrint-3.1.250.min.js"></script>
                                <script type="text/javascript">

                                var selected_device;
                                var devices = [];

                                function impAdr()
                                {
                                    writeToSelectedPrinter('ï»¿CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT33,46^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->lastname); ?> <?php echo str_replace("'", "\'", $adresseEC->firstname); ?>^FS^CI27^FT33,141^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address1); ?>^FS^CI27^FT33,236^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address2); ?>^FS^CI27^FT33,331^A0N,34,33^FH\^CI28^FD<?php echo $adresseEC->postcode; ?>^FS^CI27^FT156,331^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->city); ?>^FS^CI27^PQ1,0,1,Y^XZ');

                                    $.ajax({
                                        method: "POST",
                                        url: "/LogiGraine/valideTransport.php",
                                        data: {id_order: <?php echo $controleEC->id_order; ?>},
                                        success :function(data) {
                                            //location.reload();
                                            //console.log(data);
                                        }
                                    });
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
                                    $('#myModalSucces .lien').attr('onclick', 'impAdr();document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                                    $('#myModalSucces .modal-title').html('Commande complète');
                                    $('#myModalSucces .modal-body').html('Impression de l\'étiquette adresse');
                                    $('#myModalSucces').modal('show');
                                });
                                </script>
                                <?php
                            }
                            else
                            {*/
                            //error_log('VALIDATE');
                            /*?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalSucces .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                                    $('#myModalSucces .modal-title').html('Commandes complètes');
                                    $('#myModalSucces .modal-body').html('Vous allez passer aux commandes suivantes');
                                    $('#myModalSucces').modal('show');
                                });
                            </script>
                            <?php*/
                            //}
                        }
                    }
                }
                else // Commande simple
                {
                    $controleEC = new Controle($liste_commandes[0]);

                    if ( $controleEC->id_caisse == 0 )
                    {
                        if ( isset($_POST['codeProduit']) && !empty($_POST['codeProduit']) )
                        {
                            unset($_POST['codeProduit']);
                        }
                    }

                    if ( isset($_POST['codeCaisse']) && !empty($_POST['codeCaisse']) )
                    {
                        $tailleAPrendre = Caisse::getTailleCaisseByCommande($controleEC->id_order);
                        if ( ($retourCaisse = $controleEC->scanCaisse($_POST['codeCaisse'], $tailleAPrendre)) !== true )
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?caisse=erreur&retour='.$retourCaisse);
                            /*?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalErreur .modal-title').html('Erreur');
                                    $('#myModalErreur .modal-body').html("<?php echo $retourCaisse; ?>");
                                    $('#myModalErreur').modal('show');
                                    $('#myModalErreur .btnFermer').attr('onclick', "$('#codeCaisse').focus();");

                                    var audioElement = document.createElement("audio");
                                    audioElement.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                    audioElement.setAttribute("autoplay:false", "autoplay");
                                    audioElement.play();
                                });
                            </script>
                            <?php*/
                        }
                        else 
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php');
                        }
                    }
                    else if ( isset($_POST['codeProduit']) && !empty($_POST['codeProduit']) )
                    {
                        $req_dd = 'SELECT date_debut FROM ps_LogiGraine_controle WHERE id_order = "'.$controleEC->id_order.'";';
				        $resu_dd = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_dd);
                        if ( $resu_dd[0]['date_debut'] == '0000-00-00 00:00:00' )
                        {
                            $req_dd2 = 'UPDATE ps_LogiGraine_controle SET date_debut = NOW() WHERE id_order = "'.$controleEC->id_order.'";';
				            $resu_dd2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_dd2);
                        }

                        if ( ($retourProduit = $controleEC->scanProduit($_POST['codeProduit'])) !== true )
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?produit=erreur&retour='.$retourProduit);
                            /*?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalErreur .modal-title').html('Erreur');
                                    $('#myModalErreur .modal-body').html("<?php echo $retourProduit; ?>");
                                    $('#myModalErreur').modal('show');
                                    $('#myModalErreur .btnFermer').attr('onclick', "$('#codeProduit').focus();");

                                    var audioElement2 = document.createElement("audio");
                                    audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                    audioElement2.setAttribute("autoplay:false", "autoplay");
                                    audioElement2.play();
                                });
                            </script>
                            <?php*/
                        }
                        else 
                        {
                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php');
                        }
                    }
                    // on vérifie que toutes les commandes sont bien à traiter
                    if ( $controleEC->id_caisse == 0 )
                    {
                        $exeOK = true;
                        // Vérification que la commande est bien en statut en cours paiement accepté
                        $req_v = 'SELECT current_state FROM ps_orders WHERE id_order = "'.$controleEC->id_order.'";';
                        $resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);
                        if ( $resu_v[0]['current_state'] != 2 && $resu_v[0]['current_state'] != 18 )
                        {
                            $exeOK = false;
                        }
                        if ( $exeOK == false )
                        {
                            // on supprime le lot de commande du pda
                            $reqd1 = 'DELETE FROM ps_LogiGraine_pda_order WHERE id_order = "'.$controleEC->id_order.'";';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd1);
        
                            $reqd2 = 'DELETE FROM ps_LogiGraine_controle WHERE id_order = "'.$controleEC->id_order.'";';
			                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd2);

                            $reqd3 = 'DELETE FROM ps_LogiGraine_controle_produit_ordre WHERE id_order = "'.$controleEC->id_order.'";';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd3);

                            header('Location: /LogiGraine/modules/commandes_lettres_vertes.php');
                        }
                    }

                    $caisseEC = new Caisse($controleEC->id_caisse);

                    $produitsEC = Commande::getProductsByOrder($controleEC->id_order);
                    $totalEC = 0;
                    foreach($produitsEC as $produitEC)
                    {
                        $totalEC += $produitEC['quantity_final'];
                    }

                    echo '<div class="row topCmd">';
                    echo '<div class="col-xs-6 col-md-6 align-left"><i class="fa-solid fa-globe"></i>#'.$controleEC->id_order.'</div>';
                    echo '<div class="col-xs-6 col-md-6 align-right"><span id="compteurProduits">'.$controleEC->getNbControleEC().'</span>/'.$totalEC.'<i class="fa-solid fa-cart-shopping"></i></div>';
                    echo '<div class="col-xs-6 col-md-6 align-left"><i class="fa-solid fa-location-dot"></i>'.$controleEC->zone.'</div>';
                    echo '<div class="col-xs-6 col-md-6 align-right">'.$caisseEC->code.'<i class="fa-solid fa-box-archive"></i></div>';
                    echo '</div>';

                    if ( $controleEC->id_caisse == 0 )
                    {
                        $tailleAPrendre = Caisse::getTailleCaisseByCommande($controleEC->id_order);
                        if ( $tailleAPrendre == 100 )
                        {
                            $valeurTaille = 'petite';
                            $couleurTaille = 'rouge';
                        }
                        elseif ( $tailleAPrendre == 200 )
                        {
                            $valeurTaille = 'moyenne';
                            $couleurTaille = 'verte';
                        }
                        elseif ( $tailleAPrendre == 400 )
                        {
                            $valeurTaille = 'grande';
                            $couleurTaille = 'noire';
                        }
                        echo '<div class="mireCaisse">';
                        echo '<div class="choixCaisse sur_'.$couleurTaille.'">Prenez une '.$valeurTaille.' caisse '.$couleurTaille.'</div>';
                        echo '<form class="form-caisse" method="POST">';
                        echo '<input type="text" id="codeCaisse" onblur="focus();" name="codeCaisse" class="form-control" placeholder="Scannez la caisse" required autofocus>';
                        //echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    else
                    {
                        $fin = 1;
                        foreach($produitsEC as $produitEC)
                        {
                            $quantite_scan = 0;
                            $cpEC = ControleProduit::check($controleEC->id, $produitEC['product_id'], $produitEC['product_attribute_id']);
                            if ( $cpEC !== false )
                            {
                                // il y a un contrôle de débuté ou de terminé sur ce produit
                                $quantite_scan = $cpEC['quantite_prepare'];
                            }
                            if ( $quantite_scan == $produitEC['quantity_final'] )
                            {
                                continue;
                            }
                            $fin = 0;
                            echo '<div class="mireProduit">';
                            echo '<div class="product_ref" lbg="'.$produitEC['product_ean13'].'">'.$produitEC['product_reference'].'</div>';
                            echo '<div class="product_name">'.$produitEC['product_name_1'].'</div>';
                            $class_decli = 'sur_rouge';
                            if ( $produitEC['default_on'] == 1 )
                            {
                                $class_decli = 'noir';
                            }
                            echo '<div class="product_name '.$class_decli.'">'.$produitEC['product_name_2'].'</div>';
                            $class_qt = 'noir';
                            if ( $produitEC['quantity_final'] > 1 )
                            {
                                $class_qt = 'sur_rouge';
                            }
                            echo '<div class="product_qt '.$class_qt.'">'.$quantite_scan.'/'.$produitEC['quantity_final'].'</div>';
                            echo '<form class="form-produit" id="form-produit" method="POST">';
                            echo '<input type="text" id="codeProduit" data-produit="'.$produitEC['product_ean13'].'" onblur="focus();" name="codeProduit" class="form-control" placeholder="Scannez le produit" required autofocus>';
                            //echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';
                            echo '</form>';
                            echo '</div>';
                            break;
                        }
                        if ( $fin == 1 )
                        {
                            $controleEC->validate();
                            $commandeEC = new Order($controleEC->id_order);
                            $carrierEC = new Carrier($commandeEC->id_carrier);
                            if ( $carrierEC->id_reference == 342 ) // Lettre Verte
                            {
                                $controleEC->validateLettreVerte();
                                error_log('LETTRE VERTE');
                                
                                header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?lettreverte=ok&ctrl='.$liste_commandes[0]);

                                /*echo '<select id="selected_device" style="visibility:hidden;" onchange=onDeviceSelected(this);></select>';
                                $adresseEC = new Address($commandeEC->id_address_delivery);
                                ?>
                                <script type="text/javascript" src="../BrowserPrint-3.1.250.min.js"></script>
                                <script type="text/javascript">

                                var selected_device;
                                var devices = [];

                                function impAdr()
                                {
                                    writeToSelectedPrinter('ï»¿CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT33,46^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->lastname); ?> <?php echo str_replace("'", "\'", $adresseEC->firstname); ?>^FS^CI27^FT33,141^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address1); ?>^FS^CI27^FT33,236^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->address2); ?>^FS^CI27^FT33,331^A0N,34,33^FH\^CI28^FD<?php echo $adresseEC->postcode; ?>^FS^CI27^FT156,331^A0N,34,33^FH\^CI28^FD<?php echo str_replace("'", "\'", $adresseEC->city); ?>^FS^CI27^PQ1,0,1,Y^XZ');

                                    $.ajax({
                                        method: "POST",
                                        url: "/LogiGraine/valideTransport.php",
                                        data: {id_order: <?php echo $controleEC->id_order; ?>},
                                        success :function(data) {
                                            //location.reload();
                                            //console.log(data);
                                        }
                                    });
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
                                    $('#myModalSucces .lien').attr('onclick', 'impAdr();document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                                    $('#myModalSucces .modal-title').html('Commande complète');
                                    $('#myModalSucces .modal-body').html('Impression de l\'étiquette adresse');
                                    $('#myModalSucces').modal('show');
                                });
                                </script>
                                <?php*/
                            }
                            else
                            {
                                header('Location: /LogiGraine/modules/commandes_lettres_vertes.php?valid=ok');
                                /*?>
                                <script type="text/javascript">
                                    $( document ).ready(function() {
                                        $('#myModalSucces .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/commandes_lettres_vertes.php\'');
                                        $('#myModalSucces .modal-title').html('Commande complète');
                                        $('#myModalSucces .modal-body').html('Vous allez passer à la commande suivante');
                                        $('#myModalSucces').modal('show');
                                    });
                                </script>
                                <?php*/
                            }
                        }
                    }
                }
            }
            else
            {
                echo '<h3 class="heading">Aucune commande à traiter</h3>';
            }
        ?>
    </div>
    <?php include('../footer.php'); ?>

    <script type="text/javascript">
        $('#form-produit-multi #codeProduit').keypress(function (e) {
          var keyCode = e.keyCode ? e.keyCode : e.which;
          if (keyCode == 13)
          {
            e.preventDefault();
            //$('form#login').submit();

            if ( $('#form-produit-multi #codeProduit').val() == "" )
            {
                //console.log('1');
                $('#form-produit-multi #codeProduit').focus();
            }
            else if ( $('#form-produit-multi #codeProduit').val() != $('#form-produit-multi #codeProduit').attr('data-produit') )
            {
                $('#myModalErreur .modal-title').html('Erreur');
                $('#myModalErreur .modal-body').html("Mauvais produit scanné");
                $('#myModalErreur').modal('show');
                $('#myModalErreur .btnFermer').attr('onclick', "$('#form-produit-multi #codeProduit').val('');$('#form-produit-multi #codeProduit').focus();");

                var audioElement2 = document.createElement("audio");
                audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                audioElement2.setAttribute("autoplay:false", "autoplay");
                audioElement2.play();
            }
            else if ( $('#form-produit-multi #codeCaisseCheck').val() == "" )
            {
                //console.log('2');
                $('#form-produit-multi #codeCaisseCheck').focus();
            }

            return false;
          }
        });

        $('#form-produit-multi #codeCaisseCheck').keypress(function (e) {
          var keyCode = e.keyCode ? e.keyCode : e.which;
          if (keyCode == 13)
          {
            e.preventDefault();
            //$('form#login').submit();

            if ( $('#form-produit-multi #codeCaisseCheck').val() != "" && $('#form-produit-multi #codeCaisseCheck').val() != $('#form-produit-multi #codeCaisseCheck').attr('data-caisse') )
            {
                //console.log('3');
                //alert('erreur de caisse');
                $('#myModalErreur .modal-title').html('Erreur');
                $('#myModalErreur .modal-body').html("Mauvaise caisse scannée");
                $('#myModalErreur').modal('show');
                $('#myModalErreur .btnFermer').attr('onclick', "$('#form-produit-multi #codeCaisseCheck').focus();");

                var audioElement2 = document.createElement("audio");
                audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                audioElement2.setAttribute("autoplay:false", "autoplay");
                audioElement2.play();
            }
            else if ( $('#form-produit-multi #codeCaisseCheck').val() == "" )
            {
                $('#myModalErreur .modal-title').html('Erreur');
                $('#myModalErreur .modal-body').html("Veuillez scanner une caisse");
                $('#myModalErreur').modal('show');
                $('#myModalErreur .btnFermer').attr('onclick', "$('#form-produit-multi #codeCaisseCheck').focus();");

                var audioElement2 = document.createElement("audio");
                audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                audioElement2.setAttribute("autoplay:false", "autoplay");
                audioElement2.play();
                //alert('veuillez scanner une caisse');
            }
            else
            {
                //console.log('4');
                $('#form-produit-multi').submit();
            }

            return false;
          }
        });

        $('#form-produit').submit(function (evt) {
            //console.log('-1');
            //evt.preventDefault();
            //console.log('0');
            if ( $('#form-produit #codeProduit').val() != $('#form-produit #codeProduit').attr('data-produit') )
            {
                $('#myModalErreur .modal-title').html('Erreur');
                $('#myModalErreur .modal-body').html("Mauvais produit scanné");
                $('#myModalErreur').modal('show');
                $('#myModalErreur .btnFermer').attr('onclick', "$('#form-produit #codeProduit').val('');$('#form-produit #codeProduit').focus();");

                var audioElement2 = document.createElement("audio");
                audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                audioElement2.setAttribute("autoplay:false", "autoplay");
                audioElement2.play();

                return false;
            }
            //console.log('1');
            return true;
        });
        /*$('#form-produit-multi').submit(function (evt) {
                console.log('-1');
                evt.preventDefault();
                console.log('0');

                if ( $('#form-produit-multi #codeProduit').val() == "" )
                {
                    console.log('1');
                    $('#form-produit-multi #codeProduit').focus();
                }
                else if ( $('#form-produit-multi #codeCaisseCheck').val() == "" )
                {
                    console.log('2');
                    $('#form-produit-multi #codeCaisseCheck').focus();
                }

                if ( $('#form-produit-multi #codeCaisseCheck').val() != "" && $('#form-produit-multi #codeCaisseCheck').val() != $('#form-produit-multi #codeCaisseCheck').attr('data-caisse') )
                {
                    console.log('3');
                    alert('erreur de caisse');
                }
                else
                {
                    console.log('4');
                    $('#form-produit-multi').submit();
                }
            });*/
    </script>
  </body>
</html>