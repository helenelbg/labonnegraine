{*
 * Cart Reminder
 * 
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra
 *    @version 1.0.0
 *    @license   Commercial license
 *
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _           
 * |_   _(_)          / _ \     | | (_)          
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____ 
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *                                              
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * Template to display product line in shopping cart
 * this template is used to render shopping cart in email
 * A developper can be override this file with your template
 *}
<tr class="product-line" style="font-size: 13px;">
	<td class="product-line-col td-product-image">
		<table class="table">
			<tr>
				<td>
					<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, $id_lang, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width|intval}" height="{$smallSize.height|intval}" {/if} /></a>
				</td>
			</tr>
		</table>
	</td>
	<td class="product-line-col td-product-description" style="vertical-align:top;">
		<table class="table">
				<tr>
					<td>
						<span class="product_name">
							<strong>{$product.name|escape:'html':'UTF-8'}</strong>{if isset($product.attributes) && $product.attributes}<br /><em>{$product.attributes|escape:'html':'UTF-8'}</em>{/if}
						</span>
					</td>
			</tr>
		</table>
	</td>
	<td class="product-line-col td-product-quantity" style="vertical-align:top;">
			<table class="table">
				<tr>
					<td>
						<span class="product-quantity">
							{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
								{$product.customizationQuantityTotal|intval}&nbsp;x
							{else}
								{$product.cart_quantity|intval - $quantityDisplayed|intval}&nbsp;x
							{/if}
						</span>
					</td>
				</tr>
			</table>
	</td>
	<td class="product-line-col" style="vertical-align:top;">
		<table class="table">
			<tr>
				<td align="center" style="min-width:35px;">
						{if !empty($product.gift)}
							{l s='Gift!' mod='tacartreminder'}
						{else}
            				{if !$priceDisplay}
								<span class="product-price {if isset($product.is_discounted) && $product.is_discounted}product-discounted{/if}">{$product.price_wt_dp|escape:'htmlall':'UTF-8'}</span>
							{else}
               	 				<span class="product-price {if isset($product.is_discounted) && $product.is_discounted}product-discounted{/if}">{$product.price_dp|escape:'htmlall':'UTF-8'}</span>
							{/if}
							{if isset($product.is_discounted) && $product.is_discounted}
								{if $product.price_without_specific_price != 0}
									{assign var='priceReductonPercent' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
									{if $priceDisplay}
										{assign var='priceReductonPercent' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * 100 * -1}
									{/if}
									{if $priceReductonPercent|round < 0}
										<br/><span class="discount-percent">{$priceReductonPercent|floatval|round|string_format:"%d"}%</span>
										&nbsp;
										<span class="product-old-price"><s>{$product.price_without_specific_price_dp|escape:'htmlall':'UTF-8'}</s></span>
									{/if}
								{else}
									{assign var='priceReductonPercent' value=0}
								{/if}
							{/if}
						{/if}
				</td>
			</tr>
		</table>
	</td>
	
</tr>