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

<div id="dlc_views_panel" class="panel">
    <div class="panel-heading">{l s='View selection' mod='dmulistecommandes'}
    <span class="panel-heading-action">
        <a id="" class="list-toolbar-btn" href="{$module_link|escape:'htmlall':'UTF-8'}" style="width:initial;padding:0 5px;">
            <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Views Configuration' mod='dmulistecommandes'}" data-html="true" data-placement="left">
                <i class="icon-cog"></i> <big>{l s='Configuration' mod='dmulistecommandes'}</big>
            </span>
        </a>
        <div style="clear:both;"></div>
    </span>
    </div>
    <div class="row">
        {foreach from=$views item=view}
            <a href="index.php?controller=AdminDmuListeCommandes&id_vue={$view.id_vue|escape:'htmlall':'UTF-8'}&token={$token|escape:'htmlall':'UTF-8'}"  class="btn btn-default {if $view.id_vue == $id_vue}active{/if}"><i class="icon-eye"></i>{$view.name|escape:'htmlall':'UTF-8'}</a>
        {/foreach}
    </div>
</div>
{if $inf_ps6}
<div style="clear:both;">
    <style type="text/css">
    #dlc_views_panel {
        background-color: #F8F8F8;
        border: 1px solid #CCCCCC;
        margin-bottom: 10px;
        padding:10px;
        border-radius:5px;
        margin-bottom:30px;
    }
    #dlc_views_panel .panel-heading-action {
        float:right;
    }
    #dlc_views_panel .row a.btn {
        background-color: #F8F8F8;
        border: 1px solid #CCCCCC;
        margin-bottom: 10px;
        padding:5px 15px 5px 10px;
        border:1px solid #aaa;
        box-shadow:0 0 10px rgba(0,0,0,.5);
        border-radius:3px;
    }
    #dlc_views_panel .row a.btn.active {
        border:1px solid #000;
        color:#900;
    }
    #dlc_views_panel .row a.btn:hover {
        background-color: #49B2FF;
        color:#fff;
        box-shadow:none;
    }
    </style>
</div>
{/if}