<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from 'parent:errorsnotfound.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff7d6338_08345630',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2005fcf17c08b4306c3b82a825306bfcc6b811a0' => 
    array (
      0 => 'parent:errorsnotfound.tpl',
      1 => 1738070829,
      2 => 'parent',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff7d6338_08345630 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<!-- begin /home/dev.labonnegraine.com/public_html/themes/classic/templates/errors/not-found.tpl --><section id="content" class="page-content page-not-found">
  <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_43143638268304cff7d4794_94236296', 'page_content');
?>

</section>
<!-- end /home/dev.labonnegraine.com/public_html/themes/classic/templates/errors/not-found.tpl --><?php }
/* {block "error_content"} */
class Block_26895542268304cff7d4a87_19717438 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php if ((isset($_smarty_tpl->tpl_vars['errorContent']->value))) {?>
        <?php echo $_smarty_tpl->tpl_vars['errorContent']->value;?>

      <?php } else { ?>
        <h4><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'This page could not be found','d'=>'Shop.Theme.Global'),$_smarty_tpl ) );?>
</h4>
        <p><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Try to search our catalog, you may find what you are looking for!','d'=>'Shop.Theme.Global'),$_smarty_tpl ) );?>
</p>
      <?php }?>
    <?php
}
}
/* {/block "error_content"} */
/* {block 'search'} */
class Block_170496455568304cff7d5888_81079375 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displaySearch'),$_smarty_tpl ) );?>

    <?php
}
}
/* {/block 'search'} */
/* {block 'hook_not_found'} */
class Block_104844569568304cff7d5d71_50267222 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayNotFound'),$_smarty_tpl ) );?>

    <?php
}
}
/* {/block 'hook_not_found'} */
/* {block 'page_content'} */
class Block_43143638268304cff7d4794_94236296 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'page_content' => 
  array (
    0 => 'Block_43143638268304cff7d4794_94236296',
  ),
  'error_content' => 
  array (
    0 => 'Block_26895542268304cff7d4a87_19717438',
  ),
  'search' => 
  array (
    0 => 'Block_170496455568304cff7d5888_81079375',
  ),
  'hook_not_found' => 
  array (
    0 => 'Block_104844569568304cff7d5d71_50267222',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_26895542268304cff7d4a87_19717438', "error_content", $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_170496455568304cff7d5888_81079375', 'search', $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_104844569568304cff7d5d71_50267222', 'hook_not_found', $this->tplIndex);
?>

  <?php
}
}
/* {/block 'page_content'} */
}
