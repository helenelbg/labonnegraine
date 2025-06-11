let id_product = 0;
let id_product_attribute = 0;

const price_formatter = new Intl.NumberFormat('fr-FR', {
	style: 'currency',
	currency: 'EUR',
});

function formatCurrency(price) {
	return price_formatter.format(price);
}

prestashop.on('updatedCart', function(event) { 
	// Sur la page panier, mise à jour du bloc de frais de port gratuits.
	const reduction100 = parseFloat($('#reduction100').val());
	const frais_de_port_gratuits = parseFloat($('#frais_de_port_gratuits').val());
	//const total_including_tax = parseFloat(this.cart.totals.total_including_tax.amount);
	var total_including_tax = 0;
	/*if ( this.cart.subtotals.products.amount > 0 )
	{
		total_including_tax = parseFloat(this.cart.subtotals.products.amount);
	}
	else 
	{*/
var cpt_colisEC = 0;
		for (var sub in this.cart.subtotals)
		{
			if ( this.cart.subtotals[sub] != null && this.cart.subtotals[sub].type == 'products' )
			{
				cpt_colisEC++;
				total_including_tax += this.cart.subtotals[sub].amount;
			}
		}
	//}
	if ( cpt_colisEC == 1 )
	{
		if ( total_including_tax < frais_de_port_gratuits ) {
			$('.cart_free_shipping.reste20').show();
			$('.free_shipping20').html(formatCurrency(frais_de_port_gratuits-total_including_tax));
		}else {
			$('.cart_free_shipping.reste20').hide();
		}
	}
	
	if ( total_including_tax >= frais_de_port_gratuits && total_including_tax < reduction100) {
		$('.cart_free_shipping.reste100').show();
		$('.free_shipping100').html(formatCurrency(reduction100-total_including_tax));
	}else {
		$('.cart_free_shipping.reste100').hide();
	}
});

// Megamenu mobile

$(document).ready(function(){
	$(document).on('click', '#megamenu-icon', function(e){
		$(this).toggleClass('active');
		if( $(this).hasClass('active') ){
			$('.mm_menus_ul').addClass('active');
		}else{
			$('.mm_menus_ul').removeClass('active');
		}
	});
});

// MENU - SOUS CATEGORIE
// $(document).on('click', '.mm_has_sub .ets_mm_url .mm_menu_content_title', function(e) {
// 	var wraptitle = $(this).data('id-block');
// 	console.log(wraptitle);
// 	$('.mm_has_sub .ets_mm_url .ets_mm_block_content[data-id-block="'+wraptitle+'"]').toggle();
// 	$('.mm_has_sub .ets_mm_url .mm_menu_content_title[data-id-subcategory="'+wraptitle+'"]').toggleClass('opened');
// });
		
// Voucher

$(document).ready(function(){
	$(document).on('click', '.voucher-ok', function(e){
		$('.promo-code .js-error-text').html('');
	});
});
		
// Box
		
$( document ).ready(function() {
	$(document).on('click', '.add_to_the_cart_fake', function(event){
        var perso=true;
        var pos_ch=0;
        var regCP = new RegExp("^[0-9]{5}$");
        var regPhone = new RegExp("^[+0-9. ()-]*$");
        var message_err="";
        $(".li_required input").each(function() {            
            if(pos_ch==3 && !regCP.test($(this).val())) {   
                //console.log($(this).val());
                //console.log("err");
                 message_err=" Le code postal n'est pas correct.";
                perso=false;
                return false;
            }
            if(pos_ch==6 && !regPhone.test($(this).val())) {   
                //console.log($(this).val());
                //console.log("err");
                message_err=" Le telephone n'est pas correct.";
                perso=false;
                return false;
            }
            if($(this).val()=="") {
               perso=false;
               return false;
            }
            pos_ch++;
        });
        if(perso) 
        {
            var tvaleurs=Array();
            pos=0;
            $(".custom_box [type=text]").each(function() {

                tvaleurs[pos]=[$(this).attr("name"),$(this).val()];
                pos++;
            });

            id_pdt=$("#val_id_product").val();
            $.ajax({
                 method: "POST",
                 url: "/ajax_custo.php?id_product=" + id_pdt,
                 data: {  txts: tvaleurs,
                        id_cart: $("#val_id_product").val()},
                 success :function(data) {
                 // console.log(data);
                  $( ".add-to-cart" ).trigger( "click" );
                  setTimeout(function(){
                    $.ajax({
                     method: "POST",
                     url: "/ajax_update_cart_product.php?id_product=" + id_pdt,
                     data: {   txts: tvaleurs, id_cart: $("#val_id_product").val()},
                     success :function(data) {
                     // console.log(data);
                    }
                  });
                  }, 2500);


                }
            });

         }// fin si perso true
         else {
             alert("Veuillez remplir touts les champs obligatoires au format correct." + message_err);
         }
    });

});

// Confirmation de commande

/*$(document).ready(function(){
	$(document).on("click", '.payment-option', function(e){
		setTimeout(function(){ 
			$('#payment-confirmation button[type="submit"]').click();
		}, 500);	
	});
});*/

// début Popin Lettre verte Mars 2023

$(document).ready(function(){
	$(document).on("click", 'button[type="submit"][name="confirmDeliveryOption"]', function(e, who){
		
		if(who !== "machine"){
			// user-triggered event
			
			let lettre_verte_id = 353; // prod et php 8
			if(window.location.hostname == 'dev.labonnegraine.com'){
				lettre_verte_id = 350; // dev
			}

			// Si lettre verte est sélectionnée 
			if($('#delivery_option_'+lettre_verte_id).is(':checked')){
				
				e.preventDefault();
				
				let html = "<div class='popinSerres'>" +
					"<div class='popinSerresContenu'>" +
					"<div class='popinLettreVerteContenu'>" +
					"<b>Attention, en choisissant l’envoi par lettre verte, vous ne disposerez <u>d’aucun suivi ni d’aucune garantie sur le délai de livraison.</u></b>" +
					"<br><br>" +
					"Pour ce type d’envoi, il faut compter habituellement entre 3 à 5 jours pour recevoir votre lettre mais nous avons remarqué que ce délai peut s’étendre <b><u>jusqu’à 15 jours.</u></b> Pas d’inquiétude, la lettre finit toujours par arriver !" +
					"<br><br>" +
					"Il n'existe aucun recours auprès de La Poste en cas de non distribution d'une lettre verte. <b>Ainsi, aucune réclamation portant sur la non distribution de votre commande ne pourra nous être traitée et vous engagez votre responsabilité par ce choix.</b> Le choix de ce mode de livraison vaut acceptation de ses conditions particulières." +
					"<br><br>" +
					"En cas de besoin urgent, nous vous conseillons de choisir un autre mode d’expédition plus rapide et avec suivi." +
					"</div>" +
					"<br><br><br>" +
					"<div>" +
					"<span class='popinLettreVerteChanger' onclick='$(\".popinSerres\").remove()'><u>Je souhaite changer de mode de livraison</u></span>" +
					"<button type='button' class='button btn btn-default button-medium js-submitLettreVerte'>" +
					"<span>J'ai compris</span>" +
					"</button>" +
					"</div>" +		
					"</div>" +
					"</div>";

				$("html").append(html);
			}
		}
		else {
			// code-triggered event
			// rien à mettre ici
		}
 
	});
	$(document).on("click", '.js-submitLettreVerte', function(e){
		$('.popinSerres').remove();
		$('form#js-delivery button[type="submit"]').trigger("click", ["machine"]);
	});
});

