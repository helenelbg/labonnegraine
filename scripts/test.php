<?php
  include("../config/config.inc.php");

  $id_commande = 194306;
      $old_order=new Order($id_commande);


      $T_pdts_old=$old_order->getProducts();

      echo '<pre>';
        print_r($T_pdts_old);
      echo '</pre>';
    
      foreach($T_pdts_old as $unpdts_old)
      {
        if ( strpos($unpdts_old['product_name'], 'Conditionnement : plant') > 0 )
        {
            echo strpos($unpdts_old['product_name'], 'Conditionnement : plant').' - <b>'.$unpdts_old['product_name'].'</b><br />';
        }
        else
        {
            echo strpos($unpdts_old['product_name'], 'Conditionnement : plant') . ' - ' .$unpdts_old['product_name'].'<br />';
        }
      }
      ?>