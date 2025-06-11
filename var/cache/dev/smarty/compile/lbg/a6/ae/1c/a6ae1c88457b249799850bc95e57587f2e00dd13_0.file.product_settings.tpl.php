<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:49:20
  from '/home/dev.labonnegraine.com/public_html/modules/ets_affiliatemarketing/views/templates/hook/product_settings.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d4970532b56_65058959',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a6ae1c88457b249799850bc95e57587f2e00dd13' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ets_affiliatemarketing/views/templates/hook/product_settings.tpl',
      1 => 1738070992,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d4970532b56_65058959 (Smarty_Internal_Template $_smarty_tpl) {
?><style type="text/css">
    .ets-am-product-settings label.required:before {
        content: "*";
        color: red;
    }
</style>

<div class="ets-am-product-settings">
    <div class="row fields-setting">
        <?php if (!empty($_smarty_tpl->tpl_vars['settings']->value)) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['settings']->value, 'setting', false, 'index');
$_smarty_tpl->tpl_vars['setting']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['setting']->value) {
$_smarty_tpl->tpl_vars['setting']->do_else = false;
?>
                <?php if ($_smarty_tpl->tpl_vars['setting']->value) {?>
                    <div class="<?php if ($_smarty_tpl->tpl_vars['using_cart']->value) {?>col-md-12<?php } else { ?>col-md-6<?php }?>" <?php if ($_smarty_tpl->tpl_vars['index']->value == 'loyalty_reward' && $_smarty_tpl->tpl_vars['using_cart']->value) {?>style="display:none;"<?php }?>>
                        <div class="card <?php if ($_smarty_tpl->tpl_vars['index']->value == 'loyalty_reward') {?>loyalty_reward<?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 'aff_reward') {?>aff_reward<?php }?>"
                             data-type="<?php if ($_smarty_tpl->tpl_vars['index']->value == 'loyalty_reward') {?>loyalty_reward<?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 'aff_reward') {?>aff_reward<?php }?>">
                            <div class="card-header">
                                <?php if ($_smarty_tpl->tpl_vars['index']->value == 'loyalty_reward') {?>
                                    <h3 class="card-title"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Loyalty program','mod'=>'ets_affiliatemarketing'),$_smarty_tpl ) );?>
</h3>
                                <?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 'aff_reward') {?>
                                    <h3 class="card-title"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Affiliate program','mod'=>'ets_affiliatemarketing'),$_smarty_tpl ) );?>
</h3>
                                <?php }?>
                            </div>
                            <div class="card-body">
                                <div class="checkbox">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" data-toggle="switch" class="tiny"
                                                   name="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['index']->value,'html','UTF-8' ));?>
_use_default" value="1"
                                                   <?php if ($_smarty_tpl->tpl_vars['settings']->value[$_smarty_tpl->tpl_vars['index']->value]['use_default'] == 1) {?>checked="checked"<?php }?>>
                                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Use default setting','mod'=>'ets_affiliatemarketing'),$_smarty_tpl ) );?>

                                        </label>
                                    </div>
                                </div>
                                <?php if (!empty($_smarty_tpl->tpl_vars['setting']->value)) {?>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['setting']->value, 'input', false, 'key');
$_smarty_tpl->tpl_vars['input']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['input']->value) {
$_smarty_tpl->tpl_vars['input']->do_else = false;
?>
                                        <?php if (is_array($_smarty_tpl->tpl_vars['input']->value) && $_smarty_tpl->tpl_vars['input']->value['type'] == 'text') {?>
                                            <div class="form-group">
                                                <label <?php if ($_smarty_tpl->tpl_vars['key']->value != 'qty_min') {?> class="required" <?php }?>><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['label'],'html','UTF-8' ));?>
</label>
                                                <?php if ((isset($_smarty_tpl->tpl_vars['input']->value['suffix'])) && $_smarty_tpl->tpl_vars['input']->value['suffix']) {?>
                                                    <div class="input-group">
                                                        <input type="text" name="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['key']->value,'html','UTF-8' ));?>
"
                                                               value="<?php if ((isset($_smarty_tpl->tpl_vars['input']->value['value'])) && $_smarty_tpl->tpl_vars['input']->value['value']) {
echo round($_smarty_tpl->tpl_vars['input']->value['value'],2);
} elseif ((isset($_smarty_tpl->tpl_vars['input']->value['default'])) && $_smarty_tpl->tpl_vars['input']->value['default']) {
echo round($_smarty_tpl->tpl_vars['input']->value['default'],2);
}?>"
                                                               class="form-control <?php if ((isset($_smarty_tpl->tpl_vars['input']->value['class'])) && $_smarty_tpl->tpl_vars['input']->value['class']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['class'],'html','UTF-8' ));
}?>">
                                                        <?php if ($_smarty_tpl->tpl_vars['is17']->value) {?>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['suffix'],'html','UTF-8' ));?>
