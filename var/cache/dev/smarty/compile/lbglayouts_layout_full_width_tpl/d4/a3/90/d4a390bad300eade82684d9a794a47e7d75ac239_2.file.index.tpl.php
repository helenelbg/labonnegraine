<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d055e907_85839511',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd4a390bad300eade82684d9a794a47e7d75ac239' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/index.tpl',
      1 => 1738070992,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d055e907_85839511 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
if ((isset($_smarty_tpl->tpl_vars['CE_PAGE_INDEX']->value))) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', $_smarty_tpl->tpl_vars['layout']->value);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/index.tpl")) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', '[1]index.tpl');
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', 'parent:index.tpl');
}?>



<?php if ((isset($_smarty_tpl->tpl_vars['CE_PAGE_INDEX']->value))) {?>
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1392705894683d49d055e1c4_57121425', _q_c_('alysum' === (defined('_THEME_NAME_') ? constant('_THEME_NAME_') : null) || 'alysum' === (defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null),'main','content'));
?>

<?php }
$_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['ce_layout']->value);
}
/* {block _q_c_('alysum' === (defined('_THEME_NAME_') ? constant('_THEME_NAME_') : null) || 'alysum' === (defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null),'main','content')} */
class Block_1392705894683d49d055e1c4_57121425 extends Smarty_Internal_Block
{
public $subBlocks = array (
  '_q_c_(\'alysum\' === (defined(\'_THEME_NAME_\') ? constant(\'_THEME_NAME_\') : null) || \'alysum\' === (defined(\'_PARENT_THEME_NAME_\') ? constant(\'_PARENT_THEME_NAME_\') : null),\'main\',\'content\')' => 
  array (
    0 => 'Block_1392705894683d49d055e1c4_57121425',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<section id="content"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_PAGE_INDEX']->value )), ENT_QUOTES, 'UTF-8');?>
</section>
	<?php
}
}
/* {/block _q_c_('alysum' === (defined('_THEME_NAME_') ? constant('_THEME_NAME_') : null) || 'alysum' === (defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null),'main','content')} */
}
