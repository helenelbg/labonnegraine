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


{if $sne_orders && $sne_orders.orders}
    {foreach $sne_orders.orders as $order}

        {assign var="label_exists" value=array_key_exists($order.id_order, $sne_labels)}

        <tr rel="{$order.id_order|escape:'htmlall':'UTF-8'}" {if $label_exists}class="{if (isset($sne_ps16x) && $sne_ps16x)}success{else}validated_row{/if}"{/if}>
            <td style="text-align: center;">
                <input type="checkbox" class="sne_checkbox" name="checkbox[]" value="{$order.id_order|escape:'htmlall':'UTF-8'}" class="sne_checkbox" {*if $label_exists}disabled readonly{/if*}>
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}small-loader.gif" alt="loader" style="display: none;">
            </td>
            <td class="id_order"><a href="?tab=AdminOrders&id_order={$order.id_order|escape:'htmlall':'UTF-8'}&vieworder&token=sne_token_order" target="_blank">{$order.id_order|escape:'htmlall':'UTF-8'}</a></td>
            <td class="customer" rel="{$order.id_customer|escape:'htmlall':'UTF-8'}">{$order.customer_firstname|escape:'htmlall':'UTF-8'} {$order.customer_lastname|escape:'htmlall':'UTF-8'}</td>
            <td class="carrier{if $order.carrier_allow_modify} can_modify{/if}" rel="{$order.id_carrier|escape:'htmlall':'UTF-8'}"><span>{$order.carrier_name|escape:'htmlall':'UTF-8'}</span>{if $order.carrier_allow_modify} <img src="{$sne_img|escape:'htmlall':'UTF-8'}bullet_edit.png" class="carrier_modify" alt="edit">{/if}</td>
            <td class="address" rel="{$order.id_address_delivery|escape:'htmlall':'UTF-8'}">
                <strong>{$order.address_alias|escape:'htmlall':'UTF-8'}</strong><br>
                {$order.address_address1|escape:'htmlall':'UTF-8'}<br>
                {$order.address_postcode|escape:'htmlall':'UTF-8'} {$order.address_city|escape:'htmlall':'UTF-8'}
            </td>
            <td class="date">{$order.date_add|escape:'htmlall':'UTF-8'}</td>
            <td class="nature">
                <select name="nature[{$order.id_order|escape:'htmlall':'UTF-8'}]">
                    <option value="3" selected>Envoi commercial</option>
                    <option value="2">Echantillon commercial</option>
                    <option value="1">Cadeau</option>
                    <option value="4">Document</option>
                </select>
            </td>
            <td class="ta" style="text-align: center;">
				<input type="checkbox" name="data[{$order.id_order|escape:'htmlall':'UTF-8'}][ta]" value="{$order.ta|escape:'htmlall':'UTF-8'}">
				{*{if !$ps15x}<br>{/if} <span>{$order.ta}</span> {$order.currency} <img src="{$sne_img}bullet_edit.png" alt="edit">*}
			</td>
            <td class="d150" style="text-align: center;"><input type="radio" name="data[{$order.id_order|escape:'htmlall':'UTF-8'}][rno]" value="1"></td>
            <td class="d150" style="text-align: center;"><input type="radio" name="data[{$order.id_order|escape:'htmlall':'UTF-8'}][rno]" value="2"></td>
            <td class="d150" style="text-align: center;"><input type="radio" name="data[{$order.id_order|escape:'htmlall':'UTF-8'}][rno]" value="3"></td>
            <td class="d150" style="text-align: center;"><input type="checkbox" name="data[{$order.id_order|escape:'htmlall':'UTF-8'}][meca]" value="yes"></td>
            <td class="label_t">
                <img src="{$sne_img|escape:'htmlall':'UTF-8'}tag_hash.png" alt="parcel" {if !$label_exists}style="display: none;"{/if}>
				&nbsp;
				<a href="{if $label_exists}{$sne_labels[$order.id_order].url|escape:'htmlall':'UTF-8'}{/if}" target="_blank">
					<span class="parcel_number" {if !$label_exists}style="display: none;"{/if}>{if $label_exists}{$sne_labels[$order.id_order].parcel_number|escape:'htmlall':'UTF-8'}{/if}</span>
                </a><br>
                <a href="{if $label_exists}{$sne_labels[$order.id_order].pdfurl|escape:'htmlall':'UTF-8'}{/if}" class="pdfurl">
                    {*{if $label_exists}<img src="{$sne_img|escape:'htmlall':'UTF-8'}file_extension_pdf_16.png">{/if}*}
                </a>
                <textarea class="zpl_code" style="display: none;">{if isset($sne_labels[$order.id_order].zpl_code) && $sne_labels[$order.id_order].zpl_code}{$sne_labels[$order.id_order].zpl_code}{/if}</textarea> {* $sne_labels[$order.id_order].zpl_code => ZPL code for printer, can't escape else error *}
            </td>
        </tr>
    {/foreach}
{/if}