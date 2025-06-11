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
 * View to detail the rule select by smart cart reminder
 * User can see that why rule is selected for a cart
 *}
{*** CONTAINER ***}
<div class="ta-to-bootstrap">

{*** CART INFORMATION ***}
<div class="ta-panel">
<div class="ta-panel-heading"><i class="flaticon-shopping11"></i> {l s='Cart summary' mod='tacartreminder'}</div>
		<table class="table ta-table" id="orderProducts">
			<thead>
				<tr>
					<th class="fixed-width-xs">&nbsp;</th>
					<th><span class="title_box">{l s='Product' mod='tacartreminder'}</span></th>
					<th class="text-right fixed-width-md"><span class="title_box">{l s='Unit price' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Quantity' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-sm"><span class="title_box">{l s='Stock' mod='tacartreminder'}</span></th>
					<th class="text-right fixed-width-sm"><span class="title_box">{l s='Total' mod='tacartreminder'}</span></th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$products item='product'}
				{if isset($customized_datas[$product.id_product][$product.id_product_attribute][$product.id_address_delivery])}
					<tr>
						<td>{$product.image}{*HTML CONTENT*}</td>
						<td>
									<span class="productName">{$product.name|escape:'html':'UTF-8'}</span>{if isset($product.attributes)}<br />{$product.attributes|escape:'html':'UTF-8'}{/if}<br />
								{if $product.reference}{l s='Ref:' mod='tacartreminder'} {$product.reference|escape:'html':'UTF-8'}{/if}
								{if $product.reference && $product.supplier_reference} / {$product.supplier_reference|escape:'html':'UTF-8'}{/if}
							
						</td>
						<td class="text-right">{displayWtPriceWithCurrency price=$product.price_wt currency=$currency}</td>
						<td class="text-center">{$product.customization_quantity|intval}</td>
						<td class="text-center">{$product.qty_in_stock|escape:'html':'UTF-8'}</td>
						<td class="text-right">{displayWtPriceWithCurrency price=$product.total_customization_wt currency=$currency}</td>
					</tr>
					{foreach from=$customized_datas[$product.id_product][$product.id_product_attribute][$product.id_address_delivery] item='customization'}
					<tr>
						<td colspan="2">
						{foreach from=$customization.datas key='type' item='datas'}
							{if $type == constant('Product::CUSTOMIZE_FILE')}
								<ul style="margin: 0; padding: 0; list-style-type: none;">
								{foreach from=$datas key='index' item='data'}
										<li style="display: inline; margin: 2px;">
											<a href="displayImage.php?img={$data.value|escape:'quotes':'UTF-8'}&name={$order->id|intval}-file{$index|escape:'quotes':'UTF-8'}" target="_blank">
											<img src="{$pic_dir|escape:'quotes':'UTF-8'}{$data.value|escape:'quotes':'UTF-8'}_small" alt="" /></a>
										</li>
								{/foreach}
								</ul>
							{elseif $type == constant('Product::CUSTOMIZE_TEXTFIELD')}
								<div class="form-horizontal">
									{foreach from=$datas key='index' item='data'}
										<div class="form-group">
											<span class="control-label col-lg-3"><strong>{if $data.name}{$data.name|escape:'htmlall':'UTF-8'}{else}{l s='Text #' mod='tacartreminder'}{$index|escape:'quotes':'UTF-8'}{/if}</strong></span>
											<div class="col-lg-9">
												<p class="form-control-static">{$data.value|escape:'html':'UTF-8'}</p>
											</div>
										</div>
									{/foreach}
								</div>
							{/if}
						{/foreach}
						</td>
						<td></td>
						<td class="text-center">{$customization.quantity|intval}</td>
						<td></td>
						<td></td>
					</tr>
					{/foreach}
				{/if}
				
				{if $product.cart_quantity > $product.customization_quantity}
					<tr>
						<td>{$product.image}{*HTML CONTENT*}</td>
						<td>
							<span class="productName">{$product.name|escape:'html':'UTF-8'}</span>{if isset($product.attributes)}<br />{$product.attributes|escape:'html':'UTF-8'}{/if}<br />
							{if $product.reference}{l s='Ref:' mod='tacartreminder'} {$product.reference|escape:'html':'UTF-8'}{/if}
							{if $product.reference && $product.supplier_reference} / {$product.supplier_reference|escape:'html':'UTF-8'}{/if}
						</td>
						<td class="text-right">{displayWtPriceWithCurrency price=$product.product_price currency=$currency}</td>
						<td class="text-center">{math equation='x - y' x=$product.cart_quantity y=$product.customization_quantity|intval}</td>
						<td class="text-center">{$product.qty_in_stock|escape:'html':'UTF-8'}</td>
						<td class="text-right">{displayWtPriceWithCurrency price=$product.product_total currency=$currency}</td>
					</tr>
				{/if}
			{/foreach}
			<tr>
				<td colspan="5">{l s='Total cost of products(tax excluded):' mod='tacartreminder'}</td>
				<td class="text-right">{displayWtPriceWithCurrency price=$total_products_ht currency=$currency}</td>
			</tr>
			<tr>
				<td colspan="5">{l s='Total cost of products:' mod='tacartreminder'}</td>
				<td class="text-right">{displayWtPriceWithCurrency price=$total_products currency=$currency}</td>
			</tr>
					
			{if $total_discounts != 0}
			<tr>
				<td colspan="5">{l s='Total value of vouchers:' mod='tacartreminder'}</td>
				<td class="text-right">{displayWtPriceWithCurrency price=$total_discounts currency=$currency}</td>
			</tr>
			{/if}
			{if $total_wrapping > 0}
			<tr>
				<td colspan="5">{l s='Total cost of gift wrapping:' mod='tacartreminder'}</td>
				<td class="text-right">{displayWtPriceWithCurrency price=$total_wrapping currency=$currency}</td>
			</tr>
			{/if}
			{if $cart->getOrderTotal(true, Cart::ONLY_SHIPPING) > 0}
			<tr>
				<td colspan="5">{l s='Total cost of shipping:' mod='tacartreminder'}</td>
				<td class="text-right">{displayWtPriceWithCurrency price=$total_shipping currency=$currency}</td>
			</tr>
			{/if}
			<tr>
				<td colspan="5" class=" success"><strong>{l s='Total:' mod='tacartreminder'}</strong></td>
				<td class="text-right success"><strong>{displayWtPriceWithCurrency price=$total_price currency=$currency}</strong></td>
			</tr>
		</tbody>
	</table>
	
	{if $discounts}
	<table class="table">
		<tr>
			<th><img src="../img/admin/coupon.gif" alt="{l s='Discounts' mod='tacartreminder'}" />{l s='Discount name' mod='tacartreminder'}</th>
			<th align="center" style="width: 100px">{l s='Value' mod='tacartreminder'}</th>
		</tr>
		{foreach from=$discounts item='discount'}
			<tr>
				<td><a href="{$link->getAdminLink('AdminDiscounts')|escape:'html':'UTF-8'}&id_discount={$discount.id_discount|intval}&updatediscount">{$discount.name|escape:'html':'UTF-8'}</a></td>
				<td align="center">- {displayWtPriceWithCurrency price=$discount.value_real currency=$currency}</td>
			</tr>
		{/foreach}
	</table>
	{/if}
	<div class="alert alert-warning">
		{l s='For this particular customer group, prices are displayed as:' mod='tacartreminder'} <b>{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC}{l s='Tax excluded' mod='tacartreminder'}{else}{l s='Tax included' mod='tacartreminder'}{/if}</b>
	</div>
