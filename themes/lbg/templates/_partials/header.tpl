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

<style>
	{if Configuration::get('MP_COULEUR_TEXTE')}
	  {literal}
	  body{
		color: {/literal}{Configuration::get('MP_COULEUR_TEXTE')}{literal};
	  }
	  {/literal}
	{/if}

	{if Configuration::get('MP_PICTOS')}
	  {literal}
	  .bullet .feature {
		background-image: url(/upload/{/literal}{Configuration::get('MP_PICTOS')}{literal});
		background-repeat: no-repeat;
	  }
	  {/literal}
	{/if}
	
	{if Configuration::get('MP_FOND_DE_PAGE')}
	  {literal}
	  body {
		background-image: url(/upload/{/literal}{Configuration::get('MP_FOND_DE_PAGE')}{literal});
		background-repeat: no-repeat;
	  }
	  {/literal}
	{/if}
	
	{if Configuration::get('MP_COULEUR_FOND_DE_PAGE')}
	  {literal}
	  body {
		background-color: {/literal}{Configuration::get(MP_COULEUR_FOND_DE_PAGE)}{literal};
	  }
	  {/literal}
	{/if}
	
	{if Configuration::get('MP_MINI_HEADER')}
	  {literal}
	  body#index .Bandeau_Top {
		background-image: url(/upload/{/literal}{Configuration::get('MP_MINI_HEADER')}{literal});
		background-repeat: no-repeat;
		height: 0; /* le mini header n'est plus utilisé ? */
	  }
	  {/literal}
	{/if}
	
	{if Configuration::get('MP_HEADER')}
	  {literal}
	  body:not(#index) .Bandeau_Top {
		background-image: url(/upload/{/literal}{Configuration::get('MP_HEADER')}{literal});
		background-repeat: no-repeat;
		height: 100px;
	  }
	  {/literal}
	{/if}
	
	{if Configuration::get('MP_POSITION_LOGO') == 'centre'}
	  {literal}
	  @media (min-width: 795px) {
		#header_logo {margin-left: 45%;}
      }
	  {/literal}
	{/if}
	
	
	{if Configuration::get('MP_LOGO_B')}
	  {literal}
	  .Bandeau_Top .row #header_logo a::before, header .row #_desktop_logo a::before {
		background-image: url(/upload/{/literal}{Configuration::get('MP_LOGO_B')}{literal});
		background-repeat: no-repeat;
	  }
	  {/literal}
	{/if}

	{if Configuration::get('MP_PANCARTE') == 'off'}
		{literal}
		#pancarte {
			display: none;
		}
		{/literal}
	{/if}
	
	{if Configuration::get('MP_COULEUR_FOND_PRODUIT') && Configuration::get('MP_COULEUR_FOND_PRODUIT_A') <> 'on'}
		{literal}
		.product-information {
			background-color: {/literal}{Configuration::get(MP_COULEUR_FOND_PRODUIT)}{literal};
		}
		{/literal}
	{/if}
	
	{if Configuration::get('MP_COULEUR_JARDIN') && Configuration::get('MP_COULEUR_JARDIN_A') <> 'on'}
		{literal}
		.info_plus_container {
			background-color: {/literal}{Configuration::get(MP_COULEUR_JARDIN)}{literal};
		}
		{/literal}
	{/if}
	
	{if Configuration::get('MP_COULEUR_INFO_PRODUIT') && Configuration::get('MP_COULEUR_INFO_PRODUIT_A') <> 'on'}
		{literal}
		.product-features {
			background-color: {/literal}{Configuration::get(MP_COULEUR_INFO_PRODUIT)}{literal};
		}
		{/literal}
	{/if}
	
	{if Configuration::get('MP_COULEUR_SAVOIR_PLUS') && Configuration::get('MP_COULEUR_SAVOIR_PLUS_A') <> 'on'}
		{literal}
		.product-description {
			background-color: {/literal}{Configuration::get(MP_COULEUR_SAVOIR_PLUS)}{literal};
		}
		{/literal}
	{/if}
				  
</style>
  
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='actus_little'}
  {include file="_partials/actus_little.tpl"}
{/block}





{block name='slogan_2'}
  {if Configuration::get('MP_SLOGAN_2')}
	{assign var='slogan_1' value='/upload/'|cat:Configuration::get('MP_SLOGAN_2')}
	<div class="txt_evenement"><img src="{$slogan_2}"/></div>
  {/if}
{/block}

