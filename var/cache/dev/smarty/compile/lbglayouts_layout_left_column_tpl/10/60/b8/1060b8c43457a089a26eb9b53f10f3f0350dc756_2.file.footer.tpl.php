<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:13
  from '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/_partials/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35b9a8ba59_40477146',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1060b8c43457a089a26eb9b53f10f3f0350dc756' => 
    array (
      0 => '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/_partials/footer.tpl',
      1 => 1749809061,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:[1]_partials/footer.tpl' => 1,
    'parent:_partials/footer.tpl' => 1,
  ),
),false)) {
function content_684c35b9a8ba59_40477146 (Smarty_Internal_Template $_smarty_tpl) {
if ((isset($_smarty_tpl->tpl_vars['CE_FOOTER']->value))) {?>
	<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_FOOTER']->value ))), ENT_QUOTES, 'UTF-8');?>

<?php } elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/_partials/footer.tpl")) {?>
	<?php $_smarty_tpl->_subTemplateRender('file:[1]_partials/footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_subTemplateRender('parent:_partials/footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
}
