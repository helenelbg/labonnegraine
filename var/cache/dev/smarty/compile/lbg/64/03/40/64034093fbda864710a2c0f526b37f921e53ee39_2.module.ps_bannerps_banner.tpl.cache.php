<?php
/* Smarty version 4.5.5, created on 2025-06-13 16:27:34
  from 'module:ps_bannerps_banner.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.5',
  'unifunc' => 'content_684c35566ebe84_63884715',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '64034093fbda864710a2c0f526b37f921e53ee39' => 
    array (
      0 => 'module:ps_bannerps_banner.tpl',
      1 => 1749808843,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_684c35566ebe84_63884715 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '1801692002684c35566ea8c1_18270711';
?>
<!-- begin /home/helene/prestashop/themes/lbg/modules/ps_banner/ps_banner.tpl -->
<section id="banner">
  <div class="col-lg-6">
    <?php if ((isset($_smarty_tpl->tpl_vars['banner_img']->value))) {?>
    <div id="quelles_graines" data-lazy-background-image="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['banner_img']->value), ENT_QUOTES, 'UTF-8');?>
" style="background-image: <?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['banner_img']->value), ENT_QUOTES, 'UTF-8');?>
;">
      <a class="banner" href="/5-les-semis-du-mois?q=Date+de+semis%5C/plantation-<?php echo htmlspecialchars((string) (Tools::getMoisFrAccent()), ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['banner_desc']->value), ENT_QUOTES, 'UTF-8');?>
">

          <span><?php echo htmlspecialchars((string) ($_smarty_tpl->tpl_vars['banner_desc']->value), ENT_QUOTES, 'UTF-8');?>
</span>

      </a>
    </div>
    <?php }?>
  </div>

  <div class="col-lg-6">
      <div id="frais_port" data-lazy-background-image="/themes/lbg/assets/img/frais_port_2023.jpg" style="background-image: url(/themes/lbg/assets/img/frais_port_2023.jpg);">
          <div id="texte_graine">
          </div>
      </div>
  </div>
</section>
<!-- end /home/helene/prestashop/themes/lbg/modules/ps_banner/ps_banner.tpl --><?php }
}
