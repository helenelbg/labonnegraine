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

{$status|escape:'htmlall':'UTF-8'}
{if !empty($order_history)}
    <div class="dlc_tooltop">
        <div class="dlc_tooltip">
            <i class="icon icon-history"></i>&nbsp; <b style="text-transform:uppercase;">{l s='History' mod='dmulistecommandes'}</b><br />
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Date' mod='dmulistecommandes'}</th>
                        <th>{l s='Status' mod='dmulistecommandes'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$order_history item=order_state}
                        <tr><td>{$order_state.date_add|escape:'htmlall':'UTF-8'}</td><td>{$order_state.name|escape:'htmlall':'UTF-8'}</td></tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}