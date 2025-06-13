<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:13
  from '/home/helene/prestashop/themes/classic/templates/catalog/_partials/category-footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35b9a847e7_89305101',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ccb338f79c22f10abc574a9c155f8cc69548f6c6' => 
    array (
      0 => '/home/helene/prestashop/themes/classic/templates/catalog/_partials/category-footer.tpl',
      1 => 1749808845,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35b9a847e7_89305101 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="js-product-list-footer">
    <?php if ((isset($_smarty_tpl->tpl_vars['category']->value)) && $_smarty_tpl->tpl_vars['category']->value['additional_description'] && $_smarty_tpl->tpl_vars['listing']->value['pagination']['items_shown_from'] == 1) {?>
        <div class="card">
            <div class="card-block category-additional-description">
                <?php echo $_smarty_tpl->tpl_vars['category']->value['additional_description'];?>

            </div>
        </div>
    <?php }?>
</div>
<?php }
}
