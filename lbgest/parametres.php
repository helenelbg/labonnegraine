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

if (isset($_POST['maj_croissance'])) {
	// maj croissance
	$croissance = $_POST['croissance'];
	$croissance = str_replace(',','.',$croissance);
	$sql = 'UPDATE conditionnement SET croissance="' . pSQL($croissance) . '" WHERE id = 1;';
	$req = Db::getInstance()->Execute($sql);
}



// On récupère le coefficient de croissance depuis la BDD
$croissance = '';
$res = Db::getInstance()->ExecuteS('SELECT croissance FROM conditionnement WHERE id = 1;');
foreach ($res as $r){
  $croissance = floatval($r['croissance']);
}



// On récupère la ventilation depuis la BDD
$ventilation = array();
$sql = 'SELECT id FROM ventilation;';
$query = $bdd->prepare($sql);
$query->execute();
$ventilation = $query->fetchAll(\PDO::FETCH_GROUP);

// On récupère les graines depuis la BDD
// id conditionnement = 6

$graines = array();

// id catgories = 18, 78, 233 ( graines potagères, aromatiques, fleurs )
$categories = array(18 => 'Graines potagères', 78 => 'Graines aromatiques', 233 => 'Fleurs', 92 => 'Mélange de fleurs');

foreach ($categories as $key => $cat){

	// select toutes les sous-categories

	$sql = 'SELECT c.id_category, pa.id_product_attribute, pa.id_product, pl.name as nom, al.name as declinaison, cl.name as category_name
	FROM ps_product_attribute pa
	INNER JOIN ps_product p ON pa.id_product = p.id_product
	INNER JOIN ps_product_lang pl ON pl.id_product = p.id_product
	INNER JOIN ps_product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
	INNER JOIN ps_attribute_lang al ON al.id_attribute = pac.id_attribute
	INNER JOIN ps_category c ON c.id_category = p.id_category_default
	INNER JOIN ps_category_lang cl ON cl.id_category = c.id_category
	INNER JOIN ps_attribute a ON a.id_attribute = al.id_attribute
	WHERE pl.id_lang = 1
	AND al.id_lang = 1
	AND cl.id_lang = 1
	AND p.active = 1
	AND a.id_attribute_group = 6';

	if($key == 78){
		$sql .= ' AND c.id_category = '.$key.' ORDER BY cl.name, pl.name, al.name';
	}else{
		$sql .= ' AND c.id_parent = '.$key.' ORDER BY cl.name, pl.name, al.name';
	}


	$query = $bdd->prepare($sql);
	$query->execute();
	$res = $query->fetchAll(\PDO::FETCH_GROUP);

	$g = array('name' => $cat, 'value' => $res);
	$graines[$key] = $g;

}




echo '<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>La Bonne Graine - Besoins en conditionnement</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://unpkg.com/sticky-table-headers"></script>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>





  <style>
  .ui-widget {
	font-family: \'Open Sans\', sans-serif !important;
  }
  </style>
</head>
<body style="font-family: \'Open Sans\', sans-serif !important;">';

echo '<h1 style="text-align: center;"><a href="conditionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Paramètres</h1>';

$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo '<div style="margin-bottom:15px"><a href="conditionnement.php?token='.$_GET['token'].'">Retour</a></div>';
echo '<input class="js-token" type="hidden" value="'.$_GET['token'].'">';

// Coefficient de croissance

echo '<form action="'.$url.'" method="post" enctype="multipart/form-data"><div>
	<label for="croissance">Croissance conditionnement</label>
	<input id="croissance" type="text" name="croissance" value="'.$croissance.'">
	<input type="submit" name="maj_croissance" value="Valider" />
</div>
</form>';


// Ventilation du paquet

