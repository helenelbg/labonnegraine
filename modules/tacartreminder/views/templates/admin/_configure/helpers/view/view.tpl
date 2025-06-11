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
 * override view.tpl helper to show specific element dedicated at rule
 *}
{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
{if $type_render=='rule'}
<script>
	var validate_url = '{$validate_url|escape:'quotes':'UTF-8'}';
	var nbr_steps = {$wizard_steps.steps|count};
	var tacartreminder_configure_url = '{$tacartreminder_configure_url|escape:'quotes':'UTF-8'}';
</script>
<div class="row">
	<div class="col-sm-12">
		<div id="rule_wizard" class="ta-form">
			<ul id="progressbar">
				{foreach from=$wizard_steps.steps key=step_nbr item=step}
					<li {if $step_nbr==0}class="active"{/if}>
						{$step.title|escape:'htmlall':'UTF-8'}<br />
						{if isset($step.desc)}<small>{$step.desc|escape:'htmlall':'UTF-8'}</small>{/if}
					</li>
				{/foreach}
			</ul>
			{foreach from=$wizard_contents.contents key=step_nbr item=content}
				<fieldset class="ta-step"> 	
					{$content}{*HTML CONTENT*}
					<div class="button-option-bar">
						{if $step_nbr == 0}<a href="{$tacartreminder_configure_url|escape:'quotes':'UTF-8'}" class="previous action-button" >{l s='Return' mod='tacartreminder'}</a>{/if}
						{if $step_nbr > 0}<input type="button" name="previous" class="previous action-button" value="{l s='Previous' mod='tacartreminder'}" />{/if}
						<div class="process-next-save">
							{if $step_nbr < ($wizard_steps.steps|count - 1)}<input type="button" name="next" class="next action-button" value="{l s='Next' mod='tacartreminder'}" />{/if}
							<input type="button" name="submitRule" class="action-button pull-right  save-button" value="{l s='Save' mod='tacartreminder'}" />
						</div>
					</div>
				</fieldset>
			{/foreach}
		</div>
	</div>
</div>
{elseif $type_render=='mail_template'}
{*if $ps_version < "1.6"}
	<script type="text/javascript">
			var iso = '{$iso|addslashes}';
			var pathCSS = '{$smarty.const._THEME_CSS_DIR_|addslashes}';
			var ad = '{$ad|addslashes}';
			$(document).ready(function(){
			tinySetup({
				editor_selector :"autoload_rte",
				relative_urls : false,
				remove_script_host : false,
				plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor fullpage",
				extended_valid_elements : "em[class|name|id],html,head"
			});
			});
	</script>
{/if*}
<div class="row">
	<div class="col-sm-12 ta-form" id="mail_template_form">
		{$content}{*HTML CONTENT*}
	</div>
</div>
{/if}
{/block}
