<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from 'module:blockreassuranceviewstemp' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d02c5628_15360153',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9ffc009d1b66ea89054a8e253403b7d3a67d8150' => 
    array (
      0 => 'module:blockreassuranceviewstemp',
      1 => 1738070829,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d02c5628_15360153 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '1940250308683d49d02c3ed0_94081877';
?>
<!-- begin /home/dev.labonnegraine.com/public_html/themes/lbg/modules/blockreassurance/views/templates/hook/blockreassurance.tpl --><?php if ($_smarty_tpl->tpl_vars['elements']->value) {?>
  <div id="block-reassurance">
    <ul>
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elements']->value, 'element');
$_smarty_tpl->tpl_vars['element']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['element']->value) {
$_smarty_tpl->tpl_vars['element']->do_else = false;
?>
        <li>
          <div class="block-reassurance-item">
            <img src="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['element']->value['image'], ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['element']->value['text'], ENT_QUOTES, 'UTF-8');?>
" loading="lazy">
            <span class="h6"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['element']->value['text'], ENT_QUOTES, 'UTF-8');?>
</span>
          </div>
        </li>
      <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
  </div>
<?php }?>
<!-- end /home/dev.labonnegraine.com/public_html/themes/lbg/modules/blockreassurance/views/templates/hook/blockreassurance.tpl --><?php }
}
