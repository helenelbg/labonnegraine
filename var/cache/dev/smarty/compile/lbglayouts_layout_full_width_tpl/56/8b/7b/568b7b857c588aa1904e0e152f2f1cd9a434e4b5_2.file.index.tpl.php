<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:29:14
  from '/home/helene/prestashop/themes/lbg/templates/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35ba678b39_58980424',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '568b7b857c588aa1904e0e152f2f1cd9a434e4b5' => 
    array (
      0 => '/home/helene/prestashop/themes/lbg/templates/index.tpl',
      1 => 1749808841,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35ba678b39_58980424 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1383787628684c35ba6765f6_02997239', 'page_content_container');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, 'page.tpl');
}
/* {block 'page_content_top'} */
class Block_1578512071684c35ba6768a0_31864316 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'page_content_top'} */
/* {block 'hook_home'} */
class Block_957934477684c35ba677d63_39747053 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <?php echo $_smarty_tpl->tpl_vars['HOOK_HOME']->value;?>

          <?php
}
}
/* {/block 'hook_home'} */
/* {block 'page_content'} */
class Block_398583165684c35ba6777c5_26289116 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_957934477684c35ba677d63_39747053', 'hook_home', $this->tplIndex);
?>

        <?php
}
}
/* {/block 'page_content'} */
/* {block 'page_content_container'} */
class Block_1383787628684c35ba6765f6_02997239 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'page_content_container' => 
  array (
    0 => 'Block_1383787628684c35ba6765f6_02997239',
  ),
  'page_content_top' => 
  array (
    0 => 'Block_1578512071684c35ba6768a0_31864316',
  ),
  'page_content' => 
  array (
    0 => 'Block_398583165684c35ba6777c5_26289116',
  ),
  'hook_home' => 
  array (
    0 => 'Block_957934477684c35ba677d63_39747053',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


      <section id="content" class="page-home">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1578512071684c35ba6768a0_31864316', 'page_content_top', $this->tplIndex);
?>

        <div class="header-homepage">
          <?php echo Tools::getHomeCategories();?>

          <?php echo Tools::getCategoriesEnAvant();?>

        </div>
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_398583165684c35ba6777c5_26289116', 'page_content', $this->tplIndex);
?>

      </section>
	  <div class="clear-both"></div>
	  <div class="footer-homepage">
		<?php echo Tools::getJardinHome();?>

	  </div>
    <?php
}
}
/* {/block 'page_content_container'} */
}
