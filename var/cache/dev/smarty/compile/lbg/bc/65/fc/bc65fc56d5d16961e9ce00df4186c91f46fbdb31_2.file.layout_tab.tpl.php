<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:11
  from '/home/helene/prestashop/modules/ets_crosssell/views/templates/hook/layout_tab.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35b7a70a75_56887603',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bc65fc56d5d16961e9ce00df4186c91f46fbdb31' => 
    array (
      0 => '/home/helene/prestashop/modules/ets_crosssell/views/templates/hook/layout_tab.tpl',
      1 => 1749809004,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35b7a70a75_56887603 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['sc_configs']->value) {?>
    <div class="ets_crosssell_block ets_crosssell_layout-tab layout-<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['layout_mode']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
 block products_block ets_crosssell_<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
 layout_tab clearfix ">
        <ul id="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
-tabs" class="ets_crosssell_nav_tabs nav nav-tabs clearfix">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sc_configs']->value, 'sc_config', false, 'key');
$_smarty_tpl->tpl_vars['sc_config']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['sc_config']->value) {
$_smarty_tpl->tpl_vars['sc_config']->do_else = false;
?>
                <li class="<?php if ($_smarty_tpl->tpl_vars['key']->value == 0) {?>active<?php }?>">
                    <a class="ets_crosssell_tab" data-page="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" data-tab="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sc_config']->value['tab'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" data-id_product="<?php echo htmlspecialchars((string) (intval($_smarty_tpl->tpl_vars['id_product']->value)), ENT_QUOTES, 'UTF-8');?>
" href="#tab-content-<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sc_config']->value['tab'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sc_config']->value['tab_name'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
</a>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
        <div id="<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
-contents" class="ets_crosssell_tab_content tab-content row">
            <div id="tab-content-<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['name_page']->value,'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) (call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['sc_configs']->value[0]['tab'],'html','UTF-8' ))), ENT_QUOTES, 'UTF-8');?>
" class="list-content active<?php if ($_smarty_tpl->tpl_vars['sc_configs']->value[0]['sub_categories']) {?> ets_crosssell_has_sub<?php }?>">
                <?php echo Module::getInstanceByName('ets_crosssell')->excuteHookDisplay($_smarty_tpl->tpl_vars['sc_configs']->value[0]['hook'],$_smarty_tpl->tpl_vars['name_page']->value,$_smarty_tpl->tpl_vars['id_product']->value);?>

            </div>
        </div>
    </div>
<?php }
}
}
