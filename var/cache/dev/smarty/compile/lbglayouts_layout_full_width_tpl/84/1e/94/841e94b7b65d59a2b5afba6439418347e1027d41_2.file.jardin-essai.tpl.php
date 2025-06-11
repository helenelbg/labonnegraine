<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/jardin-essai.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0a13d74_64985257',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '841e94b7b65d59a2b5afba6439418347e1027d41' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/jardin-essai.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0a13d74_64985257 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('jardin_titre', Product::getJardinTitre($_smarty_tpl->tpl_vars['product']->value['id_product']));
$_smarty_tpl->_assignInScope('jardin_contenu', Product::getJardinContenu($_smarty_tpl->tpl_vars['product']->value['id_product']));
$_smarty_tpl->_assignInScope('type_encart', Product::getTypeEncart($_smarty_tpl->tpl_vars['product']->value['id_product']));?>

<?php if ($_smarty_tpl->tpl_vars['type_encart']->value == 1) {
if ($_smarty_tpl->tpl_vars['jardin_titre']->value || $_smarty_tpl->tpl_vars['jardin_contenu']->value) {?>
	  <div class="info_plus_container">
		<h2 class="info_plus_title"><span class="info_jardin_b">En direct du</span> <span class="info_jardin_c">Jardin d'Essai</span></h2>
		<div id="div_titre_plus"><h2><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['jardin_titre']->value, ENT_QUOTES, 'UTF-8');?>
</h2></div>
		<div id="div_contenu_plus"><?php echo $_smarty_tpl->tpl_vars['jardin_contenu']->value;?>
</div>
		<h3 class="info_plus_address"><a href="https://www.jardin-essai.com" target="_blank">www.jardin-essai.com</a></h3>
	  </div>
  <?php }
} else { ?>
	<?php if ($_smarty_tpl->tpl_vars['jardin_titre']->value || $_smarty_tpl->tpl_vars['jardin_contenu']->value) {?>
		<div class="info_plus_container">
		  <h2 class="info_plus_title2"><img src="/img/coupcoeur-olivier.png" class="visuel_olivier" /><span class="info_jardin_b">Le coup de coeur </span> <span class="info_jardin_c">d'Olivier</span></h2>
		  <div id="div_titre_plus"><h2><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['jardin_titre']->value, ENT_QUOTES, 'UTF-8');?>
</h2></div>
		  <div id="div_contenu_plus"><?php echo $_smarty_tpl->tpl_vars['jardin_contenu']->value;?>
</div>
		  <h3 class="info_plus_address2"><a href="https://www.youtube.com/@LepotagerdOlivier" target="_blank">www.youtube.com/@LepotagerdOlivier</a></h3>
		</div>
	<?php }
}
}
}
