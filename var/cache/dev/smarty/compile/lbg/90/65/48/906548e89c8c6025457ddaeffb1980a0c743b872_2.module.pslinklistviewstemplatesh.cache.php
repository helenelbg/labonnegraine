<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:25:03
  from 'module:pslinklistviewstemplatesh' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304cff7f5391_74253395',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '906548e89c8c6025457ddaeffb1980a0c743b872' => 
    array (
      0 => 'module:pslinklistviewstemplatesh',
      1 => 1738070829,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304cff7f5391_74253395 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '189684032268304cff7f1389_05295040';
?>
<!-- begin /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_linklist/views/templates/hook/linkblock.tpl --><div class="col-md-6 links">
  <div class="row">
  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['linkBlocks']->value, 'linkBlock');
$_smarty_tpl->tpl_vars['linkBlock']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['linkBlock']->value) {
$_smarty_tpl->tpl_vars['linkBlock']->do_else = false;
?>
    <div id="linkBlock-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['id'], ENT_QUOTES, 'UTF-8');?>
" class="col-md-6 wrapper">
      <p class="h3 hidden-sm-down"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['title'], ENT_QUOTES, 'UTF-8');?>
</p>
      <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['id'], ENT_QUOTES, 'UTF-8');?>
" data-toggle="collapse">
        <span class="h3"><?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['title'], ENT_QUOTES, 'UTF-8');?>
</span>
        <span class="float-xs-right">
          <span class="navbar-toggler collapse-icons">
            <i class="material-icons add">&#xE313;</i>
            <i class="material-icons remove">&#xE316;</i>
          </span>
        </span>
      </div>
      <?php if ($_smarty_tpl->tpl_vars['linkBlock']->value['title'] === 'La bonne graine') {?>
        <ul class="block_various_links_lbg">
          <span>Site Français</span>
          <li><a href="/#first-part">Qui sommes-nous ?</a></li>
          <li><a href="/#engagements">Nos engagements</a></li>
        </ul>
      <?php }?>
      <?php if ($_smarty_tpl->tpl_vars['linkBlock']->value['title'] === 'Services') {?>
        <ul class="block_various_links_lbg">
          <li class="first_item"><a href="/contactez-nous" title="">Contactez-nous</a></li>
          <li class="first_item"><a href="/contactez-nous" title="">Foire aux questions</a></li>
        </ul>
      <?php }?>
      <ul id="footer_sub_menu_<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['id'], ENT_QUOTES, 'UTF-8');?>
" class="collapse">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['linkBlock']->value['links'], 'link');
$_smarty_tpl->tpl_vars['link']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['link']->value) {
$_smarty_tpl->tpl_vars['link']->do_else = false;
?>
          <li id="item-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['id'], ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['id'], ENT_QUOTES, 'UTF-8');?>
" class="item">
            <a
                id="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['id'], ENT_QUOTES, 'UTF-8');?>
-<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['linkBlock']->value['id'], ENT_QUOTES, 'UTF-8');?>
"
                class="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['class'], ENT_QUOTES, 'UTF-8');?>
"
                href="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['url'], ENT_QUOTES, 'UTF-8');?>
"
                title="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['description'], ENT_QUOTES, 'UTF-8');?>
"
                <?php if (!empty($_smarty_tpl->tpl_vars['link']->value['target'])) {?> target="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['target'], ENT_QUOTES, 'UTF-8');?>
" <?php }?>
            >
              <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['link']->value['title'], ENT_QUOTES, 'UTF-8');?>

            </a>
          </li>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      </ul>
      <?php if ($_smarty_tpl->tpl_vars['linkBlock']->value['title'] === 'La bonne graine') {?>
        <div class="social_media_block footer-smb">
          <span class="follow">Suivez-nous :</span>
          <ul class="reseaux_newsletter">
            <li>
              <a class="_blank" href="https://fr-fr.facebook.com/LaBonneGraine49/" target="_blank" lambdaeverupdated="1">
                <img data-lazy-src="/themes/lbg/assets/img/fb.png" src="/themes/lbg/assets/img/fb.png">
              </a>
            </li>
            <li>
              <a class="_blank" href="https://www.pinterest.fr/labonnegraine/" target="_blank" lambdaeverupdated="1">
                <img data-lazy-src="/themes/lbg/assets/img/pinterest.png" src="/themes/lbg/assets/img/pinterest.png">
              </a>
            </li>
            <li>
              <a class="_blank" href="https://www.instagram.com/labonnegraine_/" target="_blank" lambdaeverupdated="1">
                <img data-lazy-src="/themes/lbg/assets/img/insta.png" src="/themes/lbg/assets/img/insta.png">
              </a>
            </li>
          </ul>
        </div>
      <?php }?>
    </div>
  <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
  </div>
</div>
<!-- end /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_linklist/views/templates/hook/linkblock.tpl --><?php }
}
