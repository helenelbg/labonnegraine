<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:32:15
  from '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/catalog/listing/new-products.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c366fea3492_29455442',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a45e84a2fc50a1568837c10e083f03bc58493f10' => 
    array (
      0 => '/home/helene/prestashop/modules/creativeelements/views/templates/front/theme/catalog/listing/new-products.tpl',
      1 => 1749809061,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_partials/microdata/product-list-jsonld.tpl' => 1,
  ),
),false)) {
function content_684c366fea3492_29455442 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
if ((isset($_smarty_tpl->tpl_vars['CE_LISTING_NEW_PRODUCTS']->value))) {?>
    <?php $_smarty_tpl->_assignInScope('ce_layout', $_smarty_tpl->tpl_vars['layout']->value);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/catalog/listing/new-products.tpl")) {?>
    <?php $_smarty_tpl->_assignInScope('ce_layout', '[1]catalog/listing/new-products.tpl');
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
    <?php $_smarty_tpl->_assignInScope('ce_layout', 'parent:catalog/listing/new-products.tpl');
}?>



<?php if ((isset($_smarty_tpl->tpl_vars['CE_LISTING_NEW_PRODUCTS']->value))) {?>
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1618949826684c366fea1408_33559692', 'head_microdata_special');
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_895304191684c366fea2301_62174866', 'content');
?>

<?php }
$_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['ce_layout']->value);
}
/* {block 'head_microdata_special'} */
class Block_1618949826684c366fea1408_33559692 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'head_microdata_special' => 
  array (
    0 => 'Block_1618949826684c366fea1408_33559692',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:_partials/microdata/product-list-jsonld.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('listing'=>$_smarty_tpl->tpl_vars['listing']->value), 0, false);
}
}
/* {/block 'head_microdata_special'} */
/* {block 'content'} */
class Block_895304191684c366fea2301_62174866 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_895304191684c366fea2301_62174866',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
<section id="content"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_LISTING_NEW_PRODUCTS']->value ))), ENT_QUOTES, 'UTF-8');?>
</section><?php
}
}
/* {/block 'content'} */
}
