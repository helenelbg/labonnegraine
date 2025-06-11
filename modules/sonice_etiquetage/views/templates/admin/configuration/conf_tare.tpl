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

<div id="conf-tare" class="panel form-horizontal" style="display: none;">
    <h2>{l s='Packaging tare weight' mod='sonice_etiquetage'}</h2>
    <div class="cleaner">&nbsp;</div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="col-lg-9 margin-form">
			<div class="{$alert_class.info|escape:'htmlall':'UTF-8'}">
				{l s='This tool allows you to add package box weight to your order weight.' mod='sonice_etiquetage'}
			</div>
		</div>
	</div>

    <div class="form-group">
        <div id="tare_model" class="new_weight">
            <label class="control-label col-lg-3">&nbsp;</label>
            <div class="col-lg-9">
                {l s='From' mod='sonice_etiquetage'} <input type="text" name="tare[0][from]" placeholder="{l s='Include' mod='sonice_etiquetage'}" size="5" disabled> &nbsp;{$sne_weight_unit|escape:'htmlall':'UTF-8'} &nbsp;
                {l s='to' mod='sonice_etiquetage'} <input type="text" name="tare[0][to]" placeholder="{l s='Exclude' mod='sonice_etiquetage'}" size="5" disabled> {$sne_weight_unit|escape:'htmlall':'UTF-8'}
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}next.png" class="arrow-next" alt="next"><input class="" name="tare[0][weight]" type="text" size="5" disabled> {$sne_weight_unit|escape:'htmlall':'UTF-8'}
                <span class="add-weight addnewweight"><img src="{$sne_img|escape:'htmlall':'UTF-8'}plus.png" alt="add" /></span>
                <span class="remove-weight removenewweight" style="display: none;"><img src="{$sne_img|escape:'htmlall':'UTF-8'}minus.png" alt="add" /></span>
            </div>
            <div class="cleaner">&nbsp;</div>
        </div>
            
        {if is_array($sne_tare_list) && count($sne_tare_list)}
            {foreach $sne_tare_list as $key => $tare}
                {if $sne_weight_unit == 'g'}
                    {$tare.from = $tare.from * 1000|escape:'htmlall':'UTF-8'}
                    {$tare.to = $tare.to * 1000|escape:'htmlall':'UTF-8'}
                    {$tare.weight = $tare.weight * 1000|escape:'htmlall':'UTF-8'}
                {/if}
                <div class="new_weight">
                    <label class="control-label col-lg-3">&nbsp;</label>
                    <div class="col-lg-9">
                        {l s='From' mod='sonice_etiquetage'} <input type="text" name="tare[{$key|escape:'htmlall':'UTF-8'}][from]" placeholder="{l s='Include' mod='sonice_etiquetage'}" value="{$tare.from|escape:'htmlall':'UTF-8'}" size="5"> &nbsp;{$sne_weight_unit|escape:'htmlall':'UTF-8'} &nbsp;
                        {l s='to' mod='sonice_etiquetage'} <input type="text" name="tare[{$key|escape:'htmlall':'UTF-8'}][to]" value="{$tare.to|escape:'htmlall':'UTF-8'}" placeholder="{l s='Exclude' mod='sonice_etiquetage'}" size="5"> {$sne_weight_unit|escape:'htmlall':'UTF-8'}
                        <img src="{$sne_img|escape:'htmlall':'UTF-8'}next.png" class="arrow-next" alt="next"><input class="" name="tare[{$key|escape:'htmlall':'UTF-8'}][weight]" value="{$tare.weight|escape:'htmlall':'UTF-8'}" type="text" size="5"> {$sne_weight_unit|escape:'htmlall':'UTF-8'}
                        <span class="remove-weight removenewweight"><img src="{$sne_img|escape:'htmlall':'UTF-8'}minus.png" alt="add" /></span>
                    </div>
                    <div class="cleaner">&nbsp;</div>
                </div>
            {/foreach}
        {/if}
    </div>

	{include file="$sne_module_path/views/templates/admin/configuration/validate.tpl"}
</div>