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

$(document).ready(function () {
    if (typeof autostartPostage !== 'undefined' && autostartPostage === 1) {
        startLabelProcess();
    }

    $('#submit-process-colissimo-configuration').off('click').on('click', function (e) {
        e.preventDefault();
        startLabelProcess();
    });
    
    $('#submit-process-colissimo-customs-documents').off('click').on('click', function (e) {
        e.preventDefault();
        startAffranchissementCustomsDocumentsProcess();
    });

    $(document).on('click', '#colissimo-affranchissement-configuration .colissimo-error-modal', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#' + id).modal('show');
    });
    
    $(document).on('click', '#colissimo-affranchissement-customs-documents .colissimo-error-modal', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#' + id).modal('show');
    });

    $(document).on('click', '#form-colissimo_order td.col-reference-plus', function () {
        var idColissimoOrder = parseInt($(this).closest('td').prev('td').find('input').attr('value'));
        var step =1; 
        
        $(this).expandOrderDetails(idColissimoOrder, $('table.colissimo_order'), step);
    });

    $(document).on('click', '#form-colissimo_order td.col-reference-minus', function () {
        var idColissimoOrder = parseInt($(this).closest('td').prev('td').find('input').attr('value'));

        $(this).collapseOrderDetails(idColissimoOrder);
    });

    $(document).on('click', '#colissimo-affranchissement-configuration td.col-reference-plus', function () {
        var colissimoOrderInput = $(this).closest('td').prev('td').find('input').attr('name');
        var idColissimoOrder = parseInt(colissimoOrderInput.substr(16));
        var step = 2;

        if ($(this).collapseDeliveryAddress(idColissimoOrder)) {
            $(this).find('i').removeClass('icon-plus-circle').addClass('icon-minus-circle');
        }
        $(this).expandOrderDetails(idColissimoOrder, $('.colissimo-configuration-table'), step);
    });

    $(document).on('click', '#colissimo-affranchissement-configuration td.col-reference-minus', function () {
        var colissimoOrderInput = $(this).closest('td').prev('td').find('input').attr('name');
        var idColissimoOrder = parseInt(colissimoOrderInput.substr(16));

        $(this).collapseOrderDetails(idColissimoOrder);
    });
    
    $(document).on('click', '#colissimo-affranchissement-customs-documents td.col-reference-plus', function () {
        var colissimoOrderInput = $(this).closest('td').prev('td').find('input').attr('name');
        var idColissimoOrder = parseInt(colissimoOrderInput.substr(16));
        var step = 4;
        $(this).find('i').removeClass('icon-plus-circle').addClass('icon-minus-circle');
        $(this).expandOrderDetails(idColissimoOrder, $('.colissimo-customs-documents-table'), step);
    });
    
    $(document).on('click', '#colissimo-affranchissement-customs-documents td.col-reference-minus', function () {
        var colissimoOrderInput = $(this).closest('td').prev('td').find('input').attr('name');
        var idColissimoOrder = parseInt(colissimoOrderInput.substr(16));
        $(this).collapseOrderDetails(idColissimoOrder);
    });

    $(document).on('click', '#colissimo-affranchissement-configuration .icon-pencil', function () {
        var colissimoOrderInput = $(this).closest('td').prev('td').prev('td').prev('td').find('input').attr('name');
        var idColissimoOrder = parseInt(colissimoOrderInput.substr(16));
        
        $(this).closest('td').expandDeliveryAddress(idColissimoOrder, $('.colissimo-configuration-table'));
    });

    $(document).on('click', '#colissimo-affranchissement-configuration .btn-close', function (e) {
        e.preventDefault();
        var td = $(this).closest('tr').prev('tr').find('td.colissimo-delivery-addr');
        var idColissimoOrder = $(this).attr('data-id');

        td.collapseDeliveryAddress(idColissimoOrder);
    });

    $(document).on('click', '#colissimo-purge-documents', function (e) {
        e.preventDefault();

        $(this).toggleClass('disabled');
        $(this).find('i').toggleClass('icon-trash icon-spin icon-spinner');
        purgeDocuments($(this));
    });

    $(document).on('click', '.btn-download', function (e) {
        e.preventDefault();

        $('#colissimo-form-documents').submit();
    });

    $(document).on('click', '.btn-print', function (e) {
        e.preventDefault();

        var form = $('#colissimo-form-documents');

        $(this).toggleClass('disabled');
        $(this).find('i').toggleClass('icon-print icon-spin icon-spinner');
        printAllDocuments(form, $(this));
    });

    $(document).on('click', '.btn-print-thermal', function (e) {
        e.preventDefault();

        var form = $('#colissimo-form-documents');

        $(this).toggleClass('disabled');
        $(this).find('i').toggleClass('icon-print icon-spin icon-spinner');
        printAllThermalDocuments(form, $(this));
    });
});

function checkQuantity(element, quantity) {
    var qteToShip =  $('input[name= '+element+']').val();
    if (qteToShip  > quantity){
        $('input[name= '+element+']').val(quantity);
    }else{
        $('input[name= '+element+']').val(qteToShip);
    }
    var form = $('#colissimo-affranchissement-configuration').serialize();    
    updateTotalweight(form, element);
}

function updateDDPInfo(element) {
    $('input[name= '+element+']').val($('input[name= '+element+']').val());
}
