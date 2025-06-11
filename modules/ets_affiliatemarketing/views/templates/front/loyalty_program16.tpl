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

<div class="ets-am-program ets-am-content">
    <div class="navbar-page">
        <ul class="ets-am-content-links">
            <li class="list-title">
                <h1 class="only-title">{$_svg_heart nofilter} {l s='Loyalty program' mod='ets_affiliatemarketing'}</h1>
            </li>
        </ul>
    </div>
   <div class="ets-am-content eam-p0">
        {if isset($alert_type) && $alert_type && $alert_type != 'registered'}
            <div class="mt-20">
                {if $alert_type == 'ALL_BANNED'}
                    <div class="alert alert-warning">
                        {l s='Your account has been banned.' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'PROGRAM_BANNED'}
                    <div class="alert alert-warning">
                        {l s='This program is unavailable for you' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'PROGRAM_PENDING'}
                    <div class="alert alert-info">
                        {l s='We are reviewing your application. Once it is approved you will be able to enter the program. Please come back to this this program later' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'NOT_VALID'}
                    <div class="alert alert-info">
                        {l s='You need to complete conditions to register to use this program' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'DISABLED'}
                    <div class="alert alert-info">
                        {l s='This program is not available' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'NEED_CONDITION'}
                    {if $message}
                        <div class="alert alert-info">
                            {$message|escape:'html':'UTF-8'}
                        </div>
                    {else}
                        <div class="alert alert-info">
                            {l s='You need to complete conditions to register to use this program' mod='ets_affiliatemarketing'}
                        </div>
                    
                    {/if}
				{elseif $alert_type == 'DISABLED'}
					<div class="alert alert-info">
						{l s='This program is disabled' mod='ets_affiliatemarketing'}
					</div>
                {/if}
            </div>
        {/if}
        {if isset($valid) && $valid}
            <p>
                {l s='You have ' mod='ets_affiliatemarketing'}<strong> {$currency.sign|escape:'html':'UTF-8'}{$eam_loyalty_reward|escape:'html':'UTF-8'}</strong>{l s=' in your loyalty rewards. The loyalty reward can be used to pay for your order on our website or convert into voucher code. Refer to ' mod='ets_affiliatemarketing'} <a href="{$eam_reward_link|escape:'html':'UTF-8'}" title="" class="eam-rewards-link">{l s='Rewards' mod='ets_affiliatemarketing'}</a>{l s=' for more details about the loyalty rewards you got' mod='ets_affiliatemarketing'}
            </p>
        {/if}
   </div>
</div>
<div class="eam-back-section">
    <a href="{if isset($my_account_link)}{$my_account_link|escape:'html':'UTF-8'}{/if}" title="{l s='Back to your account' mod='ets_affiliatemarketing'}" class="eam-back-link eam-link-go-myaccount">
        <i class="icon_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1203 544q0 13-10 23l-393 393 393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
        </i> {l s='Back to your account' mod='ets_affiliatemarketing'}</a>
    <a href="{if isset($home_link)}{$home_link|escape:'html':'UTF-8'}{/if}" title="{l s='Home' mod='ets_affiliatemarketing'}" class="eam-back-link eam-link-go-home">
        <i class="svg_icon"><svg width="15" height="15" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1472 992v480q0 26-19 45t-45 19h-384v-384h-256v384h-384q-26 0-45-19t-19-45v-480q0-1 .5-3t.5-3l575-474 575 474q1 2 1 6zm223-69l-62 74q-8 9-21 11h-3q-13 0-21-7l-692-577-692 577q-12 8-24 7-13-2-21-11l-62-74q-8-10-7-23.5t11-21.5l719-599q32-26 76-26t76 26l244 204v-195q0-14 9-23t23-9h192q14 0 23 9t9 23v408l219 182q10 8 11 21.5t-7 23.5z"/></svg>
        </i> {l s='Home' mod='ets_affiliatemarketing'}</a>
</div>
