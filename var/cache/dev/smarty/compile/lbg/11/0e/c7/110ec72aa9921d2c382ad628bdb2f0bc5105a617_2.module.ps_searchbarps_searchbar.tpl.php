<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:28:58
  from 'module:ps_searchbarps_searchbar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35aad65572_12334614',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '110ec72aa9921d2c382ad628bdb2f0bc5105a617' => 
    array (
      0 => 'module:ps_searchbarps_searchbar.tpl',
      1 => 1749808842,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35aad65572_12334614 (Smarty_Internal_Template $_smarty_tpl) {
?><!-- begin /home/helene/prestashop/themes/lbg/modules/ps_searchbar/ps_searchbar.tpl -->
<div id="search_widget" class="search-widgets" data-search-controller-url="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['search_controller_url']->value), ENT_QUOTES, 'UTF-8');?>
">
  <form method="get" action="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['search_controller_url']->value), ENT_QUOTES, 'UTF-8');?>
">
    <input type="hidden" name="controller" value="search">
	<button type="submit" name="submit_search" class="awsearch_submit">
		<i class="material-icons search" aria-hidden="true">search</i>
	</button>
    <input type="text" name="s" value="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['search_string']->value), ENT_QUOTES, 'UTF-8');?>
" placeholder="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Search our catalog','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
" aria-label="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Search','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
">
    <i class="material-icons clear" aria-hidden="true">clear</i>
  </form>
</div>
<!-- end /home/helene/prestashop/themes/lbg/modules/ps_searchbar/ps_searchbar.tpl --><?php }
}
