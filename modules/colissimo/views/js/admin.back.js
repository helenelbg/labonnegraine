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

$.fn.columnCnt = function () {
    return $(this).find('> tbody > tr:first-child td').length;
};

$.fn.expandOrderDetails = function (idColissimoOrder, tableId, step) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    if ($(this).collapseDeliveryAddress(idColissimoOrder)) {
        $(this).removeClass('col-reference-plus').addClass('col-reference-minus');
        $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: token,
                action: 'orderDetails',
                id_colissimo_order: idColissimoOrder,
                nb_col: nbCol,
                step : step
            }
        }).fail(function (jqXHR, textStatus) {
            tr.find('td.col-reference-minus').removeClass('col-reference-minus').addClass('col-reference-plus');
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            $(data.html).insertAfter(tr);
            tr.toggleClass('selected');
        }).always(function (data) {
            $('.colissimo-pending-' + idColissimoOrder).remove();
        });
    }
};

$.fn.expandDashboardOrderDetails = function (idColissimoOrder, tableId) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    $(this).removeClass('col-reference-plus').addClass('col-reference-minus');
    $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoDashboard',
            ajax: 1,
            token: token,
            action: 'orderDetails',
            id_colissimo_order: idColissimoOrder,
            nb_col: nbCol
        }
    }).fail(function (jqXHR, textStatus) {
        tr.find('td.col-reference-minus').removeClass('col-reference-minus').addClass('col-reference-plus');
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        $(data.html).insertAfter(tr);
        tr.toggleClass('selected');
    }).always(function (data) {
        $('.colissimo-pending-' + idColissimoOrder).remove();
    });
};

$.fn.expandCustomsDocumentsOrderDetails = function (idColissimoOrder, tableId) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    $(this).removeClass('col-reference-plus').addClass('col-reference-minus');
    $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoCustomsDocuments',
            ajax: 1,
            token: token,
            action: 'orderDetails',
            id_colissimo_order: idColissimoOrder,
            nb_col: nbCol
        }
    }).fail(function (jqXHR, textStatus) {
        tr.find('td.col-reference-minus').removeClass('col-reference-minus').addClass('col-reference-plus');
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        $(data.html).insertAfter(tr);
        tr.toggleClass('selected');
    }).always(function (data) {
        $('.colissimo-pending-' + idColissimoOrder).remove();
    });
};

$.fn.expandDeliveryAddress = function (idColissimoOrder, tableId) {
    var tr = $(this).closest('tr');
    var nbCol = tableId.columnCnt();

    if ($(this).prev('td').collapseOrderDetails(idColissimoOrder) && $(this).prev('td').collapseDeliveryAddress(idColissimoOrder)) {
        $('<tr class="colissimo-pending colissimo-pending-' + idColissimoOrder + '"><td class="text-center" colspan="' + nbCol + '"><img src="' + loaderPath + '"</td></tr>').insertAfter(tr);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: token,
                action: 'addressDetails',
                id_colissimo_order: idColissimoOrder,
                nb_col: nbCol,
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            $(data.html).insertAfter(tr);
        }).always(function (data) {
            $('.colissimo-pending-' + idColissimoOrder).remove();
            tr.toggleClass('selected');
        });
    }
};

