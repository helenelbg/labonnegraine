<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:57
  from '/home/dev.labonnegraine.com/public_html/modules/ets_crosssell/views/templates/hook/product_list_17.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d102d495_96170118',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b2d8a50011d437399eba00ab0b32ed97e345ede8' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ets_crosssell/views/templates/hook/product_list_17.tpl',
      1 => 1742303612,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/miniatures/product.tpl' => 1,
  ),
),false)) {
function content_683d49d102d495_96170118 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['ets_per_row_desktop']->value) {?>
    <?php $_smarty_tpl->_assignInScope('nbItemsPerLine', $_smarty_tpl->tpl_vars['ets_per_row_desktop']->value);?>
	<?php $_smarty_tpl->_assignInScope('nbItemsPerLineTablet', $_smarty_tpl->tpl_vars['ets_per_row_tablet']->value);?>
	<?php $_smarty_tpl->_assignInScope('nbItemsPerLineMobile', $_smarty_tpl->tpl_vars['ets_per_row_mobile']->value);
} else { ?>
    <?php if ($_smarty_tpl->tpl_vars['page_name']->value != 'index' && $_smarty_tpl->tpl_vars['page_name']->value != 'product' && $_smarty_tpl->tpl_vars['page_name']->value != 'order-confirmation' && $_smarty_tpl->tpl_vars['page_name']->value != 'orderconfirmation' && $_smarty_tpl->tpl_vars['page_name']->value != 'cms' && $_smarty_tpl->tpl_vars['page_name']->value != 'cart') {?>
    	<?php $_smarty_tpl->_assignInScope('nbItemsPerLine', 3);?>
    	<?php $_smarty_tpl->_assignInScope('nbItemsPerLineTablet', 2);?>
    	<?php $_smarty_tpl->_assignInScope('nbItemsPerLineMobile', 3);?>
    <?php } else { ?>
    	<?php $_smarty_tpl->_assignInScope('nbItemsPerLine', 4);?>
    	<?php $_smarty_tpl->_assignInScope('nbItemsPerLineTablet', 3);?>
    	<?php $_smarty_tpl->_assignInScope('nbItemsPerLineMobile', 2);?>
    <?php }
}
echo '<script'; ?>
 type="text/javascript">
    var nbItemsPerLine =<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLine']->value), ENT_QUOTES, 'UTF-8');?>
;
    var nbItemsPerLineTablet =<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineTablet']->value), ENT_QUOTES, 'UTF-8');?>
;
    var nbItemsPerLineMobile =<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineMobile']->value), ENT_QUOTES, 'UTF-8');?>
;
<?php echo '</script'; ?>
>
<?php if ($_smarty_tpl->tpl_vars['sub_categories']->value && ($_smarty_tpl->tpl_vars['products']->value || $_smarty_tpl->tpl_vars['id_ets_css_sub_category']->value)) {?>
    <ul class="ets_cs_sub_categories <?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['page_name']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');
if ((isset($_smarty_tpl->tpl_vars['sort_options']->value)) && $_smarty_tpl->tpl_vars['sort_options']->value && (isset($_smarty_tpl->tpl_vars['products']->value)) && $_smarty_tpl->tpl_vars['products']->value) {?> ets_cs_has_sortby<?php }?>">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sub_categories']->value, 'sub_category');
$_smarty_tpl->tpl_vars['sub_category']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sub_category']->value) {
$_smarty_tpl->tpl_vars['sub_category']->do_else = false;
?>
            <li>
                <a class="ets_crosssel_sub_category<?php if ($_smarty_tpl->tpl_vars['id_ets_css_sub_category']->value == $_smarty_tpl->tpl_vars['sub_category']->value['id_category']) {?> active<?php }?>" data-id_product="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['id_product_page']->value), ENT_QUOTES, 'UTF-8');?>
" data-page="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" data-tab="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" data-id_category="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['sub_category']->value['id_category']), ENT_QUOTES, 'UTF-8');?>
" href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['link']->value->getCategoryLink($_smarty_tpl->tpl_vars['sub_category']->value['id_category']),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sub_category']->value['name'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</a>
            </li>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
