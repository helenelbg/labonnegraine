<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/listing/product-list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dc98a66_12980021',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4bfd20be17fc68d962f14b3efb08e88ac1a30bf5' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/listing/product-list.tpl',
      1 => 1743417108,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_partials/microdata/product-list-jsonld.tpl' => 1,
    'file:catalog/_partials/subcategories.tpl' => 1,
    'file:catalog/_partials/products-top.tpl' => 1,
    'file:catalog/_partials/products.tpl' => 1,
    'file:catalog/_partials/products-bottom.tpl' => 1,
    'file:errors/not-found.tpl' => 1,
  ),
),false)) {
function content_683fe28dc98a66_12980021 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1397098065683fe28dc92273_73646322', 'head_microdata_special');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1340037570683fe28dc928e2_54644796', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['layout']->value);
}
/* {block 'head_microdata_special'} */
class Block_1397098065683fe28dc92273_73646322 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'head_microdata_special' => 
  array (
    0 => 'Block_1397098065683fe28dc92273_73646322',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <?php $_smarty_tpl->_subTemplateRender('file:_partials/microdata/product-list-jsonld.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('listing'=>$_smarty_tpl->tpl_vars['listing']->value), 0, false);
}
}
/* {/block 'head_microdata_special'} */
/* {block 'product_list_header'} */
class Block_1571001021683fe28dc92ab8_36993272 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
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
    <?php
}
}
/* {/block 'product_list_header'} */
/* {block 'subcategory_list'} */
class Block_2101133524683fe28dc93858_10165230 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php if ((isset($_smarty_tpl->tpl_vars['subcategories']->value)) && count($_smarty_tpl->tpl_vars['subcategories']->value) > 0) {?>
        <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/subcategories.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('subcategories'=>$_smarty_tpl->tpl_vars['subcategories']->value), 0, false);
?>
      <?php }?>
    <?php
}
}
/* {/block 'subcategory_list'} */
/* {block 'product_list_top'} */
class Block_1334323729683fe28dc956b0_11156761 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/products-top.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('listing'=>$_smarty_tpl->tpl_vars['listing']->value), 0, false);
?>
        <?php
}
}
/* {/block 'product_list_top'} */
/* {block 'product_list_active_filters'} */
class Block_1098429952683fe28dc95ca9_36159039 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <div>
            <?php echo $_smarty_tpl->tpl_vars['listing']->value['rendered_active_filters'];?>

          </div>
        <?php
}
}
/* {/block 'product_list_active_filters'} */
/* {block 'product_list'} */
class Block_1043079857683fe28dc96351_49385257 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/products.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('listing'=>$_smarty_tpl->tpl_vars['listing']->value,'productClass'=>"col-xs-12 col-sm-6 col-xl-3"), 0, false);
?>
        <?php
}
}
/* {/block 'product_list'} */
/* {block 'product_list_bottom'} */
class Block_1527223195683fe28dc96a19_49830944 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/products-bottom.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('listing'=>$_smarty_tpl->tpl_vars['listing']->value), 0, false);
?>
        <?php
}
}
/* {/block 'product_list_bottom'} */
/* {block 'product_list_footer'} */
class Block_1750818672683fe28dc98175_49484711 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'product_list_footer'} */
/* {block 'content'} */
class Block_1340037570683fe28dc928e2_54644796 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_1340037570683fe28dc928e2_54644796',
  ),
  'product_list_header' => 
  array (
    0 => 'Block_1571001021683fe28dc92ab8_36993272',
  ),
  'subcategory_list' => 
  array (
    0 => 'Block_2101133524683fe28dc93858_10165230',
  ),
  'product_list_top' => 
  array (
    0 => 'Block_1334323729683fe28dc956b0_11156761',
  ),
  'product_list_active_filters' => 
  array (
    0 => 'Block_1098429952683fe28dc95ca9_36159039',
  ),
  'product_list' => 
  array (
    0 => 'Block_1043079857683fe28dc96351_49385257',
  ),
  'product_list_bottom' => 
  array (
    0 => 'Block_1527223195683fe28dc96a19_49830944',
  ),
  'product_list_footer' => 
  array (
    0 => 'Block_1750818672683fe28dc98175_49484711',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <section id="main">

    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1571001021683fe28dc92ab8_36993272', 'product_list_header', $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2101133524683fe28dc93858_10165230', 'subcategory_list', $this->tplIndex);
