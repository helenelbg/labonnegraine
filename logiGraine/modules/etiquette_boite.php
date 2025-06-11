<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(3, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Etiquette boîte';
    include('../top.php'); 
    include('../header.php'); 
?>
    <div class="container">
        <?php 
            if ( !isset($_POST['codeLecture']) || empty($_POST['codeLecture']) )
            {
        ?>
        <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/accueil.php'"></i> <?php echo $title; ?></h2>
        <?php 
            if ( isset($_GET['erreur']) )
            {
                echo '<div class="alert alert-danger" role="alert">
  Produit non trouvé
</div>';
            }
        ?>
        <form class="form-etiquette" method="POST">
            <?php
                echo '<div class="mireLecture">';
                echo '<input type="text" id="codeLecture" name="codeLecture" class="form-control" placeholder="Scannez le code-barre" required autofocus>';
                echo '</div>';
                echo '</form>';
                }
                if ( isset($_POST['codeLecture']) && !empty($_POST['codeLecture']) )
                {
                    ?>
                    <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/modules/etiquette_boite.php'"></i> <?php echo $title; ?></h2>
                    <?php
                    $produit = new Produit($_POST['codeLecture']);
                    if ( empty($produit->id) )
                    {
                        header('Location: /LogiGraine/modules/etiquette_boite.php?erreur');
                    }
                    echo '<input type="hidden" id="id_product" value="'.$produit->id.'" />';
                    if ( $_SESSION['operateur'] == 4 )
                    {
                        echo '<div class="mireProduit">';
                            echo '<div class="product_ref" lbg="'.$_POST['codeLecture'].'">'.$produit->reference.'</div>';
                            echo '<div class="product_name">'.$produit->nom.'</div>';
                            echo '<div class="product_name" style="width:50%; display:inline-block;">'.$produit->declinaison.'</div>';
                            echo '<div class="product_name" style="width:50%; display:inline-block;"><input type="number" id="quantiteBoite_'.$produit->id_declinaison.'" name="quantiteBoite_'.$produit->id_declinaison.'" affichage="'.$produit->declinaison.'" class="form-control" placeholder="Quantité '.$produit->declinaison.'" required autofocus></div>';

                            echo '<div class="div_add"><i class="fa-solid fa-add" id="displayCB" aria-hidden="true"></i></div>';
                            echo '<form name="addDecli" id="addDecli" class="hidden">';
                            echo '<input type="text" id="codeLectureAutre" name="codeLectureAutre" class="form-control" placeholder="Scannez le code-barre" required autofocus>';
                            echo '</form>';

                            echo '<select id="selected_device" style="visibility:hidden;" onchange=onDeviceSelected(this);></select> 
                                <button class="btn btn-lg btn-primary btn-block" value="Valider et imprimer l\'étiquette" onclick="print_Etiq();">Valider et imprimer</button>';
                        echo '</div>';

                        foreach($produit->autresDeclis() as $autre)
                        {
                            echo '<div class="autre'.$autre['ean'].' hidden">';
                            echo '<div class="product_name" style="width:50%; display:inline-block;">'.$autre['declinaison'].'</div>';
                            echo '<div class="product_name" style="width:50%; display:inline-block;"><input type="number" id="quantiteBoite_'.$autre['id_declinaison'].'" name="quantiteBoite_'.$autre['id_declinaison'].'" affichage="'.$autre['declinaison'].'" class="form-control" placeholder="Quantité '.$autre['declinaison'].'" required></div>';
                            echo '</div>';
                        }
                    }
                    else
                    {
                        echo '<div class="mireProduit">';
                            echo '<div class="product_ref" lbg="'.$_POST['codeLecture'].'">'.$produit->reference.'</div>';
                            echo '<div class="product_name">'.$produit->nom.'</div>';
                            echo '<div class="product_name" style="width:100%; display:inline-block;">'.$produit->declinaison.'<input value="1" style="display:none;" type="number" id="quantiteBoite_'.$produit->id_declinaison.'" name="quantiteBoite_'.$produit->id_declinaison.'" affichage="'.$produit->declinaison.'" class="form-control" placeholder="Quantité '.$produit->declinaison.'" required autofocus></div>';
                            echo '<div class="div_add"><i class="fa-solid fa-add" id="displayCB" aria-hidden="true"></i></div>';
                            echo '<form name="addDecli" id="addDecli" class="hidden">';
                            echo '<input type="text" id="codeLectureAutre" name="codeLectureAutre" class="form-control" placeholder="Scannez le code-barre" required autofocus>';
                            echo '</form>';

                            echo '<select id="selected_device" style="visibility:hidden;" onchange=onDeviceSelected(this);></select> 
                                <button class="btn btn-lg btn-primary btn-block" value="Valider et imprimer l\'étiquette" onclick="print_Etiq();">Valider et imprimer</button>';
                        echo '</div>';

                        foreach($produit->autresDeclis() as $autre)
                        {
                            echo '<div class="autre'.$autre['ean'].' hidden">';
                            echo '<div class="product_name" style="width:100%; display:inline-block;">'.$autre['declinaison'].'<input value="0" style="display:none;" type="number" id="quantiteBoite_'.$autre['id_declinaison'].'" name="quantiteBoite_'.$autre['id_declinaison'].'" affichage="'.$autre['declinaison'].'" class="form-control" placeholder="Quantité '.$autre['declinaison'].'" required></div>';
                            echo '</div>';
                        }
                    }
                }
            ?>
    </div>

<script type="text/javascript" src="../BrowserPrint-3.1.250.min.js"></script>
<script type="text/javascript">

$( "#displayCB" ).on( "click", function() {
    $("#addDecli").removeClass('hidden');
} );

$("#addDecli").submit(function(event) {
  event.preventDefault();
  $('.autre'+$('#codeLectureAutre').val()).insertBefore( ".div_add" );
  $('.autre'+$('#codeLectureAutre').val()).removeClass('hidden');
  $('.autre'+$('#codeLectureAutre').val()+' input').attr('value', 1)
  $('#codeLectureAutre').val('');
  $('.autre'+$('#codeLectureAutre').val()+' input').focus();
  $("#addDecli").addClass('hidden');
});

var selected_device;
var devices = [];

function print_Etiq()
{
    var affichage = '';
    var data_ajax = '';
    var isGraine = false;
    $( "[name^=quantiteBoite_]" ).each(function( index ) {
        var split = $(this).attr('name').split("_");
        if ( $(this).val() != '' && $(this).val() > 0 )
        {            
            //console.log(split[1] +' / '+ $(this).val() + ' / ' + $(this).attr('affichage'));
            if ( affichage != '' )
            {
                affichage += ' / ';
            }
            if ( data_ajax != '' )
            {
                data_ajax += '#';
            }
            if ( isGraine == true )
            {
                let testG = $(this).attr('affichage').indexOf(" graines");
                if ( testG > 0 )
                {
                    affichage = affichage.replace(" graines", "");
                    /*let text_decl = $(this).attr('affichage').replace(" graines", "");
                    console.log('text_decl : '+text_decl);
                    $(this).attr('affichage', text_decl);*/
                }
            }
            else 
            {
                let testG1 = $(this).attr('affichage').indexOf(" graines");
                if ( testG1 > 0 )
                {
                    isGraine = true;
                }
            }
            affichage += $(this).attr('affichage');
            data_ajax += split[1]+'_'+$(this).val();
        }
    });
    //console.log(affichage);

    $.ajax({
            method: "POST",
            url: "/LogiGraine/ajax_nouvelle_boite.php?",
            data: {id_product: $('#id_product').val(), data_ajax: data_ajax},
            success :function(data) {    
                console.log(data);     

                console.log('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^BY3,3,81^FT164,365^BCN,,Y,N^FH\^FD>:'+data+'^FS^FT0,106^A0N,102,101^FB598,1,26,C^FH\^CI28^FD<?php echo $produit->reference; ?>^FS^CI27^FT0,164^A0N,34,33^FB598,1,9,C^FH\^CI28^FD<?php echo str_replace("'", "\'", $produit->nom_limit); ?>^FS^CI27^FT0,245^A0N,73,74^FB598,1,19,C^FH\^CI28^FD'+affichage+'^FS^CI27^PQ1,0,1,Y^XZ'); 

                writeToSelectedPrinter('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^BY3,3,81^FT164,365^BCN,,Y,N^FH\^FD>:'+data+'^FS^FT0,106^A0N,102,101^FB598,1,26,C^FH\^CI28^FD<?php echo $produit->reference; ?>^FS^CI27^FT0,164^A0N,34,33^FB598,1,9,C^FH\^CI28^FD<?php echo str_replace("'", "\'", $produit->nom_limit); ?>^FS^CI27^FT0,245^A0N,73,74^FB598,1,19,C^FH\^CI28^FD'+affichage+'^FS^CI27^PQ1,0,1,Y^XZ');      
                //writeToSelectedPrinter('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT1,163^A0N,40,41^FB598,1,10,C^FH\^CI28^FD<?php echo $produit->nom; ?>^FS^CI27^FT0,97^A0N,94,101^FB599,1,24,C^FH\^CI28^FD<?php echo $produit->reference; ?>^FS^CI27^FT0,246^A0N,78,79^FB599,1,20,C^FH\^CI28^FD'+affichage+'^FS^CI27^BY3,3,81^FT164,365^BCN,,Y,N^FH\^FD>:'+data+'^FS^PQ1,0,1,Y^XZ');
                //location.reload();
                document.location.href="/LogiGraine/modules/etiquette_boite.php";
        }
    });


    //writeToSelectedPrinter('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT1,163^A0N,40,41^FB598,1,10,C^FH\^CI28^FDAubergine de Barbentane\5^FS^CI27^FT0,97^A0N,94,101^FB599,1,24,C^FH\^CI28^FD1-037\5^FS^CI27^FT0,246^A0N,78,79^FB599,1,20,C^FH\^CI28^FD10 g / 250 g\5^FS^CI27^BY3,3,81^FT164,365^BCN,,Y,N^FH\^FD>:A0>50001^FS^PQ1,0,1,Y^XZ');

    //writeToSelectedPrinter('CT~~CD,~CC^~CT~^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR8,8~SD15^JUS^LRN^CI27^PA0,1,1,0^XZ^XA^MMT^PW599^LL400^LS0^FT77,163^A0N,40,41^FH\^CI28^FD<?php echo $produit->nom; ?>^FS^CI27^FT161,97^A0N,94,99^FH\^CI28^FD<?php echo $produit->reference; ?>^FS^CI27^FT217,246^A0N,78,91^FH\^CI28^FD'+affichage+'^FS^CI27^BY3,3,81^FT164,365^BCN,,Y,N^FH\^FD>:A0>50001^FS^PQ1,0,1,Y^XZ');
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
</script>
    <?php include('../footer.php'); ?>
  </body>
</html>