<?php
	
if ( $_POST['token'] != 'hdf6dfdfs6ddgs' )
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

$json = array();
$json['msg'] = '';

if( $_POST['action'] == 'maj_ventilation'){
	$checked = $_POST['checked'];
	$id_ventilation_array = json_decode(stripslashes($_POST['id_ventilation']));
	foreach($id_ventilation_array as $key => $id_ventilation){
		if($checked == 'true'){
			$sql = 'INSERT IGNORE INTO ventilation (id)
			VALUES ("'.pSQL($id_ventilation).'")';
			$req = Db::getInstance()->Execute($sql);
		}else{
			$sql = 'DELETE FROM ventilation WHERE id = "'.pSQL($id_ventilation).'"';
			$req = Db::getInstance()->Execute($sql);
			error_log($sql);
		}
	}
 
	$json['msg'] = 'ok';
	
	echo (json_encode($json));
}
?>
