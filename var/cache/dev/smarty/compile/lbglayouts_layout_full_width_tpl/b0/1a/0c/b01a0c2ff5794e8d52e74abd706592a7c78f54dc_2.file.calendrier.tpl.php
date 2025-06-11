<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/calendrier.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0a1b8f2_82840246',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b01a0c2ff5794e8d52e74abd706592a7c78f54dc' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/catalog/calendrier.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0a1b8f2_82840246 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('cal1', Product::getPlantationRecolte($_smarty_tpl->tpl_vars['product']->value['id']));
if ($_smarty_tpl->tpl_vars['cal1']->value) {?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[1] = "Jan.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[2] = "Fév.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[3] = "Mar.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[4] = "Avr.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[5] = "Mai";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[6] = "Juin";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[7] = "Jui.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[8] = "Août";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[9] = "Sep.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[10] = "Oct.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[11] = "Nov.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>
	<?php $_tmp_array = isset($_smarty_tpl->tpl_vars['T_mois']) ? $_smarty_tpl->tpl_vars['T_mois']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[12] = "Déc.";
$_smarty_tpl->_assignInScope('T_mois', $_tmp_array);?>

	<h3 id="titre_conditions_de_culture" class="mobile page-product-heading">Conditions de culture</h3>
	<div class="legende_cdiv">
		<p class="legende_c"><span class="nom_legende_calendrier">Semis/Plantation</span><span id="legende_plantation" class="legende_calendrier"></span></p>
		<p class="legende_c"><span class="nom_legende_calendrier">R&eacute;colte</span><span id="legende_recolte" class="legende_calendrier"></span></p>
	</div>
	<table id="tableau_calendrier">
		<tr>
		<?php
$_smarty_tpl->tpl_vars['pos_mois'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['pos_mois']->step = 1;$_smarty_tpl->tpl_vars['pos_mois']->total = (int) ceil(($_smarty_tpl->tpl_vars['pos_mois']->step > 0 ? 12+1 - (1) : 1-(12)+1)/abs($_smarty_tpl->tpl_vars['pos_mois']->step));
if ($_smarty_tpl->tpl_vars['pos_mois']->total > 0) {
for ($_smarty_tpl->tpl_vars['pos_mois']->value = 1, $_smarty_tpl->tpl_vars['pos_mois']->iteration = 1;$_smarty_tpl->tpl_vars['pos_mois']->iteration <= $_smarty_tpl->tpl_vars['pos_mois']->total;$_smarty_tpl->tpl_vars['pos_mois']->value += $_smarty_tpl->tpl_vars['pos_mois']->step, $_smarty_tpl->tpl_vars['pos_mois']->iteration++) {
$_smarty_tpl->tpl_vars['pos_mois']->first = $_smarty_tpl->tpl_vars['pos_mois']->iteration === 1;$_smarty_tpl->tpl_vars['pos_mois']->last = $_smarty_tpl->tpl_vars['pos_mois']->iteration === $_smarty_tpl->tpl_vars['pos_mois']->total;?>
			<td class="<?php if ((isset($_smarty_tpl->tpl_vars['cal1']->value['plantation'][$_smarty_tpl->tpl_vars['pos_mois']->value]))) {?> td_plantation <?php }
if ((isset($_smarty_tpl->tpl_vars['cal1']->value['recolte'][$_smarty_tpl->tpl_vars['pos_mois']->value]))) {?>td_recolte<?php }?>" >
				<div class="contenu_calendrier"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['T_mois']->value[$_smarty_tpl->tpl_vars['pos_mois']->value], ENT_QUOTES, 'UTF-8');?>
</div>
			</td>
		<?php }
}
?>
		</tr>
	</table>
<?php }
}
}
