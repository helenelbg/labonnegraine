<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/_partials/helpers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dca8246_40439193',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd09ea30c3c34523e2e3989eae671b324f21d200a' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/_partials/helpers.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683fe28dca8246_40439193 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'renderLogo' => 
  array (
    'compiled_filepath' => '/home/dev.labonnegraine.com/public_html/var/cache/dev/smarty/compile/lbglayouts_layout_left_column_tpl/d0/9e/a3/d09ea30c3c34523e2e3989eae671b324f21d200a_2.file.helpers.tpl.php',
    'uid' => 'd09ea30c3c34523e2e3989eae671b324f21d200a',
    'call_name' => 'smarty_template_function_renderLogo_2130384121683fe28dca5c70_39975815',
  ),
));
?> 

<?php }
/* smarty_template_function_renderLogo_2130384121683fe28dca5c70_39975815 */
if (!function_exists('smarty_template_function_renderLogo_2130384121683fe28dca5c70_39975815')) {
function smarty_template_function_renderLogo_2130384121683fe28dca5c70_39975815(Smarty_Internal_Template $_smarty_tpl,$params) {
foreach ($params as $key => $value) {
$_smarty_tpl->tpl_vars[$key] = new Smarty_Variable($value, $_smarty_tpl->isRenderingCache);
}
?>

  <?php if (Configuration::get('MP_LOGO')) {?>
    <?php $_smarty_tpl->_assignInScope('logo_url', ('/upload/').(Configuration::get('MP_LOGO')));?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['shop']) ? $_smarty_tpl->tpl_vars['shop']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['logo_details']['src'] = $_smarty_tpl->tpl_vars['logo_url']->value;
$_smarty_tpl->_assignInScope('shop', $_tmp_array);?>
  <?php }?>
  <a href="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['urls']->value['pages']['index'], ENT_QUOTES, 'UTF-8');?>
">
    <img
      class="logo img-fluid"
      src="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['shop']->value['logo_details']['src'], ENT_QUOTES, 'UTF-8');?>
"
      alt="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8');?>
"
      width="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['shop']->value['logo_details']['width'], ENT_QUOTES, 'UTF-8');?>
"
      height="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['shop']->value['logo_details']['height'], ENT_QUOTES, 'UTF-8');?>
">
  </a>
<?php
}}
/*/ smarty_template_function_renderLogo_2130384121683fe28dca5c70_39975815 */
}
