<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d0561747_39825074',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '36224db8a2b347dffedf7de6b01c39308fe25e6e' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/themes/lbg/templates/index.tpl',
      1 => 1738070828,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d0561747_39825074 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_786943028683d49d055ff28_98986623', 'page_content_container');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, 'page.tpl');
}
/* {block 'page_content_top'} */
class Block_1177533333683d49d0560128_76439345 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'page_content_top'} */
/* {block 'hook_home'} */
class Block_1614690613683d49d0560ba2_90509575 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <?php echo $_smarty_tpl->tpl_vars['HOOK_HOME']->value;?>

          <?php
}
}
/* {/block 'hook_home'} */
/* {block 'page_content'} */
class Block_599860833683d49d05609e0_51286277 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1614690613683d49d0560ba2_90509575', 'hook_home', $this->tplIndex);
?>

        <?php
}
}
/* {/block 'page_content'} */
/* {block 'page_content_container'} */
class Block_786943028683d49d055ff28_98986623 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'page_content_container' => 
  array (
    0 => 'Block_786943028683d49d055ff28_98986623',
  ),
  'page_content_top' => 
  array (
    0 => 'Block_1177533333683d49d0560128_76439345',
  ),
  'page_content' => 
  array (
    0 => 'Block_599860833683d49d05609e0_51286277',
  ),
  'hook_home' => 
  array (
    0 => 'Block_1614690613683d49d0560ba2_90509575',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


      <section id="content" class="page-home">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1177533333683d49d0560128_76439345', 'page_content_top', $this->tplIndex);
?>

        <div class="header-homepage">
          <?php echo Tools::getHomeCategories();?>

          <?php echo Tools::getCategoriesEnAvant();?>

        </div>
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_599860833683d49d05609e0_51286277', 'page_content', $this->tplIndex);
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
