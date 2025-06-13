<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:28:58
  from '/home/helene/prestashop/themes/lbg/templates/_partials/header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35aad55f62_24892832',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '052e123d0422fed961042d4967d00571d5b6610b' => 
    array (
      0 => '/home/helene/prestashop/themes/lbg/templates/_partials/header.tpl',
      1 => 1749808841,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:_partials/actus_little.tpl' => 1,
  ),
),false)) {
function content_684c35aad55f62_24892832 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>

<style>
	<?php if (Configuration::get('MP_COULEUR_TEXTE')) {?>
	  
	  body{
		color: <?php echo htmlspecialchars((string) (Configuration::get('MP_COULEUR_TEXTE')), ENT_QUOTES, 'UTF-8');?>
;
	  }
	  
	<?php }?>

	<?php if (Configuration::get('MP_PICTOS')) {?>
	  
	  .bullet .feature {
		background-image: url(/upload/<?php echo htmlspecialchars((string) (Configuration::get('MP_PICTOS')), ENT_QUOTES, 'UTF-8');?>
);
		background-repeat: no-repeat;
	  }
	  
	<?php }?>
	
	<?php if (Configuration::get('MP_FOND_DE_PAGE')) {?>
	  
	  body {
		background-image: url(/upload/<?php echo htmlspecialchars((string) (Configuration::get('MP_FOND_DE_PAGE')), ENT_QUOTES, 'UTF-8');?>
);
		background-repeat: no-repeat;
	  }
	  
	<?php }?>
	
	<?php if (Configuration::get('MP_COULEUR_FOND_DE_PAGE')) {?>
	  
	  body {
		background-color: <?php echo htmlspecialchars((string) (Configuration::get('MP_COULEUR_FOND_DE_PAGE')), ENT_QUOTES, 'UTF-8');?>
;
	  }
	  
	<?php }?>
	
	<?php if (Configuration::get('MP_MINI_HEADER')) {?>
	  
	  body#index .Bandeau_Top {
		background-image: url(/upload/<?php echo htmlspecialchars((string) (Configuration::get('MP_MINI_HEADER')), ENT_QUOTES, 'UTF-8');?>
);
		background-repeat: no-repeat;
		height: 0; /* le mini header n'est plus utilisé ? */
	  }
	  
	<?php }?>
	
	<?php if (Configuration::get('MP_HEADER')) {?>
	  
	  body:not(#index) .Bandeau_Top {
		background-image: url(/upload/<?php echo htmlspecialchars((string) (Configuration::get('MP_HEADER')), ENT_QUOTES, 'UTF-8');?>
);
		background-repeat: no-repeat;
		height: 100px;
	  }
	  
	<?php }?>
	
	<?php if (Configuration::get('MP_POSITION_LOGO') == 'centre') {?>
	  
	  @media (min-width: 795px) {
		#header_logo {margin-left: 45%;}
      }
	  
	<?php }?>
	
	
	<?php if (Configuration::get('MP_LOGO_B')) {?>
	  
	  .Bandeau_Top .row #header_logo a::before, header .row #_desktop_logo a::before {
		background-image: url(/upload/<?php echo htmlspecialchars((string) (Configuration::get('MP_LOGO_B')), ENT_QUOTES, 'UTF-8');?>
);
		background-repeat: no-repeat;
	  }
	  
	<?php }?>

	<?php if (Configuration::get('MP_PANCARTE') == 'off') {?>
		
		#pancarte {
			display: none;
		}
		
	<?php }?>
	
	<?php if (Configuration::get('MP_COULEUR_FOND_PRODUIT') && Configuration::get('MP_COULEUR_FOND_PRODUIT_A') <> 'on') {?>
		
		.product-information {
			background-color: <?php echo htmlspecialchars((string) (Configuration::get('MP_COULEUR_FOND_PRODUIT')), ENT_QUOTES, 'UTF-8');?>
;
		}
		
	<?php }?>
	
	<?php if (Configuration::get('MP_COULEUR_JARDIN') && Configuration::get('MP_COULEUR_JARDIN_A') <> 'on') {?>
		
		.info_plus_container {
			background-color: <?php echo htmlspecialchars((string) (Configuration::get('MP_COULEUR_JARDIN')), ENT_QUOTES, 'UTF-8');?>
;
		}
		
	<?php }?>
	
	<?php if (Configuration::get('MP_COULEUR_INFO_PRODUIT') && Configuration::get('MP_COULEUR_INFO_PRODUIT_A') <> 'on') {?>
		
		.product-features {
			background-color: <?php echo htmlspecialchars((string) (Configuration::get('MP_COULEUR_INFO_PRODUIT')), ENT_QUOTES, 'UTF-8');?>
;
		}
		
	<?php }?>
	
	<?php if (Configuration::get('MP_COULEUR_SAVOIR_PLUS') && Configuration::get('MP_COULEUR_SAVOIR_PLUS_A') <> 'on') {?>
		
		.product-description {
			background-color: <?php echo htmlspecialchars((string) (Configuration::get('MP_COULEUR_SAVOIR_PLUS')), ENT_QUOTES, 'UTF-8');?>
;
		}
		
	<?php }?>
				  
