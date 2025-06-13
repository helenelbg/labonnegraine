<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:14
  from '/home/helene/prestashop/themes/lbg/templates/_partials/helpers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35ba69bfa1_67137515',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6092c3ebc33f6a1e45882c21417b2383b534c002' => 
    array (
      0 => '/home/helene/prestashop/themes/lbg/templates/_partials/helpers.tpl',
      1 => 1749808842,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35ba69bfa1_67137515 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'renderLogo' => 
  array (
    'compiled_filepath' => '/home/helene/prestashop/var/cache/dev/smarty/compile/lbglayouts_layout_full_width_tpl/60/92/c3/6092c3ebc33f6a1e45882c21417b2383b534c002_2.file.helpers.tpl.php',
    'uid' => '6092c3ebc33f6a1e45882c21417b2383b534c002',
    'call_name' => 'smarty_template_function_renderLogo_273576711684c35ba698e95_63829638',
  ),
));
?> 

<?php }
/* smarty_template_function_renderLogo_273576711684c35ba698e95_63829638 */
if (!function_exists('smarty_template_function_renderLogo_273576711684c35ba698e95_63829638')) {
function smarty_template_function_renderLogo_273576711684c35ba698e95_63829638(Smarty_Internal_Template $_smarty_tpl,$params) {
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
  <a href="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['urls']->value['pages']['index']), ENT_QUOTES, 'UTF-8');?>
">
    <img
      class="logo img-fluid"
      src="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['shop']->value['logo_details']['src']), ENT_QUOTES, 'UTF-8');?>
"
      alt="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['shop']->value['name']), ENT_QUOTES, 'UTF-8');?>
"
      width="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['shop']->value['logo_details']['width']), ENT_QUOTES, 'UTF-8');?>
"
      height="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['shop']->value['logo_details']['height']), ENT_QUOTES, 'UTF-8');?>
">
  </a>
<?php
}}
/*/ smarty_template_function_renderLogo_273576711684c35ba698e95_63829638 */
}
