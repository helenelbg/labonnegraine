<?php
if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';
include_once 'util.php';

try {
       $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
       die("probleme connexion serveur" . $ex->getMessage());
}

$id_commande = $_GET['id_cmd'];	
$now = date('YmdHi');


// mails templates français et anglais
$mail_template_before = array();
$mail_template_after = array();

$mail_template_before['fr'] = '
Bonjour,<br>
Veuillez trouver ci-dessous une nouvelle commande pour l\'entreprise La Bonne Graine.
<br>
<br>
<table class="table_mail" style="border-collapse: collapse;">
	<thead>
		<tr>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">
				Nom du produit
			</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">
				Quantité à commander
			</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">
				Unité de mesure
			</th>
		</tr>
	</thead>
	<tbody class="js-tbody">
';

$mail_template_after['fr'] = '
	</tbody>
</table>
<br>
Merci de m\'adresser un devis chiffré accompagné d\'une date de disponibilité des produits.
<br><br>
Vous souhaitant une agréable journée,
<br><br>
Stéphane Di Palma
<br>
La Bonne Graine
<br>
<br>';

$mail_template_before['en'] = '
Good morning,<br>
Please find below a new order for the company La Bonne Graine :
<br>
<br>
<table class="table_mail" style="border-collapse: collapse;">
	<thead>
		<tr>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">
				Name
			</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">
				Quantity
			</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">
				Unit
			</th>
		</tr>
	</thead>
	<tbody class="js-tbody">
';

$mail_template_after['en'] = '
	</tbody>
</table>
<br>
Thank you for sending me a cost estimate accompanied by a date of availability of the products.
<br><br>
Best regards,
<br><br>
Stéphane Di Palma
<br>
La Bonne Graine
<br>
<br>';

if (isset($_POST['mail_auto']) || isset($_POST['mail_manuel'])) {

	$id_etat = 2; // Commande envoyée au fournisseur (La liste des états se trouve dans la table cmd_fournisseur_etat)

	$now_date = date('Y-m-d');

	// update de la commande en BDD
	$sql = 'UPDATE cmd_fournisseur_detail SET id_etat = '.pSQL($id_etat).', date_update = "'.$now_date.'" WHERE id_cmd = '.pSQL($id_commande);
	$req = Db::getInstance()->Execute($sql);
	
}

// On récupère les produits de la commande fournisseur depuis la BDD
$sql = 'SELECT fd.id_detail, fd.id_produit, fd.qte, fd.qte_recue, fd.unite, fd.id_etat, fd.lot_cree, fd.date_reliquat, p.reference, ps.wholesale_price as prix_achat, pl.name FROM cmd_fournisseur_detail fd
INNER JOIN ps_product p ON p.id_product = fd.id_produit
INNER JOIN ps_product_shop ps ON ps.id_product = fd.id_produit
INNER JOIN ps_product_lang pl ON pl.id_product = fd.id_produit
WHERE pl.id_lang = 1
AND fd.id_cmd = '.pSQL($id_commande).';';
$produits = Db::getInstance()->ExecuteS($sql);

// On récupère les informations de la commande fournisseur depuis la BDD
$sql = 'SELECT f.id_cmd, f.id_fournisseur, f.date, s.name, s.lang, s.email FROM cmd_fournisseur f
INNER JOIN ps_supplier s ON s.id_supplier = f.id_fournisseur
WHERE f.id_cmd = '.pSQL($id_commande).';';
$commandes = Db::getInstance()->ExecuteS($sql);
$commande = $commandes[0];
$fournisseur = $commande['name'];
$id_fournisseur = $commande['id_fournisseur'];
$lang = $commande['lang'];
$email_to = $commande['email'];

// On récupère la liste de états depuis la BDD
$sql = 'SELECT id_etat, libelle_etat, color_etat, ordre_etat FROM cmd_fournisseur_etat
ORDER BY ordre_etat;';
$etats = Db::getInstance()->ExecuteS($sql);

// On récupère tous les produits 
$sql = 'SELECT p.id_product, p.reference, pl.name FROM ps_product p
INNER join ps_product_lang pl ON p.id_product =  pl.id_product
WHERE pl.id_lang = 1
ORDER BY pl.name';
$product_list = Db::getInstance()->ExecuteS($sql);

