{*
* 2022 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2022 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*}

{capture name=path}{l s='Flash sales' mod='flashsales'}{/capture}

<div class="flashsale flashsale-page">

<h1 class="page-heading bottom-indent">
    {if isset($flashsale_name)}
        {Configuration::get('FLASHSALE_TITLE_FLASHSALE_PAGE', Context::getContext()->language->id)|escape:'html':'UTF-8'} {$flashsale_name|escape:'html':'UTF-8'}
        <span class="show-more-link"><a href="{$link->getModuleLink('flashsales', 'page')|escape:'quotes':'UTF-8'}">{l s='All flash sales' mod='flashsales'} ></a></span>
    {else}
        {l s='All flash sales' mod='flashsales'}
    {/if}
</h1>

{if isset($id_flash_sale) && $id_flash_sale}
    {if isset($products) && count($products)}
        <div class="content_sortPagiBar">
            <div class="sortPagiBar clearfix">
                {include file="$tpl_dir./product-sort.tpl"}
                {include file="$tpl_dir./nbr-product-page.tpl"}
            </div>
            <div class="top-pagination-content clearfix">
                {include file="$tpl_dir./product-compare.tpl"}
                {include file="$tpl_dir./pagination.tpl"}
            </div>
        </div>

        {include file="$tpl_dir./product-list.tpl" products=$products}

        <div class="content_sortPagiBar">
            <div class="bottom-pagination-content clearfix">
                {include file="$tpl_dir./product-compare.tpl"}
                {include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
            </div>
        </div>
    {else}
        <p class="alert alert-warning">{l s='No flash sales at this time.' mod='flashsales'}</p>
    {/if}
{else}
    {if isset($flash_sales) && count($flash_sales)}
        {foreach from=$flash_sales item=flash_sale}
            <h3 class="products-section-title">{Configuration::get('FLASHSALE_TITLE_FLASHSALE_PAGE', Context::getContext()->language->id)} {$flash_sale.name|escape:'html':'UTF-8'} <span class="show-more-link"><a href="{$link->getModuleLink('flashsales', 'page', ['id_flash_sale' => {$flash_sale.id_flash_sale|intval}])|escape:'quotes':'UTF-8'}">{l s='Show more' mod='flashsales'} ></a></span></h3>
            {if isset($flash_sale.description) && !empty($flash_sale.description) && Configuration::get('FLASHSALE_DESCRIPTION_FLASHSALE_PAGE')}
            <section class="description">
                {$flash_sale.description nofilter}{* HTML, cannot escape *}
            </section>
            {/if}
            {if $flash_sale.banner && Configuration::get('FLASHSALE_BANNER_FLASHSALE_PAGE')}
            <section class="banner">
                <a href="{$link->getModuleLink('flashsales', 'page', ['id_flash_sale' => {$flash_sale.id_flash_sale|intval}])|escape:'quotes':'UTF-8'}">
                    <img class="img-responsive" src="{$flash_sale.banner|escape:'quotes':'UTF-8'}" alt="{$flash_sale.name|escape:'html':'UTF-8'}"/>
                </a>
            </section>
            {/if}
            {include file="$tpl_dir./product-list.tpl" products=$flash_sale.products id="flashsale_{$flash_sale.id_flash_sale}" class="{if Configuration::get('FLASHSALE_CAROUSEL_FLASHSALE_PAGE')}owl-carousel owl-theme{/if}"}
        {/foreach}
    {/if}
{/if}
</div>
