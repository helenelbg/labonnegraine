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

if(!isset($_GET['idp'])){
	die;
}

$id_product = $_GET['idp'];

$declinaisons = get_declinaison($id_product);

echo json_encode($declinaisons);   

?>
