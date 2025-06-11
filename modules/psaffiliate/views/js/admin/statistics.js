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
    function loadCharts() {
        if ($(document).find('.psaffiliate-chart').length) {
            $(document).find('.psaffiliate-chart').each(function () {
                var $this = $(this);
                if ($this.data('url')) {
                    var url = $this.data('url');
                    var chart_name = $this.data('chart');
                    var id = $this.attr('id');

                    var datepickerFrom = $("#datepickerFrom").val();
                    var datepickerTo = $('#datepickerTo').val();

                    url += '&datepickerFrom=' + datepickerFrom + '&datepickerTo=' + datepickerTo;

                    if ($(document).find('#id_affiliate').length) {
                        var id_affiliate = $(document).find('#id_affiliate').val();
                        url += '&id_affiliate=' + id_affiliate;
                    }
                    if ($(document).find('#id_campaign').length) {
                        var id_campaign = $(document).find('#id_campaign').val();
                        url += '&id_campaign=' + id_campaign;
                    }

                    $.getJSON(url, function (jsonData) {
                        var options = jsonData;
                        for (y in options.yAxis) {
                            if (typeof options.yAxis[y] !== 'undefined' && typeof options.yAxis[y].labels !== 'undefined' && typeof options.yAxis[y].labels.formatter !== 'undefined' && options.yAxis[y].labels.formatter == "moneyFormat") {
                                options.yAxis[y].labels.formatter = function () {
                                    return formatCurrency(parseFloat(this.value), currencyFormat, currencySign, currencyBlank)
                                };
                            }
                        }
                        for (i in options.series) {
                            if (typeof options.series[i] !== 'undefined' && typeof options.series[i].tooltip !== 'undefined' && typeof options.series[i].tooltip.pointFormatter !== 'undefined' && options.series[i].tooltip.pointFormatter == "moneyFormat") {
                                options.series[i].tooltip.pointFormatter = function () {
                                    return '\x3cspan style\x3d"color:' + this.series.color + '"\x3e\u25cf\x3c/span\x3e ' + this.series.name + ': <b>' + formatCurrency(parseFloat(this.y), currencyFormat, currencySign, currencyBlank) + '</b><br />'
                                };
                            }
                        }
                        options['chart']['renderTo'] = id;
                        var chart = new Highcharts.Chart(options);
                    });
                }
            });
        }
    }

    $(document).ready(function () {
        $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
        loadCharts();
        $(document).on('submit', '#datepicker_statistics', function (e) {
            e.preventDefault();
            loadCharts();
        });
    });
});