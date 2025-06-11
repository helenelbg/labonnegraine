{**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 *}

<style>
    div.scalapay_payment_module {
        cursor: pointer;
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
        padding: 10px 40px 10px 0;
        letter-spacing: -1px;
        position: relative;
        margin-bottom: 10px;
    }

    div.scalapay_payment_module:hover {
        background-color: #f6f6f6;
        cursor: pointer;
    }

    div.scalapay_payment_module a.scalapay {
        background: url({$scalapayPayment["logo"]}) no-repeat 15px;
        background-size: 90px;
        padding: 10px 0 10px 120px !important;
        color: #333 !important;
    }

    div.scalapay_payment_module a.scalapay::after {
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

    @media only screen and (max-width: 768px) {
        div.scalapay_payment_module {
            padding: 10px 40px 10px 0 !important;
        }

        div.scalapay_payment_module a.scalapay {
            padding: 0 0 0 120px !important;
        }
    }
</style>

{foreach $scalapayPayment["payments"] as $payment}
    <div class="row">
        <div class="col-xs-12">
            <div class="scalapay_payment_module">
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
    document.querySelectorAll("div.scalapay_payment_module").forEach(e => {
        e.addEventListener("click", () => e.querySelector('a.scalapay').click());
    })

</script>

<script>
    (() => {
        const esmScript = document.createElement('script');
        esmScript.src = 'https://cdn.scalapay.com/widget/v3/js/scalapay-widget.esm.js';
        esmScript.type = 'module';
        document.getElementsByTagName('head')[0].appendChild(esmScript);

        const widgetScript = document.createElement('script');
        widgetScript.src = 'https://cdn.scalapay.com/widget/v3/js/scalapay-widget.js';
        widgetScript.type = 'nomodule';
        document.getElementsByTagName('head')[0].appendChild(widgetScript);
    })()
</script>