$tableau = "";

$lang = strtolower($lang);
if(!isset($mail_template_before[$lang])){
	$lang = 'fr'; // langue fr par défaut si le template n'existe pas
}

foreach($produits as $key => $product){
	$reference = $product['reference'];
	$id_product = $product['id_produit'];
	$qte = $product['qte'];
	$unite = $product['unite'];
	$name = $product['name'];
	if($lang == 'en' && $unite=="gramme"){
		$unite = "gram";
	}elseif($lang == 'en' && $unite=="grammes"){
		$unite = "grams";
	}elseif($lang == 'en' && $unite=="graine"){
		$unite = "seed";
	}elseif($lang == 'en' && $unite=="graines"){
		$unite = "seeds";
	}
	if($qte){
		$tableau .= '<tr><td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$name.'</td><td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$qte.'</td><td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$unite.'</td></tr>';
	}
}
		
if (isset($_POST['mail_auto']) || isset($_POST['mail_manuel'])) {
	$mail_auto = false;
	if (isset($_POST['mail_auto'])){
		$mail_auto = true;
	}

	if($mail_auto){
		// envoi un mail au fournisseur
		
		$sujet = 'La Bonne Graine : nouvelle commmande';
		if($lang == 'en'){
			$sujet = 'La Bonne Graine : new order';
		}
		$message = $mail_template_before[$lang].$tableau.$mail_template_after[$lang];

		$email_bcc = 'stephane.dipalma@gmail.com';
		if($_SERVER['HTTP_HOST'] == "dev.labonnegraine.com"){
			$email_bcc = 'dorian@berry-web.com';
			$message .= "Ceci est un email de test";
		}

		if($email_to){
			require_once '../Swift/swift_required.php';
			$transport = Swift_SmtpTransport::newInstance('in-v3.mailjet.com',587)
			->setUsername('34b10e378c3e0fa97459c5c143f5ec58')
			->setPassword('548160ec9d9e64da604c578c68636f08');
			Swift_Preferences::getInstance()->setCharset('utf-8');
			$mailer = Swift_Mailer::newInstance($transport);
			$messageEnvoi = Swift_Message::newInstance($sujet)
			->setFrom('info@labonnegraine.com') 
			->setTo($email_to)
			->setBcc($email_bcc)
			->setBody($message, "text/html");
			$result = $mailer->send($messageEnvoi);
		}else{
			// TODO afficher une alerte "email introuvable"
		}
	}
	
}









