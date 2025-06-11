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
<div class="panel clearfix">
    <div class="panel-heading">
        <i class="icon icon-list-ul"></i>
        {if !isset($title)}
            {l s='Campaigns' mod='psaffiliate'}
        {else}
            {$title|escape:'htmlall':'UTF-8'}
        {/if}
        <div class="panel-heading-action">
            <a class="btn btn-default" href="{$link->getAdminLink('AdminPsaffiliateCampaigns')|escape:'html':'UTF-8'}">
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
                <th><span class="title_box ">{l s='Affiliate' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Total clicks' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Sales' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Overall income' mod='psaffiliate'}</span></th>
            </tr>
            </thead>
            <tbody>
            {foreach $campaigns AS $key => $tr}
                <tr ondblclick="document.location = '{$link->getAdminLink('AdminPsaffiliateCampaigns')|escape:'html':'UTF-8'}&amp;id_campaign={$tr.id_campaign|escape:'htmlall':'UTF-8'}&amp;viewaff_campaigns'">
                    <td>{$tr.id_campaign|escape:'htmlall':'UTF-8'}</td>
                    <td>
                        <a target="_blank"
                           href="{$link->getAdminLink('AdminPsaffiliateCampaigns')|escape:'html':'UTF-8'}&amp;id_campaign={$tr.id_campaign|escape:'htmlall':'UTF-8'}&amp;viewaff_campaigns">
                            {$tr.name|escape:'htmlall':'UTF-8'}
                        </a>
                    </td>
                    <td>
                        <a target="_blank"
                           href="{$link->getAdminLink('AdminPsaffiliateAffiliates')|escape:'html':'UTF-8'}&amp;id_affiliate={$tr.id_affiliate|escape:'htmlall':'UTF-8'}&amp;viewaff_affiliates">
                            {$tr.idandname|escape:'htmlall':'UTF-8'}
                        </a>
                    </td>
                    <td>
                        {$tr.total_clicks|escape:'htmlall':'UTF-8'}
                    </td>
                    <td>
                        {$tr.sales|escape:'htmlall':'UTF-8'}
                    </td>
                    <td>
                        {if $tr.overall_commission}
                            {displayPrice price=$tr.overall_commission}
                        {else}
                            --
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p class="text-muted text-center">
            {l s='No campaigns details' mod='psaffiliate'}
        </p>
    {/if}
</div>