<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/errors/not-found.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff7d3616_04510304',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6166c1e1817da2a67a392b302be59b76c13aab3d' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/errors/not-found.tpl',
      1 => 1738070993,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:[1]errors/not-found.tpl' => 1,
    'parent:errors/not-found.tpl' => 1,
  ),
),false)) {
function content_68304cff7d3616_04510304 (Smarty_Internal_Template $_smarty_tpl) {
$_prefixVariable1 = Configuration::get('CE_LISTING_NO_RESULTS');
$_smarty_tpl->_assignInScope('id', $_prefixVariable1);
if ($_prefixVariable1) {?>
	<?php echo htmlspecialchars((string) call_user_func('CE\\setup_postdata',ce_new('CE\\UId',$_smarty_tpl->tpl_vars['id']->value,17,$_smarty_tpl->tpl_vars['language']->value['id'])), ENT_QUOTES, 'UTF-8');?>

	<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( call_user_func('CE\\apply_filters','the_content','') )), ENT_QUOTES, 'UTF-8');?>

<?php } elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/errors/not-found.tpl")) {?>
	<?php $_smarty_tpl->_subTemplateRender('file:[1]errors/not-found.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_subTemplateRender('parent:errors/not-found.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
}
