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
    $(document).on('click', '#submit-process-colissimo-deposit-slip', function (e) {
        e.preventDefault();
        var labelList = [];
        var chunks = [];
        var size = 50;
        var success = [];
        var selectedData = Object.assign(
            {},
            tableOlderParcels.rows({selected: true}).data().toArray(),
            tableParcelsOfToday.rows({selected: true}).data().toArray()
        );

        $.each(selectedData, function () {
            labelList.push(parseInt(this.id_colissimo_label));
        });

        if (labelList.length === 0) {
            showErrorMessage(noLabelSelectedText);
            return;
        }

        $('#deposit-slip-result').hide().html('');
        $(this).toggleClass('disabled');
        while (labelList.length > 0) {
            chunks.push(labelList.splice(0, size));
        }
        processColissimoDepositSlip(chunks, success);
    });
});
