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

<div class="wk-bundle-border wk-bundle-rounded wk-padding-point-5 wk-bundle-sub-product wk-bundle-sub-product-list wk-bundle-sub-product-list_{$sections.id_wk_bundle_section} wk-bundle-sub-product-list_{$product.id}_{$sections.id_wk_bundle_section} {if isset($product.selected_attribute) && $product.selected_attribute == 1}wk-select-border{/if}">
    {if $wk_multi_selection == 1}
        <div class="wk_bp_cancel wk_bp_cancel_{$sections.id_wk_bundle_section} wk_bp_cancel_{$product.id} wk_bp_cancel_{$product.id}_{$sections.id_wk_bundle_section}" {if isset($product.selected_attribute) && $product.selected_attribute == 1}style="display:block"{else}style="display:none"{/if} title="{l s='Remove product' mod='wkbundleproduct'}">
            <i class="material-icons">
                close
            </i>
        </div>
    {else}
        {if !$sections.is_required}
            <div class="wk_bp_cancel wk_bp_cancel_{$sections.id_wk_bundle_section} wk_bp_cancel_{$product.id}" {if isset($product.selected_attribute) && $product.selected_attribute == 1}style="display:block"{else}style="display:none"{/if} title="{l s='Remove product' mod='wkbundleproduct'}">
                <i class="material-icons">
                    close
                </i>
            </div>
        {/if}
    {/if}
    {block name='bundle_product_cover_thumbnails'}
        {include file='module:wkbundleproduct/views/templates/hook/_partials/bundle-sub-product-image.tpl' idproduct=$product.id idSection=$sections.id_wk_bundle_section}
    {/block}
    <div class="wk-bp-custom-margin-2-b wk-bp-custom-margin-10-t" style="line-height: 1.7rem;">
        <strong>
            <a href="{$product.link}" target='blank' style="color:#232323;text-decoration:none;">
                <h5 class="h5" itemprop="name">{$product.name}</h5>
            </a>
        </strong>
        <div id="wk-product-description-short-{$product.id}" itemprop="description">
            {* {$product.description_short nofilter}<br> *}
            {if ($product.price_tax_exc != $product.product_section_excl_wo_cond) && ($product.price_tax_exc > $product.product_section_excl_wo_cond)}
                {l s='Discounted price per unit' mod='wkbundleproduct'}<br>
                {else}
                {l s='Price per unit' mod='wkbundleproduct'}<br>
            {/if}
            <strong>{$product.product_section_price}</strong>&nbsp;
            {if $product.show_tax_incl == 1}
                {l s='(Tax incl.)' mod='wkbundleproduct'}
                {else}
                {l s='(Tax Excl.)' mod='wkbundleproduct'}
            {/if}
        </div>
        <div class="wk-product-actions wk-bp-custom-margin-t">
            <div class="wk-append-variant wk-append-variant_{$product.id}_{$sections.id_wk_bundle_section}">
                {block name='bundle_product_variants'}
                    {include file='module:wkbundleproduct/views/templates/hook/_partials/bundle-sub-product-variants.tpl' groups=$product.product_groups.groups idproduct=$product.id idsection=$sections.id_wk_bundle_section}
                {/block}
            </div>
            {if isset($catelog_mode) && !$catelog_mode}
                {block name='product_quantity'}
                    {if $sections.choose_quantity}
                        <div class="wk-product-quantity clearfix">
                            <div class="row">
                                <div class="control-label col-md-5 wk-bp-custom-margin-t ">{l s='Quantity' mod='wkbundleproduct'}</div>
                                <div class="wk-qty col-md-7">
                                    <div class="input-group wk-boot-touchspin">
                                        <input
                                            type="text"
                                            name="qty"
                                            value="{if $product.selected_qty != 0}{$product.selected_qty}{else}{$sections.min_quantity}{/if}"
                                            class="form-control wk_quantity_wanted wk_quantity_wanted_{$product.id}_{$sections.id_wk_bundle_section}"
                                            min="2"
                                            aria-label="{l s='Quantity' mod='wkbundleproduct'}"
                                            data-section_min_qty="{$sections.min_quantity}"
                                        >
                                        <div class="input-group-addon">
                                            <div class="wk-touch-btn">
                                                <a href="javascript:void(0)" class="btn wk-bootstrap-touchspin-up">
                                                    <i class="material-icons wk-touchspin-up">expand_less</i>
                                                </a>
                                                <div class="wk_touch_spin_bar">
                                                </div>
                                                <a href="javascript:void(0)" class="btn wk-bootstrap-touchspin-down">
                                                    <i class="material-icons wk-touchspin-down">expand_more</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {else}
                        <div class="wk-product-quantity clearfix">
                            <div class="row">
                                <div class="control-label col-md-5 wk-bp-custom-margin-t">{l s='Quantity' mod='wkbundleproduct'}
                                </div>
                                <div class="wk-qty col-md-7" style="cursor:not-allowed;">
                                    <div class="input-group wk-boot-touchspin">
                                        <input
                                            type="text"
                                            name="qty"
                                            value="{if $product.selected_qty != 0}{$product.selected_qty}{else}{$sections.min_quantity}{/if}"
                                            class="form-control wk_quantity_wanted wk_disable wk_quantity_wanted_{$product.id}_{$sections.id_wk_bundle_section}"
                                            min="2"
                                            aria-label="{l s='Quantity' mod='wkbundleproduct'}"
                                            data-section_min_qty="{$sections.min_quantity}"
                                        readonly>
                                        <div class="input-group-addon wk_disable" style="cursor:not-allowed;">
                                            <div class="wk-touch-btn">
                                                <a href="javascript:void(0)" class="btn wk-bootstrap-touchspin-up">
                                                    <i class="material-icons wk-touchspin-up">expand_less</i>
                                                </a>
                                                <div class="wk_touch_spin_bar">
                                                </div>
                                                <a href="javascript:void(0)" class="btn wk-bootstrap-touchspin-down">
                                                    <i class="material-icons wk-touchspin-down">expand_more</i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/block}
            {/if}
            {block name='bundle_product_select'}
                {include file='module:wkbundleproduct/views/templates/hook/_partials/bundle-sub-product-select.tpl' idpsproduct=$idpsproduct themeType=$themeType}
            {/block}
        </div>
    </div>
</div>
