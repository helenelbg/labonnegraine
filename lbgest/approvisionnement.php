<?php
if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
       $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
       die("probleme connexion serveur" . $ex->getMessage());
}
// --------------------
function conversion($id_product){
	// conversion graines/grammes
	$conversion = 0;
	$sql = 'SELECT value FROM ps_feature_value_lang v
	INNER JOIN ps_feature_product p ON p.id_feature_value = v.id_feature_value
	WHERE p.id_product = '.pSQL($id_product).'
	AND p.id_feature = 17
	AND v.id_lang = 1';
	$res = Db::getInstance()->executeS($sql);
	if(is_array($res)){
		if(count($res)){
			foreach($res as $r){
				if($r['value']){
					$conversion = $r['value'];
				}
			}
		}
	}
	return $conversion;
}
if (isset($_POST['creer_commande_submit'])) {
	
	$id_etat = 1; // A approvisioner
	
	// on vérifie que le fournisseur est choisi
	$id_fournisseur = $_POST["fourn"];
	// on vérifie qu'au moins un article est sélectionné
	$article = false;
	foreach($_POST as $key => $p){
		if(strpos($key, "check_") === 0){ // pour toutes les clés commançant par "check_"
			$article = true;
			break;
		}
	}
	if($article && $id_fournisseur){
		
		// on vérifie si une commande avec le même fournisseur est en cours
		$commandes_en_cours = get_commandes_en_cours();
		
		$now = date('YmdHi');
		$id_cmd = 0;
		
		// TODO : Peut être à optimiser lorsqu'on aura plusieurs milliers de commandes
		foreach($commandes_en_cours as $c){
			if($c['id_fournisseur'] == $id_fournisseur){
				// si on a déjà une commande chez le même fournisseur, ne pas recréer de commande mais ajouter les produits à la commande en cours
				$id_cmd = $c['id_cmd'];
				break;
			}
		}
		
		// on crée une commande si elle n'existe pas
		if(!$id_cmd){
			$sql = 'INSERT INTO cmd_fournisseur (id_fournisseur,date)
			VALUES ('.pSQL($id_fournisseur).', "'.pSQL($now).'")';
			$req = Db::getInstance()->Execute($sql);
			$id_cmd = Db::getInstance()->Insert_ID();
		}
		
		// inserttion des articles
		foreach($_POST as $key => $p){
			if(strpos($key, "check_") === 0){ // pour toutes les clés commançant par "check_"
				$reference = str_replace("check_", "",$key);
				$unite_achat = $_POST["check_".$reference]; // graines ou kg
				$qte = $_POST["qte_".$unite_achat."_".$reference];
				$unite_vente = $_POST["unite_".$reference]; // graines ou kg
				$id_product = intval($_POST["id_product_".$reference]);
				if($unite_vente == "grammes" || $unite_vente == "gramme"){
					$unite_vente == "kg";
				}elseif($unite_vente == "graine"){
					$unite_vente = "graines";
				}
				
				if($qte){
					$res2 = Db::getInstance()->ExecuteS('SELECT name FROM ps_product_lang
					WHERE id_lang = 1
					AND id_product = "'.pSQL($id_product).'";');
					if(count($res2)){
						$sql = 'INSERT INTO cmd_fournisseur_detail (id_cmd,id_produit,qte,unite,unite_vente,id_etat,date_creation)
						VALUES ('.pSQL($id_cmd).', '.pSQL($id_product).', '.pSQL($qte).', "'.pSQL($unite_achat).'", "'.pSQL($unite_vente).'", '.$id_etat.', '.pSQL($now).')';
						$req = Db::getInstance()->Execute($sql);
						$sql = 'INSERT INTO cmd_fournisseur_historique (id_cmd,id_produit,id_etat,date)
						VALUES ('.pSQL($id_cmd).', '.pSQL($id_product).', '.$id_etat.', '.pSQL($now).')';
						$req = Db::getInstance()->Execute($sql);
					}
				}
				
				// On met à jour la liste des fournisseurs associés à l'article
				$sql = 'SELECT id_product FROM ps_product_supplier
				WHERE id_product = '.pSQL($id_product).'
				AND id_supplier = '.pSQL($id_fournisseur).'';				
				$res = Db::getInstance()->ExecuteS($sql);
				if(!count($res)){
					$id_product_attribute = 0;
					$sql = 'INSERT INTO ps_product_supplier (id_product, id_product_attribute, id_supplier)
					VALUES ('.pSQL($id_product).', '.pSQL($id_product_attribute).', '.pSQL($id_fournisseur).')';
					$req = Db::getInstance()->Execute($sql);
				}
			}
		}
		
		header('Location: /lbgest/produits_fournisseur.php?id_cmd='.$id_cmd.'&token='.$_GET['token']);
		exit;
	}
	
	
}


echo '<html lang="en">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Approvisionnement - LBG</title>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script src="https://unpkg.com/sticky-table-headers"></script>
          <script src="tablesorter-master/dist/js/jquery.tablesorter.min.js"></script>
          <link href="tablesorter-master/dist/css/theme.default.min.css" rel="stylesheet">
          <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
			<script>
				$( function() {
					$("table").stickyTableHeaders();
				} );
			</script>
			<script>
				$(document).ready(function(){
					$("#myTable").tablesorter();
					function change_fournisseur(){
						var value1 = $("#fournisseur1").find("option:selected").text().toLowerCase();
						var value2 = $("#fournisseur2").find("option:selected").text().toLowerCase();
						var value4 = $("#filtre_etat").find("option:selected").text().toLowerCase().trim();
						var condition1 = false;
						var condition2 = false;
						if($("#fournisseur1").val() != "0"){ // champ recherche 1 non vide
							condition1 = true;
						}
						if($("#fournisseur2").val() != "0"){ // champ recherche 2 non vide
							condition2 = true;
						}
						if($("#filtre_etat").val() == "0"){ // champ état vide
							value4 = ""
						}
						if(!condition1 && !condition2){
							// tous les champs fournisseur vides
							value1 = "";
							value2 = "";
							condition1 = true;
							condition2 = true;
						}
						if(!condition1 && !condition2 && !condition4){
							// tous les champs vides = show all
							$("#myTable tbody tr").show();
						}else{
							$("#myTable tbody tr").hide();
							$("#myTable tbody tr").filter(function() {
								$(this).toggle(
									(
										($(".fournisseur1",this).text().toLowerCase().indexOf(value1) > -1 && condition1 ) ||
										($(".fournisseur2",this).text().toLowerCase().indexOf(value2) > -1 && condition2 )
									) && 
									($(".filtre_etat",this).text().toLowerCase().indexOf(value4) > -1 ) 
								)
							});
						}
					}
					$("#fournisseur1").on("change", function() {
						change_fournisseur();
					});
					$("#fournisseur2").on("change", function() {
						change_fournisseur();
					});
					$("#filtre_etat").on("change", function() {
						change_fournisseur();
					});
					
					$("input[type=checkbox].check_all_1").on("click", function(e){
						toggleCheckboxGramme($(this));
					});
					
					$("input[type=checkbox].check_all_2").on("click", function(e){
						toggleCheckboxGraine($(this));
					});
					
					$("input[type=checkbox].checkbox-article-2").on("click", function(e){
						var tr = $(this).closest("tr"); // on retrouve la ligne correspondante
						$(".checkbox-article-1",tr).prop( "checked", false );
					});
					
					$("input[type=checkbox].checkbox-article-1").on("click", function(e){
						var tr = $(this).closest("tr"); // on retrouve la ligne correspondante
						$(".checkbox-article-2",tr).prop( "checked", false );
					});
					function toggleCheckboxGramme(jQueryElement)
					{
						var checked = jQueryElement.is(":checked");
						$("#myTable tbody input[type=checkbox].checkbox-article-1:visible").prop( "checked", checked );
						if(checked){
							$("#myTable tbody input[type=checkbox].checkbox-article-2:visible").prop( "checked", false );
						}
					}
					
					function toggleCheckboxGraine(jQueryElement)
					{
						var checked = jQueryElement.is(":checked");
						$("#myTable tbody input[type=checkbox].checkbox-article-2:visible").prop( "checked", checked );
						if(checked){
							$("#myTable tbody input[type=checkbox].checkbox-article-1:visible").prop( "checked", false );
						}
					}
					// modal
					$("body").on("click", ".creer_commande", function(e) {
						 // au clic sur le bouton "Créer commande fournisseur"
						 e.stopPropagation();
						 var fournisseur = $("select[name=\"fourn\"]").val();
						 if(!fournisseur){
							 alert("Veuillez choisir un fournisseur");
						 }else{
							 // créée la table récapitulative des articles
							 var alertfournisseur = [];
							 $(".checkbox-article:checked").each(function() {
								var ref = $(this).data("ref");
								var tr = $(this).closest("tr"); // on retrouve la ligne correspondante
								var name = $(".js-product-name",tr).html();
								var fournisseur1 = $(".fournisseur1",tr).data("fournisseur");
								var fournisseur2 = $(".fournisseur2",tr).data("fournisseur");
								if(fournisseur != fournisseur1 && fournisseur != fournisseur2){
									var obj = {ref:ref, name:name.trim()};
									alertfournisseur.push(obj);
								}
							});
							for(var i=0; i<alertfournisseur.length; i++){
								var ref = alertfournisseur[i].ref;
								var name = alertfournisseur[i].name;
								var r = confirm("La commande sera envoyée à un fournisseur différent pour le produit "+ref+" "+name);
								if (r == true) {
									
								}else{
									return false;
								}
							}
							
							$(".creer_commande_submit").click(); 
						 }
					});

				});
			</script>
			<style>
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
				.table_mail{
					border-collapse: collapse;
				}
				.table_mail th, .table_mail td{
					border: 1px solid #7A7A7A;
					padding: 2px 10px;
				}
				
				.tablesorter-default tbody>tr.even:hover>td, .tablesorter-default tbody>tr.hover>td, .tablesorter-default tbody>tr.odd:hover>td, .tablesorter-default tbody>tr:hover>td {
					background-color: #2196f3;
				}
				
				.bold{
					font-weight: bold;
				}
	
			</style>
        </head>
        <body style="font-family: \'Open Sans\', sans-serif;">';
          echo '<h1 style="text-align: center;"><a href="approvisionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Approvisionnement</h1>';
        //echo 'SELECT * FROM operationnel WHERE declinaison = "'.$liste.'" ORDER BY nb_quantite_restant ASC, couvert_besoin ASC;';
		$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		echo '<form action="'.$url.'" method="post" enctype="multipart/form-data">';
        echo '<select name="fourn"><option value="">Choisir...</option>';
        $requete = $bdd->prepare('SELECT id_supplier, name, email, lang FROM ps_supplier ORDER BY name');
        $requete->execute() or die(print_r($requete->errorInfo()));
		$liste_fournisseurs = "";
        while (($prodEC = $requete->fetch()))
        {
		  $liste_fournisseurs .= '<option value="'.$prodEC['id_supplier'].'">'.$prodEC['name'].'</option>';
          echo '<option data-email="'.$prodEC['email'].'" data-lang="'.$prodEC['lang'].'" value="'.$prodEC['id_supplier'].'">'.$prodEC['name'].'</option>';
        }
		
		// On récupère la liste de états depuis la BDD
		$sql = 'SELECT id_etat, libelle_etat, color_etat, ordre_etat FROM cmd_fournisseur_etat
		ORDER BY ordre_etat;';
		$liste_etats = Db::getInstance()->ExecuteS($sql);
		
		$liste_etat_str = "";
		foreach($liste_etats as $etat){
			$liste_etat_str .= '<option value="'.$etat['id_etat'].'">
				'.$etat['libelle_etat'].'
			</option>';
		}
				
        echo '</select>
		<input type="button" class="creer_commande" name="creer" value="Créer commande fournisseur" />
		<input type="submit" class="creer_commande_submit" name="creer_commande_submit" value="" style="display:none" />';
       // echo '<h2><input id="myInput" type="text" placeholder="Rechercher..."></h2>';
       echo '<br><br><a href="parametres_appro.php?token='.$_GET['token'].'">Paramètres appro</a>';
       echo '<br><br><a href="commandes_fournisseur.php?token='.$_GET['token'].'">Liste des commandes fournisseur</a>';
       echo '<br><br><a href="reliquat.php?token='.$_GET['token'].'">Reliquat</a>';
        echo '<table class="table" id="myTable" style="border: 0; cellspacing: 0;" width="100%">
        <thead style="background-color:#fff">
			<tr class="tablesorter-ignoreRow">
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
					<select id="filtre_etat">
						<option value="0">Etat</option>'.$liste_etat_str.'
					</select>
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:left;">
				</th>
				<th style="text-align:right;">
					<select id="fournisseur1">
						<option value="0">Rechercher...</option>'.$liste_fournisseurs.'
					</select>
				</th>
				<th style="text-align:right;">
					<select id="fournisseur2">
						<option value="0">Rechercher...</option>'.$liste_fournisseurs.'
					</select>
				</th>
			</tr>
            <tr>
                <th style="text-align:left;">
                    <span class="title_box  active">Réference</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Nom</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Etat</span>
                </th>
				<th style="text-align:left;">
                    <span class="title_box  active">Quantité en stock</span>
                </th>
				<th style="text-align:left;">
                    <span class="title_box  active">Quantité vendue à l\'année n-1 </span>
                </th>
                <th style="text-align:right;">
                    <span class="title_box  active">Qté à commander</span>
                </th>
				
				<th class="sorter-false" style="text-align:right;">
                    <span class="title_box  active">Défaut</span>
                </th>
				
				<th class="sorter-false" style="text-align:right;">
                    <span class="title_box  active"><input type="checkbox" class="check_all_1"></span>

                </th>
                <th class="sorter-false" style="text-align:right;">
                    <span class="title_box  active"><input type="checkbox" class="check_all_2"></span>

                </th>
				
				<th class="sorter-false" style="text-align:left;width:65px;">
                    <span class="title_box  active">Autre unité</span>
                </th>
                <th style="text-align:right;">
                    <span class="title_box  active">Fournisseur par défaut</span>
                </th>
                <th style="text-align:right;">
                    <span class="title_box  active">Autres fournisseurs</span>
                </th>
            </tr>
        </thead>
        <tbody>';
        $requete = $bdd->prepare('SELECT id_product, nom, reference, SUM(nb_sachet_a_produire*declinaison)-stock_theorique_tamp AS qte_appro, quantite_en_stock, SUM(quantite_vendue_annee_n_moins_1) as quantite_vendue_annee_n_moins_1 FROM appro GROUP BY id_product');
        $requete->execute() or die(print_r($requete->errorInfo()));

        while (($prodEC = $requete->fetch()))
        {
			/*if ( $_SERVER['REMOTE_ADDR'] == '81.65.104.82')
			{
				echo $prodEC['id_product'].'<br />';
			}*/
		  
			$unite_vente = ''; // unite de vente
			$fournisseur_par_defaut = array();
			$fournisseurs_ids_array = array();
			$fournisseur_par_defaut['id_supplier'] = '';
			$fournisseur_par_defaut['name'] = '';
			$fournisseur_par_defaut['quantite'] = '';
			$fournisseur_par_defaut['unite'] = '';
			$autres_fournisseurs = array();
			$autres_fournisseurs_ids_array = array();
			$autres_fournisseurs_ids_str = '';
			$derniers_fournisseurs = array();
			
		  	$sql = 'SELECT p.id_supplier, s.name, l.quantite,l.graine_gramme,l.date_approvisionnement FROM ps_product p
			INNER JOIN ps_supplier s ON s.id_supplier = p.id_supplier
			INNER JOIN ps_inventaire_lots l
			WHERE p.id_product = '.$prodEC['id_product'].'
			AND l.id_product = '.$prodEC['id_product'].'
			AND l.fournisseur = s.name
			ORDER BY l.date_approvisionnement DESC';
			$req = $bdd->prepare($sql);
			$req->execute();
			$res = $req->fetchAll();
			if(is_array($res)){
				if(count($res)){
					$fournisseur_par_defaut['id_supplier'] = $res[0]['id_supplier'];
					$fournisseur_par_defaut['name'] = $res[0]['name'];
					$fournisseur_par_defaut['quantite'] = $res[0]['quantite'];
					$fournisseur_par_defaut['unite'] = $res[0]['graine_gramme'];
					if ( $fournisseur_par_defaut['unite'] == 'gramme' ){
						$fournisseur_par_defaut['unite'] = 'kg';
						$fournisseur_par_defaut['quantite'] *= 0.001;
					}else{ // graine
						$fournisseur_par_defaut['unite'].='s';
					}
					$fournisseurs_ids_array[] = $res[0]['id_supplier'];
					$date_approvisionnement = $res[0]['date_approvisionnement'];
					$derniers_fournisseurs[$date_approvisionnement] = $res[0]['id_supplier'];
				}
			}
			
			$sql = 'SELECT p.id_supplier, s.name, l.quantite,l.graine_gramme, l.date_approvisionnement FROM ps_product_supplier p
			INNER JOIN ps_supplier s ON s.id_supplier = p.id_supplier
			INNER JOIN ps_inventaire_lots l
			WHERE p.id_product = '.$prodEC['id_product'].'
			AND l.id_product = '.$prodEC['id_product'].'
			AND l.fournisseur = s.name
			ORDER BY l.date_approvisionnement DESC';
			$req = $bdd->prepare($sql);
			$req->execute();
			$res = $req->fetchAll();
			if(is_array($res)){
				if(count($res)){
					foreach($res as $r){
						if(!in_array($r['id_supplier'],$fournisseurs_ids_array)){ // permet de n'afficher les fournisseurs qu'une fois
							$f = array();
							$f['id_supplier'] = $r['id_supplier'];
							$f['name'] = $r['name'];
							$f['quantite'] = $r['quantite'];
							$f['unite'] = $r['graine_gramme'];
							if ( $f['unite'] == 'gramme' ){
								$f['unite'] = 'kg';
								$f['quantite'] *= 0.001;
							}else{ // graine
								$f['unite'].='s';
							}
							$autres_fournisseurs[] = $f;
							$autres_fournisseurs_ids_array[] = $r['id_supplier'];
							$fournisseurs_ids_array[] = $r['id_supplier'];
							$date_approvisionnement = $r['date_approvisionnement'];
							$derniers_fournisseurs[$date_approvisionnement] = $r['id_supplier'];
						}
					}
				}
			}
			
			// Début - Dorian, BERRY-WEB, septembre 2022
			// Affichage des fournisseurs sans commande
			$sql = 'SELECT p.id_supplier, s.name FROM ps_product_supplier p
			INNER JOIN ps_supplier s ON s.id_supplier = p.id_supplier
			WHERE p.id_product = '.$prodEC['id_product'];
			$req = $bdd->prepare($sql);
			$req->execute();
			$res = $req->fetchAll();
			if(is_array($res)){
				if(count($res)){
					foreach($res as $r){
						if(!in_array($r['id_supplier'],$fournisseurs_ids_array)){ // permet de n'afficher les fournisseurs qu'une fois
							$f = array();
							$f['id_supplier'] = $r['id_supplier'];
							$f['name'] = $r['name'];
							$f['quantite'] = 0;
							$autres_fournisseurs[] = $f;
							$autres_fournisseurs_ids_array[] = $r['id_supplier'];
							$fournisseurs_ids_array[] = $r['id_supplier'];
						}
					}
				}
			}
			// Fin - Dorian, BERRY-WEB, septembre 2022		
			
			ksort($derniers_fournisseurs); // tri par ordre croissant
			$dernier_fournisseur = array_pop($derniers_fournisseurs); // retourne l'id fournisseur de la dernière date 
			
			$autres_fournisseurs_ids_str  = implode(',',$autres_fournisseurs_ids_array);
			
			$declinaisons = get_declinaison($prodEC['id_product']);
			
			$unite_autre = 'graines';
			$unite_vente = 'kg';
            foreach ($declinaisons as $dec_prod) {
                if($dec_prod['name'] != 'Non traitée' && $dec_prod['name'] != 'BIO'){
                    $dec_prod['name'] = str_replace('Par ', '', $dec_prod['name']);
                    $exp = explode(' ', $dec_prod['name']);  
					if ( isset($exp[0]) && strtolower($exp[0]) == 'plant' ){
                      continue;
                    }				
                    if ( isset($exp[1]) && strtolower($exp[1]) == 'graines' ){
                      $unite_vente = 'graines';
                      $unite_autre = 'kg';
                    }
                    
                }
            }
			
          if ( $prodEC['qte_appro'] > 0 )
          {
            $approArron = 0;
            $approArron_autre = '--';
			$quantite_en_stock = $prodEC['quantite_en_stock'];
			$quantite_vendue_annee_n_moins_1 = $prodEC['quantite_vendue_annee_n_moins_1'];
            if ( $prodEC['qte_appro'] < 1000 ){
              $approArron = ceil($prodEC['qte_appro']/100)*100; // arrondi toutes les 100 unités
            }
            else{
              $approArron = (ceil($prodEC['qte_appro']/1000 * 2) / 2)*1000; // cast implicite et arrondi toutes les 1000 unités
            }
			if ( $unite_vente == 'kg' ){ // conversion gramme en kg
                $approArron = $approArron/1000;
				$quantite_en_stock = $quantite_en_stock/1000;
				$quantite_vendue_annee_n_moins_1 = $quantite_vendue_annee_n_moins_1/1000;
            }
			
			if($unite_vente == 'kg'){
				// kg en graine
				$conversion = conversion($prodEC['id_product']);
				if($conversion != 0)
				{
					/*if ( $_SERVER['REMOTE_ADDR'] == '81.65.104.82')
					{*/
						if ( strpos($conversion, 'graines pour 100 gr') > 0 )
						{
							$conversion = str_replace(' graines pour 100 gr', '', str_replace('environ ', '', str_replace('Environ ', '',$conversion)))/100;
						}
						/*echo $approArron.' * 1000 * '.$conversion .'('.$prodEC['id_product'].')<br />';
						if ( $prodEC['id_product'] == 80 )
						{
							die;
						}*/
					//}
					$approArron_autre = $approArron * 1000 * $conversion;
					$approArron_autre = intval($approArron_autre);
				}
			}else if($unite_vente == 'graines'){
				// cas des tomates
				// graine en kg
				$conversion = conversion($prodEC['id_product']);
				if($conversion != 0){
					if ( strpos($conversion, 'graines pour 100 gr') > 0 )
						{
							$conversion = str_replace(' graines pour 100 gr', '', str_replace('environ ', '', str_replace('Environ ', '',$conversion)))/100;
						}

					$approArron_autre = $approArron * 0.001 / $conversion;
					$approArron_autre = round($approArron_autre, 2);
				}
			}
			$etats = array();
            //$requeteD = $bdd->prepare('SELECT cfe.id_etat, cfe.libelle_etat, cfe.color_etat FROM cmd_fournisseur_historique cfh LEFT JOIN cmd_fournisseur_etat cfe ON (cfh.id_etat = cfe.id_etat) WHERE cfh.id_produit = "'.$prodEC['id_product'].'" ORDER BY date DESC LIMIT 0,1;');
            $requeteD = $bdd->prepare('
			SELECT cfe.id_etat, cfe.libelle_etat, cfe.color_etat, s.name, cfd.qte, cfd.unite, cfd.date_reliquat, cfd.date_creation, cfd.date_update
			FROM cmd_fournisseur_detail cfd
			LEFT JOIN cmd_fournisseur_etat cfe ON (cfd.id_etat = cfe.id_etat)
			LEFT JOIN cmd_fournisseur cf ON (cf.id_cmd = cfd.id_cmd)
			LEFT JOIN ps_supplier s ON (cf.id_fournisseur = s.id_supplier)
			WHERE cfd.id_produit = "'.$prodEC['id_product'].'" ORDER BY id_detail DESC;');
            $requeteD->execute() or die(print_r($requeteD->errorInfo()));
			$total_qte_commandee = 0;
            while (($rangee_etat = $requeteD->fetch()))
            {
			  $etat = array();
              $etat['id'] = $rangee_etat['id_etat'];
              $etat['libelle'] = $rangee_etat['libelle_etat'];
              $etat['color'] = $rangee_etat['color_etat'];
              $etat['supplier_name'] = $rangee_etat['name'];
              $etat['date_creation'] = $rangee_etat['date_creation'];
              $etat['date_reliquat'] = $rangee_etat['date_reliquat'];
              $etat['date_update'] = $rangee_etat['date_update'];
              $etat['qte'] = $rangee_etat['qte'];
              $etat['unite'] = $rangee_etat['unite'];
			  $etats[] = $etat;
			  
			  // id 2 : commande envoyée au fournisseur
			  // id 3 : confirmé
			  // id 4 : en reliquat
			  if( $etat['id'] == 2 || $etat['id'] == 3 || $etat['id'] == 4 ){
				  $qte = $etat['qte'];
				  
				  // conversion graine/gramme
				  if($unite_vente == 'graines' && $etat['unite'] == 'kg'){
						// kg en graine
						$qte = $qte * 1000 * conversion($prodEC['id_product']);
						$qte = intval($qte);
				  }else if($unite_vente == 'kg' && $etat['unite'] == 'graines'){
						// cas des tomates
						// graine en kg
						
						$qte = $qte * 0.001 / conversion($prodEC['id_product']);
				  }
					
				  $total_qte_commandee += $qte;
			  }
            }
			
			if(!count($etats) || $total_qte_commandee < $approArron){
				// si pas d'état ou si quantité commandée inférieure à quantité à commander
				// Ajout de l'état "A approvisionner" 
				$etat = array();
				$etat['id'] = 1;
				$etat['libelle'] = "A approvisionner";
				$etat['color'] = "#2196f3";
				$etats[] = $etat;
			}
			$link = '/admin123/index.php?controller=AdminProducts&id_product='.$prodEC['id_product'].'&updateproduct';
            echo '<tr id="tr_'.$prodEC['id_product'].'">
						
						<input type="hidden" name="id_product_'.$prodEC['reference'].'" value="'.$prodEC['id_product'].'"/>
                        <td style="border-bottom: 1px solid grey;">
							<a href="'.$link.'" target="_blank">'.$prodEC['reference'].'</a>
                        </td>
                        <td class="js-product-name" style="border-bottom: 1px solid grey;">
                            '.$prodEC['nom'].'
                        </td>
                        <td class="filtre_etat" style="border-bottom: 1px solid grey;">';
						$total_qte_commandee = 0;
						foreach($etats as $etat){
							
							$date_update = "";
							if(isset($etat['date_update']) && $etat['date_update'] != "0000-00-00"){
								$date_update = date("d/m/Y", strtotime($etat['date_update']));
							}
							else if(isset($etat['date_creation']) && $etat['date_creation'] != "0000-00-00"){
								$date_update = date("d/m/Y", strtotime($etat['date_creation']));
							}
							
							if($etat['id'] == 2){ // Commande envoyée au fournisseur
							
								$etat['libelle'] .= '- '.$date_update.' ('. $etat['supplier_name'].')';
								$etat['libelle'] .= ' - quantité commandée: '.$etat['qte'] .' '. $etat['unite'];
							}
							else if($etat['id'] == 3){ // Confirmé
								
								$etat['libelle'] .= '- '.$date_update.' ('. $etat['supplier_name'].')';
								$etat['libelle'] .= ' - quantité commandée: '.$etat['qte'] .' '. $etat['unite'];
							}
							else if($etat['id'] == 4){ // En reliquat
								$date_reliquat = "";
								if($etat['date_reliquat'] != "0000-00-00"){
									$date_reliquat = date("m/Y", strtotime($etat['date_reliquat']));
								}
								$etat['libelle'] .= '- '.$date_reliquat.' ('. $etat['supplier_name'].')';
								$etat['libelle'] .= ' - quantité commandée: '.$etat['qte'] .' '. $etat['unite'];
							}
							else if($etat['id'] == 5){ // Indisponible
								
								$etat['libelle'] .= '- '.$date_update.' ('. $etat['supplier_name'].')';
								$etat['libelle'] .= ' - quantité commandée: '.$etat['qte'] .' '. $etat['unite'];
							}
							else if($etat['id'] == 6){ // Produit reçu
							
								$etat['libelle'] .= '- '.$date_update.' ('. $etat['supplier_name'].')';
								$etat['libelle'] .= ' - quantité reçue: '.$etat['qte'] .' '. $etat['unite'];
							}
							echo '<div style="background-color:'.$etat['color'].';padding-left: 10px;padding-right: 10px;color: white;font-weight: bold;">'.$etat['libelle'].'</div>';
						}
						// le dernier fournisseur s'affiche en gras (par rapport à la date de la création du lot)
						$bold = '';
						if($dernier_fournisseur == $fournisseur_par_defaut['id_supplier']){
							$bold = 'bold';
						}
                        echo '</td>
						<td style="border-bottom: 1px solid grey;text-align:right;">
                            '.$quantite_en_stock.' '.$unite_vente.'
                        </td>
						<td style="border-bottom: 1px solid grey;text-align:right;">
                            '.$quantite_vendue_annee_n_moins_1.' '.$unite_vente.'
                        </td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">
                            '.$approArron.' '.$unite_vente.'
                        </td>
						  
						  <td style="border-bottom: 1px solid grey;text-align:right;">
                              <input type="text" data-unite="'.$unite_vente.'" name="qte_'.$unite_vente.'_'.$prodEC['reference'].'" value="'.$approArron.'" style="width: 50px;" />
                              <input type="hidden" name="unite_'.$prodEC['reference'].'" value="'.$unite_vente.'"/>
                          </td>
						  
                          <td style="border-bottom: 1px solid grey;text-align:right;">
                              <input type="checkbox" class="checkbox-article checkbox-article-1" data-ref="'.$prodEC['reference'].'" name="check_'.$prodEC['reference'].'" value="'.$unite_vente.'" />
                          </td>
						  
						  <td style="border-bottom: 1px solid grey;text-align:right;">
                              <input type="checkbox" class="checkbox-article checkbox-article-2" data-ref="'.$prodEC['reference'].'" name="check_'.$prodEC['reference'].'" value="'.$unite_autre.'" />
                          </td>
						  
						  <td style="border-bottom: 1px solid grey;text-align:left;">
                              <input type="text" data-unite="'.$unite_autre.'" name="qte_'.$unite_autre.'_'.$prodEC['reference'].'" value="'.$approArron_autre.'" style="width: 60px;" />
                          </td>
                        <td class="fournisseur1 '.$bold.'" data-fournisseur="'.$fournisseur_par_defaut['id_supplier'].'" style="border-bottom: 1px solid grey;text-align:right;">
                            '.$fournisseur_par_defaut['name'].' ('.$fournisseur_par_defaut['quantite'].' '.$fournisseur_par_defaut['unite'].')
                        </td>';
						echo '<td class="fournisseur2" data-fournisseur="'.$autres_fournisseurs_ids_str.'" style="border-bottom: 1px solid grey;text-align:right;">';
						foreach($autres_fournisseurs as $f){
							$bold = '';
							if($dernier_fournisseur == $f['id_supplier']){
								$bold = 'bold';
							}
							echo '<span class="'.$bold.'">'. $f['name'].' ('.$f['quantite'].' '.$f['unite'].')</span><br>';
						}
                        echo '</td>';
                      
                    echo '</tr>';
                  }
        }
      echo '</tbody></table></form>';
      echo '
	</body>
</html>';
function get_declinaison($id_product){
	$sql = 'SELECT *, sa.quantity as qte, pa.weight as poids FROM `ps_product_attribute` AS pa
	LEFT JOIN `ps_product_attribute_combination` AS pac ON pac.id_product_attribute = pa.id_product_attribute
	LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
	LEFT JOIN `ps_attribute_lang` AS al ON al.id_attribute = pac.id_attribute
	LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
	WHERE pa.id_product = '.$id_product.' AND al.id_lang = 1 ORDER BY pa.default_on DESC, a.position ASC';
	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	return $res;
}
function get_commandes_en_cours(){
	// On récupère les commandes fournisseur depuis la BDD
	$sql = 'SELECT f.id_cmd, f.id_fournisseur, f.date, s.name FROM cmd_fournisseur f
	INNER JOIN ps_supplier s ON s.id_supplier = f.id_fournisseur
	ORDER BY id_cmd DESC;';
	$commandes = Db::getInstance()->ExecuteS($sql);
	$commandes_terminees = [];
	$commandes_en_cours = [];
	foreach ($commandes as $commande){
		$sql = 'SELECT id_etat, lot_cree FROM cmd_fournisseur_detail
		WHERE id_cmd = '.$commande['id_cmd'];
		$commande_detail = Db::getInstance()->ExecuteS($sql);
		$terminee = true;
		foreach($commande_detail as $detail){
			// une commande est considérée terminée si tous les lots sont crées, ou si ligne indisponible.
			if($detail['id_etat'] != 5 && !$detail['lot_cree']){
				$terminee = false;
				break;
			}
		}
		if($terminee){
			$commandes_terminees[] = $commande;
		}else{
			$commandes_en_cours[] = $commande;
		}
	}
	
	return $commandes_en_cours;
}
?>
