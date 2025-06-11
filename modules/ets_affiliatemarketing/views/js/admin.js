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

var eam_payment_method_saved = false;
var eamLineChartReward;
var eamLineChartRewardData;
var eamChartPieReward;
var eamChartPieRewardData;
var eam_xhr_ajax_sortable_pmm;
var eam_xhr_ajax_search_suggestion;
var eam_xhr_ajax_update_secure_cronjob;
var xhr_search = false;
$.fn.extend({
    eamSearchProduct: function (url_ajax, class_result, el_list_add) {
        $this = $(this);
        if ($this.length > 0 && url_ajax) {
            $(this).autocomplete(url_ajax, {
                resultsClass: class_result,
                minChars: 1,
                delay: 300,
                autoFill: false,
                max: 20,
                matchContains: false,
                mustMatch: false,
                scroll: true,
                scrollHeight: 180,
                extraParams: {
                },
                formatItem: function (item) {
                    html = '<div data-item-id="' + item[0] + '" class="search_item">';
                    html += '<div class="item-img"><img src="' + item[5] + '" alt="" ></div>';
                    html += '<div class="item-body"><p>' + item[2] + (item[3] ? item[3] : '') + (item[4] ? ' (Ref:' + item[4] + ')' : '') + '<p></div>';
                    html += '</div>';
                    return html;
                },
            }).result(function (event, data, formatted) {
                input_add_ids = $(this).attr('data-target');
                if (data) {
                    //Add product
                    if ($(this).parent().find(el_list_add).length > 0) {
                        list_add = $(this).parent().find(el_list_add);
                        if (!list_add.hasClass('active')) {
                            $.ajax({
                                url: url_ajax,
                                data: {
                                    ids: data[0],
                                    product_type: 'specific'
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (json) {
                                    list_add.removeClass('active');
                                    if (json) {
                                        if (input_add_ids) {
                                            $input_add_ids = $('input[name=' + input_add_ids + ']');
                                            if (!$input_add_ids.val()) {
                                                $input_add_ids.val(data[0]);
                                                list_add.append(json.html);
                                            }
                                            else {
                                                ids = $input_add_ids.val().split(',');
                                                if (ids.indexOf(data[0]) == -1) {
                                                    $input_add_ids.val($input_add_ids.val() + ',' + data[0]);
                                                    list_add.append(json.html);
                                                } else {
                                                    showErrorMessage(data[2].toString() + ' ' + ets_snw_msg_tagged);
                                                }
                                            }
                                        }
                                    }

                                },
                                error: function (xhr, status, error) {
                                    list_add.removeClass('active');
                                }
                            });
                        }
                    }
                }

                //Clear input
                $(this).val('');
            });
        }
    }
});

//clone noti
if($('.ets-sn-admin__body .bootstrap').length){  
    $('.ets-sn-admin').before($('.ets-sn-admin__body .bootstrap').clone());
  $('.ets-sn-admin__body .bootstrap').remove();
}


//remove list results
$(document).on('click', '.snw_block_item_close', function (e) {
    e.preventDefault();
    id = $(this).parent().parent().attr('data-id');
    input_data = $(this).closest('.form-group').find('.ets_snw_ids');
    //remove value in input
    ids_prd = input_data.val().split(',');
    index_id = ids_prd.indexOf(id);
    if (index_id !== -1) {
        ids_prd.splice(index_id, 1);
    }
    input_data.val(ids_prd.toString());

    //remove item in list
    $(this).parent().parent().remove();
});
//Hide or show checbox tree
$(window).load(function () {
    if ($('.ets-sn-admin .has-tree-option').is(':checked')) {
        tree = $('.ets-sn-admin .has-tree-option').attr('data-tree');
        $(tree).show();
    }
});
var affAddUser = function(event, data, formatted)
{
	if (data == null)
		return false;
	var customerID = data[0];
	var cusomerName = data[2];
    var customerEmail = data[1];
    $('#aff_id_search_customer_user').val(customerID);
    $('#aff_search_customer_user').before('<div class="search_item_added">'+cusomerName+' ('+customerEmail+') <span class="aff_delete_customer"></span></div>');
};
function setMore_menu() {
    var menu_width_box = $('.ets-sn-admin__tabs').width();
    var menu_width = $('.ets-sn-admin__tabs .tab-list.nav').width();
    var itemwidthlist = 0;
    $(".ets-sn-admin__tabs .tab-list.nav > li.li_aff_item_menu").each(function () {
        var itemwidth = $(this).width();
        itemwidthlist = itemwidthlist + itemwidth;
        if (itemwidthlist > menu_width_box - 70 && itemwidthlist > 500) {
            $(this).addClass('hide_more');
        } else {
            $(this).removeClass('hide_more');
        }
    });
}

jQuery(document).ready(function ($) {
    setMore_menu();
    $(window).resize(function () {
        setMore_menu();
        $(".ets-sn-admin__tabs .tab-list.nav > li.hide_more").removeClass('show_hover');
    });
    $('.ets-sn-admin__tabs .tab-list.nav > li.more_menu').on('click', function (e) {
        $(".ets-sn-admin__tabs .tab-list.nav > li.hide_more").toggleClass('show_hover');
    });
    $(document).mouseup(function (e) {
        var confirm_popup = $('.ets-sn-admin__tabs .tab-list.nav > li.hide_more');
        if (!confirm_popup.is(e.target) && confirm_popup.has(e.target).length === 0) {
            $(".ets-sn-admin__tabs .tab-list.nav > li.hide_more").removeClass('show_hover');
        }
    });

    $(document).on('click','input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]',function(){
        eamCheckMultiLevel(); 
    });
    eamCheckMultiLevel();
    $(document).on('click','button.clear_qr_code_cache',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading') && confirm(ets_swn_trans['confirm_clear_qrcode']))
        {
            $(this).addClass('loading');
            $.ajax({
                url: '',
                type: 'POST',
                dataType: 'json',
                data: {
                    submitClearQRCodeCache: 1,
                },
                success: function(json){
                    $('button.clear_qr_code_cache').removeClass('loading');
                    if(json.error)
                    {
                        showErrorMessage(json.error);
                    }
                    if(json.success)
                    {
                        showSuccessMessage(json.success);
                    }   
                },
                error: function () {
                    $('button.clear_qr_code_cache').removeClass('loading');
                }
            });
        }
        
    })
    $(document).on('click','button[name="submitAddUserReward"]',function(e){
       e.preventDefault();
       $('.aff_popup_wapper .alert-success').parent().remove();
       $('.aff_popup_wapper .module_error').parent().remove();
       $(this).addClass('loading');
       $.ajax({
            url: '',
            type: 'POST',
            dataType: 'json',
            data: {
                submitAddUserReward: 1,
                id_customer: $('#aff_id_search_customer_user').val(),
                aff_customer_loyalty : $('#aff_customer_loyalty').is(':checked') ? 1:0,
                aff_customer_referral : $('#aff_customer_referral').is(':checked') ? 1:0,
                aff_customer_affiliate : $('#aff_customer_affiliate').is(':checked') ? 1:0,
            },
            success: function(json){
                if(json.errors)
                    $('.aff_popup_content').append(json.errors);
                if(json.success)
                {
                    $('.aff_popup_content').append(json.success);
                    setTimeout(function(){ 
                        window.location.reload();
                    }, 3000);
                }
                $('button[name="submitAddUserReward"]').removeClass('loading');    
            },
        }); 
    });
    $(document).on('click','.aff_delete_customer',function(){
       $(this).parent().remove(); 
       $('#aff_search_customer_user').val('');
       $('.aff_popup_wapper .alert-success').parent().remove();
       $('.aff_popup_wapper .module_error').parent().remove();
    });
    $(document).on('click','button[name="btnAddNewUserReward"]',function(e){
        e.preventDefault();
        $('.aff_popup_wapper .alert-success').parent().remove();
        $('.aff_popup_wapper .module_error').parent().remove();
       $('.aff_popup_wapper').addClass('show'); 
    });
    if($('#aff_search_customer_user').length>0)
    {
        $('#aff_search_customer_user').autocomplete(aff_link_search_customer,{
            resultsClass: 'aff_customer_search',
    		minChars: 1,
    		autoFill: true,
    		max:20,
    		matchContains: true,
    		mustMatch:false,
    		scroll:false,
    		cacheLength:0,
    		formatItem: function(item) {
    			return item[0]+' - '+item[1]+' - '+item[2];
    		}
    	}).result(affAddUser);
    }
    $(document).on('click','.ets-am-clear-log',function(event){
        event.preventDefault();
        var $this = $(this);
        var btn_html = $this.val();
        if(!$(this).hasClass('active'))
        {
            $(this).addClass('active');
            $.ajax({
                url: '',
                type: 'POST',
                dataType: 'json',
                data: {
                    clear_log: 1,
                },
                beforeSend: function(){
                    $this.addClass('clearing');
                    $this.prop('disabled', true);
                    $this.val(ets_swn_trans['clearing']);
                },
                success: function(res){
                    if(res.success)
                        showSuccessMessage(res.success);
                    else
                        showErrorMessage(res.error);
                    $('.ets-am-clear-log').removeClass('active');
                    $('.ets_conjob_log').val('');
                },
                complete: function(){
                    $this.removeClass('clearing');
                    $this.prop('disabled', false);
                    $this.val(btn_html);
                },
                error: function () {
                    $('.ets-am-clear-log').removeClass('active');
                    alert('Can not clear log. Unknown error occured. You may have logged out');
                }
            });
        }
        return false;
    });
    $(document).on('click', '.ets-am-withdraw-action', function (event) {
        event.preventDefault();
        var button = this;
        var action = $(button).attr('data-action');
        var id = $(button).attr('data-id');
        var $this = $(this);
        $.ajax({
            url: '',
            type: 'POST',
            data: {
                table_action: action,
                id: id,
            },
            success: function(res){
                if(typeof res !== 'object'){
                    res = JSON.parse(res);
                }
                if(res.success){
                    showSuccessMessage(res.message);
                    var label_status = '';
                    if(action == 'DELETE'){
                        if($this.closest('tbody').find('tr').length == 1){
                            $this.closest('tbody').html('<tr><td colspan="100%" style="text-align: center;">'+ ets_swn_trans['no_data']+'</td></tr>');
                        }
                        else{
                            $this.closest('tr').remove();
                        }
                    }
                    else{
                        if(action == 'APPROVE'){
                            label_status = '<label class="label label-success">'+res.status+'</label>';
                        }
                        else if(action == 'DECLINE_RETURN' ||action == 'DECLINE_DEDUCT'){
                            label_status = '<label class="label label-default">'+res.status+'</label>';
                            if(action == 'DECLINE_RETURN')
                                $this.closest('tr').find('td:nth-child(6)').html($this.closest('tr').find('td:nth-child(6)').html()+' - Returned reward');
                            if(action == 'DECLINE_DEDUCT')
                                $this.closest('tr').find('td:nth-child(6)').html($this.closest('tr').find('td:nth-child(6)').html()+' - Deducted reward');
                        }
                        $this.closest('tr').find('td:nth-child(5)').html(label_status);

                        var btn_html = eamGenerateBtnGroup(res.actions);
                        $this.closest('td').html(btn_html);
                    }
                    
                }
                else{
                    showErrorMessage(res.message);
                }
            }
        });
    });
    if ($('form').length > 0) {
        //Init
        if($('input[name="ETS_AM_REF_FRIEND_ORDER_REQUIRED"]').length)
        {
            if($('input[name="ETS_AM_REF_FRIEND_ORDER_REQUIRED"]:checked').val()==1 && $('input[name="ETS_AM_REF_FRIEND_REG"]:checked').val()==1)
                $('.form-group.register').show();
            else
                $('.form-group.register').hide();
            $('input[name="ETS_AM_REF_FRIEND_ORDER_REQUIRED"],input[name="ETS_AM_REF_FRIEND_REG"]').click(function(){
                if($('input[name="ETS_AM_REF_FRIEND_ORDER_REQUIRED"]:checked').val()==1 && $('input[name="ETS_AM_REF_FRIEND_REG"]:checked').val()==1)
                    $('.form-group.register').show();
                else
                    $('.form-group.register').hide();
            });
        }
        $('#ETS_AM_LOYALTY_SPECIFIC_SEARCH').eamSearchProduct(ets_snw_link_ajax,'ets_am_results','.snw_products_added');
        $('#ETS_AM_LOYALTY_EXCLUDED_SEARCH').eamSearchProduct(ets_snw_link_ajax,'ets_am_results','.snw_products_added');
        $('#ETS_AM_AFF_SPECIFIC_PRODUCTS_SEARCH').eamSearchProduct(ets_snw_link_ajax,'ets_am_results','.snw_products_added');
        $('#ETS_AM_AFF_PRODUCTS_EXCLUDED_SEARCH').eamSearchProduct(ets_snw_link_ajax,'ets_am_results','.snw_products_added');
        $('#ETS_AM_REF_REGISTER_PRODUCTS_EXCLUDED_SEARCH').eamSearchProduct(ets_snw_link_ajax,'ets_am_results','.snw_products_added');
        $('#ETS_AM_REF_PRODUCTS_EXCLUDED_SEARCH').eamSearchProduct(ets_snw_link_ajax,'ets_am_results','.snw_products_added');
        $('.ets_am_datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });

        /*== EVENT FOR CHECKBOX CHECK ALL ==*/
        //init sate
        if ($('.ets-sn-admin input[type=checkbox][value=ALL]').is(':checked')) {
            name_input = $('.ets-sn-admin input[type=checkbox][value=ALL]').attr('name');
            $('.ets-sn-admin input[name="' + name_input + '"]').prop('checked', true).prop('disabled', true);
            $('.ets-sn-admin input[type=checkbox][value=ALL]').prop('disabled', false);
        }

        //Event on change
        $('.ets-sn-admin input[type=checkbox][value=ALL]').on('change', function (event) {
            name_input = $(this).attr('name');
            if ($(this).is(':checked')) {
                $('.ets-sn-admin input[name="' + name_input + '"]').prop('checked', true).prop('disabled', true);
                $(this).prop('disabled', false);
            }
            else {
                $('.ets-sn-admin input[name="' + name_input + '"]').prop('checked', false).prop('disabled', false);
            }
        });


        //Add level
        $('.ets-sn-admin .btn-add-level').click(function (event) {
            eam_curent_el_level = $(this).parent().prev('div').find('input[name^=ETS_AM_REF_SPONSOR_COST_LEVEL_]').first();
            eam_curent_level = eam_curent_el_level.length ?  eam_curent_el_level.attr('name').replace('ETS_AM_REF_SPONSOR_COST_LEVEL_', ''):1;
            eam_curent_level = parseInt(eam_curent_level);

            eam_next_level = eam_curent_level + 1;
            eam_el_level = eamRenderLevelInput(eam_next_level, '', ets_snw_suffix_level);
            $(this).parent().before(eam_el_level);
        });

        $(document).on('click', '.ets-sn-admin .btn-remove-level', function (event) {
            event.preventDefault();
            $(this).parent().nextAll('.input-level-append').remove();
            $(this).parent().remove();
        });

        //Get level inputs
        if ($('.ets-sn-admin .btn-add-level').length > 0) {
            $.ajax({
                url: '',
                type: 'POST',
                dateType: 'json',
                data: {
                    getLevelInput: true
                },
                success: function (res) {
                    if (typeof res !== 'object') {
                        res = JSON.parse(res);

                    }
                    if (res.success) {
                        for (var i = 0; i < res.data.length; i++) {
                            eam_level_el = eamRenderLevelInput(res.data[i].level, res.data[i].value, ets_snw_suffix_level);
                            $('.ets-sn-admin .btn-add-level').parent().before(eam_level_el);
                        }
                    }
                    if($('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]:checked').val()==0)
                    {
                        $('.input-level-append').hide();
                    }
                }
            })
        }

    }

    //Hide or show checbox tree
    $('.ets-sn-admin input[type=radio]').click(function (event) {
        if ($(this).is(':checked')) {
            if ($(this).hasClass('no-tree-option')) {
                tree = $(this).attr('data-tree');
                $(tree).hide();
                $(tree).find('input[type=checkbox]').prop('checked', false);
            }
            else if ($(this).hasClass('has-tree-option')) {
                tree = $(this).attr('data-tree');
                $(tree).show();
            }
        }
    });

    //Hide or show element depent on other element
    $('input[data-decide]').each(function (index, el) {
        etsAmToggleElDecide(el);
    });
    $('input[data-decide]').change(function (event) {
        etsAmToggleElDecide(this);
    });
    $(document).on('click', '.ets-sn-admin__tabs li.has_sub > a', function(e){
        e.preventDefault();
        if ( $(this).parent().hasClass('active_show') ){
            $(this).parent().removeClass('active_show');
        } else {
            $('.ets-sn-admin__tabs li').removeClass('active_show');
            $(this).parent().addClass('active_show');
        }

    });
    /* ==== Just for voucher form ===== */
    var ets_am_voucher_form_suffix = ['_FREE_SHIPPING', '_APPLY_DISCOUNT', '_REDUCTION_PERCENT', '_REDUCTION_AMOUNT',
        '_ID_CURRENCY', '_REDUCTION_TAX', '_APPLY_DISCOUNT_IN', '_DISCOUNT_PREFIX', '_DISCOUNT_DESC','_EXCLUDE_SPECIAL'];

    var ets_am_voucher_foramount = ['_REDUCTION_AMOUNT',
        '_ID_CURRENCY', '_REDUCTION_TAX'];
    //Event change
    if($('input[name$=_VOUCHER_TYPE]:checked').length)
    {
        $('input[name$=_VOUCHER_TYPE]:checked').each(function(){
            etsamToggleVoucherForm(this, ets_am_voucher_form_suffix);
        });
    }
    $('input[name$=_VOUCHER_TYPE]').change(function (event) {
        etsamToggleVoucherForm(this, ets_am_voucher_form_suffix);
    });
    if($('input[name$=_APPLY_DISCOUNT]:checked').length)
    {
        $('input[name$=_APPLY_DISCOUNT]:checked').each(function(){
            etsamToggleTypeDiscount(this, ets_am_voucher_foramount);
        });
    }
    $('input[name$=_APPLY_DISCOUNT]').change(function (event) {
        etsamToggleTypeDiscount(this, ets_am_voucher_foramount);
    });
    /* END Just for voucher form */
     eamToggleBoxState();

    eamSetLoyCate();
    $('.ets-sn-admin input[name="ETS_AM_LOYALTY_CATEGORIES[]"]').change(function (event) {
        eamSetLoyCate();
    });

    $('.ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_FROM], .ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_TO]').change(function (event) {
        eamValidateDate('.ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_FROM]', '.ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_TO]');
    });

    $('.ets-sn-admin form').submit(function (event) {
        if ($('.ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_FROM]').length > 0) {
            eamValidateDate('.ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_FROM]', '.ets-sn-admin input[name=ETS_AM_LOYALTY_TIME_TO]');
        }
    });

    $('.ets-sn-admin').on('click', '.js-add-payment-method-field', function (event) {
        event.preventDefault();
        $this = $(this);
        if (typeof eam_languages !== 'undefined' && eam_languages) {
            eamRenderFieldsMethodPayment(this, eam_languages, eam_currency);
        }
        else {
            $.ajax({
                url: '',
                type: 'GET',
                data: {
                    'getLanguage': true,
                },
                success: function (res) {
                    if (typeof res !== 'object') {
                        res = JSON.parse(res);
                    }
                    eam_languages = res.languages;
                    eamRenderFieldsMethodPayment($this, res.languages);
                }
            })
        }
    });
    $(document).on('click','input[name="ETS_AM_CRONJOB_TOKEN_SAVE"]',function(){
        if(!$(this).hasClass('active'))
        {
            $(this).addClass('active');
            $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        updateCronjobSecureCode: 1,
                        secure_code: $('input[name="ETS_AM_CRONJOB_TOKEN"]').val(),
                    },
                    dataType: 'json',
                    success: function(res){
                        if(res.success){
                            $('.ets-sn-admin .eam-cronjob-secure-value').html($('input[name="ETS_AM_CRONJOB_TOKEN"]').val());
                            $('.js-eam-test-cronjob').attr('data-secure',res.secure);
                            showSuccessMessage(res.message);
                        }
                        else{
                            showErrorMessage(res.message);
                        }
                        $('input[name="ETS_AM_CRONJOB_TOKEN_SAVE"]').removeClass('active');
                    },
                    error: function()
                    {
                        showErrorMessage('Unknown error happens');
                        $('input[name="ETS_AM_CRONJOB_TOKEN_SAVE"]').removeClass('active');
                    },
                });
        }
        return false;
    });
    $(document).on('click', '.js-eam-close-cronjob-alert', function(event) {
        var $this = $(this);
        $.ajax({
            url: ets_snw_link_ajax,
            type: 'POST',
            data: {
                close_cronjob_alert: 1,
            },
            beforeSend: function(){
                $this.closest('.panel-cronjob-alert').remove();
            },
            success: function(res){
                //
            }
        })
    });

    $('.ets-sn-admin .js-eam-test-cronjob').click(function(event) {
        if(!$(this).hasClass('active'))
        {
            $(this).addClass('active');
            $.ajax({
                url: $(this).attr('href'),
                type: 'post',
                data: {
                    ajax: 1,
                    etsAmRunCronjob: 1,
                },
                success: function(txt)
                {
                    showSuccessMessage(txt);
                    $('.ets-sn-admin .js-eam-test-cronjob').removeClass('active');
                },
                error: function()
                {
                    showErrorMessage('Unkown error happended. Please try again.');
                    $('.ets-sn-admin .js-eam-test-cronjob').removeClass('active');
                },
            });
        }
        return false;
    });

    $('.ets-sn-admin').on('click', '.js-btn-delete-field', function (event) { 
        event.preventDefault();
        if(confirm(ets_swn_trans['confirm_delete'])){
            $(this).closest('.payment-method-field').remove();
        }
    });

    eamToggleFeePayment($('.ets-sn-admin .payment_method_fee_type'));
    $('.ets-sn-admin').on('change', '.payment_method_fee_type', function (event) {
        eamToggleFeePayment(this);
    });
    $('#eam_method_fields_append').on('mouseover', '.switch', function(event) {
        event.preventDefault();
        $('#eam_method_fields_append').sortable('disable');
        $(".sect-hot-links-inner").enableSelection(); 
    });
    $('#eam_method_fields_append').on('mouseout', '.switch', function(event) {
        event.preventDefault();
        $( "#eam_method_fields_append" ).disableSelection();
        $('#eam_method_fields_append').sortable('enable');
    });
    var eam_xhr_ajax_sortable_pm;
    //Sort payment method field
    if($('#eam_method_fields_append').length)
    {
        $( "#eam_method_fields_append" ).disableSelection();
        $('#eam_method_fields_append').sortable({
            update: function(e, ui) {
                var sort_data = [];
                $('#eam_method_fields_append .payment-method-field').each(function(index, el) {
                    sort_data.push($(this).attr('data-id'));
                });
                if(eam_xhr_ajax_sortable_pm && eam_xhr_ajax_sortable_pm.readyState != 4){
                    eam_xhr_ajax_sortable_pm.abort();
                }
                eam_xhr_ajax_sortable_pm = $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        sort_data: sort_data,
                        sortPaymentMethodField: true
                    },
                    success: function(res){
                        if(typeof res !=='object'){
                            res = JSON.parse(res);
                        }
                        if(res.success){
                            showSuccessMessage(res.message)
                        }
                        else{
                            showErrorMessage(res.message);
                        }
                    }
                });
            }
        });
    }

     $(document).on('click','.ets-sn-admin .js-action-user-reward',function(event) {
        var $this = $(this);
        var id_user_reward = $(this).attr('data-id');
        var action_user_reward = $(this).attr('data-action');
        if(action_user_reward == 'decline'){
            if(!confirm(ets_swn_trans['confirm_suspend_user'])){
                return false;
            }
        }
        $.ajax({
            url: '',
            type: 'POST',
            data: {
                actionUserReward: true,
                id_user_reward: id_user_reward,
                action_user_reward: action_user_reward
            },
            beforeSend: function(){
                $this.prop('disabled', true);
            },
            success: function(res){
                if(typeof res !== 'object'){
                    res = JSON.parse(res);
                }
                if(res.success){
                    showSuccessMessage(res.message);
                    var label_status = '';
                    var label_status_view = '';
                    if(action_user_reward == 'active'){
                        label_status = '<span class="label label-success">'+res.status+'</span>';
                        label_status_view = '<span class="label label-success"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg></i> '+res.status+'</span>';
                    }
                    else if(action_user_reward == 'decline'){
                        label_status = '<span class="label label-default">'+res.status+'</span>';
                        label_status_view = '<span class="label label-default"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg></i> '+res.status+'</span>';
                    }
                    if(eamGetParamsUrl(window.location.href, 'id_reward_users')){
                        window.location.reload();
                    }
                    else{
                        
                        $this.closest('tr').find('td:nth-child(10)').html(label_status);
                        var btn_html = eamGenerateBtnGroup(res.actions);
                        $this.closest('td').html(btn_html);
                    }
                }
                else{
                    showErrorMessage(res.message);
                }
            },
            complete: function(){
                $this.prop('disabled', false);
            }
        });
     });

});