</style>
  
<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1323715071684c35aad4ba52_73265974', 'header_banner');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1630683897684c35aad4c230_35898703', 'actus_little');
?>






<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1757231020684c35aad4cae9_73093884', 'slogan_2');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1360189856684c35aad4def6_59519775', 'logo');
?>




<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1301012009684c35aad4eff8_79823170', 'header_nav');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_55192312684c35aad54339_96070814', 'header_top');
?>


<?php }
/* {block 'header_banner'} */
class Block_1323715071684c35aad4ba52_73265974 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'header_banner' => 
  array (
    0 => 'Block_1323715071684c35aad4ba52_73265974',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <div class="header-banner">
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayBanner'),$_smarty_tpl ) );?>

  </div>
<?php
}
}
/* {/block 'header_banner'} */
/* {block 'actus_little'} */
class Block_1630683897684c35aad4c230_35898703 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'actus_little' => 
  array (
    0 => 'Block_1630683897684c35aad4c230_35898703',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <?php $_smarty_tpl->_subTemplateRender("file:_partials/actus_little.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
/* {/block 'actus_little'} */
/* {block 'slogan_2'} */
class Block_1757231020684c35aad4cae9_73093884 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'slogan_2' => 
  array (
    0 => 'Block_1757231020684c35aad4cae9_73093884',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <?php if (Configuration::get('MP_SLOGAN_2')) {?>
	<?php $_smarty_tpl->_assignInScope('slogan_1', ('/upload/').(Configuration::get('MP_SLOGAN_2')));?>
	<div class="txt_evenement"><img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['slogan_2']->value), ENT_QUOTES, 'UTF-8');?>
"/></div>
  <?php }
}
}
/* {/block 'slogan_2'} */
/* {block 'logo'} */
class Block_1360189856684c35aad4def6_59519775 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'logo' => 
  array (
    0 => 'Block_1360189856684c35aad4def6_59519775',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <?php if (Configuration::get('MP_LOGO')) {?>
    <?php $_smarty_tpl->_assignInScope('logo_url', ('/upload/').(Configuration::get('MP_LOGO')));?>
	<img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['logo_url']->value), ENT_QUOTES, 'UTF-8');?>
" alt="logo" style="display: none;">
  <?php }
}
}
/* {/block 'logo'} */
/* {block 'avis_google'} */
class Block_332147015684c35aad4f224_02059662 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

              <?php if (Configuration::get('MP_AVIS_GOOGLE')) {?>
              <?php $_smarty_tpl->_assignInScope('header_avis_google', ('/upload/').(Configuration::get('MP_AVIS_GOOGLE')));?>
              <a href="https://customerreviews.google.com/v/merchant?q=labonnegraine.com&c=FR&v=19&hl=fr" target="_blank"><div class="header_avis_google"><img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['header_avis_google']->value), ENT_QUOTES, 'UTF-8');?>
" alt="avis google"/></div></a>
              <?php }?>
            <?php
}
}
/* {/block 'avis_google'} */
/* {block 'slogan_1'} */
class Block_451785498684c35aad50417_59218310 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <?php if (Configuration::get('MP_SLOGAN_1')) {?>
            <?php $_smarty_tpl->_assignInScope('slogan_1', ('/upload/').(Configuration::get('MP_SLOGAN_1')));?>
            <div class="slogan"><img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['slogan_1']->value), ENT_QUOTES, 'UTF-8');?>
