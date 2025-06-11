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
<script type="text/javascript">
    var ets_cs_link_search_product ='{$ets_cs_link_search_product nofilter}';
</script>
<div class="etscs-left-panel col-lg-2">
    <div class="list-group">
        {foreach from = $sidebars key='key' item='sidebar'}
            <a class="list-group-item{if $control==$key} active{/if}" href="{$cs_link_module|escape:'html':'UTF-8'}&control={$key|escape:'html':'UTF-8'}">
                <i class="icon-{$key|escape:'html':'UTF-8'}"></i>
                {$sidebar|escape:'html':'UTF-8'}
            </a>
        {/foreach}
    </div>
</div>