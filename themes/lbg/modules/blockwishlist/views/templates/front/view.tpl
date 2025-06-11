{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $products}
	
	<div id="order-detail-content" class="table_block table-responsive">
		<table id="cart_summary" class="table_wishlist_view table table-bordered {if $PS_STOCK_MANAGEMENT}stock-management-on{else}stock-management-off{/if}">
			<thead>
				<tr>
					<th class="cart_product first_item">{l s='Produit'}</th>
					<th class="cart_description item">{l s='Description'}</th>
					{if $PS_STOCK_MANAGEMENT}
						{assign var='col_span_subtotal' value='3'}
						<th class="cart_avail item text-center">{l s='Disponibilité'}</th>
					{else}
						{assign var='col_span_subtotal' value='2'}
					{/if}
					<th class="cart_unit item text-right">{l s='Prix ​​unitaire'}</th>
					<th class="cart_quantity item text-center">{l s='Qté'}</th>
					<th class="cart_total item text-right">{l s='Panier'}</th>
				</tr>
			</thead>
			<tfoot>
				
				
			</tfoot>
			<tbody>
				{assign var='odd' value=0}
				{foreach $products as $product}
					{assign var='productId' value=$product.id_product}
					{assign var='productAttributeId' value=$product.id_product_attribute}
					{assign var='quantityDisplayed' value=0}
					{assign var='odd' value=($odd+1)%2}
					{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
					{* Display the product line *}
					{assign var='productLast' value=$product@last}
					{assign var='productFirst' value=$product@first}

					<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="cart_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} alternate_item{/if} address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}">
						<td class="cart_product">
							<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'small_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
						</td>

						<td class="cart_description">
							{capture name=sep} : {/capture}
							{capture}{l s=' : '}{/capture}
							<p class="product-name"><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></p>
							{if isset($product.attributes_small)}
								<small>
									<a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockwishlist'}">
										{$product.attributes_small|escape:'html':'UTF-8'}
									</a>
								</small>
							{/if}

								{if $product.reference}<small class="cart_ref">{l s='SKU'}{$smarty.capture.default}{$product.reference|escape:'html':'UTF-8'}</small>{/if}
							{if isset($product.attributes) && $product.attributes}<small><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.attributes|@replace: $smarty.capture.sep:$smarty.capture.default|escape:'html':'UTF-8'}</a></small>{/if}
						</td>
						{if $PS_STOCK_MANAGEMENT}
							<td class="cart_avail"><span class="label{if $product.attribute_quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.attribute_quantity <= 0} label-warning{else} label-success{/if}">{if $product.attribute_quantity <= 0}{if isset($product.allow_oosp) && $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='En Stock'}{/if}{else}{l s='En rupture de stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='En Stock'}{/if}{/if}</span>{if !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}</td>
						{/if}
						<td class="cart_unit" data-title="{l s='Prix ​​unitaire'}">
							<ul class="price text-right" id="product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
								{if !empty($product.gift)}
									<li class="gift-icon">{l s='Offert!'}</li>
								{else}
									<li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price}</li>
								{/if}
							</ul>
						</td>

						<td class="cart_quantity text-center" data-title="{l s='Qté'}">
							{if (isset($cannotModify) && $cannotModify == 1)}
								<span>
									{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
										{$product.customizationQuantityTotal}
									{else}
										{$product.cart_quantity-$quantityDisplayed}
									{/if}
								</span>
							{else}
								{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
									<span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
								{/if}
								{if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}

									<input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}_hidden" />
									<input size="2" type="text" autocomplete="off" id="quantity_{$product.id_product}_{$product.id_product_attribute}" value="{$product.quantity|intval}" class="cart_quantity_input form-control grey" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" onchange="$('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(
										
										Math.max(parseInt($('#quantity_{$product.id_product}_{$product.id_product_attribute}').val()) || 0, 0)
										
										
										); WishlistProductManage('wlp_bought_{$product.id_product_attribute}', 'update', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());"/>
									<div class="cart_quantity_button clearfix">
									{if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
										<a rel="nofollow" class="cart_quantity_down btn btn-default button-minus" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="javascript:;" onclick="$('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(
										
										Math.max(parseInt($('#quantity_{$product.id_product}_{$product.id_product_attribute}').val())-1,0)
										
										
										); WishlistProductManage('wlp_bought_{$product.id_product_attribute}', 'update', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());">
									<span><i class="icon-minus"></i></span>
									</a>
									{else}
										<a class="cart_quantity_down btn btn-default button-minus disabled" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
										<span><i class="icon-minus"></i></span>
									</a>
									{/if}
										<a rel="nofollow" class="cart_quantity_up btn btn-default button-plus" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="javascript:;" onclick="$('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(parseInt($('#quantity_{$product.id_product}_{$product.id_product_attribute}').val())+1); WishlistProductManage('wlp_bought_{$product.id_product_attribute}', 'update', '{$id_wishlist}', '{$product.id_product}', '{$product.id_product_attribute}', $('#quantity_{$product.id_product}_{$product.id_product_attribute}').val(), $('#priority_{$product.id_product}_{$product.id_product_attribute}').val());"><span><i class="icon-plus"></i></span></a>
									</div>
								{/if}
							{/if}
						</td>

						<td class="cart_total" data-title="{l s='Panier'}">
							
							{if $product.attribute_quantity <= 0}
								
								<a style="display:none" class="button ajax_add_to_cart_button btn btn-default" data-id-product="{$product.id_product}" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$product.id_product}&amp;token={$token}&amp;add")|escape:'html':'UTF-8'}" title="{l s='Add to cart'}">
									<span>{l s='Me prévenir de sa disponibilité' mod='blockwishlist'}</span>
								</a>
							
							{else}
							
								<a class=" btn btn-default button ajax_add_to_cart_button add-to-cart-in-wl" href="{$link->getPageLink('cart', true, NULL, "qty={$product.quantity|intval}&id_product={$product.id_product|intval}&add")|escape:'html':'UTF-8'}" data-id-attribute="{$product.id_product_attribute}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{$product.quantity|intval}" title="{l s='Ajouter au panier' mod='blockwishlist'}">									
									<img src="/themes/default-bootstrap/img/picto-panier-off.png" alt="">
									<span>{l s='Ajouter au panier' mod='blockwishlist'}</span>			
								</a>
								
							{/if}
						
						</td>

					</tr>
					
				{/foreach}
				{assign var='last_was_odd' value=$product@iteration%2}
			
			</tbody>
		</table>
	</div> <!-- end order-detail-content -->
   
   
    {if !$refresh}
        <form method="post" class="wl_send box unvisible" onsubmit="return (false);">
        <a id="hideSendWishlist" class="button_account btn icon"  href="#" onclick="WishlistVisibility('wl_send', 'SendWishlist'); return false;" rel="nofollow" title="{l s='Close this wishlist' mod='blockwishlist'}">
            <i class="icon-remove"></i>
        </a>
            <fieldset>
                <div class="required form-group">
                    <label for="email1">{l s='Email' mod='blockwishlist'}1 <sup>*</sup></label>
                    <input type="text" name="email1" id="email1" class="form-control"/>
                </div>
                {section name=i loop=11 start=2}
                    <div class="form-group">
                        <label for="email{$smarty.section.i.index}">{l s='Email' mod='blockwishlist'}{$smarty.section.i.index}</label>
                        <input type="text" name="email{$smarty.section.i.index}" id="email{$smarty.section.i.index}"
                               class="form-control"/>
                    </div>
                {/section}
                <div class="submit">
                    <button class="btn btn-default button button-small" type="submit" name="submitWishlist"
                            onclick="WishlistSend('wl_send', '{$id_wishlist}', 'email');">
                        <span>{l s='Send' mod='blockwishlist'}</span>
                    </button>
                </div>
                <p class="required">
                    <sup>*</sup> {l s='Required field' mod='blockwishlist'}
                </p>
            </fieldset>
        </form>
        {if count($productsBoughts)}
            <table class="wlp_bought_infos unvisible table table-bordered table-responsive">
                <thead>
                <tr>
                    <th class="first_item">{l s='Product' mod='blockwishlist'}</th>
                    <th class="item">{l s='Quantity' mod='blockwishlist'}</th>
                    <th class="item">{l s='Offered by' mod='blockwishlist'}</th>
                    <th class="last_item">{l s='Date' mod='blockwishlist'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$productsBoughts item=product name=i}
                    {foreach from=$product.bought item=bought name=j}
                        {if $bought.quantity > 0}
                            <tr>
                                <td class="first_item">
									<span style="float:left;">
										<img
                                                src="{$link->getImageLink($product.link_rewrite, $product.cover, 'small_default')|escape:'html':'UTF-8'}"
                                                alt="{$product.name|escape:'html':'UTF-8'}"/>
									</span>
									<span style="float:left;">
										{$product.name|truncate:40:'...'|escape:'html':'UTF-8'}
                                        {if isset($product.attributes_small)}
                                            <br/>
                                            <i>{$product.attributes_small|escape:'html':'UTF-8'}</i>
                                        {/if}
									</span>
                                </td>
                                <td class="item align_center">
                                    {$bought.quantity|intval}
                                </td>
                                <td class="item align_center">
                                    {$bought.firstname} {$bought.lastname}
                                </td>
                                <td class="last_item align_center">
                                    {$bought.date_add|date_format:"%Y-%m-%d"}
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                {/foreach}
                </tbody>
            </table>
        {/if}
    {/if}
{else}
    <p class="alert alert-warning">
        {l s='No products' mod='blockwishlist'}
    </p>
{/if}