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

<div id="glossary" style="display: none;">
	{* conf_account.tpl *}
	<div class="login">
		{l s='This is your 6 caracters user number, provided by La Poste Colissimo at the opening of your seller account.' mod='sonice_etiquetage'}<br>
		<br>
		{l s='You have to subscribe a So Colissimo Flexibilite contract with Coliposte to receive your login and password in order to access this service.' mod='sonice_etiquetage'}<br>
		<br>
		<img src="{$sne_img|escape:'htmlall':'UTF-8'}/glossary/login.png">
	</div>
	<div class="pwd">
		{l s='Password linked to your credentials upper.' mod='sonice_etiquetage'}<br>
		{l s='It must be the same as the one in your customer area.' mod='sonice_etiquetage'}<br>
		<br>
		{l s='You have to subscribe a So Colissimo Flexibilite contract with Coliposte to receive your login and password in order to access this service.' mod='sonice_etiquetage'}<br>
		<br>
		<img src="{$sne_img|escape:'htmlall':'UTF-8'}/glossary/login.png">
	</div>
	<div class="debug_mode">
		{l s='Enable traces for debugging and developpment purpose.' mod='sonice_etiquetage'}<br>
		<b {if isset($sne_debug) && $sne_debug}style="color: red;"{/if}>{l s='In exploitation this option must not be active !' mod='sonice_etiquetage'}</b>
	</div>
	<div class="test_mode">
		{l s='This is a demonstration or developpment mode, API calls are fakes.' mod='sonice_etiquetage'}<br>
		{l s='Use for developpment purpose only or for tests and validate the module under this environment.' mod='sonice_etiquetage'}<br>
		<b {if isset($sne_test_mode) && $sne_test_mode}style="color: red;"{/if}>{l s='In exploitation this option must not be active !' mod='sonice_etiquetage'}</b>
	</div>
	{* conf_filter.tpl *}
	<div class="filter">
		{l s='This tool allows you to select status available for label creation.' mod='sonice_etiquetage'}<br>
		{l s='Select a status on the right side and push it on the left side to select it.' mod='sonice_etiquetage'}
	</div>
	<div class="new_order_status_creation">
		{l s='Status which you want your order to be once the label has been created.' mod='sonice_etiquetage'}
	</div>
	<div class="mail_new_status">
		{l s='The module will send an email to your customer once the label is created for his order with the tracking number.' mod='sonice_etiquetage'}<br>
		{l s='The mail template used is the default template of PrestaShop (in_transit).' mod='sonice_etiquetage'}<br>
		<br>
		<img src="{$sne_img|escape:'htmlall':'UTF-8'}/glossary/mail_creation.png">
	</div>
	<div class="new_order_status_send">
		{l s='Status which you want your order to be once the label has been marked as "Sent".' mod='sonice_etiquetage'}
	</div>
	{* conf_carriers.tpl *}
	<div class="carriers_filter">
		{l s='This tool allows you to select your Colissimo carriers with which retrieve orders to generate labels.' mod='sonice_etiquetage'}<br>
		{l s='Select a carrier on the right side and push it on the left side to select it.' mod='sonice_etiquetage'}
	</div>
	{* conf_carrier_mapping.tpl *}
	<div class="selected_carrier">
		{l s='This tool allows you to map your Colissimo carriers with their regate code.' mod='sonice_etiquetage'}
	</div>
	{* conf_print.tpl *}
	<div class="output_print_type">
		{l s='Select the output format for your labels.' mod='sonice_etiquetage'}
	</div>
	<div class="label_printer">
		{l s='Select the printer that will be used to print your labels.' mod='sonice_etiquetage'}<br>
		{l s='The selected printer must match with your label print format.' mod='sonice_etiquetage'}
	</div>
	<div class="slip_printer">
		{l s='Select the printer that will be used to print your slips.' mod='sonice_etiquetage'}<br>
		{l s='The selected printer must match with a A4 paper printer.' mod='sonice_etiquetage'}
	</div>
	<div class="common-printserver">
		{l s='This is the utility software needed to print your label to the thermal printer.' mod='sonice_etiquetage'}<br>
		{l s='Please, read the documentation for me informations.' mod='sonice_etiquetage'}
	</div>
	<div class="legacy">
		{l s='Enable this mode if you wish to use the old printing method via QZ.' mod='sonice_etiquetage'}<br>
		{l s='Disable it to use the Common-PrintServer utility.' mod='sonice_etiquetage'}<br>
		<br>
		<strong>{l s='Recommended value : [NO]' mod='sonice_etiquetage'}</strong>
	</div>
	{* conf_settings.tpl *}
	<div class="pickup_charge_site">
		{l s='Identification code (REGATE) of the Pickup Charge Site (6 numerical caracters).' mod='sonice_etiquetage'}<br>
		{l s='This information will appear on the delivery slip.' mod='sonice_etiquetage'}<br>
		<br>
		<i>{l s='Optional' mod='sonice_etiquetage'}</i>
	</div>
	<div class="pickup_charge_site_label">
		{l s='Pickup Charge Site clear label (40 alphabetic caracters), given by the commercial representative.' mod='sonice_etiquetage'}<br>
		{l s='This information will appear on the delivery slip.' mod='sonice_etiquetage'}<br>
		<br>
		<i>{l s='Optional' mod='sonice_etiquetage'}</i>
	</div>
	<div class="estimated_deposit_date">
		{l s='Number of days after label creation before you bring your package to the post-office.' mod='sonice_etiquetage'}
	</div>
	<div class="data_handling">
		{l s='This configuration depends on your carrier module. If you use So Colissimo, So Colissimo Libert&eacute;/Flexibilit&eacute; you should use Prestashop Exp&eacute;ditor Inet, else Third Party.' mod='sonice_etiquetage'}
	</div>
	<div class="return_type_choice">
		{l s='Action to perform in case of parcel return (for Expert I or So Colissimo International).' mod='sonice_etiquetage'}
	</div>
	<div class="weight_unit">
		{l s='The weight unit of your shop. Correspond to the weight unit you are currently using for your products.' mod='sonice_etiquetage'}
	</div>
	<div class="compact_mode_default">
		{l s='Display the Orders > SoNice Étiquetage page with compact mode enabled by default.' mod='sonice_etiquetage'}<br>
		{l s='Less informations will be displayed for high readability, however it is possible to enable normal display from this page.' mod='sonice_etiquetage'}
	</div>
	{* conf_international.tpl *}
	<div class="customs_tariff">
		Depuis 1988, la Communaut&eacute; europ&eacute;enne a, apr&egrave;s nombre d'&eacute;tats dans le monde,
		adopt&eacute; le <strong>Syst&egrave;me Harmonis&eacute;</strong> (SH) de d&eacute;signation des marchandises pour les envois
		commerciaux. Ce "num&eacute;ro tarifaire" &agrave; <strong>6 chiffres</strong> permet d'identifier de mani&egrave;re
		unique et dans le monde entier tous les objets physiques. Il est un des trois &eacute;l&eacute;ments
		permettant d'&eacute;tablir la taxation en douane, avec le montant des frais de port et
		l'origine de la marchandise.<br>
		<br>
		Pour permettre un traitement rapide des op&eacute;rations douani&egrave;res &agrave; l'arriv&eacute;e dans le pays
		de destination, il est ainsi recommand&eacute; aux entreprises d'indiquer le num&eacute;ro tarifaire
		de la marchandise envoy&eacute;e.
		Dans les &eacute;changes postaux, seule l'indication du num&eacute;ro tarifaire &agrave; 6 chiffres constitue
		une obligation pour les entreprises exp&eacute;ditrices.<br>
		<br>
		<strong>O&ugrave; se procurer le num&eacute;ro tarifaire ?</strong>
		<ul>
			<li>Se rapprocher des cellules de douane de sa r&eacute;gion pour des envois r&eacute;guliers</li>
			<li>Se rendre sur le site de la douane fran&ccedil;aise : <a href="https://pro.douane.gouv.fr/prodouane.asp" target="_blank">https://pro.douane.gouv.fr/prodouane.asp</a> (dans l'encyclop&eacute;die tarifaire RITA, consulter la nomenclature)</li>
		</ul>
	</div>
</div>