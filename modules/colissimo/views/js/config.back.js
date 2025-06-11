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

var iti_sender
var iti_return

function toggleLabelGenerationMode() {
    if ($('#COLISSIMO_GENERATE_LABEL_PRESTASHOP_on').prop('checked')) {
        $('.colissimo-label-generation-mode-inputs').show(400);
    } else {
        $('.colissimo-label-generation-mode-inputs').hide(200);
    }
}

function toggleWeightTareOption() {
    if ($('#COLISSIMO_USE_WEIGHT_TARE_on').prop('checked')) {
        $('.colissimo-default-tare-input').show(400);
    } else {
        $('.colissimo-default-tare-input').hide(200);
    }
}

function toggleReturnOption() {
    if ($('#COLISSIMO_ENABLE_RETURN_on').prop('checked')) {
        $('.colissimo-enable-return-inputs').show(400);
    } else {
        $('.colissimo-enable-return-inputs').hide(200);
    }
}

function toggleReturnLabelOption() {
    if ($('#COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER_on').prop('checked')) {
        $('.colissimo-generate-label-customer-inputs').show(400);
    } else {
        $('.colissimo-generate-label-customer-inputs').hide(200);
    }
}

function toggleReturnAddress() {
    if ($('#COLISSIMO_USE_RETURN_ADDRESS_on').prop('checked')) {
        $('#colissimo-return-address').show(400);
    } else {
        $('#colissimo-return-address').hide(200);
    }
}

function togglePrintingMode() {
    if ($('#COLISSIMO_USE_THERMAL_PRINTER_on').prop('checked')) {
        $('.colissimo-thermal-printer-inputs').show(400);
    } else {
        $('.colissimo-thermal-printer-inputs').hide(200);
    }
}

function togglePort() {
    if ($('#COLISSIMO_USE_ETHERNET_on').prop('checked')) {
        $('.colissimo-thermal-printer-ethernet').show(200);
        $('.colissimo-thermal-printer-usb').hide(200);
    } else {
        $('.colissimo-thermal-printer-ethernet').hide(200);
        $('.colissimo-thermal-printer-usb').show(200);
    }
}

function toggleDDPOption() {
    if ($("#COLISSIMO_ENABLE_DDP_on").prop("checked")) {
        $(".colissimo-ddp-costs").show(400);
    } else {
        $(".colissimo-ddp-costs").hide(200);
    }
}

function toggleWeightingOption() {
    if ($("#COLISSIMO_WEIGHTING_PRICES_on").prop("checked")) {
        $(".colissimo-weighting-type").show(400);
        toggleWeightingValue();
    } else {
        $(".colissimo-weighting-type").hide(200);
        $('.colissimo-weighting-value-percent').hide(200);
        $('.colissimo-weighting-value-amount').hide(200);
    }
}

function toggleWeightingValue() {
    var percent = $('.colissimo-weighting-value-percent');
    var amount = $('.colissimo-weighting-value-amount');
    if ($('#type-percent').prop('checked')) {
        amount.hide(200);
        percent.show(400);
    } else {
        percent.hide(200);
        amount.show(400);
    }
}

function testWSCredentials(colissimoCheckCredentials) {
    var data = {
        controller: 'AdminColissimoTestCredentials',
        ajax: 1,
        token: colissimoCredentialsToken,
        action: 'testWSCredentials'
    };

    colissimoCheckCredentials.find('i').attr('class', 'icon icon-spin icon-spinner');
    colissimoCheckCredentials.addClass('disabled');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: $('#colissimo-account-config-form').serialize()
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        if (data.errors !== true) {
            showSuccessMessage(data.message);
        } else {
            showErrorMessage(data.message);
        }
    }).always(function (data) {
        colissimoCheckCredentials.find('i').attr('class', 'icon icon-check');
        colissimoCheckCredentials.removeClass('disabled');
    });
}

