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
{extends file='page.tpl'}
{block name="page_content"}
    {*{include file="$tpl_dir./errors.tpl"}*}
    {if isset($successRegistration) && $successRegistration}
        <div class="alert alert-success">
            {if $affiliate.active}
                {l s='Success! You have just registered as an affiliate.' mod='psaffiliate'}
            {else}
                {l s='Success! You have just registered as an affiliate. Your request will be reviewed soon by an administrator.' mod='psaffiliate'}
            {/if}
        </div>
    {/if}
    <div id="myaffiliateaccount">
        {*{capture name=path}*}
        {*<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='psaffiliate'}</a>*}
        {*<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>*}
        {*<span>{l s='My affiliate account' mod='psaffiliate'}</span>*}
        {*{/capture}*}

        {if $affiliate.active}
            <div class="row gap-sm">
                <div class="col-md-3">
                    <ul class="nav nav-pills nav-panel nav-stacked">
                        <li class="nav-item"><a href="#myaffiliateaccount-home" data-toggle="tab"
                                                class="nav-link{if $current_tab == 'home'} active{/if}">{l s='My affiliate account' mod='psaffiliate'}</a>
                        </li>
                        <li class="nav-item"><a href="#myaffiliateaccount-summary" data-toggle="tab"
                                                class="nav-link">{l s='Summary' mod='psaffiliate'}</a></li>
                        <li class="nav-item"><a href="#myaffiliateaccount-campaigns-list" data-toggle="tab"
                                                class="nav-link">{l s='Campaigns' mod='psaffiliate'}</a></li>
                        <li class="nav-item"><a href="#myaffiliateaccount-product-commissions" data-toggle="tab"
                                                class="nav-link{if $current_tab == 'products'} active{/if}">{l s='Products & commisions' mod='psaffiliate'}</a>
                        </li>
                        <li class="nav-item"><a href="#myaffiliateaccount-sales-commissions" data-toggle="tab"
                                                class="nav-link{if $current_tab == 'sales'} active{/if}">{l s='Sales & commisions' mod='psaffiliate'}</a>
                        </li>
                        {if $voucher_payments_enabled}
                            <li class="nav-item"><a href="#myaffiliateaccount-myvouchers" data-toggle="tab"
                                                    class="nav-link">{l s='My vouchers' mod='psaffiliate'}</a></li>
                        {/if}
                        {if $lifetime_affiliates_enabled}
                            <li class="nav-item"><a href="#myaffiliateaccount-mylifetimeaffiliations" data-toggle="tab"
                                                    class="nav-link">{l s='My lifetime affiliations' mod='psaffiliate'}</a>
                            </li>
                        {/if}
                    </ul>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane{if $current_tab == 'home'} active{/if}" id="myaffiliateaccount-home">
                            <div class="card">
                                <div class="card-header clearfix text-center-xs">
                                    <h2 class="h3 m-t-sm m-b-sm pull-sm-left float-sm-left pull-none-xs float-none-xs">{l s='My affiliate account' mod='psaffiliate'}</h2>
                                    <div class="pull-sm-right float-md-right m-t-n-xs">
                                        {if $hasTexts}
                                            <a href="{$link->getModuleLink('psaffiliate', 'texts', array(), true)|escape:'html':'UTF-8'}"
                                               class="btn btn-secondary m-t-xs"><i
                                                        class="material-icons">text_format</i> {l s='Text Ads' mod='psaffiliate'}
                                            </a>
                                        {/if}
                                        {if $hasBanners}
                                            <a href="{$link->getModuleLink('psaffiliate', 'banners', array(), true)|escape:'html':'UTF-8'}"
                                               class="btn btn-secondary m-t-xs"><i
                                                        class="material-icons">photo</i> {l s='Banner ads' mod='psaffiliate'}
                                            </a>
                                        {/if}
                                    </div>
                                </div>
                                <div class="card-block">
                                    <form action="#" class="form-horizontal">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label label-my_affiliate_link">{l s='Your affiliate link' mod='psaffiliate'}</label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="my_affiliate_link"
                                                           value="{$myaffiliatelink|escape:'html':'UTF-8'}" readonly>
                                                    <span class="input-group-btn">
												<button type="button" class="btn btn-primary btn-sm btn-copy"
                                                        data-clipboard-target="#my_affiliate_link"><i
                                                            class="material-icons">filter_none</i></button>
											</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">{l s='Your affiliate ID' mod='psaffiliate'}</label>
                                            <div class="col-sm-9">
                                                <p class="form-control-static">{$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="myaffiliateaccount-summary">
                            <div class="card">
                                <div class="card-header clearfix text-center-xs">
                                    <h3 class="m-t-sm m-b-sm pull-sm-left float-md-left">{l s='Summary' mod='psaffiliate'}</h3>
                                    {if $affiliate.balance >= $minimum_payment_amount}
                                        <div class="pull-sm-right float-md-right m-t-n-xs">
                                            <a href="{$link->getModuleLink('psaffiliate', 'requestpayment', array(), true)|escape:'html':'UTF-8'}"
                                               class="btn btn-secondary m-t-xs"><i
                                                        class="material-icons">credit_card</i> {l s='Request a payment' mod='psaffiliate'}
                                            </a>
                                            {if $voucher_payments_enabled}
                                                <a href="{$link->getModuleLink('psaffiliate', 'requestvoucherpayment', array(), true)|escape:'html':'UTF-8'}"
                                                   class="btn btn-secondary m-t-xs"><i class="material-icons">card_giftcard</i> {l s='Request a voucher' mod='psaffiliate'}
                                                </a>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="h4 font-bold m-b">
                                                {l
                                                s='Last %days% days'
                                                sprintf=[
                                                '%days%' => $days_current_summary
                                                ]
                                                d='Modules.Psaffiliate.Shop'
                                                }
                                            </div>
                                            <div class="list-group list-group-hover">
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.clicks|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Clicks' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.unique_clicks|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Unique clicks' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.pending_sales|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Pending sales' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.approved_sales|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Approved sales' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="pull-xs-right font-bold">{Tools::displayPrice(Tools::convertPrice($affiliate.earnings, $currency))|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Earnings' mod='psaffiliate'}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="h4 font-bold m-b">{l s='Total' mod='psaffiliate'}</div>
                                            <div class="list-group list-group-hover">
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.clicks_total|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Clicks' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.unique_clicks_total|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Unique clicks' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="badge badge-pill badge-default pull-xs-right">{$affiliate.approved_sales_total|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Approved sales' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="pull-xs-right font-bold">{Tools::displayPrice(Tools::convertPrice($affiliate.earnings_total, $currency))|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Total earnings' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="pull-xs-right font-bold">{Tools::displayPrice(Tools::convertPrice($affiliate.payments, $currency))|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Total payments' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="pull-xs-right font-bold">{Tools::displayPrice(Tools::convertPrice($affiliate.pending_payments, $currency))|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Pending payments' mod='psaffiliate'}</span>
                                                </div>
                                                <div class="list-group-item">
                                                    <span class="pull-xs-right font-bold">{Tools::displayPrice(Tools::convertPrice($affiliate.balance, $currency))|escape:'htmlall':'UTF-8'}</span>
                                                    <span>{l s='Balance' mod='psaffiliate'}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="myaffiliateaccount-campaigns-list">
                            <div class="card">
                                <div class="card-header clearfix text-center-xs">
                                    <h3 class="m-t-sm m-b-sm pull-sm-left float-md-left">{l s='Campaigns' mod='psaffiliate'}</h3>
                                    <div class="pull-sm-right float-md-right m-t-n-xs">
                                        <a href="{$link->getModuleLink('psaffiliate', 'campaign', array(), true)|escape:'html':'UTF-8'}"
                                           class="btn btn-secondary m-t-xs"><i
                                                    class="material-icons">add_circle</i> {l s='Create a campaign' mod='psaffiliate'}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <p class="subtitle">
                                        {if sizeof($campaigns)}
                                            {l
                                            s='You have %camp% campaigns'
                                            sprintf=[
                                            '%camp%' => sizeof($campaigns)
                                            ]
                                            d='Modules.Psaffiliate.Shop'
                                            }
                                        {else}
                                            {l s='You do not have any campaign' mod='psaffiliate'}
                                        {/if}
                                    </p>
                                </div>
                                {if sizeof($campaigns)}
                                    <div class="table-responsive m-n">
                                        <table id="campaigns" class="table table-hover table-striped b-t m-n">
                                            <thead>
                                            <tr class="text-sm">
                                                <th>{l s='ID' mod='psaffiliate'}</th>
                                                <th>{l s='Name' mod='psaffiliate'}</th>
                                                <th>{l s='Link' mod='psaffiliate'}</th>
                                                <th class="text-xs-center">{l s='Clicks' mod='psaffiliate'}</th>
                                                <th class="text-xs-center">{l s='Approved sales' mod='psaffiliate'}</th>
                                                <th class="text-xs-right">{l s='Total earnings' mod='psaffiliate'}</th>
                                                <th class="text-xs-center">{l s='Details' mod='psaffiliate'}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {foreach from=$campaigns item=campaign}
                                                <tr>
                                                    <td class="text-xs-center">{$campaign.id_campaign|escape:'htmlall':'UTF-8'}</td>
                                                    <td>{$campaign.name|escape:'htmlall':'UTF-8'}</td>
                                                    <td style="min-width: 200px;">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control"
                                                                   id="my_affiliate_link_{$campaign.id_campaign|escape:'htmlall':'UTF-8'}"
                                                                   value="{$campaign.campaign_link|escape:'htmlall':'UTF-8'}"
                                                                   readonly>
                                                            <span class="input-group-btn">
														<button type="button" class="btn btn-primary btn-copy"
                                                                data-clipboard-target="#my_affiliate_link_{$campaign.id_campaign|escape:'htmlall':'UTF-8'}"><i
                                                                    class="material-icons">filter_none</i></button>
													</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-xs-center">{$campaign.clicks|escape:'htmlall':'UTF-8'}</td>
                                                    <td class="text-xs-center">{$campaign.sales|escape:'htmlall':'UTF-8'}</td>
                                                    <td class="text-xs-right">{Tools::displayPrice(Tools::convertPrice($campaign.total_earnings_clicks + $campaign.total_earnings_sales, $currency))|escape:'htmlall':'UTF-8'}</td>
                                                    <td class="text-xs-center"><a
                                                                href='{$campaigns_links[$campaign.id_campaign]|escape:'html':'UTF-8'}'
                                                                class="btn btn-secondary" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="{l s='More info' mod='psaffiliate'}"><i
                                                                    class="material-icons">dehaze</i></a></td>
                                                </tr>
                                            {/foreach}
                                            </tbody>
                                        </table>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="tab-pane{if $current_tab == 'products'} active{/if}"
                             id="myaffiliateaccount-product-commissions">
                            <div class="card">
                                <div class="card-header clearfix text-center-xs">
                                    <h3 class="m-t-sm m-b-sm">{l s='Products & commisions listing' mod='psaffiliate'}</h3>
                                </div>
                                <div class="card-block">
                                    <form action="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)}"
                                          method="get">
                                        <input type="hidden" name="t" value="products">
                                        <input type="hidden" name="p" value="1">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="s"
                                                   placeholder="{l s='Filter products by the name or reference' mod='psaffiliate'}"
                                                   value="{if $search_terms}{$search_terms|escape:'html':'UTF-8'}{/if}">
                                            <span class="input-group-btn">
										<button type="submit" class="btn btn-primary btn-sm"><i class="material-icons">search</i></button>
									</span>
                                        </div>

                                    </form>
                                </div>
                                {if ! $product_commisions}
                                    <div class="card-block">
                                        <div class="alert alert-warning">{l s='No products found.' mod='psaffiliate'}</div>
                                    </div>
                                {else}
                                    <table class="table table-hover table-striped b-t m-n">
                                        <thead>
                                        <tr>
                                            <th style="width: 1px;">{l s='Cover' mod='psaffiliate'}</th>
                                            <th>{l s='Name / Affiliate link' mod='psaffiliate'}</th>
                                            <th class="text-center">{l s='Reference' mod='psaffiliate'}</th>
                                            <th class="text-center">{l s='Commission' mod='psaffiliate'}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {foreach $product_commisions as $product}
                                            <tr>
                                                <td style="width: 1px;" class="v-middle">
                                                    {if $product.image}
                                                        <a href="{$product.link|escape:'html':'UTF-8'}" target="_blank">
                                                            <img src="{$product.image|escape:'html':'UTF-8'}"
                                                                 alt="{$product.name|escape:'html':'UTF-8'}">
                                                        </a>
                                                    {/if}
                                                </td>
                                                <td class="v-middle">
                                                    <div class="h4">
                                                        <a href="{$product.link}"
                                                           target="_blank">{$product.name|escape:'html':'UTF-8'}</a>
                                                    </div>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control"
                                                               id="product_affiliate_link_{$product.id_product|escape:'html':'UTF-8'}"
                                                               value="{$product.aff_link|escape:'html':'UTF-8'}"
                                                               readonly>
                                                        <span class="input-group-btn">
														<button type="button" class="btn btn-primary btn-sm btn-copy"
                                                                data-clipboard-target="#product_affiliate_link_{$product.id_product|escape:'html':'UTF-8'}"><i
                                                                    class="material-icons">filter_none</i></button>
													</span>
                                                    </div>
                                                </td>
                                                <td class="v-middle text-center">
                                                    {if $product.reference}
                                                        {$product.reference|escape:'html':'UTF-8'}
                                                    {else}
                                                        <span class="text-muted">n/a</span>
                                                    {/if}
                                                </td>
                                                <td class="text-center v-middle">
                                                    <strong>{$product.commision|escape:'html':'UTF-8'}</strong>
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                    {if count($product_commisions_pages) > 1}
                                        <div class="card-block">
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination m-0">
                                                    {if $current_page != 1}
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                               href="{$link->getModuleLink('psaffiliate', 'myaccount', ['t' => 'products', 'p' => 1, 's' => $search_terms], true)|escape:'html':'UTF-8'}">
                                                                <span>&laquo;</span>
                                                            </a>
                                                        </li>
                                                    {/if}
                                                    {foreach $product_commisions_pages as $page}
                                                        <li class="page-item{if $current_page == $page} active{/if}">
                                                            <a class="page-link"
                                                               href="{if $page == '...'}javascript:void(0);{else}{$link->getModuleLink('psaffiliate', 'myaccount', ['t' => 'products', 'p' => $page, 's' => $search_terms], true)|escape:'html':'UTF-8'}{/if}">{$page|escape:'html':'UTF-8'}</a>
                                                        </li>
                                                    {/foreach}
                                                    {if $current_page != $product_commisions_last}
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                               href="{$link->getModuleLink('psaffiliate', 'myaccount', ['t' => 'products', 'p' => $product_commisions_last, 's' => $search_terms], true)|escape:'html':'UTF-8'}">
                                                                <span>&raquo;</span>
                                                            </a>
                                                        </li>
                                                    {/if}
                                                </ul>
                                            </nav>
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                        </div>
                        <div class="tab-pane{if $current_tab == 'sales'} active{/if}"
                             id="myaffiliateaccount-sales-commissions">
                            <div class="card">
                                <div class="card-header clearfix text-center-xs">
                                    <h3 class="m-t-sm m-b-sm">{l s='My sales & commissions' mod='psaffiliate'}</h3>
                                </div>
                                {if ! $sale_commissions}
                                    <div class="card-block">
                                        <div class="alert alert-warning">{l s='You haven\'t recieved any commisions yet.' mod='psaffiliate'}</div>
                                    </div>
                                {else}
									<div class="card-block">Total commission : {$total_commissions} </div>
                                    <table class="table table-hover table-striped b-t m-n">
                                        <thead>
                                        <tr>
                                            <th>{l s='ID' mod='psaffiliate'}</th>
                                            <th>{l s='Date' mod='psaffiliate'}</th>
                                            <th>{l s='Order total' mod='psaffiliate'}</th>
                                            <th>{l s='Your commission' mod='psaffiliate'}</th>
                                            <th class="text-center">{l s='Approved' mod='psaffiliate'}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {foreach $sale_commissions as $commission}
                                            <tr>
                                                <td class="v-middle">#{$commission.id_sale|escape:'html':'UTF-8'}</td>
                                                <td class="v-middle">{$commission.date|escape:'html':'UTF-8'}</td>
                                                <td class="v-middle">{$commission.order_total|escape:'html':'UTF-8'}</td>
                                                <td class="v-middle">{$commission.commission|escape:'html':'UTF-8'}</td>
                                                <td class="v-middle text-center">
                                                    {if $commission.approved}
                                                        <i class="material-icons text-success">check</i>
                                                    {else}
                                                        <i class="material-icons text-danger">close</i>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                    {if count($sale_commissions_pages) > 1}
                                        <div class="card-block">
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination m-0">
                                                    {if $current_page != 1}
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                               href="{$link->getModuleLink('psaffiliate', 'myaccount', ['t' => 'sales', 'p' => 1], true)|escape:'html':'UTF-8'}">
                                                                <span>&laquo;</span>
                                                            </a>
                                                        </li>
                                                    {/if}
                                                    {foreach $sale_commissions_pages as $page}
                                                        <li class="page-item{if $current_page == $page} active{/if}">
                                                            <a class="page-link"
                                                               href="{if $page == '...'}javascript:void(0);{else}{$link->getModuleLink('psaffiliate', 'myaccount', ['t' => 'sales', 'p' => $page], true)|escape:'html':'UTF-8'}{/if}">{$page|escape:'html':'UTF-8'}</a>
                                                        </li>
                                                    {/foreach}
                                                    {if $current_page != $sale_commissions_last}
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                               href="{$link->getModuleLink('psaffiliate', 'myaccount', ['t' => 'sales', 'p' => $sale_commissions_last], true)|escape:'html':'UTF-8'}">
                                                                <span>&raquo;</span>
                                                            </a>
                                                        </li>
                                                    {/if}
                                                </ul>
                                            </nav>
                                        </div>
                                    {/if}
                                {/if}
                            </div>
                        </div>
                        {if $voucher_payments_enabled}
                            <div class="tab-pane" id="myaffiliateaccount-myvouchers">
                                <div class="card">
                                    <div class="card-header clearfix text-center-xs">
                                        <h3 class="m-t-sm m-b-sm">{l s='My payment vouchers' mod='psaffiliate'}</h3>
                                    </div>
                                    <div class="card-block">
                                        {if ! $vouchers}
                                            <div class="alert alert-warning">{l s='No payment vouchers requested yet.' mod='psaffiliate'}</div>
                                        {else}
                                            <table class="table table-hover table-striped b-t m-n">
                                                <thead>
                                                <tr>
                                                    <th>{l s='Made at' mod='psaffiliate'}</th>
                                                    <th>{l s='Code' mod='psaffiliate'}</th>
                                                    <th>{l s='Amount' mod='psaffiliate'}</th>
                                                    <th class="text-center">{l s='Approved' mod='psaffiliate'}</th>
                                                    <th class="text-center">{l s='Used' mod='psaffiliate'}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                {foreach $vouchers as $voucher}
                                                    <tr>
                                                        <td>{$voucher->date_add|escape:'html':'UTF-8'}</td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                       id="my_voucher_{$voucher->id|escape:'htmlall':'UTF-8'}"
                                                                       value="{$voucher->code|escape:'html':'UTF-8'}"
                                                                       readonly>
                                                                <span class="input-group-btn">
																<button type="button" class="btn btn-primary btn-copy"
                                                                        data-clipboard-target="#my_voucher_{$voucher->id|escape:'htmlall':'UTF-8'}"><i
                                                                            class="material-icons">filter_none</i></button>
															</span>
                                                            </div>
                                                        </td>
                                                        <td>{Tools::displayPrice($voucher->reduction_amount, intval($voucher->reduction_currency))|escape:'html':'UTF-8'}</td>
                                                        <td class="text-center">
                                                            {if $voucher->active}
                                                                <i class="material-icons text-success">check</i>
                                                            {else}
                                                                <i class="material-icons text-danger">close</i>
                                                            {/if}
                                                        </td>
                                                        <td class="text-center">
                                                            {if ! $voucher->quantity}
                                                                <i class="material-icons text-success">check</i>
                                                            {else}
                                                                <i class="material-icons text-danger">close</i>
                                                            {/if}
                                                        </td>
                                                    </tr>
                                                {/foreach}
                                                </tbody>
                                            </table>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {if $lifetime_affiliates_enabled}
                            <div class="tab-pane" id="myaffiliateaccount-mylifetimeaffiliations">
                                <div class="card">
                                    <div class="card-header clearfix text-center-xs">
                                        <h3 class="m-t-sm m-b-sm">{l s='My lifetime affiliations' mod='psaffiliate'}</h3>
                                    </div>
                                    <div class="card-block">
                                        {if ! $lifetime_affiliations}
                                            <div class="alert alert-warning">{l s='You do not have any lifetime affiliations yet.' mod='psaffiliate'}</div>
                                        {else}
                                            <table class="table table-hover table-striped b-t m-n">
                                                <thead>
                                                <tr>
                                                    <th>{l s='Date' mod='psaffiliate'}</th>
                                                    <th>{l s='Customer name' mod='psaffiliate'}</th>
                                                    <th>{l s='Total commission made' mod='psaffiliate'}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                {foreach $lifetime_affiliations as $row}
                                                    <tr>
                                                        <td>{dateFormat date=$row.date|escape:'html':'UTF-8' full=0}</td>
                                                        <td>{$row.customer_name|escape:'html':'UTF-8'}</td>
                                                        <td>{Tools::displayPrice(Tools::convertPrice($row.commission, $currency))}</td>
                                                    </tr>
                                                {/foreach}
                                                </tbody>
                                            </table>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {elseif !(isset($successRegistration) && $successRegistration)}
            <div class="alert alert-warning"><p>
                    {if $affiliate.has_been_reviewed}
                        {l s='Your affiliate account is not active.' mod='psaffiliate'}
                    {else}
                        {l s='Your account has not been reviewed by an administrator yet.' mod='psaffiliate'}
                    {/if}
                </p></div>
        {/if}
    </div>
{/block}