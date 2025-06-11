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

<div style="line-height:12px;"><b>{$price_total|escape:'htmlall':'UTF-8'}</b><br />
    <small>{$products_qty|escape:'htmlall':'UTF-8'} {if $products_qty > 1}{l s='PRODUCTS' mod='dmulistecommandes'}{else}{l s='PRODUCT' mod='dmulistecommandes'}{/if}</small>
</div>
<div class="dlc_tooltop" style="right:30px;left:auto;">
    <div class="dlc_tooltip">
        <i class="icon-list"></i>&nbsp; <b style="text-transform:uppercase;">{l s='Order details' mod='dmulistecommandes'}</b><br />
        {if !$dmu_show_buttons}
            {l s='Click to show the Order.' mod='dmulistecommandes'}<br />
        {/if}
        {if $products}
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2">{l s='Products' mod='dmulistecommandes'}</th>
                        <th>{l s='Price' mod='dmulistecommandes'}</th>
                        <th>{l s='Reference' mod='dmulistecommandes'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item=product key=key name=name}
                        <tr>
                            <td style="text-align:right;">{$product.product_quantity|escape:'htmlall':'UTF-8'} x</td>
                            <td>{$product.product_name|escape:'htmlall':'UTF-8'}</td>
                            <td style="text-align:right;"><b>{displayPrice price=$product.total_price_tax_incl}</b></td>
                            <td>{if $product.product_reference}{l s='Ref.' mod='dmulistecommandes'} {$product.product_reference|escape:'htmlall':'UTF-8'}{/if}</td>
                        </tr>
                    {/foreach}
                    {if $rest}
                        <tr>
                            <td colspan="2"><b>{l s='Miscellaneous' mod='dmulistecommandes'}</b> &nbsp; 
                                ( {l s='Shipping' mod='dmulistecommandes'}, {l s='wrapping' mod='dmulistecommandes'},... )</td>
                            <td style="text-align:right;">{$rest|escape:'htmlall':'UTF-8'}</td>
                            <td></td>
                        </tr>
                    {/if}
                    <tr>
                            <td colspan="2"><b style="color:#900;">{l s='TOTAL' mod='dmulistecommandes'}</b></td>
                            <td style="text-align:right;"><b style="color:#900;">{$total|escape:'htmlall':'UTF-8'}</b></td>
                            <td></td>
                        </tr>
                </tbody>
            </table>
        {else}
            {l s='No product found !' mod='dmulistecommandes'}
        {/if}
    </div>
</div>