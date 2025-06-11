<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d09b5107_79022309',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '90e2ec61b8938b72ad97966cfebe884a010130f2' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/product.tpl',
      1 => 1744029252,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_partials/microdata/product-jsonld.tpl' => 1,
    'file:catalog/product-wkpack.tpl' => 1,
    'file:catalog/_partials/product-flags.tpl' => 1,
    'file:catalog/_partials/product-cover-thumbnails.tpl' => 1,
    'file:catalog/jardin-essai.tpl' => 2,
    'file:catalog/calendrier.tpl' => 2,
    'file:catalog/_partials/product-customization.tpl' => 1,
    'file:catalog/plant-precommande.tpl' => 1,
    'file:catalog/equivalent-plant.tpl' => 1,
    'file:catalog/_partials/product-variants.tpl' => 1,
    'file:catalog/_partials/miniatures/pack-product.tpl' => 1,
    'file:catalog/_partials/product-prices.tpl' => 1,
    'file:catalog/_partials/product-discounts.tpl' => 1,
    'file:catalog/_partials/product-add-to-cart.tpl' => 1,
    'file:catalog/_partials/product-images-modal.tpl' => 1,
  ),
),false)) {
function content_683d49d09b5107_79022309 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_409785972683d49d097df56_28085101', 'head');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_646202932683d49d0982128_25408398', 'head_microdata_special');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_389748062683d49d0982aa0_07451986', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['layout']->value);
}
/* {block 'head'} */
class Block_409785972683d49d097df56_28085101 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'head' => 
  array (
    0 => 'Block_409785972683d49d097df56_28085101',
  ),
);
public $append = 'true';
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <meta property="og:type" content="product">
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
  <?php if ((isset($_smarty_tpl->tpl_vars['product']->value['weight'])) && ($_smarty_tpl->tpl_vars['product']->value['weight'] != 0)) {?>
  <meta property="product:weight:value" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['weight'], ENT_QUOTES, 'UTF-8');?>
">
  <meta property="product:weight:units" content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['weight_unit'], ENT_QUOTES, 'UTF-8');?>
">
  <?php }
}
}
/* {/block 'head'} */
/* {block 'head_microdata_special'} */
class Block_646202932683d49d0982128_25408398 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'head_microdata_special' => 
  array (
    0 => 'Block_646202932683d49d0982128_25408398',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <?php $_smarty_tpl->_subTemplateRender('file:_partials/microdata/product-jsonld.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
/* {/block 'head_microdata_special'} */
/* {block 'page_title'} */
class Block_499227059683d49d0983ee3_16737021 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['name'], ENT_QUOTES, 'UTF-8');
}
}
/* {/block 'page_title'} */
/* {block 'page_header'} */
class Block_431520987683d49d0983b59_78702787 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

        <h1 class="h1"><?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_499227059683d49d0983ee3_16737021', 'page_title', $this->tplIndex);
?>
</h1><button onclick="window.history.go(-1)" class="retour_page_produit"></button>
      <?php
}
}
/* {/block 'page_header'} */
/* {block 'page_header_container'} */
class Block_2096700247683d49d0983805_48897084 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_431520987683d49d0983b59_78702787', 'page_header', $this->tplIndex);
?>

    <?php
}
}
/* {/block 'page_header_container'} */
/* {block 'product_cover_thumbnails'} */
class Block_570026511683d49d0986447_62644289 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-cover-thumbnails.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
              <?php
}
}
/* {/block 'product_cover_thumbnails'} */
/* {block 'page_content'} */
class Block_443513069683d49d0985da8_51235729 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

              <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-flags.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

              <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_570026511683d49d0986447_62644289', 'product_cover_thumbnails', $this->tplIndex);
?>

              <div class="scroll-box-arrows">
                <i class="material-icons left">&#xE314;</i>
                <i class="material-icons right">&#xE315;</i>
              </div>

            <?php
}
}
/* {/block 'page_content'} */
/* {block 'page_content_container'} */
class Block_1438434378683d49d0985a69_31323872 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <section class="page-content" id="content">
            <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_443513069683d49d0985da8_51235729', 'page_content', $this->tplIndex);
