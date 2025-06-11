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
{if isset($eam_css_link)}
    <link rel="stylesheet" href="{$eam_css_link nofilter}" />
{/if}
{assign var='_svg_envelop' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg></i>'}
{assign var='_svg_twitter' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1684 408q-67 98-162 167 1 14 1 42 0 130-38 259.5t-115.5 248.5-184.5 210.5-258 146-323 54.5q-271 0-496-145 35 4 78 4 225 0 401-138-105-2-188-64.5t-114-159.5q33 5 61 5 43 0 85-11-112-23-185.5-111.5t-73.5-205.5v-4q68 38 146 41-66-44-105-115t-39-154q0-88 44-163 121 149 294.5 238.5t371.5 99.5q-8-38-8-74 0-134 94.5-228.5t228.5-94.5q140 0 236 102 109-21 205-78-37 115-142 178 93-10 186-50z"/></svg></i>'}
{assign var='_svg_facebook' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1343 12v264h-157q-86 0-116 36t-30 108v189h293l-39 296h-254v759h-306v-759h-255v-296h255v-218q0-186 104-288.5t277-102.5q147 0 228 12z"/></svg></i>'}

{if isset($eam_display_aff_promo_code) && $eam_display_aff_promo_code}
    <div id="ets_affiliatemarketing_product_message">
        <div class="alert alert-info">
            {$eam_aff_promo_code_message nofilter}
        </div>
    </div>
    
{/if}
{if $eam_product_addition_aff_message != 'wating_confirm' && $eam_product_addition_aff_message != 'ban'}
    {if (isset($affiliate_suspended) && !$affiliate_suspended && isset($eam_product_addition_aff_message))  || (isset($link_share) && $link_share)}
    <div class="ets_affiliatemarketing_product_message s2">
        {if isset($affiliate_suspended) && !$affiliate_suspended}
            {if isset($eam_product_addition_aff_message)}
                <div class="alert alert-info">
                    {$eam_product_addition_aff_message nofilter}
                </div>
            {/if}
        {/if}
        {if isset($link_share) && $link_share}
            <div class="aff-product-share-list product-page" style="">
                <label>{l s='Share this product' mod='ets_affiliatemarketing'}&nbsp;</label>
                <a class="aff-product-share-fb" href="https://www.facebook.com/sharer/sharer.php?u={$link_share|urlencode nofilter}" target="_blank" title="{l s='Share on facebook' mod='ets_affiliatemarketing'}">{$_svg_facebook nofilter}</a>
                <a class="aff-product-share-tw" href="https://twitter.com/intent/tweet?text={$product->name|urlencode nofilter}&url={$link_share|urlencode nofilter}" target="_blank" title="{l s='Share on twitter' mod='ets_affiliatemarketing'}">{$_svg_twitter nofilter}</a>
                <a href="{$link_share|urlencode nofilter}" data-product-name="{$product->name|escape:'html':'UTF-8'}" title="{l s='Share via email' mod='ets_affiliatemarketing'}" class="aff-product-share-email">
                    {$_svg_envelop nofilter}</a>
            </div>
        {/if}
    </div>
    {/if}
{/if}
{if $eam_product_addition_loy_message != 'wating_confirm' && $eam_product_addition_loy_message != 'ban'}
    {if isset($loyalty_suspended) && !$loyalty_suspended}
        {if isset($eam_product_addition_loy_message)}
            <div id="ets_affiliatemarketing_product_message">
                <div class="alert alert-info">
                    {$eam_product_addition_loy_message nofilter}
                </div>
            </div>
        {/if}
    {/if}
{/if}
<div class="aff-product-popup-share-mail">
    <span class="aff-close">{l s='Close' mod='ets_affiliatemarketing'}</span>
    <div class="popup-content">
        <form action="" method="post">
            <div class="form-wrapper">
                <input name="aff-product-share-link" type="hidden" id="aff-product-share-link" value="" />
                <input name="aff-product-share-name" type="hidden" id="aff-product-share-name" value="" />
                <div class="form-group">
                    <label class="col-lg-2">{l s='Name' mod='ets_affiliatemarketing'}</label>
                    <div class="col-lg-9">
                        <input name="aff-name" id="aff-name" type="text" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 required">{l s='Email' mod='ets_affiliatemarketing'}</label>
                    <div class="col-lg-9">
                        <input type="text" name="aff-emails" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2">{l s='Message' mod='ets_affiliatemarketing'}</label>
                    <div class="col-lg-9">
                        <textarea name="aff-messages" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <button char="btn btn-default" name="affSubmitSharEmail" data-link="{$link->getModuleLink('ets_affiliatemarketing','aff_products') nofilter}">{l s='Send mail' mod='ets_affiliatemarketing'}</button>
            </div>
        </form>
    </div>
</div>
