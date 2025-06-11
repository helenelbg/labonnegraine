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
        <i class="icon icon-group"></i>
        {if !isset($title)}
            {l s='Lifetime affiliations' mod='psaffiliate'}
        {else}
            {$title|escape:'htmlall':'UTF-8'}
        {/if}
    </div>
    {if isset($lifetime_affiliations) && $lifetime_affiliations && count($lifetime_affiliations)}
        <table class="table">
            <thead>
            <tr>
                <th><span class="title_box ">{l s='Date' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Customer' mod='psaffiliate'}</span></th>
                <th><span class="title_box ">{l s='Total commission' mod='psaffiliate'}</span></th>
            </tr>
            </thead>
            <tbody>
            {foreach $lifetime_affiliations AS $key => $tr}
                <tr>
                    <td>{dateFormat date=$tr.date full=1}</td>
                    <td>
                        <a target="_blank"
                           href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&amp;id_customer={$tr.id_customer|escape:'htmlall':'UTF-8'}&amp;viewcustomer">
                            {$tr.customer_name|escape:'htmlall':'utf-8'}
                        </a>
                    </td>
                    <td>{Tools::displayPrice($tr.commission)|escape:'htmlall':'utf-8'}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p class="text-muted text-center">
            {l s='This affiliate has not lifetime affiliations' mod='psaffiliate'}
        </p>
    {/if}
</div>