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

<div class="wk-bundle-product-select">
    {if $themeType == 'grid'}
        <div class="text-md-center text-danger" id='wk_out_of_stock_{$product.id}_{$sections.id_wk_bundle_section}'></div>
    {/if}
    <div class="text-md-center text-danger" id='wk_out_of_stock_{$product.id}_{$sections.id_wk_bundle_section}'></div>
    <button {if $themeType == 'list'} style="display:none;"{/if} class="btn {if isset($product.selected_attribute) && $product.selected_attribute == 1}
    btn-primary{else}btn-secondary{/if} wk-select-sub-product wk-select-sub-product_{$product.id}_{$sections.id_wk_bundle_section} wk_select_list_{$product.id}_{$sections.id_wk_bundle_section}" data-id-product="{$product.id}" data-id-product-attribute="{$product.id_product_attribute}" data-id-section="{$sections.id_wk_bundle_section}" data-theme_type = {$themeType} data-is_selected = "{if isset($product.selected_attribute) && $product.selected_attribute == 1}1{else}0{/if}" data-is_required = '{$sections.is_required}' data-id-ps-product ="{$idpsproduct}">
    {if isset($product.selected_attribute) && $product.selected_attribute == 1}<span>{l s='Selected' mod='wkbundleproduct'}</span>{else}<span>{l s='Select' mod='wkbundleproduct'}</span>{/if}
    </button>
</div>