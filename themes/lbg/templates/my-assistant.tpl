{extends file='page.tpl'}

{block name='page_content'}

<h1>Mes assistants</h1>

<input type="hidden" id="idCustomer" value="{$idCustomer}">

{if isset($namedAssistant) && !empty($namedAssistant)}
<span class="info_localisation"><h2>Localisation de mes cultures</h2>{$adresseAssistant[0]["numDepartement"]} - {$adresseAssistant[0]["labelDepartement"]}{if $adresseAssistant[0]["montagne"] eq 1} | Haute altitude : {if $adresseAssistant[0]["montagne"] eq 0}non{else}oui{/if}{/if}</span>
{/if}
{if isset($namedAssistant) && !empty($namedAssistant)}
	{foreach from=$namedAssistant item=assistant}
		<div class="my_assistant">
			<div class="my_assistant_content">
				<div class="my_assistant_content2">
					<img class="my_assistant_image" src="{$assistant["image"]}">
				</div>
			</div>
			<span class="my_assistant_texte">{$assistant["value"]}</span>
			{if $assistant["etat"] eq 1}<br><span class="my_assistant_texte">Inscription validée</span>{/if}
			<span class="my_assistant_cross {$assistant['id_feature_value']}">&times;</span>

			{if $assistant["titre_etape"]}<div>Étape actuelle : {$assistant["titre_etape"]}</div>{/if}
			{if $assistant["jours_restants"]}<div>Prochaine étape dans {$assistant["jours_restants"]} jour{if $assistant["jours_restants"]>=2}s{/if}</div>{/if}

		</div>
	{/foreach}
{else}
	<br><br>
	<span style="text-transform: uppercase; font-weight: bold; border-bottom: solid 3px #99b06a;">Aucun assistant lié à ce compte</span>
{/if}

<div id="append_cyril"></div>

<div class="cyril_dernieres_commandes">
	<span class="center">Assistants disponibles basés sur vos dernières commandes</span>
	<table>
		<tbody>
		{foreach from=$resultProduit key=k item=monProduit}
			<tr>
				<td>
					<img class="cyril_dernieres_commandes_logo" src="{$monProduit["image"]}" alt="{$monProduit.value|escape:'html':'UTF-8'}" />
				</td>
				<td>
					{$monProduit["value"]}
				</td>
				<td>
					<img src="/themes/default-bootstrap/img/tete-assistant-cyril.png" alt="cyril" title="Je m'appelle Cyril et je suis l'assistant jardinier de La Bonne Graine. Si vous débutez en jardinage, je vous envoie par mail des conseils pas à pas durant toute la culture de vos légumes" >
					<label class="switch-cyril">
					  <input type="checkbox" class="checkboxProduit not_uniform comparator" value="{$monProduit['id_feature_value']}" {if $monProduit["etat"] eq '1' || $monProduit["etat"] eq '2'} checked="checked" {/if}>
					  <span class="slider round"></span>
					</label>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>

<div>
	<select id="depCyril" name="dep">
		{foreach from=$lesDepartements item=unDepartement}
			{if $adresseLivraison eq $unDepartement.numDepartement}
				<option value="{$unDepartement.numDepartement}" selected="selected">{$unDepartement.numDepartement} - {$unDepartement.labelDepartement}</option>
				{assign var="isMontagne" value=$unDepartement.montagne}
			{else}
				<option value="{$unDepartement.numDepartement}">{$unDepartement.numDepartement} - {$unDepartement.labelDepartement}</option>
			{/if}
		{/foreach}
	</select>

	<div id="formAltitude" {if $isMontagne eq 0} style="display: none" {/if}>

		<p>Votre plantation aura-t-elle lieu au dessus de 500 m d'altitude ?</p>

		<div>
			<input type="radio" id="ouiAltitude" name="altitude" value="1">
			<label for="ouiAltitude">Oui</label>
		</div>

		<div>
			<input type="radio" id="nonAltitude" name="altitude" value="0" checked>
			<label for="nonAltitude">Non</label>
		</div>

	</div>

</div>
{/block}
