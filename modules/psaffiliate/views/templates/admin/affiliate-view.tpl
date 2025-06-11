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
<div id="container-affiliate-view">
    <div class="row">
        {*left*}
        <div class="col-lg-6">
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon-user"></i>
                    {$affiliate.firstname|escape:'htmlall':'UTF-8'}
                    {$affiliate.lastname|escape:'htmlall':'UTF-8'}
                    [{$affiliate.id_affiliate|string_format:"%06d"|escape:'htmlall':'UTF-8'}]
                    -
                    <a href="mailto:{$affiliate.email|escape:'htmlall':'UTF-8'}"><i class="icon-envelope"></i>
                        {$affiliate.email|escape:'htmlall':'UTF-8'}
                    </a>
                    <div class="panel-heading-action">
                        <a class="btn btn-default"
                           href="{$current|escape:'html':'UTF-8'}&amp;id_affiliate={$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}&amp;updateaff_affiliates&amp;token={$token|escape:'htmlall':'UTF-8'}">
                            <i class="icon-edit"></i>
                            {l s='Edit' mod='psaffiliate'}
                        </a>
                    </div>
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='First name' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$affiliate.firstname|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Last name' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$affiliate.lastname|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Email' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$affiliate.email|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Website' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{if $affiliate.website}{$affiliate.website|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
                        </div>
                    </div>
                    {if $affiliate.textarea_registration_label}
                        <div class="row">
                            <label class="control-label col-lg-3">{$affiliate.textarea_registration_label|escape:'htmlall':'UTF-8'}</label>
                            <div class="col-lg-9">
                                <p class="form-control-static">{if $affiliate.textarea_registration}{$affiliate.textarea_registration|escape:'htmlall':'UTF-8'}{else}--{/if}</p>
                            </div>
                        </div>
                    {/if}

                    {if $custom_fields}
                        {foreach $custom_fields as $custom_field}
                            <div class="row">
                                <label class="control-label col-lg-3">{$custom_field.name|escape:'htmlall':'UTF-8'}</label>
                                <div class="col-lg-9">
                                    <p class="form-control-static">
                                        {if $custom_field.value}
                                            {if $custom_field.type === 'link'}
                                                <a href="{$custom_field.value|escape:'htmlall':'UTF-8'}"
                                                   target="_blank">{$custom_field.value|escape:'htmlall':'UTF-8'}</a>
                                            {elseif $custom_field.type === 'textarea'}
                                                {$custom_field.value|escape:'htmlall':'UTF-8'|nl2br}
                                            {else}
                                                {$custom_field.value|escape:'htmlall':'UTF-8'}
                                            {/if}
                                        {else}
                                            --
                                        {/if}
                                    </p>
                                </div>
                            </div>
                        {/foreach}
                    {/if}

                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Date created' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$affiliate.date_created|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Last seen' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$affiliate.date_lastseen|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Customer account' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $affiliate.id_customer}
                                    <a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&id_customer={$customer.id|escape:'htmlall':'UTF-8'}&viewcustomer">
                                        <span class="label label-success">
                                            <i class="icon-check"></i>
                                            {$customer.firstname|escape:'htmlall':'UTF-8'}
                                            {$customer.lastname|escape:'htmlall':'UTF-8'}
                                            [{$customer.id|string_format:"%06d"|escape:'htmlall':'UTF-8'}]
                                        </span>
                                    </a>
                                {else}
                                    <span class="label label-danger">
										<i class="icon-remove"></i>
                                        {l s='No customer account' mod='psaffiliate'}
									</span>
                                {/if}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Status' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $affiliate.active}
                                    <span class="label label-success">
										<i class="icon-check"></i>
                                        {l s='Active' mod='psaffiliate'}
									</span>
                                {else}
                                    <span class="label label-danger">
										<i class="icon-remove"></i>
                                        {l s='Inactive' mod='psaffiliate'}
									</span>
                                {/if}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Has been reviewed' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $affiliate.has_been_reviewed}
                                    <span class="label label-success">
										<i class="icon-check"></i>
                                        {l s='Yes' mod='psaffiliate'}
									</span>
                                {else}
                                    <span class="label label-danger">
										<i class="icon-remove"></i>
                                        {l s='No' mod='psaffiliate'}
									</span>
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon icon-list-ul"></i>
                    {l s='Current Rates' mod='psaffiliate'}
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Per click' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{if $affiliate.per_click}{displayPrice price=$affiliate.per_click}{else}{l s='--' mod='psaffiliate'}{/if}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Per unique click' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{if $affiliate.per_unique_click}{displayPrice price=$affiliate.per_unique_click}{else}{l s='--' mod='psaffiliate'}{/if}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Per sale' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{if $affiliate.per_sale}{displayPrice price=$affiliate.per_sale}{else}{l s='--' mod='psaffiliate'}{/if}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Per sale percent' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.per_sale_percent|escape:'htmlall':'UTF-8'}{l s='%' mod='psaffiliate'}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon icon-list-ul"></i>
                    {l s='Summary (last %s days)' mod='psaffiliate' sprintf=$days_current_summary}
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.clicks|escape:'htmlall':'UTF-8'}</p></div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Unique clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.unique_clicks|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Pending sales' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.pending_sales|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Approved sales' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.approved_sales|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Earnings' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{displayPrice price=$affiliate.earnings}</p></div>
                    </div>
                </div>
            </div>
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon icon-list-ul"></i>
                    {l s='Summary (Total)' mod='psaffiliate'}
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.clicks_total|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Unique clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.unique_clicks_total|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Approved sales' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{$affiliate.approved_sales_total|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Total earnings' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{displayPrice price=$affiliate.earnings_total}</p></div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Total payments' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{displayPrice price=$affiliate.payments}</p></div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Pending payments' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p
                                    class="form-control-static">{displayPrice price=$affiliate.pending_payments}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Balance' mod='psaffiliate'}</label>
                        <div class="col-lg-9"><p class="form-control-static">{displayPrice price=$affiliate.balance}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon icon-list-ul"></i>
                    {l s='Campaigns' mod='psaffiliate'}
                    <div class="panel-heading-action">
                        <a class="btn btn-default"
                           href="{$link->getAdminLink('AdminPsaffiliateCampaigns')|escape:'html':'UTF-8'}&amp;aff_campaignsFilter_a!id_affiliate={$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}">
                            <i class="icon-search"></i>
                            {l s='View all' mod='psaffiliate'}
                        </a>
                    </div>
                </div>
                {if isset($campaigns) && $campaigns && count($campaigns)}
                    <table class="table">
                        <thead>
                        <tr>
                            <th><span class="title_box ">{l s='ID' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Name' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Clicks' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Sales' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Earnings' mod='psaffiliate'}</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $campaigns AS $key => $tr}
                            <tr ondblclick="document.location = '{$link->getAdminLink('AdminPsaffiliateCampaigns')|escape:'html':'UTF-8'}&amp;id_campaign={$tr.id_campaign|escape:'htmlall':'UTF-8'}&amp;viewaff_campaigns'">
                                <td>{$tr.id_campaign|escape:'htmlall':'UTF-8'}</td>
                                <td><a target="_blank"
                                       href="{$link->getAdminLink('AdminPsaffiliateCampaigns')|escape:'html':'UTF-8'}&amp;id_campaign={$tr.id_campaign|escape:'htmlall':'UTF-8'}&amp;viewaff_campaigns">{$tr.name|escape:'htmlall':'UTF-8'}</a>
                                </td>
                                <td>{$tr.clicks|escape:'htmlall':'UTF-8'}</td>
                                <td>{$tr.sales|escape:'htmlall':'UTF-8'}</td>
                                <td>{displayPrice price=$tr.total_earnings}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                {else}
                    <p class="text-muted text-center">
                        {l s='This affiliate has no campaigns.' mod='psaffiliate'}
                    </p>
                {/if}
            </div>
        </div>
        {*right*}
        <div class="col-lg-6">
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon icon-dollar"></i>
                    {l s='Rates History' mod='psaffiliate'}
                </div>
                {if isset($rates) && $rates && count($rates)}
                    <table class="table">
                        <thead>
                        <tr>
                            <th><span class="title_box ">{l s='ID' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Date' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Type' mod='psaffiliate'}</span></th>
                            <th><span class="title_box ">{l s='Value' mod='psaffiliate'}</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $rates AS $key => $tr}
                            <tr>
                                <td>{$tr.id_commission|escape:'htmlall':'UTF-8'}</td>
                                <td>{$tr.date|escape:'htmlall':'UTF-8'}</td>
                                <td>
                                    {l s='Per' mod='psaffiliate'}
                                    {if $tr.type == "click"}
                                        {l s='click' mod='psaffiliate'}
                                    {elseif $tr.type == "unique_click"}
                                        {l s='unique click' mod='psaffiliate'}
                                    {elseif $tr.type == "sale"}
                                        {l s='sale' mod='psaffiliate'}
                                    {elseif $tr.type == "sale_percent"}
                                        {l s='sale percent' mod='psaffiliate'}
                                    {/if}
                                    {if $tr.id_affiliate == 0}
                                        {l s='(general)' mod='psaffiliate'}
                                    {/if}
                                </td>
                                <td>
                                    {if $tr.type == "sale_percent"}
                                        {$tr.value|escape:'htmlall':'UTF-8'}{l s='%' mod='psaffiliate'}
                                    {else}
                                        {if $tr.value}{displayPrice price=$tr.value}{else}{l s='--' mod='psaffiliate'}{/if}
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                {else}
                    <p class="text-muted text-center">
                        {l s='No rates details for this affiliate.' mod='psaffiliate'}
                    </p>
                {/if}
            </div>
            {include file="./boxes/traffic.tpl" title={l s='Traffic' mod='psaffiliate'}}
            {include file="./boxes/sales.tpl" title={l s='Sales' mod='psaffiliate'}}
            {include file="./boxes/payments.tpl" title={l s='Payments' mod='psaffiliate'}}
            {include file="./boxes/lifetime_affiliations.tpl" title={l s='Lifetime affiliations' mod='psaffiliate'}}
        </div>
        <div class="col-xs-12">
            <div class="panel clearfix">
                <div class="panel-heading">
                    {l s='Statistics' mod='psaffiliate'}
                </div>
                {include file="./boxes/statistics-datebar.tpl"}
                <input name="id_affiliate" id="id_affiliate" type="hidden"
                       value="{$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}"/>
                <div class="col-lg-6">
                    {include file="./boxes/statistics-traffic.tpl"}
                </div>
                <div class="col-lg-6">
                    {include file="./boxes/statistics-sales.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>