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
<select name="payment_module_name" id="payment_module_name">
  {if !$PS_CATALOG_MODE}
    {foreach from=$payment_modules item='module'}
        {if $module->name!='ets_payment_with_fee'}
            <option value="{$module->name|escape:'html':'UTF-8'}" {if isset($smarty.post.payment_module_name) && $module->name == $smarty.post.payment_module_name}selected="selected"{/if}>{$module->displayName|escape:'html':'UTF-8'}</option>
        {else}
            {Ets_payment_with_fee::displayPaymentMethodCustom() nofilter}
        {/if}
    {/foreach}
  {else}
      <option value="{l s='Back office order' mod='ets_payment_with_fee'}">{l s='Back office order' mod='ets_payment_with_fee'}</option>
  {/if}
</select>
<br />
<input type="text" placeholder="{$text_payment_fee_excl|escape:'html':'UTF-8'}" value="" style="width: 150px;" name="payment_fee_order" />
<input type="hidden" name="id_ets_paymentmethod_admin" id="id_ets_paymentmethod_admin" value=""  />
