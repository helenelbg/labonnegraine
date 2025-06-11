<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:15:20
  from '/home/dev.labonnegraine.com/public_html/admin123/themes/new-theme/template/content.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304ab817e461_43586470',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c37bad752ca2c54dee9428a2908e59aa787d67ef' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/admin123/themes/new-theme/template/content.tpl',
      1 => 1738070872,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304ab817e461_43586470 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="ajax_confirmation" class="alert alert-success" style="display: none;"></div>
<div id="content-message-box"></div>


<?php if ((isset($_smarty_tpl->tpl_vars['content']->value))) {?>
  <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php }
}
}
