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

<div class="mt-1 wk-bundle-border wk_bp_slider_view" id="{$sections.id_wk_bundle_section}">
    <div class="row" style="padding-top: 6px;background-color: {Configuration::get(
            'WK_BUNDLE_PRODUCT_SECTION_BG')};color:{Configuration::get(
            'WK_BUNDLE_PRODUCT_SECTION_TXT_COLOR')};margin-left:-16px;margin-right:-16px;">
        <div class="col-md-7 wk_bp_slider_view-title">
            <strong>{$sections.section_name}</strong>
        </div>
        <div class="col-md-5" style="color:{Configuration::get(
            'WK_BUNDLE_PRODUCT_SECTION_TXT_COLOR')};text-align:right;">
            {if !$sections.is_required}
                {l s='Optional' mod='wkbundleproduct'}
            {/if}
            <span class="wk_section_header"><a href="javascript:void(0);" data-toggle="tooltip" title="{l s='You can select upto %mx_qty% quantity from this section.' sprintf=['%mx_qty%' => $sections.max_quantity] mod='wkbundleproduct'}"><i class="material-icons" style="font-size:15px;">info</i></a></span>
        </div>
    </div>
    <div class="row">
        <div class="MultiCarousel" data-items="2,3,6,6" data-slide="1" id="MultiCarousel"  data-interval="1000">
            <div class="MultiCarousel-inner">
                {foreach from=$sections.product_detail item=$products key=key}
                    <div class="item">
                        <div class="pad15">
                            {block name='bundle-sub-products'}
                                {include file='module:wkbundleproduct/views/templates/hook/_partials/bundle-sub-products.tpl' product=$products sections=$sections idpsproduct=$idpsproduct themeType=$themeType}
                            {/block}

                        </div>
                    </div>
                {/foreach}
            </div>
            <button class="leftLst" style="background-color: {Configuration::get(
            'WK_BUNDLE_PRODUCT_SLIDE_BG')};color:{Configuration::get(
            'WK_BUNDLE_PRODUCT_SLIDE_TXT_COLOR')};"><</button>
            <button class="rightLst" style="background-color: {Configuration::get(
            'WK_BUNDLE_PRODUCT_SLIDE_BG')};color:{Configuration::get(
            'WK_BUNDLE_PRODUCT_SLIDE_TXT_COLOR')};">></button>
        </div>
    </div>
</div>