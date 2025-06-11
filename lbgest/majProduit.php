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


    /*$sql = 'SELECT *  FROM `ps_inventaire` WHERE `id_product` = "'.$_POST['id_product'].'" AND `id_product_attribute` = "'.$_POST['id_product_attribute'].'" ORDER BY date DESC LIMIT 0,1';

    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

                $tab_qty[$_POST['id_product']][$_POST['id_product_attribute']] = $res[0];

                    $nb_quantite_restant = $tab_qty[$_POST['id_product']][$_POST['id_product_attribute']]['valeur'];

                        $jour_inv = substr($res[0]['date'], 6, 2);
                        $mois_inv = substr($res[0]['date'], 4, 2);
                        $annee_inv = substr($res[0]['date'], 0, 4);
                        $heure_inv = substr($res[0]['date'], 8, 2);
                        $minutes_inv = substr($res[0]['date'], 10, 2);

                    $commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $_POST['id_product'] . '" AND pod.product_attribute_id = "' . $_POST['id_product_attribute'] . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '" AND (SELECT logable FROM ps_order_state WHERE id_order_state LIKE (SELECT id_order_state FROM ps_order_history WHERE id_order = po.id_order ORDER BY date_add DESC LIMIT 0,1)) LIKE 1;');

                    foreach ($commandes AS $commande)
                    {
                      $nb_quantite_restant -= $commande['product_quantity'];
                    }*/

                    $sql = 'SELECT *  FROM `ps_stock_available` WHERE `id_product` = "'.$_POST['id_product'].'" AND `id_product_attribute` = "'.$_POST['id_product_attribute'].'";';
                    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
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
$sql = $bdd->prepare('DELETE FROM operationnel WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";');
$sql->execute();

// MAJ STOCK SITE
$sqlAppro = $bdd->prepare('UPDATE ps_stock_available SET quantity = "'.$nouvelle_qt.'" WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";');
$sqlAppro->execute();

/*$sqlAppro = $bdd->prepare('UPDATE ps_product_attribute SET quantity = "'.$nouvelle_qt.'" WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "'.$_POST['id_product_attribute'].'";');
$sqlAppro->execute();*/

//$sqltot = 'SELECT SUM(quantity) as qttot FROM `ps_product_attribute` WHERE `id_product` = "'.$_POST['id_product'].'";';
$sqltot = 'SELECT SUM(quantity) as qttot FROM `ps_stock_available` WHERE `id_product` = "'.$_POST['id_product'].'" AND id_product_attribute <> "0";';
$restot = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqltot);

$sqlAppro = $bdd->prepare('UPDATE ps_stock_available SET quantity = "'.$restot[0]['qttot'].'" WHERE id_product = "'.$_POST['id_product'].'" AND id_product_attribute = "0";');
$sqlAppro->execute();

$sqlAppro = $bdd->prepare('UPDATE ps_product SET quantity = "'.$restot[0]['qttot'].'" WHERE id_product = "'.$_POST['id_product'].'";');
$sqlAppro->execute();

?>
