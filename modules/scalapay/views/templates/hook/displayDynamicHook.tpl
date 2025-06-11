{**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 *}


<style>
    scalapay-widget {
        all: initial;
        display: block;
    }

    {if !empty($scalapay['css'])}
    {$scalapay['css'] nofilter}
    {/if}
</style>
<script type="application/json" id="scalapayConfig">{$scalapay["widgets"]|json_encode nofilter}</script>
<script>
    {literal}

    const widgets = JSON.parse(document.getElementById('scalapayConfig').textContent ?? '');
    if (!widgets) {
        console.warn("No scalapay widgets configuration found for scalapay.")
    }

    function addWidget(product) {


        const widgetConfig = widgets[product];

        const positionElement = document.querySelector(widgetConfig['position'])

        if (positionElement?.parentNode?.querySelector(`scalapay-widget[product="${product}"]`)) {
            return;
        }

        const widget = document.createElement('scalapay-widget');
        widget.setAttribute('product', product)
        for (const widgetConfigKey in widgetConfig) {
            if (['afterWidgetText', 'position'].includes(widgetConfigKey)) continue;
            if (widgetConfigKey === 'style') {
                widget.style.cssText = ` ${widgetConfig[widgetConfigKey]}`;
                continue;
            }
            widget.setAttribute(widgetConfigKey.replace(/[A-Z]/g, letter => `-${letter.toLowerCase()}`), widgetConfig[widgetConfigKey])
        }

        positionElement?.insertAdjacentElement('afterend', widget);
    }


    document.addEventListener("DOMContentLoaded", function () {
        const observer1 = new MutationObserver(() => {
            for (const type in widgets) {
                addWidget(type)
            }
        });
        observer1.observe(document.querySelector('body'), {subtree: true, childList: true, attributes: true});
    });

    {/literal}


</script>

{if $scalapay["requireScripts"]}
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
{/if}