?>

          </section>
		  
		  <div class="fiche-produit-vente-flash">
		    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductFlash','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

		  </div>
		  
		  <div class="desktop">
		    <?php $_smarty_tpl->_subTemplateRender('file:catalog/jardin-essai.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
		  </div>
		  
			
				
        <?php
}
}
/* {/block 'page_content_container'} */
/* {block 'calendrier'} */
class Block_1865625917683d49d098cf73_57877875 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

					<?php $_smarty_tpl->_subTemplateRender('file:catalog/calendrier.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
				<?php
}
}
/* {/block 'calendrier'} */
/* {block 'product_description_short'} */
class Block_1263863529683d49d098ad86_29263155 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

			  <?php $_smarty_tpl->_assignInScope('desc_nosemi', explode("Semis",$_smarty_tpl->tpl_vars['product']->value['description_short']));?>
              <div id="product-description-short-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['id'], ENT_QUOTES, 'UTF-8');?>
" class="product-description">
			    <?php if (!empty($_smarty_tpl->tpl_vars['expedProdDesc']->value)) {?>
					<p class="expedTop"><?php echo $_smarty_tpl->tpl_vars['expedProdDesc']->value;?>
</p>
				<?php }?>
			  	<?php echo $_smarty_tpl->tpl_vars['desc_nosemi']->value[0];?>

			  </div>
			  
			  <div class="div_conditions_de_culture desktop">
				<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1865625917683d49d098cf73_57877875', 'calendrier', $this->tplIndex);
?>

			  </div>
			  
            <?php
}
}
/* {/block 'product_description_short'} */
/* {block 'product_customization'} */
class Block_1033827388683d49d098e6b9_67701380 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                <?php $_smarty_tpl->_subTemplateRender("file:catalog/_partials/product-customization.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('customizations'=>$_smarty_tpl->tpl_vars['product']->value['customizations']), 0, false);
?>
              <?php
}
}
/* {/block 'product_customization'} */
/* {block 'product_variants'} */
class Block_1000169773683d49d0991d10_30084433 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

						  <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-variants.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
						<?php
}
}
/* {/block 'product_variants'} */
/* {block 'product_quantity'} */
class Block_24775094683d49d0992663_18285072 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

							<div class="product-quantity clearfix">
								<label for="quantity_wanted">Quantité : </label><br />
								<div class="qty">
									<input
											type="number"
											name="qty"
											id="quantity_wanted"
											inputmode="numeric"
											pattern="[0-9]*"
											<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity_wanted']) {?>
												value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['quantity_wanted'], ENT_QUOTES, 'UTF-8');?>
"
												min="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['minimal_quantity'], ENT_QUOTES, 'UTF-8');?>
"
											<?php } else { ?>
												value="1"
												min="1"
											<?php }?>
											class="input-group"
											aria-label="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Quantity','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>
"
									>
								</div>
							</div>
						<?php
}
}
/* {/block 'product_quantity'} */
/* {block 'product_availability'} */
class Block_1354418370683d49d0994335_66871889 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

							<span id="product-availability" class="js-product-availability">
						<?php if ($_smarty_tpl->tpl_vars['product']->value['show_availability'] && $_smarty_tpl->tpl_vars['product']->value['availability_message']) {?>
							<?php if ($_smarty_tpl->tpl_vars['product']->value['availability'] == 'available') {?>
															<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['availability'] == 'last_remaining_items') {?>
							<i class="material-icons product-last-items">&#xE002;</i>
						<?php } else { ?>
							<i class="material-icons product-unavailable">&#xE14B;</i>
							<?php }?>


							<?php if ($_smarty_tpl->tpl_vars['product']->value['availability'] == 'last_remaining_items') {?>
								<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['availability_message'], ENT_QUOTES, 'UTF-8');?>

							<?php } else { ?>
								<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity'] > 0) {?>
									<?php if ($_smarty_tpl->tpl_vars['product']->value['available_now'] != '') {?>
																				<label id="availability_label">Expédition : </label><b><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Immédiate'),$_smarty_tpl ) );?>
</b>
									<?php } else { ?>
																				<label id="availability_label">Expédition : </label><b><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Immédiate'),$_smarty_tpl ) );?>
</b>
									<?php }?>
								<?php } else { ?>
									<?php if ($_smarty_tpl->tpl_vars['product']->value['not_available_message'] != '') {?>
										<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['not_available_message'], ENT_QUOTES, 'UTF-8');?>

									<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['available_later'] != '') {?>
										<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['available_later'], ENT_QUOTES, 'UTF-8');?>

									<?php } else { ?>
										<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Rupture stock'),$_smarty_tpl ) );?>

									<?php }?>
								<?php }?>
							<?php }?>
						<?php }?>
					</span>
						<?php
}
}
/* {/block 'product_availability'} */
/* {block 'product_miniature'} */
class Block_65374399683d49d099b8d3_55738299 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

								<?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/miniatures/pack-product.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('product'=>$_smarty_tpl->tpl_vars['product_pack']->value,'showPackProductsPrice'=>$_smarty_tpl->tpl_vars['product']->value['show_price']), 0, true);
