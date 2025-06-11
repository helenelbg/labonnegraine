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
    <div id="requestapayment">
        <div class="card">
            <div class="card-header">
                <h2 class="h3 m-t-sm m-b-sm">{l s='Request a payment' mod='psaffiliate'}</h2>
            </div>
            <div class="card-block">
                {if isset($success)}
                    {if $success}
                        <div class="alert alert-success">{l s='Success! Your payment request has been submitted.' mod='psaffiliate'}</div>
                    {else}
                        <div class="alert alert-warning">{l s='Error! Please try again later.' mod='psaffiliate'}</div>
                    {/if}
                {else}
                    {if $affiliate.balance >= $minimum_payment_amount}
                        <form class="requestapayment-form form-horizontal" method="POST" enctype="multipart/form-data">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3"
                                       for="payment_method">{l s='Available balance' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{Tools::displayPrice($affiliate.balance, $default_currency)|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3"
                                       for="payment_method">{l s='Payment method' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <select name="payment_method" class="form-control">
                                        <option value="0">-</option>
                                        {foreach from=$payment_methods item=payment_method}
                                            <option value="{$payment_method.id_payment_method|escape:'htmlall':'UTF-8'}">{$payment_method.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3" for="amount">
                                    {l
                                    s='Amount requested (%iso%)'
                                    sprintf=[
                                    '%iso%' => $currency_iso
                                    ]
                                    d='Modules.Psaffiliate.Shop'
                                    }
                                </label>
                                <div class="col-sm-5 col-md-4">
                                    <input type="text" name="amount" class="form-control"
                                           value="{number_format($affiliate.balance, 2, '.', '')|escape:'htmlall':'UTF-8'}"
                                           data-min="{$minimum_payment_amount|escape:'htmlall':'UTF-8'}"
                                           data-max="{number_format($affiliate.balance, 2)|escape:'htmlall':'UTF-8'}"/>
                                </div>
                            </div>

                            {if $invoicing_details}
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="invoice">
                                        {l s='Invocing details' mod='psaffiliate'}
                                    </label>
                                    <div class="col-sm-5 col-md-7">
                                        <div class="legacy-well">
                                            {$invoicing_details|nl2br nofilter} {* HTML CODE *}
                                        </div>
                                    </div>
                                </div>
                            {/if}

                            {if $invoices_enabled}
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-3" for="invoice">
                                        {l s='Upload invoice' mod='psaffiliate'}
                                    </label>
                                    <div class="col-sm-5 col-md-4">
                                        <input type="file" name="invoice" class="form-control"
                                               {if $invoices_mandatory}required{/if}>
                                        <p class="small form-text text-muted">
                                            {l s='.pdf and .zip files only' mod='psaffiliate'}
                                        </p>
                                    </div>
                                </div>
                            {/if}

                            <div class="paymentmethodfields-container">
                                {foreach from=$payment_methods item=payment_method}
                                    <div class="fields-{$payment_method.id_payment_method|escape:'htmlall':'UTF-8'} fields"
                                         style="display: none;">
                                        {foreach from=$payment_method.fields item=field}
                                            <div class="form-group row clearfix">
                                                <label class="control-label col-sm-3"
                                                       for="amount">{$field.field_name|escape:'htmlall':'UTF-8'}</label>
                                                <div class="col-sm-5 col-md-4">
                                                    <input type="text"
                                                           name="paymentmethodfield_{$field.id_payment_method_field|escape:'htmlall':'UTF-8'}"
                                                           class="form-control" value=""/>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                {/foreach}
                            </div>
                            <input type="hidden" name="submitRequestpayment" value="1"/>
                            <div class="row">
                                <div class="offset-sm-3 col-sm-5 col-md-4 m-t">
                                    <button type="submit"
                                            class="btn btn-success btn-lg btn-block">{l s='Request payment' mod='psaffiliate'}</button>
                                </div>
                            </div>
                        </form>
                    {else}
                        {l s='You still need another %s to reach the minimum payment amount.' mod='psaffiliate' sprintf=[{Tools::displayPrice($minimum_payment_amount-$affiliate.balance)}]}
                    {/if}
                {/if}
            </div>
        </div>
    </div>
{/block}