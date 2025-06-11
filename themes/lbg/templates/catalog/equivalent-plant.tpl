<div class="plant-section js-plant-precommande">
	<hr class="plant-hr">
	<div class="row row-graine-plant">
		<div class="col-xs-12 col-sm-7 plant-attributes-left">
			<span class="plant-conditionnement">Choisissez votre conditionnement :</span>
			<div class="product-variants-plant">
                {foreach from=$groups key=id_attribute_group item=group}
					{if !empty($group.attributes)}
						<div class="clearfix product-variants-item">
							{if $group.name == "Production"}
								<span class="control-label plant-production">{$group.name}{l s=': ' d='Shop.Theme.Catalog'}
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
										{if $group_attribute.selected}{$group_attribute.name}{/if}
									{/foreach}
								</span>
							{/if}

							{if $group.group_type == 'radio'}
								{*<ul id="group_{$id_attribute_group}">*}
									{$cpt1 = 0}
									{foreach from=$group.attributes key=id_attribute item=group_attribute}
										{if isset($group_attribute.type) && $group_attribute.type == 'graine'}
											{if !$group_attribute.name|strstr:"godet"}
												{if $cpt1 == 0}
												<ul id="group_{$id_attribute_group}">
												{$cpt1 = $cpt1 + 1}
												{/if}
												<li class="input-container graine-input-container float-xs-left">
													<label>
														<input class="input-radio" type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}" title="{$group_attribute.name}"{if $group_attribute.selected} checked="checked"{/if}>
														<span class="radio-label graine-radio">{$group_attribute.name}</span>
													</label>
												</li>
											{/if}
										{/if}
									{/foreach}
									{if $cpt1 == 1}
									</ul>
									{/if}
								{*</ul>*}
							{/if}
							
							
						</div>
					{/if}
				{/foreach}
			</div>
		</div>
		<div class="col-xs-12 col-sm-4 plant-attributes-right">
			<div class="plant-quantity product-quantity clearfix">
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
		</div>
		<div class="col-xs-12">
			<label id="availability_label" style="padding-left:0px;">Expédition : </label><span class="expedBottom">{$expedProd}</span>
		</div>
	</div>
	<hr class="plant-hr">
	<div class="row">
		<div class="col-xs-12 plant-quantity-price">
			{block name='product_quantity'}
				{*<div class="plant-quantity product-quantity clearfix">
					<label for="quantity_wanted">Quantité : </label>
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
				</div>*}
				<div class="plant-price">
					{block name='product_prices'}
						{include file='catalog/_partials/product-prices.tpl'}
					{/block}
				</div>
				<div class="plant-prix-barre">
					<img src="/themes/lbg/assets/img/prix-barre.png" alt="prix">
				</div>
				<div class="plant-loading"></div>
			{/block}
		</div>
	</div>
	<div class="plant-remise-section">
		<div class="plant-remise-wrap">
			{hook h='displayQuantityDiscountProCustom1' product=$product}		
		</div>
		{*<div class="plant-text-a"><u>La vente des plants est uniquement sur précommande.</u></div>
		<div class="plant-text-b"><u>La livraison est prévue entre le 20/04 et le 10/05.</u></div>*}
	</div>

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

	<div class="product-add-to-cart clearfix">

		{if $product.availability == 'last_remaining_items'}
			{$product.availability_message}
		{else}
			{if $product.quantity > 0 }
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
			{else}
				<div class="product-additional-info js-product-additional-info">
					{hook h='displayProductAdditionalInfo' product=$product}
				</div>
			{/if}
		{/if}
		{hook h='displayProductActions' product=$product}
	</div>

	<div class="col-xs-12 col-sm-12">
		<div class="product-variants-plant">
			<a href="{url entity='category' id=$lien_plant id_lang=1}">
			<div class="plant-nouveau">
				<div class="div_picto_plant">
					<img class="picto-plant" src="/themes/lbg/assets/img/picto-plant.png" alt="" title="Existe en plant">
				</div>
				<span>Vous cherchez des plants ?<br />Découvez notre gamme</span>
			</div>
			</a>
		</div>
	</div>
</div>

