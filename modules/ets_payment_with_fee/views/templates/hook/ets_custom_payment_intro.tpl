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
<section class="payment_fee">
    {*if $paymentMethod.logo_payment || $paymentMethod.description}
        <dl class="payment-desc">
            {if $paymentMethod.logo_payment}
                <img style="max-width:200px; max-height: 60px;" src="{_PS_ETS_PAYMENT_FEE_IMG_|escape:'html':'UTF-8'}{$paymentMethod.logo_payment|escape:'html':'UTF-8'}" />
            {/if}
            {if $paymentMethod.description}
                <p>{$paymentMethod.description|nl2br nofilter}</p>
            {/if}
        </dl>
    {/if*}
    {*if $paymentMethod.fee >0}
      <dl>
          <dt>{if $tax_incl}{$text_payment_fee_incl|escape:'html':'UTF-8'}{else}{$text_payment_fee_excl|escape:'html':'UTF-8'}{/if}{if $paymentMethod.fee_type=='percentage'} ({$paymentMethod.percentage|floatval}%){/if}</dt>
          <dd class="fee">+{$paymentMethod.fee_price|escape:'html':'UTF-8'}</dd> 
      </dl>
    {/if*}
    {if $paymentMethod.fee > 0}
        +{$paymentMethod.fee_price|escape:'html':'UTF-8'}
    {/if}
</section>