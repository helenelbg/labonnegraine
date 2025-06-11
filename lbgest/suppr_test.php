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

$sqlD = $bdd->prepare('DELETE FROM germination WHERE id_test = "'.$_POST['id_test'].'";');
$sqlD->execute();
$sqlD2 = $bdd->prepare('DELETE FROM AW_test_lots WHERE id = "'.$_POST['id_test'].'";');
$sqlD2->execute();
