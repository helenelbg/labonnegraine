{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{if isset($product_name)}
    {$product_name}
{/if}
{if $product_array}
   
        {if isset($formail) && $formail == 1}
            {l s='This bundle contains following products - ' mod='wkbundleproduct'}
            {assign var=count value=1}
            {foreach $product_array as $info}
                ({$count}) {$info.product_name} {l s='Quantity' mod='wkbundleproduct'} :{$info.product_qty}
                {assign var=count value=$count+1}
            {/foreach}
        {else}
			<div class="wk-bp-cart-popup">
				<strong>{l s='This bundle contains following products - ' mod='wkbundleproduct'}</strong><br>
				<span style="font-size:6.5px">
				{foreach $product_array as $info}
					 {$info.reference} - {$info.botanic_name} - {$info.product_name}&nbsp;|&nbsp;{l s='Quantity' mod='wkbundleproduct'} :{$info.product_qty}<br>
				{/foreach}
				</span>
			</div>
        {/if}
{/if}
