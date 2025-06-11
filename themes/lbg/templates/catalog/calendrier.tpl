{$cal1 = Product::getPlantationRecolte($product.id)}
{if $cal1}
	{$T_mois[1]="Jan."}
	{$T_mois[2]="Fév."}
	{$T_mois[3]="Mar."}
	{$T_mois[4]="Avr."}
	{$T_mois[5]="Mai"}
	{$T_mois[6]="Juin"}
	{$T_mois[7]="Jui."}
	{$T_mois[8]="Août"}
	{$T_mois[9]="Sep."}
	{$T_mois[10]="Oct."}
	{$T_mois[11]="Nov."}
	{$T_mois[12]="Déc."}

	<h3 id="titre_conditions_de_culture" class="mobile page-product-heading">Conditions de culture</h3>
	<div class="legende_cdiv">
		<p class="legende_c"><span class="nom_legende_calendrier">Semis/Plantation</span><span id="legende_plantation" class="legende_calendrier"></span></p>
		<p class="legende_c"><span class="nom_legende_calendrier">R&eacute;colte</span><span id="legende_recolte" class="legende_calendrier"></span></p>
	</div>
	<table id="tableau_calendrier">
		<tr>
		{for $pos_mois=1 to 12}
			<td class="{if isset($cal1['plantation'][$pos_mois])} td_plantation {/if}{if isset($cal1['recolte'][$pos_mois])}td_recolte{/if}" >
				<div class="contenu_calendrier">{$T_mois[$pos_mois]}</div>
			</td>
		{/for}
		</tr>
	</table>
{/if}