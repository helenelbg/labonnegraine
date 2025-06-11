{extends file='page.tpl'}

{$success = 0}
{if isset($smarty.get.success)}
	{$success = $smarty.get.success}
{/if}
{$error = 0}
{if isset($smarty.get.error)}
	{$error = $smarty.get.error}
{/if}

{block name='page_content'}
	{if $success == 1}
	<div class="cmsNewsletterSuccess">Merci de vous être inscrit à notre newsletter Les Bon Plan(ts). Bienvenue dans notre communauté de jardiniers passionnés.</div>
	{elseif $error == 1}
	<div class="cmsNewsletterError">Nous sommes désolés. Il y a eu une erreur et le formulaire n'a pas pu être envoyé. Merci de réessayer en vérifiant votre email.</div>
	{else}
	<div class="cmsNewsletterPage">
		<img class="cmsNewsletterLogo text-center" src="/themes/lbg/assets/img/la-bonne-graine-1441378319.png">
		<hr>
		<div class="cmsNewsletterSuccess hidden">Un email vient de vous être envoyé pour valider votre inscription.<br>N’oubliez pas de cliquer sur le lien pour recevoir notre prochaine newsletter Les Bons Plan(t)s.</div>
		<div class="cmsNewsletterWrapper">
			<div class="cmsNewsletterTextA text-center">Inscrivez-vous à notre newsletter Les Bons Plan(t)s !</div>
			<form method="post" id="cmsNewsletterForm" class="cmsNewsletterForm">

				<div class="cmsNewsletterTextB">Recevez des informations sur nos dernières nouveautés et sur nos promotions en cours.</div>

				<input class="cmsNewsletterPrenom form-control" type="text" name="prenom" placeholder="Prénom"> 
				<input class="cmsNewsletterNom form-control" type="text" name="nom" placeholder="Nom"> 
				<input class="cmsNewsletterEmail form-control" type="text" name="email" placeholder="Adresse e-mail*" required> 
				
				<div class="cmsNewsletterTextC">* Champ obligatoire</div>
				
				<div>
					<input class="cmsNewsletterRgpd not_uniform comparator" type="checkbox" value='rgpd' name='rgpd' required>
					<div class="cmsNewsletterRgpdText">En cochant cette case, j'accepte que mon adresse mail soit utilisée pour me recontacter dans le cadre de l'inscription aux newsletters. Aucun autre traitement ne sera effectué avec celle-ci. Je comprends que je peux me désabonner à tout moment et facilement.</div>
				</div>
				
				<div style="clear:both"></div>
				
				<div class="cmsNewsletterError hidden">Nous sommes désolés. Il y a eu une erreur et le formulaire n'a pas pu être envoyé. Merci de réessayer en vérifiant votre email.</div>

				<input class="button button-medium" type="submit" name="cmsNewsletterSubmit" id="cmsNewsletterSubmit" value="S'ABONNER">

				
			</form>
			<hr>
				
			<div class="cmsNewsletterTextD">À l'avenir, ne passez plus à côté de ce genre d'offres !</div>
			<img class="cmsNewsletterImg" src="/themes/lbg/assets/img/img-newsletter.jpg">
		</div>
	</div>
	{/if}
{/block}
	