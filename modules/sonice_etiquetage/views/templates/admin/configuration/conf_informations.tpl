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


<div id="conf-informations" class="panel form-horizontal" style="display: none;">
    <h2 style="margin-top: 1px;">{l s='Configuration Check' mod='sonice_etiquetage'}</h2>
	<div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label class="control-label col-lg-3">So Colissimo Service Etiquetage</label>
        <div class="col-lg-9 margin-form">
            {if !$sne_info.module_info_ok}
                {foreach from=$sne_info.module_infos item=module_info}
                    <div class="{$module_info.level|escape:'htmlall':'UTF-8'}">
                        {$module_info.message|escape:'htmlall':'UTF-8'}
                    </div>
                {/foreach}
            {else}
                <div class="conf confirm alert alert-success">
                    {l s='Module configuration and integrity check passed successfully' mod='sonice_etiquetage'}
                </div>
            {/if}        
        </div>
    </div>
        
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='PHP Settings' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            {if !$sne_info.php_info_ok}
                {foreach from=$sne_info.php_infos item=php_info}
                    <div class="{$php_info.level|escape:'htmlall':'UTF-8'}">
                        {$php_info.message|escape:'htmlall':'UTF-8'}
                        {if isset($php_info.link)}<br>{l s='More informations' mod='sonice_etiquetage'} : <a href="{$php_info.link|escape:'htmlall':'UTF-8'}" target="_blank">{$php_info.link|escape:'htmlall':'UTF-8'}</a>{/if}
                    </div>
                {/foreach}
            {else}
                <div class="conf confirm alert alert-success">
                    {l s='Module configuration and integrity check passed successfully' mod='sonice_etiquetage'}
                </div>
            {/if}        
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Prestashop Settings' mod='sonice_etiquetage'}</label>
        <div class="col-lg-9 margin-form">
            {if !$sne_info.prestashop_info_ok}
                {foreach from=$sne_info.prestashop_infos item=prestashop_info}
                    <div class="{$prestashop_info.level|escape:'htmlall':'UTF-8'}">
                        {$prestashop_info.message|escape:'htmlall':'UTF-8'}
                    </div>
                {/foreach}
            {else}
                <div class="conf confirm alert alert-success">
                    {l s='Module configuration and integrity check passed successfully' mod='sonice_etiquetage'}
                </div>
            {/if}        
        </div>
    </div>

	<div class="form-group">
		<label class="control-label col-lg-3">&nbsp;</label>
		<div class="margin-form col-lg-9">
			<p>
				<button type="button" id="phpinfo_button" class="btn btn-default button">PHP Info</button>&nbsp;&nbsp;&nbsp;
				<button type="button" id="psinfo_button" class="btn btn-default button">PrestaShop Info</button>
			</p>
			<div id="phpinfo" style="display: none;">{*$sne_info.phpinfo_str|escape:'quotes':'UTF-8'*}</div>
			<div id="psinfo" style="display: none;">{*$sne_info.psinfo_str|escape:'quotes':'UTF-8'*}{*$sne_info.dbinfo_str|escape:'quotes':'UTF-8'*}</div>
		</div>
	</div>
</div>