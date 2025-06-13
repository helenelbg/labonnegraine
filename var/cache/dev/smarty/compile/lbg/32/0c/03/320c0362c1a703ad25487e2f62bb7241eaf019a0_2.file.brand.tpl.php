<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:26:43
  from '/home/helene/prestashop/modules/ets_crosssell/views/templates/hook/brand.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35230f6ab0_88187681',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '320c0362c1a703ad25487e2f62bb7241eaf019a0' => 
    array (
      0 => '/home/helene/prestashop/modules/ets_crosssell/views/templates/hook/brand.tpl',
      1 => 1749809004,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35230f6ab0_88187681 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'There is no brand on your site.','mod'=>'ets_crosssell'),$_smarty_tpl ) );?>
 <a href="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminManufacturers'),'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Click here','mod'=>'ets_crosssell'),$_smarty_tpl ) );?>
</a> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'to add new brand','mod'=>'ets_crosssell'),$_smarty_tpl ) );
}
}
