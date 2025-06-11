jQuery.extend(jQuery.fn, {
	frameColissimoOpen: function(params)
	{
		
		var colissimo = jQuery;
		
		//Customisation des couleurs
		var custmomisation = [];
		custmomisation.push('<div class="couleur1" id="widget_colissimo_couleur1" style="display : none;" ></div>');
		custmomisation.push('<div class="couleur2" id="widget_colissimo_couleur2" style="display : none;" ></div>');
		custmomisation.push('<div class="police" id="widget_colissimo_police" style="display : none;" ></div>');
		
		//Le conteneur
		conteneur_widget_colissimo = this;
		
		conteneur_widget_colissimo.html(custmomisation.join('\n'));
		
		//récupération des couleurs
		if(params.couleur1 == null)
		{
			if(colissimo(".couleur1").css('color') != null && colissimo(".couleur1").css('color') != undefined && colissimo(".couleur1").css('color') != '' && colissimo(".couleur1").css('color') != "rgb(0, 0, 0)")
			{
				params.couleur1 = colissimo(".couleur1").css('color');
				params.couleurBouton = colissimo(".couleur1").css('color');
				console.log('couleur 1 detectee en css ' + params.couleur1);
			} else {
			
				console.log('couleur 1 par defaut ');
				//Couleurs neutres
				params.couleur1 = 'black';
				params.couleurBouton = '#3C3C3B';
				
				//Couleur colissimo
				params.couleur1 = '#EA690A';
				params.couleurBouton = '#EA690A';
			}
			
		} else {
			console.log('couleur 1 en parametre');
			params.couleurBouton = params.couleur1;
		}
		
		
		if(params.couleur2 == null)
		{
			if(colissimo(".couleur2").css('color') != null && colissimo(".couleur2").css('color') != undefined && colissimo(".couleur2").css('color') != '' && colissimo(".couleur2").css('color') != "rgb(0, 0, 0)")
			{
				params.couleur2 = colissimo(".couleur2").css('color');
				console.log('couleur 2 detectee en css ' + params.couleur2);
			} else {
				console.log('couleur 2 par defaut ');
				params.couleur2 = 'black';
			}
		} else {
			console.log('couleur 2 en parametre');
		}
		if(params.font == null)
		{
			if(colissimo(".police").css('font-family') != null && colissimo(".police").css('font-family') != undefined && colissimo(".police").css('font-family') != '' && colissimo(".police").css('font-family') != 'serif')
			{
				params.font = colissimo(".police").css('font-family');
				console.log('font detectee en css ' + params.font);
			} else {
				params.font = 'Barlow';
				console.log('font par defaut ');
			}
		} else {
			console.log('font en parametre');
		}
		
		colissimo_widget_paramsCharges = params;
		
		colissimo_widget_callBack = params.callBackFrame;
		
		
		//en fonction de la largeure de l'écran on affiche la version mobile ou PC ou tablette
		var css = ''; 
		var colissimo_widget_ismobile = false;
		if(window.innerWidth < 1000)
		{
			if(window.innerWidth < 501)
			{
				console.log('on est sur mobile, charger la verison mobile');
				css = colissimo_widget_css_mobile(params);
				colissimo_widget_ismobile = true;
				
				//Ajouter la classe widget_colissimo sur le div du widget
				conteneur_widget_colissimo.addClass("widget_colissimo");
				
			} else {
				console.log('on est sur tablette, charger la verison tablette');
				css = colissimo_widget_css_ipad(params);
			}
		} else {
			console.log('on est sur PC, charger la verison PC');
			css = colissimo_widget_css(params);
		}
		
		//CSS Du WIDGET
		var html = [];
		
		html.push(css);
		//Les traductions
		
		colissimo_widget_messages = colissimo_widget_getWording(colissimo_widget_paramsCharges);
		
		
		//Layer du widget et HTML
		
		html.push('<div class="widget_colissimo_contenu" id="widget_colissimo_contenu" >');
		
		
		html.push('<span class="widget_colissimo_close" onclick="conteneur_widget_colissimo.html(\'\');" >&times;</span>');
		
		html.push('<table id="widget_colissimo_table_adresse" class="widget_colissimo_table_adresse widget_colissimo_w95 widget_colissimo_padding" >');
		
		if(colissimo_widget_ismobile)
		{
			html.push('	<tr class="widget_colissimo_padding" >');
			html.push('		<td class="widget_colissimo_padding" colspan="3" >');
			html.push('			<div class="widget_colissimo_logo" >');
			html.push('				<img class="" src="' + params.URLColissimo + '/widget-colissimo/images/Colissimo.svg" alt="">');
			html.push('			</div>');
			html.push('		</td>');
			html.push('	</tr>');
		}
		
		html.push('	<tr class="widget_colissimo_padding widget_colissimo_hidden" id="widget_colissimo_text_erreur" >');
		
		html.push('		<td class="widget_colissimo_padding" ></td>');
		
		if(!colissimo_widget_ismobile)
		{
			html.push('		<td class="widget_colissimo_padding" ></td>');
		}
		
		html.push('		<td class="widget_colissimo_padding" >');
		html.push('			<p class="widget_colissimo_text_erreur" >' + colissimo_widget_messages.adresse + '</p>');
		html.push('		</td>');
		html.push('		<td class="widget_colissimo_padding" ></td>');
		html.push('	</tr>');
		
		html.push('	<tr class="widget_colissimo_padding" >');
		
		if(!colissimo_widget_ismobile)
		{
			html.push('		<td class="widget_colissimo_padding" >');
			html.push('			<div class="widget_colissimo_logo" >');
			html.push('				<img class="" src="' + params.URLColissimo + '/widget-colissimo/images/Colissimo.svg" alt="">');
			html.push('			</div>');
			html.push('		</td>');
		}
		
		html.push('		<td class="widget_colissimo_table_adresse" >');
		//html.push('			<div class="widget_colissimo_sliders" ><div class="widget_colissimo_sliders_img" ></div></div>');
		html.push('			<div class="widget_colissimo_point_input" >');
		html.push('				<img class="widget_colissimo_point_img" src="' + params.URLColissimo + '/widget-colissimo/images/pictoAdresse.svg" alt="">');
		html.push('			</div>');
		html.push('		</td>');
		
		html.push('		<td class="widget_colissimo_table_adresse">');
		html.push('			<input id="widget_colissimo_adresse" type="text" class="widget_colissimo_input_text_normal" placeholder="23 rue de Rivoli, 75004 Paris" ></input>');
		html.push('		</td>');
		html.push('		<td class="widget_colissimo_table_adresse" onclick="widget_colissimo_reloadPDR();">');
		html.push('			<div class="widget_colissimo_mode_transport" >');
		html.push('				<div id="widget_colissimo_loupe_img" class="widget_colissimo_loupe_img" ></div>');
		
		html.push('			</div>');
		html.push('		</td>');
		html.push('	</tr>');
		html.push('</table>');
		
		
		html.push('	<div class="widget_colissimo_filtres" id="widget_colissimo_filtres" >');
		
		html.push('	<table class="widget_colissimo_table_filtres" >');
		
		html.push('	<tr>');
		
		html.push('	<td class="widget_colissimo_padding0" >');
		
		
		html.push('	<svg id="Icon_awesome-wheelchair" data-name="Icon awesome-wheelchair" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24">');
		html.push('		<path id="Icon_awesome-wheelchair-2" data-name="Icon awesome-wheelchair" d="M23.255,18.078l.667,1.344a.75.75,0,0,1-.338,1.005l-3.069,1.542a1.5,1.5,0,0,1-2.024-.706L15.547,15H9a1.5,1.5,0,0,1-1.485-1.288C5.927,2.593,6.018,3.283,6,3A3,3,0,1,1,9.439,5.968L9.658,7.5H15.75a.75.75,0,0,1,.75.75v1.5a.75.75,0,0,1-.75.75H10.087L10.3,12h6.2a1.5,1.5,0,0,1,1.358.862l2.7,5.738,1.7-.86a.75.75,0,0,1,1.005.338ZM14.595,16.5H13.446A5.25,5.25,0,1,1,5.64,11.2L5.2,8.086A8.25,8.25,0,1,0,15.8,19.069Z" fill="' + params.couleur2 + '"/>');
		html.push('	</svg></br>');
		
		html.push('	' + colissimo_widget_messages.pmr);
		html.push('	</td>');
		html.push('	<td class="widget_colissimo_padding0">');
		
		
		html.push('	<svg id="Composant_10_2" data-name="Composant 10 – 2" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24">');
		html.push('		<g id="Rectangle_563" data-name="Rectangle 563" fill="none" stroke="' + params.couleur2 + '" stroke-width="2">');
		html.push('			<rect width="24" height="24" rx="3" stroke="none"/>');
		html.push('			<rect x="1" y="1" width="22" height="22" rx="2" fill="none"/>');
		html.push('		</g>');
		html.push('		<g id="Tracé_845" data-name="Tracé 845" transform="translate(7.048 17.918)" fill="none" stroke-linecap="round">');
		html.push('			<path d="M5.95-11.917a4.087,4.087,0,0,1,1.972.459,3.235,3.235,0,0,1,1.317,1.3,3.906,3.906,0,0,1,.468,1.929,3.626,3.626,0,0,1-.485,1.887,3.3,3.3,0,0,1-1.36,1.267,4.342,4.342,0,0,1-2.015.451H3.434a.075.075,0,0,0-.085.085V-.2A.2.2,0,0,1,3.29-.06.2.2,0,0,1,3.145,0H1.156a.2.2,0,0,1-.145-.06A.2.2,0,0,1,.952-.2V-11.713a.2.2,0,0,1,.059-.145.2.2,0,0,1,.145-.059ZM5.593-6.545A1.755,1.755,0,0,0,6.851-7a1.552,1.552,0,0,0,.476-1.182,1.6,1.6,0,0,0-.476-1.207,1.736,1.736,0,0,0-1.258-.459H3.434a.075.075,0,0,0-.085.085V-6.63a.075.075,0,0,0,.085.085Z" stroke="none"/>');
		html.push('			<path d="M 1.156001091003418 -11.91700077056885 C 1.099330902099609 -11.91700077056885 1.051170349121094 -11.8971700668335 1.011500358581543 -11.85750007629395 C 0.9718303680419922 -11.81783008575439 0.952000617980957 -11.7696704864502 0.952000617980957 -11.71300029754639 L 0.952000617980957 -0.2040004730224609 C 0.952000617980957 -0.1473302841186523 0.9718303680419922 -0.09917068481445312 1.011500358581543 -0.05950069427490234 C 1.051170349121094 -0.01983070373535156 1.099330902099609 0 1.156001091003418 0 L 3.145000457763672 0 C 3.20167064666748 0 3.249830722808838 -0.01983070373535156 3.289500713348389 -0.05950069427490234 C 3.329170703887939 -0.09917068481445312 3.349000453948975 -0.1473302841186523 3.349000453948975 -0.2040004730224609 L 3.349000453948975 -4.539000511169434 C 3.349000453948975 -4.595670223236084 3.377330780029297 -4.624000549316406 3.434000492095947 -4.624000549316406 L 5.848000526428223 -4.624000549316406 C 6.607330799102783 -4.624000549316406 7.278830528259277 -4.774170398712158 7.862500667572021 -5.074500560760498 C 8.446170806884766 -5.37483024597168 8.899500846862793 -5.797000408172607 9.222500801086426 -6.341000556945801 C 9.545500755310059 -6.885000228881836 9.707000732421875 -7.514000415802002 9.707000732421875 -8.228000640869141 C 9.707000732421875 -8.953330039978027 9.551170349121094 -9.596500396728516 9.239500999450684 -10.15750026702881 C 8.927830696105957 -10.7185001373291 8.488670349121094 -11.15200042724609 7.922000885009766 -11.45800018310547 C 7.355330467224121 -11.76399993896484 6.698000907897949 -11.91700077056885 5.950000762939453 -11.91700077056885 L 1.156001091003418 -11.91700077056885 M 5.593000888824463 -6.545000553131104 L 3.434000492095947 -6.545000553131104 C 3.377330780029297 -6.545000553131104 3.349000453948975 -6.573330402374268 3.349000453948975 -6.630000591278076 L 3.349000453948975 -9.758000373840332 C 3.349000453948975 -9.814670562744141 3.377330780029297 -9.843000411987305 3.434000492095947 -9.843000411987305 L 5.593000888824463 -9.843000411987305 C 6.114330768585205 -9.843000411987305 6.533670902252197 -9.690000534057617 6.851000785827637 -9.384000778198242 C 7.168330669403076 -9.078000068664551 7.327000617980957 -8.675670623779297 7.327000617980957 -8.177000045776367 C 7.327000617980957 -7.689670562744141 7.168330669403076 -7.295830249786377 6.851000785827637 -6.995500564575195 C 6.533670902252197 -6.695170402526855 6.114330768585205 -6.545000553131104 5.593000888824463 -6.545000553131104 M 1.156001091003418 -13.91700077056885 L 5.950000762939453 -13.91700077056885 C 7.029930591583252 -13.91700077056885 8.013130187988281 -13.68176078796387 8.87229061126709 -13.21781063079834 C 9.777510643005371 -12.72900009155273 10.48927021026611 -12.02615070343018 10.98781108856201 -11.1287899017334 C 11.46503067016602 -10.26981067657471 11.70700073242188 -9.293840408325195 11.70700073242188 -8.228000640869141 C 11.70700073242188 -7.153040409088135 11.44969081878662 -6.174620151519775 10.94221115112305 -5.319920539855957 C 10.42597103118896 -4.450470447540283 9.697680473327637 -3.769570350646973 8.777570724487305 -3.296120643615723 C 7.910830497741699 -2.850130081176758 6.925180435180664 -2.624000549316406 5.848000526428223 -2.624000549316406 L 5.349000453948975 -2.624000549316406 L 5.349000453948975 -0.2040004730224609 C 5.349000453948975 0.3850297927856445 5.11983060836792 0.9385900497436523 4.703710556030273 1.354709625244141 C 4.287590503692627 1.770829200744629 3.734030723571777 2 3.145000457763672 2 L 1.156001091003418 2 C 0.5669708251953125 2 0.01341056823730469 1.770829200744629 -0.4027090072631836 1.354709625244141 C -0.8188295364379883 0.9385900497436523 -1.047999382019043 0.3850297927856445 -1.047999382019043 -0.2040004730224609 L -1.047999382019043 -11.71300029754639 C -1.047999382019043 -12.30203056335449 -0.8188295364379883 -12.8555908203125 -0.4027090072631836 -13.27171039581299 C 0.01341056823730469 -13.68782997131348 0.5669708251953125 -13.91700077056885 1.156001091003418 -13.91700077056885 Z" stroke="none" fill="' + params.couleur2 + '"/>');
		html.push('		</g>');
		html.push('	</svg></br>');
		
		html.push('	' + colissimo_widget_messages.parking);
		html.push('	</td>');
		html.push('	<td class="widget_colissimo_padding0">');
		
		html.push('	<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 22.7 25">');
		html.push('		<g id="Composant_11_2" data-name="Composant 11 – 2" transform="translate(1 1)">');
		html.push('		<g id="Groupe_1437" data-name="Groupe 1437" transform="translate(-201.383 -168)">');
		html.push('		<g id="Icon_feather-calendar" data-name="Icon feather-calendar" transform="translate(198.383 166)">');
		html.push('			<path id="Tracé_846" data-name="Tracé 846" d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z" transform="translate(0 0.3)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('			<path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6" transform="translate(1.95 0)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('			<path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6" transform="translate(0.75 0)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('			<path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7" transform="translate(0 1.2)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('		</g>');
		html.push('			<path id="Tracé_853" data-name="Tracé 853" d="M3.311.088a3.913,3.913,0,0,1-1.54-.281A2.271,2.271,0,0,1,.754-1,2.1,2.1,0,0,1,.4-2.211v-.242a.127.127,0,0,1,.038-.094.127.127,0,0,1,.093-.038H1.782a.127.127,0,0,1,.094.038.127.127,0,0,1,.039.094v.165a.9.9,0,0,0,.407.743,1.8,1.8,0,0,0,1.1.3,1.3,1.3,0,0,0,.869-.248.776.776,0,0,0,.286-.61A.616.616,0,0,0,4.4-2.547a1.554,1.554,0,0,0-.478-.319q-.3-.137-.962-.379a9.426,9.426,0,0,1-1.249-.517,2.507,2.507,0,0,1-.858-.72A1.811,1.811,0,0,1,.506-5.61,1.986,1.986,0,0,1,.847-6.765a2.171,2.171,0,0,1,.946-.759,3.465,3.465,0,0,1,1.4-.264,3.57,3.57,0,0,1,1.49.3A2.43,2.43,0,0,1,5.7-6.661a2.124,2.124,0,0,1,.369,1.238v.165a.127.127,0,0,1-.039.094.127.127,0,0,1-.094.038H4.675a.127.127,0,0,1-.093-.038.127.127,0,0,1-.039-.094v-.088a1,1,0,0,0-.379-.786,1.548,1.548,0,0,0-1.04-.325,1.316,1.316,0,0,0-.808.22.716.716,0,0,0-.292.605.66.66,0,0,0,.171.462,1.5,1.5,0,0,0,.506.335q.336.148,1.039.4a12.948,12.948,0,0,1,1.227.506,2.444,2.444,0,0,1,.8.655,1.717,1.717,0,0,1,.358,1.128A1.97,1.97,0,0,1,5.368-.512,3.231,3.231,0,0,1,3.311.088Z" transform="translate(208.383 188)" fill="' + params.couleur2 + '"/>');
		html.push('		</g>');
		html.push('		</g>');
		html.push('	</svg></br>');
		
		html.push('	' + colissimo_widget_messages.samedi);
		html.push('	</td>');
		html.push('	<td class="widget_colissimo_padding0">');
		
		
		
		html.push('	<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 22.7 25">');
		html.push('	 <g id="Composant_12_2" data-name="Composant 12 – 2" transform="translate(1 1)">');
		html.push('	  <g id="Icon_feather-calendar" data-name="Icon feather-calendar" transform="translate(-3 -2)">');
		html.push('	    <path id="Tracé_846" data-name="Tracé 846" d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z" transform="translate(0 0.3)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('	    <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6" transform="translate(1.95 0)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('	     <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6" transform="translate(0.75 0)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('	     <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7" transform="translate(0 1.2)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('	   </g>');
		html.push('	   <path id="Tracé_852" data-name="Tracé 852" d="M.8,0A.127.127,0,0,1,.71-.038.127.127,0,0,1,.671-.132V-7.568A.127.127,0,0,1,.71-7.661.127.127,0,0,1,.8-7.7h2.75A3.373,3.373,0,0,1,5-7.409a2.252,2.252,0,0,1,.963.82A2.2,2.2,0,0,1,6.3-5.368v3.036a2.2,2.2,0,0,1-.341,1.221A2.252,2.252,0,0,1,5-.292,3.373,3.373,0,0,1,3.553,0ZM2.222-1.386a.049.049,0,0,0,.055.055H3.608a1.061,1.061,0,0,0,.82-.341,1.336,1.336,0,0,0,.325-.913v-2.53a1.3,1.3,0,0,0-.313-.913A1.087,1.087,0,0,0,3.6-6.369H2.277a.049.049,0,0,0-.055.055Z" transform="translate(7 20)" fill="' + params.couleur2 + '"/>');
		html.push('	  </g>');
		html.push('	</svg></br>');
		
		
		
		html.push('	' + colissimo_widget_messages.dimanche);
		html.push('	</td>');
		html.push('	<td class="widget_colissimo_padding0">');
		
		html.push('	<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24">');
		html.push('	  <g id="Icon_feather-clock" data-name="Icon feather-clock" transform="translate(1 1)">');
		html.push('	    <path id="Tracé_850" data-name="Tracé 850" d="M24,13A11,11,0,1,1,13,2,11,11,0,0,1,24,13Z" transform="translate(-2 -2)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('	    <path id="Tracé_851" data-name="Tracé 851" d="M12,6v6.6l4.4,2.2" transform="translate(-1 -1.6)" fill="none" stroke="' + params.couleur2 + '" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>');
		html.push('	  </g>');
		html.push('	</svg></br>');
		
		html.push('	' + colissimo_widget_messages.dixneuf);
		html.push('	</td>');

		html.push('	</tr>');
		
//		html = html + '	<tr>';
//		
//		html = html + '	<td>';
//		html = html + '	<img class="widget_colissimo_icone_img_filtre" src="' + params.URLColissimo + '/widget-colissimo/images/awesome-wheelchair.svg" alt="" />';
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	<img class="widget_colissimo_icone_img_filtre" src="' + params.URLColissimo + '/widget-colissimo/images/parking.svg" alt="" />';
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	<img class="widget_colissimo_icone_img_filtre" src="' + params.URLColissimo + '/widget-colissimo/images/calendrierS.svg" alt="" />';
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	<img class="widget_colissimo_icone_img_filtre" src="' + params.URLColissimo + '/widget-colissimo/images/calendrierD.svg" alt="" />';
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	<img class="widget_colissimo_icone_img_filtre" src="' + params.URLColissimo + '/widget-colissimo/images/feather-clock.svg" alt="" />';
//		html = html + '	</td>';
//
//		html = html + '	</tr>';
//		
//		html = html + '	<tr>';
//		
//		html = html + '	<td>';		
//		html = html + '	' + colissimo_widget_messages.pmr;
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	' + colissimo_widget_messages.parking;
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	' + colissimo_widget_messages.samedi;
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	' + colissimo_widget_messages.dimanche;
//		html = html + '	</td>';
//		html = html + '	<td>';
//		html = html + '	' + colissimo_widget_messages.dixneuf;
//		html = html + '	</td>';
//		
//		html = html + '	</tr>');
		
		html.push('	</table>');
		
		html.push('	</div>');
		
		html.push('	<div class="widget_colissimo_liste" id="widget_colissimo_liste" ></div>');
		
		

		if(colissimo_widget_ismobile)
		{
		
			html.push('	<div class="widget_colissimo_map_zone" id="widget_colissimo_map_zone" onclick="widget_colissimo_showMAP();" ></div>');
			
			html.push('	<div class="widget_colissimo_map_liste" id="widget_colissimo_map_liste" onclick="widget_colissimo_hideMAP();" >');
			html.push('	<img class="widget_colissimo_map_img" src="' + params.URLColissimo + '/widget-colissimo/images/liste.svg" alt="" ></img></br>');
			html.push('	</div>');
			
			html.push('	<div class="widget_colissimo_map_icone" id="widget_colissimo_map_icone" onclick="widget_colissimo_showMAP();" >');
			html.push('	<img class="widget_colissimo_map_img" src="' + params.URLColissimo + '/widget-colissimo/images/carte.svg" alt="" ></img></br>');
			html.push('	</div>');
		}
		
		html.push('	<div id="widget_colissimo_map" class="widget_colissimo_map" ></div>');
		
		html.push('	<table id="widget_colissimo_autocomplete" class="widget_colissimo_autocomplete widget_colissimo_padding0" ></table>');
		
		
		
		html.push('</div>');
		
		conteneur_widget_colissimo.html(html.join('\n'));
		
		////////////////////////////////////
		//Autocompletion avec le service SERCA
		///////////////////////////////////
		
		colissimo('#widget_colissimo_adresse').on('keypress keydown', function() {
			
			
			if(this.value.length > 2)
			{
				console.log('appel au serca');
				
				
   				colissimo.ajax({
				   type: "GET",
				   encoding:"UTF-8",
				   contentType: "application/x-www-form-urlencoded; charset=utf-8",
				   dataType: "json",
						   url: params.URLColissimo + '/widget-colissimo/rest/GetAddresses.rest',
						   data: "chaineRecherche="+colissimo("#widget_colissimo_adresse").val().toUpperCase()+'&token=' + params.token,
				   success: function(msg){
					   
				    	console.log('retour'); 
				    	console.log(msg); 
				    	
				    	
				    	//On efface
				    	colissimo("#widget_colissimo_autocomplete").html('');
				    	var html_auto = '';
				    	//On affiche l'autocompletion
					   	colissimo.each(msg, function(index, currAdr) {
					   		var adr = widget_colissimo_format(currAdr.voie.toLowerCase()) + ', ' + currAdr.cp + ' ' + currAdr.ville;
					   		
					   		if(index > 0)
					   		{
						   		html_auto = html_auto + '<tr class="widget_colissimo_padding0" >';
						   		html_auto = html_auto + '<td class="widget_colissimo_padding0" ><div class="widget_colissimo_autocomplete_trait" ></td>';
						   		html_auto = html_auto + '</tr>';
					   		}
					   		html_auto = html_auto + '<tr class="widget_colissimo_padding0" >';
					   		html_auto = html_auto + '<td class="widget_colissimo_autocomplete_li" onclick="widget_colissimo_select_autocomplete(this.innerText);" >' + adr + '</td>';
					   		html_auto = html_auto + '</tr>';
					   	});
					
					   	colissimo("#widget_colissimo_autocomplete").append(html_auto);
					   	//on positionne l'autocomplete
					   	if(window.innerWidth < 1000)
					   	{
					   		//pour mobile
							if(window.innerWidth < 501)
							{
								colissimo("#widget_colissimo_autocomplete").css("left", 1);
						   		//colissimo("#widget_colissimo_autocomplete").css("top", colissimo('#widget_colissimo_filtres').offset().top+1);
							} else {
								//Pour ipad
								colissimo("#widget_colissimo_autocomplete").css("left", 1);
						   		//colissimo("#widget_colissimo_autocomplete").css("top", colissimo('#widget_colissimo_filtres').offset().top+1);
						   		console.log('width : ' + colissimo('#widget_colissimo_filtres').width());
						   		colissimo("#widget_colissimo_autocomplete").css("width", colissimo('#widget_colissimo_filtres').width());
							}
					   	}
   	
				   },
				    error : function(resultat, statut, erreur){
				    	console.log(statut); 
				    	console.log(erreur); 
				    } 
			 });
				
			}
			 
		});
		////////////////////////////////////
		//FIN avec le service SERCA
		///////////////////////////////////
		
		
		//On va chercher les clefs
		colissimo.ajax({
			type: 'GET',
			async: false,
			encoding:"UTF-8",
			url: params.URLColissimo + '/widget-colissimo/rest/GetKey.rest',
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			dataType: 'json',
			data: "data=" + window.btoa(JSON.stringify({token : params.token})),
			success: function(data) {
				mapboxgl.accessToken = data;
			}
		});	
		
		//Recherche sur l'adresse passée en paramètre
		var adr = params.ceAddress.replace("’", "'") + ', ' + params.ceZipCode + ' ' + params.ceTown.replace("’", "'") + ', ' + params.ceCountry;
		colissimo("#widget_colissimo_adresse").val(params.ceAddress + ', ' + params.ceZipCode + ' ' + params.ceTown);
		
		var colissimo_widget_adresse_valide = true;
		if(adr == '')
		{
			colissimo_widget_map = colissimo_widget_loadingMap(2.3446, 48.8579, 14, params);
		} else {
		
			colissimo.ajax({
				type: "GET",
				async: false, // Mode synchrone
				encoding:"UTF-8",
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				dataType: "json",
						   url: params.URLColissimo + '/widget-colissimo/rest/GetLocation.rest',
						   //data: "adresse="+adr+'&token=' + params.token,
						   data: "data=" + window.btoa(JSON.stringify({addresse : adr, token : params.token})),
			    success: function(msg){
				   	console.log(msg);
				   	
				   	//Si il n' pas trouvé l'adresse, on met des coordonnées par défaut
				   	if(msg.lng == null)
				   	{
				   		colissimo_widget_adresse_valide = false;
				   		colissimo_widget_map = colissimo_widget_loadingMap(0, 45, 4, params);
				   		
				   		//on met un message d'erreur
				   		colissimo("#widget_colissimo_text_erreur").removeClass("widget_colissimo_hidden");
				   		
				   	} else {
				   		
				   		//on met un message d'erreur
				   		colissimo("#widget_colissimo_text_erreur").addClass("widget_colissimo_hidden");
				   		
				   		colissimo_widget_map = colissimo_widget_loadingMap(msg.lng, msg.lat, 14, params);
						var el = document.createElement('div');
						el.className = 'widget_colissimo_marker_home';
						  
						//console.log('add marker to map : ' + colissimo_widget_map);
						var marker = new mapboxgl.Marker(el)
						.setLngLat([msg.lng, msg.lat])
						//.setPopup(new mapboxgl.Popup().setHTML("<h1>Vous êtes ici</h1>")) // add popup
						.addTo(colissimo_widget_map);

						colissimo_widget_markers.push(marker);
				   	}
					
			   },
			    error : function(resultat, statut, erreur){
			    	console.log(statut); 
			    	console.log(erreur); 
			    } 
			});
		
		
		}
		widget_colissimo_loadPDR(params);
	
		
		var $ = jQuery;
		return this;
	},
	frameColissimoClose: function()
	{
		conteneur_widget_colissimo.html('');
		colissimo_widget_map = null;
		colissimo_widget_markers = [];
		conteneur_widget_colissimo = null;
		colissimo_widget_listPoint = [];
		colissimo_widget_callBack = null;
		colissimo_widget_paramsCharges = null;
		colissimo_widget_ismobile = null;
		colissimo_widget_messages = null;
		return this;
	}
});

var colissimo_widget_map = null;
var colissimo_widget_markers = [];
var conteneur_widget_colissimo = null;
var colissimo_widget_listPoint = [];
var colissimo_widget_callBack = null;
var colissimo_widget_paramsCharges = null;
var colissimo_widget_ismobile = null;
var colissimo_widget_messages = null;


//Pour la selection sur l'autocompletion

function widget_colissimo_select_autocomplete(val)
{
	//On efface
	colissimo("#widget_colissimo_autocomplete").html('');
	colissimo("#widget_colissimo_adresse").val(val);
}


var colissimo = jQuery;
colissimo(document).bind('keypress', function(e) {
    if(e.keyCode==13){
         if(colissimo("#widget_colissimo_adresse").is(":focus"))
         {
        	 widget_colissimo_reloadPDR();
         }
         
     } 
});

var $ = jQuery;

function widget_colissimo_showMAP()
{
	console.log('show MAP');
	document.getElementById("widget_colissimo_filtres").style.display = 'none';
	document.getElementById("widget_colissimo_liste").style.display = 'none';
	document.getElementById("widget_colissimo_map_zone").style.display = 'none';
	document.getElementById("widget_colissimo_map").scrollIntoView();
	window.scrollBy(0, -110);
	document.getElementById("widget_colissimo_map_icone").style.display = 'none';
	
	//Gestion positionnement autocomplete
	if(window.innerWidth < 501)
	{
		
		var colissimo = jQuery;
		colissimo("#widget_colissimo_autocomplete").css("top", (-window.innerHeight+100));
	}
}

function widget_colissimo_hideMAP()
{
	console.log('hide MAP');
	document.getElementById("widget_colissimo_contenu").scrollIntoView();
	document.getElementById("widget_colissimo_filtres").style.display = 'block';
	document.getElementById("widget_colissimo_liste").style.display = 'block';
	document.getElementById("widget_colissimo_map_icone").style.display = 'block';
	document.getElementById("widget_colissimo_map_zone").style.display = 'block';
	
	//Gestion positionnement autocomplete
	if(window.innerWidth < 501)
	{
		var colissimo = jQuery;
		colissimo("#widget_colissimo_autocomplete").css("top", (-window.innerHeight*2+266));
	}
}

function colissimo_widget_selectingPDR(i)
{
	var colissimo = jQuery;
	
	 
	colissimo(".widget_colissimo_bouton_validation").addClass("widget_colissimo_hidden");
	colissimo(".widget_colissimo_bouton_retour").removeClass("widget_colissimo_hidden");
	colissimo(".widget_colissimo_bouton_confirmation").removeClass("widget_colissimo_hidden");

}
function colissimo_widget_deselectingPDR(i)
{
	var colissimo = jQuery;
	
	colissimo(".widget_colissimo_bouton_validation").removeClass("widget_colissimo_hidden");
	colissimo(".widget_colissimo_bouton_retour").addClass("widget_colissimo_hidden");
	colissimo(".widget_colissimo_bouton_confirmation").addClass("widget_colissimo_hidden");
}


function colissimo_widget_validatePDR(i)
{
	console.log('validation PDR');
	var colissimo = jQuery;
	console.log(colissimo_widget_callBack);
	if(colissimo_widget_callBack !=  null)
	{
		eval(colissimo_widget_callBack + "(colissimo_widget_listPoint[i-1])");
	}
	//on ferme le widget
	//conteneur_widget_colissimo.html('');
}

function colissimo_widget_openDetails(i)
{
	var colissimo = jQuery;
	
	if(colissimo("#widget_colissimo_detailsPDR" + i).hasClass("widget_colissimo_hidden"))
	{
		colissimo("#widget_colissimo_detailsPDR" + i).removeClass("widget_colissimo_hidden");
		colissimo("#widget_colissimo_icone_coche_" + i).addClass("widget_colissimo_rotation180");
	} else {
		colissimo("#widget_colissimo_detailsPDR" + i).addClass("widget_colissimo_hidden");
		colissimo("#widget_colissimo_icone_coche_" + i).removeClass("widget_colissimo_rotation180");
	}
}

function colissimo_widget_selectPDR(i)
{
	var colissimo = jQuery;
	
	console.log('selection PDR : ' + i);
	
	if(colissimo("#widget_colissimo_detailsPDR" + i).hasClass("widget_colissimo_hidden"))
	{
		//On ferme et on dégrise tous les PDR
		colissimo(".widget_colissimo_PDR").removeClass("widget_colissimo_PDR_border");
		colissimo(".widget_colissimo_PDR").addClass("widget_colissimo_grise");
		colissimo(".widget_colissimo_detailsPDR").addClass("widget_colissimo_hidden");
		colissimo(".widget_colissimo_PDR").removeClass("widget_colissimo_selected");
		//on grise tous les markers
		colissimo(".widget_colissimo_marker_S").addClass("widget_colissimo_marker");
		colissimo(".widget_colissimo_marker_S").empty();
		colissimo(".widget_colissimo_marker_S").removeClass("widget_colissimo_marker_S");
		
		//on ouvre et on selectionne le point
		colissimo("#widget_colissimo_detailsPDR" + i).removeClass("widget_colissimo_hidden");
		colissimo("#colissimo_widget_table_PDR_" + i).addClass("widget_colissimo_selected");
		colissimo("#colissimo_widget_table_PDR_" + i).removeClass("widget_colissimo_grise");
		//Gestion coche
		colissimo("#widget_colissimo_text_coche_ouvert" + i).removeClass("widget_colissimo_hidden");
		colissimo("#widget_colissimo_text_coche" + i).addClass("widget_colissimo_hidden");
		

		//on selectionne le marker
		colissimo("#widget_colissimo_marker_" + i).addClass("widget_colissimo_marker_S");
		colissimo("#widget_colissimo_marker_" + i).removeClass("widget_colissimo_marker");
		
		
		//Gestion SVG et couleur pour le PDR selectionné
		
//		colissimo("#widget_colissimo_marker_" + i).append('test');
//		
//		//'test'.appendTo(colissimo("#widget_colissimo_marker_" + i));
		
		colissimo("#widget_colissimo_marker_" + i).append('<svg xmlns="http://www.w3.org/2000/svg" width="25.434" height="39.654" viewBox="0 0 25.434 39.654"><path fill = "' + colissimo_widget_paramsCharges.couleur1 + '" d="M735.192,302.049a12.716,12.716,0,0,0-12.716,12.716c-.2,10.21,12.716,26.938,12.716,26.938s12.825-14.766,12.716-26.938A12.716,12.716,0,0,0,735.192,302.049Z" transform="translate(-722.474 -302.049)"/><g transform="translate(5.694 4.999)"><path fill = "#ffffff" class="a" d="M7.79,9.858,1.355,13.409,8.568,17.5a.244.244,0,0,1,.117.2l0,6.144a.243.243,0,0,1-.119.2l-.386.214a.248.248,0,0,1-.115.027H8.055a.251.251,0,0,1-.123-.025l-.386-.216a.245.245,0,0,1-.117-.2l0-5.476a.152.152,0,0,0-.063-.106L1.006,14.65,1,21.83a.57.57,0,0,0,.254.443L7.8,26.117a.545.545,0,0,0,.505,0l6.463-3.85a.577.577,0,0,0,.252-.445V14.573l-4.3,2.587a.235.235,0,0,1-.113.029h0a.223.223,0,0,1-.121-.033l-.375-.233a.262.262,0,0,1-.113-.2v-.445a.251.251,0,0,1,.117-.2l0,0a.2.2,0,0,1,.054-.052l4.425-2.66L8.3,9.86a.552.552,0,0,0-.257-.06.538.538,0,0,0-.256.058" transform="translate(-1 -9.8)"/></g></svg>');
		
	} else {
		
		
		//on ferme
		colissimo("#widget_colissimo_detailsPDR" + i).addClass("widget_colissimo_hidden");
		
		colissimo(".widget_colissimo_PDR").removeClass("widget_colissimo_grise");
		colissimo("#colissimo_widget_table_PDR_" + i).removeClass("widget_colissimo_selected");
		//gestion de la coche
		colissimo("#widget_colissimo_text_coche_ouvert" + i).addClass("widget_colissimo_hidden");
		colissimo("#widget_colissimo_text_coche" + i).removeClass("widget_colissimo_hidden");
		
		//on grise tous les markers
		colissimo(".widget_colissimo_marker_S").addClass("widget_colissimo_marker");
		colissimo(".widget_colissimo_marker_S").empty();
		colissimo(".widget_colissimo_marker_S").removeClass("widget_colissimo_marker_S");
		
	}
	
	
	
	//Sur mobile, on ferme la map
	if(window.innerWidth < 501)
	{
		console.log('on ferme suite a selection');
		widget_colissimo_hideMAP();
	} else {
		
		//On met ou enleve le bord noir
		colissimo("#colissimo_widget_table_PDR_" + i).toggleClass('widget_colissimo_PDR_border');
	   	//on recentre
	   	colissimo_widget_map.setCenter([colissimo_widget_listPoint[i-1].coordGeolocalisationLongitude, colissimo_widget_listPoint[i-1].coordGeolocalisationLatitude]);
	   	//colissimo_widget_map.setZoom(14);
		
	}
	var container = colissimo('#widget_colissimo_liste');
    scrollTo = colissimo("#colissimo_widget_table_PDR_" + i);
	container.animate({
	    scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
	});
}

function widget_colissimo_reloadPDR(){
	console.log('reload PDR');
	var colissimo = jQuery;
	
	//On lance le loader
	colissimo("#widget_colissimo_loupe_img").removeClass("widget_colissimo_loupe_img");
	colissimo("#widget_colissimo_loupe_img").addClass("widget_colissimo_loader");
	
	//On efface l'autocomplete
	colissimo("#widget_colissimo_autocomplete").html('');
	
	var recherche = colissimo("#widget_colissimo_adresse").val();
	
	var tab = recherche.split(',');
	console.log(tab);
	var params = colissimo_widget_paramsCharges;
	
	if(tab != null && tab.length > 0)
	{
		params.ceAddress = tab[0].trim();
	}
	if(tab != null && tab.length > 1)
	{
		if(tab[1].match("[0-9]{4,5}") != null)
		{
			params.ceZipCode = tab[1].match("[0-9]{4,5}")[0].trim();
		}
		if(tab[1].match("[A-Za-z -]+[0-9]*$") != null)
		{
			params.ceTown = tab[1].match("[A-Za-z -]+[0-9]*$")[0].trim();
			console.log('ville = ' + params.ceTown)
		}
	}
	if(tab != null && tab.length > 2)
	{
		params.ceCountry = tab[2].trim();
	}
	
	console.log(params);

	//On recharge la map
	var adr = params.ceAddress + ', ' + params.ceZipCode + ' ' + params.ceTown + ', ' + params.ceCountry;
	console.log('adr = ' + adr);
	colissimo.ajax({
		type: "GET",
		encoding:"UTF-8",
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: "json",
				   url: params.URLColissimo + '/widget-colissimo/rest/GetLocation.rest',
				   //data: "adresse="+adr+'&token=' + params.token,
				   data: "data=" + window.btoa(JSON.stringify({addresse : adr, token : params.token})),
	    success: function(msg){
		   	console.log(msg);
		   	
		   	//on enleve les markers
		   	colissimo.each(colissimo_widget_markers, function(index, currMarker) {
		   		currMarker.remove();
		   	});
		   	
		   	//Si il n' pas trouvé l'adresse, on met des coordonnées par défaut
		   	if(msg.lng == null)
		   	{
		   		colissimo_widget_adresse_valide = false;
		   		colissimo_widget_map = colissimo_widget_loadingMap(0, 45, 4, params);
		   		
		   		//on met un message d'erreur
		   		colissimo("#widget_colissimo_text_erreur").removeClass("widget_colissimo_hidden");
		   		
		   	} else {

		   		//on met un message d'erreur
		   		colissimo("#widget_colissimo_text_erreur").addClass("widget_colissimo_hidden");
		   		
			   	//on recentre et zoom
			   	colissimo_widget_map.setCenter([msg.lng, msg.lat]);
			   	//colissimo_widget_map.setZoom(14);
	
				var el = document.createElement('div');
				el.className = 'widget_colissimo_marker_home';
				  
				
				//console.log('add marker to map : ' + colissimo_widget_map);
				var marker = new mapboxgl.Marker(el)
				.setLngLat([msg.lng, msg.lat])
				//.setPopup(new mapboxgl.Popup().setHTML("<h1>Vous êtes ici</h1>")) // add popup
				.addTo(colissimo_widget_map);
				
				colissimo_widget_markers.push(marker);
				
				widget_colissimo_loadPDR(params);
		   	}

	   },
	    error : function(resultat, statut, erreur){
	    	console.log(statut); 
	    	console.log(erreur); 
	    } 
	});
	
}


function widget_colissimo_format(s)
{
	
	var colissimo = jQuery;
	
	var mots =  s.split(' ');
	var adresse = '';
	colissimo.each(mots, function(index, mot) {
		
		if(index > 1)
		{
			if(mot.length > 3)
			{
				adresse = adresse + ' ' + mot.charAt(0).toUpperCase()+mot.substr(1);
			} else {
				adresse = adresse + ' ' + mot;
			}
		} else {
			adresse = adresse + ' ' + mot.charAt(0).toUpperCase()+mot.substr(1);
		}
	});
	return adresse;
}

function widget_colissimo_loadPDR(params)
{
	console.log('load PDR');
	var colissimo = jQuery;
	//Recherche des points de retrait
	
	var distanceMax = 0;
	
	//Si la map n'est pas chargée, on la charge
//	if(colissimo_widget_map == null)
//	{
//		colissimo_widget_map = colissimo_widget_loadingMap(2.3446, 48.8579, 4, params);
//	}
	
	colissimo.ajax({
		type: 'GET',
		encoding:"UTF-8",
		url: params.URLColissimo + '/widget-colissimo/rest/GetPointsRetraitGET.rest',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: 'json',
		data: "data=" + window.btoa(JSON.stringify({addresse : params.ceAddress.replace("’", "'"), codePostal : params.ceZipCode, ville : params.ceTown.replace("’", "'"), pays : params.ceCountry, dyPreparationTime : params.dyPreparationTime, dyWeight : params.dyWeight , token : params.token})),
		success: function(data) {
			console.log(data);
			//Si pas de point, message d'erreur
			if(data == null || data.length == 0)
			{
				colissimo("#widget_colissimo_liste").html('<div>' + colissimo_widget_messages.error + '</div>');
				return;
			}
			
			
			var listeBuffer = [];
			var i = 0;
			colissimo_widget_listPoint = data;
			
			colissimo.each(data, function(index, currEmp) {
				i++;
				//on limite �  19 PDR
				if(i < 20)
				{
					if(colissimo_widget_map != null)
					{
						//colissimo_widget_selectingPDR(i)
						var el = document.createElement('div');
						
						
//						el.append('<svg xmlns="http://www.w3.org/2000/svg" width="25.434" height="39.654" viewBox="0 0 25.434 39.654">');
//						el.append('	<path fill = "#AA0000" d="M735.192,302.049a12.716,12.716,0,0,0-12.716,12.716c-.2,10.21,12.716,26.938,12.716,26.938s12.825-14.766,12.716-26.938A12.716,12.716,0,0,0,735.192,302.049Z" transform="translate(-722.474 -302.049)"/>');
//						el.append('	<g transform="translate(5.694 4.999)">');
//						el.append('	<path fill = "#ffffff" class="a" d="M7.79,9.858,1.355,13.409,8.568,17.5a.244.244,0,0,1,.117.2l0,6.144a.243.243,0,0,1-.119.2l-.386.214a.248.248,0,0,1-.115.027H8.055a.251.251,0,0,1-.123-.025l-.386-.216a.245.245,0,0,1-.117-.2l0-5.476a.152.152,0,0,0-.063-.106L1.006,14.65,1,21.83a.57.57,0,0,0,.254.443L7.8,26.117a.545.545,0,0,0,.505,0l6.463-3.85a.577.577,0,0,0,.252-.445V14.573l-4.3,2.587a.235.235,0,0,1-.113.029h0a.223.223,0,0,1-.121-.033l-.375-.233a.262.262,0,0,1-.113-.2v-.445a.251.251,0,0,1,.117-.2l0,0a.2.2,0,0,1,.054-.052l4.425-2.66L8.3,9.86a.552.552,0,0,0-.257-.06.538.538,0,0,0-.256.058" transform="translate(-1 -9.8)"/>');
//						el.append('	</g>');
//						el.append('</svg>');
						
						
						el.className = 'widget_colissimo_marker';
						el.id="widget_colissimo_marker_" + i;
						//console.log('add marker to map : ' + colissimo_widget_map);
						var marker = new mapboxgl.Marker(el)
						.setLngLat([currEmp.coordGeolocalisationLongitude, currEmp.coordGeolocalisationLatitude])
						.addTo(colissimo_widget_map);
						
						var cp = i;
				        marker.getElement().addEventListener('click', () => {
							console.log('click!! : ' + cp);
							colissimo_widget_selectPDR(cp);
				          });
				        
				        //Que sur PC
//				        if(window.innerWidth > 500)
//				        {
//					        marker.getElement().addEventListener('mouseover', () => {
//					        	colissimo("#colissimo_widget_table_PDR_" + cp).addClass('widget_colissimo_PDR_border');
//					        	var container = colissimo('#widget_colissimo_liste');
//					            scrollTo = colissimo("#colissimo_widget_table_PDR_" + cp);
//					        	container.animate({
//					        	    scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
//					        	});
//					          }
//					        );
//					        marker.getElement().addEventListener('mouseout', () => {
//					        	colissimo(".widget_colissimo_PDR").removeClass("widget_colissimo_PDR_border");
//					          }
//					        );
//				        }

				        distanceMax = currEmp.distanceEnMetre;
				        colissimo_widget_markers.push(marker);
					}

					listeBuffer.push('<div id="colissimo_widget_table_PDR_' + i + '" class="widget_colissimo_PDR" >');
					listeBuffer.push('<table class="widget_colissimo_w100 widget_colissimo_grab widget_colissimo_padding widget_colissimo_table" onclick="colissimo_widget_selectPDR(' + i + ')" >');
					
					listeBuffer.push("<tr class='widget_colissimo_padding' >");
					listeBuffer.push('<td rowspan="5" class="widget_colissimo_w30 widget_colissimo_padding" >');	
					//listeBuffer.push('<div class="widget_colissimo_checkcircle_img" ></div>');
					listeBuffer.push("</td>");
					listeBuffer.push("<td class='widget_colissimo_padding' >");
					listeBuffer.push('<p class="widget_colissimo_text_bold" >' + currEmp.nom + '</p>');
					listeBuffer.push("</td>");
					listeBuffer.push('<td class="widget_colissimo_w70 widget_colissimo_padding ">');
					listeBuffer.push('<p class="widget_colissimo_text_bold widget_colissimo_right" style="display: none;" >4 min <img class="widget_colissimo_walking_img" src="' + params.URLColissimo + '/widget-colissimo/images/awesome-walking.svg" alt="" /></p>');
					listeBuffer.push("</td>");
					listeBuffer.push("</tr>");
					listeBuffer.push("<tr>");
					listeBuffer.push("<td class='widget_colissimo_padding' >");
					
					
					//-Majuscule en début de mot sur les adresses sauf les mots de 3 caractères ou moins et le premier en fixe toujours majuscule (rue)
					
					
					listeBuffer.push('<p class="widget_colissimo_text_normal" >' + widget_colissimo_format(currEmp.adresse1.toLowerCase()) + '</p>');
					listeBuffer.push("</td>");
					listeBuffer.push("<td class='widget_colissimo_padding' >");
					
					if(currEmp.distanceEnMetre > 999)
					{
						listeBuffer.push('<p class="widget_colissimo_text_normal widget_colissimo_right " >' + (Math.trunc(currEmp.distanceEnMetre/10)/100) + ' km</p>');
					} else {
						listeBuffer.push('<p class="widget_colissimo_text_normal widget_colissimo_right " >' + currEmp.distanceEnMetre + ' m</p>');
					}
					
					
					listeBuffer.push("</td>");
					listeBuffer.push("</tr>");
					listeBuffer.push("<tr>");
					listeBuffer.push("<td class='widget_colissimo_padding' >");	
					listeBuffer.push('<p class="widget_colissimo_text_normal" >' + widget_colissimo_format(currEmp.adresse2.toLowerCase()) + '</p>');
					listeBuffer.push("</td>");
					listeBuffer.push("</tr>");
					
					listeBuffer.push("<tr>");
					listeBuffer.push("<td class='widget_colissimo_padding' >");	
					listeBuffer.push('<p class="widget_colissimo_text_normal" >' + currEmp.codePostal + ' ' + currEmp.localite + '</p>');
					listeBuffer.push("</td>");
					listeBuffer.push("</tr>");
					
					listeBuffer.push("<tr>");
					listeBuffer.push('<td style="width:65%;" class="widget_colissimo_padding" >');	
					//listeBuffer.push('<p class="widget_colissimo_text_bold widget_colissimo_grab" onclick="colissimo_widget_openDetails(' + i + ')" >Plus de détails');
					//L'ouverture des détails se fait par la sélection
					listeBuffer.push('<p id = "widget_colissimo_text_coche' + i + '" class="widget_colissimo_text_bold" >' + colissimo_widget_messages.details);
					listeBuffer.push('<img class="widget_colissimo_icone_coche" src="' + params.URLColissimo + '/widget-colissimo/images/coche.svg" alt="" />');
					
					
					
					
					//on met le contenu du paragraphe dans un buffer pour le dupliquer ensuite
					var iconeBuffer = [];
					

					
					
					if(currEmp.accesPersonneMobiliteReduite)
					{
						iconeBuffer.push('<img class="widget_colissimo_icone_img" src="' + params.URLColissimo + '/widget-colissimo/images/awesome-wheelchair.svg" alt="" />');
					}
					if(currEmp.parking)
					{
						iconeBuffer.push('<img class="widget_colissimo_icone_img" src="' + params.URLColissimo + '/widget-colissimo/images/parking.svg" alt="" />');
					}
					
					if(currEmp.horairesOuvertureSamedi != null && currEmp.horairesOuvertureSamedi != '' && currEmp.horairesOuvertureSamedi != ' ')
					{
						iconeBuffer.push('<img class="widget_colissimo_icone_img" src="' + params.URLColissimo + '/widget-colissimo/images/calendrierS.svg" alt="" />');
					}
					if(currEmp.horairesOuvertureDimanche != null && currEmp.horairesOuvertureDimanche != '' && currEmp.horairesOuvertureDimanche != ' ')
					{
						iconeBuffer.push('<img class="widget_colissimo_icone_img" src="' + params.URLColissimo + '/widget-colissimo/images/calendrierD.svg" alt="" />');
					}
					
					
					//On va regarder si c'est ouvert après 19h
					if(currEmp.horairesOuvertureLundi != null && currEmp.horairesOuvertureLundi.length > 5)
					{
						var dernier = currEmp.horairesOuvertureLundi.replace(' ', '');
						dernier = dernier.substring(currEmp.horairesOuvertureLundi.length-7, currEmp.horairesOuvertureLundi.length-1).trim();
						var heure = dernier.substring(0,2);
						var minutes = dernier.substring(3,5);
						
						
						if(heure > 19 || (heure == 19 && minutes > 0 ))
						{
							iconeBuffer.push('<img class="widget_colissimo_icone_img" src="' + params.URLColissimo + '/widget-colissimo/images/feather-clock.svg" alt="" />');
						}
					}
					
					
					
					
					
		
					
					listeBuffer.push(iconeBuffer.join('\n'));
					listeBuffer.push("</p>");
					//Fin paragraphe
					
					listeBuffer.push('<p id = "widget_colissimo_text_coche_ouvert' + i + '" class="widget_colissimo_text_bold widget_colissimo_hidden" style="width:105%;" >' + colissimo_widget_messages.details2);
					listeBuffer.push('<img class="widget_colissimo_icone_coche widget_colissimo_rotation180" src="' + params.URLColissimo + '/widget-colissimo/images/coche.svg" alt="" />');
					listeBuffer.push(iconeBuffer.join('\n'));
					listeBuffer.push("</p>");
					
					
					listeBuffer.push("</td>");
					listeBuffer.push("<td class='widget_colissimo_padding' >");
					
					//Type de point
					var type = colissimo_widget_messages.poste;
					
					if(currEmp.typeDePoint == 'A2P')
					{
						type = colissimo_widget_messages.relais;
					}
					if(currEmp.typeDePoint == 'CIT' || currEmp.nom.toLowerCase().startsWith('consigne'))
					{
						type = colissimo_widget_messages.consigne;
					}
					listeBuffer.push('<p class="widget_colissimo_text_bold widget_colissimo_right" >' + type + '</p>');
					listeBuffer.push("</td>");
					listeBuffer.push("</tr>");
										
					listeBuffer.push("</table>");
					
					
					
					
					//DETAILS DU PDR/////////////////////////////////////
					
					listeBuffer.push('<table id="widget_colissimo_detailsPDR' + i + '" class="widget_colissimo_w100 widget_colissimo_hidden widget_colissimo_detailsPDR widget_colissimo_padding widget_colissimo_table" >');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td rowspan="8" class="widget_colissimo_w60 widget_colissimo_padding" >	');
							
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td colspan="2" class="widget_colissimo_padding" >');
					listeBuffer.push("		<p class='widget_colissimo_text_bold' >" + colissimo_widget_messages.horaires + "</p>");
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td rowspan="8" class="widget_colissimo_w60 widget_colissimo_padding" >');
							
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.lundi + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					
					if(currEmp.horairesOuvertureLundi.trim() == '' || currEmp.horairesOuvertureLundi == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureLundi + '</p>');
					}
					
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.mardi + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					
					if(currEmp.horairesOuvertureMardi.trim() == '' || currEmp.horairesOuvertureMardi == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureMardi + '</p>');
					}
					
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.mercredi + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					
					if(currEmp.horairesOuvertureMercredi.trim() == '' || currEmp.horairesOuvertureMercredi == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureMercredi + '</p>');
					}
					
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.jeudi + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					
					if(currEmp.horairesOuvertureJeudi.trim() == '' || currEmp.horairesOuvertureJeudi == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureJeudi + '</p>');
					}
					
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.vendredi + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					
					if(currEmp.horairesOuvertureVendredi.trim() == '' || currEmp.horairesOuvertureVendredi == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureVendredi + '</p>');
					}
					
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.samedi + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					
					if(currEmp.horairesOuvertureSamedi.trim() == '' || currEmp.horairesOuvertureSamedi == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureSamedi + '</p>');
					}
					
					
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.dimanche + '</p>');
					listeBuffer.push('	</td>');
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					if(currEmp.horairesOuvertureDimanche.trim() == '' || currEmp.horairesOuvertureDimanche == null)
					{
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + colissimo_widget_messages.ferme + '</p>');	
					} else {
						listeBuffer.push('		<p class="widget_colissimo_text_petit" >' + currEmp.horairesOuvertureDimanche + '</p>');
					}
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');
					
					listeBuffer.push('<tr>');
					listeBuffer.push('	<td colspan="4" class="widget_colissimo_padding" >');
					listeBuffer.push('		<div class="widget_colissimo_bouton_validation widget_colissimo_grab" onclick="colissimo_widget_validatePDR(' + i + ')" ><p class="widget_colissimo_text_normal_blanc widget_colissimo_text_bouton" >' + colissimo_widget_messages.livrer + '</p></div>');
					listeBuffer.push('	</td>');
					listeBuffer.push('</tr>');

					listeBuffer.push('<tr>');
					listeBuffer.push('	<td class="widget_colissimo_padding" ></td>');
				
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<div class="widget_colissimo_bouton_retour widget_colissimo_grab widget_colissimo_hidden" onclick="colissimo_widget_deselectingPDR(' + i + ')" ><p class="widget_colissimo_text_normal widget_colissimo_text_bouton" >Modifier</p></div>');
					listeBuffer.push('	</td>');
					
					listeBuffer.push('	<td class="widget_colissimo_padding" >');
					listeBuffer.push('		<div class="widget_colissimo_bouton_confirmation widget_colissimo_grab widget_colissimo_hidden" onclick="colissimo_widget_validatePDR(' + i + ')" ><p class="widget_colissimo_text_normal_blanc widget_colissimo_text_bouton" >Confirmer</p></div>');
					listeBuffer.push('	</td>');
					
					
					listeBuffer.push('	<td class="widget_colissimo_padding" ></td>');
					listeBuffer.push('</tr>');
					
					listeBuffer.push('</table>');
					
					
					
				// FIN DETAILS DU PDR/////////////////////////////////////
					
					
					listeBuffer.push("</div>");
					listeBuffer.push('<div class="widget_colissimo_barre" ></div>');
					
					
					
				}

			});
			
			colissimo("#widget_colissimo_liste").html(listeBuffer.join('\n'));
			
			////////////////////////////////AUTO FOCUS////////////////////////////////////////
			//distanceMax
			//La regression logarithmique donne cette formule :
			//13-1.41*log(x)
			if(window.innerWidth < 501)
			{
				//Formule pour mobile
                var zoom = 12.67 - 1.49*Math.log(distanceMax/1000);
			} else {
                //Version PC
                var zoom = 13.31 - 1.34*Math.log(distanceMax/1000);
            }
			if(colissimo_widget_map != null)
				colissimo_widget_map.setZoom(zoom);		
			
			//On arrête le loader
			colissimo("#widget_colissimo_loupe_img").addClass("widget_colissimo_loupe_img");
			colissimo("#widget_colissimo_loupe_img").removeClass("widget_colissimo_loader");
		},
		error : function(res) {
			//
		}
	});
	
}