$(document).ready(function () {
    menuheaderheight();
    $(window).resize(function(){
        menuheaderheight();
    });
    $(window).load(function(){
        menuheaderheight();
    });
    $('.ets-sn-admin').find('input').attr('autocomplete','off');
$('.ets-sn-admin__tabs').removeClass('eam_scroll_heading');
if($('.ets-sn-admin').length > 0){
    $('body').addClass('amp-affmarketing');
    if($('.amp-affmarketing .bootstrap > .module_confirmation.alert').length){
        $('.ets-sn-admin__tabs').after($('.amp-affmarketing .bootstrap > .module_confirmation.alert').clone());
    }
    else if($('.amp-affmarketing .bootstrap > .module_error.alert').length){
        $('.ets-sn-admin__tabs').after($('.amp-affmarketing .bootstrap > .module_error.alert').clone());
    }
    setTimeout(function(){
       $('.ets-sn-admin .module_confirmation.alert').addClass('amb-hide-alert');
    },5000);
}
  
    $(function () {
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });
         
    });
    $('.ets-sn-admin').on('click', '.js-eam-update-status-program-user', function(event) {
        event.preventDefault();
        var id_user = $(this).attr('data-id');
        var program = $(this).attr('data-program');
        var action = $(this).attr('data-status');
        var $this = $(this);
        var confirm_message = '';
        switch (action) {
            case 'approve':
                confirm_message = ets_swn_trans['confirm_approve_program'];
                break; 
            case 'suspend':
                confirm_message = ets_swn_trans['confirm_suspend_program'];
                break;
            case 'decline':
                confirm_message = ets_swn_trans['confirm_decline_program'];
                break;
        }
        switch (program) {
            case 'loy':
                confirm_message += ' ' +ets_swn_trans['loyalty_program'];
                break; 
            case 'ref':
                confirm_message += ' ' +ets_swn_trans['referral_program'];
                break;
            case 'aff':
                confirm_message += ' ' +ets_swn_trans['affiliate_program'];
                break;
        }
        confirm_message += '?';
        if(!confirm(confirm_message)){
            return false;
        }
        var popup_action = 'no_action';
        if($('#modalReasonAproveApp').length > 0 && action == 'approve'){
            popup_action = 'approve';
        }
        else if($('#modalReasonDeclineApp').length > 0 && action == 'decline'){
            popup_action = 'decline';
        }
        if(action == popup_action){
            var modal = '#modalReasonDeclineApp';
            var btn_submit = '#submit_reason_decline';
            if(action == 'approve'){
                modal = '#modalReasonAproveApp';
                btn_submit = '#submit_reason_approve';
            }

            $(modal).modal('show');
            setTimeout(function(){
                $(modal+' textarea[name=reason]').focus();
            }, 500);
            $(btn_submit).click(function(event) {
                var reason_decline = $(modal+' textarea[name=reason]').val();
                $(modal).modal('hide');
                if (typeof id_user !== 'undefined') {
                    $.ajax({
                        url: ets_snw_link_ajax,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id_user: parseInt(id_user),
                            program: program,
                            action_user: action,
                            actionProgramUser: true,
                            reason: reason_decline
                        },
                        beforeSend: function () {
                            $this.parent().find('button').addClass('loading');
                        },
                        success: function (res) {
                            if (typeof res.success !== 'undefined' && res.success) {
                                showSuccessMessage(res.message);
                                window.location.reload();
                            }
                            else {
                                showErrorMessage(res.message);
                            }
                        },
                        complete: function () {
                            $this.parent().find('button').removeClass('loading');
                        }
                    });
                }
            });
            return false;
        }
        if(action == 'delete'){
            if(!confirm(ets_swn_trans['confirm_delete_application'])){
                return false;
            }
        }
        if (typeof id_user !== 'undefined') {
            $.ajax({
                url: ets_snw_link_ajax,
                type: 'POST',
                dataType: 'json',
                data: {
                    id_user: parseInt(id_user),
                    program: program,
                    action_user: action,
                    actionProgramUser: true,
                },
                beforeSend: function () {
                    $this.parent().find('button').prop('disabled', true);
                },
                success: function (res) {
                    if (typeof res.success !== 'undefined' && res.success) {
                        showSuccessMessage(res.message);
                        window.location.reload();
                    }
                    else {
                        showErrorMessage(res.message);
                    }
                },
                complete: function () {
                    $this.parent().find('button').prop('disabled', false);
                }
            });
        }
    });
    $(document).on('click','#submit_reason_approve',function(e){
        if($('.ets-sn-admin  .js-btn-action-app.active').length>0)
        {
            var $this=$('.ets-sn-admin  .js-btn-action-app.active');
            var id = $('.ets-sn-admin  .js-btn-action-app.active').attr('data-id');
            var action ='approve';
            var reason_decline = $('#modalReasonAproveApp textarea[name=reason]').val();
            $('#modalReasonAproveApp').modal('hide');
            $('#modalReasonAproveApp').on('hidden.bs.modal', function (e) {
              $('.ets-sn-admin__tabs').removeClass('show_modal');
            });
            if (typeof id !== 'undefined') {
                $.ajax({
                    url: '',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_approve: parseInt(id),
                        action_user: action,
                        actionApplication: true,
                        reason: reason_decline
                    },
                    beforeSend: function () {
                        $this.parent().find('button').prop('disabled', true);
                    },
                    success: function (res) {
                        if(typeof res !== 'object'){
                            res = JSON.parse(res);
                        }
                        if (typeof res.success !== 'undefined' && res.success) {
                            showSuccessMessage(res.message);
                            var label_status = '';
                            if(action == 'approve'){
                                label_status = '<span class="label label-success">'+res.status+'</span>';
                            }
                            else if(action == 'decline'){
                                label_status = '<span class="label label-default">'+res.status+'</span>';
                            }
    
                            if(eamGetParamsUrl(window.location.href, 'viewapp')){
                                if(action == 'delete' && res.redirect){
                                    window.location.href = res.redirect;
                                }
                                else{
                                    $this.closest('.eam-view-application').find('.js-eam-app-status').html(label_status);
                                    var btn_html = eamGenerateAppBtn(res.actions);
                                    $this.closest('.js-eam-app-btns').html(btn_html);
                                }
                            }
                            else{
                                if(action == 'delete'){
                                    if($this.closest('tbody').find('tr').length == 1){
                                        $this.closest('tbody').html('<tr><td colspan="100%" style="text-align: center;">'+ ets_swn_trans['no_data']+'</td></tr>');
                                    }
                                    else{
                                        $this.closest('tr').remove();
                                    }
                                }
                                else{
                                    
                                    $this.closest('tr').find('td:nth-child(4)').html(label_status);
                                    var btn_html = eamGenerateBtnGroup(res.actions);
                                    $this.closest('td').html(btn_html);
                                }
                            }
                            
                        }
                        else {
                            showErrorMessage(res.message);
                        }
                    },
                    complete: function () {
                        $this.parent().find('button').prop('disabled', false);
                    }
                });
            }
        }
        
    });
    $(document).on('click','#submit_reason_decline',function(e){
        if($('.ets-sn-admin  .js-btn-action-app.active').length>0)
        {
            var action ='decline';
            var $this=$('.ets-sn-admin  .js-btn-action-app.active');
            var id = $('.ets-sn-admin  .js-btn-action-app.active').attr('data-id');
            var reason_decline = $('#modalReasonDeclineApp textarea[name=reason]').val();
            $('#modalReasonDeclineApp').modal('hide');
            $('#modalReasonDeclineApp').on('hidden.bs.modal', function (e) {
              $('.ets-sn-admin__tabs').removeClass('show_modal');
            });
            if (typeof id !== 'undefined') {
                $.ajax({
                    url: ets_snw_link_ajax,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_approve: parseInt(id),
                        action_user: action,
                        actionApplication: true,
                        reason: reason_decline
                    },
                    beforeSend: function () {
                        $this.parent().find('button').prop('disabled', true);
                    },
                    success: function (res) {
                        if(typeof res !== 'object'){
                            res = JSON.parse(res);
                        }
                        if (typeof res.success !== 'undefined' && res.success) {
                            showSuccessMessage(res.message);
                            var label_status = '';
                            if(action == 'approve'){
                                label_status = '<span class="label label-success">'+res.status+'</span>';
                            }
                            else if(action == 'decline'){
                                label_status = '<span class="label label-default">'+res.status+'</span>';
                            }
    
                            if(eamGetParamsUrl(window.location.href, 'viewapp')){
                                if(action == 'delete' && res.redirect){
                                    window.location.href = res.redirect;
                                }
                                else{
                                    $this.closest('.eam-view-application').find('.js-eam-app-status').html(label_status);
                                    var btn_html = eamGenerateAppBtn(res.actions);
                                    $this.closest('.js-eam-app-btns').html(btn_html);
                                }
                            }
                            else{
                                if(action == 'delete'){
                                    if($this.closest('tbody').find('tr').length == 1){
                                        $this.closest('tbody').html('<tr><td colspan="100%" style="text-align: center;">'+ ets_swn_trans['no_data']+'</td></tr>');
                                    }
                                    else{
                                        $this.closest('tr').remove();
                                    }
                                }
                                else{
                                    
                                    $this.closest('tr').find('td:nth-child(4)').html(label_status);
                                    var btn_html = eamGenerateBtnGroup(res.actions);
                                    $this.closest('td').html(btn_html);
                                }
                            }
                            
                        }
                        else {
                            showErrorMessage(res.message);
                        }
                    },
                    complete: function () {
                        $this.parent().find('button').prop('disabled', false);
                    }
                });
            }
        }
    });
    $(document).on('click', '.ets-sn-admin  .js-btn-action-app', function (event) {
        event.preventDefault();
        var id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        $('.ets-sn-admin  .js-btn-action-app').removeClass('active');
        $(this).addClass('active');
        var $this = $(this);
        var popup_action = 'no_action';
        if($('#modalReasonAproveApp').length > 0 && action == 'approve'){
            popup_action = 'approve';
            $('#modalReasonAproveApp textarea').val('');
        }
        else if($('#modalReasonDeclineApp').length > 0 && action == 'decline'){
            popup_action = 'decline';
            $('#modalReasonDeclineApp textarea').val('');
        }
        
        if(action == popup_action){
            var modal = '#modalReasonDeclineApp';
            var btn_submit = '#submit_reason_decline';
            if(action == 'approve'){
                modal = '#modalReasonAproveApp';
                btn_submit = '#submit_reason_approve';
            }
            $('.ets-sn-admin__tabs').addClass('show_modal');
            $(modal).modal('show');
            setTimeout(function(){
                $(modal+' textarea[name=reason]').focus();
            }, 500);
            return false;
        }
        if(action == 'delete'){
            if(!confirm(ets_swn_trans['confirm_delete_application'])){
                return false;
            }
        }
        if (typeof id !== 'undefined') {
            $.ajax({
                url: '',
                type: 'POST',
                dataType: 'json',
                data: {
                    id_approve: parseInt(id),
                    action_user: action,
                    actionApplication: true
                },
                beforeSend: function () {
                    $this.parent().find('button').prop('disabled', true);
                },
                success: function (res) {
                    if(typeof res !== 'object'){
                        res = JSON.parse(res);
                    }
                    if (typeof res.success !== 'undefined' && res.success) {
                        showSuccessMessage(res.message);
                        
                        var label_status = '';
                        if(action == 'approve'){
                            label_status = '<span class="label label-success">'+res.status+'</span>';
                        }
                        else if(action == 'decline'){
                            label_status = '<span class="label label-default">'+res.status+'</span>';
                        }

                        if(eamGetParamsUrl(window.location.href, 'viewapp')){
                            if(action == 'delete' && res.redirect){
                                window.location.href = res.redirect;
                            }
                            else{
                                $this.closest('.eam-view-application').find('.js-eam-app-status').html(label_status);
                                var btn_html = eamGenerateAppBtn(res.actions);
                                $this.closest('.js-eam-app-btns').html(btn_html);
                            }
                        }
                        else{
                            if(action == 'delete'){
                                if($this.closest('tbody').find('tr').length == 1){
                                    $this.closest('tbody').html('<tr><td colspan="100%" style="text-align: center;">'+ ets_swn_trans['no_data']+'</td></tr>');
                                }
                                else{
                                    $this.closest('tr').remove();
                                }
                            }
                            else{
                                
                                $this.closest('tr').find('td:nth-child(4)').html(label_status);
                                var btn_html = eamGenerateBtnGroup(res.actions);
                                $this.closest('td').html(btn_html);
                            }
                        }
                    }
                    else {
                        showErrorMessage(res.message);
                    }
                },
                complete: function () {
                    $this.parent().find('button').prop('disabled', false);
                }
            });
        }
    });

    $(document).on('click', '.ets-sn-admin .ets-am-list-app .js-btn-delete-app', function (event) {
        event.preventDefault();
        id = $(this).attr('data-id');
        $this = $(this);
        $this = $(this);
        if (typeof id !== 'undefined') {
            if(!confirm(ets_swn_trans['confirm_delete'])){
                return;
            }
            $.ajax({
                url: ets_snw_link_ajax,
                type: 'POST',
                dataType: 'json',
                data: {
                    id_delete: parseInt(id),
                    delete_customer: true
                },
                beforeSend: function () {
                    $this.parent().find('button').prop('disabled', true);
                },
                success: function (res) {
                    if (typeof res.success !== 'undefined' && res.success) {
                        showSuccessMessage(res.message);
                        window.location.reload();
                    }
                    else {
                        showErrorMessage(res.message);
                    }
                },
                complete: function () {
                    $this.parent().find('button').prop('disabled', false);
                }
            });
        }
    });

    $('.ets-sn-admin .js-eam-filter-time-stats-reward').click(function(event) {
        $(this).closest('.dropdown-menu').prev().html($(this).html());
        if($(this).hasClass('show-time-ranger')){
            $(this).closest('.box-tool').find('.box-date-ranger').show();
        }
        else{
            $(this).closest('.box-tool').find('.box-date-ranger').hide();
            var filter_time_type = $(this).attr('data-value');
            var filter_time_from = $(this).closest('.box-tool').find('.date_from_reward').val();
            var filter_time_to = $(this).closest('.box-tool').find('.date_to_reward').val();
            if($(this).closest('.box-dashboard').hasClass('line-chart-reward')){
                eamAjaxChartStat({
                    get_stat_reward: 1,
                    filter_date_type: filter_time_type,
                    filter_date_from: filter_time_from,
                    filter_date_to: filter_time_to
                }, $(this).closest('.box-dashboard').prev('.stats-loading'));
            }
            if($(this).closest('.box-dashboard').hasClass('pie-chart-reward')){
                eamAjaxPieChartReward({
                    get_pie_chart_reward: 1,
                    filter_date_type: filter_time_type,
                    filter_date_from: filter_time_from,
                    filter_date_to: filter_time_to
                }, $(this).closest('.box-dashboard').prev('.stats-loading'));
            }
            
        }

    });


    /*============ CHART JS ================*/

    eam_url_vars = eamGetUrlVars();
    eam_data_stats_req = {
        get_stat_reward: true,
        tab_active: eam_url_vars.tabActive !== 'undefined' ? eam_url_vars.tabActive : '',
        filter_type_stats : $('.js-eam-dashboard input[name=type_stats]').val()
    };
    if(typeof eam_init_data_stats !== 'undefined' && typeof nv !== 'undefined'){
        var init_define_columns = [];
        $.each(eam_init_data_stats.data[0].values, function(index, el) {
            init_define_columns.push(el.x);
        });
        nv.addGraph(function() {
            eamLineChartReward = nv.models.lineChart()
                        .margin({left: 75, bottom: 50, top : 50, right : 5})
                        .useInteractiveGuideline(true)
                        .transitionDuration(350)
                        .showLegend(true)
                        .showYAxis(true)
                        .showXAxis(true)
          ;
          //eamLineChartReward.legend.margin({top: 288, right: 0, left: 0, bottom: 20})
          var axisLabel = eam_init_data_stats.x_asis == 'date'? ets_swn_chart_day : (eam_init_data_stats.x_asis == 'month'? ets_swn_chart_month : ets_swn_chart_year);
          eamLineChartReward.xAxis     //Chart x-axis settings
                .tickValues(init_define_columns)
                .axisLabel(axisLabel)
                .rotateLabels(eam_init_data_stats.count_values > 12? -45 : 0)
                .tickFormat(function(d) {
                    if(eam_init_data_stats.x_asis == 'date'){
                        
                        return parseInt(d3.time.format('%d')(new Date(d)));
                    }
                    else if(eam_init_data_stats.x_asis == 'month'){
                        
                        return parseInt(d3.time.format('%m')(new Date(d)));
                    }
                    else if(eam_init_data_stats.x_asis == 'year'){
                        return d3.time.format('%Y')(new Date(d))
                    }
                    return d;
                });
          eamLineChartReward.yAxis     //Chart y-axis settings
              .axisLabel(typeof eam_currency_code !== 'undefined' ? eam_currency_code : ets_swn_currency_code) //ets_swn_currency_code
              .tickFormat(d3.format('.02f'));

          eamLineChartRewardData = d3.select('#eam_stats_reward_line svg')    //Select the <svg> element you want to render the chart in.   
              .datum(eam_init_data_stats.data)         //Populate the <svg> element with chart data...
              .call(eamLineChartReward);          //Finally, render the chart!

          //Update the chart when window resizes.
          nv.utils.windowResize(eamLineChartReward.update);
          return eamLineChartReward;
        });

        //Pie chart

        nv.addGraph(function() {
          eamChartPieReward = nv.models.pieChart()
              .x(function(d) { return d.label })
              .y(function(d) { return d.value })
              .margin({left: 0, right: 0, top : 25, bottom : 0})
              .showLabels(true)
              .labelThreshold(.05)
              .pieLabelsOutside(false)
              .labelType("percent");
            eamChartPieReward.noData(ets_swn_trans['no_data']);
            eamChartPieRewardData = d3.select("#eam-pie-chart-reward svg")
                .datum(eam_init_data_pie)
                .transition().duration(350)
                .call(eamChartPieReward);
            nv.utils.windowResize(eamChartPieReward.update);
            eamChartPieReward.update();
            eamChartPieReward.tooltipContent(function(key, y, e, graph) {
                return '<h3>' + key + '</h3>' +
                       '<p>' +  y + ' '+(typeof eam_currency_code !== 'undefined' ? eam_currency_code : ets_swn_currency_code)+ '</p>';
            });
          return eamChartPieReward;
        });
    }
  
    /*============ END CHART JS ================*/
    if($('.stats-data-reward button[data-type=time_ranger]').hasClass('active')){
        $('.stats-data-reward .box-date-ranger').css('display', 'inline-block');
    }
    else{
        $('.stats-data-reward .box-date-ranger').hide();
    }

    $('.eam_date_ranger_filter').on('apply.daterangepicker', function(ev, picker) {
        $(this).closest('.box-date-ranger').find('.date_from_reward').val(picker.startDate.format('YYYY-MM-DD'));
        $(this).closest('.box-date-ranger').find('.date_to_reward').val(picker.endDate.format('YYYY-MM-DD'));
        if($(this).hasClass('ajax-date-range')){
            if($(this).closest('.box-dashboard').hasClass('line-chart-reward')){
                 eamAjaxChartStat({
                    get_stat_reward: 1,
                    filter_date_type: 'time_ranger',
                    filter_date_from: picker.startDate.format('YYYY-MM-DD'),
                    filter_date_to: picker.endDate.format('YYYY-MM-DD'),
                }, $(this).closest('.box-dashboard').prev('.stats-loading'));
            }
            if($(this).closest('.box-dashboard').hasClass('pie-chart-reward')){
                 eamAjaxPieChartReward({
                    get_pie_chart_reward: 1,
                    filter_date_type: 'time_ranger',
                    filter_date_from: picker.startDate.format('YYYY-MM-DD'),
                    filter_date_to: picker.endDate.format('YYYY-MM-DD'),
                }, $(this).closest('.box-dashboard').prev('.stats-loading'));
            }

           
        }
    });
    $('.eam_date_ranger_filter').on('hide.daterangepicker', function(ev, picker) {
        $(this).closest('.box-date-ranger').find('.date_from_reward').val(picker.startDate.format('YYYY-MM-DD'));
        $(this).closest('.box-date-ranger').find('.date_to_reward').val(picker.endDate.format('YYYY-MM-DD'));
    });

    $('.js-eam-dashboard .js-type-info-stats').click(function(event) {
        var type_stats = $(this).attr('data-type');
        var bg_stats = $(this).attr('data-bg');
        if(type_stats == 'reward'){
            $('.ets-sn-admin .filter-for-box-reward').show();
        }
        else{
             $('.ets-sn-admin .filter-for-box-reward').hide();
        }
        if(type_stats == 'orders'){
            $('.ets-sn-admin .filter-for-box-order').show();
        }
        else{
             $('.ets-sn-admin .filter-for-box-order').hide();
        }
        if(type_stats !== 'orders' && type_stats !== 'reward'){
            if(!$('.ets-sn-admin .filer-box-time').hasClass('col-lg-10')){
                $('.ets-sn-admin .filer-box-time').removeClass('col-lg-6');
                $('.ets-sn-admin .filer-box-time').addClass('col-lg-10');
            }   
        }
        else{
            $('.ets-sn-admin .filer-box-time').removeClass('col-lg-10');
             $('.ets-sn-admin .filer-box-time').addClass('col-lg-6');
        }
        if(type_stats){
            $('.js-eam-dashboard input[name=type_stats]').val(type_stats);
            $('.js-eam-dashboard .js-type-info-stats').each(function(index, el) {
                $(el).find('.box-inner').removeClass($(el).attr('data-bg'));
            });
            $(this).find('.box-inner').addClass(bg_stats);
            $('.stats-data-reward .js-btn-submit-filter').click();
        }
    });

    $('.stats-data-reward .js-btn-submit-filter').click(function (event) {
        from = $('.stats-data-reward .date_from_reward').val();
        to = $('.stats-data-reward .date_to_reward').val();
        if (from && to) {
            // First check for the pattern
            regex_date = /^\d{4}\-\d{1,2}\-\d{1,2}$/;

            if (!regex_date.test(from) || !regex_date.test(to)) {
                showErrorMessage('The format of date is invalid');
                return false;
            }
        }
        eam_data_stats_req = {
            get_stat_reward: true,
            tab_active: eam_url_vars.tabActive !== 'undefined' ? eam_url_vars.tabActive : '',
            program: $('.stats-data-reward select[name=program]').val()
        };

        eam_data_stats_req.filter_date_type = $('.stats-data-reward select[name=type_date_filter]').val();
        if($('.stats-data-reward select[name=type_date_filter]').val() == 'time_ranger'){
            eam_data_stats_req.filter_date_from = from;
            eam_data_stats_req.filter_date_to = to;
        }
        
        eam_data_stats_req.filter_status = '';
        eam_data_stats_req.filter_reward_status = '';
        eam_data_stats_req.filter_order_status = '';
        eam_data_stats_req.filter_type_stats =  $('.js-eam-dashboard input[name=type_stats]').val();
        if($('.stats-data-reward select[name=status]').val()){
            eam_data_stats_req.filter_status = $('.stats-data-reward select[name=status]').val();
        }
        if($('.stats-data-reward select[name=reward_status]').val()){
            eam_data_stats_req.filter_reward_status = $('.stats-data-reward select[name=reward_status]').val();
        }
        if($('.stats-data-reward select[name=order_status]').val()){
            eam_data_stats_req.filter_order_status = $('.stats-data-reward select[name=order_status]').val();
        }
        eamAjaxChartStat(eam_data_stats_req,'');
    });

    $('.stats-data-reward .js-btn-reset-filter').click(function (event) {
        $('.stats-data-reward .stat-filter input[type=text]').val('');
        $('.stats-data-reward .stat-filter input[name=id_customer]').val('');
        $('.stats-data-reward .stat-filter select option:first-child').prop('selected', 'selected');
        $(this).closest('form').submit();
        if($('#eam_stats_reward_line').length > 0){
            eam_data_stats_req = {
                get_stat_reward: true,
                tab_active: eam_url_vars.tabActive !== 'undefined' ? eam_url_vars.tabActive : ''
            };
            eam_data_stats_req.filter_type_stats =  $('.js-eam-dashboard input[name=type_stats]').val();
            eamAjaxChartStat(eam_data_stats_req,'');
        }
        
    });

    $('.eam-box-filter .js-btn-reset-form-filter').click(function(event) {
        $('.eam-box-filter input[type=text]').val('');
        $('.eam-box-filter select option:first-child').prop('selected', 'selected');
        $(this).closest('form').find('.box-date-ranger').hide();
        if($('#formFilterRankDashboard').length){
            $('#formFilterRankDashboard').submit();
        }
    });

    $('.stats-data-reward select[name=type_date_filter]').change(function(event) {
        if($(this).val() == 'time_ranger'){
            $(this).next('.box-date-ranger').css('display', 'inline-block');
        }
        else{
             $(this).next('.box-date-ranger').hide();
        }
    });

    $('.eam-box-filter select[name=type_date_filter]').change(function(event) {
        if($(this).val() == 'time_ranger'){
            $(this).next('.box-date-ranger').css('display', 'inline-block');
        }
        else{
             $(this).next('.box-date-ranger').hide();
        }
    });

    if($('.eam-box-filter select[name=type_date_filter]').val() == 'time_ranger') {
        $('.eam-box-filter select[name=type_date_filter]').closest('.eam-box-filter').find('.box-date-ranger').css('display', 'inline-block');
    } else{
         $('.eam-box-filter select[name=type_date_filter]').closest('.eam-box-filter').find('.box-date-ranger').hide();
    }

    if($('.stats-data-reward select[name=type_date_filter]').val() == 'time_ranger') {
        $('.stats-data-reward select[name=type_date_filter]').next('.box-date-ranger').css('display', 'inline-block');
    } else {
         $('.stats-data-reward select[name=type_date_filter]').next('.box-date-ranger').hide();
    }


    eamFilterDatatable('#eamFormFilterHistoryReward');
    $('.js-eam-page-item').click(function(event) {
        var current_uri = window.location.href;
        page = $(this).attr('data-page');
        if(page){
            current_uri = eamUpdateQueryStringParameter(current_uri, 'page', page);
            window.location.href = current_uri;
        }

    })

    var eamDate = new Date();
    if(typeof daterangepicker !== 'undefined'){
        $('.eam_date_ranger_filter').daterangepicker({
            locale: { 
                format: 'YYYY/MM/DD'
            },
            showDropdowns:true,
        });

        if(!$('.eam_date_ranger_filter').val() && $('.eam_date_ranger_filter').length > 0){
            $('.eam_date_ranger_filter').data('daterangepicker').setStartDate(moment(new Date(eamDate.getFullYear(), eamDate.getMonth(), 1)));
            $('.eam_date_ranger_filter').data('daterangepicker').setEndDate(moment(new Date(eamDate.getFullYear(), eamDate.getMonth() + 1, 0)));
        }
    }
    if(!eam_submit_error){
        eamToggleStateOptions('.js-eam-toggle-states-payment', 'hide');
    }
    $('.js-eam-toggle-states-payment').click(function(event) {
        if($(this).hasClass('states-hide')){
            $(this).removeClass('states-hide');
            $(this).addClass('states-show');
            $(this).find('i.fa').removeClass('fa-plus');
            $(this).find('i.fa').addClass('fa-minus');
            eamToggleStateOptions(this, 'show');
            eamToggleBoxState();
        }
        else if($(this).hasClass('states-show')){
            $(this).removeClass('states-show');
            $(this).addClass('states-hide');
            $(this).find('i.fa').removeClass('fa-minus');
            $(this).find('i.fa').addClass('fa-plus');
            eamToggleStateOptions(this, 'hide');
            eamToggleBoxState();
        }

    });

    eamToggleFields('input[name=ETS_AM_AFF_ALLOW_WITHDRAW]', ['input[name=ETS_AM_AFF_WITHDRAW_INVOICE_REQUIRED]']);
    eamToggleFields('input[name=ETS_AM_REF_FRIEND_REG]', ['input[name=ETS_AM_REF_FRIEND_EACH_REG_COST]', 'input[name=ETS_AM_REF_FRIEND_FIRST_REG_ONLY]', 'input[name=ETS_AM_REF_FRIEND_ORDER_REQUIRED]']);
    eamToggleFields('input[name=ETS_AM_REF_EMAIL_INVITE_FRIEND]', ['input[name=ETS_AM_REF_MAX_INVITATION]']);
    eamToggleFields('input[name=ETS_AM_AFF_ALLOW_BALANCE_TO_PAY]', ['input[name=ETS_AM_MIN_BALANCE_REQUIRED_FOR_ORDER]','input[name=ETS_AM_MAX_BALANCE_REQUIRED_FOR_ORDER]']);
    eamToggleFields('input[name=ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER]', ['input[name=ETS_AM_MIN_BALANCE_REQUIRED_FOR_VOUCHER]','input[name="ETS_AM_MAX_BALANCE_REQUIRED_FOR_VOUCHER"]','input[name="ETS_AM_AFF_ALLOW_VOUCHER_IN_CART"]','input[name="ETS_AM_CAN_USE_OTHER_VOUCHER"]','input[name="ETS_AM_VOUCHER_AVAILABILITY"]']);
    eamToggleFields('input[name=ETS_AM_AFF_ALLOW_WITHDRAW]', ['input[name=ETS_AM_MIN_BALANCE_REQUIRED_FOR_WITHDRAW]','input[name=ETS_AM_MAX_WITHDRAW]', 'input[ETS_AM_AFF_WITHDRAW_INVOICE_REQUIREDETS_AM_AFF_WITHDRAW_ONE_ONLY', 'input[name=ETS_AM_AFF_WITHDRAW_ONE_ONLY]', 'input[name=ETS_AM_ALLOW_WITHDRAW_LOYALTY_REWARDS]']);
    eamToggleFields('input[name=ETS_AM_REF_GIVE_REWARD_ON_ORDER]', ['input[name=ETS_AM_REF_REWARD_FRIEND_LIMIT]','input[name=ETS_AM_REF_REWARD_ORDER_LIMIT]', 'input[name=ETS_AM_REF_REWARD_ORDER_MIN]', 'input[name=ETS_AM_REF_HOW_TO_CALCULATE', 
        'input[name=ETS_AM_REF_SPONSOR_COST_PERCENT]','input[name=ETS_AM_REF_TAX_EXCLUDED]', 'input[name^=ETS_AM_REF_SPONSOR_COST_LEVEL_]','.input-level-append','input[name=ETS_AM_REF_SPONSOR_COST_LEVEL_LOWER]', 'input[name=ETS_AM_REF_SPONSOR_COST_REST_TO_FIRST]', 'button[name=ETS_AM_ADD_LEVEL]', 'input[name=ETS_AM_REF_SPONSOR_COST_FIXED]','input[name="ETS_AM_REF_CATEGORIES[]"]','input[name="ETS_AM_REF_PRODUCTS_EXCLUDED"]','input[name="ETS_AM_REF_PRODUCTS_EXCLUDED_DISCOUNT"]','input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]']);
    eamToggleFields('input[name=ETS_AM_TERM_AND_COND_REQUIRED]', ['input[name^=ETS_AM_TERM_AND_COND_URL]']);
    eamToggleFields('input[name=ETS_AM_LOYALTY_REGISTER]', ['input[name=ETS_AM_LOY_INTRO_REG]']);
    eamToggleFields('input[name=ETS_AM_REF_REGISTER_REQUIRED]', ['input[name=ETS_AM_REF_INTRO_REG]']);
    eamToggleFields('input[name=ETS_AM_AFF_REGISTER_REQUIRED]', ['input[name=ETS_AM_AFF_INTRO_REG]']);
    eamToggleFields('input[name=ETS_AM_RESIZE_BANNER]', ['input[name=ETS_AM_RESIZE_BANNER_WITH]','input[name=ETS_AM_RESIZE_BANNER_HEIGHT]']);
    eamToggleFields('input[name=ETS_AM_LOYALTY_EMAIL_GOING_EXPIRED]', ['input[name=ETS_AM_LOYALTY_EMAIL_EXPIRED_DAY]']);
    //Load pending applications to apps
    $.ajax({
        url: ets_snw_link_ajax,
        type: 'GET',
        data: {
            getTotalUserAppPending: true,
        },
        success: function(res){
            if(typeof res !== 'object'){
                res = JSON.parse(res);
            }
            if(res.success && res.total){
                if(!$('#subtab-AdminEtsAmApp').find('.count-pending').length){
                    $('#subtab-AdminEtsAmApp a').append('<span class="count-pending">'+res.total+'</span>');
                }
                else{
                    $('#subtab-AdminEtsAmApp .count-pending').html(res.total);
                }

                if($('#eam-count-app-admin').length > 0){
                    $('#eam-count-app-admin').html('('+res.total+')');
                }
            }
        },
        error: function(){

        }
    })


   if($('input[name=ETS_AM_LOYALTY_AMOUNT_PER]').closest('.form-group').is(':visible')){
        $('input[name=ETS_AM_LOYALTY_AMOUNT]').closest('.eam-input-loyalty-amount').addClass('split-field');
   }

   $('input[name=ETS_AM_LOYALTY_BASE_ON]').change(function(event) {
        if($('input[name=ETS_AM_LOYALTY_AMOUNT_PER]').closest('.form-group').is(':visible')){
            $('input[name=ETS_AM_LOYALTY_AMOUNT]').closest('.eam-input-loyalty-amount').addClass('split-field');
       }
       else{
            $('input[name=ETS_AM_LOYALTY_AMOUNT]').closest('.eam-input-loyalty-amount').removeClass('split-field');
       }
   });

   $('#formFilterRankDashboard').submit(function(event) {
        var data_filter = $('#formFilterRankDashboard').serializeArray();
        var tab_active = $('#nav-tab-rank').find('li.active a').attr('href');
        eamLoadAjaxDataDashboard(tab_active, 1, data_filter);
   });
   $('.js-eam-dashboard #nav-tab-rank a').click(function(event) {
        var tab_active = $(this).attr('href');
        if(tab_active !== '#recent_orders' && tab_active !== '#best_seller'){
             $('#formFilterRankDashboard select[name=reward_status]').closest('.eam_select_filter').show();
        }
        else{
            $('#formFilterRankDashboard select[name=reward_status]').closest('.eam_select_filter').hide();
        }
        $('#formFilterRankDashboard').find('select option:first-child').prop('selected', true);
        $('#formFilterRankDashboard').find('.box-date-ranger').hide();
        $('#formFilterRankDashboard').find('input[type=hidden]').val('');
        eamLoadAjaxDataDashboard(tab_active,1,[]);
   });

   $('.js-eam-dashboard').on('click', '.js-paginate-dashboard-tab', function(event) {
        event.preventDefault();
        var data_filter = $('#formFilterRankDashboard').serializeArray();
        var tab_active = $('#nav-tab-rank').find('li.active a').attr('href');
        var current_page = $(this).attr('data-page');
        eamLoadAjaxDataDashboard(tab_active, parseInt(current_page), data_filter);
   });
   eamActionRewardItem('approve');
   eamActionRewardItem('delete');
   eamActionRewardItem('cancel');

   eamActionRewardUsageItem('approve');
   eamActionRewardUsageItem('delete');
   eamActionRewardUsageItem('cancel');
   eamSortTableRewardUser();
   eamFilterRewardUser();
   eamLoadmoreData();
   $('.ets-sn-admin button[name=submitResetreward_users]').click(function(event) {
       $(this).closest('form').find('input[type=text], select').val('');
   });
   $('.ets-sn-admin #eamFormActionRewardUser select[name=action]').change(function(event) {
        var action_val = $(this).val();
        $('.ets-sn-admin #eamFormActionRewardUser button[type=submit]').hide();
        $('.ets-sn-admin #eamFormActionRewardUser button[type=submit][name='+action_val+'_reward_by_admin]').show();
        if(action_val=='deduct')
            $('#eamFormActionRewardUser textarea[name="reason"]').val(reason_deducted);
        else
            $('#eamFormActionRewardUser textarea[name="reason"]').val(reason_add);
   });
    $('.ets-sn-admin .payment-setting .list-pm .eam-active-sortable, .ets-sn-admin .payment-setting .list-pm .eam-active-sortable i, .ets-sn-admin .payment-setting .list-pm .eam-active-sortable span').hover(function(event) {
       $('.ets-sn-admin .payment-setting .list-pm').disableSelection();
        $('.ets-sn-admin .payment-setting .list-pm').sortable('enable');
    });

    $('.ets-sn-admin .payment-setting .list-pm .eam-active-sortable').mouseout(function(event) {
       
        $('.ets-sn-admin .payment-setting .list-pm').sortable('disable');
        $('.ets-sn-admin .payment-setting .list-pm').enableSelection();
    });
    if($('.ets-sn-admin .payment-setting .list-pm').length)
    {
        $('.ets-sn-admin .payment-setting .list-pm').addClass('ui-sortable ui-sortable-disabled');
        $('.ets-sn-admin .payment-setting .list-pm').sortable({
            update: function(e, ui){
                var sort_data = [];
                $('.ets-sn-admin .payment-setting .list-pm tr').each(function(index, el) {
                    sort_data.push($(this).attr('data-id'));
                });
                if(eam_xhr_ajax_sortable_pmm && eam_xhr_ajax_sortable_pmm.readyState != 4){
                    eam_xhr_ajax_sortable_pmm.abort();
                }
                eam_xhr_ajax_sortable_pmm = $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        sort_data: sort_data,
                        sortPaymentMethod: true
                    },
                    success: function(res){
                        if(typeof res !=='object'){
                            res = JSON.parse(res);
                        }
                        if(res.success){
                            showSuccessMessage(res.message);
                            $('.ets-sn-admin .payment-setting .list-pm tr').each(function(index, el) {
                                $(el).find('.sort-order').html(index+1);
                            });
                        }
                        else{
                            showErrorMessage(res.message);
                        }
                    }
                });
            }
        });
    }
    $('.ets-sn-admin .filter_limit select[name=limit]').change(function(event) {
        var current_url = window.location.href;
        var new_url = eamUpdateQueryStringParameter(current_url, 'limit', $(this).val());
        window.location.href = new_url;
    });

    $('input[name=ETS_AM_REWARD_DISPLAY]').change(function(event) {
        if($(this).val() == 'money'){
            if( $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').find('.translatable-field').length > 0){
                $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').parent().parent().hide();
            }
            else{
                $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').hide();
            }
            $('input[name=ETS_AM_CONVERSION]').closest('.form-group').hide();
            $('input[name=ETS_AM_REWARD_DISPLAY_BO]').first().closest('.form-group').hide();
        }
        else{
            if( $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').find('.translatable-field').length > 0){
                $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').parent().parent().show();
            }
            else{
                $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').show();
            }
            $('input[name=ETS_AM_CONVERSION]').closest('.form-group').show();
            $('input[name=ETS_AM_REWARD_DISPLAY_BO]').first().closest('.form-group').show();
        }
    });

    if($('input[name=ETS_AM_REWARD_DISPLAY]:checked').val() == 'point'){
        if( $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').find('.translatable-field').length > 0){
            $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').parent().parent().show();
        }
        else{
            $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').show();
        }
        $('input[name=ETS_AM_CONVERSION]').closest('.form-group').show();
        $('input[name=ETS_AM_REWARD_DISPLAY_BO]').first().closest('.form-group').show();
    }
    else{
        if( $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').find('.translatable-field').length > 0){
            $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').parent().parent().hide();
        }
        else{
            $('input[name^=ETS_AM_REWARD_UNIT_LABEL]').closest('.form-group').hide();
        }
        $('input[name=ETS_AM_CONVERSION]').closest('.form-group').hide();
        $('input[name=ETS_AM_REWARD_DISPLAY_BO]').first().closest('.form-group').hide();
    }

    $('.ets-sn-admin .payment-method-field .collapse').on('show.bs.collapse', function() {
        var minus = '<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-768q-26 0-45 19t-19 45v128q0 26 19 45t45 19h768q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>';
        $(this).prev('a').find('i').attr('class', 'minus-circle pull-right').html(minus);
    });
    $('.ets-sn-admin .payment-method-field .collapse').on('hide.bs.collapse', function() {
        var plus = '<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>'
        $(this).prev('a').find('i').attr('class', 'plus-circle pull-right').html(plus);
    });

    //Validate form edit payment method
    $('#eamFormEditPaymentMethod').submit(function(event) {
        var error_validate = [];
        var title_fill = 0;
        $('#eamFormEditPaymentMethod input[name^=payment_method_name]').each(function(index, el) {
            if($(el).val().trim()){
                title_fill = 1;
            }
        });
        if(!title_fill){
            error_validate.push($('#eamFormEditPaymentMethod input[name^=payment_method_name]').first().attr('data-error'));
        }

        if($('#eamFormEditPaymentMethod .payment_method_fee_type').val() == 'FIXED'){
            if(!$('#eamFormEditPaymentMethod .payment_method_fee_fixed').val()){
                error_validate.push(ets_swn_trans['pm_fee_fixed_required']);
            }
        }
        else if($('#eamFormEditPaymentMethod .payment_method_fee_type').val() == 'PERCENT'){
            if(!$('#eamFormEditPaymentMethod .payment_method_fee_percent').val()){
                error_validate.push(ets_swn_trans['pm_fee_percent_required']);
            }
        }
        if(error_validate.length > 0){
            var alert_error = '<div class="bootstrap eam-alert-error">';
                alert_error += '<div class="module_error alert alert-danger" style="display: block;">';
                alert_error += '<button type="button" class="close" data-dismiss="alert">×</button>';
            alert_error += '<ul>';
                $.each(error_validate,function(index, el) {
                    alert_error += '<li>'+el+'</li>';
                });
                
             alert_error += '</ul>';
            alert_error += '</div></div>';
            $('.ets-sn-admin').parent().find('.eam-alert-error').remove();
            $('.ets-sn-admin').parent().find('.module_confirmation').parent('.bootstrap').html('');
            $('.ets-sn-admin .ets-sn-admin__content').prepend(alert_error);
            return false;
        }

        return true;
       
    });

    $('#eamFormImportExport input[name=restore_reward]').change(function(event) {
        if($(this).is(':checked')){
            $('#eamFormImportExport input[name=delete_reward]').closest('.form-group').show();
        }
        else{
            $('#eamFormImportExport input[name=delete_reward]').closest('.form-group').hide();
            $('#eamFormImportExport input[name=delete_reward]').prop('checked', false);
        }
    });
    if($('#eamFormImportExport input[name=restore_reward]').is(':checked')){
        $('#eamFormImportExport input[name=delete_reward]').closest('.form-group').show();
    }
    else{
        $('#eamFormImportExport input[name=delete_reward]').closest('.form-group').hide();
        $('#eamFormImportExport input[name=delete_reward]').prop('checked', false);
    }


    $('.ets-sn-admin .input-search-suggestion').keyup(function(event) {
        var $this = $(this);
        var search_val = $(this).val();
        var search_type = $(this).closest('.filter_search').find('.data-suggestion').attr('data-type');
        var suggest = false;
        if(!isNaN(search_val)){
            suggest = true;
        }
        else if(isNaN(search_val) && search_val.length >= 3){
            suggest = true;
        }
        if(suggest){
            if(eam_xhr_ajax_search_suggestion && eam_xhr_ajax_search_suggestion.readyState != 4){
                eam_xhr_ajax_search_suggestion.abort();
            }
            eam_xhr_ajax_search_suggestion = $.ajax({
                url: '',
                type: 'GET',
                data: {
                    searchSuggestion: true,
                    query: search_val,
                    query_type: search_type,
                },
                success: function(res){
                    if(typeof res !== 'object'){
                        res = JSON.parse(res);
                    }
                    if(res.success){
                        $this.next('.data-suggestion').html(res.html);             
                    }
                },

            });
        }
    });

    $('.ets-sn-admin').on('click', '.js-accept-result-search', function(event) {
        event.preventDefault();
        var type_result = $(this).closest('.data-suggestion').attr('data-type');
        var id_result = $(this).attr('data-id');
        var text_result = $(this).attr('data-item');
        
        $(this).closest('.data-suggestion').next('input[name=id_customer]').val(id_result);
        if(text_result.length > 30){
            text_result = text_result.substring(0, 29)+'...';
        }
        $(this).closest('.filter_search').find('input[name=search]').val(text_result);
        window.location.href = eamUpdateQueryStringParameter(window.location.href, 'page', 1);
        var params = $(this).closest('form').serializeArray();
        var filter_url = window.location.href;
        $.each(params, function(index, el) {
            filter_url = eamUpdateQueryStringParameter(filter_url, el.name, el.value);
        });
        filter_url = eamUpdateQueryStringParameter(filter_url, 'page', 1);
        window.location.href = filter_url;
    });
     $('.ets-sn-admin').on('click', '.tag-query-search .remove-tag', function(event) {
         event.preventDefault();
         var form_submit = $(this).closest('form');
         $(this).closest('.filter_search').find('input[name=search]').val('');
         form_submit.find('input[name=id_customer]').val('');
         window.location.href = eamUpdateQueryStringParameter(window.location.href, 'page', 1);
        var params = $(this).closest('form').serializeArray();
        var filter_url = window.location.href;
        $.each(params, function(index, el) {
            filter_url = eamUpdateQueryStringParameter(filter_url, el.name, el.value);
        });
         $(this).parent().remove();
        filter_url = eamUpdateQueryStringParameter(filter_url, 'page', 1);
        window.location.href = filter_url;
     });
    eamConfirmWithdrawal();

    $('#form-reward_users .filtercenter').removeAttr('onchange');
    $('.ets-sn-admin .eam-link-no-action').closest('.dropdown-menu').remove();
    /*$('.ets-sn-admin .resize_banner_width').closest('.form-group').css({'width': '50%','float': 'left'});
    $('.ets-sn-admin .resize_banner_height').closest('.form-group').css({'width': '50%','float': 'left'});*/
    $('.ets-sn-admin .resize_banner_width').closest('.form-group').find('label').removeClass('col-lg-3');
    $('.ets-sn-admin .resize_banner_width').closest('.form-group').find('label').addClass('col-lg-6');
    $('.ets-sn-admin .resize_banner_height').closest('.form-group').find('label').removeClass('col-lg-3');
    $('.ets-sn-admin .resize_banner_height').closest('.form-group').find('label').addClass('col-lg-6');
    eamConfigLoyCalculate();

    $('.ets-sn-admin input[name=ETS_AM_LOYALTY_BASE_ON]').change(function(event) {
        eamConfigLoyCalculate();
    });
    
    $('.ets-sn-admin .js-eam-btn-delete-file').click(function(event) {
        if(confirm(ets_swn_trans['confirm_delete_photo'])){
            var name_config = $(this).attr('data-name');
            var $this = $(this);
            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    deletefileBackend: true,
                    name_config: name_config
                },
                success: function(res){
                    if(typeof res !== 'object'){
                        res = JSON.parse(res);
                    }
                    if(res.success){
                        showSuccessMessage(res.message);
                        $this.prev('img').remove();
                        
                        $this.closest('form').submit();
                        $this.remove();
                    }
                    else{
                        showErrorMessage(res.message);
                    }
                }
            })
        }
    });

    if($('input[name=ETS_AM_REF_HOW_TO_CALCULATE]:checked').val() == 'PERCENTATE'){
        $('input[name=ETS_AM_REF_SPONSOR_COST_PERCENT]').closest('.form-group').show();
        $('input[name=ETS_AM_REF_TAX_EXCLUDED]').first().closest('.form-group').show();
        $('input[name=ETS_AM_REF_SPONSOR_COST_FIXED]').first().closest('.form-group').hide();
    }
    else{
        $('input[name=ETS_AM_REF_SPONSOR_COST_PERCENT]').closest('.form-group').hide();
        $('input[name=ETS_AM_REF_TAX_EXCLUDED]').first().closest('.form-group').hide();
        $('input[name=ETS_AM_REF_SPONSOR_COST_FIXED]').first().closest('.form-group').show();
    }

    /* ADD NEW JS */
    if($('input[name=ETS_AM_REF_GIVE_REWARD_ON_ORDER][value=0]').is(':checked')){
        $('input[name=ETS_AM_REF_SPONSOR_COST_PERCENT]').closest('.form-group').hide();
        $('input[name=ETS_AM_REF_TAX_EXCLUDED]').first().closest('.form-group').hide();
    }
    /* ADD NEW JS */
});
function etsAmToggleElDecide(input) {
    if ($(input).attr('type') == 'radio' && $(input).is(':checked')) {
        name_el = $(input).attr('name');
        $('input[name=' + name_el + ']').each(function (index, el) {
            if (typeof $(el).attr('data-decide') !== 'undefined') {
                decide_els = $(el).attr('data-decide').split(',');
                $.each(decide_els, function (i2, el2) {
                    if ($('input[name="' + el2 + '"]').length > 0) {
                        $('input[name="' + el2 + '"]').first().closest('.form-group').hide();

                        if ($('input[name="' + el2 + '"]').attr('data-decide') && $('input[name="' + el2 + '"]').attr('type') == 'radio') {
                            sub_decide = $('input[name="' + el2 + '"]:checked').attr('data-decide').split(',');
                            if (sub_decide.length > 0) {
                                $.each(sub_decide, function (i3, el3) {
                                    if ($('input[name="' + el3 + '"]').length > 0) {
                                        $('input[name="' + el3 + '"]').first().closest('.form-group').hide();
                                    }
                                });
                            }
                        }
                    }
                });
            }

        });
        if ($(input).is(':visible')) {
            str_els = $(input).attr('data-decide');
            array_els = str_els.split(',');
            $.each(array_els, function (index, el) {
                if ($('input[name="' + el + '"]').length > 0) {
                    $('input[name="' + el + '"]').first().closest('.form-group').show();

                    if ($('input[name="' + el + '"]').attr('data-decide') && $('input[name="' + el + '"]').attr('type') == 'radio') {
                        sub_decide = $('input[name="' + el + '"]:checked').attr('data-decide').split(',');
                        if (sub_decide.length > 0) {
                            $.each(sub_decide, function (i3, el3) {
                                if ($('input[name="' + el3 + '"]').length > 0) {
                                    $('input[name="' + el3 + '"]').first().closest('.form-group').show();
                                }
                            });
                        }
                    }
                }
            });
        }
    }
    eam_loy_cat_type();
}
function etsamToggleVoucherForm(input, ets_am_voucher_form_suffix) {
    
    if (typeof input !== 'object') {
        input = input + ':checked';
    }
    if ($(input).val() == 'FIXED') {
        $('input[name$=_VOUCHER_CODE]').closest('.form-group').show();
        $.each(ets_am_voucher_form_suffix, function (index, el) {
            if ($('input[name$=' + el + ']').length > 0) {
                $('input[name$=' + el + ']').first().closest('.form-group').hide();

            }
            else if ($('select[name$=' + el + ']').length > 0) {
                $('select[name$=' + el + ']').closest('.form-group').hide();
            }

        });
        if($('input[name^=ETS_AM_AFF_DISCOUNT_DESC]').first().closest('.form-group').find('translatable-field')){
            $('input[name^=ETS_AM_AFF_DISCOUNT_DESC]').first().closest('.form-group').parents('.form-group').hide();
        }
        else{
            $('input[name^=ETS_AM_AFF_DISCOUNT_DESC]').first().closest('.form-group').hide();
        }
        
        if($('input[name^=ETS_AM_REF_DISCOUNT_DESC]').first().closest('.form-group').find('translatable-field')){
            $('input[name^=ETS_AM_REF_DISCOUNT_DESC]').first().closest('.form-group').parents('.form-group').hide();
        }
        else{
            $('input[name^=ETS_AM_REF_DISCOUNT_DESC]').first().closest('.form-group').hide();
        }
        $('input[name$=DISCOUNT_MIN_AMOUNT]').first().closest('.form-group').hide();
        
    }
    else if ($(input).val() == 'DYNAMIC') {
        if($('input[name^=ETS_AM_AFF_DISCOUNT_DESC]').first().closest('.form-group').find('translatable-field')){
            $('input[name^=ETS_AM_AFF_DISCOUNT_DESC]').first().closest('.form-group').parents('.form-group').show();
        }
        else{
            $('input[name^=ETS_AM_AFF_DISCOUNT_DESC]').first().closest('.form-group').show();
        }

        if($('input[name^=ETS_AM_REF_DISCOUNT_DESC]').first().closest('.form-group').find('translatable-field')){
            $('input[name^=ETS_AM_REF_DISCOUNT_DESC]').first().closest('.form-group').parents('.form-group').show();
        }
        else{
            $('input[name^=ETS_AM_REF_DISCOUNT_DESC]').first().closest('.form-group').show();
        }
        $('input[name$=DISCOUNT_MIN_AMOUNT]').first().closest('.form-group').show();
        $('input[name$=_VOUCHER_CODE]').closest('.form-group').hide();

        $('input[name$=_FREE_SHIPPING]').closest('.form-group').show();
        $('input[name$=_APPLY_DISCOUNT]').closest('.form-group').show();
        $('input[name$=_APPLY_DISCOUNT_IN]').closest('.form-group').show();
        $('input[name$=_DISCOUNT_DESC]').closest('.form-group').show();
        $('input[name$=_DISCOUNT_PREFIX]').closest('.form-group').show();

        if($('input[name$=_APPLY_DISCOUNT]:checked').val() == 'PERCENT'){
            $('input[name$=_REDUCTION_PERCENT').closest('.form-group').show();
            $('input[name$=_EXCLUDE_SPECIAL]').first().closest('.form-group').show();
        }
        else if($('input[name$=_APPLY_DISCOUNT]:checked').val() == 'AMOUNT'){
            $('input[name$=_REDUCTION_AMOUNT').closest('.form-group').show();
            $('input[name$=_EXCLUDE_SPECIAL]').first().closest('.form-group').hide();
        }
        else
            $('input[name$=_EXCLUDE_SPECIAL]').first().closest('.form-group').hide();
    }
}

