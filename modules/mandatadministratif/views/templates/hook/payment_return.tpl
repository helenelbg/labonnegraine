{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{if $status == 'ok'}
	<p>
		Vous avez choisi de régler par mandat administratif.
	</p>

	<ul>
		<li>
			Le montant total de votre commande s'élève à <span class="price"><strong>{$total_to_pay}</strong></span>
		</li>

		<li>
			Nous acceptons la devise suivante pour votre paiement par mandat administratif : <b>Euro</b>.
		</li>
		
		<li>
			Afin de valider votre commande, vous allez recevoir par email votre bon de commande (en .pdf).
		</li>
		
		<li>
			<u>Merci de bien vouloir nous retourner le bon de commande signé avec les informations suivantes : SIRET, numéro d’engagement Chorus.</u>
		</li>

		<li>
			Une fois la commande validée, nous vous l’expédierons et enverrons votre facture sur votre interface de paiement des administrations (Chorus).
		</li>

		<li>
			<b>La Bonne Graine vous remercie pour votre commande.</b>
		</li>		
	</ul>


{else}
	<p class="warning">
		{l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' d='Modules.Checkpayment.Shop'}
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' d='Modules.Checkpayment.Shop'}</a>.
	</p>
{/if}
