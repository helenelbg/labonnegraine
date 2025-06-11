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

foreach (getProducts2(2) as $product)
{
  $declinaisons = get_declinaison($product['id_product']);
  foreach ($declinaisons as $dec_prod)
  {
      $aa = get_quantity($dec_prod['id_product'], $dec_prod['id_product_attribute']);
      $bb = get_quantity2($dec_prod['id_product'], $dec_prod['id_product_attribute']);

      $jour_inv = substr($aa['date'], 6, 2);
      $mois_inv = substr($aa['date'], 4, 2);
      $annee_inv = substr($aa['date'], 0, 4);
      $heure_inv = substr($aa['date'], 8, 2);
      $minutes_inv = substr($aa['date'], 10, 2);

      $commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $dec_prod['id_product_attribute'] . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '" AND po.valid = 1 AND po.current_state NOT IN (6,8,7) AND (SELECT logable FROM ps_order_state WHERE id_order_state LIKE (SELECT id_order_state FROM ps_order_history WHERE id_order = po.id_order ORDER BY date_add DESC LIMIT 0,1)) LIKE 1;');

      //echo 'SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $attrib['id_product_attribute'] . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '" AND (SELECT logable FROM ps_order_state WHERE id_order_state LIKE (SELECT id_order_state FROM ps_order_history WHERE id_order = po.id_order ORDER BY date_add DESC LIMIT 0,1)) LIKE 1;';

      foreach ($commandes AS $commande)
      {
        $aa['valeur'] -= $commande['product_quantity'];
      }
      if ( $aa['valeur'] < 0 )
      {
          $aa['valeur'] = 0;
      }

      if ( ($aa['valeur'] <> $bb['quantity']) && $dec_prod['default_on'] == 0 )
      {
        $ecart = ($bb['quantity'] - $aa['valeur']);
        $color = 'black';
        if ( abs($ecart) >= 5 )
        {
          $color = 'red';
        }
        echo '<span style="color:'.$color.'">'.$ecart.'</span>;'.$aa['valeur'].';'.$bb['quantity'].';'.$product['name'].';'.$dec_prod['name'].'<br />';
      }
      $dd = ($aa['valeur']-3);
      if ( $dd < 0 )
      {
        $dd = 0;
      }
      if ( ( $dd <> $bb['quantity']) && $dec_prod['default_on'] == 1 )
      {
        $ecart = ($bb['quantity'] - ($dd));
        $color = 'black';
        if ( abs($ecart) >= 5 )
        {
          $color = 'red';
        }
        echo '<span style="color:'.$color.'">'.$ecart.'</span>;'.($dd).';'.$bb['quantity'].';'.$product['name'].';'.$dec_prod['name'].'<br />';
      }
  }
}

function getProducts2($id_lang){
global $id_category;
/*$sql = 'SELECT p.`id_product` , p.reference, c.id_category, c.id_parent, pl.`name` , IFNULL( stock.quantity, 0 ) AS quantity
FROM  `ps_product` p
LEFT JOIN ps_stock_available stock ON ( stock.id_product = p.id_product
AND stock.id_product_attribute =0
AND stock.id_shop =1 )
LEFT JOIN  `ps_product_lang` pl ON p.`id_product` = pl.`id_product`
AND pl.id_shop =1
INNER JOIN ps_product_shop product_shop ON ( product_shop.id_product = p.id_product
AND product_shop.id_shop =1 )
LEFT JOIN  `ps_category_product` cp ON p.`id_product` = cp.`id_product`
LEFT JOIN  `ps_category` c ON c.id_parent = '.$id_category.'
WHERE p.active  = 1 AND pl.`id_lang` = '.(int)$id_lang.'
AND cp.id_category = '.$id_category.' OR cp.id_category = c.id_category
ORDER BY  `pl`.`name` ASC ';*/


$sql = 'SELECT p.`id_product` , p.reference, pl.`name` , IFNULL( stock.quantity, 0 ) AS quantity
    FROM  `ps_product` p
    LEFT JOIN ps_stock_available stock ON ( stock.id_product = p.id_product
    AND stock.id_product_attribute =0
    AND stock.id_shop =1 )
    LEFT JOIN  `ps_product_lang` pl ON p.`id_product` = pl.`id_product`
    AND pl.id_shop =1
    INNER JOIN ps_product_shop product_shop ON ( product_shop.id_product = p.id_product
    AND product_shop.id_shop =1 )
    WHERE p.active  = 1 AND p.visibility <> "none" AND pl.`id_lang` = '.(int)$id_lang.' AND (p.reference LIKE "0-%" OR p.reference LIKE "1-%" OR p.reference LIKE "2-%" OR p.reference LIKE "3-%" OR p.reference LIKE "4-%") AND p.reference NOT LIKE "0-9%"
    AND p.id_category_default NOT IN (129,135,132,131,133,134,213)
    ORDER BY  `pl`.`name` ASC ';

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
}

function get_declinaison($id_product){
    $sql = 'SELECT *, sa.quantity as qte, pa.weight as poids FROM `ps_product_attribute` AS pa
    LEFT JOIN `ps_product_attribute_combination` AS pac ON pac.id_product_attribute = pa.id_product_attribute
    LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
    LEFT JOIN `ps_attribute_lang` AS al ON al.id_attribute = pac.id_attribute
    LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
    WHERE pa.id_product = '.$id_product.' AND al.id_lang = 1 and a.id_attribute_group IN (6,8) GROUP BY pa.id_product_attribute ORDER BY pa.default_on DESC, a.position ASC';

    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    return $res;
}

function get_quantity($id_product, $id_attr){
    $sql = 'SELECT *  FROM `ps_inventaire` WHERE `id_product` = "'.$id_product.'" AND `id_product_attribute` = "'.$id_attr.'" ORDER BY date DESC ';

    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    return $res[0];
}
function get_quantity2($id_product, $id_attr){
    $sql = 'SELECT *  FROM `ps_stock_available` WHERE `id_product` = "'.$id_product.'" AND `id_product_attribute` = "'.$id_attr.'";';

    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    return $res[0];
}
