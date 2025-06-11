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

{if isset($flashsales.flash_sales) && count($flashsales.flash_sales)}
    <div class="flashsale flashsale-home">
      {foreach from=$flashsales.flash_sales item=flash_sale}
          {if Configuration::get('FLASHSALE_BANNER_HOME_PAGE') || Configuration::get('FLASHSALE_CAROUSEL_HOME_PAGE')}
              <h3 class="products-section-title">{Configuration::get('FLASHSALE_TITLE_HOME_PAGE', Context::getContext()->language->id)|escape:'html':'UTF-8'} {$flash_sale.name|escape:'html':'UTF-8'} <span class="show-more-link"><a href="{$link->getModuleLink('flashsales', 'page', ['id_flash_sale' => {$flash_sale.id_flash_sale|intval}])|escape:'quotes':'UTF-8'}">{l s='Show more' mod='flashsales'} ></a></span></h3>
              {if isset($flash_sale.description) && !empty($flash_sale.description) && Configuration::get('FLASHSALE_DESCRIPTION_HOME_PAGE')}
                  <section class="description">
                      {$flash_sale.description nofilter}{* HTML, cannot escape *}
                  </section>
              {/if}
              {if $flash_sale.banner && Configuration::get('FLASHSALE_BANNER_HOME_PAGE')}
                  <section class="banner">
                      <a href="{$link->getModuleLink('flashsales', 'page', ['id_flash_sale' => {$flash_sale.id_flash_sale|intval}])|escape:'quotes':'UTF-8'}">
                          <img class="img-responsive" src="{$flash_sale.banner|escape:'quotes':'UTF-8'}" alt="{$flash_sale.name|escape:'html':'UTF-8'}"/>
                      </a>
                  </section>
              {/if}
              {if version_compare($smarty.const._PS_VERSION_,'1.7','<')}
                  {include file=$flashsales.tpl_product_list products=$flash_sale.products id="flashsale_{$flash_sale.id_flash_sale}" class="{if Configuration::get('FLASHSALE_CAROUSEL_HOME_PAGE')}owl-carousel owl-theme{/if}"}
              {else}
                  {capture assign="productClasses"}
                      {if !Configuration::get('FLASHSALE_CAROUSEL_HOME_PAGE')}
                        {if !empty($productClass)}{$productClass}{else}col-xs-12 col-sm-6 col-lg-4 col-xl-3{/if}
                      {/if}
                  {/capture}

                  <section class="featured-products clearfix">
                      <div class="products {if Configuration::get('FLASHSALE_CAROUSEL_HOME_PAGE')}owl-carousel owl-theme{else}row{/if}">
                          {foreach from=$flash_sale.products item=product name=myLoop}
                              {include file=$flashsales.tpl_product_list product=$product productClasses=$productClasses}
                          {/foreach}
                      </div>
                  </section>
              {/if}
          {/if}
      {/foreach}
  </div>
{/if}
