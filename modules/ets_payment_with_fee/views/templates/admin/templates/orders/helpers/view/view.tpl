{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
  <script type="text/javascript">
  var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|addslashes|escape:'html':'UTF-8'}";
  var id_order = {$order->id|intval};
  var id_lang = {$current_id_lang|intval};
  var id_currency = {$order->id_currency|intval};
  var id_customer = {$order->id_customer|intval};
  {assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
  var id_address = {$order->$PS_TAX_ADDRESS_TYPE|escape:'html':'UTF-8'};
  var currency_sign = "{$currency->sign|escape:'html':'UTF-8'}";
  var currency_format = "{$currency->format|escape:'html':'UTF-8'}";
  var currency_blank = "{$currency->blank|escape:'html':'UTF-8'}";
  var priceDisplayPrecision = {$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval};
  var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
  var stock_management = {$stock_management|intval};
  var txt_add_product_stock_issue = "{l s='Are you sure you want to add this quantity?' mod='ets_payment_with_fee' js=1}";
  var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' mod='ets_payment_with_fee' js=1}";
  var txt_add_product_no_product = "{l s='Error: No product has been selected' mod='ets_payment_with_fee' js=1}";
  var txt_add_product_no_product_quantity = "{l s='Error: Quantity of products must be set' mod='ets_payment_with_fee' js=1}";
  var txt_add_product_no_product_price = "{l s='Error: Product price must be set' mod='ets_payment_with_fee' js=1}";
  var txt_confirm = "{l s='Are you sure?' mod='ets_payment_with_fee' js=1}";
  var statesShipped = new Array();
  var has_voucher = {if count($discounts)}1{else}0{/if};
  {foreach from=$states item=state}
    {if (isset($currentState->shipped) && !$currentState->shipped && $state['shipped'])}
      statesShipped.push({$state['id_order_state']|escape:'html':'UTF-8'});
    {/if}
  {/foreach}
  var order_discount_price = {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                  {$order->total_discounts_tax_excl|escape:'html':'UTF-8'}
                {else}
                  {$order->total_discounts_tax_incl|escape:'html':'UTF-8'}
                {/if};

  var errorRefund = "{l s='Error. You cannot refund a negative amount.' mod='ets_payment_with_fee'}";
  </script>

  {assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
  {if ($hook_invoice)}
  <div>{$hook_invoice nofilter}</div>
  {/if}

  <div class="panel kpi-container">
    <div class="row">
      <div class="col-xs-6 col-sm-3 box-stats color3" >
        <div class="kpi-content">
          <i class="icon-calendar-empty"></i>
          <span class="title">{l s='Date' mod='ets_payment_with_fee'}</span>
          <span class="value">{dateFormat date=$order->date_add full=false}</span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3 box-stats color4" >
        <div class="kpi-content">
          <i class="icon-money"></i>
          <span class="title">{l s='Total' mod='ets_payment_with_fee'}</span>
          <span class="value">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3 box-stats color2" >
        <div class="kpi-content">
          <i class="icon-comments"></i>
          <span class="title">{l s='Messages' mod='ets_payment_with_fee'}</span>
          <span class="value"><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">{sizeof($customer_thread_message)|escape:'html':'UTF-8'}</a></span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3 box-stats color1" >
        <a href="#start_products">
          <div class="kpi-content">
            <i class="icon-book"></i>
            <span class="title">{l s='Products' mod='ets_payment_with_fee'}</span>
            <span class="value">{sizeof($products)|escape:'html':'UTF-8'}</span>
          </div>
        </a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-7">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-credit-card"></i>
          {l s='Order' mod='ets_payment_with_fee'}
          <span class="badge">{$order->reference|escape:'html':'UTF-8'}</span>
          <span class="badge">{l s='#' mod='ets_payment_with_fee'}{$order->id|intval}</span>
          <div class="panel-heading-action">
            <div class="btn-group">
              <a class="btn btn-default{if !$previousOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$previousOrder|intval}">
                <i class="icon-backward"></i>
              </a>
              <a class="btn btn-default{if !$nextOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$nextOrder|intval}">
                <i class="icon-forward"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- Orders Actions -->
        <div class="well hidden-print">
          <a class="btn btn-default" href="javascript:window.print()">
            <i class="icon-print"></i>
            {l s='Print order' mod='ets_payment_with_fee'}
          </a>
          &nbsp;
          {if Configuration::get('PS_INVOICE') && count($invoices_collection) && $order->invoice_number}
            <a data-selenium-id="view_invoice" class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order={$order->id|intval}">
              <i class="icon-file"></i>
              {l s='View invoice' mod='ets_payment_with_fee'}
            </a>
          {else}
            <span class="span label label-inactive">
              <i class="icon-remove"></i>
              {l s='No invoice' mod='ets_payment_with_fee'}
            </span>
          {/if}
          &nbsp;
          {if $order->delivery_number}
            <a class="btn btn-default _blank"  href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id|intval}">
              <i class="icon-truck"></i>
              {l s='View delivery slip' mod='ets_payment_with_fee'}
            </a>
          {else}
            <span class="span label label-inactive">
              <i class="icon-remove"></i>
              {l s='No delivery slip' mod='ets_payment_with_fee'}
            </span>
          {/if}
          &nbsp;
          {if Configuration::get('PS_ORDER_RETURN')}
            <a id="desc-order-standard_refund" class="btn btn-default" href="#refundForm">
              <i class="icon-exchange"></i>
              {if $order->hasBeenShipped()}
                {l s='Return products' mod='ets_payment_with_fee'}
              {elseif $order->hasBeenPaid()}
                {l s='Standard refund' mod='ets_payment_with_fee'}
              {else}
                {l s='Cancel products' mod='ets_payment_with_fee'}
              {/if}
            </a>
            &nbsp;
          {/if}
          {if $order->hasInvoice()}
            <a id="desc-order-partial_refund" class="btn btn-default" href="#refundForm">
              <i class="icon-exchange"></i>
              {l s='Partial refund' mod='ets_payment_with_fee'}
            </a>
          {/if}
          {hook h='displayBackOfficeOrderActions' id_order=$order->id|intval}
        </div>
        <!-- Tab nav -->
        <ul class="nav nav-tabs" id="tabOrder">
          {$HOOK_TAB_ORDER nofilter}
          <li class="active">
            <a href="#status">
              <i class="icon-time"></i>
              {l s='Status' mod='ets_payment_with_fee'} <span class="badge">{$history|@count|escape:'html':'UTF-8'}</span>
            </a>
          </li>
          <li>
            <a href="#documents">
              <i class="icon-file-text"></i>
              {l s='Documents' mod='ets_payment_with_fee'} <span class="badge">{$order->getDocuments()|@count|escape:'html':'UTF-8'}</span>
            </a>
          </li>
        </ul>
        <!-- Tab content -->
        <div class="tab-content panel">
          {$HOOK_CONTENT_ORDER nofilter}
          <!-- Tab status -->
          <div class="tab-pane active" id="status">
            <h4 class="visible-print">{l s='Status' mod='ets_payment_with_fee'} <span class="badge">({$history|@count|escape:'html':'UTF-8'})</span></h4>
            <!-- History of status -->
            <div class="table-responsive">
              <table class="table history-status row-margin-bottom">
                <tbody>
                  {foreach from=$history item=row key=key}
                    {if ($key == 0)}
                      <tr>
                        <td style="background-color:{$row['color']|escape:'html':'UTF-8'}"><img src="../img/os/{$row['id_order_state']|intval}.gif" width="16" height="16" alt="{$row['ostate_name']|stripslashes|escape:'html':'UTF-8'}" /></td>
                        <td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">{$row['ostate_name']|stripslashes|escape:'html':'UTF-8'}</td>
                        <td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes|escape:'html':'UTF-8'} {$row['employee_lastname']|stripslashes|escape:'html':'UTF-8'}{/if}</td>
                        <td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">{dateFormat date=$row['date_add'] full=true}</td>
                        <td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}" class="text-right">
                          {if $row['send_email']|intval}
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer' mod='ets_payment_with_fee'}">
                              <i class="icon-mail-reply"></i>
                              {l s='Resend email' mod='ets_payment_with_fee'}
                            </a>
                          {/if}
                        </td>
                      </tr>
                    {else}
                      <tr>
                        <td><img src="../img/os/{$row['id_order_state']|intval}.gif" width="16" height="16" /></td>
                        <td>{$row['ostate_name']|stripslashes|escape:'html':'UTF-8'}</td>
                        <td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes|escape:'html':'UTF-8'} {$row['employee_lastname']|stripslashes|escape:'html':'UTF-8'}{else}&nbsp;{/if}</td>
                        <td>{dateFormat date=$row['date_add'] full=true}</td>
                        <td class="text-right">
                          {if $row['send_email']|intval}
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer' mod='ets_payment_with_fee'}">
                              <i class="icon-mail-reply"></i>
                              {l s='Resend email' mod='ets_payment_with_fee'}
                            </a>
                          {/if}
                        </td>
                      </tr>
                    {/if}
                  {/foreach}
                </tbody>
              </table>
            </div>
            <!-- Change status form -->
            <form action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" class="form-horizontal well hidden-print">
              <div class="row">
                <div class="col-lg-9">
                  <select id="id_order_state" class="chosen form-control" name="id_order_state">
                  {foreach from=$states item=state}
                    <option value="{$state['id_order_state']|intval}"{if isset($currentState) && $state['id_order_state'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$state['name']|escape}</option>
                  {/foreach}
                  </select>
                  <input type="hidden" name="id_order" value="{$order->id|intval}" />
                </div>
                <div class="col-lg-3">
                  <button type="submit" name="submitState" class="btn btn-primary">
                    {l s='Update status' mod='ets_payment_with_fee'}
                  </button>
                </div>
              </div>
            </form>
          </div>
          <!-- Tab documents -->
          <div class="tab-pane" id="documents">
            <h4 class="visible-print">{l s='Documents' mod='ets_payment_with_fee'} <span class="badge">({$order->getDocuments()|@count|escape:'html':'UTF-8'})</span></h4>
            {* Include document template *}
            {include file='controllers/orders/_documents.tpl'}
          </div>
        </div>
        <script>
          $('#tabOrder a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
          })
        </script>
        <hr />
        <!-- Tab nav -->
        <ul class="nav nav-tabs" id="myTab">
          {$HOOK_TAB_SHIP nofilter}
          <li class="active">
            <a href="#shipping">
              <i class="icon-truck "></i>
              {l s='Shipping' mod='ets_payment_with_fee'} <span class="badge">{$order->getShipping()|@count|escape:'html':'UTF-8'}</span>
            </a>
          </li>
          <li>
            <a href="#returns">
              <i class="icon-undo"></i>
              {l s='Merchandise Returns' mod='ets_payment_with_fee'} <span class="badge">{$order->getReturn()|@count|escape:'html':'UTF-8'}</span>
            </a>
          </li>
        </ul>
        <!-- Tab content -->
        <div class="tab-content panel">
        {$HOOK_CONTENT_SHIP nofilter}
          <!-- Tab shipping -->
          <div class="tab-pane active" id="shipping">
            <h4 class="visible-print">{l s='Shipping' mod='ets_payment_with_fee'} <span class="badge">({$order->getShipping()|@count|escape:'html':'UTF-8'})</span></h4>
            <!-- Shipping block -->
            {if !$order->isVirtual()}
            <div class="form-horizontal">
              {if $order->gift_message}
              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Message' mod='ets_payment_with_fee'}</label>
                <div class="col-lg-9">
                  <p class="form-control-static">{$order->gift_message|nl2br|escape:'html':'UTF-8'}</p>
                </div>
              </div>
              {/if}
              {include file='controllers/orders/_shipping.tpl'}
              {if $carrierModuleCall}
                {$carrierModuleCall|escape:'html':'UTF-8'}
              {/if}
              <hr />
              {if $order->recyclable}
                <span class="label label-success"><i class="icon-check"></i> {l s='Recycled packaging' mod='ets_payment_with_fee'}</span>
              {else}
                <span class="label label-inactive"><i class="icon-remove"></i> {l s='Recycled packaging' mod='ets_payment_with_fee'}</span>
              {/if}

              {if $order->gift}
                <span class="label label-success"><i class="icon-check"></i> {l s='Gift wrapping' mod='ets_payment_with_fee'}</span>
              {else}
                <span class="label label-inactive"><i class="icon-remove"></i> {l s='Gift wrapping' mod='ets_payment_with_fee'}</span>
              {/if}
            </div>
            {/if}
          </div>
          <!-- Tab returns -->
          <div class="tab-pane" id="returns">
            <h4 class="visible-print">{l s='Merchandise Returns' mod='ets_payment_with_fee'} <span class="badge">({$order->getReturn()|@count|escape:'html':'UTF-8'})</span></h4>
            {if !$order->isVirtual()}
            <!-- Return block -->
              {if $order->getReturn()|count > 0}
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th><span class="title_box ">{l s='Date' mod='ets_payment_with_fee'}</span></th>
                      <th><span class="title_box ">{l s='Type' mod='ets_payment_with_fee'}</span></th>
                      <th><span class="title_box ">{l s='Carrier' mod='ets_payment_with_fee'}</span></th>
                      <th><span class="title_box ">{l s='Tracking number' mod='ets_payment_with_fee'}</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    {foreach from=$order->getReturn() item=line}
                    <tr>
                      <td>{$line.date_add|escape:'html':'UTF-8'}</td>
                      <td>{$line.type|escape:'html':'UTF-8'}</td>
                      <td>{$line.state_name|escape:'html':'UTF-8'}</td>
                      <td class="actions">
                        <span class="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number|escape:'html':'UTF-8'}</a>{elseif isset($line.tracking_number)}{$line.tracking_number|escape:'html':'UTF-8'}{/if}</span>
                        {if $line.can_edit}
                        <form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|intval}{else}0{/if}&amp;id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'html':'UTF-8'}{else}0{/if}">
                          <span class="shipping_number_edit" style="display:none;">
                            <button type="button" name="tracking_number">
                              {$line.tracking_number|htmlentities|escape:'html':'UTF-8'}
                            </button>
                            <button type="submit" class="btn btn-default" name="submitShippingNumber">
                              {l s='Update' mod='ets_payment_with_fee'}
                            </button>
                          </span>
                          <button href="#" class="edit_shipping_number_link">
                            <i class="icon-pencil"></i>
                            {l s='Edit' mod='ets_payment_with_fee'}
                          </button>
                          <button href="#" class="cancel_shipping_number_link" style="display: none;">
                            <i class="icon-remove"></i>
                            {l s='Cancel' mod='ets_payment_with_fee'}
                          </button>
                        </form>
                        {/if}
                      </td>
                    </tr>
                    {/foreach}
                  </tbody>
                </table>
              </div>
              {else}
              <div class="list-empty hidden-print">
                <div class="list-empty-msg">
                  <i class="icon-warning-sign list-empty-icon"></i>
                  {l s='No merchandise returned yet' mod='ets_payment_with_fee'}
                </div>
              </div>
              {/if}
              {if $carrierModuleCall}
                {$carrierModuleCall|escape:'html':'UTF-8'}
              {/if}
            {/if}
          </div>
        </div>
        <script>
          $('#myTab a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
          })
        </script>
      </div>
      <!-- Payments block -->
      <div id="formAddPaymentPanel" class="panel">
        <div class="panel-heading">
          <i class="icon-money"></i>
          {l s='Payment' mod='ets_payment_with_fee'} <span class="badge">{$order->getOrderPayments()|@count|escape:'html':'UTF-8'}</span>
        </div>
        {if count($order->getOrderPayments()) > 0}
          <p class="alert alert-danger"{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || (isset($currentState) && $currentState->id == 6)} style="display: none;"{/if}>
            {l s='Warning' mod='ets_payment_with_fee'}
            <strong>{displayPrice price=$total_paid currency=$currency->id}</strong>
            {l s='paid instead of' mod='ets_payment_with_fee'}
            <strong class="total_paid">{displayPrice price=$orders_total_paid_tax_incl currency=$currency->id}</strong>
            {foreach $order->getBrother() as $brother_order}
              {if $brother_order@first}
                {if count($order->getBrother()) == 1}
                  <br />{l s='This warning also concerns order ' mod='ets_payment_with_fee'}
                {else}
                  <br />{l s='This warning also concerns the next orders:' mod='ets_payment_with_fee'}
                {/if}
              {/if}
              <a href="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$brother_order->id|intval}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                #{'%06d'|sprintf:$brother_order->id|escape:'html':'UTF-8'}
              </a>
            {/foreach}
          </p>
        {/if}
        <form id="formAddPayment"  method="post" action="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th><span class="title_box ">{l s='Date' mod='ets_payment_with_fee'}</span></th>
                  <th><span class="title_box ">{l s='Payment method' mod='ets_payment_with_fee'}</span></th>
                  <th><span class="title_box ">{l s='Transaction ID' mod='ets_payment_with_fee'}</span></th>
                  <th><span class="title_box ">{l s='Amount' mod='ets_payment_with_fee'}</span></th>
                  <th><span class="title_box ">{l s='Invoice' mod='ets_payment_with_fee'}</span></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {foreach from=$order->getOrderPaymentCollection() item=payment}
                <tr>
                  <td>{dateFormat date=$payment->date_add full=true}</td>
                  <td>{$payment->payment_method|escape:'html':'UTF-8'}</td>
                  <td>{$payment->transaction_id|escape:'html':'UTF-8'}</td>
                  <td>{displayPrice price=$payment->amount currency=$payment->id_currency}</td>
                  <td>
                  {if $invoice = $payment->getOrderInvoice($order->id)}
                    {$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)|escape:'html':'UTF-8'}
                  {else}
                  {/if}
                  </td>
                  <td class="actions">
                    <button class="btn btn-default open_payment_information">
                      <i class="icon-search"></i>
                      {l s='Details' mod='ets_payment_with_fee'}
                    </button>
                  </td>
                </tr>
                <tr class="payment_information" style="display: none;">
                  <td colspan="5">
                    <p>
                      <b>{l s='Card Number' mod='ets_payment_with_fee'}</b>&nbsp;
                      {if $payment->card_number}
                        {$payment->card_number|escape:'html':'UTF-8'}
                      {else}
                        <i>{l s='Not defined' mod='ets_payment_with_fee'}</i>
                      {/if}
                    </p>
                    <p>
                      <b>{l s='Card Brand' mod='ets_payment_with_fee'}</b>&nbsp;
                      {if $payment->card_brand}
                        {$payment->card_brand|escape:'html':'UTF-8'}
                      {else}
                        <i>{l s='Not defined' mod='ets_payment_with_fee'}</i>
                      {/if}
                    </p>
                    <p>
                      <b>{l s='Card Expiration' mod='ets_payment_with_fee'}</b>&nbsp;
                      {if $payment->card_expiration}
                        {$payment->card_expiration|escape:'html':'UTF-8'}
                      {else}
                        <i>{l s='Not defined' mod='ets_payment_with_fee'}</i>
                      {/if}
                    </p>
                    <p>
                      <b>{l s='Card Holder' mod='ets_payment_with_fee'}</b>&nbsp;
                      {if $payment->card_holder}
                        {$payment->card_holder|escape:'html':'UTF-8'}
                      {else}
                        <i>{l s='Not defined' mod='ets_payment_with_fee'}</i>
                      {/if}
                    </p>
                  </td>
                </tr>
                {foreachelse}
                <tr>
                  <td class="list-empty hidden-print" colspan="6">
                    <div class="list-empty-msg">
                      <i class="icon-warning-sign list-empty-icon"></i>
                      {l s='No payment methods are available' mod='ets_payment_with_fee'}
                    </div>
                  </td>
                </tr>
                {/foreach}
                <tr class="current-edit hidden-print">
                  <td>
                    <div class="input-group fixed-width-xl">
                      <input type="text" name="payment_date" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
                      <div class="input-group-addon">
                        <i class="icon-calendar-o"></i>
                      </div>
                    </div>
                  </td>
                  <td>
                    <input name="payment_method" list="payment_method" class="payment_method">
                    <datalist id="payment_method">
                    {foreach from=$payment_methods item=payment_method}
                      <option value="{$payment_method|escape:'html':'UTF-8'}">
                    {/foreach}
                    </datalist>
                  </td>
                  <td>
                    <input type="text" name="payment_transaction_id" value="" class="form-control fixed-width-sm"/>
                  </td>
                  <td>
                    <input type="text" name="payment_amount" value="" class="form-control fixed-width-sm pull-left" />
                    <select name="payment_currency" class="payment_currency form-control fixed-width-xs pull-left">
                      {foreach from=$currencies item=current_currency}
                        <option value="{$current_currency['id_currency']|escape:'html':'UTF-8'}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']|escape:'html':'UTF-8'}</option>
                      {/foreach}
                    </select>
                  </td>
                  <td>
                    {if count($invoices_collection) > 0}
                      <select name="payment_invoice" id="payment_invoice">
                      {foreach from=$invoices_collection item=invoice}
                        <option value="{$invoice->id|intval}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)|escape:'html':'UTF-8'}</option>
                      {/foreach}
                      </select>
                    {/if}
                  </td>
                  <td class="actions">
                    <button class="btn btn-primary" type="submit" name="submitAddPayment">
                      {l s='Add' mod='ets_payment_with_fee'}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </form>
        {if (!$order->valid && sizeof($currencies) > 1)}
          <form class="form-horizontal well" method="post" action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
            <div class="row">
              <label class="control-label col-lg-3">{l s='Change currency' mod='ets_payment_with_fee'}</label>
              <div class="col-lg-6">
                <select name="new_currency">
                {foreach from=$currencies item=currency_change}
                  {if $currency_change['id_currency'] != $order->id_currency}
                  <option value="{$currency_change['id_currency']|escape:'html':'UTF-8'}">{$currency_change.name|escape:'html':'UTF-8'} - {$currency_change.sign|escape:'html':'UTF-8'}</option>
                  {/if}
                {/foreach}
                </select>
                <p class="help-block">{l s='Do not forget to update your exchange rate before making this change.' mod='ets_payment_with_fee'}</p>
              </div>
              <div class="col-lg-3">
                <button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> {l s='Change' mod='ets_payment_with_fee'}</button>
              </div>
            </div>
          </form>
        {/if}
      </div>
      {hook h="displayAdminOrderLeft" id_order=$order->id}
    </div>
    <div class="col-lg-5">
      <!-- Customer informations -->
      <div class="panel">
        {if $customer->id}
          <div class="panel-heading">
            <i class="icon-user"></i>
            {l s='Customer' mod='ets_payment_with_fee'}
            <span class="badge">
              <a href="?tab=AdminCustomers&amp;id_customer={$customer->id|intval}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}">
                {if Configuration::get('PS_B2B_ENABLE')}{$customer->company|escape:'html':'UTF-8'} - {/if}
                {$gender->name|escape:'html':'UTF-8'}
                {$customer->firstname|escape:'html':'UTF-8'}
                {$customer->lastname|escape:'html':'UTF-8'}
              </a>
            </span>
            <span class="badge">
              {l s='#' mod='ets_payment_with_fee'}{$customer->id|intval}
            </span>
          </div>
          <div class="row">
            <div class="col-xs-6">
              {if ($customer->isGuest())}
                {l s='This order has been placed by a guest.' mod='ets_payment_with_fee'}
                {if (!Customer::customerExists($customer->email))}
                  <form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id|intval}&amp;id_order={$order->id|intval}&amp;token={getAdminToken tab='AdminCustomers'}">
                    <input type="hidden" name="id_lang" value="{$order->id_lang|intval}" />
                    <input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer' mod='ets_payment_with_fee'}" />
                    <p class="help-block">{l s='This feature will generate a random password and send an email to the customer.' mod='ets_payment_with_fee'}</p>
                  </form>
                {else}
                  <div class="alert alert-warning">
                    {l s='A registered customer account has already claimed this email address' mod='ets_payment_with_fee'}
                  </div>
                {/if}
              {else}
                <dl class="well list-detail">
                  <dt>{l s='Email' mod='ets_payment_with_fee'}</dt>
                    <dd><a href="mailto:{$customer->email|escape:'html':'UTF-8'}"><i class="icon-envelope-o"></i> {$customer->email|escape:'html':'UTF-8'}</a></dd>
                  <dt>{l s='Account registered' mod='ets_payment_with_fee'}</dt>
                    <dd class="text-muted"><i class="icon-calendar-o"></i> {dateFormat date=$customer->date_add full=true}</dd>
                  <dt>{l s='Valid orders placed' mod='ets_payment_with_fee'}</dt>
                    <dd><span class="badge">{$customerStats['nb_orders']|intval}</span></dd>
                  <dt>{l s='Total spent since registration' mod='ets_payment_with_fee'}</dt>
                    <dd><span class="badge badge-success">{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</span></dd>
                  {if Configuration::get('PS_B2B_ENABLE')}
                    <dt>{l s='SIRET' mod='ets_payment_with_fee'}</dt>
                      <dd>{$customer->siret|escape:'html':'UTF-8'}</dd>
                    <dt>{l s='APE' mod='ets_payment_with_fee'}</dt>
                      <dd>{$customer->ape|escape:'html':'UTF-8'}</dd>
                  {/if}
                </dl>
              {/if}
            </div>

            <div class="col-xs-6">
              <div class="form-group hidden-print">
                <a href="?tab=AdminCustomers&amp;id_customer={$customer->id|intval}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="btn btn-default btn-block">{l s='View full details...' mod='ets_payment_with_fee'}</a>
              </div>
              <div class="panel panel-sm">
                <div class="panel-heading">
                  <i class="icon-eye-slash"></i>
                  {l s='Private note' mod='ets_payment_with_fee'}
                </div>
                <form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote({$customer->id|intval});return false;" >
                  <div class="form-group">
                    <div class="col-lg-12">
                      <textarea name="note" id="noteContent" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#submitCustomerNote').removeAttr('disabled') : $('#submitCustomerNote').attr('disabled', 'disabled')">{$customer->note|escape:'html':'UTF-8'}</textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled">
                        <i class="icon-save"></i>
                        {l s='Save' mod='ets_payment_with_fee'}
                      </button>
                    </div>
                  </div>
                  <span id="note_feedback"></span>
                </form>
              </div>
            </div>
          </div>
        {/if}
        <!-- Tab nav -->
        <div class="row">
          <ul class="nav nav-tabs" id="tabAddresses">
            <li class="active">
              <a href="#addressShipping">
                <i class="icon-truck"></i>
                {l s='Shipping address' mod='ets_payment_with_fee'}
              </a>
            </li>
            <li>
              <a href="#addressInvoice">
                <i class="icon-file-text"></i>
                {l s='Invoice address' mod='ets_payment_with_fee'}
              </a>
            </li>
          </ul>
          <!-- Tab content -->
          <div class="tab-content panel">
            <!-- Tab status -->
            <div class="tab-pane  in active" id="addressShipping">
              <!-- Addresses -->
              <h4 class="visible-print">{l s='Shipping address' mod='ets_payment_with_fee'}</h4>
              {if !$order->isVirtual()}
              <!-- Shipping address -->
                {if $can_edit}
                  <form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
                    <div class="form-group">
                      <div class="col-lg-9">
                        <select name="id_address">
                          {foreach from=$customer_addresses item=address}
                          <option value="{$address['id_address']|escape:'html':'UTF-8'}"
                            {if $address['id_address'] == $order->id_address_delivery}
                              selected="selected"
                            {/if}>
                            {$address['alias']|escape:'html':'UTF-8'} -
                            {$address['address1']|escape:'html':'UTF-8'}
                            {$address['postcode']|escape:'html':'UTF-8'}
                            {$address['city']|escape:'html':'UTF-8'}
                            {if !empty($address['state'])}
                              {$address['state']|escape:'html':'UTF-8'}
                            {/if},
                            {$address['country']|escape:'html':'UTF-8'}
                          </option>
                          {/foreach}
                        </select>
                      </div>
                      <div class="col-lg-3">
                        <button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> {l s='Change' mod='ets_payment_with_fee'}</button>
                      </div>
                    </div>
                  </form>
                {/if}
                <div class="well">
                  <div class="row">
                    <div class="col-sm-6">
                      <a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.delivery->id|intval}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id|intval}&amp;address_type=1&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode|escape:'html':'UTF-8'}">
                        <i class="icon-pencil"></i>
                        {l s='Edit' mod='ets_payment_with_fee'}
                      </a>
                      {displayAddressDetail address=$addresses.delivery newLine='<br />'}
                      {if $addresses.delivery->other}
                        <hr />{$addresses.delivery->other|escape:'html':'UTF-8'}<br />
                      {/if}
                    </div>
                    <div class="col-sm-6 hidden-print">
                      <div id="map-delivery-canvas" style="height: 190px"></div>
                    </div>
                  </div>
                </div>
              {/if}
            </div>
            <div class="tab-pane " id="addressInvoice">
              <!-- Invoice address -->
              <h4 class="visible-print">{l s='Invoice address' mod='ets_payment_with_fee'}</h4>
              {if $can_edit}
                <form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
                  <div class="form-group">
                    <div class="col-lg-9">
                      <select name="id_address">
                        {foreach from=$customer_addresses item=address}
                        <option value="{$address['id_address']|intval}"
                          {if $address['id_address'] == $order->id_address_invoice}
                          selected="selected"
                          {/if}>
                          {$address['alias']|escape:'html':'UTF-8'} -
                          {$address['address1']|escape:'html':'UTF-8'}
                          {$address['postcode']|escape:'html':'UTF-8'}
                          {$address['city']|escape:'html':'UTF-8'}
                          {if !empty($address['state'])}
                            {$address['state']|escape:'html':'UTF-8'}
                          {/if},
                          {$address['country']|escape:'html':'UTF-8'}
                        </option>
                        {/foreach}
                      </select>
                    </div>
                    <div class="col-lg-3">
                      <button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> {l s='Change' mod='ets_payment_with_fee'}</button>
                    </div>
                  </div>
                </form>
              {/if}
              <div class="well">
                <div class="row">
                  <div class="col-sm-6">
                    <a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.invoice->id|intval}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id|intval}&amp;address_type=2&amp;back={$smarty.server.REQUEST_URI|urlencode|escape:'html':'UTF-8'}&amp;token={getAdminToken tab='AdminAddresses'}">
                      <i class="icon-pencil"></i>
                      {l s='Edit' mod='ets_payment_with_fee'}
                    </a>
                    {displayAddressDetail address=$addresses.invoice newLine='<br />'}
                    {if $addresses.invoice->other}
                      <hr />{$addresses.invoice->other|escape:'html':'UTF-8'}<br />
                    {/if}
                  </div>
                  <div class="col-sm-6 hidden-print">
                    <div id="map-invoice-canvas" style="height: 190px"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script>
          $('#tabAddresses a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
          })
        </script>
      </div>
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-envelope"></i> {l s='Messages' mod='ets_payment_with_fee'} <span class="badge">{sizeof($customer_thread_message)|escape:'html':'UTF-8'}</span>
        </div>
        {if (sizeof($messages))}
          <div class="panel panel-highlighted">
            <div class="message-item">
              {foreach from=$messages item=message}
                <div class="message-avatar">
                  <div class="avatar-md">
                    <i class="icon-user icon-2x"></i>
                  </div>
                </div>
                <div class="message-body">

                  <span class="message-date">&nbsp;<i class="icon-calendar"></i>
                    {dateFormat date=$message['date_add']} -
                  </span>
                  <h4 class="message-item-heading">
                    {if ($message['elastname']|escape:'html':'UTF-8')}{$message['efirstname']|escape:'html':'UTF-8'}
                      {$message['elastname']|escape:'html':'UTF-8'}{else}{$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
                    {/if}
                    {if ($message['private'] == 1)}
                      <span class="badge badge-info">{l s='Private' mod='ets_payment_with_fee'}</span>
                    {/if}
                  </h4>
                  <p class="message-item-text">
                    {$message['message']|escape:'html':'UTF-8'|nl2br}
                  </p>
                </div>
              {/foreach}
            </div>
          </div>
        {/if}
        <div id="messages" class="well hidden-print">
          <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?' mod='ets_payment_with_fee'}');">
            <div id="message" class="form-horizontal">
              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Choose a standard message' mod='ets_payment_with_fee'}</label>
                <div class="col-lg-9">
                  <select class="chosen form-control" name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?' mod='ets_payment_with_fee'}')">
                    <option value="0" selected="selected">-</option>
                    {foreach from=$orderMessages item=orderMessage}
                        <option value="{$orderMessage['message']|escape:'html':'UTF-8'}">{$orderMessage['name']|escape:'html':'UTF-8'}</option>
                    {/foreach}
                  </select>
                  <p class="help-block">
                    <a href="{$link->getAdminLink('AdminOrderMessage')|escape:'html':'UTF-8'}">
                      {l s='Configure predefined messages' mod='ets_payment_with_fee'}
                      <i class="icon-external-link"></i>
                    </a>
                  </p>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Display to customer?' mod='ets_payment_with_fee'}</label>
                <div class="col-lg-9">
                  <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="visibility" id="visibility_on" value="0" />
                    <label for="visibility_on">
                      {l s='Yes' mod='ets_payment_with_fee'}
                    </label>
                    <input type="radio" name="visibility" id="visibility_off" value="1" checked="checked" />
                    <label for="visibility_off">
                      {l s='No' mod='ets_payment_with_fee'}
                    </label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Message' mod='ets_payment_with_fee'}</label>
                <div class="col-lg-9">
                  <textarea id="txt_msg" class="textarea-autosize" name="message">{Tools::getValue('message')|escape:'html':'UTF-8'}</textarea>
                  <p id="nbchars"></p>
                </div>
              </div>


              <input type="hidden" name="id_order" value="{$order->id|intval}" />
              <input type="hidden" name="id_customer" value="{$order->id_customer|intval}" />
              <button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
                {l s='Send message' mod='ets_payment_with_fee'}
              </button>
              <a class="btn btn-default" href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">
                {l s='Show all messages' mod='ets_payment_with_fee'}
                <i class="icon-external-link"></i>
              </a>
            </div>
          </form>
        </div>
      </div>
      {hook h="displayAdminOrderRight" id_order=$order->id}
    </div>
  </div>
  {hook h="displayAdminOrder" id_order=$order->id}
  <div class="row" id="start_products">
    <div class="col-lg-12">
      <form class="container-command-top-spacing" action="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post" onsubmit="return orderDeleteProduct('{l s='This product cannot be returned.' mod='ets_payment_with_fee'}', '{l s='Quantity to cancel is greater than quantity available.' mod='ets_payment_with_fee'}');">
        <input type="hidden" name="id_order" value="{$order->id|intval}" />
        <div style="display: none">
          <input type="hidden" value="{$order->getWarehouseList()|implode|escape:'html':'UTF-8'}" id="warehouse_list" />
        </div>

        <div class="panel">
          <div class="panel-heading">
            <i class="icon-shopping-cart"></i>
            {l s='Products' mod='ets_payment_with_fee'} <span class="badge">{$products|@count|escape:'html':'UTF-8'}</span>
          </div>
          <div id="refundForm">
          </div>

          {capture "TaxMethod"}
            {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
              {l s='Tax excluded' mod='ets_payment_with_fee'}
            {else}
              {l s='Tax included' mod='ets_payment_with_fee'}
            {/if}
          {/capture}
          {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
            <input type="hidden" name="TaxMethod" value="0">
          {else}
            <input type="hidden" name="TaxMethod" value="1">
          {/if}
          <div class="table-responsive">
            <table class="table" id="orderProducts">
              <thead>
                <tr>
                  <th></th>
                  <th><span class="title_box ">{l s='Product' mod='ets_payment_with_fee'}</span></th>
                  <th>
                    <span class="title_box ">{l s='Price per unit' mod='ets_payment_with_fee'}</span>
                    <small class="text-muted">{$smarty.capture.TaxMethod|escape:'html':'UTF-8'}</small>
                  </th>
                  <th class="text-center"><span class="title_box ">{l s='Qty' mod='ets_payment_with_fee'}</span></th>
                  {if $display_warehouse}<th><span class="title_box ">{l s='Warehouse' mod='ets_payment_with_fee'}</span></th>{/if}
                  {if ($order->hasBeenPaid())}<th class="text-center"><span class="title_box ">{l s='Refunded' mod='ets_payment_with_fee'}</span></th>{/if}
                  {if ($order->hasBeenDelivered() || $order->hasProductReturned())}
                    <th class="text-center"><span class="title_box ">{l s='Returned' mod='ets_payment_with_fee'}</span></th>
                  {/if}
                  {if $stock_management}<th class="text-center"><span class="title_box ">{l s='Available quantity' mod='ets_payment_with_fee'}</span></th>{/if}
                  <th>
                    <span class="title_box ">{l s='Total' mod='ets_payment_with_fee'}</span>
                    <small class="text-muted">{$smarty.capture.TaxMethod|escape:'html':'UTF-8'}</small>
                  </th>
                  <th style="display: none;" class="add_product_fields"></th>
                  <th style="display: none;" class="edit_product_fields"></th>
                  <th style="display: none;" class="standard_refund_fields">
                    <i class="icon-minus-sign"></i>
                    {if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
                      {l s='Return' mod='ets_payment_with_fee'}
                    {elseif ($order->hasBeenPaid())}
                      {l s='Refund' mod='ets_payment_with_fee'}
                    {else}
                      {l s='Cancel' mod='ets_payment_with_fee'}
                    {/if}
                  </th>
                  <th style="display:none" class="partial_refund_fields">
                    <span class="title_box ">{l s='Partial refund' mod='ets_payment_with_fee'}</span>
                  </th>
                  {if !$order->hasBeenDelivered()}
                  <th></th>
                  {/if}
                </tr>
              </thead>
              <tbody>
              {foreach from=$products item=product key=k}
                {* Include customized datas partial *}
                {include file='controllers/orders/_customized_data.tpl'}
                {* Include product line partial *}
                {include file='controllers/orders/_product_line.tpl'}
              {/foreach}
              {if $can_edit}
                {include file='controllers/orders/_new_product.tpl'}
              {/if}
              </tbody>
            </table>
          </div>

          {if $can_edit}
          <div class="row-margin-bottom row-margin-top order_action">
          {if !$order->hasBeenDelivered()}
            <button type="button" id="add_product" class="btn btn-default">
              <i class="icon-plus-sign"></i>
              {l s='Add a product' mod='ets_payment_with_fee'}
            </button>
          {/if}
            <button id="add_voucher" class="btn btn-default" type="button" >
              <i class="icon-ticket"></i>
              {l s='Add a new discount' mod='ets_payment_with_fee'}
            </button>
          </div>
          {/if}
          <div class="clear">&nbsp;</div>
          <div class="row">
            <div class="col-xs-6">
              <div class="alert alert-warning">
                {* [1][/1] is for a HTML tag. *}
                {l
                  s='For this customer group, prices are displayed as: [1]%tax_method%[/1]'
                  sprintf=[
                    '%tax_method%' => $smarty.capture.TaxMethod,
                    '[1]' => '<strong>',
                    '[/1]' => '</strong>'
                  ]
                 
                }
                {if !Configuration::get('PS_ORDER_RETURN')}
                  <br/><strong>{l s='Merchandise returns are disabled' mod='ets_payment_with_fee'}</strong>
                {/if}
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel panel-vouchers" style="{if !sizeof($discounts)}display:none;{/if}">
                {if (sizeof($discounts) || $can_edit)}
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>
                          <span class="title_box ">
                            {l s='Discount name' mod='ets_payment_with_fee'}
                          </span>
                        </th>
                        <th>
                          <span class="title_box ">
                            {l s='Value' mod='ets_payment_with_fee'}
                          </span>
                        </th>
                        {if $can_edit}
                        <th></th>
                        {/if}
                      </tr>
                    </thead>
                    <tbody>
                      {foreach from=$discounts item=discount}
                      <tr>
                        <td>{$discount['name']|escape:'html':'UTF-8'}</td>
                        <td>
                        {if $discount['value'] != 0.00}
                          -
                        {/if}
                        {displayPrice price=$discount['value'] currency=$currency->id}
                        </td>
                        {if $can_edit}
                        <td>
                          <a href="{$current_index|escape:'html':'UTF-8'}&amp;submitDeleteVoucher&amp;id_order_cart_rule={$discount['id_order_cart_rule']|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                            <i class="icon-minus-sign"></i>
                            {l s='Delete voucher' mod='ets_payment_with_fee'}
                          </a>
                        </td>
                        {/if}
                      </tr>
                      {/foreach}
                    </tbody>
                  </table>
                </div>
                <div class="current-edit" id="voucher_form" style="display:none;">
                  {include file='controllers/orders/_discount_form.tpl'}
                </div>
                {/if}
              </div>
              <div class="panel panel-total">
                <div class="table-responsive">
                  <table class="table">
                    {* Assign order price *}
                    {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                      {assign var=order_product_price value=($order->total_products)}
                      {assign var=order_discount_price value=$order->total_discounts_tax_excl}
                      {assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
                      {assign var=order_shipping_price value=$order->total_shipping_tax_excl}
                    {else}
                      {assign var=order_product_price value=$order->total_products_wt}
                      {assign var=order_discount_price value=$order->total_discounts_tax_incl}
                      {assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
                      {assign var=order_shipping_price value=$order->total_shipping_tax_incl}
                    {/if}
                    <tr id="total_products">
                      <td class="text-right">{l s='Products:' mod='ets_payment_with_fee'}</td>
                      <td class="amount text-right nowrap">
                        {displayPrice price=$order_product_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
                      <td class="text-right">{l s='Discounts' mod='ets_payment_with_fee'}</td>
                      <td class="amount text-right nowrap">
                        -{displayPrice price=$order_discount_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
                      <td class="text-right">{l s='Wrapping' mod='ets_payment_with_fee'}</td>
                      <td class="amount text-right nowrap">
                        {displayPrice price=$order_wrapping_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="total_shipping">
                      <td class="text-right">{l s='Shipping' mod='ets_payment_with_fee'}</td>
                      <td class="amount text-right nowrap" >
                        {displayPrice price=$order_shipping_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;">
                        <div class="input-group">
                          <div class="input-group-addon">
                            {$currency->sign|escape:'html':'UTF-8'}
                          </div>
                          <input type="text" name="partialRefundShippingCost" value="0" />
                        </div>
                        <p class="help-block"><i class="icon-warning-sign"></i> {l s='(%s)' sprintf=$smarty.capture.TaxMethod mod='ets_payment_with_fee'}</p>
                      </td>
                      
                    </tr>
                    <tr id="total_fee_payment" {if !isset($custom_payment) ||  !$custom_payment || $fee_payment<=0}style="display: none;"{/if}>
						<td class="text-right">{$text_payment_fee_incl|escape:'html':'UTF-8'}</td>
						<td class="amount text-right nowrap" >
							{if isset($fee_payment) && $fee_payment}
                                {displayPrice price=$fee_payment currency=$currency->id}
                            {else}
                                {l s='Free' mod='ets_payment_with_fee'}
                            {/if}
						</td>
						<td class="partial_refund_fields current-edit" style="display:none;">
						</td>
					</tr>
                    {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                    <tr id="total_taxes">
                      <td class="text-right">{l s='Taxes' mod='ets_payment_with_fee'}</td>
                      <td class="amount text-right nowrap" >{displayPrice price=($order->total_paid_tax_incl-$order->total_paid_tax_excl) currency=$currency->id}</td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    {/if}
                    {assign var=order_total_price value=$order->total_paid_tax_incl}
                    <tr id="total_order">
                      <td class="text-right"><strong>{l s='Total' mod='ets_payment_with_fee'}</strong></td>
                      <td class="amount text-right nowrap">
                        <strong>{displayPrice price=$order_total_price currency=$currency->id}</strong>
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div style="display: none;" class="standard_refund_fields form-horizontal panel">
            <div class="form-group">
              {if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
              <p class="checkbox">
                <label for="reinjectQuantities">
                  <input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" />
                  {l s='Re-stock products' mod='ets_payment_with_fee'}
                </label>
              </p>
              {/if}
              {if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
              <p class="checkbox">
                <label for="generateCreditSlip">
                  <input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost()" />
                  {l s='Generate a credit slip' mod='ets_payment_with_fee'}
                </label>
              </p>
              <p class="checkbox">
                <label for="generateDiscount">
                  <input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost()" />
                  {l s='Generate a voucher' mod='ets_payment_with_fee'}
                </label>
              </p>
              <p class="checkbox" id="spanShippingBack" style="display:none;">
                <label for="shippingBack">
                  <input type="checkbox" id="shippingBack" name="shippingBack" />
                  {l s='Repay shipping costs' mod='ets_payment_with_fee'}
                </label>
              </p>
              {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
              <br/><p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund:' mod='ets_payment_with_fee'}</p>
              <p class="radio">
                <label id="lab_refund_total_1" for="refund_total_1">
                  <input type="radio" value="0" name="refund_total_voucher_off" id="refund_total_1" checked="checked" />
                  {l s='Include amount of initial voucher: ' mod='ets_payment_with_fee'}
                </label>
              </p>
              <p class="radio">
                <label id="lab_refund_total_2" for="refund_total_2">
                  <input type="radio" value="1" name="refund_total_voucher_off" id="refund_total_2"/>
                  {l s='Exclude amount of initial voucher: ' mod='ets_payment_with_fee'}
                </label>
              </p>
              <div class="nowrap radio-inline">
                <label id="lab_refund_total_3" class="pull-left" for="refund_total_3">
                  {l s='Amount of your choice: ' mod='ets_payment_with_fee'}
                  <input type="radio" value="2" name="refund_total_voucher_off" id="refund_total_3"/>
                </label>
                <div class="input-group col-lg-1 pull-left">
                  <div class="input-group-addon">
                    {$currency->sign|escape:'html':'UTF-8'}
                  </div>
                  <input type="text" class="input fixed-width-md" name="refund_total_voucher_choose" value="0"/>
                </div>
              </div>
              {/if}
            {/if}
            </div>
            {if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
            <div class="row">
              <input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products' mod='ets_payment_with_fee'}{elseif $order->hasBeenPaid()}{l s='Refund products' mod='ets_payment_with_fee'}{else}{l s='Cancel products' mod='ets_payment_with_fee'}{/if}" class="btn btn-default" />
            </div>
            {/if}
          </div>
          <div style="display:none;" class="partial_refund_fields">
            <p class="checkbox">
              <label for="reinjectQuantitiesRefund">
                <input type="checkbox" id="reinjectQuantitiesRefund" name="reinjectQuantities" />
                {l s='Re-stock products' mod='ets_payment_with_fee'}
              </label>
            </p>
            <p class="checkbox">
              <label for="generateDiscountRefund">
                <input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" onclick="toggleShippingCost()" />
                {l s='Generate a voucher' mod='ets_payment_with_fee'}
              </label>
            </p>
            {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
            <p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund: ' mod='ets_payment_with_fee'}</p>
            <p class="radio">
              <label id="lab_refund_1" for="refund_1">
                <input type="radio" value="0" name="refund_voucher_off" id="refund_1" checked="checked" />
                {l s='Product(s) price: ' mod='ets_payment_with_fee'}
              </label>
            </p>
            <p class="radio">
              <label id="lab_refund_2" for="refund_2">
                <input type="radio" value="1" name="refund_voucher_off" id="refund_2"/>
                {l s='Product(s) price, excluding amount of initial voucher: ' mod='ets_payment_with_fee'}
              </label>
            </p>
            <div class="nowrap radio-inline">
                <label id="lab_refund_3" class="pull-left" for="refund_3">
                  {l s='Amount of your choice: ' mod='ets_payment_with_fee'}
                  <input type="radio" value="2" name="refund_voucher_off" id="refund_3"/>
                </label>
                <div class="input-group col-lg-1 pull-left">
                  <div class="input-group-addon">
                    {$currency->sign|escape:'html':'UTF-8'}
                  </div>
                  <input type="text" class="input fixed-width-md" name="refund_voucher_choose" value="0"/>
                </div>
              </div>
            {/if}
            <br/>
            <button type="submit" name="partialRefund" class="btn btn-default">
              <i class="icon-check"></i> {l s='Partial refund' mod='ets_payment_with_fee'}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <!-- Sources block -->
      {if (sizeof($sources))}
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-globe"></i>
          {l s='Sources' mod='ets_payment_with_fee'} <span class="badge">{$sources|@count|intval}</span>
        </div>
        <ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll;"{/if}>
        {foreach from=$sources item=source}
          <li>
            {dateFormat date=$source['date_add'] full=true}<br />
            <b>{l s='From' mod='ets_payment_with_fee'}</b>{if $source['http_referer'] != ''}<a href="{$source['http_referer']|escape:'html':'UTF-8'}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a>{else}-{/if}<br />
            <b>{l s='To' mod='ets_payment_with_fee'}</b> <a href="http://{$source['request_uri']|escape:'html':'UTF-8'}">{$source['request_uri']|truncate:100:'...'|escape:'html':'UTF-8'}</a><br />
            {if $source['keywords']|escape:'html':'UTF-8'}<b>{l s='Keywords' mod='ets_payment_with_fee'}</b> {$source['keywords']|escape:'html':'UTF-8'}<br />{/if}<br />
          </li>
        {/foreach}
        </ul>
      </div>
      {/if}

      <!-- linked orders block -->
      {if count($order->getBrother()) > 0}
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-cart"></i>
          {l s='Linked orders' mod='ets_payment_with_fee'}
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>
                  {l s='Order no. ' mod='ets_payment_with_fee'}
                </th>
                <th>
                  {l s='Status' mod='ets_payment_with_fee'}
                </th>
                <th>
                  {l s='Amount' mod='ets_payment_with_fee'}
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {foreach $order->getBrother() as $brother_order}
              <tr>
                <td>
                  <a href="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$brother_order->id|intval}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">#{$brother_order->id|intval}</a>
                </td>
                <td>
                  {$brother_order->getCurrentOrderState()->name[$current_id_lang]|escape:'html':'UTF-8'}
                </td>
                <td>
                  {displayPrice price=$brother_order->total_paid_tax_incl currency=$currency->id}
                </td>
                <td>
                  <a href="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$brother_order->id|intval}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                    <i class="icon-eye-open"></i>
                    {l s='View order' mod='ets_payment_with_fee'}
                  </a>
                </td>
              </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </div>
      {/if}
    </div>
  </div>

  <script type="text/javascript">
    var geocoder = new google.maps.Geocoder();
    var delivery_map, invoice_map;

    $(document).ready(function()
    {
      $(".textarea-autosize").autosize();

      geocoder.geocode({
        address: '{$addresses.delivery->address1|@addcslashes:'\''|escape:'html':'UTF-8'},{$addresses.delivery->postcode|@addcslashes:'\''|escape:'html':'UTF-8'},{$addresses.delivery->city|@addcslashes:'\''|escape:'html':'UTF-8'}{if isset($addresses.deliveryState->name) && $addresses.delivery->id_state},{$addresses.deliveryState->name|@addcslashes:'\''|escape:'html':'UTF-8'}{/if},{$addresses.delivery->country|@addcslashes:'\''|escape:'html':'UTF-8'}'
        }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK)
        {
          delivery_map = new google.maps.Map(document.getElementById('map-delivery-canvas'), {
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: results[0].geometry.location
          });
          var delivery_marker = new google.maps.Marker({
            map: delivery_map,
            position: results[0].geometry.location,
            url: "http://maps.google.com?q={$addresses.delivery->address1|urlencode|escape:'html':'UTF-8'},{$addresses.delivery->postcode|urlencode|escape:'html':'UTF-8'},{$addresses.delivery->city|urlencode|escape:'html':'UTF-8'}{if isset($addresses.deliveryState->name) && $addresses.delivery->id_state},{$addresses.deliveryState->name|urlencode|escape:'html':'UTF-8'}{/if},{$addresses.delivery->country|urlencode|escape:'html':'UTF-8'}"
          });
          google.maps.event.addListener(delivery_marker, 'click', function() {
            window.open(delivery_marker.url);
          });
        }
      });

      geocoder.geocode({
        address: '{$addresses.invoice->address1|@addcslashes:'\''|escape:'html':'UTF-8'},{$addresses.invoice->postcode|@addcslashes:'\''|escape:'html':'UTF-8'},{$addresses.invoice->city|@addcslashes:'\''|escape:'html':'UTF-8'}{if isset($addresses.deliveryState->name) && $addresses.invoice->id_state},{$addresses.deliveryState->name|@addcslashes:'\''|escape:'html':'UTF-8'}{/if},{$addresses.invoice->country|@addcslashes:'\''|escape:'html':'UTF-8'}'
        }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK)
        {
          invoice_map = new google.maps.Map(document.getElementById('map-invoice-canvas'), {
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: results[0].geometry.location
          });
          invoice_marker = new google.maps.Marker({
            map: invoice_map,
            position: results[0].geometry.location,
            url: "http://maps.google.com?q={$addresses.invoice->address1|urlencode|escape:'html':'UTF-8'},{$addresses.invoice->postcode|urlencode|escape:'html':'UTF-8'},{$addresses.invoice->city|urlencode|escape:'html':'UTF-8'}{if isset($addresses.deliveryState->name) && $addresses.invoice->id_state},{$addresses.deliveryState->name|urlencode|escape:'html':'UTF-8'}{/if},{$addresses.invoice->country|urlencode|escape:'html':'UTF-8'}"
          });
          google.maps.event.addListener(invoice_marker, 'click', function() {
            window.open(invoice_marker.url);
          });
        }
      });

      $('.datepicker').datetimepicker({
        prevText: '',
        nextText: '',
        dateFormat: 'yy-mm-dd',
        // Define a custom regional settings in order to use PrestaShop translation tools
        currentText: '{l s='Now' mod='ets_payment_with_fee' js=1}',
        closeText: '{l s='Done' mod='ets_payment_with_fee' js=1}',
        ampm: false,
        amNames: ['AM', 'A'],
        pmNames: ['PM', 'P'],
        timeFormat: 'hh:mm:ss tt',
        timeSuffix: '',
        timeOnlyTitle: '{l s='Choose Time' mod='ets_payment_with_fee' js=1}',
        timeText: '{l s='Time' mod='ets_payment_with_fee' js=1}',
        hourText: '{l s='Hour' mod='ets_payment_with_fee' js=1}',
        minuteText: '{l s='Minute' mod='ets_payment_with_fee' js=1}'
      });
    });

    // Fix wrong maps center when map is hidden
    $('#tabAddresses').click(function(){
      if (delivery_map) {
        x = delivery_map.getZoom();
        c = delivery_map.getCenter();
        google.maps.event.trigger(delivery_map, 'resize');
        delivery_map.setZoom(x);
        delivery_map.setCenter(c);
      }

      if (invoice_map) {
        x = invoice_map.getZoom();
        c = invoice_map.getCenter();
        google.maps.event.trigger(invoice_map, 'resize');
        invoice_map.setZoom(x);
        invoice_map.setCenter(c);
      }
    });
  </script>

{/block}
