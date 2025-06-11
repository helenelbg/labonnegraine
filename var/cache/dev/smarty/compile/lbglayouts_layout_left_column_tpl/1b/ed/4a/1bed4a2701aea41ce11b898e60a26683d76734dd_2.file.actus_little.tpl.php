<?php
/* Smarty version 4.2.1, created on 2025-06-04 08:07:09
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/_partials/actus_little.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683fe28dd05b60_03341618',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1bed4a2701aea41ce11b898e60a26683d76734dd' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/_partials/actus_little.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683fe28dd05b60_03341618 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="position_actus">
	<div id="actus_header">
		<div id="news-container1">
			<ul>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, Tools::getActualites(), 'actu', false, 'k');
$_smarty_tpl->tpl_vars['actu']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['actu']->value) {
$_smarty_tpl->tpl_vars['actu']->do_else = false;
?>
				<li><a href="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value->getCMSLink($_smarty_tpl->tpl_vars['actu']->value['id_cms'],$_smarty_tpl->tpl_vars['actu']->value['link_rewrite']), ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['actu']->value['meta_title'], ENT_QUOTES, 'UTF-8');?>
"><p><u><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['actu']->value['meta_title'], ENT_QUOTES, 'UTF-8');?>
</u>
				<?php if ($_smarty_tpl->tpl_vars['actu']->value['meta_description']) {?>
				: <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['actu']->value['meta_description'], ENT_QUOTES, 'UTF-8');?>

				<?php }?></p></a>
				</li>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			</ul>
		</div>
	</div>
</div>

<?php }
}