function testWidgetCredentials(checkWidget) {
    var data = {
        controller: 'AdminColissimoTestCredentials',
        ajax: 1,
        token: colissimoCredentialsToken,
        action: 'testWidgetCredentials'
    };

    checkWidget.find('i').attr('class', 'icon icon-spin icon-spinner');
    checkWidget.addClass('disabled');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: $('#colissimo-widget-config-form, #colissimo-account-config-form').serialize()
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        if (data.errors !== true) {
            showSuccessMessage(data.message);
        } else {
            showErrorMessage(data.message);
        }
    }).always(function (data) {
        checkWidget.find('i').attr('class', 'icon icon-check');
        checkWidget.removeClass('disabled');
    });
}

function bindSwapSaveColissimo() {
    if ($('#selectedSwap option').length !== 0)
        $('#selectedSwap option').attr('selected', 'selected');
    else
        $('#availableSwap option').attr('selected', 'selected');
}

function bindSwapButtonColissimo(prefix_button, prefix_select_remove, prefix_select_add) {
    $('#' + prefix_button + 'Swap').on('click', function (e) {
        e.preventDefault();
        $('#' + prefix_select_remove + 'Swap option:selected').each(function () {
            $('#' + prefix_select_add + 'Swap').append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option>");
            $(this).remove();
        });
        $('#selectedSwap option').prop('selected', true);
    });
}

function processMigrationStep(processes, modulesToMigrate) {
    var process = processes.pop();
    var step = $('.colissimo-migration-step2 .step-' + process);

    if (process) {
        step.toggleClass('current');
        step.find('i').toggleClass('icon-spinner icon-spin icon-check');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoMigration',
                ajax: 1,
                token: colissimoTokenMigration,
                action: 'migrateStep',
                step: process,
                modules_to_migrate: modulesToMigrate
            }
        }).fail(function (jqXHR, textStatus) {
        }).done(function (data) {
            step.toggleClass('current done');
            step.find('i').toggleClass('icon-spinner icon-spin icon-check');
            processMigrationStep(processes, modulesToMigrate);
        }).always(function (data) {
        });
    } else {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoMigration',
                ajax: 1,
                token: colissimoTokenMigration,
                action: 'endMigration',
                modules_to_migrate: modulesToMigrate
            }
        }).fail(function (jqXHR, textStatus) {
        }).done(function (data) {
            $('#colissimo-migration-result').html(data.html_result);
        }).always(function (data) {
        });
    }
}

function startMigration(modulesToMigrate) {
    var processes = ['credentials', 'carriers', 'configuration', 'data', 'documents'];

    processes.reverse();
    processMigrationStep(processes, modulesToMigrate);
}

function countDown($source, $target) {
    var max = $source.attr("data-maxchar");
    $target.html(max - $source.val().length);

    $source.keyup(function () {
        $target.html(max - $source.val().length);
    });
}

function initSenderPhoneField(iso) {
    var input = document.querySelector('#sender-phone');
    var element = $('#sender-phone');

    iti_sender = initPhoneField(iti_sender, input, element, iso);
}

function initReturnPhoneField(iso) {
    var input = document.querySelector("#return-phone");
    var element = $("#return-phone");

    iti_return = initPhoneField(iti_return, input, element, iso);
}