function etsamToggleTypeDiscount(input, ets_am_voucher_foramount) {
    if (!$(input).is(':visible')) {
        return;
    }
    if (typeof input !== 'object') {
        input = input + ':checked';
    }
    if($(input).attr('name')=='ETS_AM_SELL_APPLY_DISCOUNT')
        sell = true;
    else
        sell=false;
    $input_name = $(input).attr('name').replaceAll('_APPLY_DISCOUNT','');
    if ($(input).val() == 'PERCENT') {
        $.each(ets_am_voucher_foramount, function (index, el) {
            if ($('input[name='+ $input_name+ el + ']').length > 0) {
                if(sell)
                {
                    $('input[name='+ $input_name+ el + ']').last().closest('.form-group').hide();
                }
                else
                    $('input[name='+ $input_name+ el + ']').first().closest('.form-group').hide();
            }
            else if ($('select[name='+ $input_name+ el + ']').length > 0) {
                $('select[name='+ $input_name+ el + ']').closest('.form-group').hide();
            }
        });
        if(sell)
            $('input[name='+$input_name+'_REDUCTION_PERCENT]').closest('.form-group').show();
        else
            $('input[name='+$input_name+'_REDUCTION_PERCENT]').closest('.form-group').show();
        if(sell)
            $('input[name='+$input_name+'_EXCLUDE_SPECIAL]').closest('.form-group').show();
        else
            $('input[name='+$input_name+'_EXCLUDE_SPECIAL]').closest('.form-group').show();
    }
    else if ($(input).val() == 'AMOUNT') {
        $('input[name='+$input_name+'_REDUCTION_PERCENT]').closest('.form-group').hide();
        $('input[name='+$input_name+'_EXCLUDE_SPECIAL]').closest('.form-group').hide();
        $.each(ets_am_voucher_foramount, function (index, el) {
            if ($('input[name='+$input_name + el + ']').length > 0) {
                $('input[name='+$input_name + el + ']').closest('.form-group').show();
            }
            else if ($('select[name='+$input_name +el + ']').length > 0) {
                $('select[name='+$input_name +el + ']').closest('.form-group').show();
            }
        });
    }
    else if ($(input).val() == 'OFF') {
        $('input[name='+$input_name+'_REDUCTION_PERCENT]').closest('.form-group').hide();
        $('input[name='+$input_name+'_EXCLUDE_SPECIAL]').closest('.form-group').hide();
        $.each(ets_am_voucher_foramount, function (index, el) {
            if ($('input[name='+$input_name + el + ']').length > 0) {
                $('input[name='+$input_name + el + ']').closest('.form-group').hide();
            }
            else if ($('select[name='+$input_name +el + ']').length > 0) {
                $('select[name='+$input_name +el + ']').closest('.form-group').hide();
            }
        });
    }
}

