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
<div class="col-lg-12">
    <div class="panel" id="psaffiliate_customer_view">
        <div class="panel-heading"><i class="icon-group"></i> {l s='Affiliation info' mod='psaffiliate'}</div>
        <div class="form-horizontal">
            <div class="row">
                <label class="control-label col-lg-3">{l s='Is affiliate?' mod='psaffiliate'}</label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {if $id_affiliate}
                            <span class="label label-success"><i class="icon-check"></i>
                                {l s='Yes' mod='psaffiliate'}</span>
                            <a target="_blank"
                               href="?tab=AdminPsaffiliateAffiliates&amp;id_affiliate={$id_affiliate|intval}&amp;viewaff_affiliates&amp;token={getAdminToken tab='AdminPsaffiliateAffiliates'}">{l s='View affiliate account' mod='psaffiliate'}</a>
                        {else}
                            <span class="label label-danger"><i
                                        class="icon-remove"></i> {l s='No' mod='psaffiliate'}</span>
                        {/if}
                    </p>
                </div>
            </div>
            <div class="row">
                <label class="control-label col-lg-3">{l s='Is lifetime affiliated?' mod='psaffiliate'}</label>
                <div class="col-lg-9">
                    <p class="form-control-static">
                        {if $lifetime_affiliate_id}
                            <span class="label label-success"><i class="icon-check"></i>
                                {l s='Yes' mod='psaffiliate'}</span>
                            {l s='with' mod='psaffiliate'}
                            <a target="_blank"
                               href="?tab=AdminPsaffiliateAffiliates&amp;id_affiliate={$lifetime_affiliate_id|intval}&amp;viewaff_affiliates&amp;token={getAdminToken tab='AdminPsaffiliateAffiliates'}">{$lifetime_affiliate_name|escape:'htmlall':'utf-8'}</a>
                        {else}
                            <span class="label label-danger"><i
                                        class="icon-remove"></i> {l s='No' mod='psaffiliate'}</span>
                        {/if}
                    </p>
                </div>
            </div>
            <div class="row">
                {if sizeof($commissions_generated)}
                    <h4>{l s='Generated commissions - last 100' mod='psaffiliate'}</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{l s='Type' mod='psaffiliate'}</th>
                            <th>{l s='Date' mod='psaffiliate'}</th>
                            <th>{l s='Affiliate' mod='psaffiliate'}</th>
                            <th>{l s='Commission' mod='psaffiliate'}</th>
                            <th>{l s='Approved' mod='psaffiliate'}</th>
                            <th>{l s='View' mod='psaffiliate'}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$commissions_generated item=row}
                            <tr>
                                <td>
                                    {if $row.type == 'tracking'}
                                        {l s='Click' mod='psaffiliate'}
                                    {else}
                                        {l s='Sale' mod='psaffiliate'}
                                    {/if}
                                </td>
                                <td>{$row.date|escape:'htmlall':'utf-8'}</td>
                                <td><a target="_blank"
                                       href="?tab=AdminPsaffiliateAffiliates&amp;id_affiliate={$row.id_affiliate|intval}&amp;viewaff_affiliates&amp;token={getAdminToken tab='AdminPsaffiliateAffiliates'}">{$row.affiliate_name}</a>
                                </td>
                                <td>{displayPrice price=$row.commission|escape:'htmlall':'utf-8'}</td>
                                <td>
                                    {if $row.approved}
                                        <span class="label label-success"><i
                                                    class="icon-check"></i> {l s='Yes' mod='psaffiliate'}</span>
                                    {else}
                                        <span class="label label-danger"><i
                                                    class="icon-remove"></i> {l s='No' mod='psaffiliate'}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $row.type == 'tracking'}
                                        <a target="_blank"
                                           href="?tab=AdminPsaffiliateTraffic&amp;id_tracking={$row.id|intval}&amp;updateaff_tracking&amp;token={getAdminToken tab='AdminPsaffiliateTraffic'}">{l s='View' mod='psaffiliate'}</a>
                                    {else}
                                        <a target="_blank"
                                           href="?tab=AdminPsaffiliateSales&amp;id_sale={$row.id|intval}&amp;updateaff_sales&amp;token={getAdminToken tab='AdminPsaffiliateSales'}">{l s='View' mod='psaffiliate'}</a>
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
</div>