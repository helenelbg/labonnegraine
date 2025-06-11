<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/catalog/product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d097a659_49786922',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1d4ec5f189bd7faef35baa61cac27db33ce4f7c6' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/catalog/product.tpl',
      1 => 1738070993,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/microdata/product-jsonld.tpl' => 1,
  ),
),false)) {
function content_683d49d097a659_49786922 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
if ((isset($_smarty_tpl->tpl_vars['CE_PRODUCT']->value))) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', $_smarty_tpl->tpl_vars['layout']->value);
} elseif (file_exists(((string)(defined('_PS_THEME_DIR_') ? constant('_PS_THEME_DIR_') : null))."templates/catalog/product.tpl")) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', '[1]catalog/product.tpl');
} elseif ((defined('_PARENT_THEME_NAME_') ? constant('_PARENT_THEME_NAME_') : null)) {?>
	<?php $_smarty_tpl->_assignInScope('ce_layout', 'parent:catalog/product.tpl');
}?>



<?php if ((isset($_smarty_tpl->tpl_vars['CE_PRODUCT']->value))) {?>
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_310759957683d49d0970699_97581594', 'head');
?>


	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1703829110683d49d0976b97_14274829', 'content');
?>

<?php }
$_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['ce_layout']->value);
}
/* {block 'head'} */
class Block_310759957683d49d0970699_97581594 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'head' => 
  array (
    0 => 'Block_310759957683d49d0970699_97581594',
  ),
);
public $append = 'true';
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/microdata/product-jsonld.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

	<meta property="og:type" content="product">
	<?php if (version_compare((defined('_PS_VERSION_') ? constant('_PS_VERSION_') : null),'1.7.8','<')) {?>
		<meta property="og:title" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['page']->value['meta']['title'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="og:description" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['page']->value['meta']['description'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="og:url" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['urls']->value['current_url'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="og:site_name" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8');?>
">
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['product']->value['cover']) {?>
		<meta property="og:image" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['cover']['large']['url'], ENT_QUOTES, 'UTF-8');?>
">
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['product']->value['show_price']) {?>
		<meta property="product:pretax_price:amount" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['price_tax_exc'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="product:pretax_price:currency" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['currency']->value['iso_code'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="product:price:amount" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['price_amount'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="product:price:currency" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['currency']->value['iso_code'], ENT_QUOTES, 'UTF-8');?>
">
	<?php }?>
	<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['weight'])) {?>
		<meta property="product:weight:value" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['weight'], ENT_QUOTES, 'UTF-8');?>
">
		<meta property="product:weight:units" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['weight_unit'], ENT_QUOTES, 'UTF-8');?>
">
	<?php }?>
	<?php
}
}
/* {/block 'head'} */
/* {block 'content'} */
class Block_1703829110683d49d0976b97_14274829 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_1703829110683d49d0976b97_14274829',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<section id="content" style="max-width: none">
		<form id="add-to-cart-or-refresh" action="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['urls']->value['pages']['cart'], ENT_QUOTES, 'UTF-8');?>
" method="post" style="display:none">
			<input type="hidden" name="token" value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['static_token']->value, ENT_QUOTES, 'UTF-8');?>
">
			<input type="hidden" name="id_product" value="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['product']->value['id']), ENT_QUOTES, 'UTF-8');?>
" id="product_page_product_id">
			<input type="hidden" name="id_customization" value="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['product']->value['id_customization']), ENT_QUOTES, 'UTF-8');?>
" id="product_customization_id">
			<input type="hidden" name="qty" value="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['product']->value['quantity_wanted']), ENT_QUOTES, 'UTF-8');?>
" id="quantity_wanted"
				<?php if ($_smarty_tpl->tpl_vars['product']->value['show_quantities']) {?>data-stock="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['quantity'], ENT_QUOTES, 'UTF-8');?>
" data-allow-oosp="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['allow_oosp'], ENT_QUOTES, 'UTF-8');?>
"<?php }?>>
			<input type="submit" class="ce-add-to-cart" data-button-action="add-to-cart">
		</form>
		<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'cefilter' ][ 0 ], array( $_smarty_tpl->tpl_vars['CE_PRODUCT']->value )), ENT_QUOTES, 'UTF-8');?>

	</section>
	<?php
}
}
/* {/block 'content'} */
}
