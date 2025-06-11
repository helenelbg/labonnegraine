{* NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * ...........................................................................
 *
 * @package    SoNice
 * @copyright  Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @author     Debusschere A.
 * @license    Commercial license
 *}

<div id="conf-carrier_mapping" class="panel form-horizontal" style="display: none;">
    <h2>{l s='Carriers Mapping' mod='sonice_etiquetage'}</h2>
    <div class="cleaner">&nbsp;</div>
	
	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='More information in the online documentation' mod='sonice_etiquetage'} :<br>
				<a href="http://documentation.common-services.com/sonice_etiquetage/transporteurs/" target="_blank">http://documentation.common-services.com/sonice_etiquetage/transporteurs/</a>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3" rel="selected_carrier">{l s='Selected carriers' mod='sonice_etiquetage'}</label>
		<div class="col-lg-9 margin-form">
			<br>
		</div>
	</div>

    {foreach $sne_carriers.filtered as $carrier}
        <div class="form-group">
            <label class="control-label col-lg-3">{$carrier['name']|escape:'htmlall':'UTF-8'} <img src="{$sne_img|escape:'htmlall':'UTF-8'}next.png" alt="next"></label>
            <div class="col-lg-9 margin-form">
                {if $sne_carrier_mapping && array_key_exists($carrier['id_carrier'], $sne_carrier_mapping)}
                    {assign var="mapping" value=$sne_carrier_mapping[$carrier['id_carrier']]}
                {else}
                    {assign var="mapping" value="Nothing"}
                {/if}

                <select name="carrier_mapping[{$carrier['id_carrier']|escape:'htmlall':'UTF-8'}]">
					<option style="color: blue;" disabled>Offre France</option>
					<option value="COLD" {if "COLD" == $mapping}selected{/if}>(COLD 9L) Colissimo Domicile sans signature (France)</option>
					<option value="COL" {if "COL" == $mapping}selected{/if}>(COL 9V) Colissimo Domicile avec signature (France)</option>

					<option style="color: blue;" disabled>Offre France et Europe</option>
					<option value="DOM" {if "DOM" == $mapping}selected{/if}>(DOM / BOM 6A 6Q CA) Colissimo Domicile sans signature (France, Belgique, Suisse)</option>
					<option value="DOS" {if "DOS" == $mapping}selected{/if}>(DOS / BOS 6C CB) Colissimo Domicile avec signature (France, Belgique, Suisse, Allemagne, Pays-Bas, Espagne, Grande-Bretagne, Luxembourg, Portugal, Autriche, République Tchèque, Hongrie, Slovaquie, Slovénie, Lituanie, Lettonie, Estonie)</option>
					<option value="BPR" {if "BPR" == $mapping}selected{/if}>(BPR / BDP 6H 6R CI) Colissimo Bureau de poste (France, Belgique)</option>
					<option value="A2P" {if "A2P" == $mapping}selected{/if}>(A2P / CMT 6M 6W CM) Colissimo Relais commerçant, PickUp Station (France, Belgique, Allemagne, Pays-Bas, Espagne, Grande-Bretagne, Luxembourg, Portugal, Autriche, Lituanie, Lettonie, Estonie)</option>

					<option style="color: blue;" disabled>Offre Outre-Mer</option>
					<option value="COM" {if "COM" == $mapping}selected{/if}>(COM 8Q) Colissimo Domicile sans signature (Outre-Mer)</option>
					<option value="CDS" {if "CDS" == $mapping}selected{/if}>(CDS 7Q) Colissimo Domicile avec signature (Outre-Mer)</option>

					<option style="color: blue;" disabled>Offre International</option>
					<option value="COLI" {if "COLI" == $mapping}selected{/if}>(COLI CP EY) Colissimo Expert International avec signature</option>
                </select>
            </div>
        </div>
    {/foreach}

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>