$.fn.collapseOrderDetails = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var orderDetails = $('.colissimo-order-detail-' + idColissimoOrder);

    if (nbPending === 0) {
        if (orderDetails.length) {
            orderDetails.remove();
            $(this).removeClass('col-reference-minus').addClass('col-reference-plus');
            $(this).find('i').removeClass('icon-minus-circle').addClass('icon-plus-circle');
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

$.fn.collapseDashboardOrderDetails = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var orderDetails = $('.colissimo-order-detail-' + idColissimoOrder);

    if (nbPending === 0) {
        if (orderDetails.length) {
            orderDetails.remove();
            $(this).removeClass('col-reference-minus').addClass('col-reference-plus');
            $(this).find('i').removeClass('icon-minus-circle').addClass('icon-plus-circle');
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

$.fn.collapseCustomsDocumentsOrderDetails = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var orderDetails = $('.colissimo-order-detail-' + idColissimoOrder);
    if (nbPending === 0) {
        if (orderDetails.length) {
            orderDetails.remove();
            $(this).removeClass('col-reference-minus').addClass('col-reference-plus');
            $(this).find('i').removeClass('icon-minus-circle').addClass('icon-plus-circle');
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

$.fn.collapseDeliveryAddress = function (idColissimoOrder) {
    var nbPending = $('.colissimo-pending-' + idColissimoOrder).length;
    var tr = $(this).closest('tr');
    var addressDetails = $('.colissimo-address-detail-' + idColissimoOrder);

    if (nbPending === 0) {
        if (addressDetails.length) {
            addressDetails.remove();
            tr.toggleClass('selected');
        }

        return 1;
    } else {
        return 0;
    }
};

function removeDupplicates(val, index, self) {
    return self.indexOf(val) === index;
}

function processColissimoOrders(ordersList, success, ordersCount, form) {
    var percent = ((ordersCount - ordersList.length + 1) / ordersCount) * 100;
    var idColissimoOrder = parseInt(ordersList.pop());

    if (idColissimoOrder) {
        var data = {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: token,
            action: 'generateLabel',
            id_colissimo_order: idColissimoOrder
        };
        $('.colissimo-order-result-' + idColissimoOrder).html('<img src="' + loaderPath + '" />');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php?' + $.param(data),
            data: form
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            if (data.id_label) {
                success.push(data.id_label);
            }
            if (data.id_return_label) {
                success.push(data.id_return_label);
            }
            $('.js-step-line').css({width: percent + '%'});
            $('.colissimo-order-result-' + idColissimoOrder).html(data.result_html);
            $('input[name= colissimo_weight_'+ idColissimoOrder+']').val(data.order_weight);
            for (var i = 0; i < data.products.length; i++) {
                $('input[name= colissimo_orderBox_'+ idColissimoOrder + '_' + data.products[i].product_id + '_'+ data.products[i].product_attribute_id + ']').val(data.products[i].product_quantity);
            }
            if(data.show_customs_documents === true){
                $('.step-documents').css({'display':'inline'});
                $('#submit-edit-colissimo-customs-documents').css({'display':'block', 'margin-left':'5px'});
            }
            processColissimoOrders(ordersList, success, ordersCount, form);
        });
    } else {
        $('html, body').animate({scrollTop: 0}, 'slow');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoAffranchissement',
                ajax: 1,
                token: token,
                action: 'displayResult',
                label_ids: JSON.stringify(success)
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            if ($('#colissimo-form-documents').length) {
                var ids = $('input[name=colissimo_label_ids]').val();
                var jsonIds = JSON.parse(ids);
                console.debug(jsonIds);
                jsonIds = jsonIds.concat(JSON.parse(data.labels_ids));
                var jsonIdsUnique = jsonIds.filter(removeDupplicates);
                console.debug(jsonIds);
                console.debug(JSON.stringify(jsonIdsUnique));
                $('input[name=colissimo_label_ids]').val(JSON.stringify(jsonIdsUnique));
            } else {
                $('#colissimo-process-result').html(data.result_html);
            }
        }).always(function (data) {
            $('#colissimo-process').hide(200);
            $('#submit-process-colissimo-configuration').toggleClass('disabled');
        });
        $('.step.current').removeClass('current').addClass('complete');
        $('.step.incomplete').removeClass('incomplete').addClass('current');
        $('.step-documents .step.current').removeClass('current').addClass('incomplete');
    }
}

function processAffranchissementCustomsDocuments(labelsList, labelsCount, formData) {
    var idlabel = parseInt(labelsList.pop());
    if (idlabel) {
        var data = {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: token,
            action: 'sendCustomsDocuments',
            id_label: idlabel
           };
           $.ajax({
               type: 'POST',
               dataType: 'json',
               url: baseAdminDir + 'index.php?' + $.param(data), 
               contentType: false,
               processData: false,
               data: formData
           }).fail(function (jqXHR, textStatus) {
              showErrorMessage(genericErrorMessage);
           }).done(function (data) {
                $('.colissimo-custom-document-result-' + idlabel).html(data.result_html);
                processAffranchissementCustomsDocuments(labelsList, labelsCount, formData);
           });
       
    } else {
        $('html, body').animate({scrollTop: 0}, 'slow');
        $('#colissimo-process-result').css({'display':'block'});
        $('#colissimo-process').hide(200);
    }
}

function processColissimoDepositSlip(labelList, success) {
    var labels = labelList.pop();

    if (labels) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoDepositSlip',
                ajax: 1,
                token: token,
                action: 'generateDepositSlip',
                label_ids: JSON.stringify(labels)
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            if (data.error) {
                $('#deposit-slip-result').html(data.result_html).show();
                $('#submit-process-colissimo-deposit-slip').toggleClass('disabled');
                return;
            } else {
                success.push(data.depositSlipId);
            }
            processColissimoDepositSlip(labelList, success);
        });
    } else {
        displayDepositSlipResult(success);
    }
}

