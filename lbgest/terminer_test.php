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

$germ = Db::getInstance()->ExecuteS('SELECT * FROM AW_test_lots WHERE id = "' . $_POST['id_test'] . '";');
$date_fin = '';
$valeur_fin = 0;

if ( $germ[0]['date_etape_3'] != '0000-00-00' )
{
  $date_fin = $germ[0]['date_etape_3'];
  $valeur_fin = $germ[0]['resultat_etape_3'];
}
elseif ( $germ[0]['date_etape_2'] != '0000-00-00' )
{
  $date_fin = $germ[0]['date_etape_2'];
  $valeur_fin = $germ[0]['resultat_etape_2'];
}
elseif ( $germ[0]['date_etape_1'] != '0000-00-00' )
{
  $date_fin = $germ[0]['date_etape_1'];
  $valeur_fin = $germ[0]['resultat_etape_1'];
}
else
{
  $date_fin = date('Y-m-d');
  $valeur_fin = 0;
}

$sql = $bdd->prepare('UPDATE AW_test_lots SET date_fin_test = "'.$date_fin.'", pourcentage_germ = "'.$valeur_fin.'" WHERE id = "'.$_POST['id_test'].'";');
$sql->execute();

$sqlD = $bdd->prepare('DELETE FROM germination WHERE id_test = "'.$_POST['id_test'].'";');
$sqlD->execute();
