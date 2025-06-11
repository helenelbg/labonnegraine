/**
 * Cart Reminder
 *
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA <support@timactive.com>
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra AI
 *    @version 1.0.0
 *    @license   Commercial license
 *
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * All action for the form email
 */

$(document).ready(function () {
		$( '#carousel' ).elastislide();


		/*$('#example_mails').ddslick({
            width: '100%'
        });*/
		$('.convertHtmlToTxt').on("click", function (e) {
			tinyMCE.triggerSave();
			$form = $('#ta_cartreminder_mail_template_form');

			$.ajax({
				type: "POST",
				cache: false,
				data: $form.serialize() + '&convertHTMLTOTXT=1' + '&id_lang=' + id_language,
				success: function (data) {
					$("textarea[name='content_txt_"+id_language+"']").val(data);
				}
			});
		});
		$('.copyExampleMailTemplate').on("click", function (e) {
			var templateSelected = $('#example_mails').data('ddslick').selectedData.value;
			$form = $('#ta_cartreminder_mail_template_form');
			tinyMCE.triggerSave();
			$.ajax({
				type: "POST",
				cache: false,
				data: $form.serialize() + '&getExampleMailTemplate=' + templateSelected + '&id_lang=' + id_language,
				success: function (data) {
					$("textarea[name='content_html_"+id_language+"']").val(data);
					for (editorId in tinyMCE.editors) {
						if (editorId.indexOf('content_html_') !== -1) {
							var orig_element = $(tinyMCE.get(editorId).getElement());
							var name = orig_element.attr('name');
							if (name === 'content_html_' + id_language) {
								tinyMCE.get(editorId).setContent(data);
							}
						}
					}
				}
			});
		});
		var egmail_height_preview = 0;
		var mobile_preview_height = 636;
		$('a.egmail_item').on("click", function (e) {
			var egmail_fnc_load = false;
			var egmail_id = $(this).data('egmailid');
			$.ajax({
				type: "POST",
				cache: false,
				data: tamodule_url+'&ajax_action=render_egmail_custom&mode=egmail&egmail_id='+egmail_id,
				success: function (data) {
					$.fancybox({
						content   : '<div id="eg_mail_container" ></div>',
						width     : '98%',
						height    : '98%',
						padding   : '3px',
						autoDimensions: false,
						autoSize: false, //
						closeClick: false,
						openEffect: 'elastic',
						closeEffect: 'fade',
						helpers: { overlay: { css: { 'background': 'rgba(0, 0, 0, 0.65)' } } },
						afterShow : function() {
							if (!egmail_fnc_load)
							{
								$('#eg_mail_container').html(data);
								egmail_fnc_load = true;
								$( '#carousel-bgpattern' ).elastislide({minItems:3});
								egmail_height_preview = $('.fancybox-inner').height() - 120;
								$('#viewreportcontainer').height(egmail_height_preview);
								$('#ta-variable-selector').css('max-height',(egmail_height_preview - 200 - 145));
								$('.ta-palette-selector li:first').click();
								$('.preview_switch  a:first').click();
							}
						},
						afterClose: function () {
							return;
						},
						onClosed : function () {
							$("body").css({'overflow-y':'visible'});
						},
						onComplete: function () {
							if (!egmail_fnc_load)
							{
								if ($('#fancybox-content').length > 0)
								{
									$("body").css({'overflow-y':'hidden'});
									$('#eg_mail_container').html(data);
									egmail_fnc_load = true;
									$( '#carousel-bgpattern' ).elastislide({minItems:3});
									egmail_height_preview = $('#fancybox-content').height() - 120;
									$('#viewreportcontainer').height(egmail_height_preview);
									$('#ta-variable-selector').css('max-height',(egmail_height_preview - 200 - 145));
									$('.ta-palette-selector li:first').click();
									$('.preview_switch  a:first').click();
								}
							}
						}
					});
				}});
		});
		$(document).on('click','.bgpattern_item',function(){
			$('.bgpattern_item').removeClass('selected');
			$(this).addClass('selected');
			previewEgMail();
		});

		$(document).on('click','.ta-palette-selector li',function(){
			$('.ta-palette-selector li').removeClass('selected');
			var suggestion_index = $(this).data('suggestion-index');
			$('.suggestion-custom').removeClass('active');

			$('#suggestion-'+suggestion_index).addClass('active');
			$(this).addClass('selected');
			$('.colorpickeredit').colorpicker({'container':true}).on('changeColor', function(ev) {
				previewEgMail();
			});
			/*$('.colorpickeredit input').spectrum({
                   preferredFormat: "hex",
                   showInput: true
               });*/
			/*$(".mColorPicker").mColorPicker({
                               onChange: function (hsb, hex, rgb) {
                                  console.log('change');
                               }
                           }
                          );*/

			previewEgMail();
		});
		$(document).on('change','.egmail-input',function(){
			console.log('preview');
			previewEgMail();
		});


		$(document).on('click','#egmailSend', function (e) {
			$('.egmail-send-form .ta_form_loader').show();
			$('.egmail_send_test').hide();
			var id_cart = $('#egmail_cartmail_preview').val();
			var lang_iso = $('#egmail_template_lang').val();
			var emails = $('#egmail_mails_test').val();
			var id_cart_lang = $('#egmail_cartmail_preview').find(':selected').data('id-lang');
			var bgpattern = $('.bgpattern_item.selected:first').data('patternid');
			var subject = '';
			var title = '';
			if( $('#subject_'+id_cart_lang).length > 0)
			{
				subject = $('#subject_'+id_cart_lang).val();
				title = $('#title_'+id_cart_lang).val();
			}
			var $form = $('.suggestion-custom.active form');
			$.ajax({
				type: "POST",
				cache: false,
				data: $form.serialize() + '&ajax_action=send_egmail_custom&id_cart='+id_cart+'&lang_iso='+lang_iso+'&emails='+emails+
					'&subject='+subject+'&title='+title+'&bgpattern='+bgpattern,
				success: function (data) {
					$('.egmail-send-form .ta_form_loader').hide();
					$('.egmail_send_test').show();
				}
			});
		});
		$(document).on('click','#emailSend', function (e) {
			$('.egmail-send-form .ta_form_loader').show();
			$('.egmail_send_test').hide();
			var id_cart = $('#egmail_cartmail_preview').val();
			var emails = $('#egmail_mails_test').val();
			/*var subject = $('#subject_'+id_cart_lang).val();
           var title = $('#title_'+id_cart_lang).val();*/
			var $form = $('#ta_cartreminder_mail_template_form');
			$.ajax({
				type: "POST",
				cache: false,
				data: $form.serialize() + '&ajax_action=send_mail&id_cart='+id_cart+'&emails='+emails,
				success: function (data) {
					$('.egmail-send-form .ta_form_loader').hide();
					$('.egmail_send_test').show();
				}
			});
		});
		$(document).on('click','#egmailEdit', function (e) {
			var lang_iso = $('#egmail_template_lang').val();
			var bgpattern = $('.bgpattern_item.selected:first').data('patternid');
			var $form = $('.suggestion-custom.active form');
			$.ajax({
				type: "POST",
				cache: false,
				data: $form.serialize() + '&withoutinfo=1&bgpattern='+bgpattern+'&ajax_action=preview_egmail_custom&lang_iso='+lang_iso,
				success: function (data) {
					$("textarea[name='content_html_"+id_language+"']").val(data);
					for (editorId in tinyMCE.editors) {
						if (editorId.indexOf('content_html_') !== -1) {
							var orig_element = $(tinyMCE.get(editorId).getElement());
							var name = orig_element.attr('name');
							if (name === 'content_html_' + id_language) {
								tinyMCE.get(editorId).setContent(data);
							}
						}
					}
					$.fancybox.close();
					$('html, body').animate({ scrollTop: $("#content_mail_template").offset().top }, 'slow');
				}
			});
		});
		$(document).on('click','#egmailCancel', function (e) {
			$.fancybox.close();
		});

		$('.previewMail').on("click", function (e) {
			tinyMCE.triggerSave();
			var preview_mail_loader = false;
			$.ajax({
				type: "POST",
				cache: false,
				data: tamodule_url+'&ajax_action=render_egmail_custom&mode=preview',
				success: function (data) {
					$.fancybox({
						content   : '<div id="eg_mail_container" ></div>',
						width     : '98%',
						height    : '98%',
						padding   : '3px',
						autoDimensions: false,
						autoSize: false, //
						closeClick: false,
						openEffect: 'elastic',
						closeEffect: 'fade',
						helpers: { overlay: { css: { 'background': 'rgba(0, 0, 0, 0.65)' } } },
						afterShow : function() {
							if (!preview_mail_loader)
							{
								$('#eg_mail_container').html(data);
								egmail_height_preview = $('.fancybox-inner').height() - 120;
								$('#viewreportcontainer').height(egmail_height_preview);
								$('.preview_switch  a:first').click();
								previewMail();
								preview_mail_loader = true;
							}

						},
						afterClose: function () {
							return;
						},
						onClosed : function () {
							$("body").css({'overflow-y':'visible'});
						},
						onComplete: function () {
							if (!preview_mail_loader)
							{
								if ($('#fancybox-content').length > 0)
								{
									$("body").css({'overflow-y':'hidden'});
									$('#eg_mail_container').html(data);
									egmail_height_preview = $('#fancybox-content').height() - 120;
									$('#viewreportcontainer').height(egmail_height_preview);
									$('.preview_switch  a:first').click();
									previewMail();
									preview_mail_loader = true;
								}
							}
						}
					});
				}});
		});
		$(document).on('click','.preview_switch a',function(){
			$('.preview_switch a').removeClass('active');
			$('#resizer').removeClass('mobile-device');
			$('#resizer').removeClass('full-device');
			$('#resizer').css("height", "");
			$(this).addClass('active');
			var reso = $(this).data('reso');
			var zoom_percent = 100;
			if(reso=='mobile')
			{
				$('#resizer').addClass('mobile-device');
				if(egmail_height_preview > 0 & egmail_height_preview < mobile_preview_height)
				{
					zoom_percent = egmail_height_preview/mobile_preview_height * 100 - 1;
				}
				$('#resizer iframe').animate({
					width: '322px'
				}, 300);
			}
			else
			{
				$('#resizer').addClass('full-device');
				$('#resizer').height(egmail_height_preview);
				$('#resizer iframe').animate({
					width: '100%'
				}, 300);
			}
			$('#resizer').css('zoom', zoom_percent +'%');
		});

	}
);
function previewEgMail()
{
	$('.ta-content-box .ta_form_loader').show();
	var id_cart = $('#egmail_cartmail_preview').val();
	var lang_iso = $('#egmail_template_lang').val();
	var bgpattern = $('.bgpattern_item.selected:first').data('patternid');
	var $form = $('.suggestion-custom.active form');
	$.ajax({
		type: "POST",
		cache: false,
		data: $form.serialize() + '&ajax_action=preview_egmail_custom&bgpattern='+bgpattern+'&id_cart='+id_cart+'&lang_iso='+lang_iso,
		success: function (data) {
			$('.ta-content-box .ta_form_loader').hide();
			//$('#content-egmail-preview').html(data);
			var doc = document.getElementById('content-egmail-preview').contentWindow.document;
			doc.open();
			doc.write(data);
			doc.close();
		}
	});
}
function previewMail()
{
	$('.ta-content-box .ta_form_loader').show();
	var id_cart = $('#egmail_cartmail_preview').val();
	var type_render = 'html';
	var $form = $('#ta_cartreminder_mail_template_form');
	$.ajax({
		type: "POST",
		cache: false,
		data: $form.serialize() + '&ajax_action=preview_mail&id_cart='+id_cart+'&type_render='+type_render,
		success: function (data) {
			$('.ta-content-box .ta_form_loader').hide();
			//$('#content-egmail-preview').html(data);
			var doc = document.getElementById('content-egmail-preview').contentWindow.document;
			doc.open();
			doc.write(data);
			doc.close();
		}
	});
}