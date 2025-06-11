<div class="product-description-wkpack">
{block name='product_description_short'}
  {$desc_nosemi = explode("Semis",$product.description_short)}
  <div id="product-description-short-{$product.id}" class="product-description">{$desc_nosemi[0] nofilter}</div>

{/block}
</div>
			
<div class="Product_Partie_ G pb-center-column">
	<div class="images-container js-images-container">
  {block name='product_cover'}
    <div class="product-cover">
      {if $product.default_image}
        <img
          class="js-qv-product-cover img-fluid"
          src="{$product.default_image.bySize.large_default.url}"
          {if !empty($product.default_image.legend)}
            alt="{$product.default_image.legend}"
            title="{$product.default_image.legend}"
          {else}
            alt="{$product.name}"
          {/if}
          loading="lazy"
          width="{$product.default_image.bySize.large_default.width}"
          height="{$product.default_image.bySize.large_default.height}"
        >
        <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal">
          <i class="material-icons zoom-in">search</i>
        </div>
      {else}
        <img
          class="img-fluid"
          src="{$urls.no_picture_image.bySize.medium_default.url}"
          loading="lazy"
          width="{$urls.no_picture_image.bySize.medium_default.width}"
          height="{$urls.no_picture_image.bySize.medium_default.height}"
        >
      {/if}
    </div>
  {/block}

	</div>
