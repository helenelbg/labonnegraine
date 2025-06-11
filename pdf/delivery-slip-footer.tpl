{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
 



<table style="width: 100%;">
	<tr>
		<td>

			<div style="font-family:brody;font-size:12px">La Bonne Graine vous remercie de votre commande.</div>
			<div style="font-family:dejavusans;font-size:8px">Nos graines potagères, ainsi que l’ensemble de nos produits, font l’objet de toute notre attention. C’est pour cette raison que nous ne proposons
			que des produits à la germination testée et assurée.</div>
			<div style="font-family:dejavusans;font-size:8px">Malgré cela, il peut arriver parfois que la levée ne soit pas à la hauteur de vos attentes pour de multiples raisons : températures trop basses,
			manque d’eau, enfouissement trop profond de la graine, climat inadapté, etc.</div>
			<div style="font-family:dejavusans;font-size:8px">Si toutefois vous estimez que la non-réussite de votre semis est liée à la qualité de la semence, n’hésitez pas à nous le faire savoir
			(info@labonnegraine.com) en nous communiquant le numéro du lot indiqué sur chaque paquet de graines. Nous relancerons immédiatement un test
			de germination et ferons le nécessaire afin de répondre au mieux à vos attentes.</div>
			<div style="font-family:brody;font-size:12px">L’équipe de La Bonne Graine vous souhaite de bonnes plantations !</div>

			<div style="font-size:9px; line-height:5px;">* Produits soumis au Passeport Phytosanitaire</div>
			<div style="font-size:9px; line-height:5px;">Bio : produit issu de l'agriculture biologique certifié par FR-BIO-10</div>

		</td>
	</tr>
	<tr>
		<td style="height: 20px;">
	 
		</td>
	</tr>
	<tr>
		<td style="text-align: center; font-size: 7px; color: #444;  width:100%;">
			{if $available_in_your_account}
				{l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' d='Shop.Pdf' pdf='true'}
				<br />
			{/if}
			{$shop_address|escape:'html':'UTF-8'}<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='For more assistance, contact Support:' d='Shop.Pdf' pdf='true'}<br />
				{if !empty($shop_phone)}
					{l s='Tel: %s' sprintf=[$shop_phone|escape:'html':'UTF-8'] d='Shop.Pdf' pdf='true'}
				{/if}

				{if !empty($shop_fax)}
					{l s='Fax: %s' sprintf=[$shop_fax|escape:'html':'UTF-8'] d='Shop.Pdf' pdf='true'}
				{/if}
				<br />
			{/if}

		</td>
	</tr>
</table>

