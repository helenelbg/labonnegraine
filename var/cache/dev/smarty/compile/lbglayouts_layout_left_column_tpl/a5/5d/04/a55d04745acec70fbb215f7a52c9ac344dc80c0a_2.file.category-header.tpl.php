<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/category-header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dd326c0_56394386',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a55d04745acec70fbb215f7a52c9ac344dc80c0a' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/category-header.tpl',
      1 => 1743417086,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683fe28dd326c0_56394386 (Smarty_Internal_Template $_smarty_tpl) {
?> 
<h1 id="js-product-list-header" class="h2">
	<span class="cat-name"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['listing']->value['label'], ENT_QUOTES, 'UTF-8');?>
</span>
	<?php if ((isset($_smarty_tpl->tpl_vars['category']->value))) {?>
	  <span class="heading-counter">
		 - <span class="heading-counter-products"><?php echo htmlspecialchars((string) Product::countInCategory($_smarty_tpl->tpl_vars['category']->value['id']), ENT_QUOTES, 'UTF-8');?>
 produits.</span>
	  </span>
	<?php }?>
</h1>
<?php }
}
