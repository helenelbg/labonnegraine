<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:49:20
  from '/home/dev.labonnegraine.com/public_html/modules/awproduct/views/templates/hook/productextrafields.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49708ab836_82338166',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ae8ae8e2ba0921faf9f5d920ac1fc70f8d4cd235' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/awproduct/views/templates/hook/productextrafields.tpl',
      1 => 1738070957,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49708ab836_82338166 (Smarty_Internal_Template $_smarty_tpl) {
?><fieldset class="form-group">
	
	<div class="mb-4">
		<input id="coeur" type="checkbox" name="coeur" <?php echo $_smarty_tpl->tpl_vars['coeur']->value;?>
 />
		<label for="coeur">Coup de coeur ♥</label>
	</div>

	<div class="mb-4">
	<?php if ($_smarty_tpl->tpl_vars['type']->value == 1) {?>
		<?php $_smarty_tpl->_assignInScope('checked1', " checked");?>
		<?php $_smarty_tpl->_assignInScope('checked2', '');?>
	<?php } else { ?>
		<?php $_smarty_tpl->_assignInScope('checked1', '');?>
		<?php $_smarty_tpl->_assignInScope('checked2', " checked");?>
	<?php }?>
	<input id="type1" type="radio" name="type" value="1"<?php echo $_smarty_tpl->tpl_vars['checked1']->value;?>
 />
	<label for="type1">Jardin d'essai</label>
	&nbsp;&nbsp;&nbsp;
	<input id="type2" type="radio" name="type" value="2"<?php echo $_smarty_tpl->tpl_vars['checked2']->value;?>
 />
	<label for="type2">Influenceur</label>
	</div>

	<div class="mb-4">
		<label for="jardin_titre">Titre encart</label>
		<input id="jardin_titre" type="text" name="jardin_titre" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['jardin_titre']->value;?>
"/>
	</div>
	
	<div class="mb-4">
		<label for="jardin_contenu">Contenu encart</label>
		<textarea id="jardin_contenu" name="jardin_contenu" class="autoload_rte"><?php echo $_smarty_tpl->tpl_vars['jardin_contenu']->value;?>
</textarea>
	</div>
	
	<div class="mb-4">
		<label for="botanic_name">Nom botanique</label>
		<input id="botanic_name" type="text" name="botanic_name" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['botanic_name']->value;?>
"/>
	</div>
	

</fieldset>
<div class="clearfix"></div>


<div class="sachet-container">
	<h2>Informations sachet</h2>
    <div class="mb-4">
		<label for="sachet_titre1">Titre 1</label>
		<input id="sachet_titre1" type="text" name="sachet_titre1" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['sachet_titre1']->value;?>
"/>
	</div>
    <div class="mb-4">
		<label for="sachet_titre2">Titre 2</label>
		<input id="sachet_titre2" type="text" name="sachet_titre2" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['sachet_titre2']->value;?>
"/>
	</div>
	<div class="mb-4">
		<label for="sachet_desc_recto">Description recto</label>
		<textarea id="sachet_desc_recto" name="sachet_desc_recto" class="autoload_rte"><?php echo $_smarty_tpl->tpl_vars['sachet_desc_recto']->value;?>
</textarea>
	</div>
	<div class="mb-4">
		<label for="sachet_desc_verso">Description verso</label>
		<textarea id="sachet_desc_verso" name="sachet_desc_verso" class="autoload_rte"><?php echo $_smarty_tpl->tpl_vars['sachet_desc_verso']->value;?>
</textarea>
	</div>
    <div class="mb-4">
		<label for="sachet_normes">Normes</label>
		<input id="sachet_normes" type="text" name="sachet_normes" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['sachet_normes']->value;?>
"/>
	</div>
	<div class="mb-4">
		<input id="sachet_passphy" type="checkbox" name="sachet_passphy" <?php echo $_smarty_tpl->tpl_vars['sachet_passphy']->value;?>
 />
		<label for="sachet_passphy">Passeport Phyto</label>
	</div>
</div>
<?php }
}
