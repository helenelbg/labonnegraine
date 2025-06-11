/*
* 2023 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2023 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*/

var original_url = window.location + '';
var first_url_check = true;
var selected_attributes = [];
var pdf_preview;

$(document).ready(function () {
	initUrl();
	initBinds();
	initAssets();
});

function initUrl() {
	if (original_url != window.location || first_url_check) {
		first_url_check = false;
		var url = window.location + '';

		// if we need to load a specific combination
		if (url.indexOf('#/') != -1) {
			// get the params to fill from a "normal" url
			params = url.substring(url.indexOf('#') + 1, url.length);
			tabParams = params.split('/');
			tabValues = [];
			if (tabParams[0] == '') {
				tabParams.shift();
			}

			var len = tabParams.length;
			for (var i = 0; i < len; i++) {
				tabValues.push(tabParams[i].split('-'));
			}

			$('#block_templates, #block_amounts').each(function () {
				var group_name = $(this).attr('data-rewrite-group-name');
				for (var a in tabValues) {
					if (group_name === decodeURIComponent(tabValues[a][0])) {
						var blockId = $(this).attr('id');
						if (blockId === "block_templates") {
							$('#block_templates input[type=radio]').prop('checked', false).closest('.img_attribute').find('.product-image-container').removeClass('selected');
							$('#block_templates input[type=radio][value=' + tabValues[a][1] + ']').prop('checked', true).closest('.img_attribute').find('.product-image-container').addClass('selected');
						} else if (blockId === "block_amounts" && isAmountInList(tabValues[a][1])) {
							if (isAmountInFixedList(tabValues[a][1])) {
								$('#block_amounts select[name="amount_select"]').val(tabValues[a][1]);
								$('#block_amounts input[name="amount_input"]').val('');
							} else {
								$('#block_amounts select[name="amount_select"]').val(-1);
								$('#block_amounts input[name="amount_input"]').val(tabValues[a][1]).closest('.form-group').show();
							}
							$('#block_amounts input[name="amount"]').val(tabValues[a][1]);
						}
					}
				}
			});
		}
	}

	getProductAttribute(false);
}

function isAmountInList(amount) {
	if (isAmountInFixedList(amount)
		|| (custom_amount_feature
			&& (amount >= custom_amount_from && amount <= custom_amount_to && amount % pitch == 0))
	) {
		return true;
	}

	return false;
}

function isAmountInFixedList(amount) {
	var inList = false;

	$('#block_amounts select[name="amount_select"]').find('option').each(function () {
		if ($(this).val() == amount) {
			inList = true;
			return;
		}
	});

	return inList;
}

function initBinds() {
	if (!('ontouchstart' in window)) {
		$(document).on({
			mouseover: function () {
				$(this).find('.view_larger').show();
			},

			mouseout: function () {
				$(this).find('.view_larger').hide();
			}
		}, '.product-image-container');
	}

	$(document).on({
		click: function () {
			var templateId = $(this).attr('data-id');

			$('.product-image-container').each(function () {
				$(this).removeAttr('id').removeClass('selected');
				if ('ontouchstart' in window) {
					$(this).find('.view_larger').hide();
				}
				$('#block_templates input[type=radio]').prop('checked', false);

			});

			$(this).attr('id', 'bigpic').addClass('selected');

			if ('ontouchstart' in window) {
				$(this).find('.view_larger').show();
			}

			$('#block_templates input[type=radio][value=' + templateId + ']').prop('checked', true);

			var auto = $(this).attr('data-auto');
			if (typeof auto !== 'undefined' && auto !== false) {
				$('#block_amounts select[name="amount_select"] option[value="' + auto + '"]').prop('selected', true);
				$('#block_amounts select[name="amount_select"]').val(auto);
				$('#block_amounts select[name="amount_select"]').trigger('change')
			} else {
				getProductAttribute(true);
			}
		}
	}, '.product-image-container');

	$('#block_amounts select[name="amount_select"]').on('change', function () {
		$('#block_amounts input[name="amount_input"]').val('').closest('.form-group').hide();
		$('#block_amounts input[name="amount"]').val('');
		if (custom_amount_feature && ($(this).val() == -1)) {
			$('#block_amounts input[name="amount_input"]').closest('.form-group').show();
		} else {
			$('#block_amounts input[name="amount"]').val($(this).val());
			getProductAttribute(true);
		}
	});

	$('#block_amounts input[name="amount_input"]').focusout(function () {
		var amount = $(this).val();

		$('#block_amounts input[name="amount"]').val(amount);
		getProductAttribute(true);

		if (!isAmountInList(amount)) {
			showErrorMessage(invalidAmountMsg);
		}
	});

	$('#block_customization input[name="sending_method"]').on('change', function () {
		var sendingMethod = $(this).val();
		if (sendingMethod == printAtHome) {
			$('#card_text_fields').hide();
		} else {
			$('#card_text_fields').show();
		}
	});

	$('#block_button button[js-action="add-to-cart"]').on('click', function () {
		ajaxCall('getCombination');
	});

	$('#block_button button[js-action="preview"]').on('click', function () {
		ajaxCall('generatePdf');
	});
}

