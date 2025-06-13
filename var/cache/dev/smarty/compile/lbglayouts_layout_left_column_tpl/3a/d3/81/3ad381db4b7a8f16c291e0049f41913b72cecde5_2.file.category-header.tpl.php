<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:00
  from '/home/helene/prestashop/themes/lbg/templates/catalog/_partials/category-header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35ac0e0959_35050257',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3ad381db4b7a8f16c291e0049f41913b72cecde5' => 
    array (
      0 => '/home/helene/prestashop/themes/lbg/templates/catalog/_partials/category-header.tpl',
      1 => 1749808842,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35ac0e0959_35050257 (Smarty_Internal_Template $_smarty_tpl) {
?> 
<h1 id="js-product-list-header" class="h2">
	<span class="cat-name"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['listing']->value['label']), ENT_QUOTES, 'UTF-8');?>
</span>
	<?php if ((isset($_smarty_tpl->tpl_vars['category']->value))) {?>
	  <span class="heading-counter">
		 - <span class="heading-counter-products"><?php echo htmlspecialchars((string) (Product::countInCategory($_smarty_tpl->tpl_vars['category']->value['id'])), ENT_QUOTES, 'UTF-8');?>
 produits.</span>
	  </span>
	<?php }?>
</h1>
<?php }
}
