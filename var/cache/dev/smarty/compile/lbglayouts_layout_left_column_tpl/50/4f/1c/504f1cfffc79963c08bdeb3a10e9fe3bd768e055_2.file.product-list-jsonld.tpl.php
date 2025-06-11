<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/_partials/microdata/product-list-jsonld.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dcdde04_82388189',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '504f1cfffc79963c08bdeb3a10e9fe3bd768e055' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/creativeelements/views/templates/front/theme/_partials/microdata/product-list-jsonld.tpl',
      1 => 1738070992,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683fe28dcdde04_82388189 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "ItemList",
	"itemListElement": [
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['listing']->value['products'], 'item', false, NULL, 'products', array (
  'iteration' => true,
  'last' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['total'];
?>
		{
			"@type": "ListItem",
			"position": <?php echo htmlspecialchars((string) intval((isset($_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['iteration'] : null)), ENT_QUOTES, 'UTF-8');?>
,
			"name": "<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['item']->value['name'], ENT_QUOTES, 'UTF-8');?>
",
			"url": "<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['item']->value['url'], ENT_QUOTES, 'UTF-8');?>
"
		}<?php if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_products']->value['last'] : null)) {?>,<?php }?>
	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	]
}
<?php echo '</script'; ?>
><?php }
}
