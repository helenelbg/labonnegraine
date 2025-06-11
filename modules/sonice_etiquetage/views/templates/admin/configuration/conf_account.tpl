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

<div id="conf-account" class="panel form-horizontal" style="display: none;">
	<h2>{l s='Login' mod='sonice_etiquetage'}</h2>
	<div class="clearfix">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} :<br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/configuration-identifiants/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/configuration-identifiants/</a>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="login" data-myat="{literal}{'my':'top left','at':'bottom left'}{/literal}">{l s='Login' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<input type="text" data-type="positive_integer" name="return_info[ContractNumber]" value="{$sne_config.ContractNumber|escape:'htmlall':'UTF-8'}" class="connectParam">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-lg-3" rel="pwd" data-myat="{literal}{'my':'top left','at':'bottom left'}{/literal}">{l s='Password' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<input type="password" name="return_info[Password]" value="{$sne_config.Password|escape:'htmlall':'UTF-8'}" class="connectParam">
		</div>
	</div>

    <div class="form-group">
        <label class="control-label col-lg-3">&nbsp;</label>
        <div class="col-lg-9 margin-form">
            <input type="button" class="button btn btn-primary" id="login_checker" value="{l s='Check your login' mod='sonice_etiquetage'}">
            &nbsp;<img src="{$sne_img|escape:'htmlall':'UTF-8'}import-loader.gif" alt="loader" id="sne_loader" style="width: 20px; display: none;">
        </div>
    </div>

    <div class="form-group login_checker_result" style="display: none;">
        <label class="control-label col-lg-3">&nbsp;</label>
        <div class="col-lg-9 margin-form">
            <div class="{$alert_class.success|escape:'htmlall':'UTF-8'}" id="login_ok" style="display: none;">
                {l s='Your login is correct !' mod='sonice_etiquetage'}
            </div>
            <div class="{$alert_class.danger|escape:'htmlall':'UTF-8'}" id="login_not_ok" style="display:none">
                {l s='Your login is incorrect :' mod='sonice_etiquetage'}<br>
                <strong>ID :</strong> <span id="errorID"></span><br>
                <strong>Message :</strong> <span id="error"></span>
				<span id="error_request"></span>
				<span id="error_response"></span>
            </div>
        </div>
    </div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="margin-form col-lg-9">
			<hr>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="debug_mode">{l s='Debug mode' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="sne_debug" id="debug_on" value="1" {if isset($sne_debug) && $sne_debug}checked{/if}>
				<label for="debug_on" class="label-checkbox">{l s='Yes' mod='sonice_etiquetage'}</label>
				<input type="radio" name="sne_debug" id="debug_off" value="0" {if !isset($sne_debug) || !$sne_debug}checked{/if}>
				<label for="debug_off" class="label-checkbox">{l s='No' mod='sonice_etiquetage'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="test_mode">{l s='Test mode' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="sne_test_mode" id="test_mode_on" value="1" {if isset($sne_test_mode) && $sne_test_mode}checked{/if}>
				<label for="test_mode_on" class="label-checkbox">{l s='Yes' mod='sonice_etiquetage'}</label>
				<input type="radio" name="sne_test_mode" id="test_mode_off" value="0" {if !isset($sne_test_mode) || !$sne_test_mode}checked{/if}>
				<label for="test_mode_off" class="label-checkbox">{l s='No' mod='sonice_etiquetage'}</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>