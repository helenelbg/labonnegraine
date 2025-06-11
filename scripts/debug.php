<?php
die;
ini_set('memory_limit', '-1');
include("../config/config.inc.php");
/*
$array = array(
  220441,
222411,
221318,
221167,
221490,
225035,
225012,
220408,
220369,
221148,
220808,
224983,
225039,
225030,
223531,
221386,
225025,
220940
);

foreach($array as $cmd)
{
    $req = 'SELECT c.email FROM ps_orders o LEFT JOIN ps_customer c ON o.id_customer = c.id_customer WHERE o.id_order = "'.$cmd.'";';
    $rangee = Db::getInstance()->executeS($req);

      //----------- BOUCLE LES LIGNE ************
      foreach($rangee as $val)
      {
        echo $cmd.';'.$val['email'].'<br />';
        //$req_updeta='UPDATE `'._DB_PREFIX_.'order_detail` SET product_quantity = product_quantity / 3 WHERE id_order = "'.$cmd.'";';
        Db::getInstance()->executeS($req_updeta);
      }
}*/

$req_dup='SELECT DISTINCT od.id_order, o.id_customer FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_id IN (3857,3858) AND o.payment = "X" AND o.date_add >= "2024-06-27 00:00:00";';
$T_lignes_dup = Db::getInstance()->executeS($req_dup);

foreach($T_lignes_dup as $T_ligne_commande)
{
  $req_v='SELECT product_id, product_attribute_id, product_quantity FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE o.id_customer = "'.$T_ligne_commande['id_customer'].'" AND od.product_id IN (3857,3858) AND o.payment = "X" AND o.date_add >= "2024-01-01 00:00:00" AND o.date_add <= "2024-06-01 00:00:00";';
  echo $req_v.'<br />';
  $T_lignes_v = Db::getInstance()->executeS($req_v);
  foreach($T_lignes_v as $T_ligne_v)
  {
    $req_aj = 'UPDATE ps_order_detail SET product_quantity = (product_quantity - '.$T_ligne_v['product_quantity'].') WHERE id_order = "'.$T_ligne_commande['id_order'].'" AND product_id = "'.$T_ligne_v['product_id'].'" AND product_attribute_id = "'.$T_ligne_v['product_attribute_id'].'";';
    echo $req_aj.'<br />';
    Db::getInstance()->executeS($req_aj);
  }

}