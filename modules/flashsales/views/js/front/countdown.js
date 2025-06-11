/*
* 2022 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2022 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*/

$(document).ready(function() {
    flashSale.initAssets();
    flashSale.initBind();

    if (typeof prestashop === 'undefined' && typeof combinations !== 'undefined') {
        flashSale.refreshProductAttribute();
        flashSale.afterRefreshProductAttribute(); 
    } else {
        flashSale.startCountdown();
    }
});

var flashSale = {
    getCarouselConfigs: function(layout) {
        var configs = {};

        configs = {
            responsiveClass: true,
            responsiveBaseElement: ".flashsale-"+layout,
            nav: false,
        };

        if (layout == 'column') {
            $.extend(true, configs, {
                margin: 10,
                items: 1,
                dots: true
            });
        } else if (layout == 'page' || layout == 'home') {
            $.extend(true, configs, {
                margin: 30,
                responsive: {
                    0: {
                        items: 1,
                        dots: false
                    },
                    450: {
                        items: 2,
                        dots: false
                    },
                    720: {
                        items: 3,
                        dots: true
                    },
                    940: {
                        items: 4,
                        dots: true
                    },
                }
            });
        }

        return configs;
    },

    initAssets: function() {
        if (!!$.prototype.owlCarousel2) {
            $('.flashsale-home .owl-carousel').owlCarousel2(flashSale.getCarouselConfigs('home'));
            $('.flashsale-page .owl-carousel').owlCarousel2(flashSale.getCarouselConfigs('page'));
            $('.flashsale-column .owl-carousel').owlCarousel2(flashSale.getCarouselConfigs('column'));
        }
    },

    initBind: function() {
        if (typeof prestashop !== 'undefined') {
            prestashop.on('updatedProduct', function(e) {
                $('.flashsale-countdown-box').attr('data-id-product-attribute', e.id_product_attribute);
                flashSale.afterRefreshProductAttribute();
            });
        }

        $(document).on('click', '.color_pick', function(e) {
            $('.flashsale-countdown-box').hide();
            flashSale.refreshProductAttribute();
            flashSale.afterRefreshProductAttribute();
        });

        $(document).on('change', '.attribute_select', function(e) {
            $('.flashsale-countdown-box').hide();
        	flashSale.refreshProductAttribute();
            flashSale.afterRefreshProductAttribute();
        });

        $(document).on('click', '.attribute_radio', function(e) {
            $('.flashsale-countdown-box').hide();
            flashSale.refreshProductAttribute();
            flashSale.afterRefreshProductAttribute();
        });
    },

    refreshProductAttribute: function() {
        var idProductAttribute = flashSale.getProductAttribute();

        $('.flashsale-countdown-box').attr('data-id-product-attribute', idProductAttribute);
    },

    getProductAttribute: function() {
        var choice = [];

    	$('#attributes select, #attributes input[type=hidden], #attributes input[type=radio]:checked').each(function() {
    		choice.push(parseInt($(this).val()));
    	});

        for (var combination = 0; combination < combinations.length; ++combination) {
    		//verify if this combinaison is the same that the user's choice
    		var combinationMatchForm = true;
    		$.each(combinations[combination]['idsAttributes'], function(key, value) {
    			if (!in_array(parseInt(value), choice)) {
                    combinationMatchForm = false;
                }
    		});

    		if (combinationMatchForm) {
                return combinations[combination]['idCombination']
            }
        }

        return -1;
    },

    afterRefreshProductAttribute: function() {
        var combinations, idProductAttribute;

        combinations = $('.flashsale-countdown-box').attr('data-combinations');
        idProductAttribute = $('.flashsale-countdown-box').attr('data-id-product-attribute');

        if (typeof combinations !== 'undefined' && combinations !== false) {
            combinations = JSON.parse(combinations);
            if (idProductAttribute in combinations) {
                $('.flashsale-countdown-box').show().find('.countdown').attr('data-to', combinations[idProductAttribute]);
            } else {
                $('.flashsale-countdown-box').hide().find('.countdown').removeAttr('data-to');
            }
        }

        flashSale.startCountdown();
    },

    startCountdown: function() {
        $('.flashsale-countdown-box').each(function() {
    		var to = $(this).find('.countdown').attr('data-to');
            if (typeof to !== 'undefined' && to !== false) {
                flashSale.updateTime($(this), to);
            }
    	});

    	if ($('.flashsale-countdown-box').length) {
            setTimeout("flashSale.startCountdown()",1000);
        }
    },

    setTimerValue: function(f, h, d) {
        d = Math.max(d, 0);
        var c = d;
        var k = Math.floor(c / (24 * 60 * 60));
        c -= k * 24 * 60 * 60;
        h.find(".TimerDay").text(k);
        if (k > 0) {
            h.find(".timerDay").show();
        } else {
            h.find(".timerDay").hide();
        }
        var i = Math.floor(c / (60 * 60));
        c -= i * 60 * 60;
        h.find(".TimerHour").text(i);
        if (i > 0) {
             h.find(".timerHour").show();
        } else {
            h.find(".timerHour").hide();
        }
        var b = Math.floor(c / 60);
        c -= b * 60;
        h.find(".TimerMin").text(b);
        var j = c;
        h.find(".TimerSec").text(j);
    },

    getTimeLeftObject: function (d) {
        var b = Math.round(new Date().getTime() / 1000);
    	var f = d - b;
    	return f;
    },

    updateTime: function(a, c) {
        if (c >= 0) {
            var d = flashSale.getTimeLeftObject(c);

    		if (d >= 0) {
    			flashSale.setTimerValue(a, a.find(".countdown"), d)
    		}
        }
    }
}
