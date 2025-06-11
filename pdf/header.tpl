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


<table style="width: 100%">
<tr>
	<td style="width: 15%">
		{if $logo_path}
			<img src="{$logo_path}" style="width:60px; height:60px;" />
		{/if}
	</td>
	<td style="width: 65%">
		<span style="font-size:12px;font-weight:bold;">La Bonne Graine</span><br style="line-height:0.9;">
		<span style="font-size:7px;">SARL LBG 49, au capital de 50.000 €</span><br style="line-height:0.9;">
		<span style="font-size:7px;">ZA Le Pontail 49540 Aubigné sur Layon</span><br style="line-height:0.9;">
		<span style="font-size:7px;">Téléphone : 0241517993</span><br style="line-height:0.9;">
		<span style="font-size:7px;">TVA Intra. : FR 62 813218658</span><br style="line-height:0.9;">
		<span style="font-size:7px;">APE : 4791B SIRET : 813 218 658 R.C.S. Angers</span><br style="line-height:1.2;">
		<span style="font-size:7px;line-height:1;">Membre d'une association agréée, le règlement par chèque est accepté.</span>
	</td>
	<td style="width: 20%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'html':'UTF-8'}</td>
			</tr>
		</table>
	</td>
</tr>
</table>

