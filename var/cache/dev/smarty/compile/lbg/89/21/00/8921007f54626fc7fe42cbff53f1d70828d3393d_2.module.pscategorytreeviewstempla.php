<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from 'module:pscategorytreeviewstempla' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d09fc970_44102764',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8921007f54626fc7fe42cbff53f1d70828d3393d' => 
    array (
      0 => 'module:pscategorytreeviewstempla',
      1 => 1742500482,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d09fc970_44102764 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'categories' => 
  array (
    'compiled_filepath' => '/home/dev.labonnegraine.com/public_html/var/cache/dev/smarty/compile/lbg/89/21/00/8921007f54626fc7fe42cbff53f1d70828d3393d_2.module.pscategorytreeviewstempla.php',
    'uid' => '8921007f54626fc7fe42cbff53f1d70828d3393d',
    'call_name' => 'smarty_template_function_categories_848561995683d49d09f3465_65257793',
  ),
));
?><!-- begin /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_categorytree/views/templates/hook/ps_categorytree.tpl -->


<div class="block-categories">
  <ul class="category-top-menu">
    <li><div class="text-uppercase h6">
	Notre catalogue
		</div></li>
    <?php if (!empty($_smarty_tpl->tpl_vars['categories']->value['children'])) {?>
      <li><?php $_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'categories', array('nodes'=>$_smarty_tpl->tpl_vars['categories']->value['children']), true);?>
</li>
    <?php }?>
  </ul>
</div>

<?php if (Configuration::get('MP_VISUEL_1')) {?>
	<?php $_smarty_tpl->_assignInScope('visuel_1', ('/upload/').(Configuration::get('MP_VISUEL_1')));?>
	<div class="visuel_colonne_gauche">
		<br />
		<a href="/content/89-reseaux-sociaux">
			<img src="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['visuel_1']->value, ENT_QUOTES, 'UTF-8');?>
" alt="" />
		</a><br />&nbsp;
	</div>
<?php }?>			
<!-- end /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_categorytree/views/templates/hook/ps_categorytree.tpl --><?php }
/* smarty_template_function_categories_848561995683d49d09f3465_65257793 */
if (!function_exists('smarty_template_function_categories_848561995683d49d09f3465_65257793')) {
function smarty_template_function_categories_848561995683d49d09f3465_65257793(Smarty_Internal_Template $_smarty_tpl,$params) {
$params = array_merge(array('nodes'=>array(),'depth'=>0), $params);
foreach ($params as $key => $value) {
$_smarty_tpl->tpl_vars[$key] = new Smarty_Variable($value, $_smarty_tpl->isRenderingCache);
}
?>

  <?php $_smarty_tpl->_assignInScope('categoryId', 0);?>
  <?php if ((isset($_smarty_tpl->tpl_vars['category']->value))) {?>
    <?php if ((isset($_smarty_tpl->tpl_vars['category']->value->id))) {?>
      <?php $_smarty_tpl->_assignInScope('categoryId', $_smarty_tpl->tpl_vars['category']->value->id);?>     <?php } elseif ((isset($_smarty_tpl->tpl_vars['category']->value['id']))) {?>
      <?php $_smarty_tpl->_assignInScope('categoryId', $_smarty_tpl->tpl_vars['category']->value['id']);?>     <?php }?>
  <?php }?>
  
  <?php if (count($_smarty_tpl->tpl_vars['nodes']->value)) {?><ul class="category-sub-menu"><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['nodes']->value, 'node');
$_smarty_tpl->tpl_vars['node']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['node']->value) {
$_smarty_tpl->tpl_vars['node']->do_else = false;
if ($_smarty_tpl->tpl_vars['node']->value['id'] != 5 && $_smarty_tpl->tpl_vars['node']->value['id'] != 227) {?> 			  <?php $_smarty_tpl->_assignInScope('expanded', false);
if ($_smarty_tpl->tpl_vars['node']->value['id'] == $_smarty_tpl->tpl_vars['categoryId']->value) {
$_smarty_tpl->_assignInScope('expanded', true);
}
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['node']->value['children'], 'child');
$_smarty_tpl->tpl_vars['child']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['child']->value) {
$_smarty_tpl->tpl_vars['child']->do_else = false;
if ($_smarty_tpl->tpl_vars['child']->value['id'] == $_smarty_tpl->tpl_vars['categoryId']->value) {
$_smarty_tpl->_assignInScope('expanded', true);
}
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['child']->value['children'], 'child2');
$_smarty_tpl->tpl_vars['child2']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['child2']->value) {
$_smarty_tpl->tpl_vars['child2']->do_else = false;
if ($_smarty_tpl->tpl_vars['child2']->value['id'] == $_smarty_tpl->tpl_vars['categoryId']->value) {
$_smarty_tpl->_assignInScope('expanded', true);
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><li data-depth="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['depth']->value, ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['node']->value['id'] == $_smarty_tpl->tpl_vars['categoryId']->value) {?>class="active"<?php }?>><a href="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['node']->value['link'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['node']->value['name'], ENT_QUOTES, 'UTF-8');?>
</a><?php if ($_smarty_tpl->tpl_vars['node']->value['children']) {?><div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#exCollapsingNavbar<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['node']->value['id'], ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['expanded']->value) {?> aria-expanded="true"<?php }?>><i class="material-icons add">&#xE145;</i><i class="material-icons remove">&#xE15B;</i></div><div class="collapse<?php if ($_smarty_tpl->tpl_vars['expanded']->value) {?> in<?php }?>" id="exCollapsingNavbar<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['node']->value['id'], ENT_QUOTES, 'UTF-8');?>
"><?php $_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'categories', array('nodes'=>$_smarty_tpl->tpl_vars['node']->value['children'],'depth'=>$_smarty_tpl->tpl_vars['depth']->value+1), true);?>
</div><?php }?></li><?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></ul><?php }?>

<?php
}}
/*/ smarty_template_function_categories_848561995683d49d09f3465_65257793 */
}
