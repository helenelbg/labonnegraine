{if $cart.products}

{assign var="frais_de_port_gratuits" value=59}
<h4 class="bouton_semaine_reset modif_exped"><img src="/img/calendar-month-reset.png" class="picto_calendar" />Envoi optimisé</h4>
<h4 class="bouton_semaine_rapide modif_exped"><img src="/img/calendar-month-reset.png" class="picto_calendar" />Envoi rapide</h4>

{$whEC = $cart.products.0.warehouse_en_cours}
{$idCatDefault = $cart.products.0.id_category_default}
{$cpt_colis = 1}
{$cpt_categorie = 1}
{$affiche_groupe = false}
{$affiche_semaine = 0}
{if $whEC > 0 || $whEC == -2}
    <h3 class="differee">Commande N°{$cpt_colis} - Expédition prévue semaine {$cart.products.0.exped_semaine}</h3>
{else}
    <h3 class="immediate">Commande N°{$cpt_colis} - Expédition {$cart.products.0.exped}</h3>
{/if}
<div class="div_colis colis{$cpt_colis}">
{if $whEC > 0 || $whEC == -2}
    <span class="depart">Départ de notre entrepôt <span class="date_colis">{$cart.products.0.exped}</span></span>
{else}
    <span class="depart">Départ de notre entrepôt <span class="date_colis">immédiat</span></span>
{/if}
{$catName = Category::getCategoryInformations([$idCatDefault])}
{if isset($cart.products.0.warehouse_list_default.0) && $affiche_groupe == false}
{$affiche_groupe = true}
<h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">Produits en stock</h4>
<h4 class="modif_exped {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} col{$cpt_colis}_{$cpt_categorie}" {if $whEC == -2}style="display:none;"{/if} data-semaine="{$cpt_colis}_0" data-colis="{$cpt_colis}"><img src="/img/calendar-month-{if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}.png" class="picto_calendar" />Modifier la date d'envoi</h4>
{$affiche_semaine = 1}
{else if isset($cart.products.0.warehouse_list_default.0) && $affiche_groupe == true}
{else if !isset($cart.products.0.warehouse_list_default.0) || $cart.products.0.exped <> 'immédiate'}
<h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">{$catName.$idCatDefault.name}</h4>
<h4 class="modif_exped {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} col{$cpt_colis}_{$cpt_categorie}" {if $whEC == -2}style="display:none;"{/if} data-semaine="{$cpt_colis}_{$idCatDefault}" data-colis="{$cpt_colis}"><img src="/img/calendar-month-{if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}.png" class="picto_calendar" />Modifier la date d'envoi</h4>
{$affiche_semaine = 2}
{/if}
{$cpt_categorie = $cpt_categorie + 1}
{if $affiche_semaine <> 0}
{if $affiche_semaine == 1}
    {$valCat = 0}
{else if $affiche_semaine == 2}
    {$valCat = $idCatDefault}
{/if}
<div class="liste_semaines" id="semaine{$cpt_colis}_{$valCat}">

{$cpt_s = 0}
{$array_cal = []}
{foreach $cart.products.0.warehouse_list_default as $liste_semaine}
{if $liste_semaine < 10}
    {$liste_semaine = '0'|cat:$liste_semaine}
{/if}
{if $liste_semaine == '00'}
    {$liste_semaine = $smarty.now|date_format:"%W"}
{/if}
{$array_cal[$cpt_s] = $liste_semaine}
{$cpt_s = $cpt_s+1}
{/foreach}

<div class="caldiff" id="weekpicker{$cpt_colis}_{$valCat}" data-category="{$valCat}"></div>
<script language="Javascript">
{literal}
setTimeout(function(){
$(document).ready(function(){
calendrier("{/literal}{$cpt_colis}_{$valCat}{literal}", "{/literal}{';'|implode:$array_cal}{literal}","{/literal}{$cart.products.0.exped_semaine}{literal}");
});
}, 500);
{/literal}
</script> 

