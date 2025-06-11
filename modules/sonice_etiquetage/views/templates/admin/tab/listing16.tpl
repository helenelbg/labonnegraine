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

<div id="tab-listing" class="row" style="display: none;">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-top: 15px;">
                <a class="navbar-brand" href="#">{l s='Available orders' mod='sonice_etiquetage'}</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="add_package"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}package_add.png" alt="listing"><br>{l s='Add to session' mod='sonice_etiquetage'}</a></li>
                </ul>
            </div>
        </div>
    </nav>

    {*
     * AVAILABLE ORDERS
     *}
     
    <!-- Error div -->
    <div id="sne_error_listing2" class="alert alert-danger" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_listing2" class="alert alert-warning" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div -->
    <div id="sne_conf_listing2" class="alert alert-success" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>

    {if $sne_labels_available.pages > 1}
        <div class="float-right page">
            <span class="total_orders">{$sne_labels_available.total|escape:'htmlall':'UTF-8'}</span> {l s='Result' mod='sonice_etiquetage'}(s) |
            {l s='Display' mod='sonice_etiquetage'}  <select class="nb_display">
                                                        <option value="20">20</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="300">300</option>
                                                    </select> |
            <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-prev2.gif" class="page_first" alt="last" style="display: none;"> <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-prev.gif" class="page_previous" alt="next" style="display: none;">
            Page <strong><span class="current_page">1</span></strong> / <span class="total_page">{$sne_labels_available.pages|escape:'htmlall':'UTF-8'}</span>
            <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-next.gif" class="page_next" alt="next"> <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-next2.gif" class="page_last" alt="last">
            <div class="cleaner">&nbsp;</div>
        </div>
    {/if}
    
    <table class="table table_grid table-striped" style="border-spacing: 0;" id="sne_labels_availables">
        <thead>
            <tr class="nodrag nodrop active">
                <th class="center"><input type="checkbox" id="one_checkbox_to_rule_them_all_listing"></th>
                <th class="left">ID<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                {if $ps15x}<th class="left">{l s='Reference' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>{/if}
                <th class="left" {if !$ps15x}style="width: 10%;"{/if}>Date<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Address' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Zip Code' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='City' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Country' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Quantity' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Total Weight' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
            </tr>
        </thead>
        <tbody>
            {if $sne_labels_available && isset($sne_labels_available.orders) && is_array($sne_labels_available.orders) && count($sne_labels_available.orders)}
                {assign var="count_orders" value=0}
                
                {foreach $sne_labels_available.orders as $order}
                    
                    {$count_orders = $count_orders + 1}

                    <tr rel="{$order.id_order|escape:'htmlall':'UTF-8'}" {if $count_orders > 20}style="display: none;"{/if}>
                        <td style="text-align: center;"><input type="checkbox" class="sne_checkbox" name="listing_checkbox[]" value="{$order.id_order|escape:'htmlall':'UTF-8'}"></td>
                        <td class="id_order"><a href="?tab=AdminOrders&id_order={$order.id_order|escape:'htmlall':'UTF-8'}&vieworder&token={$sne_token_order|escape:'htmlall':'UTF-8'}" target="_blank">{$order.id_order|escape:'htmlall':'UTF-8'}</a></td>
                        {if $ps15x}<td class="reference">{$order.reference|escape:'htmlall':'UTF-8'}</td>{/if}
                        <td class="date">{$order.date|escape:'htmlall':'UTF-8'}</td>
                        <td class="customer">{$order.customer_firstname|escape:'htmlall':'UTF-8'} {$order.customer_lastname|escape:'htmlall':'UTF-8'}</td>
                        <td class="address">{$order.address_address1|escape:'htmlall':'UTF-8'}</td>
                        <td class="zipcode">{$order.address_postcode|escape:'htmlall':'UTF-8'}</td>
                        <td class="city">{$order.address_city|escape:'htmlall':'UTF-8'}</td>
                        <td class="country">{$order.address_country|escape:'htmlall':'UTF-8'}</td>
                        <td class="qty">{$order.qty|escape:'htmlall':'UTF-8'}</td>
                        <td class="weight">{$order.weight|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>

    {if $sne_labels_available.pages > 1}
        <div class="cleaner">&nbsp;</div>
        <div class="float-right page">
            <span class="total_orders">{$sne_labels_available.total|escape:'htmlall':'UTF-8'}</span> {l s='Result' mod='sonice_etiquetage'}(s) |
            {l s='Display' mod='sonice_etiquetage'}  <select class="nb_display">
                                                        <option value="20">20</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="300">300</option>
                                                    </select> |
            <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-prev2.gif" class="page_first" alt="last" style="display: none;"> <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-prev.gif" class="page_previous" alt="next" style="display: none;">
            Page <strong><span class="current_page">1</span></strong> / <span class="total_page">{$sne_labels_available.pages|escape:'htmlall':'UTF-8'}</span>
            <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-next.gif" class="page_next" alt="next"> <img src="{$sne_img|escape:'htmlall':'UTF-8'}list-next2.gif" class="page_last" alt="last">
            <div class="cleaner">&nbsp;</div>
        </div>
    {/if}
        
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>

    <hr class="sne_table_split">
    
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>

    {*
     * SESSION ORDERS
     *}

    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-top: 15px;">
                <a class="navbar-brand" href="#">{l s='Current session orders' mod='sonice_etiquetage'} <small class="current_session_name"></small></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="generate_invoices"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}receipt_invoice.png" alt="Invoices"><br>{l s='Generate invoices' mod='sonice_etiquetage'}</a></li>
                    <li id="generate_delivery_slips"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}page_white_go.png" alt="Delivery slips"><br>{l s='Generate delivery slips' mod='sonice_etiquetage'}</a></li>
                    <li id="generate_listing"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}mark_to_download.png" alt="Listing"><br>{l s='Generate listing' mod='sonice_etiquetage'}</a></li>
                    <li id="delete_package"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}package_delete.png" alt="Remove from session"><br>{l s='Remove from session' mod='sonice_etiquetage'}</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Error div -->
    <div id="sne_error_listing" class="alert alert-danger" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_listing" class="alert alert-warning" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div -->
    <div id="sne_conf_listing" class="alert alert-success" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    
    <table class="table table_grid table-striped" style="border-spacing: 0;" id="sne_labels_session">
        <thead>
            <tr class="nodrag nodrop active">
                <th class="center"><input type="checkbox" id="one_checkbox_to_rule_them_all_session"></th>
                <th class="left">ID<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                {if $ps15x}<th class="left">{l s='Reference' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>{/if}
                <th class="left" {if !$ps15x}style="width: 10%;"{/if}>Date<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Address' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Zip Code' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='City' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Country' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Quantity' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Total Weight' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
            
    <div class="cleaner">&nbsp;</div>
</div>