<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:14
  from '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/_partials/javascript.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35ba6ce961_98198962',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b7a29dc85df35e77e8c8deafcb74d50ab024b454' => 
    array (
      0 => '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/_partials/javascript.tpl',
      1 => 1749809061,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:[1]_partials/javascript.tpl' => 1,
    'parent:_partials/javascript.tpl' => 1,
  ),
),false)) {
function content_684c35ba6ce961_98198962 (Smarty_Internal_Template $_smarty_tpl) {
if (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/_partials/javascript.tpl")) {?>
	<?php $_smarty_tpl->_subTemplateRender('file:[1]_partials/javascript.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_subTemplateRender('parent:_partials/javascript.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}?>
<!--CE-JS--><?php }
}
