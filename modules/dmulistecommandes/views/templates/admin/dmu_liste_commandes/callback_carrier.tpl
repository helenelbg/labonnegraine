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

{if $exportorder}
    {$carrier->name|escape:'htmlall':'UTF-8'}
{else}
    {if $id_carrier}
        <span class="label color_field carrier_label carrier_label_'.$tr['id_order'].'" rel="{$tr.id_order|escape:'htmlall':'UTF-8'}"  style="background-color:{$background|escape:'htmlall':'UTF-8'};color:{$text_bg|escape:'htmlall':'UTF-8'};">{$carrier->name|escape:'htmlall':'UTF-8'}</span>
        {if $url}
            <span rel="{$url|escape:'htmlall':'UTF-8'}" class="tracking_link" style="display:none;"></span>
        {/if}
        {if (isset($tr.shipping_number) && !empty($tr.shipping_number)) || ($carrier->url)}
            <div class="dlc_tooltop"><div class="dlc_tooltip">
                {if isset($tr.shipping_number) && !empty($tr.shipping_number)}
                    {if $carrier->url}
                        <i class="icon-truck"></i>&nbsp; <b style="text-transform:uppercase;">{$carrier->name|escape:'htmlall':'UTF-8'}</b><br/>{l s='Click to access package tracking' mod='dmulistecommandes'}<br />
                        <hr class="dlc_hr"/>{l s='Order Weight' mod='dmulistecommandes'} : {$order_weight|escape:'htmlall':'UTF-8'} Kg<br/>
                        <span class="dlc_title">{l s='Tracking number' mod='dmulistecommandes'}</span> : {$tr.shipping_number|escape:'htmlall':'UTF-8'}<br />
                    {else}
                        <i class="icon-truck"></i>&nbsp; <b style="text-transform:uppercase;">{$carrier->name|escape:'htmlall':'UTF-8'}</b><br/>
                        <hr class="dlc_hr"/>{l s='Order Weight' mod='dmulistecommandes'} : {$order_weight|escape:'htmlall':'UTF-8'} Kg<br/>
                        <span class="dlc_title">{l s='Tracking number' mod='dmulistecommandes'}</span> : {$tr.shipping_number|escape:'htmlall':'UTF-8'}<br />
                    {/if}
                {else if $carrier->url}
                    <i class="icon-truck"></i>&nbsp; <b style="text-transform:uppercase;">{$carrier->name|escape:'htmlall':'UTF-8'}</b><br />
                    <hr class="dlc_hr" />{l s='Order Weight' mod='dmulistecommandes'} : {$order_weight|escape:'htmlall':'UTF-8'} Kg<br/>
                    {l s='Click to enter a tracking number.' mod='dmulistecommandes'}<br />
                {/if}
            </div>
        {/if}
    {else}
        ?
    {/if}
{/if}

