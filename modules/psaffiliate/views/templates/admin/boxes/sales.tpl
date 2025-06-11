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
        <i class="icon icon-money"></i>
        {if !isset($title)}
            {l s='Sales' mod='psaffiliate'}
        {else}
            {$title|escape:'htmlall':'UTF-8'}
        {/if}
        <div class="panel-heading-action">
            <a class="btn btn-default"
               href="{$link->getAdminLink('AdminPsaffiliateSales')|escape:'html':'UTF-8'}{if isset($campaign)}&amp;aff_salesFilter_a!id_campaign={$campaign.id_campaign|escape:'htmlall':'UTF-8'}{elseif isset($affiliate)}&amp;aff_salesFilter_a!id_affiliate={$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}{/if}">
                <i class="icon-search"></i>
                {l s='View all' mod='psaffiliate'}
            </a>
        </div>
    </div>
    {if isset($sales) && $sales && count($sales)}
        <table class="table">
            <thead>
            <tr>
                <th><span class="title_box ">{l s='Sale ID' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Date' mod='psaffiliate'}</span></th>
                {if !isset($affiliate)}
                    <th><span class="title_box ">{l s='Affiliate' mod='psaffiliate'}</span></th>
                {/if}
                <th><span class="title_box ">{l s='Commission' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Order total' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Approved' mod='psaffiliate'}</span></th>
            </tr>
            </thead>
            <tbody>
            {foreach $sales AS $key => $tr}
                <tr ondblclick="document.location = '{$link->getAdminLink('AdminPsaffiliateSales')|escape:'html':'UTF-8'}&amp;id_sale={$tr.id_sale|escape:'htmlall':'UTF-8'}&amp;updateaff_sales'">
                    <td>{$tr.id_sale|escape:'htmlall':'UTF-8'}</td>
                    <td>
                        <a target="_blank"
                           href="{$link->getAdminLink('AdminPsaffiliateSales')|escape:'html':'UTF-8'}&amp;id_sale={$tr.id_sale|escape:'htmlall':'UTF-8'}&amp;updateaff_sales">
                            {dateFormat date=$tr.date full=1}
                        </a>
                    </td>
                    {if !isset($affiliate)}
                        <td>
                            <a target="_blank"
                               href="{$link->getAdminLink('AdminPsaffiliateAffiliates')|escape:'html':'UTF-8'}&amp;id_affiliate={$tr.id_affiliate|escape:'htmlall':'UTF-8'}&amp;viewaff_affiliates">
                                {$tr.affiliate_name|escape:'htmlall':'UTF-8'}
                            </a>
                        </td>
                    {/if}
                    <td>{if $tr.commission}{displayPrice price=$tr.commission}{else}{l s='--' mod='psaffiliate'}{/if}</td>
                    <td>{if $tr.order_total}{displayPrice price=$tr.order_total}{else}{l s='--' mod='psaffiliate'}{/if}</td>
                    <td>
                        {if $tr.approved}
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
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p class="text-muted text-center">
            {l s='No sales details' mod='psaffiliate'}
        </p>
    {/if}
</div>