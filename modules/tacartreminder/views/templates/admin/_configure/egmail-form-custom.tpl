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
 * View for personnalize email template
 *}
<div id="egmail-panels" class="bootstrap ta-to-bootstrap ta-form" style="margin: 5px;padding: 0;" >
	<div id="egmail-custom" class="{if $mode=='egmail'}col-xs-8 col-sm-9 col-lg-10{else}col-lg-12{/if} ta-content-box">
		<div class="row" id="ta-tool-preview">
			<div class="col-xs-1" style="padding-top: 3px;">
				<i class="flaticon flaticon-eye46" style="font-size: 24px;"></i>
			</div>
			<div class="col-xs-3 box-preview">
					{if $mode=='egmail'}
					<select name="egmail_template_lang" id="egmail_template_lang" width="100%" class="egmail-input">
	        			<option value="en" 
	            			data-description="Base template reminder">{l s='Language' mod='tacartreminder'} EN</option>
	            		<option value="fr" 
	            			data-description="Base template reminder">{l s='Language' mod='tacartreminder'} FR</option>
	            		<option value="es" 
	            			data-description="Base template reminder">{l s='Language' mod='tacartreminder'} ES</option>
	    			</select>
	    			{/if}
			</div>
			<div class="col-xs-4 box-preview">
					<div id="reso_switch" class="preview_switch">
						<a class="first_switch" href="javascript:;" title="{l s='Desktop' mod='tacartreminder'}" data-reso="desktop">
							<span class="taicon taicon-screenfull"></span> </a> 
						<a class="last_switch" href="javascript:;" title="{l s='Mobile' mod='tacartreminder'}" data-reso="mobile">
							<span class="taicon taicon-smartphone55"></span></a>
					</div>
			</div>
			<div class="col-xs-4 box-preview">
					<select name="egmail_cartmail_preview" id="egmail_cartmail_preview"{if $mode=='egmail'} class="egmail-input"{else} onchange="previewMail()"{/if}>
						{foreach from=$cart_mails item='cart_mail'}
							<option value="{$cart_mail.id_cart|intval}" data-id-lang="{$cart_mail.id_lang|intval}">{l s='Cart' mod='tacartreminder'} #{$cart_mail.id_cart|intval} - {$cart_mail.firstname|escape:'htmlall':'UTF-8'} {$cart_mail.lastname|escape:'htmlall':'UTF-8'} - {$cart_mail.iso_code|escape:'htmlall':'UTF-8'}</option>									
						{/foreach}
					</select>
			</div>
		</div>
		
		<div class="row egmail" id="viewreportcontainer">
			<div id="resizer">
				<div class="ta_form_loader" style="display:none;text-align:center"></div>
				<iframe id="content-egmail-preview"  border="0" frameborder="0" style="margin-left: 0px; margin-top: 0px;">

				</iframe>
			</div>
		</div>
	</div>
	{if $mode=='egmail'}
	<div id="egmail-custom" class="col-xs-4 col-sm-3 col-lg-2 ta-tool-box">
			<h2><span class="ta-badge">1</span>{l s='Suggestion' mod='tacartreminder'}</h2>
			<div class="ta-tool-box-panel clearfix">
				<ul class="ta-palette-selector">
				{foreach from=$egmail->suggestions item='suggestion' name='suggestionitem'}
					<li data-suggestion-index="{$smarty.foreach.suggestionitem.index|intval}">
						<div class="ta-palette-color-container">
						<span style="background-color:{$suggestion->palette_color1|escape:'html':'UTF-8'}"></span>
						<span style="background-color:{$suggestion->palette_color2|escape:'html':'UTF-8'}"></span>
						<span style="background-color:{$suggestion->palette_color3|escape:'html':'UTF-8'}"></span>
						<span style="background-color:{$suggestion->palette_color4|escape:'html':'UTF-8'}"></span>
						<span style="background-color:{$suggestion->palette_color5|escape:'html':'UTF-8'}"></span>
						<span style="background-color:{$suggestion->palette_color6|escape:'html':'UTF-8'}"></span>
						</div>
					</li>									
				{/foreach}
				</ul>
			</div>
			<h2><span class="ta-badge">2</span>{l s='Customize' mod='tacartreminder'}</h2>
			<div class="ta-tool-box-panel clearfix">
			{foreach from=$egmail->suggestions item='suggestion' name='suggestionitem'}
			<div id="suggestion-{$smarty.foreach.suggestionitem.index|intval}" class="suggestion-custom">
				<form id="suggestion-custom-form-{$smarty.foreach.suggestionitem.index|intval}" action="{$module_url|escape:'html':'UTF-8'}">
				<input type="hidden" name="egmail_id" value="{$egmail->id|intval}"/>
				<ul class="ta-variable-selector" id="ta-variable-selector" style="min-height:200px">
					{foreach from=$suggestion->variables item='variable' name='variableitem'}
						<li><span class="ta-variable-title">{$variable->id|escape:'html':'UTF-8'}</span>
							{if $variable->type=='color'}
							<!--div class="input-group" class="form-group">
							<input type="text" size="33" data-hex="true" class="color mColorPickerInput mColorPicker egmail-input" name="{$variable->id|escape:'html':'UTF-8'}" 
										value="{$variable->value|escape:'html':'UTF-8'}" id="color_{$smarty.foreach.variableitem.index|intval}" 
										style="background-color: {$variable->value|escape:'html':'UTF-8'}; color: white;">
										<span style="cursor:pointer;" id="icp_color_{$smarty.foreach.variableitem.index|intval}" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true"><img src="../img/admin/color.png" style="border:0;margin:0 0 0 3px" align="absmiddle"></span>
							
							</div-->
							<div class="input-group colorpickeredit">
    							<span class="input-group-addon"><i></i></span>
    							<input type="text" value="{$variable->value|escape:'html':'UTF-8'}" class="form-control"  name="{$variable->id|escape:'html'}"/>
							</div>
							{/if}
						</li>
					{/foreach}
				</ul>
				<ul class="ta-image-selector" style="display:none">
					{foreach from=$suggestion->images item='image' name='imageitem'}
						<li>
							<img src="{$image->url|escape:'html':'UTF-8'}" style="max-width:75px;height:auto"/>
						</li>
					{/foreach}
				</ul>
				</form>
			</div>
			{/foreach}
			<h2><span class="ta-badge">3</span>{l s='Background' mod='tacartreminder'}</h2>
			<div class="ta-tool-box-panel clearfix">
				<ul id="carousel-bgpattern" class="elastislide-list">
					{foreach from=$bgpatterns item='bgpattern'}
						<li style="background-color:#ddd;">
							<a href="javascript:;" class="bgpattern_item {if $bgpattern==$suggestion->bgpattern}selected{/if}" data-patternid="{$bgpattern|escape:'html':'UTF-8'}">
								<span class="rollover" ></span>
								<span class="selected-pattern"></span>
								<img src="//:0" style="background:url('{$ta_img_url|escape:'quotes':'UTF-8'}/egmail/bgpattern/{$bgpattern|escape:'html':'UTF-8'}');height:90px;width:90px">
								</img>
							</a>
						</li>
					{/foreach}
				</ul>
			</div>
	</div>
	</div>
	{/if}
<div class="ta-action-bottom">
			<div class="ta-action-left">
							<a href="javascript:;" class="ta-action-button" id="egmailCancel">
								{l s='Cancel' mod='tacartreminder'}
							</a>
			</div>
			<div class="egmail-send-form">
				<div class="ta_form_loader" style="display:none;text-align:center"></div>
						<input type="text" placeholder="{l s='mymail@gmail.com,mail2@yahoo.fr' mod='tacartreminder'}" id="egmail_mails_test" class="egmail_send_test"/>
						<a href="javascript:;" class="ta-action-button egmail_send_test" {if $mode=='egmail'}id="egmailSend"{else}id="emailSend"{/if}><i class="flaticon-mail29"></i>
							{l s='Send' mod='tacartreminder'}
						</a>
				</div>
				{if $mode=='egmail'}
				<div class="ta-action-right">
							<a href="javascript:;" class="ta-action-button ta-action-save" id="egmailEdit">
								{l s='Copy to editor' mod='tacartreminder'}
							</a>
				</div>
				{/if}
			</div>
</div>