function colissimo_widget_loadingMap(lng, lat, zoom, params ){
	
	//Si la map existe déj� , on ne la recharge pas. 
	if(colissimo_widget_map != null)
	{
		return colissimo_widget_map;
	}
	
	var colissimo = jQuery;
	
   	//Intégration de la MAP avec On Your Map
	mapboxgl.accessToken = null;
	var colissimo_widget_OYM_APP_KEY = null;
	//On va chercher les clefs
	colissimo.ajax({
		type: 'GET',
		async: false,
		encoding:"UTF-8",
		url: params.URLColissimo + '/widget-colissimo/rest/GetKey.rest',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: 'json',
		data: "data=" + window.btoa(JSON.stringify({token : params.token})),
		success: function(data) {
			mapboxgl.accessToken = data;
		}
	});	
	colissimo.ajax({
		type: 'GET',
		async: false,
		encoding:"UTF-8",
		url: params.URLColissimo + '/widget-colissimo/rest/GetKeyOYM.rest',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: 'json',
		data: "data=" + window.btoa(JSON.stringify({token : params.token})),
		success: function(data) {
			colissimo_widget_OYM_APP_KEY = data;
		}
	});	
	
	var map = new mapboxgl.Map({
		container: 'widget_colissimo_map', 
		style: {
			'version': 8,
			'sources': {
				'oym-tiles': {
					'type': 'raster',
					'tiles': [
					'https://t1-maps.onyourmap.com/oym?f=m&ft=png_std_256&x={x}&y={y}&z={z}&key=' + colissimo_widget_OYM_APP_KEY + '&Referer=http://ws.colissimo.fr',
					'https://t2-maps.onyourmap.com/oym?f=m&ft=png_std_256&x={x}&y={y}&z={z}&key=' + colissimo_widget_OYM_APP_KEY + '&Referer=http://ws.colissimo.fr',
					'https://t3-maps.onyourmap.com/oym?f=m&ft=png_std_256&x={x}&y={y}&z={z}&key=' + colissimo_widget_OYM_APP_KEY + '&Referer=http://ws.colissimo.fr',
					'https://t4-maps.onyourmap.com/oym?f=m&ft=png_std_256&x={x}&y={y}&z={z}&key=' + colissimo_widget_OYM_APP_KEY + '&Referer=http://ws.colissimo.fr'
					],
					'tileSize': 256,
					'attribution': '&copy; onyourmap &copy; Here 2021'
				}
			},
			'layers': [
				{
				'id': 'oym-tiles',
				'type': 'raster',
				'source': 'oym-tiles',
				'minzoom': 2,
				'maxzoom': 18
				}
			]
		},
		transformRequest: function(url, resourceType) {
			if (resourceType === 'Tile') {
				var x = NaN;
				var y = NaN;
				var z = NaN;
				var tokens = url.split("&");
				for (var i = 0; i < tokens.length; i++) {
					if (tokens[i].indexOf("x=") == 0) x = tokens[i].substring(2);
					if (tokens[i].indexOf("y=") == 0) y = tokens[i].substring(2);
					if (tokens[i].indexOf("z=") == 0) z = tokens[i].substring(2);
				}

				var oymZ = 18 - z;
				var oymY = Math.pow(2, z - 1) - 1 - y;
				var oymX = x - Math.pow(2, z - 1);


				var oymUrl = url.replace("&x=" + x, "&x=" + oymX).replace("&y=" + y, "&y=" + oymY).replace("&z=" + z, "&z=" + oymZ);

				return {
					url: oymUrl,
				}
			}
		},
		
		center: [lng, lat], 
		zoom: zoom,
		minZoom: 2,
		maxZoom: 17
	});

	// supprimer le logo mapbox (normalement pas le droit avec les versions mapbox 2.x)
	document.getElementsByClassName("mapboxgl-ctrl-logo")[0].style.display = "none";
	
	return map;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////LES TROIS CSS POUR PC, MOBILE ET TABLETTE///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function colissimo_widget_css(params){
	
	var cssBuffer = [];
	
	cssBuffer.push("<style>");
	
		
	
	cssBuffer.push(".widget_colissimo_logo {");
	cssBuffer.push("padding-left: 39px;");
	cssBuffer.push("padding-right: 29px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker_home {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/ionic-md-locate.svg');");
	cssBuffer.push("background-size: cover;");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 25px;");
	cssBuffer.push("border-radius: 50%;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDR.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker:hover {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDRS.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker_S {");
	//cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDRS.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	
	
	
	//FORCER LES PADDINGS POUR EMPECHER LES CMS DE CASSER lA MISE EN PAGE
	cssBuffer.push(".widget_colissimo_padding {");
	cssBuffer.push("padding:1px!important");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_padding0 {");
	cssBuffer.push("padding:0px!important");
	cssBuffer.push("}");
	
	
	//LES CSS DU LAYER WIDGET
	cssBuffer.push(".widget_colissimo_contenu {");
	cssBuffer.push("width: 1000px;");
	cssBuffer.push("height: 574px;"); //PRESTASHOP : On enleve 2 pixels en esperant que cela corrige le souci sur prestashop d'affichage de la map
	cssBuffer.push("background: #F7F7F7 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin: auto;");
	cssBuffer.push("box-sizing: content-box !important;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_map {");
	cssBuffer.push("width: 598px;");
	cssBuffer.push("height: 483px;");
	cssBuffer.push("background: #AAAAAA 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("border-left: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("float: right;");
	cssBuffer.push("top: -57px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_map_img {");
	cssBuffer.push("display: none;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_liste {");
	cssBuffer.push("overflow-y: scroll;");
	cssBuffer.push("width: 400px;");
	cssBuffer.push("height: 426px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("float: left;");
	cssBuffer.push("}");
	
	
	
	cssBuffer.push(".widget_colissimo_filtres {");
	cssBuffer.push("width: 400px;");
	cssBuffer.push("height: 56px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("font: normal normal normal 13px/16px " + params.font + " ;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_icone_img_filtre {");
	cssBuffer.push("width: 19px;");
	cssBuffer.push("height: 19px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 1px;");
	cssBuffer.push("margin-right : 1px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_table {");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_table_filtres {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 100%;");
	cssBuffer.push("text-align: center;");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");
	
	
	
	/* bouton close */
	cssBuffer.push(".widget_colissimo_close {");
	cssBuffer.push("color: #000;");
	cssBuffer.push("float: right;");
	cssBuffer.push("font: normal normal bold 35px/35px Montserrat;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-right: 10px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_close:hover, .widget_colissimo_close:focus {");
	cssBuffer.push("color: #000;");
	cssBuffer.push("text-decoration: none;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");

	
	
	
	cssBuffer.push(".widget_colissimo_sliders {");
	//cssBuffer.push("width: 45px;");
	cssBuffer.push("height: 36px;");
	//cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-radius: 23px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-top: 20px;");
	cssBuffer.push("margin-bottom: 20px;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_sliders_img {");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 21px;");
	//cssBuffer.push("transform: matrix(0, -1, 1, 0, 0, 0);");
	//cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/awesome-sliders-h.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 13px;");
	cssBuffer.push("top: 12px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_loupe_img {");
	cssBuffer.push("width: 24px;");
	cssBuffer.push("height: 24px;");
	//cssBuffer.push("transform: matrix(0, -1, 1, 0, 0, 0);");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/loupe.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("background-size: 24px 24px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 18px;");
	cssBuffer.push("top: 8px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////LE LOADER///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


	cssBuffer.push(".widget_colissimo_loader {");
	cssBuffer.push("border: 4px solid #f3f3f3;");
	cssBuffer.push("border-top: 4px solid #BDBDBD;");
	cssBuffer.push("border-radius: 50%;");
	cssBuffer.push("width: 14px;");
	cssBuffer.push("height: 14px;");
	cssBuffer.push("animation: testouille 2s linear infinite;");
	
	cssBuffer.push("margin-left: 18px;");
	cssBuffer.push("top: 8px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	
	cssBuffer.push("}");
	cssBuffer.push("@keyframes testouille {");
	cssBuffer.push("0% { transform: rotate(0deg); }");
	cssBuffer.push("100% { transform: rotate(360deg); }");
	cssBuffer.push("}");


///////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	cssBuffer.push(".widget_colissimo_point_img {");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 18px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 5px;");
	cssBuffer.push("margin-right: 5px;");
	cssBuffer.push("top: 3px;");
	cssBuffer.push("left: 40px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
	
	
	
	cssBuffer.push(".widget_colissimo_input_text_normal {");
	cssBuffer.push("width: 387px;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("margin-top: 26px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #CDCDCD;");
	cssBuffer.push("border-radius: 8px 0px 0px 8px;");
	cssBuffer.push("opacity: 1;");
		
	cssBuffer.push("font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
		
	cssBuffer.push("padding-left: 45px!important;");
	cssBuffer.push("padding-right: 0px!important;");
	cssBuffer.push("padding-top: 0px!important;");
	cssBuffer.push("padding-bottom: 0px!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_text_normal {");
	cssBuffer.push("text-align: left;");
	cssBuffer.push("font: normal normal normal 13px  " + params.font + " ;");
	cssBuffer.push("line-height: 10px;");
	cssBuffer.push("margin : 0px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_text_erreur {");
	cssBuffer.push("text-align: center;");
	cssBuffer.push("font: normal normal normal 11px  " + params.font + " ;");
	cssBuffer.push("line-height: 10px;");
	cssBuffer.push("margin : 0px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #FF0000;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("top: 10px!important;");
	cssBuffer.push("position: relative!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_text_bold {");
	cssBuffer.push("text-align: left;");
	cssBuffer.push("font: normal normal bold 12px  " + params.font + " ;");
	cssBuffer.push("margin-bottom : 2px;");
	cssBuffer.push("margin-top : 10px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_mode_transport {");
	cssBuffer.push("width: 60px;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("margin-top: 26px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #CDCDCD;");
	cssBuffer.push("border-left : 0px;");
	cssBuffer.push("border-radius: 0px 8px 8px 0px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("left : 50px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_table_adresse {");
	cssBuffer.push("border-collapse: collapse;");
	cssBuffer.push("text-indent: initial;");
	cssBuffer.push("border-spacing: 0px;");
	cssBuffer.push("padding : 0px!important;");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_checkcircle_img {");
	cssBuffer.push("width: 21px;");
	cssBuffer.push("height: 21px;");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/awesome-check-circle.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("margin-left : 21px;");
	cssBuffer.push("margin-right : 21px;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_walking_img {");
	cssBuffer.push("width: 11px;");
	cssBuffer.push("height: 17px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 5px;");
	cssBuffer.push("margin-right : 5px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_icone_img {");
	cssBuffer.push("width: 16px;");
	cssBuffer.push("height: 16px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 1px;");
	cssBuffer.push("margin-right : 1px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_right {");
	cssBuffer.push("text-align: right;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_w100 {");
	cssBuffer.push("width: 95%;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_w60 {");
	cssBuffer.push("width: 60px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_w30 {");
	cssBuffer.push("width: 20px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_margin_right20 {");
	cssBuffer.push("margin-right: 20px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_barre {");
	cssBuffer.push("margin-left : 5%;");
	cssBuffer.push("margin-right : 5%;");
	cssBuffer.push("width: 90%;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_w70 {");
	cssBuffer.push("width: 70px;");
	cssBuffer.push("}");
	
	
	
	
	//POUR LES DETAILS DES PDR/////////////////
	
	cssBuffer.push(".widget_colissimo_PDR {");
	cssBuffer.push("padding-bottom : 10px!important;");
	cssBuffer.push("border-left: solid 8px #FBFBFB;");
	cssBuffer.push("background: #FBFBFB 0% 0% no-repeat padding-box;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_PDR:hover {");
	cssBuffer.push("padding-bottom : 10px;!important");
	cssBuffer.push("border-left: solid 8px " + params.couleur1 + ";");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_PDR_border {");
	cssBuffer.push("border-left: solid 8px " + params.couleur1 + "!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_grab {");
	cssBuffer.push("	cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_hidden {");
	cssBuffer.push("	display : none;");
	cssBuffer.push("}");


	cssBuffer.push(".widget_colissimo_text_petit {");
	cssBuffer.push("	font: normal normal normal 13px  " + params.font + " ;");
	cssBuffer.push("	text-align: left;");
	cssBuffer.push("	margin : 0px;");
	cssBuffer.push("	letter-spacing: 0px;");
	cssBuffer.push("	color: #3C3C3B;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_selected {");
	cssBuffer.push("	border-left: solid 8px #EBEBEB;");
	cssBuffer.push("	background: #EBEBEB 0% 0% no-repeat padding-box;");
	cssBuffer.push("	opacity: 0.85;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_grise {");
	cssBuffer.push("	opacity: 0.30;");
	cssBuffer.push("}");
	
	
	
	cssBuffer.push(".widget_colissimo_icone_coche {");
	cssBuffer.push("width: 14px;");
	cssBuffer.push("height: 14px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 2px;");
	cssBuffer.push("margin-right : 8px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("top: 3px;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_rotation180 {");
	cssBuffer.push("-webkit-transform: rotate(180deg);");
	cssBuffer.push("-moz-transform: rotate(180deg);");
	cssBuffer.push("-ms-transform: rotate(180deg);");
	cssBuffer.push("-o-transform: rotate(180deg);");
	cssBuffer.push("transform: rotate(180deg);");
	cssBuffer.push("}");
	
	//FIN///////////////////////////
	
	
	
	
	cssBuffer.push(".widget_colissimo_text_bouton {");
	cssBuffer.push("	font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("	text-align: left;");
	cssBuffer.push("	margin : 0px;");
	cssBuffer.push("	letter-spacing: 0px;");
	cssBuffer.push("	color: #FFFFFF;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_bouton_validation {");
	cssBuffer.push("	width: 176px;");
	cssBuffer.push("	height: 29px;");
	cssBuffer.push("	margin-top: 30px !important;");
	cssBuffer.push("	background: " + params.couleurBouton + " 0% 0% no-repeat padding-box;");
	cssBuffer.push("	border-radius: 23px;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	margin: auto;");
	cssBuffer.push("	padding-left: 32px!important;");
	cssBuffer.push("	padding-right: 11px!important;");
	cssBuffer.push("	padding-top: 11px!important;");
	cssBuffer.push("}");
	
	
	////////////////////////////////////////////
	//SERCA
	///////////////////////////////////////////
	
	
	cssBuffer.push(".widget_colissimo_autocomplete");
	cssBuffer.push("{");
	cssBuffer.push("	position: relative;");
	cssBuffer.push("	top: -569px;");
	cssBuffer.push("	left: 261px;");
	cssBuffer.push("	cursor: default;");
	cssBuffer.push("	z-index: 10000000!important;");
	cssBuffer.push("	background-color: #FFFFFF!important;");
	cssBuffer.push("	box-shadow: 0px 3px 4px #00000029;");
	cssBuffer.push("	width: 433px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_ul");
	cssBuffer.push("{");
	cssBuffer.push("	position: relative;");
	cssBuffer.push("	margin-top:0px;");
	cssBuffer.push("	margin-bottom:0px;");
	cssBuffer.push("	padding-left:0px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_li");
	cssBuffer.push("{");
	cssBuffer.push("	position: relative;");
	cssBuffer.push("	font: normal normal normal 15px/40px  " + params.font + " ;");	
	cssBuffer.push("	list-style-type: none;");	
	cssBuffer.push("	background-image: url('" + params.URLColissimo + "/widget-colissimo/images/pictoAdresse.svg');");	
	cssBuffer.push("	background-repeat: no-repeat;");	
	cssBuffer.push("	background-position: 15px center;");	
	cssBuffer.push("	padding-left: 45px!important;");
	cssBuffer.push("	padding-right: 0px!important;");	
	cssBuffer.push("	padding-top: 0px!important;");	
	cssBuffer.push("	padding-bottom: 0px!important;");	
	cssBuffer.push("	background-size: 15px;");	
	cssBuffer.push("	height: 35px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_li:hover");
	cssBuffer.push("{");	
	cssBuffer.push("	background-color: #00000020;");	
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_trait");
	cssBuffer.push("{");
	cssBuffer.push("	border-bottom: 1px solid #00000029;");
	cssBuffer.push("	margin-left:15px;");
	cssBuffer.push("	margin-right:15px;");
	cssBuffer.push("	list-style-type: none;");	
	cssBuffer.push("	height: 0px;");
	cssBuffer.push("	opacity: 0.5;");	
	cssBuffer.push("}");
	
	////////////////////////////////////////////
	
	cssBuffer.push("</style>");
	
	return cssBuffer.join('\n');

}


function colissimo_widget_css_mobile(params){
	
	var cssBuffer = [];

	
	cssBuffer.push("<style>");
	
	cssBuffer.push(".widget_colissimo_logo {");
	cssBuffer.push("padding-left: " + (window.innerWidth-200)/2 + "px;");
	cssBuffer.push("padding-right: 29px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker_home {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/ionic-md-locate.svg');");
	cssBuffer.push("background-size: cover;");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 25px;");
	cssBuffer.push("border-radius: 50%;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDR.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker:hover {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDRS.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker_S {");
	//cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDRS.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	//Prendre tout l'écran du téléphone
	
	cssBuffer.push("html, body {");
	cssBuffer.push("height: 100% !important;");
	cssBuffer.push("margin: 0 !important;");
	cssBuffer.push("padding: 0 !important;");
	//on empeche le scrolling
	cssBuffer.push("overflow-y: hidden;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo {");
	cssBuffer.push("position: absolute;");
	cssBuffer.push("top: 0;");
	cssBuffer.push("left: 0;");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 100%;");
	cssBuffer.push("}");
	
	
	//FORCER LES PADDINGS POUR EMPECHER LES CMS DE CASSER lA MISE EN PAGE
	cssBuffer.push(".widget_colissimo_padding {");
	cssBuffer.push("padding:1px!important");
	cssBuffer.push("}");
	
	
	////////////////////////////////
	//LES CSS DU LAYER WIDGET
	////////////////////////////////
	cssBuffer.push(".widget_colissimo_contenu {");
	cssBuffer.push("width: 100%;");
//	cssBuffer.push("overflow-y: scroll;");
	cssBuffer.push("background: #F7F7F7 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("box-sizing: content-box !important;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_map {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: " + (window.innerHeight-116) + "px;");
	cssBuffer.push("background: #AAAAAA 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_map_barre {");
	cssBuffer.push("width: 51px;");
	cssBuffer.push("border: 4px solid #A2A2A2;");
	cssBuffer.push("position: absolute;");
	cssBuffer.push("top: " + (window.innerHeight-48) + "px;");
	var colissimo_widget_left_barre = Math.trunc(window.innerWidth/2-32);
	cssBuffer.push("left: " + colissimo_widget_left_barre + "px;");
	cssBuffer.push("z-index: 1000;");
	cssBuffer.push("border-radius: 5px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin: 5px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_map_zone {");
	cssBuffer.push("width: " + (window.innerWidth) + "px;");
	cssBuffer.push("height: " + (window.innerHeight-112) + "px;");
	//cssBuffer.push("border: 5px solid #A2A2A2;");
	cssBuffer.push("position: absolute;");
	cssBuffer.push("top: " + (window.innerHeight-58) + "px;");
	cssBuffer.push("left: 0px;");
	cssBuffer.push("z-index: 500;");
	cssBuffer.push("opacity: 0.3;");
	cssBuffer.push("margin: 0px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_map_icone {");
	cssBuffer.push("width: 70px;");
	cssBuffer.push("height: 70px;");
	cssBuffer.push("background-size: cover;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("box-shadow: 0px 0px 6px #00000029;");
	cssBuffer.push("border: 1px solid #ECECEC;");
	cssBuffer.push("border-radius: 35px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("z-index: 1000;");
	cssBuffer.push("position: absolute;");
	cssBuffer.push("top: " + (window.innerHeight-90) + "px;");
	var colissimo_widget_left_barre = Math.trunc(window.innerWidth-90);
	cssBuffer.push("left: " + colissimo_widget_left_barre + "px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_map_liste {");
	cssBuffer.push("width: 70px;");
	cssBuffer.push("height: 70px;");
	cssBuffer.push("background-size: cover;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("box-shadow: 0px 0px 6px #00000029;");
	cssBuffer.push("border: 1px solid #ECECEC;");
	cssBuffer.push("border-radius: 35px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("z-index: 1000;");
	cssBuffer.push("position: absolute;");
	cssBuffer.push("top: " + (window.innerHeight-90) + "px;");
	//cssBuffer.push("top: " + (2*window.innerHeight-150) + "px;");
	var colissimo_widget_left_barre = Math.trunc(window.innerWidth-90);
	cssBuffer.push("left: " + colissimo_widget_left_barre + "px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_map_img {");
	cssBuffer.push("width: 30px;");
	cssBuffer.push("height: 30px;");
	cssBuffer.push("margin: 20px;");
	cssBuffer.push("}");

	
	cssBuffer.push(".widget_colissimo_liste {");
	cssBuffer.push("overflow-y: scroll;");
	cssBuffer.push("width: 100%;");
	
	//on calcule la hauteur en fonction de l'écran. 
	var colissimo_widget_hauteur_liste = Math.trunc(window.innerHeight-220);
	cssBuffer.push("height: " + colissimo_widget_hauteur_liste + "px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_filtres {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 56px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("font: normal normal normal 13px/16px  " + params.font + " ;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_icone_img_filtre {");
	cssBuffer.push("width: 19px;");
	cssBuffer.push("height: 19px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 1px;");
	cssBuffer.push("margin-right : 1px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_table_filtres {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 100%;");
	cssBuffer.push("text-align: center;");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_table {");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");
	
	////////////////////////////////
	//FIN LES CSS DU LAYER WIDGET
	////////////////////////////////
	
	/* bouton close */
	cssBuffer.push(".widget_colissimo_close {");
	cssBuffer.push("color: #000;");
	cssBuffer.push("float: right;");
	cssBuffer.push("font: normal normal bold 35px/35px Montserrat;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-right: 10px;");
	cssBuffer.push("margin-bottom: -20px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_close:hover, .widget_colissimo_close:focus {");
	cssBuffer.push("color: #000;");
	cssBuffer.push("text-decoration: none;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	
	
	cssBuffer.push(".widget_colissimo_sliders {");
	cssBuffer.push("height: 25px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-radius: 23px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin: 26px;");
	cssBuffer.push("display : none;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_sliders_img {");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 21px;");
	//cssBuffer.push("transform: matrix(0, -1, 1, 0, 0, 0);");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/awesome-sliders-h.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 13px;");
	cssBuffer.push("top: 12px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_loupe_img {");
	cssBuffer.push("width: 24px;");
	cssBuffer.push("height: 24px;");
	//cssBuffer.push("transform: matrix(0, -1, 1, 0, 0, 0);");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/loupe.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("background-size: 24px 24px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 18px;");
	cssBuffer.push("top: 8px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////LE LOADER///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


cssBuffer.push(".widget_colissimo_loader {");
cssBuffer.push("border: 4px solid #f3f3f3;");
cssBuffer.push("border-top: 4px solid #BDBDBD;");
cssBuffer.push("border-radius: 50%;");
cssBuffer.push("width: 14px;");
cssBuffer.push("height: 14px;");
cssBuffer.push("animation: testouille 2s linear infinite;");

cssBuffer.push("margin-left: 18px;");
cssBuffer.push("top: 8px;");
cssBuffer.push("position: relative;");
cssBuffer.push("vertical-align: unset !important;");

cssBuffer.push("}");
cssBuffer.push("@keyframes testouille {");
cssBuffer.push("0% { transform: rotate(0deg); }");
cssBuffer.push("100% { transform: rotate(360deg); }");
cssBuffer.push("}");


///////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////Barre de recherche///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	cssBuffer.push(".widget_colissimo_point_img {");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 18px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("opacity: 1;");
	//cssBuffer.push("margin-left: 5px;");
	//cssBuffer.push("margin-right: 5px;");
	cssBuffer.push("top: 10px;");
	cssBuffer.push("left: 20%;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_point_input {");
	cssBuffer.push("margin-top: 20px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("margin-left: 0px;");
	//cssBuffer.push("width: 30px;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-left: 1px solid #CDCDCD;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("border-bottom: 1px solid #CDCDCD;");
	cssBuffer.push("border-radius: 8px 0px 0px 8px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding-top: 0px!important;");
	cssBuffer.push("padding-bottom: 0px!important;");
	//cssBuffer.push("float:right;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_input_text_normal {");
	cssBuffer.push("margin-top: 20px;");
	cssBuffer.push("margin-bottom: 26px;");
	//cssBuffer.push("margin-left: -3px;");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-left: 0px solid #CDCDCD;");
	cssBuffer.push("border-right: 1px solid #CDCDCD;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("border-bottom: 1px solid #CDCDCD;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("padding-left: 5px!important;");
	//cssBuffer.push("padding-right: 0px;");
	cssBuffer.push("padding-top: 0px!important;");
	cssBuffer.push("padding-bottom: 0px!important;");
	//cssBuffer.push("float:right;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_mode_transport {");
	cssBuffer.push("width: 60px;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #CDCDCD;");
	cssBuffer.push("border-left : 0px;");
	cssBuffer.push("border-radius: 0px 8px 8px 0px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("left : 50px;");
	//cssBuffer.push("margin-left : -3px;");
	//cssBuffer.push("margin-top: -4px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("margin-top: 20px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_text_erreur {");
	cssBuffer.push("text-align: center;");
	cssBuffer.push("font: normal normal normal 11px  " + params.font + " ;");
	cssBuffer.push("line-height: 10px;");
	cssBuffer.push("margin : 0px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #FF0000;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("top: 10px!important;");
	cssBuffer.push("position: relative!important;");
	cssBuffer.push("}");
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	cssBuffer.push(".widget_colissimo_text_normal {");
	cssBuffer.push("text-align: left;");
	cssBuffer.push("font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("line-height: 10px;");
	cssBuffer.push("margin : 0px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_text_bold {");
	cssBuffer.push("text-align: left;");
	cssBuffer.push("font: normal normal bold 14px  " + params.font + " ;");
	cssBuffer.push("margin-bottom : 2px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_table_adresse {");
	cssBuffer.push("margin: auto;");
	cssBuffer.push("border-collapse: collapse;");
	cssBuffer.push("text-indent: initial;");
	cssBuffer.push("border-spacing: 0px;");
	cssBuffer.push("padding : 0px!important;");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_checkcircle_img {");
	cssBuffer.push("width: 21px;");
	cssBuffer.push("height: 21px;");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/awesome-check-circle.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("margin-left : 21px;");
	cssBuffer.push("margin-right : 21px;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_walking_img {");
	cssBuffer.push("width: 11px;");
	cssBuffer.push("height: 17px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 5px;");
	cssBuffer.push("margin-right : 5px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_icone_img {");
	cssBuffer.push("width: 16px;");
	cssBuffer.push("height: 16px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 1px;");
	cssBuffer.push("margin-right : 1px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_right {");
	cssBuffer.push("text-align: right;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_w95 {");
	cssBuffer.push("width: 95%;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_w100 {");
	cssBuffer.push("width: 90%;");
	cssBuffer.push("margin-left: 18px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_w60 {");
	cssBuffer.push("width: 60px;");
	cssBuffer.push("display : none;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_w30 {");
	cssBuffer.push("width: 30px;");
	cssBuffer.push("display : none;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_margin_right20 {");
	cssBuffer.push("margin-right: 20px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_barre {");
	cssBuffer.push("margin-left : 5%;");
	cssBuffer.push("margin-right : 5%;");
	cssBuffer.push("width: 90%;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_w70 {");
	cssBuffer.push("width: 70px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_padding0 {");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	
	
	//POUR LES DETAILS DES PDR/////////////////
	
	cssBuffer.push(".widget_colissimo_PDR {");
	cssBuffer.push("padding-bottom : 18px!important;");
	cssBuffer.push("border-left: solid 8px FBFBFB;");
	cssBuffer.push("background: #FBFBFB 0% 0% no-repeat padding-box;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_PDR:hover {");
	cssBuffer.push("padding-bottom : 18px!important;");
	cssBuffer.push("border-left: solid 8px " + params.couleur1 + ";");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_grab {");
	cssBuffer.push("	cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_hidden {");
	cssBuffer.push("	display : none;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_text_petit {");
	cssBuffer.push("	font: normal normal normal 13px  " + params.font + " ;");
	cssBuffer.push("	text-align: left;");
	cssBuffer.push("	margin : 0px;");
	cssBuffer.push("	letter-spacing: 0px;");
	cssBuffer.push("	color: #3C3C3B;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_selected {");
	cssBuffer.push("	background: #EBEBEB 0% 0% no-repeat padding-box;");
	cssBuffer.push("	opacity: 0.85;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_grise {");
	cssBuffer.push("	opacity: 0.30;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_icone_coche {");
	cssBuffer.push("width: 14px;");
	cssBuffer.push("height: 14px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 2px;");
	cssBuffer.push("margin-right : 8px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("top: 3px;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_rotation180 {");
	cssBuffer.push("-webkit-transform: rotate(180deg);");
	cssBuffer.push("-moz-transform: rotate(180deg);");
	cssBuffer.push("-ms-transform: rotate(180deg);");
	cssBuffer.push("-o-transform: rotate(180deg);");
	cssBuffer.push("transform: rotate(180deg);");
	cssBuffer.push("}");
	
	//FIN///////////////////////////
	
	cssBuffer.push(".widget_colissimo_text_bouton {");
	cssBuffer.push("	font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("	text-align: left;");
	cssBuffer.push("	margin : 0px;");
	cssBuffer.push("	letter-spacing: 0px;");
	cssBuffer.push("	color: #FFFFFF;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_bouton_validation {");
	cssBuffer.push("	width: 176px;");
	cssBuffer.push("	height: 29px;");
	cssBuffer.push("	margin-top: 30px !important;");
	cssBuffer.push("	background: " + params.couleurBouton + " 0% 0% no-repeat padding-box;");
	cssBuffer.push("	border-radius: 23px;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	margin: auto;");
	cssBuffer.push("	padding-left: 32px!important;");
	cssBuffer.push("	padding-right: 11px!important;");
	cssBuffer.push("	padding-top: 11px!important;");
	cssBuffer.push("}");
	
	
	////////////////////////////////////////////
	//SERCA
	///////////////////////////////////////////
	
	
	cssBuffer.push(".widget_colissimo_autocomplete");
	cssBuffer.push("{");
	cssBuffer.push("	position: relative;");
	cssBuffer.push("	top:" + (-window.innerHeight*2+266) + "px;");
	//cssBuffer.push("	left: 0px;");
	cssBuffer.push("	cursor: default;");
	cssBuffer.push("	z-index: 10000000!important;");
	cssBuffer.push("	background-color: #FFFFFF!important;");
	cssBuffer.push("	box-shadow: 0px 3px 4px #00000029;");
	cssBuffer.push("	width: " + (window.innerWidth-2) + "px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_ul");
	cssBuffer.push("{");
	cssBuffer.push("	margin-top:0px;");
	cssBuffer.push("	margin-bottom:0px;");
	cssBuffer.push("	padding-left:0px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_li");
	cssBuffer.push("{");
	cssBuffer.push("	font: normal normal normal 15px/40px  " + params.font + " ;");	
	cssBuffer.push("	list-style-type: none;");	
	cssBuffer.push("	background-image: url('" + params.URLColissimo + "/widget-colissimo/images/pictoAdresse.svg');");	
	cssBuffer.push("	background-repeat: no-repeat;");	
	cssBuffer.push("	background-position: 15px center;");	
	cssBuffer.push("	padding-left: 45px!important;");	
	cssBuffer.push("	background-size: 15px;");	
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_li:hover");
	cssBuffer.push("{");	
	cssBuffer.push("	background-color: #00000020;");	
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_trait");
	cssBuffer.push("{");
	cssBuffer.push("	border-bottom: 1px solid #00000029;");
	cssBuffer.push("	margin-left:15px;");
	cssBuffer.push("	margin-right:15px;");
	cssBuffer.push("	list-style-type: none;");	
	cssBuffer.push("	height: 0px;");
	cssBuffer.push("	opacity: 0.5;");	
	cssBuffer.push("}");
	
	////////////////////////////////////////////
	
	
	
	cssBuffer.push("</style>");
	
	return cssBuffer.join('\n');

}

function colissimo_widget_css_ipad(params){
	
	var cssBuffer = [];
	
	cssBuffer.push("<style>");
	
	cssBuffer.push(".widget_colissimo_logo {");
	cssBuffer.push("padding-left: 39px;");
	cssBuffer.push("padding-right: 0px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker_home {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/ionic-md-locate.svg');");
	cssBuffer.push("background-size: cover;");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 25px;");
	cssBuffer.push("border-radius: 50%;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDR.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker:hover {");
	cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDRS.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_marker_S {");
	//cssBuffer.push("background-image: url('" + params.URLColissimo + "/widget-colissimo/images/markerPDRS.svg');");
	cssBuffer.push("width: 25px;");
	cssBuffer.push("height: 40px;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	
	//FORCER LES PADDINGS POUR EMPECHER LES CMS DE CASSER lA MISE EN PAGE
	cssBuffer.push(".widget_colissimo_padding {");
	cssBuffer.push("padding:1px!important");
	cssBuffer.push("}");
	
	////////////////////////////////
	//LES CSS DU LAYER WIDGET
	////////////////////////////////
	cssBuffer.push(".widget_colissimo_contenu {");
	cssBuffer.push("width: 100%;");
//	cssBuffer.push("overflow-y: scroll;");
	cssBuffer.push("background: #F7F7F7 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("box-sizing: content-box !important;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_map {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 585px;");
	cssBuffer.push("background: #AAAAAA 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_liste {");
	cssBuffer.push("overflow-y: scroll;");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 706px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_filtres {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 56px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-top: 1px solid #E7E7E7;");
	cssBuffer.push("font: normal normal normal 13px/16px  " + params.font + " ;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_icone_img_filtre {");
	cssBuffer.push("width: 19px;");
	cssBuffer.push("height: 19px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 1px;");
	cssBuffer.push("margin-right : 1px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_table_filtres {");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 100%;");
	cssBuffer.push("text-align: center;");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_table {");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");
	
	////////////////////////////////
	//FIN LES CSS DU LAYER WIDGET
	////////////////////////////////
	
	/* bouton close */
	cssBuffer.push(".widget_colissimo_close {");
	cssBuffer.push("color: #000;");
	cssBuffer.push("float: right;");
	cssBuffer.push("font: normal normal bold 35px/35px Montserrat;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-right: 10px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_close:hover, .widget_colissimo_close:focus {");
	cssBuffer.push("color: #000;");
	cssBuffer.push("text-decoration: none;");
	cssBuffer.push("cursor: pointer;");
	cssBuffer.push("}");
	
	
	
	cssBuffer.push(".widget_colissimo_sliders {");
	cssBuffer.push("width: 45px;");
	cssBuffer.push("height: 45px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-radius: 23px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin: 26px;");
	cssBuffer.push("display : none;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_sliders_img {");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 21px;");
	//cssBuffer.push("transform: matrix(0, -1, 1, 0, 0, 0);");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/awesome-sliders-h.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 13px;");
	cssBuffer.push("top: 12px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_loupe_img {");
	cssBuffer.push("width: 24px;");
	cssBuffer.push("height: 24px;");
	//cssBuffer.push("transform: matrix(0, -1, 1, 0, 0, 0);");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/loupe.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("background-size: 24px 24px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 18px;");
	cssBuffer.push("top: 8px;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_point_img {");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 18px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("margin-left: 5px;");
	cssBuffer.push("margin-right: 5px;");
	cssBuffer.push("top: 36px;");
	cssBuffer.push("left: 32%;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
	
	
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////LE LOADER///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


cssBuffer.push(".widget_colissimo_loader {");
cssBuffer.push("border: 4px solid #f3f3f3;");
cssBuffer.push("border-top: 4px solid #BDBDBD;");
cssBuffer.push("border-radius: 50%;");
cssBuffer.push("width: 14px;");
cssBuffer.push("height: 14px;");
cssBuffer.push("animation: testouille 2s linear infinite;");

cssBuffer.push("margin-left: 18px;");
cssBuffer.push("top: 8px;");
cssBuffer.push("position: relative;");
cssBuffer.push("vertical-align: unset !important;");

cssBuffer.push("}");
cssBuffer.push("@keyframes testouille {");
cssBuffer.push("0% { transform: rotate(0deg); }");
cssBuffer.push("100% { transform: rotate(360deg); }");
cssBuffer.push("}");


///////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////Barre de recherche///////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	cssBuffer.push(".widget_colissimo_point_img {");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("width: 18px;");
	cssBuffer.push("height: 18px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("opacity: 1;");
	//cssBuffer.push("margin-left: 5px;");
	//cssBuffer.push("margin-right: 5px;");
	cssBuffer.push("top: 10px;");
	cssBuffer.push("left: 20%;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_point_input {");
	cssBuffer.push("margin-top: 26px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("margin-left: 0px;");
	//cssBuffer.push("width: 30px;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-left: 1px solid #CDCDCD;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("border-bottom: 1px solid #CDCDCD;");
	cssBuffer.push("border-radius: 8px 0px 0px 8px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding-top: 0px!important;");
	cssBuffer.push("padding-bottom: 0px!important;");
	//cssBuffer.push("float:right;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_input_text_normal {");
	cssBuffer.push("margin-top: 26px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("margin-left: -3px;");
	cssBuffer.push("width: 100%;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border-left: 0px solid #CDCDCD;");
	cssBuffer.push("border-right: 1px solid #CDCDCD;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("border-bottom: 1px solid #CDCDCD;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("padding-left: 5px!important;");
	//cssBuffer.push("padding-right: 0px;");
	cssBuffer.push("padding-top: 0px!important;");
	cssBuffer.push("padding-bottom: 0px!important;");
	//cssBuffer.push("float:right;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_mode_transport {");
	cssBuffer.push("width: 60px;");
	cssBuffer.push("height: 36px;");
	cssBuffer.push("background: #FFFFFF 0% 0% no-repeat padding-box;");
	cssBuffer.push("border: 1px solid #CDCDCD;");
	cssBuffer.push("border-left : 0px;");
	cssBuffer.push("border-radius: 0px 8px 8px 0px;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("left : 50px;");
	//cssBuffer.push("margin-left : -3px;");
	//cssBuffer.push("margin-top: -4px;");
	cssBuffer.push("margin-bottom: 26px;");
	cssBuffer.push("margin-top: 26px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_text_erreur {");
	cssBuffer.push("text-align: center;");
	cssBuffer.push("font: normal normal normal 11px  " + params.font + " ;");
	cssBuffer.push("line-height: 10px;");
	cssBuffer.push("margin : 0px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #FF0000;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("top: 10px!important;");
	cssBuffer.push("position: relative!important;");
	cssBuffer.push("}");
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	


	cssBuffer.push(".widget_colissimo_text_normal {");
	cssBuffer.push("text-align: left;");
	cssBuffer.push("font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("line-height: 10px;");
	cssBuffer.push("margin : 0px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_text_bold {");
	cssBuffer.push("text-align: left;");
	cssBuffer.push("font: normal normal bold 14px  " + params.font + " ;");
	cssBuffer.push("margin-bottom : 2px;");
	cssBuffer.push("letter-spacing: 0px;");
	cssBuffer.push("color: #3C3C3B;");
	cssBuffer.push("opacity: 1;");
	cssBuffer.push("padding:0px!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_table_adresse {");
	cssBuffer.push("margin: auto;");
	cssBuffer.push("border-collapse: collapse;");
	cssBuffer.push("text-indent: initial;");
	cssBuffer.push("border-spacing: 0px;");
	cssBuffer.push("padding : 0px!important;");
	cssBuffer.push("border-collapse: inherit!important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_checkcircle_img {");
	cssBuffer.push("width: 21px;");
	cssBuffer.push("height: 21px;");
	cssBuffer.push("background: transparent url('" + params.URLColissimo + "/widget-colissimo/images/awesome-check-circle.svg') 0% 0% no-repeat padding-box;");
	cssBuffer.push("margin-left : 21px;");
	cssBuffer.push("margin-right : 21px;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_walking_img {");
	cssBuffer.push("width: 11px;");
	cssBuffer.push("height: 17px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 5px;");
	cssBuffer.push("margin-right : 5px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_icone_img {");
	cssBuffer.push("width: 16px;");
	cssBuffer.push("height: 16px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 1px;");
	cssBuffer.push("margin-right : 1px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("vertical-align: unset !important;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_right {");
	cssBuffer.push("text-align: right;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_w95 {");
	cssBuffer.push("width: 95%;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_w100 {");
	cssBuffer.push("width: 90%;");
	cssBuffer.push("margin-left: 18px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_w60 {");
	cssBuffer.push("width: 60px;");
	cssBuffer.push("display : none;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_w30 {");
	cssBuffer.push("width: 30px;");
	cssBuffer.push("display : none;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_margin_right20 {");
	cssBuffer.push("margin-right: 20px;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_barre {");
	cssBuffer.push("margin-left : 5%;");
	cssBuffer.push("margin-right : 5%;");
	cssBuffer.push("width: 90%;");
	cssBuffer.push("border-top: 1px solid #CDCDCD;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_w70 {");
	cssBuffer.push("width: 70px;");
	cssBuffer.push("}");
	
	
	
	//POUR LES DETAILS DES PDR/////////////////
	
	cssBuffer.push(".widget_colissimo_PDR {");
	cssBuffer.push("padding-bottom : 18px!important;");
	cssBuffer.push("border-left: solid 8px FBFBFB;");
	cssBuffer.push("background: #FBFBFB 0% 0% no-repeat padding-box;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_PDR:hover {");
	cssBuffer.push("padding-bottom : 18px!important;");
	cssBuffer.push("border-left: solid 8px " + params.couleur1 + ";");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_grab {");
	cssBuffer.push("	cursor: pointer;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_hidden {");
	cssBuffer.push("	display : none;");
	cssBuffer.push("}");

	cssBuffer.push(".widget_colissimo_text_petit {");
	cssBuffer.push("	font: normal normal normal 13px  " + params.font + " ;");
	cssBuffer.push("	text-align: left;");
	cssBuffer.push("	margin : 0px;");
	cssBuffer.push("	letter-spacing: 0px;");
	cssBuffer.push("	color: #3C3C3B;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_selected {");
	cssBuffer.push("	background: #EBEBEB 0% 0% no-repeat padding-box;");
	cssBuffer.push("	opacity: 0.85;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_grise {");
	cssBuffer.push("	opacity: 0.30;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_icone_coche {");
	cssBuffer.push("width: 14px;");
	cssBuffer.push("height: 14px;");
	cssBuffer.push("background-size: contain;");
	cssBuffer.push("margin-left : 2px;");
	cssBuffer.push("margin-right : 8px;");
	cssBuffer.push("border: 0px solid #FFFFFF;");
	cssBuffer.push("position: relative;");
	cssBuffer.push("top: 3px;");
	cssBuffer.push("}");
	
	
	cssBuffer.push(".widget_colissimo_rotation180 {");
	cssBuffer.push("-webkit-transform: rotate(180deg);");
	cssBuffer.push("-moz-transform: rotate(180deg);");
	cssBuffer.push("-ms-transform: rotate(180deg);");
	cssBuffer.push("-o-transform: rotate(180deg);");
	cssBuffer.push("transform: rotate(180deg);");
	cssBuffer.push("}");
	
	//FIN///////////////////////////
	
	cssBuffer.push(".widget_colissimo_text_bouton {");
	cssBuffer.push("	font: normal normal normal 14px  " + params.font + " ;");
	cssBuffer.push("	text-align: left;");
	cssBuffer.push("	margin : 0px;");
	cssBuffer.push("	letter-spacing: 0px;");
	cssBuffer.push("	color: #FFFFFF;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	padding:0px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_bouton_validation {");
	cssBuffer.push("	width: 176px;");
	cssBuffer.push("	height: 29px;");
	cssBuffer.push("	margin-top: 30px !important;");
	cssBuffer.push("	background: " + params.couleurBouton + " 0% 0% no-repeat padding-box;");
	cssBuffer.push("	border-radius: 23px;");
	cssBuffer.push("	opacity: 1;");
	cssBuffer.push("	margin: auto;");
	cssBuffer.push("	padding-left: 32px!important;");
	cssBuffer.push("	padding-right: 11px!important;");
	cssBuffer.push("	padding-top: 11px!important;");
	cssBuffer.push("}");
	
	
	////////////////////////////////////////////
	//SERCA
	///////////////////////////////////////////
	
	
	cssBuffer.push(".widget_colissimo_autocomplete");
	cssBuffer.push("{");
	cssBuffer.push("	position: relative;");
	cssBuffer.push("	top:-1380px;");
	//cssBuffer.push("	left: 0px;");
	cssBuffer.push("	cursor: default;");
	cssBuffer.push("	z-index: 10000000!important;");
	cssBuffer.push("	background-color: #FFFFFF!important;");
	cssBuffer.push("	box-shadow: 0px 3px 4px #00000029;");
	//cssBuffer.push("	width: " + (window.innerWidth-2) + "px!important;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_ul");
	cssBuffer.push("{");
	cssBuffer.push("	margin-top:0px;");
	cssBuffer.push("	margin-bottom:0px;");
	cssBuffer.push("	padding-left:0px;");
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_li");
	cssBuffer.push("{");
	cssBuffer.push("	font: normal normal normal 15px/40px  " + params.font + " ;");	
	cssBuffer.push("	list-style-type: none;");	
	cssBuffer.push("	background-image: url('" + params.URLColissimo + "/widget-colissimo/images/pictoAdresse.svg');");	
	cssBuffer.push("	background-repeat: no-repeat;");	
	cssBuffer.push("	background-position: 15px center;");	
	cssBuffer.push("	padding-left: 45px!important;");	
	cssBuffer.push("	background-size: 15px;");	
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_li:hover");
	cssBuffer.push("{");	
	cssBuffer.push("	background-color: #00000020;");	
	cssBuffer.push("}");
	
	cssBuffer.push(".widget_colissimo_autocomplete_trait");
	cssBuffer.push("{");
	cssBuffer.push("	border-bottom: 1px solid #00000029;");
	cssBuffer.push("	margin-left:15px;");
	cssBuffer.push("	margin-right:15px;");
	cssBuffer.push("	list-style-type: none;");	
	cssBuffer.push("	height: 0px;");
	cssBuffer.push("	opacity: 0.5;");	
	cssBuffer.push("}");
	
	////////////////////////////////////////////
	
	
	
	cssBuffer.push("</style>");
	
	return cssBuffer.join('\n');

}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////TRADUCTIONS//////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function colissimo_widget_getWording(params)
{
	var messages = new Object();
	
	console.log('langue : ' + navigator.language);
	
	
//	if(navigator.language.includes('fr'))
//	{
//		messages.pmr = 'Accès PMR1';
//		messages.parking = 'Parking';
//		messages.samedi = 'Samedi';
//		messages.dimanche = 'Dimanche';
//		messages.dixneuf = 'Après 19h';
//		messages.poste = 'Bureau de poste';
//		messages.consigne = 'Consigne';
//		messages.relais = 'Relais';
//		messages.ferme = 'Fermé';
//		messages.details = 'Plus de détails';
//		messages.details2 = 'Moins de détails';
//		messages.horaires = 'Horaires d\'ouverture';
//		messages.lundi = 'Lundi';
//		messages.mardi= 'Mardi';
//		messages.mercredi = 'Mercredi';
//		messages.jeudi = 'Jeudi';
//		messages.vendredi = 'Vendredi';
//		messages.livrer = 'Me livrer �  cette adresse';
//		messages.adresse = 'L\'adresse renseignée est incorrecte. Veuillez la modifier';
//
//	} else {
//		messages.pmr = 'Disabled acces';
//		messages.parking = 'Parking';
//		messages.samedi = 'Saturday';
//		messages.dimanche = 'Sunday';
//		messages.dixneuf = 'After 7pm';
//		messages.poste = 'Post office';
//		messages.consigne = 'Locker';
//		messages.relais = 'Relay';
//		messages.ferme = 'Closed';
//		messages.details = 'More details';
//		messages.details2 = 'Less details';
//		messages.horaires = 'Opening hours';
//		messages.lundi = 'Monday';
//		messages.mardi= 'Tuesday';
//		messages.mercredi = 'Wednesday';
//		messages.jeudi = 'Thursday';
//		messages.vendredi = 'Friday';
//		messages.livrer = 'Be delivered at this address';
//		messages.adresse = 'The address provided is incorrect. Please modify it';
//
//	}
	
	var labels = [
		'messages.pmr',
		'messages.parking',
		'messages.samedi',
		'messages.dimanche',
		'messages.dixneuf',
		'messages.poste',
		'messages.consigne',
		'messages.relais',
		'messages.ferme',
		'messages.details',
		'messages.details2',
		'messages.horaires',
		'messages.lundi',
		'messages.mardi',
		'messages.mercredi',
		'messages.jeudi',
		'messages.vendredi',
		'messages.livrer',
		'messages.adresse',
		'messages.error'
	];
	
	//On va chercher les messages
	colissimo.ajax({
		type: 'GET',
		async: false,
		encoding:"UTF-8",
		url: params.URLColissimo + '/widget-colissimo/rest/GetMessages.rest',
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		dataType: 'json',
		data: "data=" + window.btoa(JSON.stringify({labels : labels, langue : navigator.language, token : params.token})),
		success: function(data) {
			console.log('messages recuperes');
			console.log(data);
			//messages = data;
			messages.pmr=data[0].label;
			messages.parking=data[1].label;
			messages.samedi=data[2].label;
			messages.dimanche=data[3].label;
			messages.dixneuf=data[4].label;
			messages.poste=data[5].label;
			messages.consigne=data[6].label;
			messages.relais=data[7].label;
			messages.ferme=data[8].label;
			messages.details=data[9].label;
			messages.details2=data[10].label;
			messages.horaires=data[11].label;
			messages.lundi=data[12].label;
			messages.mardi=data[13].label;
			messages.mercredi=data[14].label;
			messages.jeudi=data[15].label;
			messages.vendredi=data[16].label;
			messages.livrer=data[17].label;
			messages.adresse=data[18].label;
		}
	});	

	
	return messages;

}






