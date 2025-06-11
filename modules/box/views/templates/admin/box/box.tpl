{$saison = 0}
{if isset($smarty.get.saison)}
	{$saison = $smarty.get.saison}
{/if}

<style>
    #div_gestion_box{
        color:black;
    }
    #div_gestion_box h1 {

    }
    #div_gestion_box h2 {
        font-size: 14px;
        color:black;
        text-align: center;
    }
    #div_gestion_box .stats_box {
        color:black;
        text-align: center;
        font-size: 14px;
        max-width: 600px;
        margin: 6px auto 6px auto;

    }
    .stats_box h5.titre_stat {
        font-weight: bold;
        font-size: 14px;
        margin-top: 5px;
        margin-bottom: 3px;
    }
    #tableau_box_onglets {
        width: 100%;
    }
    #tableau_box_onglets th{
        background-color: red;
        color:white;
        padding-left:15px;
        padding-right: 15px;
        padding-top: 5px;
        padding-bottom: 5px;
        border:5px solid white;
        font-weight: normal;
    }
    #tableau_box_onglets th a{
        padding: 0;
        width: 100%;
        height: 100%;
        line-height: 100%;
        color:white;
        display: block;
        padding: 5px;
        text-decoration: none;
    }
    #tableau_box {
        width: 100%;
        margin-top: 30px;
    }
    #tableau_box th, #tableau_box td {
        padding-left: 6px;
        padding-right: 6px;
        text-align: left;
        color:black;
    }
    #tableau_box th {
        border: solid grey 1px;
        font-weight: bold;
        padding-top: 3px;
        padding-bottom: 15px;
    }
    #tableau_box td {
        border: solid grey 1px;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .td_nom {
        width: 35%;
    }
    input[value="Modifier"] {
        padding: 3px;
        margin-left: 7px;
        font-size: 18px;
    }
    .un_div_stat {
        border: 1px solid #CCCCCC;
        padding: 4px;
        margin-bottom: 20px;
    }
    input[name="action_traiter"] {
        font-size: 14px;
        padding: 3px;
    }
	
    .input-container {
        width: 49%;
        display: inline-block;
    }
    
    .input-label {
        width: 33%;
        display: inline-block;
    }
	