</div>
{$affiche_semaine = 0}
{/if}
<ul class="cart-items {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} colis colis{$whEC}_{$idCatDefault}">
    {foreach from=$cart.products item=product}
    {if $product.warehouse_en_cours <> $whEC}
        </ul>
        </div>
        {$shipEC = 'shipping'|cat:$cpt_colis}
        {$prodEC = 'products'|cat:$cpt_colis}
        <div class="colis_exped">{$cart.summary_string_detail.$cpt_colis} : <span class="product-price">{$cart.subtotals.$prodEC.value}</span></div>
        <div class="colis_exped livraison">Livraison : <span class="product-price">{$cart.subtotals.$shipEC.value}</span></div>

        {assign var="subtotals{$cpt_colis}" value=0}
        {if isset($cart.subtotals.$prodEC.value)}
            {$subtotals{$cpt_colis} = $cart.subtotals.$prodEC.value|replace:",":"."|floatval}
        {/if}
        {assign var="reste20{$cpt_colis}" value=$frais_de_port_gratuits-$subtotals{$cpt_colis}}

        <div class="cart_free_shipping reste20" {if $reste20{$cpt_colis} <= 0 || $cart.subtotals.$shipEC.value == 'gratuit'} style="display: none;" {/if}>
            <div>
                Pour obtenir les frais de port offerts en France métropolitaine,<br />vous devez encore commander pour <span class="free_shipping20">{Tools::displayPrice($reste20{$cpt_colis})}</span> (hors réductions).
            </div>
        </div>

        {$cpt_colis = $cpt_colis + 1}
        {$cpt_categorie = 1}
        {$whEC = $product.warehouse_en_cours}
        {if $whEC > 0 || $whEC == -2}
        <h3 class="differee">Commande N°{$cpt_colis} - Expédition prévue semaine {$product.exped_semaine}</h3>
        {else}
        <h3 class="immediate">Commande N°{$cpt_colis} - Expédition {$product.exped}</h3>
        {/if}
        <div class="div_colis colis{$cpt_colis}">
        {if $whEC > 0 || $whEC == -2}
            <span class="depart">Départ de notre entrepôt <span class="date_colis">{$product.exped}</span></span>
        {else}
            <span class="depart">Départ de notre entrepôt <span class="date_colis">immédiat</span></span>
        {/if}
        {$idCatDefault = $product.id_category_default}
        {$catName = Category::getCategoryInformations([$idCatDefault])}
        {if isset($product.warehouse_list_default.0) && $affiche_groupe == false}
        {$affiche_groupe = true}
        <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">Produits en stock</h4>
        <h4 class="modif_exped {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} col{$cpt_colis}_{$cpt_categorie}" {if $whEC == -2}style="display:none;"{/if} data-semaine="{$cpt_colis}_0" data-colis="{$cpt_colis}"><img src="/img/calendar-month-{if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}.png" class="picto_calendar" />Modifier la date d'envoi</h4>
        {$affiche_semaine = 1}
        {else if isset($product.warehouse_list_default.0) && $affiche_groupe == true}
        {else if !isset($product.warehouse_list_default.0) || $product.exped <> 'immédiate'}
        <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">{$catName.$idCatDefault.name}</h4>
        <h4 class="modif_exped {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} col{$cpt_colis}_{$cpt_categorie}" {if $whEC == -2}style="display:none;"{/if} data-semaine="{$cpt_colis}_{$idCatDefault}" data-colis="{$cpt_colis}"><img src="/img/calendar-month-{if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}.png" class="picto_calendar" />Modifier la date d'envoi</h4>
        {$affiche_semaine = 2}
        {/if}
        {$cpt_categorie = $cpt_categorie + 1}
        {if $affiche_semaine <> 0}
        {if $affiche_semaine == 1}
            {$valCat = 0}
        {else if $affiche_semaine == 2}
            {$valCat = $idCatDefault}
        {/if}
        <div class="liste_semaines" id="semaine{$cpt_colis}_{$valCat}">

        {$cpt_s = 0}
        {$array_cal = []}
        {foreach $product.warehouse_list_default as $liste_semaine}
            {if $liste_semaine < 10}
                {$liste_semaine = '0'|cat:$liste_semaine}
            {/if}
            {if $liste_semaine == '00'}
                {$liste_semaine = $smarty.now|date_format:"%W"}
            {/if}
            {$array_cal[$cpt_s] = $liste_semaine}
            {$cpt_s = $cpt_s+1}
        {/foreach}

        <div class="caldiff" id="weekpicker{$cpt_colis}_{$valCat}" data-category="{$valCat}"></div>
        <script language="Javascript">
        {literal}
        setTimeout(function(){
        $(document).ready(function(){
        calendrier("{/literal}{$cpt_colis}_{$valCat}{literal}", "{/literal}{';'|implode:$array_cal}{literal}","{/literal}{$product.exped_semaine}{literal}");
	    });
        }, 500);
        {/literal}
        </script> 

        </div>
        {$affiche_semaine = 0}
        {/if}
        <ul class="cart-items {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} colis{$whEC}_{$idCatDefault}">
    {/if}
    {if $product.id_category_default <> $idCatDefault}
        </ul>
        {$idCatDefault = $product.id_category_default}
        {$catName = Category::getCategoryInformations([$idCatDefault])}
        {if isset($product.warehouse_list_default.0) && $affiche_groupe == false}
        {$affiche_groupe = true}
        <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">Produits en stock</h4>
        <h4 class="modif_exped {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} col{$cpt_colis}_{$cpt_categorie}" {if $whEC == -2}style="display:none;"{/if} data-semaine="{$cpt_colis}_0" data-colis="{$cpt_colis}"><img src="/img/calendar-month-{if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}.png" class="picto_calendar" />Modifier la date d'envoi</h4>
        {$affiche_semaine = 1}
        {else if isset($product.warehouse_list_default.0) && $affiche_groupe == true}
        {elseif !isset($product.warehouse_list_default.0) || $product.exped <> 'immédiate'}
        <h4 class="colis_categorie {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}">{$catName.$idCatDefault.name}</h4>   
        <h4 class="modif_exped {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} col{$cpt_colis}_{$cpt_categorie}" {if $whEC == -2}style="display:none;"{/if} data-semaine="{$cpt_colis}_{$idCatDefault}" data-colis="{$cpt_colis}"><img src="/img/calendar-month-{if $whEC > 0 || $whEC == -2}differee{else}immediate{/if}.png" class="picto_calendar" />Modifier la date d'envoi</h4>
        {$affiche_semaine = 2}
        {/if}
        {$cpt_categorie = $cpt_categorie + 1}
        {if $affiche_semaine <> 0}
        {if $affiche_semaine == 1}
            {$valCat = 0}
        {else if $affiche_semaine == 2}
            {$valCat = $idCatDefault}
        {/if}
        <div class="liste_semaines" id="semaine{$cpt_colis}_{$valCat}">
        
        {$cpt_s = 0}
        {$array_cal = []}
        {foreach $product.warehouse_list_default as $liste_semaine}
        {if $liste_semaine < 10}
            {$liste_semaine = '0'|cat:$liste_semaine}
        {/if}
        {if $liste_semaine == '00'}
            {$liste_semaine = $smarty.now|date_format:"%W"}
        {/if}
        {$array_cal[$cpt_s] = $liste_semaine}
        {$cpt_s = $cpt_s+1}
        {/foreach}

        <div class="caldiff" id="weekpicker{$cpt_colis}_{$valCat}" data-category="{$valCat}"></div>
        <script language="Javascript">
        {literal}
        setTimeout(function(){
        $(document).ready(function(){
        calendrier("{/literal}{$cpt_colis}_{$valCat}{literal}", "{/literal}{';'|implode:$array_cal}{literal}","{/literal}{$product.exped_semaine}{literal}");
	    });
        }, 500);
        {/literal}
        </script> 

        </div>
        {$affiche_semaine = 0}
        {/if}
        <ul class="cart-items {if $whEC > 0 || $whEC == -2}differee{else}immediate{/if} colis{$whEC}_{$idCatDefault}">
    {/if}
    <li class="cart-item">
        <div class="product-line-grid">
        <!--  product line left content: image-->
        <div class="product-line-grid-left col-md-1 col-xs-2">
            <span class="product-image media-middle">
            {if $product.default_image}
                <img src="{$product.default_image.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}" loading="lazy">
            {else}
                <img src="{$urls.no_picture_image.bySize.cart_default.url}" loading="lazy" />
            {/if}
            </span>
        </div>

        <!--  product line body: label, discounts, price, attributes, customizations -->
        <div class="product-line-grid-body col-md-6 col-xs-7">
            <div class="product-line-info">
            {$product.name}
            </div>

        <div class="cart-product-attributes">
            {', '|implode:$product.attributes}
        </div>

            {if is_array($product.customizations) && $product.customizations|count}
            <br>
            {block name='cart_detailed_product_line_customization'}
                {foreach from=$product.customizations item="customization"}
                <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                <div class="modal fade customization-modal js-customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                        </div>
                        <div class="modal-body">
                        {foreach from=$customization.fields item="field"}
                            <div class="product-customization-line row">
                            <div class="col-sm-3 col-xs-4 label">
                                {$field.label}
                            </div>
                            <div class="col-sm-9 col-xs-8 value">
                                {if $field.type == 'text'}
                                {if (int)$field.id_module}
                                    {$field.text nofilter}
                                {else}
                                    {$field.text}
                                {/if}
                                {elseif $field.type == 'image'}
                                <img src="{$field.image.small.url}" loading="lazy">
                                {/if}
                            </div>
                            </div>
                        {/foreach}
                        </div>
                    </div>
                    </div>
                </div>
                {/foreach}
            {/block}
            {/if}
        </div>

        <!--  product line right content: actions (quantity, delete), price -->
  <div class="product-line-grid-right product-line-actions col-md-4 col-xs-3">
    <div class="row">
      <div class="col-md-3 col-xs-12 product-line-info product-price h5 {if $product.has_discount}has-discount{/if}">
        {if $product.has_discount}
          <div class="product-discount">
            <span class="regular-price">{$product.regular_price}</span>
            {if $product.discount_type === 'percentage'}
              <span class="discount discount-percentage">
                  -{$product.discount_percentage_absolute}
                </span>
            {else}
              <span class="discount discount-amount">
                  -{$product.discount_to_display}
                </span>
            {/if}
          </div>
        {/if}
        <div class="current-price">
          <span class="price">{$product.price}</span>
          {if $product.unit_price_full}
            <div class="unit-price-cart">{$product.unit_price_full}</div>
          {/if}
        </div>
        {hook h='displayProductPriceBlock' product=$product type="unit_price"}
      </div>

      <div class="col-md-6 col-xs-12">
        <div class="row">
          <div class="col-md-12 col-xs-12 qty">
            <div class="cart-line-product-actions">
			  <a
				  class                       = "remove-from-cart"
				  rel                         = "nofollow"
				  href                        = "{$product.remove_from_cart_url}"
				  data-link-action            = "delete-from-cart"
				  data-id-product             = "{$product.id_product|escape:'javascript'}"
				  data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
				  data-id-customization       = "{$product.id_customization|default|escape:'javascript'}"
			  >
				{if empty($product.is_gift)}
				  <i class="material-icons float-xs-left">delete</i>
				{/if}
			  </a>

			  {block name='hook_cart_extra_product_actions'}
				{hook h='displayCartExtraProductActions' product=$product}
			  {/block}

			</div>
            {if !empty($product.is_gift)}
              <span class="gift-quantity">{$product.quantity}</span>
            {else}
              <input
                class="js-cart-line-product-quantity"
                data-down-url="{$product.down_quantity_url}"
                data-up-url="{$product.up_quantity_url}"
                data-update-url="{$product.update_quantity_url}"
                data-product-id="{$product.id_product}"
                type="number"
                inputmode="numeric"
                pattern="[0-9]*"
                value="{$product.quantity}"
                name="product-quantity-spin"
                aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}"
              />
            {/if}            
          </div>
          {*<div class="col-md-3 col-xs-12">
		    <div class="cart-line-product-actions">
			  <a
				  class                       = "remove-from-cart"
				  rel                         = "nofollow"
				  href                        = "{$product.remove_from_cart_url}"
				  data-link-action            = "delete-from-cart"
				  data-id-product             = "{$product.id_product|escape:'javascript'}"
				  data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript'}"
				  data-id-customization       = "{$product.id_customization|default|escape:'javascript'}"
			  >
				{if empty($product.is_gift)}
				  <i class="material-icons float-xs-left">delete</i>
				{/if}
			  </a>

			  {block name='hook_cart_extra_product_actions'}
				{hook h='displayCartExtraProductActions' product=$product}
			  {/block}

			</div>
          </div>*}
        </div>
      </div>
      <div class="col-md-3 col-xs-12 price">
          <span class="product-price">
                {if !empty($product.is_gift)}
                  <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
                {else}
                  {$product.total}
                {/if}
            </span>
      </div>
    </div>
  </div>

        <div class="clearfix"></div>
        </div>
    </li>
    {if is_array($product.customizations) && $product.customizations|count >1}<hr>{/if}
    {/foreach}
