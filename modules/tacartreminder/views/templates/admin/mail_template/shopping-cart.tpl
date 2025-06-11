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
 * template to display shopping cart
 * this template is used to render shopping cart in email
 * A developper can be override this file with your template
 *}
<table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse"><!-- Title -->
<tbody>
{assign var='have_non_virtual_products' value=false}
{foreach $products as $product}
	{if $product.is_virtual == 0}
		{assign var='have_non_virtual_products' value=true}						
	{/if}
	{assign var='productId' value=$product.id_product}
	{assign var='productAttributeId' value=$product.id_product_attribute}
	{assign var='quantityDisplayed' value=0}
	{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
	{* Display the product line *}
	{include file="$tpl_mt_path/shopping-cart-product-line.tpl" id_lang="$id_lang"}
	{* Then the customized datas ones*}
	{if isset($customizedDatas.$productId.$productAttributeId)}
		{foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
			<tr>
				<td></td>
				<td colspan="3">
					{foreach $customization.datas as $type => $custom_data}
						{if $type == $CUSTOMIZE_FILE}
								<ul>
									{foreach $custom_data as $picture}
										<li><img src="{$pic_dir|escape:'quotes':'UTF-8'}{$picture.value|escape:'quotes':'UTF-8'}_small" alt="" width="50px" height="auto"/></li>
									{/foreach}
								</ul>
						{elseif $type == $CUSTOMIZE_TEXTFIELD}
							<ul>
								{foreach $custom_data as $textField}
									<li>
										{if $textField.name}
											{$textField.name|escape:'html':'UTF-8'}
										{else}
											{l s='Text #' mod='tacartreminder'}{$textField@index+1|escape:'html':'UTF-8'}
										{/if}
										: {$textField.value|escape:'html':'UTF-8'}
									</li>
								{/foreach}
							</ul>
						{/if}
					{/foreach}
				</td>
				<td>
					{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
						{$customizedDatas.$productId.$productAttributeId|@count|intval}
					{else}
						{$product.cart_quantity|intval - $quantityDisplayed|intval}
					{/if}
				</td>
			</tr>
			{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
		{/foreach}
		{* If it exists also some uncustomized products *}
		{if $product.quantity-$quantityDisplayed > 0}
			{include file="$tpl_product_line_path" }
		{/if}
	{/if}
{/foreach}
{foreach $gift_products as $product}
					{assign var='productId' value=$product.id_product}
					{assign var='productAttributeId' value=$product.id_product_attribute}
					{assign var='quantityDisplayed' value=0}
					{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
					{assign var='cannotModify' value=1}
					{* Display the gift product line *}
					{include file="$tpl_product_line_path" productLast=$product@last productFirst=$product@first}
{/foreach}
</tbody>
</table>