</style>
<div id="div_gestion_box">
    <h1>Gestion des commandes de Box</h1>
    <table id="tableau_box_onglets">
        <tr><th><a href="index.php?controller=AdminBox&d&saison=-4&token={$smarty.get.token}">Saison -4</a></th><th><a href="index.php?controller=AdminBox&d&saison=-3&token={$smarty.get.token}">Saison -3</a></th><th><a href="index.php?controller=AdminBox&d&saison=-2&token={$smarty.get.token}">Saison -2</a></th><th><a href="index.php?controller=AdminBox&d&saison=-1&token={$smarty.get.token}">Saison -1</a></th><th><a href="index.php?controller=AdminBox&d&saison=1&token={$smarty.get.token}">Saison 1</a></th><th><a href="index.php?controller=AdminBox&d&saison=2&token={$smarty.get.token}">Saison 2</a></th><th><a href="index.php?controller=AdminBox&d&saison=3&token={$smarty.get.token}">Saison 3</a></th><th><a href="index.php?controller=AdminBox&d&saison=4&token={$smarty.get.token}">Saison 4</a></th></tr>
    </table>
    <h2>Saison {$saison} : exp&eacute;dition mi-{$mois_lien}</h2>
    <div class="stats_box">
        {$stat_insolite_xxl=$T_stat_declinaisons[11771] + $T_stat_declinaisons[11772] + $T_stat_declinaisons[11773] + $T_stat_declinaisons[11774]}
        {$stat_insolite_standard=$T_stat_declinaisons[11767] + $T_stat_declinaisons[11768] + $T_stat_declinaisons[11769] + $T_stat_declinaisons[11770]}
        <div class="un_div_stat" >
            <h5 class="titre_stat">Nombre Box Premier Jardin : {if isset( $T_stat[1849])}{$T_stat[1849]}{else}0{/if}</h5>
        </div>
          <div class="un_div_stat" >
              <h5 class="titre_stat">Nombre total Box Insolite : {if isset( $T_stat[1850])}{$T_stat[1850]}{else}0{/if}</h5>
            - Standard : {if isset( $stat_insolite_standard)}{$stat_insolite_standard}{else}0{/if}<br/>
            - XXL : {if isset( $stat_insolite_xxl)}{$stat_insolite_xxl}{else}0{/if}<br/>
          </div>
          <div class="un_div_stat" >
              <h5 class="titre_stat">Nombre Box Aromes : {if isset( $T_stat[1851])}{$T_stat[1851]}{else}0{/if}</h5>
          </div>
          <div class="un_div_stat" >
              <h5 class="titre_stat">Nombre Box Sous ma serre : {if isset( $T_stat[2638])}{$T_stat[2638]}{else}0{/if}</h5>
          </div>
                {if $saison==-1}
                <form action="" method="POST">
                    <input name="action_traiter" id="action_traiter" type="submit" value="Traiter toutes les commandes *" /><br/>
                    (*) Cr&eacute;e une commande pour chaque box command&eacute;e dont le paiement &agrave; &eacute;t&eacute; accept&eacute; ou dont le statut commande est "Box &agrave; livrer prochainement".
                    {if isset($smarty.get.message) && $smarty.get.message=="traiter_toutes" }
                        <div style="color:green; font-weight: bold; font-size: 20px;">Toutes les box sont trait&eacute;es.
                    {/if}
                    {if isset($smarty.get.message) && $smarty.get.message=="traiter_une" }
                        <div style="color:green; font-weight: bold; font-size: 20px;">La box est trait&eacute;e.
                    {/if}
                </form>
                {/if}
            </div>
    </div>
    <table id="tableau_box">
        <tr><th colspan="9">Box</th><th colspan="3">Commande</th></tr>
         {foreach from=$T_lignes_box key=id_od item=T_ligne_box}
				
            {$deb_cust=$T_ligne_box['deb_cust']}
			<tr><td class="td_nom">{$T_ligne_box['product_name']}</td>
			
			<td><form action="" method="POST">
            
			<div class="input-container">
				<span class="input-label">Nom : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_nom" id="id_modif_nom">
				<input type="hidden" value="{$T_ligne_box['index_custo_nom']}" name="index_custo_nom" id="id_custo_nom">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust]}" value="{$T_ligne_box['new_datas'][$deb_cust]}" name="txt_nom" id="txt_nom">
			</div>

			<div class="input-container">
				<span class="input-label">Prénom : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_prenom" id="id_modif_prenom">
				<input type="hidden" value="{$T_ligne_box['index_custo_prenom']}" name="index_custo_prenom" id="id_custo_prenom">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+1]}" value="{$T_ligne_box['new_datas'][$deb_cust+1]}" name="txt_prenom" id="txt_prenom">
			</div>

			<div class="input-container">
				<span class="input-label">Adresse 1 : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_adresse_1" id="id_modif_adresse_1">
				<input type="hidden" value="{$T_ligne_box['index_custo_adresse_1']}" name="index_custo_adresse_1" id="id_custo_adresse_1">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+2]}" value="{$T_ligne_box['new_datas'][$deb_cust+2]}" name="txt_adresse_1" id="txt_adresse_1">
			</div>

			<div class="input-container">
				<span class="input-label">Adresse 2 : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_adresse_2_vraie" id="id_modif_adresse_2_vraie">
				<input type="hidden" value="{$T_ligne_box['index_custo_adresse_2_vraie']}" name="index_custo_adresse_2_vraie" id="id_custo_adresse_2_vraie">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+3]}" value="{$T_ligne_box['new_datas'][$deb_cust+3]}" name="txt_adresse_2_vraie" id="txt_adresse_2_vraie">
			</div>

			<div class="input-container">
				<span class="input-label">CP : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_code_postal" id="id_modif_code_postal">
				<input type="hidden" value="{$T_ligne_box['index_custo_code_postal']}" name="index_custo_code_postal" id="id_custo_code_postal">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+4]}" value="{$T_ligne_box['new_datas'][$deb_cust+4]}" name="txt_code_postal" id="txt_code_postal">
			</div>

			<div class="input-container">
				<span class="input-label">Ville : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_adresse_2" id="id_modif_adresse_2">
				<input type="hidden" value="{$T_ligne_box['index_custo_adresse_2']}" name="index_custo_adresse_2" id="id_custo_adresse_2">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+5]}" value="{$T_ligne_box['new_datas'][$deb_cust+5]}" name="txt_adresse_2" id="txt_adresse_2">
			</div>

			<div class="input-container">
				<span class="input-label">Email : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_cf" id="id_cf">
				<input type="hidden" value="{$T_ligne_box['index_custo_mail']}" name="index_custo_mail" id="id_custo_mail">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+6]}" value="{$T_ligne_box['new_datas'][$deb_cust+6]}" name="txt_email" id="txt_email">
			</div>

			<div class="input-container">
				<span class="input-label">Tél : </span>
				<input type="hidden" value="{$T_ligne_box['new_id_customization']}" name="id_modif_phone" id="id_modif_phone">
				<input type="hidden" value="{$T_ligne_box['index_custo_phone']}" name="index_custo_phone" id="id_custo_phone">
				<input type="text" title="{$T_ligne_box['new_datas'][$deb_cust+7]}" value="{$T_ligne_box['new_datas'][$deb_cust+7]}" name="txt_phone" id="txt_phone">
			</div>

            <br /><input type="submit" value="Modifier" /></form>
			</td>
                <td>{$T_ligne_box['date_add']}</td><td><a href="{$T_ligne_box['link']}">{$T_ligne_box['reference']}</a></td><td>{$T_ligne_box['etat_commande_lib']}</td>{if $saison==-1}<td>{if $T_ligne_box['paiement_ok']==true && $T_ligne_box['date_saison']==""}<a href="index.php?controller=AdminBox&d{if isset($saison)}&saison={$saison}{/if}&token={$smarty.get.token}&id_custo2={$id_od}">Traiter</a>{elseif $T_ligne_box['date_saison']!=""}d&eacute;ja trait&eacute;e{else}En attente de paiement{/if}</td>{/if}
                {if isset( $smarty.get.debug)}
                    {for $foo=0 to 8}

                    <td>{$T_ligne_box['new_datas'][$deb_cust+$foo]}</td>
                    {/for}
                    <td>{$deb_cust}</td>
                {/if}
             </tr>
         {/foreach}
    </table>
</div>