"/></div>
            <?php }?>
          <?php
}
}
/* {/block 'slogan_1'} */
/* {block 'header_nav'} */
class Block_1301012009684c35aad4eff8_79823170 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'header_nav' => 
  array (
    0 => 'Block_1301012009684c35aad4eff8_79823170',
  ),
  'avis_google' => 
  array (
    0 => 'Block_332147015684c35aad4f224_02059662',
  ),
  'slogan_1' => 
  array (
    0 => 'Block_451785498684c35aad50417_59218310',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <nav class="header-nav">
    <div class="container">
      <div class="row">
        <div class="desktop">
          <div class="col-md-5 col-xs-12">
            <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_332147015684c35aad4f224_02059662', 'avis_google', $this->tplIndex);
?>

            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayNav1'),$_smarty_tpl ) );?>

          </div>
          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_451785498684c35aad50417_59218310', 'slogan_1', $this->tplIndex);
?>

          <div class="col-md-7 right-nav">
              <a type="button" title="Recherche" name="submit_search" class="btn btn-default button-search" href="#" tabindex="1"></a>
              <div class="header_user_info header_user_info_wishlist">
                  <a title="Liste d'envies" href="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getModuleLink('blockwishlist','lists',array(),true),'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
">
                  </a>
              </div>
              <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayNav2'),$_smarty_tpl ) );?>

          </div>
        </div>
        <div class="mobile">
          <div class="float-xs-left" id="megamenu-icon">
            <i class="material-icons">&#xE5D2;</i>
          </div>
          <div class="float-xs-right _mobile_cart">
			<div class="blockcart cart-preview" data-refresh-url="/module/ps_shoppingcart/ajax">
				<div class="cart-preview-div">
					<a rel="nofollow" href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['pages']['cart']), ENT_QUOTES, 'UTF-8');?>
?action=show">
					  <img src="/themes/lbg/assets/img/picto-panier.png" alt="panier">
					</a>
				</div>
			</div>  
		  </div>
		  <a type="button" class="js-button-search-mobile" href="#" tabindex="1">
			<i class="material-icons search" aria-hidden="true">search</i>
		  </a>
          <div class="float-xs-right _mobile_user_info">
			<div class="user-info">
			<?php if ($_smarty_tpl->tpl_vars['customer']->value['is_logged']) {?>
			  <a
				class="account"
				href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['pages']['my_account']), ENT_QUOTES, 'UTF-8');?>
"
				title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'View my customer account','d'=>'Shop.Theme.Customeraccount'),$_smarty_tpl ) );?>
"
				rel="nofollow"
			  >
				<i class="material-icons hidden-md-up logged">&#xE7FF;</i>
			  </a>
			<?php } else { ?>
			  <a
				href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['pages']['authentication']), ENT_QUOTES, 'UTF-8');?>
?back=<?php echo htmlspecialchars((string) (urlencode($_smarty_tpl->tpl_vars['urls']->value['current_url'])), ENT_QUOTES, 'UTF-8');?>
"
				title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Log in to your customer account','d'=>'Shop.Theme.Customeraccount'),$_smarty_tpl ) );?>
"
				rel="nofollow"
			  >
				<i class="material-icons">&#xE7FF;</i>
			  </a>
			<?php }?>
		    </div>
		  </div>
          <div class="top-logo mobile">
		    <a href="/">
			  <img class="logo img-fluid" src="/img/logo-1684248802.jpg" alt="La Bonne Graine" width="217" height="213">
		    </a>
		  </div>
		  <div id="aw_bloc_search_absolute">
			<form method="get" action="/recherche">
				<input type="hidden" name="controller" value="search">
				<div class="div_search_input_button"> 
					<input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="Rechercher"> 
					<button type="submit" name="submit_search" class="awsearch_submit">
						<i class="material-icons search" aria-hidden="true">search</i>
					</button>
				</div>
			</form>
		  </div>
		  <div class="slogan-mobile"><img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['slogan_1']->value), ENT_QUOTES, 'UTF-8');?>
"/></div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </nav>
<?php
}
}
/* {/block 'header_nav'} */
/* {block 'header_top'} */
class Block_55192312684c35aad54339_96070814 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'header_top' => 
  array (
    0 => 'Block_55192312684c35aad54339_96070814',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <div class="header-top">
  <div class="Bandeau_Top"></div>
    <div class="container">

       <div class="row">
        <div class="desktop" id="_desktop_logo">
          <?php if ($_smarty_tpl->tpl_vars['shop']->value['logo_details']) {?>
            <?php if ($_smarty_tpl->tpl_vars['page']->value['page_name'] == 'index') {?>
                <?php $_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'renderLogo', array(), true);?>

            <?php } else { ?>
              <?php $_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'renderLogo', array(), true);?>

            <?php }?>
          <?php }?>
        </div>
        <div class="header-top-right position-static">
          <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayTop'),$_smarty_tpl ) );?>

        </div>
      </div>
      <div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
        <div class="js-top-menu-bottom">
          <div id="_mobile_currency_selector"></div>
          <div id="_mobile_language_selector"></div>
          <div id="_mobile_contact_link"></div>
        </div>
      </div>
    </div>
  </div>
  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayNavFullWidth'),$_smarty_tpl ) );?>

<?php
}
}
/* {/block 'header_top'} */
}