</div>
<div class="Product_Partie_ R pb-right-column">
	<div class="product-information">
		<div class="compo-pack-1">COMPOSITION DU PACK :</div>
			
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


				
			<div class="product-wkpack">
				{block name='bundle_page_content_container'}
									
					{if isset($bundleDetail.sections) && $bundleDetail.sections}
						{if $theme_type == 'list'}
							{foreach from=$bundleDetail.sections item=$section key=key}
								{block name='bundle-slider'}
									{include file='module:wkbundleproduct/views/templates/hook/_partials/bundle_slider_view.tpl' sections=$section idpsproduct=$bundleDetail.id_ps_product themeType=$theme_type}
								{/block}
							{/foreach}
						{elseif $theme_type == 'grid'}
							<div class="card card-block">
								{foreach from=$bundleDetail.sections item=$section key=key}
									  
											<div class="row wk-row-margin">
								   
											{if isset($section.product_detail)}
												<div class="col-md-12">
											   
											   
													<div class="wk_bp_loader_{$section.id_wk_bundle_section}" style="display:none;">
														<center>
															<img class="wk_bp_loading-image" src="{$wk_loader|escape:'html':'UTF-8'}" alt="loading..">
														</center>
													</div>
													<div id="{$section.id_wk_bundle_section}">
														<div id="wk_bp_section_{$section.id_wk_bundle_section}" class="wk_bp_section_resp">
															{include file='module:wkbundleproduct/views/templates/hook/_partials/bundle_grid_view.tpl' sections=$section idpsproduct=$bundleDetail.id_ps_product themeType=$theme_type}
														</div>
													</div>
												</div>
											{/if}
									  
											</div>
								 
								{/foreach}
							</div>
						{/if}
						
						<div class="compo-pack-2">
							<table>
								<tbody>
									<tr>
										<td>
										
										</td>
										<td class="align-center">
											PRIX NORMAL
										</td>
										<td class="normal_price pr-20">
											{* calcul du prix *}
											{$normalPrice = 0}
											{$ruptureStock = 0}
											{foreach from=$bundleDetail.sections item=$section key=key}
												{foreach from=$section.product_detail item=$product_detail}
													{$normalPrice = $normalPrice + $product_detail.price_without_reduction}
														{* vérifie si il y a du stock *}
														{if !$product_detail.quantity}
															{$ruptureStock = 1}
														{/if}
												{/foreach}
											{/foreach}
										
									

											{$normalPriceDisplay = $normalPrice|string_format:"%.2f"}
											{$normalPriceDisplay} €
										</td>
									</tr>
									
									{if $bundleDetail.discount}
									<tr>
										<td>
										
										</td>
										<td class="align-center">
											REMISE {$bundleDetail.discount|string_format:"%.2f"} %
										</td>
										<td class="reduction_amount pr-20">
											{$discountPrice = $normalPrice * 0.01 * $bundleDetail.discount}
											{$discountPrice|string_format:"%.2f"} €
										</td>
									</tr>
									{/if}
									<tr>
										<td>
										
										</td>
										<td class="compo-pack-red align-center">
											PRIX DU PACK
										</td>
										<td class="compo-pack-red reduced_price pr-20">
										
											{*$product.price*}
										
											{$packPrice = $normalPrice * (1 - 0.01 * $bundleDetail.discount)}
											{$packPriceDisplay = $packPrice|string_format:"%.2f"}
											{$packPriceDisplay} €
										
										</td>
									</tr>
									
									<tr class="compo-pack-total">
										<td>
										
										</td>
										<td colspan="2" class="compo-pack-red">
											TOTAL DU PACK<br>
											<span class="packPriceReduce">{$packPriceDisplay} €</span>
											
											{if $bundleDetail.discount}
												<span class="compo-pack-reduction">
													au lieu de 
													<span class="packPriceWithoutReduce">{$normalPriceDisplay} €</span>
												</span>
											{/if}
											
										</td>

									</tr>
								</tbody>
							</table>
						</div>
					{/if}
				{/block}
			</div>


            <div class="product-actions js-product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">
				  
				  <input type="hidden" name="qty" value="1">

				  
				  
				 
				 {if $ruptureStock}
					<div id="product-availability" class="js-product-availability">
					<i class="material-icons product-unavailable"></i>
						En rupture de stock
					  </div>
				{/if}
				
                  <div class="product-add-to-cart clearfix">

						<button
								class="btn btn-primary add-to-cart"
								data-button-action="add-to-cart"
								type="submit"
								{if !$product.add_to_cart_url || $ruptureStock}
									disabled
								{/if}
						>
							<i class="material-icons shopping-cart">&#xE547;</i>
							{l s='Add to cart' d='Shop.Theme.Actions'}
						</button>
						

						{hook h='displayProductActions' product=$product}
					</div>
					
					
				  
				  

                  {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                  {block name='product_refresh'}{/block}
                </form>
              {/block}

            </div>

	</div>
</div>

<div class="clearboth" style="clear:both"></div>

<section class="page-product-wkpack">
	<h3 class="page-product-heading">Votre pack en détails</h3>
	<div class="slide-wkpack-left">
	
		{foreach from=$bundleDetail.sections item=$section key=key}
			{$subproduct = $section.product_detail[0]}
			
			<div class="js-wkpack-product{if $section@iteration == 1} active{/if}" data-id="{$subproduct.id}">
				<img class="img-responsive" src="{$subproduct.cover.bySize.home_default.url}" alt="{$subproduct.cover.legend}" title="{$subproduct.cover.legend}" itemprop="image">
				<div class="ap5-pack-product-tab-name">{$subproduct.name}</div>
			</div>
		{/foreach}
	
	</div>
	<div class="slide-wkpack-right">
		{foreach from=$bundleDetail.sections item=$section key=key}
			{$product = $section.product_detail[0]}			
			
			<div class="wkpack-product-detail{if $section@iteration != 1} hidden{/if}" data-id="{$product.id}">

				<div class="wkpack-product-name">{$product.name}</div>
				
				<div class="wkpack-description-short">{$product.description_short nofilter}</div>

				<div class="div_conditions_de_culture desktop">
				  {block name='calendrier'}
					{include file='catalog/calendrier.tpl'}
				  {/block}
				</div>
				
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
			
				<div class="wkpack-description-bottom">{$product.description nofilter}</div>
			</div>
		{/foreach}
	
	</div>
</section>
