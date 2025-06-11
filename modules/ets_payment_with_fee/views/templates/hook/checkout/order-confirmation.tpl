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
{extends file='checkout/order-confirmation.tpl'}
{block name='page_content_container'}
    <section id="content-hook_order_confirmation" class="card">
      <div class="card-block">
        <div class="row">
          <div class="col-md-12">
            <h3 class="h1 card-title">
              <i class="material-icons done">&#xE876;</i>{l s='Your order is confirmed' mod='ets_payment_with_fee'}
            </h3>
            <p>
              {$message|nl2br nofilter}
              {if $order.details.invoice_url}
                {l s='You can also' mod='ets_payment_with_fee'}&nbsp;<a href="{$order.details.invoice_url|escape:'html':'UTF-8'}">{l s='download your invoice' mod='ets_payment_with_fee'}</a>
              {/if}
            </p>
            {$HOOK_ORDER_CONFIRMATION nofilter}
          </div>
        </div>
      </div>
    </section>
{/block}