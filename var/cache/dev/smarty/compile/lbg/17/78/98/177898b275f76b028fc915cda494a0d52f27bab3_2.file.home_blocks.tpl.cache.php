<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/home_blocks.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d054f8b4_88343561',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '177898b275f76b028fc915cda494a0d52f27bab3' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/home_blocks.tpl',
      1 => 1738070956,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d054f8b4_88343561 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '140745920683d49d054e1f7_81644757';
if ($_smarty_tpl->tpl_vars['position_homepages']->value) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        var number_home_posts_per_row = <?php if ($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_PER_ROW']) {
echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_PER_ROW']), ENT_QUOTES, 'UTF-8');
} else { ?>4<?php }?>;
    <?php echo '</script'; ?>
>
<?php }
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['position_homepages']->value, 'position');
$_smarty_tpl->tpl_vars['position']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['position']->value) {
$_smarty_tpl->tpl_vars['position']->do_else = false;
?>
    <?php echo $_smarty_tpl->tpl_vars['homepages']->value[$_smarty_tpl->tpl_vars['position']->value];?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
