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

<br>
<fieldset id="sonice_etiquetage" style="{(!$ps15x && !$ps16x) ? 'width: 400px;' : ''}">
    
    <!-- URL -->
    <input type="hidden" id="sne_webservice_url" value="{$sne_webservice_url|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="sne_common_printserver" value="{$sne_common_printserver|escape:'htmlall':'UTF-8'}">
    
    <!-- DATA -->
    <input type="hidden" name="checkbox[]" value="{$data.id_order|escape:'htmlall':'UTF-8'}">

    {if isset($sne_config.legacy) && $sne_config.legacy}
        <!-- QZ -->
        <applet id="qz" code="qz.PrintApplet.class" archive="{$sne_module_url|escape:'htmlall':'UTF-8'}tools/applet/qz-print.jar" width="1" height="1">
            <param name="jnlp_href" value="{$sne_module_url|escape:'htmlall':'UTF-8'}tools/applet/qz-print_jnlp.jnlp">
            <param name="cache_option" value="plugin">
            <param name="disable_logging" value="false">
            <param name="initial_focus" value="false">
            <param name="codebase_lookup" value="false">
        </applet>
    {/if}

    <legend>
        <img src="{$sne_img|escape:'htmlall':'UTF-8'}barcode_16.png" alt="lorry_go">
        {l s='Label' mod='sonice_etiquetage'}
    </legend>
    
    {if $data.exists}
        <div class="font-green">
            {l s='The label for this order has already been created.' mod='sonice_etiquetage'}
        </div>
        <br>
        
        <div>
            <strong>{l s='Label' mod='sonice_etiquetage'} N&deg;</strong> {$data.info.parcel_number|escape:'htmlall':'UTF-8'}<br>
            <strong>{l s='Download' mod='sonice_etiquetage'} :</strong> <a href="{$data.info.pdfurl|escape:'htmlall':'UTF-8'}" target="_blank">{substr($data.info.pdfurl|escape:'htmlall':'UTF-8', 0, 64)}...</a><br>
            <strong>Date :</strong> {$data.info.date_add|escape:'htmlall':'UTF-8'}<br>
        </div>
    {else}
        <div class="font-red">
            {l s='The label for this order has not been created yet.' mod='sonice_etiquetage'}
        </div>
        <div class="font-green" style="display: none;">
            {l s='The label for this order has already been created.' mod='sonice_etiquetage'}
        </div>
        <br>
        
        <div style="display: none;" id="sne_show_label">
            <strong>{l s='Label' mod='sonice_etiquetage'} N&deg;</strong> <span class="sne_label_num"></span><br>
            <strong>{l s='Download' mod='sonice_etiquetage'} :</strong> <a href="" target="_blank"></a><br>
            <strong>Date :</strong> <span class="sne_label_date"></span><br>
        </div>
        
        <button class="button" id="sne_create_label">{l s='Generate Label' mod='sonice_etiquetage'}</button> <img src="{$sne_img|escape:'htmlall':'UTF-8'}loader.gif"  width="16px" id="sne_loader" style="display: none; vertical-align: middle;" alt="loader">
        <br>
        <br>
        
        <div class="conf confirm" id="sne_conf" style="display: none;">
            {l s='The label has been created successfully.' mod='sonice_etiquetage'}
        </div>
        <div class="conf error" id="sne_error" style="display: none;">
            {l s='An error occured during the process.' mod='sonice_etiquetage'}<br>
            <br>
            ID : <span></span><br>
            Message : <span></span><br>
            <code></code>
        </div>
    {/if}
</fieldset>