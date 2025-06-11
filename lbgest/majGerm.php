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

$lot_germination = '';
$id_product = pSQL($_POST['id_product']);
if(isset($_POST['lot_germination'])){
	$lot_germination = pSQL($_POST['lot_germination']);
}
	
if ( $_POST['status'] == 1 )
{	
	if(!$lot_germination){
		// on prend le premier lot si $lot_germination est vide
		$germ = Db::getInstance()->ExecuteS('SELECT * FROM germination WHERE id_product = "' . $id_product . '";');
		$lot_germination = $germ[0]['lot_germination'];
	}
	
	$sqlD = $bdd->prepare('DELETE FROM germination WHERE id_product = "'.$id_product.'" AND lot_germination = "'.$lot_germination.'" ;'); 	
	$sqlD->execute();
	
	$sqlF = Db::getInstance()->ExecuteS('SELECT * FROM `ps_inventaire_lots` WHERE `id_product` = "'.$id_product.'" AND `numero_lot_LBG` = "'.$lot_germination.'";');

    
    if ( !empty($sqlF[0]['percent_germination']) && $sqlF[0]['percent_germination'] > 0 )
    {
        $sql = $bdd->prepare('INSERT INTO AW_test_lots SET id_lot = "'.$sqlF[0]['id_inventaire_lots'].'", pourcentage_germ = "'.$sqlF[0]['percent_germination'].'", origine_test = "LBG", date_debut_semis = "'.date('Y-m-d').'", date_fin_test = "'.date('Y-m-d').'", date_etape_1 = "'.date('Y-m-d').'", resultat_etape_1 = "'.$sqlF[0]['percent_germination'].'", commentaire = "Origine : fournisseur";');  
    }
    else 
    {
        $sql = $bdd->prepare('INSERT INTO AW_test_lots SET id_lot = "'.$sqlF[0]['id_inventaire_lots'].'", pourcentage_germ = "110", origine_test = "LBG", date_debut_semis = "'.date('Y-m-d').'", date_fin_test = "'.date('Y-m-d').'", date_etape_1 = "'.date('Y-m-d').'", resultat_etape_1 = "110";');  
    }        
    $sql->execute();
}

if ( $_POST['status'] == 2 )
{
	
	if(!$lot_germination){
		// on prend le premier lot si $lot_germination est vide
		$germ = Db::getInstance()->ExecuteS('SELECT * FROM germination WHERE id_product = "' . $id_product . '";');
		$lot_germination = $germ[0]['lot_germination'];
	}
	
    $sqlD = $bdd->prepare('DELETE FROM germination WHERE id_product = "'.$id_product.'" AND lot_germination = "'.$lot_germination.'" ;');          
    $sqlD->execute();

    $sqlF = Db::getInstance()->ExecuteS('SELECT * FROM `ps_inventaire_lots` WHERE `id_product` = "'.$id_product.'" AND `numero_lot_LBG` = "'.$lot_germination.'";');
    
    $sql = $bdd->prepare('INSERT INTO AW_test_lots SET id_lot = "'.$sqlF[0]['id_inventaire_lots'].'", origine_test = "LBG", date_debut_semis = "'.date('Y-m-d').'";');          
    $sql->execute();

}

/*
$sql = 'SELECT * FROM `ps_stock_available` WHERE `id_product` = "'.$_POST['id_product'].'" AND `id_product_attribute` = "'.$_POST['id_product_attribute'].'";';
$nb_quantite_restant = $res[0]['quantity'];

if ( $nb_quantite_restant < 0 )
{
    $nb_quantite_restant = 0;
}

$def = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute WHERE id_product_attribute = "' . $_POST['id_product_attribute'] . '" AND id_product = "' . $_POST['id_product'] . '";');
if ( $def[0]['default_on'] == 1 )
{
    $_POST['sachet'] -= 3;
}

$nouvelle_qt = $nb_quantite_restant + $_POST['sachet'];

// MAJ DE LA QUANTITE PRODUITE
//echo 'INSERT INTO ps_inventaire SET date="'.date('YmdHis').'", id_product = "'.$_POST['id_product'].'", id_product_attribute = "'.$_POST['id_product_attribute'].'", valeur = "'.$nouvelle_qt.'";<br />'."\n";
$sql = $bdd->prepare('INSERT INTO ps_inventaire SET date="'.date('YmdHis').'", id_product = "'.$_POST['id_product'].'", id_product_attribute = "'.$_POST['id_product_attribute'].'", valeur = "'.$nouvelle_qt.'", type = "Conditionnement (R : '.$nb_quantite_restant.' + '.$_POST['sachet'].')";');          
$sql->execute();

// MAJ DU NOUVEAU STOCK TAMPON
//echo 'INSERT INTO ps_inventaire SET date="'.date('YmdHis').'", id_product = "'.$_POST['id_product'].'", id_product_attribute = "0", valeur = "'.$_POST['tampon'].'";<br />'."\n";
$sql = $bdd->prepare('INSERT INTO ps_inventaire SET date="'.date('YmdHis').'", id_product = "'.$_POST['id_product'].'", id_product_attribute = "0", valeur = "'.$_POST['tampon'].'", type = "Conditionnement (Tampon)";');          
$sql->execute();

// MAJ DU TABLEAU CONDITIONNEMENT
//echo 'DELETE FROM ps_operationnel WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";<br />'."\n";
$sql = $bdd->prepare('DELETE FROM ps_operationnel WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";');        
$sql->execute();

// MAJ STOCK SITE
$sqlAppro = $bdd->prepare('UPDATE ps_stock_available SET quantity = "'.$nouvelle_qt.'" WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";');
$sqlAppro->execute();
                        
$sqlAppro = $bdd->prepare('UPDATE ps_product_attribute SET quantity = "'.$nouvelle_qt.'" WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";');
$sqlAppro->execute();

$sqltot = 'SELECT SUM(quantity) as qttot FROM `ps_product_attribute` WHERE `id_product` = "'.$_POST['id_product'].'" AND `id_product_attribute` = "0";';
$restot = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqltot);

$sqlAppro = $bdd->prepare('UPDATE ps_stock_available SET quantity = "'.$restot[0]['qttot'].'" WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "0";');
$sqlAppro->execute();

$sqlAppro = $bdd->prepare('UPDATE ps_product SET quantity = "'.$restot[0]['qttot'].'" WHERE id_product = "'.$_POST['id_product'].'";');
$sqlAppro->execute();
*/
?>