function displayDepositSlipResult(success) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoDepositSlip',
            ajax: 1,
            token: token,
            action: 'displayResult',
            deposit_slip_ids: JSON.stringify(success)
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            $('#deposit-slip-result').html(data.result_html).show();
            tableOlderParcels.ajax.reload(function () {
                var count1 = tableParcelsOfToday.rows({selected: true}).count();
                var count2 = tableOlderParcels.rows({selected: true}).count();

                $('.js-colissimo-count-selected').text(count1 + count2);
            }, false);
            tableParcelsOfToday.ajax.reload(function () {
                var count1 = tableParcelsOfToday.rows({selected: true}).count();
                var count2 = tableOlderParcels.rows({selected: true}).count();

                $('.js-colissimo-count-selected').text(count1 + count2);
            }, false);
            showSuccessMessage(successMessage);
        }
    }).always(function (data) {
        $('#submit-process-colissimo-deposit-slip').toggleClass('disabled');
    });
}

function startLabelProcess() {
    var ordersList = [];
    var success = [];
    var form = $('#colissimo-affranchissement-configuration').serialize();

    $('.colissimo-configuration-table input.colissimo-order-selection[type=checkbox]').each(function () {
        var idColissimoOrder = parseInt(this.name.substr(16, this.name.length - 16)) || 0;

        if (this.checked) {
            $('.colissimo-order-result-' + idColissimoOrder).html('<i>' + queueingText + '</i>');
            ordersList.push(idColissimoOrder);
        }
        $('tr.selected td.colissimo-delivery-addr').collapseDeliveryAddress(idColissimoOrder);
        $('td.col-reference-minus').collapseOrderDetails(idColissimoOrder);
    });
    $('.js-step-line').css({width: '0%'});
    $('.js-step-line-end').css({width: '0%'});
    if ($('.step.complete').length === 2) {
        $('.step.current').removeClass('current').addClass('incomplete');
        $('.step-config').removeClass('complete').addClass('current');
    }
    ordersList.reverse();
    if (ordersList.length !== 0) {
        $('#submit-process-colissimo-configuration').toggleClass('disabled');
        $('html, body').animate({scrollTop: 0}, 'slow');
        $('#colissimo-process').show(200);
        processColissimoOrders(ordersList, success, ordersList.length, form);
    } else {
        showErrorMessage(noOrdersText);
    }
}