function eamRenderLevelInput(eam_next_level, value, ets_snw_suffix_level) {
    var input_display = 'block';
    if($('input[name=ETS_AM_REF_GIVE_REWARD_ON_ORDER]:checked').val() == 0){
        input_display = 'none';
    }
    eam_el_level = '<div class="form-group input-level-append" style="display:'+input_display+';">';
    eam_el_level += '<label class="control-label col-lg-3 required">Level ' + eam_next_level + '</label>';
    eam_el_level += '<div class="col-lg-9">';
    eam_el_level += '<div class="input-group">';
    eam_el_level += '<input type="text" name="ETS_AM_REF_SPONSOR_COST_LEVEL_' + eam_next_level + '" id="ETS_AM_REF_SPONSOR_COST_LEVEL_' + eam_next_level + '" value="' + value + '">';
    eam_el_level += '<span class="input-group-addon">' + ets_snw_suffix_level + '</span>';
    eam_el_level += '</div>';
    eam_el_level += '</div>';
    eam_el_level += '<a href="javascript:void(0)" class="btn-remove-level">x</a>';
    eam_el_level += '</div>';
    return eam_el_level
}

function eamToggleBoxState() {
    eamToggleBtnStates();
    eam_array_state_suff = ['order_state_wating_', 'order_state_validated_', 'order_state_canceled_'];
    $.each(eam_array_state_suff, function(index, state) {
        $('.ets-sn-admin input[id^="'+state+'"]:checked').each(function(idx, el_checked) {
            value = $(el_checked).val();
            $.each(eam_array_state_suff, function(i2, state_offset) {
                if(state_offset !== state){
                    $('.ets-sn-admin input[id^="'+state_offset+'"]').each(function(i3, el) {
                        if($(el).val() == value){
                            $(el).closest('tr').hide();
                        }
                    });

                }

            });
        });
        
        $('.ets-sn-admin input[id^="'+state+'"]').change(function(event) {
            value = $(this).val();
            $this = $(this);
            $.each(eam_array_state_suff, function (i2, state_offset) {
                if (state_offset !== state) {
                    $('.ets-sn-admin input[id^="' + state_offset + '"]').each(function (i3, el) {
                        if ($(el).val() == value) {
                            if ($this.is(':checked')) {
                                if ($(el).is(':checked')) {
                                    showErrorMessage(ets_snw_dumplicate_msg);
                                    $this.prop('checked', false);
                                    return;
                                }
                                else {
                                    $(el).closest('tr').hide();
                                }
                            }
                            else {
                                $(el).closest('tr').show();
                            }
                        }
                    });
                }
            });
            eamToggleBtnStates();
        });
    });
}

