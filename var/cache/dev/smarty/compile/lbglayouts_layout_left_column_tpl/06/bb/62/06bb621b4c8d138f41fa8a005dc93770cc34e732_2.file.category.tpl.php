<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/catalog/listing/category.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dc8e673_65263120',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '06bb621b4c8d138f41fa8a005dc93770cc34e732' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/catalog/listing/category.tpl',
      1 => 1738070993,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_partials/microdata/product-list-jsonld.tpl' => 1,
  ),
),false)) {
function content_683fe28dc8e673_65263120 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
if ((isset($_smarty_tpl->tpl_vars['CE_LISTING_CATEGORY']->value))) {?>
    <?php $_smarty_tpl->_assignInScope('ce_layout', $_smarty_tpl->tpl_vars['layout']->value);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/catalog/listing/category.tpl")) {?>
    <?php $_smarty_tpl->_assignInScope('ce_layout', '[1]catalog/listing/category.tpl');
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
    <?php $_smarty_tpl->_assignInScope('ce_layout', 'parent:catalog/listing/category.tpl');
}?>



<?php if ((isset($_smarty_tpl->tpl_vars['CE_LISTING_CATEGORY']->value))) {?>
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1606311374683fe28dc8d152_59992652', 'head_microdata_special');
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_855820475683fe28dc8dee8_67879052', 'content');
?>

<?php }
$_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['ce_layout']->value);
}
/* {block 'head_microdata_special'} */
class Block_1606311374683fe28dc8d152_59992652 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'head_microdata_special' => 
  array (
    0 => 'Block_1606311374683fe28dc8d152_59992652',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:_partials/microdata/product-list-jsonld.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('listing'=>$_smarty_tpl->tpl_vars['listing']->value), 0, false);
}
}
/* {/block 'head_microdata_special'} */
/* {block 'content'} */
class Block_855820475683fe28dc8dee8_67879052 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_855820475683fe28dc8dee8_67879052',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
<section id="content"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_LISTING_CATEGORY']->value )), ENT_QUOTES, 'UTF-8');?>
</section><?php
}
}
/* {/block 'content'} */
}
