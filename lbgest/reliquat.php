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
  
  <script>
	$(document).ready(function(){
		
		$(".table_reliquat").tablesorter();
		
		function get_reliquat(){
			var token = $(".js-token").val();
			var date_reliquat = $(".js-reliquat").val(); 
		
			$.ajax({
			  url: "ajax_get_reliquat.php",
			  type: "POST",
			  data: {
				"token" : token,
				"date_reliquat": date_reliquat
			  }
			}).done(function(response) {
			  res = JSON.parse(response);
			  
			  var tbody = "";
			  for(var i=0; i<res.length; i++){
				  var cmd = \'<a href="/lbgest/produits_fournisseur.php?id_cmd=\'+res[i]["id_cmd"]+\'&token=hdf6dfdfs6ddgs" target="_blank">\';
				  cmd += res[i]["id_cmd"] + " (" +res[i]["nom_fournisseur"] + ") ";
				  cmd += "</a>";
				  
				  var ref = res[i]["reference"];
				  var nom = res[i]["name"];
				  var qte = res[i]["qte"] + " " +res[i]["unite"];
				  var date = res[i]["date_reliquat"];
				  tbody += "<tr><td>"+cmd+"</td><td>"+ref+"</td><td>"+nom+"</td><td>"+qte+"</td><td>"+date+"</td></tr>";
			  }
			  
			  $(".table_reliquat_tbody").html(tbody);
			  $(".table_reliquat").trigger("update"); // update le tablesorter

			});
		}
		
		get_reliquat();
		
		$("body").on("click", ".js-reliquat", function() {
			get_reliquat();
		});
		
	});
  </script>

  <style>
		h1{
			font-weight: 400;
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
		
  </style>
</head>
<body style="font-family: \'Open Sans\', sans-serif !important;">';

echo '<h1 style="text-align: center;"><a href="approvisionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Reliquat</h1>';

echo '<div style="margin-bottom:15px"><a href="approvisionnement.php?token='.$_GET['token'].'">Retour</a></div>';
echo '<input class="js-token" type="hidden" value="'.$_GET['token'].'">';

$sql = 'SELECT date_reliquat FROM cmd_fournisseur_detail
WHERE id_etat = 4 
GROUP BY date_reliquat
ORDER BY date_reliquat ASC';
$res = Db::getInstance()->ExecuteS($sql);
	
$select_date = '<select class="js-reliquat">';
foreach($res as $r){
	$reliquat_datetime = new DateTime($r['date_reliquat']);
	$date_display = $reliquat_datetime->format('m/Y');
	$date_value = $reliquat_datetime->format('Y-m');
	$select_date .= '<option value="'.$date_value.'">'.$date_display.'</option>';
}
$select_date .= '</select>';

echo $select_date;

echo '<table class="table_reliquat">
	<thead>
		<tr>
			<th style="text-align:left">Commande</th>
			<th style="text-align:left">Réference</th>
			<th style="text-align:left">Nom</th>
			<th style="text-align:left">Quantité</th>
			<th style="text-align:left">Date reliquat</th>
		</tr>
	</thead>
	<tbody class="table_reliquat_tbody">

	</tbody>
</table>';



?>

