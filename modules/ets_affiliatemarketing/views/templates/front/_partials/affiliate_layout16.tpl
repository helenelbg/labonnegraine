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
                    <h1>
                        {$_svg_share_alt nofilter}
                        {l s='Affiliate program' mod='ets_affiliatemarketing'}
                    </h1>
                </li>
                <li>
                    <a href="{$aff_product_url nofilter}"
                       class="{if $controller == 'aff_products'}active{/if}">{l s='Affiliate Products' mod='ets_affiliatemarketing'}</a>
                </li>
                <li>
                    <a href="{$my_sale_url nofilter}"
                       class="{if $controller == 'my_sale'}active{/if}">{l s='My sales' mod='ets_affiliatemarketing'}</a>
                </li> 
                
            </ul>
        </div>
        <div class="col-lg-12 eam-p0">
            {if isset($alert_type) && $alert_type && $alert_type != 'registered'}
                <div class="mt-20 px-20">
                    {if $alert_type == 'ACCOUNT_BANNED'}
                        <div class="alert alert-warning">
                            {l s='You has been banned.' mod='ets_affiliatemarketing'}
                        </div>
                    {elseif $alert_type == 'PROGRAM_DECLINED'}
                        <div class="alert alert-warning">
                            {l s='You has been declined to join this program' mod='ets_affiliatemarketing'}
                        </div>
                    {elseif $alert_type == 'PROGRAM_SUSPENDED'}
                        <div class="alert alert-warning">
                            {l s='You has been suspended this program' mod='ets_affiliatemarketing'}
                        </div>
                    {elseif $alert_type == 'REGISTER_SUCCESS'}
                        <div class="alert alert-info">
                            {l s='We are reviewing your application. Once it is approved you will be able to join the program. Please come back to this this program later' mod='ets_affiliatemarketing'}
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
                            {l s='This program has been disabled' mod='ets_affiliatemarketing'}
                        </div>
                    {/if}
                </div>
            {/if}
            {if isset($valid) && $valid}
                <div class="ets-am-content">
                    <div class="content">
                        <div class="row">
                            <div class="col-lg-8">
                                {if isset($ets_am_product_sale) && count($ets_am_product_sale)}
                                    <div class="product_info">
                                        <p class="mb-5">
                                            <strong>{l s='Product name: ' mod='ets_affiliatemarketing'}</strong>
                                            <a target="_blank" href="{$ets_am_product_sale.link|escape:'html':'UTF-8'}">{$ets_am_product_sale.product_name nofilter}</a></p>
                                        <p class="mb-5">
                                            <strong>{l s='Number of sale: ' mod='ets_affiliatemarketing'}</strong>
                                            {$ets_am_product_sale.number_sale nofilter}</p>
                                        <p class="mb-5">
                                            <strong>{l s='Number of order: ' mod='ets_affiliatemarketing'}</strong>
                                            {$ets_am_product_sale.total_order nofilter}</p>
                                        {if $ets_am_product_sale.approved}
                                            <p class="mb-5">
                                                <strong>{l s='Approved: ' mod='ets_affiliatemarketing'}</strong>
                                                {$ets_am_product_sale.approved nofilter}</p>
                                        {/if}

                                        {if $ets_am_product_sale.expired}
                                            <p class="mb-5">
                                                <strong>{l s='Pending: ' mod='ets_affiliatemarketing'}</strong>
                                                {$ets_am_product_sale.pending nofilter}</p>
                                        {/if}

                                        {if $ets_am_product_sale.expired}
                                            <p class="mb-5"><strong>{l s='Expired: ' mod='ets_affiliatemarketing'}
                                                {$ets_am_product_sale.expried nofilter}</p>
                                        {/if}
                                        {if $ets_am_product_sale.canceled}
                                            <p class="mb-5">
                                                <strong>{l s='Canceled: ' mod='ets_affiliatemarketing'}</strong>
                                                {$ets_am_product_sale.canceled nofilter}</p>
                                        {/if}
                                        {if $ets_am_product_sale.view_count}
                                            <p class="mb-5">
                                                <strong>{l s='Views: ' mod='ets_affiliatemarketing'}</strong>
                                                {$ets_am_product_sale.view_count nofilter}
                                            </p>
                                        {/if}
                                        {if $ets_am_product_sale.c_rate}
                                            <p class="mb-15">
                                                <strong>{l s='Conversion Rate: ' mod='ets_affiliatemarketing'}</strong>
                                                {$ets_am_product_sale.c_rate nofilter}</p>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        </div>
                        {if $template=='affiliate_product.tpl'}
                            {include 'modules/ets_affiliatemarketing/views/templates/front/affiliate_product.tpl'}
                        {/if}
                        {if $template=='affiliate_program.tpl'}
                            {include 'modules/ets_affiliatemarketing/views/templates/front/affiliate_program.tpl'}
                        {/if}
                        {if $template=='my_sale.tpl'}
                            {include 'modules/ets_affiliatemarketing/views/templates/front/my_sale.tpl'}
                        {/if}
                        {if $template=='my_sale_products.tpl'}
                            {include 'modules/ets_affiliatemarketing/views/templates/front/my_sale_products.tpl'}
                        {/if}
                        {if $template=='my_sale_statistics.tpl'}
                            {include 'modules/ets_affiliatemarketing/views/templates/front/my_sale_statistics.tpl'}
                        {/if}
                    </div>
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