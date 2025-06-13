<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:28:58
  from '/home/helene/prestashop/modules/cdc_googletagmanager/views/templates/hook/gtm_tag.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35aacf45f7_99145227',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '76568f102e56b7b4880ebccd8438ea14c3b4e7c6' => 
    array (
      0 => '/home/helene/prestashop/modules/cdc_googletagmanager/views/templates/hook/gtm_tag.tpl',
      1 => 1749809085,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35aacf45f7_99145227 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 data-keepinline="true">
    var ajaxGetProductUrl = '<?php if (!empty($_smarty_tpl->tpl_vars['ajaxGetProductUrl']->value)) {
echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['ajaxGetProductUrl']->value), ENT_QUOTES, 'UTF-8');
}?>';
    var ajaxShippingEvent = <?php if ((isset($_smarty_tpl->tpl_vars['ajaxShippingEvent']->value))) {
echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['ajaxShippingEvent']->value), ENT_QUOTES, 'UTF-8');
} else { ?>1<?php }?>;
    var ajaxPaymentEvent = <?php if ((isset($_smarty_tpl->tpl_vars['ajaxPaymentEvent']->value))) {
echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['ajaxPaymentEvent']->value), ENT_QUOTES, 'UTF-8');
} else { ?>1<?php }?>;

/* datalayer */
dataLayer = window.dataLayer || [];
<?php if (!empty($_smarty_tpl->tpl_vars['preDataLayer']->value)) {?>dataLayer.push(<?php echo $_smarty_tpl->tpl_vars['preDataLayer']->value;?>
);<?php }
if (!empty($_smarty_tpl->tpl_vars['dataLayer']->value)) {?>
    let cdcDatalayer = <?php echo $_smarty_tpl->tpl_vars['dataLayer']->value;?>
;
    dataLayer.push(cdcDatalayer);
<?php }?>

/* call to GTM Tag */
<?php if (!(isset($_smarty_tpl->tpl_vars['load_gtm_script']->value)) || $_smarty_tpl->tpl_vars['load_gtm_script']->value) {?>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'<?php echo $_smarty_tpl->tpl_vars['google_script_server_url']->value;?>
?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $_smarty_tpl->tpl_vars['gtm_id']->value;?>
');
<?php }?>

/* async call to avoid cache system for dynamic data */
<?php if ($_smarty_tpl->tpl_vars['async_user_info']->value) {?>
var cdcgtmreq = new XMLHttpRequest();
cdcgtmreq.onreadystatechange = function() {
    if (cdcgtmreq.readyState == XMLHttpRequest.DONE ) {
        if (cdcgtmreq.status == 200) {
          	var datalayerJs = cdcgtmreq.responseText;
            try {
                var datalayerObj = JSON.parse(datalayerJs);
                dataLayer = dataLayer || [];
                dataLayer.push(datalayerObj);
            } catch(e) {
               console.log("[CDCGTM] error while parsing json");
            }

            <?php if ($_smarty_tpl->tpl_vars['gtm_debug']->value) {?>
            // display debug
            console.log('[CDCGTM] DEBUG ENABLED');
            console.log(datalayerObj);
            document.addEventListener('DOMContentLoaded', function() {
              if(document.getElementById("cdcgtm_debug_asynccall")) {
                  document.getElementById("cdcgtm_debug_asynccall").innerHTML = datalayerJs;
              }
            }, false);
            <?php }?>
        }
        dataLayer.push({
          'event': '<?php echo $_smarty_tpl->tpl_vars['event_datalayer_ready']->value;?>
'
        });
    }
};
cdcgtmreq.open("GET", "<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['async_url']->value), ENT_QUOTES, 'UTF-8');?>
" /*+ "?" + new Date().getTime()*/, true);
cdcgtmreq.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
cdcgtmreq.send();
<?php } else { ?>
dataLayer.push({
  'event': '<?php echo $_smarty_tpl->tpl_vars['event_datalayer_ready']->value;?>
'
});
<?php }
echo '</script'; ?>
><?php }
}
