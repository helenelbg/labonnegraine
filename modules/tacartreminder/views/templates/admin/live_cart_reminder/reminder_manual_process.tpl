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
 * It is the page display for manual remind a cart(call customer with all information need
 *}
<div id="content" class="bootstrap ta-to-bootstrap" style="margin: 5px;padding: 0;" >

<div class="row" style="padding:5px">
	<div class="col-lg-12" style="text-align:center">
		{if isset($journal_reminder['date_performed']) && $journal_reminder['performed']}
			<div class="ta-alert alert-info">
				{l s='Flagged as completed on' mod='tacartreminder'}  {dateFormat date=$journal_reminder['date_performed']|escape:'html':'UTF-8' full=1}
			</div>
		{elseif  $journal->state == 'FINISHED'}
			<div class="ta-alert alert-info">
				{l s='Reminder cart is finished for this cart' mod='tacartreminder'}
			</div>
		{elseif  $journal->state == 'CANCEL'}
			<div class="ta-alert alert-info">
				{l s='Reminder cart is canceled for this cart' mod='tacartreminder'}
			</div>
		{else}
			<a href="javascript:;" style="font-size: 14px;font-weight: bold;" data-id-reminder='{$reminder.id_reminder|intval}' data-id-cart='{$cart->id|intval}' class="btn btn-primary manual-submit-perform-reminder" data-type-perform='DONE' ><i class="flaticon-check33"></i>&nbsp;{l s='Flag as completed' mod='tacartreminder'}</a>			
		{/if}
		{*
		<a href="javascript:;"  class="btn btn-primary manual-submit-perform-reminder"  data-id-reminder='{$reminder.id_reminder|intval}' data-id-cart='{$cart->id|intval}' data-type-perform='FINISH'>
		<span title="" class="label-tooltip" data-toggle="tooltip" data-placement="left" data-original-title="{l s='Flag as completed and close all reminders for this cart' mod='tacartreminder'}" data-html="true">
		<i class="flaticon-finish"></i>&nbsp;{l s='Completed reminders' mod='tacartreminder'}</span></a>
		*}
		<span class="ta_form_loader" style="display:none" id="ta_form_loader_manual_perform"></span>
		<div class="ta_form_error" style="display: none; padding: 15px 25px" id="ta_form_error_manual_perform">
				<ul class="alert-list"></ul>
		</div>
	</div>
