<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/modules/ets_crosssell/views/templates/hook/layout_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0ae5193_17327109',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '229e9aed4e485c5af5c5c7f6082543a075de06c0' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ets_crosssell/views/templates/hook/layout_list.tpl',
      1 => 1742303612,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0ae5193_17327109 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['sc_configs']->value) {?>
    <div class="<?php if ((isset($_smarty_tpl->tpl_vars['sub_categories']->value)) && $_smarty_tpl->tpl_vars['sub_categories']->value && ($_smarty_tpl->tpl_vars['products']->value || ((isset($_smarty_tpl->tpl_vars['id_ets_css_sub_category']->value)) && $_smarty_tpl->tpl_vars['id_ets_css_sub_category']->value))) {?>ets_crosssell_has_sub <?php }?>ets_crosssell_block block products_block featured-products ets_crosssell_<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
 layout_list clearfix ">
        <ul>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sc_configs']->value, 'sc_config');
$_smarty_tpl->tpl_vars['sc_config']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['sc_config']->value) {
$_smarty_tpl->tpl_vars['sc_config']->do_else = false;
?>
                <li class="ets_crosssell_list_blocks ">
                    <h4 class="ets_crosssell_title"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sc_config']->value['tab_name'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</h4>
                    <div class="tab_content<?php if ($_smarty_tpl->tpl_vars['sc_config']->value['sub_categories']) {?> ets_crosssell_has_sub<?php }?>" id="tab-content-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sc_config']->value['tab'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
">
                        <?php echo Module::getInstanceByName('ets_crosssell')->excuteHookDisplay($_smarty_tpl->tpl_vars['sc_config']->value['hook'],$_smarty_tpl->tpl_vars['name_page']->value,$_smarty_tpl->tpl_vars['id_product']->value);?>

                    </div>
                    <div class="clearfix"></div>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    </div>
<?php }
}
}
