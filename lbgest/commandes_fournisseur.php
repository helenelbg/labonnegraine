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
		if($detail['id_etat'] != 5 && $detail['id_etat'] != 7 && !$detail['lot_cree']){
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
  
  <script src="https://unpkg.com/sticky-table-headers"></script>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/e35ed65262.js" crossorigin="anonymous"></script>
  <script>
	  $( function() {
		$( "#tabs" ).tabs();
		$("table").stickyTableHeaders();
	  } );

      function search(ref)
	  {
		if ( ref == 0 )
		{
		  $(".table tr").show();
		  $(".fa-search-plus").show();
		  $(".fa-search-minus").hide();
		}
		else
		{
		  $(".table tr").hide();
		  $("[filtre_ref=\'"+ref+"\']").show();
		  $(".table tr#headert").show();
		  $(".fa-search-plus").hide();
		  $(".fa-search-minus").show();
		}
	  }

	$(document).ready(function(){
		$(".myTable").tablesorter();
	});
  </script>

  <style>
  .ui-widget {
	font-family: \'Open Sans\', sans-serif !important;
  }
  </style>
</head>
<body style="font-family: \'Open Sans\', sans-serif !important;">';

echo '<h1 style="text-align: center;"><a href="approvisionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Commandes fournisseur</h1>';

$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo '<div style="margin-bottom:15px"><a href="approvisionnement.php?token='.$_GET['token'].'">Retour</a></div>';

echo '<div id="tabs">';
        echo '<ul>';
        echo '<li><a href="#tabCommandesencours">Commandes en cours</a></li>';
        echo '<li><a href="#tabCommandesterminees">Commandes terminées</a></li>';
        echo '</ul>';
		
$tab_decli = array('1' => 'Commandes en cours', '2' => 'Commandes terminees');
        foreach($tab_decli as $key => $liste){
			echo '<div id="tab'.str_replace(' ','', $liste).'">';
			
			echo '<table class="myTable" style="border: 0px; padding: 0px;" width="100%">
					<thead>
					<tr>
							<th style="text-align:left">Id commande</th>
							<th style="text-align:left">Fournisseur</th>
							<th style="text-align:left">Date</th>
							<th class="sorter-false"></th>
						</tr>
					</thead>
					<tbody>';
					
			if($key == 1){
				$liste_commandes = $commandes_en_cours;
			}else{
				$liste_commandes = $commandes_terminees;
			}
			foreach ($liste_commandes as $commande){

					$cmd_date = DateTime::createFromFormat('YmdHi', $commande['date'])->format('d/m/Y H:i');

					echo '<tr style="height: 30px;">
					
					<td>'.$commande['id_cmd'].'</td>
					<td>'.$commande['name'].'</td>
					<td style="letter-spacing: 1.2px;">'.$cmd_date.'</td>
					<td style="padding-left: 15px;"><a href="produits_fournisseur.php?id_cmd='.$commande['id_cmd'].'&token='.$_GET['token'].'"><input type="button" value="Modifier" /></a></td>
							
					</tr>';
						
			}
			
			echo '		</tbody>
				</table>';
				 echo '</div>';
		}
echo '
</body>
</html>';
