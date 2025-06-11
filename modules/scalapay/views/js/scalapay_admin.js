/**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
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
