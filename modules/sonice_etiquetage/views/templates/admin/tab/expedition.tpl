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

<div id="tab-exp" style="display: none;">
    
    <div class="barcode-div">
        <form name="order_send">
            <span>{l s='Scan outgoing packages' mod='sonice_etiquetage'} :</span> <img src="{$sne_img|escape:'htmlall':'UTF-8'}douchette_1.png" class="sne_douchette" alt="douchette"><input type="text" placeholder="{l s='Parcel Number' mod='sonice_etiquetage'}" class="barcode-input">
        </form>
    </div>
        
    <div class="clean">&nbsp;</div>
        
    <!-- Error div -->
    <div id="sne_error_exp" class="conf error" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_exp" class="conf warn" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div -->
    <div id="sne_conf_exp" class="conf confirm" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    
    <table class="table order table_grid" style="border-spacing: 0;" id="table_exp">
        <thead>
            <tr class="nodrag nodrop" style="height: 40px;">
                <th class="center" width="20px"><input type="checkbox" id="one_checkbox_to_rule_them_all_exp"></th>
                <th class="left">ID</th>
                <th class="left">{l s='Label' mod='sonice_etiquetage'}</th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}</th>
                <th class="left">Date</th>
                <th class="left">{l s='Delivery Address' mod='sonice_etiquetage'}</th>
                <th class="left">{l s='Weight' mod='sonice_etiquetage'}</th>
                <th class="center">Action</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
                            
    <div class="cleaner">&nbsp;</div>
    
    <button class="button" id="send_all_exp">{l s='Send' mod='sonice_etiquetage'}</button>
    <span class="float-right nb_awaiting_package" style="font-size: 16px;"><b></b> {l s='Awaiting package(s)' mod='sonice_etiquetage'}</span>
    
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>
    <hr>
    <div class="cleaner">&nbsp;</div>
    
    <h3>{l s='Outgoing packages' mod='sonice_etiquetage'}</h3>
    <table class="table order table_grid" style="border-spacing: 0;" id="table_exp_done">
        <thead>
            <tr class="nodrag nodrop" style="height: 40px;">
                <th class="left">ID</th>
                <th class="left">{l s='Label' mod='sonice_etiquetage'}</th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}</th>
                <th class="left">Date</th>
                <th class="left">{l s='Delivery Address' mod='sonice_etiquetage'}</th>
                <th class="left">{l s='Weight' mod='sonice_etiquetage'}</th>
                <th class="center"></th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>