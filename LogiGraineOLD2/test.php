<?php 
require 'application_top.php';

/** TEST SI MODULE AUTORISE POUR OPERATEUR **/
$testAutorisation = LBGModule::testModuleByOperateur(2, $operateur->id);
if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
{
    header('Location: /LogiGraine/accueil.php');
}

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