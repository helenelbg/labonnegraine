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

// Dans l’idée générale, le but est de pouvoir croiser, trier et comprendre ce qui se vend (ou pas), suivant les déclinaisons.

$id_lang = 1; // FR
$id_categorie_root = intval(Configuration::get('PS_ROOT_CATEGORY'));
$categories = Category::getCategories(intval($id_lang), false);


// On récupère tous les produits 

$sql = 'SELECT p.id_product, p.reference, pl.name FROM ps_product p
INNER join ps_product_lang pl ON p.id_product =  pl.id_product
WHERE pl.id_lang = '. $id_lang .' AND p.active = 1 
ORDER BY pl.name';
$product_list = Db::getInstance()->ExecuteS($sql);

$product_list_str = '<option value="0">Produit</option>';
foreach($product_list as $p){
	$product_list_str .= '<option value="'.$p['id_product'].'">'.$p['reference'].' '.$p['name'].'</option>';
}

$date_debut = '';
$date_fin = '';
$date_debut2 = '';
$date_fin2 = '';

if(isset($_POST['date_debut'])){
	$date_debut = $_POST['date_debut'];
}
	
if(isset($_POST['date_fin'])){
	$date_fin = $_POST['date_fin'];
}
	
if(isset($_POST['date_debut2'])){
	$date_debut2 = $_POST['date_debut2'];
}
	
if(isset($_POST['date_fin2'])){
	$date_fin2 = $_POST['date_fin2'];
}
		
echo '
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Export Data - LBG</title>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="tablesorter-master/dist/js/jquery.tablesorter.min.js"></script>
		<script src="select2/dist/js/select2.min.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link href="tablesorter-master/dist/css/theme.default.min.css" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
		<link href="select2/dist/css/select2.min.css" rel="stylesheet" />
	</head>
	<body>	
    <h1>Export Data</h1>
    <hr>
    <h3>Détaillé</h3>
    <form action="export_data_csv.php?token=hdf6dfdfs6ddgs" method="post" id="exportDataForm" class="form-horizontal">
    <div class="id_category">
        <select name="id_category" id="id_category">
            <option value="">Catégorie</option>
            <option value="0">Toutes</option>';

    Category::recurseCategory($categories, $categories[0][$id_categorie_root], 1, 0);

    echo '

        </select>
        </div>
        <div class="id_product">
            <select name="id_product" id="id_product">
                
                '.$product_list_str.'
            </select>
        </div>
        <div class="row row-margin-bottom">
            <div class="col-lg-2">
                <label for="date_debut" class="col-md-12">Date début</label>
                <input type="date" name="date_debut" id="date_debut" class="col-md-4 form-control" value="'.$date_debut.'">
            </div>
            <div class="col-lg-2">
                <label for="date_fin" class="col-md-12">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" class="col-md-4 form-control" value="'.$date_fin.'">
            </div>
            
        </div>
        
        
        <div class="row">
            <input type="checkbox" id="checkbox_ref" name="checkbox_ref">
            <label for="checkbox_ref">Référence du produit</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_nom" name="checkbox_nom">
            <label for="checkbox_nom">Nom du produit</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_quantite" name="checkbox_quantite">
            <label for="checkbox_quantite">Quantité</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_commande" name="checkbox_commande">
            <label for="checkbox_commande">ID Commande</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_codepostal" name="checkbox_codepostal">
            <label for="checkbox_codepostal">Code postal</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_departement" name="checkbox_departement">
            <label for="checkbox_departement">Département</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_genre" name="checkbox_genre">
            <label for="checkbox_genre">Genre</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_age" name="checkbox_age">
            <label for="checkbox_age">Age</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_prix" name="checkbox_prix">
            <label for="checkbox_prix">Prix de vente unitaire TTC</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_date" name="checkbox_date">
            <label for="checkbox_date">Date</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_jour" name="checkbox_jour">
            <label for="checkbox_jour">Jour de la semaine</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_email" name="checkbox_email">
            <label for="checkbox_email">Email client</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_utm_medium" name="checkbox_utm_medium">
            <label for="checkbox_utm_medium">Utm medium</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_mode_paiement" name="checkbox_mode_paiement">
            <label for="checkbox_mode_paiement">Mode de paiement</label>
        </div>
        <div class="row">
            <input type="checkbox" id="checkbox_mode_livraison" name="checkbox_mode_livraison">
            <label for="checkbox_mode_livraison">Mode de livraison</label>
        </div>
            
        <input type="submit" class="submit_export" name="submit_export" value="Export">
        
    </form>	
