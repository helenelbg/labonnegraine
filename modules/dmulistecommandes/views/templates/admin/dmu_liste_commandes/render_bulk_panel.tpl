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

<div id="dlc_bulk_panel" style="z-index:666;position:fixed;right:4px;bottom:4px;">
    <div class="dlc_bulk_div" style="padding:10px;">
        <i class="icon-list"></i>&nbsp; <b style="font-size:13px;color:#FFF;">{l s='Bulk actions' mod='dmulistecommandes'} :</b>
    </div>
    <div class="dlc_bulk_div" style="padding:5px 0;">
        <select id="dlc_bulk_select_status" class="dlc_bulk_select">
            <option value=0>{l s='Change status of selected Orders' mod='dmulistecommandes'}</option>
            {foreach from=$order_states item=order_state}
                <option value="{$order_state.id_order_state|escape:'htmlall':'UTF-8'}">&#x25ba; {$order_state.name|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="dlc_bulk_div" style="padding:5px;">
        <select id="dlc_bulk_select_printing" class="dlc_bulk_select">
            <option value=0>{l s='Generate printings of selected Orders' mod='dmulistecommandes'}</option>
            <option value="Invoices">&#x25ba; {l s='Print selected Invoices' mod='dmulistecommandes'}</option>
            <option value="Deliveries">&#x25ba; {l s='Print selected Deliveries' mod='dmulistecommandes'}</option>
        </select>
    </div>
</div>