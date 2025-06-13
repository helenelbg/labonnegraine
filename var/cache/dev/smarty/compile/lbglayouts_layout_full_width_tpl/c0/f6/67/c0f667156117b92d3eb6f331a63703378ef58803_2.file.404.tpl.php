<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:32:01
  from '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/errors/404.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c366112b8f2_31789014',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c0f667156117b92d3eb6f331a63703378ef58803' => 
    array (
      0 => '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/errors/404.tpl',
      1 => 1749809061,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c366112b8f2_31789014 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
if ((isset($_smarty_tpl->tpl_vars['CE_PAGE_NOT_FOUND']->value))) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', $_smarty_tpl->tpl_vars['layout']->value);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/errors/404.tpl")) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', '[1]errors/404.tpl');
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', 'parent:errors/404.tpl');
}?>



<?php if ((isset($_smarty_tpl->tpl_vars['CE_PAGE_NOT_FOUND']->value))) {?>
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_53701502684c366112aa26_95368999', 'content');
?>

<?php }
$_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['ce_layout']->value);
}
/* {block 'content'} */
class Block_53701502684c366112aa26_95368999 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_53701502684c366112aa26_95368999',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
<section id="content"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_PAGE_NOT_FOUND']->value ))), ENT_QUOTES, 'UTF-8');?>
</section><?php
}
}
/* {/block 'content'} */
}
