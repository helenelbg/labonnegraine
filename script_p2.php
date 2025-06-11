<?php
die;
include(dirname(__FILE__) . '/config/config.inc.php');
include(dirname(__FILE__) . '/init.php');

$req = 'SELECT distinct o.id_order 
FROM ps_orders o 
LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order) 
WHERE o.date_add >= "2024-01-01 00:00:00" AND o.payment <> "X" LIMIT 0,1000;';


$resu = Db::getInstance()->ExecuteS($req);

$array = array('seul' => 0, 'mix' => 0);

foreach ($resu as $rangee)
{
    //$req_1 = 'SELECT distinct product_attribute_id FROM ps_order_detail od2 LEFT JOIN ps_product_attribute_combination pa ON (od2.product_attribute_id = pa.id_product_attribute AND pa.id_attribute IN (10513,10512)) WHERE od2.id_order = "'.$rangee['id_order'].'";';
    $req_1 = 'SELECT distinct product_attribute_id FROM ps_order_detail od2 WHERE od2.id_order = "'.$rangee['id_order'].'" AND product_attribute_id IN (SELECT id_product_attribute FROM ps_product_attribute_combination WHERE id_attribute IN (10513,10512));';
    //echo $req_1.'<br />';
    $resu_1 = Db::getInstance()->ExecuteS($req_1);
    //print_r($resu_1);

    $req_2 = 'SELECT distinct product_attribute_id FROM ps_order_detail od2 WHERE od2.id_order = "'.$rangee['id_order'].'" AND product_attribute_id NOT IN (SELECT id_product_attribute FROM ps_product_attribute_combination WHERE id_attribute IN (10513,10512));';
    //echo $req_2.'<br />';
    $resu_2 = Db::getInstance()->ExecuteS($req_2);
    //print_r($resu_2);

    if ( count($resu_1) > 0 && count($resu_2) == 0 )
    {
        $array['seul']++;
        echo $rangee['id_order'].' / '.count($resu_1).' / ' . count($resu_2) .'<br />';
    }
    elseif ( count($resu_1) > 0 && count($resu_2) > 0 )
    {
        $array['mix']++;
        echo $rangee['id_order'].' / '.count($resu_1).' / ' . count($resu_2) .'<br />';
    }
}
print_r($array);
?>