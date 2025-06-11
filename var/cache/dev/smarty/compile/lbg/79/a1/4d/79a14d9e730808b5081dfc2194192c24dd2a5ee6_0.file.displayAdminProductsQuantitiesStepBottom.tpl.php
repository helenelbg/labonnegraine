<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:49:20
  from '/home/dev.labonnegraine.com/public_html/modules/aw_addfield/views/templates/hook/admin/displayAdminProductsQuantitiesStepBottom.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49709c6687_15642383',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '79a14d9e730808b5081dfc2194192c24dd2a5ee6' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/aw_addfield/views/templates/hook/admin/displayAdminProductsQuantitiesStepBottom.tpl',
      1 => 1738071002,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49709c6687_15642383 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="col-md-4">
    <label class="form-control-label">Libellé si non disponible</label>
    <div class="translations tabbable" id="form_step3_not_available_message">
        <div class="translationsFields tab-content">
            <div data-locale="fr" class="translationsFields-form_step3_not_available_message_1 tab-pane translation-field show active translation-label-fr">
                <input type="text" name="not_available_message" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['not_available_message']->value;?>
">
            </div>
        </div>
    </div>
</div><?php }
}
