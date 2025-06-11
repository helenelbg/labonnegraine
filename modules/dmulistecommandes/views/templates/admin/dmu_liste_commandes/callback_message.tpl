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
    {$gift|escape:'htmlall':'UTF-8'}
{else}
    {if $gift == 1}
        <img src="/modules/dmulistecommandes/views/img/gift.png" width=16 height=16 border=0 /><br />
    {/if}
    {if $cmessages}
        <img src="/modules/dmulistecommandes/views/img/email_edit.gif" width=16 height=16 border=0 /><br />
    {/if}
    {if $messages}
        <img src="/modules/dmulistecommandes/views/img/email.gif" width=16 height=16 border=0 /><br />
    {/if}
    {if $is_message}
        <div class="dlc_tooltop">
            <div class="dlc_tooltip">
                <i class="icon-envelope"></i>&nbsp; <b style="text-transform:uppercase;">{l s='Messages' mod='dmulistecommandes'}</b><br />
                {if $gift == 1}
                    {l s='This order requires a gift wrapping !' mod='dmulistecommandes'}<br />
                    {if !empty($gift_message)}
                        <hr class="dlc_hr"/><span class="dlc_title">{l s='Gift message' mod='dmulistecommandes'}</span><br />
                        <div>{$gift_message|escape:'quotes':'UTF-8'}</div>
                    {/if}
                {/if}
                {if !empty($cmessages)}
                    <hr class="dlc_hr"/>
                    <span class="dlc_title">
                        {if $cmessages|@count > 1} {l s='Dialogues' mod='dmulistecommandes'} {else} {l s='Dialogue' mod='dmulistecommandes'} {/if}
                    </span><br/>
                    {foreach from=$cmessages item=message}
                        <div>{$message.message|nl2br|escape:'quotes':'UTF-8'}</div><hr class="dlc_hr"/>
                    {/foreach}
                {/if}
                {if !empty($messages)}
                    <hr class="dlc_hr"/>
                    <span class="dlc_title">
                        {if $messages|@count > 1} {l s='Customer messages' mod='dmulistecommandes'} {else} {l s='Customer message' mod='dmulistecommandes'} {/if}
                    </span><br/>
                    {foreach from=$messages item=message}
                        <div>{$message.message|nl2br|escape:'quotes':'UTF-8'}</div><hr class="dlc_hr"/>
                    {/foreach}
                {/if}
            </div>
        </div>
    {/if}
{/if}
