<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(6, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Rangement pommes de terre réserve';
    include('../top.php'); 
    include('../header.php'); 
?>
    <div class="container">
        <?php 
            if ( !isset($_POST['codeLecture']) || empty($_POST['codeLecture']) )
            {
        ?>
        <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/accueil.php'"></i> <?php echo $title; ?></h2>
        <form class="form-rangement" method="POST">
            <?php
                echo '<div class="mireLecture">';
                echo '<input type="text" id="codeLecture" name="codeLecture" class="form-control" placeholder="Scannez l\'emplacement" required autofocus>';
                echo '</div>';
                echo '</form>';
                }
                if ( isset($_POST['codeLecture']) && !empty($_POST['codeLecture']) )
                {
                    ?>
                    <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/modules/rangement_pdt.php'"></i> <?php echo $title; ?></h2>
                    <?php
                    $emplacement = $_POST['codeLecture'];
                    echo '<h3>'.$emplacement.'</h3>';                   
                    echo '<input type="hidden" id="emplacement" value="'.$emplacement.'" />';
                    echo '<div class="mireEan">';                        
                        echo '<form name="addEan" id="addEan">';
                        echo '<input type="text" id="codeLectureEan" name="codeLectureEan" class="form-control" placeholder="Scannez le produit" required autofocus>';
                        echo '</form><br />';
                        echo '<div id="rangementEC">';
                        $req = 'SELECT * FROM ps_LogiGraine_rangement_pdt WHERE emplacement = "'.$_POST['codeLecture'].'";';
                        foreach ( Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req) as $rangee )
                        {
                            $prodEC = new Produit($rangee['ean']);
                            echo '<div class="ligne'.$rangee['id_rangement_pdt'].'"><b>'.$rangee['quantity'] . ' x </b>' . $prodEC->nom . ' / ' . $prodEC->declinaison.'&nbsp;<i class="fa-solid fa-trash" aria-hidden="true" idR="'.$rangee['id_rangement_pdt'].'" idPA="'.$rangee['id_product_attribute'].'"></i><br /><br /></div>';
                        }
                        echo '</div>';
                        echo '<button type="button" class="btn btn-default btnPrint" data-dismiss="modal" style="padding: 20px;"><i class="fa-solid fa-print" aria-hidden="true"></i> Imprimer</button>';
                    echo '</div>';
                }
            ?>
    </div>
    <script type="text/javascript" src="../BrowserPrint-3.1.250.min.js"></script>
    <script type="text/javascript">
        var selected_device;
        var devices = [];

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
                        alert('Imprimante non trouvée');
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
            $( ".fa-trash" ).on( "click", function() {
                $.ajax({
                    method: "POST",
                    url: "/LogiGraine/ajax_rangement_pdt_remove.php?",
                    data: {id: $(this).attr('idR'), id_product_attribute: $(this).attr('idPA')},
                    success :function(data) {  
                        $('.ligne'+data).remove();  
                    },
                    error: function(xhr){
                        alert("Erreur");
                    }
                });
            } );

            $( ".btnPrint" ).on( "click", function() {
                $.ajax({
                    method: "POST",
                    url: "/LogiGraine/ajax_rangement_pdt_print.php?",
                    data: {emplacement: $('#emplacement').val()},
                    success :function(data) {
                        var prodEC = data.split('@');
                        var nbEC = prodEC.length;
                        if ( nbEC == 1 )
                        {
                            var prod0 = prodEC[0].split('#');
                            writeToSelectedPrinter('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT0,116^A0N,51,51^FB598,1,13,C^FH\^CI28^FD'+prod0[0]+'^FS^CI27^FT0,311^A0N,51,51^FB598,1,13,C^FH\^CI28^FD'+prod0[1]+'^FS^CI27^PQ1,,,Y^XZ');
                        }
                        else if ( nbEC == 2 )
                        {
                            var prod1 = prodEC[0].split('#');
                            var prod2 = prodEC[1].split('#');
                            writeToSelectedPrinter('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT0,73^A0N,51,51^FB598,1,13,C^FH\^CI28^FD'+prod1[0]+'^FS^CI27^FT0,159^A0N,51,51^FB598,1,13,C^FH\^CI28^FD'+prod1[1]+'^FS^CI27^FT0,279^A0N,51,51^FB598,1,13,C^FH\^CI28^FD'+prod2[0]+'^FS^CI27^FT0,365^A0N,51,51^FB598,1,13,C^FH\^CI28^FD'+prod2[1]+'^FS^CI27^FO6,195^GB585,0,8^FS^PQ1,,,Y^XZ');
                        }
                        else 
                        {
                            alert('Erreur, pas plus de 2 produits différents');
                        }
                    },
                    error: function(xhr){
                        alert("Erreur");
                    }
                });
            } );
        });
        $("#addEan").submit(function(event) {
            event.preventDefault();
            
            $('#myModalQte').modal('show');
            $('#myModalQte .btnVal').attr('onclick', "stockerPdt('"+$('#emplacement').val()+"', '"+$('#codeLectureEan').val()+"');");    
            setTimeout(function(){ $('#myModalQte #qte').val(''); $('#myModalQte #qte').focus(); }, 500); 
        });
        function stockerPdt(emplacement, ean)
        {
            $.ajax({
                method: "POST",
                url: "/LogiGraine/ajax_rangement_pdt.php?",
                data: {code_ean: ean, emplacement: emplacement, qte: $('#qte').val()},
                success :function(data) {  
                    if (data != '')  
                    {
                        $('#rangementEC').append(data);       
                        $( ".fa-trash" ).on( "click", function() {
                            $.ajax({
                                method: "POST",
                                url: "/LogiGraine/ajax_rangement_pdt_remove.php?",
                                data: {id: $(this).attr('idR'), id_product_attribute: $(this).attr('idPA')},
                                success :function(data) {  
                                    $('.ligne'+data).remove();  
                                },
                                error: function(xhr){
                                    alert("Erreur");
                                }
                            });
                        } );         
                    }
                    else 
                    {
                        alert('Erreur');
                    }
                    $('#codeLectureEan').val('');  
                    $('#codeLectureEan').focus();  
                },
                error: function(xhr){
                    alert("Erreur");
                    $('#codeLectureEan').val('');  
                    $('#codeLectureEan').focus(); 
                }
            });
        }
    </script>
    <div class="modal fade qte" id="myModalQte" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Quantité stockée</h4>
                </div>
                <div class="modal-body">
                    <input type="number" id="qte" name="qte" value="" required autofocus />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btnVal" data-dismiss="modal" style="padding: 20px;">Valider</button>
                </div>
            </div>
        </div>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>