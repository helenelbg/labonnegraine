$(function() {

	$('.etq_col').click(function(){


		var id_order = parseInt($('#id_order').val());
		var pAjax = new Object();

		var token = $('#token_etiquetage').val();

		pAjax.type = 'POST';
		//pAjax.url = '../../../modules/sonice_etiquetage/functions/get_labels2.php?poste='+localStorage.getItem('poste') +'&token='+token;
		pAjax.url = '/modules/controlecommande/generateColissimo.php?poste='+localStorage.getItem('poste'); 
		pAjax.data_type = 'html';
		pAjax.data = 'id_order='+id_order; 
		//pAjax.data = 'checkbox[]='+id_order;

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
	location.href = $('#url_col').val();
}



				function printEtiq(url)
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
						var printer_name = "DatamaxONeilE4204BMarkIII";
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

	$('.etq_cac').click(function(){


		var id_order = parseInt($('#id_order').val());
		var pAjax = new Object();

		var token = $('#token_etiquetage').val();

		pAjax.type = 'POST';
		//pAjax.url = '../../../modules/sonice_etiquetage/functions/get_labels2.php?poste='+localStorage.getItem('poste') +'&token='+token;
		pAjax.url = '/modules/controlecommande/generateClickandcollect.php?poste='+localStorage.getItem('poste'); 
		pAjax.data_type = 'html';
		pAjax.data = 'id_order='+id_order; 
		//pAjax.data = 'checkbox[]='+id_order;

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
	location.href = $('#url_col').val();
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
						var printer_name = "DatamaxONeilE4204BMarkIII";
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



	// Cookie Poste Controle
	$('#poste_controle1').click(function(){
		localStorage.setItem('poste', 'controle1');
	});

	$('#poste_controle2').click(function(){
		localStorage.setItem('poste', 'controle2');
	});

	$(document).ready(function() {
		var poste = localStorage.getItem('poste');
		if(poste){
			poste = "poste_" + poste;
		}else{
			poste = "poste_controle1";
		}
		if ($('#'+poste).length)
		{
			document.getElementById(poste).checked = true;
		}
	});

});
