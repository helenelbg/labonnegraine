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
<script type="text/javascript">
    var ets_cookie_module_name = '{$ets_cookie_module_name|escape:'html':'UTF-8'}';
    var ets_cookie_id_payment_method ={$ets_cookie_id_payment_method|intval};
    var ets_cookie_payment_option = '{$ets_cookie_payment_option|escape:'html':'UTF-8'}';
    var label_payment_fee = "{if $ets_pmwf_use_tax}{$text_payment_fee_incl|escape:'html':'UTF-8'}{else}{$text_payment_fee_excl|escape:'html':'UTF-8'}{/if}";
</script>