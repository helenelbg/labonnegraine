{block name='product_flags'}
    <ul class="product-flags js-product-flags">
        {foreach from=$product.flags item=flag}
            {if $flag.type == "out_of_stock"}
                {if $product.not_available_message != ""}
                    <li class="product-flag {$flag.type}">{$product.not_available_message}</li>
                {elseif $product.available_later != ""}
                    <li class="product-flag {$flag.type}">{$product.available_later}</li>
                {else}
                    <li class="product-flag {$flag.type}">{l s='Rupture stock'}</li>
                {/if}
            {else}
				{if $flag.label == "Neuf"}
					{$flag.label = "Nouveau"}
				{/if}
                <li class="product-flag {$flag.type}">{$flag.label}</li>
            {/if}
        {/foreach}
    </ul>
{/block}
