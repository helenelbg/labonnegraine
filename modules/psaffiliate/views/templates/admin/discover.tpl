{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code.
*
*  @author Active Design <office@activedesign.ro>
*  @copyright  2017-2018 Active Design
*  @license LICENSE.txt
*}
{if $addons_modules}
    <div id="__actd_modules">
        <h1 class="text-center" id="__actd_modules_title"><span>{l s='Discover Our Products' mod='psaffiliate'}</span>
        </h1>
        <div class="row">
            <div class="col-lg-offset-1 col-lg-10">
                <div class="owl-carousel owl-theme">
                    {foreach $addons_modules as $module}
                        <div class="__actd_module item">
                            <a href="{$module.url}" title="{$module.displayName}" target="_blank" rel="nofollow">
                                <div class="module-head">
                                    <div class="module-image">
                                        <img alt="{$module.displayName}" height="57" width="57" src="{$module.img}">
                                    </div>
                                    <p class="headings headings-xsmall title-block-module xs-margin-bottom text-center"
                                       title="{$module.displayName}">
                                        {$module.displayName}
                                    </p>
                                </div>

                                <div class="module-body">
                                    <div class="module-entry clearfix">
                                        <p class="primary-text xsmall-text desc-block-module text-center is-truncated">
                                            {$module.description}
                                        </p>
                                    </div>
                                </div>
                                <div class="module-footer">
                                    <div class="price-rates clearfix">
                                        <p class="pull-right">
                                            <span class="primary-text regular-text price"></span>

                                            <span class="primary-text regular-text">{$module.price_formatted}</span>
                                        </p>
                                    </div>
                                    <div class="module-hover text-center">
                                        <p class="btn btn-plain btn-neutral">{l s='Discover' mod='psaffiliate'}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div id="__actd_discover_all" class="text-center">
            <a href="https://addons.prestashop.com/en/2_community-developer?contributor=156845"
               class="btn btn-plain btn-neutral" target="_blank">
                {l s='Discover all products' mod='psaffiliate'}</a>
        </div>
    </div>
{/if}