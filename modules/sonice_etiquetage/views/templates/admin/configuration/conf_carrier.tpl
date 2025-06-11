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

<div id="conf-carrier" class="panel form-horizontal" style="display: none;">
    <h2>{l s='Carriers' mod='sonice_etiquetage'}</h2>
    <div class="cleaner">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} :<br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/transporteurs/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/transporteurs/</a>
			</div>
		</div>
	</div>
    <div class="form-group">
        <label class="control-label col-lg-3" rel="carriers_filter">{l s='Carrier Filters' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <div class="sne_multi_select_heading">
                <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}cross.png" alt="Excluded" /></span>
                <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}tick.png" alt="Included" /></span>
            </div>
            <br>

            <select class="sne_multi_select float-left" id="available-carriers" style="margin-left: 10px;" multiple>
                <option value="0" disabled style="color:red;">{l s='Excluded Carriers' mod='sonice_etiquetage'}</option>
                {foreach $sne_carriers.available as $carrier}
                    <option value="{$carrier['id_carrier']|escape:'htmlall':'UTF-8'}">{$carrier['name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <div class="sne_sep float-left">
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}next.png" class="sne_move" id="carrier-sne_move-right" alt="Right" /><br /><br />
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}previous.png" class="sne_move" id="carrier-sne_move-left" alt="Left" />
            </div>
            <select name="filtered_carriers[]" class="sne_multi_select float-left" id="filtered-carriers" multiple>
                <option value="0" disabled style="color: #4F8A10;">{l s='Included Carriers' mod='sonice_etiquetage'}</option>
                {foreach $sne_carriers.filtered as $carrier}
                    <option value="{$carrier['id_carrier']|escape:'htmlall':'UTF-8'}">{$carrier['name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
                {* Deleted carriers *}
                {if count($sne_carriers.deleted)}
                    {*<option value="0" style="color: red;" disabled>-- {l s='Deleted Carriers, do not remove' mod='sonice_etiquetage'} --</option>*}
                    {foreach $sne_carriers.deleted as $carrier}
                        <option value="{$carrier['id_carrier']|escape:'htmlall':'UTF-8'}" style="display: none;">(#{$carrier['id_carrier']|escape:'htmlall':'UTF-8'}) {$carrier['name']|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                {/if}
            </select>
        </div>
    </div>

	<div class="clearfix">&nbsp;</div>
	<div class="clearfix">&nbsp;</div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>