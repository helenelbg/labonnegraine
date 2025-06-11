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
        <i class="icon icon-mail-forward"></i>
        {if !isset($title)}
            {l s='Traffic' mod='psaffiliate'}
        {else}
            {$title|escape:'htmlall':'UTF-8'}
        {/if}
        <div class="panel-heading-action">
            <a class="btn btn-default"
               href="{$link->getAdminLink('AdminPsaffiliateTraffic')|escape:'html':'UTF-8'}{if isset($campaign)}&amp;aff_trackingFilter_a!id_campaign={$campaign.id_campaign|escape:'htmlall':'UTF-8'}{elseif isset($affiliate)}&amp;aff_trackingFilter_a!id_affiliate={$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}{/if}">
                <i class="icon-search"></i>
                {l s='View all' mod='psaffiliate'}
            </a>
        </div>
    </div>
    {if isset($traffic) && $traffic && count($traffic)}
        <table class="table">
            <thead>
            <tr>
                <th><span class="title_box ">{l s='ID' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Date' mod='psaffiliate'}</span></th>
                {if !isset($affiliate)}
                    <th><span class="title_box ">{l s='Affiliate' mod='psaffiliate'}</span></th>
                {/if}
                <th><span class="title_box ">{l s='Referral URL' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='URL' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Comission' mod='psaffiliate'}</span></th>
            </tr>
            </thead>
            <tbody>
            {foreach $traffic AS $key => $tr}
                <tr ondblclick="document.location = '{$link->getAdminLink('AdminPsaffiliateTraffic')|escape:'html':'UTF-8'}&amp;id_tracking={$tr.id_tracking|escape:'htmlall':'UTF-8'}&amp;updateaff_tracking'">
                    <td>{$tr.id_tracking|escape:'htmlall':'UTF-8'}</td>
                    <td>
                        <a target="_blank"
                           href="{$link->getAdminLink('AdminPsaffiliateTraffic')|escape:'html':'UTF-8'}&amp;id_tracking={$tr.id_tracking|escape:'htmlall':'UTF-8'}&amp;updateaff_tracking">
                            {dateFormat date=$tr.date full=1}
                        </a>
                    </td>
                    {if !isset($affiliate)}
                        <td>
                            <a target="_blank"
                               href="{$link->getAdminLink('AdminPsaffiliateAffiliates')|escape:'html'}&amp;id_affiliate={$tr.id_affiliate|escape:'htmlall':'UTF-8'}&amp;viewaff_affiliates">
                                {$tr.affiliate_name|escape:'htmlall':'UTF-8'}
                            </a>
                        </td>
                    {/if}
                    <td>{if $tr.referral}<a href="{$tr.referral|escape:'htmlall':'UTF-8'}"
                                            title="{$tr.referral|escape:'htmlall':'UTF-8'}">{$tr.referral|escape:'htmlall':'UTF-8'|truncate:30}</a>{else}{l s='--' mod='psaffiliate'}{/if}
                    </td>
                    <td>{if $tr.url}<a href="{$tr.url|escape:'htmlall':'UTF-8'}"
                                       title="{$tr.url|escape:'htmlall':'UTF-8'}">{$tr.url|escape:'htmlall':'UTF-8'|truncate:30}</a>{else}{l s='--' mod='psaffiliate'}{/if}
                    </td>
                    <td>{if $tr.commission}{displayPrice price=$tr.commission|escape:'htmlall':'UTF-8'}{else}{l s='--' mod='psaffiliate'}{/if}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p class="text-muted text-center">
            {l s='No traffic details.' mod='psaffiliate'}
        </p>
    {/if}
</div>