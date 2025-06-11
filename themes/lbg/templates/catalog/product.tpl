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

{block name='head' append}
  <meta property="og:type" content="product">
  {if $product.cover}
    <meta property="og:image" content="{$product.cover.large.url}">
  {/if}

  {if $product.show_price}
    <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
    <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
    <meta property="product:price:amount" content="{$product.price_amount}">
    <meta property="product:price:currency" content="{$currency.iso_code}">
  {/if}
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='head_microdata_special'}
  {include file='_partials/microdata/product-jsonld.tpl'}
{/block}

{block name='content'}

  <div id="left-column">
      <img src="/img/jardinez-authentique.png" alt="La Bonne Graine, Jardinez authentique !" class="jardinez" />
      {hook h='displayLeftColumn'}
  </div>
  <section id="center_column">
    <meta content="{$product.url}">
    {block name='page_header_container'}
      {block name='page_header'}
        <h1 class="h1">{block name='page_title'}{$product.name}{/block}</h1><button onclick="window.history.go(-1)" class="retour_page_produit"></button>
      {/block}
    {/block}
    <div class="product-container js-product-container">
	{if Product::isBundleProduct($product.id)}
		{include file='catalog/product-wkpack.tpl'}
	{else}
      <div class="Product_Partie_ G pb-center-column">
        {block name='page_content_container'}
          <section class="page-content" id="content">
            {block name='page_content'}
              {include file='catalog/_partials/product-flags.tpl'}

              {block name='product_cover_thumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}
              <div class="scroll-box-arrows">
                <i class="material-icons left">&#xE314;</i>
                <i class="material-icons right">&#xE315;</i>
              </div>

            {/block}
          </section>
		  
		  <div class="fiche-produit-vente-flash">
		    {hook h='displayProductFlash' product=$product}
		  </div>
		  
		  <div class="desktop">
		    {include file='catalog/jardin-essai.tpl'}
		  </div>
		  
			
				
        {/block}
        </div>
        <div class="Product_Partie_ R pb-right-column">


          <div class="product-information">
			
			{if $aff_cyril eq "true"}
                {assign var="isCyril" value="false"}
                {foreach from=$product_cyril item=unProduit}
					{if $unProduit.id_product eq $smarty.get.id_product}
						{assign var="isCyril" value="true"}
					{/if}
				{/foreach}

                {if $isCyril eq "true"}

					<div class="background_lightbox_cyril hidden" onclick="$(this).fadeOut();">
						<div class="lightbox_cyril" style="width: 100% !important; min-width: 400px;text-align: center">
							<div class="chat_cyril_cross">
								<div class="close"><img src="/themes/lbg/assets/img/multiply.png" alt="assistant cyril" width="30px" height="30px" 	onclick="$('.background_lightbox_cyril').fadeOut();"/>
								</div>
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
								<button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=oui';$('.background_lightbox_cyril').fadeOut();">Je veux être assisté par Cyril</button><button class="btCyril" type="button" onclick="document.cookie = 'assisteCyril=non';$('.background_lightbox_cyril').fadeOut();">Pas pour le moment</button>
							<br><br>
						</div>
					</div>

					<div class="entete_cyril">
						<div><img src="/themes/lbg/assets/img/cyril_entete_produit.png" alt="assistant"/></div>
						<div>"Je m'appelle Cyril et je peux vous aider à réussir vos cultures"<br /><a onclick="$('.background_lightbox_cyril').fadeIn();">En savoir plus</a></div>
					</div>
				{/if}
			{/if}
										
            {block name='product_description_short'}
			  {$desc_nosemi = explode("Semis",$product.description_short)}
              <div id="product-description-short-{$product.id}" class="product-description">
			    {if !empty($expedProdDesc)}
					<p class="expedTop">{$expedProdDesc nofilter}</p>
				{/if}
			  	{$desc_nosemi[0] nofilter}
			  </div>
			  
			  <div class="div_conditions_de_culture desktop">
				{block name='calendrier'}
					{include file='catalog/calendrier.tpl'}
				{/block}
			  </div>
			  
            {/block}
			
			  

            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}
	
            <div class="product-actions js-product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">

				  {if isset($plant_precommande) && $plant_precommande}
					{include file='catalog/plant-precommande.tpl'}
				  {elseif isset($equivalent_plant) && $equivalent_plant}
					{include file='catalog/equivalent-plant.tpl'}
				  {else}
				  <hr class="plant-hr">
				  <div class="row">
				    <div class="col-xs-12 col-sm-7">
						{block name='product_variants'}
						  {include file='catalog/_partials/product-variants.tpl'}
						{/block}
						</div>
						<div class="col-xs-12 col-sm-4">
						{block name='product_quantity'}
							<div class="product-quantity clearfix">
								<label for="quantity_wanted">Quantité : </label><br />
								<div class="qty">
									<input
											type="number"
											name="qty"
											id="quantity_wanted"
											inputmode="numeric"
											pattern="[0-9]*"
											{if $product.quantity_wanted}
												value="{$product.quantity_wanted}"
												min="{$product.minimal_quantity}"
											{else}
												value="1"
												min="1"
											{/if}
											class="input-group"
											aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
									>
								</div>
							</div>
						{/block}
						</div>
					  </div>

					  <div>
						{block name='product_availability'}
							<span id="product-availability" class="js-product-availability">
						{if $product.show_availability && $product.availability_message}
							{if $product.availability == 'available'}
								{*<label id="availability_label">Disponibilité : </label>*}
							{elseif $product.availability == 'last_remaining_items'}
							<i class="material-icons product-last-items">&#xE002;</i>
						{else}
							<i class="material-icons product-unavailable">&#xE14B;</i>
							{/if}


							{if $product.availability == 'last_remaining_items'}
								{$product.availability_message}
							{else}
								{if $product.quantity > 0 }
									{if $product.available_now != ""}
										{*<span id="availability_value" class="label-success">{$product.available_now}</span>*}
										<label id="availability_label">Expédition : </label><b>{l s='Immédiate'}</b>
									{else}
										{*<label id="availability_label">Disponibilité : </label>{l s='En stock'}*}
										<label id="availability_label">Expédition : </label><b>{l s='Immédiate'}</b>
									{/if}
								{else}
									{if $product.not_available_message != ""}
										{$product.not_available_message}
									{elseif $product.available_later != ""}
										{$product.available_later}
									{else}
										{l s='Rupture stock'}
									{/if}
								{/if}
							{/if}
						{/if}
					</span>
						{/block}
					  </div>
					  
					
					  {block name='product_pack'}
						{if $packItems}
						  <section class="product-pack">
							<p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
							{foreach from=$packItems item="product_pack"}
							  {block name='product_miniature'}
								{include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack showPackProductsPrice=$product.show_price}
							  {/block}
							{/foreach}
						</section>
						{/if}
					  {/block}
						<hr class="plant-hr">
							<div>
								{block name='product_prices'}
									{include file='catalog/_partials/product-prices.tpl'}
								{/block}
							</div>

					  {block name='product_discounts'}
						{include file='catalog/_partials/product-discounts.tpl'}
					  {/block}

					  {block name='product_add_to_cart'}
						{include file='catalog/_partials/product-add-to-cart.tpl'}
					  {/block}

					  {*{block name='product_additional_info'}
						{include file='catalog/_partials/product-additional-info.tpl'}
					  {/block}*}

					  {* Input to refresh product HTML removed, block kept for compatibility with themes *}
					  {block name='product_refresh'}{/block}
				  
				  {/if}
                </form>
              {/block}

            </div>

        </div>
		
	  {*<img class="logos-rea-produit" src="/img/desktop-2022/moyens-paiement-lbg.png" alt="logos-page-produit">*}
	  
	  <div class="mobile">
		{include file='catalog/jardin-essai.tpl'}
	  </div>
		  
	  {block name='hook_display_reassurance'}
		  {hook h='displayReassurance'}
	  {/block}

      </div>
	  
	  
	  
	  <div class="div_conditions_de_culture mobile">
		{block name='calendrier'}
			{include file='catalog/calendrier.tpl'}
		{/block}
	  </div>
	  
	  			

      {block name='product_tabs'}
              <div class="tabs">
                <ul class="nav nav-tabs" role="tablist">
				
                </ul>
				
				
					<span class="info_produit_title">Caractéristiques</span>
				<ul class="bullet info_produit">
					{foreach from=Product::getFrontFeaturesStatic($language.id, $product.id_product) item=feature}
						{if $feature.id_feature<21}
							{if $feature.id_feature!=19}
								<li class="feature feature_{$feature.id_feature}"><span>{$feature.name|escape:'htmlall':'UTF-8'}  : </span>{$feature.value|escape:'htmlall':'UTF-8'}</li>
							{else}
								<li class="feature_{$feature.id_feature}">
									{if $feature.value=="Godets de 7 cm"}
										<img src="/img/godets7.png">
									 {elseif $feature.value=="Godets de 8 cm"}
										<img src="/img/godets8.png">
									 {elseif $feature.value=="Godets de 9 cm"}
										<img src="/img/godets9.png">
									 {elseif $feature.value=="Pots d'un litre"}
										<img src="/img/pot.png">
									 {elseif $feature.value=="Pots de 2 litres"}
										<img src="/img/pot_2l.png">
									 {else}
										<span>{$feature.name|escape:'htmlall':'UTF-8'}  :</span>  {$feature.value|escape:'htmlall':'UTF-8'}
									{/if}
								</li>
							{/if}
						{/if}
				   {/foreach}
				</ul>
		
                <div class="tab-content" id="tab-content">
                 <div class="tab-pane fade in{if $product.description} active js-product-tab-active{/if}" id="description" role="tabpanel">
                   {block name='product_description'}
                     <div class="product-description-bottom">{$product.description nofilter}</div>
                   {/block}
                 </div>

                 {block name='product_details'}
                   {*include file='catalog/_partials/product-details.tpl'*}
                 {/block}

                 {foreach from=$product.extraContent item=extra key=extraKey}
                 <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" role="tabpanel" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
                   {$extra.content nofilter}
                 </div>
                 {/foreach}
              </div>
            </div>
          {/block}
		  
	{/if} {* fin wkpack *}
	
    </div>

	{if Product::getFichesAttachments($product.id_product)}
		{foreach from=Product::getFichesAttachments($product.id_product) item=attachment}
			<div class="attachment-download">
				<a class="js_download_fiche_pratique" href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}" title="{l s='Download'}">
					{if isset($attachment.picto) && $attachment.picto}
						<img src="/upload/{$attachment.picto}" alt="{l s='Download'} 
					{else}
						<img src="/themes/lbg/assets/img/picto-pdf.png" alt="{l s='Download'} 
					{/if}
					{$attachment.name}" title="{l s='Download'} {$attachment.name}" class="download_picto"/><br/>
					{$attachment.name|escape:'htmlall':'UTF-8'}
				</a>
				<p>{$attachment.description|escape:'htmlall':'UTF-8'}</p>
			</div>
		{/foreach}
	{/if}
		
    {*block name='product_accessories'}
		  
          {if isset($accessories) && $accessories}
			<!-- begin Accessories -->
			<section class="page-product-box blockproductscategory">
				<div class="Page_Produit_Accessoires_Titre">
					<h3 class="productscategory_h3 page-product-heading">
					{l s='Vous pourriez en avoir besoin:'}
					</h3>
				</div>
				<div class="clearfix">
					<div id="bxslider2" class="bxslider clearfix">
						{foreach from=$accessories item=accessory name=accessories_list}

							{if !isset($restricted_country_mode)}
								{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
								<div class="product-box item Page_Produit_MMCat_Case_Img">
									<a href="{$accessoryLink|escape:'html':'UTF-8'}" class="lnk_img product-image">
										<div class="category_block">
											<img data-lazy-src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$accessory.legend|escape:'html':'UTF-8'}" />
										</div>
										<h5 itemprop="name" class="product-name Page_Produit_MMCat_Nom">
											<div>{$accessory.name|escape:'html':'UTF-8'}</div>
										</h5>	
									</a>
								</div>
							{/if}
						{/foreach}
					</div>
				</div>
			</section>
			<!-- end Accessories -->
		{/if}
    {/block*}

    {block name='product_footer'}
      {hook h='displayFooterProduct' product=$product category=$category}
    {/block}

    {block name='product_images_modal'}
      {include file='catalog/_partials/product-images-modal.tpl'}
    {/block}

    {block name='page_footer_container'}
      <footer class="page-footer">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
  </section>

{/block}
