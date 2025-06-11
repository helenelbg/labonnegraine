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
** Bordereau de remise colissimo suivi
*}

<div style="text-align: right; font-size: 12px;"><b>BORDEREAU DE REMISE Offre entreprise Colissimo</b></div>

<table>
    <tr>
        <td>SITE DE PRISE EN CHARGE : {$module_conf.pickup_site|escape:'htmlall':'UTF-8'}</td>
        <td></td>
    </tr>
    <tr>
        <td>LIBELLE SITE DE PRISE EN CHARGE : {$module_conf.pickup_label|escape:'htmlall':'UTF-8'}</td>
        <td></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>N&deg; CLIENT : {$contract_number|escape:'htmlall':'UTF-8'}</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="width: 100%">LIBELLE CLIENT : {$customer_label|escape:'htmlall':'UTF-8'}</td>
        <td>&nbsp;</td>
    </tr>
</table>

<table>
    <tr>
        <td>N&deg; BORDEREAU : {date('ymdHis')|escape:'htmlall':'UTF-8'}</td>
        <td>EDITE LE : {date('d/m/Y')|escape:'htmlall':'UTF-8'}</td>
    </tr>
</table>

<div>&nbsp;</div>

<table border="0">
    <thead>
        <tr style="font-weight: bold;">
            <th width="65px">Ref. CLIENT<br></th>
            <th width="65px">Nom destinataire<br></th> {* <=== ICI *}
            <th width="150px">Adresse Destinataire<br></th>
            <th width="80px">N&deg;COLIS<br></th>
            <th width="60px">CPOST<br></th>
            <th width="60px">CPAYS<br></th>
            <th width="60px">POIDS (Kg)<br></th>
            <th width="50px">NM<br></th>
        </tr>
    </thead>
    <tbody>
        {foreach $sne_orders as $key => $order}
            <tr {if $key % 2 === 0}bgcolor="lemonchiffon"{/if}>
                <td width="65px">{$order.id_order|escape:'htmlall':'UTF-8'}</td>
                <td width="65px">{$nom_client|escape:'htmlall':'UTF-8'}</td> {* <=== ICI *}
                <td width="150px">{$order.address|escape:'htmlall':'UTF-8'}</td>
                <td width="80px">{$order.nb_parcel|escape:'htmlall':'UTF-8'}</td>
                <td width="60px">{$order.postcode|escape:'htmlall':'UTF-8'}</td>
                <td width="60px">{$order.iso_country|escape:'htmlall':'UTF-8'}</td>
                <td width="60px">{$order.weight|escape:'htmlall':'UTF-8'}</td>
                <td width="50px">{$order.D150|escape:'htmlall':'UTF-8'}</td>
            </tr>
        {/foreach}
    </tbody>
</table>
    
<div>&nbsp;</div>
<hr>
<diV>&nbsp;</diV>

<table>
    <tr>
        <td width="60%">
            <b>NOMBRE TOTAL DE COLIS : {$total_parcel|escape:'htmlall':'UTF-8'}<br>
                POIDS TOTAL DES COLIS : {$total_weight|escape:'htmlall':'UTF-8'} Kg<br>
            </b>
            <br>
            <br>
			{if $pdf417}
				<img src="{$barcode_png|escape:'htmlall':'UTF-8'}" width="300px" />
			{/if}
            <br>
            <br>
            (<sup>1</sup>) La signature de l'agent de la Poste ne vaut pas validation des données utiles à la facturation portées par le client.
        </td>
        <td border="1" width="35%">
            <span style="font-size: 13px;"><b>SIGNATURE AGENT(<sup>1</sup>) :</b></span><br>
            <br>
            <br>
            <br>
            <br>
            <span style="font-size: 13px;"><b>DATE :</span></b><br>
        </td>
    </tr>
</table>