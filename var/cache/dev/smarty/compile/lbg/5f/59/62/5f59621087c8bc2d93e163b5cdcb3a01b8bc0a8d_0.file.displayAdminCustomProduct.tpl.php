<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:49:20
  from '/home/dev.labonnegraine.com/public_html/modules/colissimo/views/templates/hook/admin/displayAdminCustomProduct.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49705148a8_52932445',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5f59621087c8bc2d93e163b5cdcb3a01b8bc0a8d' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/colissimo/views/templates/hook/admin/displayAdminCustomProduct.tpl',
      1 => 1738070976,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49705148a8_52932445 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="row">
  <div class="col-md-12">
    <p class="subtitle"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Please fill in the customs information of your product','mod'=>'colissimo'),$_smarty_tpl ) );?>
</p>
    <div class="row">
      <div class="form-group col-md-4">
        <label class="form-control-label"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Hs code','mod'=>'colissimo'),$_smarty_tpl ) );?>
</label>
        <input name="colissimo_hs_code"
               type="text"
               class="form-control"
               value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['product_details']->value->hs_code,'html','UTF-8' ));?>
"/>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-4">
        <label class="form-control-label"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Country origin','mod'=>'colissimo'),$_smarty_tpl ) );?>
</label>
        <select class="form-control" name="colissimo_country_origin">
          <option value="0"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'-- Please select a country --','mod'=>'colissimo'),$_smarty_tpl ) );?>
</option>
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['countries']->value, 'country');
$_smarty_tpl->tpl_vars['country']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['country']->value) {
$_smarty_tpl->tpl_vars['country']->do_else = false;
?>
            <option value="<?php echo intval($_smarty_tpl->tpl_vars['country']->value['id_country']);?>
"
                    <?php if ($_smarty_tpl->tpl_vars['product_details']->value->id_country_origin == $_smarty_tpl->tpl_vars['country']->value['id_country']) {?>selected<?php }?>>
              <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['country']->value['name'],'html','UTF-8' ));?>

            </option>
          <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-4">
        <label class="form-control-label"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Short description','mod'=>'colissimo'),$_smarty_tpl ) );?>
</label>
        <input name="colissimo_short_desc"
               class="form-control"
               maxLenght="64"
               value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['product_details']->value->short_desc,'html','UTF-8' ));?>
"/>
      </div>
    </div>
    <input name="colissimo_update_product" type="hidden" value="1" />
  </div>
</div>
<?php }
}
