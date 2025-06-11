{**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * @package   sonice_etiquetage
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @license   Commercial license
 *}

<div id="conf-filter" class="panel form-horizontal" style="display: none;">
    <h2>{l s='Status' mod='sonice_etiquetage'}</h2>
    <div class="cleaner">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} : <br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/filtre-des-statuts/" target="_blank">
					http://documentation.common-services.com/sonice_etiquetage/filtre-des-statuts/
				</a>
			</div>
		</div>
	</div>
    <div class="form-group">
        <label class="control-label col-lg-3" rel="filter">{l s='Status Filters' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <div class="sne_multi_select_heading">
                <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}cross.png" alt="Excluded" /></span>  
                <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}tick.png" alt="Included" /></span>
            </div>
            <br>
            <select class="sne_multi_select float-left" id="available-status" style="margin-left: 10px;" multiple>
                <option value="0" disabled style="color:red;">{l s='Excluded Status' mod='sonice_etiquetage'}</option>
                {foreach $sne_status.available as $status}
                    <option value="{$status['id_order_state']|escape:'htmlall':'UTF-8'}">{$status['name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <div class="sne_sep float-left">
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}next.png" class="sne_move" id="status-sne_move-right" alt="Right" /><br /><br />
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}previous.png" class="sne_move" id="status-sne_move-left" alt="Left" />
            </div>
            <select name="filtered_status[]" class="sne_multi_select float-left" id="filtered-status" multiple>
                <option value="0" disabled style="color: #4F8A10;">{l s='Included Status' mod='sonice_etiquetage'}</option>
                {foreach $sne_status.filtered as $status}
                    <option value="{$status['id_order_state']|escape:'htmlall':'UTF-8'}">{$status['name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="cleaner clean clearfix">&nbsp;</div>
    <div class="cleaner clean clearfix">&nbsp;</div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="new_order_status_creation">{l s='New order status (Created)' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <select name="return_info[new_order_state_created]" class="order_new_status">
                <option value="0" style="color: blue;">{l s='Do not change status' mod='sonice_etiquetage'}</option>
                {foreach $sne_status.all as $state}
                    <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {if isset($sne_config.new_order_state_created) && $sne_config.new_order_state_created == $state.id_order_state}selected{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
        <div class="cleaner clean clearfix">&nbsp;</div>
		<label class="control-label col-lg-3" rel="mail_new_status" data-myat="{literal}{'my':'bottom left','at':'top left'}{/literal}">{l s='Notify customer of his tracking number' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="return_info[send_mail_creation]" id="send_mail_creation_on" value="1" {if isset($sne_config.send_mail_creation) && $sne_config.send_mail_creation}checked{/if}>
				<label for="send_mail_creation_on" class="label-checkbox">{l s='Yes' mod='sonice_etiquetage'}</label>
				<input type="radio" name="return_info[send_mail_creation]" id="send_mail_creation_off" value="0" {if !isset($sne_config.send_mail_creation) || !$sne_config.send_mail_creation}checked{/if}>
				<label for="send_mail_creation_off" class="label-checkbox">{l s='No' mod='sonice_etiquetage'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
    </div>

	<div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="new_order_status_send">{l s='New order status (Send)' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <select name="return_info[new_order_state_send]" class="order_new_status">
                <option value="0" style="color: blue;">{l s='Do not change status' mod='sonice_etiquetage'}</option>
                {foreach $sne_status.all as $state}
                    <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {if isset($sne_config.new_order_state_send) && $sne_config.new_order_state_send == $state.id_order_state}selected{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>