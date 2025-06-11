{*
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*}

<script type="text/javascript">
var Card = new Object();
var Customer = new Object();
var default_language = {$default_language|intval};
var selectedProducts = new Array();
{if isset($selected_products) && $selected_products}
	{foreach from=$selected_products item=selected_product}
        var selectedItem = new Array();
        selectedItem['item'] = "{$selected_product.item|escape:'quotes':'UTF-8'}";
        selectedItem['id_item'] = "{$selected_product.id_item|intval}";
        selectedItem['custom_reduction'] = false;
        {if isset($selected_product.custom_reduction) && $selected_product.custom_reduction}
            selectedItem['reduction'] = "{$selected_product.reduction|floatval}";
            selectedItem['reduction_type'] = "{$selected_product.reduction_type|escape:'html':'UTF-8'}";
            selectedItem['from'] = "{$selected_product.from|escape:'html':'UTF-8'}";
            selectedItem['to'] = "{$selected_product.to|escape:'html':'UTF-8'}";
            selectedItem['custom_reduction'] = true;
        {/if}
        selectedItem['ids_product'] = new Array();
        {assign var="ids_product" value=explode(',', $selected_product.ids_product)}
        {foreach from=$ids_product item=id_product}
            selectedItem['ids_product'].push("{$id_product|intval}");
        {/foreach}
        selectedProducts.push(selectedItem);
	{/foreach}
{/if}
var combinations = new Array();
var combs = new Object();
var active = {
	class: 'info',
	text: "{l s='Active' mod='flashsales'}"
};
var pending = {
	class: 'warning',
	text: "{l s='Pending' mod='flashsales'}"
};
var expired = {
	class: 'danger',
	text: "{l s='Expired' mod='flashsales'}"
};
var allCustomers = "{l s='All customers' mod='flashsales'}";
var selectAllText = "{l s='Select all' mod='flashsales'}";
var deselectAllText = "{l s='Deselect all' mod='flashsales'}";
var choose = "{l s='Choose' mod='flashsales'}";
var remove = "{l s='Remove' mod='flashsales'}";
var noCustomersFound = "{l s='No customers found' mod='flashsales'}";
var customerControllerLink = "{$link->getAdminLink('AdminCustomers')|escape:'quotes':'UTF-8'}";
var flashsalesControllerLink = "{$link->getAdminLink('AdminFlashSales')|escape:'quotes':'UTF-8'}";
var iso = "{$iso|escape:'html':'UTF-8'}";
var pathCSS = "{$path_css|escape:'html':'UTF-8'}";
var ad = "{$ad|escape:'html':'UTF-8'}";
</script>

