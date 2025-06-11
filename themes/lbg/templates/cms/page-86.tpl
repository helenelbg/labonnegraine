{extends file='page.tpl'}

{block name='page_content'}
	<form  method="post" id="newsletters_form_form" class="std">
		
		<fieldset class="account_creation dni" id="account_creation_4">

			<table class="table_newsletter">

				<h1>Mes Newsletters</h1>

				<h3 class="text-center">Lettres d'informations<span class="span_newsletter_a"><b>"Facultatives mais tellement utiles !"</b></span></h3>
				<tr class="tr_newsletter tr_newsletter_bonsplans">
					<td class="td_newsletter td_newsletter_img td_newsletter_img_bonsplans">
						<img src="/themes/lbg/assets/img/LBG_newsletter_bonsplans.jpg" alt="Smiley face" height="320" width="200">
					</td> 
					<td class="td_newsletter td_newsletter_txt_a">
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
						<p class="text p_newsletter_txt p_newsletter_ir">
							Fréquence d'envoi : maximum 2 par mois
						</p>
					</td>
				</tr>
				<tr class="tr_newsletter_b">
					<td colspan="2">
						<p align="center">
						<span>
							<input type="checkbox" class="checker not_uniform comparator" name="newsletter_bonplan" id="newsletter_bonplan" {if Context::getContext()->customer->optin} checked="checked" {/if} /> </span>
							<label for="newsletter_bonplan" class="newsletter_dossiercyril"><font size="4">{l s='Oui je veux être abonné à cette newsletter, il faudrait être fou pour ne pas en profiter !'} 😉</font></label>
						</p>
					</td>
				</tr>
				
				<tr class="tr_newsletter tr_newsletter_cyril">
					<td class="td_newsletter td_newsletter_txt_b">
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
						<p class="text p_newsletter_txt p_newsletter_ir">
							Fréquence d'envoi : maximum 1 par mois
						</p>
					</td>
					<td class="td_newsletter td_newsletter_img text-right">
						<img src="/themes/lbg/assets/img/LBG_newsletter_cyril.jpg" alt="Smiley face" height="320" width="200">
					</td> 
				</tr>
				<tr class="tr_newsletter_b">
					<td colspan="2">
						<p align="center">
							<input type="checkbox" class="checker not_uniform comparator" name="newsletter_dossiercyril" id="newsletter_dossiercyril" {if Context::getContext()->customer->newsletter} checked="checked"{/if}/>
							<label for="newsletter_dossiercyril" class="newsletter_dossiercyril";"><font size="4">Oui je veux que Cyril m’accompagne dans mon jardin, je me sentirai moins seul ! 😉</font></label>
						</p>
					</td>
				</tr>
				
			</table>

			<p id="newsletters_submit">
				<input type="submit" name="submitNewsletters" id="submitNewsletters" value="{l s='Sauvegarder'}" class="exclusive" />
			</p>
			
		</fieldset>
		
		<a class="newsletter_back_button" href="/mon-compte" title="Accueil"><span><i class="icon-chevron-left"></i>Mon Compte</span></a>

	</form>
{/block}
