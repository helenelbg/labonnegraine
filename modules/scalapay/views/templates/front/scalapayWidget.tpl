{**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 *}

<scalapay-widget
        style="all: initial; display: block; {$scalapayWidget["css"]}"
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