// fin Popin Lettre verte Mars 2023

// début Lazy load

// cas Internet Explorer, IntersectionObserver n'est pas supporté: pas de lazy load
$(document).ready(function(){
	var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
	var ua = window.navigator.userAgent;
	var msie = ua.indexOf("MSIE ");
	if ((msie > 0)||(isIE11)){ // If Internet Explorer
		$('img[data-lazy-src]').each(function() {
			var src = $(this).data('lazy-src');
			$(this).attr('src',src);
		});

		$('[data-lazy-background-image]').each(function() {
			var backgroundImage = $(this).data('lazy-background-image');
			$(this).css("background-image", "url("+backgroundImage+")");
		});
	}else{

	}
});

// lazy load pour les images img
document.addEventListener("DOMContentLoaded", function() {
	const imageObserver = new IntersectionObserver(function(entries, imgObserver) {
		entries.forEach(function(entry) {
			if (entry.isIntersecting) {
				const lazyImage = entry.target;
				lazyImage.src = lazyImage.dataset.lazySrc;
				imgObserver.unobserve(lazyImage);
			}
		})
	});
	const arr = document.querySelectorAll('img[data-lazy-src]')

	arr.forEach(function(v) {
		imageObserver.observe(v);
	})
})

// lazy load pour les backgrounds css
document.addEventListener("DOMContentLoaded", function() {
	const imageObserver = new IntersectionObserver(function(entries, imgObserver) {
		entries.forEach(function(entry) {
			if (entry.isIntersecting) {
				const lazyImage = entry.target;
				lazyImage.style.backgroundImage = "url("+lazyImage.dataset.lazyBackgroundImage+")";
				imgObserver.unobserve(lazyImage);
			}
		})
	});
	const arr = document.querySelectorAll('[data-lazy-background-image]');

	arr.forEach(function(v) {
		imageObserver.observe(v);
	})
})

// fin Lazy load

// début FAQ

$(document).on("click", ".page_faq_square", function(){
	$(".page_faq_square").removeClass("active");
	$(this).addClass("active");
	var faq = $(this).data("faq");
	$(".page_faq_block").hide();
	$(".page_faq_block[data-faq="+faq+"]").show();
});

$(document).on("click", ".page_faq_question", function(){
	$(this).next().slideToggle();
	$(this).toggleClass('active');
	if($(this).hasClass('active')){
		$('.page_faq_cropped img',this).animate({ 'marginTop': '-17px' }, 800);
	}else{
		$('.page_faq_cropped img',this).animate({ 'marginTop': '0' }, 800);
	}
});

$(document).on("click", ".page_faq_bouton_contact", function(){
	$('.page_faq_form').slideDown();
});


// fin FAQ

// début Modal général

$(document).ready(function(){
	
	$(document).click(function(){
        $('.modal_box').hide();
		$('body').removeClass('body-modal');
    });
  
	$(document).on('click', '.modal_close', function(e) {
        $('.modal_box').hide();
		$('body').removeClass('body-modal');
    });
  
	$(document).on('click', '.modal_content', function(e) {
		e.stopPropagation();
	});
	
	// Modale de la page "Mon compte"
	if($('body#my-account .modal_box').length){
		$('body').addClass('body-modal');
	}

});


// fin Modal général

// début Popin Bons Plants

$(document).ready(function(){
	
	if($("#mp_popin_bons_plants").length){
		$(".modal_box_bons_plants").show();
	}

});

// fin Popin Bons Plants


$(document).ready(function(){

	// BLOCK EDITO / CMS HOME
	$(document).on('click', '#index #first-part .voir-plus', function() {
        $('#index #first-part').addClass('engagements-open');
        $('#index .engagement').addClass('engagements-open');
   });

	$(document).on('click', '#index #first-part .voir-moins', function() {
        $('#index #first-part').removeClass('engagements-open');
        $('#index .engagement').removeClass('engagements-open');
   });

	$(document).on('click', 'header .button-search', function(e) {
		e.stopPropagation();
     e.preventDefault();
        $('header #search_widget').show();
   });

	$("body").click(function(){
        $("header #search_widget").hide();
  });

  $('body').on('click', 'header #search_widget', function(e) {
  	e.stopPropagation();
  });
});

// Sticky desktop et mobile

let megamenu_top = 0;
$(document).ready(function(){
	megamenu_top = $('.ets_mm_megamenu:not(.scroll_heading)').offset().top; 
});

$(window).scroll(function() {
	let pad = 220; // je ne connais pas la valeur exacte, mais c'est autour de 200 pour empêcher les sauts d'écrans lorsque les pages sont trop petites.
	if(document.body.scrollHeight > window.innerHeight + pad){
		let _scroll = $(window).scrollTop();
		if(_scroll > megamenu_top) {
			$('body').addClass("header-scrolled");
		} else {
			 $('body').removeClass("header-scrolled");
		}
	}
});

// Slider box
$(document).ready(function(){
  $('.un_slick_slider').slick({
    autoplay: true,
    autoplaySpeed: 10000,
	slidesToShow: 1,
	slidesToScroll: 1,
    arrows: true,
    nextArrow:'<a href="" class="btn_next custom-arrow"></a>',
    prevArrow:'<a href="" class="btn_prev custom-arrow"></a>'
  });
});

// Ouvre une popin contenant le PDF fiche pratique

$(document).ready(function(){
  $(document).on('click', '.js_download_fiche_pratique', function(e) {
    e.preventDefault();
	let lien = "/imgPDF/pdf.php?name="+encodeURIComponent($(this).attr('href'));
    $('body').append('<div class="popupPDF"><div class="popupPDF_close">&times;</div><iframe src="'+lien+'"></iframe></div>');return false;
  });
  $(document).on('click', '.popupPDF_close', function(e) {
	$('.popupPDF').remove();
  });
});

// début Evol. Mars 2022

$(document).ready(function(){

	$(document).on("click", "#account_creation_newsletter", function(e){
		var newsletter_bonplan = 0;
		var newsletter_dossiercyril = 0;

		if($('#newsletter_bonplan').is(':checked')){
			newsletter_bonplan = 1;
		}

		if($('#newsletter_dossiercyril').is(':checked')){
			newsletter_dossiercyril = 1;
		}

		$.ajax({
			type: "POST",
			url: "/ajax_update_newsletter.php",
			data: {
				newsletter_bonplan: newsletter_bonplan,
				newsletter_dossiercyril: newsletter_dossiercyril
			},
			success: function(result) {
				$('.modal_box').hide();
				$('body').removeClass('body-modal');
			},
			error: function(){

			}
		});
	});
});

// fin Evol. Mars 2022

$(document).ready(function(){

	// FILTRE CATEGORIE
	$(document).on('click', '.mes-filtres', function(e) {
        $('#category #center_column').addClass('open-sort');
        $('#category .content_sortPagiBar').addClass('open-sort');
   });

	$(document).on('click', '#category #filter_collapse .button_collapsing.btn-info', function(e) {
        $('#category #center_column').removeClass('open-sort');
        $('#category .content_sortPagiBar').removeClass('open-sort');
   });

	$(document).on('focus', '#category #layered_form .select', function(e) {
        $('#category #layered_form .select').toggleClass('open-sort');
   });
   
});

