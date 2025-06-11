<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/product-add-to-cart.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0a386d5_22313426',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bd26c0e1a602f6582692849f853f6eea68b6d95a' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/product-add-to-cart.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0a386d5_22313426 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<div class="product-add-to-cart js-product-add-to-cart">
    <?php if (!$_smarty_tpl->tpl_vars['configuration']->value['is_catalog']) {?>
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1425007795683d49d0a35bc9_91121207', 'product_minimal_quantity');
?>

		
		<div class="remise-wrap">
			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayQuantityDiscountProCustom1','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

		</div>
		
		<div class="product-add-to-cart clearfix">

            <?php if ($_smarty_tpl->tpl_vars['product']->value['availability'] == 'last_remaining_items') {?>
                <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['availability_message'], ENT_QUOTES, 'UTF-8');?>

            <?php } else { ?>
                <?php if ($_smarty_tpl->tpl_vars['product']->value['quantity'] > 0) {?>
                    <?php if ($_smarty_tpl->tpl_vars['isSerres']->value && !$_smarty_tpl->tpl_vars['isNATURA']->value) {?>
						<p id="popinSerresBtn" class="buttons_bottom_block no-print">
							<button type="button" class="exclusive">
								<span>Demande d'informations</span>
							</button>
						</p>
					<?php } else { ?>
						 <button
                                class="btn btn-primary add-to-cart"
                                data-button-action="add-to-cart"
                                type="submit"
                                <?php if (!$_smarty_tpl->tpl_vars['product']->value['add_to_cart_url']) {?>
                                    disabled
                                <?php }?>
                        >
                            <i class="material-icons shopping-cart">&#xE547;</i>
                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add to cart','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>

                        </button>
                    <?php }?>
                <?php } else { ?>
                    <div class="product-additional-info js-product-additional-info">
              <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductAdditionalInfo','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

            </div>
                <?php }?>
            <?php }?>
			

			

					

			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductActions','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

		</div>
    <?php }?>
</div>
<?php }
/* {block 'product_minimal_quantity'} */
class Block_1425007795683d49d0a35bc9_91121207 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_minimal_quantity' => 
  array (
    0 => 'Block_1425007795683d49d0a35bc9_91121207',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <p class="product-minimal-quantity js-product-minimal-quantity">
                <?php if ($_smarty_tpl->tpl_vars['product']->value['minimal_quantity'] > 1) {?>
                    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'The minimum purchase order quantity for the product is %quantity%.','d'=>'Shop.Theme.Checkout','sprintf'=>array('%quantity%'=>$_smarty_tpl->tpl_vars['product']->value['minimal_quantity'])),$_smarty_tpl ) );?>

                <?php }?>
            </p>
        <?php
}
}
/* {/block 'product_minimal_quantity'} */
}