echo '<form action="'.$url.'" method="post" enctype="multipart/form-data"><div>
	<h2 class="ventilation">Ventilation du paquet</h2>
	<table id="table_parametres">
		<thead>
			<tr>
				<th>Graines</th>
				<th>Victoria</th>
				<th>Carmen</th>
			</tr>
		</thead>
		<tbody>';

		foreach($graines as $key1 => $g){
			echo '
			<tr class="parent_category" data-depth="1" data-id="'.$key1.'">
				<td class="nom"><span>'.$g['name'].'</span> <span class="grower">+</span> </td>
				<td></td>
				<td></td>
			</tr>';

			// Pacourt des catégories
			foreach($g['value'] as $key2 => $c){
				$checked1 = isset($ventilation['1_'.$key1.'_'.$key2]) ? 'checked="checked"' : '';
				$checked2 = isset($ventilation['2_'.$key1.'_'.$key2]) ? 'checked="checked"' : '';
				echo '<tr class="child_category" data-depth="2" data-parent-1="'.$key1.'" data-id="'.$key2.'">
					<td class="nom"><span>   '.$c[0]['category_name'].'</span> <span class="grower">+</span> </td>
					<td><input type="checkbox" name="ventilation_1_'.$key1.'_'.$key2.'" '.$checked1.'></td>
					<td><input type="checkbox" name="ventilation_2_'.$key1.'_'.$key2.'" '.$checked2.'></td>
				</tr>';
				// Formatte la liste, puis pacourt les produits
				$produits = array();
				foreach($c as $produit){
					$p = array();
					$key = $produit['id_product'];
					$p['nom'] = $produit['nom'];
					$p['id_product_attribute'] = $produit['id_product_attribute'];
					$p['declinaison'] = $produit['declinaison'];
					$produits[$key][] = $p;
				}
				foreach($produits as $key3 => $produit){
					$nom = $produit[0]['nom'];
					$checked1 = isset($ventilation['1_'.$key1.'_'.$key2.'_'.$key3]) ? 'checked="checked"' : '';
					$checked2 = isset($ventilation['2_'.$key1.'_'.$key2.'_'.$key3]) ? 'checked="checked"' : '';
					echo '<tr class="product" data-depth="3" data-parent-1="'.$key1.'" data-parent-2="'.$key2.'" data-id="'.$key3.'">
						<td class="nom"><span>      '.$nom.'</span> <span class="grower">+</span></td>
						<td><input type="checkbox" name="ventilation_1_'.$key1.'_'.$key2.'_'.$key3.'" '.$checked1.'></td>
						<td><input type="checkbox" name="ventilation_2_'.$key1.'_'.$key2.'_'.$key3.'" '.$checked2.'></td>
						</tr>';
					// Pacourt les déclinaisons de produits
					foreach($produit as $declinaison){
						$key4 = $declinaison['id_product_attribute'];
						$d = $declinaison['declinaison'];
						$checked1 = isset($ventilation['1_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4]) ? 'checked="checked"' : '';
						$checked2 = isset($ventilation['2_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4]) ? 'checked="checked"' : '';
						
						// Début - Dorian, BERRY-WEB, aout 2022
						
						// Si pas de ventilation pour un produit, le mettre par défaut dans Victoria
						if(!$checked1  && !$checked2){
							$id_ventilation = '1_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4;
							$sql = 'INSERT IGNORE INTO ventilation (id)
							VALUES ("'.pSQL($id_ventilation).'")';
							$req = Db::getInstance()->Execute($sql);
						}
						
						// Fin - Dorian, BERRY-WEB, aout 2022
						
						echo '<tr class="declinaison" data-depth="4" data-parent-1="'.$key1.'" data-parent-2="'.$key2.'" data-parent-3="'.$key3.'" data-id="'.$key4.'">
							<td class="nom"><span>         '.$d.'</span></td>
							<td><input type="checkbox" name="ventilation_1_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4.'" '.$checked1.'></td>
							<td><input type="checkbox" name="ventilation_2_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4.'" '.$checked2.'></td>
							</tr>';
					}
				}
			}
		}

echo '		</tbody>
	</table>

</div>
</form>';

?>

<script>
	$(".grower").on("click", function(e){
		toggleBranch($(this));
	});

	$("input[type=checkbox]").on("click", function(e){
		toggleCheckbox($(this));
	});

	function toggleBranch(jQueryElement)
	{
		if(jQueryElement.hasClass('OPEN'))
			closeBranch(jQueryElement);
		else
			openBranch(jQueryElement);
	}

	function openBranch(jQueryElement)
	{
		jQueryElement.addClass('OPEN').removeClass('CLOSE');
		var tr = jQueryElement.closest('tr');
		var depth = $(tr).data( "depth" );
		var depth_parent = depth;
		depth++;
		var id = $(tr).data( "id" );
		$('tr[data-parent-'+depth_parent+'="'+id+'"][data-depth="'+depth+'"]').show();
		jQueryElement.html('-');
	}

	function closeBranch(jQueryElement)
	{
		jQueryElement.addClass('CLOSE').removeClass('OPEN');
		var tr = jQueryElement.closest('tr');
		var depth = $(tr).data( "depth" );
		var depth_parent = depth;
		var id = $(tr).data( "id" );
		var depth_max = 10;

		for(var i=0; i<depth_max; i++){
			depth++;
			$('tr[data-parent-'+depth_parent+'="'+id+'"][data-depth="'+depth+'"]').hide();
			$('tr[data-parent-'+depth_parent+'="'+id+'"][data-depth="'+depth+'"] .grower').addClass('CLOSE').removeClass('OPEN').html('+');
		}

		jQueryElement.html('+');
	}

	function toggleCheckbox(jQueryElement)
	{
		var checked = jQueryElement.is(':checked');
		var tr = jQueryElement.closest('tr');
		var depth = $(tr).data( "depth" );
		var depth_parent = depth;
		var id = $(tr).data( "id" );
		var depth_max = 10;
		var index = jQueryElement.closest('td').index() + 1;
		var id_ventilation = [];
		
		var name = jQueryElement.attr('name');
		name = name.replace("ventilation_", "");
		id_ventilation.push(name);
		
		for(var i=0; i<depth_max; i++){
			depth++;
			$('tr[data-parent-'+depth_parent+'="'+id+'"][data-depth="'+depth+'"] td:nth-child('+index+') input[type=checkbox]').prop( "checked", checked );
			var name = $('tr[data-parent-'+depth_parent+'="'+id+'"][data-depth="'+depth+'"] td:nth-child('+index+') input[type=checkbox]').each(function() {
				var name = $(this).attr('name');
				name = name.replace("ventilation_", "");
				id_ventilation.push(name);
			});
			
			
		}
		
		var token = $(".js-token").val();
		var jsonString = JSON.stringify(id_ventilation);

		$.ajax({
		  url: "ajax_ventilation.php",
		  type: "POST",
		  data: {
			"action": "maj_ventilation",
			"token" : token,
			"id_ventilation": jsonString,
			"checked": checked,
		  }
		}).done(function(response) {
		  
		});
	}

</script>

<?php


echo '</body>
</html>';
