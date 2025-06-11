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
{extends file=$layout}

{assign var="frais_de_port_gratuits" value=59}
{assign var="reduction100" value=120}
{assign var="subtotals" value=0}
{assign var="cpt_colisEC" value=0}
{if isset($cart.subtotals.products.amount)}
	{$subtotals = $cart.subtotals.products.amount|replace:",":"."|floatval}
{else}
  {foreach from=$cart.subtotals item=sub}
    {if $sub.type == 'products'}
	    {$subtotals = $subtotals + $sub.amount|replace:",":"."|floatval}
      {$cpt_colisEC++}
    {/if}
  {/foreach}
{/if}
{assign var="reste20" value=$frais_de_port_gratuits-$subtotals}
{assign var="reste100" value=$reduction100-$subtotals}

{block name='content'}
  <input id="frais_de_port_gratuits" type="hidden" value="{$frais_de_port_gratuits}">
  <input id="reduction100" type="hidden" value="{$reduction100}">
	
  <section id="main">
    <div class="cart-grid row">

      <!-- Left Block: cart product informations & shipping -->
      <div class="cart-grid-body col-lg-8">

        <!-- cart products detailed -->
        <div class="card cart-container">
          <div class="card-block">
            <h1 class="h1">{l s='Shopping Cart' d='Shop.Theme.Checkout'}</h1>
          </div>
          <hr class="separator">
          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}
		  
		  {*<div class="aw-promo-discount-wrap">
			 {if $cart.vouchers.added}
			 <div class="aw-promo-discount">
			  {block name='cart_voucher_list'}
				<ul class="promo-name card-block">
				  {foreach from=$cart.vouchers.added item=voucher}
					<li class="cart-summary-line">
					  <span class="label">{$voucher.name}</span>
					  <div class="float-xs-right">
						<span>{$voucher.reduction_formatted}</span>
						  {if isset($voucher.code) && $voucher.code !== ''}
							<a href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">&#xE872;</i></a>
						  {/if}
					  </div>
					</li>
				  {/foreach}
				</ul>
			  {/block}
			  </div>
			{/if}
		  </div>*}
      <div class="aw-promo-discount-wrap">
			{if $cart.vouchers.added}
          <div class="aw-promo-discount fin">
          <ul>
      {foreach $cartRulesEnCours as $REC}
        {foreach $REC['product_CR'] as $REC_g}
          {*if count($REC_g['values']) == 0 || $REC_g['id_cart_rule'] == 2347*}
            {foreach from=$cart.vouchers.added item=voucher}
              {if $voucher.id_cart_rule == $REC_g['id_cart_rule']}
                <li class="cart-summary-line">
                  <span class="label">{$voucher.name}</span>
                  <div class="float-xs-right">
                  <span>{$voucher.reduction_formatted}</span>
                    {if isset($voucher.code) && $voucher.code !== ''}
                    <a href="{$voucher.delete_url}" data-link-action="remove-voucher"><i class="material-icons">&#xE872;</i></a>
                    {/if}
                  </div>
                </li>
              {/if}
            {/foreach}
          {*/if*}
        {/foreach}
      {/foreach}
          </ul>
          </div>
      {/if}
      </div>
		  
		  
        </div>
		
	<div class="cart-discount-message">
		{hook h='displayQuantityDiscountProCustom4'}
	</div>
		
    <div id="cart_summary_foot">
  		<div class="block-promo">
  		  <div class="aw-cart-voucher">

  			<div id="promo-code" class="collapse in">
  			  <h4>Vous avez un code de réduction ?</h4>
  			  <p>Chèque-cadeau, code de parrainage, code newsletter, bon d'achat, bon de réduction...</p>
  			  <div class="promo-code">
  				{block name='cart_voucher_form'}
  				  <form action="{$urls.pages.cart}" data-link-action="add-voucher" method="post">
  					<input type="hidden" name="token" value="{$static_token}">
  					<input type="hidden" name="addDiscount" value="1">
  					<input class="promo-input" type="text" name="discount_name" placeholder="insérer votre code ici">
  					<button type="submit" class="voucher-ok"><span>OK</span></button>
  				  </form>
  				{/block}

  				{block name='cart_voucher_notifications'}
  				  <div class="alert alert-danger js-error" role="alert">
  					<i class="material-icons">&#xE001;</i><span class="ml-1 js-error-text"></span>
  				  </div>
  				{/block}

  			  </div>
  			</div>

          {if $cart.discounts|count > 0}
            <p class="block-promo promo-highlighted">
              {l s='Take advantage of our exclusive offers:' d='Shop.Theme.Actions'}
            </p>
            <ul class="js-discount card-block promo-discounts">
              {foreach from=$cart.discounts item=discount}
                <li class="cart-summary-line">
                  <span class="label">
                    <span class="code">{$discount.code}</span> - {$discount.name}
                  </span>
                </li>
              {/foreach}
            </ul>
          {/if}
        </div>
      </div>

      <div class="right-block">
        {block name='cart_summary'}
          <div class="card cart-summary">

            {block name='hook_shopping_cart'}
              {hook h='displayShoppingCart'}
            {/block}

            {block name='cart_totals'}
              {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
            {/block}


          </div>
        {/block}
{if $cpt_colisEC == 1}
    		<div class="cart_free_shipping reste20" {if $reste20 <= 0} style="display: none;" {/if}>
    			<div>
    			  Pour obtenir les frais de port offerts en France métropolitaine, vous devez encore commander pour <span class="free_shipping20">{Tools::displayPrice($reste20)}</span> (hors réductions et plants en précommande).
    			</div>
				{block name='continue_shopping'}
				<a class="continue_shopping label mobile" href="{$urls.pages.index}">
					<i class="material-icons">chevron_left</i>{l s='Continue shopping' d='Shop.Theme.Actions'}
				</a>
				{/block}
    		</div>
			{/if}
			<div class="cart_free_shipping reste100" {if $reste100 <= 0 || $reste20 > 0} style="display: none;" {/if}>
				<div>Pour obtenir une réduction de 10%, vous devez encore commander pour <span class="free_shipping100">{Tools::displayPrice($reste100)}</span>.<br />Attention cette réduction ne s'applique pas sur les box.</div>
			</div>
		
      </div>
    </div>

    <div class="cart_navigation clearfix">
      <div class="left-part">
        {block name='continue_shopping'}
          <a class="continue_shopping label desktop" href="{$urls.pages.index}">
            <i class="material-icons">chevron_left</i>{l s='Continue shopping' d='Shop.Theme.Actions'}
          </a>
        {/block}
      </div>
      {block name='cart_actions'}
        {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
      {/block}
    </div>



        <!-- shipping informations -->
	
        {block name='hook_shopping_cart_footer'}
          {hook h='displayShoppingCartFooter'}
        {/block}
      </div>
	  
	  

      <!-- Right Block: cart subtotal & cart total -->
      <div class="cart-grid-right col-lg-4">

        <div id="order-infos-content">
          <div id="reinsurance_block_prdt" class="clearfix">
            <div class="blockreassurance_product first">
              <div style="width: 100%;background-image: url(/modules/blockreassurance/views/img/img_perso/reinsurance-6-1.png);">
                <p class="block-title" style="color:#000000;">Paiement 100% sécurisé</p>
              </div>
               <img class="logos-rea-produit" src="/img/desktop-2022/moyens-paiement-lbg.png" alt="logos-page-produit">
            </div>


            <div class="blockreassurance_product second">
              <div>
                <p class="block-title" style="color:#000000; font-size: 15px;">Remise -10% dès 120€ d'achats (hors box et serres)</p>
              </div>
              <div>
                <p class="block-title" style="color:#000000;">Expédition en 48/72h</p>
              </div>
            </div>
          </div>

          <div id="google-review-desktop">
            {literal}
              <!-- DÉBUT du code d'affichage du badge Google Avis clients -->

              <span id="google-avis-client-bloc"><span id="google-avis-client" style="border: 1px none rgb(245, 245, 245); text-indent: 0px; margin: 0px; padding: 0px; background: transparent; float: right; line-height: normal; font-size: 1px; vertical-align: baseline; display: inline-block; width: 165px; height: 54px;"><iframe ng-non-bindable="" hspace="0" marginheight="0" marginwidth="0" scrolling="no" style="position: static; top: 0px; width: 165px; margin: 0px; border-style: none; display: block; left: 0px; visibility: visible; height: 54px;" tabindex="0" vspace="0" id="I0_1671093214245" name="I0_1671093214245" src="https://www.google.com/shopping/customerreviews/badge?usegapi=1&amp;merchant_id=8265898&amp;position=INLINE&amp;origin=https%3A%2F%dev.labonnegraine.com&amp;gsrc=3p&amp;jsh=m%3B%2F_%2Fscs%2Fabc-static%2F_%2Fjs%2Fk%3Dgapi.lb.fr.xFYH_S4Arb0.O%2Fd%3D1%2Frs%3DAHpOoo-GHFDQGtQ3VH9EXG2N8TRCzcabQw%2Fm%3D__features__#_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe%2C_renderstart%2Concircled%2Cdrefresh%2Cerefresh&amp;id=I0_1671093214245&amp;_gfid=I0_1671093214245&amp;parent=https%3A%2F%dev.labonnegraine.com&amp;pfname=&amp;rpctoken=30809071" data-gapiattached="true" title="Google Avis clients" width="100%" frameborder="0"></iframe></span></span>

              <script src="https://apis.google.com/js/platform.js?onload=renderBadge" async defer></script>

              <script>
                //window.renderBadge = function() {
                var ratingBadgeContainer = document.getElementById("google-avis-client");

                window.gapi.load('ratingbadge', function() {
                  window.gapi.ratingbadge.render(ratingBadgeContainer, {"merchant_id": 8265898, "position": "INLINE"});
                });
                //}
              </script>

              <!-- FIN du code d'affichage du badge Google Avis clients -->
              {/literal}
          </div>

          <div class="cheque_cadeau">
            <a class="class-a" href="/cartes-cadeaux">
              <h2>Faites-lui plaisir,<br>offrez une carte cadeau !</h2>
            </a>
          </div>

          <div class="faq-block">
            <span>Consultez les<span class="uppercase">Questions Fréquentes</span></span>
            <a class="class-a uppercase" href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">J'y vais</a>
          </div>

      </div>


      </div>

    </div>
    
    {hook h='displayCrossSellingShoppingCart'}
    
	</section>
	{if Configuration::get('MP_POPIN_BONS_PLANTS') == "on"}
	  <input id="mp_popin_bons_plants" type="hidden" value="1">
	{/if}

	<div class="modal_box modal_box_bons_plants">
		<div class="modal_content modal_content_bons_plants">
			<span class="modal_close modal_close_bons_plants">&times;</span>
			<div class="row">
				<div class="col-lg-12 col-xs-12">

					<a href="{Configuration::get('MP_POPIN_BP_LIEN')}">
						<img class="modal_img_bons_plants" src="/upload/{Configuration::get('MP_POPIN_BP_VISUEL')}" alt="bons plants">
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal_box modal_box_plant" style="display:none">
		<div class="modal_content modal_content_plant">
			<span class="modal_close modal_close_plant">&times;</span>
			<div class="modal-plant-row-1">
				Les frais de ports sont offerts dès 49 € d'achats <u>hors produits en précommande</u> et pour une livraison en France métropolitaine.
			</div>
			<div class="modal-plant-row-2">
				<b>Un forfait de 4.90 € est appliqué dès l'achat d'un produit en précommande quelque soit la quantité et le choix du mode de livraison (relais-colis ou domicile).</b>
			</div>
		</div>
	</div>

{/block}


