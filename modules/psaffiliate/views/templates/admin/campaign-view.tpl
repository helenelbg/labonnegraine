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
<div id="container-campaign-view">
    <div class="row">
        {*left*}
        <div class="col-lg-6">
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon-user"></i> {l s='Campaign #%s' sprintf=$campaign.id_campaign mod='psaffiliate'}
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Campaign ID' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.id_campaign|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Affiliate' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static"><a
                                        href="{$link->getAdminLink('AdminPsaffiliateAffiliates')|escape:'html':'UTF-8'}&amp;id_affiliate={$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}&amp;viewaff_affiliates"
                                        target="_blank">{$affiliate.firstname|escape:'htmlall':'UTF-8'} {$affiliate.lastname|escape:'htmlall':'UTF-8'}</a>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Name' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.name|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Description' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.description|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Date created' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.date_created|date_format:"%D %T"|escape:'html':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Date last earnings' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.date_lastactive|date_format:"%D %T"|escape:'html':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Unique clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.unique_clicks|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.clicks|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Approved sales' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.sales|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Total sales' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$campaign.sales_total|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Earnings from clicks' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{displayPrice price=$campaign.total_earnings_clicks}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Earnings from sales' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{displayPrice price=$campaign.total_earnings_sales}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Total earnings' mod='psaffiliate'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{displayPrice price=$campaign.total_earnings}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {*right*}
        <div class="col-lg-6">
            {include file="./boxes/traffic.tpl"}
            {include file="./boxes/sales.tpl"}
        </div>
        <div class="col-xs-12">
            <div class="panel clearfix">
                <div class="panel-heading">
                    {l s='Statistics' mod='psaffiliate'}
                </div>
                {include file="./boxes/statistics-datebar.tpl"}
                <input name="id_campaign" id="id_campaign" type="hidden"
                       value="{$campaign.id_campaign|escape:'htmlall':'UTF-8'}"/>
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