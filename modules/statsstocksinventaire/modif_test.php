<?php

   $host = '92.243.24.83';
$dbname = 'lbg';
$user = 'lbg';
$pwd = 'bgtlR-2d';


if (isset($_POST['id']) && !empty($_POST['id'])) {
	$id = $_POST['id'];
}
else{
	die();
}
if (isset($_POST['id_lot']) && !empty($_POST['id_lot'])) {
	$id_lot = $_POST['id_lot'];
}
else{
	$id_lot = ' ';
}
if (isset($_POST['date_debut_test']) && !empty($_POST['date_debut_test'])) {
	$date_debut_test = $_POST['date_debut_test'];
}
else{
	$date_debut_test = '0000-00-00';
}
if (isset($_POST['date_etape_1']) && !empty($_POST['date_etape_1'])) {
	$date_etape_1 = $_POST['date_etape_1'];
}
else{
	$date_etape_1 = '0000-00-00';
}
if (isset($_POST['resultat_etape_1']) && !empty($_POST['resultat_etape_1'])) {
	$resultat_etape_1 = $_POST['resultat_etape_1'];
}
else{
	$resultat_etape_1 = '0';
}
if (isset($_POST['date_etape_2']) && !empty($_POST['date_etape_2'])) {
	$date_etape_2 = $_POST['date_etape_2'];
}
else{
	$date_etape_2 = '0000-00-00';
}
if (isset($_POST['resultat_etape_2']) && !empty($_POST['resultat_etape_2'])) {
	$resultat_etape_2 = $_POST['resultat_etape_2'];
}
else{
	$resultat_etape_2 = '0';
}
if (isset($_POST['date_etape_3']) && !empty($_POST['date_etape_3'])) {
	$date_etape_3 = $_POST['date_etape_3'];
}
else{
	$date_etape_3 = '0000-00-00';
}
if (isset($_POST['resultat_etape_3']) && !empty($_POST['resultat_etape_3'])) {
	$resultat_etape_3 = $_POST['resultat_etape_3'];
}
else{
	$resultat_etape_3 = '0';
}
/*if (isset($_POST['date_fin_test']) && !empty($_POST['date_fin_test'])) {
	$date_fin_test = $_POST['date_fin_test'];
}
else{
	$date_fin_test = '0000-00-00';
}*/
if (isset($_POST['commentaire_test']) && !empty($_POST['commentaire_test'])) {
	$commentaire_test = $_POST['commentaire_test'];
}
else{
	$commentaire_test = ' ';
}
if (isset($_POST['origine_test']) && !empty($_POST['origine_test'])) {
	$origine_test = $_POST['origine_test'];
}
else{
	$origine_test = ' ';
}


try{
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $user, $pwd);
}

catch (exeption $ex) {
	die("probleme connexion serveur" . $ex->getMessage());
}

$sql = 'UPDATE AW_test_lots (id_lot, date_debut_semis, resultat_etape_1, date_etape_2, resultat_etape_2, date_etape_3, resultat_etape_3, date_fin_test, commentaire, origine_test) SET ('.$id_lot.', "'.$date_debut_test.'", '.$resultat_etape_1.', "'.$date_etape_2.'", '.$resultat_etape_2.', "'.$date_etape_3.'", '.$resultat_etape_3.', "'.$date_fin_test.'", "'.$commentaire_test.'", "'.$origine_test.'") WHERE id='.$id;
echo $sql;
$req = $bdd->prepare("UPDATE AW_test_lots SET id_lot = :id_lot, date_debut_semis = :date_debut, date_etape_1 = :date_etape_1, resultat_etape_1 = :resultat_etape_1, date_etape_2 = :date_etape_2, resultat_etape_2 = :resultat_etape_2, date_etape_3 = :date_etape_3, resultat_etape_3 = :resultat_etape_3, date_fin_test = :date_fin_test, commentaire = :commentaire , origine_test = :origine_test WHERE id=".$id);
$req->execute(array(
	"id_lot" => $id_lot,
	"date_debut" => $date_debut_test,
	"date_etape_1" => $date_etape_1,
	"resultat_etape_1" => $resultat_etape_1,
	"date_etape_2" => $date_etape_2,
	"resultat_etape_2" => $resultat_etape_2,
	"date_etape_3" => $date_etape_3,
	"resultat_etape_3" => $resultat_etape_3,
	"date_fin_test" => '0000-00-00',
	"commentaire" => $commentaire_test,
        "origine_test" => $origine_test,
	));

?>
