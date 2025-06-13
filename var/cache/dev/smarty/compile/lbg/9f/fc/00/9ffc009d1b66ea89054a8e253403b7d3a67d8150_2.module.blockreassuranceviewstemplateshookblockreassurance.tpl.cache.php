<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:27:34
  from 'module:blockreassuranceviewstemplateshookblockreassurance.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35566d8c91_42964948',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9ffc009d1b66ea89054a8e253403b7d3a67d8150' => 
    array (
      0 => 'module:blockreassuranceviewstemplateshookblockreassurance.tpl',
      1 => 1749808843,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35566d8c91_42964948 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '456680755684c35566d5d40_54390442';
?>
<!-- begin /home/helene/prestashop/themes/lbg/modules/blockreassurance/views/templates/hook/blockreassurance.tpl --><?php if ($_smarty_tpl->tpl_vars['elements']->value) {?>
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
            <img src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['element']->value['image']), ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['element']->value['text']), ENT_QUOTES, 'UTF-8');?>
" loading="lazy">
            <span class="h6"><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['element']->value['text']), ENT_QUOTES, 'UTF-8');?>
</span>
          </div>
        </li>
      <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
  </div>
<?php }?>
<!-- end /home/helene/prestashop/themes/lbg/modules/blockreassurance/views/templates/hook/blockreassurance.tpl --><?php }
}
