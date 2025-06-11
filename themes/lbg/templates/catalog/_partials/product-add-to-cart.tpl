<div class="product-add-to-cart js-product-add-to-cart">
    {if !$configuration.is_catalog}
        {block name='product_minimal_quantity'}
            <p class="product-minimal-quantity js-product-minimal-quantity">
                {if $product.minimal_quantity > 1}
                    {l
                    s='The minimum purchase order quantity for the product is %quantity%.'
                    d='Shop.Theme.Checkout'
                    sprintf=['%quantity%' => $product.minimal_quantity]
                    }
                {/if}
            </p>
        {/block}
		
		<div class="remise-wrap">
			{hook h='displayQuantityDiscountProCustom1' product=$product}
		</div>
		
		<div class="product-add-to-cart clearfix">

            {if $product.availability == 'last_remaining_items'}
                {$product.availability_message}
            {else}
                {if $product.quantity > 0 }
                    {if $isSerres && !$isNATURA}
						<p id="popinSerresBtn" class="buttons_bottom_block no-print">
							<button type="button" class="exclusive">
								<span>Demande d'informations</span>
							</button>
						</p>
					{else}
						 <button
                                class="btn btn-primary add-to-cart"
                                data-button-action="add-to-cart"
                                type="submit"
                                {if !$product.add_to_cart_url}
                                    disabled
                                {/if}
                        >
                            <i class="material-icons shopping-cart">&#xE547;</i>
                            {l s='Add to cart' d='Shop.Theme.Actions'}
                        </button>
                    {/if}
                {else}
                    <div class="product-additional-info js-product-additional-info">
              {hook h='displayProductAdditionalInfo' product=$product}
            </div>
                {/if}
            {/if}
			

			

					

			{hook h='displayProductActions' product=$product}
		</div>
    {/if}
</div>
