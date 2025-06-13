<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:00
  from '/home/helene/prestashop/themes/lbg/templates/catalog/_partials/miniatures/product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35ac3b3663_18560330',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f41aaa21ab987d757ebeef83567a4a0f25364a69' => 
    array (
      0 => '/home/helene/prestashop/themes/lbg/templates/catalog/_partials/miniatures/product.tpl',
      1 => 1749808842,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35ac3b3663_18560330 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
 
<?php $_smarty_tpl->_assignInScope('produit_coeur', 0);
if (Product::getCoeur($_smarty_tpl->tpl_vars['product']->value['id_product'])) {?>
	<?php $_smarty_tpl->_assignInScope('produit_coeur', 1);
}?>

<?php $_smarty_tpl->_assignInScope('isSerres', 0);
if ((isset($_smarty_tpl->tpl_vars['product']->value['id_category_default'])) && ($_smarty_tpl->tpl_vars['product']->value['id_category_default'] >= 273 && $_smarty_tpl->tpl_vars['product']->value['id_category_default'] <= 282)) {?>
	<?php $_smarty_tpl->_assignInScope('isSerres', 1);
}?>
	
<?php $_smarty_tpl->_assignInScope('isNATURA', 0);
if ((isset($_smarty_tpl->tpl_vars['product']->value['id_category_default'])) && $_smarty_tpl->tpl_vars['product']->value['id_category_default'] == 274) {?>
	<?php $_smarty_tpl->_assignInScope('isNATURA', 1);
}?>

<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['quantity'] = Product::getTotalQuantity($_smarty_tpl->tpl_vars['product']->value['id_product']);
$_smarty_tpl->_assignInScope('product', $_tmp_array);?>

<?php $_smarty_tpl->_assignInScope('isWKPACK', 0);
if (Product::isBundleProduct($_smarty_tpl->tpl_vars['product']->value['id_product'])) {?>
	<?php $_smarty_tpl->_assignInScope('isWKPACK', 1);?>
	<?php $_smarty_tpl->_assignInScope('bundleDetail', Product::awGetBundleDetail($_smarty_tpl->tpl_vars['product']->value['id_product']));?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['has_discount'] = true;
$_smarty_tpl->_assignInScope('product', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['regular_price'] = $_smarty_tpl->tpl_vars['bundleDetail']->value['displayPrice'];
$_smarty_tpl->_assignInScope('product', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['discount_type'] = 'percentage';
$_smarty_tpl->_assignInScope('product', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['discount_percentage'] = $_smarty_tpl->tpl_vars['bundleDetail']->value['displayDiscount'];
$_smarty_tpl->_assignInScope('product', $_tmp_array);?>
	<?php if (!Product::awIsBundleStock($_smarty_tpl->tpl_vars['product']->value['id_product'])) {?>
		<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['product']) ? $_smarty_tpl->tpl_vars['product']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['quantity'] = 0;
$_smarty_tpl->_assignInScope('product', $_tmp_array);?>
	<?php }
}?>

<?php $_smarty_tpl->_assignInScope('price_min', Product::getPriceMin($_smarty_tpl->tpl_vars['product']->value['id_product'],$_smarty_tpl->tpl_vars['product']->value['id_product_attribute'],$_smarty_tpl->tpl_vars['product']->value['regular_price_amount']));?>



<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1922330984684c35ac397ac7_18623187', 'product_miniature_item');
?>

<?php }
/* {block 'product_flags'} */
class Block_219241367684c35ac39aae5_07185830 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

        <ul class="product-flags">
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value['flags'], 'flag');
$_smarty_tpl->tpl_vars['flag']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['flag']->value) {
$_smarty_tpl->tpl_vars['flag']->do_else = false;
?>
            <li class="product-flag <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['flag']->value['type']), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['flag']->value['label']), ENT_QUOTES, 'UTF-8');?>
</li>
          <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
	<?php
}
}
/* {/block 'product_flags'} */
/* {block 'product_thumbnail'} */
class Block_1814183565684c35ac39c157_57620292 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php if ($_smarty_tpl->tpl_vars['product']->value['cover']) {?>
            <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['url']), ENT_QUOTES, 'UTF-8');?>
" class="thumbnail product-thumbnail">
              <img
                src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['cover']['bySize']['home_default']['url']), ENT_QUOTES, 'UTF-8');?>
"
                alt="<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['cover']['legend'])) {
echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['cover']['legend']), ENT_QUOTES, 'UTF-8');
} else {
echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'truncate' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['name'],60,'...' ))), ENT_QUOTES, 'UTF-8');
}?>"
                loading="lazy"
                data-full-size-image-url="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['cover']['large']['url']), ENT_QUOTES, 'UTF-8');?>
"
                width="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['cover']['bySize']['home_default']['width']), ENT_QUOTES, 'UTF-8');?>
"
                height="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['cover']['bySize']['home_default']['height']), ENT_QUOTES, 'UTF-8');?>
"
              />
            </a>
          <?php } else { ?>
            <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['url']), ENT_QUOTES, 'UTF-8');?>
" class="thumbnail product-thumbnail">
              <img
                src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['no_picture_image']['bySize']['home_default']['url']), ENT_QUOTES, 'UTF-8');?>
"
                loading="lazy"
                width="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['no_picture_image']['bySize']['home_default']['width']), ENT_QUOTES, 'UTF-8');?>
"
                height="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['no_picture_image']['bySize']['home_default']['height']), ENT_QUOTES, 'UTF-8');?>
"
              />
            </a>
          <?php }?>
        <?php
}
}
/* {/block 'product_thumbnail'} */
/* {block 'product_name'} */
class Block_1254718626684c35ac3a3214_37270188 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php if ($_smarty_tpl->tpl_vars['page']->value['page_name'] == 'index') {?>
            <h3 class="h3 product-title"><a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['url']), ENT_QUOTES, 'UTF-8');?>
" content="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['url']), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'truncate' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['name'],60,'...' ))), ENT_QUOTES, 'UTF-8');?>
</a></h3>
          <?php } else { ?>
            <h2 class="h3 product-title"><a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['url']), ENT_QUOTES, 'UTF-8');?>
" content="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['url']), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'truncate' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['name'],60,'...' ))), ENT_QUOTES, 'UTF-8');?>
</a></h2>
          <?php }?>
        <?php
}
}
/* {/block 'product_name'} */
/* {block 'product_price_and_shipping'} */
class Block_1633350063684c35ac3a5784_74871327 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/home/helene/prestashop/vendor/smarty/smarty/libs/plugins/modifier.replace.php','function'=>'smarty_modifier_replace',),));
?>

		 
			  <?php if ($_smarty_tpl->tpl_vars['product']->value['show_price']) {?>
			  	<?php if ($_smarty_tpl->tpl_vars['price_min']->value) {?>
				<div class="product-price-and-shipping">
				  <span class="price">De <?php echo htmlspecialchars((string) (Tools::displayPrice($_smarty_tpl->tpl_vars['price_min']->value)), ENT_QUOTES, 'UTF-8');?>
 à <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['regular_price']), ENT_QUOTES, 'UTF-8');?>
</span>
				</div>
				<?php } else { ?>
				<div class="product-price-and-shipping">
				  <?php if ($_smarty_tpl->tpl_vars['product']->value['has_discount']) {?>
					
					<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>"old_price"),$_smarty_tpl ) );?>


					<?php if (Configuration::get('MP_BADGE')) {?>
						<?php $_smarty_tpl->_assignInScope('mp_badge', (('background: url(/upload/').(Configuration::get('MP_BADGE'))).(');'));?>
						<div class="picto_badge" style="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['mp_badge']->value), ENT_QUOTES, 'UTF-8');?>
">
						</div>
					<?php }?>	
					
					<span class="regular-price" aria-label="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Regular price','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['regular_price']), ENT_QUOTES, 'UTF-8');?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['product']->value['discount_type'] === 'percentage') {?>
					  <span class="discount-percentage discount-product"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['discount_percentage']), ENT_QUOTES, 'UTF-8');?>
</span>
					<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['discount_type'] === 'amount') {?>
					  <span class="discount-amount discount-product"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['discount_amount_to_display']), ENT_QUOTES, 'UTF-8');?>
</span>
					<?php }?>
				  <?php }?>

				  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>"before_price"),$_smarty_tpl ) );?>


				  <span class="price" aria-label="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Price','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
