<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/_partials/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff7dd4a5_16255355',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '197cf988628e1aa56ca9c9710382799307e40ceb' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/_partials/footer.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff7dd4a5_16255355 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<div class="container">
  <div class="row">
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_171706196068304cff7da570_24468885', 'hook_footer_before');
?>

  </div>
</div>
<div class="footer-container">
  <div class="container">
    <div class="f-row">
      <div class="bloc_question">
        <h2>Besoin d'aide ?</h2>
        <h3>On est l&agrave; pour vous !</h3>
        <br>
        <a href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" title="">
          <b><span class="bouton_contact">Contactez-nous</span></b>
        </a>
      </div>
      <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_27841589168304cff7db188_49512761', 'hook_footer');
?>

    </div>
    <div class="d-row">
      <div class="col-md-12">
        <p class="text-sm-center">
          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_164837272968304cff7db6a9_77235808', 'copyright_link');
?>

        </p>
      </div>
    </div>
  </div>
  <div class="aw-wishlists">
	<div tabindex="-1" role="dialog" aria-modal="true" class="wishlist-modal modal fade">
		<div role="document" class="modal-dialog modal-dialog-centered">
			<?php $_smarty_tpl->_assignInScope('wish', Tools::getWishlists());?>
			<?php if (!$_smarty_tpl->tpl_vars['wish']->value['logged']) {?>
				<div class="modal-content">
					<div class="modal-body">
						Vous devez être connecté pour ajouter des produits à votre liste d'envies.
					</div> 
				</div>	
			<?php } elseif (!$_smarty_tpl->tpl_vars['wish']->value['wishlists']) {?>
				
			<?php } else { ?>
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							Ajouter à ma liste d'envies
						</h5> 
						<button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
					</div> 
					<div class="modal-body">
						<div class="wishlist-chooselist">
							<ul class="wishlist-list">
								<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['wish']->value['wishlists'], 'wishlist');
$_smarty_tpl->tpl_vars['wishlist']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['wishlist']->value) {
$_smarty_tpl->tpl_vars['wishlist']->do_else = false;
?>
									<li class="wishlist-list-item" data-id="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['wishlist']->value['id_wishlist'], ENT_QUOTES, 'UTF-8');?>
"><p><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['wishlist']->value['name'], ENT_QUOTES, 'UTF-8');?>
</p></li>
								<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
							</ul>
						</div>
					</div> 
				</div>	
			<?php }?>
			</div>
		</div> 
	<div class="modal-backdrop fade"></div>
  </div>
</div>
<?php }
/* {block 'hook_footer_before'} */
class Block_171706196068304cff7da570_24468885 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer_before' => 
  array (
    0 => 'Block_171706196068304cff7da570_24468885',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooterBefore'),$_smarty_tpl ) );?>

    <?php
}
}
/* {/block 'hook_footer_before'} */
/* {block 'hook_footer'} */
class Block_27841589168304cff7db188_49512761 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer' => 
  array (
    0 => 'Block_27841589168304cff7db188_49512761',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooter'),$_smarty_tpl ) );?>

      <?php
}
}
/* {/block 'hook_footer'} */
/* {block 'hook_footer_after'} */
class Block_22714469268304cff7db867_72879393 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooterAfter'),$_smarty_tpl ) );?>

              <?php
}
}
/* {/block 'hook_footer_after'} */
/* {block 'copyright_link'} */
class Block_164837272968304cff7db6a9_77235808 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'copyright_link' => 
  array (
    0 => 'Block_164837272968304cff7db6a9_77235808',
  ),
  'hook_footer_after' => 
  array (
    0 => 'Block_22714469268304cff7db867_72879393',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <ul id="block_various_links_footer" class="block_various_links">
              <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_22714469268304cff7db867_72879393', 'hook_footer_after', $this->tplIndex);
?>

              <li class="item"><a href="/content/5-paiement-securise">Paiement 100% sécurisé</a></li>
              <li class="item"><a href="/content/3-conditions-generales-de-ventes#cgv13">Données Personnelles </a></li>
              <li class="last_item">Site 100% Français<img class="France" src="/themes/lbg/assets/img/france.png" alt="France" ></li>
            </ul>
          <?php
}
}
/* {/block 'copyright_link'} */
}
