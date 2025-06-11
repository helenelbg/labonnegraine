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

<div id="tab-session" class="row">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-top: 15px;">
                <a class="navbar-brand" href="#">Session</a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav" style="margin-top: 10px;">
                    <li>
                        <span id="session_selection">
                            <select id="session_select">
                                <option value="0"></option>
                                {foreach $sne_session_list as $session}
                                    <option value="{$session.id_session|escape:'htmlall':'UTF-8'}">{if $session.alias}{$session.alias|escape:'htmlall':'UTF-8'}{/if}</option>
                                {/foreach}
                            </select>
                        </span>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="create_session"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}calendar_add.png" alt="listing"><br>{l s='Create session' mod='sonice_etiquetage'}</a></li>
                    <li id="delete_session"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}calendar_delete.png" alt="listing"><br>{l s='Delete session' mod='sonice_etiquetage'}</a></li>
                    <li id="fusion_session"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}calendar_link.png" alt="listing"><br>{l s='Fusion session' mod='sonice_etiquetage'}</a></li>
                </ul>
                <div class="navbar-form navbar-right" role="search" style="margin-right: 10px;">
                    <div class="form-group">
                        <input type="text" class="form-control session_name" placeholder="{l s='Session name' mod='sonice_etiquetage'}" style="margin-top: 15px;">
                    </div>
                </div>
            </div>
        </div>
    </nav>
            
    <!-- Error div -->
    <div id="sne_error_session" class="alert alert-danger" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_session" class="alert alert-warning" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div  -->
    <div id="sne_conf_session" class="alert alert-success" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    
    <div id="session_fusion" class="col-lg-12 well form-inline" style="display: none;">
        {l s='This session' mod='sonice_etiquetage'}
        <select id="session_select_fusion">
            <option value=""></option>
            {foreach $sne_session_list as $session}
                <option value="{$session.id_session|escape:'htmlall':'UTF-8'}">{if $session.alias}{$session.alias|escape:'htmlall':'UTF-8'}{/if}</option>
            {/foreach}
        </select>
        {l s='will be integrated into the current session' mod='sonice_etiquetage'} <strong></strong>
        <input type="hidden" id="session_fusion_confirm" value="{l s='Are you sure that you want to fusion these sessions ?' mod='sonice_etiquetage'}">
    </div>
    
    <div id="session_creation" class="col-lg-12 well form-inline" {if count($sne_session_list)}style="display: none;"{/if}>
        <div>{l s='Session name' mod='sonice_etiquetage'} <input type="text" id="new_sesseion_name" value="{$sne_default_session_name|escape:'htmlall':'UTF-8'}" placeholder="{$sne_default_session_name|escape:'htmlall':'UTF-8'}"> <button class="button btn" id="new_sesseion_create">OK</button></div>
    </div>
</div>