">
					<?php $_smarty_tpl->smarty->ext->_capture->open($_smarty_tpl, 'custom_price', null, null);
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>'custom_price','hook_origin'=>'products_list'),$_smarty_tpl ) );
$_smarty_tpl->smarty->ext->_capture->close($_smarty_tpl);?>
					<?php if ('' !== $_smarty_tpl->smarty->ext->_capture->getBuffer($_smarty_tpl, 'custom_price')) {?>
					  <?php echo $_smarty_tpl->smarty->ext->_capture->getBuffer($_smarty_tpl, 'custom_price');?>

					<?php } else { ?>
						<?php $_smarty_tpl->_assignInScope('combi', Product::getDefaultCombination($_smarty_tpl->tpl_vars['product']->value['id_product']));?>

						<?php if ($_smarty_tpl->tpl_vars['combi']->value["id_product_attribute"] > 0) {?>
							<?php echo htmlspecialchars((string) (smarty_modifier_replace(sprintf("%.2f",Product::getPriceStatic($_smarty_tpl->tpl_vars['combi']->value["id_product"],true,$_smarty_tpl->tpl_vars['combi']->value["id_product_attribute"])),'.',',')), ENT_QUOTES, 'UTF-8');?>
 €
						<?php } else { ?>
							<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['price']), ENT_QUOTES, 'UTF-8');?>

						<?php }?>
					<?php }?>
				  </span>

				  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>'unit_price'),$_smarty_tpl ) );?>


				</div>
		       <?php }?>
		     <?php }?>

        <?php
}
}
/* {/block 'product_price_and_shipping'} */
/* {block 'product_miniature_item'} */
class Block_1922330984684c35ac397ac7_18623187 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_miniature_item' => 
  array (
    0 => 'Block_1922330984684c35ac397ac7_18623187',
  ),
  'product_flags' => 
  array (
    0 => 'Block_219241367684c35ac39aae5_07185830',
  ),
  'product_thumbnail' => 
  array (
    0 => 'Block_1814183565684c35ac39c157_57620292',
  ),
  'product_name' => 
  array (
    0 => 'Block_1254718626684c35ac3a3214_37270188',
  ),
  'product_price_and_shipping' => 
  array (
    0 => 'Block_1633350063684c35ac3a5784_74871327',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

<div class="js-product product<?php if (!empty($_smarty_tpl->tpl_vars['productClasses']->value)) {?> <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['productClasses']->value), ENT_QUOTES, 'UTF-8');
}?>">

	<?php $_smarty_tpl->_assignInScope('combi', Product::getDefaultCombination($_smarty_tpl->tpl_vars['product']->value['id_product']));?>

	<?php if ($_smarty_tpl->tpl_vars['combi']->value["id_product_attribute"] > 0) {?>
		<article class="product-miniature js-product-miniature" data-id-product="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['id_product']), ENT_QUOTES, 'UTF-8');?>
" data-id-product-attribute="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['combi']->value["id_product_attribute"]), ENT_QUOTES, 'UTF-8');?>
" data-quantity="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['quantity']), ENT_QUOTES, 'UTF-8');?>
">
	<?php } else { ?>
		<article class="product-miniature js-product-miniature" data-id-product="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['id_product']), ENT_QUOTES, 'UTF-8');?>
" data-id-product-attribute="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['id_product_attribute']), ENT_QUOTES, 'UTF-8');?>
" data-quantity="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['quantity']), ENT_QUOTES, 'UTF-8');?>
">
	<?php }?>
	
	<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_219241367684c35ac39aae5_07185830', 'product_flags', $this->tplIndex);
?>

	
    <div class="thumbnail-container">
      <div class="thumbnail-top left-block">
        <div class="product_img_link<?php if ($_smarty_tpl->tpl_vars['produit_coeur']->value) {?> border_coup_de_coeur_products_list<?php }?>">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1814183565684c35ac39c157_57620292', 'product_thumbnail', $this->tplIndex);
?>

		
		<div class="hover-desc">
			<a class="product_img_link" href="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['link'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['product']->value['name'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" itemprop="url">
				<span class="p-desc" itemprop="description">
					<?php echo nl2br((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'truncate' ][ 0 ], array( preg_replace('!<[^>]*?>!', ' ', (string) $_smarty_tpl->tpl_vars['product']->value['description_short']),320,'...' )), (bool) 1);?>

				</span>
			</a>

			<div class="button-action-block<?php if ($_smarty_tpl->tpl_vars['isSerres']->value) {?> cat-serres<?php }?>">
				<div class="button-action">
					<?php if ($_smarty_tpl->tpl_vars['isSerres']->value && !$_smarty_tpl->tpl_vars['isNATURA']->value) {?>
						
					<?php } else { ?>
						<a class="button btn btn-default js-listing-add-to-cart" title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Ajouter au panier'),$_smarty_tpl ) );?>
">
							<img class="img-off" src="/themes/lbg/assets/img/picto-panier-off.png" alt="">
							<img class="img-on" src="/themes/lbg/assets/img/picto-panier-on.png" alt="">
						</a>
					<?php }?>
				</div>
				<div class="button-action js-wishlist-button-add" title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Ajouter à la liste d\'envie'),$_smarty_tpl ) );?>
