<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:26:43
  from '/home/helene/prestashop/modules/scalapay/views/templates/hook/displayDynamicHook.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35230e3f75_36849069',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c1521ba50c1ee12f57859d0938238af652eefcd5' => 
    array (
      0 => '/home/helene/prestashop/modules/scalapay/views/templates/hook/displayDynamicHook.tpl',
      1 => 1749808899,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35230e3f75_36849069 (Smarty_Internal_Template $_smarty_tpl) {
?>

<style>
    scalapay-widget {
        all: initial;
        display: block;
    }

    <?php if (!empty($_smarty_tpl->tpl_vars['scalapay']->value['css'])) {?>
    <?php echo $_smarty_tpl->tpl_vars['scalapay']->value['css'];?>

    <?php }?>
</style>
<?php echo '<script'; ?>
 type="application/json" id="scalapayConfig"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'json_encode' ][ 0 ], array( $_smarty_tpl->tpl_vars['scalapay']->value["widgets"] ));
echo '</script'; ?>
>
<?php echo '<script'; ?>
>
    

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

    


<?php echo '</script'; ?>
>

<?php if ($_smarty_tpl->tpl_vars['scalapay']->value["requireScripts"]) {?>
    <?php echo '<script'; ?>
>
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
    <?php echo '</script'; ?>
>
<?php }
}
}
