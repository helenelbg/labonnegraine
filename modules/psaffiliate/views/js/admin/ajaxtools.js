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
        if ($(document).find('.ajaxselectpicker').length) {
            $(document).find('.ajaxselectpicker').each(function () {
                var run = false;
                if ($(this).attr('name') == 'id_customer') {
                    var action = 'getCustomers';
                    run = true;
                } else if ($(this).attr('name') == 'id_affiliate') {
                    var action = 'getAffiliates';
                    run = true;
                } else if ($(this).attr('name') == 'id_order') {
                    var action = 'getOrders';
                    run = true;
                }
                if (run) {
                    $(this)
                        .selectpicker({
                            liveSearch: true
                        })
                        .ajaxSelectPicker({
                            ajax: {
                                url: ajaxtools_url,
                                data: function () {
                                    var params = {
                                        search_key: '{{{q}}}',
                                        action: action,
                                    };
                                    return params;
                                }
                            },
                            locale: {
                                emptyTitle: 'Type to search...'
                            },
                            preprocessData: function (data) {
                                var result = [];
                                if (typeof data.result === 'undefined') {
                                    return false;
                                }
                                for (i in data.result) {
                                    var curr = data.result[i];
                                    result.push(
                                        {
                                            'value': curr.id_object,
                                            'text': curr.value,
                                            'disabled': false
                                        }
                                    );
                                }
                                return result;
                            },
                            preserveSelected: true
                        });
                } else {
                    console.log('No action');
                }
            });
        }
    });
});