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
{extends file=$layout}

{block name='head_microdata_special'}
  {include file='_partials/microdata/product-list-jsonld.tpl' listing=$listing}
{/block}

{block name='content'}
  <section id="main">

    {block name='product_list_header'}
      <h1 id="js-product-list-header" class="h2">
		<span class="cat-name">{$listing.label}</span>
		{if isset($category)}
		  <span class="heading-counter">
			 - <span class="heading-counter-products">{Product::countInCategory($category.id)} produits.</span>
		  </span>
		{/if}
	  </h1>
    {/block}

    {block name='subcategory_list'}
      {if isset($subcategories) && $subcategories|@count > 0}
        {include file='catalog/_partials/subcategories.tpl' subcategories=$subcategories}
      {/if}
    {/block}
    
	{if isset($category)}
	  <div class="block-category-inner">
        <div id="category-description" class="text-muted">{$category.description nofilter}</div>
      </div>
	{/if}
      
    {hook h="displayHeaderCategory" category=$category}
    

    <section id="products">
      {if $listing.products|count}

        {block name='product_list_top'}
          {include file='catalog/_partials/products-top.tpl' listing=$listing}
        {/block}

        {block name='product_list_active_filters'}
          <div>
            {$listing.rendered_active_filters nofilter}
          </div>
        {/block}

        {block name='product_list'}
          {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xs-12 col-sm-6 col-xl-3"}
        {/block}

        {block name='product_list_bottom'}
          {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
        {/block}

      {else}
        <div id="js-product-list-top"></div>

        <div id="js-product-list">
          {capture assign="errorContent"}
            <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>
            <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>
          {/capture}

          {include file='errors/not-found.tpl' errorContent=$errorContent}
        </div>

        <div id="js-product-list-bottom"></div>
      {/if}
    </section>
	
	{if $aff_cyril eq "true"}

		<div class="chat_cyril">
			<div class="chat_cyril_cross">
				<div class="close">
					<img onclick="$('.chat_cyril').css('transition', 'bottom .5s linear'); $('.chat_cyril').css('bottom', '-300px'); document.cookie = 'assistantClose=1; expires='+new Date(new Date().setDate(new Date().getDate() + 7))" src="/themes/lbg/assets/img/multiply.png" alt="assistant cyril" width="30px" height="30px"/>
				</div>
			</div>
			<a onclick="$('.background_lightbox_cyril').fadeIn();" class="fancybox">Souhaitez vous être accompagné<br />par notre assistant Cyril ?</a>
		</div>
		<div class="background_lightbox_cyril" onclick="$(this).fadeOut();">
			<div class="lightbox_cyril">
				<div class="chat_cyril_cross">
					<div class="close"><img src="/themes/lbg/assets/img/multiply.png" alt="assistant cyril" width="30px" height="30px" 	onclick="$('.background_lightbox_cyril').fadeOut();"/></div>
				</div>
				<h2>Bonjour ami jardinier,</h2>
				<p>
					<img class="floatright" src="/themes/lbg/assets/img/cyril_entier.png">

					Je me présente, je m'appelle Cyril et je suis l'assistant jardinier de La Bonne Graine.<br />
					Vous débutez au jardin et vous avez peur de faire des erreurs ? <br />Je suis là pour vous !<br /><br>
					<span>Je vais vous guider dans la culture de vos légumes</span>. Comment ?<br />
					En vous envoyant des e-mails avec de nombreux conseils pour chaque grande étape.<br />
					Vous apprendrez ainsi les bons gestes et les actions nécessaires pour obtenir des légumes sains et savoureux.<br />
					De la préparation du terrain à la récolte, vous recevrez sur votre boîte les informations pour l'étape du moment. Une fois que vous avez réalisé les actions indiquées, vous validez et vous recevrez, en temps voulu, les informations pour l'étape suivante. Vous saurez tout sur le binage, buttage, arrosage, etc., ainsi que les besoins spécifiques du légume pour lequel vous êtes assisté.<br />
					On vous assiste en vous suivant pas-à-pas.<br>C'est rassurant, n'est ce pas ?<br />
				</p>
				<button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=oui';$('.background_lightbox_cyril').fadeOut();">Je veux être assisté par Cyril</button>
				<button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=non';$('.background_lightbox_cyril').fadeOut();">Pas pour le moment</button>
				<br><br>
			</div>
		</div>

	{/if}


    {block name='product_list_footer'}{/block}

    {hook h="displayFooterCategory"}

  </section>
{/block}
