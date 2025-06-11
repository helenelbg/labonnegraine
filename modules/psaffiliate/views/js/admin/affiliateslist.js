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
    $(document).ready(function () {
        var ids_affiliate = [];
        if ($(document).find('input[name="aff_affiliatesBox[]"]').length) {
            $(document).find('input[name="aff_affiliatesBox[]"]').each(function () {
                ids_affiliate.push($(this).val());
            });
        }
        else {
            if ($(document).find('.table.aff_affiliates').length) {
                var $this = $(document).find('.table.aff_affiliates');
                if ($this.find('tr td:nth-child(1)').length) {
                    $this.find('tr td:nth-child(1)').each(function () {
                        if($(this).text().trim().match(/^[0-9]+$/) != null) {
                            var id_aff = $(this).text().trim().match(/^[0-9]+$/).join("");
                        }
                        ids_affiliate.push(id_aff);
                    });
                }
            }
        }
        if (typeof urlForAffiliateCheck !== 'undefined') {
            url = urlForAffiliateCheck;

            $.ajax({
                method: 'GET',
                url: url,
                data: 'ids_affiliate=' + ids_affiliate.join(","),
                dataType: 'json',
                success: function (data) {
                    if (data.success && typeof data.result !== 'undefined') {
                        for (i in data.result) {
                            var id_affiliate = data.result[i]['id_affiliate'];
                            if ($(document).find('input[name="aff_affiliatesBox[]"]').length) {
                                $(document).find('input[name="aff_affiliatesBox[]"]').each(function () {
                                    if ($(this).val() == id_affiliate) {
                                        var tr = $(this).closest('tr');
                                        hasNotBeenReviewed(tr, 3);
                                    }
                                });
                            }
                            else {
                                if ($(document).find('.table.aff_affiliates').length) {
                                    var $this = $(document).find('.table.aff_affiliates');
                                    if ($this.find('tr td:nth-child(1)').length) {
                                        $this.find('tr td:nth-child(1)').each(function () {
                                            var id_aff = $(this).text().trim().match(/^[0-9]+$/).join("");
                                            if (id_aff == id_affiliate) {
                                                var tr = $(this).closest('tr');
                                                hasNotBeenReviewed(tr, 2);
                                            }
                                        });
                                    }
                                }
                            }
                            $('.has_not_been_reviewed_span').tooltip();
                        }
                    }
                    else {
                        console.log("ERROR! Can't get non-reviewed affiliates.");
                    }
                }
            });
        }
    });
});
function hasNotBeenReviewed(tr, index) {
    index = index || 3;

    var td = tr.find('td:nth-child(' + index + ')');
    hasNotBeenReviewedSpan = '<a class="has_not_been_reviewed_span" data-toggle="tooltip" title="' + hasNotBeenReviewedText + '" data-original-title="' + hasNotBeenReviewedText + '" href="#"></a>';
    td.prepend(hasNotBeenReviewedSpan);
}