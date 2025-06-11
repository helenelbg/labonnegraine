<?php
die;
  include("../config/config.inc.php");

  $sql = 'SELECT distinct id_product FROM ps_category_product;';
  $produits = Db::getInstance()->executeS($sql);
  foreach ($produits as $rangee_c)
  {
	  $tab = array();

	  $sql_attr = 'SELECT ps_product_attribute_combination.id_attribute, ps_product_attribute.id_product_attribute FROM ps_product_attribute, ps_product_attribute_combination WHERE id_product = "'.$rangee_c['id_product'].'" AND ps_product_attribute.id_product_attribute=ps_product_attribute_combination.id_product_attribute;';
      $attributs = Db::getInstance()->executeS($sql_attr);
      foreach ($attributs as $rangee)
	  {
          $poids = 0;
      	  if($rangee['id_attribute'] == "41")
		  {
            $poids = '0.0002';
		  }
		  elseif($rangee['id_attribute'] == "42")  
		  {
            $poids = '0.0005';
		  }
		  elseif($rangee['id_attribute'] == "43")  
		  {
            $poids = '0.001';
		  }
		  elseif($rangee['id_attribute'] == "56")  
		  {
            $poids = '0.002';
		  }
		  elseif($rangee['id_attribute'] == "45")  
		  {
            $poids = '0.003';
		  }
		  elseif($rangee['id_attribute'] == "44")  
		  {
            $poids = '0.005';
		  }
		  elseif($rangee['id_attribute'] == "46")  
		  {
            $poids = '0.01';
		  }
		  elseif($rangee['id_attribute'] == "113")  
		  {
            $poids = '0.02';
		  }
		  elseif($rangee['id_attribute'] == "47")  
		  {
            $poids = '0.025';
		  }
		  elseif($rangee['id_attribute'] == "48")  
		  {
            $poids = '0.05';
		  }
		  elseif($rangee['id_attribute'] == "49")  
		  {
            $poids = '0.1';
		  }
		  elseif($rangee['id_attribute'] == "50")  
		  {
            $poids = '0.2';
		  }
		  elseif($rangee['id_attribute'] == "72")  
		  {
            $poids = '0.25';
		  }
		  elseif($rangee['id_attribute'] == "51")  
		  {
            $poids = '0.5';
		  }
		  elseif($rangee['id_attribute'] == "52")  
		  {
            $poids = '1';
		  }
		  elseif($rangee['id_attribute'] == "96")  
		  {
            $poids = '1.5';
		  }
		  elseif($rangee['id_attribute'] == "102")  
		  {
            $poids = '2';
		  }
		  elseif($rangee['id_attribute'] == "82")  
		  {
            $poids = '5';
		  }
		  elseif($rangee['id_attribute'] == "95")  
		  {
            $poids = '1';
		  }
		  elseif($rangee['id_attribute'] == "67")  
		  {
            $poids = '1.5';
		  }
		  elseif($rangee['id_attribute'] == "68")  
		  {
            $poids = '3';
		  }
		  elseif($rangee['id_attribute'] == "69")  
		  {
            $poids = '5';
		  }
		  elseif($rangee['id_attribute'] == "106")  
		  {
            $poids = '15';
		  }
		  elseif($rangee['id_attribute'] == "105")  
		  {
            $poids = '25';
		  }
		  elseif($rangee['id_attribute'] == "256")  
		  {
            $poids = '3.5';
		  }
		  elseif($rangee['id_attribute'] == "3836")  
		  {
            $poids = '0.75';
		  }
		  elseif($rangee['id_attribute'] == "40")  
		  {
            $poids = '0.0001';
		  }
		  elseif($rangee['id_attribute'] == "5317")  
		  {
            $poids = '0.004';
		  }
		  elseif($rangee['id_attribute'] == "10083")  
		  {
            $poids = '1.5';
		  }
		  elseif($rangee['id_attribute'] == "10538")  
		  {
            $poids = '4';
		  }
		  elseif($rangee['id_attribute'] == "10555")  
		  {
            $poids = '0.00025';
		  }
		  elseif($rangee['id_attribute'] == "10559")  
		  {
            $poids = '0.008';
		  }
		  elseif($rangee['id_attribute'] == "10560")  
		  {
            $poids = '0.0003';
		  }
		  elseif($rangee['id_attribute'] == "10561")  
		  {
            $poids = '0.00025';
		  }
		  if ( $poids > 0 )
          {
            $query_update = "UPDATE ps_product_attribute SET weight = '".$poids."' WHERE id_product_attribute='".$rangee['id_product_attribute']."';";
            echo $query_update.' (p = '.$rangee_c['id_product'].')<br />';
            Db::getInstance()->execute($query_update);
          }
          //mysql_query ( $query_update, $connexion );
	  }
  }