</div>
{*** END CART INFORMATION ***}
{if isset($journal) && $journal->id}
<div class="ta-alert alert-info">
	{l s='When a cart reminder is running, finished or canceled, the first rule that has been applied to the cart is selected.' mod='tacartreminder'}
</div>
{/if}
{*** COMMON CONDITION ***}
{assign var="ruleselected" value="no"}
{if isset($checkrules.rules) && $checkrules.rules && $checkrules.rules|@count > 0}
<div class="panel-groupcondition common-condition {if $display_not_applicable}ko{elseif $checkrules.rule_id_selected}success{/if}">
		<div class="panel-groupcondition-heading">{l s='Common condition' mod='tacartreminder'}
		</div>
		<div class="ta-alert alert-info">
			{l s='Common conditions must all be valid.' mod='tacartreminder'}
		</div>
		<div class="condition-line {if $checkrules.common_conditions.is_not_unsubscribed.test}success{/if}">
			<span class="condition-value">{if $checkrules.common_conditions.is_not_unsubscribed.test}<i class="flaticon-check33"></i>{else}<i class="flaticon-cancel6"></i>{/if}</span>
			<span class="condition-title">{if $checkrules.common_conditions.is_not_unsubscribed.test}{l s='The customer is not unsubscribed' mod='tacartreminder'}{else}{l s='The customer is unsubscribed' mod='tacartreminder'}{/if}</span>
		</div>
		{if isset($checkrules.common_conditions.afterreminder)}
		<div class="condition-line {if $checkrules.common_conditions.afterreminder.test}success{/if}">
			<span class="condition-value">{if $checkrules.common_conditions.afterreminder.test}<i class="flaticon-check33"></i>{else}<i class="flaticon-cancel6"></i>{/if}</span>
			<span class="condition-title">{$checkrules.common_conditions.afterreminder.info|escape:'htmlall':'UTF-8'}</span>
		</div>
		{/if}
