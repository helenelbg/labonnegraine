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

{extends file=$layout}

{block name='content'}
    <section id="main" class="flashsale flashsale-page">

        {block name='product_list_header'}
            {if isset($id_flash_sale) && $id_flash_sale}
                <h2 class="h2">{$listing.label|escape:'html':'UTF-8'} {$flashsale_name|escape:'html':'UTF-8'} <span class="show-more-link"><a href="{$link->getModuleLink('flashsales', 'page')|escape:'quotes':'UTF-8'}">{l s='All flash sales' mod='flashsales'} ></a></span></h2>
                {if isset($flashsale_description) && !empty($flashsale_description) && Configuration::get('FLASHSALE_DESCRIPTION_FLASHSALE_PAGE')}
                <section class="description">
                    {$flashsale_description nofilter}{* HTML, cannot escape *}
                </section>
                {/if}
                {if $flashsale_banner && Configuration::get('FLASHSALE_BANNER_FLASHSALE_PAGE')}
                <section class="banner">
                    <img class="img-responsive" src="{$flashsale_banner|escape:'quotes':'UTF-8'}" alt="{$flashsale_name|escape:'html':'UTF-8'}"/>
                </section>
                {/if}
            {else}
                <h2 class="h2">{l s='All flash sales' mod='flashsales'}</h2>
            {/if}
        {/block}

        <section id="products">
        {if isset($id_flash_sale) && $id_flash_sale}
            {if $listing.products|count}
                <div id="">
                    {block name='product_list_top'}
                        {include file='catalog/_partials/products-top.tpl' listing=$listing}
                    {/block}
                </div>
                {block name='product_list_active_filters'}
                    <div id="" class="hidden-sm-down">
                        {$listing.rendered_active_filters nofilter}
                    </div>
                {/block}
                <div id="">
                    {block name='product_list'}
                        {include file='catalog/_partials/products.tpl' listing=$listing}
                    {/block}
                </div>
                <div id="js-product-list-bottom">
                    {block name='product_list_bottom'}
                        {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
                    {/block}
                </div>
            {else}
                {include file='errors/not-found.tpl'}
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
                        {capture assign="productClasses"}
                            {if !Configuration::get('FLASHSALE_CAROUSEL_FLASHSALE_PAGE')}
                                {if !empty($productClass)}{$productClass}{else}col-xs-12 col-sm-6 col-lg-4 col-xl-3{/if}
                            {/if}
                        {/capture}

                        <section class="featured-products clearfix">
                            <div class="products {if Configuration::get('FLASHSALE_CAROUSEL_FLASHSALE_PAGE')}owl-carousel owl-theme{else}row{/if}">
                                {foreach from=$flash_sale.products item=product name=myLoop}
                                    {if Configuration::get('FLASHSALE_PRODUCT_LIST')}
                                        {include file="module:flashsales/views/templates/front/1.7/product-list.tpl" product=$product productClasses=$productClasses}
                                    {else}
                                        {include file="catalog/_partials/miniatures/product.tpl" product=$product productClasses=$productClasses}
                                    {/if}
                                {/foreach}
                            </div>
                        </section>
                {/foreach}
            {/if}
        {/if}
        </section>
    </section>
{/block}
