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
<div class="container">
  <div class="row">
    {block name='hook_footer_before'}
      {hook h='displayFooterBefore'}
    {/block}
  </div>
</div>
<div class="footer-container">
  <div class="container">
    <div class="f-row">
      <div class="bloc_question">
        <h2>Besoin d'aide ?</h2>
        <h3>On est l&agrave; pour vous !</h3>
        <br>
        <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}" title="">
          <b><span class="bouton_contact">Contactez-nous</span></b>
        </a>
      </div>
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
    </div>
    <div class="d-row">
      <div class="col-md-12">
        <p class="text-sm-center">
          {block name='copyright_link'}
            <ul id="block_various_links_footer" class="block_various_links">
              {block name='hook_footer_after'}
                {hook h='displayFooterAfter'}
              {/block}
              <li class="item"><a href="/content/5paiement-securise">Paiement 100% sécurisé</a></li>
              <li class="item"><a href="/content/3conditions-generales-de-ventes#cgv13">Données Personnelles </a></li>
              <li class="last_item">Site 100% Français<img class="France" src="/themes/lbg/assets/img/france.png" alt="France" ></li>
            </ul>
          {/block}
        </p>
      </div>
    </div>
  </div>
</div>
