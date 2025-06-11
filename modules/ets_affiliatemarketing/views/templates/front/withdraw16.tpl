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

{if isset($flash_message)}
    <div class="alert alert-info alert-dismissible est-am-alert" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span>
        </button>
        {$flash_message  nofilter}
    </div>
{/if}
{if isset($valid) && $valid}
    <div id="customer-reward-withdraw" class="customer-reward-voucher row">
        <div class="col-sm-12">
            <h1 id="ets-am-customer-reward-heading">
                <span class="left">{l s='Withdraw to bank' mod='ets_affiliatemarketing'}</span>
                <small class="right">
                    {l s='Total: ' mod='ets_affiliatemarketing'} {$available_reward nofilter}
                    &nbsp;
                </small>
            </h1>
            <div class="row">
                <div class="col-sm-12 col-md-10 offset-md-1">
                    <form action="{$json_url nofilter}" method="post">
                        <div class="payment-start">
                            {if isset($errors)}
                                {foreach from=$errors item=error}
                                    <div class="alert alert-danger">
                                        {$error nofilter}
                                    </div>
                                {/foreach}
                            {/if}
                            {if isset($messages)}
                                {foreach from=$messages item=message}
                                    <div class="alert alert-success">
                                        {$message nofilter}
                                    </div>
                                {/foreach}
                            {/if}
                            {if isset($payment_methods) && count($payment_methods)}
                                <div class="form-group text-center">
                                    {foreach from=$payment_methods item=method}
                                        <label for="payment_method_{$method.id_ets_am_payment_method nofilter}"
                                               class="payment-method">
                                            <input type="radio" value="{$method.id_ets_am_payment_method nofilter}"
                                                   name="method"
                                                   {if isset($old_method) && $method.id_ets_am_payment_method == $old_method}checked{/if}
                                                   id="payment_method_{$method.id_ets_am_payment_method nofilter}">
                                            <span>
                                            {$method.title|escape:'html':'UTF-8'}
                                        </span>
                                            <span>
                                            {if $method.fee_type === 'FIXED'}
                                                {l s='Fixed' mod='ets_affiliatemarketing'}
                                                {if $display_reward == 'point'}
                                                    {$method.fee_fixed * $exchange_rate nofilter} {l s='points' mod='ets_affiliatemarketing'}
                                                {else}
                                                    {(float)$method.fee_fixed nofilter} {$currency['iso_code'] nofilter}
                                                {/if}
                                                {l s='fee' mod='ets_affiliatemarketing'}
                                            {else}
                                                {(float)$method.fee_percent nofilter} {l s='percentage fee' mod='ets_affiliatemarketing'}
                                            {/if}
                                        </span>
                                        </label>
                                    {/foreach}
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-secondary btn-add-on float-xs-right" type="button">
                                        {if isset($display_reward) && $display_reward == 'point'}
                                            {$display_reward nofilter}
                                        {else}
                                            {$currency['iso_code'] nofilter}
                                        {/if}
                                    </button>
                                    <div class="input-wrapper">
                                        <input type="text"
                                               name="amount"
                                               placeholder="{l s='Amount' mod='ets_affiliatemarketing'}"
                                               {if isset($old_amount)}value="{$old_amount nofilter}" {/if}
                                               id="ets_am_amount">
                                        <input type="hidden" name="reward_type"
                                               {if isset($display_reward)}value="{$display_reward nofilter}"{/if}>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="payment-method-fields"></div>
                                <div class="form-group clearfix">
                                    <a href="{$back_url nofilter}" class="btn btn-secondary pull-left">{l s='Back' mod='ets_affiliatemarketing'}</a>
                                    <button class="btn btn-primary withdraw-submit pull-right" type="submit">
                                        {l s='Continue' mod='ets_affiliatemarketing'}
                                    </button>
                                </div>
                            {else}
                                <p class="text-warning">{l s='Sorry, there is no payment method available.' mod='ets_affiliatemarketing'}</p>
                            {/if}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{/if}
