<?php

include(dirname(__FILE__) . '/config/config.inc.php');
include(dirname(__FILE__) . '/init.php');

$req_product = 'SELECT od.product_id, SUM(product_quantity) AS nb_vente FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od ON o.id_order = od.id_order WHERE o.valid = "1" AND o.date_add >= "'.(date('Y')-1).date('-m-d').' 00:00:00" AND o.date_add <= "'.date('Y-m-d').' 23:59:59" GROUP BY od.product_id ORDER BY nb_vente DESC;';
$resu_product = Db::getInstance()->ExecuteS($req_product);

$ordre = 1;
foreach ($resu_product as $rangee_product)
{
    //$req_up = 'INSERT INTO best SET id_product = "'.$rangee_product['product_id'].'", classement = "'.$ordre.'" ON DUPLICATE KEY UPDATE classement = "'.$ordre.'";';
    $req_up = 'UPDATE ps_category_product SET position = "'.$ordre.'" WHERE id_product = "'.$rangee_product['product_id'].'";';
    $resu_up = Db::getInstance()->Execute($req_up);
    $ordre++;
}

$req_product_n = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p WHERE p.id_product NOT IN (SELECT id_product FROM best);';
$resu_product_n = Db::getInstance()->ExecuteS($req_product_n);

$ordre = 99999;
foreach ($resu_product_n as $rangee_product_n)
{
    //$req_up = 'INSERT INTO best SET id_product = "'.$rangee_product_n['id_product'].'", classement = "'.$ordre.'" ON DUPLICATE KEY UPDATE classement = "'.$ordre.'";';
    $req_up = 'UPDATE ps_category_product SET position = "'.$ordre.'" WHERE id_product = "'.$rangee_product_n['product_id'].'";';
    $resu_up = Db::getInstance()->Execute($req_up);
}
?>