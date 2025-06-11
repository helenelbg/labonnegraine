<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/products-top.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dd3f6a2_02515686',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e4039a1ad856306c2c6966691137821e553151b5' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/products-top.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/sort-orders.tpl' => 1,
  ),
),false)) {
function content_683fe28dd3f6a2_02515686 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
 
 
<div id="js-product-list-top" class="row products-selection">
  <div class="col-md-6">
    <div class="row sort-by-row">

      <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_900840528683fe28dd3e7d7_46952843', 'sort_by');
?>


      <?php if (!empty($_smarty_tpl->tpl_vars['listing']->value['rendered_facets'])) {?>
        <div class="col-sm-3 col-xs-4 hidden-md-up filter-button">
          <button id="search_filter_toggler" class="btn btn-secondary js-search-toggler">
            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Filter','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>

          </button>
        </div>
      <?php }?>
    </div>
  </div>
  <div class="col-md-6">
  </div>

  <div class="col-sm-12">
	<button type="button" class="btn btn-info my_btn_collapse button_collapsing mes-filtres" data-toggle="collapse" data-target="#filter_collapse">Mes filtres</button>
  </div>
  
</div>
<?php }
/* {block 'sort_by'} */
class Block_900840528683fe28dd3e7d7_46952843 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'sort_by' => 
  array (
    0 => 'Block_900840528683fe28dd3e7d7_46952843',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

        <?php $_smarty_tpl->_subTemplateRender('file:catalog/_partials/sort-orders.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('sort_orders'=>$_smarty_tpl->tpl_vars['listing']->value['sort_orders']), 0, false);
?>
      <?php
}
}
/* {/block 'sort_by'} */
}
