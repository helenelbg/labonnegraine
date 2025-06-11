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

<div id="conf-print" class="panel form-horizontal" style="display: none;">
    <h2>{l s='Printing options' mod='sonice_etiquetage'}</h2>
    <div class="cleaner">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} :<br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/impression/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/impression/</a>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="output_print_type">{l s='Output label format' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<select name="return_info[output_print_type]">
				<option value="PDF_A4_300dpi" {if $sne_config.output_print_type == 'PDF_A4_300dpi'}selected{/if}>PDF - 21x29.7cm (A4) - 300dpi</option>
				<option value="PDF_10x15_300dpi" {if $sne_config.output_print_type == 'PDF_10x15_300dpi'}selected{/if}>PDF - 10x15cm - 300dpi</option>
				<option value="ZPL_10x15_203dpi" {if $sne_config.output_print_type == 'ZPL_10x15_203dpi'}selected{/if}>ZPL - 10x15cm - 203dpi (* {l s='Recommanded' mod='sonice_etiquetage'})</option>
				<option value="ZPL_10x15_300dpi" {if $sne_config.output_print_type == 'ZPL_10x15_300dpi'}selected{/if}>ZPL - 10x15cm - 300dpi</option>
				<option value="DPL_10x15_203dpi" {if $sne_config.output_print_type == 'DPL_10x15_203dpi'}selected{/if}>DPL - 10x15cm - 203dpi</option>
				<option value="DPL_10x15_300dpi" {if $sne_config.output_print_type == 'DPL_10x15_300dpi'}selected{/if}>DPL - 10x15cm - 300dpi</option>
			</select>
		</div>
	</div>

	<div class="form-group" id="merge_pdf" {if in_array($sne_config.output_print_type, array('ZPL_10x15_203dpi', 'ZPL_10x15_300dpi', 'DPL_10x15_203dpi', 'DPL_10x15_300dpi'))}style="display: none;"{/if}>
		<label class="control-label col-lg-3" rel="merge_pdf">{l s='Merge PDF' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="return_info[merge_pdf]" id="merge_pdf_on" value="1" {if isset($sne_config.merge_pdf) && $sne_config.merge_pdf}checked{/if}>
				<label for="merge_pdf_on" class="label-checkbox">Oui</label>
				<input type="radio" name="return_info[merge_pdf]" id="merge_pdf_off" value="0" {if !isset($sne_config.merge_pdf) || !$sne_config.merge_pdf}checked{/if}>
				<label for="merge_pdf_off" class="label-checkbox">Non</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div class="form-group" id="label_printer" {if !in_array($sne_config.output_print_type, array('ZPL_10x15_203dpi', 'ZPL_10x15_300dpi', 'DPL_10x15_203dpi', 'DPL_10x15_300dpi'))}style="display: none;"{/if}>
		<label class="control-label col-lg-3" rel="label_printer">{l s='Label Printer' mod='sonice_etiquetage'}</label>
		<div class="col-lg-8 margin-form">
			<select name="return_info[printer2]">
				{foreach $sne_printers_list as $printer}
					<option value="{$printer|escape:'htmlall':'UTF-8'}" {(isset($sne_config.printer2) && $sne_config.printer2 == $printer) ? 'selected' : ''}>
						{$printer|escape:'htmlall':'UTF-8'}
					</option>
				{/foreach}
			</select>
		</div>
		<div class="col-lg-1">
			<button type="button" class="btn btn-primary button test_printer" rel="ZPL">Test</button>
		</div>
	</div>

	<div class="clearfix">&nbsp;</div>

	<div class="form-group" id="legacy" {if !in_array($sne_config.output_print_type, array('ZPL_10x15_203dpi', 'ZPL_10x15_300dpi', 'DPL_10x15_203dpi', 'DPL_10x15_300dpi'))}style="display: none;"{/if}>
		<label class="control-label col-lg-3" rel="legacy">{l s='Legacy Mode' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="return_info[legacy]" id="legacy_on" value="1" {if isset($sne_config.legacy) && $sne_config.legacy}checked{/if}>
				<label for="legacy_on" class="label-checkbox">Oui</label>
				<input type="radio" name="return_info[legacy]" id="legacy_off" value="0" {if !isset($sne_config.legacy) || !$sne_config.legacy}checked{/if}>
				<label for="legacy_off" class="label-checkbox">Non</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div class="clearfix">&nbsp;</div>

	<div class="form-group" id="printme" {if $sne_config.legacy|default:0 || !in_array($sne_config.output_print_type, array('ZPL_10x15_203dpi', 'ZPL_10x15_300dpi', 'DPL_10x15_203dpi', 'DPL_10x15_300dpi'))}style="display: none;"{/if}>
		<label class="control-label col-lg-3" rel="common-printserver">{l s='Common-PrintServer Utility' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<a href="https://dl.dropboxusercontent.com/u/60698220/Common-PrintServer.zip" target="_blank" class="button btn btn-primary">{l s='Download' mod='sonice_etiquetage'}</a>
		</div>
	</div>

	<div class="clearfix">&nbsp;</div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>