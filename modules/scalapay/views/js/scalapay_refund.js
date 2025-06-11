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

$(document).ready(() => {
    $(document).on('click', '#desc-order-partial_refund', function () {

        if (!$('#refundWithScalapay').length) {
            $('button[name=partialRefund]')
                .parent('.partial_refund_fields')
                .prepend(`<p class="checkbox"><label for="refundWithScalapay">
                    <input type="checkbox" id="refundWithScalapay" name="refundWithScalapay" value="1">
                      ${chb_scalapay_refund}</label></p>`);
        }
    });

    $(document).on('click', '.partial-refund-display', function () {

        if (!$('#refundWithScalapay').length) {

            $('.refund-checkboxes-container')
                .prepend(`<div class="cancel-product-element form-group" style="display: block;">
                            <div class="checkbox">
                                <div class="md-checkbox md-checkbox-inline">
                                  <label>
                                      <input type="checkbox" id="refundWithScalapay" name="refundWithScalapay"  value="1">
                                      <i class="md-checkbox-control"></i>
                                        ${chb_scalapay_refund}
                                    </label>
                                </div>
                            </div>
                     </div>`);
        }
    });
});


