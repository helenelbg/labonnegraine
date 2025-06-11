{**
 * 2007-2022 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2022 PrestaShop SA
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<div style="{$scalapayWidget["css"]}">
    <scalapay-widget
            style="all: initial;"
            type='checkout'
            frequency-number="{$scalapayWidget["frequencyNumber"]}"
            number-of-installments='{$scalapayWidget["numberOfInstallments"]}'
            amount="{$scalapayWidget["amount"]}"
            min="0"
            max="10000"
            locale='{$scalapayWidget["locale"]}'
            {if isset($scalapayWidget["productType"]) && $scalapayWidget["productType"]}product-type='[{$scalapayWidget["productType"]|escape:'htmlall':'UTF-8'}]'{/if}
            {if isset($scalapayWidget["amountSelectors"]) && $scalapayWidget["amountSelectors"]}amount-selectors='[{$scalapayWidget["amountSelectors"]|escape:'htmlall':'UTF-8'}]'{/if}
            show-title="{$scalapayWidget["showTitle"]}"
            hide-price="{$scalapayWidget["hidePrice"]}"
            {if isset($scalapayWidget["currencyPosition"])}currency-position="{$scalapayWidget["currencyPosition"]|escape:'htmlall':'UTF-8'}"{/if}
            {if isset($scalapayWidget["currencyDisplay"])}currency-display="{$scalapayWidget["currencyDisplay"]|escape:'htmlall':'UTF-8'}"{/if}
    ></scalapay-widget>
</div>