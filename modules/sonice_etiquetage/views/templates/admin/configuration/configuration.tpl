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

<!-- URL -->
<input type="hidden" id="check_login_url" value="{$sne_check_login|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_common_printserver" value="{$sne_common_printserver|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_test_printers" value="{$sne_test_printers|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_get_shop_info" value="{$sne_get_shop_info|escape:'htmlall':'UTF-8'}">

<!-- MSG -->
<input type="hidden" id="alert_deposit" value="{l s='Estimated deposit time must be a valid number.' mod='sonice_etiquetage'}">
<input type="hidden" id="alert_meca" value="{l s='Un-mechanised value must be a valid number.' mod='sonice_etiquetage'}">
<input type="hidden" id="msg_connect_error" value="{l s='Unable to connect to remote host' mod='sonice_etiquetage'}">

<!-- PRINTER CONF -->
<input type="hidden" id="printer1_name" value="{if isset($sne_config.printer1)}{$sne_config.printer1|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" id="printer2_name" value="{if isset($sne_config.printer2)}{$sne_config.printer2|escape:'htmlall':'UTF-8'}{/if}">
<form id="_form" class="defaultForm blockcms form-horizontal sne" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" autocomplete="off" method="post"
	  enctype="multipart/form-data">
	<fieldset id="sonice_configuration">
		<input type="hidden" name="selected_tab" id="selected_tab" value="{$selected_tab|escape:'htmlall':'UTF-8'}">
		<!-- SoNice Etiquetage -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_sonice_etiquetage.tpl"}
		<!-- INFORMATION -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_informations.tpl"}
		<!-- ACCOUNT -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_account.tpl"}
		<!-- ADDRESS -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_address.tpl"}
		<!-- FILTER -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_filter.tpl"}
		<!-- CARRIER -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_carrier.tpl"}
		<!-- CARRIER MAPPING -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_carrier_mapping.tpl"}
		<!-- CUSTOMS TARIFF -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_international.tpl"}
		<!-- TARE WEIGHT -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_tare.tpl"}
		<!-- Printing -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_print.tpl"}
		<!-- Configuration -->
		{include file="$sne_module_path/views/templates/admin/configuration/conf_setting.tpl"}
	</fieldset>
	<!-- Glossary -->
	{include file="$sne_module_path/views/templates/admin/configuration/glossary.tpl"}
</form>

{if isset($sne_config.legacy) && $sne_config.legacy}

<applet id="qz" code="qz.PrintApplet.class" archive="{$sne_module_dir|escape:'htmlall':'UTF-8'}tools/applet/qz-print.jar" width="1" height="1">
	<param name="jnlp_href" value="{$sne_module_dir|escape:'htmlall':'UTF-8'}tools/applet/qz-print_jnlp.jnlp">
	<param name="cache_option" value="plugin">
	<param name="disable_logging" value="false">
	<param name="initial_focus" value="false">
	<param name="codebase_lookup" value="false">
</applet>

{/if}