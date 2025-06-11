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

$sql = $bdd->prepare('UPDATE AW_test_lots SET date_etape_'.$_POST['etape'].' = "'.date('Y-m-d').'", resultat_etape_'.$_POST['etape'].' = "'.$_POST['valeur'].'", commentaire = "'.addslashes($_POST['commentaires']).'" WHERE id_lot = "'.$_POST['lot'].'" AND date_etape_'.$_POST['etape'].' = "0000-00-00" AND origine_test = "LBG" AND date_fin_test = "0000-00-00";');

$sql->execute();

if ( $_POST['termine'] == 'true' )
{
  $sql2 = $bdd->prepare('UPDATE AW_test_lots SET date_fin_test = "'.date('Y-m-d').'", pourcentage_germ = resultat_etape_'.$_POST['etape'].' WHERE id_lot = "'.$_POST['lot'].'" AND origine_test = "LBG" AND date_fin_test = "0000-00-00";');
  $sql2->execute();
}

?>
