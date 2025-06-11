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
{if $status == 'ok'}
    {$return_message nofilter}
    <p>
      {l s='Your order on %s is complete.' sprintf=[$shop_name] mod='ets_payment_with_fee'}<br/>
    </p>
    <p>
      {l s='Please specify your order reference %s in the bankwire description.' sprintf=[$reference] mod='ets_payment_with_fee'}<br/>
      {l s='We\'ve also sent you this information by e-mail.' mod='ets_payment_with_fee'}
    </p>
    <strong>{l s='Your order will be sent as soon as we receive payment.' mod='ets_payment_with_fee'}</strong>
    <p>
      {l s='If you have questions, comments or concerns, please contact our' mod='ets_payment_with_fee'}
      <a href="{$contact_url|escape:'html':'UTF-8'}">{l s='expert customer support team' mod='ets_payment_with_fee'}</a>
    </p>
{else}
    <p class="warning">
      {l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='ets_payment_with_fee'}
      <a href="{$contact_url|escape:'html':'UTF-8'}">{l s='expert customer support team' mod='ets_payment_with_fee'}</a>
    </p>
{/if}

