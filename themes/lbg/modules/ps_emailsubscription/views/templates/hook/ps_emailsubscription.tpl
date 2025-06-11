{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

<div id="google-review-desktop">
  {literal}
    <!-- DÉBUT du code d'affichage du badge Google Avis clients -->

    <span id="google-avis-client-bloc"><span id="google-avis-client" style="border: 1px none rgb(245, 245, 245); text-indent: 0px; margin: 0px; padding: 0px; background: transparent; float: none; line-height: normal; font-size: 1px; vertical-align: baseline; display: inline-block; width: 165px; height: 54px;"><iframe ng-non-bindable="" hspace="0" marginheight="0" marginwidth="0" scrolling="no" style="position: static; top: 0px; width: 165px; margin: 0px; border-style: none; display: block; left: 0px; visibility: visible; height: 54px;" tabindex="0" vspace="0" id="I0_1671093214245" name="I0_1671093214245" src="https://www.google.com/shopping/customerreviews/badge?usegapi=1&amp;merchant_id=8265898&amp;position=INLINE&amp;origin=https%3A%2F%2Fdev.labonnegraine.com&amp;gsrc=3p&amp;jsh=m%3B%2F_%2Fscs%2Fabc-static%2F_%2Fjs%2Fk%3Dgapi.lb.fr.xFYH_S4Arb0.O%2Fd%3D1%2Frs%3DAHpOoo-GHFDQGtQ3VH9EXG2N8TRCzcabQw%2Fm%3D__features__#_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart%2Concircled%2Cdrefresh%2Cerefresh&amp;id=I0_1671093214245&amp;_gfid=I0_1671093214245&amp;parent=https%3A%2F%2Fdev.labonnegraine.com&amp;pfname=&amp;rpctoken=30809071" data-gapiattached="true" title="Google Avis clients" width="100%" frameborder="0"></iframe></span></span>

    <a class="avis_google_link" href="https://customerreviews.google.com/v/merchant?q=labonnegraine.com&c=FR&v=19&hl=fr" target="_blank">Lire les avis ></a>

    <script src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer></script>

    <script>
      //window.renderBadge = function() {
      var ratingBadgeContainer = document.getElementById("google-avis-client");

      window.gapi.load('ratingbadge', function() {
        window.gapi.ratingbadge.render(ratingBadgeContainer, {"merchant_id": 8265898, "position": "INLINE"});
      });
      //}
    </script>

    <!-- FIN du code d'affichage du badge Google Avis clients -->
    {/literal}
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
  <div class="block_newsletter col-lg-8 col-md-12 col-sm-12" id="blockEmailSubscription_{$hookName}">
    <div class="row">
      <p id="block-newsletter-label">{l s='Get our latest news and special sales' d='Shop.Theme.Global'}</p>
      <div class="div-content">
        <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
          <div class="row">
            <div class="col-xs-12">
              <input
                class="btn btn-primary float-xs-right hidden-xs-down"
                name="submitNewsletter"
                type="submit"
                value="{l s='Subscribe' d='Shop.Theme.Actions'}"
              >
              <input
                class="btn btn-primary float-xs-right hidden-sm-up"
                name="submitNewsletter"
                type="submit"
                value="{l s='OK' d='Shop.Theme.Actions'}"
              >
              <div class="input-wrapper">
                <input
                  name="email"
                  type="email"
                  value="{$value}"
                  placeholder="{l s='Your email address' d='Shop.Forms.Labels'}"
                  aria-labelledby="block-newsletter-label"
                  required
                >
              </div>
              <input type="hidden" name="blockHookName" value="{$hookName}" />
              <input type="hidden" name="action" value="0">
              <div class="clearfix"></div>
            </div>
            <div class="col-xs-12">
                {*{if $conditions}
                  <p>{$conditions}</p>
                {/if}*}
                {if $msg}
                  <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
                    {$msg}
                  </p>
                {/if}
                <div class="rgpd">
                    <input type="checkbox" name="rgpd" required>
                    <label for="rgpd">{l s="En cochant cette case et en soumettant ce formulaire, j'accepte que mon adresse mail soit utilisée pour me recontacter dans le cadre de l'inscription à la newsletter. Aucun autre traitement ne sera effectué avec celle-ci*" d='Shop.Theme.Actions'}</label>
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
