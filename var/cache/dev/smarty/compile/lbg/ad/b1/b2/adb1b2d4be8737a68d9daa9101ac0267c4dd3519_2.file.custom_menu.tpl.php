<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from '/home/dev.labonnegraine.com/public_html/modules/ets_megamenu/views/templates/hook/custom_menu.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff7bd109_23318168',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'adb1b2d4be8737a68d9daa9101ac0267c4dd3519' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ets_megamenu/views/templates/hook/custom_menu.tpl',
      1 => 1738070952,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff7bd109_23318168 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['ETS_MM_DISPLAY_SHOPPING_CART']->value || $_smarty_tpl->tpl_vars['ETS_MM_DISPLAY_SEARCH']->value || $_smarty_tpl->tpl_vars['ETS_MM_DISPLAY_CUSTOMER_INFO']->value || $_smarty_tpl->tpl_vars['ETS_MM_CUSTOM_HTML_TEXT']->value) {?>
    <div class="mm_extra_item<?php if ($_smarty_tpl->tpl_vars['ETS_MM_SEARCH_DISPLAY_DEFAULT']->value) {?> mm_display_search_default<?php }?>">
        <?php if ($_smarty_tpl->tpl_vars['ETS_MM_CUSTOM_HTML_TEXT']->value) {?>
            <div class="mm_custom_text">
                <?php echo $_smarty_tpl->tpl_vars['ETS_MM_CUSTOM_HTML_TEXT']->value;?>

            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['ETS_MM_DISPLAY_SEARCH']->value) {?>
            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displaySearch'),$_smarty_tpl ) );?>

        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['ETS_MM_DISPLAY_CUSTOMER_INFO']->value) {?>
            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayCustomerInforTop'),$_smarty_tpl ) );?>

        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['ETS_MM_DISPLAY_SHOPPING_CART']->value) {?>
            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayCartTop'),$_smarty_tpl ) );?>

        <?php }?>
    </div>
<?php }
}
}
