<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/product-discounts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0a344b3_24406940',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dcd270a25316f3c83ca839c71dfc7af413c32346' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/product-discounts.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0a344b3_24406940 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>

<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity_discounts']) {?>
<section class="product-discounts js-product-discounts">
    <p class="h6 product-discounts-title"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Volume discounts','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
</p>
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2010477668683d49d0a329c8_99796754', 'product_discount_table');
?>

</section>
<?php }
}
/* {block 'product_discount_table'} */
class Block_2010477668683d49d0a329c8_99796754 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_discount_table' => 
  array (
    0 => 'Block_2010477668683d49d0a329c8_99796754',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	  
	  <div class="std degressif-table">
	  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value['quantity_discounts'], 'quantity_discount', false, NULL, 'quantity_discounts', array (
));
$_smarty_tpl->tpl_vars['quantity_discount']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['quantity_discount']->value) {
$_smarty_tpl->tpl_vars['quantity_discount']->do_else = false;
?>
		<div class="ligne">
			<div class="title">A partir de <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['quantity_discount']->value['quantity'], ENT_QUOTES, 'UTF-8');?>
</div>
			<div class="pourcent">-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['quantity_discount']->value['discount'], ENT_QUOTES, 'UTF-8');?>
</div>
			<strike class="prix-barre"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['regular_price'], ENT_QUOTES, 'UTF-8');?>
</strike>
			<div class="priceWithReduc"><?php echo htmlspecialchars((string) Tools::displayPrice($_smarty_tpl->tpl_vars['product']->value['regular_price_amount']*(1-0.01*$_smarty_tpl->tpl_vars['quantity_discount']->value['real_value'])), ENT_QUOTES, 'UTF-8');?>
</div>
		</div>
      <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	  </div>
	    
    <?php
}
}
/* {/block 'product_discount_table'} */
}
