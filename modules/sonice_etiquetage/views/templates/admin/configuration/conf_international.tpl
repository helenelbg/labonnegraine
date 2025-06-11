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

<div id="conf-international" class="panel form-horizontal" style="display: none;">
	<h2>{l s='International' mod='sonice_etiquetage'}</h2>
	<div class="cleaner">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} :<br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/international/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/international/</a>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="customs_tariff">{l s='Customs Tariff' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9">
			<table cellspacing="0" cellpadding="0" width="100%" class="table">
				<tr class="active">
					<th>{l s='Name' mod='sonice_etiquetage'}</th>
					<th>{l s='Customs Tariff' mod='sonice_etiquetage'}</th>
				</tr>
				{if isset($sne_hscode) && isset($sne_hscode.html)}
                    {$sne_hscode.html|escape:'none':'UTF-8'}{* Recursively created in sonice_etiquetage.php, escape "none" else there are display issues,
					 * already filtered in PHP, no need to escape for now
					 *}
				{/if}
			</table>
		</div>
	</div>

	<div class="clearfix">&nbsp;</div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>