function initAssets() {
	$('.fancybox').fancybox({
		'hideOnContentClick': true,
		'transitionIn': 'elastic',
		'transitionOut': 'elastic'
	});

	$(".datepicker").datepicker({
		dateFormat: "yy-mm-dd"
	});
}


function getProductAttribute(addToHistory) {
	var request = '';
	var tab_attributes = [];

	$('#block_templates input[type=radio]:checked, #block_amounts input[name="amount"]').each(function () {
		var attribute = new Object();
		attribute['id_attribute_group'] = $(this).closest('.attributes').attr('data-id-attribute-group');
		attribute['group_name'] = $(this).closest('.attributes').attr('data-rewrite-group-name');
		attribute['value'] = $(this).val();
		tab_attributes.push(attribute);
	});

	selected_attributes = tab_attributes;

	if (addToHistory) {
		// build new request
		for (var a in tab_attributes) {
			request += '/' + tab_attributes[a]['group_name'] + attribute_anchor_separator + tab_attributes[a]['value'];
		}

		request = request.replace(request.substring(0, 1), '#/');
		url = window.location + '';

		// redirection
		if (url.indexOf('#') != -1) {
			url = url.substring(0, url.indexOf('#'));
		}

		window.history.replaceState({}, document.title, url + request);
	}
}

function ajaxCall(action) {
	var sendingMethod = $('#block_customization input[name="sending_method"]:checked').val();

	var customizationData = new Object();
	if ($('#card_text_fields').is(':visible') && sendingMethod == sendToFriend) {
		$('#card_text_fields').find('input[type=text], textarea').each(function () {
			customizationData[$(this).attr('name')] = this.value;
		});
	}

	if (action === 'generatePdf' && isOnNewWindow()) {
		pdf_preview = window.open('', '_blank');
	}

	var params = {
		sendingMethod: sendingMethod,
		attributes: selected_attributes,
		customizationData: customizationData,
		ajax: true,
		action: action
	};

	$.ajax({
		type: 'POST',
		url: $('#buy_block').attr('data-action'),
		data: params,
		success: function (data) {
			data = $.parseJSON(data);

			if (!data.error) {
				if (action === 'getCombination') {
					addToCart(data);
				} else if (action === 'generatePdf') {
					preview(data);
				}
			} else {
				if (action === 'generatePdf' && isOnNewWindow()) {
					pdf_preview.close();
				}

				showErrorMessage(data.error);
			}
		},
		error: function (data) {
			showErrorMessage("[TECHNICAL ERROR]");
		}
	});
}

function addToCart(data) {
	var $form = $('#buy_block');
	var query = $form.serialize() + '&id_product_attribute=' + data.giftcard_vars.id_combination + '&action=update';
	var actionURL = $form.attr('action');

	if (data.giftcard_vars.id_customization != 'undefined') {
		query += '&id_customization=' + data.giftcard_vars.id_customization;
	}

	if (!ajax_allowed) {
		window.location.href = actionURL + '?' + query;
	} else {
		if (is17) {
			$.post(actionURL, query, null, 'json').then(function (resp) {
				prestashop.emit('updateCart', {
					reason: {
						idProduct: resp.id_product,
						idProductAttribute: resp.id_product_attribute,
						idCustomization: resp.id_customization,
						linkAction: 'add-to-cart',
						cart: resp.cart
					},
					resp: resp
				});
			}).fail(function (resp) {
				prestashop.emit('handleError', { eventType: 'addProductToCart', resp: resp });
			});
		} else {
			ajaxCart.add($('#product_page_product_id').val(), data.giftcard_vars.id_combination, true, null, 1, null);
		}
	}
}

function preview(data) {
	var byteString = window.atob(data.url.split(',')[1]);
	var mimeString = data.url
		.split(',')[0]
		.split(':')[1]
		.split(';')[0];

	var num = new Array(byteString.length);
	for (var i = 0; i < byteString.length; i++) {
		num[i] = byteString.charCodeAt(i);
	}
	
	var pdfData = new Uint8Array(num);
	var blob = new Blob([pdfData], { type: mimeString });
	var url = URL.createObjectURL(blob);

	if (!isOnNewWindow()) {
		var anchor = document.createElement('a');
		anchor.href = url;
		anchor.download = 'the_gift_card.pdf';
		// document.body.appendChild(anchor);
		anchor.click();
		// document.body.removeChild(anchor);
		// URL.revokeObjectURL(url);
	} else {
		pdf_preview.location.href = url
	}
}

function isOnNewWindow() {
	if (window.navigator.userAgent.match(/Android/i)) {
		return false;
	}

	return true;
}

function showErrorMessage(msg) {
	$.growl.error({ title: "", message: msg });
}
