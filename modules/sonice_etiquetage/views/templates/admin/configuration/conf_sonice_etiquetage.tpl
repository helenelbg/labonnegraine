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

<div id="conf-sonice_etiquetage" class="panel form-horizontal">
	<h2>SoNice &Eacute;tiquetage v{$sne_version|escape:'htmlall':'UTF-8'}</h2>
	<div class="clearfix">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="margin-form col-lg-9">
			<p class="descriptionBold">{l s='This module allows you to edit Colissimo and So Colissimo labels for your national and international shippings.' mod='sonice_etiquetage'}</p>
			<hr />
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Informations' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9" style="margin-top: 6px;">
			<span style="color:navy">{l s='This module is provided by' mod='sonice_etiquetage'} :</span> Common-Services<br>
			<br>
			<span style="color:navy">{l s='Informations, follow up on our blog' mod='sonice_etiquetage'} :</span><br>
			<a href="http://www.common-services.com" target="_blank">http://www.common-services.com</a><br>
			<br>
			<span style="color:navy">{l s='More informations about us on Prestashop website' mod='sonice_etiquetage'} :</span><br>
			<a href="http://www.prestashop.com/fr/agences-web-partenaires/or/common-services" target="_blank">http://www.prestashop.com/fr/agences-web-partenaires/or/common-services</a><br>
			<br>
			<span style="color:navy">{l s='You will appreciate our other modules' mod='sonice_etiquetage'} :</span><br>
			<a href="http://addons.prestashop.com/fr/58_common-services" target="_blank">http://addons.prestashop.com/fr/58_common-services</a>
		</div>
	</div>
	<br>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Documentation' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9">
			<div class="col-lg-1"><img src="{$sne_img|escape:'htmlall':'UTF-8'}books.png" alt="docs" /></div>
			<div class="col-lg-11">
				<span style="color:red; font-weight:bold;">{l s='Please, first read the provided documentation' mod='sonice_etiquetage'} :</span><br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/</a>
			</div>
		</div>
	</div>
	<br>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Support' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9">
			<div class="col-lg-1"><img src="{$sne_img|escape:'htmlall':'UTF-8'}submit_support_request.png" alt="support"></div>
			<div class="col-lg-11">
                        <span style="color:red; font-weight:bold;">
                            {l s='The technical support is available by e-mail only.' mod='sonice_etiquetage'}
                        </span><br>
                        <span style="color: navy;">
                            {l s='For any support, please provide us' mod='sonice_etiquetage'} :<br>
                        </span>
				<ul>
					<li>{l s='A detailled description of the issue or encountered problem' mod='sonice_etiquetage'}</li>
					<li>{l s='Your Pretashop Addons Order ID available in your Prestashop Addons order history' mod='sonice_etiquetage'}</li>
					<li>{l s='Your Prestashop version' mod='sonice_etiquetage'} : <span style="color: red;">Prestashop {$ps_version|escape:'htmlall':'UTF-8'}</span></li>
					<li>{l s='Your module version' mod='sonice_etiquetage'} : <span style="color: red;">SoNice &Eacute;tiquetage v{$sne_version|escape:'htmlall':'UTF-8'}</span></li>
				</ul>
				<br>
				<span style="color:navy">{l s='Support Common-Services' mod='sonice_etiquetage'} :</span>
				<a href="mailto:support.sonice@common-services.com?subject={l s='Support for SoNice Etiquetage' mod='sonice_etiquetage'}&body={l s='Dear Support, I am currently having some trouble with your module v%s on my Prestashop v%s.' sprintf=[$sne_version, $ps_version] mod='sonice_etiquetage'}" title="Email" >
					support.sonice@common-services.com
				</a><br>
				<br>
			</div>
			<hr style="clear: both;">
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">{l s='Licence' mod='sonice_etiquetage'}</label>
		<div class="margin-form col-lg-9">
			<p style="padding-top: 5px;">
				{l s='This add-on is under a commercial licence from S.A.R.L. SMC' mod='sonice_etiquetage'}.<br>
				{l s='In case of purchase on Prestashop Addons, the invoice is the final proof of license.' mod='sonice_etiquetage'}<br>
				{l s='Contact us to obtain a license only in other cases' mod='sonice_etiquetage'} : <a href="mailto:contact@common-services.com">contact@common-services.com</a>
			</p>
		</div>
	</div>
</div>