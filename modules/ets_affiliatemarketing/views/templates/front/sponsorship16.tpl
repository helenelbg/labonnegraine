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
                <h1>{$_svg_sitemap nofilter} {l s='Referral program' mod='ets_affiliatemarketing'}</h1>
            </li>
            {if !isset($alert_type)|| !$alert_type || $alert_type == 'registered'}
            <li role="presentation">
                <a href="{$link_tab.ref_friends nofilter}" class="{if isset($tab_active) && $tab_active == 'how-to-refer-friends'}active{/if}">{l s='How to refer friends?' mod='ets_affiliatemarketing'}</a>
            </li>
            <li role="presentation">
                <a href="{$link_tab.my_friends nofilter}"  class="{if isset($tab_active) && $tab_active == 'my-friends'}active{/if}">{l s='My friends' mod='ets_affiliatemarketing'}</a>
            </li>
            {/if}
        </ul>
    </div>
    <div class="ets-am-content" style="padding-top: 0;">
        {if isset($alert_type) && $alert_type && $alert_type != 'registered'}
            <div class="mt-20">
                {if $alert_type == 'account_banned'}
                    <div class="alert alert-warning">
                        {l s='You has been banned.' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'program_declined'}
                    <div class="alert alert-warning">
                        {l s='You has been declined to join this program' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'program_suspened'}
                    <div class="alert alert-warning">
                        {l s='You has been suspended this program' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'not_required'}
                    <div class="alert alert-info">
                        {l s='Not required to register' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'register_success'}
                    <div class="alert alert-info">
                        {l s='We are reviewing your application. Once it is approved you will be able to join the program. Please come back to this this program later' mod='ets_affiliatemarketing'}
                    </div>
                {elseif $alert_type == 'need_condition'}
                    {if $message}
                        <div class="alert alert-info">
                            {$message|escape:'html':'UTF-8'}
                        </div>
                    {else}
                    <div class="alert alert-info">
                        {l s='You need to complete conditions to register to use this program' mod='ets_affiliatemarketing'}
                    </div>
                    {/if}
                {elseif $alert_type == 'disabled'}
                    <div class="alert alert-info">
                        {l s='This program is disabled' mod='ets_affiliatemarketing'}
                    </div>
                {/if}
            </div>
        {else}
        <div class="content">
            {if $template=='sponsorship_refer_friend.tpl'}
                {include 'modules/ets_affiliatemarketing/views/templates/front/sponsorship_refer_friend.tpl'}
            {/if}
            {if $template=='sponsorship_customer.tpl'}
                {include 'modules/ets_affiliatemarketing/views/templates/front/sponsorship_customer.tpl'}
            {/if}
            {if $template=='sponsorship_myfriend.tpl'}
                {include 'modules/ets_affiliatemarketing/views/templates/front/sponsorship_myfriend.tpl'}
            {/if}
        </div>
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