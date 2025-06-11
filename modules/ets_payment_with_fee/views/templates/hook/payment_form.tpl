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
<form id="payment-form" method="POST" action="http://localhost/ps1767/en/module/ets_payment_with_fee/validation?id_payment_method={$paymentMethod.id_payment_method|intval}">
    <input name="id_payment_method" value="{$paymentMethod.id_payment_method|intval}" type="hidden" />
    <button id="pay-with-payment-option-4" style="display:none" type="submit"></button>
</form>