</div>
{*** END COMMON CONDITION ***}
{*** RULES CONDITION ***}

<div id="checkrules">
{foreach $checkrules.rules key=cptrule item=checkrule}
<div class="block-rule {if $checkrules.rule_id_selected == $checkrule.rule->id && $display_not_applicable}condition-ko{elseif $checkrules.rule_id_selected == $checkrule.rule->id}condition-success{assign var="ruleselected" value="yes"}{elseif $checkrules.rule_id_selected|intval && $ruleselected=='no' && !$display_not_applicable}after-success{elseif $checkrules.rule_id_selected|intval && $ruleselected=='no' && $display_not_applicable}after-ko{/if}">
<div class="clearfix rule-summary">
	<span class="resultrule resultrule-success pull-left">
		{if $checkrule.cg|@count > 0}
			{$checkrule.nbsuccess|intval}
		{else}
			0
		{/if}
	</span>
	<span class="resultrule  resultrule-error pull-left">
		{if $checkrule.cg|@count > 0}
			{(($checkrule.cg|@count)-($checkrule.nbsuccess|intval))}
		{else}
			0
		{/if}
	</span>
	<span class="title-rule">{$checkrule.rule->name|escape:'html':'UTF-8'}</span>
	<span class="cg-rule-openorclose {if $checkrules.rule_id_selected == $checkrule.rule->id}flaticon-minus87{else}open flaticon-add133{/if}" data-id-rule="{$checkrule.rule->id|intval}">
	</span>
	
