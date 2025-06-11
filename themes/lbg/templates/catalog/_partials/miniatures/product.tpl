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
 
{$produit_coeur = 0}
{if Product::getCoeur($product.id_product)}
	{$produit_coeur = 1}
{/if}

{$isSerres = 0}
{if isset($product.id_category_default) && ($product.id_category_default >= 273 && $product.id_category_default <=  282)}
	{$isSerres = 1}
{/if}
	
{$isNATURA = 0}
{if isset($product.id_category_default) && $product.id_category_default == 274}
	{$isNATURA = 1}
{/if}

{$product.quantity = Product::getTotalQuantity($product.id_product)}

{$isWKPACK = 0}
{if Product::isBundleProduct($product.id_product)}
	{$isWKPACK = 1}
	{$bundleDetail = Product::awGetBundleDetail($product.id_product)}
	{$product.has_discount = true}
	{$product.regular_price = $bundleDetail.displayPrice}
	{$product.discount_type = 'percentage'}
	{$product.discount_percentage = $bundleDetail.displayDiscount}
	{if !Product::awIsBundleStock($product.id_product)}
		{$product.quantity = 0}
	{/if}
{/if}

{$price_min = Product::getPriceMin($product.id_product, $product.id_product_attribute, $product.regular_price_amount)}



