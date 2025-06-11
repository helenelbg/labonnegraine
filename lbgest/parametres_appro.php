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

if (isset($_POST['maj_croissance_appro'])) {
	// maj croissance
	$croissance_appro = $_POST['croissance_appro'];
	$croissance_appro = str_replace(',','.',$croissance_appro);
	$sql = 'UPDATE conditionnement SET croissance="' . pSQL($croissance_appro) . '" WHERE id = 2;';
	$req = Db::getInstance()->Execute($sql);
}

// On récupère le coefficient de croissance depuis la BDD

$croissance_appro = '';
$res_appro = Db::getInstance()->ExecuteS('SELECT croissance FROM conditionnement WHERE id = 2;');
foreach ($res_appro as $r){
  $croissance_appro = floatval($r['croissance']);
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

echo '<h1 style="text-align: center;"><a href="approvisionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Paramètres</h1>';

$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo '<div style="margin-bottom:15px"><a href="approvisionnement.php?token='.$_GET['token'].'">Retour</a></div>';

// Coefficient de croissance

echo '<form action="'.$url.'" method="post" enctype="multipart/form-data"><div>
	<label for="croissance_appro">Croissance approvisionnement</label>
	<input id="croissance_appro" type="text" name="croissance_appro" value="'.$croissance_appro.'">
	<input type="submit" name="maj_croissance_appro" value="Valider" />
</div>
</form>';


echo '</body>
</html>';
