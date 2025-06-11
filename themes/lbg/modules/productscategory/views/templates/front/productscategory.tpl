{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if count($categoryProducts) > 0 && $categoryProducts !== false}
<section  id="productscategory_list" class="page-product-box blockproductscategory">
	<div class="Page_Produit_MMCat_Titre">
		<h3 class="productscategory_h3 page-product-heading">
			{l s='%s Autres produits dans la même catégorie:' sprintf=[$categoryProducts|@count] mod='productscategory'}
		</h3>
	</div>	
    
	<div class="clearfix">
		<div id="bxslider1" class="bxslider clearfix">
                    
		{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}

			<div class="product-box item Page_Produit_MMCat_Case_Img">
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">

					<img data-lazy-src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />

					<h5 itemprop="name" class="product-name Page_Produit_MMCat_Nom">
						<div>{$categoryProduct.name|truncate:54:'...'|escape:'html':'UTF-8'}</div>
					</h5>

					
				</a>

			</div>
		{/foreach}
		</div>
	</div>
</section>

<section  id="productscategory_list_responsive" class="page-product-box blockproductscategory">
	<div class="clearfix">
		<div id="bxslider1" class="bxslider clearfix">
			<div class="product-box item Page_Produit_MMCat_Case_Img">
				<div class="Page_Produit_MMCat_Titre">
					<h3 class="productscategory_h3 page-product-heading">
						{l s='%s Autres produits dans la même catégorie:' sprintf=[$categoryProducts|@count] mod='productscategory'}
					</h3>
				</div>
			</div>	
			{$pos_categoryproduct=0}
			{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}

				{if $pos_categoryproduct<=2}
				<div class="product-box item Page_Produit_MMCat_Case_Img">
					<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">
				
						<div class="category_block">
							<img data-lazy-src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
						</div>

						<h5 itemprop="name" class="product-name Page_Produit_MMCat_Nom">
							<div>{$categoryProduct.name|truncate:54:'...'|escape:'html':'UTF-8'}</div>			
						</h5>

					
					</a>
				</div>
				{$pos_categoryproduct=$pos_categoryproduct+1}
			{/if}
		{/foreach}
		</div>
	</div>
</section>
{/if}