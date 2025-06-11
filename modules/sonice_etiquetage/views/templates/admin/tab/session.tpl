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

<div id="tab-session">
    <div class="cleaner">&nbsp;</div>
    
    <!-- Static toolbar / PS1.5 -->
    <div class="toolbarBox">
        <ul class="cc_button">
            <li>
                <a class="toolbar_btn" id="create_session">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}calendar_add.png" alt="listing"></span>
                    <div>{l s='Create session' mod='sonice_etiquetage'}</div>
                </a>
                <a class="toolbar_btn" id="delete_session">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}calendar_delete.png" alt="listing"></span>
                    <div>{l s='Delete session' mod='sonice_etiquetage'}</div>
                </a>
                <a class="toolbar_btn" id="fusion_session">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}calendar_link.png" alt="listing"></span>
                    <div>{l s='Fusion session' mod='sonice_etiquetage'}</div>
                </a>
            </li>
        </ul>
        <div class="pageTitle">
            <h3>
                <span class="current_obj">Session</span>
                <span id="session_selection" {*class="container" {if !count($sne_session_list)}style="display: none;"{/if*}>
                    <select id="session_select">
                        <option value="0"></option>
                        {foreach $sne_session_list as $session}
                            <option value="{$session.id_session|escape:'htmlall':'UTF-8'}">{if $session.alias}{$session.alias|escape:'htmlall':'UTF-8'} {*-*} {/if}{*From [{substr($session.from, 0, 10)}] to [{substr($session.to, 0, 10)}]*}</option>
                        {/foreach}
                    </select>
                </span>
                <input type="text" class="session_name" style="display: none;" placeholder="{l s='Session name' mod='sonice_etiquetage'}">
            </h3>
        </div>
    </div>
            
    <!-- Error div -->
    <div id="sne_error_session" class="conf error" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_session" class="conf warn" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div  -->
    <div id="sne_conf_session" class="conf confirm" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!--
    <div id="session_selection" class="container" {if !count($sne_session_list)}style="display: none;"{/if}>
        <select id="session_select">
            <option value="0"></option>
            {foreach $sne_session_list as $session}
                <option value="{$session.id_session|escape:'htmlall':'UTF-8'}">{if $session.alias}{$session.alias|escape:'htmlall':'UTF-8'} {*-*} {/if}{*From [{substr($session.from, 0, 10)}] to [{substr($session.to, 0, 10)}]*}</option>
            {/foreach}
        </select>
    </div>
    -->
    <div id="session_fusion" class="container" style="display: none;">
        {l s='This session' mod='sonice_etiquetage'}
        <select id="session_select_fusion">
            <option value=""></option>
            {foreach $sne_session_list as $session}
                <option value="{$session.id_session|escape:'htmlall':'UTF-8'}">{if $session.alias}{$session.alias|escape:'htmlall':'UTF-8'} {*-*} {/if}{*From [{substr($session.from, 0, 10)}] to [{substr($session.to, 0, 10)}]*}</option>
            {/foreach}
        </select>
        {l s='will be integrated into the current session' mod='sonice_etiquetage'} <strong></strong>
        <input type="hidden" id="session_fusion_confirm" value="{l s='Are you sure that you want to fusion these sessions ?' mod='sonice_etiquetage'}">
    </div>
        
    <div id="session_creation" class="container" {if count($sne_session_list)}style="display: none;"{/if}>
        <div>{l s='Session name' mod='sonice_etiquetage'} <input type="text" id="new_sesseion_name" value="{$sne_default_session_name|escape:'htmlall':'UTF-8'}" placeholder="{$sne_default_session_name|escape:'htmlall':'UTF-8'}"> <button class="button" id="new_sesseion_create">OK</button></div>
    </div>
                
</div>