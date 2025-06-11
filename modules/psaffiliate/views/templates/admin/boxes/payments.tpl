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
            {l s='Payments' mod='psaffiliate'}
        {else}
            {$title|escape:'htmlall':'UTF-8'}
        {/if}
        <div class="panel-heading-action">
            <a class="btn btn-default"
               href="{$link->getAdminLink('AdminPsaffiliatePayments')|escape:'html':'UTF-8'}{if isset($affiliate)}&amp;aff_paymentsFilter_a!id_affiliate={$affiliate.id_affiliate|escape:'htmlall':'UTF-8'}{/if}">
                <i class="icon-search"></i>
                {l s='View all' mod='psaffiliate'}
            </a>
        </div>
    </div>
    {if isset($payments) && $payments && count($payments)}
        <table class="table">
            <thead>
            <tr>
                <th><span class="title_box ">{l s='ID' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Date' mod='psaffiliate'}</span></th>
                {if !isset($affiliate)}
                    <th><span class="title_box ">{l s='Affiliate' mod='psaffiliate'}</span></th>
                {/if}
                <th><span class="title_box ">{l s='Amount' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Approved' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Paid' mod='psaffiliate'}</span></th>
            </tr>
            </thead>
            <tbody>
            {foreach $payments AS $key => $tr}
                <tr ondblclick="document.location = '{$link->getAdminLink('AdminPsaffiliatePayments')|escape:'html':'UTF-8'}&amp;id_payment={$tr.id_payment|escape:'htmlall':'UTF-8'}&amp;updateaff_payments'">
                    <td>{$tr.id_payment|escape:'htmlall':'UTF-8'}</td>
                    <td>
                        <a target="_blank"
                           href="{$link->getAdminLink('AdminPsaffiliatePayments')|escape:'html':'UTF-8'}&amp;id_payment={$tr.id_payment|escape:'htmlall':'UTF-8'}&amp;updateaff_payments">
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
                    <td>{if $tr.amount}{displayPrice price=$tr.amount}{else}{l s='--' mod='psaffiliate'}{/if}</td>
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
                    <td>
                        {if $tr.paid}
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
            {l s='No payments data.' mod='psaffiliate'}
        </p>
    {/if}
</div>