// début Evol. Newsletter Janvier 2023

$(document).ready(function(){
	$(document).on("click", "#cmsNewsletterSubmit", function(e){
		let email = $('#cmsNewsletterForm [name="email"]').val();
		let prenom = $('#cmsNewsletterForm [name="prenom"]').val();
		let nom = $('#cmsNewsletterForm [name="nom"]').val();
		let rgpd = $('#cmsNewsletterForm [name="rgpd"]').is(':checked');

		if(rgpd && email){
			e.preventDefault();
			$.ajax({
				type: "POST",
				url: "/ajax_newsletter_2023.php",
				data: {
					email: email,
					prenom: prenom,
					nom: nom
				},
				success: function(result) {
					if( result == 'ok'){
						$('.cmsNewsletterSuccess').removeClass('hidden');
						$('.cmsNewsletterError').addClass('hidden');
						$('.cmsNewsletterWrapper').addClass('hidden');
						$('#cmsNewsletterSubmit').addClass('hidden');
					}else{
						$('.cmsNewsletterError').removeClass('hidden');
					}
				},
				error: function(){
					$('.cmsNewsletterError').removeClass('hidden');
				}
			});
		}
	});
});

// fin Evol. Newsletter Janvier 2023


// début assistant Cyril

$(".my_assistant_cross").on("click", function(){
	var produit = $(this).parent();

	var idAssistant = $(this).attr("class").split(" ")[1];
	var idCustomer = $("#idCustomer").val();
	supprAssitant(idAssistant, idCustomer, null);
});

$("#depCyril, #ouiAltitude, #nonAltitude").on("change", function(){
	var numDep = $("#depCyril").val();
	var idDuCustomer = $("#idCustomer").val();
	var uneHauteAltitude = 0;

	if($("#formAltitude").css("display") == "none"){
		uneHauteAltitude = 0;
	}else{
		if($("#ouiAltitude").prop("checked") == true){
			uneHauteAltitude = 1;
		}else{
			uneHauteAltitude = 0;
		}
	}

	$.ajax({
		url: './../../assistant_ajax.php',
		type: 'POST',
		data: {numDep: numDep, idDuCustomer: idDuCustomer, uneHauteAltitude: uneHauteAltitude},
		dataType: 'json',
		timeout: 3000,
		success: function (data) {
			if(data == 1){
				$("#formAltitude").slideDown();
			}else{
				$("#formAltitude").slideUp();
			}
		}
	});
});

$(".checkboxProduit").on("click", function(){

	var chb = $(this);

	var idClient = $("#idCustomer").val();
	var idCart = 0;
	var idAssistantProduit = $(this).val();
	var idDepartement = $("#depCyril").val();
	var hauteAltitude = 0;

	if($("#formAltitude").css("display") == "none"){
		hauteAltitude = 0;
	}else{
		if($("#ouiAltitude").prop("checked") == true){
			hauteAltitude = 1;
		}else{
			hauteAltitude = 0;
		}
	}

	//console.log("assistant Cyril "+idClient+" / "+idAssistantProduit);

	if ($(this).prop('checked') == true ){
		$("#append_cyril").append('<div id="my_filter_assistant"><div id="my_confirm_assistant"><div class="my_assistant_content2"><br><span style="text-transform: uppercase; font-weight: bold">Etes-vous sûr de vouloir vous inscrire à l\'assistant ?</span><br><br><br><button id="my_add_assistant" type="button">S\'inscrire</button>   <button id="my_back_assistant" type="button">Retour</button></div></div></div>');

		$("#my_filter_assistant").fadeIn();
		$("#my_confirm_assistant").slideDown();

		$("#my_add_assistant").on("click", function(){

			$.ajax({
				url: './../../assistant_ajax.php',
				type: 'POST',
				data: {addClient: idClient, addAssistant: idAssistantProduit, idDepartement: idDepartement, hauteAltitude: hauteAltitude, idCart: idCart, initial_state: 2},
				timeout: 3000,
				success: function (data) {
					window.location.href = "mon-assistant";
				}
			});
		});

		$("#my_back_assistant").on("click", function(){
			$("#my_confirm_assistant").slideUp();
			$("#my_filter_assistant").fadeOut();
			chb.prop("checked", false);

			setTimeout(function(){ $("#my_filter_assistant").remove(); }, 500);
		});
	}else{
		supprAssitant(idAssistantProduit, idClient, chb);
	}
});

function supprAssitant(idAssistant, idCustomer, item){
	$("#append_cyril").append('<div id="my_filter_assistant"><div id="my_confirm_assistant"><div class="my_assistant_content2"><br><span style="text-transform: uppercase; font-weight: bold">Etes-vous sûr de vouloir vous désinscrire de l\'assistant ? La progression sera perdue.</span><br><br><br><button id="my_delete_assistant" type="button">Se désinscrire</button>   <button id="my_back_assistant" type="button">Retour</button></div></div></div>');

	$("#my_filter_assistant").fadeIn();
	$("#my_confirm_assistant").slideDown();

	$("#my_delete_assistant").on("click", function(){

		$.ajax({
			url: './../../assistant_ajax.php',
			type: 'POST',
			data: {idCustomer: idCustomer, idAssistant: idAssistant},
			timeout: 3000,
			success: function (data) {
				if (data != null) {
				   window.location.href = "mon-assistant";
				}
			}
		});
	});

	$("#my_back_assistant").on("click", function(){
		$("#my_confirm_assistant").slideUp();
		$("#my_filter_assistant").fadeOut();
		item.prop("checked", true);

		setTimeout(function(){ $("#my_filter_assistant").remove(); }, 500);
	});
}

// fin assistant Cyril

// début navigation à facettes

$(document).ready(function(){
	$(document).on('change', '#filter_collapse select', function(e){
		let val = $(this).val();
		
		/*$.ajax({
			type: "POST",
			url: "/scripts/ajax_facet.php",
			data: {

			},
			cache: false,
			success: function(response) {
			  $('#js-product-list').html(response);
			},
			error: function (e) {
			}
		});*/
		
	});
});

// fin navigation à facettes

// wkpack
$(document).ready(function(){
	$(document).on('click', '.js-wkpack-product', function(e){
		let id = $(this).attr('data-id');
		$('.js-wkpack-product').removeClass('active');
		$(this).addClass('active');
		$('.wkpack-product-detail').hide();
		$('.wkpack-product-detail[data-id="'+id+'"]').show();
	});
});



// focus input recherche (loupe)
$(document).ready(function(){
	$(document).on('click', '.button-search', function(e){
		$('#search_widget .ui-autocomplete-input').focus();
	});
});

// Bouton Mes Filtres des pages catégories en mobile
$(document).ready(function(){
	$(document).on('click', '.mes-filtres', function(e){
		$('#search_filters_wrapper').show();
		$('#search_filters_wrapper').removeClass("hidden-sm-down");
		$('#content-wrapper').addClass('open-sort');
		$('.mes-filtres').hide();
	});
	
	$(document).on('click', '.ok-filtres', function(e){
		$('#search_filters_wrapper').hide();
		$('#content-wrapper').removeClass('open-sort');
		$('.mes-filtres').show();
	});
});

