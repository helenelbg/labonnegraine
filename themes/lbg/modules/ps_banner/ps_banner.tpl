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

<section id="banner">
  <div class="col-lg-6">
    {if isset($banner_img)}
    <div id="quelles_graines" data-lazy-background-image="{$banner_img}" style="background-image: {$banner_img};">
      <a class="banner" href="/5-les-semis-du-mois?q=Date+de+semis%5C/plantation-{Tools::getMoisFrAccent()}" title="{$banner_desc}">

          <span>{$banner_desc}</span>

      </a>
    </div>
    {/if}
  </div>

  <div class="col-lg-6">
      <div id="frais_port" data-lazy-background-image="/themes/lbg/assets/img/frais_port_2023.jpg" style="background-image: url(/themes/lbg/assets/img/frais_port_2023.jpg);">
          <div id="texte_graine">
          </div>
      </div>
  </div>
</section>
