<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:32:01
  from '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/errors/not-found.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c36615c78f6_70681852',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ea39ceebe5dd484117962d235389ce5dee591161' => 
    array (
      0 => '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/errors/not-found.tpl',
      1 => 1749809061,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:[1]errors/not-found.tpl' => 1,
    'parent:errors/not-found.tpl' => 1,
  ),
),false)) {
function content_684c36615c78f6_70681852 (Smarty_Internal_Template $_smarty_tpl) {
$_prefixVariable1 = Configuration::get('CE_LISTING_NO_RESULTS');
$_smarty_tpl->_assignInScope('id', $_prefixVariable1);
if ($_prefixVariable1) {?>
	<?php echo htmlspecialchars((string) (call_user_func('CE\\setup_postdata',ce_new('CE\\UId',$_smarty_tpl->tpl_vars['id']->value,17,$_smarty_tpl->tpl_vars['language']->value['id']))), ENT_QUOTES, 'UTF-8');?>

	<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( call_user_func('CE\\apply_filters','the_content','') ))), ENT_QUOTES, 'UTF-8');?>

<?php } elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/errors/not-found.tpl")) {?>
	<?php $_smarty_tpl->_subTemplateRender('file:[1]errors/not-found.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_subTemplateRender('parent:errors/not-found.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
}
