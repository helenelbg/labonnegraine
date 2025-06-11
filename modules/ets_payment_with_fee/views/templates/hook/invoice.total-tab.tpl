{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<table id="total-tab" width="100%">

	<tr>
		<td class="grey" width="70%">
			{$Total_Products_text|escape:'html':'UTF-8'}
		</td>
		<td class="white" width="30%">
			{displayPrice currency=$order->id_currency price=$footer.products_before_discounts_tax_excl}
		</td>
	</tr>

	{if $footer.product_discounts_tax_excl > 0}

		<tr>
			<td class="grey" width="70%">
				{$Total_Discounts_text|escape:'html':'UTF-8'}
			</td>
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
			</td>
		</tr>

	{/if}
	{if !$order->isVirtual()}
	<tr>
		<td class="grey" width="70%">
			{$Shipping_Costs_text|escape:'html':'UTF-8'}
		</td>
		<td class="white" width="30%">
			{if $footer.shipping_tax_excl > 0}
				{displayPrice currency=$order->id_currency price=$footer.shipping_tax_excl}
			{else}
				{$Free_Shipping_text|escape:'html':'UTF-8'}
			{/if}
		</td>
	</tr>
	{/if}
    {if $custom_payment && $fee_payment > 0}
        <tr>
            <td class="grey" width="70%">
    			{if $display_tax}{$text_payment_fee_incl|escape:'html':'UTF-8'}{else}{$text_payment_fee_excl|escape:'html':'UTF-8'}{/if}
    		</td>
    		<td class="white" width="30%">
    			{if $fee_payment > 0}
    				{displayPrice currency=$order->id_currency price=$fee_payment}
    			{else}
    				{$Free_text|escape:'html':'UTF-8'}
    			{/if}
    		</td>
        </tr>
    {/if}
	{if $footer.wrapping_tax_excl > 0}
		<tr>
			<td class="grey">
				{$Wrapping_Costs_text|escape:'html':'UTF-8'}
			</td>
			<td class="white">{displayPrice currency=$order->id_currency price=$footer.wrapping_tax_excl}</td>
		</tr>
	{/if}

	<tr class="bold">
		<td class="grey">
			{$Total_tax_excl_text|escape:'html':'UTF-8'}
		</td>
		<td class="white">
			{displayPrice currency=$order->id_currency price=$footer.total_paid_tax_excl}
		</td>
	</tr>
	{if $footer.total_taxes > 0}
	<tr class="bold">
		<td class="grey">
			{$Total_tax_text|escape:'html':'UTF-8'}
		</td>
		<td class="white">
			{displayPrice currency=$order->id_currency price=$footer.total_taxes}
		</td>
	</tr>
	{/if}
	<tr class="bold big">
		<td class="grey">
			{$Total_text|escape:'html':'UTF-8'}
		</td>
		<td class="white">
			{displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}
		</td>
	</tr>
</table>
