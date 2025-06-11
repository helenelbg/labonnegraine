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
{assign var=_svg_copy value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg></i>'}
{assign var=_svg_envelop value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg></i>'}
{assign var=_svg_twitter value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1684 408q-67 98-162 167 1 14 1 42 0 130-38 259.5t-115.5 248.5-184.5 210.5-258 146-323 54.5q-271 0-496-145 35 4 78 4 225 0 401-138-105-2-188-64.5t-114-159.5q33 5 61 5 43 0 85-11-112-23-185.5-111.5t-73.5-205.5v-4q68 38 146 41-66-44-105-115t-39-154q0-88 44-163 121 149 294.5 238.5t371.5 99.5q-8-38-8-74 0-134 94.5-228.5t228.5-94.5q140 0 236 102 109-21 205-78-37 115-142 178 93-10 186-50z"/></svg></i>'}
{assign var=_svg_facebook value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1343 12v264h-157q-86 0-116 36t-30 108v189h293l-39 296h-254v759h-306v-759h-255v-296h255v-218q0-186 104-288.5t277-102.5q147 0 228 12z"/></svg></i>'}

<div class="eam-alert alert alert-info">
    {l s='Sell our affiliate products to earn commissions. Each product in the table below is available for the affiliate program and is attached to your "affiliate link". Share these links with your friends via Facebook, Twitter, Email, Blog, Google Adwords, etc. When any of your friends purchase the products, you will earn commission from the sales' mod='ets_affiliatemarketing'}
</div>
<div class="aff-product-popup-share-mail">
    <span class="aff-close">{l s='Close' mod='ets_affiliatemarketing'}</span>
    <div class="popup-content">
        <form action="" method="post">
            <div class="form-wrapper">
                <input name="aff-product-share-link" type="hidden" id="aff-product-share-link" value="" />
                <input name="aff-product-share-name" type="hidden" id="aff-product-share-name" value="" />
                <div class="form-group">
                    <label class="col-lg-2 col-md-2 col-sm-2">{l s='Name' mod='ets_affiliatemarketing'}</label>
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <input name="aff-name" id="aff-name" type="text" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 col-md-2 col-sm-2 required">{l s='Email' mod='ets_affiliatemarketing'}</label>
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <input type="text" name="aff-emails" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 col-md-2 col-sm-2">{l s='Message' mod='ets_affiliatemarketing'}</label>
                    <div class="col-lg-9 col-md-9 col-sm-9">
                        <textarea name="aff-messages" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="form-footer">
                <button char="btn btn-default" name="affSubmitSharEmail" data-link="{$link->getModuleLink('ets_affiliatemarketing','aff_products') nofilter}">{l s='Send email' mod='ets_affiliatemarketing'}</button>
            </div>
        </form>
    </div>
</div>
<div class="table-responsive">
    <table class="table eam-table-flat table-label-custom">
        <thead>
        <tr>
            <th class="text-center">
                {l s='ID' mod='ets_affiliatemarketing'}
                <a href="javascript:void(0)" title=""
                   class="eam-sort-desc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'desc' && isset($query.orderBy) && $query.orderBy == 'id_product'}active{/if}"
                   data-order-by="id_product" data-order-way="DESC">{$_svg_desc nofilter}</a>
                <a href="javascript:void(0)" title=""
                   class="eam-sort-asc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'asc' && isset($query.orderBy) && $query.orderBy == 'id_product'}active{/if}"
                   data-order-by="id_product" data-order-way="ASC">{$_svg_asc nofilter}</a>
            </th>
            <th class="text-center">
                {l s='Image' mod='ets_affiliatemarketing'}
            </th>
            <th class="text-center">
                {l s='Product' mod='ets_affiliatemarketing'}
                <a href="javascript:void(0)" title=""
                   class="eam-sort-desc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'desc' && isset($query.orderBy) && $query.orderBy == 'name'}active{/if}"
                   data-order-by="name" data-order-way="DESC">{$_svg_desc nofilter}</a>
                <a href="javascript:void(0)" title=""
                   class="eam-sort-asc js-eam-order-data {if (isset($query.orderWay) && $query.orderWay == 'asc' && isset($query.orderBy) && $query.orderBy == 'name') || ! isset($query.orderBy)}active{/if}"
                   data-order-by="name" data-order-way="ASC">{$_svg_asc nofilter}</a>
            </th>
            <th class="text-center">
                {l s='Price' mod='ets_affiliatemarketing'}
                <a href="javascript:void(0)" title=""
                   class="eam-sort-desc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'desc' && isset($query.orderBy) && $query.orderBy == 'price'}active{/if}"
                   data-order-by="price" data-order-way="DESC">{$_svg_desc nofilter}</a>
                <a href="javascript:void(0)" title=""
                   class="eam-sort-asc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'asc' && isset($query.orderBy) && $query.orderBy == 'price'}active{/if}"
                   data-order-by="price" data-order-way="ASC">{$_svg_asc nofilter}</a>
            </th>
            <th class="text-center">
                {l s='Commission Rate' mod='ets_affiliatemarketing'}
                <a href="javascript:void(0)" title=""
                   class="eam-sort-desc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'desc' && isset($query.orderBy) && $query.orderBy == 'commission_rate'}active{/if}"
                   data-order-by="commission_rate" data-order-way="DESC">{$_svg_desc nofilter}</a>
                <a href="javascript:void(0)" title=""
                   class="eam-sort-asc js-eam-order-data {if isset($query.orderWay) && $query.orderWay == 'asc' && isset($query.orderBy) && $query.orderBy == 'commission_rate'}active{/if}"
                   data-order-by="commission_rate" data-order-way="ASC">{$_svg_asc nofilter}</a>
            </th>
            <th>{l s='Share' mod='ets_affiliatemarketing'}</th>
        </tr>
        </thead>
        <tbody>
        {if isset($eam_aff_products.results) && count($eam_aff_products.results)}
            {foreach $eam_aff_products.results as $result}
                <tr>
                    <td class="text-center">{$result.id_product nofilter}</td>
                    <td class="text-center"><img src="{$result.image nofilter}" alt="{$result.id_product nofilter}" class="eam-img-table"></td>
                    <td>
                        <a href="{$result.link nofilter}">{$result.name nofilter}</a>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control eam-input-link eam-tooltip" value="{$result.aff_link nofilter}" data-eam-tooltip="Click to copy to clipboard"
                                   data-eam-copy="Copied to clipboard" aria-describedby="eam-affiliate-link-add-on">
                            <span class="input-group-addon eam-tooltip eam-affiliate-link-add-on" data-eam-tooltip="{l s='Click to copy affiliate link' mod='ets_affiliatemarketing'}"
                                  data-eam-copy="Copied to clipboard" id="eam-affiliate-link-add-on">
                                    {$_svg_copy nofilter}
                                </span>
                        </div>
                    </td>
                    <td class="text-center">{$result.display_price nofilter nofilter}</td>
                    <td class="text-center">{$result.commission_rate nofilter} ({$result.commission_rate_percentage nofilter})</td>
                    <td>

                        <div class="aff-product-share-frontend">
                            <span class="aff-product-share">{$_svg_share_alt nofilter}</span>
                            <div class="aff-product-share-list" style="display:none;">
                                <a class="aff-product-share-fb" href="https://www.facebook.com/sharer/sharer.php?u={$result.aff_link|urlencode nofilter}" target="_blank" title="{l s='Share on facebook' mod='ets_affiliatemarketing'}">
                                    {$_svg_facebook nofilter}</a><br />
                                <a class="aff-product-share-tw" href="https://twitter.com/intent/tweet?text={$result.name|urlencode nofilter}&url={$result.aff_link|urlencode nofilter}" target="_blank" title="{l s='Share on twitter' mod='ets_affiliatemarketing'}">
                                    {$_svg_twitter nofilter}</a>
                                <a href="{$result.aff_link|urlencode nofilter}" data-product-name="{$result.name|escape:'html':'UTF-8'}" title="{l s='Share via email' mod='ets_affiliatemarketing'}" class="aff-product-share-email">
                                    {$_svg_envelop nofilter}</a>
                            </div>
                        </div>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr class="text-center">
                <td colspan="100%">
                    {l s='No data was found.' mod='ets_affiliatemarketing'}
                </td>
            </tr>
        {/if}
        </tbody>
    </table>
    {if $eam_aff_products.total_page > 1}
        <div class="eam-pagination">
            <ul>
                {if $eam_aff_products.current_page > 1}
                    <li class="{if $eam_aff_products.current_page == 1} active {/if}">
                        <a href="javascript:void(0)" data-page="{$eam_aff_products.current_page + 1 nofilter}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                    </li>
                {/if}
                {assign 'minRange' 1}
                {assign 'maxRange' $eam_aff_products.total_page}
                {if $eam_aff_products.total_page > 10}
                    {if $eam_aff_products.current_page < ($eam_aff_products.total_page - 3)}
                        {assign 'maxRange' $eam_aff_products.current_page + 2}
                    {/if}
                    {if $eam_aff_products.current_page > 3}
                        {assign 'minRange' $eam_aff_products.current_page - 2}
                    {/if}
                {/if}
                {if $minRange > 1}
                    <li><span class="eam-page-3dot">...</span></li>
                {/if}
                {for $page=$minRange to $maxRange}
                    <li class="{if $page == $eam_aff_products.current_page} active {/if}">
                        <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}"
                           class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                    </li>
                {/for}
                {if $maxRange < $eam_aff_products.total_page}
                    <li><span class="eam-page-3dot">...</span></li>
                {/if}
                {if $eam_aff_products.current_page < $eam_aff_products.total_page}
                    <li>
                        <a href="javascript:void(0)" data-page="{$eam_aff_products.current_page + 1|escape:'html':'UTF-8'}"
                           class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'} </a>
                    </li>
                {/if}
            </ul>
        </div>
    {/if}
</div>
<div class="stat-filter eam-box-filter">
    <form class="form-inline" action="" method="post">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-xs-12">
                <input type="hidden" name="page" value="1" />
                <label>{l s='Filter Categories' mod='ets_affiliatemarketing'}</label>
                <select name="category" class="form-control">
                    <option value="all"
                            {if isset($eam_aff_products.query.category) && $eam_aff_products.query.category == 'all'}selected="selected"{/if}>{l s='All' mod='ets_affiliatemarketing'}</option>
                    {foreach from=$eam_cats item=cat}
                        <option {if isset($eam_aff_products.query.category) && $eam_aff_products.query.category == $cat.id_category} selected="selected"{/if}
                                value="{$cat.id_category|escape:'html':'UTF-8'}">{$cat.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-lg-3 col-md-6 col-xs-12">
                <label>{l s='Filter Product' mod='ets_affiliatemarketing'}</label>
                <input type="text" class="form-control" placeholder="{l s='Search by product name' mod='ets_affiliatemarketing'}" name="product_name" value="{if isset($smarty.get.product_name)}{$smarty.get.product_name|escape:'html':'UTF-8'}{/if}" />
            </div>
            <div class="eam_action">
                <div class="form-group">
                    <button type="submit"
                            class="btn btn-default btn-block js-btn-submit-filter">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}
                    </button>
                    <button type="button"
                            class="btn btn-default btn-block js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
