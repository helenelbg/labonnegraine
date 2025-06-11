<?php
die;
include(dirname(__FILE__) . '/config/config.inc.php');
include(dirname(__FILE__) . '/init.php');


  $sql_product =  'SELECT DISTINCT id_product FROM ps_product;';    
  $result_p = Db::getInstance()->ExecuteS($sql_product);
  foreach ($result_p as $datas)
  {
      $total_p = 0;
      $req_i = 'SELECT * FROM `ps_stock_available` WHERE `id_product` ="'.$datas['id_product'].'";';
      $resu_i = Db::getInstance()->ExecuteS($req_i);
      foreach ($resu_i as $rangee_i)
      {
          if ( $rangee_i['id_product_attribute'] != 0 )
          {
            $total_p += $rangee_i['quantity'];
            /*$req_u = 'UPDATE ps_stock_available SET quantity = "'.$rangee_i['quantity'].'" WHERE id_product = "'.$datas['id_product'].'" AND id_product_attribute = "'.$rangee_i['id_product_attribute'].'";';
            echo $req_u.'<br />';
            //mysql_query ($req_u, $connexion);

            $req_u2 = 'UPDATE ps_product_attribute SET quantity = "'.$rangee_i['quantity'].'" WHERE id_product = "'.$datas['id_product'].'" AND id_product_attribute = "'.$rangee_i['id_product_attribute'].'";';
            echo $req_u2.'<br />';*/
            //mysql_query ($req_u2, $connexion);
          }

      }
      $req_u3 = 'UPDATE ps_product SET quantity = "'.$total_p.'" WHERE id_product = "'.$datas['id_product'].'";';
          //mysql_query ($req_u3, $connexion);
          Db::getInstance()->ExecuteS($req_u3);
            //echo $req_u3.'<br />';

          $req_u4 = 'UPDATE ps_stock_available SET quantity = "'.$total_p.'" WHERE id_product = "'.$datas['id_product'].'" AND id_product_attribute = "0";';
            //echo $req_u4.'<br /><br />';
            Db::getInstance()->ExecuteS($req_u4);
          //mysql_query ($req_u4, $connexion);
  }