</span>
                                                        </div>
                                                        <?php } else { ?>
                                                        <span class="input-group-addon"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['suffix'],'html','UTF-8' ));?>
</span>
                                                        <?php }?>
                                                    </div>
                                                <?php } else { ?>
                                                    <input type="text" name="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['key']->value,'html','UTF-8' ));?>
"
                                                           value="<?php if ((isset($_smarty_tpl->tpl_vars['input']->value['value'])) && $_smarty_tpl->tpl_vars['input']->value['value']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['value'],'html','UTF-8' ));
} elseif ((isset($_smarty_tpl->tpl_vars['input']->value['default'])) && $_smarty_tpl->tpl_vars['input']->value['default']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['default'],'html','UTF-8' ));
}?>"
                                                           class="form-control <?php if ((isset($_smarty_tpl->tpl_vars['input']->value['class'])) && $_smarty_tpl->tpl_vars['input']->value['class']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['class'],'html','UTF-8' ));
}?>">
                                                <?php }?>
                                            </div>
                                        <?php } elseif (is_array($_smarty_tpl->tpl_vars['input']->value) && $_smarty_tpl->tpl_vars['input']->value['type'] == 'ets_radio_group') {?>
                                            <?php $_smarty_tpl->_assignInScope('radios_group', $_smarty_tpl->tpl_vars['input']->value['values']);?>
                                            <?php if (!empty($_smarty_tpl->tpl_vars['radios_group']->value)) {?>
                                                <div class="form-group">
                                                    <label><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['label'],'html','UTF-8' ));?>
</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['radios_group']->value, 'group');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
                                                                    <?php if ((isset($_smarty_tpl->tpl_vars['group']->value['is_all'])) && $_smarty_tpl->tpl_vars['group']->value['is_all']) {?>
                                                                        <tr>
                                                                            <td class="w-10 border-r">
    										                                    <span class="title_box">
    										                                        <input type="radio"
                                                                                           name="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['key']->value,'html','UTF-8' ));?>
"
                                                                                           value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['value'],'html','UTF-8' ));?>
"
                                                                                           id="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['id'],'html','UTF-8' ));?>
" <?php if ((isset($_smarty_tpl->tpl_vars['input']->value['value'])) && $_smarty_tpl->tpl_vars['input']->value['value'] == $_smarty_tpl->tpl_vars['group']->value['value']) {?> checked <?php } elseif ((!(isset($_smarty_tpl->tpl_vars['input']->value['value'])) || !$_smarty_tpl->tpl_vars['input']->value['value']) && (isset($_smarty_tpl->tpl_vars['group']->value['default'])) && $_smarty_tpl->tpl_vars['group']->value['default']) {?> checked <?php }?> <?php if ((isset($_smarty_tpl->tpl_vars['group']->value['data_decide'])) && $_smarty_tpl->tpl_vars['group']->value['data_decide']) {?>data-decide="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['data_decide'],'html','UTF-8' ));?>
"<?php }?>
                                                                                           class="<?php if ((isset($_smarty_tpl->tpl_vars['input']->value['class'])) && $_smarty_tpl->tpl_vars['input']->value['class']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['class'],'html','UTF-8' ));
}?>">
    										                                    </span>
                                                                            </td>
                                                                            <td>
                                                                                <label class="mb-0" for="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['id'],'html','UTF-8' ));?>
