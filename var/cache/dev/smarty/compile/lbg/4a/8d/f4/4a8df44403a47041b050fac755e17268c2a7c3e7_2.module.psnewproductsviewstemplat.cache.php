<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from 'module:psnewproductsviewstemplat' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d049eb59_99662207',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4a8df44403a47041b050fac755e17268c2a7c3e7' => 
    array (
      0 => 'module:psnewproductsviewstemplat',
      1 => 1738070829,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/productlist.tpl' => 1,
  ),
),false)) {
function content_683d49d049eb59_99662207 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '303430775683d49d049d656_82563286';
?>
<!-- begin /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_newproducts/views/templates/hook/ps_newproducts.tpl -->
<section id="home-new-products" class="featured-products clearfix mt-3">
  <h2 class="h2 products-section-title text-uppercase">
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'New products','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>

  </h2>
  <?php $_smarty_tpl->_subTemplateRender("file:catalog/_partials/productlist.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('products'=>$_smarty_tpl->tpl_vars['products']->value,'productClass'=>"col-xs-12 col-sm-6 col-lg-4 col-xl-3"), 0, false);
?>
  <a class="all-product-link float-xs-left float-md-right h4" href="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['allNewProductsLink']->value, ENT_QUOTES, 'UTF-8');?>
">
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Découvrir les nouveaux produits','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>

  </a>
</section>

<!-- end /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_newproducts/views/templates/hook/ps_newproducts.tpl --><?php }
}
