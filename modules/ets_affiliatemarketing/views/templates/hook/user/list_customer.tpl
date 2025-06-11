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
{assign var='_svg_plus_circle' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}

{if $customers}
    {foreach from=$customers item='customer'}
        <li>{$customer.firstname|escape:'html':'UTF-8'}&nbsp;{$customer.lastname|escape:'html':'UTF-8'} ({$customer.email|escape:'html':'UTF-8'})
            {if !$customer.friend}<span class="add_friend_customer btn btn-default" data-id="{$customer.id_customer|intval}">
                {$_svg_plus_circle nofilter}{l s='Add' mod='ets_affiliatemarketing'}</span>
            {else}
                {if $customer.friend==1}
                    <span class="atf_added">{l s='Already in friends list' mod='ets_affiliatemarketing'}</span>
                {elseif $customer.friend==2}
                    <span class="atf_added atf_added_another">{l s='Already in friends list of another sponsor' mod='ets_affiliatemarketing'}</span>
                {else}
                    <span class="atf_added atf_added_another">{l s='Already is a referral/sponsor' mod='ets_affiliatemarketing'}</span>
                {/if}
            {/if}
        </li>
    {/foreach}
{else}
    <li class="aff_no_customer">{l s='No customer was found' mod='ets_affiliatemarketing'}</li>
{/if}