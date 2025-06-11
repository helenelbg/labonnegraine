<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from 'module:psfeaturedproductsviewste' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d00e5d28_86822678',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fa6cc378d2942c8857b89d6bca728048c0caeedd' => 
    array (
      0 => 'module:psfeaturedproductsviewste',
      1 => 1738070829,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/productlist.tpl' => 1,
  ),
),false)) {
function content_683d49d00e5d28_86822678 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '580767390683d49d00e4978_76300790';
?>
<!-- begin /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl --><section id="home-featured-products" class="featured-products clearfix">
  <h1><img data-lazy-src="/themes/lbg/assets/img/lbg-h1.png" loading="lazy"  /> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'VOUS CONSEILLE','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
</h1>
  <?php $_smarty_tpl->_subTemplateRender("file:catalog/_partials/productlist.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('products'=>$_smarty_tpl->tpl_vars['products']->value,'cssClass'=>"row",'productClass'=>"col-xs-12 col-sm-6 col-lg-4 col-xl-3"), 0, false);
?>
</section>
<!-- end /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl --><?php }
}
