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

{if $sne_exp_list}
    {foreach $sne_exp_list as $order}
        <tr rel="{$order.id_order|escape:'htmlall':'UTF-8'}" class="listing_order">
            <td style="text-align: center;"><input type="checkbox" class="sne_checkbox" name="exp_checkbox[]" value="{$order.parcel_number|escape:'htmlall':'UTF-8'}"></td>
            <td class="left">{$order.id_order|escape:'htmlall':'UTF-8'}</td>
            <td class="left">{$order.parcel_number|escape:'htmlall':'UTF-8'}</td>
            <td class="left">{$order.customer|escape:'htmlall':'UTF-8'}</td>
            <td class="left">{$order.date|escape:'htmlall':'UTF-8'}</td>
            <td class="left">{$order.address|escape:'htmlall':'UTF-8'}</td>
            <td class="left">{substr($order.weight|escape:'htmlall':'UTF-8', 0, 4)}</td>
            {if !$for_session}<td class="send_package"><div class="send_exp">{l s='Send' mod='sonice_etiquetage'}</div></td>{/if}
        </tr>
    {/foreach}
{/if}