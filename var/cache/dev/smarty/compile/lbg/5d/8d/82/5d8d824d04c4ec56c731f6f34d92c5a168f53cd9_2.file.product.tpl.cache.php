<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/catalog/_partials/miniatures/product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d00ee283_70512735',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d8d824d04c4ec56c731f6f34d92c5a168f53cd9' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/catalog/_partials/miniatures/product.tpl',
      1 => 1738070992,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/miniatures/product-".((string)$_smarty_tpl->tpl_vars[\'CE_PRODUCT_MINIATURE_UID\']->value).".tpl' => 1,
    'file:[1]catalog/_partials/miniatures/product.tpl' => 1,
    'parent:catalog/_partials/miniatures/product.tpl' => 1,
  ),
),false)) {
function content_683d49d00ee283_70512735 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '1180919436683d49d00ea2c0_76729058';
if ((isset($_smarty_tpl->tpl_vars['CE_PRODUCT_MINIATURE_UID']->value)) && get_class($_smarty_tpl->tpl_vars['CE_PRODUCT_MINIATURE_UID']->value) === 'CE\\UId' && (file_exists(((string)(defined('_CE_TEMPLATES_') ? constant('_CE_TEMPLATES_') : null))."front/theme/catalog/_partials/miniatures/product-".((string)$_smarty_tpl->tpl_vars['CE_PRODUCT_MINIATURE_UID']->value).".tpl") || CE\Plugin::$instance->documents->get($_smarty_tpl->tpl_vars['CE_PRODUCT_MINIATURE_UID']->value)->saveTpl())) {?>
	<?php if (!(isset($_smarty_tpl->tpl_vars['productClasses']->value))) {?>
		<?php if (!(isset($_smarty_tpl->tpl_vars['layout']->value))) {?>
			<?php $_smarty_tpl->_assignInScope('layout', Context::getContext()->controller->getLayout());?>
		<?php }?>
		<?php if (preg_match('/(left|right|both)-column/',$_smarty_tpl->tpl_vars['layout']->value)) {?>
			<?php $_smarty_tpl->_assignInScope('productClasses', 'col-xs-6 col-xl-4');?>
		<?php } else { ?>
			<?php $_smarty_tpl->_assignInScope('productClasses', 'col-xs-6 col-xl-3');?>
		<?php }?>
	<?php }?>
	<?php $_smarty_tpl->_subTemplateRender("file:catalog/_partials/miniatures/product-".((string)$_smarty_tpl->tpl_vars['CE_PRODUCT_MINIATURE_UID']->value).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, true);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/catalog/_partials/miniatures/product.tpl")) {?>
	<?php $_smarty_tpl->_subTemplateRender('file:[1]catalog/_partials/miniatures/product.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, false);
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_subTemplateRender('parent:catalog/_partials/miniatures/product.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
}
