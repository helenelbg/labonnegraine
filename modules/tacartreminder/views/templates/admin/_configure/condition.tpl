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
 * this view display associated html for condition
 * many type condition exist(cart_product,cart_category, ..)
 *}
<tr id="condition_{$condition_group_id|intval}_{$condition_id|intval}_tr">
	<td>
		<input type="hidden" name="condition_{$condition_group_id|intval}[]" value="{$condition_id|intval}" />
		<input type="hidden" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_type" value="{$condition_type|escape:'htmlall':'UTF-8'}" />
		<input type="hidden" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_typevalue" value="{$condition_type_value|escape:'htmlall':'UTF-8'}"/>
		{if $condition_type == 'cart_product'}
			{l s='Products:' mod='tacartreminder'}
		{elseif $condition_type == 'cart_category'}
			{l s='Categories:' mod='tacartreminder'}
		{elseif $condition_type == 'cart_product_manufacturer'}
			{l s='Manufacturers:' mod='tacartreminder'}
		{elseif $condition_type == 'cart_product_supplier'}
			{l s='Suppliers:' mod='tacartreminder'}
		{elseif $condition_type == 'cart_amount'}
			{l s='Cart amount:' mod='tacartreminder'}
		{elseif $condition_type == 'cart_product_stockavailable'}
			{l s='Product stock (at least 1):' mod='tacartreminder'}
		{elseif $condition_type == 'cart_product_stockavailable_forall'}
			{l s='Product stock (for all):' mod='tacartreminder'}
		{elseif $condition_type == 'cart_product_quantity_total'}
			{l s='Product quantity total:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_lang'}
			{l s='Customer language:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_email'}
			{l s='Customer email:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_newsletter'}
			{l s='Customer newsletter:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_optin'}
			{l s='Customer optin:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_gender'}
			{l s='Customer gender:' mod='tacartreminder'}
		{elseif $condition_type == 'address_country'}
			{l s='Countries:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_group'}
			{l s='Customer group:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_order_count'}
			{l s='Customer order count:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_registration_date'}
			{l s='Customer registration date:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_age'}
			{l s='Customer age:' mod='tacartreminder'}
		{elseif $condition_type == 'customer_rule_already_applied'}
			{l s='Rule already applied to this same customer:' mod='tacartreminder'}
		{/if}
	</td>
	<td {if $condition_type_value!='list'} colspan="2"{/if}>
		{if $condition_type_value=='list'}
			<input type="text" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_match" value="" disabled="disabled" />
		{elseif $condition_type_value=='integer' || $condition_type_value=='string' || $condition_type_value=='price'}
			<table>
			<tr>			
				<td><select name="condition_{$condition_group_id|intval}_{$condition_id|intval}_sign" >
					{if $condition_type_value=='integer' || $condition_type_value=='price'}
						<option value="=" {if $condition_sign == '='}selected{/if}>{l s='=  (Equal)' mod='tacartreminder'}</option>
						<option value=">" {if $condition_sign == '>'}selected{/if}>{l s='>  (Exceeds)' mod='tacartreminder'}</option>
						<option value=">=" {if $condition_sign == '>='}selected{/if}>{l s='>= (Exceeds or Equal)' mod='tacartreminder'}</option>
						<option value="<" {if $condition_sign == '<'}selected{/if}>{l s='<  (Less than)' mod='tacartreminder'}</option>
						<option value="<=" {if $condition_sign == '<='}selected{/if}>&lt;= {l s='(Less than or Equal)' mod='tacartreminder'}</option>
						<option value="<>" {if $condition_sign == '<>'}selected{/if}>{l s='<> (Different)' mod='tacartreminder'}</option>
					{else}
						<option value="=" {if $condition_sign == '='}selected{/if}>{l s='Equal' mod='tacartreminder'}</option>
						<option value="contain" {if $condition_sign == 'contain'}selected{/if}>{l s='Contains' mod='tacartreminder'}</option>
						<option value="not_contain" {if $condition_sign == 'not_contain'}selected{/if}>{l s='Does not contain' mod='tacartreminder'}</option>
						<option value="<>" {if $condition_sign == '<>'}selected{/if}>{l s='Different' mod='tacartreminder'}</option>
						<option value="match" {if $condition_sign == 'match'}selected{/if}>{l s='Match' mod='tacartreminder'}</option>
					{/if}
					</select>
				</td>
				<td>
					{if $condition_type == 'customer_registration_date'}
						<div class="input-group">
							<span class="input-group-addon">{l s='Days' mod='tacartreminder'}</span>
							<input maxlength="14" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" 
								type="text" value="{$condition_value|floatval}" onchange="this.value = this.value.replace(/,/g, '.');">
						</div>
					{elseif $condition_type_value=='price'}
						<div class="input-group">
							<span class="input-group-addon">{$currency_sign|escape:'html':'UTF-8'}</span>
							<input maxlength="14" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" 
								type="text" value="{$condition_value|floatval}" onchange="this.value = this.value.replace(/,/g, '.');">
						</div>
					{else}
						<input type="text" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" value="{$condition_value|escape:'htmlall':'UTF-8'}"/>						
					{/if}
				</td>
			</tr>
			</table>
		{elseif $condition_type_value=='bool'}
			<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_value_on" value="1" {if $condition_value} checked="checked" {/if}>
					<label for="condition_{$condition_group_id|intval}_{$condition_id|intval}_value_on">{l s='Yes' mod='tacartreminder'}</label>
					<input type="radio" name="condition_{$condition_group_id|intval}_{$condition_id|intval}_value" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_value_off" value="0" {if !$condition_value} checked="checked" {/if}>
					<label for="condition_{$condition_group_id|intval}_{$condition_id|intval}_value_off">{l s='No' mod='tacartreminder'}</label>
					<a class="slide-button btn"></a>
			</span>
		{/if}
	</td>
	{if $condition_type_value=='list'}
	<td>
		<a class="btn btn-default" id="condition_{$condition_group_id|intval}_{$condition_id|intval}_choose_link" href="#condition_{$condition_group_id|intval}_{$condition_id|intval}_choose_content">
			<i class="flaticon-list30"></i>
			{l s='Select' mod='tacartreminder'}
		</a>
		<div>
			<div id="condition_{$condition_group_id|intval}_{$condition_id|intval}_choose_content">
				{$condition_choose_content}{*HTML CONTENT*}
			</div>
		</div>
	</td>
	{/if}
	<td class="text-right">
		<a class="btn btn-default" href="javascript:removeCondition({$condition_group_id|intval}, {$condition_id|intval});">
			<i class="flaticon-cancel6"></i>
		</a>
	</td>
</tr>

<script type="text/javascript">
{if $condition_type_value=='list'}
	$('#condition_{$condition_group_id|intval}_{$condition_id|intval}_choose_content').parent().hide();
	$("#condition_{$condition_group_id|intval}_{$condition_id|intval}_choose_link").fancybox({
		autoDimensions: false,
		autoSize: false,
		width: 600,
		height: 290});
	{if $condition_type != 'cart_category'}
		$(document).ready(function() { updateConditionShortDescription($('#condition_select_{$condition_group_id|intval}_{$condition_id|intval}_add')); });
	{/if}
{/if}
</script>