{if isset($category_header_messages) && $category_header_messages|count > 0}
<div class="category-header-messages">
    {foreach from=$category_header_messages item=message name=messages}
        <div class="category-header-message">
            <div class="container">
                <div class="row">
                    {if isset($message.image) && $message.image}
                        <div class="col-xs-12 col-sm-12 col-md-6 image" style="background-image:url('{$message.image_url|escape:'html':'UTF-8'}')">                        
                        {if $message.type == "produit_phare"}
                            <div class="accroche produit_phare"><i class="fa fa-heart"></i> Notre produit coup de coeur</div>
                        {else if $message.type == "promo_moment"}
                            <div class="accroche promo_moment"><i class="fa fa-tag"></i> Promos du moment !</div>
                        {else if $message.type == "reduction_lot"}
                            <div class="accroche reduction_lot"><i class="fa-solid fa-layer-group"></i> Réduction par lot !</div>
                        {else if $message.type == "accessoires"}
                            <div class="accroche accessoires"><i class="fa-solid fa-circle-plus"></i> L'accessoire indispensable !</div>
                        {else if $message.type == "offre_eco"}
                            <div class="accroche offre_eco"><i class="fa-solid fa-lightbulb"></i> Top prix !</div>
                        {/if}
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 right {$message.type}">
                    {else}
                        <div class="col-md-12 right {$message.type}">
                    {/if}
                    {if $message.id_product > 0}
                    <a href="{$message.product.link}">
                    {else}
                    <a href="{$message.cta_link}">
                    {/if}
                            <h3 class="message-title">{$message.title|escape:'html':'UTF-8'}</h3>                            
                    </a>
                            <div class="message-content description">
                                {$message.content nofilter}
                            </div>
                            <div class="message-content image">
                            {if $message.id_product > 0}
                    <a href="{$message.product.link}">
                    {else}
                    <a href="{$message.cta_link}">
                    {/if}
                                <img src="{$message.image_url}" />
                                {if $message.id_product > 0}
                    </a>
                    {/if}
                            </div>
                            {if isset($message.cta_text) && $message.cta_text && isset($message.cta_link) && $message.cta_link}
                                <div class="message-cta">
                                {if $message.id_product > 0}
                                        <span class="price">{$message.product.price}</span>
                                        {if $message.product.price != $message.product.price_without_reduction}
                                            <span class="old_price">{$message.product.price_without_reduction}</span>
                                        {/if}
                                        <button class="btn btn-primary add-to-cart-commercial" data-quantity="1" data-id-product="{$message.id_product}" data-id-product-attribute="{$message.id_product_attribute}">
                                            <img class="img-off" src="/themes/lbg/assets/img/picto-panier-off.png" alt="">
							                <img class="img-on" src="/themes/lbg/assets/img/picto-panier-on.png" alt="">
                                            {l s='Add to cart' d='Shop.Theme.Actions'}
                                        </button>
                                {else}
                                    <a href="{$message.cta_link|escape:'html':'UTF-8'}" class="btn btn-primary {$message.type}">{$message.cta_text|escape:'html':'UTF-8'}</a>
                                {/if}
                                </div>
                            {/if}
                            {*<pre>
                            {$message|print_r}
                            </pre>*}
                        </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>
{/if}