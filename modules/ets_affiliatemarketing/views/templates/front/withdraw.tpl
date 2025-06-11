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

{extends file='page.tpl'}
{block name="page_content"}
    <h1 id="ets-am-customer-reward-heading">
        {l s='My rewards' mod='ets_affiliatemarketing'}
    </h1>
    <div class="ets-am-content">
        <ul class="ets-am-content-links">
            <li><a href="">{l s='Dashboard' mod='ets_affiliatemarketing'}</a></li>
            <li><a href="">{l s='Reward History' mod='ets_affiliatemarketing'}</a></li>
            <li><a href="" class="active">{l s='Withdrawals' mod='ets_affiliatemarketing'}</a></li>
            <li><a href="">{l s='Convert to vouchers' mod='ets_affiliatemarketing'}</a></li>
        </ul>
        <div class="content">

        </div>
    </div>
{/block}
{block name='page_footer'}
<div class="eam-back-section">
    <a href="{if isset($my_account_link)}{$my_account_link|escape:'html':'UTF-8'}{/if}" title="{l s='Back to your account' mod='ets_affiliatemarketing'}" class="eam-back-link eam-link-go-myaccount">
        <i class="icon_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1203 544q0 13-10 23l-393 393 393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
        </i> {l s='Back to your account' mod='ets_affiliatemarketing'}</a>
    <a href="{if isset($home_link)}{$home_link|escape:'html':'UTF-8'}{/if}" title="{l s='Home' mod='ets_affiliatemarketing'}" class="eam-back-link eam-link-go-home">
        <i class="svg_icon"><svg width="15" height="15" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1472 992v480q0 26-19 45t-45 19h-384v-384h-256v384h-384q-26 0-45-19t-19-45v-480q0-1 .5-3t.5-3l575-474 575 474q1 2 1 6zm223-69l-62 74q-8 9-21 11h-3q-13 0-21-7l-692-577-692 577q-12 8-24 7-13-2-21-11l-62-74q-8-10-7-23.5t11-21.5l719-599q32-26 76-26t76 26l244 204v-195q0-14 9-23t23-9h192q14 0 23 9t9 23v408l219 182q10 8 11 21.5t-7 23.5z"/></svg>
        </i> {l s='Home' mod='ets_affiliatemarketing'}</a>
</div>
{/block}