function eamToggleBtnStates(){
    var eam_array_state_suff = ['order_state_wating_', 'order_state_validated_', 'order_state_canceled_'];
    var total_states = $('.ets-sn-admin input[id^="order_state_wating_"]').length;
    var counter_state = 0;
    $.each(eam_array_state_suff, function(index, state) {
        $('.ets-sn-admin input[id^="'+state+'"]:checked').each(function(idx, el_checked) {
            counter_state++;
        });
    });

    if(counter_state >= total_states){
        $('.ets-sn-admin .js-eam-toggle-states-payment').hide();
    }
    else{
        $('.ets-sn-admin .js-eam-toggle-states-payment').show();
    }
}

function eamSetLoyCate() {
    eamLoyCateVal = [];
    $('.ets-sn-admin input[name="ETS_AM_LOYALTY_CATEGORIES[]"]:checked').each(function (index, el) {
        eamLoyCateVal.push($(el).val());
    });
    if ($('.ets-sn-admin input[name="ETS_AM_LOYALTY_CATEGORIES"]:checked').hasClass('has-tree-option')) {
        $('.ets-sn-admin input[name="ETS_AM_LOYALTY_CATEGORIES"]:checked').val(eamLoyCateVal.toString());
    }

    $('.ets-sn-admin form').submit(function (event) {
        if ($('.ets-sn-admin input[name="ETS_AM_LOYALTY_CATEGORIES"]:checked').hasClass('no-tree-option')) {
            $('.ets-sn-admin input[name="ETS_AM_LOYALTY_CATEGORIES[]"]').prop('checked', false);
        }
    });
}