<hr>
    <h3>Cumulé</h3>
    <form action="export_data_csv2.php?token=hdf6dfdfs6ddgs" method="post" id="exportDataForm2" class="form-horizontal">
		<div class="id_category2">
			<select name="id_category2" id="id_category2">
				<option value="">Catégorie</option>
				<option value="0">Toutes</option>';

		Category::recurseCategory($categories, $categories[0][$id_categorie_root], 1, 0);

		echo '

			</select>
			</div>
			<div class="id_product2">
				<select name="id_product2" id="id_product2">
					
					'.$product_list_str.'
				</select>
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-2">
					<label for="date_debut2" class="col-md-12">Date début</label>
					<input type="date" name="date_debut2" id="date_debut2" class="col-md-4 form-control" value="'.$date_debut2.'">
				</div>
				<div class="col-lg-2">
					<label for="date_fin2" class="col-md-12">Date fin</label>
					<input type="date" name="date_fin2" id="date_fin2" class="col-md-4 form-control" value="'.$date_fin2.'">
				</div>
				
			</div>
			
			
			<div class="row">
				<input type="checkbox" id="checkbox_ref2" name="checkbox_ref2">
				<label for="checkbox_ref2">Référence du produit</label>
			</div>
			<div class="row">
				<input type="checkbox" id="checkbox_nom2" name="checkbox_nom2">
				<label for="checkbox_nom2">Nom du produit</label>
			</div>
			<div class="row">
				<input type="checkbox" id="checkbox_quantite2" name="checkbox_quantite2">
				<label for="checkbox_quantite2">Quantité</label>
			</div>
			<div class="row">
				<input type="checkbox" id="checkbox_commande2" name="checkbox_commande2">
				<label for="checkbox_commande2">Commandes</label>
			</div>
			<div class="row">
				<input type="checkbox" id="checkbox_prix2" name="checkbox_prix2">
				<label for="checkbox_prix2">CA</label>
			</div>
				
			<input type="submit" class="submit_export" name="submit_export2" value="Export">
			
		</form>	

		<script>
			$(document).ready(function () {
				$("#id_category").select2({dropdownAutoWidth : true});
				$("#id_product").select2({dropdownAutoWidth : true});

				$("#id_category2").select2({dropdownAutoWidth : true});
				$("#id_product2").select2({dropdownAutoWidth : true});
			});		

			// filtre des produits par catégorie
			$("#id_category").on("change", function() {
				var id_category = $(this).val();
				 $.ajax({
					method: "POST",
					url: "export_data_ajax.php?token=hdf6dfdfs6ddgs",
					data: { id_category: id_category }
				})
				.done(function( msg ) {
					$("#id_product").html(msg);
					$("#id_product").select2({dropdownAutoWidth : true});
				});
			});

            // filtre des produits par catégorie
			$("#id_category2").on("change", function() {
				var id_category2 = $(this).val();
				 $.ajax({
					method: "POST",
					url: "export_data_ajax.php?token=hdf6dfdfs6ddgs",
					data: { id_category: id_category2 }
				})
				.done(function( msg ) {
					$("#id_product2").html(msg);
					$("#id_product2").select2({dropdownAutoWidth : true});
				});
			});
					
		</script>

		<style>		
			body {
				font-family: "Open Sans", sans-serif;
			}
		
			.id_product, .id_product2 {
				margin-top: 15px;
			}
			
			.row {
				margin-top: 15px;
			}
			
			.col-lg-2 {
				display: inline-block;
				margin-right: 20px;
			}
			
			.submit_export, .submit_export2 {
				margin-top: 15px;
			}
			
			label {
				margin-right: 10px;
			}
			
		</style>		
	</body>
</html>';


?>