<?php }
if ((isset($_smarty_tpl->tpl_vars['sort_options']->value)) && $_smarty_tpl->tpl_vars['sort_options']->value && (isset($_smarty_tpl->tpl_vars['products']->value)) && $_smarty_tpl->tpl_vars['products']->value) {?>
    <form class="ets_sortby_form" action="" method="post">
        <label for="ets_crosssell_sort_by_<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
_<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Sort by','mod'=>'ets_crosssell'),$_smarty_tpl ) );?>
</label>
        <select name="ets_crosssell_sort_by" data-id_product="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['id_product_page']->value), ENT_QUOTES, 'UTF-8');?>
" data-page="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" data-tab="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" id="ets_crosssell_sort_by_<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
_<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" class="ets_crosssel_sort_by">
            <option value="">--</option>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sort_options']->value, 'option');
$_smarty_tpl->tpl_vars['option']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['option']->value) {
$_smarty_tpl->tpl_vars['option']->do_else = false;
?>
                <option<?php if ($_smarty_tpl->tpl_vars['option']->value['id_option'] == $_smarty_tpl->tpl_vars['sort_by']->value) {?> selected="selected"<?php }?> value="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['option']->value['id_option'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['option']->value['name'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</option>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </select>
    </form>
<?php }
if ((isset($_smarty_tpl->tpl_vars['products']->value)) && $_smarty_tpl->tpl_vars['products']->value) {?>
    <div class="featured-products product_list">
    	<div data-row-desktop="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLine']->value), ENT_QUOTES, 'UTF-8');?>
" data-row-tablet="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineTablet']->value), ENT_QUOTES, 'UTF-8');?>
" data-row-mobile="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineMobile']->value), ENT_QUOTES, 'UTF-8');?>
" id="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" class="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
 product_list product-list product_grid grid products crosssell_product_list_wrapper<?php if ((isset($_smarty_tpl->tpl_vars['tab']->value)) && $_smarty_tpl->tpl_vars['tab']->value) {?> cs-wrapper-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');
}?> layout-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['layout_mode']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
 ets_mp_desktop_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLine']->value), ENT_QUOTES, 'UTF-8');?>
 ets_mp_tablet_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineTablet']->value), ENT_QUOTES, 'UTF-8');?>
 ets_mp_mobile_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineMobile']->value), ENT_QUOTES, 'UTF-8');?>
">
    	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
              <?php $_smarty_tpl->_subTemplateRender("file:catalog/_partials/miniatures/product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('product'=>$_smarty_tpl->tpl_vars['product']->value), 0, true);
?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
<?php } else { ?>
    <div data-row-desktop="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLine']->value), ENT_QUOTES, 'UTF-8');?>
" data-row-tablet="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineTablet']->value), ENT_QUOTES, 'UTF-8');?>
" data-row-mobile="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineMobile']->value), ENT_QUOTES, 'UTF-8');?>
" id="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" class="no-product <?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
 crosssell_product_list_wrapper<?php if ((isset($_smarty_tpl->tpl_vars['tab']->value)) && $_smarty_tpl->tpl_vars['tab']->value) {?> cs-wrapper-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['tab']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');
}?> layout-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['layout_mode']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
 ets_mp_desktop_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLine']->value), ENT_QUOTES, 'UTF-8');?>
 ets_mp_tablet_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineTablet']->value), ENT_QUOTES, 'UTF-8');?>
 ets_mp_mobile_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['nbItemsPerLineMobile']->value), ENT_QUOTES, 'UTF-8');?>
">
        <div class="col-sm-12 col-xs-12"><div class="clearfix"></div><span class="alert alert-warning"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'No products available','mod'=>'ets_crosssell'),$_smarty_tpl ) );?>
</span></div>
    </div>
<?php }
}
}
