<?php
/* Smarty version 4.2.1, created on 2025-06-10 09:12:23
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/contact.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_6847dad7535c76_66859800',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d6d854007a5777d68a3dfe6fa5f8d5485dee277' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/contact.tpl',
      1 => 1738070993,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6847dad7535c76_66859800 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
if ((isset($_smarty_tpl->tpl_vars['CE_PAGE_CONTACT']->value))) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', $_smarty_tpl->tpl_vars['layout']->value);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/contact.tpl")) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', '[1]contact.tpl');
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', 'parent:contact.tpl');
}?>



<?php if ((isset($_smarty_tpl->tpl_vars['CE_PAGE_CONTACT']->value))) {?>
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_9792535816847dad75351f6_43220277', 'content');
?>

<?php }
$_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['ce_layout']->value);
}
/* {block 'content'} */
class Block_9792535816847dad75351f6_43220277 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_9792535816847dad75351f6_43220277',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
<section id="content"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_PAGE_CONTACT']->value )), ENT_QUOTES, 'UTF-8');?>
</section><?php
}
}
/* {/block 'content'} */
}
