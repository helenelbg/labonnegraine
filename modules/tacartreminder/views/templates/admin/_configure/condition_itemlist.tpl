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
 * View for condition list use in rule setting
 *}
<div class="col-lg-12 bootstrap ta-to-bootstrap ta-form ta-condition-list">
	<div class="col-lg-6">
		{l s='Unselected' mod='tacartreminder'}
		{if $condition_type == 'cart_product'}
		<div class="row" style="height:30px">
					<div class="col-md-10">
						<div class="input-group">
							<span class="input-group-addon"><i class="flaticon-magnifier13"></i></span>
							<input type="text" value="" name="search" class="unselected-search-text"  placeholder="{l s='ID, name, reference, EAN' mod='tacartreminder'}"/>
						</div>
					</div>
					<div class="col-md-2">
						<a class="btn btn-default unselected-search-button" data-action-url="{$module_url|escape:'html':'UTF-8'}&ajax_action=searchItemList" data-condition-type="{$condition_type|escape:'html'}">
								{l s='GO' mod='tacartreminder'}
						</a>
					</div>
		</div>
		{/if}
		<select multiple size="10" id="condition_select_{$condition_group_id|intval}_{$condition_id|intval}_1" class="unselected-list"
		{if $condition_type == 'cart_product' || $condition_type == 'cart_category'}
			style="font-size: 10px;height: 195px"
		{/if}
		>
			{foreach from=$condition_itemlist.unselected item='item'}
				{if $condition_type == 'cart_product'}
					<option value="{$item.id|intval}">{$item.id|intval}&nbsp;{$item.name|escape:'html':'UTF-8'}&nbsp;{$item.reference|escape:'html':'UTF-8'}</option>
				{else}
				<option value="{$item.id|intval}">&nbsp;{$item.name|escape:'html':'UTF-8'}</option>
				{/if}
			{/foreach}
		</select>
		<div class="clearfix">&nbsp;</div>
		<a id="condition_select_{$condition_group_id|intval}_{$condition_id|intval}_add" class="btn btn-default btn-block" >
			{l s='Add' mod='tacartreminder'}
			<i class="flaticon-right208"></i>
		</a>
	</div>
	<div class="col-lg-6">
		{l s='Selected' mod='tacartreminder'}
		{if $condition_type == 'cart_product'}
		<div class="row" style="height:30px">
		</div>
		{/if}
		<select multiple size="10" name="condition_select_{$condition_group_id|intval}_{$condition_id|intval}[]" id="condition_select_{$condition_group_id|intval}_{$condition_id|intval}_2" class="condition_toselect" 
		{if $condition_type == 'cart_product' || $condition_type == 'cart_category'}
			style="font-size: 10px;height: 195px"
		{/if}>
			{foreach from=$condition_itemlist.selected item='item'}
				{if $condition_type == 'cart_product'}
					<option value="{$item.id|intval}">{$item.id|intval}&nbsp;{$item.name|escape:'html':'UTF-8'}&nbsp;{$item.reference|escape:'html':'UTF-8'}</option>
				{else}
				<option value="{$item.id|intval}">&nbsp;{$item.name|escape:'html':'UTF-8'}</option>
				{/if}
			{/foreach}
		</select>
		<div class="clearfix">&nbsp;</div>
		<a id="condition_select_{$condition_group_id|intval}_{$condition_id|intval}_remove" class="btn btn-default btn-block" >
			<i class="flaticon-left224"></i>
			{l s='Remove' mod='tacartreminder'}
		</a>
	</div>
</div>
			
<script type="text/javascript">
	$('#condition_select_{$condition_group_id|intval}_{$condition_id|intval}_remove').click(function() { removeConditionOption(this); updateConditionShortDescription(this); });
	$('#condition_select_{$condition_group_id|intval}_{$condition_id|intval}_add').click(function() { addConditionOption(this); updateConditionShortDescription(this); });
	$(document).ready(function() { updateConditionShortDescription($('#condition_select_{$condition_group_id|intval}_{$condition_id|intval}_add')); });
</script>