$(document).ready(function(){
	$(document).on('click', '.js-button-search-mobile', function(e){
		$('#aw_bloc_search_absolute').show();
	});
});

// Slider fiche produit
$(document).ready(function() {
	if (!!$.prototype.bxSlider){
		$('#bxslider1').bxSlider({
			minSlides: 2,
			maxSlides: 4,
			slideWidth: 178,
			slideMargin: 20,
			pager: false,
			nextText: '>',
			prevText: '<',
			moveSlides:1,
			infiniteLoop:false,
			hideControlOnEnd: true
		});
	}
});

// Slider fiche produit
$(document).ready(function() {
	if (!!$.prototype.bxSlider){
		$('#bxslider2').bxSlider({
			minSlides: 2,
			maxSlides: 4,
			slideWidth: 178,
			slideMargin: 20,
			pager: false,
			nextText: '>',
			prevText: '<',
			moveSlides:1,
			infiniteLoop:false,
			hideControlOnEnd: true
		});
	}
});


$(document).ready(function(){
	// Slider des produits sur la homepage en mobile
	$('#home-featured-products .products.row').slick({
	 responsive: [
		{
		  breakpoint: 991,
		  settings: {
			slidesToShow: 2,
			slidesToScroll: 1,
			dots: true,
			autoplay: true,
			autoplaySpeed: 9000,
	  touchMove: true
		  }
		}
	]});
});

/* ANDY - DEBUT SERRES */

$(function(){
	$("#popinSerresBtn").on("click", function(){
		let productTitle = $("h1.h1").text();

		let html = "<div class='popinSerres'>" +
			"<div class='popinSerresContenu'>" +
			"<span class='cross' title='Fermer la fenêtre' onclick='$(\".popinSerres\").remove()'></span>" +
			"<div class='title'>Vous souhaitez recevoir des informations sur : <br>"+productTitle+"<br>Nous transmettons votre demande à notre partenaire Serres Lams qui prendra contact avec vous au plus vite.</div>" +
			"<br><br>" +
			"<form id='formSerres'>" +
			"<label for='nomSerres'>Nom</label><br><input type='text' name='nomSerres' id='nomSerres' required><br>" +
			"<label for='prenomSerres'>Prénom</label><br><input type='text' name='prenomSerres' id='prenomSerres' required><br>" +
			"<label for='telSerres'>Téléphone</label><br><input type='tel' name='telSerres' id='telSerres' required><br>" +
			"<label for='mailSerres'>Adresse e-mail</label><br><input type='email' name='mailSerres' id='mailSerres' required><br>" +
			"<label for='prefSerres'>Préférence de contact</label><br><select name='prefSerres' id='prefSerres' required><option val=''>--</option><option>Adresse e-mail</option><option>Téléphone</option></select><br>" +
			"<input type='checkbox' name='CGVSerres' id='CGVSerres' required><label for='CGVSerres'>  En cochant cette case et en soumettant ce formulaire, j'accepte que mes données personnelles soient utilisées pour me recontacter dans le cadre de ma demande indiquée dans ce formulaire. Aucun autre traitement ne sera effectué avec mes informations.*</label>" +
			"<input type='hidden' name='sujet' value=\"Demande d'informations sur : "+productTitle+"\">" +
			"<div class='buttonGroup'>" +
			"<button type='submit'>Envoyer</button>" +
			"</div>" +
			"</form>" +
			"</div>" +
		"</div>";

		$("html").append(html);
	});

	$(document).on("submit", "#formSerres", function(){
		let dataTab = $("#formSerres").serializeArray();
		$.ajax({
			url: "/ajax_mail_serres.php",
			type: "POST",
			data: dataTab,
			dataType: "json",
			timeout: 3000,
			success: function(data){
				alert("La mail a été envoyé");
				$(".popinSerres").remove();
			},
			error: function(){
				console.log("Erreur lors de l'envoi du mail");
			}
		});
		return false;
	});
});

/* ANDY - FIN SERRES */

//$(document).ready(function(){
// MENU - SOUS CATEGORIE
	//$(document).on('click', 'div.mm_block_type_category span a', function(e) {
        //var subcategory = $(this).data('id-subcategorytrigger');
        //console.log(subcategory);
        //$('div.mm_block_type_category div.ets_mm_block_content[data-id-subcategory="'+subcategory+'"]').toggle().toggleClass('open');
   //});
//});

/* DORIAN - AJOUT PANIER AJAX - DEBUT */

$(document).ready(function(){

	$.ajax({
		method: "POST",
		url: "/module/ps_shoppingcart/ajax",
		data: {  
		},
		success :function(data) {
			$('.mm_extra_item').html(data.preview);
		}
	});

	$(document).on('click', '.add-to-cart-commercial', function(e) {
		const elm = $(this);
		const data_id_product = elm.attr('data-id-product');
		const data_id_product_attribute = elm.attr('data-id-product-attribute');
		const q = elm.attr('data-quantity');
		if(q <= 0){
			alert('Il n\'y a pas assez de produits en stock.');
			return false;
		}
		$.ajax({
			method: "POST",
			url: "/panier",
			data: {   
				token: eam_token,
				id_product: data_id_product,
				id_customization: 0,
				qty: 1,
				add: 1,
				action: 'update'
			},
			success :function(data) {
				//console.log(data);
				$.ajax({
					method: "POST",
					url: "/module/ps_shoppingcart/ajax",
					data: {   
						id_product_attribute: data_id_product_attribute,
						id_product: data_id_product,
						id_customization: 0,
						action: 'add-to-cart'
					},
					success :function(data) {
						//console.log(data);

						$('.mm_extra_item').html(data.preview); // incrémente le compteur du panier
						
						 // affiche la modale
						prestashop.blockcart = prestashop.blockcart || {};

						var showModal = prestashop.blockcart.showModal || function (modal) {
							var $body = $('body');
							$body.append(modal);
							$body.one('click', '#blockcart-modal', function (event) {
							  if (event.target.id === 'blockcart-modal') {
								$(event.target).remove();
							  }
							});
						};
						
						if (data.modal) {
						  showModal(data.modal);
						}
					}
				});
			}
		});
	});

	$(document).on('click', '.js-listing-add-to-cart', function(e) {
		const elm = $(this).closest('.js-product-miniature');
		const data_id_product = elm.attr('data-id-product');
		const data_id_product_attribute = elm.attr('data-id-product-attribute');
		const q = elm.attr('data-quantity');
		if(q <= 0){
			alert('Il n\'y a pas assez de produits en stock.');
			return false;
		}
		$.ajax({
			method: "POST",
			url: "/panier",
			data: {   
				token: eam_token,
				id_product: data_id_product,
				id_customization: 0,
				qty: 1,
				add: 1,
				action: 'update'
			},
			success :function(data) {
				//console.log(data);
				$.ajax({
					method: "POST",
					url: "/module/ps_shoppingcart/ajax",
					data: {   
						id_product_attribute: data_id_product_attribute,
						id_product: data_id_product,
						id_customization: 0,
						action: 'add-to-cart'
					},
					success :function(data) {
						//console.log(data);

						$('.mm_extra_item').html(data.preview); // incrémente le compteur du panier
						
						 // affiche la modale
						prestashop.blockcart = prestashop.blockcart || {};

						var showModal = prestashop.blockcart.showModal || function (modal) {
							var $body = $('body');
							$body.append(modal);
							$body.one('click', '#blockcart-modal', function (event) {
							  if (event.target.id === 'blockcart-modal') {
								$(event.target).remove();
							  }
							});
						};
						
						if (data.modal) {
						  showModal(data.modal);
						}
					}
				});
			}
		});
	});
});


