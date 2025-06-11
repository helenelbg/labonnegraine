<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff8129d1_81797205',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '922192d02e0362a31c4e102fb510b5ff4524b845' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/footer.tpl',
      1 => 1738070956,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff8129d1_81797205 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/javascript">
    ybc_blog_like_url = '<?php echo addslashes($_smarty_tpl->tpl_vars['like_url']->value);?>
';
    ybc_like_error ='<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( addslashes($_smarty_tpl->tpl_vars['ybc_like_error']->value),'quotes','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
'
    YBC_BLOG_GALLERY_SPEED = <?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['YBC_BLOG_GALLERY_SPEED']->value), ENT_QUOTES, 'UTF-8');?>
;
    YBC_BLOG_SLIDER_SPEED = <?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['YBC_BLOG_SLIDER_SPEED']->value), ENT_QUOTES, 'UTF-8');?>
;
    YBC_BLOG_GALLERY_SKIN = '<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( addslashes($_smarty_tpl->tpl_vars['YBC_BLOG_GALLERY_SKIN']->value),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
';
    YBC_BLOG_GALLERY_AUTO_PLAY = <?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['YBC_BLOG_GALLERY_AUTO_PLAY']->value), ENT_QUOTES, 'UTF-8');?>
;
<?php echo '</script'; ?>
><?php }
}
