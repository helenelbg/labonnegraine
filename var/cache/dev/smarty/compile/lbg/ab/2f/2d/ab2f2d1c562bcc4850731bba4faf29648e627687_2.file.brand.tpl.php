<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from '/home/dev.labonnegraine.com/public_html/modules/ets_crosssell/views/templates/hook/brand.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff4e6d22_54411360',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ab2f2d1c562bcc4850731bba4faf29648e627687' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ets_crosssell/views/templates/hook/brand.tpl',
      1 => 1742303612,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff4e6d22_54411360 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'There is no brand on your site.','mod'=>'ets_crosssell'),$_smarty_tpl ) );?>
 <a href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminManufacturers'),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Click here','mod'=>'ets_crosssell'),$_smarty_tpl ) );?>
</a> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'to add new brand','mod'=>'ets_crosssell'),$_smarty_tpl ) );
}
}
