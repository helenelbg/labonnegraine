/*
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2020 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

var iti;

function initMobileField() {
    var input = document.querySelector("#colissimo-pickup-mobile-phone");
    var onlyCountries;
    var allowDropDown;
    var isoDelivery;

    if (typeof colissimoDeliveryAddress === 'undefined') {
        return;
    }
    isoDelivery = colissimoDeliveryAddress['isoCountry'];
    if (isoDelivery == 'MC') {
        isoDelivery = 'FR';
    }
    if (isoDelivery == 'FR') {
        onlyCountries = ['FR'];
        allowDropDown = false;
    } else if (isoDelivery == 'BE') {
        onlyCountries = ['BE'];
        allowDropDown = false;
    } else {
        onlyCountries = ['IE', 'DK', 'IT', 'AD', 'AT', 'BE', 'DE', 'ES', 'FR', 'EE', 'GB', 'HU', 'LT', 'LU', 'LV', 'NL', 'PL', 'PT', 'SE', 'DK', 'FI', 'CZ', 'SK', 'SI'];
        allowDropDown = true;
    }

    if (input !== null) {
        iti = window.intlTelInput(input, {
            utilsScript: colissimoAjaxWidget + 'modules/colissimo/views/js/utils.js',
            initialCountry: isoDelivery,
            nationalMode: true,
            separateDialCode: true,
            hiddenInput: 'full',
            preferredCountries: [],
            onlyCountries: onlyCountries,
            allowDropdown: allowDropDown,
            customPlaceholder: typeof fctCustomPlaceholder === 'function' ? function (selectedCountryPlaceholder, selectedCountryData) {
                return fctCustomPlaceholder(selectedCountryPlaceholder, selectedCountryData)
            } : '',
        });
        var handleChange = function () {
            if(isoDelivery !== 'FR' && isoDelivery !== 'BE' ){
                if (iti.isValidNumber()) {
                    jQuery('.js-colissimo-mobile-valid').show();
                    jQuery('.js-colissimo-mobile-invalid').hide();
                    jQuery('.js-colissimo-is-mobile-valid').val('1');
                } else {
                    jQuery('.js-colissimo-mobile-valid').hide();
                    jQuery('.js-colissimo-mobile-invalid').show();
                    jQuery('.js-colissimo-is-mobile-valid').val('0');
                }
            }
        };

        input.addEventListener('change', handleChange);
        input.addEventListener('keyup', handleChange);
        input.addEventListener('countrychange', function () {
            handleChange();
        });
        iti.promise.then(function () {
            handleChange();
        });

        return iti;
    }
}

function callBackFrame(point) {
    var infoPoint = new Object({
        colissimo_id: point['identifiant'],
        company_name: point['nom'],
        address1: point['adresse1'],
        address2: point['adresse2'],
        address3: point['adress3'],
        city: point['localite'],
        zipcode: point['codePostal'],
        country: point['libellePays'],
        iso_country: point['codePays'],
        product_code: point['typeDePoint'],
        network: point['reseau']
    });
    var mobilePhoneToStore;
    var mobilePhoneToDisplay;

    if (iti === undefined) {
        mobilePhoneToStore = mobilePhone;
    } else {
        mobilePhoneToStore = iti.getNumber();
    }
    mobilePhoneToDisplay = mobilePhoneToStore;
    jQuery('.colissimo-front-widget').modal('hide');
    jQuery('#colissimo-widget-container').frameColissimoClose();
    jQuery('#checkout-delivery-step').addClass('-current js-current-step');
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'selectPickupPoint',
            infoPoint: JSON.stringify(infoPoint),
            mobilePhone: mobilePhoneToStore
        }
    }).fail(function (jqXHR, textStatus) {
    }).done(function (data) {
        jQuery('.colissimo-pickup-point-address').html(data.html_result);
        initMobileField();
        blockFixedNumber();
        iti.setNumber(mobilePhoneToDisplay);
    }).always(function (data) {
    });
}

jQuery(document).on('click', 'a.colissimo_widget_liste-points', function(e){
    e.stopPropagation();
    e.preventDefault();
});

jQuery(document).on('click', '.colissimo-front-widget', function (e) {
    const popupQuerySelector = "#colissimo-widget-container";
    const isClosest = e.target.closest(popupQuerySelector);
    if (isClosest == null && jQuery('#colissimo-widget-container').html().length != 0){
        jQuery('#colissimo-widget-container').frameColissimoClose();
        jQuery('.colissimo-front-widget').modal('hide');
    }
});

jQuery(document).on('click', '#colissimo-pickup-point-address-selected a, #colissimo-select-pickup-point', function () {
    var countryList;
    if (colissimoDeliveryAddress['isoCountry'] == 'MC') {
        countryList = 'FR';
    } else {
        countryList = colissimoDeliveryAddress['isoCountry'];
    }

    /*console.log('>>>>>>>>>'+jQuery(this).attr('id'));
    if ( jQuery(this).attr('id') == 'colissimo-select-pickup-point' )
    {
        var elementEC = jQuery(this).parent().prev();
        console.log('-------------------');
        console.log(elementEC);
        console.log(elementEC.children(jQuery('#colissimo-widget-container')));
        elementEC.children(jQuery('#colissimo-widget-container')).frameColissimoOpen({
            "ceLang": widgetLang,
            "callBackFrame": 'callBackFrame',
            "URLColissimo": "https://ws.colissimo.fr",
            "ceCountryList": countryList,
            "ceCountry": countryList,
            "dyPreparationTime": colissimoPreparationTime,
            "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
            "ceZipCode": colissimoDeliveryAddress['zipcode'],
            "ceTown": colissimoDeliveryAddress['city'],
            "token": colissimoToken,
        });
        elementEC.children(jQuery('.colissimo-front-widget')).modal('show');
        setTimeout(function(){colissimo_widget_map.resize(); }, 500);
        elementEC.children(jQuery('#widget_colissimo_adresse')).keyup(function(){
            jQuery(this).val(jQuery(this).val().toUpperCase());
        });
    }
    else 
    {*/

    if ( jQuery('.colissimo-front-widget').length > 1 )
    {
        jQuery('.colissimo-front-widget').slice(1).remove();
        
        $('#hook-display-after-carrier').append(jQuery('.colissimo-front-widget'))
    }

    jQuery('#colissimo-widget-container').frameColissimoOpen({
        "ceLang": widgetLang,
        "callBackFrame": 'callBackFrame',
        "URLColissimo": "https://ws.colissimo.fr",
        "ceCountryList": countryList,
        "ceCountry": countryList,
        "dyPreparationTime": colissimoPreparationTime,
        "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
        "ceZipCode": colissimoDeliveryAddress['zipcode'],
        "ceTown": colissimoDeliveryAddress['city'],
        "token": colissimoToken,
    });
    jQuery('.colissimo-front-widget').modal('show');
    setTimeout(function(){colissimo_widget_map.resize(); }, 500);
    jQuery('#widget_colissimo_adresse').keyup(function(){
        jQuery(this).val(jQuery(this).val().toUpperCase());
    });
    //}
});