?>

    
	<?php if ((isset($_smarty_tpl->tpl_vars['category']->value))) {?>
	  <div class="block-category-inner">
        <div id="category-description" class="text-muted"><?php echo $_smarty_tpl->tpl_vars['category']->value['description'];?>
</div>
      </div>
	<?php }?>
      
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>"displayHeaderCategory",'category'=>$_smarty_tpl->tpl_vars['category']->value),$_smarty_tpl ) );?>

    

    <section id="products">
      <?php if (count($_smarty_tpl->tpl_vars['listing']->value['products'])) {?>

        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1334323729683fe28dc956b0_11156761', 'product_list_top', $this->tplIndex);
?>


        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1098429952683fe28dc95ca9_36159039', 'product_list_active_filters', $this->tplIndex);
?>


        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1043079857683fe28dc96351_49385257', 'product_list', $this->tplIndex);
?>


        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1527223195683fe28dc96a19_49830944', 'product_list_bottom', $this->tplIndex);
?>


      <?php } else { ?>
        <div id="js-product-list-top"></div>

        <div id="js-product-list">
          <?php $_smarty_tpl->smarty->ext->_capture->open($_smarty_tpl, 'default', "errorContent", null);?>
            <h4><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'No products available yet','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
</h4>
            <p><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Stay tuned! More products will be shown here as they are added.','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
</p>
          <?php $_smarty_tpl->smarty->ext->_capture->close($_smarty_tpl);?>

          <?php $_smarty_tpl->_subTemplateRender('file:errors/not-found.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('errorContent'=>$_smarty_tpl->tpl_vars['errorContent']->value), 0, false);
?>
        </div>

        <div id="js-product-list-bottom"></div>
      <?php }?>
    </section>
	
	<?php if ($_smarty_tpl->tpl_vars['aff_cyril']->value == "true") {?>

		<div class="chat_cyril">
			<div class="chat_cyril_cross">
				<div class="close">
					<img onclick="$('.chat_cyril').css('transition', 'bottom .5s linear'); $('.chat_cyril').css('bottom', '-300px'); document.cookie = 'assistantClose=1; expires='+new Date(new Date().setDate(new Date().getDate() + 7))" src="/themes/lbg/assets/img/multiply.png" alt="assistant cyril" width="30px" height="30px"/>
				</div>
			</div>
			<a onclick="$('.background_lightbox_cyril').fadeIn();" class="fancybox">Souhaitez vous être accompagné<br />par notre assistant Cyril ?</a>
		</div>
		<div class="background_lightbox_cyril" onclick="$(this).fadeOut();">
			<div class="lightbox_cyril">
				<div class="chat_cyril_cross">
					<div class="close"><img src="/themes/lbg/assets/img/multiply.png" alt="assistant cyril" width="30px" height="30px" 	onclick="$('.background_lightbox_cyril').fadeOut();"/></div>
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
				<button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=oui';$('.background_lightbox_cyril').fadeOut();">Je veux être assisté par Cyril</button>
				<button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=non';$('.background_lightbox_cyril').fadeOut();">Pas pour le moment</button>
				<br><br>
			</div>
		</div>

	<?php }?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1750818672683fe28dc98175_49484711', 'product_list_footer', $this->tplIndex);
?>


    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>"displayFooterCategory"),$_smarty_tpl ) );?>


  </section>
<?php
}
}
/* {/block 'content'} */
}
