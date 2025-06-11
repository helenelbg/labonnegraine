<?php
    session_start();

    include(dirname(__FILE__).'/../config/config.inc.php');
    require dirname(__FILE__).'/../init.php';

    include(dirname(__FILE__).'/classes/ClassOperateur.php');
    include(dirname(__FILE__).'/classes/ClassLBGModule.php');
    include(dirname(__FILE__).'/classes/ClassCommande.php');
    include(dirname(__FILE__).'/classes/ClassControle.php');
    include(dirname(__FILE__).'/classes/ClassControleProduit.php');
    include(dirname(__FILE__).'/classes/ClassCaisse.php');

    $title = 'Etiquettes transports';

    include('top.php'); 
    include('header.php'); 

    if ( isset($_POST['codeCaisse']) && !empty($_POST['codeCaisse']) )
    {
        $idCommandeEC = Caisse::getCommandeByCode($_POST['codeCaisse']);
        //echo $idCommandeEC;
        //die;
        if ( empty($idCommandeEC) )
        {
            ?>
            <script type="text/javascript">
                $( document ).ready(function() {
                    $('#myModalErreur .modal-title').html('Erreur');
                    $('#myModalErreur .modal-body').html("Aucune commande associée");
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
        else
        {
            $commandeEC = new Order($idCommandeEC);
            $clientEC = new Customer($commandeEC->id_customer);
            $carrierEC = new Carrier($commandeEC->id_carrier);

            // COLISSIMO
            if ( $carrierEC->id_reference == 348 || $carrierEC->id_reference == 142 || $carrierEC->id_reference == 189 || $carrierEC->id_reference == 190 || $carrierEC->id_reference == 191 || $carrierEC->id_reference == 192 || $carrierEC->id_reference == 112 )
            {
                ?>
                <script type="text/javascript">
                    $( document ).ready(function() {
                        var id_order = <?php echo $idCommandeEC; ?>;
                        var pAjax = new Object();
                        //var token = $('#token_etiquetage').val();

                        pAjax.type = 'POST';
                        pAjax.url = '/modules/controlecommande/generateColissimo.php?poste='+localStorage.getItem('poste'); 
                        pAjax.data_type = 'html';
                        pAjax.data = 'id_order='+id_order; 

                        $.ajax({
                            type: pAjax.type,
                            url: pAjax.url,
                            dataType: pAjax.data_type,
                            data: pAjax.data,
                            error: function (data) {
                                window.console && console.log(data);
                                $('#sne_loader').hide();
                                $('#errorColissimo').html('No data returned 1 ...<br><br>' + data.responseText);
                            },
                            success: function (data) {
                                if(data.startsWith('Erreur')){
                                    console.log(data);
                                    $('#errorColissimo').html(data);
                                    // Fin - Dorian, BERRY-WEB, septembre 2022
                                }else{
                                    console.log('SUCCESS');
                                    var exp = data.split("###");
                                    if ( exp[0].substr(0, 21) == 'http://localhost:8000' )
                                    {
                                        window.open(exp[0]);
                                    }
                                    else {
                                        console.log('NON : '.data.substr(0, 21));
                                    }
                                    if ( exp[1] != '0' )
                                    {
                                        window.open(exp[1]);
                                    }
                                    $.ajax({
                                        method: "POST",
                                        url: "/LogiGraine/valideTransport.php",
                                        data: {id_order: id_order},
                                        success :function(data) {
                                            //location.reload();
                                            //console.log(data);
                                        }
                                    });
                                    //location.href = $link->getAdminLink('AdminControleCommande')}&saveControl&id_order_control={$id_order_control}&printLabel;
                                }

                                function printEtiq(url)
                                {
                                    if ( localStorage.getItem('poste') == 'controle2' )
                                    {
                                        var printer_name = "ZDesigner GC420d";
                                    }
                                    else 
                                    {
                                        //var printer_name = "DatamaxONeilE4204BMarkIII";
                                        var printer_name = "ZDesigner ZD230-203dpi ZPL";
                                    }
                                    if (typeof(CommonPrintServer) == 'object' && typeof(CommonPrintServer.getPrinters) == 'function') 
                                    {
                                        CommonPrintServer.setPrinter(printer_name, function () {
                                            //console.log("printer4 : "+url);
                                            CommonPrintServer.printFileByURL(url, function (err) {
                                                //console.log("printer5 : "+url);
                                                //window.console && console.log(err);
                                                /*setTimeout(function () {
                                                    //printEtiq(urls);
                                                }, 1000);*/
                                                if (typeof(err) == 'undefined' || err == null || typeof(err.statusText) !== 'undefined') {
                                                    alert('--Common-PrinterServer inaccessible, démarrez le ou changez le mode HTTP/HTTPS--');
                                                }
                                            });
                                        });
                                    } else if (typeof(qz) == 'object' && typeof(qz.findPrinters) == 'function') {
                                        console.log("printer0");
                                        qz.findPrinter(printer_name);
                                        console.log("printer1 : "+url);
                                        qz.appendFile(url);
                                        console.log("printer2");
                                        qz.print();
                                        console.log("printer3");
                                    } else {
                                        alert('Utilitaire d\'impression non détecté, impossible de tester l\'impression.\nEssayez de rafraîchir la page.');
                                    }
                                }

                                var CommonPrintServer = (function () {
                                    var cps = {
                                        fn: {
                                            version: '1.0.0',
                                            callMethod: function(method, param, callback) {
                                                $.ajax({
                                                    type: CommonPrintServer.getAjaxTypeFromMethod(method),
                                                    url: CommonPrintServer.getWebServiceUrl() + method + CommonPrintServer.getFormatedParamsURL(method, param),
                                                    dataType: 'json',
                                                    data: param || [],
                                                    success: function (data) {
                                                        typeof(callback) == 'function' && callback(data);
                                                    },
                                                    error: function (data) {
                                                        typeof(callback) == 'function' && callback(data);
                                                    }
                                                });
                                            }
                                        },
                                        getWebServiceUrl: function () {
                                            var protocol = 'http:';
                                            var port = 4567;

                                            return protocol + '//localhost:' + port + '/';
                                        },
                                        getFormatedParamsURL: function (method, param) {
                                            return (method == 'setPrinter' && param !== '')  ? '/' + encodeURI(param) : '';
                                        },
                                        getAjaxTypeFromMethod: function(method) {
                                            return (method == 'printFileByURL' || method == 'printRaw') ? 'POST' : 'GET';
                                        },
                                        getPrinters: function (callback) {
                                            this.fn.callMethod('getPrinters', null, callback);
                                        },
                                        getPrinter: function (callback) {
                                            this.fn.callMethod('getPrinter', null, callback);
                                        },
                                        setPrinter: function (printer_name, callback) {
                                            console.log('printer_name AW : '+printer_name);
                                            this.fn.callMethod('setPrinter', printer_name, callback);
                                        },
                                        printFileByURL: function (file_url, callback) {
                                            this.fn.callMethod('printFileByURL', file_url, callback);
                                        }
                                    };
                                    return cps;
                                }());
                            }
                        });
                    });
                </script>
                <?php
            }
            elseif ( $carrierEC->id_reference == 391 )
            {
                $history = new OrderHistory();
                $history->id_order = $idCommandeEC;
                $history->id_order_state = 42;
                $history->add();
                $history->changeIdOrderState(42, $idCommandeEC);
                ?>
                <script type="text/javascript">
                    $( document ).ready(function() {
                        var id_order = <?php echo $idCommandeEC; ?>;
                        var pAjax = new Object();
                        //var token = $('#token_etiquetage').val();
                        pAjax.type = 'POST';
                        pAjax.url = '/modules/controlecommande/generateClickandcollect.php?poste='+localStorage.getItem('poste'); 
                        pAjax.data_type = 'html';
                        pAjax.data = 'id_order='+id_order; 

                        $.ajax({
                            type: pAjax.type,
                            url: pAjax.url,
                            dataType: pAjax.data_type,
                            data: pAjax.data,
                            error: function (data) {
                                window.console && console.log(data);
                                $('#sne_loader').hide();
                                //plug.alert('No data returned...<br><br>' + data.responseText);
                                //console.log('No data returned 1 ...<br><br>' + data.responseText);
                                $('#errorColissimo').html('No data returned 1 ...<br><br>' + data.responseText);
                            },
                            success: function (data) {
                                // Début - Dorian, BERRY-WEB, septembre 2022
                                if(data.startsWith('Erreur')){
                                    console.log(data);
                                    $('#errorColissimo').html(data);
                                    // Fin - Dorian, BERRY-WEB, septembre 2022
                                }else{
                                    console.log('SUCCESS');
                                    var exp = data.split("###");
                                    if ( exp[0].substr(0, 21) == 'http://localhost:8000' )
                                    {
                                        window.open(exp[0]);
                                    }
                                    else {
                                        console.log('NON : '.data.substr(0, 21));
                                    }
                                    if ( exp[1] != '0' )
                                    {
                                        window.open(exp[1]);
                                    }
                                    $.ajax({
                                        method: "POST",
                                        url: "/LogiGraine/valideTransport.php",
                                        data: {id_order: id_order},
                                        success :function(data) {
                                            //location.reload();
                                            //console.log(data);
                                        }
                                    });
                                    //location.href = $('#url_col').val();
                                }
                                function printEtiq2(url)
                                {
                                    /*if (!urls.length) {
                                            return false;
                                        }*/
                                    //var url = urls[0];
                                    //urls.splice(0,1);
                                    if ( localStorage.getItem('poste') == 'controle2' )
                                    {
                                        var printer_name = "ZDesigner GC420d";
                                    }
                                    else {
                                        //var printer_name = "DatamaxONeilE4204BMarkIII";
                                        var printer_name = "ZDesigner ZD230-203dpi ZPL";
                                    }
                                    if (typeof(CommonPrintServer) == 'object' && typeof(CommonPrintServer.getPrinters) == 'function') {
                                    CommonPrintServer.setPrinter(printer_name, function () {
                                        //console.log("printer4 : "+url);
                                        CommonPrintServer.printFileByURL(url, function (err) {
                                            //console.log("printer5 : "+url);
                                            //window.console && console.log(err);
                                            /*setTimeout(function () {
                                                //printEtiq(urls);
                                            }, 1000);*/
                                            if (typeof(err) == 'undefined' || err == null || typeof(err.statusText) !== 'undefined') {
                                                alert('--Common-PrinterServer inaccessible, démarrez le ou changez le mode HTTP/HTTPS--');
                                            }
                                        });
                                    });
                                    } else if (typeof(qz) == 'object' && typeof(qz.findPrinters) == 'function') {
                                    console.log("printer0");
                                    qz.findPrinter(printer_name);
                                    console.log("printer1 : "+url);
                                    qz.appendFile(url);
                                    console.log("printer2");
                                    qz.print();
                                    console.log("printer3");
                                    /*setTimeout(function () {
                                                        //printEtiq(urls);
                                                }, 1000);*/
                                    } else {
                                    alert('Utilitaire d\'impression non détecté, impossible de tester l\'impression.\nEssayez de rafraîchir la page.');
                                }
                                }

                                var CommonPrintServer = (function () {

                                    var cps = {

                                        fn: {

                                            version: '1.0.0',

                                            callMethod: function(method, param, callback) {
                                        /*console.log('AW : ' + CommonPrintServer.getWebServiceUrl());
                                        console.log(method);
                                        console.log(CommonPrintServer.getFormatedParamsURL(method, param));
                                        console.log(param);
                                                console.log('URL : '+CommonPrintServer.getWebServiceUrl() + method + CommonPrintServer.getFormatedParamsURL(method, param));*/
                                                //console.log('FIN');

                                                $.ajax({
                                                    type: CommonPrintServer.getAjaxTypeFromMethod(method),
                                                    url: CommonPrintServer.getWebServiceUrl() + method + CommonPrintServer.getFormatedParamsURL(method, param),
                                                    dataType: 'json',
                                                    data: param || [],
                                                    success: function (data) {
                                                        typeof(callback) == 'function' && callback(data);
                                                    },
                                                    error: function (data) {
                                                        typeof(callback) == 'function' && callback(data);
                                                    }
                                                });
                                            }

                                        },

 
                                        getWebServiceUrl: function () {
                                            var protocol = 'http:';
                                            var port = 4567;

                                            return protocol + '//localhost:' + port + '/';
                                        },

                                        getFormatedParamsURL: function (method, param) {
                                            return (method == 'setPrinter' && param !== '')  ? '/' + encodeURI(param) : '';
                                        },

                                        getAjaxTypeFromMethod: function(method) {
                                            return (method == 'printFileByURL' || method == 'printRaw') ? 'POST' : 'GET';
                                        },


                                        getPrinters: function (callback) {
                                            this.fn.callMethod('getPrinters', null, callback);
                                        },

                                        getPrinter: function (callback) {
                                            this.fn.callMethod('getPrinter', null, callback);
                                        },

                                        setPrinter: function (printer_name, callback) {
                                    console.log('printer_name AW : '+printer_name);
                                            this.fn.callMethod('setPrinter', printer_name, callback);
                                        },

                                        printFileByURL: function (file_url, callback) {
                                            this.fn.callMethod('printFileByURL', file_url, callback);
                                        }

                                    };

                                    return cps;

                                }());



                            }
                        });
                    });
                </script>
                <?php
            }
            elseif ($carrierEC->id_reference == 155 || $carrierEC->id_reference == 193)
            {
                ?>
                <script type="text/javascript">
                    $( document ).ready(function() {
                        var id_order = <?php echo $idCommandeEC; ?>;
                                    $.ajax({
                                        method: "POST",
                                        url: "/LogiGraine/valideTransport.php",
                                        data: {id_order: id_order},
                                        success :function(data) {
                                            //location.reload();
                                            //console.log(data);
                                        }
                                    });
                         alert('Contrôle de commande ok. Vous pouvez imprimer votre étiquette DPD.');
                     });
                    </script>
                    <?php
            }
        }
    }
?>
    <div class="container">
      <form class="form-signin" method="POST">
        <h2 class="form-signin-heading">Etiquettes transports</h2>
        <input type="text" id="codeCaisse" name="codeCaisse" class="form-control" placeholder="Scannez la boite" required autofocus><br />
        <div class="poste_controle">
            <input type="radio" id="poste_controle1" name="poste_controle" value="1" checked="">
            <label for="poste_controle">Contrôle 1</label>
        </div>
        <div class="poste_controle">
            <input type="radio" id="poste_controle2" class="poste_controle" name="poste_controle" value="2">
            <label for="poste_controle">Contrôle 2</label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" style="visibility:hidden;">Valider</button>
        <?php 
            if ( isset($_POST['codeCaisse']) && !empty($_POST['codeCaisse']) )
            {
                echo "Caisse <b>".$_POST['codeCaisse'].'</b><br />';
            }
            if ( isset($idCommandeEC) && !empty($idCommandeEC) )
            {
                echo "Commande <b>".$idCommandeEC.'</b>';
                echo '<h3>'.$clientEC->lastname.' '.$clientEC->firstname.'</h3>';
                $produitsEC = Commande::getProductsByOrder($idCommandeEC);
                $totalEC = 0;
                foreach($produitsEC as $produitEC)
                {
                    $totalEC += $produitEC['quantity_final'];
                }
                if ( $totalEC == 1 )
                {
                    echo '<h3>'.$totalEC.'</h3> article';
                }
                else 
                {
                    echo '<h3>'.$totalEC.'</h3> articles';
                }
            }
        ?>
      </form>
    </div>
    <?php include('footer.php'); ?>
  </body>
</html>
<script type="text/javascript">
    $(document).ready(function() {
        // Cookie Poste Controle
        $('#poste_controle1').click(function(){
            localStorage.setItem('poste', 'controle1');
        });

        $('#poste_controle2').click(function(){
            localStorage.setItem('poste', 'controle2');
        });

		var poste = localStorage.getItem('poste');
        var tmpPoste = localStorage.getItem('poste');
        console.log('1 : '+poste);
		if(poste){
			poste = "poste_" + poste;
		}else{
			poste = "poste_controle1";
            tmpPoste = "controle1";
		}
        console.log('2 : '+poste);  
		if ($('#'+poste).length)
		{
			document.getElementById(poste).checked = true;
            localStorage.setItem('poste', tmpPoste);
		}
	});
</script>