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
<div class="card card-block" style="background-color:unset;">
<p style="text-align:left"><strong>{l s='To get extra benefit purchase this product with following bundle -' mod='wkbundleproduct'}</strong></p>
    <div class="products wk_bundle_promotion_slider_cont" style="display: flex; flex-wrap: wrap;">
        {block name='bundle-promotion-product'}
            {if is_array($products) && count($products) > 2}
                <ul class="wk_bundle_promotion_slider">
                    {foreach from=$products item="product"}
                        <li>
                            {include file="modules/wkbundleproduct/views/templates/hook/_partials/product.tpl" product=$product  productClasses="wk-bundle-promotion col-lg-12"}
                        </li>
                    {/foreach}
                </ul>
            {else}
                {foreach from=$products item="product"}
                    {include file="modules/wkbundleproduct/views/templates/hook/_partials/product.tpl" product=$product  productClasses="wk-bundle-promotion col-lg-6"}
                {/foreach}
            {/if}
        {/block}
    </div>
</div>