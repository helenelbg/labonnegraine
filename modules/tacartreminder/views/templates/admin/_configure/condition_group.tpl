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
 **          V 1.0.0                 *Stock Cart product (at least 1):
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
 * display condition group
 *}
<tr id="condition_group_{$condition_group_id|intval}_tr" data-count-condition="{$conditions|@count}" data-id-group-condition="{$condition_group_id|intval}">
	<td>
		<a class="btn btn-default" href="javascript:removeConditionGroup({$condition_group_id|intval});">
			<i class="flaticon-cancel6 text-danger"></i>
		</a>
	</td>
	<td>
		<div class="col-lg-12">
			<input type="hidden" name="condition_group[]" value="{$condition_group_id|intval}" />
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4">{l s='Add a rule applicable to' mod='tacartreminder'}</label>
			<div class="col-lg-4">
				<select class="form-control" id="condition_type_{$condition_group_id|intval}">
					<option value="">{l s='-- Choose --' mod='tacartreminder'}</option>
					<option value="cart_product" >{l s='Cart - Products' mod='tacartreminder'}</option>
					<option value="cart_category">{l s='Cart - Product Categories' mod='tacartreminder'}</option>
					<option value="cart_product_manufacturer">{l s='Cart - Product Manufacturers' mod='tacartreminder'}</option>
					<option value="cart_product_supplier">{l s='Cart - Product Suppliers' mod='tacartreminder'}</option>
					<option value="cart_amount">{l s='Cart - Amount (before tax)' mod='tacartreminder'}</option>
					<option value="cart_product_stockavailable">{l s='Cart - Product stock (at least 1)' mod='tacartreminder'}</option>
					<option value="cart_product_stockavailable_forall">{l s='Cart - Product stock (for all)' mod='tacartreminder'}</option>
					<option value="cart_product_quantity_total">{l s='Cart - Product quantity total' mod='tacartreminder'}</option>
					<option value="customer_gender">{l s='Customer - Gender' mod='tacartreminder'}</option>
					<option value="customer_order_count">{l s='Customer - Order count' mod='tacartreminder'}</option>
					<option value="customer_age">{l s='Customer - Age' mod='tacartreminder'}</option>
					<option value="customer_lang">{l s='Customer - Language' mod='tacartreminder'}</option>
					<option value="customer_email">{l s='Customer - Email' mod='tacartreminder'}</option>
					<option value="customer_newsletter">{l s='Customer - Newsletter' mod='tacartreminder'}</option>
					<option value="customer_group">{l s='Customer - Group' mod='tacartreminder'}</option>
					<option value="customer_registration_date">{l s='Customer - Registration date' mod='tacartreminder'}</option>
					<option value="customer_rule_already_applied">{l s='Customer - Rule already applied' mod='tacartreminder'}</option>
					<option value="customer_optin">{l s='Customer - Optin' mod='tacartreminder'}</option>
					<option value="address_country">{l s='Customer - Country address' mod='tacartreminder'}</option>
					
				</select>
			</div>
			<div class="col-lg-4">
				<a class="btn btn-default" href="javascript:addCondition({$condition_group_id|intval});">
					<i class="flaticon-add11"></i>
					{l s='Add' mod='tacartreminder'}
				</a>
			</div>

		</div>
		{l s='The cart must meet all of these:' mod='tacartreminder'}
		<table id="condition_table_{$condition_group_id|intval}" class="table table-bordered">
			
			{if isset($conditions) && $conditions|@count > 0}
				{foreach from=$conditions item='condition'}
					{$condition}{*HTML CONTENT*}
				{/foreach}
			{/if}
		</table>
	</td>
</tr>
