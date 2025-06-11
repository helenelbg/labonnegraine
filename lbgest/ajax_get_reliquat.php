<?php

if ( $_POST['token'] != 'hdf6dfdfs6ddgs' )
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

if(!isset($_POST['date_reliquat'])){
	die;
}

$date_reliquat = pSQL($_POST['date_reliquat']);

$fin_datetime = new DateTime($date_reliquat);
$debut = $fin_datetime->format('Y-m-d');
$fin_datetime->add(new DateInterval('P1M')); // P1M = Plus 1 Month
$fin = $fin_datetime->format('Y-m-d');

$sql = 'SELECT fd.id_cmd, s.name as nom_fournisseur, fd.id_produit, p.reference, pl.name, fd.qte, fd.unite, fd.date_reliquat FROM cmd_fournisseur_detail fd
LEFT JOIN ps_product p ON fd.id_produit = p.id_product
LEFT JOIN ps_product_lang pl ON fd.id_produit = pl.id_product
LEFT JOIN cmd_fournisseur cf ON (cf.id_cmd = fd.id_cmd)
LEFT JOIN ps_supplier s ON (cf.id_fournisseur = s.id_supplier)
WHERE pl.id_lang = 1 AND fd.id_etat = 4 AND date_reliquat >= "'.$debut.'" AND date_reliquat < "'.$fin.'"';
$res = Db::getInstance()->ExecuteS($sql);

echo json_encode($res);   

?>
