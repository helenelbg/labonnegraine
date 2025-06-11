<?php
// ajax_get_test_germination.php
require 'application_top.php';

header('Content-Type: application/json');

if (!isset($_POST['id_test'])) {
    echo json_encode(['error' => 'ID test manquant']);
    exit;
}

$id_test = (int)$_POST['id_test'];

$sql = 'SELECT tl.*, il.numero_lot_LBG, il.id_product, pl.name, p.reference 
        FROM AW_test_lots tl 
        LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) 
        LEFT JOIN ps_product_lang pl ON (il.id_product = pl.id_product AND pl.id_lang = 1) 
        LEFT JOIN ps_product p ON (pl.id_product = p.id_product) 
        WHERE tl.id = "'.$id_test.'" AND tl.origine_test = "LBG"';

$result = Db::getInstance()->executeS($sql);

if (empty($result)) {
    echo json_encode(['error' => 'Test non trouvé']);
    exit;
}

$test = $result[0];

// Retourner les données au format JSON
echo json_encode($test);
?>