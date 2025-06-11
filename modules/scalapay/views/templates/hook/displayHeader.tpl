{**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 *}

{if $scalapay_in_page_checkout['enabled']}
    <script src="{$scalapay_in_page_checkout['cdnJsUrl'] nofilter}" type="text/javascript"></script>
    <script type="text/javascript">
        // instance Scalapay Checkout Popup class
        const scalapayCheckoutPopup = new ScalapayCheckoutPopup();

        /**
         * @class ScalapayPopup
         */
        class ScalapayPopup {
            /**
             * Initialize the Scalapay Popup configuration.
             *
             * @returns void
             */
            initConfiguration() {
                // build the payload
                const getAjaxControllerPayload = () => {
                    // get and check scalapay payment selected element
                    const scalapayPaymentSelected = document.querySelector(
                        'input[type=radio][data-module-name=scalapay]:checked'
                    );

                    if (!scalapayPaymentSelected || !scalapayPaymentSelected.id) {
                        return;
                    }

                    // get and check the scalapay widget element
                    const scalapayWidget = document.querySelector(
                        '#' + scalapayPaymentSelected.id + '-additional-information scalapay-widget'
                    );

                    if (!scalapayWidget || !scalapayWidget.getAttribute('product-type')) {
                        return;
                    }

                    // build and return the payload
                    const product = scalapayWidget.getAttribute('product-type').replace(/[\[\]']+/g, '');
                    const inPage = '1';
                    return new URLSearchParams({ product, inPage }).toString();
                }

                // set configurations
                scalapayCheckoutPopup.setConfig('paymentSelectors', ['{$scalapay_in_page_checkout['paymentSelectors'] nofilter}']);
                scalapayCheckoutPopup.setConfig('agreementSelectors', [{$scalapay_in_page_checkout['agreementSelectors'] nofilter}]);
                scalapayCheckoutPopup.setConfig('placeOrderSelector', {$scalapay_in_page_checkout['checkoutPlaceOrderButtonSelector'] nofilter});
                scalapayCheckoutPopup.setConfig('scalapayCdnUrl', '{$scalapay_in_page_checkout['cdnHtmlUrl'] nofilter}');
                scalapayCheckoutPopup.setConfig('ajaxController', '{$scalapay_in_page_checkout['ajaxController'] nofilter}');
                scalapayCheckoutPopup.setConfig('ajaxMode', '{$scalapay_in_page_checkout['ajaxMode'] nofilter}');
                scalapayCheckoutPopup.setConfig('ajaxContentTypeHeader', '{$scalapay_in_page_checkout['ajaxContentTypeHeader'] nofilter}');
                scalapayCheckoutPopup.setConfig('ajaxControllerPayload', getAjaxControllerPayload);
                scalapayCheckoutPopup.setConfig('placeOrderStyle', '{$scalapay_in_page_checkout['placeOrderStyle'] nofilter}');

                // run bootstrap
                scalapayCheckoutPopup.bootstrap();
            }
        }

        // start after the DOM has been loaded
        document.addEventListener('DOMContentLoaded', () => {
            // init scalapay popup class
            const scalapayPopup = new ScalapayPopup();

            // init the plugin process
            scalapayPopup.initConfiguration();
        });
    </script>
{/if}
