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

    Date.prototype.psformat = function () {

        var yyyy = this.getFullYear().toString();
        var mm = (this.getMonth() + 1).toString();
        var dd = this.getDate().toString();
        var hh = this.getHours().toString();
        var ii = this.getMinutes().toString();
        var ss = this.getSeconds().toString();

        return (yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]) + ' ' + hh + ':' + ii + ':' + ss);
    };


    $('#sne_create_label').click(function () {

        $.post(
            "https://dev.labonnegraine.com/script_add_order_sonice_etq.php",
            { id_order: $('input[name=id_order]').val() },
            function(data) {
                console.log(data);
            }
        );

        var id_order = parseInt($('input[name^="checkbox"]').val());
        var pAjax = new Object();
        var click_next = true;
        $('#sne_loader').show();

        pAjax.type = 'POST';
        pAjax.url = $('#sne_webservice_url').val();
        pAjax.data_type = 'jsonp';
        pAjax.data = $('#sonice_etiquetage input[name^="checkbox"]:first').serialize() + '&' + $('input[name^="data"]:checked').serialize();

        $.ajax({
            type: pAjax.type,
            url: pAjax.url,
            dataType: pAjax.data_type,
            data: pAjax.data,
            error: function (data) {
                window.console && console.log(data);
                $('#sne_loader').hide();
                plug.alert('No data returned...<br><br>' + data.responseText);
            },
            success: function (data) {
                window.console && console.log(data);
                $('#sne_loader').hide();

                if (typeof(data) === 'undefined' || data === null) {
                    click_next = false;
                    plug.alert('No data returned...<br><br>' + data.console);
                    return false;
                }

                if (typeof(data.pdfs) === 'undefined' || data.pdfs === null || typeof(data.pdfs) !== 'object' || data.pdfs.length === 0 || !data.pdfs[id_order].PdfUrl) {
                    plug.alert('No label returned...<br><br>' + data.console);
                    return false;
                }

                if (data.console !== '' && data.console !== null) {
                    plug.alert(data.console);
                }

                $('.font-red, #sne_create_label').hide();

                if ($('#sonice_etiquetage .panel').length) {
                    // PS 1.6
                    var info_div = $('#sonice_etiquetage .panel .col-lg-6:last');
                    var today = new Date();

                    info_div.prepend('<strong>' + data.pdfs[id_order].parcelNumber + '</strong><br><strong>Date :</strong> ' + today.psformat() + '<br>');
                    $('#sne_print_label').attr('rel', data.pdfs[id_order].PdfUrl).show();
                    if (data.pdfs[id_order].cn23) {
                        $('#sne_print_cn23').attr('rel', data.pdfs[id_order].cn23).show();
                    }
                } else {
                    var info_div = $('#sne_show_label');
                    var today = new Date();

                    info_div.show();
                    info_div.find('.sne_label_num').text(data.pdfs[id_order].parcelNumber);
                    info_div.find('a').attr('href', data.pdfs[id_order].PdfUrl);
                    info_div.find('.sne_label_date').text(today.psformat());
                }
                if(click_next)
                {
                    $('#sne_print_cn23').trigger('click');
                    $('#sne_print_label').click();
                }
            }
        });
    });


    $('#sne_print_label').click(function () {
        if ($('#sne_print_type').val() == 'PDF') {
            $('#sne_to_print').length && $('#sne_to_print').remove();

            $('body').append('<iframe id="sne_to_print" style="display:none;"></iframe>');
            $('#sne_to_print').attr('src', $('#sne_print_label').attr('rel'));
            $('#sne_to_print').load(function () {
                this.contentWindow.print();
            });
        } else {
            if (typeof(CommonPrintServer) == 'object' && typeof(CommonPrintServer.getPrinters) == 'function') {
                CommonPrintServer.setPrinter($('#sne_printer_name').val(), function () {
                    CommonPrintServer.printFileByURL($('#sne_print_label').attr('rel'), function (err) {
                        window.console && console.log(err);
                        if (typeof(err) == 'undefined' || err == null || typeof(err.statusText) !== 'undefined') {
                            alert($('#sne_printme_not_reached').val());
                        }
                    });
                });
            } else if (typeof(qz) == 'object' && typeof(qz.findPrinters) == 'function') {
                qz.findPrinter($('#sne_printer_name').val());
                qz.appendFile($('#sne_print_label').attr('rel'));
                console.log($('#sne_print_label').attr('rel'));
                qz.print();
            } else {
                alert('Utilitaire d\'impression non détecté, impossible de tester l\'impression.\nEssayez de rafraîchir la page.')
            }
        }
        $('#btn_delivery_next').click();
    });

    $('#sne_print_cn23').click(function () {
        $('#sne_to_print').length && $('#sne_to_print').remove();

        /*$('body').append('<iframe id="sne_to_print" style="display:none;"></iframe>');
        $('#sne_to_print').attr('src', $('#sne_print_cn23').attr('rel'));
        $('#sne_to_print').load(function () {
            this.contentWindow.print();
        });*/

        var url_cn23 = $('#sne_print_cn23').attr('rel');
        var url_print = $('#sne_print_label').attr('rel');
        var url_liv = $('#btn_delivery_next').attr('onclick');

        if (url_cn23.length > 0)
        {
            //url_cn23
            window.open(url_cn23, '_blank');
            window.open(url_print);
            $('#btn_delivery_next').trigger('click');
        }

    });

});
