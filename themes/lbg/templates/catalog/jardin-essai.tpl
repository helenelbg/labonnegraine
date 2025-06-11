{$jardin_titre = Product::getJardinTitre($product.id_product)}
{$jardin_contenu = Product::getJardinContenu($product.id_product)}
{$type_encart = Product::getTypeEncart($product.id_product)}

{if $type_encart == 1}
{if $jardin_titre || $jardin_contenu}
	  <div class="info_plus_container">
		<h2 class="info_plus_title"><span class="info_jardin_b">En direct du</span> <span class="info_jardin_c">Jardin d'Essai</span></h2>
		<div id="div_titre_plus"><h2>{$jardin_titre}</h2></div>
		<div id="div_contenu_plus">{$jardin_contenu nofilter}</div>
		<h3 class="info_plus_address"><a href="https://www.jardin-essai.com" target="_blank">www.jardin-essai.com</a></h3>
	  </div>
  {/if}
{else}
	{if $jardin_titre || $jardin_contenu}
		<div class="info_plus_container">
		  <h2 class="info_plus_title2"><img src="/img/coupcoeur-olivier.png" class="visuel_olivier" /><span class="info_jardin_b">Le coup de coeur </span> <span class="info_jardin_c">d'Olivier</span></h2>
		  <div id="div_titre_plus"><h2>{$jardin_titre}</h2></div>
		  <div id="div_contenu_plus">{$jardin_contenu nofilter}</div>
		  <h3 class="info_plus_address2"><a href="https://www.youtube.com/@LepotagerdOlivier" target="_blank">www.youtube.com/@LepotagerdOlivier</a></h3>
		</div>
	{/if}
{/if}