<form id="flashsale-form" class="form-horizontal" enctype="multipart/form-data" action="{$link->getAdminLink('AdminFlashSales')|escape:'quotes':'UTF-8'}&submitAdd{$table|escape:'html':'UTF-8'}=1" method="post" autocomplete="off">

	<ul id="progressbar" class="nav" role="tablist">
		<li role="presentation" class="active"><a href="#general-settings" role="tab" data-toggle="tab">{l s='General settings' mod='flashsales'}</a></li>
		<li role="presentation"><a href="#communication" role="tab" data-toggle="tab">{l s='Communication' mod='flashsales'}</li></a>
		<li role="presentation"><a href="#review" role="tab" data-toggle="tab">{l s='Review & confirm' mod='flashsales'}</li></a>
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="general-settings">
			<div class="panel" id="specific_price_rule">
				<h3>
					<i class="icon-align-justify"></i>
					{l s='Flash sale rule' mod='flashsales'}
				</h3>
				<div class="well clearfix">
					<div class="col-lg-12">
						{if ($shops && count($shops) > 1) || ($currencies && count($currencies) > 1) || ($countries && count($countries) > 1) || ($groups && count($groups) > 1)}
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='For' mod='flashsales'}</label>
							<div class="col-lg-10">
								<div class="row">
									{if ($shops && count($shops) > 1)}
									<div class="col-lg-3">
										<select class="selectpicker form-control" multiple name="shops[]" title="{l s='All shops' mod='flashsales'}" id="shops">
											{foreach from=$shops item=shop}
											<option value="{$shop.id_shop|intval}" {if in_array($shop.id_shop, $selected_shops)} selected="selected"{/if}>{$shop.name|htmlentitiesUTF8}</option>
											{/foreach}
										</select>
									</div>
									{/if}
									{if ($currencies && count($currencies) > 1)}
									<div class="col-lg-3">
										<select class="selectpicker form-control" multiple name="currencies[]" title="{l s='All currencies' mod='flashsales'}" id="currencies">
											{foreach from=$currencies item=curr}
											<option value="{$curr.id_currency|intval}" {if in_array($curr.id_currency, $selected_currencies)} selected="selected"{/if}>{$curr.name|htmlentitiesUTF8}</option>
											{/foreach}
										</select>
									</div>
									{/if}
									{if ($countries && count($countries) > 1)}
									<div class="col-lg-3">
										<select class="selectpicker form-control" multiple data-live-search="true" name="countries[]" title="{l s='All countries' mod='flashsales'}" id="countries">
											{foreach from=$countries item=country}
											<option value="{$country.id_country|intval}" {if in_array($country.id_country, $selected_countries)} selected="selected"{/if}>{$country.name|htmlentitiesUTF8}</option>
											{/foreach}
										</select>
									</div>
									{/if}
									{if ($groups && count($groups) > 1)}
									<div class="col-lg-3">
										<select class="selectpicker form-control" multiple name="groups[]" title="{l s='All groups' mod='flashsales'}" id="groups">
											{foreach from=$groups item=group}
											<option value="{$group.id_group|intval}" {if in_array($group.id_group, $selected_groups)} selected="selected"{/if}>{$group.name|escape:'html':'UTF-8'}</option>
											{/foreach}
										</select>
									</div>
									{/if}
								</div>
							</div>
						</div>
						{/if}

						<div class="form-group">
							<label class="control-label col-lg-2" for="customer">{l s='Customer' mod='flashsales'}</label>
							<div class="col-lg-4">
								<input type="hidden" name="id_customer" id="id_customer" value="{$currentTab->getFieldValue($currentObject, 'id_customer')|intval}" />
								<div class="input-group">
									<input type="text" name="customer" value="{$customer_name|escape:'quotes':'UTF-8'}" id="customer" autocomplete="off" />
									<span class="input-group-addon"><i id="customerLoader" class="icon-refresh icon-spin" style="display: none;"></i> <i class="icon-search"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-10 col-lg-offset-2">
								<div id="customers"></div>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-2" for="from">{l s='Available' mod='flashsales'}</label>
							<div class="col-lg-9">
								<div class="row">
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon">{l s='from' mod='flashsales'}</span>
											<input class="datepicker" type="text" name="from" value="{$currentTab->getFieldValue($currentObject, 'from')|escape:'html':'UTF-8'}" style="text-align: center" id="from" />
											<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon">{l s='to' mod='flashsales'}</span>
											<input class="datepicker" type="text" name="to" value="{$currentTab->getFieldValue($currentObject, 'to')|escape:'html':'UTF-8'}" style="text-align: center" id="to" />
											<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-2" for="reduction">{l s='Apply a discount of' mod='flashsales'}</label>
							<div class="col-lg-4">
								<div class="row">
									<div class="col-lg-6">
										<input type="text" name="reduction" id="reduction" value="{$currentTab->getFieldValue($currentObject, 'reduction')|floatval}"/>
									</div>
									<div class="col-lg-6">
										<select name="reduction_type" id="reduction_type">
											<option selected="selected" value="percentage" {if $currentTab->getFieldValue($currentObject, 'reduction_type')|escape:'quotes':'UTF-8' == 'percentage'} selected="selected"{/if}>{l s='Percent' mod='flashsales'}</option>
											<option value="amount" {if $currentTab->getFieldValue($currentObject, 'reduction_type')|escape:'quotes':'UTF-8' == 'amount'} selected="selected"{/if}>{l s='Currency Units' mod='flashsales'}</option>
										</select>
									</div>
									<input type="hidden" name="reduction_tax" value="1" />
								</div>
							</div>
							<p class="help-block">{l s='The discount is applied after the tax' mod='flashsales'}</p>
						</div>
						
						<div class="form-group">
							<label class="control-label col-lg-2" for="active">{l s='Status' mod='flashsales'}</label>
							<div class="col-lg-4">
								<span class="switch prestashop-switch fixed-width-lg">
								<input id="active_on" type="radio" value="1" name="active" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if}>
								<label class="radioCheck" for="active_on">{l s='Enabled' mod='flashsales'}</label>
								<input id="active_off" type="radio" value="0" name="active" {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if}>
								<label class="radioCheck" for="active_off">{l s='Disabled' mod='flashsales'}</label>
								<a class="slide-button btn"></a>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="panel" id="product_part">
				<h3>
					<i class="icon-align-justify"></i>
					{l s='Product selection' mod='flashsales'}
				</h3>
				<div id="search-product-form-group" class="form-group">
					<div class="col-lg-offset-3 col-lg-6">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<span data-bind="label">{l s='Product' mod='flashsales'}</span>&nbsp;<span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li data-item="product"><a href="javascript:void(0);">{l s='Product' mod='flashsales'}</a></li>
									<li data-item="category"><a href="javascript:void(0);">{l s='Category' mod='flashsales'}</a></li>
									<li data-item="manufacturer"><a href="javascript:void(0);">{l s='Manufacturer' mod='flashsales'}</a></li>
								</ul>
							</div><!-- /btn-group -->
							<input type="text" id="product" value="" />
							<span class="input-group-addon">
								<i class="icon-search"></i>
							</span>
						</div>
					</div>
					<div class="col-lg-3">
						<input type="hidden" id="search_action" value="product" />
					</div>
				</div>

				<div class="row cards" id="search_result"></div>
				<div class="row" id="selected_products">
					{foreach from=$item_properties key=item_name item=properties}
					<div class="panel" {if !isset($properties) || !count($properties)}style="display:none"{/if}>
						<h3>
							{if $item_name == 'product'}{l s='Selected products' mod='flashsales'}
							{elseif $item_name == 'category'}{l s='Selected categories' mod='flashsales'}
							{elseif $item_name == 'manufacturer'}{l s='Selected manufacturers' mod='flashsales'}
							{/if}
						</h3>
						<div class="row cards" id="selected_{$item_name|escape:'html':'UTF-8'}">
							{if isset($properties) && count($properties)}
								{include file="$item_card" key=$item_name items=$properties}
							{/if}
						</div>
						{if $item_name != 'product'}
							<div class="row cards products_detail" id="{$item_name|escape:'html':'UTF-8'}_products_detail" data-parent-panel="{$item_name|escape:'html':'UTF-8'}" data-id-item="" style="display:none">
								<h4 class='col-lg-12'>{l s='Product list detail' mod='flashsales'} > <span></span></h4>
								<div class="content"></div>
							</div>
						{/if}
					</div>
					{/foreach}
				</div>
				{include file="$custom_price"}
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="communication">
			<div class="panel" id="front_office_features">
				<h3>
					<i class="icon-align-justify"></i>
					{l s='Front office features' mod='flashsales'}
				</h3>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label col-lg-3 required">
								<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Invalid characters:' mod='flashsales'} &lt;&gt;;=#{}">
									{l s='Name' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								{foreach from=$languages item=language}
								{if $languages|count > 1}
								<div class="row">
									<div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $default_language}style="display:none"{/if}>
										<div class="col-lg-9">
								{/if}
											<input type="text" id="name_{$language.id_lang|intval}" name="name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'name', $language.id_lang|intval)|escape:'quotes':'UTF-8'}">
								{if $languages|count > 1}
										</div>
										<div class="col-lg-2">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
												{$language.iso_code|escape:'html':'UTF-8'}
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu">
												{foreach from=$languages item=language}
												<li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
												{/foreach}
											</ul>
										</div>
									</div>
								</div>
								{/if}
								{/foreach}
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3" for="description">
								<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Invalid characters:' mod='flashsales'} &lt;&gt;;=#{}">
									{l s='Description' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								{foreach from=$languages item=language}
								{if $languages|count > 1}
								<div class="translatable-field row lang-{$language.id_lang|intval}">
									<div class="col-lg-9">
								{/if}
										<textarea id="description_{$language.id_lang|intval}" name="description_{$language.id_lang|intval}" class="autoload_rte">{$currentTab->getFieldValue($currentObject, 'description', $language.id_lang|intval)|htmlentitiesUTF8}</textarea>
									<span class="counter" data-max="{if isset($max)}{$max|escape:'html':'UTF-8'}{else}none{/if}"></span>
								{if $languages|count > 1}
									</div>
									<div class="col-lg-2">
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											{$language.iso_code|escape:'html':'UTF-8'}
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$languages item=language}
											<li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});">{$language.name|escape:'html':'UTF-8'}</a></li>
											{/foreach}
										</ul>
									</div>
								</div>
								{/if}
								{/foreach}

								<script type="text/javascript">
									$(".textarea-autosize").autosize();
								</script>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip"
									title="{l s='Upload a banner image from your computer.' mod='flashsales'}">
									{l s='Image' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								{$uploader}{* HTML, cannot escape *}
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Enable to display banner in the home page.' mod='flashsales'}">
									{l s='Display in home page' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="display_home" id="display_home_on" value="1" {if $currentTab->getFieldValue($currentObject, 'display_home')|intval}checked="checked"{/if} />
									<label class="t" for="display_home_on">{l s='Yes' mod='flashsales'}</label>
									<input type="radio" name="display_home" id="display_home_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'display_home')|intval}checked="checked"{/if} />
									<label class="t" for="display_home_off">{l s='No' mod='flashsales'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>

						{if version_compare($smarty.const._PS_VERSION_, '1.7', '<')}
						<div class="form-group">
							<label class="control-label col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Enable to display flash sale products in the home tab content.' mod='flashsales'}">
									{l s='Display in home tab content' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="display_home_tab" id="display_home_tab_on" value="1" {if $currentTab->getFieldValue($currentObject, 'display_home_tab')|intval}checked="checked"{/if} />
									<label class="t" for="display_home_tab_on">{l s='Yes' mod='flashsales'}</label>
									<input type="radio" name="display_home_tab" id="display_home_tab_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'display_home_tab')|intval}checked="checked"{/if} />
									<label class="t" for="display_home_tab_off">{l s='No' mod='flashsales'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
						{else}
						<input type="hidden" name="display_home_tab" value="1" />
						{/if}

						<div class="form-group">
							<label class="control-label col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Enable to display flash sale products in the left/right column.' mod='flashsales'}">
									{l s='Display in column' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="display_column" id="display_column_on" value="1" {if $currentTab->getFieldValue($currentObject, 'display_column')|intval}checked="checked"{/if} />
									<label class="t" for="display_column_on">{l s='Yes' mod='flashsales'}</label>
									<input type="radio" name="display_column" id="display_column_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'display_column')|intval}checked="checked"{/if} />
									<label class="t" for="display_column_off">{l s='No' mod='flashsales'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3">
								<span class="label-tooltip" data-toggle="tooltip"
								title="{l s='Enable to display flash sales products in the related page.' mod='flashsales'}">
									{l s='Display in flash sale page' mod='flashsales'}
								</span>
							</label>
							<div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" name="display_page" id="display_page_on" value="1" {if $currentTab->getFieldValue($currentObject, 'display_page')|intval}checked="checked"{/if} />
									<label class="t" for="display_page_on">{l s='Yes' mod='flashsales'}</label>
									<input type="radio" name="display_page" id="display_page_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'display_page')|intval}checked="checked"{/if} />
									<label class="t" for="display_page_off">{l s='No' mod='flashsales'}</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-lg-3" for="depends_on_stock">{l s='Hide products out of stock' mod='flashsales'}</label>
							<div class="col-lg-9">
								<span class="switch prestashop-switch fixed-width-lg">
								<input id="depends_on_stock_on" type="radio" value="1" name="depends_on_stock" {if $currentTab->getFieldValue($currentObject, 'depends_on_stock')|intval}checked="checked"{/if}>
								<label class="radioCheck" for="depends_on_stock_on">{l s='Yes' mod='flashsales'}</label>
								<input id="depends_on_stock_off" type="radio" value="0" name="depends_on_stock" {if !$currentTab->getFieldValue($currentObject, 'depends_on_stock')|intval}checked="checked"{/if}>
								<label class="radioCheck" for="depends_on_stock_off">{l s='No' mod='flashsales'}</label>
								<a class="slide-button btn"></a>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="review">
			<div class="panel" id="summary_part">
				<h3>
					<i class="icon-align-justify"></i>
					{l s='Summary' mod='flashsales'}
				</h3>
				<div id="summary_products" style="display:none"></div>
				<input type="hidden" name="id_flash_sale" value="{$currentTab->getFieldValue($currentObject, 'id')|intval}" />
				<div class="row">
					<div class="col-lg-12">
						<div class="summary_group">
							<ul>
								<li>{l s='Shop restriction' mod='flashsales'} : <span id="summary_shops"></span></li>
								<li>{l s='Currency restriction' mod='flashsales'} : <span id="summary_currencies"></span></li>
								<li>{l s='Country restriction' mod='flashsales'} : <span id="summary_countries"></span></li>
								<li>{l s='Group restriction' mod='flashsales'} : <span id="summary_groups"></span></li>
								<li>{l s='Customer' mod='flashsales'} : <span id="summary_customer"></span></li>
								<li>{l s='Status' mod='flashsales'} : <span id="summary_active"></span></li>
							</ul>
						</div>
						<div class="summary_group table-responsive">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>{l s='Product ID' mod='flashsales'}</th>
										<th>{l s='Product Image' mod='flashsales'}</th>
										<th>{l s='Product Name' mod='flashsales'}</th>
										<th>{l s='Combination' mod='flashsales'}</th>
										<th>{l s='Reduction' mod='flashsales'}</th>
										<th>{l s='Period' mod='flashsales'}</th>
										<th>{l s='Status' mod='flashsales'}</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>

						<div class="summary_group">
							<div class="col-lg-9 col-lg-offset-3">
							{if $currentObject->id}
								<button type="submit" name="submitEditFlashSale" class="btn-save" />
									<i class="icon-check"></i>
									{l s='Update' mod='flashsales'}
								</button>
							{else}
								<button type="submit" name="submitAddFlashSale" class="btn-save" />
									<i class="icon-check"></i>
									{l s='Create the flash sale' mod='flashsales'}
								</button>
							{/if}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
