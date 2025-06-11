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

<div id="tab-listing" style="display: none;">
    
    
    <div class="toolbarBox">
        <ul class="cc_button">
            <li>
                <a class="toolbar_btn" id="add_package">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}package_add.png" alt="listing"></span>
                    <div>{l s='Add to session' mod='sonice_etiquetage'}</div>
                </a>
            </li>
        </ul>
        <div class="pageTitle">
            <h3>
                <span class="current_obj">{l s='Available orders' mod='sonice_etiquetage'}</span>
            </h3>
        </div>
    </div>
            
            
            
    {*
     * AVAILABLE ORDERS
     *}
     
    <!-- Error div -->
    <div id="sne_error_listing2" class="conf error" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_listing2" class="conf warn" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div -->
    <div id="sne_conf_listing2" class="conf confirm" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    
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
    
    <table class="table table_grid" style="border-spacing: 0;" id="sne_labels_availables">
        <thead>
            <tr class="nodrag nodrop">
                <th class="center"><input type="checkbox" id="one_checkbox_to_rule_them_all_listing"></th>
                <th class="left">ID<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                {if $ps15x}<th class="left">{l s='Reference' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>{/if}
                <th class="left" {if !$ps15x}style="width: 10%;"{/if}>Date<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Address' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Zip Code' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='City' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Quantity' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Weight' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
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
                        <td class="qty">{$order.qty|escape:'htmlall':'UTF-8'}</td>
                        <td class="weight">{$order.weight|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/foreach}
            {/if}
        </tbody>
    </table>
    
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
        
    <div class="cleaner">&nbsp;</div>
    <div class="cleaner">&nbsp;</div>
    
    <hr class="sne_table_split">

    <div class="toolbarBox">
        <ul class="cc_button">
            <li>
                <a class="toolbar_btn" id="generate_delivery_slips">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}receipt_invoice.png" id="download_listing" alt="listing"></span>
                    <div>{l s='Generate delivery slips' mod='sonice_etiquetage'}</div>
                </a>
                <a class="toolbar_btn" id="generate_listing">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}mark_to_download.png" id="download_listing" alt="listing"></span>
                    <div>{l s='Generate listing' mod='sonice_etiquetage'}</div>
                </a>
                <a class="toolbar_btn" id="delete_package">
                    <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}package_delete.png" alt="listing"></span>
                    <div>{l s='Remove from session' mod='sonice_etiquetage'}</div>
                </a>
            </li>
        </ul>
        <div class="pageTitle">
            <h3>
                <span class="current_obj">{l s='Current session orders' mod='sonice_etiquetage'} <small class="current_session_name"></small> </span>
            </h3>
        </div>
    </div>
            
            
    
    {*
     * SESSION ORDERS
     *}
     
    <!-- Error div -->
    <div id="sne_error_listing" class="conf error" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_listing" class="conf warn" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div -->
    <div id="sne_conf_listing" class="conf confirm" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    
    <table class="table table_grid" style="border-spacing: 0;" id="sne_labels_session">
        <thead>
            <tr class="nodrag nodrop">
                <th class="center"><input type="checkbox" id="one_checkbox_to_rule_them_all_session"></th>
                <th class="left">ID<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                {if $ps15x}<th class="left">{l s='Reference' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>{/if}
                <th class="left" {if !$ps15x}style="width: 10%;"{/if}>Date<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Address' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Zip Code' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='City' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Quantity' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Weight' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
            
    <div class="cleaner">&nbsp;</div>
</div>