/* DORIAN - AJOUT PANIER AJAX - FIN */

// Modale fiche produit
$(document).ready(function(){
	$(document).click(function(){
		$("#blockcart-modal .close").click();
	});
	
	$(document).on('click', '#blockcart-modal', function(e){
		e.stopPropagation();
	});
});

(function (a) {
    a.fn.vTicker = function (b) {
        var c = {
            speed: 700,
            pause: 4000,
            showItems: 3,
            animation: "",
            mousePause: true,
            isPaused: false,
            direction: "up",
            height: 0
        };
        var b = a.extend(c, b);
        moveUp = function (g, d, e) {
            if (e.isPaused) {
                return
            }
            var f = g.children("ul");
            var h = f.children("li:first").clone(true);
            if (e.height > 0) {
                d = f.children("li:first").height()
            }
            f.animate({
                top: "-=" + d + "px"
            }, e.speed, function () {
                a(this).children("li:first").remove();
                a(this).css("top", "0px");
            });
            if (e.animation == "fade") {
                f.children("li:first").fadeOut(e.speed);
                if (e.height == 0) {
                    //f.children("li:eq(" + e.showItems + ")").hide().fadeIn(e.speed)
                }
            }
            h.appendTo(f)
        };
        moveDown = function (g, d, e) {
            if (e.isPaused) {
                return
            }
            var f = g.children("ul");
            var h = f.children("li:last").clone(true);
            if (e.height > 0) {
                d = f.children("li:first").height()
            }
            f.css("top", "-" + d + "px").prepend(h);

            f.animate({
                top: 0
            }, e.speed, function () {
                a(this).children("li:last").remove()
            });
            if (e.animation == "fade") {
                if (e.height == 0) {
                    f.children("li:eq(" + e.showItems + ")").fadeOut(e.speed)
                }
                f.children("li:first").hide().fadeIn(e.speed)
            }
        };
        return this.each(function () {
            var f = a(this);
            var e = 0;
            f.css({
                overflow: "hidden",
                position: "relative"
            }).children("ul").css({
                position: "absolute",
                margin: 0,
                padding: 0
            }).children("li").css({
                margin: 0,
                padding: 0
            });
            if (b.height == 0) {
                f.children("ul").children("li").each(function () {
                    if (a(this).height() > e) {
                        e = a(this).height()
                    }
                });
                f.children("ul").children("li").each(function () {
                    a(this).height(e)
                });
                f.height(e * b.showItems)
            } else {
                f.height(b.height)
            }
            var d = setInterval(function () {
                if (b.direction == "up") {
                    moveUp(f, e, b)
                } else {
                    moveDown(f, e, b)
                }
            }, b.pause);
            if (b.mousePause) {
                f.bind("mouseenter", function () {
                    b.isPaused = true
                }).bind("mouseleave", function () {
                    b.isPaused = false
                })
            }
        })
    }
})(jQuery);

 $(document).ready(function() {
    // ACTUS LITTLE
    if ( jQuery('#news-container1').length > 0 )
    {
        jQuery('#news-container1').vTicker({
            speed: 2000,
            pause: 6000,
            animation: 'fade',
            mousePause: true,
            showItems: 1
        });
     }
});

// Discount dans la page panier
 $(document).ready(function() {
	 $(document).on('click', '.cart-summary-line a[data-link-action="remove-voucher"]', function(e){
		$(this).closest('.cart-summary-line').remove();
	});
});


// Bloque les numéros de téléphones fixes pour Colissimo en point retrait
 $(document).ready(function() {
	$(document).on('click', '#js-delivery .btn', function(e){
		blockFixedNumber();
	});
});
		
// Codes de réduction
prestashop.on('updateCart', function(event) { 
	// Cet événement se déclenche avant une mise à jour du panier.
	
	var vouchers = false;
	if ( event.resp )
		{
			var vouchers = event.resp.cart.vouchers.added;
		}
	if(vouchers){
		//console.log(vouchers);
		let html = '<div class="aw-promo-discount fin">';
		html += '<ul class="promo-name card-block">';
		for (let k in vouchers){
			
			if (vouchers.hasOwnProperty(k)) {
				let voucher = vouchers[k];
				if ( voucher.reduction_product == 0 )
				{
					html += '<li class="cart-summary-line">';
					html += '<span class="label">'+voucher.name+'</span>';
					html += '<div class="float-xs-right">';
					html += '<span>'+voucher.reduction_formatted+'</span>';
					html += '<a href="/panier?deleteDiscount='+k+'&amp;token='+eam_token+'" data-link-action="remove-voucher"><i class="material-icons"></i></a>';
					html += '</div>';
					html += '</li>';
				}
			}
		}
		html += '</div>';
		html += '</ul>';
		$('.aw-promo-discount-wrap').html(html);
	}
});


// Calcul de prix des plants quand on choisit une déclinaison
 $(document).ready(function() {
	$(document).on('click', '.product-variants-plant .input-radio', function(e){
		prestashop.emit('updateProduct', {reason: ''});
		$('.plant-loading').css("opacity",1);
	});

	prestashop.on('updatedProduct', function (event) {
		//console.log('aw-updatedProduct');
		// Cet événement se déclenche après une mise à jour d'un produit.
		
		id_product_attribute = event.id_product_attribute;
		
		// Refresh du bouton ajout panier (sauf pour la page box)
		if(event.product_add_to_cart){
			if(!$('.simple_box_block').length){
				$('.js-product-add-to-cart').html(event.product_add_to_cart);
			}
		}
		
		// Ajout wishlist sur la fiche produit après qu'on ait changé de déclinaison
		if(!$('.wishlist-button-add').length){
			let wishlist_button_add = '<button class="wishlist-button-add wishlist-button-product js-aw-wishlist-open"><i class="material-icons">favorite_border</i></button>';
			$('.wishlist-button').before(wishlist_button_add);
		}
		
		$('.plant-loading').css("opacity",0);
		if(event.plant_precommande){
			$('.js-plant-precommande').html(event.plant_precommande);
			// Remet les flèches sur le bouton quantité
			$('#quantity_wanted').TouchSpin({
				verticalbuttons: !0,
				verticalupclass: "material-icons touchspin-up",
				verticaldownclass: "material-icons touchspin-down",
				buttondown_class: "btn btn-touchspin js-touchspin js-increase-product-quantity",
				buttonup_class: "btn btn-touchspin js-touchspin js-decrease-product-quantity",
				min: 1,
				max: 1e6
			});
			$('.plant-prix-barre').hide();	
			if($('.plant-input-radio-1').is(':checked')) {
				$('.plant-prix-barre').css('display','inline-block'); 
			}
		}
	});
});

