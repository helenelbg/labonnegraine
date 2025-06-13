<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:00
  from '/home/helene/prestashop/modules/categoryheadermessages/views/templates/hook/header_category.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35ac3633b6_80067471',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a1a107fbefde7517415ecd95d68ee9547342e97d' => 
    array (
      0 => '/home/helene/prestashop/modules/categoryheadermessages/views/templates/hook/header_category.tpl',
      1 => 1749808903,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35ac3633b6_80067471 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/home/helene/prestashop/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
if ((isset($_smarty_tpl->tpl_vars['category_header_messages']->value)) && smarty_modifier_count($_smarty_tpl->tpl_vars['category_header_messages']->value) > 0) {?>
<div class="category-header-messages">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['category_header_messages']->value, 'message', false, NULL, 'messages', array (
));
$_smarty_tpl->tpl_vars['message']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
$_smarty_tpl->tpl_vars['message']->do_else = false;
?>
        <div class="category-header-message">
            <div class="container">
                <div class="row">
                    <?php if ((isset($_smarty_tpl->tpl_vars['message']->value['image'])) && $_smarty_tpl->tpl_vars['message']->value['image']) {?>
                        <div class="col-xs-12 col-sm-12 col-md-6 image" style="background-image:url('<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['message']->value['image_url'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
')">                        
                        <?php if ($_smarty_tpl->tpl_vars['message']->value['type'] == "produit_phare") {?>
                            <div class="accroche produit_phare"><i class="fa fa-heart"></i> Notre produit coup de coeur</div>
                        <?php } elseif ($_smarty_tpl->tpl_vars['message']->value['type'] == "promo_moment") {?>
                            <div class="accroche promo_moment"><i class="fa fa-tag"></i> Promos du moment !</div>
                        <?php } elseif ($_smarty_tpl->tpl_vars['message']->value['type'] == "reduction_lot") {?>
                            <div class="accroche reduction_lot"><i class="fa-solid fa-layer-group"></i> Réduction par lot !</div>
                        <?php } elseif ($_smarty_tpl->tpl_vars['message']->value['type'] == "accessoires") {?>
                            <div class="accroche accessoires"><i class="fa-solid fa-circle-plus"></i> L'accessoire indispensable !</div>
                        <?php } elseif ($_smarty_tpl->tpl_vars['message']->value['type'] == "offre_eco") {?>
                            <div class="accroche offre_eco"><i class="fa-solid fa-lightbulb"></i> Top prix !</div>
                        <?php }?>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 right <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['type']), ENT_QUOTES, 'UTF-8');?>
">
                    <?php } else { ?>
                        <div class="col-md-12 right <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['type']), ENT_QUOTES, 'UTF-8');?>
">
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['message']->value['id_product'] > 0) {?>
                    <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['product']['link']), ENT_QUOTES, 'UTF-8');?>
">
                    <?php } else { ?>
                    <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['cta_link']), ENT_QUOTES, 'UTF-8');?>
">
                    <?php }?>
                            <h3 class="message-title"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['message']->value['title'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
</h3>                            
                    </a>
                            <div class="message-content description">
                                <?php echo $_smarty_tpl->tpl_vars['message']->value['content'];?>

                            </div>
                            <div class="message-content image">
                            <?php if ($_smarty_tpl->tpl_vars['message']->value['id_product'] > 0) {?>
                    <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['product']['link']), ENT_QUOTES, 'UTF-8');?>
">
                    <?php } else { ?>
                    <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['cta_link']), ENT_QUOTES, 'UTF-8');?>
">
                    <?php }?>
                                <img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['image_url']), ENT_QUOTES, 'UTF-8');?>
" />
                                <?php if ($_smarty_tpl->tpl_vars['message']->value['id_product'] > 0) {?>
                    </a>
                    <?php }?>
                            </div>
                            <?php if ((isset($_smarty_tpl->tpl_vars['message']->value['cta_text'])) && $_smarty_tpl->tpl_vars['message']->value['cta_text'] && (isset($_smarty_tpl->tpl_vars['message']->value['cta_link'])) && $_smarty_tpl->tpl_vars['message']->value['cta_link']) {?>
                                <div class="message-cta">
                                <?php if ($_smarty_tpl->tpl_vars['message']->value['id_product'] > 0) {?>
                                        <span class="price"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['product']['price']), ENT_QUOTES, 'UTF-8');?>
</span>
                                        <?php if ($_smarty_tpl->tpl_vars['message']->value['product']['price'] != $_smarty_tpl->tpl_vars['message']->value['product']['price_without_reduction']) {?>
                                            <span class="old_price"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['product']['price_without_reduction']), ENT_QUOTES, 'UTF-8');?>
</span>
                                        <?php }?>
                                        <button class="btn btn-primary add-to-cart-commercial" data-quantity="1" data-id-product="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['id_product']), ENT_QUOTES, 'UTF-8');?>
" data-id-product-attribute="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['id_product_attribute']), ENT_QUOTES, 'UTF-8');?>
">
                                            <img class="img-off" src="/themes/lbg/assets/img/picto-panier-off.png" alt="">
							                <img class="img-on" src="/themes/lbg/assets/img/picto-panier-on.png" alt="">
                                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add to cart','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>

                                        </button>
                                <?php } else { ?>
                                    <a href="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['message']->value['cta_link'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" class="btn btn-primary <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['message']->value['type']), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['message']->value['cta_text'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
</a>
                                <?php }?>
                                </div>
                            <?php }?>
                                                    </div>
                </div>
            </div>
        </div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
}