function eamValidateDate(from, to) {
    loy_time_from = $(from).val();
    loy_time_to = $(to).val();

    if (loy_time_from && loy_time_to) {
        loy_date_from = new Date(loy_time_from);
        loy_date_to = new Date(loy_time_to);
        if (loy_date_from > loy_date_to) {
            showErrorMessage(ets_snw_date_msg);
        }

    }
}

function eamRenderFieldsMethodPayment(input, langs, currency) {
    var date = new Date();
    var rand_num = parseInt(date.getTime());
    method_name_html = '';
    method_name_html += '<div class="form-group payment-method-field">';
        method_name_html += '<div class="form-group row">';
            method_name_html += '<label class="control-label required col-lg-3">' + ets_swn_trans['method_field_title'] + '</label>';
            method_name_html += '<div class="col-lg-6">';
            for (var l = 0; l < langs.length; l++) {
                lang = langs[l];
                method_name_html += '<div class="form-group row trans_field trans_field_' + lang.id_lang + ' ' + (l > 0 ? 'hidden' : '') + '">';
                method_name_html += '<div class="col-lg-9">';
                method_name_html += '<input type="text" name="payment_method_field['+rand_num+'][title][' + lang.id_lang + ']" value="" class="form-control '+(lang.id_lang == currency.id ? 'required' : '')+'" data-error="'+ets_swn_trans['pmf_title_required']+'">';
                method_name_html += '</div>';
                method_name_html += '<div class="col-lg-2">';
                method_name_html += '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                method_name_html += lang.iso_code + ' ';
                method_name_html += '<span class="caret"></span>';
                method_name_html += '</button>';
                method_name_html += '<ul class="dropdown-menu">';
                for (var i = 0; i < langs.length; i++) {
                    method_name_html += '<li><a href="javascript:eamHideOtherLang(' + langs[i].id_lang + ')" title="">' + langs[i].name + '</a></li>';
                }
                method_name_html += '</ul>';
                method_name_html += '</div>';
                method_name_html += '</div>';
            }
            method_name_html += '</div>';
        method_name_html += '</div>';
    method_name_html += '<div class="form-group row">';
    method_name_html += '<label class="control-label col-lg-3">' + ets_swn_trans['method_field_type'] + '</label>';
    method_name_html += '<div class="col-lg-5">';
    method_name_html += '<select name="payment_method_field['+rand_num+'][type]" class="form-control">';
    method_name_html += '<option value="text" selected>Text</option>';
    method_name_html += '<option value="textarea">Textarea</option>';
    method_name_html += '</select>';
    method_name_html += '</div>';
    method_name_html += '</div>';
    method_name_html += '<div class="form-group row">';
        method_name_html += '<label class="control-label col-lg-3">' + ets_swn_trans['description'] + '</label>';
        method_name_html += '<div class="col-lg-6">';
        for (var l = 0; l < langs.length; l++) {
            lang = langs[l];
            method_name_html += '<div class="form-group row trans_field trans_field_' + lang.id_lang + ' ' + (l > 0 ? 'hidden' : '') + '">';
            method_name_html += '<div class="col-lg-9">';
            method_name_html += '<textarea name="payment_method_field['+rand_num+'][description][' + lang.id_lang + ']" class="form-control"></textarea>';
            method_name_html += '</div>';
            method_name_html += '<div class="col-lg-2">';
            method_name_html += '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
            method_name_html += lang.iso_code + ' ';
            method_name_html += '<span class="caret"></span>';
            method_name_html += '</button>';
            method_name_html += '<ul class="dropdown-menu">';
            for (var i = 0; i < langs.length; i++) {
                method_name_html += '<li><a href="javascript:eamHideOtherLang(' + langs[i].id_lang + ')" title="">' + langs[i].name + '</a></li>';
            }
            method_name_html += '</ul>';
            method_name_html += '</div>';
            method_name_html += '</div>';
        }
        method_name_html += '</div>';
    method_name_html += '</div>';
    
    method_name_html += '<div class="form-group row ">';
        method_name_html += '<label class="control-label col-lg-3">'+ets_swn_trans['required']+'</label>';
        method_name_html += '<div class="col-lg-3">';
            method_name_html += '<select name="payment_method_field['+rand_num+'][required]" class="form-control">';
                method_name_html += '<option value="1">'+ets_swn_trans['yes']+'</option>';
                method_name_html += '<option value="0">'+ets_swn_trans['no']+'</option>';
            method_name_html += '</select>';
        method_name_html += '</div>';
    method_name_html +=  '</div>';

    method_name_html += '<div class="form-group row ">';
        method_name_html += '<label class="control-label col-lg-3">'+ets_swn_trans['enable']+'</label>';
        method_name_html += '<div class="col-lg-9">';
            method_name_html +=  '<span class="switch prestashop-switch fixed-width-lg">';
            method_name_html += '<input type="radio" name="payment_method_field['+rand_num+'][enable]" id="payment_method_field_'+rand_num+'_enable_on" value="1" class="payment_method_field_enable" checked="checked">';
            method_name_html += '<label for="payment_method_field_'+rand_num+'_enable_on">'+ets_swn_trans['yes']+'</label>';
            method_name_html += '<input type="radio" name="payment_method_field['+rand_num+'][enable]" id="payment_method_field_'+rand_num+'_enable_off" class="payment_method_field_enable" value="0">';
            method_name_html += '<label for="payment_method_field_'+rand_num+'_enable_off">'+ets_swn_trans['no']+'</label>';
            method_name_html += '<a class="slide-button btn"></a>';
            method_name_html += '</span>'
        method_name_html += '</div>';
    method_name_html +=  '</div>';
    method_name_html += '<a class="btn btn-default btn-sm btn-delete-field js-btn-delete-field" href="javascript:void(0)"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i> ' + ets_swn_trans['delete'] + '</a>';
    method_name_html += '</div>';

    $(input).closest('.form-group').before(method_name_html);
}

function eamHideOtherLang(id_lang) {
    $('.ets-sn-admin .trans_field').addClass('hidden');
    $('.ets-sn-admin .trans_field_' + id_lang).removeClass('hidden');
}

function eamToggleFeePayment(input) {
    if (typeof input !== 'object') {
        input = $(input + ':selected');
    }
    if ($(input).val() == 'FIXED') {
        $(input).closest('.payment-method').find('.payment_method_fee_percent').closest('.form-group').hide();
        $(input).closest('.payment-method').find('.payment_method_fee_fixed').closest('.form-group').show();
    }
    else if ($(input).val() == 'PERCENT') {
        $(input).closest('.payment-method').find('.payment_method_fee_percent').closest('.form-group').show();
        $(input).closest('.payment-method').find('.payment_method_fee_fixed').closest('.form-group').hide();
    }
    else {
        $(input).closest('.payment-method').find('.payment_method_fee_percent').closest('.form-group').hide();
        $(input).closest('.payment-method').find('.payment_method_fee_fixed').closest('.form-group').hide();
    }
}


