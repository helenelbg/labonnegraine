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

{*
*   TCPDF - Order list template
*}

<h1>{l s='Orders' mod='sonice_etiquetage'} {$date_session|escape:'htmlall':'UTF-8'}</h1>

<table cellspacing="0" cellpadding="3" border="1">
    <tr align="center">
        <td width="35px" bgcolor="#FFFACD"><b>ID</b></td>
        {if $ps15x}<td width="70px" bgcolor="#FFFACD"><b>{l s='Reference' mod='sonice_etiquetage'}</b></td>{/if}
        <td bgcolor="#FFFACD"><b>{if $ps15x}{l s='Customer' mod='sonice_etiquetage'}{else}Client{/if}</b></td>
        <td bgcolor="#FFFACD"><b>{if $ps15x}{l s='Address' mod='sonice_etiquetage'}{else}Adresse{/if}</b></td>
        <td bgcolor="#FFFACD"><b>{if $ps15x}{l s='Product name' mod='sonice_etiquetage'}{else}Nom du produit{/if}</b></td>
        <td width="20px" bgcolor="#FFFACD"><b>{if $ps15x}{l s='Qty' mod='sonice_etiquetage'}{else}Qt&eacute;{/if}</b></td>
        <td width="35px" bgcolor="#FFFACD"><b>{if $ps15x}{l s='Weight' mod='sonice_etiquetage'}{else}Poids{/if}</b></td>
        <td width="335px" bgcolor="#FFFACD"><b>{if $ps15x}{l s='Comment' mod='sonice_etiquetage'}{else}Commentaire{/if}</b></td>
    </tr>
    {foreach $sne_orders as $order}
        <tr>
            <td>{$order[0]|escape:'htmlall':'UTF-8'}</td>
            {if $ps15x}<td>{$order[1]|escape:'htmlall':'UTF-8'}</td>{/if}
            <td>{$order[2]|escape:'htmlall':'UTF-8'}</td>
            <td>{$order[3]|escape:'htmlall':'UTF-8'}</td>
            <td>{$order[4]|escape:'htmlall':'UTF-8'}</td>
            <td>{$order[5]|escape:'htmlall':'UTF-8'}</td>
            <td>{$order[6]|escape:'htmlall':'UTF-8'}</td>
            <td></td>
        </tr>
    {/foreach}
</table>