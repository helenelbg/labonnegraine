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
<div class="col-md-6 links">
  <div class="row">
  {foreach $linkBlocks as $linkBlock}
    <div id="linkBlock-{$linkBlock.id}" class="col-md-6 wrapper">
      <p class="h3 hidden-sm-down">{$linkBlock.title}</p>
      <div class="title clearfix hidden-md-up" data-target="#footer_sub_menu_{$linkBlock.id}" data-toggle="collapse">
        <span class="h3">{$linkBlock.title}</span>
        <span class="float-xs-right">
          <span class="navbar-toggler collapse-icons">
            <i class="material-icons add">&#xE313;</i>
            <i class="material-icons remove">&#xE316;</i>
          </span>
        </span>
      </div>
      {if $linkBlock.title === 'La bonne graine'}
        <ul class="block_various_links_lbg">
          <span>Site Français</span>
          <li><a href="/#first-part">Qui sommes-nous ?</a></li>
          <li><a href="/#engagements">Nos engagements</a></li>
        </ul>
      {/if}
      {if $linkBlock.title === 'Services'}
        <ul class="block_various_links_lbg">
          <li class="first_item"><a href="/contactez-nous" title="">Contactez-nous</a></li>
          <li class="first_item"><a href="/contactez-nous" title="">Foire aux questions</a></li>
        </ul>
      {/if}
      <ul id="footer_sub_menu_{$linkBlock.id}" class="collapse">
        {foreach $linkBlock.links as $link}
          <li id="item-{$link.id}-{$linkBlock.id}" class="item">
            <a
                id="{$link.id}-{$linkBlock.id}"
                class="{$link.class}"
                href="{$link.url}"
                title="{$link.description}"
                {if !empty($link.target)} target="{$link.target}" {/if}
            >
              {$link.title}
            </a>
          </li>
        {/foreach}
      </ul>
      {if $linkBlock.title === 'La bonne graine'}
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
      {/if}
    </div>
  {/foreach}
  </div>
</div>