function eamGetUrlVars() {
    vars = {};
    parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function eamAjaxChartStat(data_ajax, loading_el) {
    $.ajax({
        url: '',
        type: 'GET',
        data: data_ajax,
        beforeSend: function () {
           if(loading_el){
            loading_el.show();
           }
        },
        success: function (res) {

            if (typeof res !== 'object') {
                res = JSON.parse(res);
            }
            var has_data = 0;
            for (var i = 0; i < res.data.length; i++) {
                if(res.data[i].values.length > 0){
                    has_data = 1;
                    break;
                }
            }
            if(!has_data){
                $("#eam_stats_reward_line svg").hide();
                $('#eam_stats_reward_line').append('<span class="eam-chart-no-data">'+ets_swn_trans['no_data']+'</span>');
            }
            else{
                $("#eam_stats_reward_line svg").show();
                $('#eam_stats_reward_line .eam-chart-no-data').remove();
                var define_columns = [];
                $.each(res.data[0].values, function(index, el) {
                    define_columns.push(el.x);
                });
                var axisLabel = res.x_asis == 'date'? ets_swn_chart_day : (res.x_asis == 'month'? ets_swn_chart_month : ets_swn_chart_year);
                eamLineChartReward.xAxis
                    .axisLabel(axisLabel)
                    .tickValues(define_columns)
                    .rotateLabels(res.count_values > 12? -45 : 0)
                    .tickFormat(function(d){
                        if(res.x_asis == 'date'){
                            return parseInt(d3.time.format('%d')(new Date(d)));
                        }
                        else if(res.x_asis == 'month'){
                            return parseInt(d3.time.format('%m')(new Date(d)));
                        }
                        else if(res.x_asis == 'year'){
                            return d3.time.format('%Y')(new Date(d));
                        }
                        return d;
                    });
                eamLineChartReward.yAxis     //Chart y-axis settings
                  .axisLabel(ets_swn_currency_code) //ets_swn_currency_code
                  .tickFormat(function(d){
                    if(data_ajax.filter_type_stats == 'orders' || data_ajax.filter_type_stats == 'customers'){
                        return d3.format('d')(d);
                    }
                    return d3.format('.02f')(d);
                  });
                eamLineChartRewardData.datum(res.data)
                .call(eamLineChartReward);
                nv.utils.windowResize(eamLineChartReward.update);

            }
            
        },
        complete: function () {

            if(loading_el){
                loading_el.hide();
            }
        }
    });
}
function eamAjaxPieChartReward(data_ajax, loading_el) {
    $.ajax({
        url: '',
        type: 'GET',
        data: data_ajax,
        beforeSend: function () {
           if(loading_el){
            loading_el.show();
           }
        },
        success: function (res) {

            if (typeof res !== 'object') {
                res = JSON.parse(res);
            }
            if(!res.length){
                d3.select("#eam-pie-chart-reward svg").selectAll('*').remove();
                $('#eam-pie-chart-reward').append('<span class="eam-chart-no-data">'+ets_swn_trans['no_data']+'</span>');
            }
            else{
                $('#eam-pie-chart-reward .eam-chart-no-data').remove();
               nv.addGraph(function() {
                  eamChartPieReward = nv.models.pieChart()
                      .x(function(d) { return d.label })
                      .y(function(d) { return d.value })
                      .margin({left: 0, right: 0, top : 25, bottom : 0})
                      .showLabels(true)
                      .labelThreshold(.05)
                      .pieLabelsOutside(false)
                      .labelType("percent");
                    eamChartPieReward.noData(ets_swn_trans['no_data']);
                    eamChartPieReward.tooltipContent(function(key, y, e, graph) {
                        return '<h3>' + key + '</h3>' +
                               '<p>' +  y + ' '+(typeof eam_currency_code !== 'undefined' ? eam_currency_code : ets_swn_currency_code)+ '</p>';
                    });
                    eamChartPieRewardData = d3.select("#eam-pie-chart-reward svg")
                        .datum(res)
                        .transition().duration(350)
                        .call(eamChartPieReward);

                  return eamChartPieReward;
                }); 
            }
        },
        complete: function () {

            if(loading_el){
                loading_el.hide();
            }
        }
    });
}
function eamToggleAllSettings(input){
    if(typeof input !== 'object'){
        input = input + ':checked';
    }

    if($(input).val() == 0){
        $(input).first().closest('.form-group').nextAll().hide();
    }
    else{
        $(input).first().closest('.form-group').nextAll().show();
    }
}

function update_query_string( uri, key, value ) {
    if ( ! uri ) { uri = window.location.href; }
    var a = document.createElement( 'a' ),
        reg_ex = new RegExp( key + '((?:\\[[^\\]]*\\])?)(=|$)(.*)' ),
        qs,
        qs_len,
        key_found = false;
    a.href = uri;
    if ( ! a.search ) {
        a.search = '?' + key + '=' + value;
        return a.href;
    }
    qs = a.search.replace( /^\?/, '' ).split( /&(?:amp;)?/ );
    qs_len = qs.length;
    while ( qs_len > 0 ) {
        qs_len--;
        if ( reg_ex.test( qs[qs_len] ) ) {
            qs[qs_len] = qs[qs_len].replace( reg_ex, key + '$1' ) + '=' + value;
            key_found = true;
        }
    }
    if ( ! key_found ) { qs.push( key + '=' + value ); }
    a.search = '?' + qs.join( '&' );
    return a.href;
}

function eamFilterDatatable(form){
    $(form).submit(function(event) {

        data_filter = $(form).serializeArray();
        params = '';
        filter_uri = window.location.href;
        $.each(data_filter, function(index, el) {
            if(el.name != 'date_ranger'){
                filter_uri = eamUpdateQueryStringParameter(filter_uri, el.name, encodeURI(el.value));
            }
            
        });
        
        window.location.href = filter_uri;
        return false;
    });
    
}
function eamUpdateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}

function eamToggleStateOptions(input, status){
    eam_setting_states = $(input).parent().prev().find('input[type=checkbox]');
    if(status == 'show'){
        eam_setting_states.each(function(index, el) {
            $(el).closest('tr').show();
        });
    }
    else{
        eam_setting_states.each(function(index, el) {
            if(!$(el).is(':checked')){
                $(el).closest('tr').hide();
            }
        });
    }
}

function eamToggleFields(a, b){
    if($(a+':checked').val() == 1){
        $.each(b, function(index, el) {
            if($(el).first().closest('.form-group').find('.translatable-field').length > 0){
                $(el).first().closest('.form-group').parent().parent().show();
            }
            else if($(el).hasClass('form-group'))
                $(el).show();
            else{
                $(el).first().closest('.form-group').show();
            }
        });
        if(('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]:checked').length && $('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]:checked').val()==0)
        {
            $('.input-level-append').hide();
        }
    }
    else{
        $.each(b, function(index, el) {
            if($(el).first().closest('.form-group').find('.translatable-field').length > 0){
                $(el).first().closest('.form-group').parent().parent().hide();
            }
            else if($(el).hasClass('form-group'))
                $(el).hide();
            else{
                $(el).first().closest('.form-group').hide();
            }
        });
    }
    if(a=='input[name=ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER]')
    {
        
        if($(a+':checked').val() == 1){
            if($('input[name="ETS_AM_AFF_ALLOW_VOUCHER_IN_CART"]:checked').val()==1)
            {
                $('.form_ets_am_aff_convert_voucher_msg').show();
            }
            else
                $('.form_ets_am_aff_convert_voucher_msg').hide();
        }
        else
        {
            $('.form_ets_am_aff_convert_voucher_msg').hide();
        }
        $('input[name="ETS_AM_AFF_ALLOW_VOUCHER_IN_CART"]').change(function(event) {
            if($('input[name="ETS_AM_AFF_ALLOW_VOUCHER_IN_CART"]:checked').val()==1)
            {
                $('.form_ets_am_aff_convert_voucher_msg').show();
            }
            else
                $('.form_ets_am_aff_convert_voucher_msg').hide()
        });
    }
    $(a).change(function(event) {
        if($(this).val() == 1){
            $.each(b, function(index, el) {
                if($(el).first().closest('.form-group').find('.translatable-field').length > 0){
                    $(el).first().closest('.form-group').parent().parent().show();
                }
                else if($(el).first().hasClass('form-group'))
                    $(el).show();
                else{
                    $(el).first().closest('.form-group').show();
                }
            });
            if(('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]:checked').length && $('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]:checked').val()==0)
            {
                $('.input-level-append').hide();
            }
        }
        else{
            $.each(b, function(index, el) {
                if($(el).first().closest('.form-group').find('.translatable-field').length > 0){
                    $(el).first().closest('.form-group').parent().parent().hide();
                }
                else if($(el).first().hasClass('form-group'))
                    $(el).hide();
                else{
                    $(el).first().closest('.form-group').hide();
                }
            });
        }
        if($(this).attr('name')=='ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER')
        {
            if($(this).val() == 1){
                if($('input[name="ETS_AM_AFF_ALLOW_VOUCHER_IN_CART"]:checked').val()==1)
                {
                    $('.form_ets_am_aff_convert_voucher_msg').show();
                }
                else
                    $('.form_ets_am_aff_convert_voucher_msg').hide();
            }
            else
            {
                $('.form_ets_am_aff_convert_voucher_msg').hide();
            }
        }
        if($('input[name=ETS_AM_REF_HOW_TO_CALCULATE]').length > 0){
            $('input[data-decide]').each(function (index, el) {
                etsAmToggleElDecide(el);
            });
        }

    });

}

function eamConfirmDelete(){
    if(confirm(ets_swn_trans['confirm_delete'])){
        return true;
    }
    return false;
}

function eamLoadAjaxDataDashboard(tab_active, page, data_filter){
    if(!$('.js-eam-dashboard').length){
        return;
    }
    var data_filter_obj = {};
    if(data_filter.length > 0){
        $.each(data_filter, function(index, el) {
            data_filter_obj[el.name] = el.value;
        });
    }
    $.ajax({
        url: '',
        type: 'GET',
        data: {
            page: page,
            data_filter: data_filter_obj,
            type: tab_active.replace('#', ''),
            getTabDataDasboard: true
        },
        beforeSend: function(){
            var loading = '<div class="stats-loading" style="display: block; padding: 0;">';
                loading +='<div class="loading-text">'+ets_swn_trans['loading']+'</div>';
             loading += '</div>';
            $(tab_active).closest('.eam-position-relative').prepend(loading);
        },
        success: function(res){
            if(typeof res !== 'object'){
                res = JSON.parse(res);
            }
            if(res.success){
                $(tab_active).find('.panel-body').html(res.html);
            }
        },
        complete: function(){
            $(tab_active).closest('.eam-position-relative').find('.stats-loading').remove();
        }
    });
}

function eamActionRewardItem(type){
    var eam_button_action_reward;
    var eam_button_action_reward;
    if(type == 'approve'){
        eam_button_action_reward ='.ets-sn-admin .js-approve-reward-item';
    }
    else if(type == 'delete'){
        eam_button_action_reward ='.ets-sn-admin .js-delete-reward-item';
    }
    else if(type == 'cancel'){
        eam_button_action_reward ='.ets-sn-admin .js-cancel-reward-item';
    }
    $(document).on('click', eam_button_action_reward, function(event) {
        event.preventDefault();
        if(type == 'delete'){
            if(!confirm(ets_swn_trans['confirm_delete_reward'])){
                return false;
            }
        }
        else if(type == 'cancel'){
            if(!confirm(ets_swn_trans['confirm_cancel_reward'])){
                return false;
            }
        }
        var id_reward = $(this).attr('data-id');
        var $this = $(this);
        $.ajax({
            url: '',
            type: 'POST',
            data: {
                doActionRewardItem: type,
                id_reward: id_reward
            },
            beforeSend: function(){
                $this.addClass('loading');
            },
            success: function(res){
                if(typeof res !=='object'){
                    res = JSON.parse(res);
                }
                if(res.success){
                    showSuccessMessage(res.message);
                    if($(eam_button_action_reward+'[data-id="'+id_reward+'"]').length > 1){
                        window.location.reload();
                    }
                    var td_status = 5;
                    if(eamGetParamsUrl(window.location.href,'id_reward_users')){
                        td_status = 4;
                    }
                    if(type == 'delete'){
                        if($this.closest('tbody').find('tr').length == 1){
                            $this.closest('tbody').html('<tr><td colspan="100%" style="text-align: center;">'+ ets_swn_trans['no_data']+'</td></tr>');
                        }
                        else{
                            $this.closest('tr').remove();
                        }
                    }
                    else{
                        var label_status = '';
                        if(type == 'approve'){
                            label_status = '<label class="label label-success">'+res.status+'</label>';
                        }
                        else if(type == 'cancel'){
                            label_status = '<label class="label label-default">'+res.status+'</label>';
                        }
                        $this.closest('tr').find('td:nth-child('+td_status+')').html(label_status);
                        var btn_html = eamGenerateBtnGroup(res.actions);
                        $this.closest('td').html(btn_html);
                    }
                    if(res.user)
                    {
                        $('#total_balance').html(res.user.total_balance);
                        $('#loy_rewards').html(res.user.loy_rewards);
                        $('#ref_rewards').html(res.user.ref_rewards);
                        $('#aff_rewards').html(res.user.aff_rewards);
                        $('#mnu_rewards').html(res.user.mnu_rewards);
                        $('#withdrawn').html(res.user.withdrawn);
                        $('#pay_for_order').html(res.user.pay_for_order);
                        $('#convert_to_voucher').html(res.user.convert_to_voucher);
                        $('#total_usage').html(res.user.total_usage);
                    }
                }
                else{
                    showErrorMessage(res.message);
                }
            },
            complete: function(){
                $this.removeClass('loading');
            }
        });
    });
}
function eamActionRewardUsageItem(type){
    var eam_button_action_reward;
    var eam_button_action_reward;
    if(type == 'approve'){
        eam_button_action_reward ='.ets-sn-admin .js-approve-reward-usage-item';
    }
    else if(type == 'delete'){
        eam_button_action_reward ='.ets-sn-admin .js-delete-reward-usage-item';
    }
    else if(type == 'cancel'){
        eam_button_action_reward ='.ets-sn-admin .js-cancel-reward-usage-item';
    }
    $(document).on('click', eam_button_action_reward, function(event) {
        event.preventDefault();
        if(type == 'delete'){
            if(!confirm(ets_swn_trans['confirm_delete_reward'])){
                return false;
            }
        }
        else if(type == 'cancel'){
            if(!confirm(ets_swn_trans['confirm_refund_reward'])){
                return false;
            }
        }
        var id_reward = $(this).attr('data-id');
        var $this = $(this);
        $.ajax({
            url: '',
            type: 'POST',
            data: {
                doActionRewardUsageItem: type,
                id_reward: id_reward
            },
            beforeSend: function(){
                $this.addClass('loading');
            },
            success: function(res){
                if(typeof res !=='object'){
                    res = JSON.parse(res);
                }
                if(res.success){
                    showSuccessMessage(res.message);
                    var td_status = 5;
                    if(eamGetParamsUrl(window.location.href,'id_reward_users')){
                        td_status = 4;
                    }
                    if(type == 'delete'){
                        if($this.closest('tbody').find('tr').length == 1){
                            $this.closest('tbody').html('<tr><td colspan="100%" style="text-align: center;">'+ ets_swn_trans['no_data']+'</td></tr>');
                        }
                        else{
                            $this.closest('tr').remove();
                        }
                    }
                    else{
                        var label_status = '';
                        if(type == 'approve'){
                            label_status = '<label class="label label-deducted">'+res.status+'</label>';
                        }
                        else if(type == 'cancel'){
                            label_status = '<label class="label label-refunded">'+res.status+'</label>';
                        }
                        $this.closest('tr').find('td:nth-child('+td_status+')').html(label_status);
                        var btn_html = eamGenerateBtnGroup(res.actions);
                        $this.closest('td').html(btn_html);
                    }
                    if(res.user)
                    {
                        $('#total_balance').html(res.user.total_balance);
                        $('#loy_rewards').html(res.user.loy_rewards);
                        $('#ref_rewards').html(res.user.ref_rewards);
                        $('#aff_rewards').html(res.user.aff_rewards);
                        $('#mnu_rewards').html(res.user.mnu_rewards);
                        $('#withdrawn').html(res.user.withdrawn);
                        $('#pay_for_order').html(res.user.pay_for_order);
                        $('#convert_to_voucher').html(res.user.convert_to_voucher);
                        $('#total_usage').html(res.user.total_usage);
                    }
                    
                }
                else{
                    showErrorMessage(res.message);
                }
            },
            complete: function(){
                $this.removeClass('loading');
            }
        });
    });
}

