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

 <div class="cart-summary-products js-cart-summary-products">
  <p>{$cart.summary_string}</p>

  <p>
    <a href="#" data-toggle="collapse" data-target="#cart-summary-product-list" class="js-show-details">
      {l s='show details' d='Shop.Theme.Actions'}
      <i class="material-icons">expand_more</i>
    </a>
  </p>

  {block name='cart_summary_product_list'}
    <div class="collapse" id="cart-summary-product-list">


{$whEC = $cart.products.0.warehouse_en_cours}
{$idCatDefault = $cart.products.0.id_category_default}
{$cpt_colis = 1}
{$cpt_categorie = 1}
{$affiche_groupe = false}
{$affiche_semaine = 0}
<div id="wh{$whEC}">
{if $whEC > 0 || $whEC == -2}
    <h3 class="differee">Commande N°{$cpt_colis} - Expédition prévue semaine {$cart.products.0.exped_semaine}</h3>
{else}
    <h3 class="immediate">Commande N°{$cpt_colis} - Expédition {$cart.products.0.exped}</h3>
{/if}
{if $whEC > 0 || $whEC == -2}
    <span class="depart">Départ de notre entrepôt <span class="date_colis">{$cart.products.0.exped}</span></span>
{/if}
{$catName = Category::getCategoryInformations([$idCatDefault])}
{if isset($cart.products.0.warehouse_list_default.0) && $affiche_groupe == false}
{$affiche_groupe = true}
<h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">Produits en stock</h4>
{$affiche_semaine = 1}
{else if isset($cart.products.0.warehouse_list_default.0) && $affiche_groupe == true}
{else if !isset($cart.products.0.warehouse_list_default.0) || $cart.products.0.exped <> 'immédiate'}
<h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">{$catName.$idCatDefault.name}</h4>
{$affiche_semaine = 2}
{/if}
{$cpt_categorie = $cpt_categorie + 1}
{if $affiche_semaine <> 0}
{if $affiche_semaine == 1}
    {$valCat = 0}
{else if $affiche_semaine == 2}
    {$valCat = $idCatDefault}
{/if}

{$affiche_semaine = 0}
{/if}



      <ul class="media-list">



        {foreach from=$cart.products item=product}
          


{if $product.warehouse_en_cours <> $whEC}
    </ul>
    {$cpt_colis = $cpt_colis + 1}
    {$cpt_categorie = 1}
    {$whEC = $product.warehouse_en_cours}
    </div>
    <div id="wh{$whEC}">
    {if $whEC > 0 || $whEC == -2}
    <h3 class="differee">Commande N°{$cpt_colis} - Expédition prévue semaine {$product.exped_semaine}</h3>
    {else}
    <h3 class="immediate">Commande N°{$cpt_colis} - Expédition {$product.exped}</h3>
    {/if}
    {if $whEC > 0 || $whEC == -2}
        <span class="depart">Départ de notre entrepôt <span class="date_colis">{$product.exped}</span></span>
    {/if}
    {$idCatDefault = $product.id_category_default}
    {$catName = Category::getCategoryInformations([$idCatDefault])}
    {if isset($product.warehouse_list_default.0) && $affiche_groupe == false}
    {$affiche_groupe = true}
    <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">Produits en stock</h4>
    {$affiche_semaine = 1}
    {else if isset($product.warehouse_list_default.0) && $affiche_groupe == true}
    {else if !isset($product.warehouse_list_default.0) || $product.exped <> 'immédiate'}
    <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">{$catName.$idCatDefault.name}</h4>
    {$affiche_semaine = 2}
    {/if}
    {$cpt_categorie = $cpt_categorie + 1}
    {if $affiche_semaine <> 0}
    {if $affiche_semaine == 1}
        {$valCat = 0}
    {else if $affiche_semaine == 2}
        {$valCat = $idCatDefault}
    {/if}
    {$affiche_semaine = 0}
    {/if}
    <ul class="media-list {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} colis{$whEC}_{$idCatDefault}">
{/if}
{if $product.id_category_default <> $idCatDefault}
    </ul>
    {$idCatDefault = $product.id_category_default}
    {$catName = Category::getCategoryInformations([$idCatDefault])}
    {if isset($product.warehouse_list_default.0) && $affiche_groupe == false}
    {$affiche_groupe = true}
    <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">Produits en stock</h4>
    {$affiche_semaine = 1}
    {else if isset($product.warehouse_list_default.0) && $affiche_groupe == true}
    {elseif !isset($product.warehouse_list_default.0) || $product.exped <> 'immédiate'}
    <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">{$catName.$idCatDefault.name}</h4>   
    {$affiche_semaine = 2}
    {/if}
    {$cpt_categorie = $cpt_categorie + 1}
    {if $affiche_semaine <> 0}
    {if $affiche_semaine == 1}
        {$valCat = 0}
    {else if $affiche_semaine == 2}
        {$valCat = $idCatDefault}
    {/if}
    {$affiche_semaine = 0}
    {/if}
    <ul class="media-list {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} colis{$whEC}_{$idCatDefault}">
{/if}
        


          <li class="media">{include file='checkout/_partials/cart-summary-product-line.tpl' product=$product}</li>
        {/foreach}
      </ul>
    </div>
    </div>
  {/block}
</div>