function startAffranchissementCustomsDocumentsProcess(){
    var formData = new FormData($("#colissimo-affranchissement-customs-documents").get(0));
    var labelsList = [];
    $('.colissimo-customs-documents-table input.colissimo-order-selection[type=checkbox]').each(function () {
        var idColissimoOrder = parseInt(this.name.substr(16, this.name.length - 16)) || 0;
        if (this.checked) {
            var label = $('.label_'+idColissimoOrder);
            if (label.length) {
                labelsList.push(label.val());
            }
        }
        $('td.col-reference-minus').collapseOrderDetails(idColissimoOrder);
    });
    if (labelsList.length !== 0) {
        $('html, body').animate({scrollTop: 0}, 'slow');
        $('#colissimo-process').show(200);
        processAffranchissementCustomsDocuments(labelsList, labelsList.length, formData);
    } else {
        showErrorMessage(noOrdersText);
    }
}

function submitAddress(form) {
    var data = {
        controller: 'AdminColissimoAffranchissement',
        ajax: 1,
        token: token,
        action: 'addressSave'
    };

    form.find('button > i').removeClass('process-icon-save').addClass('process-icon-loading');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: form.serialize()
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(addressErrorMessage);
    }).done(function (data) {
        var td = form.closest('tr').prev('tr').find('td.colissimo-delivery-addr');

        if (!data.error) {
            showSuccessMessage(data.message);
            td.html(data.html);
        } else {
            showErrorMessage(data.message);
        }
    }).always(function (data) {
        var idColissimoOrder = form.closest('.sub-tr').find('.btn-close').attr('data-id');
        var td = form.closest('tr').prev('tr').find('td.colissimo-delivery-addr');

        form.find('button > i').removeClass('process-icon-loading').addClass('process-icon-save');
        td.collapseDeliveryAddress(idColissimoOrder);
    });
}

function findLabel(tableParcelsOfToday, tableOlderParcels) {
    var parcelNumberInput = $('#colissimo-parcel-number-barcode');

    tableOlderParcels.rows(function (idx, data, node) {
        if (data.shipping_number === parcelNumberInput.val()) {
            var row = tableOlderParcels.row(idx);
            var rowNode = row.node();

            if ($(rowNode).hasClass('selected')) {
                row.deselect();
            } else {
                row.select();
            }
            showSuccessMessage(label_selection_text);
            parcelNumberInput.val('');
            parcelNumberInput.typeWatch({
                captureLength: 12,
                highlight: true,
                wait: 50,
                callback: function () {
                    findLabel(tableParcelsOfToday, tableOlderParcels);
                }
            });
            return;
        }

        return false;
    });
    tableParcelsOfToday.rows(function (idx, data, node) {
        if (data.shipping_number === parcelNumberInput.val()) {
            var row = tableParcelsOfToday.row(idx);
            var rowNode = row.node();

            if ($(rowNode).hasClass('selected')) {
                row.deselect();
            } else {
                row.select();
            }
            showSuccessMessage(label_selection_text);
            parcelNumberInput.val('');
            parcelNumberInput.typeWatch({
                captureLength: 12,
                highlight: true,
                wait: 50,
                callback: function () {
                    findLabel(tableParcelsOfToday, tableOlderParcels);
                }
            });

            return;
        }

        return false;
    });
}

function purgeDocuments(btn) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: token,
            action: 'purgeDocuments'
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            showSuccessMessage(data.message);
            $('.colissimo-docs-alert').remove();
        }
    }).always(function (data) {
        btn.find('i').toggleClass('icon-trash icon-spin icon-spinner');
        btn.toggleClass('disabled');
    });
}

function loadColissimoServiceModalUpdate(idOrder) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoAffranchissement',
            ajax: 1,
            token: tokenAffranchissement,
            id_order: parseInt(idOrder),
            action: 'loadServiceUpdateModal'
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            $(data.result_html).appendTo('body');
            $('.colissimo-back-widget').modal('show');
        }
    }).always(function (data) {
        $('.colissimo-service-selection').removeClass('disabled');
        $('.colissimo-service-selection').find('i').removeClass('icon-spin icon-spinner').addClass('icon-refresh');
    });
}