function eamGenerateAppBtn(btns){
    if(btns.length == 0){
        return '';
    }
    var html = '';
    for (var i = 1; i < btns.length; i++) {
        html +=  '<button type="button" class="btn btn-default '+btns[i].class+'" data-id="'+btns[i].id+'"><i class="fa fa-'+btns[i].icon+'"></i> '+btns[i].label+'</button>';
    }
    return html;
}

function eamGenerateBtnGroup(btns){
    if(btns.length == 0){
        return '';
    }
    var html = '';
    if(btns.length == 1){
        var btn = btns[0];
        if(typeof btn.href !== 'undefined'){
            html += '<a href="'+btn.href+'" class="btn btn-default '+btn.class+'" data-id="'+btn.id+'"><i class="fa fa-'+btn.icon+'"></i> '+btn.label+'</a>';
        }
        else{
            html += '<button type="button" class="btn btn-default '+btn.class+'" data-id="'+btn.id+'" data-action="'+(typeof btn.action !== 'undefined' ? btn.action : '') +'"><i class="fa fa-'+btn.icon+'"></i> '+btn.label+'</button>';
        }
    }
    else{
        html += '<div class="btn-group">';
            if(typeof btns[0].href !== 'undefined'){
                html +=  '<a href="'+btns[0].href+'" class="btn btn-default '+btns[0].class+'" data-id="'+btns[0].id+'"><i class="fa fa-'+btns[0].icon+'"></i> '+btns[0].label+'</a>';
            }else{
                html +=  '<button type="button" class="btn btn-default '+btns[0].class+'" data-id="'+btns[0].id+'" data-action="'+(typeof btns[0].action !== 'undefined' ? btns[0].action : '') +'"><i class="fa fa-'+btns[0].icon+'"></i> '+btns[0].label+'</button>';
            }
            
            html += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
            html += '<span class="caret"></span>';
            html += '<span class="sr-only">Toggle Dropdown</span>';
            html += '</button>';
            html += ' <ul class="dropdown-menu">';
                for (var i = 1; i < btns.length; i++) {
                    html += '<li><a href="javascript:void(0)" data-id="'+btns[i].id+'" class="'+btns[i].class+'" data-action="'+(typeof btns[i].action !== 'undefined' ? btns[i].action : '') +'"><i class="fa fa-'+btns[i].icon+'"></i> '+btns[i].label+'</a></li>';
                }
            html += ' </ul>';
        html += ' </div>';
                                              
    }
    return html;

}

function eamSortTableRewardUser(){
    $('.ets-sn-admin').on('click', '.js-sort-user-reward', function(event) {
        event.preventDefault();
        var data_sort = $(this).attr('data-sort');
        var data_col = $(this).attr('data-col');
        var url_query = window.location.href;
        url_query = eamUpdateQueryStringParameter( url_query, 'order_by', data_col);
        url_query = eamUpdateQueryStringParameter( url_query, 'order_dir', data_sort);
        window.location.href = url_query;
    });
}

function eamFilterRewardUser(){
    if($('.ets-sn-admin #form-reward_users table.reward_users').length > 0){
        $('.ets-sn-admin #form-reward_users table.reward_users #submitFilterButtonreward_users').html('<i class="icon-search"></i> '+ets_swn_trans['filter']);
        $('.ets-sn-admin #form-reward_users table.reward_users thead tr.filter th').each(function(index, el) {
            if($(el).find('input[type=text][class=filter]').length > 0 && index > 1){
                $(el).find('input[type=text][class=filter]').css({
                    width: '47%',
                    float: 'left',
                });
                $(el).find('input[type=text][class=filter]').attr('placeholder', 'MIN');
                if(typeof eam_cookie_filter[name_input] !== 'undefined'){
                     $(el).find('input[type=text][class=filter]').val( eam_cookie_filter[name_input]);
                }
                var name_input = $(el).find('input[type=text][class=filter]').attr('name');
                var value_input_max = '';
                if(typeof eam_cookie_filter[name_input+'_max'] !== 'undefined'){
                     value_input_max = eam_cookie_filter[name_input+'_max'];
                }
                var max_input = '<input type="text" class="filter" name="'+name_input+'_max'+'" style="width:47%; float:left;" value="'+value_input_max+'" placeholder="MAX">';
                $(el).append(max_input);
            }
        });
        $('.ets-sn-admin #form-reward_users table.reward_users select option:first-child').html('--');

    }
}

function eamLoadmoreData(){
    $('.eam-view-info-user .js-btn-show-more-sponsored-friends').click(function(event) {
        var current_page = parseInt($(this).attr('data-current-page'));
        var total_page = parseInt($(this).attr('data-total-page'));
        var id_customer = parseInt($(this).attr('data-id-customer'));
        var show_more = $(this).html();
        $this = $(this);
        if(current_page < total_page){
            $.ajax({
                url: '',
                type: 'GET',
                data: {
                    loadMoreSponsorFriend: true,
                    page: current_page + 1,
                    id_customer: id_customer
                },
                beforeSend: function(){
                    $this.html('<div class="eam-btn-loader"></div>');
                },
                success: function(res){
                    if(typeof res !== 'object'){
                        res = JSON.parse(res);
                    }
                    if(res.success){
                        if((current_page + 1) >= total_page){
                            $this.remove();
                        }
                        else{
                            $this.attr('data-current-page', current_page + 1);
                        }
                        $('.eam-view-info-user #table-sponsored-friends .body-table').append(res.html);
                    }
                },
                complete: function(){
                    $this.html(show_more);
                }
            })
        }
    });
    $('.eam-view-info-user .js-btn-show-more-reawrd-history').click(function(event) {
        var current_page = parseInt($(this).attr('data-current-page'));
        var total_page = parseInt($(this).attr('data-total-page'));
        var id_customer = parseInt($(this).attr('data-id-customer'));
        var show_more = $(this).html();
        $this = $(this);
        if(current_page < total_page){
            $.ajax({
                url: '',
                type: 'GET',
                data: {
                    loadMoreHistoryReward: true,
                    page: current_page + 1,
                    id_customer: id_customer
                },
                beforeSend: function(){
                    $this.html('<div class="eam-btn-loader"></div>');
                },
                success: function(res){
                    if(typeof res !== 'object'){
                        res = JSON.parse(res);
                    }
                    if(res.success){

                        if((current_page + 1) >= total_page){
                            $this.remove();
                        }
                        else{
                            $this.attr('data-current-page', current_page + 1);
                        }
                        $('.eam-view-info-user #table-history-reward .body-table').append(res.html);
                    }
                },
                complete: function(){
                    $this.html(show_more);
                }
            })
        }
    });
    $(document).on('click','.action_add_friend_user',function(){
        $('.aff_popup_wapper').addClass('show');
        $('input[name="aff_search_customer"]').val('');
    });
    $(document).on('click','.aff_close_popup',function(){
        $('.aff_popup_wapper').removeClass('show');
    });
    $(document).on('keyup','input[name="aff_search_customer"]',function(){
        if(xhr_search)
            xhr_search.abort();
        if( $(this).val()!='')
        {
            var customer = $(this).val();
            var $this= $(this);
            $(this).next('.input-group-addon').addClass('loading');
            xhr_search = $.ajax({
                url: '',
                type: 'POST',
                dataType: 'json',
                data: {
                    aff_search_customer: true,
                    ajax: true,
                    customer : customer,
                },
                success: function(json){
                    $('.aff-list-customer-search').html('<ul class="aff-list-customer-search-ul">'+json.list_customers+'</ul>');
                    $this.next('.input-group-addon').removeClass('loading');
                }
            })
        }
        else
            $('.aff-list-customer-search').html('<ul class="aff-list-customer-search-ul"><li class="aff_no_customer">'+no_customer_search+'</li></ul>');
    });
    $(document).on('click','.add_friend_customer',function(){
        var id_friend = $(this).data('id');
        var $this = $(this);
        $(this).addClass('loading');
        $.ajax({
                url: '',
                type: 'POST',
                dataType: 'json',
                data: {
                    aff_add_search_customer: true,
                    ajax: true,
                    id_friend : id_friend,
                    id_customer: idRewardUser
                },
                success: function(json){
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        if($('.aff_data_not_found').length)
                            $('.aff_data_not_found').remove();
                        $this.remove();
                        var $html ='<tr>';
                            $html += '<td> '+json.sponsor.id_customer+' </td>';
                            $html += '<td><a href="'+json.sponsor.link+'">'+json.sponsor.firstname+' '+json.sponsor.lastname+'</a></td>';
                            $html += '<td > '+json.sponsor.email+' </td>';
                            $html += '<td class="text-center"> '+json.sponsor.level+' </td>';
                            $html += '<td class="text-center"> '+json.sponsor.total_order+' </td>';
                            $html += '<td class="text-right"> '+json.sponsor.reward+' </td>';
                        $html +='</tr>';
                        $('#table-sponsored-friends tbody').append($html);
                        $('.aff_popup_wapper').removeClass('show');
                    }
                    else
                        $.growl.error({ message: json.errors });
                }
            });
    });
    $(document).mouseup(function (e)
    {
        var container_pop_table=$('.aff_popup_content');
        if (!container_pop_table.is(e.target)&& container_pop_table.has(e.target).length === 0 && !$('.aff_customer_search').is(e.target)&& $('.aff_customer_search').has(e.target).length === 0)
        {
            $('.aff_popup_wapper').removeClass('show');
        }
    });
    $(document).keyup(function(e) { 
        if(e.keyCode == 27) {
            $('.aff_popup_wapper').removeClass('show');
        }
    });
    $(document).on('change','input[name="ETS_AM_SAVE_LOG"]',function(e){
        var $this= $(this);
        $this.parent().addClass('loading');
        $.ajax({
            url: '',
            type: 'post',
            dataType: 'json',
            data: {
                ETS_AM_SAVE_LOG: $('input[name="ETS_AM_SAVE_LOG"]:checked').val(),
            },
            success: function(json)
            {
                $.growl.notice({ message: json.success });
                $this.parent().removeClass('loading');  
            },
            error: function(xhr, status, error)
            {
                $this.parent().removeClass('loading');       
            }
        });
    });
}

function eamConfirmWithdrawal(){
    $('.ets-sn-admin .js-confirm-approve-withdraw').click(function(event) {
        if(!confirm(ets_swn_trans['confirm_approve_withdrawal'])){
            return false;
        }
    });
    $('.ets-sn-admin .js-confirm-decline-return-withdraw').click(function(event) {
        if(!confirm(ets_swn_trans['confirm_decline_return_withdrawal'])){
            return false;
        }
    });
    $('.ets-sn-admin .js-confirm-decline-deduct-withdraw').click(function(event) {
        if(!confirm(ets_swn_trans['confirm_decline_deduct_withdrawal'])){
            return false;
        }
    });
    $('.ets-sn-admin .js-confirm-delete-withdraw').click(function(event) {
        if(!confirm(ets_swn_trans['confirm_delete_withdrawal'])){
            return false;
        }
    });
}

function eamConfigLoyCalculate() {
    if(!$('input[name=ETS_AM_LOYALTY_BASE_ON]:checked').length){
        return;
    }
    var loyalty = $('input[name=ETS_AM_LOYALTY_BASE_ON]:checked').val();
    if (loyalty.indexOf('FIXED') != - 1 || loyalty == 'NOREWARD' ) {
        $('.form_ets_am_loyalty_exclude_tax').hide();
    } else  {
        $('.form_ets_am_loyalty_exclude_tax').show();
    }
    if (loyalty != 'FIXED' && loyalty != 'CART') {
        $('.form_ets_am_loyalty_multipe_by_product,.form_ets_am_loy_cat_type,.form_ets_am_loyalty_categories,.form_ets_am_loyalty_include_sub,.form_ets_am_loyalty_specific,.form_ets_am_loyalty_excluded').hide();
    } else {
        $('.form_ets_am_loy_cat_type,.form_ets_am_loyalty_categories,.form_ets_am_loyalty_include_sub,.form_ets_am_loyalty_specific,.form_ets_am_loyalty_excluded').show();
    }
    if (loyalty == 'CART') {
        $('.form_ets_am_loyalty_multipe_by_product').show();
    }
    if (loyalty == 'SPC_FIXED' || loyalty == 'SPC_PERCENT') {
        $('.form_ets_am_loyalty_not_for_discounted, .form_ets_am_qty_min, .form_ets_am_loyalty_base_on .help-block').hide();
    } else {
        $('.form_ets_am_loyalty_not_for_discounted, .form_ets_am_qty_min, .form_ets_am_loyalty_base_on .help-block').show();
    }
    eam_loy_cat_type(loyalty);
}

function eam_loy_cat_type(loyalty) {
    var loyalty = loyalty || $('input[name=ETS_AM_LOYALTY_BASE_ON]:checked').val();
    if ($('#ETS_AM_LOY_CAT_TYPE_ALL').is(':checked') || (loyalty != 'FIXED' && loyalty != 'CART')) {
        $('.form_ets_am_loyalty_include_sub').hide();
    }
    else if (!$('#ETS_AM_LOY_CAT_TYPE_ALL').is(':checked')) {
        $('.form_ets_am_loyalty_include_sub').show();
    }
}
function menuheaderheight(){
    var menuheight = $('.ets-sn-admin__tabs').height();
    $('.ets-sn-admin__tabs_height').css('height',menuheight);
}
function eamSetPieChartReward(data){
    return function() {
      var eamChartPieReward = nv.models.pieChart()
          .x(function(d) { return d.label })
          .y(function(d) { return d.value })
          .margin({left: 0, right: 0, top : 25, bottom : 0})
          .showLabels(true)
          .labelThreshold(.05)
          .pieLabelsOutside(false)
          .labelType("percent");

        eamChartPieRewardData = d3.select("#eam-pie-chart-reward svg")
            .datum(data)
            .transition().duration(350)
            .call(eamChartPieReward);

      return eamChartPieReward;
    }
}

function eamGetParamsUrl(url_string, p){
    var url = new URL(url_string);
    var res = url.searchParams.get(p);
    return res;
}
function eamCheckMultiLevel()
{
    if($('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]').length)
    {
        if($('input[name="ETS_AM_REF_ENABLED_MULTI_LEVEL"]:checked').val()==1)
        {
            $('.form-group.input-level-append').show();
            $('.btn-add-level').show();
            $('.form_ets_am_ref_sponsor_cost_level_lower').show();
        }
        else
        {
            $('.form-group.input-level-append').hide();
            $('.btn-add-level').hide();
            $('.form_ets_am_ref_sponsor_cost_level_lower').hide();
        }
    }
}