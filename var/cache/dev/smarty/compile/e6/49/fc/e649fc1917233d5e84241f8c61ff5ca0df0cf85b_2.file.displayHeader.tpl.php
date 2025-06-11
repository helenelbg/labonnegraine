<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from '/home/dev.labonnegraine.com/public_html/modules/scalapay/views/templates/hook/displayHeader.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff4a2eb4_20546907',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e649fc1917233d5e84241f8c61ff5ca0df0cf85b' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/scalapay/views/templates/hook/displayHeader.tpl',
      1 => 1738070873,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff4a2eb4_20546907 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['enabled']) {?>
    <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['cdnJsUrl'];?>
" type="text/javascript"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript">
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
                scalapayCheckoutPopup.setConfig('paymentSelectors', ['<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['paymentSelectors'];?>
']);
                scalapayCheckoutPopup.setConfig('agreementSelectors', [<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['agreementSelectors'];?>
]);
                scalapayCheckoutPopup.setConfig('placeOrderSelector', <?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['checkoutPlaceOrderButtonSelector'];?>
);
                scalapayCheckoutPopup.setConfig('scalapayCdnUrl', '<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['cdnHtmlUrl'];?>
');
                scalapayCheckoutPopup.setConfig('ajaxController', '<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['ajaxController'];?>
');
                scalapayCheckoutPopup.setConfig('ajaxMode', '<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['ajaxMode'];?>
');
                scalapayCheckoutPopup.setConfig('ajaxContentTypeHeader', '<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['ajaxContentTypeHeader'];?>
');
                scalapayCheckoutPopup.setConfig('ajaxControllerPayload', getAjaxControllerPayload);
                scalapayCheckoutPopup.setConfig('placeOrderStyle', '<?php echo $_smarty_tpl->tpl_vars['scalapay_in_page_checkout']->value['placeOrderStyle'];?>
');

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
    <?php echo '</script'; ?>
>
<?php }
}
}