function updateTotalweight(form, element)
{
    var idColissimiOrder = element.split('_')['2'];
    var data = {
        controller: 'AdminColissimoAffranchissement',
        ajax: 1,
        token: token,
        action: 'updateTotalweight',
        id_colissimo_order: idColissimiOrder 
    };
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: form
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        $('input[name= colissimo_weight_'+idColissimiOrder+']').val(data.weight);
    });
}

function printAllDocuments(form, btn) {
    var data = {
        controller: 'AdminColissimoLabel',
        ajax: 1,
        token: tokenLabel,
        action: 'printDocuments',
    };

    var base64string;

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: form.serialize(),
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-print icon-spin icon-spinner');
    }).done(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            base64string = data.file_string;
            printJS({
                printable: base64string,
                type: 'pdf',
                base64: true,
                showModal: true,
                fallbackPrintable: `data:application/pdf;base64,${data.file_string}`,
                onBrowserIncompatible: function () {
                    var data = base64string;
                    var fileName = 'Colissimo_Documents.pdf';

                    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                        var byteCharacters = atob(data);
                        var byteNumbers = new Array(byteCharacters.length);
                        for (var i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        var byteArray = new Uint8Array(byteNumbers);
                        var blob = new Blob([byteArray], {type: 'application/pdf'});

                        window.navigator.msSaveOrOpenBlob(blob, fileName);
                    } else {
                        window.open('data:application/pdf;base64, ' + data, '');
                    }
                }
            });
        }
    }).always(function (data) {
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-print icon-spin icon-spinner');
    });
}

function printAllThermalDocuments(form, btn) {
    var data = {
        controller: 'AdminColissimoLabel',
        ajax: 1,
        token: tokenLabel,
        action: 'printThermalLabels',
    };

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php?' + $.param(data),
        data: form.serialize(),
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-print icon-spin icon-spinner');
    }).done(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            for (var i = 0; i < data.request_urls.length; i++) {
                $.get(data.request_urls[i]);
            }
            showSuccessMessage(data.message);
        }
    }).always(function (data) {
        btn.toggleClass('disabled');
        btn.find('i').toggleClass('icon-print icon-spin icon-spinner');
    });
}

function printThermal(base64) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminColissimoLabel',
            ajax: 1,
            token: tokenLabel,
            base64: base64,
            action: 'printLabelThermal'
        }
    }).fail(function (jqXHR, textStatus) {
        showErrorMessage(genericErrorMessage);
    }).done(function (data) {
        if (data.error) {
            showErrorMessage(data.message);
        } else {
            var successMessage = data.message;
            $.get(data.request_url, function(data, textStatus, jqXHR) {
                if (jqXHR.status === 200) {
                    showSuccessMessage(successMessage);
                } else {
                    showErrorMessage(genericErrorMessage);
                }
            }).fail(function(data, textStatus, jqXHR) {
                showErrorMessage(genericErrorMessage);
            });
        }
    });
}

$(document).ready(function () {
    $('.colissimo-signature').off('click').on('click', function (e) {
        e.preventDefault();
        var ordersList = [];
        var colissimoService = $(this).attr('data-colissimo-is-signature');
        $.each($("input[name='orderBox[]']:checked"), function() {
            ordersList.push($(this).val());
        });
        if (ordersList.length !== 0) {
            for (var i = 0; i < ordersList.length; i++) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: baseAdminDir + 'index.php',
                    data: {
                        controller: 'AdminModules',
                        ajax: 1,
                        configure: 'colissimo',
                        token: token,
                        action: 'ordersMassAssignment',
                        id_order: ordersList[i],
                        service: colissimoService,
                    }
                }).fail(function (jqXHR, textStatus) {
                    showErrorMessage(genericErrorMessage);
                }).done(function (data) {
                    if (data.error) {
                        showErrorMessage(data.message);
                    } else {
                        showSuccessMessage(data.message);
                    }
                });
            }
        }else{
            showErrorMessage(noOrdersText);
        }
    });
});  
