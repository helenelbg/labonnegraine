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



$now = date('Y-m-d');
$id_lot = pSQL($_POST['id_lot']);
	
// insert into table AW_test_lots
$sql = 'INSERT INTO AW_test_lots (id_lot,date_debut_semis,origine_test) 
VALUES('.$id_lot.',"'.$now.'","LBG")';
$req = Db::getInstance()->Execute($sql);



echo 'ok';





?>
