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
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Your account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}

	{if isset($smarty.get.create)}
	<div class="modal_box">
		<div class="modal_content">
			<span class="modal_close">&times;</span>	
			<fieldset class="account_creation dni" id="account_creation_4">
				<h3>{l s='Lettres d\'informations'}<span style="font-style: italic; color: red; font-size: 18px; display: block; margin-top: 15px;"><b>"Facultatives mais tellement utiles !"</b></span></h3>
				
				<table class="table_newsletter">
					<tr class="tr_newsletter tr_newsletter_bonsplans">
						<td class="td_newsletter td_newsletter_img td_newsletter_img_bonsplans">
							<img src="/themes/lbg/assets/img/LBG_newsletter_bonsplans.jpg" alt="Smiley face" height="320" width="200">
						</td> 
						<td class="td_newsletter td_newsletter_txt td_newsletter_txt_a">
							<p class="text p_newsletter_txt">
								La newsletter "Les Bons plan(t)s de la Bonne Graine" vous proposera des <b>offres <u>totalement exclusives aux abonnés</u>, membres de la communauté La Bonne Graine</b>, et vous indiquera les produits de saison, ceux qui s'arrêtent et ceux qui vont arriver afin de ne pas passer à côté de la bonne période de semis ou de plantation !
							</p>
							<p class="text p_newsletter_txt">
								La Bonne Graine, c’est déjà des produits de qualité à des prix abordables toute l’année. 
								Mais, si vous en voulez plus et souhaitez bénéficier d’offres exclusives, une seule solution :
							</p>
							<p class="text p_newsletter_txt">
								<b>Abonnez-vous à la newsletter “Les Bons Plan(t)s de La Bonne Graine” !</b>
							</p>
							<p class="text p_newsletter_txt">
								Rendez jaloux vos voisins, s'ils ne sont pas abonnés, ils n'y auront pas droit !
							</p>
							<p class="text p_newsletter_txt" style="font-style: italic; color: red;">
								Fréquence d'envoi : maximum 2 par mois
							</p>
						</td>
					</tr>
					<tr class="tr_newsletter_b">
						<td>
							<p align="center">
								<input type="checkbox" class="checker" name="newsletter_bonplan" id="newsletter_bonplan" {if isset($smarty.post.newsletter_bonplan) AND $smarty.post.newsletter_bonplan == 'on'} checked="checked"{/if}/>
								<label for="newsletter_bonplan" class="newsletter_dossiercyril"><font size="4">{l s='Oui je veux être abonné à cette newsletter, il faudrait être fou pour ne pas en profiter !'} 😉</font></label>
							</p>
						</td>
					</tr>
					
					<tr class="tr_newsletter tr_newsletter_cyril" style="margin-top: 20px;">
						<td class="td_newsletter td_newsletter_txt td_newsletter_txt_b">
							<p class="text p_newsletter_txt">
								La Bonne Graine vous présente sa nouvelle recrue : Cyril, notre nouveau jardinier en chef !
							</p>
							<p class="text p_newsletter_txt">
								Si vous doutez de vos compétences, Cyril va vous accompagner dans les bonnes pratiques au potager grâce à son stock de dossiers accumulés au fil du temps.
							</p>
							<p class="text p_newsletter_txt">
								Dans "Les Dossiers de Cyril", vous retrouverez <b>des astuces, des tutoriels des retours d'expérience qui feront de vous un expert du jardinage</b>.
							</p>
							<p class="text p_newsletter_txt">
								Et parce que les légumes, ça se mange, <b>Cyril vous proposera également des recettes pour profiter pleinement de votre récolte</b>.
							</p>
							<p class="text p_newsletter_txt" style="font-style: italic; color: red;">
								Fréquence d'envoi : maximum 1 par mois
							</p>
						</td>
						<td class="td_newsletter td_newsletter_img td_newsletter_img_cyril">
							<img src="/themes/lbg/assets/img/LBG_newsletter_cyril.jpg" alt="Smiley face" height="320" width="200">
						</td> 
					</tr>
					<tr class="tr_newsletter_b">
						<td>
							<p align="center">
								<input type="checkbox" class="checker" name="newsletter_dossiercyril" id="newsletter_dossiercyril" {if isset($smarty.post.newsletter_dossiercyril) AND $smarty.post.newsletter_dossiercyril == 'on'} checked="checked"{/if}/>
								<label for="newsletter_dossiercyril" class="newsletter_dossiercyril";"><font size="4">{l s='Oui je veux que Cyril m’accompagne dans mon jardin, je me sentirai moins seul !'} 😉</font></label>
							</p>
						</td>
					</tr>
				</table>
				
				<input type="button" name="account_creation_newsletter" id="account_creation_newsletter" value="{l s='Valider'}" class="exclusive" />

			</fieldset>
			
		</div>
	</div>

  {/if}

  <div class="row">
    <div class="links">

      <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="identity-link" href="{$urls.pages.identity}">
        <span class="link-item">
          <i class="material-icons">&#xE853;</i>
          {l s='Information' d='Shop.Theme.Customeraccount'}
        </span>
      </a>
	  
	  {if !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="history-link" href="{$urls.pages.history}">
          <span class="link-item">
            <i class="material-icons">&#xE916;</i>
            {l s='Order history and details' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}

      {if $customer.addresses|count}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="addresses-link" href="{$urls.pages.addresses}">
          <span class="link-item">
            <i class="material-icons">&#xE56A;</i>
            {l s='Addresses' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {else}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="address-link" href="{$urls.pages.address}">
          <span class="link-item">
            <i class="material-icons">&#xE567;</i>
            {l s='Add first address' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}
	  
			  
		
		
		{if $configuration.voucher_enabled && !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="discounts-link" href="{$urls.pages.discount}">
          <span class="link-item">
            <i class="material-icons">&#xE54E;</i>
            {l s='Vouchers' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}


		<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="psgdpr-link" href="/module/psgdpr/gdpr">
			<span class="link-item">
				<i class="material-icons">account_box</i> Mes données personnelles
			</span>
		</a>
		
      <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="wishlist-link" href="/module/blockwishlist/lists">
		  <span class="link-item">
			<i class="material-icons">favorite</i>
			Mes listes d'envies
		  </span>
		</a>

<a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="newsletters-link" href="/content/86-newsletters">
        <span class="link-item">
          <i class="icon-envelope"></i>
          Mes Newsletters
        </span>
      </a>
	  
      {if $configuration.return_enabled && !$configuration.is_catalog}
        <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="returns-link" href="{$urls.pages.order_follow}">
          <span class="link-item">
            <i class="material-icons">&#xE860;</i>
            {l s='Merchandise returns' d='Shop.Theme.Customeraccount'}
          </span>
        </a>
      {/if}
	  
	  
	  
	  <a href="/how-to-refer-friends" class="col-lg-4 col-md-6 col-sm-6 col-xs-12 eam-box-featured">
	  <span class="link-item">
	    <i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 1248v320q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h96v-192h-512v192h96q40 0 68 28t28 68v320q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h96v-192h-512v192h96q40 0 68 28t28 68v320q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h96v-192q0-52 38-90t90-38h512v-192h-96q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h320q40 0 68 28t28 68v320q0 40-28 68t-68 28h-96v192h512q52 0 90 38t38 90v192h96q40 0 68 28t28 68z"></path></svg></i>
	    Programme de parrainage 
        <p class="desc">Parrainer des amis pour obtenir une récompense</p>
	  </span>
	</a>

	{if isset($aff_cyril) && $aff_cyril eq "true"}
	    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12" href="/mon-assistant">
          <span class="link-item">
            <i class="icyril"></i>
            Mes cultures assistées avec Cyril
          </span>
        </a>
	  {/if}
	  
      {block name='display_customer_account'}
        {hook h='displayCustomerAccount'}
      {/block}

    </div>
  </div>
{/block}


{block name='page_footer'}
  {block name='my_account_links'}
    <div class="text-sm-center">
      <a href="{$urls.actions.logout}" >
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
    </div>
  {/block}
{/block}