jQuery(document).on('click', '.widget_colissimo_close', function(){
    jQuery('#colissimo-widget-container').frameColissimoClose();
    jQuery('.colissimo-front-widget').modal('hide');
});

jQuery(document).on('click', '#colissimo-opc-phone-validation', function () {
    var mobilePhoneSave = iti.getNumber();
    var isMobileValid = iti.isValidNumber();
    var btnValidation = jQuery('#colissimo-opc-phone-validation');
    var result = jQuery('.js-colissimo-mobile-validation');
    var iso = colissimoDeliveryAddress['isoCountry'];
    if (iso == 'FR' || iso == 'BE') {
        if (jQuery("input[name=colissimo_is_mobile_valid]").val() == 0) {
            isMobileValid =  0;
        } else {
            isMobileValid = 1;
        }
    }
    if (mobilePhoneSave === undefined) {
        mobilePhoneSave = '';
    }

    btnValidation.find('i').removeClass('icon-check').addClass('icon-spinner icon-spin');
    result.removeClass('colissimo-mobile-validation-success').removeClass('colissimo-mobile-validation-error').text('');

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'saveMobilePhoneOpc',
            mobilePhone: mobilePhoneSave,
            isMobileValid: isMobileValid ? 1 : 0,
        }
    }).fail(function (jqXHR, textStatus) {
    }).done(function (data) {
        result.text(data.text_result);
        if (!data.errors) {
            result.addClass('colissimo-mobile-validation-success');
            //location.reload(true);
        } else {
            result.addClass('colissimo-mobile-validation-error');
        }
    }).always(function (data) {
        btnValidation.find('i').addClass('icon-check').removeClass('icon-spinner icon-spin');
    });

});

function blockFixedNumber() {
    if (typeof colissimoDeliveryAddress === 'undefined') {
        return;
    }
    var iso = colissimoDeliveryAddress['isoCountry'];
    var inputMobile = jQuery("#colissimo-pickup-mobile-phone");
    if (inputMobile.length) {
        if (iso == 'FR' || iso == 'BE') {
            var value = inputMobile.val();
            if (value.length !== 0) {
                value = inputMobile.val().replace(/ /g, '');
            }
            CheckInputMobile(value);
            inputMobile.keyup(function(e) {
                CheckInputMobile(value);
            });
        }
    }
}

function CheckInputMobile(value) {
    
    var iso = colissimoDeliveryAddress['isoCountry'];
    var validformat = 0;
    var length = 9;
    if (iso == 'FR') {
		value = value.replace('+33', '');
        if (value.substr(0, 1) == 7 || value.substr(0, 1) == 6) {
            validformat = 1;
        }
        if (value.substr(0, 2) == 07 || value.substr(0, 2) == 06) {
            validformat = 1;
            length = 10;
        }
    }
    if (iso == 'BE') {
        var list = ['11111111', '22222222', '33333333', '44444444', '55555555', '66666666', '77777777', '88888888', '99999999', '12345678', '23456789'];
		value = value.replace('+32', '');
        if (value.substr(0, 1) == 4 && list.indexOf(value.substr(1, 8)) == -1) {
            validformat = 1;
        }
        if (value.substr(0, 2) == 04 && list.indexOf(value.substr(1, 8)) == -1) {
            validformat = 1;
            length = 10;
        }
    }
    if (validformat == 1 && value.length == length) {
        jQuery('.js-colissimo-mobile-valid').show();
        jQuery('.js-colissimo-mobile-invalid').hide();
        jQuery('.js-colissimo-is-mobile-valid').val('1');
    } else {
        jQuery('.js-colissimo-mobile-valid').hide();
        jQuery('.js-colissimo-mobile-invalid').show();
        jQuery('.js-colissimo-is-mobile-valid').val('0');
    }
}

$(document).ready(function () {
    var colissimoFrontWidget17 = jQuery('.colissimo-front-widget-17');

    colissimoFrontWidget17.appendTo('body');
    if (colissimoFrontWidget17.size() > 0) {
        iti = initMobileField();
    }
    blockFixedNumber();
});