</div>
<div class="clearfix">
	{if $checkrule.cg|@count > 0}
		{foreach $checkrule.cg key=cptcg item=group}
		<div class="panel-groupcondition groupcondition-{$checkrule.rule->id|intval}" {if $checkrules.rule_id_selected != $checkrule.rule->id}style="display:none"{/if}>
			<div class="panel-groupcondition-heading"><i class="flaticon-verified18"></i>&nbsp;{l s='Group' mod='tacartreminder'}&nbsp;{($cptcg+1)|intval}
				<span class="badge-rule badge-rule-sucess">{$group.cptok|intval}</span>
				<span class="badge-rule badge-rule-error">{$group.conditions|@count - $group.cptok|intval}</span>
			</div>
			<div class="clearfix">
				<table class="table">
					<thead>
						<tr>
							<th class="text-center"><span class="title_box" style="text-align:right">{l s='Condition' mod='tacartreminder'}</span></th>
							<th class="text-center"><span class="title_box">{l s='Value' mod='tacartreminder'}</span></th>
							<th class="text-center"><span class="title_box">{l s='Sign' mod='tacartreminder'}</span></th>
							<th class="text-center"><span class="title_box">{l s='Condition Value' mod='tacartreminder'}</span></th>
						</tr
					</thead>
					<tbody>
					{foreach $group.conditions key=cpt item=condition}
					<tr class="{cycle values="odd,even"} {if $condition.condition_result}success{else}error{/if}" >
						<td style="text-align:right">
						{if $condition.condition_type == 'cart_category'}
							{l s='Categories' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_product'}
							{l s='Products' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_product_manufacturer'}
							{l s='Manufacturers' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_product_supplier'}
							{l s='Suppliers' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_amount'}
							{l s='Total cost of products(tax excluded)' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_product_stockavailable'}
							{l s='Stock product (at least 1)' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_product_stockavailable_forall'}
							{l s='Stock product (for all)' mod='tacartreminder'}
						{elseif $condition.condition_type == 'cart_product_quantity_total'}
							{l s='Product quantity total' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_lang'}
							{l s='Customer lang' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_email'}
							{l s='Customer email' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_newsletter'}
							{l s='Customer newsletter' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_optin'}
							{l s='Customer optin' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_gender'}
							{l s='Customer gender' mod='tacartreminder'}
						{elseif $condition.condition_type == 'address_country'}
							{l s='Country address' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_group'}
							{l s='Customer group' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_order_count'}
							{l s='Customer order count' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_registration_date'}
							{l s='Customer registration date' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_age'}
							{l s='Customer age' mod='tacartreminder'}
						{elseif $condition.condition_type == 'customer_rule_already_applied'}
							{l s='Rule already applied for this customer:' mod='tacartreminder'}
						{else}
							{$condition.condition_type|escape:'html':'UTF-8'}
						{/if}
						</td>
						{if $condition.condition_typevalue == 'list'}
							<td style="text-align:left"><i class="flaticon-verified18"></i>&nbsp;{$condition.value1|truncate:30:"...":true|escape:'html':'UTF-8'}</td> 
							<td colspan="2" style="text-align:left">{if $condition.condition_result}{l s='included in the condition list' mod='tacartreminder'}{else}{l s='not included in the condition list' mod='tacartreminder'}{/if}</td>
						{else}
							<td>
								{if $condition.condition_typevalue == 'price'}
									{displayPrice price=$condition.value1}
								{else}
									{if $condition.condition_typevalue == 'bool'}
										{if $condition.value1|intval > 0}
											{l s='Yes' mod='tacartreminder'}
										{else}
											{l s='No' mod='tacartreminder'}
										{/if} 
									{else}
										{if $condition.condition_type == 'cart_product_stockavailable' ||
											$condition.condition_type == 'cart_product_stockavailable_forall'}
											{$condition.value1}{*HTML CONTENT*}
										{else}
											{$condition.value1|escape:'html':'UTF-8'}
										{/if}
									{/if}
								{/if}
							</td>
							<td>
								{if $condition.sign == 'not_contain'}
									{l s='Does not contain' mod='tacartreminder'}
								{else}
									{$condition.sign|escape:'html':'UTF-8'}
								{/if}
								
							</td>
							<td>
								{if $condition.condition_typevalue == 'price'}
									{displayPrice price=$condition.value2}
								{else}
									{if $condition.condition_typevalue == 'bool'}
										{if $condition.value2|intval > 0}
											{l s='Yes' mod='tacartreminder'}
										{else}
											{l s='No' mod='tacartreminder'}
										{/if} 
									{else}
										{$condition.value2|escape:'html':'UTF-8'}
									{/if}
									
								{/if}
							</td>
						{/if}
					</tr>
					{/foreach}
					</tbody>			
				</table>
			</div>
		</div>	
		{/foreach}
	{else}
		<div class="panel-groupcondition groupcondition-{$checkrule.rule->id|intval}" {if $checkrules.rule_id_selected != $checkrule.rule->id}style="display:none"{/if}>
			{l s='There is no condition saved for this rule' mod='tacartreminder'}
		</div>
	{/if}
</div>
</div>
{/foreach}
</div>
{*** END RULES CONDITION ***}
{else}
<p class="ta-alert alert-info">
		{l s='No rules available or active.' mod='tacartreminder'}
</p>
{/if}
</div>
{*** END CONTAINER ***}