// Modale livraison
$(document).ready(function() {
	$(document).on('click', '.js-info-livraison', function(e){
		e.stopPropagation();
		$('.modal_box_plant').show();
	});
	
	// On ouvre la modale de livraison des plants lors de la première visite sur le panier (si le panier contient un plant en précommande)
	let pep = false;
	if(typeof plant_en_precommande !== "undefined"){
		pep = plant_en_precommande;
	}
	if(!localStorage.modal_box_plant && pep){
		$('.modal_box_plant').show();
		localStorage.modal_box_plant = '1';
	}
});

// Picto plant
$(document).ready(function() {
	
	let str = '';
	
	// Suppression du picto
	$('.text-uppercase.h6').each(function() {
		str = $(this).html().replaceAll("🌱",'');
		$(this).html(str);
	});
	
	// Suppression du picto
	$('.breadcrumb').each(function() {
		str = $(this).html().replaceAll("🌱",'');
		$(this).html(str);
	});
	
	// Suppression du picto
	$('.cat-name').each(function() {
		str = $(this).html().replaceAll("🌱",'');
		$(this).html(str);
	});
	
	// Modification du picto
	$('.category-sub-menu').each(function() {
		str = $(this).html().replaceAll("🌱",'<img class="picto-plant" src="/themes/lbg/assets/img/picto-plant.png" alt="" title="Existe en plant">');
		$(this).html(str);
	});
	
	// Modification du picto
	$('.ets_mm_url').each(function() {
		str = $(this).html().replaceAll("🌱",'<img class="picto-plant" src="/themes/lbg/assets/img/picto-plant.png" alt="" title="Existe en plant">');
		$(this).html(str);
	});
	
});

// Page commande

$(document).ready(function(){
	$(document).on('click', '.js-aw-create-account-button', function(e){
		$('.js-aw-login-form').slideUp(600);
		$('.js-aw-no-account').slideUp(600);
		$('#checkout-guest-form').fadeIn();
	});
});

// Wishlist

$(document).ready(function(){
	$(document).on('click', '.js-aw-wishlist-open', function(e){
		e.preventDefault();
		setTimeout(function(){
			$('.aw-wishlists .wishlist-modal').addClass('show');
			$('.aw-wishlists .modal-backdrop').addClass('in');
		}, 200);
	});
	
	$(document).on('click', 'body', function(e){
		$('.aw-wishlists .wishlist-modal').removeClass('show');
		$('.aw-wishlists .modal-backdrop').removeClass('in');
	});
	
	$(document).on('click', 'body .modal-content', function(e){
		e.stopPropagation();
	});
	
	$(document).on('click', '.aw-wishlists .close', function(e){
		$('.aw-wishlists .wishlist-modal').removeClass('show');
		$('.aw-wishlists .modal-backdrop').removeClass('in');
	});
	
	$(document).on('click', '.aw-wishlists .wishlist-list-item', function(e){
		let idWishList = $(this).attr('data-id');
		
		// Fiche produit
		if($('#product_page_product_id').length){
			id_product = $('#product_page_product_id').val();
		}
		
		if($('.wishlist-button').length){
			const a = $('.wishlist-button').attr('data-product-attribute-id');
			if(a){
				id_product_atribute = a;
			}
			
			const b = $('.wishlist-button').attr('data-product-id');
			if(b){
				id_product = b;
			}
		}
		
		$.ajax({
			type: "POST",
			url: blockwishlistController + '?action=addProductToWishlist&params[id_product]='+ id_product +'&params[idWishList]='+ idWishList +'&params[quantity]=1&params[id_product_attribute]='+ id_product_attribute,
			success: function(result) {
				//console.log(result);
				$('.aw-wishlists .wishlist-modal').removeClass('show');
				$('.aw-wishlists .modal-backdrop').removeClass('in');
			}
		});

	});
	
	// Sur listing produits
	$(document).on('click', '.js-wishlist-button-add', function(e){
		let el = $(this).closest('.js-product-miniature');
		id_product = el.attr('data-id-product');
		id_product_attribute = el.attr('data-id-product-attribute');
		setTimeout(function(){
			$('.aw-wishlists .wishlist-modal').addClass('show');
			$('.aw-wishlists .modal-backdrop').addClass('in');
		}, 200);
	});
	
	// Bouton wishlist : tout ajouter au panier
	$(document).on('click', '.wishlist-addAll', function(e){
		$(this).attr('disabled','disabled');
		$(".wishlist-product-addtocart").each(function() {
			if ($(this).css("display") != "none") {
			  $(this).click();
			}
		});
	});

	/* GESTION DES LIMITATION DE NAVIGATION A FACETTES */
	if($("body.page-category").length > 0){

		/*$("#search_filters p.text-uppercase.h6").html($("#search_filters p.text-uppercase.h6").text()+" <span style='text-transform: lowercase'>(trois filtres maximum)</span>");

		if($("#js-active-search-filters .h6.active-filter-title").length > 0){
			var item = $("#js-active-search-filters .h6.active-filter-title");
			item.text(item.text() + " (3 max.)");

			var params = $("#js-active-search-filters ul li").length;
			if(params >= 3){
				$("#search_filters .facet.clearfix").each(function(){
					if($(this).find("ul li span").text().indexOf("(") == -1){
						$(this).remove();
					}
				});
			}
		}

		$(document).on("click", "#js-active-search-filters ul li a", function(){
			var timer = setInterval(function(){
				if($(".faceted-overlay").length == 0){
					$("#search_filters p.text-uppercase.h6").html($("#search_filters p.text-uppercase.h6").text()+" <span style='text-transform: lowercase'>(trois filtres maximum)</span>");

					var item = $("#js-active-search-filters .h6.active-filter-title");
					item.text(item.text() + " (3 max.)");

					var params = $("#js-active-search-filters ul li").length;
					if(params >= 3){
						$("#search_filters .facet.clearfix").each(function(){
							if($(this).find("ul li span").text().indexOf("(") == -1){
								$(this).remove();
							}
						});
					}

					clearInterval(timer);
				}
			}, 100);
		});

		$(document).on("click", "#search_filters .dropdown-menu .js-search-link", function(){
			var url = $(this).attr("href");
			var query = url.split("q=")[1];
			var nbParams = query.match(/-/g);
			var nbFauxParams = query.match(/%5C-/g);

			nbParams = (nbParams && nbParams != null) ? nbParams.length : 0;
			nbFauxParams = (nbFauxParams && nbFauxParams != null) ? nbFauxParams.length : 0;

			var nbTrueParams = nbParams - nbFauxParams;

			var timer = setInterval(function(){
				if($(".faceted-overlay").length == 0){
					$("#search_filters p.text-uppercase.h6").html($("#search_filters p.text-uppercase.h6").text()+" <span style='text-transform: lowercase'>(trois filtres maximum)</span>");
					
					var item = $("#js-active-search-filters .h6.active-filter-title");
					item.text(item.text() + " (3 max.)");
					
					if(nbTrueParams >= 3){
						$("#search_filters .facet.clearfix").each(function(){
							if($(this).find("ul li span").text().indexOf("(") == -1){
								$(this).remove();
							}
						});
					}

					clearInterval(timer);
				}
			}, 100);
		});*/
	}
});

