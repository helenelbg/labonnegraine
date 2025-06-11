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
 * form.tpl extends form to add specific field in setting form
 *}
{extends file="helpers/form/form.tpl"}
{block name=script}
	var tacartreminder_ajax_url = '{$tacartreminder_ajax_url|escape:'quotes':'UTF-8'}';
	var tamodule_url = '{$module_url|escape:'quotes':'UTF-8'}';
{/block}

{if isset($tinymce) && $tinymce}
<script type="text/javascript">
	var iso = '{$iso|escape:'quotes':'UTF-8'}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_|escape:'quotes':'UTF-8'}';
	var ad = '{$ad|escape:'quotes':'UTF-8'}';
	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			{if $ps_version < "1.6"}
			tinySetupPS15({
				editor_selector :"autoload_rte",
				relative_urls : false,
				extended_valid_elements : "em[class|name|id],html,head"
			});
			{else}
			tinySetup({
				editor_selector :"autoload_rte",
				relative_urls : false,
				plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor fullpage",
				extended_valid_elements : "em[class|name|id],html,head"
			});
			{/if}
		{/block}
	});
</script>
{/if}
{block name="input"}
{if $input.type == 'datetime' && $ps_version < "1.6"}
			<div class="input-group" class="form-group">
					<input id="date_from" type="text" data-hex="true" class="datetimepicker hasDatepicker" name="date_from" value="0000-00-00 00:00:00">
					<span class="input-group-addon">
						<i class="flaticon-calendar146"></i>
					</span>
			</div>
{else}
  		{$smarty.block.parent}
{/if}
{/block}
{block name="field"}

	{if $input.type == 'discountselect'}
			<div class="col-lg-9" id="block_select_discount" style="border-left:1px #cecece dashed;padding-left:10px">
				<div class="row form-group">
					<input type="hidden" id="id_cart_rule" name="id_cart_rule" value="{$fields_value['id_cart_rule']|intval}">
					<label class="control-label col-lg-2">
					 {l s='Cart rule' mod='tacartreminder'}
					</label>
					<div class="input-group col-lg-7">
							<span class="input-group-addon"><i class="flaticon-search103"></i></span>
							<input type="text" id="cart_rule_filter" name="cart_rule_filter" value="{$fields_value['cart_rule_filter']|escape:'htmlall':'UTF-8'}">
					</div>
				</div>
				<div class="row form-group" >
					<label class="control-label col-lg-2">{l s='Nb day validity' mod='tacartreminder'}</label>
					<div class="col-lg-10">
						<div class="input-group col-lg-6">
							<span class="input-group-addon">{l s='Days' mod='tacartreminder'}</span>
							<input type="text" id="cart_rule_nbday_validity" class="fixed-width-xs" name="cart_rule_nbday_validity" value="{$fields_value['cart_rule_nbday_validity']|intval}">
						</div>
					</div>
				</div>
				<div class="row form-group" >
					<div class="col-lg-2"></div>
					<div class="col-lg-10">
						<span class="help-block"><i class="flaticon-info2"></i>&nbsp;{l s='This action will duplicate the voucher and associate it to the customer' mod='tacartreminder'}</span>
					</div>
				</div>
			</div>
	{elseif $input.type == 'datetime' && $ps_version < "1.6"}
			<div class="form-group row">
				<label for="{$input.name|escape:'htmlall':'UTF-8'}" class="control-label col-lg-3 ">
				{if $input.name=='date_from'}
					{l s='From' mod='tacartreminder'}
				{else}
					{l s='To' mod='tacartreminder'}
				{/if}
				</label>
				<div class="col-lg-9">
				<div class="row">
					<div class="input-group col-lg-4" class="form-group">
						<input id="{$input.name|escape:'html':'UTF-8'}" type="text" data-hex="true" class="datetimepicker" name="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}">
						<span class="input-group-addon">
							<i class="flaticon-calendar146"></i>
						</span>
					</div>
				</div>
				</div>
			</div>
			<script type="text/javascript">
			$(document).ready(function(){
				$('#{$input.name|escape:'htmlall':'UTF-8'}').datetimepicker({
					prevText: '',
					nextText: '',
					dateFormat: 'yy-mm-dd',
					// Define a custom regional settings in order to use PrestaShop translation tools
					currentText: '{l s='Now' mod='tacartreminder'}',
					closeText: '{l s='Done' mod='tacartreminder'}',
					ampm: false,
					amNames: ['AM', 'A'],
					pmNames: ['PM', 'P'],
					timeFormat: 'hh:mm:ss tt',
					timeSuffix: '',
					timeOnlyTitle: '{l s='Choose Time' mod='tacartreminder'}',
					timeText: '{l s='Time' mod='tacartreminder'}',
					hourText: '{l s='Hour' mod='tacartreminder'}',
					minuteText: '{l s='Minute' mod='tacartreminder'}',
				});
			});
			</script>
	{elseif $input.type == 'condition'}
			<script type="text/javascript">
				var condition_groups_counter = {if isset($condition_groups_counter)}{$condition_groups_counter|intval}{else}0{/if};
				var condition_counters = new Array();
			</script>
			<div class="col-lg-12">
					<div class="row">
						<h4>{l s='The cart must validate the following rules:' mod='tacartreminder'}</h4>
					</div>
					<div class="row">
						<div id="condition_restriction_div">
							<table id="condition_group_table" class="table">
								{foreach from=$condition_groups item='condition_group'}
									{$condition_group}{*HTML CONTENT*}
								{/foreach}
							</table>
							<a href="javascript:addConditionGroup();" class="btn btn-default ">
								<i class="flaticon-add11"></i> {l s='New condition group' mod='tacartreminder'}
							</a>
						</div>
					</div>
				</div>
	{elseif $input.type == 'reminder'}
			<script type="text/javascript">
				var reminders_counter = {if isset($reminders_counter)}{$reminders_counter|intval}{else}0{/if};
			</script>
			<div class="col-lg-12">
					<div class="row">
						<div id="reminder_div">
							<table id="reminder_table" class="table">
								{foreach from=$reminders item='reminder'}
									{$reminder}{*HTML CONTENT*}
								{/foreach}
							</table>
							<a href="javascript:addReminder();" class="btn btn-default ">
								<i class="flaticon-add11"></i> {l s='New reminder' mod='tacartreminder'}
							</a>
						</div>
					</div>
			</div>
	{elseif $input.type == 'force_reminder'}
		<div class="form-group">
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Perform the following reminder without checking if the cart is subject to conditions.'  mod='tacartreminder' }">
						{l s='Force launch reminder' mod='tacartreminder' }
					</span>
				</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg" >
						<input type="radio" name="force_reminder" id="force_reminder_on" value="1" {if $fields_value['force_reminder']|intval} checked="checked"{/if}/>
						<label for="force_reminder_on">{l s='Yes' mod='tacartreminder'}</label>
						<input type="radio" name="force_reminder" id="force_reminder_off" value="0" {if !$fields_value['force_reminder']|intval} checked="checked"{/if} />
						<label for="force_reminder_off">{l s='No' mod='tacartreminder'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
		</div>
	{elseif $input.type == 'textarea' && $input.name == 'content_html'}

	<div id="content_mail_template">
		{$smarty.block.parent}
	</div>
	{elseif $input.type == 'example_mails'}

	<div class="form-group">
			<label for="example_mails" class="control-label {if $ps_version < "1.6"}{else}col-lg-3{/if}">
				<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Here you can select a sample email and click "copy" to replace the contents' mod='tacartreminder'}">
					{l s='Sample emails' mod='tacartreminder'}
				</span>
			</label>
			<div class="col-lg-9">
				<ul id="carousel" class="elastislide-list">
					{foreach from=$egmails item='egmail'}
						<li><a href="javascript:;" class="egmail_item" data-egmailid="{$egmail->id|intval}"><span class="rollover" ></span><img src="{$ta_img_url|addslashes}/egmail/{$egmail->id|intval}-cover.jpg" alt="{$egmail->title|escape:'htmlall':'UTF-8'}" /></a></li>
					{/foreach}
				</ul>
			</div>
	</div>

	{elseif $input.type == 'convert_html_to_txt'}
	{if $ps_version < "1.6"}
		<div class="margin-form">
			<a href="javascript:;" class="btn btn-default convertHtmlToTxt">
						<i class="flaticon flaticon-wizard"></i> {l s='Auto convert html -> txt' mod='tacartreminder'}
			</a>
		</div>
	{else}
	<div class="form-group">
		<div class="col-lg-12">
			<div class="col-lg-3"></div>
			<div class="col-lg-9">
			<a href="javascript:;" class="btn btn-default convertHtmlToTxt">
						<i class="flaticon flaticon-wizard"></i> {l s='Auto convert html -> txt' mod='tacartreminder'}
			</a>
			</div>
		</div>
	</div>
	{/if}
	{elseif $input.type == 'preview_mail'}
	{if $ps_version < "1.6"}
		<label>
			<span >
					{l s='Email preview' mod='tacartreminder'}
			</span>
		</label>
		<div class="margin-form">
			<!--select name="cart_mail_preview" id="cart_mail_preview">
						{foreach from=$cart_mails item='cart_mail'}
							<option value="{$cart_mail.id_cart|intval}">{$cart_mail.date_add|escape:'htmlall':'UTF-8'} {$cart_mail.id_cart|intval} - {$cart_mail.firstname|escape:'htmlall':'UTF-8'} {$cart_mail.lastname|escape:'htmlall':'UTF-8'} - {$cart_mail.iso_code|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
			</select-->
			<a href="javascript:;" class="btn btn-default previewMail" data-type-render="html" style="float:left;" >
						<i class="flaticon flaticon-eye46"></i> {l s='Preview HTML' mod='tacartreminder'}
			</a>
			<!--a href="javascript:;" class="btn btn-default previewMail" data-type-render="txt" >
						<i class="flaticon flaticon-eye46"></i> {l s='Preview TXT' mod='tacartreminder'}
			</a-->
		</div>
	<div class="clear"></div><br/>
	{else}
	<div class="form-group">
			<label for="content_txt_preview_mail" class="control-label col-lg-3 ">
					{l s='Email preview' mod='tacartreminder'}
			</label>
			<div class="col-lg-9 ">
			<div class="col-lg-12">
					<a href="javascript:;" class="btn btn-default previewMail" data-type-render="html" style="float:left;" >
						<i class="flaticon flaticon-eye46"></i> {l s='Preview HTML' mod='tacartreminder'}
					</a>&nbsp;
			</div>
			</div>
	</div>
	{/if}
	{elseif $input.type == 'crontab'}
			<div class="col-lg-12">
					<div class="row"><i class="flaticon flaticon-chronometer10" style="font-size:20px"></i>
						{l s='The following lines are to be added to crontab :' mod='tacartreminder'} <br/>
						{$cron_urls}{*HTML CONTENT*}
						{l s='In this example, the module will check for reminders to run every 15 minutes.' mod='tacartreminder'}
					</div>
			</div>
	{else}
		{$smarty.block.parent}
    {/if}

{/block}
