{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<div class="ybc-left-panel col-lg-2">
    <div class="list-group">
        {if $list}
            {foreach from=$list item='tab'}
                <a class="{if $active == $tab.id}active{/if} list-group-item" href="{$tab.url|escape:'html':'UTF-8'}" id="{$tab.id|escape:'html':'UTF-8'}">{if isset($tab.icon)}<i class="{$tab.icon|escape:'html':'UTF-8'}"></i> {/if}{$tab.label|escape:'html':'UTF-8'}</a>
            {/foreach}
        {/if}
    </div>
</div>