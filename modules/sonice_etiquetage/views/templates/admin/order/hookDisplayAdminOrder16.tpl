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

<div class="row" id="sonice_etiquetage">
    <!-- URL -->
    <input type="hidden" id="sne_webservice_url" value="{$sne_webservice_url|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="sne_print_type" value="{$print_type|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="sne_printer_name" value="{$printer_name|escape:'htmlall':'UTF-8'}">
	<input type="hidden" id="sne_common_printserver" value="{$sne_common_printserver|escape:'htmlall':'UTF-8'}">

	<!-- MESSAGES -->
	<input type="hidden" id="sne_printme_not_reached" value="{l s='Common-PrinterServer was not reached, please start it or change the HTTP/HTTPS mode if it is started.' mod='sonice_etiquetage'}">

    <!-- DATA -->
    <input type="hidden" name="checkbox[]" value="{$data.id_order|escape:'htmlall':'UTF-8'}">

	{if isset($sne_config.legacy) && $sne_config.legacy}
		<!-- QZ -->
		<applet id="qz" code="qz.PrintApplet.class" archive="{$sne_module_url|escape:'htmlall':'UTF-8'}tools/applet/qz-print.jar" width="1" height="1">
			<param name="jnlp_href" value="{$sne_module_url|escape:'htmlall':'UTF-8'}tools/applet/qz-print_jnlp.jnlp">
			<param name="cache_option" value="plugin">
			<param name="disable_logging" value="false">
			<param name="initial_focus" value="false">
			<param name="codebase_lookup" value="false">
		</applet>
	{/if}

    <div>
        <div class="panel">
			<div class="col-lg-6 text-center">
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}logo.png" alt="SoNice &Eacute;tiquetage"><br>
				<br>
                <strong style="font-size: 16px;">SoNice &Eacute;tiquetage</strong>
            </div>
			<div class="col-lg-6">
				{if $data.exists}
					<strong>{$data.info.parcel_number|escape:'htmlall':'UTF-8'}</strong><br>
					<strong>Date :</strong> {$data.info.date_add|escape:'htmlall':'UTF-8'}<br>
					<strong>Session :</strong> {(is_array($data.info.session)) ? ('%s (%s)'|sprintf:($data.info.session.id_session|intval):($data.info.session.alias|escape:'htmlall':'UTF-8')) : 'N/A'}<br>
                    <button type="button" id="sne_print_label" class="button btn btn-primary" rel="{$label_url|escape:'htmlall':'UTF-8'}" style="margin-top: 10px;">
                        {l s='Print' mod='sonice_etiquetage'}
                    </button>
					{if $cn23_url}
						<button type="button" id="sne_print_cn23" class="button btn btn-success" rel="{$cn23_url|escape:'htmlall':'UTF-8'}" style="margin-top: 10px;">
							CN23
						</button>
					{/if}
				{elseif isset($eligible_status) && !$eligible_status}
					<div class="clearfix">&nbsp;</div>
					<strong>{l s='Label has not been created.' mod='sonice_etiquetage'}</strong>
				{else}
					<div class="font-red">
						{l s='The label for this order has not been created yet.' mod='sonice_etiquetage'}
					</div>
					<strong>Session :</strong> {(is_array($data.info.session)) ? ('Session #%s (%s)'|sprintf:($data.info.session.id_session|intval):($data.info.session.alias|escape:'htmlall':'UTF-8')) : 'N/A'}<br>

					<button type="button" id="sne_print_label" class="button btn btn-primary" rel="" style="display: none;">{l s='Print' mod='sonice_etiquetage'}</button>
					<button type="button" id="sne_print_cn23" class="button btn btn-success" rel="" style="display: none;">CN23</button>

					<button id="sne_create_label" class="button btn btn-primary" style="margin-top: 10px;">
						{l s='Generate Label' mod='sonice_etiquetage'}
					</button>
					<img src="{$sne_img|escape:'htmlall':'UTF-8'}loader.gif"  width="16px" id="sne_loader" style="display: none; vertical-align: middle;" alt="loader">

					<div class="alert alert-success" id="sne_conf" style="display: none;">
						{l s='The label has been created successfully.' mod='sonice_etiquetage'}
					</div>
					<div class="alert alert-danger" id="sne_error" style="display: none;">
						{l s='An error occured during the process.' mod='sonice_etiquetage'}<br>
					</div>
				{/if}
			</div>
			<div class="clearfix" style="height: 95px;">&nbsp;</div>
        </div>
    </div>

	{literal}
		<script>
			$(document).ready(function() {
				if ($('#myTab').length) {
					setTimeout(function() {
						$('#myTab').parent().append('<hr>');
						$('#sonice_etiquetage').appendTo($('#myTab').parent());
					}, 100);
				}
			});
		</script>
	{/literal}
</div>