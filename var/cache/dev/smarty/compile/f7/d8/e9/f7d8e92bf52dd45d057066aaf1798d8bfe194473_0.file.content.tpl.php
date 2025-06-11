<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:15:17
  from '/home/dev.labonnegraine.com/public_html/admin123/themes/default/template/content.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304ab5a92275_80210355',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f7d8e92bf52dd45d057066aaf1798d8bfe194473' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/admin123/themes/default/template/content.tpl',
      1 => 1738070869,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304ab5a92275_80210355 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="ajax_confirmation" class="alert alert-success hide"></div>
<div id="ajaxBox" style="display:none"></div>
<div id="content-message-box"></div>

<?php if ((isset($_smarty_tpl->tpl_vars['content']->value))) {?>
	<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php }
}
}