{block name='logo'}
  {if Configuration::get('MP_LOGO')}
    {assign var="logo_url" value='/upload/'|cat:Configuration::get('MP_LOGO')}
	<img src="{$logo_url}" alt="logo" style="display: none;">
  {/if}
{/block}



{block name='header_nav'}
  <nav class="header-nav">
    <div class="container">
      <div class="row">
        <div class="desktop">
          <div class="col-md-5 col-xs-12">
            {block name='avis_google'}
              {if Configuration::get('MP_AVIS_GOOGLE')}
              {assign var='header_avis_google' value='/upload/'|cat:Configuration::get('MP_AVIS_GOOGLE')}
              <a href="https://customerreviews.google.com/v/merchant?q=labonnegraine.com&c=FR&v=19&hl=fr" target="_blank"><div class="header_avis_google"><img src="{$header_avis_google}" alt="avis google"/></div></a>
              {/if}
            {/block}
            {hook h='displayNav1'}
          </div>
          {block name='slogan_1'}
            {if Configuration::get('MP_SLOGAN_1')}
            {assign var='slogan_1' value='/upload/'|cat:Configuration::get('MP_SLOGAN_1')}
            <div class="slogan"><img src="{$slogan_1}"/></div>
            {/if}
          {/block}
          <div class="col-md-7 right-nav">
              <a type="button" title="Recherche" name="submit_search" class="btn btn-default button-search" href="#" tabindex="1"></a>
              <div class="header_user_info header_user_info_wishlist">
                  <a title="Liste d'envies" href="{$link->getModuleLink('blockwishlist', 'lists', array(), true)|escape:'html':'UTF-8'}">
                  </a>
              </div>
              {hook h='displayNav2'}
          </div>
        </div>
        <div class="mobile">
          <div class="float-xs-left" id="megamenu-icon">
            <i class="material-icons">&#xE5D2;</i>
          </div>
          <div class="float-xs-right _mobile_cart">
			<div class="blockcart cart-preview" data-refresh-url="/module/ps_shoppingcart/ajax">
				<div class="cart-preview-div">
					<a rel="nofollow" href="{$urls.pages.cart}?action=show">
					  <img src="/themes/lbg/assets/img/picto-panier.png" alt="panier">
					</a>
				</div>
			</div>  
		  </div>
		  <a type="button" class="js-button-search-mobile" href="#" tabindex="1">
			<i class="material-icons search" aria-hidden="true">search</i>
		  </a>
          <div class="float-xs-right _mobile_user_info">
			<div class="user-info">
			{if $customer.is_logged}
			  <a
				class="account"
				href="{$urls.pages.my_account}"
				title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
				rel="nofollow"
			  >
				<i class="material-icons hidden-md-up logged">&#xE7FF;</i>
			  </a>
			{else}
			  <a
				href="{$urls.pages.authentication}?back={$urls.current_url|urlencode}"
				title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
				rel="nofollow"
			  >
				<i class="material-icons">&#xE7FF;</i>
			  </a>
			{/if}
		    </div>
		  </div>
          <div class="top-logo mobile">
		    <a href="/">
			  <img class="logo img-fluid" src="/img/logo-1684248802.jpg" alt="La Bonne Graine" width="217" height="213">
		    </a>
		  </div>
		  <div id="aw_bloc_search_absolute">
			<form method="get" action="/recherche">
				<input type="hidden" name="controller" value="search">
				<div class="div_search_input_button"> 
					<input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="Rechercher"> 
					<button type="submit" name="submit_search" class="awsearch_submit">
						<i class="material-icons search" aria-hidden="true">search</i>
					</button>
				</div>
			</form>
		  </div>
		  <div class="slogan-mobile"><img src="{$slogan_1}"/></div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </nav>
{/block}

{block name='header_top'}
  <div class="header-top">
  <div class="Bandeau_Top"></div>
    <div class="container">

       <div class="row">
        <div class="desktop" id="_desktop_logo">
          {if $shop.logo_details}
            {if $page.page_name == 'index'}
                {renderLogo}
            {else}
              {renderLogo}
            {/if}
          {/if}
        </div>
        <div class="header-top-right position-static">
          {hook h='displayTop'}
        </div>
      </div>
      <div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
        <div class="js-top-menu-bottom">
          <div id="_mobile_currency_selector"></div>
          <div id="_mobile_language_selector"></div>
          <div id="_mobile_contact_link"></div>
        </div>
      </div>
    </div>
  </div>
  {hook h='displayNavFullWidth'}
{/block}