{block name='product_miniature_item'}
<div class="js-product product{if !empty($productClasses)} {$productClasses}{/if}">

	{$combi = Product::getDefaultCombination($product.id_product)}

	{if $combi["id_product_attribute"] > 0}
		<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$combi["id_product_attribute"]}" data-quantity="{$product.quantity}">
	{else}
		<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" data-quantity="{$product.quantity}">
	{/if}
	
	{block name='product_flags'}
        <ul class="product-flags">
          {foreach from=$product.flags item=flag}
            <li class="product-flag {$flag.type}">{$flag.label}</li>
          {/foreach}
        </ul>
	{/block}
	
    <div class="thumbnail-container">
      <div class="thumbnail-top left-block">
        <div class="product_img_link{if $produit_coeur} border_coup_de_coeur_products_list{/if}">
        {block name='product_thumbnail'}
          {if $product.cover}
            <a href="{$product.url}" class="thumbnail product-thumbnail">
              <img
                src="{$product.cover.bySize.home_default.url}"
                alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:60:'...'}{/if}"
                loading="lazy"
                data-full-size-image-url="{$product.cover.large.url}"
                width="{$product.cover.bySize.home_default.width}"
                height="{$product.cover.bySize.home_default.height}"
              />
            </a>
          {else}
            <a href="{$product.url}" class="thumbnail product-thumbnail">
              <img
                src="{$urls.no_picture_image.bySize.home_default.url}"
                loading="lazy"
                width="{$urls.no_picture_image.bySize.home_default.width}"
                height="{$urls.no_picture_image.bySize.home_default.height}"
              />
            </a>
          {/if}
        {/block}
		
		<div class="hover-desc">
			<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
				<span class="p-desc" itemprop="description">
					{$product.description_short|strip_tags|truncate:320:'...'|nl2br nofilter}
				</span>
			</a>

			<div class="button-action-block{if $isSerres} cat-serres{/if}">
				<div class="button-action">
					{if $isSerres && !$isNATURA}
						
					{else}
						<a class="button btn btn-default js-listing-add-to-cart" title="{l s='Ajouter au panier'}">
							<img class="img-off" src="/themes/lbg/assets/img/picto-panier-off.png" alt="">
							<img class="img-on" src="/themes/lbg/assets/img/picto-panier-on.png" alt="">
						</a>
					{/if}
				</div>
				<div class="button-action js-wishlist-button-add" title="{l s='Ajouter à la liste d\'envie'}" onclick="$('.img-off',this).attr('src','/themes/lbg/assets/img/picto-envies-on.png'); return false;">
					{if isset($product['is_in_wishlist'])}
						<img class="img-off" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
					{else}
						<img class="img-off" src="/themes/lbg/assets/img/picto-envies-off.png" alt="">
					{/if}
					<img class="img-on" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
				</div>
				{* COMPARE<div class="button-action add_to_compare" title="{l s='Ajouter au comparatif'}" data-id-product="{$product.id_product}">
					{if isset($product['is_in_compare'])}
						<img class="img-off" src="/themes/lbg/assets/img/picto-comparatif-on.png" alt="">
					{else}
						<img class="img-off" src="/themes/lbg/assets/img/picto-comparatif-off.png" alt="">
					{/if}
					<img class="img-on" src="/themes/lbg/assets/img/picto-comparatif-on.png" alt="">
				</div> *}
			</div>

		</div>
		
      </div>
		
		{if $produit_coeur}
			<div class="gif_coup_de_coeur_products_list"></div>
		{/if}
		
		
		
      </div>

      <div class="product-description">
        {block name='product_name'}
          {if $page.page_name == 'index'}
            <h3 class="h3 product-title"><a href="{$product.url}" content="{$product.url}">{$product.name|truncate:60:'...'}</a></h3>
          {else}
            <h2 class="h3 product-title"><a href="{$product.url}" content="{$product.url}">{$product.name|truncate:60:'...'}</a></h2>
          {/if}
        {/block}

        {block name='product_price_and_shipping'}
		 
			  {if $product.show_price}
			  	{if $price_min}
				<div class="product-price-and-shipping">
				  <span class="price">De {Tools::displayPrice($price_min)} à {$product.regular_price}</span>
				</div>
				{else}
				<div class="product-price-and-shipping">
				  {if $product.has_discount}
					
					{hook h='displayProductPriceBlock' product=$product type="old_price"}

					{if Configuration::get('MP_BADGE')}
						{assign var='mp_badge' value='background: url(/upload/'|cat:Configuration::get('MP_BADGE')|cat:');'}
						<div class="picto_badge" style="{$mp_badge}">
						</div>
					{/if}	
					
					<span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{$product.regular_price}</span>
					{if $product.discount_type === 'percentage'}
					  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
					{elseif $product.discount_type === 'amount'}
					  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
					{/if}
				  {/if}

				  {hook h='displayProductPriceBlock' product=$product type="before_price"}

				  <span class="price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
					{capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='products_list'}{/capture}
					{if '' !== $smarty.capture.custom_price}
					  {$smarty.capture.custom_price nofilter}
					{else}
						{$combi = Product::getDefaultCombination($product.id_product)}

						{if $combi["id_product_attribute"] > 0}
							{Product::getPriceStatic($combi["id_product"], true, $combi["id_product_attribute"])|string_format:"%.2f"|replace:'.':','} €
						{else}
							{$product.price}
						{/if}
					{/if}
				  </span>

				  {hook h='displayProductPriceBlock' product=$product type='unit_price'}

				</div>
		       {/if}
		     {/if}

        {/block}

	    <span class="combi_liste">
			{$combi["name"]}
		</span>
		
	
		{if $product.quantity > 0 }
		  {if $product.available_now != ""}
			  <div class="availability deg">{$product.available_now}</div>
		  {else}
			  <div class="availability deg">{l s='Produit en stock'}</div>
		  {/if}
		{else}
			{if $product.not_available_message}
				<div class="not_available deg aw-not-available-1">{$product.not_available_message}</div>
			{elseif $product.available_later}
				<div class="not_available deg aw-not-available-2">{$product.available_later}</div>
			{else}
				<div class="not_available deg aw-not-available-3">{l s='Rupture stock'}</div>
			{/if}
		{/if}
    

        <div class="mobile button-action-block{if $isSerres} cat-serres{/if}">
			<div class="button-action">
				{if $isSerres && !$isNATURA}
					
				{else}
					<a class="button ajax_add_to_cart_button btn btn-default js-listing-add-to-cart" title="{l s='Ajouter au panier'}">
						<img class="img-off" src="/themes/lbg/assets/img/picto-panier-off.png" alt="">
						<img class="img-on" src="/themes/lbg/assets/img/picto-panier-on.png" alt="">
					</a>
				{/if}
			</div>
			<div class="button-action js-wishlist-button-add" title="{l s='Ajouter à la liste d\'envie'}" onclick="$('.img-off',this).attr('src','/themes/lbg/assets/img/picto-envies-on.png'); return false;">
				{if isset($product['is_in_wishlist'])}
					<img class="img-off" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
				{else}
					<img class="img-off" src="/themes/lbg/assets/img/picto-envies-off.png" alt="">
				{/if}
				<img class="img-on" src="/themes/lbg/assets/img/picto-envies-on.png" alt="">
			</div>
			{* COMPARE<div class="button-action add_to_compare" title="{l s='Ajouter au comparatif'}" data-id-product="{$product.id_product}">
				{if isset($product['is_in_compare'])}
					<img class="img-off" src="/themes/lbg/assets/img/picto-comparatif-on.png" alt="">
				{else}
					<img class="img-off" src="/themes/lbg/assets/img/picto-comparatif-off.png" alt="">
				{/if}
				<img class="img-on" src="/themes/lbg/assets/img/picto-comparatif-on.png" alt="">
			</div>*}
		</div>
			
		<div class="liste_combis">
			{hook h='displayProductPriceBlock' product=$product type='weight'}

			{Product::getOthersCombination($product.id_product)}
			{if $price_min}
				<span class="combi_liste2">{l s='En fonction des quantités'}</span>
			{/if}
		</div>
		

		
		
		<div class="listing-produit-vente-flash">
			{hook h='displayProductFlash' product=$product}
		</div>

      </div>
    </div>
  </article>
</div>
{/block}