echo '<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>La Bonne Graine - Commandes fournisseur</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="style.css">
  
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="tablesorter-master/dist/js/jquery.tablesorter.min.js"></script>
  <link href="tablesorter-master/dist/css/theme.default.min.css" rel="stylesheet">
  
  <link href="select2/dist/css/select2.min.css" rel="stylesheet" />
  <script src="select2/dist/js/select2.min.js"></script>
  <script src="unpkg/imask.js"></script>
  
  <script>
	$(document).ready(function(){
		$("#myTable").tablesorter();
		
		$(".js-product-content").select2({dropdownAutoWidth : true});
		
		// On désactive le bouton "Mail manuel / auto" quand on clique pour éviter d\'envoyer deux fois

		$("input[name=mail_auto]").on("click", function(e){
			$("input[name=mail_auto]").hide();
			$("input[name=mail_manuel]").hide();
		});

		$("input[name=mail_manuel]").on("click", function(e){
			$("input[name=mail_auto]").hide();
			$("input[name=mail_manuel]").hide();
		});
					
		$("body").on("click", ".js-submit-lot", function() {
			
			var token = $(".js-token").val();
			var id_product = $(".modal_box .js-id-product").val();
			var id_commande = $(".js-id-commande").val(); 
			var qte = $(".modal_box .js-qte-integr").val();
			var unite = $(".modal_box .js-unite-b").html();
			var date_germination = $(".modal_box .js-date-germination").val();
			var pourcentage_germination = $(".modal_box .js-pourcentage-germination").val();
			var fournisseur = $(".js-fournisseur").val();
			var numero_lot_origine = $(".js-lot-origine").val();
			var date_approvisionnement = $(".js-date-approvisionnement").val();
			var numero_lot_LBG = $(".js-lot-lbg").val();
			var commentaire = "";
	
			$.ajax({
			  url: "ajax_lot.php",
			  type: "POST",
			  data: {
				"action": "creer_lot",
				"token" : token,
				"id_product": id_product,
				"id_commande": id_commande,
				"numero_lot_origine": numero_lot_origine,
				"date_approvisionnement": date_approvisionnement,
				"numero_lot_LBG": numero_lot_LBG,
				"commentaire": commentaire,
				"qte": qte,
				"unite": unite,
				"date_germination": date_germination,
				"pourcentage_germination": pourcentage_germination,
				"fournisseur": fournisseur
				
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			  // console.log(res["sql"]);
			  // TODO afficher un message d\'erreur ou un message de confirmation 
			  $("td[data-id-product="+id_product+"]").html("Lot crée");
			  $("tr[data-id-product="+id_product+"] .js-etat").val(6); // change l état en produit reçu
			  $(".modal_box").hide();
			});
		});
		
		
		// modal
		
		$("body").on("click", ".modal_close", function(e) {
			$(".modal_box").hide();
			$(".modal_product").hide();
			$(".modal_mail").hide();
		});
		
		$("body").on("click", ".modal_content", function(e) {
			 e.stopPropagation();
		});
		
		Date.prototype.toDateInputValue = (function() {
			var local = new Date(this);
			local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
			return local.toJSON().slice(0,10);
		});
				
		$("body").on("click", ".modal_button_open", function(e) {
			 // au clic sur le bouton "Créer lot"
			 e.stopPropagation();
			
			var tr = $(this).closest("tr"); // on retrouve la ligne correspondante
			var id_product = $(this).data("id-product");
			var reference = $(".js-ref",tr).html();
			var name = $(".js-name",tr).html();
			var qte_recue = $(".js-qte-demandee",tr).val();
			var conversion = $(".js-conversion",tr).attr("data-conversion");
			var unite_achat = $(".js-qte-demandee",tr).attr("data-unite-achat");
			var unite_vente = $(".js-qte-demandee",tr).attr("data-unite-vente");
			var numero_lot_LBG = $(".js-ref",tr).data("numero-lot-lbg");
			var quantite = qte_recue;
			quantite = quantite.replace(" ", ""); // supprime les espaces
			var conversion_int = parseInt(conversion);
			
			if(unite_achat == "kg"){
				// conversion kg => gramme
				quantite = quantite * 1000;
			}
			
			if(conversion_int && Number.isInteger(conversion_int)){
				if(unite_vente == "gramme" && unite_achat == "graines"){
					// graine en gramme
					quantite = quantite / conversion_int;
					quantite = parseInt(quantite);
				}else if(unite_vente == "graine" && unite_achat == "kg"){
					// cas des tomates
					// kg en graine
					quantite = quantite * conversion_int;
					quantite = parseInt(quantite);
				}
			}
				
			$(".modal_box .js-id-product").val(id_product);
			$(".modal_box .js-reference").html(reference);
			$(".modal_box .js-name").html(name);
			$(".modal_box .js-qte-recue").val(qte_recue);
			$(".modal_box .js-qte-integr").val(quantite);
			$(".modal_box .js-graine-gramme").val(conversion);
			$(".modal_box .js-unite").val(unite_achat);
			$(".modal_box .js-unite-a").html(unite_achat);
			$(".modal_box .js-unite-b").html(unite_vente+"s");
			
			// on reset les anciennes valeurs
			$(".js-lot-origine").val("");
			$(".js-date-approvisionnement").val(new Date().toDateInputValue()); // date du jour
			$(".js-lot-lbg").val(numero_lot_LBG);
			
			
			
			if(
				((unite_vente == "gramme" && unite_achat == "graines") || (unite_vente == "graine" && unite_achat == "kg")) &&
				(!conversion_int || !Number.isInteger(conversion_int))
			){
				$(".js-submit-lot").hide();
				$(".js-warning-lot").show();
			}else{
				$(".js-submit-lot").show();
				$(".js-warning-lot").hide();
			}

			$(".modal_box").show();
			 
		});
		
		$("body").on("click", ".js-add-product", function(e) {
			 // au clic sur le bouton "Ajouter un produit"
			 e.stopPropagation();
			
			// on reset les anciennes valeurs
			$(".js-product-content").val("");
			
			$(".modal_product").show();
			 
		});	
		
		$("body").on("click", ".js-remove-product", function(e) {
			 // au clic sur le bouton "Supprimer le produit"
			 e.stopPropagation();
			
			var token = $(".js-token").val();
			var id_commande = $(".js-id-commande").val(); 
			var tr = $(this).closest("tr"); // on retrouve la ligne correspondante
			var id_detail = $(this).data("id-detail");
			var id_product = $(this).data("id-product");
			var name = $(".js-name",tr).html();
			
			var r = confirm("Supprimer le produit "+name+" ?");
			if (r == true) {
				$.ajax({
				  url: "ajax_remove_product.php",
				  type: "POST",
				  data: {
					"action": "supprimer_produit",
					"token" : token,
					"id_product": id_product,
					"id_detail": id_detail,
					"id_commande": id_commande,
				  }
				}).done(function(response) {
				  res = JSON.parse(response);
				  window.location.href = window.location.href
				});
			
			} 
		});	
		
		
		
		$("body").on("click", ".js-submit-new-product", function(e) {
			var token = $(".js-token").val();
			var id_product = $(".modal_product .js-product-content").val();
			var unite = $(".modal_product .js-product-unite").val();
			var qte = $(".modal_product .js-product-qte").val();
			var id_commande = $(".js-id-commande").val(); 
			
			$.ajax({
			  url: "ajax_add_product.php",
			  type: "POST",
			  data: {
				"action": "ajout_produit",
				"token" : token,
				"id_product": id_product,
				"id_commande": id_commande,
				"unite": unite,
				"qte": qte,
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			  window.location.href = window.location.href
			});
		});
		
		// coche ou décoche toutes les checkboxes
		$("input[type=checkbox].check_all").on("click", function(e){
			var checked = $(this).is(":checked");
			$(".js-input-checkbox").prop( "checked", checked );
		});
					
		// change tous les états
		$(".js-all-etat").on("change", function() {
			var value = $(this).val();
			if(value != "0"){
				$(".tr-line .js-input-checkbox:checked").each(function() {
					var tr = $(this).closest("tr");
					$(".js-etat",tr).val(value); 
					$(tr).attr("data-etat",value); 
					ajax_change_etat(this);	
				});
			}
		});
		
		// au changement d\'état
		$(".js-etat").on("change", function() {
			var value = $(this).val();
			var tr = $(this).closest("tr");
			$(tr).attr("data-etat",value); 
			ajax_change_etat(this);	
		});
		
		$(".js-prix-achat").on("change", function() {
			var token = $(".js-token").val();
			var prix_achat = $(this).val();
			var tr = $(this).closest("tr");
			var id_detail = $(tr).attr("data-id-detail");
			var id_product = $(tr).attr("data-id-product");
			var unite_achat = $(".js-qte-demandee",tr).attr("data-unite-achat");
			
			$.ajax({
			  url: "ajax_produits_fournisseur.php",
			  type: "POST",
			  data: {
				"action": "change_prix",
				"token" : token,
				"id_detail": id_detail,
				"id_product": id_product,
				"prix_achat" : prix_achat,
				"unite_achat": unite_achat
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			});
		});
		
		function ajax_change_quantite(e){
			var token = $(".js-token").val();
			var tr = $(e).closest("tr");
			var qte_demandee = $(".js-qte-demandee",tr).val();
			qte_demandee = qte_demandee.replace(/\s+/g, ""); // supprime les espaces dûes au masque de saisie
			var id_commande = $(".js-id-commande").val(); 
			var id_detail = $(tr).attr("data-id-detail");
			var id_product = $(tr).attr("data-id-product");
			var unite_achat = $(".js-qte-demandee",tr).attr("data-unite-achat");

			$.ajax({
			  url: "ajax_produits_fournisseur.php",
			  type: "POST",
			  data: {
				"action": "change_quantite",
				"token" : token,
				"id_detail": id_detail,
				"id_product": id_product,
				"id_commande": id_commande,
				"qte_demandee" : qte_demandee,
				"unite_achat": unite_achat
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			});
		}
		
		function insertThousandsSeparators(value) {
	      // https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript
		  value = value + "";
	      var parts = value.split(".");
	      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
	      return parts.join(".");
	    }
		
		$(".js-conversion").on("click", function() {
			// conversion graine-gramme
			
			var tr = $(this).closest("tr");
			var quantite = $(".js-qte-demandee",tr).val();
			quantite = quantite.replace(/\s+/g, ""); // supprime les espaces dûes au masque de saisie

			var prix_achat = $(".js-prix-achat",tr).val();
			var unite_achat = $(".js-qte-demandee",tr).attr("data-unite-achat");
			
			// conversion
			var conversion = $(".js-conversion",tr).attr("data-conversion");
			conversion = parseInt(conversion);
			if(conversion && Number.isInteger(conversion)){
				if(unite_achat == "kg"){
					unite_achat = "graines";
					quantite = parseFloat(quantite) * 1000 * conversion;
					prix_achat = parseFloat(prix_achat) / conversion;
					quantite = parseInt(quantite);
					prix_achat = prix_achat.toFixed(2);
				}else if(unite_achat == "graines"){
					unite_achat = "kg";
					quantite = parseFloat(quantite) * 0.001 / conversion;
					prix_achat = parseFloat(prix_achat) * conversion;
					quantite = quantite.toFixed(3);
					prix_achat = prix_achat.toFixed(2);
				}
			}
			
			// masque de saisie
			quantite = insertThousandsSeparators(quantite);
			
			// on modifie le DOM avec les nouvelles valeurs
						
			$(".js-qte-demandee",tr).val(quantite);
			$(".js-prix-achat",tr).val(prix_achat);
			$(".js-qte-demandee",tr).attr("data-unite-achat",unite_achat);
			$(".js-unite-achat",tr).html(unite_achat);
			
			// ajax pour sauvegarder en BDD :
			// - la quantité après conversion 
			// - la nouvelle unité d\'achat
			
			ajax_change_quantite(this);	
		});
		
		$(".js-qte-demandee").on("change", function() {
			ajax_change_quantite(this);	
		});
		
		$(".date_reliquat").on("change", function() {
			var token = $(".js-token").val();
			var tr = $(this).closest("tr");
			var id_commande = $(".js-id-commande").val(); 
			var id_detail = $(tr).attr("data-id-detail");
			var id_product = $(tr).attr("data-id-product");
			var date_reliquat = $(this).val();
			
			$.ajax({
			  url: "ajax_produits_fournisseur.php",
			  type: "POST",
			  data: {
				"action": "change_date_reliquat",
				"token" : token,
				"id_commande": id_commande,
				"id_detail": id_detail,
				"id_product": id_product,
				"date_reliquat": date_reliquat
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			});
		});
		
		function ajax_change_etat(e) {
			var token = $(".js-token").val();
			var tr = $(e).closest("tr");
			var id_commande = $(".js-id-commande").val(); 
			var id_detail = $(tr).attr("data-id-detail");
			var id_product = $(tr).attr("data-id-product");
			var id_etat = $(".js-etat",tr).val();
			
			$.ajax({
			  url: "ajax_produits_fournisseur.php",
			  type: "POST",
			  data: {
				"action": "change_etat",
				"token" : token,
				"id_commande": id_commande,
				"id_product": id_product,
				"id_detail": id_detail,
				"id_etat": id_etat
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			});
		}
		
		// masque de saisie
		var items = document.getElementsByClassName("js-qte-demandee");
		Array.prototype.forEach.call(items, function(element) {
			var numberMask = new IMask(element, {
				mask: Number,
				thousandsSeparator: " ",
				radix: ".",
				scale: 3
			});
		});
		
		
	});
  </script>

  <style>
  .ui-widget {
	font-family: \'Open Sans\', sans-serif !important;
  }
  
	.modal_box {
	  display: none;
	  position: fixed;
	  z-index: 15;
	  left: 0;
	  top: 0;
	  width: 100%;
	  height: 100%;
	  overflow: auto;
	  background-color: rgb(0,0,0);
	  background-color: rgba(0,0,0,0.4);
	}
	
	.modal_product {
	  display: none;
	  position: fixed;
	  z-index: 15;
	  left: 0;
	  top: 0;
	  width: 100%;
	  height: 100%;
	  overflow: auto;
	  background-color: rgb(0,0,0);
	  background-color: rgba(0,0,0,0.4);
	}

	.modal_mail {
	  position: fixed;
	  z-index: 15;
	  left: 0;
	  top: 0;
	  width: 100%;
	  height: 100%;
	  overflow: auto;
	  background-color: rgb(0,0,0);
	  background-color: rgba(0,0,0,0.4);
	}
	
	.modal_content {
	  background-color: #fefefe;
	  margin: 15% auto;
	  padding: 20px;
	  border: 1px solid #888;
	  width: 80%;
	  font-size: 18px;
	}

	.modal_close {
	  color: #aaa;
	  float: right;
	  font-size: 28px;
	  font-weight: bold;
	  cursor: pointer;
	  margin-top: -20px;
	  margin-right: 5px;
	}

	.modal_close:hover,
	.modal_close:focus {
	  color: black;
	  text-decoration: none;
	}
	
	.date_reliquat {
		display:none;
	}
	
	tr[data-etat="4"] .date_reliquat {
		display:inline-block;
	}
	
	.modal_button_open {
		display:none;
	}
	
	tr[data-etat="3"] .modal_button_open {
		display:block;
	}
	
	.tablesorter-default tbody>tr.even:hover>td, .tablesorter-default tbody>tr.hover>td, .tablesorter-default tbody>tr.odd:hover>td, .tablesorter-default tbody>tr:hover>td {
		background-color: #2196f3;
	}
	
	.modal_product .select2 {
		width: 500px;
	}
	
	.table_mail{
		border-collapse: collapse;
	}

	.table_mail th, .table_mail td{
		border: 1px solid #7A7A7A;
		padding: 2px 10px;
	}
	
	.js-conversion{
		font-size: 25px;
		cursor: pointer;
	}
	
  </style>
</head>
<body style="font-family: \'Open Sans\', sans-serif !important;">';

echo '

<input class="js-token" type="hidden" value="'.$_GET['token'].'">
<input class="js-id-commande" type="hidden" value="'.$commande['id_cmd'].'">
<input class="js-fournisseur" type="hidden" value="'.$fournisseur.'">

<h1 style="text-align: center;"><a href="approvisionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Commande '.$commande['id_cmd'].' '.$fournisseur.'</h1>';

$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo '<div style="margin-bottom:15px"><a href="commandes_fournisseur.php?token='.$_GET['token'].'">Retour</a></div>';

echo '<form action="'.$url.'" method="post" enctype="multipart/form-data"><div>';

echo '<input type="submit" name="envoi_mail" value="Envoi mail" />';

echo '<table id="myTable" style="border: 0px; padding: 0px;" width="100%">
		<thead>
		<tr>
				<th style="text-align:left">Réference</th>
				<th style="text-align:left" class="sorter-false"><input type="checkbox" class="check_all"></th>
				<th style="text-align:left">Nom</th>
				<th style="text-align:left" class="sorter-false">Etat
				<select style="margin-left:8px" class="js-all-etat" name="etat_">
				<option value="0">Changer tous les états</option>';
				foreach($etats as $etat){
					echo '<option value="'.$etat['id_etat'].'">
						'.$etat['libelle_etat'].'
					</option>';
				}
				echo '</select>
				</th>
				<th style="text-align:left">Prix d\'achat</th>
				<th style="text-align:left">Quantité demandée</th>
				<th style="text-align:left" class="sorter-false"></th>
				<th style="text-align:left" class="sorter-false"></th>
				<th style="text-align:left" class="sorter-false"></th>
			</tr>
		</thead>
		<tbody>';
		
foreach ($produits as $produit){
	
	$produit['conversion'] = conversion($produit['id_produit']);
	
	$produit['prix_achat'] = number_format($produit['prix_achat'], 2, '.', '');
	
	$unite_vente = get_unite_vente($produit['id_produit']);
	$unite_achat = $produit['unite'];
	$conversion = $produit['conversion'];
	
	// conversion du prix d'achat si l'unité d'achat est différente de l'unité de vente
	// TODO à vérifier

	if($unite_vente == 'gramme' && $unite_achat == 'graines'){
		if($conversion != 0){
			$produit['prix_achat'] = (float) $produit['prix_achat'] / (float) $conversion;
			$produit['prix_achat'] = number_format($produit['prix_achat'], 2, '.', '');
		}
	}else if($unite_vente == 'graine' && $unite_achat == 'kg'){
		if($conversion != 0){
			$produit['prix_achat'] = (float) $produit['prix_achat'] * (float) $conversion;
			$produit['prix_achat'] = number_format($produit['prix_achat'], 2, '.', '');
		}
	}
	
	// On récupère le numéro du lot LBG
	$sql = 'SELECT numero_lot_LBG FROM ps_inventaire_lots
	WHERE id_product = '.pSQL($produit['id_produit']).'
	ORDER BY `numero_lot_LBG` DESC LIMIT 0,1';
	$res = Db::getInstance()->ExecuteS($sql);
	$numero_lot_LBG = $res[0]['numero_lot_LBG']; 
	
	$sql = 'SELECT upc FROM ps_product
	WHERE id_product = '.pSQL($produit['id_produit']);
	$res = Db::getInstance()->ExecuteS($sql);
	$nomenclature = $res[0]['upc']; 	
	
	$annee = substr($numero_lot_LBG, -4, 2);
	$increment = substr($numero_lot_LBG, -2, 2);
	$cette_annee = date("y");
	$cet_increment = "01";
	if($cette_annee == $annee){
		$cet_increment = (int)$increment + 1;
		$cet_increment = str_pad($cet_increment, 2, '0', STR_PAD_LEFT);
	}
	$numero_lot_LBG = $nomenclature.$cette_annee.$cet_increment;

	$qte_recue = $produit['qte_recue'];
	if(!$qte_recue){
		// on met quantité = quantité reçue par défaut, sauf si qte_recue != 0
		$qte_recue = $produit['qte'];
	}
	$link = '/admin123/index.php?controller=AdminProducts&id_product='.$produit['id_produit'].'&updateproduct';
	
	echo '<tr class="tr-line" data-id-detail="'.$produit['id_detail'].'" data-id-product="'.$produit['id_produit'].'" data-etat="'.$produit['id_etat'].'" style="height: 30px;">
	
	<td class="js-ref" data-reference="'.$produit['reference'].'" data-numero-lot-lbg="'.$numero_lot_LBG.'" ><a href="'.$link.'" target="_blank">'.$produit['reference'].'</a></td>
	<td class="js-checkbox"><input type="checkbox" class="js-input-checkbox"></td>
	<td class="js-name">'.$produit['name'].'</td>
	<td>
	<select class="js-etat" name="etat_'.$produit['id_produit'].'">';
	foreach($etats as $etat){
		$selected = '';
		if($etat['id_etat'] == $produit['id_etat']){
			$selected = 'selected="selected"';
		}
		echo '<option '.$selected.' value="'.$etat['id_etat'].'">
			'.$etat['libelle_etat'].'
		</option>';
	}
	echo '</select>

	<select class="date_reliquat" name="date_reliquat_'.$produit['id_produit'].'">
		<option value="0000-00-00"> -- </option>';
		$pyear = date("Y", strtotime($produit['date_reliquat']));
		$pmonth = date("m", strtotime($produit['date_reliquat']));
		$year = date('Y');
		$month = date('m');
		for($i=0; $i<12; $i++){
			$selected = '';
			if($pyear == $year && $pmonth == $month){
				$selected = 'selected="selected"';
			}
			$formatted_month = str_pad($month, 2, '0', STR_PAD_LEFT);
			$formatted_date = $year.'-'.$formatted_month.'-01';
			$display_date = date("m / Y", strtotime($formatted_date));
			echo '<option '.$selected.' value="'.$formatted_date.'">
				'.$display_date.'
			</option>';
			$month++;
			if($month > 12){
				$month = 1;
				$year++;
			}
		}
		
		
	
	echo '</select>	

	</td>
		
	<td><input class="js-prix-achat" name="prix_achat_'.$produit['id_produit'].'" value="'.$produit['prix_achat'].'"></td>	
	<td><input class="js-qte-demandee" data-unite-vente="'.$unite_vente.'" data-unite-achat="'.$produit['unite'].'" name="qte_demandee_'.$produit['id_produit'].'" value="'.$produit['qte'].'">
		<span class="js-unite-achat">'.$produit['unite'].'</span>
	</td>
	<td class="js-conversion" data-conversion="'.$produit['conversion'].'" >&#128972;</td>
	<td data-id-product='.$produit['id_produit'].'>';
	if($produit['lot_cree']){
		// on n'affiche pas le bouton pour créer le lot si le lot a déjà été crée
		echo 'Lot crée';
	}else{
		echo '<input data-id-product="'.$produit['id_produit'].'" type="button" class="modal_button_open" value="Créer lot">';
	}
	echo '</td>	
	<td>
		<input data-id-detail="'.$produit['id_detail'].'" data-id-product="'.$produit['id_produit'].'" type="button" class="js-remove-product" value="Supprimer le produit"
	</td>
	</tr>';
			
}

echo '	<div class="modal_box" style="display:none;">
	<div class="modal_content">
		<span class="modal_close">&times;</span>
		<div class="js-box-content">
		
			<input type="hidden" class="js-id-product" value="">
			<input type="hidden" class="js-unite" value="">		
			
			<label>Référence </label><label class="js-reference"></label>
			<label> </label><label class="js-name"></label>
			<br><br>
			
			<label>Numéro lot origine</label>
			<br>
			<input type="text" class="js-lot-origine">
			<br><br>
			
			<label>Date d\'approvisionnement</label>
			<br>
			<input type="date" value="'.date('Y-m-d').'" class="js-date-approvisionnement">
			<br><br>
			
			<label>Numéro lot LBG</label>
			<br>
			<input type="text" class="js-lot-lbg">
			<br><br>
			
			<label>Date de germination</label>
			<br>
			<input type="date" class="js-date-germination" value="">
			<br><br>
			
			<label>Pourcentage germination</label>
			<br>
			<input class="js-pourcentage-germination" value="">
			<br><br>
			
			<label>Quantité commandée</label>
			<br>
			<input class="js-qte-recue" value="" disabled>
			<span class="js-unite-a"></span>
			<br><br>
			
			<label>Nombre de graines au gramme</label>
			<br>
			<input class="js-graine-gramme" value="" disabled>
			<br><br>
			
			<label>Quantité à intégrer au stock</label>
			<br>
			<input class="js-qte-integr" value="" disabled>
			<span class="js-unite-b"></span>
			<br><br>


		</div>
		<div class="js-warning-lot">Attention : impossible de créer un lot car le nombre de graines au gramme est manquant</div>
		<input type="button" class="js-submit-lot" value="Créer lot">
	</div>
</div>	';

$product_list_str = "";
foreach($product_list as $p){
	$product_list_str .= '<option value="'.$p['id_product'].'">'.$p['reference'].' '.$p['name'].'</option>';
}

echo '	<div class="modal_product" style="display:none;">
	<div class="modal_content">
		<span class="modal_close">&times;</span>
		<select class="js-product-content">
			<option value="0"> -- </option>
			'.$product_list_str.'
		</select>
		<br><br>
		
		<label>Quantité </label>
		<input type="text" class="js-product-qte" value="">
		
		<select class="js-product-unite">
			<option value="kg">kg</option>
			<option value="graines">graines</option>
		</select>
		
		<br><br>
		<input type="button" class="js-submit-new-product" value="Ajouter ce produit">
	</div>
</div>	';	


			
echo '		</tbody>
	</table>
	
</div>';

if(isset($_POST['envoi_mail'])){
	echo '	<div class="modal_mail">
		<div class="modal_content">
			<span class="modal_close">&times;</span>
			<div class="js-mail-content">
				'.$mail_template_before[$lang].$tableau.$mail_template_after[$lang].'
			</div>
			<input type="submit" name="mail_manuel" value="Mail manuel">
			<input type="submit" name="mail_auto" value="Mail auto">
		</div>
	</div>	';
}

echo '</form>';

echo '<input class="js-add-product" type="button" value="Ajouter un produit" />';

echo '</body>
</html>';
