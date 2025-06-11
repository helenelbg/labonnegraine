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
 $(document).ready(function () {
    $('.ets-am-product-settings input[data-decide]').each(function (index, el) {
        etsAmToggleElDecide(el);
    });
    $(document).on('change','.ets-am-product-settings input[data-decide]',function (event) {
        etsAmToggleElDecide(this);
    });

    eamToggleCardSettingPrd('.ets-am-product-settings  input[name=aff_reward_use_default]');
    eamToggleCardSettingPrd('.ets-am-product-settings  input[name=loyalty_reward_use_default]');

    $(document).on('change', '.ets-am-product-settings  input[name=aff_reward_use_default]', function(event) {
        eamToggleCardSettingPrd(this);
    });
    $(document).on('change', '.ets-am-product-settings  input[name=loyalty_reward_use_default]', function(event) {
        eamToggleCardSettingPrd(this);
    });

    $(document).on('click', '.ets-am-product-settings .js-ets-sm-save-setting-prd', function (event) {
        id_product = $('.ets-am-product-settings input[name=id_product]').val();
        id_product = parseInt(id_product);
        ets_error = false;
        if($('input[name=loyalty_reward_use_default]').length)
        {
            eam_loy_settings = {
                use_default: $('input[name=loyalty_reward_use_default]').is(':checked') ? 1 : 0,
                id_product: id_product
            };
        }
        else
            eam_loy_settings ={};
        if($('input[name=aff_reward_use_default]').length){
            eam_aff_settings = {
                use_default: $('input[name=aff_reward_use_default]').is(':checked') ? 1 : 0,
                id_product: id_product
            };
        }
        else
            eam_aff_settings ={};

        $('.ets-am-product-settings input:visible').each(function (index, el) {
            $(this).closest('.form-group').find('.ets-error').remove();
            if (!$(this).val() && $(this).attr('name') !== 'qty_min') {
                if (typeof ets_am_msg_required !== 'undefined') {
                    eamShowError(this);
                    ets_error = true;
                    return;
                }
            }
            else {
                if ($(this).attr('type') == 'text') {
                    if (isNaN($(this).val()) || (!isNaN($(this).val()) && $(this).val() < 0)) {
                        eamShowError(this);
                        ets_error = true;
                        return;
                    }
                }
                if($(this).attr('name') == 'aff_reward_use_default' || $(this).attr('name') == 'loyalty_reward_use_default'){
                    return;
                }

                if ($(this).attr('type') == 'radio') {
                    if ($(this).is(':checked')) {
                        if ($(this).closest('.card').attr('data-type') == 'loyalty_reward') {
                            eam_loy_settings[$(this).attr('name')] = $(this).val();
                        }
                        else if ($(this).closest('.card').attr('data-type') == 'aff_reward') {
                            eam_aff_settings[$(this).attr('name')] = $(this).val();
                        }
                    }
                }
                
                else {
                    if ($(this).closest('.card').attr('data-type') == 'loyalty_reward') {
                        eam_loy_settings[$(this).attr('name')] = $(this).val();
                    }
                    else if ($(this).closest('.card').attr('data-type') == 'aff_reward') {
                        eam_aff_settings[$(this).attr('name')] = $(this).val();
                    }

                }
            }
        });

        if (typeof ets_am_link_ajax !== 'undefined' && !ets_error) {
            ajaxUpdateSettingPrd(ets_am_link_ajax, {
                loy_settings: eam_loy_settings,
                aff_settings: eam_aff_settings,
            });
        }
    });
});

function etsAmToggleElDecide(input) {
    if ($(input).attr('type') == 'radio' && $(input).is(':checked')) {
        name_el = $(input).attr('name');
        $('.ets-am-product-settings input[name=' + name_el + ']').each(function (index, el) {
            if (typeof $(el).attr('data-decide') !== 'undefined') {
                decide_els = $(el).attr('data-decide').split(',');
                $.each(decide_els, function (i2, el2) {
                    if ($('input').hasClass(el2)) {

                        $('.' + el2).first().closest('.form-group').hide();
                    }
                });
            }

        });

        str_els = $(input).attr('data-decide');
        array_els = str_els.split(',');
        $.each(array_els, function (index, el) {
            if ($('input').hasClass(el)) {
                $('input.' + el).first().closest('.form-group').show();
            }
        });
    }
}

function eamShowError(input) {
    $(input).closest('.form-group').find('.ets-error').remove();
    $(input).closest('.form-group').append('<div class="ets-error" style="color: red; font-size: 12px;">' + ets_am_msg_required + '</div>');
}

function ajaxUpdateSettingPrd(url, data) {
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            data: data,
            updateProductSetting: true
        },
        success: function (res) {
            if (res.success) {
                showSuccessMessage(res.message);
            }
            else {
                showErrorMessage(res.message);
            }
        }
    })
}

function eamToggleCardSettingPrd(input){
    if($(input).is(':checked')){
        $(input).closest('.card').find('.form-group').hide();
        $(input).closest('.form-group').show();
    }
    else{
        $(input).closest('.card').find('.form-group').show();
        $(input).closest('.card').find('input[data-decide]').each(function (index, el) {
            etsAmToggleElDecide(el);
        });
    }
}