function commandes_differees()
{
	$(".modif_exped").on("click", function(){
		/*var marginTop = $(this).offset().top - $('.modif_exped.col'+$(this).attr('data-colis')+'_1').offset().top;
		console.log('marginTop : '+marginTop);
		$('#semaine'+$(this).attr('data-semaine')).css('margin-top', (marginTop+35)+'px');*/
		$('#semaine'+$(this).attr('data-semaine')).addClass('active');
	});
	$(document).on('click', function (e) {
		if ($(e.target).closest(".modif_exped").length === 0 && $(e.target).closest(".liste_semaines").length === 0 && $(e.target).closest("span.ui-icon.ui-icon-circle-triangle-e").length === 0 && $(e.target).closest("span.ui-icon.ui-icon-circle-triangle-w").length === 0) {
			$(".liste_semaines").removeClass('active');
		}
	});
	/*$(".bouton_semaine").on("click", function(){
		$.ajax({
			method: "POST",
			url: "/ajax_colis.php",
			data: {id_category: $(this).attr('data-category'), semaine: $(this).attr('data-week')},
			success :function(data) {
			 $('.step_colis').html(data);
			 commandes_differees();
		   }
		});
	});*/

	$(".bouton_semaine_reset").on("click", function(){
		$.ajax({
			method: "POST",
			url: "/ajax_colis_reset.php",
			success :function(data) {
			 $('.step_colis').html(data);
			 commandes_differees();
			 $('.bouton_semaine_reset').css('display', 'none');
			 $('.bouton_semaine_rapide').css('display', 'block');

			 $.ajax({
				method: "POST",
				url: "/panier?ajax=1&action=refresh",
				success :function(data) {
					prestashop.emit('updatedCart', {eventType: "updateCart"});
					/*$('.js-cart-summary-subtotals-container').replaceWith(data.cart_summary_subtotals_container);
					$('.js-cart-summary-totals').replaceWith(data.cart_summary_totals);*/
				}
			 });
		   }
		});
	});

	$(".bouton_semaine_rapide").on("click", function(){
		$.ajax({
			method: "POST",
			url: "/ajax_colis_rapide.php",
			success :function(data) {
			 $('.step_colis').html(data);
			 commandes_differees();
			 $('.bouton_semaine_rapide').css('display', 'none');
			 $('.bouton_semaine_reset').css('display', 'block');

			 $.ajax({
				method: "POST",
				url: "/panier?ajax=1&action=refresh",
				success :function(data) {
					prestashop.emit('updatedCart', {eventType: "updateCart"});
					/*$('.js-cart-summary-subtotals-container').replaceWith(data.cart_summary_subtotals_container);
					$('.js-cart-summary-totals').replaceWith(data.cart_summary_totals);*/
				}
			 });
		   }
		});
	});
}

prestashop.on(
    'updatedCart',
    function (event) {
		commandes_differees();
    }
  );

$(document).ready(function(){
	/*if ( $('.js-current-step').length > 0 )
	{
		$('.js-current-step').click();
	}
	if ( $('.custom-checkout-step').length > 0 )
	{*/
	if ( $('.modif_exped').length > 0 )
	{
		commandes_differees();
		$.ajax({
			method: "POST",
			url: "/test_optim.php",
			success :function(data) {
			if (data == 1)
			{
				$('.bouton_semaine_reset').css('display', 'block');
			}
			else 
			{
				$('.bouton_semaine_rapide').css('display', 'block');
			}
		}
		});
	}
});

