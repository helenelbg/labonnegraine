/*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */
$(function () {
    if ($(document).find('select#id_campaign').length) {
        var selectedCampaign = $('select#id_campaign').val();
        if (typeof campaigns_controller_link !== 'undefined') {
            $(document).on('change', 'select#id_affiliate', function (e) {
                var id_affiliate = $(this).val();
                getCampaignsList(id_affiliate);
            });
            if ($('select#id_affiliate').length) {
                if ($('select#id_affiliate').val()) {
                    getCampaignsList($('select#id_affiliate').val());
                }
            }
        }
    }

    function getCampaignsList(id_affiliate) {
        var url = campaigns_controller_link + '&action=getCampaignsOfAffiliate&id_affiliate=' + id_affiliate;
        $.getJSON(url, function (jsonData) {
            if (typeof jsonData.data !== 'undefined') {
                var html = '<option value="0">--</option>';
                for (i in jsonData.data) {
                    html += '<option value="' + i + '"';
                    if (i == selectedCampaign) {
                        html += ' selected="selected"';
                    }
                    html += '>' + jsonData.data[i] + '</option>';
                }
                $('select#id_campaign').html(html);
            }
        });
    }
});