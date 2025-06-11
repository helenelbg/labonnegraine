{**
* 2019 Scalapay
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author Scalapay <info@scalapay.it>
*  @copyright  2019 scalapay
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<style>

    div.payment_module {
        background-color: #fbfbfb;
        display: block;
        border: 1px solid #c7c7c7;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
        font-size: 17px;
        line-height: 23px;
        color: #5d5d5d;
        font-weight: bold;
        padding: 33px 40px 34px 99px;
        letter-spacing: -1px;
        position: relative;
        margin-bottom: 10px;
    }

    div.payment_module a.scalapay {
        background: #fbfbfb url({$scalapayPayment["logo"]}) no-repeat 10px;
        padding: 33px 40px 34px 150px !important;
    }

    div.payment_module a.scalapay::after {
        display: block;
        position: absolute;
        content: "\f054";
        font-family: "FontAwesome", serif;
        right: 15px;
        margin-top: -11px;
        top: 50%;
        font-size: 25px;
        height: 22px;
        width: 14px;
        color: #777;
    }

</style>

{foreach $scalapayPayment["payments"] as $payment}
    <div class="row">
        <div class="col-xs-12">
            <div class="payment_module">
                <a class="scalapay"
                   href="{$payment["action"]}"
                   title="{$payment["callToActionText"]}">
                    {$payment["callToActionText"]}
                </a>
                {$payment["additionalInformation"]}
            </div>

        </div>
    </div>
{/foreach}

<script>
    document.querySelectorAll("div.payment_module").forEach(e => {
        e.addEventListener("click", () => e.querySelector('a.scalapay').click());
    })

</script>