function calendrier(num_cal, semaines, semaine_en_cours) {
	/*function weekToDate(year, week) {
		const days = (7 * (week - 1))-1;
		const date = new Date(year, 0, days);
		return date;
	}
	function weekToDateF(year, week) {
		const days = (6 + (7 * (week - 1))-1);
		const date = new Date(year, 0, days);
		return date;
	}*/

	// Returns the ISO week of the date.
	Date.prototype.getWeek = function() {
		var date = new Date(this.getTime());
		date.setHours(0, 0, 0, 0);
		// Thursday in current week decides the year.
		date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
		// January 4 is always in week 1.
		var week1 = new Date(date.getFullYear(), 0, 4);
		// Adjust to Thursday in week 1 and count number of weeks from date to week1.
		return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
	}
	
	function weekToDate(y, weekNo){
		var d1, numOfdaysPastSinceLastMonday, rangeIsFrom, rangeIsTo;
		d1 = new Date(''+y+'');
		//console.log(y);
		//console.log(d1);
		//console.log(d1.getWeek());
		numOfdaysPastSinceLastMonday = d1.getDay() - 1;
		d1.setDate(d1.getDate() - numOfdaysPastSinceLastMonday);
		d1.setDate(d1.getDate() + (7 * (weekNo - d1.getWeek())));
		
		var jour = d1.getDate();
		if ( jour < 10 )
		{
			jour = '0'+jour;
		}
		var mois = (d1.getMonth() + 1);
		if ( mois < 10 )
		{
			mois = '0'+mois;
		}
		
		//rangeIsFrom = mois + "-" + jour + "-" + d1.getFullYear();
		rangeIsFrom = mois + "/" + jour + "/" + d1.getFullYear() ;
		
		return rangeIsFrom;
	};
	
	function weekToDateF(y, weekNo){
		var d1, numOfdaysPastSinceLastMonday, rangeIsFrom, rangeIsTo;
		d1 = new Date(''+y+'');
		numOfdaysPastSinceLastMonday = d1.getDay() - 1;
		d1.setDate(d1.getDate() - numOfdaysPastSinceLastMonday);
		d1.setDate(d1.getDate() + (7 * (weekNo - d1.getWeek())));
		d1.setDate(d1.getDate() + 6);

		var jour = d1.getDate();
		if ( jour < 10 )
		{
			jour = '0'+jour;
		}
		var mois = (d1.getMonth() + 1);
		if ( mois < 10 )
		{
			mois = '0'+mois;
		}

		//rangeIsTo = mois + "-" + jour + "-" + d1.getFullYear() ;
		rangeIsTo = mois + "/" + jour + "/" + d1.getFullYear() ;
		return rangeIsTo;
	};

	function getDateWeek(date) {
		const currentDate = 
			(typeof date === 'object') ? date : new Date();
		const januaryFirst = 
			new Date(currentDate.getFullYear(), 0, 1);
		const daysToNextMonday = 
			(januaryFirst.getDay() === 1) ? 0 : 
			(7 - januaryFirst.getDay()) % 7;
		const nextMonday = 
			new Date(currentDate.getFullYear(), 0, 
			januaryFirst.getDate() + daysToNextMonday);
	
		return (currentDate < nextMonday) ? 52 : 
		(currentDate > nextMonday ? Math.ceil(
		(currentDate - nextMonday) / (24 * 3600 * 1000) / 7) : 1);
	}

	const currentDate = new Date();
	const weekNumber = getDateWeek();

	//console.log(currentDate);

	if ( semaine_en_cours >= weekNumber )
	{
		//console.log(currentDate.getFullYear());
		var date = weekToDate(currentDate.getFullYear(), semaine_en_cours);
	}
	else
	{
		//console.log((currentDate.getFullYear()+1));
		var date = weekToDate((currentDate.getFullYear()+1), semaine_en_cours);
	}
//console.log(date);
	//const formatter = new Intl.DateTimeFormat('en-US', { day: '2-digit', month: '2-digit', year: 'numeric' });
	//const formattedDate = formatter.format(date);
	var dateText = date;
	//console.log(dateText);
	var explode = semaines.split(";");

	if ( explode[0] >= weekNumber )
	{
		var Ymin = currentDate.getFullYear();
	}
	else
	{
		var Ymin = currentDate.getFullYear()+1;
	}
	
	if ( explode[explode.length-1] >= weekNumber )
	{
		var Ymax = currentDate.getFullYear();
	}
	else
	{
		var Ymax = currentDate.getFullYear()+1;
	}

	var auxMinDate = weekToDate(Ymin, explode[0]).split('/');
	var aux2MinDate = auxMinDate[2]+auxMinDate[0]+auxMinDate[1];
	var auxMois = (currentDate.getMonth()+1);
	if ( auxMois < 10 )
	{
		auxMois = '0'+auxMois;
	}
	var auxjour = currentDate.getDate();
	if ( auxjour < 10 )
	{
		auxjour = '0'+auxjour;
	}
	var aujourdhui = currentDate.getFullYear() + '' + auxMois + '' + auxjour;
	//console.log(aux2MinDate + ' >> '+aujourdhui);
	if ( aujourdhui > aux2MinDate)
	{
		var minDateDef = auxMois + '/' + auxjour + '/' + currentDate.getFullYear();
	}
	else 
	{
		var minDateDef = weekToDate(Ymin, explode[0]);
	}
	$('#weekpicker'+num_cal).weekpicker({
	  minDate: minDateDef,
	  maxDate: weekToDateF(Ymax, explode[explode.length-1]),
	  currentText: dateText,
	  onUpdateDatepicker: function(inst) {
		$('.ui-datepicker-week-end a').removeClass("ui-state-active");
		$('.ui-datepicker-week-end.ui-datepicker-current-day').removeClass("ui-datepicker-current-day");
		$('.ui-datepicker-week-end').addClass("ui-datepicker-unselectable");
		$('.ui-datepicker-week-end').addClass("ui-state-disabled");
	  },
	  onSelect: function(dateText, startDateText, startDate, endDate, inst) {
		$.ajax({
			method: "POST",
			url: "/ajax_colis.php",
			data: {id_category: $('#'+inst.id).attr('data-category'), semaine: $.datepicker.iso8601Week(new Date(dateText))},
			success :function(data) {
			 $('.step_colis').html(data);
			 commandes_differees();
			 $('.bouton_semaine_reset').css('display', 'block');

			 $.ajax({
				method: "POST",
				url: "/panier?ajax=1&action=refresh",
				success :function(data) {
					prestashop.emit('updatedCart', {eventType: "updateCart"});
					/*$('.js-cart-summary-subtotals-container').replaceWith(data.cart_summary_subtotals_container);
					$('.js-cart-summary-totals').replaceWith(data.cart_summary_totals);*/
				}
			 });
		   }
		});
	  }
	});
  }

  (function($, undefined) {

	$.widget('lugolabs.weekpicker', {
	  _weekOptions: {
		showOtherMonths:   true,
		selectOtherMonths: true,
		showWeek: true,
		firstDay: 1,
		closeText: 'Fermer',
prevText: 'Précédent',
nextText: 'Suivant',
currentText: 'Aujourd\'hui',
monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
dayNamesMin: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
weekHeader: 'Sem'
	  },
  
	  _create: function() {
		var self = this;
		this._dateFormat = this.options.dateFormat || $.datepicker._defaults.dateFormat;
		var date = this._initialDate();
		this._setWeek(date);
		this._extendRestrictedWeeks();
		var onSelect = this.options.onSelect;
		this._picker = $(this.element).datepicker($.extend(this.options, this._weekOptions, {
		  onSelect: function(dateText, inst) {
			self._select(dateText, inst, onSelect);
		  },
		  beforeShowDay: function(date) {
			return self._showDay(date);
		  }
		}));
		$(document)
		  .on('mousemove',  '.ui-datepicker-calendar tr', function() { 
			$(this).find('td a:eq(0)').addClass('ui-state-hover'); 
			$(this).find('td a:eq(1)').addClass('ui-state-hover'); 
			$(this).find('td a:eq(2)').addClass('ui-state-hover'); 
			$(this).find('td a:eq(3)').addClass('ui-state-hover'); 
			$(this).find('td a:eq(4)').addClass('ui-state-hover'); 
			$(this).find('td a:eq(5)').removeClass('ui-state-hover'); 
			$(this).find('td a:eq(6)').removeClass('ui-state-hover'); 
		  })
		  .on('mouseleave', '.ui-datepicker-calendar tr', function() { 
			$(this).find('td a').removeClass('ui-state-hover'); 
		  });
		this._picker.datepicker('setDate', date);
	  },
  
	  _initialDate: function() {
		if (this.options.currentText) {
		  return $.datepicker.parseDate(this._dateFormat, this.options.currentText);
		} else {
		  return new Date;
		}
	  },
  
	  _select: function(dateText, inst, onSelect) {
		this._setWeek(this._picker.datepicker('getDate'));
		//console.log(this._picker.datepicker('getDate'));
		var startDateText = $.datepicker.formatDate(this._dateFormat, this._startDate, inst.settings);
		this._picker.val(startDateText);
		if (onSelect) onSelect(dateText, startDateText, this._startDate, this._endDate, inst);
	  },
  
	  _showDay: function(date) {
		var dt       = jQuery.datepicker.formatDate(this._dateFormat, date);
		var show     = this._restrictDates.indexOf(dt) < 0;
		var cssClass = date >= this._startDate && date <= this._endDate ? 'ui-datepicker-current-day' : '';
		return [show, cssClass];
	  },
  
	  _setWeek: function(date) {
		var explodedDate = this._explodeDate(date);
		this._startDate = new Date(explodedDate.year, explodedDate.month, explodedDate.day);
		this._endDate   = new Date(explodedDate.year, explodedDate.month, explodedDate.day + 5);
	  },
  
	  _selectCurrentWeek: function() {
		/*$('.ui-datepicker-calendar')
		  .find('.ui-datepicker-current-day a')
		  .addClass('ui-state-active');*/
	  },
  
	  _extendRestrictedWeeks: function() {
		this._restrictDates = [];
		if (this.options.restrictWeeks && this.options.restrictWeeks.length) {
		  var date, explodedDate;
		  for (var i = 0; i < this.options.restrictWeeks.length; i++) {
			date = $.datepicker.parseDate(this._dateFormat, this.options.restrictWeeks[i]);
			for (var j = 0; j < 7; j++) {
			  explodedDate = this._explodeDate(date);
			  date = new Date(explodedDate.year, explodedDate.month, explodedDate.day + j);
			  this._restrictDates.push(jQuery.datepicker.formatDate(this._dateFormat, date));
			}
		  }
		}
	  },
  
	  _explodeDate: function(date) {
		var dayEC = date.getDay();
		if ( dayEC == 0 )
		{
		  dayEC = 7;
		}
  
		return {
		  year:  date.getFullYear(),
		  month: date.getMonth(),
		  day:   date.getDate() - dayEC
		};
	  }
	});
  
  })(jQuery);
  