</div>
<div class="row">
		{*left*}
		<div class="col-lg-12">
			<div class="col-lg-6">
			<div class="panel clearfix">
				<div class="panel-heading"><span class="ta-panel-openorclose open flaticon-minus87"></span>
					<i class="icon-user"></i>
					{if $gender->name}{$gender->name|escape:'htmlall':'UTF-8'}{else}{l s='?' mod='tacartreminder'}{/if}&nbsp;{$customer->firstname|escape:'htmlall':'UTF-8'}
					{$customer->lastname|escape:'htmlall':'UTF-8'}
					[{$customer->id|intval|string_format:"%06d"}]
					-
					<a href="mailto:{$customer->email|escape:'html':'UTF-8'}"><i class="icon-envelope"></i>
						{$customer->email|escape:'html':'UTF-8'}
					</a>
				</div>
				<div class="panel-content">
				<div class="form-horizontal">
					<div class="row">
						<label class="control-label col-lg-3">{l s='Age' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
								{if isset($customer->birthday) && $customer->birthday != '0000-00-00'}
									{l s='%1$d years old (birth date: %2$s)' sprintf=[$customer_stats['age'], $customer_birthday] mod='tacartreminder'}
								{else}
									{l s='Unknown' mod='tacartreminder'}
								{/if}
							</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Registration Date' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">{$registration_date|escape:'htmlall':'UTF-8'}</p>
						</div>
					</div>
					<div class="row">
						<label class="control-label col-lg-3">{l s='Last Visit' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">{if $customer_stats['last_visit']}{$last_visit|escape:'htmlall':'UTF-8'}{else}{l s='Never' mod='tacartreminder'}{/if}</p>
						</div>
					</div>
					{if $count_better_customers != '-'}
						<div class="row">
							<label class="control-label col-lg-3">{l s='Best Customer Rank' mod='tacartreminder'}</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$count_better_customers|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
					{/if}
					{if $shop_is_feature_active}
						<div class="row">
							<label class="control-label col-lg-3">{l s='Shop' mod='tacartreminder'}</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$name_shop|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
					{/if}
					<div class="row">
						<label class="control-label col-lg-3">{l s='Language' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
								{if isset($customerLanguage)}
									{$customerLanguage->name|escape:'htmlall':'UTF-8'}
								{else}
									{l s='Unknown' mod='tacartreminder'}
								{/if}
							</p>
						</div>
					</div>
				</div>
				</div>
			</div>
			</div>
			<div class="col-lg-6">
				<div class="panel clearfix">
				<div class="panel-heading"><span class="ta-panel-openorclose open flaticon-minus87"></span>
					<i class="icon-user"></i>&nbsp;{l s='Address summary' mod='tacartreminder'}
				</div>
				<div class="panel-content">
				<div class="form-horizontal">
					{if $phones_summary && $phones_summary|@count > 0}
					<div class="row">
						<label class="control-label col-lg-3"><i class="icon-phone"></i> {l s='Phone' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
							{foreach from=$phones_summary item='phone_summary'}
								{$phone_summary|escape:'html':'UTF-8'}&nbsp;
							{/foreach}
							</p>
						</div>
					</div>
					{/if}
					{if $mobiles_summary && $mobiles_summary|@count > 0}
					<div class="row">
						<label class="control-label col-lg-3"><i class="icon-mobile-phone"></i> {l s='Mobile' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
							{foreach from=$mobiles_summary item='mobile_summary'}
								{$mobile_summary|escape:'html':'UTF-8'}&nbsp;
							{/foreach}
							</p>
						</div>
					</div>
					{/if}
					{if $countries_summary && $countries_summary|@count > 0}
					<div class="row">
						<label class="control-label col-lg-3"><i class="icon-flag"></i> {l s='Country' mod='tacartreminder'}</label>
						<div class="col-lg-9">
							<p class="form-control-static">
							{foreach from=$countries_summary item='country_summary'}
								{$country_summary|escape:'html':'UTF-8'}&nbsp;
							{/foreach}
							</p>
						</div>
					</div>
					{/if}
				</div>
				</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-heading"><span class="ta-panel-openorclose flaticon-add133"></span>
							<i class="flaticon-comment3"></i>
							{l s='Private message - cart reminder' mod='tacartreminder'} <span class="ta-badge">{$journal_messages|@count|intval}</span>
				</div>
				<div class="panel-content" style="display:none">
					<div class="row">
					<div class="ta-alert alert-info">{l s='This note will be displayed to employees but not to customers.' mod='tacartreminder'}</div>
					<span class="ta_form_loader" style="display:none"></span>
					<form action="#" class="ta-form" >
						<div class="ta_form_error" style="display: none; padding: 15px 25px">
							<ul class="alert-list"></ul>
						</div>
						<div id="message" class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Message' mod='tacartreminder'}</label>
								<div class="col-lg-9">
									<textarea id="txt_msg" class="textarea-autosize" name="message"></textarea>
									<p id="nbchars"></p>
								</div>
							</div>
							<input type="hidden" name="id_journal" value="{$journal->id|intval}" />
							<input type="hidden" name="id_reminder" value="{$reminder.id_reminder|intval}" />
							<a href="javascript:;"   class="btn btn-primary pull-right submit-message-reminder" name="submitMessageReminder">
								{l s='Save' mod='tacartreminder'}
							</a>
						</div>
					</form>
					</div>
					
					<div class="panel-highlighted messages-reminder" {if !(sizeof($journal_messages))}style="display:none"{/if} id="messages-reminder">
							{include file="$tpl_lvc_path/journal-messages.tpl" messages=$journal_messages} 
					</div>
					
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-heading"><span class="ta-panel-openorclose flaticon-add133"></span>
							<i class="flaticon-mail29"></i>
							{l s='Send an email to customer' mod='tacartreminder'}
				</div>
				<div class="panel-content" style="display:none">
					<div class="row">
						<span class="ta_form_loader" style="display:none"></span>
					<form action="#" class="ta-form">
						<div class="ta_form_error" style="display: none; padding: 15px 25px">
							<ul class="alert-list"></ul>
						</div>
						<div id="mail-customer" class="form-horizontal">
							{if sizeof($mails_templates) > 0}
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Template' mod='tacartreminder'}</label>
								<div class="col-lg-9">
									<div class="col-lg-8" >
										<select name="id_mail_template">
											{foreach from=$mails_templates item='mail_template'}
												<option value="{$mail_template.id_mail_template|intval}">{$mail_template.name|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</select>
									</div>
									<div class="col-lg-1" >
										<select name="id_lang">
											{foreach from=$languages item='language'}
												<option value="{$language.id_lang|intval}" {if  isset($customerLanguage) && $customerLanguage->id==$language.id_lang}selected{/if}>{$language.iso_code|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</select>
									</div>
									<div class="col-lg-3" >
										<a href="javascript:;" class="btn btn-default use-mail-template" data-type-render="txt" >
											{l s='Use' mod='tacartreminder'}										
										</a>
									</div>
								</div>
							</div>
							{/if}
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Subject' mod='tacartreminder'}</label>		
								<div class="col-lg-9">
									<input type="text" name="subject" value="" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Recipient' mod='tacartreminder'}</label>		
								<div class="col-lg-9">
									<input type="text" name="mail_to" value="{$customer->email|escape:'htmlall':'UTF-8'}" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='BCC' mod='tacartreminder'}</label>		
								<div class="col-lg-9">
									<input type="text" name="mail_bcc" value="" />
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Mail' mod='tacartreminder'}</label>
								<textarea id="ta_mail_content" name="content_html" class="rte autoload_rte" ></textarea>
							</div>
						</div>
						<div id="message" class="form-horizontal">
							<input type="hidden" name="id_cart" value="{$cart->id|intval}" />
							<input type="hidden" name="id_journal" value="{$journal->id|intval}" />
							<input type="hidden" name="id_reminder" value="{$reminder.id_reminder|intval}" />
							
							<a href="javascript:;"   class="btn btn-primary pull-right send-mail-customer" name="submitMailCustomer">
								{l s='Send' mod='tacartreminder'}
							</a>
						</div>
					</form>
					</div>
					
					
					
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel">
			<div class="panel-heading"><span class="ta-panel-openorclose open flaticon-minus87"></span>
						<i class="icon-shopping-cart"></i>
						{l s='Cart summary' mod='tacartreminder'} <span class="ta-badge">{$products|@count|intval}</span>
			</div>
			<div class="panel-content">
				<table class="table" id="orderProducts">
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
							<td><a href="{$link->getAdminLink('AdminProducts')|escape:'quotes':'UTF-8'}&id_product={$product.id_product|intval}&updateproduct">
										<span class="productName">{$product.name|escape:'htmlall':'UTF-8'}</span>{if isset($product.attributes)}<br />{$product.attributes|escape:'htmlall':'UTF-8'}{/if}<br />
									{if $product.reference}{l s='Ref:' mod='tacartreminder'} {$product.reference|escape:'htmlall':'UTF-8'}{/if}
									{if $product.reference && $product.supplier_reference} / {$product.supplier_reference|escape:'htmlall':'UTF-8'}{/if}
								</a>
							</td>
							<td class="text-right">{displayWtPriceWithCurrency price=$product.price_wt currency=$currency}</td>
							<td class="text-center">{$product.customization_quantity|intval}</td>
							<td class="text-center">{$product.qty_in_stock|escape:'htmlall':'UTF-8'}</td>
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
												<a href="displayImage.php?img={$data.value|escape:'quotes':'UTF-8'}-file{$index|escape:'quotes':'UTF-8'}" target="_blank">
												<img src="{$pic_dir|escape:'quotes':'UTF-8'}{$data.value|escape:'quotes':'UTF-8'}_small" alt="" /></a>
											</li>
									{/foreach}
									</ul>
								{elseif $type == constant('Product::CUSTOMIZE_TEXTFIELD')}
									<div class="form-horizontal">
										{foreach from=$datas key='index' item='data'}
											<div class="form-group">
												<span class="control-label col-lg-3"><strong>{if $data.name}{$data.name|escape:'html':'UTF-8'}{else}{l s='Text #' mod='tacartreminder'}{$index|escape:'html':'UTF-8'}{/if}</strong></span>
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
								<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&id_product={$product.id_product|intval}&updateproduct">
								<span class="productName">{$product.name|escape:'html':'UTF-8'}</span>{if isset($product.attributes)}<br />{$product.attributes|escape:'html':'UTF-8'}{/if}<br />
								{if $product.reference}{l s='Ref:' mod='tacartreminder'} {$product.reference|escape:'html':'UTF-8'}{/if}
								{if $product.reference && $product.supplier_reference} / {$product.supplier_reference|escape:'html':'UTF-8'}{/if}
								</a>
							</td>
							<td class="text-right">{displayWtPriceWithCurrency price=$product.product_price currency=$currency}</td>
							<td class="text-center">{math equation='x - y' x=$product.cart_quantity y=$product.customization_quantity|intval}</td>
							<td class="text-center">{$product.qty_in_stock|intval}</td>
							<td class="text-right">{displayWtPriceWithCurrency price=$product.product_total currency=$currency}</td>
						</tr>
					{/if}
				{/foreach}
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
		<div class="ta-alert alert-warning">
			{l s='For this particular customer group, prices are displayed as:' mod='tacartreminder'} <b>{if $tax_calculation_method == $smarty.const.PS_TAX_EXC}{l s='Tax excluded' mod='tacartreminder'}{else}{l s='Tax included' mod='tacartreminder'}{/if}</b>
		</div>	
		<div class="clear" style="height:20px;">&nbsp;</div>
	</div>
	</div>
	</div>
	</div>
	<!-- END CART SUMMARY-->
	<div class="row">
		<div class="col-lg-12">
		<div class="panel" id="vouchers_part" >
			<div class="panel-heading">
				<span class="ta-panel-openorclose open flaticon-minus87"></span>
							<i class="icon-ticket"></i>
							{l s='Vouchers in the cart' mod='tacartreminder'} <span class="ta-badge">{$discounts|@count|intval}</span>
			</div>
			<div class="panel-content">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Search for a voucher' mod='tacartreminder'} 
				</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-6">
							<div class="input-group">
								<input type="text" id="cart_rule_filter" value="" />
								<input type="hidden" id="cart_rule_customer_id" value="{$customer->id|intval}" />
								<div class="input-group-addon">
									<i class="icon-search"></i>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<span class="form-control-static">{l s='Or' mod='tacartreminder'}&nbsp;</span>
							<a href="javascript:;"   class="btn btn-default cart_rule_fancy"  data-fancybox-href="{$link->getAdminLink('AdminCartRules')|escape:'htmlall':'UTF-8'}&addcart_rule&liteDisplaying=1&submitFormAjax=1#" data-id-cart="{$cart->id|intval}" data-id-reminder="{$reminder.id_reminder|intval}">
								<i class="icon-plus-sign-alt"></i>
								{l s='Add new voucher' mod='tacartreminder'}
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<table class="table" id="voucher_list">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Name' mod='tacartreminder'}</span></th>
							<th><span class="title_box">{l s='Description' mod='tacartreminder'}</span></th>
							<th><span class="title_box">{l s='Value' mod='tacartreminder'}</span></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$discounts item='discount'}
							<tr><td>{$discount.name|escape:'html':'UTF-8'}</td>
								<td>{$discount.description|escape:'html':'UTF-8'}</td>
								<td>{displayWtPriceWithCurrency price=$discount.value_real currency=$currency}</td>
							<td><a href="javascript:remove_cart_rule_in_cart({$discount.id_discount|intval},{$cart->id|intval},{$customer->id|intval},{$reminder.id_reminder|intval})" class="btn btn-default delete_cart_discount" data-id-discount="{$discount.id_discount|intval}"><i class="icon-remove"></i>&nbsp;{l s='Delete' mod='tacartreminder'}</a><td></tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<div id="vouchers_err" class="ta-alert alert-warning" style="display:none;"></div>
			</div>
		</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel">
				<div class="panel-heading"><span class="ta-panel-openorclose open flaticon-minus87"></span>
							<i class="icon-ticket"></i>
							{l s='Customer vouchers' mod='tacartreminder'} <span class="ta-badge">{$customer_discounts|@count|intval}</span>
				</div>
				<div class="panel-content">
					<div  class="form-horizontal">
							<a href="javascript:;"   class="btn btn-primary pull-left cart_rule_fancy"  data-fancybox-href="{$link->getAdminLink('AdminCartRules')|escape:'htmlall':'UTF-8'}&addcart_rule&liteDisplaying=1&submitFormAjax=1#" data-id-cart="{$cart->id|intval}" data-id-reminder="{$reminder.id_reminder|intval}">
								{l s='Add new voucher' mod='tacartreminder'}
							</a>
					</div>
					{if count($customer_discounts)}
						<table class="table">
							<thead>
								<tr>
									<th><span class="title_box ">{l s='ID' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Code' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Name' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Status' mod='tacartreminder'}</span></th>
								<tr/>
							</thead>
							<tbody>
						{foreach $customer_discounts AS $key => $discount}
								<tr>
									<td>{$discount['id_cart_rule']|intval}</td>
									<td>{$discount['code']|escape:'html':'UTF-8'}</td>
									<td>{$discount['name']|escape:'html':'UTF-8'}</td>
									<td>
										{if $discount['active']}
											<i class="flaticon flaticon-check33"></i>
										{else}
											<i class="flaticon flaticon-cancel1"></i>
										{/if}
									</td>
								</tr>
							</tbody>
						{/foreach}
						</table>
					{else}
					<p class="text-muted text-center">
						{l s='%1$s %2$s has no discount vouchers' sprintf=[$customer->firstname, $customer->lastname] mod='tacartreminder'}
					</p>
					{/if}
					
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					<span class="ta-panel-openorclose flaticon-add133"></span>
					<i class="icon-map-marker"></i> {l s='Addresses' mod='tacartreminder'} <span class="ta-badge">{$addresses|@count|intval}</span>
				</div>
				<div class="panel-content" style="display:none">
				{if count($addresses)}
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box ">{l s='Company' mod='tacartreminder'}</span></th>
								<th><span class="title_box ">{l s='Name' mod='tacartreminder'}</span></th>
								<th><span class="title_box ">{l s='Address' mod='tacartreminder'}</span></th>
								<th><span class="title_box ">{l s='Country' mod='tacartreminder'}</span></th>
								<th><span class="title_box ">{l s='Phone number(s)' mod='tacartreminder'}</span></th>
							</tr>
						</thead>
						<tbody>
							{foreach $addresses AS $key => $address}
							<tr>
								<td>{if $address['company']}{$address['company']|escape:'htmlall':'UTF-8'}{else}--{/if}</td>
								<td>{$address['firstname']|escape:'html':'UTF-8'} {$address['lastname']|escape:'html':'UTF-8'}</td>
								<td>{$address['address1']|escape:'html':'UTF-8'} {if $address['address2']}{$address['address2']|escape:'html':'UTF-8'}{/if} {$address['postcode']|escape:'htmlall':'UTF-8'} {$address['city']|escape:'htmlall':'UTF-8'}</td>
								<td>{$address['country']|escape:'html':'UTF-8'}</td>
								<td>
									{if $address['phone']}
										{$address['phone']|escape:'html':'UTF-8'}
										{if $address['phone_mobile']}<br />{$address['phone_mobile']|escape:'html':'UTF-8'}{/if}
									{else}
										{if $address['phone_mobile']}<br />{$address['phone_mobile']|escape:'html':'UTF-8'}{else}--{/if}
									{/if}
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
				{else}
					<p class="text-muted text-center">
						{l s='%1$s %2$s has not registered any addresses yet' sprintf=[$customer->firstname, $customer->lastname] mod='tacartreminder'}
					</p>
				{/if}
			</div>
		</div>
	</div>
	<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					<span class="ta-panel-openorclose flaticon-add133"></span>
					<i class="icon-group"></i>
					{l s='Groups' mod='tacartreminder'}
					<span class="ta-badge">{$groups|@count|intval}</span>
				</div>
				<div class="panel-content" style="display:none">
					{if $groups AND count($groups)}
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box ">{l s='ID' mod='tacartreminder'}</span></th>
								<th><span class="title_box ">{l s='Name' mod='tacartreminder'}</span></th>
							</tr>
						</thead>
						<tbody>
							{foreach $groups AS $key => $group}
							<tr onclick="document.location = '?tab=AdminGroups&amp;id_group={$group['id_group']|intval}&amp;viewgroup&amp;token={getAdminToken tab='AdminGroups'}'">
								<td>{$group['id_group']|intval}</td>
								<td>
									<a href="?tab=AdminGroups&amp;id_group={$group['id_group']|intval}&amp;viewgroup&amp;token={getAdminToken tab='AdminGroups'}">
										{$group['name']|escape:'html':'UTF-8'}
									</a>
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
					{/if}
				</div>
			</div>
	</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="col-lg-6">
			{if count($connections)}
			<div class="panel">
				<div class="panel-heading">
					<span class="ta-panel-openorclose flaticon-add133"></span> <i class="icon-time"></i> {l s='Last log-in(s)' mod='tacartreminder'}
				</div>
				<div class="panel-content" style="display:none">
					<table class="table">
						<thead>
						<tr>
							<th><span class="title_box">{l s='Date' mod='tacartreminder'}</span></th>
							<th><span class="title_box">{l s='Pages viewed' mod='tacartreminder'}</span></th>
							<th><span class="title_box">{l s='Total time' mod='tacartreminder'}</span></th>
							<th><span class="title_box">{l s='Origin' mod='tacartreminder'}</span></th>
							<th><span class="title_box">{l s='IP Address' mod='tacartreminder'}</span></th>
						</tr>
						</thead>
						<tbody>
						{foreach $connections as $connection}
							<tr>
								<td>{dateFormat date=$connection['date_add'] full=0}</td>
								<td>{$connection['pages']|escape:'html':'UTF-8'}</td>
								<td>{$connection['time']|escape:'html':'UTF-8'}</td>
								<td>{$connection['http_referer']|escape:'html':'UTF-8'}</td>
								<td>{$connection['ipaddress']|escape:'html':'UTF-8'}</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			{/if}
		</div>
		<div class="col-lg-6">
			<div class="panel">
				<div class="panel-heading">
					<span class="ta-panel-openorclose flaticon-add133"></span>  <i class="icon-file"></i> {l s='Orders' mod='tacartreminder'} <span class="ta-badge">{$orders|@count|intval}</span>
				</div>
				<div class="panel-content" style="display:none">
				{if $orders AND count($orders)}
					{assign var=count_ok value=count($orders_ok)}
					{assign var=count_ko value=count($orders_ko)}
					<div class="panel">
						<div class="row">
							<div class="col-lg-6">
								<i class="icon-ok-circle icon-big"></i>
								{l s='Valid orders:' mod='tacartreminder'}
								<span class="label label-success">{$count_ok|intval}</span>
							</div>
							<div class="col-lg-6">
								<i class="icon-exclamation-sign icon-big"></i>
								{l s='Invalid orders:' mod='tacartreminder'}
								<span class="label label-danger">{$count_ko|intval}</span>
							</div>
						</div>
					</div>
					
					{if $count_ok}
						<table class="table">
							<thead>
								<tr>
									<th class="center"><span class="title_box ">{l s='ID' mod='tacartreminder'}</span></th>
									<th><span class="title_box">{l s='Date' mod='tacartreminder'}</span></th>
									<th><span class="title_box">{l s='Payment' mod='tacartreminder'}</span></th>
									<th><span class="title_box">{l s='Status' mod='tacartreminder'}</span></th>
									<th><span class="title_box">{l s='Products' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Total spent' mod='tacartreminder'}</span></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							{foreach $orders_ok AS $key => $order}
								<tr onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']|intval}&vieworder&token={getAdminToken tab='AdminOrders'}'">
									<td>{$order['id_order']|intval}</td>
									<td>{dateFormat date=$order['date_add'] full=0}</td>
									<td>{$order['payment']|escape:'html':'UTF-8'}</td>
									<td>{$order['order_state']|escape:'html':'UTF-8'}</td>
									<td>{$order['nb_products']|intval}</td>
									<td>{$order['total_paid_real']|escape:'html':'UTF-8'}</td>
									<td>
										<a class="btn btn-default" href="?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}">
											<i class='icon-search'></i> {l s='View' mod='tacartreminder'}
										</a>
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					{/if}

					{if $count_ko}
						<table class="table">
							<thead>
								<tr>
									<th><span class="title_box ">{l s='ID' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Date' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Payment' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Status' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Products' mod='tacartreminder'}</span></th>
									<th><span class="title_box ">{l s='Total spent' mod='tacartreminder'}</span></th>
								</tr>
							</thead>
							<tbody>
								{foreach $orders_ko AS $key => $order}
								<tr onclick="document.location = '?tab=AdminOrders&id_order={$order['id_order']|intval}&vieworder&token={getAdminToken tab='AdminOrders'}'">
									<td>{$order['id_order']|intval}</td>
									<td><a href="?tab=AdminOrders&amp;id_order={$order['id_order']|intval}&amp;vieworder&amp;token={getAdminToken tab='AdminOrders'}">{dateFormat date=$order['date_add'] full=0}</a></td>
									<td>{$order['payment']|escape:'html':'UTF-8'}</td>
									<td>{$order['order_state']|escape:'html':'UTF-8'}</td>
									<td>{$order['nb_products']|escape:'html':'UTF-8'}</td>
									<td>{$order['total_paid_real']|escape:'html':'UTF-8'}</td>
								</tr>
								{/foreach}
							</tbody>
						</table>	
					{/if}
				{else}
				<p class="text-muted text-center">
					{l s='%1$s %2$s has not placed any orders yet' sprintf=[$customer->firstname, $customer->lastname] mod='tacartreminder'}
				</p>
				{/if}
			</div>
			</div>
		</div>
	</div>
</div>