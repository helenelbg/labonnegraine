{* NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * ...........................................................................
 *
 * @package    SoNice
 * @copyright  Copyright(c) 2010-2013 S.A.R.L S.M.C - http://www.common-services.com
 * @author     Debusschere A.
 * @license    Commercial license
 *}

<div id="conf-address" class="panel form-horizontal" style="display: none;">
	<h2>{l s='Address' mod='sonice_etiquetage'}</h2>
	<div class="clearfix">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='This address will be used on the label during its creation.' mod='sonice_etiquetage'}<br>
				{l s='It is the sender address.' mod='sonice_etiquetage'}
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Company Name' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<div class="input-group">
				<input type="text" name="return_info[companyName]" class="form-control" value="{$sne_config.companyName|escape:'htmlall':'UTF-8'}">
				<span class="input-group-addon">*</span>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Name' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<input type="text" name="return_info[Name]" class="form-control" value="{$sne_config.Name|escape:'htmlall':'UTF-8'}">
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Surname' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<input type="text" name="return_info[Surname]" class="form-control" value="{$sne_config.Surname|escape:'htmlall':'UTF-8'}">
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Service Name' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<input type="text" name="return_info[ServiceInfo]" class="form-control" value="{$sne_config.ServiceInfo|escape:'htmlall':'UTF-8'}">
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Address' mod='sonice_etiquetage'} 1</label>
		<div class="col-lg-9 margin-form">
			<div class="input-group">
				<input type="text" name="return_info[Line2]" class="form-control" value="{$sne_config.Line2|escape:'htmlall':'UTF-8'}">
				<span class="input-group-addon">*</span>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Address' mod='sonice_etiquetage'} 2</label>
		<div class="col-lg-9 margin-form">
			<input type="text" name="return_info[Line0]" class="form-control" value="{$sne_config.Line0|escape:'htmlall':'UTF-8'}">
		</div>
	</div>

	{*<div class="form-group">*}
		{*<label class="col-lg-3 control-label">{l s='Address' mod='sonice_etiquetage'} 3</label>*}
		{*<div class="col-lg-9 margin-form">*}
			{*<input type="text" name="return_info[Line2]" class="form-control" value="{$sne_config.Line2|escape:'htmlall':'UTF-8'}">*}
		{*</div>*}
	{*</div>*}

	{*<div class="form-group">*}
		{*<label class="col-lg-3 control-label">{l s='Address' mod='sonice_etiquetage'} 4</label>*}
		{*<div class="col-lg-9 margin-form">*}
			{*<input type="text" name="return_info[Line3]" class="form-control" value="{$sne_config.Line3|escape:'htmlall':'UTF-8'}">*}
		{*</div>*}
	{*</div>*}

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Zip Code' mod='sonice_etiquetage'}</label>

		<div class="col-lg-9 margin-form">
			<div class="input-group">
				<input type="text" name="return_info[PostalCode]" class="form-control" value="{$sne_config.PostalCode|escape:'htmlall':'UTF-8'}">
				<span class="input-group-addon">*</span>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='City' mod='sonice_etiquetage'}</label>

		<div class="col-lg-9 margin-form">
			<div class="input-group">
				<input type="text" name="return_info[City]" class="form-control" value="{$sne_config.City|escape:'htmlall':'UTF-8'}">
				<span class="input-group-addon">*</span>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Country' mod='sonice_etiquetage'}</label>

		<div class="col-lg-9 margin-form">
			<select name="return_info[countryCode]" class="form-control">
				{foreach $sne_address_countries as $country}
					<option value="{$country.iso_code|escape:'htmlall':'UTF-8'}" {($sne_config.countryCode == $country.iso_code) ? 'selected' : ''}>{$country.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">{l s='Phone' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<input type="tel" data-type="positive_integer" name="return_info[phoneNumber]" class="form-control" value="{$sne_config.phoneNumber|escape:'htmlall':'UTF-8'}">
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 control-label">Email</label>
		<div class="col-lg-9 margin-form">
			<input type="email" name="return_info[Mail]" class="form-control" value="{$sne_config.Mail|escape:'htmlall':'UTF-8'}" pattern={literal}"^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"{/literal}>
		</div>
	</div>

	<p><span>*</span> {l s='Required field' mod='sonice_etiquetage'}</p>
	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>