?>
							  <?php
}
}
/* {/block 'product_miniature'} */
/* {block 'product_pack'} */
class Block_823928509683d49d099a5d0_57671250 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

						<?php if ($_smarty_tpl->tpl_vars['packItems']->value) {?>
						  <section class="product-pack">
							<p class="h4"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'This pack contains','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
</p>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['packItems']->value, 'product_pack');
$_smarty_tpl->tpl_vars['product_pack']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product_pack']->value) {
$_smarty_tpl->tpl_vars['product_pack']->do_else = false;
?>
							  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_65374399683d49d099b8d3_55738299', 'product_miniature', $this->tplIndex);
?>

							<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</section>
						<?php }?>
					  <?php
}
}
/* {/block 'product_pack'} */
/* {block 'product_prices'} */
class Block_2077134959683d49d099cc15_43916778 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

									<?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-prices.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
								<?php
}
}
/* {/block 'product_prices'} */
/* {block 'product_discounts'} */
class Block_1225614248683d49d099d544_99129118 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

						<?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-discounts.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
					  <?php
}
}
/* {/block 'product_discounts'} */
/* {block 'product_add_to_cart'} */
class Block_207246920683d49d099de49_01585398 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

						<?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-add-to-cart.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
					  <?php
}
}
/* {/block 'product_add_to_cart'} */
/* {block 'product_refresh'} */
class Block_1399862468683d49d099e857_79402812 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'product_refresh'} */
/* {block 'product_buy'} */
class Block_1765471235683d49d098f567_08375527 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                <form action="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['urls']->value['pages']['cart'], ENT_QUOTES, 'UTF-8');?>
" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['static_token']->value, ENT_QUOTES, 'UTF-8');?>
">
                  <input type="hidden" name="id_product" value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['id'], ENT_QUOTES, 'UTF-8');?>
" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['id_customization'], ENT_QUOTES, 'UTF-8');?>
" id="product_customization_id" class="js-product-customization-id">

				  <?php if ((isset($_smarty_tpl->tpl_vars['plant_precommande']->value)) && $_smarty_tpl->tpl_vars['plant_precommande']->value) {?>
					<?php $_smarty_tpl->_subTemplateRender('file:catalog/plant-precommande.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
				  <?php } elseif ((isset($_smarty_tpl->tpl_vars['equivalent_plant']->value)) && $_smarty_tpl->tpl_vars['equivalent_plant']->value) {?>
					<?php $_smarty_tpl->_subTemplateRender('file:catalog/equivalent-plant.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
				  <?php } else { ?>
				  <hr class="plant-hr">
				  <div class="row">
				    <div class="col-xs-12 col-sm-7">
						<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1000169773683d49d0991d10_30084433', 'product_variants', $this->tplIndex);
?>

						</div>
						<div class="col-xs-12 col-sm-4">
						<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_24775094683d49d0992663_18285072', 'product_quantity', $this->tplIndex);
?>

						</div>
					  </div>

					  <div>
						<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1354418370683d49d0994335_66871889', 'product_availability', $this->tplIndex);
?>

					  </div>
					  
					
					  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_823928509683d49d099a5d0_57671250', 'product_pack', $this->tplIndex);
?>

						<hr class="plant-hr">
							<div>
								<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2077134959683d49d099cc15_43916778', 'product_prices', $this->tplIndex);
?>

							</div>

					  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1225614248683d49d099d544_99129118', 'product_discounts', $this->tplIndex);
?>


					  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_207246920683d49d099de49_01585398', 'product_add_to_cart', $this->tplIndex);
?>


					  
					  					  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1399862468683d49d099e857_79402812', 'product_refresh', $this->tplIndex);
?>

				  
				  <?php }?>
                </form>
              <?php
}
}
/* {/block 'product_buy'} */
/* {block 'hook_display_reassurance'} */
class Block_444439881683d49d099f7b2_17089581 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

		  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayReassurance'),$_smarty_tpl ) );?>

	  <?php
}
}
/* {/block 'hook_display_reassurance'} */
/* {block 'calendrier'} */
class Block_1784680443683d49d09a00c3_57742778 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

			<?php $_smarty_tpl->_subTemplateRender('file:catalog/calendrier.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
		<?php
}
}
/* {/block 'calendrier'} */
/* {block 'product_description'} */
class Block_31912501683d49d09a8652_13831228 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                     <div class="product-description-bottom"><?php echo $_smarty_tpl->tpl_vars['product']->value['description'];?>
</div>
                   <?php
}
}
/* {/block 'product_description'} */
/* {block 'product_details'} */
class Block_982700022683d49d09a9113_57063333 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                                    <?php
}
}
/* {/block 'product_details'} */
/* {block 'product_tabs'} */
class Block_649556715683d49d09a09b9_30564949 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

              <div class="tabs">
                <ul class="nav nav-tabs" role="tablist">
				
                </ul>
				
				
					<span class="info_produit_title">Caractéristiques</span>
				<ul class="bullet info_produit">
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, Product::getFrontFeaturesStatic($_smarty_tpl->tpl_vars['language']->value['id'],$_smarty_tpl->tpl_vars['product']->value['id_product']), 'feature');
$_smarty_tpl->tpl_vars['feature']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['feature']->value) {
$_smarty_tpl->tpl_vars['feature']->do_else = false;
?>
						<?php if ($_smarty_tpl->tpl_vars['feature']->value['id_feature'] < 21) {?>
							<?php if ($_smarty_tpl->tpl_vars['feature']->value['id_feature'] != 19) {?>
								<li class="feature feature_<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['feature']->value['id_feature'], ENT_QUOTES, 'UTF-8');?>
"><span><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['feature']->value['name'],'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
  : </span><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['feature']->value['value'],'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</li>
							<?php } else { ?>
								<li class="feature_<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['feature']->value['id_feature'], ENT_QUOTES, 'UTF-8');?>
">
									<?php if ($_smarty_tpl->tpl_vars['feature']->value['value'] == "Godets de 7 cm") {?>
										<img src="/img/godets7.png">
									 <?php } elseif ($_smarty_tpl->tpl_vars['feature']->value['value'] == "Godets de 8 cm") {?>
										<img src="/img/godets8.png">
									 <?php } elseif ($_smarty_tpl->tpl_vars['feature']->value['value'] == "Godets de 9 cm") {?>
										<img src="/img/godets9.png">
									 <?php } elseif ($_smarty_tpl->tpl_vars['feature']->value['value'] == "Pots d'un litre") {?>
										<img src="/img/pot.png">
									 <?php } elseif ($_smarty_tpl->tpl_vars['feature']->value['value'] == "Pots de 2 litres") {?>
										<img src="/img/pot_2l.png">
									 <?php } else { ?>
										<span><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['feature']->value['name'],'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
  :</span>  <?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['feature']->value['value'],'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>

									<?php }?>
								</li>
							<?php }?>
						<?php }?>
				   <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</ul>
		
                <div class="tab-content" id="tab-content">
                 <div class="tab-pane fade in<?php if ($_smarty_tpl->tpl_vars['product']->value['description']) {?> active js-product-tab-active<?php }?>" id="description" role="tabpanel">
                   <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_31912501683d49d09a8652_13831228', 'product_description', $this->tplIndex);
?>

                 </div>

                 <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_982700022683d49d09a9113_57063333', 'product_details', $this->tplIndex);
?>


                 <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value['extraContent'], 'extra', false, 'extraKey');
