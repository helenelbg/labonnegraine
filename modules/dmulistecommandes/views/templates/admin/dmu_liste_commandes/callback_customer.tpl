{**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL DREAM ME UP
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL DREAM ME UP is strictly forbidden.
*
*   .--.
*   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
*   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
*   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
*        w w w . d r e a m - m e - u p . f r       '
*
*  @author    Dream me up <prestashop@dream-me-up.fr>
*  @copyright 2007 - 2023 Dream me up
*  @license   All Rights Reserved
*}

{if !empty($iso)}
<img src="/modules/dmulistecommandes/views/img/flags/{$iso|escape:'htmlall':'UTF-8'}.gif" width=16 height=11 border=0 /> &nbsp;
<b>{$customer_name_short|escape:'htmlall':'UTF-8'}</b>
<span rel="index.php?controller=AdminCustomers&id_customer={$customer->id|escape:'htmlall':'UTF-8'}&viewcustomer&token={$token|escape:'htmlall':'UTF-8'}" class="customer_link" style="display:none;"></span>
<div class="dlc_tooltop">
    <div class="dlc_tooltip">
        {if isset($customer->id_gender) && !empty($customer->id_gender)}
            {if $ps7}
                <i class="icon-{if $customer->id_gender > 1}venus{else}mars{/if}" style="float:right;"></i>
            {else}
                <img src="'/img/admin/{if $customer->id_gender > 1}fe{/if}male.gif" width=16 height=16 border=0 align="right">
            {/if}
        {/if}
        <i class="icon-user"></i>&nbsp; <span class="dlc_customer_title">{$customer_name|escape:'htmlall':'UTF-8'}</span><br />
        <hr class="dlc_hr" /><span class="dlc_title">{l s='Contact information' mod='dmulistecommandes'} :</span><br />
        <div class="dlc_push">
            {if isset($delivery_address->phone) && !empty($delivery_address->phone)}
                <i class="icon-phone"></i>&nbsp;{$delivery_address->phone|escape:'htmlall':'UTF-8'}<br />
            {/if}
            {if isset($delivery_address->phone_mobile) && !empty($delivery_address->phone_mobile)}
                <i class="icon-phone"></i>&nbsp;{$delivery_address->phone_mobile|escape:'htmlall':'UTF-8'}<br />
            {/if}
            <i class="icon-pencil"></i>&nbsp; <i>{$customer->email|escape:'htmlall':'UTF-8'}</i>
        </div>
        <hr class="dlc_hr" /><span class="dlc_title">{l s='Delivery address' mod='dmulistecommandes'} :</span><br />
        <div class="dlc_push" style="font-size:13px;">
            {$address_format|cleanHtml nofilter}
        </div>
        {if !empty($customer->note)}
            <hr class="dlc_hr" /><span class="dlc_title">{l s='Private note' mod='dmulistecommandes'} :</span><br />
            <div class="dlc_push">{$customer->note|nl2br|escape:'htmlall':'UTF-8'}</div>
        {/if}
    </div>
</div>
{/if}