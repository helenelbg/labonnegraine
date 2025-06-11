{extends file='page.tpl'}

{block name='page_content'}

	<h1 class="category_title">{$category.name}</h1>

	{if $category.description}
		<div class="cat_desc_box">{$category.description nofilter}</div>
	{/if}

	{if $listing.products}
		<div id="conteneur_slide"> 
			<div class="un_slick_slider">
				<div> 
					<p><span class="num_etape_box">1 ) </span>Choisissez votre Box coup de coeur et sélectionnez votre formule d'abonnement (1, 2, 3 ou 4 saisons)</p>
				</div>
				<div>
					<p><span class="num_etape_box">2 ) </span>Recevez votre Box La Bonne Graine dans votre boite aux lettres (mi-janvier, mi-avril, mi-juillet ou mi-octobre - fin des abonnements le 10 du mois de l'expédition)</p>
				</div>
				<div>
					<p><span class="num_etape_box">3 ) </span>Chaussez vos bottes en caoutchouc et faites de votre jardin un laboratoire à ciel ouvert !</p>
				</div>
				<div>
					<p><span class="num_etape_box">4 ) </span>Partagez avec nous vos expériences sur nos réseaux sociaux !</p>
				</div>
			</div>
		</div>
		<ul id="product_list_box">
			{foreach from=$listing.products item=product}
				
				<li class="ajax_block_product {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}item{/if}">
					<div class="center_block">
						<div class="image_block">
							
							  {if $product.cover}
								<a href="{$product.url}" class="thumbnail product-thumbnail">
								  <img
									src="{$product.cover.bySize.home_default.url}"
									alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
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
			
						
						</div>
						<div class="desc_block">
							<p class="product_desc"><a href="{$product.link}">{$product.description_short nofilter}</a></p>
						</div>
					</div>
					<div class="right_block">
						<div class="prix_div"><span class="price">{$product.price}</span> / saison</div>
						<a class="lien_sabonner" href="{$product.link}">S'abonner</a>
						<div class="mini_desc">{$product.info_sup nofilter}</div>
					</div>
				</li>
			{/foreach}
		</ul>
	
	{/if}

{/block}