$_smarty_tpl->tpl_vars['extra']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['extraKey']->value => $_smarty_tpl->tpl_vars['extra']->value) {
$_smarty_tpl->tpl_vars['extra']->do_else = false;
?>
                 <div class="tab-pane fade in <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['extra']->value['attr']['class'], ENT_QUOTES, 'UTF-8');?>
" id="extra-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['extraKey']->value, ENT_QUOTES, 'UTF-8');?>
" role="tabpanel" <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['extra']->value['attr'], 'val', false, 'key');
$_smarty_tpl->tpl_vars['val']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->do_else = false;
?> <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['key']->value, ENT_QUOTES, 'UTF-8');?>
="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['val']->value, ENT_QUOTES, 'UTF-8');?>
"<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>>
                   <?php echo $_smarty_tpl->tpl_vars['extra']->value['content'];?>

                 </div>
                 <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
              </div>
            </div>
          <?php
}
}
/* {/block 'product_tabs'} */
/* {block 'product_footer'} */
class Block_1319017502683d49d09b27c1_05228959 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooterProduct','product'=>$_smarty_tpl->tpl_vars['product']->value,'category'=>$_smarty_tpl->tpl_vars['category']->value),$_smarty_tpl ) );?>

    <?php
}
}
/* {/block 'product_footer'} */
/* {block 'product_images_modal'} */
class Block_1482724880683d49d09b3375_71569823 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/product-images-modal.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <?php
}
}
/* {/block 'product_images_modal'} */
/* {block 'page_footer'} */
class Block_1260198250683d49d09b3fc3_39390118 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <!-- Footer content -->
        <?php
}
}
/* {/block 'page_footer'} */
/* {block 'page_footer_container'} */
class Block_143664039683d49d09b3c80_70299516 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <footer class="page-footer">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1260198250683d49d09b3fc3_39390118', 'page_footer', $this->tplIndex);
?>

      </footer>
    <?php
}
}
/* {/block 'page_footer_container'} */
/* {block 'content'} */
class Block_389748062683d49d0982aa0_07451986 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_389748062683d49d0982aa0_07451986',
  ),
  'page_header_container' => 
  array (
    0 => 'Block_2096700247683d49d0983805_48897084',
  ),
  'page_header' => 
  array (
    0 => 'Block_431520987683d49d0983b59_78702787',
  ),
  'page_title' => 
  array (
    0 => 'Block_499227059683d49d0983ee3_16737021',
  ),
  'page_content_container' => 
  array (
    0 => 'Block_1438434378683d49d0985a69_31323872',
  ),
  'page_content' => 
  array (
    0 => 'Block_443513069683d49d0985da8_51235729',
  ),
  'product_cover_thumbnails' => 
  array (
    0 => 'Block_570026511683d49d0986447_62644289',
  ),
  'product_description_short' => 
  array (
    0 => 'Block_1263863529683d49d098ad86_29263155',
  ),
  'calendrier' => 
  array (
    0 => 'Block_1865625917683d49d098cf73_57877875',
    1 => 'Block_1784680443683d49d09a00c3_57742778',
  ),
  'product_customization' => 
  array (
    0 => 'Block_1033827388683d49d098e6b9_67701380',
  ),
  'product_buy' => 
  array (
    0 => 'Block_1765471235683d49d098f567_08375527',
  ),
  'product_variants' => 
  array (
    0 => 'Block_1000169773683d49d0991d10_30084433',
  ),
  'product_quantity' => 
  array (
    0 => 'Block_24775094683d49d0992663_18285072',
  ),
  'product_availability' => 
  array (
    0 => 'Block_1354418370683d49d0994335_66871889',
  ),
  'product_pack' => 
  array (
    0 => 'Block_823928509683d49d099a5d0_57671250',
  ),
  'product_miniature' => 
  array (
    0 => 'Block_65374399683d49d099b8d3_55738299',
  ),
  'product_prices' => 
  array (
    0 => 'Block_2077134959683d49d099cc15_43916778',
  ),
  'product_discounts' => 
  array (
    0 => 'Block_1225614248683d49d099d544_99129118',
  ),
  'product_add_to_cart' => 
  array (
    0 => 'Block_207246920683d49d099de49_01585398',
  ),
  'product_refresh' => 
  array (
    0 => 'Block_1399862468683d49d099e857_79402812',
  ),
  'hook_display_reassurance' => 
  array (
    0 => 'Block_444439881683d49d099f7b2_17089581',
  ),
  'product_tabs' => 
  array (
    0 => 'Block_649556715683d49d09a09b9_30564949',
  ),
  'product_description' => 
  array (
    0 => 'Block_31912501683d49d09a8652_13831228',
  ),
  'product_details' => 
  array (
    0 => 'Block_982700022683d49d09a9113_57063333',
  ),
  'product_footer' => 
  array (
    0 => 'Block_1319017502683d49d09b27c1_05228959',
  ),
  'product_images_modal' => 
  array (
    0 => 'Block_1482724880683d49d09b3375_71569823',
  ),
  'page_footer_container' => 
  array (
    0 => 'Block_143664039683d49d09b3c80_70299516',
  ),
  'page_footer' => 
  array (
    0 => 'Block_1260198250683d49d09b3fc3_39390118',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


  <div id="left-column">
      <img src="/img/jardinez-authentique.png" alt="La Bonne Graine, Jardinez authentique !" class="jardinez" />
      <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayLeftColumn'),$_smarty_tpl ) );?>

  </div>
  <section id="center_column">
    <meta content="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['url'], ENT_QUOTES, 'UTF-8');?>
">
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2096700247683d49d0983805_48897084', 'page_header_container', $this->tplIndex);
?>

    <div class="product-container js-product-container">
	<?php if (Product::isBundleProduct($_smarty_tpl->tpl_vars['product']->value['id'])) {?>
		<?php $_smarty_tpl->_subTemplateRender('file:catalog/product-wkpack.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	<?php } else { ?>
      <div class="Product_Partie_ G pb-center-column">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1438434378683d49d0985a69_31323872', 'page_content_container', $this->tplIndex);
?>

        </div>
        <div class="Product_Partie_ R pb-right-column">


          <div class="product-information">
			
			<?php if ($_smarty_tpl->tpl_vars['aff_cyril']->value == "true") {?>
                <?php $_smarty_tpl->_assignInScope('isCyril', "false");?>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product_cyril']->value, 'unProduit');
$_smarty_tpl->tpl_vars['unProduit']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['unProduit']->value) {
$_smarty_tpl->tpl_vars['unProduit']->do_else = false;
?>
					<?php if ($_smarty_tpl->tpl_vars['unProduit']->value['id_product'] == $_GET['id_product']) {?>
						<?php $_smarty_tpl->_assignInScope('isCyril', "true");?>
					<?php }?>
				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                <?php if ($_smarty_tpl->tpl_vars['isCyril']->value == "true") {?>

					<div class="background_lightbox_cyril hidden" onclick="$(this).fadeOut();">
						<div class="lightbox_cyril" style="width: 100% !important; min-width: 400px;text-align: center">
							<div class="chat_cyril_cross">
								<div class="close"><img src="/themes/lbg/assets/img/multiply.png" alt="assistant cyril" width="30px" height="30px" 	onclick="$('.background_lightbox_cyril').fadeOut();"/>
								</div>
							</div>
							<h2>Bonjour ami jardinier,</h2>
							<p>
								<img class="floatright" src="/themes/lbg/assets/img/cyril_entier.png">

								Je me présente, je m'appelle Cyril et je suis l'assistant jardinier de La Bonne Graine.<br />
								Vous débutez au jardin et vous avez peur de faire des erreurs ? <br />Je suis là pour vous !<br /><br>
								<span>Je vais vous guider dans la culture de vos légumes</span>. Comment ?<br />
								En vous envoyant des e-mails avec de nombreux conseils pour chaque grande étape.<br />
								Vous apprendrez ainsi les bons gestes et les actions nécessaires pour obtenir des légumes sains et savoureux.<br />
								De la préparation du terrain à la récolte, vous recevrez sur votre boîte les informations pour l'étape du moment. Une fois que vous avez réalisé les actions indiquées, vous validez et vous recevrez, en temps voulu, les informations pour l'étape suivante. Vous saurez tout sur le binage, buttage, arrosage, etc., ainsi que les besoins spécifiques du légume pour lequel vous êtes assisté.<br />
								On vous assiste en vous suivant pas-à-pas.<br>C'est rassurant, n'est ce pas ?<br />
								</p>
								<button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=oui';$('.background_lightbox_cyril').fadeOut();">Je veux être assisté par Cyril</button><button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=non';$('.background_lightbox_cyril').fadeOut();">Pas pour le moment</button>
							<br><br>
						</div>
					</div>

					<div class="entete_cyril">
						<div><img src="/themes/lbg/assets/img/cyril_entete_produit.png" alt="assistant"/></div>
						<div>"Je m'appelle Cyril et je peux vous aider à réussir vos cultures"<br /><a onclick="$('.background_lightbox_cyril').fadeIn();">En savoir plus</a></div>
					</div>
				<?php }?>
			<?php }?>
										
            <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1263863529683d49d098ad86_29263155', 'product_description_short', $this->tplIndex);
?>

			
			  

            <?php if ($_smarty_tpl->tpl_vars['product']->value['is_customizable'] && count($_smarty_tpl->tpl_vars['product']->value['customizations']['fields'])) {?>
              <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1033827388683d49d098e6b9_67701380', 'product_customization', $this->tplIndex);
?>

            <?php }?>
	
            <div class="product-actions js-product-actions">
              <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1765471235683d49d098f567_08375527', 'product_buy', $this->tplIndex);
?>


            </div>

        </div>
		
	  	  
	  <div class="mobile">
		<?php $_smarty_tpl->_subTemplateRender('file:catalog/jardin-essai.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
	  </div>
		  
	  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_444439881683d49d099f7b2_17089581', 'hook_display_reassurance', $this->tplIndex);
?>


      </div>
	  
	  
	  
	  <div class="div_conditions_de_culture mobile">
		<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1784680443683d49d09a00c3_57742778', 'calendrier', $this->tplIndex);
?>

	  </div>
	  
	  			

      <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_649556715683d49d09a09b9_30564949', 'product_tabs', $this->tplIndex);
?>

		  
	<?php }?> 	
    </div>

	<?php if (Product::getFichesAttachments($_smarty_tpl->tpl_vars['product']->value['id_product'])) {?>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, Product::getFichesAttachments($_smarty_tpl->tpl_vars['product']->value['id_product']), 'attachment');
$_smarty_tpl->tpl_vars['attachment']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['attachment']->value) {
$_smarty_tpl->tpl_vars['attachment']->do_else = false;
?>
			<div class="attachment-download">
				<a class="js_download_fiche_pratique" href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getPageLink('attachment',true,NULL,"id_attachment=".((string)$_smarty_tpl->tpl_vars['attachment']->value['id_attachment'])),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Download'),$_smarty_tpl ) );?>
">
					<?php if ((isset($_smarty_tpl->tpl_vars['attachment']->value['picto'])) && $_smarty_tpl->tpl_vars['attachment']->value['picto']) {?>
						<img src="/upload/<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['attachment']->value['picto'], ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Download'),$_smarty_tpl ) );?>
 
					<?php } else { ?>
						<img src="/themes/lbg/assets/img/picto-pdf.png" alt="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Download'),$_smarty_tpl ) );?>
 
					<?php }?>
					<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['attachment']->value['name'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Download'),$_smarty_tpl ) );?>
 <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['attachment']->value['name'], ENT_QUOTES, 'UTF-8');?>
" class="download_picto"/><br/>
					<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['attachment']->value['name'],'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>

				</a>
				<p><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['attachment']->value['description'],'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</p>
			</div>
		<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	<?php }?>
		
    
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1319017502683d49d09b27c1_05228959', 'product_footer', $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1482724880683d49d09b3375_71569823', 'product_images_modal', $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_143664039683d49d09b3c80_70299516', 'page_footer_container', $this->tplIndex);
?>

  </section>

<?php
}
}
/* {/block 'content'} */
}
