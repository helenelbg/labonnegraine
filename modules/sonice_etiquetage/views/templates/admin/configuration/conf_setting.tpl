{**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 *}

<div id="conf-conf" class="panel form-horizontal" style="display: none;">
    <h2>{l s='Settings' mod='sonice_etiquetage'}</h2>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} :<br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/configuration/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/configuration/</a>
			</div>
		</div>
	</div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="compact_mode_default">{l s='Compact mode by default' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="return_info[compact_mode]" id="compact_mode_on" value="1" {if isset($sne_config.compact_mode) && $sne_config.compact_mode}checked{/if}>
				<label for="compact_mode_on" class="label-checkbox">{l s='Yes' mod='sonice_etiquetage'}</label>
				<input type="radio" name="return_info[compact_mode]" id="compact_mode_off" value="0" {if !isset($sne_config.compact_mode) || !$sne_config.compact_mode}checked{/if}>
				<label for="compact_mode_off" class="label-checkbox">{l s='No' mod='sonice_etiquetage'}</label>
				<a class="slide-button btn"></a>
			</span>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">&nbsp;</label>
        <div class="col-lg-9 margin-form">
            <br>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Delivery slip' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <br>
        </div>
    </div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="">{l s='Generate PDF417 (barcode)' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="return_info[pdf417]" id="pdf417_on" value="1" {if isset($sne_config.pdf417) && $sne_config.pdf417}checked{/if}>
				<label for="pdf417_on" class="label-checkbox">{l s='Yes' mod='sonice_etiquetage'}</label>
				<input type="radio" name="return_info[pdf417]" id="pdf417_off" value="0" {if !isset($sne_config.pdf417) || !$sne_config.pdf417}checked{/if}>
				<label for="pdf417_off" class="label-checkbox">{l s='No' mod='sonice_etiquetage'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="pickup_charge_site">{l s='Pick-up charge site' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <input type="text" name="return_info[pickup_site]" value="{if isset($sne_config.pickup_site)}{$sne_config.pickup_site|escape:'htmlall':'UTF-8'}{/if}">
        </div>
    </div>

    
    <div class="form-group">
        <label class="control-label col-lg-3"  rel="pickup_charge_site_label">{l s='Pick-up charge label' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <input type="text" name="return_info[pickup_label]" value="{if isset($sne_config.pickup_label)}{$sne_config.pickup_label|escape:'htmlall':'UTF-8'}{/if}">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">&nbsp;</label>
        <div class="col-lg-9 margin-form">
            <br>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">Web Service</label>
        <div class="col-lg-9 margin-form">
            <br>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="estimated_deposit_date">{l s='Estimated Deposit Date' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <select name="return_info[deposit_date]" style="width: 60px; display: inline;">
                {section name=nb_days start=0 loop=15}
                    <option value="{$smarty.section.nb_days.index|escape:'htmlall':'UTF-8'}" {if isset($sne_config.deposit_date) && $sne_config.deposit_date == $smarty.section.nb_days.index}selected{/if}>{$smarty.section.nb_days.index|escape:'htmlall':'UTF-8'}</option>
                {/section}
            </select> {l s='days' mod='sonice_etiquetage'}
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="data_handling">{l s='Data Handling / Compatibility' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <select name="return_info[compatibility]">
                <option value="1" {if isset($sne_config.compatibility) && $sne_config.compatibility}selected{/if}>Colissimo Flexibilit&eacute; / Prestashop Expéditor Inet</option>
                <option value="0" {if isset($sne_config.compatibility) && !$sne_config.compatibility}selected{/if}>{l s='Third Party' mod='sonice_etiquetage'}</option>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-lg-3" rel="return_type_choice">{l s='Return type choice' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <select name="return_info[returnTypeChoice]">
                <option value="2" {if isset($sne_config.returnTypeChoice) && $sne_config.returnTypeChoice == 2}selected{/if}>{l s='Do not return to the sender (all destinations)' mod='sonice_etiquetage'}</option>
                <option value="3" {if isset($sne_config.returnTypeChoice) && $sne_config.returnTypeChoice == 3}selected{/if}>{l s='Return to the sender as priority parcel (Out of EU only)' mod='sonice_etiquetage'}</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3" rel="weight_unit">{l s='Weight unit' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            <select name="return_info[weight_unit]">
                <option value="Kg" {if isset($sne_config.weight_unit) && $sne_config.weight_unit == 'Kg'}selected{/if}>Kg</option>
                <option value="g" {if isset($sne_config.weight_unit) && $sne_config.weight_unit == 'g'}selected{/if}>g</option>
            </select>
        </div>
    </div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>