<?php
if ( isset($_FILES))
{
    echo '<pre>';
    print_r($_FILES);
    echo '</pre>';
}
if ( isset($_POST))
{
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
?>
<form id="form" enctype="multipart/form-data" method="post">
      <div class="input-group">
        <label for="files">Select files</label>
        <input id="file" name="fileUpload" type="file" />
        <input type="text" name="test" value="aa" />
      </div>
      <button class="submit-btn" type="submit">Upload</button>
    </form>
<?php

phpinfo();
die;
die;
require 'config/config.inc.php';
require 'init.php';

/*require_once(_PS_MODULE_DIR_."psaffiliate/psaffiliate.php");
require_once(_PS_MODULE_DIR_."psaffiliate/classes/Tracking.php");
require_once(_PS_MODULE_DIR_."psaffiliate/classes/Sale.php");


$ps = new Psaffiliate();

$array = array(196056);

foreach ($array as $id_order)
{
//$id_order = 194582;
$order = new Order($id_order);

$id_customer = $order->id_customer;

$commission = $ps->calculateCommission_debug($order, 8, false);
echo 'commission : '.$commission;
}*/


      /*Context::getContext()->customer=new Customer(93918);
      $new_cart=new Cart();
      $new_cart->id_customer=93918;
      $new_cart->id_lang = 2;
      $new_cart->id_currency=1;
      $new_cart->add();

      $cat = new Category(359);
      $prods=$cat->getProducts(1,1,2000);
      foreach($prods as $prod)
      {
        $attr = Product::getDefaultAttribute($prod['id_product']);
          $new_cart->updateQty(1, $prod['id_product'], $attr);
          $new_cart->update();
      }
      echo 'ok';*/
      $order = new Order(197716);
      $product_list_box = $order->getProducts();
		$only_box = true;
		$tab_box = array('1849', '1850', '1851');
		$only_patate_douce = true;
		$tab_patate_douce = array('1940', '1809', '1810', '1575', '1576', '1588', '2336', '1805', '2196', '2335', '2334', '2541', '2542');
		$only_plant_en_precommande = true;

        $only_plant = true;

		foreach ($product_list_box as $product_test)
		{
			if ( !in_array($product_test['product_id'], $tab_box) )
			{
				$only_box = false;
			}

            if ( !in_array($product_test['product_id'], $tab_patate_douce) && !Product::isPlantEnPrecommande($product_test['product_name'],$product_test['id_category_default']) )
			{
                echo $product_test['product_id'].' '.$product_test['product_name'].' '.$product_test['id_category_default'].'<br />';
                $only_plant = false;
            }

			/*if ( !in_array($product_test['product_id'], $tab_patate_douce) )
			{
				$only_patate_douce = false;
			}
			if ( !Product::isPlantEnPrecommande($product_test['product_name'],$product_test['id_category_default']) ){
				$only_plant_en_precommande = false;
			}*/
		}
echo '$only_plant : '.$only_plant;
