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
<div id="container-psaffiliate-dashboard">
    <div class="row">
        <div class="col-lg-6">
            {*left*}
            {include file="./boxes/traffic.tpl" title={l s='Last traffic' mod='psaffiliate'}}
            {include file="./boxes/sales.tpl" title={l s='Last sales' mod='psaffiliate'}}
            {include file="./boxes/payments.tpl" title={l s='Last payments' mod='psaffiliate'}}
        </div>
        <div class="col-lg-6">
            {*right*}
            {include file="./boxes/affiliates.tpl" title={l s='Last affiliates registered' mod='psaffiliate'} affiliates=$last_affiliates}
            {include file="./boxes/affiliates.tpl" title={l s='Best affiliates' mod='psaffiliate'} affiliates=$best_affiliates}
            {include file="./boxes/campaigns.tpl" title={l s='Best campaigns' mod='psaffiliate'}}
        </div>
        <div class="col-xs-12">
            <div class="panel clearfix">
                <div class="panel-heading">
                    {l s='Statistics' mod='psaffiliate'}
                    <div class="panel-heading-action">
                        <a class="btn btn-default"
                           href="{$link->getAdminLink('AdminPsaffiliateStatistics')|escape:'html':'UTF-8'}">
                            <i class="icon-search"></i>
                            {l s='View all' mod='psaffiliate'}
                        </a>
                    </div>
                </div>
                {include file="./boxes/statistics-datebar.tpl"}
                <div class="col-lg-6">
                    {include file="./boxes/statistics-traffic.tpl"}
                </div>
                <div class="col-lg-6">
                    {include file="./boxes/statistics-sales.tpl"}
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        {include file="$discover_tpl.tpl"}
    </div>
</div>