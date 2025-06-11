/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

$(document).ready(function () {

    /*
     * From: http://phpjs.org/functions
     */
    function version_compare(v1, v2, operator) {
        this.php_js = this.php_js || {};
        this.php_js.ENV = this.php_js.ENV || {};
        var i = 0, x = 0, compare = 0, vm = {
                'dev': -6,
                'alpha': -5,
                'a': -5,
                'beta': -4,
                'b': -4,
                'RC': -3,
                'rc': -3,
                '#': -2,
                'p': 1,
                'pl': 1
            },
            prepVersion = function (v) {
                v = ('' + v).replace(/[_\-+]/g, '.');
                v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
                return (!v.length ? [-8] : v.split('.'));
            },
            numVersion = function (v) {
                return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
            };
        v1 = prepVersion(v1);
        v2 = prepVersion(v2);
        x = Math.max(v1.length, v2.length);
        for (i = 0; i < x; i++) {
            if (v1[i] == v2[i]) {
                continue;
            }
            v1[i] = numVersion(v1[i]);
            v2[i] = numVersion(v2[i]);
            if (v1[i] < v2[i]) {
                compare = -1;
                break;
            } else if (v1[i] > v2[i]) {
                compare = 1;
                break;
            }
        }
        if (!operator) {
            return compare;
        }
        switch (operator) {
            case '>':
            case 'gt':
                return (compare > 0);
            case '>=':
            case 'ge':
                return (compare >= 0);
            case '<=':
            case 'le':
                return (compare <= 0);
            case '==':
            case '=':
            case 'eq':
                return (compare === 0);
            case '<>':
            case '!=':
            case 'ne':
                return (compare !== 0);
            case '':
            case '<':
            case 'lt':
                return (compare < 0);
            default:
                return null;
        }
    }


    /*
     * Get current browser
     * @see http://stackoverflow.com/questions/2400935/browser-detection-in-javascript#answer-2401861
     */
    navigator.sayswho = (function () {
        var ua = navigator.userAgent, tem,
            M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*([\d\.]+)/i) || [];
        if (/trident/i.test(M[1])) {
            tem = /\brv[ :]+(\d+(\.\d+)?)/g.exec(ua) || [];
            return ('IE ' + (tem[1] || ''));
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        tem = ua.match(/version\/([\.\d]+)/i);
        if (tem !== null)
            M[2] = tem[1];
        return (M.join(' '));
    })();


    /*
     * Multi Select Carriers
     */
    $('#carrier-sne_move-right').click(function () {
        return !$('#available-carriers').find('option:selected').remove().appendTo('#filtered-carriers');
    });
    $('#carrier-sne_move-left').click(function () {
        return !$('#filtered-carriers').find('option:selected').remove().appendTo('#available-carriers');
    });
    /*
     * Multi Select Status
     */
    $('#status-sne_move-right').click(function () {
        return !$('#available-status').find('option:selected').remove().appendTo('#filtered-status');
    });
    $('#status-sne_move-left').click(function () {
        return !$('#filtered-status').find('option:selected').remove().appendTo('#available-status');
    });


    /*
     * Common Multi Select
     */
    $('input[name="submitsonice_etiquetage"], button[name="submitsonice_etiquetage"]').click(function () {
        $('#filtered-carriers option, #filtered-status option').attr('selected', true);
    });


    /*
     * Login checking
     */
    $('#login_checker').click(function () {
        $('#sne_loader').show();

        $.ajax({
            type: 'POST',
            url: $('#check_login_url').val(),
            dataType: 'jsonp',
            data: $('#conf-account').find('input[name^="return_info"]').serialize(),
            success: function (data) {
                $('#sne_loader').hide();
                if (window.console)
                    console.log(data);

                if (typeof(data) === 'undefined' || data === null || typeof(data.info) === 'undefined' || data.info === null) {
                    $('.login_checker_result, #login_not_ok').show();
                    $('#error').html(data.output + '<br>' + data.console);
                    $('#error_request').html(data.request);
                    $('#error_response').html(data.response);
                    return;
                }

                if (data.info.status === true && !data.console) {
                    $('.login_checker_result, #login_ok').show();
                    $('#login_not_ok').hide();
                    if (data.output) {
                        $('#error').html(data.output);
                        $('#error_request').html(data.request);
                        $('#error_response').html(data.response);
                        $('.login_checker_result, #login_not_ok').show();
                    }
                }
                else {
                    $('#errorID').html(data.info.errorID[0] ? data.info.errorID[0] : '?');
                    $('#error').html(data.info.error[0] + '<br>' + data.console);
                    $('#error_request').html(data.request);
                    $('#error_response').html(data.response);
                    $('.login_checker_result, #login_not_ok').show();
                }
            },
            error: function (data) {
                if (window.console)
                    console.log(data);

                $('#sne_loader').hide();
                $('.login_checker_result, #login_not_ok').show();

                var login_not_ok = $('#login_not_ok');
                login_not_ok.find('strong:first').html(data.console);
                login_not_ok.find('span:last').html(data.responseText);

                $('#error_request').html(data.request);
                $('#error_response').html(data.response);
            }
        });
    });


    /*
     * TARE WEIGHT
     */
    $('.addnewweight').click(function () {
        var cloned = $('#tare_model').clone();

        cloned.find('input[type="text"]').each(function (ind, elem) {
            var name = $(elem).attr('name');
            var timestamp = $('.new_weight').length + 1;//new Date().getTime();
            $(elem).attr('name', name.replace('[0]', '[' + timestamp + ']'));
            $(elem).removeAttr('disabled');
        });

        cloned.find('.add-weight').remove();
        cloned.find('.remove-weight').removeAttr('style').click(function () {
            $(this).parent().parent().remove();
        });
        cloned.find('input').keypress(function (event) {
            if (event.which === 44) {
                var pos = $(this).val().length + 1;

                event.which = 46;
                if (event.which === 46 && $(this).val().indexOf('.') !== -1)
                    event.preventDefault();
                else
                    $(this).val($(this).val().substr(0, pos) + '.' + $(this).val().substr(pos + 1));
            }
            if (event.which < 46 || event.which > 59)
                event.preventDefault();
            if (event.which === 46 && $(this).val().indexOf('.') !== -1)
                event.preventDefault();
        });
        cloned.removeAttr('id');
        cloned.insertAfter($('.new_weight:last'));
        cloned.find('input:first').focus();
    });
    $('.remove-weight').click(function () {
        $(this).parent().parent().remove();
    });


    /*
     * FORCE FLOAT TO ... FLOAT
     * @see http://stackoverflow.com/questions/10514106/only-float-value-with-dot-not-comma-in-input-jquery
     */
    $('input[name^="tare"], input[name="return_info[deposit_date]"], input[name="return_info[meca]"]').keypress(function (event) {
        if (event.which === 44) {
            var pos = $(this).val().length + 1;

            event.which = 46;
            if (event.which === 46 && $(this).val().indexOf('.') !== -1)
                event.preventDefault();
            else
                $(this).val($(this).val().substr(0, pos) + '.' + $(this).val().substr(pos + 1));
        }

        if (event.which < 46 || event.which > 59)
            event.preventDefault(); // prevent if not number/dot

        if (event.which === 46 && $(this).val().indexOf('.') !== -1)
            event.preventDefault(); // prevent if already dot
    });

    // Chosen
    if (typeof($.fn.chosen) === 'function' && parseInt($.fn.jquery.split('.').join('')) > 172) {
        $('#conf-filter select.order_new_status, #conf-carrier_mapping select').chosen({
            width: '100%'
        });
        $('[name="return_info[output_print_type]"], [name="return_info[printer2]"], [name="return_info[compatibility]"], [name="return_info[returnTypeChoice]"], [name="return_info[weight_unit]"]').chosen({
            width: '100%',
            disable_search_threshold: 10
        });
    }

    // PHPINFO & PSINFO
    $('#phpinfo_button, #psinfo_button').click(function () {
        $('#phpinfo, #psinfo').hide().html('');
        var info_type = $(this).attr('id');

        $.ajax({
            type: 'POST',
            url: $('#sne_get_shop_info').val(),
            dataType: 'json',
            data: {
                'info': info_type == 'phpinfo_button' ? 'PHP' : 'PS'
            },
            success: function (data) {
                window.console && console.log(data);

                $('#' + info_type.split('_button').join('')).show().html(
                    typeof data == 'object' ? data.responseText : data
                );
            },
            error: function (data) {
                window.console && console.log(data);

                $('#' + info_type.split('_button').join('')).show().html(
                    typeof data == 'object' ? data.responseText : data
                );
            }
        });
    });

    // qTip
    if (typeof($.fn.qtip) !== 'undefined') {
        $('label[rel]').each(function () {
            var target_glossary_key = $(this).attr('rel') || 'qqch';
            var target_glossary_div = $('#glossary').find('div.' + target_glossary_key);

            if (target_glossary_div && target_glossary_div.length) {
                var title = $(this).text() || null;
                var content = target_glossary_div.html().trim() || 'N/A';
                var position = JSON.parse(($(this).data('myat') || '{}').replace(/'/g, '"'));

                $(this).addClass('tip').html('<span>' + title + '</span>').find('span').qtip({
                    content: {
                        text: content,
                        title: title
                    },
                    position: position,
                    hide: {
                        fixed: true,
                        delay: 300
                    }
                });
            }
        });
    }

    // Get Printers
    if (typeof(qz) == 'object' && typeof(qz.findPrinters) == 'function') {
        console.log('QZ');

        qz.findPrinter('\\{bogus_printer\\}');
        setTimeout(function () {
            var printer_list = qz.getPrinters().split(',');
            var printer2 = $('#printer2_name').val();
            console.log(printer_list);

            for (i = 0; i < printer_list.length; i++) {
                $('[name="return_info[printer2]"]').append('<option value="' + printer_list[i] + '" ' + (printer_list[i] == printer2 ? 'selected' : '') + '>' + printer_list[i] + '</option>');
            }

            if (typeof($.fn.chosen) === 'function' && parseInt($.fn.jquery.split('.').join('')) > 172) {
                $('[name="return_info[printer2]"]').trigger('chosen:updated');
            }
        }, 3000);
    } else {
        if ($('[name="return_info[printer2]"]').find('option').length < 1 && typeof(CommonPrintServer) !== 'undefined') {
            CommonPrintServer.getPrinters(function(printer_list) {
                if (typeof(printer_list) == 'undefined' || printer_list == null || typeof(printer_list.response) != 'string') {
                    printer_list = [];
                    printer_list.response = 'N/A';
                }

                printer_list = printer_list.response.split('|');
                var printer2 = $('#printer2_name').val();

                for (i = 0; i < printer_list.length; i++) {
                    $('[name="return_info[printer2]"]').append('<option value="' + printer_list[i] + '" ' + (printer_list[i] == printer2 ? 'selected' : '') + '>' + printer_list[i] + '</option>');
                }

                if (typeof($.fn.chosen) === 'function' && parseInt($.fn.jquery.split('.').join('')) > 172) {
                    $('[name="return_info[printer2]"]').trigger('chosen:updated');
                }
            })
        }
    }

    // Test Printer
    $('.test_printer').click(function () {
        $('.error_print').html('').hide();

        var printer_name = $(this).parent().parent().find('select option:selected').val();
        var file_to_print = null;
        var selected_method = $('[name="return_info[output_print_type]"] option:selected').text();

        switch (selected_method.split(' ')[0]) {
            case 'ZPL':
                file_to_print = 'https://dl.dropboxusercontent.com/u/60698220/test_zpl.prn';
                if (selected_method.indexOf('300') > -1)
                    file_to_print = 'https://dl.dropboxusercontent.com/u/60698220/test_zpl_300.prn';
                break;
            case 'DPL':
                file_to_print = 'https://dl.dropboxusercontent.com/u/60698220/test_dpl.prn';
                if (selected_method.indexOf('300') > -1)
                    file_to_print = 'https://dl.dropboxusercontent.com/u/60698220/test_dpl_300.prn';
                break;
            default:
                alert('Testez avec le format ZPL ou DPL !');
                return false;
                break;
        }

        if (typeof(CommonPrintServer) == 'object' && typeof(CommonPrintServer.getPrinters) == 'function') {

            CommonPrintServer.setPrinter(printer_name, function () {
                CommonPrintServer.printFileByURL(file_to_print);
            });
        } else if (typeof(qz) == 'object' && typeof(qz.findPrinters) == 'function') {
            qz.findPrinter(printer_name);
            qz.appendFile(file_to_print);
            qz.print();
        } else {
            alert('Utilitaire d\'impression non détecté, impossible de tester l\'impression.\nEssayez de rafraîchir la page.')
        }
    });

    // Display Label printer configuration
    $('[name="return_info[output_print_type]"]').change(function () {
        if (/(ZPL_|DPL_)/.test($(this).val())) {
            $('#label_printer, #legacy').fadeIn();
            if (!$('#legacy').find('input[name="return_info[legacy]"]:checked').val()) {
                $('#printme').fadeIn();
            }
            $('#merge_pdf').hide();
        } else {
            $('#label_printer, #printme, #legacy').fadeOut();
            $('#merge_pdf').fadeIn();
        }
    });

    $('#legacy').find('input[name="return_info[legacy]"]').change(function () {
        var print_me = $('#printme');

        $(this).val() == 1 ? print_me.hide() : print_me.show();
    });

});