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

{if $sne_labels_available}
    {foreach $sne_labels_available as $order}
        <tr rel="{$order.id_order|escape:'htmlall':'UTF-8'}" class="listing_order">
            <td style="text-align: center;"><input type="checkbox" class="sne_checkbox" name="listing_session_checkbox[]" value="{$order.id_order|escape:'htmlall':'UTF-8'}"></td>
            <td class="id_order"><a href="?tab=AdminOrders&id_order={$order.id_order|escape:'htmlall':'UTF-8'}&vieworder&token=sne_token_order" target="_blank">{$order.id_order|escape:'htmlall':'UTF-8'}</a></td>
            {if $ps15x}<td class="reference">{$order.reference|escape:'htmlall':'UTF-8'}</td>{/if}
            <td class="date">{$order.date|escape:'htmlall':'UTF-8'}</td>
            <td class="customer">{$order.customer_firstname|escape:'htmlall':'UTF-8'} {$order.customer_lastname|escape:'htmlall':'UTF-8'}</td>
            <td class="address" rel="{$order.address_id|escape:'htmlall':'UTF-8'}"><input type="text" value="{$order.address_address1|escape:'htmlall':'UTF-8'}"></td>
            <td class="zipcode" rel="{$order.address_id|escape:'htmlall':'UTF-8'}"><input type="text" value="{$order.address_postcode|escape:'htmlall':'UTF-8'}"></td>
            <td class="city" rel="{$order.address_id|escape:'htmlall':'UTF-8'}"><input type="text" value="{$order.address_city|escape:'htmlall':'UTF-8'}"></td>
            <td class="country" rel="{$order.address_id|escape:'htmlall':'UTF-8'}"><input type="text" value="{$order.address_country|escape:'htmlall':'UTF-8'}"></td>
            <td class="qty">{$order.qty|escape:'htmlall':'UTF-8'}</td>
            <td class="weight"><input type="text" value="{$order.weight|escape:'htmlall':'UTF-8'}"></td>
            <td class="edit">
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}bullet_edit.png" alt="edit">
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}tick2.png" alt="tick" style="display: none;">
            </td>
        </tr>
        <tr class="product_list" rel="{$order.id_order|escape:'htmlall':'UTF-8'}">
            <td colspan="{if $ps15x}3{else}4{/if}">&nbsp;</td>
            <td colspan="{if $ps15x}8{else}7{/if}">
                <table class="table table-condensed" style="border-spacing: 0; width: 100%;">
                    <tr style="font-weight: bold;">
                        <td>ID</td>
                        {if $ps15x}<td>{l s='Reference' mod='sonice_etiquetage'}</td>{/if}
                        <td {if !$ps15x}style=" width: 90%;"{/if}>{if $ps15x}{l s='Product name' mod='sonice_etiquetage'}{else}Nom du produit{/if}</td>
                        <td>{if $ps15x}{l s='Qty' mod='sonice_etiquetage'}{else}Qt&eacute;{/if}</td>
                        <td>{if $ps15x}{l s='Weight' mod='sonice_etiquetage'}{else}Poids{/if}</td>
                    </tr>
                    {foreach $order.products as $product}
                        <tr rel="{$product.id_order_detail|escape:'htmlall':'UTF-8'}">
                            <td class="product_id">{$product.product_id|escape:'htmlall':'UTF-8'}{if $product.product_attribute_id}/{$product.product_attribute_id|escape:'htmlall':'UTF-8'}{/if}</td>
                            {if $ps15x}<td class="reference">{$product.reference|escape:'htmlall':'UTF-8'}</td>{/if}
                            <td class="product_name">{$product.product_name|escape:'htmlall':'UTF-8'}</td>
                            <td class="product_quantity">{$product.product_quantity|escape:'htmlall':'UTF-8'}</td>
                            <td class="weight_product">{substr($product.weight|escape:'htmlall':'UTF-8', 0, 4)}</td>
                        </tr>
                    {/foreach}
                </table>
            </td>
        </tr>
    {/foreach}
{/if}