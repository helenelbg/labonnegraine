<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:14
  from '/home/helene/prestashop/themes/lbg/templates/_partials/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35baaae247_20996463',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd69643fb5c4d75fe9bf8b56e33bfbc61da4eac42' => 
    array (
      0 => '/home/helene/prestashop/themes/lbg/templates/_partials/footer.tpl',
      1 => 1749808841,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35baaae247_20996463 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<div class="container">
  <div class="row">
    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_517675438684c35baaa94b0_50587120', 'hook_footer_before');
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
        <a href="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true),'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" title="">
          <b><span class="bouton_contact">Contactez-nous</span></b>
        </a>
      </div>
      <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_719479646684c35baaaa5a2_72625434', 'hook_footer');
?>

    </div>
    <div class="d-row">
      <div class="col-md-12">
        <p class="text-sm-center">
          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2123366862684c35baaaac44_22984536', 'copyright_link');
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
									<li class="wishlist-list-item" data-id="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['wishlist']->value['id_wishlist']), ENT_QUOTES, 'UTF-8');?>
"><p><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['wishlist']->value['name']), ENT_QUOTES, 'UTF-8');?>
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
class Block_517675438684c35baaa94b0_50587120 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer_before' => 
  array (
    0 => 'Block_517675438684c35baaa94b0_50587120',
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
class Block_719479646684c35baaaa5a2_72625434 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer' => 
  array (
    0 => 'Block_719479646684c35baaaa5a2_72625434',
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
class Block_868588945684c35baaaae80_44481529 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

                <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooterAfter'),$_smarty_tpl ) );?>

              <?php
}
}
/* {/block 'hook_footer_after'} */
/* {block 'copyright_link'} */
class Block_2123366862684c35baaaac44_22984536 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'copyright_link' => 
  array (
    0 => 'Block_2123366862684c35baaaac44_22984536',
  ),
  'hook_footer_after' => 
  array (
    0 => 'Block_868588945684c35baaaae80_44481529',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <ul id="block_various_links_footer" class="block_various_links">
              <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_868588945684c35baaaae80_44481529', 'hook_footer_after', $this->tplIndex);
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
