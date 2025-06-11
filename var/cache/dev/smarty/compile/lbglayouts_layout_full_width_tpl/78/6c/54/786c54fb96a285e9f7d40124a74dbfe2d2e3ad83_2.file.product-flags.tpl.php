<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/product-flags.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0a07c97_01684661',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '786c54fb96a285e9f7d40124a74dbfe2d2e3ad83' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/_partials/product-flags.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0a07c97_01684661 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1697963549683d49d0a048e9_63881829', 'product_flags');
?>

<?php }
/* {block 'product_flags'} */
class Block_1697963549683d49d0a048e9_63881829 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_flags' => 
  array (
    0 => 'Block_1697963549683d49d0a048e9_63881829',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <ul class="product-flags js-product-flags">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value['flags'], 'flag');
$_smarty_tpl->tpl_vars['flag']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['flag']->value) {
$_smarty_tpl->tpl_vars['flag']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['flag']->value['type'] == "out_of_stock") {?>
                <?php if ($_smarty_tpl->tpl_vars['product']->value['not_available_message'] != '') {?>
                    <li class="product-flag <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['flag']->value['type'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['not_available_message'], ENT_QUOTES, 'UTF-8');?>
</li>
                <?php } elseif ($_smarty_tpl->tpl_vars['product']->value['available_later'] != '') {?>
                    <li class="product-flag <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['flag']->value['type'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['product']->value['available_later'], ENT_QUOTES, 'UTF-8');?>
</li>
                <?php } else { ?>
                    <li class="product-flag <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['flag']->value['type'], ENT_QUOTES, 'UTF-8');?>
"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Rupture stock'),$_smarty_tpl ) );?>
</li>
                <?php }?>
            <?php } else { ?>
				<?php if ($_smarty_tpl->tpl_vars['flag']->value['label'] == "Neuf") {?>
					<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['flag']) ? $_smarty_tpl->tpl_vars['flag']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['label'] = "Nouveau";
$_smarty_tpl->_assignInScope('flag', $_tmp_array);?>
				<?php }?>
                <li class="product-flag <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['flag']->value['type'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['flag']->value['label'], ENT_QUOTES, 'UTF-8');?>
</li>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
<?php
}
}
/* {/block 'product_flags'} */
}
