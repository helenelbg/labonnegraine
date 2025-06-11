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

    /** @typedef {string} data.console */
    /** @typedef {string} data.output */
    /** @typedef {string} data.warning */
    /** @typedef {string} data.html_done */
    /** @typedef {string} data.urls */
    /** @typedef {function} showNoticeMessage */
    /** @typedef {function} qz.findPrinter */
    /** @typedef {function} qz.appendFile */

    /*
     * SELECTOR CACHE
     */
    var selector_table_orders = $('#table_orders');
    var selector_label_session = $('#sne_labels_session');
    var selector_table_exp = $('#table_exp');
    var selector_table_exp_done = $('#table_exp_done');
    var selector_filter_carrier = $('#sne_filter_carrier');
    var selector_filter_date = $('#sne_filter_date');
    var selector_labels_availables = $('#sne_labels_availables');
    var selector_nb_display_first = $('.nb_display:first');
    var selector_current_page = $('.current_page');
    var selector_page_previous_first = $('.page_previous, .page_first');
    var selector_page_next_last = $('.page_next, .page_last');
    var selector_total_page = $('.total_page');
    var selector_total_orders = $('.total_orders');
    var selector_session_creation = $('#session_creation');
    var selector_session_selection = $('#session_selection');
    var selector_session_select = $('#session_select');
    var selector_session_fusion = $('#session_fusion');
    var selector_session_select_fusion = $('#session_select_fusion');
    var selector_sne_error_orders = $('#sne_error_orders');

    /*
     * DATA
     */
    var nb_order = selector_table_orders.find('tbody tr').length;
    var count_nb_order = 0;
    var id_session;
    var err_warn_conf = $('div[id^="sne_error_"], div[id^="sne_warn_"], div[id^="sne_conf_"]');
    var last_session_used = $('#sne_last_session_used').val();
    var printer1 = $('#printer1_name').val();
    var printer2 = $('#printer2_name').val();
    var last_ta_d150 = null;
    var print_type = /PDF_/.test($('#sne_print_type').val()) ? 'PDF' : 'ZPL';
    var ps16x = parseInt($.fn.jquery.split('.').join('')) >= parseInt('1110');

    /*
     * Tabs
     */
    $('[id^="menu-"]').click(function () {
        var tab_name = $(this).attr('id').split('-')[1];

        $('[id^="menu-"]').removeClass(), $(this).addClass('active menuTabButton selected'), $('div[id^="tab-"]').hide(), $('#tab-' + tab_name).show();

        switch ($(this).attr('id')) {
            case 'menu-exp':
                get_expedition_list();
                break;
            case 'menu-close':
                last_ta_d150 = selector_table_orders.find('tbody tr.validated_row input[name^="data"], tbody tr.success input[name^="data"]').serialize();
                try_once = false;
            /* FALLTHROUGH */
            case 'menu-orders':
                get_order_list();
                break;
        }
    });


    /**
     * Checkbox range selection (a la GMail)
     * source : http://www.barneyb.com/barneyblog/2008/01/08/checkbox-range-selection-a-la-gmail/
     *
     * @param {jQuery} $ jQuery object
     */
    (function ($) {
        $.fn.enableCheckboxRangeSelection = function () {
            var lastCheckbox = null;
            var $spec = this;
            $spec.unbind("click.checkboxrange");
            $spec.bind("click.checkboxrange", function (e) {
                if (lastCheckbox !== null && (e.shiftKey || e.metaKey)) {
                    $spec.slice(
                        Math.min($spec.index(lastCheckbox), $spec.index(e.target)),
                        Math.max($spec.index(lastCheckbox), $spec.index(e.target)) + 1
                    ).attr({checked: e.target.checked ? "checked" : ""});
                }
                lastCheckbox = e.target;
            });
        };
    })(jQuery);


    /**
     * Determine if user finished typing
     * source : http://stackoverflow.com/questions/14042193/how-to-trigger-an-event-in-input-text-after-i-stop-typing-writting
     *
     * @param {jQuery} $ jQuery object
     */
    (function ($) {
        $.fn.extend({
            donetyping: function (callback, timeout) {
                timeout = timeout || 1300; // default timeout 1.3s
                var timeoutReference, doneTyping = function (el) {
                    if (!timeoutReference) return;
                    timeoutReference = null;
                    callback.call(el);
                };
                return this.each(function (i, el) {
                    var $el = $(el);
                    $el.is(':input') && $el.keypress(function () {
                        if (timeoutReference) clearTimeout(timeoutReference);
                        timeoutReference = setTimeout(function () {
                            doneTyping(el);
                        }, timeout);
                    }).blur(function () {
                        doneTyping(el);
                    });
                });
            }
        });
    })(jQuery);


    /**
     * Sort list of element
     */
    jQuery.fn.sortElements = (function () {
        var sort = [].sort;
        return function (comparator, getSortable) {
            getSortable = getSortable || function () {
                    return this;
                };
            var placements = this.map(function () {
                var sortElement = getSortable.call(this), parentNode = sortElement.parentNode, nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );
                return function () {
                    if (parentNode === this) {
                        throw new Error("You can't sort elements if any one is a descendant of another.");
                    }
                    parentNode.insertBefore(this, nextSibling);
                    parentNode.removeChild(nextSibling);
                };
            });
            return sort.call(this, comparator).each(function (i) {
                placements[i].call(getSortable.call(this));
            });
        };
    })();


    /**
     * Blink an element
     *
     * @param {jQuery} $ jQuery object
     */
    (function ($) {
        $.fn.blink = function (remove, complete) {
            //noinspection JSUnresolvedFunction
            this.fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100, function () {
                if (complete)
                    complete();
                if (remove) {
                    this.remove();
                    $('.nb_awaiting_package b').text(selector_table_exp.find('tbody tr').length);
                }
            });
        };
    })(jQuery);


    function get_labels() {
        err_warn_conf.hide();

        var selector_checkbox_checked = $('input[name^="checkbox"]:checked');

        if (count_nb_order > nb_order) {
            window.console && console.log('Infinite loop stopped.');

            $('#sne_warn_orders').show().find('code').html($('#warn_infinite_loop').val());
            selector_checkbox_checked.show().parent().find('img').hide();

            return (false);
        }

        var pAjax = {
            type: 'POST',
            url: $('#sne_webservice_url').val(),
            data_type: 'jsonp',
            data: selector_checkbox_checked.not(':disabled').serialize() + '&' + $('input[name^="data"]:checked').serialize() + '&context_key=' + $('#sne_context_key').val()
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            error: function (data) {
                selector_sne_error_orders.show().find('code').html(data.responseText);
                selector_checkbox_checked.show().parent().find('img').hide();
            },
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    selector_sne_error_orders.show();
                    selector_checkbox_checked.show().parent().find('img').hide();
                    return;
                }

                if (typeof(data.pdfs) === 'undefined' || data.pdfs === null || typeof(data.pdfs) !== 'object') {
                    window.console && console.log(data);
                    selector_sne_error_orders.show().find('code').html(data.console);
                    selector_checkbox_checked.show().parent().find('img').hide();
                    return;
                }

                if (data.console !== '' && data.console !== null) {
                    window.console && console.log(data);
                    selector_sne_error_orders.show().find('code').html(data.console);
                    selector_checkbox_checked.show().parent().find('img').hide();
                    return;
                }

                $.each(data.pdfs, function (key, val) {
                    var table_order_selector = selector_table_orders.find('tr[rel="' + key + '"]');

                    table_order_selector.find('.parcel_number').text(val.parcelNumber);
                    table_order_selector.find('.pdfurl').attr('href', val.PdfUrl).show();
                    table_order_selector.find('.zpl_code').text(val.zpl_code);
                    ps16x ? table_order_selector.addClass('success') : table_order_selector.addClass('validated_row');
                    table_order_selector.find('.label *, .label_t *').not('textarea').toggle();
                    table_order_selector.find('.label_t br, .label_t a, .label_t a span').show();
                    table_order_selector.find('input').show().attr('disabled', true).attr('readonly', true);
                });

                err_warn_conf.hide();
                count_nb_order += selector_checkbox_checked.length > 3 ? 3 : selector_checkbox_checked.length;

                if (selector_checkbox_checked.not(':disabled').length)
                    get_labels();
                else {
                    selector_checkbox_checked.show().parent().find('img').hide();
                    selector_table_orders.find('tr input').show().attr('disabled', false).attr('readonly', false);
                }
            }
        });
    }

    function delete_labels() {
        var selector_checkbox_checked = $('input[name^="checkbox"]:checked');
        var pAjax = {
            type: 'POST',
            url: $('#sne_deletelabel_url').val(),
            data_type: 'jsonp',
            data: selector_checkbox_checked.serialize()
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    selector_sne_error_orders.show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    selector_sne_error_orders.show().find('code').html(data.console);
                    return;
                }

                if (typeof(data.result) !== 'undefined' && data.result !== null && data.result) {
                    selector_checkbox_checked.each(function () {
                        $(this).closest('tr').removeClass().find('.label_t *').not('textarea').toggle();
                    });
                }
                else
                    selector_sne_error_orders.show().find('code').html($('#err_exp_false').val());
            },
            error: function (data) {
                window.console && console.log(data);
                selector_sne_error_orders.show().find('code').html(data.responseText);
            }
        });
    }


    function get_order_list() {
        if (!selector_label_session.find('tbody tr').length)
            return;

        err_warn_conf.hide();

        $('#one_checkbox_to_rule_them_all_session').attr('checked', true);
        var orders = $('input[name^="listing_session_checkbox"]').attr('checked', true).serialize();
        var pAjax = {
            type: 'POST',
            url: $('#sne_getorderlist_url').val(),
            data_type: 'jsonp',
            data: orders
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    selector_sne_error_orders.show();
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    selector_sne_error_orders.show().find('code').html(data.console);
                }

                if (data.html !== 'undefined' || data.html !== null) {
                    selector_table_orders.find('tbody').html(data.html).find('td a').each(function () {
                        $(this).attr('href', $(this).attr('href').replace('sne_token_order', $('#sne_token_order_url').val()));
                    });
                    if ($('#mode-normal').is(':visible'))
                        selector_table_orders.find('tbody .address').hide();
                }
                else {
                    window.console && console.log(data);
                    selector_sne_error_orders.show();
                }

                nb_order = selector_table_orders.find('tbody tr').length;
            },
            error: function (data) {
                window.console && console.log(data);
                selector_sne_error_orders.show(data.console);
            }
        });
    }


    function get_expedition_list() {
        err_warn_conf.hide();

        var pAjax = {
            type: 'POST',
            url: $('#sne_getlabelexpedition_url').val(),
            data_type: 'jsonp',
            data: {
                id_session: id_session,
                for_session: 0
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_exp').show();
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_error_exp').show().find('code').html(data.console);
                }

                selector_table_exp.find('tbody').html(data.html);
                selector_table_exp_done.find('tbody').html(data.html_done);
                $('.barcode-input').focus();
                $('.nb_awaiting_package b').text(selector_table_exp.find('tbody tr').length);
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_exp').show().find('code').html(data.responseText);
            }
        });
    }


    function printDocument(sne_print_list, printer_name) {
        err_warn_conf.hide();

        if (!sne_print_list.length) {
            return false;
        }

        var id_order = sne_print_list.shift();
        var pdf_url = selector_table_orders.find('tr[rel="' + id_order + '"] .pdfurl').attr('href');
        var protocol = window.location.protocol;

        if (pdf_url.split(protocol).length == 1) {
            // current protocol and protocol of label not matching, need to fix
            pdf_url = protocol + pdf_url.substr(pdf_url.indexOf(':') + 1);
        }

        if (print_type == 'PDF') {
            var id_iframe = 'label_order_' + id_order;

            $('body').append('<iframe id="' + id_iframe + '" style="display:none;"></iframe>');
            $('#' + id_iframe).attr('src', pdf_url).load(function () {
                this.contentWindow.print();
            });
            setTimeout(function () {
                $('#' + id_iframe).remove();
            }, 120000);

            setTimeout(function () {
                printDocument(sne_print_list, printer_name);
            }, 800)
        } else {
            if (typeof(CommonPrintServer) == 'object' && typeof(CommonPrintServer.getPrinters) == 'function') {
                CommonPrintServer.setPrinter(printer_name, function () {
                    CommonPrintServer.printFileByURL(pdf_url, function () {
                        window.console && console.log('Printed #' + id_order + ' [' + pdf_url + ']');
                        printDocument(sne_print_list, printer_name);
                    });
                });
            }  else if (typeof(qz) == 'object' && typeof(qz.findPrinters) == 'function') {
                qz.findPrinter(printer_name);
                qz.appendFile(pdf_url);
                qz.print();
                setTimeout(function () {
                    printDocument(sne_print_list, printer_name);
                }, 800)
            }
        }
    }


    $('#one_checkbox_to_rule_them_all').change(function () {
        var state = Boolean($(this).attr('checked'));
        $('input[name="checkbox[]"]').not(':disabled').attr('checked', state);
    });
    $('#one_checkbox_to_rule_them_all_listing').change(function () {
        var state = Boolean($(this).attr('checked'));
        $('input[name="listing_checkbox[]"]:visible').not(':disabled').attr('checked', state);
    });
    $('#one_checkbox_to_rule_them_all_session').change(function () {
        var state = Boolean($(this).attr('checked'));
        $('input[name="listing_session_checkbox[]"]').not(':disabled').attr('checked', state);
    });
    $('#one_checkbox_to_rule_them_all_exp').change(function () {
        var state = Boolean($(this).attr('checked'));
        $('input[name="exp_checkbox[]"]').not(':disabled').attr('checked', state);
    });


    selector_label_session.delegate('tr .customer, tr .carrier, tr .reference, tr .date, tr .value, tr .qty', 'click', function () {
        if ($(this).parent().find('input[type="checkbox"]').attr('disabled'))
            return;

        var state = Boolean($(this).parent().find('input[type="checkbox"]').attr('checked'));
        $(this).parent().find('input[type="checkbox"]').attr('checked', !state);
    });
    selector_labels_availables.delegate('tr td', 'click', function () {
        if ($(this).parent().find('input[type="checkbox"]').attr('disabled'))
            return;

        if ($(this).find('input[type="checkbox"]').length || $(this).attr('class') === 'id_order')
            return;

        var state = Boolean($(this).parent().find('input[type="checkbox"]').attr('checked'));
        $(this).parent().find('input[type="checkbox"]').attr('checked', !state);
    });
    selector_table_orders.delegate('.customer, .carrier, .address, .date', 'click', function () {
        if ($(this).parent().find('input[type="checkbox"]:first').attr('disabled'))
            return;

        var state = Boolean($(this).parent().find('input[type="checkbox"]:first').attr('checked'));
        $(this).parent().find('input[type="checkbox"]:first').attr('checked', !state);
    });
    selector_table_exp.delegate('.left', 'click', function () {
        if ($(this).parent().find('input[type="checkbox"]:first').attr('disabled'))
            return;

        var state = Boolean($(this).parent().find('input[type="checkbox"]:first').attr('checked'));
        $(this).parent().find('input[type="checkbox"]:first').attr('checked', !state);
    });


    $('#get_labels').click(function () {
        var selector_checkbox_checked = $('input[name^="checkbox"]:checked');

        selector_checkbox_checked.each(function () {
            if (ps16x && $(this).closest('tr').hasClass('success')) {
                $(this).attr('checked', false);
            } else if (!ps16x && $(this).closest('tr').hasClass('validated_row') || $(this).closest('tr').hasClass('validated_row2')) {
                $(this).attr('checked', false);
            }
        });

        selector_checkbox_checked = selector_checkbox_checked.filter(':checked')

        if (!selector_checkbox_checked.length) {
            return false;
        }

        selector_checkbox_checked.parent().find('*').toggle();

        setTimeout(function () {
            get_labels();
        }, 500);
    });
    $('#delete_labels').click(function () {
        var selector_checkbox_checked = $('input[name^="checkbox"]:checked');

        selector_checkbox_checked.each(function () {
            if (ps16x && !$(this).closest('tr').hasClass('success'))
                $(this).attr('checked', false);
            else if (!ps16x && !$(this).closest('tr').hasClass('validated_row') && !$(this).closest('tr').hasClass('validated_row2'))
                $(this).attr('checked', false);
        });

        if (!selector_checkbox_checked.not(':disabled').length)
            return;

        delete_labels();
    });
    $('#print_labels').click(function () {
        var selector_checkbox_checked = $('input[name^="checkbox"]:checked');

        selector_checkbox_checked.each(function () {
            if (ps16x && !$(this).closest('tr').hasClass('success'))
                $(this).attr('checked', false);
            else if (!ps16x && !$(this).closest('tr').hasClass('validated_row') && !$(this).closest('tr').hasClass('validated_row2'))
                $(this).attr('checked', false);
        });

        if (!selector_checkbox_checked.not(':disabled').length)
            return;

        var sne_print_list = [];

        selector_checkbox_checked.each(function () {
            sne_print_list.push($(this).val());
        });
        printDocument(sne_print_list, printer2);
        selector_checkbox_checked.closest('tr').removeClass().addClass(ps16x ? 'success' : 'validated_row2');
    }); 


    /*
     * Filters
     */
    $('#sne_filter_id').keyup(function () {
        var value_to_filter = $(this).val();
        $('input[id^="sne_filter_"]').not('#sne_filter_id').val('');

        if (value_to_filter === '')
            selector_table_orders.find('.id_order').each(function () {
                $(this).parent().show();
            });
        else
            selector_table_orders.find('.id_order').each(function () {
                if ($(this).text().indexOf(value_to_filter) > -1) {
                    $(this).parent().show();
                }
                else {
                    $(this).parent().hide();
                }
            });
    });
    $('#sne_filter_customer').keyup(function () {
        var value_to_filter = $(this).val().toUpperCase();
        $('input[id^="sne_filter_"]').not('#sne_filter_customer').val('');

        if (value_to_filter === '')
            selector_table_orders.find('.customer').each(function () {
                $(this).parent().show();
            });
        else
            selector_table_orders.find('.customer').each(function () {
                if ($(this).text().toUpperCase().indexOf(value_to_filter) > -1) {
                    $(this).parent().show();
                }
                else {
                    $(this).parent().hide();
                }
            });
    });
    selector_filter_carrier.change(function () {
        var value_to_filter = selector_filter_carrier.find('option:selected').val();
        $('input[id^="sne_filter_"]').not('#sne_filter_carrier').val('');

        if (value_to_filter === '')
            selector_table_orders.find('.carrier').each(function () {
                $(this).parent().show();
            });
        else
            selector_table_orders.find('.carrier').each(function () {
                if ($(this).text().replace(/\s+/g, ' ').indexOf(value_to_filter) > -1) {
                    $(this).parent().show();
                }
                else {
                    $(this).parent().hide();
                }
            });
    });
    $('#sne_filter_address').change(function () {
        var value_to_filter = $(this).val();
        $('input[id^="sne_filter_"]').not('#sne_filter_address').val('');

        if (value_to_filter === '')
            selector_table_orders.find('.address').each(function () {
                $(this).parent().show();
            });
        else
            selector_table_orders.find('.address').each(function () {
                if ($(this).text().indexOf(value_to_filter) > -1) {
                    $(this).parent().show();
                }
                else {
                    $(this).parent().hide();
                }
            });
    });
    selector_filter_date.change(function () {
        var value_to_filter = $(this).val();
        $('input[id^="sne_filter_"]').not('#sne_filter_date').val('');

        if (value_to_filter === '')
            selector_table_orders.find('.date').each(function () {
                $(this).parent().show();
            });
        else
            selector_table_orders.find('.date').each(function () {
                if ($(this).text().indexOf(value_to_filter) > -1) {
                    $(this).parent().show();
                }
                else {
                    $(this).parent().hide();
                }
            });
    });
    // If ESC key is pressed, refresh all filters
    $('body').keyup(function (e) {
        if (e.keyCode === 27) {
            selector_table_orders.find('tbody tr').show();
            $('input[id^="sne_filter_"]').val('');
            selector_filter_carrier.find('option:first').attr('selected', true);
        }
    });

    // Date Picker
    selector_filter_date.datepicker({
        dateFormat: 'yy-mm-dd'
    });
    // CheckBox Range Selection
    $('input[name^="checkbox"]').enableCheckboxRangeSelection();
    $('input[name^="listing_checkbox"]').enableCheckboxRangeSelection();


    /*
     * Pagination
     */
    $('.nb_display').change(function () {
        var row = parseInt($(this).val());
        var total_page = parseInt(selector_total_orders.first().text());

        selector_labels_availables.find('tbody tr').hide().slice(0, row).show();
        selector_page_next_last.show();
        selector_total_page.text(Math.ceil(total_page / row));
        selector_current_page.text(1);
        $(this).val(row);
    });
    $('.page_next, .page_last, .page_previous, .page_first').click(function () {
        var offset = 0;
        var row = 20;
        var direction = $(this).attr('class');

        switch (direction) {
            case ('page_next'):
                offset = parseInt(selector_labels_availables.find('tbody tr:visible:last').next('tr')[0].sectionRowIndex);
                row = parseInt(selector_nb_display_first.val());

                selector_current_page.text(parseInt(selector_current_page.first().text()) + 1);
                selector_page_previous_first.show();
                (parseInt(selector_current_page.first().text()) === parseInt(selector_total_page.first().text())) ?
                    selector_page_next_last.hide() : selector_page_next_last.show();
                break;

            case ('page_previous'):
                row = parseInt(selector_nb_display_first.val());
                offset = parseInt(selector_labels_availables.find('tbody tr:visible:first').prev('tr')[0].sectionRowIndex - (row - 1));

                selector_current_page.text(parseInt(selector_current_page.first().text()) - 1);
                (parseInt(selector_current_page.first().text()) === 1) ?
                    selector_page_previous_first.hide() : selector_page_previous_first.show();
                break;

            case ('page_first'):
                row = parseInt(selector_nb_display_first.val());

                selector_current_page.text(1);
                selector_page_next_last.show();
                selector_page_previous_first.hide();
                break;

            case ('page_last'):
                row = parseInt(selector_nb_display_first.val());
                offset = parseInt(selector_total_orders.first().text() - (selector_total_orders.first().text() - row));

                selector_current_page.text(selector_total_page.first().text());
                selector_page_previous_first.show();
                selector_page_next_last.hide();
                break;
        }

        selector_labels_availables.find('tbody tr').hide().slice(offset, (offset + row)).show();
    });


    /*
     * LISTING OF THE DAY
     */
    $('#new_sesseion_name').keypress(function (e) {
        if (e.keyCode === 13)
            $('#new_sesseion_create').click();
    });
    $('#create_session').click(function () {
        err_warn_conf.hide();
        if (selector_session_creation.is(':visible')) {
            selector_session_creation.slideUp();
            selector_session_fusion.slideUp();
        } else {
            selector_session_creation.slideDown();
            selector_session_fusion.slideUp();
        }
    });
    $('#new_sesseion_create').click(function () {
        var name = $('#new_sesseion_name').val();
        var pAjax = {
            type: 'POST',
            url: $('#sne_createsession_url').val(),
            data_type: 'jsonp',
            data: {
                name: name,
                context_key: $('#sne_context_key').val()
            }
        };

        if ($(this).hasClass('button_disabled') || name === '')
            return;

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                err_warn_conf.hide();

                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (!data.success || data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html($('#err_create_false').val());
                    return;
                }

                $('.session_name').show().val(name);

                if (typeof(data.id_session) === 'undefined' || data.id_session === null) {
                    window.console && console.log(data);
                    $('#sne_warn_session').show().find('code').html($('#warn_id_session_not_exists').val());
                }
                else
                    id_session = data.id_session;

                $('.current_session_name').text(name + ' #' + data.id_session);

                selector_session_select.append('<option value="' + id_session + '" selected>' + name + '</option>');
                selector_session_select_fusion.append('<option value="' + id_session + '">' + name + '</option>');
                $('#delete_session').removeClass('button_disabled');
                selector_session_creation.slideUp();
                selector_label_session.find('tbody').html('');
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_session').show().find('code').html(data.responseText);
            }
        });
    });
    $('#use_session').click(function () {
        if (selector_session_selection.is(':visible')) {
            selector_session_creation.slideUp();
            selector_session_fusion.slideUp();
        }
        else {
            selector_session_selection.slideDown();
            selector_session_creation.slideUp();
            selector_session_fusion.slideUp();
        }
        $('#session_delete_success').hide();
    });
    selector_session_select.change(function () {
        id_session = $(this).val();
        var name = $(this).find('option:selected').text();
        var pAjax = {
            type: 'POST',
            url: $('#sne_usesession_url').val(),
            data_type: 'jsonp',
            data: {
                id_session: id_session,
                context_key: $('#sne_context_key').val()
            }
        };

        if (id_session === '0')
            return;

        $('#session_fusion').find('strong').text(selector_session_selection.find('option[value="' + id_session + '"]').text());

        err_warn_conf.hide();

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_warn_session').show().find('code').html(data.console);
                }

                if (typeof(data.alias) === 'undefined' || data.alias === null) {
                    window.console && console.log(data);
                    $('#sne_warn_session').show().find('code').html($('#warn_no_alias_received').val());
                }
                else
                    $('.session_name').show().val(data.alias);

                $('.current_session_name').text(name + ' #' + id_session);

                err_warn_conf.hide();
                count_nb_order = 0;
                selector_label_session.find('tbody').html(data.html).find('td a').each(function () {
                    $(this).attr('href', $(this).attr('href').replace('sne_token_order', $('#sne_token_order_url').val()));
                });
                $('#delete_session').removeClass('button_disabled');
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_session').show().find('code').html(data.responseText);
            }
        });
    });
    $('.session_name').donetyping(function () {
        var name = $(this).val();
        var pAjax = {
            type: 'POST',
            url: $('#sne_changesessionname_url').val(),
            data_type: 'jsonp',
            data: {
                id_session: id_session,
                name: name
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                err_warn_conf.hide();

                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_warn_session').show().find('code').html(data.output);
                }

                $('#sne_conf_session').show().find('code').html($('#conf_rename').val());
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_session').show().find('code').html(data.responseText);
            }
        });
    });
    $('#delete_session').click(function () {
        var pAjax = {
            type: 'POST',
            url: $('#sne_deletesession_url').val(),
            data_type: 'jsonp',
            data: {
                id_session: id_session
            }
        };

        if (!id_session || $(this).hasClass('button_disabled'))
            return;

        err_warn_conf.hide();

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html($('#err_delete_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_warn_session').show().find('code').html(data.console);
                }

                selector_label_session.find('tbody').html('');
                $('#session_delete_success').show();
                $('.session_name').hide();
                selector_session_select.find('option[value="' + id_session + '"]').remove();
                (selector_session_select.find('option').length > 1) ?
                    selector_session_selection.slideDown() : selector_session_creation.slideDown();
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_session').show().find('code').html(data.responseText);
            }
        });
    });
    $('#fusion_session').click(function () {
        selector_session_creation.slideUp();

        if (selector_session_fusion.is(':visible')) {
            selector_session_fusion.slideUp();
        }
        else {
            selector_session_fusion.slideDown();
            $('#session_fusion').find('strong').text(selector_session_selection.find('option[value="' + id_session + '"]').text());
        }
        selector_session_select_fusion.find('option').show();
        selector_session_select_fusion.find('option[value="' + id_session + '"]').hide();
    });
    selector_session_select_fusion.change(function () {
        var id_fusion = $(this).find('option:selected').val();
        var pAjax = {
            type: 'POST',
            url: $('#sne_fusionsession_url').val(),
            data_type: 'jsonp',
            data: {
                id_session: id_session,
                id_fusion: id_fusion
            }
        };

        if (id_session === '0' || id_fusion === '')
            return;

        err_warn_conf.hide();

        if (confirm($('#session_fusion_confirm').val())) {
            $.ajax({
                type: pAjax.type,
                url: pAjax.url,
                dataType: pAjax.data_type,
                data: pAjax.data,
                success: function (data) {
                    if (typeof(data) === 'undefined' || data === null) {
                        window.console && console.log(data);
                        $('#sne_error_session').show().find('code').html($('#err_ajax_no_data_received').val());
                        return;
                    }

                    if (data.console !== '') {
                        window.console && console.log(data);
                        $('#sne_warn_session').show().find('code').html(data.console);
                        return;
                    }

                    $('select option[value="' + id_fusion + '"]').remove();
                    $('#sne_conf_session').show().find('code').html($('#conf_fusion').val());
                },
                error: function (data) {
                    window.console && console.log(data);
                    $('#sne_error_session').show().find('code').html(data.responseText);
                }
            });
        }
    });
    $('#add_package').click(function () {
        var selector_listing_checkbox_checked = $('input[name^="listing_checkbox"]:checked');
        var checkbox = selector_listing_checkbox_checked.serialize();
        var nb_orders = selector_listing_checkbox_checked.length;
        var pAjax = {
            type: 'POST',
            url: $('#sne_updatesession_url').val(),
            data_type: 'jsonp',
            data: checkbox + '&id_session=' + id_session + '&action=add'
        };

        if (!id_session)
            return;

        err_warn_conf.hide();

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_listing2').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_listing2').show().find('code').html($('#err_update_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                if (typeof(data.html) === 'undefined' || data.html === null || data.html === '') {
                    window.console && console.log(data);
                    $('#sne_error_listing2').show().find('code').html($('#err_update_false').val() + ' -> $html' + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_error_listing2').show().find('code').html(data.console);
                }

                if (data.warning !== false)
                    $('#sne_warn_listing').show().find('code').html(data.warning);

                selector_label_session.find('tbody:first').append(data.html);
                $('input[name^="listing_checkbox"]:checked').closest('tr').remove();
                if (selector_labels_availables.find('tbody tr:visible').length < parseInt(selector_nb_display_first.val()))
                    selector_labels_availables.find('tbody tr').hide().slice(0, parseInt(selector_nb_display_first.val())).show();
                // Pagination update
                var total_orders = parseInt(selector_total_orders.first().text()) - nb_orders;
                selector_total_orders.text('').text(total_orders);
                selector_total_page.text('').text(Math.ceil(total_orders / parseInt(selector_nb_display_first.val())));
                $('#one_checkbox_to_rule_them_all_listing').attr('checked', false);
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_listing2').show().find('code').html(data.responseText);
            }
        });
    });
    $('#delete_package').click(function () {
        var selector_listing_session_checkbox_checked = $('input[name^="listing_session_checkbox"]:checked');
        var checkbox = selector_listing_session_checkbox_checked.serialize();
        var nb_orders = selector_listing_session_checkbox_checked.length;
        var pAjax = {
            type: 'POST',
            url: $('#sne_updatesession_url').val(),
            data_type: 'jsonp',
            data: checkbox + '&id_session=' + id_session + '&action=delete'
        };

        if (!id_session)
            return;

        err_warn_conf.hide();

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_ajax_no_data_received').val());
                    return false;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_update_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return false;
                }

                if (data.console !== '') {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html(data.output);
                }

                selector_listing_session_checkbox_checked.closest('tr').next('tr').remove();
                var packages = selector_listing_session_checkbox_checked.closest('tr');
                packages.find('td:last').remove();
                packages.find('input[type="text"]').each(function (ind, ele) {
                    $(ele).parent().text($(ele).val());
                });
                selector_labels_availables.prepend(selector_listing_session_checkbox_checked.attr('checked', false).attr('name', 'listing_checkbox[]').closest('tr'));
                if (!selector_label_session.find('tbody:first input[type="checkbox"]').length)
                    selector_label_session.find('tbody:first').html('');
                selector_labels_availables.find('tbody tr').hide().slice(0, parseInt(selector_nb_display_first.val() || 20)).show();
                // Pagination update
                var total_orders = parseInt(selector_total_orders.first().text()) + nb_orders;
                selector_total_orders.text(total_orders);
                selector_total_page.text('').text(Math.ceil(total_orders / parseInt(selector_nb_display_first.val())));
                $('#one_checkbox_to_rule_them_all_session').attr('checked', false);
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_listing').show().find('code').html(data.responseText);
            }
        });
    });


    /*
     * LISTING SESSION ORDERS
     */
    $('#generate_listing').click(function () {
        if (!selector_label_session.find('tbody tr').length)
            return;

        var selector_listing_session_checkbox_checked = $('input[name^="listing_session_checkbox"]:checked');

        if (!selector_listing_session_checkbox_checked.length) {
            $('#sne_warn_listing').show().find('code').html($('#warn_listing_no_selection').val());
            return;
        }

        err_warn_conf.hide();

        var checkbox = [];
        selector_listing_session_checkbox_checked.each(function () {
            checkbox.push($(this).val());
        });
        var pAjax = {
            type: 'POST',
            url: $('#sne_generatelisting_url').val(),
            data_type: 'jsonp',
            data: {
                checkbox: checkbox,
                id_session: id_session,
                printer_name: printer1,
                context_key: $('#sne_context_key').val()
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_update_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                if (data.console !== '' && data.console !== null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html(data.console);
                }

                // DOWNLOAD
                if (typeof(data.url) !== 'undefined' && data.url !== null && data.url.length) {
                    $('#PDF_to_print').attr('src', data.url).load(function () {
                        // this.focus();
                        this.contentWindow.print();
                    });
                }
                else {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html((data.console !== '' ? '<br>' + data.console : ''));
                }
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_listing').show().find('code').html(data.responseText);
            }
        });
    });


    $('#generate_delivery_slips, #generate_invoices').click(function () {
        if (!selector_label_session.find('tbody tr').length)
            return;

        var selector_listing_session_checkbox_checked = $('input[name^="listing_session_checkbox"]:checked');

        if (!selector_listing_session_checkbox_checked.length) {
            $('#sne_warn_listing').show().find('code').html($('#warn_listing_no_selection').val());
            return;
        }

        err_warn_conf.hide();

        var checkbox = [];
        selector_listing_session_checkbox_checked.each(function () {
            checkbox.push($(this).val());
        });
        var pAjax = {
            type: 'POST',
            url: $('#sne_generatedeliveryslips_url').val(),
            data_type: 'jsonp',
            data: {
                checkbox: checkbox,
                printer_name: printer1,
                template: $(this).attr('id') === 'generate_invoices' ? 'invoices' : 'deliveries',
                context_key: $('#sne_context_key').val()
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                window.console && console.log(data);

                if (typeof(data) === 'undefined' || data === null) {
                    $('#sne_error_listing').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_update_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                // DOWNLOAD
                if (typeof(data.url) !== 'undefined' && data.url !== null && data.url.length) {
                    $('#PDF_to_print').attr('src', data.url).load(function () {
                        var _this = this;

                        setTimeout(function () {
                            _this.contentWindow.print();
                        }, 800);
                    });
                }
                else {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html((data.console !== '' ? '<br>' + data.console : ''));
                }
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_listing').show().find('code').html(data.responseText);
            }
        });
    });


    /*
     * SORT
     */
    var sort_listing = false;
    $('thead tr th img').click(function () {
        var cellIndex = $(this).parent()[0].cellIndex;

        if ($(this).closest('table').attr('id') === 'sne_labels_session') {
            $(this).closest('table').find('tbody tr.listing_order:visible').sortElements(function (a, b) {
                var elem1 = $(a).find('td').get(cellIndex);
                var elem2 = $(b).find('td').get(cellIndex);

                return ($(elem1).text() > $(elem2).text() ?
                    sort_listing ? -1 : 1 :
                    sort_listing ? 1 : -1);
            });
            $('.product_list').each(function () {
                var id_order = $(this).attr('rel');
                $(this).insertAfter($('.listing_order').find('[rel="' + id_order + '"]'));
            });
        }
        else
            $(this).closest('table').find('tbody tr:visible').sortElements(function (a, b) {
                var elem1 = $(a).find('td').get(cellIndex);
                var elem2 = $(b).find('td').get(cellIndex);

                return ($(elem1).text() > $(elem2).text() ?
                    sort_listing ? -1 : 1 :
                    sort_listing ? 1 : -1);
            });
        sort_listing = !sort_listing;
    });


    /* TODAY */
    var try_once = false;
    $('#generate_slip').click(function () {

        $.post(
            "https://dev.labonnegraine.com/script_ferme_session.php",
            { id_session: $('#session_select').val() },
            function(data) {
                console.log(data);
            }
        );


        var orders = [];
        selector_table_exp_done.find('tr > td:first-child').each(function () {
            orders.push($(this).text());
        });

        if(orders){
            $.post(
                "https://dev.labonnegraine.com/script_update_order_state.php",
                { cloture: 1, orders: orders },
                function(data) {
                    console.log(data);
                }
            );
        }

        if (!orders.length && !try_once) {
            try_once = true;

            get_expedition_list();
            setTimeout(function () {
                $('#generate_slip').click();
            }, 1000);

            return false;
        }

        err_warn_conf.hide();

        var pAjax = {
            type: 'POST',
            url: $('#sne_generatetoday_url').val(),
            data_type: 'jsonp',
            data: {
                orders: orders,
                last_ta_d150: last_ta_d150,
                printer_name: printer1,
                context_key: $('#sne_context_key').val()
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html($('#err_update_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                if (data.console !== '' && data.console !== null) {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html(data.console);
                }

                // DOWNLOAD
                if (typeof(data.url) !== 'undefined' && data.url !== null && data.url.length) {
                    $('#PDF_to_print').attr('src', data.url).load(function () {
                        var _this = this;

                        setTimeout(function () {
                            _this.contentWindow.print();
                        }, 800);
                    });
                }
                else {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html((data.console !== '' ? '<br>' + data.console : ''));
                }
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_close').show().find('code').html(data.responseText);
            }
        });
    });

    // CN23
    $('#download_cn23').click(function () {
        var parcel_numbers = [];
        selector_table_exp_done.find('tr > td:nth-child(2)').each(function () {
            parcel_numbers.push($(this).text());
        });

        err_warn_conf.hide();

        var pAjax = {
            type: 'POST',
            url: $('#sne_downloadcn23_url').val(),
            data_type: 'jsonp',
            data: {
                parcel_numbers: parcel_numbers
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (typeof(data.result) === 'undefined' || data.result === null || data.result === false) {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html($('#err_update_false').val() + (data.console !== '' ? '<br>' + data.console : ''));
                    return;
                }

                if (data.console !== '' && data.console !== null) {
                    window.console && console.log(data);
                    $('#sne_error_close').show().find('code').html(data.console);
                }

                // DOWNLOAD
                if (typeof(data.urls) !== 'undefined' && data.urls !== null && data.urls.length) {
                    for (var idx in data.urls) {
                        if (data.urls.hasOwnProperty(idx)) {
                            var id_iframe = 'cn23_' + idx;

                            $('body').append('<iframe id="' + id_iframe + '" style="display:none;"></iframe>');
                            $('#' + id_iframe).attr('src', data.urls[idx]).load(function () {
                                this.contentWindow.print();
                            });
                        }
                    }

                    setTimeout(function () {
                        $('[id^="cn23_"]').remove();
                    }, 60000);
                }
                else {
                    window.console && console.log(data);
                    if (typeof(showNoticeMessage) === 'function')
                        showNoticeMessage($('#no_cn23_to_print').val());
                    else
                        $('#sne_info_close').show().text($('#no_cn23_to_print').val());
                }
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_close').show().find('code').html(data.responseText);
            }
        });
    });


    function modifyAddress(pAjax, field) {
        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                field.text(field.find('input').val());
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (data.console !== '' && data.console !== null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html(data.console);
                }
                field.blur().blink();
            },
            error: function (data) {
                field.text(field.find('input').val());
                window.console && console.log(data);
                $('#sne_error_listing').show().find('code').html(data.responseText);
                field.blur().blink();
            }
        });
    }

    function modifyWeight(pAjax, field) {
        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                field.text(field.find('input').val());
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (data.console !== '' && data.console !== null) {
                    window.console && console.log(data);
                    $('#sne_error_listing').show().find('code').html(data.console);
                }
                field.blur().blink();
            },
            error: function (data) {
                field.text(field.find('input').val());
                window.console && console.log(data);
                $('#sne_error_listing').show().find('code').html(data.responseText);
                field.blur().blink();
            }
        });
    }


    selector_label_session.delegate('tbody .address input, tbody .zipcode input, tbody .city input', 'change', function () {
        var field = $(this);
        var id_address = $(this).parent().attr('rel');

        field.keyup(function (e) {
            e.preventDefault();
            if (e.keyCode === 13) {
                field.unbind('blur');
                field.unbind('keyup');
                var pAjax = {
                    type: 'POST',
                    url: $('#sne_modifyaddress_url').val(),
                    data_type: 'jsonp',
                    data: {
                        id_address: id_address,
                        class: $(this).parent().attr('class'),
                        new_val: $(this).val()
                    }
                };

                modifyAddress(pAjax, field);
            }
        });
        field.blur(function () {
            field.unbind('blur');
            field.unbind('keyup');
            var pAjax = {
                type: 'POST',
                url: $('#sne_modifyaddress_url').val(),
                data_type: 'jsonp',
                data: {
                    id_address: id_address,
                    class: $(this).parent().attr('class'),
                    new_val: $(this).val()
                }
            };

            modifyAddress(pAjax, field);
        });
    });
    // WEIGHT
    selector_label_session.delegate('tbody .weight input', 'change', function () {
        var field = $(this);
        var id_order = $(this).parent().parent().attr('rel');

        field.keyup(function (e) {
            e.preventDefault();
            if (e.keyCode === 13) {
                field.unbind('blur');
                field.unbind('keyup');
                var pAjax = {
                    type: 'POST',
                    url: $('#sne_modifyweight_url').val(),
                    data_type: 'jsonp',
                    data: {
                        id_order: id_order,
                        class: $(this).parent().attr('class'),
                        new_val: $(this).val()
                    }
                };

                modifyWeight(pAjax, field);
            }
        });
        field.blur(function () {
            field.unbind('blur');
            field.unbind('keyup');
            var pAjax = {
                type: 'POST',
                url: $('#sne_modifyweight_url').val(),
                data_type: 'jsonp',
                data: {
                    id_order: id_order,
                    class: $(this).parent().attr('class'),
                    new_val: $(this).val()
                }
            };

            modifyWeight(pAjax, field);
        });

        /*
         * TA & D150
         */
        selector_table_orders.delegate('.ta img', 'click', function () {
            var assurance = prompt($('#prompt_ta').val());

            if (assurance !== null && !isNaN(parseFloat(assurance)) && isFinite(assurance)) {
                $(this).parent().find('span').text(assurance);
                $(this).parent().find('input').attr('checked', true).val(assurance);
            }
        });
    });


    /*
     * SET ORDER AS SENT WHEN PASSED IN EXPEDITION
     */
    function set_order_as_sent(parcel, isArray) {
        err_warn_conf.hide();

        var pAjax = {
            type: 'POST',
            url: $('#sne_setorderassent_url').val(),
            data_type: 'jsonp',
            data: isArray ? parcel : {
                parcel_number: parcel
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                if (typeof(data) === 'undefined' || data === null) {
                    window.console && console.log(data);
                    $('#sne_error_exp').show().find('code').html($('#err_ajax_no_data_received').val());
                    return;
                }

                if (data.console) {
                    window.console && console.log(data);
                    $('#sne_error_exp').show().find('code').html(data.console);
                }

                if (typeof(data.success) !== 'undefined' && data.success !== null && data.success) {
                    var img_tick_link = $('#img_tick').val();

                    if (isArray) {
                        selector_table_exp.find('tbody .listing_order td .sne_checkbox:checked').each(function () {
                            var current_tr = $(this).closest('tr');

                            current_tr.blink(true, function () {
                                var cmd_clone = current_tr.clone();

                                $(cmd_clone).find('td:first').remove();
                                $(cmd_clone).find('td:last').removeClass().addClass('center').html('<img src="' + img_tick_link + '" alt="tick">');
                                selector_table_exp_done.find('tbody').append(cmd_clone);
                            });
                        });

                    }
                    else
                        selector_table_exp.find('td:contains(' + parcel + ')').parent().blink(true, function () {
                            var cmd_clone = selector_table_exp.find('td:contains(' + parcel + ')').parent().clone();

                            $(cmd_clone).find('td:first').remove();
                            $(cmd_clone).find('td:last').removeClass().addClass('center').html('<img src="' + img_tick_link + '" alt="tick">');
                            selector_table_exp_done.find('tbody').append(cmd_clone);
                        });
                }
                else
                    $('#sne_error_exp').show().find('code').html($('#err_exp_false').val());
            },
            error: function (data) {
                window.console && console.log(data);
                $('#sne_error_exp').show().find('code').html(data.responseText);
            }
        });
    }


    $('form[name="order_send"]').submit(function (ev) {
        ev.preventDefault();

        var parcel_number = $(this).find('input').val();

        $(this).find('input').val('');
        set_order_as_sent(parcel_number);
    });
    selector_table_exp.delegate('.send_package div', 'click', function () {
        var parcel_number = $(this).closest('tr').find('td:eq(2)').text();
        set_order_as_sent(parcel_number);
    });

    /*
     * Load last session used
     */
    (last_session_used.length) ?
        selector_session_select.find('option[value="' + last_session_used + '"]').attr('selected', true).change() :
        selector_session_select.find('option:last').attr('selected', true).change();

    /*
     * Click on edit button, show/hide editable fields
     */
    selector_label_session.delegate('.edit', 'click', function () {
        var inputs = selector_label_session.find('tbody .address input, tbody .zipcode input, tbody .city input, tbody .weight input');

        if (inputs.hasClass('edit_on'))
            inputs.removeClass('edit_on');
        else
            inputs.addClass('edit_on');
    });


    /*
     * Change to an other sonice etiquette service (PDF/ZPL)
     */
    $('.etq_selector').change(function () {
        window.location.href = window.location.href.replace('#', '') + '&sne_switcher=' + $(this).val();
    });


    /*
     * Set selected packages as "sent"
     */
    $('#send_all_exp').click(function () {
        var parcel_number_list = selector_table_exp.find('tbody .listing_order td .sne_checkbox:checked').serialize();
        set_order_as_sent(parcel_number_list, true);
    });


    /*
     * Change order's carrier
     */
    $('#tab-orders').delegate('.can_modify img', 'click', function () {
        var old_id_carrier = $(this).parent().attr('rel');
        var selector = $('#select_carrier_modify').clone().removeAttr('id').show();

        $(this).hide();
        $(this).parent().find('span').html(selector);
        $(this).parent().find('option[value="' + old_id_carrier + '"]').attr('selected', true);
        $(this).parent().find('select').change(function () {
            var id_order = $(this).closest('tr').attr('rel');
            var id_carrier = $(this).val();

            change_order_id_carrier(id_order, id_carrier);
        });
    });


    function change_order_id_carrier(id_order, id_carrier) {
        err_warn_conf.hide();

        var pAjax = {
            type: 'POST',
            url: $('#sne_changeordercarrier_url').val(),
            data_type: 'jsonp',
            data: {
                id_order: id_order,
                id_carrier: id_carrier
            }
        };

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            success: function (data) {
                window.console && console.log(data);

                if (typeof(data) === 'undefined' || data === null) {
                    selector_sne_error_orders.show();
                    selector_sne_error_orders.find('code').html('Data undefined or null.');
                }

                if (data.console !== '') {
                    selector_sne_error_orders.show().find('code').html(data.console);
                    return;
                }

                var carrier_name = selector_table_orders.find('tbody tr[rel="' + id_order + '"]').find('select option:selected').text();
                selector_table_orders.find(' tbody tr[rel="' + id_order + '"] .can_modify span').html(carrier_name);
                selector_table_orders.find('tbody tr[rel="' + id_order + '"] .can_modify img').show();
            },
            error: function (data) {
                window.console && console.log(data);

                selector_sne_error_orders.show();
                selector_sne_error_orders.find('code').html(data.responseText);
            }
        });
    }

    $('li[id^="mode-"]').click(function () {
        var elements = 'li[id^="mode-"], img[alt="down"], img[alt="up"], .product_list, #table_orders > thead > tr:nth-child(1) > th:nth-child(5), #table_orders > thead > tr.nodrag.nodrop.filter.active > td:nth-child(5), #table_orders .address';
        $(elements).toggle();
    });

    if ($('#sne_compact_mode').val() == '1')  {
        setTimeout(function() {
            $('#mode-compact').click();
        }, 1000);
    }

});