"
                                                                                       style="width: 100%; font-weight: 400;">
                                                                                    <span class="title_box"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['title'],'html','UTF-8' ));?>
</span>
                                                                                </label>
                                                                            </td>
                                                                        </tr>
                                                                        <?php break 1;?>
                                                                    <?php }?>
                                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['radios_group']->value, 'group');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
                                                                    <?php if (!(isset($_smarty_tpl->tpl_vars['group']->value['is_all'])) || !$_smarty_tpl->tpl_vars['group']->value['is_all']) {?>
                                                                        <tr>
                                                                            <td class="w-10 border-r">
    										                                        <span class="title_box">
    										                                            <input type="radio"
                                                                                               name="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['key']->value,'html','UTF-8' ));?>
"
                                                                                               value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['value'],'html','UTF-8' ));?>
"
                                                                                               id="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['id'],'html','UTF-8' ));?>
"
                                                                                                <?php if ((isset($_smarty_tpl->tpl_vars['input']->value['value'])) && $_smarty_tpl->tpl_vars['input']->value['value'] == $_smarty_tpl->tpl_vars['group']->value['value']) {?> checked
                                                                                                <?php } elseif ((!(isset($_smarty_tpl->tpl_vars['input']->value['value'])) || !$_smarty_tpl->tpl_vars['input']->value['value']) && (isset($_smarty_tpl->tpl_vars['group']->value['default'])) && $_smarty_tpl->tpl_vars['group']->value['default']) {?> checked  <?php }?>
                                                                                                <?php if ((isset($_smarty_tpl->tpl_vars['group']->value['data_decide'])) && $_smarty_tpl->tpl_vars['group']->value['data_decide']) {?> data-decide="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['data_decide'],'html','UTF-8' ));?>
"<?php }?>
                                                                                               class="<?php if ((isset($_smarty_tpl->tpl_vars['input']->value['class'])) && $_smarty_tpl->tpl_vars['input']->value['class']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['input']->value['class'],'html','UTF-8' ));
}?>">
    										                                        </span>
                                                                            </td>
                                                                            <td>
                                                                                <label class="mb-0" for="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['id'],'html','UTF-8' ));?>
"
                                                                                       style="width: 100%; font-weight: 400;">
                                                                                    <span class="title_box"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['group']->value['title'],'html','UTF-8' ));?>
</span>
                                                                                </label>
                                                                            </td>
                                                                        </tr>
                                                                    <?php }?>
                                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }?>

                                        <?php }?>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                                <?php }?>
                                <input type="hidden" name="id_product" value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['id_product']->value,'html','UTF-8' ));?>
">
                            </div>
                        </div>
                    </div>
                <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php }?>
        <div class="col-md-12 px-15">
            <button type="button"
                    class="btn btn-primary js-ets-sm-save-setting-prd"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Save tab settings','mod'=>'ets_affiliatemarketing'),$_smarty_tpl ) );?>
</button>
        </div>
    </div>
    <?php echo '<script'; ?>
 type="text/javascript">
        var ets_am_msg_required = "<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'This value field is invalid.','mod'=>'ets_affiliatemarketing'),$_smarty_tpl ) );?>
";
        var ets_am_link_ajax = "<?php echo $_smarty_tpl->tpl_vars['linkAjax']->value;?>
";
    <?php echo '</script'; ?>
>
    <?php if (!$_smarty_tpl->tpl_vars['is17']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['linkJs']->value;?>
"><?php echo '</script'; ?>
>
    <?php }?>
</div><?php }
}