</ul>
</div>
{if $cpt_colis > 1}
    {$shipEC = 'shipping'|cat:$cpt_colis}
    {$prodEC = 'products'|cat:$cpt_colis}
    <div class="colis_exped">{$cart.summary_string_detail.$cpt_colis} : <span class="product-price">{$cart.subtotals.$prodEC.value}</span></div>
    <div class="colis_exped livraison">Livraison : <span class="product-price">{$cart.subtotals.$shipEC.value}</span></div>

    {assign var="subtotals{$cpt_colis}" value=0}
    {if isset($cart.subtotals.$prodEC.value)}
        {$subtotals{$cpt_colis} = $cart.subtotals.$prodEC.value|replace:",":"."|floatval}
    {/if}
    {assign var="reste20{$cpt_colis}" value=$frais_de_port_gratuits-$subtotals{$cpt_colis}}

    <div class="cart_free_shipping reste20" {if $reste20{$cpt_colis} <= 0 || $cart.subtotals.$shipEC.value == 'gratuit'} style="display: none;" {/if}>
        <div>
            Pour obtenir les frais de port offerts en France métropolitaine,<br />vous devez encore commander pour <span class="free_shipping20">{Tools::displayPrice($reste20{$cpt_colis})}</span> (hors réductions).
        </div>
    </div>
{/if}

<div class="row" style="clear: both;">
    <div class="col-md-6 col-xs-12">
        Envoi optimisé
    </div>
    <div class="col-md-6 col-xs-12">
        Envoi rapide
    </div>
</div>

{else}
    <span class="no-items">{l s='There are no more items in your cart' d='Shop.Theme.Checkout'}</span>
{/if}