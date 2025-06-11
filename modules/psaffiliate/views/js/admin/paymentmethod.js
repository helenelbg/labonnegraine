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
        var container = $('#aff_payment_methods_form .form-wrapper').first();
        var fieldHtml = '';
        fieldHtml += '<div class="form-group">';
        fieldHtml += '<label class="control-label col-lg-3">%label%</label>';
        fieldHtml += '<div class="col-lg-4">';
        fieldHtml += '<input name="%name%" value="%value%" autocomplete="off" type="text" />';
        fieldHtml += '</div>';
        fieldHtml += '</div>';

        var i = $('input[name^="payment_method_field"]').length;
        if (i < 1) {
            i = 1;
        }


        container.append('<div class="clearfix"></div>');
        container.append('<h4>Fields</h4>');

        if (typeof payment_method_fields != 'undefined' && payment_method_fields.length > 0) {
            payment_method_fields = $.parseJSON(payment_method_fields);
            for (var j in payment_method_fields) {
                container.append(fieldHtml.replace('%label%', 'Field #' + i).replace('%name%', 'payment_method_field[' + payment_method_fields[j]['id_payment_method_field'] + ']').replace('%value%', payment_method_fields[j]['field_name']));
                i++;
            }
        }

        container.append(fieldHtml.replace('%label%', 'Field #' + i).replace('%name%', 'payment_method_field[new][]').replace('%value%', ''));
        i++;
        $(document).on('keyup', '#aff_payment_methods_form .form-wrapper .form-group:last-child input', function () {
            var $this = $(this);
            if ($this.val() != "") {
                container.append(fieldHtml.replace('%label%', 'Field #' + i).replace('%name%', 'payment_method_field[new][]').replace('%value%', ''));
                i++;
            }
        });
    });
});