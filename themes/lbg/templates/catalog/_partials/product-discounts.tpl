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

{if $product.quantity_discounts}
<section class="product-discounts js-product-discounts">
    <p class="h6 product-discounts-title">{l s='Volume discounts' d='Shop.Theme.Catalog'}</p>
    {block name='product_discount_table'}
	  
	  <div class="std degressif-table">
	  {foreach from=$product.quantity_discounts item='quantity_discount' name='quantity_discounts'}
		<div class="ligne">
			<div class="title">A partir de {$quantity_discount.quantity}</div>
			<div class="pourcent">-{$quantity_discount.discount}</div>
			<strike class="prix-barre">{$product.regular_price}</strike>
			<div class="priceWithReduc">{Tools::displayPrice($product.regular_price_amount * ( 1 - 0.01 * $quantity_discount.real_value))}</div>
		</div>
      {/foreach}
	  </div>
	    
    {/block}
</section>
{/if}
