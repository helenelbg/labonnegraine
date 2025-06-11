/*
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
    if($('.eam-back-section').length>0 && $('.eam-btn-back-link').length)
    {
        var $html = $('.eam-btn-back-link').clone();
        $('.eam-btn-back-link').hide();
        $('.eam-back-section').append($html); 
    }
    $('.form-group.hidden').hide();
    if ($('#customer-reward-withdraw input[name=method]:checked').length) {
        var method_id = $('#customer-reward-withdraw input[name=method]:checked').val();
        getMethodFields(method_id);
    }
    $('#customer-reward-withdraw input[name=method]').on('change', function () {
        var radio = $(this);
        $('.form-group.hidden').hide();
        if (radio.is(':checked')) {
            var method_id = radio.val();
            getMethodFields(method_id);
        }
    });
    $(document).on('click','.button-refer-friends',function(e){
        e.preventDefault(); 
        if(!$('.list-refer-friends').hasClass('loading'))
        {
            var url_ajax = $(this).attr('href');
            $('.list-refer-friends').addClass('loading');
            $.ajax({
                    url: url_ajax,
                    type: 'post',
                    dataType: 'json',
                    data: '',
                    success: function(json)
                    {
                        $('.list-refer-friends').removeClass('loading');
                        $('.button-refer-friends').parent().remove();
                        $('.list-refer-friends').append(json.list_html);
                    },
                    error: function(xhr, status, error)
                    {
                        $('.list-refer-friends').removeClass('loading');       
                    }
            });
        }
        
    });
    var tabActive = readCookie('ets_am_customer_reward_tab');
    if (tabActive !== null && tabActive != 'undefined') {
        $('#ets-am-tabs a').removeClass('active');
        $('#ets-am-tabs a[href="' + tabActive + '"]').addClass('active');
        $('#ets-am-tab-contents').find('.tab-pane').removeClass('active').removeClass('in');
        $('#ets-am-tab-contents').find(tabActive).addClass('active').addClass('in');
    }
    var current = $('#ets-am-tabs a.active');
    current.tab('show');
    createCookie('ets_am_customer_reward_tab', current.attr('href'));
    var currentTarget = current.attr('href');
    if (currentTarget === '#history') {
        getCustomerRewardHistory(ets_am_reward_history_url,1);
    }
    if (currentTarget === '#withdraw') {
        getCustomerWithdraw(ets_am_reward_withdraw_url,1);
    }
    $('#ets-am-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        createCookie('ets_am_customer_reward_tab', $(this).attr('href'));
        var target = $(this).attr('href');
        if (target === '#history') {
            getCustomerRewardHistory(ets_am_reward_history_url,1);
        }
        if (target === '#withdraw') {
            getCustomerWithdraw(ets_am_reward_withdraw_url,1);
        }
    });
    if ($('#ets_am_modal_promo_code').length) {
        setTimeout(function () {
            $(this).modal('show');
        }, 1000);
    }
    if ($('#ets_am_aff_modal_promo_code').length) {
        setTimeout(function () {
            $('#ets_am_aff_modal_promo_code').show();
        }, 1000);
    }


    var wrapper = $('#customer-reward-voucher');
    wrapper.on('click', 'button.submit', function (e) {
        var form = wrapper.find('.form');
        e.preventDefault();
        form.find('span.help-block').remove();
        form.find('.message').remove();
        form.find('.form-group').removeClass('has-error');
        var data = {
            promo_type: 'submit',
            name: $('input[name=name]').val(),
            amount: $('input[name=amount]').val(),
            currency: $('select[name=currency]').val(),
            token: eam_token
        };
        for (var key in data) {
            var input = $('input[name=' + key + ']');
            if (key === 'promo_type') {
                continue;
            } else {
                if ($.trim(data[key]) === '') {
                    input.closest('.form-group').addClass('has-error');
                    input.closest('.form-group').append('<span class="help-block">' + field_required + '</span>');
                } else {
                    if (key === 'code') {
                        if (!/[a-zA-Z0-9]$/.test(data[key])) {
                            input.closest('.form-group').addClass('has-error');
                            input.closest('.form-group').append('<span class="help-block">' + field_contain_special + '</span>');
                        }
                    }
                    if (key === 'amount') {
                        if (isNaN(data[key])) {
                            input.closest('.form-group').addClass('has-error');
                            input.closest('.form-group').append('<span class="help-block">' + must_be_number + '</span>');
                        } else {
                            if (data[key] < 0) {
                                input.closest('.form-group').addClass('has-error');
                                input.closest('.form-group').append('<span class="help-block">' + unsigned + '</span>');
                            }
                        }
                    }
                }
            }
        }
        var errors = wrapper.find('span.help-block');
        if (!errors.length) {
            $.ajax({
                type: 'POST',
                url: ets_am_json_url,
                data: data,
                beforeSend: function () {
                    wrapper.find('input, button, select').prop('disabled', true)
                }, success: function (data) {
                    wrapper.find('input, button, select').prop('disabled', false);
                    handleSuccessResponse(data, form);
                }
            })
        }
    });
    $('#customer-reward-withdraw .payment-method').on('click', function (event) {
        var id = $(this).attr('data-id');
        $('#customer-reward-withdraw .payment-method').removeClass('active');
        $(this).addClass('active');
    });
    $('#ets-am-withdraw-table').on('click', '.eam-pagination a', function(event) {
        event.preventDefault();
        page = $(this).attr('data-page');
        getCustomerWithdraw(ets_am_reward_withdraw_url, parseInt(page));
    });
    $('#ets-am-histories-table').on('click', '.eam-pagination a', function(event) {
        event.preventDefault();
        page = $(this).attr('data-page');
        getCustomerRewardHistory(ets_am_reward_history_url, parseInt(page));
    });
    $(document).on('click','.create_voucher_code_sell',function(e){
        e.preventDefault();
        var $this= $(this);
        if(!$(this).hasClass('loading') && !$(this).hasClass('created'))
        {
            $(this).addClass('loading');
            $.ajax({
                url: '',
                type: 'post',
                dataType: 'json',
                data: {
                    create_voucher_code_sell: 1,
                },
                success: function(json)
                {
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        $this.addClass('created');
                        $this.attr('disabled','disabled');
                        $('.voucher_code_sell').val(json.code);
                        $('.voucher_code_sell').show();
                        $('.voucher_code_sell').next().show();
                    }
                    else
                        alert(json.error);
                    
                },
                error: function(xhr, status, error)
                {
                    $this.removeClass('loading');       
                }
            });
        }
        
    });
});

function getMethodFields(method_id) {
    var form = $('#customer-reward-withdraw form');
    $.ajax({
        type: 'POST',
        data: {
            method_id: method_id,
            type: 'get-fields',
            token: eam_token
        },
        beforeSend: function () {
            form.find('input, textarea, button').prop('disabled', true);
        },
        success: function (data) {
            form.find('input, textarea, button').prop('disabled', false);
            var fieldData = JSON.parse(data);
            var renderer = '';
            for (var index in fieldData) {
                var field = fieldData[index];
                renderer += '<div class="form-group">' +
                    '<div class="input-wrapper">';
                if (field['field_type'] == 'text') {
                    renderer += '<input type="text" name="field_names['+ field['field_id'] +']['+ field['field_alias'] +']" placeholder="' + field['field_title'] + '">';
                } else {
                    renderer += '<textarea name="field_names[' + field['field_id'] + ']['+ field['field_alias'] +']" placeholder="' + field['field_title'] + '"></textarea>';
                }
                renderer += '</div></div>';
            }

            form.find('.payment-method-fields').empty().html(renderer);
        }
    })
}

function getCustomerWithdraw(url, page) {
    var table = $('#ets-am-withdraw-table table');
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            withdraw_list: true,
            page: page,
            token: eam_token
        }, success: function (data) {
            var jsonData = JSON.parse(data);
            var results = jsonData.results;
            var total = jsonData.total;
            var current = jsonData.page;
            var total_page = jsonData.total_page;

            var htmlRenderer = '<tbody>';
            for (var index in results) {
                var result = results[index];
                var label_status = 'default';
                if(result['status'] == 'PENDING'){
                    label_status = 'warning';
                }
                if(result['status'] == 'APPROVE'){
                    label_status = 'success';
                }
                htmlRenderer += '<tr>';
                htmlRenderer += '<td>' + result['id'] + '</td>';
                htmlRenderer += '<td>' + result['amount'] + ' ' + result['currency'] + '</td>';
                htmlRenderer += '<td><label class="label label-'+label_status+'">' + result['status'].toLowerCase() + '</label></td>';
                htmlRenderer += '<td>' + result['payment_method'] + '</td>';
                htmlRenderer += '</tr>';
            }
            htmlRenderer += '</tbody>';
            table.find('tbody').remove();
            table.append(htmlRenderer);
            if(total_page > 1){
                var pagination = '<div class="eam-pagination">';
                    pagination += '<ul>';
                        for(var i = 1; i <= total_page; i++){
                            pagination += '<li class="'+(i == current ? 'active' : '')+'"><a href="javascript:void(0)" data-page="'+i+'">'+i+'</a></li>';
                        }
                    pagination += '</ul>';
                pagination += '</div>';
                table.next('.eam-pagination').remove();
                table.after(pagination);
            }
            
        }
    })
}

function handleSuccessResponse(data, form) {
    try {
        data = JSON.parse(data);
        if (data.success) {
            var htmlRenderer = '<div class="message success">' + success_create_promo;
            htmlRenderer += ' ' + data.promo.code + ' </div>';
            form.prepend(htmlRenderer);
        } else {
            var errors = data.errors;
            for (var key in errors) {
                var input = $('input[name=' + key + ']');
                if (key !== 'other') {
                    input.closest('.form-group').addClass('has-error');
                    input.closest('.form-group').append('<span class="help-block">' + errors[key] + '</span>');
                } else {
                    var message = '<div class="message error">' + errors[key] + '</div>';
                    form.prepend(message);
                }
            }
        }
    } catch (e) {
    }
}

function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
        return clipboardData.setData("Text", text);
    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";
        document.body.appendChild(textarea);
        textarea.select();
        try {
            textarea.focus();
            return document.execCommand("copy");
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}

function readCookie(name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0)
            return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = ';expires=' + date.toGMTString();
    }
    else
        var expires = '';
    document.cookie = name + '=' + value + expires + '; path=/';
}

function getCustomerRewardHistory(url,page) {
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            page: page
        },
        success: function (data) {
            try {
                data = JSON.parse(data);
                var table = $('#ets-am-histories-table table.table');
                var dataRenderer = '<tbody>';
                var total = data.total,
                    total_page = data.total_page,
                    limit = data.limit,
                    current = data.page,
                    histories = data.data;
                for (var index in histories) {
                    var history = histories[index];
                    var rowClass = (history.type && history.type == 1) ? 'added' : 'used';
                    var icon = rowClass == 'added' ? 'arrow_upward' : 'arrow_downward';
                    var program = history.program !== null ? history.program : '-';
                    dataRenderer += '<tr class="' + rowClass + '">' +
                        '<td><i class="material-icons eam_icon_'+rowClass+'">' + icon + '</i></td>' +
                        '<td>' + history.point + '</td>' +
                        '<td>' + history.status + '</td>' +
                        '<td>' + program + '</td>' +
                        '<td>' + (history.id_voucher == null || history.id_voucher == 0 ? '-' : '<i class="material-icons icon_check">check</i>') + '</td>' +
                        '<td>' + (history.id_withdraw == null || history.id_withdraw == 0 ? '-' : '<i class="material-icons icon_check">check</i>') + '</td>' +
                        '<td>' + (history.id_order == null || history.id_order == 0 ? '-' : '<i class="material-icons icon_check">check</i>') + '</td>' +
                        '<td>' + history.datetime_added + '</td>' +
                        '<td>' + history.note + '</td>' +
                        '</tr>';
                }
                dataRenderer += '</tbody>';
                table.find('tbody').remove();
                table.append(dataRenderer);

                if(total_page > 1){
                    var pagination = '<div class="eam-pagination">';
                        pagination += '<ul>';
                            for(var i = 1; i <= total_page; i++){
                                pagination += '<li class="'+(i == current ? 'active' : '')+'"><a href="javascript:void(0)" data-page="'+i+'">'+i+'</a></li>';
                            }
                        pagination += '</ul>';
                    pagination += '</div>';
                    table.next('.eam-pagination').remove();
                    table.after(pagination);
                }
            } catch (e) {
            }
        }
    })
}
