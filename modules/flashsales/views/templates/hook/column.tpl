{*
* 2022 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2022 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*}

<!-- MODULE Block flash sales -->
<div class="flashsale flashsale-column block">
	<h4 class="title_block">{Configuration::get('FLASHSALE_TITLE_COLUMN', Context::getContext()->language->id)|escape:'html':'UTF-8'}</h4>
	<div class="content_block">
	{if $flashsales.flash_sales && $flashsales.flash_sales|@count > 0}
        {if Configuration::get('FLASHSALE_CAROUSEL_COLUMN')}
            {if version_compare($smarty.const._PS_VERSION_,'1.7','<')}
                {include file=$flashsales.tpl_product_list products=$flashsales.flash_sales id="flashsale_column" class="owl-carousel owl-theme"}
            {else}
                <section class="featured-products clearfix">
                    <div class="products owl-carousel owl-theme">
                    {foreach from=$flashsales.flash_sales item=product name=myLoop}
                        {include file=$flashsales.tpl_product_list product=$product}
                    {/foreach}
                    </div>
                </section>
            {/if}
        {else}
		<ul class="flashsale-product-listing">
			{foreach from=$flashsales.flash_sales item=product name=myLoop}
			<li class="clearfix">
				<a href="{$product.link|escape:'quotes':'UTF-8'}" title="{$product.legend|escape:'html':'UTF-8'}" class="product-image clearfix">
					<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'quotes':'UTF-8'}" alt="{$product.legend|escape:'html':'UTF-8'}" />
				</a>
				<div class="product-content">
                	<h5 class="product-name">
                    	<a href="{$product.link|escape:'quotes':'UTF-8'}" title="{$product.legend|escape:'html':'UTF-8'}">
                            {$product.name|escape:'html':'UTF-8'}
                        </a>
                    </h5>
                    <div class="product-price">
                    	<span class="price special-price">{if !$flashsales.priceDisplay}{$product.price}{else}{$product.price_tax_exc}{/if}</span>
                         {if $product.specific_prices}
							{assign var='specific_price' value=$product.specific_prices}
							{if $specific_price.reduction_type == 'percentage' && ($specific_price.from == $specific_price.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_price.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_price.from))}
								<span class="price-percent-reduction">-{$specific_price.reduction*100|floatval}%</span>
							{/if}
						{/if}
                         <span class="old-price">{$product.price_without_reduction}</span>
                    </div>
                </div>
			</li>
			{/foreach}
		</ul>
        {/if}
		<div class="show-more-button">
        	<a href="{$link->getModuleLink('flashsales','page')|escape:'quotes':'UTF-8'}" class="btn btn-primary" title="{l s='Show more' mod='flashsales'}"><span>{l s='Show more' mod='flashsales'}</span></a>
        </div>
	{else}
		<p>{l s='No flash sales at this time.' mod='flashsales'}</p>
	{/if}
	</div>
</div>
<!-- /MODULE Block flashsales -->
