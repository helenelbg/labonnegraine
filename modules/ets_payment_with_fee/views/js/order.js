/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
$(document).ready(function(){
    ajaxChangePaymentMethod($('#'+$( "input[data-module-name='paypal']" ).attr('id')), false);
    ajaxChangePaymentMethod($('#'+$( "input[data-module-name='payline']" ).attr('id')));

    setTimeout(function(){     
        if(ets_cookie_payment_option && $('#'+ets_cookie_payment_option).length)
        {
                $('#'+ets_cookie_payment_option).click();
                ajaxChangePaymentMethod($('#'+ets_cookie_payment_option));
        }
        else
        {
            if($('#payment-method input[name="payment_method"]').length)
            {
                $('#payment-method input[name="payment_method"]:first').click();
                ajaxChangePaymentMethod($('#payment-method input[name="payment_method"]:first')); 
            }
            if($('#checkout-payment-step input[name="payment-option"]').length)
            {
                $('#checkout-payment-step input[name="payment-option"]:first').click();
                ajaxChangePaymentMethod($('#checkout-payment-step input[name="payment-option"]:first')); 
            }
        } 
    },3000);
    $( document ).ajaxComplete(function( event, request, settings ) {
        var data_post = settings.data;
        if(data_post && typeof(data_post)!='object' && data_post.indexOf('method=loadPaymentAdditionalInfo')>=0)
        {
            if(ets_cookie_payment_option && $('#'+ets_cookie_payment_option).length)
            {
                    $('#'+ets_cookie_payment_option).click();
                    ajaxChangePaymentMethod($('#'+ets_cookie_payment_option));
            }
            else
            {
                if($('#payment-method input[name="payment_method"]').length)
                {
                    $('#payment-method input[name="payment_method"]:first').click();
                    ajaxChangePaymentMethod($('#payment-method input[name="payment_method"]:first')); 
                }
                if($('#checkout-payment-step input[name="payment-option"]').length)
                {
                    $('#checkout-payment-step input[name="payment-option"]:first').click();
                    ChangePaymentMethod($('#checkout-payment-step input[name="payment-option"]:first')); 
                }
            }
        }
    });
    if(ets_cookie_payment_option)
    {
        $(document ).ajaxComplete(function( event, xhr, settings ) {
            if(xhr.responseText && xhr.responseText.indexOf("preview")>=0)
            {
                var data = JSON.parse(xhr.responseText);
                if(data.preview)
                {
                    setTimeout(function(){ajaxGetPaymentFee();},500);
                }
            }
        });
    }
    if($('#checkout-payment-step input[name="payment-option"]').length)
    {
        $(document).on('click','#checkout-payment-step input[name="payment-option"]',function(){
            ajaxChangePaymentMethod($(this));
        });
    }
    $(document).on('click','#payment-method input[name="payment_method"]',function(){
        ajaxChangePaymentMethod($(this));
    });
});
function ajaxGetPaymentFee()
{
    $.ajax({
        url: '',
        data: 'ets_get_payment_fee=1',
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.payment_fee)
            {
                if($('#blockcart-modal .fee_payment').length)
                {
                    $('#blockcart-modal .fee_payment').html('<strong>'+label_payment_fee+': </strong>'+json.payment_fee);
                }
                else
                {
                    if($('#blockcart-modal .cart-content-btn').length>0)
                    {
                        if($('#blockcart-modal .cart-content-btn').prev('p').length)
                        {
                           $('#blockcart-modal .cart-content-btn').prev('p').before('<p class="fee_payment"><strong>'+label_payment_fee+': </strong>'+json.payment_fee+'</p>'); 
                        }
                        else
                        {
                            $('#blockcart-modal .cart-content-btn').before('<p class="fee_payment"><strong>'+label_payment_fee+': </strong>'+json.payment_fee+'</p>');
                        }
                    }
                    
                }
            }
            else
            {
                if($('#blockcart-modal .fee_payment').length)
                   $('#blockcart-modal .fee_payment').remove();
            }
        },
        error: function(xhr, status, error)
        {            
        }
    });
}
function ajaxChangePaymentMethod($this, afficher = true)
{
    $('.js-payment-confirmation button').addClass('loading');
    var $module_name = $('#form_ets_onepagecheckout').length==0  ? $this.data('module-name') :  $this.val();
    var $payment_option = $this.attr('id');
    ets_cookie_payment_option = $payment_option;
    if($module_name=='ets_payment_with_fee')
    {
        if($('#payment-method input[name="payment_method"]').length)
            var $id_payment_method = $('#pay-with-'+$payment_option).prev('input[name="id_payment_method"]').val();
        else
            var $id_payment_method = $('#pay-with-'+$payment_option+'-form input[name="id_payment_method"]').val();
    }
    else
        var $id_payment_method = 0;
    if($('#payment-confirmation button').attr('disabled') != 'disabled')
    {
        $('#payment-confirmation button').addClass('loading');
        $('#payment-confirmation button').attr('disabled','disabled');
    }
    $.ajax({
        url: '',
        data: 'ets_set_payment_option=1&module_name='+$module_name+'&id_payment_method='+$id_payment_method+'&payment_option='+$payment_option+($('#shipping_address_id_country').length ? '&id_country='+$('#shipping_address_id_country').val():'')+($('#shipping_address_id_state').length ? '&id_state='+$('#shipping_address_id_state').val() :'')+($('#shipping_address_postal_code').length ? '&postal_code='+$('#shipping_address_postal_code').val():''),
        type: 'post',
        dataType: 'json',
        success: function(json){
            
            if ( afficher )
            {
                if($('#payment-confirmation button').hasClass('loading'))
                {
                    $('#payment-confirmation button').removeClass('loading');
                    $('#payment-confirmation button').removeAttr('disabled');
                }
                $('.cart-summary-totals .value').html(json.total_cart_excl);
                $('.cart-summary-line.cart-total .value').html(json.total_cart);
                $('.cart-summary-totals .value.sub').html(json.total_tax);
                $('.supercheckout-totals #total_price').html(json.total_cart);
            }
            if(json.payment_fee)
            {
                if ( afficher )
                {
                    if($('table.supercheckout-totals').length > 0){
                        if($('#cart-subtotal-fee_payment').length)
                        {
                            $('#cart-subtotal-fee_payment .value .price').html(json.payment_fee);
                        }
                        else
                        {
                            var html_section_onpage='<tr id="cart-subtotal-fee_payment"><td class="title"><span class="label"><b>'+label_payment_fee+'</b></span></td><td class="fee value"><span class="price">'+json.payment_fee+'</span></td></tr>';
                            $('#supercehckout_summary_total_shipping').after(html_section_onpage);  
                        }
                        
                    }
                    else
                    {
                        if($('#cart-subtotal-fee_payment').length)
                        {
                            $('#cart-subtotal-fee_payment .value').html(json.payment_fee);
                        }
                        else
                        {
                            if($('#cart-subtotal-shipping').length>0)
                                $('#cart-subtotal-shipping').after('<div id="cart-subtotal-fee_payment" class="cart-summary-line cart-summary-subtotals"><span class="label">'+label_payment_fee+'</span><span class="value">'+json.payment_fee+'</span></div>')
                        }
                    }
                }
                if($('#'+$payment_option+'-additional-information').length==0 && $('#'+$payment_option+'-container').length)
                {
                    $('#'+$payment_option+'-container').parent().after('<div id="'+$payment_option+'-additional-information" class="js-additional-information definition-list additional-information ps-hidden " style="display: block;"> <div>');
                }
                if($('#'+$payment_option+'-container label').length)
                {
                    if($('#'+$payment_option+'-container label .fee').length==0)
                    {
                        var html_section = '<span class="fee">+ '+json.payment_fee+'</span>';
                        if($('#'+$payment_option+'-container label').length > 0)
                        {
                            $('#'+$payment_option+'-container label').append(html_section);
                        }
                    }
                    else
                    {
                        $('#'+$payment_option+'-container label .fee').html('+ '+json.payment_fee);
                    }
                }
                if($('.'+$payment_option+'_info_container').length)
                {
                    if($('.'+$payment_option+'_info_container .payment_fee').length==0)
                    {
                        var html_section = '<div class="supercheckout-blocks js-additional-information definition-list additional-information"><section class="payment_fee"><dl><dt>'+label_payment_fee+' '+json.text_percentage+'</dt><dd class="fee">+'+json.payment_fee+'</dd></dl></section></div>';
                        if($('.'+$payment_option+'_info_container').length >0)
                        {
                            $('.'+$payment_option+'_info_container').append(html_section);
                        }
                    }
                    else
                    {
                        $('.'+$payment_option+'_info_container .payment_fee .fee').html('+'+json.payment_fee);
                    }
                }
            }
            else
            {
                if($('#cart-subtotal-fee_payment').length)
                   $('#cart-subtotal-fee_payment').remove();
            }
            $('#paypal-buttons').show();
            $('.js-payment-confirmation button').removeClass('loading');
        },
        error: function(xhr, status, error)
        {            
        }
    });
}