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
<div id="requestapayment">
    {capture name=path}
        <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='psaffiliate'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
        <a href="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)|escape:'html':'UTF-8'}">{l s='My affiliate account' mod='psaffiliate'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
        <span>{l s='Request a voucher payment' mod='psaffiliate'}</span>
    {/capture}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="h3 m-t-sm m-b-sm">{l s='Request a voucher payment' mod='psaffiliate'}</h2>
        </div>
        <div class="panel-body">
            {include file="$tpl_dir./errors.tpl"}
            {if isset($success)}
                {if $success}
                    <div class="alert alert-success">{l s='Success! Your payment request has been submitted.' mod='psaffiliate'}</div>
                {else}
                    <div class="alert alert-warning">{l s='Error! Please try again later.' mod='psaffiliate'}</div>
                {/if}
            {else}
                {if $for_affiliates_only}
                    <div class="alert alert-info">
                        <i class="icon-info-circle"></i>
                        {l s='The voucher may only be used by this account.' mod='psaffiliate'}
                    </div>
                {/if}
                <form class="requestapayment-form form-horizontal" method="POST">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3"
                               for="payment_method">{l s='Available balance' mod='psaffiliate'}</label>
                        <div class="col-sm-5 col-md-4">
                            <div class="form-control-static">{displayPrice price=$affiliate.balance currency=$default_currency}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3" for="amount">
                            {l s='Amount requested (%s)' mod='psaffiliate' sprintf=$currency_iso}
                        </label>
                        <div class="col-sm-5 col-md-4">
                            <input type="text" name="amount" id="psaff_voucher_amount" class="form-control"
                                   value="{number_format($affiliate.balance, 2)|escape:'htmlall':'UTF-8'}"
                                   data-min="{$minimum_payment_amount|escape:'htmlall':'UTF-8'}"
                                   data-max="{number_format($affiliate.balance, 2)|escape:'htmlall':'UTF-8'}"
                                   data-exchange-rate="{$vouchers_exchange_rate|escape:'htmlall':'UTF-8'}"/>
                        </div>
                    </div>
                    {if $vouchers_exchange_rate && $vouchers_exchange_rate != 1}
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="amount">
                                {l s='Amount received (%s)' mod='psaffiliate' sprintf=$currency_iso}
                            </label>
                            <div class="col-sm-5 col-md-4">
                                <input type="text" name="amount_final" id="psaff_voucher_amount_final"
                                       class="form-control"
                                       value="{number_format($affiliate.balance * $vouchers_exchange_rate, 2)|escape:'htmlall':'UTF-8'}"
                                       readonly>
                                <p class="help-block">
                                    <small>{l s='Based on voucher exchange rate.' mod='psaffiliate'} {l s='Read-only field.' mod='psaffiliate'}</small>
                                </p>
                            </div>
                        </div>
                    {/if}
                    <input type="hidden" name="submitRequestvoucherpayment" value="1"/>
                    <div class="row">
                        <div class="offset-sm-3 col-sm-5 col-md-4 m-t">
                            <button type="submit"
                                    class="btn btn-success btn-lg btn-block">{l s='Request voucher payment' mod='psaffiliate'}</button>
                        </div>
                    </div>
                </form>
            {/if}
        </div>
    </div>
</div>