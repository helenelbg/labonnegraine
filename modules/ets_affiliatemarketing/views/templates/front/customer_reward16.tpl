{*
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
*}
{assign var='_svg_question_circle_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}    <div class="ets-am-program ets-am-content">
        <div class="navbar-page">
            <ul class="ets-am-content-links">
                <li class="list-title">
                    <h1>
                        {$_svg_trophy nofilter}
                        {l s='My rewards' mod='ets_affiliatemarketing'}
                    </h1>
                </li>
                <li>
                    <a href="{$link_reward nofilter}"
                       class="{if isset($controller) && $controller == 'dashboard'} active {/if}">{l s='Dashboard' mod='ets_affiliatemarketing'}</a>
                </li>
                <li><a href="{$link_reward_history nofilter}"
                       class="{if isset($controller) && $controller == 'history'} active {/if}">{l s='Reward history' mod='ets_affiliatemarketing'}</a>
                </li>
                {if isset($allow_withdraw) && $allow_withdraw}
                    <li>
                        <a href="{$link_withdraw nofilter}"
                           class="{if isset($controller) && $controller == 'withdraw'} active {/if}">{l s='Withdrawals' mod='ets_affiliatemarketing'}</a>
                    </li>
                {/if}
                {if isset($allow_convert_voucher) && $allow_convert_voucher}
                    <li>
                        <a href="{$link_voucher nofilter}"
                           class="{if isset($controller) && $controller == 'voucher'} active {/if}">{l s='Convert into vouchers' mod='ets_affiliatemarketing'}</a>
                    </li>
                {/if}
            </ul>
        </div>
        <div class="ets-am-content eam-my20">
            {if $controller == 'dashboard'}
            <div class="eam-rewards-boxes boxes-color">
                {if isset($eam_allow_withdraw_loyalty) && $eam_allow_withdraw_loyalty}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="box box-pink">
                                <h5 class="box-title">{l s='Reward balance' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_reward_balance nofilter}
                                </div>
                                <div class="box-desc">
                                    {l s="Total remaining reward after withdrawing, converting into voucher or paying for orders." mod='ets_affiliatemarketing'}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="box box-teal">
                                <h5 class="box-title">{l s='Reward used' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_usage nofilter}
                                </div>
                                <div class="box-desc">
                                    {l s="Total reward used to withdraw, convert into voucher or pay for orders." mod='ets_affiliatemarketing'}
                                </div>
                            </div>
                        </div>
                    </div>
                {else}
                    <div class="row">
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-pink box-col-3 eam-box-tooltip" data-placement="bottom"
                         data-title="{l s='Total remaining reward amount including loyalty reward and earning reward after withdrawing, converting into voucher or paying for orders.' mod='ets_affiliatemarketing'}">
                                <h5 class="box-title">{l s='Reward balance' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_reward_balance nofilter}
                                </div>
                               
                            </div>
                        </div>
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-teal box-col-3 eam-box-tooltip" data-placement="bottom" data-title="{l s='Total remaining reward earned from loyalty program.' mod='ets_affiliatemarketing'} {if $message_title_reward}{l s='Can be used for the following purpose(s):' mod='ets_affiliatemarketing'} {$message_title_reward|escape:'html':'UTF-8'}{/if}">
                                <h5 class="box-title">{l s='Loyalty reward' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_loyalty_left nofilter}
                                </div>
                            </div>
                        </div>
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-orange box-col-3 eam-box-tooltip" data-placement="bottom" data-title="{l s='Total remaining reward earned from Referral program and affiliate program.' mod='ets_affiliatemarketing'} {if $message_title_reward_earning}{l s='Can be used for the following purpose(s):' mod='ets_affiliatemarketing'} {$message_title_reward_earning|escape:'html':'UTF-8'}{/if}">
                                <h5 class="box-title">{l s='Earning reward' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_earning_left nofilter}
                                </div>
                            </div>
                        </div>
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-blue box-col-3 eam-box-tooltip" data-placement="bottom" data-title="{l s='Total reward used to pay for orders, convert into voucher or withdraw.' mod='ets_affiliatemarketing'}">
                                <h5 class="box-title">{l s='Reward used' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_usage nofilter}
                                </div>
                                
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
        <div class="stats-data-reward eam-list-box-dashboard">
            <div class="panel">
                <div class="panel-body pl-25 pr-25">
                    <div class="stats-container eam-dasboad-reward">
                        <div class="stats-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="eam-box-chart">
                                        <div class="box-header">
                                            <h3 class="box-title">{l s='Earned and used rewards' mod='ets_affiliatemarketing'}</h3>
                                        </div>
                                        <div class="box-body">
                                            <div id="eam_stats_reward_line">
                                                <svg></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="eam-box-chart">
                                        <div class="box-header">
                                            <h3 class="box-title">{l s='Reward ratio' mod='ets_affiliatemarketing'} <small>({$eam_currency_code|escape:'html':'UTF-8'})</small></h3>
                                        </div>
                                        <div class="box-body">
                                            <div id="eam_stats_reward_pie">
                                                <span class="eam-chart-no-data">{l s='No data found' mod='ets_affiliatemarketing'}</span>
                                                <svg></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-filter eam-box-filter eam-my30 eam-br6">
                            <form class="form-inline" action="" method="post">
                                <div class="row">
                                    
                                    <div class="eam_select_filter">
                                        <label>{l s='Reward status' mod='ets_affiliatemarketing'}</label>
                                        <select name="status" class="form-control">
                                            <option value="all">{l s='All' mod='ets_affiliatemarketing'}</option>
                                            <option value="approved"
                                                    selected>{l s='Approved' mod='ets_affiliatemarketing'}</option>
                                            <option value="pending">{l s='Pending' mod='ets_affiliatemarketing'}</option>
                                            <option value="canceled">{l s='Canceled' mod='ets_affiliatemarketing'}</option>
                                            <option value="expired">{l s='Expired' mod='ets_affiliatemarketing'}</option>
                                        </select>
                                    </div>
                                    <div class="eam_select_filter col-lg-5 col-mb-12">
                                        <div>
                                            <label>{l s='Time range' mod='ets_affiliatemarketing'}</label>
                                            <select name="type_date_filter" class="form-control field-inline">
                                                <option value="all_times" {if isset($data_stats.distance) && $data_stats.distance > 5}selected="selected"{/if}>{l s='All the time' mod='ets_affiliatemarketing'}</option>
                                                <option value="this_month">{l s="This month" mod='ets_affiliatemarketing'} - {date('m/Y') nofilter}</option>
                                                <option value="this_year" {if isset($data_stats.distance) && $data_stats.distance <= 5}selected="selected"{/if}>{l s="This year" mod='ets_affiliatemarketing'} - {date('Y') nofilter}</option>
                                                <option value="time_ranger">{l s='Time range' mod='ets_affiliatemarketing'}</option>
                                            </select>
                                            <div class="box-date-ranger">
                                                <input type="text" name="date_ranger" value=""
                                                       class="form-control eam_date_ranger_filter">
                                                <input type="hidden" name="date_from_reward"
                                                       class="date_from_reward"
                                                       value="{date('Y-m-01') nofilter}">
                                                <input type="hidden" name="date_to_reward"
                                                       class="date_to_reward"
                                                       value="{date('Y-m-t') nofilter}">
                                                <input type="hidden" name="type_stats" value="reward">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="eam_action">
                                        <div class="form-group">
                                            <button type="button"
                                                    class="btn btn-default btn-block js-btn-submit-filter">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}
                                            </button>
                                            <button type="button"
                                                    class="btn btn-default btn-block js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {elseif $controller == 'withdraw'}
        {if isset($is_request_withdraw_page) && $is_request_withdraw_page}
            {if isset($eam_allow_withdraw) && $eam_allow_withdraw}
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="fs-16 text-uppercase mb-15">{l s='Submit withdrawal request' mod='ets_affiliatemarketing'}</h3>
                        {if isset($eam_payment_method)}
                            <div class="payment-info">
                                <p class="mb-0">{l s='Payment method' mod='ets_affiliatemarketing'}:
                                    <strong>{$eam_payment_method.title|escape:'html':'UTF-8'}</strong></p>
                                <p class="mb-0">{l s='Fee' mod='ets_affiliatemarketing'}:
                                    <strong>{$eam_payment_method.fee|escape:'html':'UTF-8'}</strong></p>
                                {if isset($eam_payment_method.estimated_processing_time) && $eam_payment_method.estimated_processing_time}
                                <p class="mb-0">{l s='Estimated processing time' mod='ets_affiliatemarketing'}:
                                    <strong>{$eam_payment_method.estimated_processing_time|escape:'html':'UTF-8'}</strong> {l s='day(s)' mod='ets_affiliatemarketing'}</p>
                                {/if}
                                {if isset($eam_payment_method.note) && $eam_payment_method.note}
                                <p class="mb-15">{l s='Note' mod='ets_affiliatemarketing'}
                                    : {$eam_payment_method.note nofilter}</p>
                                {/if}
                                <p class="mb-15">{l s='Balance available for withdrawal' mod='ets_affiliatemarketing'}
                                    : <strong>{$eam_can_withdraw nofilter}</strong></p>
                            </div>
                        {/if}
                    </div>
                </div>
                {if isset($eam_payment_method) && count($eam_payment_method) && isset($eam_payment_fields) && count($eam_payment_fields)}
                    <div class="row">
                        <div class="col-md-12">
                            {if (isset($eam_reward_enough) && $eam_reward_enough)}
                                {if isset($eam_reward_has_pending) && !$eam_reward_has_pending}
                                    <form class="eam-withdraw-form" novalidate method="post"
                                          action=""
                                          autocomplete="off" enctype="multipart/form-data">
                                        <p class="mb-15">{l s='Please fill in the fields below with required information then submit your withdrawal request.' mod='ets_affiliatemarketing'}</p>
                                        <div class="eam-box-content-withdraw">
                                            <div class="form-panel">
                                                <div class="form-panel-header">
                                                    <h4 class="form-panel-title">{l s='Amount to withdraw' mod='ets_affiliatemarketing'}</h4>
                                                </div>
                                                <div class="form-panel-body mb-5">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group {if isset($eam_form_errors) && array_key_exists('EAM_AMOUNT_WITHDRAW', $eam_form_errors)}has-error{/if}">
                                                                <div class="input-group display-flex mb-5">
                                                                    <input type="text"
                                                                           name="EAM_AMOUNT_WITHDRAW"
                                                                           class="form-control"
                                                                           aria-describedby="EAM_AMOUNT_WITHDRAW-addon"
                                                                           id="EAM_AMOUNT_WITHDRAW"
                                                                           value="{if isset($eam_form_old_data) && $eam_form_old_data.EAM_AMOUNT_WITHDRAW}{$eam_form_old_data.EAM_AMOUNT_WITHDRAW nofilter}{/if}"
                                                                           placeholder="{'0.00' nofilter}">
                                                                    <div class="input-group-append">
                                                                                <span class="input-group-text"
                                                                                      id="EAM_AMOUNT_WITHDRAW-addon">{$currency->iso_code|escape:'html':'UTF-8'}</span>
                                                                    </div>
                                                                </div>
                                                                {if isset($eam_withdraw_condition) && (isset($eam_withdraw_condition.min) || isset($eam_withdraw_condition) && isset($eam_withdraw_condition.max))}
                                                                    <p class="eam-note">
                                                                        {l s='Note:' mod='ets_affiliatemarketing'}
                                                                        {if isset($eam_withdraw_condition) && isset($eam_withdraw_condition.min)}
                                                                            {l s='Min amount' mod='ets_affiliatemarketing'} {$eam_withdraw_condition.min|escape:'html':'UTF-8'}.
                                                                        {/if}
                                                                        {if isset($eam_withdraw_condition) && isset($eam_withdraw_condition.min)}
                                                                            {l s='Max amount' mod='ets_affiliatemarketing'} {$eam_withdraw_condition.max|escape:'html':'UTF-8'}.
                                                                        {/if}
                                                                    </p>
                                                                {/if}
                                                                {if isset($eam_form_errors) && array_key_exists('EAM_AMOUNT_WITHDRAW', $eam_form_errors)}
                                                                    {foreach from=$eam_form_errors key=key item=error}
                                                                        {if $error@iteration == 1}
                                                                            <span class="help-block">{$eam_form_errors.EAM_AMOUNT_WITHDRAW nofilter}</span>
                                                                        {/if}
                                                                    {/foreach}
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="eam-withdraw-boxes">
                                                                <h3>
                                                                    <small>{l s='You will receive:' mod='ets_affiliatemarketing'}</small>
                                                                    <span class="price">{$currency->sign nofilter}{'0.00' nofilter}</span>
                                                                </h3>
                                                                <p class="eam-note">
                                                                    {l s='Note: Withdrawal fee has been calculated.' mod='ets_affiliatemarketing'}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-panel">
                                                <div class="form-panel-header">
                                                    <h4 class="form-panel-title">{l s='Additional information' mod='ets_affiliatemarketing'}</h4>
                                                </div>
                                                <div class="form-panel-body">
                                                    <div class="row">
                                                        <div class="col-md-8 col-sm-full">
                                                            <div class="form-payment-fields">
                                                                {foreach from=$eam_payment_fields item=field}
                                                                    <div class="row">
                                                                        <div class="form-group {if isset($eam_form_errors) && array_key_exists($field.field_alias, $eam_form_errors)}has-error{/if}">
                                                                            <label class="col-md-3 mt-5 pr-10">{$field.field_title nofilter}
                                                                                {if $field.required == 1}
                                                                                    <sup>*</sup>
                                                                                {/if}</label>
                                                                            <div class="col-md-5 p-0">
                                                                                {assign "field_val" ''}
                                                                                {if isset($eam_form_old_data) && count($eam_form_old_data)}
                                                                                    {foreach from=$eam_form_old_data key=k item=v}
                                                                                        {if $k == $field.field_alias}
                                                                                            {assign "field_val" $v|strip}
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if isset($eam_payment_history) && count($eam_payment_history)}
                                                                                        {foreach from=$eam_payment_history item=history }
                                                                                            {if $history.id_ets_am_withdrawal_field == $field.field_id}
                                                                                                {assign "field_val" $history.value|strip}
                                                                                            {/if}
                                                                                        {/foreach}
                                                                                    {/if}
                                                                                {/if}
                                                                                {if !$field_val && $field_values && isset($field_values[$field.field_id])}
                                                                                    {assign "field_val" $field_values[$field.field_id].value}
                                                                                {/if}
                                                                                {if $field.field_type == 'text' || $field.field_type == 'file'}
                                                                                    {if $field.field_type != 'file'}
                                                                                        
                                                                                        <input type="{$field.field_type|escape:'html':'UTF-8'}"
                                                                                               name="{$field.field_alias|escape:'html':'UTF-8'}"
                                                                                                {if $field.required == 1}
                                                                                                    required
                                                                                                {/if}
                                                                                               class="form-control"
                                                                                               value="{$field_val|escape:'html':'UTF-8'}" />
                                                                                    {else}
                                                                                        <div class="eam-box-upload-invoice">
                                                                                            <p class="eam-file-upload-invoice-return">
                                                                                                <label>{l s='Upload your invoice' mod='ets_affiliatemarketing'}</label>
                                                                                            </p>
                                                                                            <input type="{$field.field_type|escape:'html':'UTF-8'}"
                                                                                                   name="{$field.field_alias|escape:'html':'UTF-8'}"
                                                                                                    {if $field.required == 1}
                                                                                                        required
                                                                                                    {/if}
                                                                                                   class="form-control"
                                                                                                   id="eam-input-upload-invoice">
                                                                                            <label tabindex="0"
                                                                                                   class="eam-input-upload-invoice-trigger">Select</label>
                                                                                        </div>
                                                                                    {/if}
                                                                                {else}
                                                                                    <textarea
                                                                                            name="{$field.field_alias|escape:'html':'UTF-8'}"
                                                                                            cols="10"
                                                                                            rows="5" class="form-control eam-bg-white">{$field_val|escape:'html':'UTF-8'}</textarea>
                                                                                {/if}
                                                                                {if isset($eam_form_errors) && array_key_exists($field.field_alias, $eam_form_errors)}
                                                                                    {foreach from=$eam_form_errors key=key item=error}
                                                                                        {if $error@iteration == 1}
                                                                                            <span class="help-block">{$eam_form_errors.$key nofilter}</span>
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {/if}
                                                                            </div>
                                                                            {if isset($field.description) && $field.description}
                                                                                 <a class="eam-help eam-tooltip-bs"
                                                                                   href="javascript:void(0)"
                                                                                    data-toggle="tooltip" data-placement="top" title="{$field.description nofilter}">
                                                                                     {$_svg_question_circle_icon nofilter}
                                                                                 </a>

                                                                            {/if}
                                                                        </div>

                                                                    </div>
                                                                {/foreach}
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <p class="note mb-35">
                                                                        {l s='*Note: Please enter the required information above exactly to receive your funds. Wrong information may result in losing the money that you’re withdrawing' mod='ets_affiliatemarketing'}
                                                                    </p>
                                                                    <div class="form-buttons">
                                                                        <input type="hidden" value="1"
                                                                               name="eam_withdraw_submit">
                                                                        <button type="submit"
                                                                                class="btn btn-info eam-button eam-submit-request">{l s='Withdraw Funds' mod='ets_affiliatemarketing'}</button>
                                                                        <a href="{$link_withdraw nofilter}"
                                                                           class="eam-button eam-button-default eam-button-cancel btn btn-default">{l s='Cancel' mod='ets_affiliatemarketing'}</a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                {else}
                                    {if isset($message)}
                                        <div class="alert alert-info alert-error">
                                            <p class="mb-0">{$message nofilter}</p>
                                        </div>
                                    {/if}
                                {/if}
                            {else}
                                {if isset($message)}
                                    <div class="alert alert-info alert-error">
                                        <p class="mb-0">{$message nofilter}</p>
                                    </div>
                                {/if}
                            {/if}
                        </div>
                    </div>
                {else}
                    <div class="alert alert-danger alert-error">
                        {l s='We\'re sorry! This payment method is not available, please select other method ' mod='ets_affiliatemarketing'}
                    </div>
                {/if}
            {/if}
        {else}
            {if isset($eam_allow_withdraw) && $eam_allow_withdraw}
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-25">{l s='Select one of available withdrawal methods below to submit your money withdrawal request' mod='ets_affiliatemarketing'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="eam-table-data table table-striped mb-50">
                                <thead>
                                <tr>
                                    <th>{l s='Method' mod='ets_affiliatemarketing'}</th>
                                    <th>{l s='Description' mod='ets_affiliatemarketing'}</th>
                                    <th>{l s='Estimate processing time' mod='ets_affiliatemarketing'}</th>
                                    <th>{l s='Fee' mod='ets_affiliatemarketing'}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if isset($eam_payment_methods) && count($eam_payment_methods)}
                                    {foreach from=$eam_payment_methods item=method}
                                        <tr>
                                            <td>
                                                <a href="{$method.link nofilter}">{$method.title nofilter}</a>
                                            </td>
                                            <td>{$method.description nofilter}</td>
                                            <td>{if $method.estimated_processing_time}{$method.estimated_processing_time nofilter nofilter} {l s='day(s)' mod='ets_affiliatemarketing'}{else} -- {/if}</td>
                                            <td>{$method.fee nofilter}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr class="text-center">
                                        <td colspan="100%">
                                            {l s='No data found' mod='ets_affiliatemarketing'}
                                        </td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" table-responsive">
                            <table class="table eam-table-data table-striped">
                                <thead>
                                <tr>
                                    <th>{l s='Available balance for withdrawal' mod='ets_affiliatemarketing'}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="fs-18 fw-b">
                                        {$eam_can_withdraw nofilter}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}
            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-uppercase fs-14 mb-20">{l s='Your last withdrawal requests' mod='ets_affiliatemarketing'}</h4>
                    {if isset($eam_success_message)}
                        <div class="alert alert-success alert-dismissible eam-alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {$eam_success_message nofilter}
                        </div>
                    {/if}
                    <div class="table-response table-responsive">
                        <table class="table eam-table-flat table-label-custom">
                            <thead>
                            <tr>
                                <th class="text-center">{l s='Withdrawal ID' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Amount' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Payment method' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Status' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Processed date' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Note' mod='ets_affiliatemarketing'}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {if isset($eam_withdrawal_requests) && isset($eam_withdrawal_requests.results) && count($eam_withdrawal_requests.results)}
                                {foreach from=$eam_withdrawal_requests.results item=request}
                                    <tr>
                                        <td class="text-center">{$request.id_ets_am_withdrawal nofilter}</td>
                                        <td class="text-center">{$request.display_amount nofilter}</td>
                                        <td class="text-center">{$request.title nofilter}</td>
                                        <td class="text-center">
                                            {if $request.status == 1}
                                                <span class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</span>
                                            {elseif $request.status == 0}
                                                <span class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</span>
                                            {elseif $request.status == -1}
                                                <span class="label label-default">{l s='Declined' mod='ets_affiliatemarketing'}</span>
                                            {else}
                                                <span class="label label-default">{l s='Canceled' mod='ets_affiliatemarketing'}</span>
                                            {/if}
                                        </td>
                                        <td class="text-center">
                                            {if isset($request.date_process) && $request.date_process && $request.date_process!='0000-00-00'}
                                                {dateFormat date=$request.date_process full=0}
                                            {/if}
                                        </td>
                                        <td class="text-center">{$request.note nofilter}</td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr class="text-center">
                                    <td colspan="100%">
                                        {l s='No data found' mod='ets_affiliatemarketing'}
                                    </td>
                                </tr>
                            {/if}
                            </tbody>
                        </table>
                        {if $eam_withdrawal_requests.total_page > 1}
                            <div class="eam-pagination">
                                <ul>
                                    {if $eam_withdrawal_requests.current_page > 1}
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$eam_withdrawal_requests.current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                                        </li>
                                    {/if}
                                    {assign 'minRange' 1}
                                    {assign 'maxRange' $eam_withdrawal_requests.total_page}
                                    {if $friends.total_page > 10}
                                        {if $eam_withdrawal_requests.current_page < ($eam_withdrawal_requests.total_page - 3)}
                                            {assign 'maxRange' $eam_withdrawal_requests.current_page + 2}
                                        {/if}
                                        {if $friends.current_page > 3}
                                            {assign 'minRange' $eam_withdrawal_requests.current_page - 2}
                                        {/if}
                                    {/if}
                                    {if $minRange > 1}
                                        <li><span class="eam-page-3dot">...</span></li>
                                    {/if}
                                    {for $page=$minRange to $maxRange}
                                        <li class="{if $page == $eam_withdrawal_requests.current_page} active {/if}">
                                            <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}" class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                        </li>
                                    {/for}
                                    {if $maxRange < $eam_withdrawal_requests.total_page}
                                        <li><span class="eam-page-3dot">...</span></li>
                                    {/if}
                                    {if $eam_withdrawal_requests.current_page < $eam_withdrawal_requests.total_page}
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$eam_withdrawal_requests.current_page + 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'}</a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
        {elseif $controller == 'voucher'}
        <div class="eam-dashboard eam-convert-voucher">
            <div class="eam-voucer-message">
                {if isset($eam_voucher_success_message)}
                    <div class="alert alert-success alert-dismissable">
                        {$eam_voucher_success_message nofilter}
                        <a href="javascript:void(0)"
                           class="btn btn-success eam-apply-voucher b-radius-3 text-uppercase"
                           data-voucher-code="{$eam_voucher_id|escape:'html':'UTF-8'}">{l s='Apply Voucher code to my cart' mod='ets_affiliatemarketing'}</a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                {/if}
            </div>
            <div class="eam-form eam-voucher-form mb-40">
                <p>{$eam_voucher_info|replace:'[strong]':'<strong>'|replace:'[endstrong]':'</strong>' nofilter}</p>
                <form action="" method="post">
                    <div class="form-group {if isset($eam_form_error) && array_key_exists('EAM_VOUCHER_AMOUNT', $eam_form_error)}has-error{/if}">
                        <label class="fw-b mb-5"
                               for="EAM_VOUCHER_AMOUNT">{l s='Amount to convert:' mod='ets_affiliatemarketing'}</label>
                        <div class="input-group mb-5">
                            <input type="text"
                                   class="form-control"
                                   name="EAM_VOUCHER_AMOUNT"
                                   placeholder="0.00" aria-label="0.00"
                                   value="{if isset($eam_form_data) && isset($eam_form_data.EAM_VOUCHER_AMOUNT)}{$eam_form_data.EAM_VOUCHER_AMOUNT nofilter}{/if}"
                                   aria-describedby="EAM_VOUCHER_AMOUNT">
                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                      id="EAM_VOUCHER_AMOUNT">{$currency.iso_code nofilter}</span>
                            </div>
                        </div>
                        {if isset($eam_form_error) && array_key_exists('EAM_VOUCHER_AMOUNT', $eam_form_error)}
                            {foreach from=$eam_form_error key=key item=error}
                                {if $error@iteration == 1}
                                    <span class="help-block">{$eam_form_error.EAM_VOUCHER_AMOUNT nofilter}</span>
                                {/if}
                            {/foreach}
                        {/if}
                        <p class="eam-note mb-20">
                            {l s='Note:' mod='ets_affiliatemarketing'}
                            {if isset($eam_voucher_min) && $eam_voucher_min}
                                {l s='Min amount to convert %min_convert%.' mod='ets_affiliatemarketing' sprintf=['%min_convert%' => $eam_voucher_min]}
                            {/if}
                            {if isset($eam_voucher_max) && $eam_voucher_max}
                                {l s='Max amount to convert %max_convert%.' mod='ets_affiliatemarketing' sprintf=['%max_convert%' => $eam_voucher_max]}
                            {/if}
                            {if isset($ETS_AM_VOUCHER_AVAILABILITY) && $ETS_AM_VOUCHER_AVAILABILITY}
                                {l s='Voucher availability: [1]%ETS_AM_VOUCHER_AVAILABILITY% days[/1].' tags=['<strong>'] mod='ets_affiliatemarketing' sprintf=['%ETS_AM_VOUCHER_AVAILABILITY%' => $ETS_AM_VOUCHER_AVAILABILITY]}
                            {/if}
                        </p>
                    </div>
                    <input type="hidden" name="eam-submit-voucher" value="1">
                    <button class="btn btn-info text-uppercase b-radius-3 fs-14"
                            type="submit">{l s='Convert now' mod='ets_affiliatemarketing'}</button>
                </form>
            </div>
            <div class="eam-voucher-history">
                <h4 class="text-uppercase fs-14 mb-15">{l s='Your voucher codes' mod='ets_affiliatemarketing'}</h4>
                <div class="table-responsive">
                    <table class="table eam-table-flat">
                        <thead>
                        <tr>
                            <th>{l s='Code' d='Shop.Theme.Checkout'}</th>
                            <th>{l s='Description' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Quantity' d='Shop.Theme.Checkout'}</th>
                            <th>{l s='Value' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Minimum' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Cumulative' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Expiration date' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Status' d='Shop.Theme.Checkout'}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {if $cart_rules.results }
                            {foreach from=$cart_rules.results item=cart_rule}
                                <tr>
                                    <td>{$cart_rule.code nofilter}</td>
                                    <td>{$cart_rule.name nofilter}</td>
                                    <td class="text-center">{$cart_rule.quantity_per_user nofilter}</td>
                                    <td>{$cart_rule.value nofilter}</td>
                                    <td class="text-center">{$cart_rule.voucher_minimal nofilter}</td>
                                    <td class="text-center">{$cart_rule.voucher_cumulable nofilter}</td>
                                    <td class="text-center">
                                        {if $cart_rule.voucher_date && $cart_rule.voucher_date!='0000-00-00 00:00:00'}
                                            {dateFormat date=$cart_rule.voucher_date full=0}
                                        {/if}
                                    </td>
                                    <td class="text-center">
                                        {if $cart_rule.status == 1}
                                            <i class="i-mr-2 text-warning">{$_svg_clock_o nofilter}</i>{l s='Used' mod='ets_affiliatemarketing'}
                                        {elseif $cart_rule.status == -1}
                                            <span class="text-danger">{$_svg_check_icon nofilter}</span>{l s='Expired' mod='ets_affiliatemarketing'}
                        
                                        {else}
                                            <span class="text-success">{$_svg_check_icon nofilter}</span>{l s='Available' mod='ets_affiliatemarketing'}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr class="text-center">
                                <td colspan="100%">
                                    {l s='No data found' mod='ets_affiliatemarketing'}
                                </td>
                            </tr>
                        {/if}

                        </tbody>
                    </table>
                    {if $cart_rules.total_page > 1}
                        <div class="eam-pagination">
                            <ul>
                                {if $cart_rules.current_page > 1}
                                    <li>
                                        <a href="javascript:void(0)" data-page="{$cart_rules.current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                                    </li>
                                {/if}
                                {assign 'minRange' 1}
                                {assign 'maxRange' $friends.total_page}
                                {if $cart_rules.total_page > 10}
                                    {if $cart_rules.current_page < ($cart_rules.total_page - 3)}
                                        {assign 'maxRange' $cart_rules.current_page + 2}
                                    {/if}
                                    {if $friends.current_page > 3}
                                        {assign 'minRange' $cart_rules.current_page - 2}
                                    {/if}
                                {/if}
                                {if $minRange > 1}
                                    <li><span class="eam-page-3dot">...</span></li>
                                {/if}
                                {for $page=$minRange to $maxRange}
                                    <li class="{if $page == $cart_rules.current_page} active {/if}">
                                        <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}" class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                    </li>
                                {/for}
                                {if $maxRange < $cart_rules.total_page}
                                    <li><span class="eam-page-3dot">...</span></li>
                                {/if}
                                {if $cart_rules.current_page < $cart_rules.total_page}
                                    <li>
                                        <a href="javascript:void(0)" data-page="{$cart_rules.current_page + 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'}</a>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        {elseif isset($controller) && $controller == 'history'}
        <div class="eam-dashboard">
            <div class="table-responsive ">
                <table class="table eam-table-flat  table-label-custom">
                    <thead>
                    <tr>
                        <th width="10%" class="text-center">{l s='Reward ID' mod='ets_affiliatemarketing'}</th>
                        <th width="10%" class="text-center">{l s='Reward value' mod='ets_affiliatemarketing'}</th>
                        <th class="text-center">{l s='Program' mod='ets_affiliatemarketing'}</th>
                        <th class="text-left">{l s='Products' mod='ets_affiliatemarketing'}</th>
                        <th width="10%" class="text-center">{l s='Status' mod='ets_affiliatemarketing'}</th>
                        <th width="20%" class="text-center">{l s='Note' mod='ets_affiliatemarketing'}</th>
                        <th width="15%" class="text-center">{l s='Date' mod='ets_affiliatemarketing'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {if $reward_history.results}
                        {foreach $reward_history.results as $reward}
                            <tr>
                                <td class="text-center">{$reward.id_ets_am_reward nofilter}</td>
                                <td class="text-center">
                                    {if $reward.amount|strpos:'-' !== false}
                                        <span class="eam-reward-usage">{$reward.amount|escape:'html':'UTF-8'}</span>
                                    {else}
                                        {$reward.amount|escape:'html':'UTF-8'}
                                    {/if}
                                </td>
                                <td class="text-center">{$reward.program nofilter}</td>
                                <td class="text-left">
                                    {if $reward.products} 
                                        {foreach $reward.products as $prd}
                                            <a href="{$prd.link|escape:'html':'UTF-8'}" title="{l s='View product' mod='ets_affiliatemarketing'}">{$prd.name|escape:'html':'UTF-8'}</a><br/>
                                        {/foreach}
                                    {else}
                                        --
                                    {/if}
                                </td>
                                <td class="text-center">
                                    {if $reward.status == -2}
                                        <label class="label label-danger">{l s='Expired' mod='ets_affiliatemarketing'}</label>
                                    {elseif $reward.status == -1}
                                        <label class="label label-default">{l s='Canceled' mod='ets_affiliatemarketing'}</label>
                                    {elseif $reward.status == 0}
                                        <label class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</label>
                                    {else}
                                        <label class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</label>
                                    {/if}
                                </td>
                                <td class="text-center">{if $reward.note}{$reward.note nofilter}{else}--{/if}</td>
                                <td class="text-center">{dateFormat date=$reward.datetime_added full=1}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr class="text-center">
                            <td colspan="100%">
                                {l s='No data found' mod='ets_affiliatemarketing'}
                            </td>
                        </tr>
                    {/if}
                    </tbody>
                </table>
                {if $reward_history.total_page > 1}
                    <div class="eam-pagination">
                        <ul>
                            {if $reward_history.current_page > 1}
                                <li class="{if $reward_history.current_page == 1} active {/if}">
                                    <a href="javascript:void(0)" data-page="{$reward_history.current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                                </li>
                            {/if}
                            {assign 'minRange' 1}
                            {assign 'maxRange' $reward_history.total_page}
                            {if $reward_history.total_page > 10}
                                {if $reward_history.current_page < ($reward_history.total_page - 3)}
                                    {assign 'maxRange' $reward_history.current_page + 2}
                                {/if}
                                {if $reward_history.current_page > 3}
                                    {assign 'minRange' $reward_history.current_page - 2}
                                {/if}
                            {/if}
                            {if $minRange > 1}
                                <li><span class="eam-page-3dot">...</span></li>
                            {/if}
                            {for $page=$minRange to $maxRange}
                                <li class="{if $page == $reward_history.current_page} active {/if}">
                                    <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}"
                                       class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                </li>
                            {/for}
                            {if $maxRange < $reward_history.total_page}
                                <li><span class="eam-page-3dot">...</span></li>
                            {/if}
                            {if $reward_history.current_page < $reward_history.total_page}
                                <li>
                                    <a href="javascript:void(0)" data-page="{$reward_history.current_page + 1|escape:'html':'UTF-8'}"
                                       class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'} </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                {/if}
            </div>

            <div class="stat-filter eam-box-filter">
                <form class="form-inline" action="" method="post">
                    <div class="row">
                        <div class="eam_select_filter">
                            <label>{l s='Program' mod='ets_affiliatemarketing'}</label>
                            <select name="program" id="" class="form-control">
                                <option value="all"
                                        {if isset($query.program) && $query.program == 'all'}selected="selected"{/if}>{l s='All' mod='ets_affiliatemarketing'}</option>
                                <option value="loy"
                                        {if isset($query.program) && $query.program == 'loy'}selected="selected"{/if}>{l s='Loyalty' mod='ets_affiliatemarketing'}</option>
                                <option value="ref"
                                        {if isset($query.program) && $query.program == 'ref'}selected="selected"{/if}>{l s='Referral' mod='ets_affiliatemarketing'}</option>
                                <option value="aff"
                                        {if isset($query.program) && $query.program == 'aff'}selected="selected"{/if}>{l s='Affiliate' mod='ets_affiliatemarketing'}</option>
                                <option value="reward_used" {if isset($query.program) && $query.program == 'reward_used'} selected="selected" {/if}>{l s='Rewards used only' mod='ets_affiliatemarketing'}</option>

                            </select>
                        </div>
                        <div class="eam_select_filter">
                            <label>{l s='Reward status' mod='ets_affiliatemarketing'}</label>
                            <select name="status" class="form-control">
                                <option value="all"
                                        {if isset($query.status) && $query.status == 'all'}selected="selected"{/if}>{l s='All' mod='ets_affiliatemarketing'}</option>
                                <option value="1"
                                        {if isset($query.status) && $query.status == 1 }selected="selected"{/if}>{l s='Approved' mod='ets_affiliatemarketing'}</option>
                                <option value="0"
                                        {if isset($query.status) && $query.status == 0 && $query.status != 'all'}selected="selected"{/if}>{l s='Pending' mod='ets_affiliatemarketing'}</option>
                                <option value="-1"
                                        {if isset($query.status) && $query.status == -1}selected="selected"{/if}>{l s='Canceled' mod='ets_affiliatemarketing'}</option>
                                <option value="-2"
                                        {if isset($query.status) && $query.status == -2}selected="selected"{/if}>{l s='Expired' mod='ets_affiliatemarketing'}</option>
                            </select>
                        </div>
                        <div class="eam_select_filter col-mb-12">
                            <div>
                                <label>{l s='Time frame' mod='ets_affiliatemarketing'}</label>

                                <select name="type_date_filter" class="form-control field-inline">
                                    <option value="all_times"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'all_times'}selected="selected"{/if}>{l s='All the time' mod='ets_affiliatemarketing'}</option>
                                    <option value="this_month"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'this_month'}selected="selected"{/if}>{l s="This month" mod='ets_affiliatemarketing'} - {date('m/Y') nofilter}</option>
                                    <option value="this_year"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'this_year'}selected="selected"{/if}>{l s="This year" mod='ets_affiliatemarketing'} - {date('Y') nofilter}</option>
                                    
                                    <option value="time_ranger"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'time_ranger'}selected="selected"{/if}>{l s='Time range' mod='ets_affiliatemarketing'}</option>
                                </select>
                                <div class="box-date-ranger"
                                     {if isset($query.type_date_filter) && $query.type_date_filter == 'time_ranger'}style="display-block;"{/if}>
                                    <input type="text" name="date_ranger" value=""
                                           class="form-control eam_date_ranger_filter">
                                    <input type="hidden" name="date_from_reward"
                                           class="date_from_reward"
                                           value="{date('Y-m-01') nofilter}">
                                    <input type="hidden" name="date_to_reward"
                                           class="date_to_reward"
                                           value="{date('Y-m-t') nofilter}">
                                    <input type="hidden" name="type_stats" value="reward">
                                </div>
                            </div>
                        </div>

                        <div class="eam_action">
                            <div class="form-group">
                                <button type="submit"
                                        class="btn btn-default btn-block js-btn-submit-filter">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}
                                </button>
                                <button type="button"
                                        class="btn btn-default btn-block js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        {/if}
    </div>
    {if isset($eam_currency)}
        <script>
            var eam_currency_sign = '{$eam_currency->sign nofilter}';
        </script>
    {/if}
    {if isset($controller) && $controller == 'dashboard'}
        <script type="text/javascript">
            var eam_chart_day = "{l s='Day' mod='ets_affiliatemarketing'}";
            var eam_chart_month = "{l s='Month' mod='ets_affiliatemarketing'}";
            var eam_chart_year = "{l s='Year' mod='ets_affiliatemarketing'}";
            var eam_chart_currency_code = "{$eam_currency_code|escape:'html':'UTF-8'}";
            var eam_data_stats = '{$data_stats|json_encode}';
            eam_data_stats = JSON.parse(eam_data_stats.replace(/&quot;/g, '"'));
            var eam_data_pie_chart = '{$pie_reward|@json_encode nofilter}';
        </script>
    {elseif isset($controller) && $controller == 'withdraw'}
        <script>
            var eam_confirmation_withdraw = '{$eam_confirm nofilter}';
        </script>
    {elseif isset($controller) && $controller == 'voucher'}
        <script>
            var eam_confirm_convert_voucher = '{$eam_confirm_convert_voucher nofilter}'
        </script>
    {/if}
    