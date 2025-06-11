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
 * Template to display product line in text format for old client messagery
 *}
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
	{include file="$tpl_product_line_txt_path"}
	{* Then the customized datas ones*}
{/foreach}
{foreach $gift_products as $product}
					{assign var='productId' value=$product.id_product}
					{assign var='productAttributeId' value=$product.id_product_attribute}
					{assign var='quantityDisplayed' value=0}
					{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
					{assign var='cannotModify' value=1}
					{* Display the gift product line *}
					{include file="$tpl_product_line_txt_path" productLast=$product@last productFirst=$product@first}
{/foreach}
