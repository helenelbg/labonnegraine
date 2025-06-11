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

<div id="tab-orders"class="row" style="display: none;">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-top: 15px;">
                <a class="navbar-brand" href="#">{l s='Orders' mod='sonice_etiquetage'} <small class="current_session_name"></small></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="print_labels"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}printer.png" alt="listing"><br>{l s='Print label' mod='sonice_etiquetage'}</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="get_labels"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}barcode.png" alt="listing"><br>{l s='Generate label' mod='sonice_etiquetage'}</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li class="divider"></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="delete_labels"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}cross_32.png" alt="listing"><br>{l s='Delete label' mod='sonice_etiquetage'}</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Error div -->
    <div id="sne_error_orders" class="alert alert-danger" style="display: none;">
        {l s='An error occured.' mod='sonice_etiquetage'}<br><br>
		<code></code>
    </div>
    <!-- Warning div -->
    <div id="sne_warn_orders" class="conf warn" style="display: none;">
        {l s='Something unexpected occured.' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>
    <!-- Success div -->
    <div id="sne_conf_orders" class="alert alert-success" style="display: none;">
        {l s='Success !' mod='sonice_etiquetage'}<br>
        <code></code>
    </div>

    <table class="table order table_grid" style="border-spacing: 0;" id="table_orders">
        <thead>
            <tr class="nodrag nodrop active" style="height: 40px;">
                <th class="center"><input type="checkbox" id="one_checkbox_to_rule_them_all"></th>
                <th class="left">ID<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Customer' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Carrier' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">{l s='Delivery Address' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">Date<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
                <th class="left">Nature</th>
                <th class="left">TA</th>
                <th class="left">R1</th>
                <th class="left">R2</th>
                <th class="left">R3</th>
                <th class="left">D150</th>
                <th class="left">{l s='Label' mod='sonice_etiquetage'}<br><img src="{$sne_img|escape:'htmlall':'UTF-8'}down.gif" alt="down"><img src="{$sne_img|escape:'htmlall':'UTF-8'}up.gif" alt="up"></th>
            </tr>
            <tr class="nodrag nodrop filter active" style="height: 35px;">
                <td class="center">&nbsp;</td>
                <td class="left"><input type="text" name="sne_filters[id]" id="sne_filter_id" value="" style="width: 50px;"></td>
                <td class="left"><input type="text" name="sne_filters[customer]" value="" id="sne_filter_customer"></td>
                <td class="left">
                    <select id="sne_filter_carrier" name="sne_filters[carrier]">
                        <option></option>
                        {foreach $sne_carrier_list as $carrier}
                            <option>{$carrier|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </td>
                <td class="left"><input type="text" value="" id="sne_filter_address" ></td>
                <td class="left"><input type="text" value="" id="sne_filter_date" name="sne_filters[date]" class="datepicker"></td>
                <td class="left">&nbsp;</td>
                <td class="left">&nbsp;</td>
                <td class="left">&nbsp;</td>
                <td class="left"> </td>
                <td class="left"> </td>
                <td class="left"> </td>
                <td class="left">&nbsp;</td>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

    <select id="select_carrier_modify" style="display: none;">
        {if !empty($sne_carrier_modify)}
            {foreach $sne_carrier_modify as $carrier}
                {if $carrier->active}
                    <option value="{$carrier->id|escape:'htmlall':'UTF-8'}">{$carrier->name|escape:'htmlall':'UTF-8'}</option>
                {/if}
            {/foreach}
        {/if}
    </select>

    <div class="cleaner">&nbsp;</div>
</div>
