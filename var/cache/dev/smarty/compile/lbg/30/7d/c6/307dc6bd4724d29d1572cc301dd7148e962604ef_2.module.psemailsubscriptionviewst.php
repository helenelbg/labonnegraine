<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from 'module:psemailsubscriptionviewst' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d04db8c9_87045688',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '307dc6bd4724d29d1572cc301dd7148e962604ef' => 
    array (
      0 => 'module:psemailsubscriptionviewst',
      1 => 1738070828,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d04db8c9_87045688 (Smarty_Internal_Template $_smarty_tpl) {
?><!-- begin /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_emailsubscription/views/templates/hook/ps_emailsubscription.tpl -->
<div id="google-review-desktop">
  
    <!-- DÉBUT du code d'affichage du badge Google Avis clients -->

    <span id="google-avis-client-bloc"><span id="google-avis-client" style="border: 1px none rgb(245, 245, 245); text-indent: 0px; margin: 0px; padding: 0px; background: transparent; float: none; line-height: normal; font-size: 1px; vertical-align: baseline; display: inline-block; width: 165px; height: 54px;"><iframe ng-non-bindable="" hspace="0" marginheight="0" marginwidth="0" scrolling="no" style="position: static; top: 0px; width: 165px; margin: 0px; border-style: none; display: block; left: 0px; visibility: visible; height: 54px;" tabindex="0" vspace="0" id="I0_1671093214245" name="I0_1671093214245" src="https://www.google.com/shopping/customerreviews/badge?usegapi=1&amp;merchant_id=8265898&amp;position=INLINE&amp;origin=https%3A%2F%2Fdev.labonnegraine.com&amp;gsrc=3p&amp;jsh=m%3B%2F_%2Fscs%2Fabc-static%2F_%2Fjs%2Fk%3Dgapi.lb.fr.xFYH_S4Arb0.O%2Fd%3D1%2Frs%3DAHpOoo-GHFDQGtQ3VH9EXG2N8TRCzcabQw%2Fm%3D__features__#_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart%2Concircled%2Cdrefresh%2Cerefresh&amp;id=I0_1671093214245&amp;_gfid=I0_1671093214245&amp;parent=https%3A%2F%2Fdev.labonnegraine.com&amp;pfname=&amp;rpctoken=30809071" data-gapiattached="true" title="Google Avis clients" width="100%" frameborder="0"></iframe></span></span>

    <a class="avis_google_link" href="https://customerreviews.google.com/v/merchant?q=labonnegraine.com&c=FR&v=19&hl=fr" target="_blank">Lire les avis ></a>

    <?php echo '<script'; ?>
 src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer><?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
>
      //window.renderBadge = function() {
      var ratingBadgeContainer = document.getElementById("google-avis-client");

      window.gapi.load('ratingbadge', function() {
        window.gapi.ratingbadge.render(ratingBadgeContainer, {"merchant_id": 8265898, "position": "INLINE"});
      });
      //}
    <?php echo '</script'; ?>
>

    <!-- FIN du code d'affichage du badge Google Avis clients -->
    
</div>
<div id="three-paves">
  <div class="pave_box" data-lazy-background-image="/themes/lbg/assets/img/fond_ardoise.jpg">
    <a href="/227-les-box-de-la-bonne-graine?n=100"><img data-lazy-src="/themes/lbg/assets/img/box.png"></a>
  </div>
  <div class="cheque_cadeau" data-lazy-background-image="/themes/lbg/assets/img/carte-cadeau-labonnegraine.jpg">
    <a href="/cartes-cadeaux">
      <h2>Faites-lui plaisir,<br />offrez une carte cadeau !!</h2>
    </a>
  </div>
  <div class="block_newsletter col-lg-8 col-md-12 col-sm-12" id="blockEmailSubscription_<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['hookName']->value, ENT_QUOTES, 'UTF-8');?>
">
    <div class="row">
      <p id="block-newsletter-label"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Get our latest news and special sales','d'=>'Shop.Theme.Global'),$_smarty_tpl ) );?>
</p>
      <div class="div-content">
        <form action="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['urls']->value['current_url'], ENT_QUOTES, 'UTF-8');?>
#blockEmailSubscription_<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['hookName']->value, ENT_QUOTES, 'UTF-8');?>
" method="post">
          <div class="row">
            <div class="col-xs-12">
              <input
                class="btn btn-primary float-xs-right hidden-xs-down"
                name="submitNewsletter"
                type="submit"
                value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Subscribe','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>
"
              >
              <input
                class="btn btn-primary float-xs-right hidden-sm-up"
                name="submitNewsletter"
                type="submit"
                value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'OK','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>
"
              >
              <div class="input-wrapper">
                <input
                  name="email"
                  type="email"
                  value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['value']->value, ENT_QUOTES, 'UTF-8');?>
"
                  placeholder="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Your email address','d'=>'Shop.Forms.Labels'),$_smarty_tpl ) );?>
"
                  aria-labelledby="block-newsletter-label"
                  required
                >
              </div>
              <input type="hidden" name="blockHookName" value="<?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['hookName']->value, ENT_QUOTES, 'UTF-8');?>
" />
              <input type="hidden" name="action" value="0">
              <div class="clearfix"></div>
            </div>
            <div class="col-xs-12">
                                <?php if ($_smarty_tpl->tpl_vars['msg']->value) {?>
                  <p class="alert <?php if ($_smarty_tpl->tpl_vars['nw_error']->value) {?>alert-danger<?php } else { ?>alert-success<?php }?>">
                    <?php echo htmlspecialchars((string) $_smarty_tpl->tpl_vars['msg']->value, ENT_QUOTES, 'UTF-8');?>

                  </p>
                <?php }?>
                <div class="rgpd">
                    <input type="checkbox" name="rgpd" required>
                    <label for="rgpd"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>"En cochant cette case et en soumettant ce formulaire, j'accepte que mon adresse mail soit utilisée pour me recontacter dans le cadre de l'inscription à la newsletter. Aucun autre traitement ne sera effectué avec celle-ci*",'d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>
</label>
                </div>
            </div>
          </div>
        </form>
        <div class="social_media_block desktop">
      <h4>Suivez-nous</h4>
      <ul class="reseaux_newsletter">
        <li>
          <a class="_blank" href="https://fr-fr.facebook.com/LaBonneGraine49/" target="_blank" lambdaeverupdated="1">
            <img data-lazy-src="/themes/lbg/assets/img/fb.png">
          </a>
        </li>
        <li>
          <a class="_blank" href="https://www.pinterest.fr/labonnegraine/" target="_blank" lambdaeverupdated="1">
            <img data-lazy-src="/themes/lbg/assets/img/pinterest.png">
          </a>
        </li>
        <li>
          <a class="_blank" href="https://www.instagram.com/labonnegraine_/" target="_blank" lambdaeverupdated="1">
            <img data-lazy-src="/themes/lbg/assets/img/insta.png">
          </a>
        </li>
      </ul>
    </div>
      </div>
    </div>
  </div>
</div>
<!-- end /home/dev.labonnegraine.com/public_html/themes/lbg/modules/ps_emailsubscription/views/templates/hook/ps_emailsubscription.tpl --><?php }
}