" onclick="$('.img-off',this).attr('src','/themes/lbg/assets/img/picto-envies-on.png'); return false;">
					<?php if ((isset($_smarty_tpl->tpl_vars['product']->value['is_in_wishlist']))) {?>
						<img class="img-off" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
					<?php } else { ?>
						<img class="img-off" src="/themes/lbg/assets/img/picto-envies-off.png" alt="">
					<?php }?>
					<img class="img-on" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
				</div>
							</div>

		</div>
		
      </div>
		
		<?php if ($_smarty_tpl->tpl_vars['produit_coeur']->value) {?>
			<div class="gif_coup_de_coeur_products_list"></div>
		<?php }?>
		
		
		
      </div>

      <div class="product-description">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1254718626684c35ac3a3214_37270188', 'product_name', $this->tplIndex);
?>


        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1633350063684c35ac3a5784_74871327', 'product_price_and_shipping', $this->tplIndex);
?>


	    <span class="combi_liste">
			<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['combi']->value["name"]), ENT_QUOTES, 'UTF-8');?>

		</span>
		
	
		<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity'] > 0) {?>
		  <?php if ($_smarty_tpl->tpl_vars['product']->value['available_now'] != '') {?>
			  <div class="availability deg"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['available_now']), ENT_QUOTES, 'UTF-8');?>
</div>
		  <?php } else { ?>
			  <div class="availability deg"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Produit en stock'),$_smarty_tpl ) );?>
</div>
		  <?php }?>
		<?php } else { ?>
			<?php if ($_smarty_tpl->tpl_vars['product']->value['not_available_message']) {?>
				<div class="not_available deg aw-not-available-1"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['not_available_message']), ENT_QUOTES, 'UTF-8');?>
</div>
			<?php } elseif ($_smarty_tpl->tpl_vars['product']->value['available_later']) {?>
				<div class="not_available deg aw-not-available-2"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['product']->value['available_later']), ENT_QUOTES, 'UTF-8');?>
</div>
			<?php } else { ?>
				<div class="not_available deg aw-not-available-3"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Rupture stock'),$_smarty_tpl ) );?>
</div>
			<?php }?>
		<?php }?>
    

        <div class="mobile button-action-block<?php if ($_smarty_tpl->tpl_vars['isSerres']->value) {?> cat-serres<?php }?>">
			<div class="button-action">
				<?php if ($_smarty_tpl->tpl_vars['isSerres']->value && !$_smarty_tpl->tpl_vars['isNATURA']->value) {?>
					
				<?php } else { ?>
					<a class="button ajax_add_to_cart_button btn btn-default js-listing-add-to-cart" title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Ajouter au panier'),$_smarty_tpl ) );?>
">
						<img class="img-off" src="/themes/lbg/assets/img/picto-panier-off.png" alt="">
						<img class="img-on" src="/themes/lbg/assets/img/picto-panier-on.png" alt="">
					</a>
				<?php }?>
			</div>
			<div class="button-action js-wishlist-button-add" title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Ajouter à la liste d\'envie'),$_smarty_tpl ) );?>
" onclick="$('.img-off',this).attr('src','/themes/lbg/assets/img/picto-envies-on.png'); return false;">
				<?php if ((isset($_smarty_tpl->tpl_vars['product']->value['is_in_wishlist']))) {?>
					<img class="img-off" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
				<?php } else { ?>
					<img class="img-off" src="/themes/lbg/assets/img/picto-envies-off.png" alt="">
				<?php }?>
				<img class="img-on" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
			</div>
					</div>
			
		<div class="liste_combis">
			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>'weight'),$_smarty_tpl ) );?>


			<?php echo htmlspecialchars((string) (Product::getOthersCombination($_smarty_tpl->tpl_vars['product']->value['id_product'])), ENT_QUOTES, 'UTF-8');?>

			<?php if ($_smarty_tpl->tpl_vars['price_min']->value) {?>
				<span class="combi_liste2"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'En fonction des quantités'),$_smarty_tpl ) );?>
</span>
			<?php }?>
		</div>
		

		
		
		<div class="listing-produit-vente-flash">
			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductFlash','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

		</div>

      </div>
    </div>
  </article>
</div>
<?php
}
}
/* {/block 'product_miniature_item'} */
}
