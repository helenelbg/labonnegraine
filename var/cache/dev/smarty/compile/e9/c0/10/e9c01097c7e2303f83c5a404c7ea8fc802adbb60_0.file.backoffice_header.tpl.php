<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:15:17
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/hook/backoffice_header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304ab598c0b2_91430585',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e9c01097c7e2303f83c5a404c7ea8fc802adbb60' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/hook/backoffice_header.tpl',
      1 => 1738070992,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304ab598c0b2_91430585 (Smarty_Internal_Template $_smarty_tpl) {
?><style>
i.mi-ce {
	font-size: 14px !important;
}
i.icon-AdminParentCEContent, i.mi-ce {
	position: relative;
	height: 1em;
	width: 1.2857em;
}
i.icon-AdminParentCEContent:before, i.mi-ce:before,
i.icon-AdminParentCEContent:after, i.mi-ce:after {
	content: '';
	position: absolute;
	margin: 0;
	left: .2143em;
	top: 0;
	width: .9286em;
	height: .6428em;
	border-width: .2143em 0;
	border-style: solid;
	border-color: currentColor;
	box-sizing: content-box;
}
i.icon-AdminParentCEContent:after, i.mi-ce:after {
	top: .4286em;
	width: .6428em;
	height: 0;
	border-width: .2143em 0 0;
}
#maintab-AdminParentCreativeElements, #subtab-AdminParentCreativeElements {
	display: none;
}
</style>
<?php if (!empty($_smarty_tpl->tpl_vars['edit_width_ce']->value)) {
echo '<script'; ?>
 type="text/html" id="tmpl-btn-back-to-ps">
    <a href="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['edit_width_ce']->value,'html','UTF-8' ));?>
&amp;action=backToPsEditor" class="btn btn-default btn-back-to-ps"><i class="material-icons">navigate_before</i> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Back to PrestaShop Editor','mod'=>'creativeelements'),$_smarty_tpl ) );?>
</a>
<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/html" id="tmpl-btn-edit-with-ce">
    <a href="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['edit_width_ce']->value,'html','UTF-8' ));?>
" class="btn pointer btn-edit-with-ce"><i class="material-icons mi-ce"></i> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Edit with Creative Elements','mod'=>'creativeelements'),$_smarty_tpl ) );?>
</a>
<?php echo '</script'; ?>
>
<?php }
}
}
