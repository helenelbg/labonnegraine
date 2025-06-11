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

    $(document).on('click', '#form-colissimo-customs-documents td.col-reference-plus', function () {
        var idColissimoOrder = parseInt($(this).closest('td').prev('td').find('input').attr('value'));
        $(this).find('i').removeClass('icon-plus-circle').addClass('icon-minus-circle');
        $(this).expandCustomsDocumentsOrderDetails(idColissimoOrder, $('.colissimo-customs-documents-table'));
    });
    
    $(document).on('click', '#form-colissimo-customs-documents td.col-reference-minus', function () {
        var idColissimoOrder = parseInt($(this).closest('td').prev('td').find('input').attr('value'));
        $(this).collapseCustomsDocumentsOrderDetails(idColissimoOrder);
    });
    
    $('a.colissimo-delete-invoice').off('click').on('click', function (e) {
        e.preventDefault();
        var idColissimoLabel = parseInt($(this).attr('data-colissimo-label-id'));
        var div = $(this).closest('div.colissimo-document-info');
        div.empty();
        $('<div class="inputfile-box"><input type="file" id="invoice-file-'+idColissimoLabel+'" class="inputfile" name="invoice_'+idColissimoLabel+'" onchange="uploadFile(this)"/>\n\
             <label for="invoice-file-'+idColissimoLabel+'"><span id="invoice-'+idColissimoLabel+'" class="file-box"></span><span class="file-button">Select File</span></label></div>').insertBefore(div);
    });
    
    $('a.colissimo-delete-document').off('click').on('click', function (e) {
        e.preventDefault();
        var idColissimoLabel = parseInt($(this).attr('data-colissimo-label-id'));
        var div = $(this).closest('div.colissimo-document-info');
        div.empty();
        $('<div class="inputfile-box"><input type="file" id="doc-file-'+idColissimoLabel+'" class="inputfile" name="administrative_doc_'+idColissimoLabel+'" onchange="uploadFile(this)"/>\n\
             <label for="doc-file-'+idColissimoLabel+'"><span id="administrative-doc-'+idColissimoLabel+'" class="file-box"></span><span class="file-button">Select File</span></label></div>').insertBefore(div);
    });
});


