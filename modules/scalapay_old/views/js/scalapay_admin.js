/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5.0
 */
jQuery(
    function ($) {
        $(function() {
            // Configuration tab.
            // Those tabs are mapped on scalapay.php file on renderForm() function.
            // If you need to change the order or add different tabs, please change it the on renderForm() function.
            let tabs = {
                "scalapay_common": "GENERAL SETTINGS",
                "scalapay_pay_in_3": "PAY IN 3",
                "scalapay_pay_in_4": "PAY IN 4",
                "scalapay_pay_later": "PAY LATER"
            }

            if (!isPs16()) {
                tabs["scalapay_in_page_checkout"] = "IN PAGE CHECKOUT";
            }

            Object.keys(tabs).forEach(id => $("ul[scalapay-nav-bar]").append(`<li ><a href="#" content="${id}" >${tabs[id]}</a></li>`))

            $("ul[scalapay-nav-bar] li:first-child").addClass("active");
            $("#configuration_form").css("margin-top", "25px");
            //$(".module_confirmation").parent('.bootstrap').css({"margin-top": "50px"});

            $("#configuration_form .panel").not(`[id*="fieldset_${Object.keys(tabs)[0]}"]`).hide();

            $("ul[scalapay-nav-bar] li a").on("click", (e) => {

                e.preventDefault();

                $("ul[scalapay-nav-bar] li").removeClass("active");
                $(e.target).parent().addClass("active");
                // todo: improve the code. Hide first show only the id*="".
                $("#configuration_form .panel").show();
                $("#configuration_form .panel").not(`[id*="fieldset_${e.target.getAttribute("content")}"]`).hide();
            });

            function isPs16() {
                return typeof _PS_VERSION_ !== 'undefined' && parseInt(_PS_VERSION_.replaceAll('.', '').slice(0, 2)) === 16;
            }
        });
    }
);