function initPhoneField(iti, input, element, iso) {
    var allowDropDown = true;

    if (input !== null) {
        iti = window.intlTelInput(input, {
            utilsScript: baseDir + 'modules/colissimo/views/js/utils.js',
            initialCountry: iso,
            nationalMode: true,
            separateDialCode: true,
            hiddenInput: 'full',
            preferredCountries: [],
            onlyCountries: onlyCountries,
            allowDropdown: allowDropDown,
        });
        var handleChange = function () {
            if (iti.isValidNumber()) {
                element.closest('.adr-form').find('.js-colissimo-mobile-valid').show();
                element.closest('.adr-form').find('.js-colissimo-mobile-invalid').hide();
                element.closest('.adr-form').find('.js-colissimo-is-mobile-valid').val('1');
            } else {
                element.closest('.adr-form').find('.js-colissimo-mobile-valid').hide();
                element.closest('.adr-form').find('.js-colissimo-mobile-invalid').show();
                element.closest('.adr-form').find('.js-colissimo-is-mobile-valid').val('0');
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

function processUseReturnAddress(val) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminModules',
            ajax: 1,
            configure: 'colissimo',
            token: token,
            action: 'useReturnAddress',
            returnAddress: val,
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(addressErrorMessage);
    }).done(function (data) {
        showSuccessMessage(data.message);
    });
}

$(document).ready(function () {
    if (!$("#sender-company").length) {
        return;
    }

    if (!$("#return-company").length) {
        return;
    }

    initSenderPhoneField(colissimoSenderPhone);
    initReturnPhoneField(colissimoReturnPhone);

    countDown($("#sender-company"), $("#sender-company_counter"));
    countDown($("#sender-lastname"), $("#sender-lastname_counter"));
    countDown($("#sender-firstname"), $("#sender-firstname_counter"));
    countDown($("#sender-address1"), $("#sender-address1_counter"));
    countDown($("#sender-address2"), $("#sender-address2_counter"));
    countDown($("#sender-address3"), $("#sender-address3_counter"));
    countDown($("#sender-address4"), $("#sender-address4_counter"));
    countDown($("#sender-city"), $("#sender-city_counter"));
    countDown($("#sender-email"), $("#sender-email_counter"));

    countDown($("#return-company"), $("#return-company_counter"));
    countDown($("#return-lastname"), $("#return-lastname_counter"));
    countDown($("#return-firstname"), $("#return-firstname_counter"));
    countDown($("#return-address1"), $("#return-address1_counter"));
    countDown($("#return-address2"), $("#return-address2_counter"));
    countDown($("#return-address3"), $("#return-address3_counter"));
    countDown($("#return-address4"), $("#return-address4_counter"));
    countDown($("#return-city"), $("#return-city_counter"));
    countDown($("#return-email"), $("#return-email_counter"));

    toggleLabelGenerationMode();
    toggleWeightTareOption();
    toggleReturnOption();
    toggleReturnLabelOption();
    toggleReturnAddress();
    togglePrintingMode();
    togglePort();
    toggleDDPOption();
    toggleWeightingOption();
    
    $(document).on('click', '.colissimo-generate-label-prestashop span', function () {
        toggleLabelGenerationMode();
    });
    $(document).on('click', '.colissimo-use-weight-tare span', function () {
        toggleWeightTareOption();
    });
    $(document).on('click', '.colissimo-enable-return span', function () {
        toggleReturnOption();
    });
    $(document).on('click', '.colissimo-display-return-label-customer span', function () {
        toggleReturnLabelOption();
    });
    $(document).on('click', '.colissimo-use-thermal-printer span', function () {
        togglePrintingMode();
    });
    $(document).on('click', '.colissimo-use-ethernet span', function () {
        togglePort();
    });
    $(document).on("click", ".colissimo-active-ddp span", function () {
        toggleDDPOption();
    });
    $(document).on("click", ".colissimo-weighting-prices span", function () {
        toggleWeightingOption();
    });
    $(document).on('click', '#type-percent', function () {
        toggleWeightingValue();
    });
    $(document).on('click', '#type-amount', function () {
        toggleWeightingValue();
    });
    $('input[type=radio][name=COLISSIMO_USE_RETURN_ADDRESS]').change(function () {
        var useReturnAddress = $('input[name=COLISSIMO_USE_RETURN_ADDRESS]:checked').val();

        toggleReturnAddress();
        processUseReturnAddress(useReturnAddress);
    });
    $('select[name="sender_country"]').change(function () {
        if ($(this).val() == 'MC') {
            iti_sender.setCountry('FR');
        } else {
            iti_sender.setCountry($(this).val());
        }
    });
    $('select[name="return_country"]').change(function () {
        if ($(this).val() == 'MC') {
            iti_return.setCountry('FR');
        } else {
            iti_return.setCountry($(this).val());
        }
    });
    if (typeof $('#addSwap') !== undefined && typeof $("#removeSwap") !== undefined &&
        typeof $('#selectedSwap') !== undefined && typeof $('#availableSwap') !== undefined) {
        bindSwapButtonColissimo('add', 'available', 'selected');
        bindSwapButtonColissimo('remove', 'selected', 'available');

        $('button:submit').click(bindSwapSaveColissimo());
    }
});
