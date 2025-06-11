{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div id="product-description-short-{$idproduct}" itemprop="description">
    {* {$product.description_short nofilter}<br> *}
    {if ($price_info.price_tax_exc != $price_info.product_section_excl_wo_cond) && ($price_info.price_tax_exc > $price_info.product_section_excl_wo_cond)}
        {l s='Discounted price per unit' mod='wkbundleproduct'}<br>
        {else}
        {l s='Price per unit' mod='wkbundleproduct'}<br>
    {/if}
    <strong>{$price_info.product_section_price}</strong>&nbsp;
    {if $price_info.show_tax_incl == 1}
        {l s='(Tax incl.)' mod='wkbundleproduct'}
        {else}
        {l s='(Tax Excl.)' mod='wkbundleproduct'}
    {/if}
</div>