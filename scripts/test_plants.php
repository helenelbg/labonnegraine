<?php
  // Création des commandes patates douces

  if (!isset($_GET['secure']) && $_GET['secure'] != '1548dfs5656' )
  {
    die();
  }

  ini_set('memory_limit', '-1');
  include("../config/config.inc.php");

  $array_cmd = array();
  $histo = array();

  function dupliquer_commande($id_commande)
  {
    global $histo;
    if ( !in_array($id_commande, $histo) )
    {
      $old_order=new Order($id_commande);

      $T_pdts_old=$old_order->getProducts();

      Context::getContext()->customer=new Customer($old_order->id_customer);

      $T_pdts_old=$old_order->getProducts();

      

      $req_dup_reg='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE o.id_customer = "'.$old_order->id_customer.'" AND o.id_order <> "'.$id_commande.'" AND od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >= "220261" AND o.payment <> "X";';

      $T_lignes_dup_reg = Db::getInstance()->executeS($req_dup_reg);


      //----------- BOUCLE LES LIGNE ************
      foreach($T_pdts_old as $unpdts_old)
      {
        if ( strpos($unpdts_old['product_name'], 'Conditionnement : plant') > 0 )
        {
          $qt3 = $unpdts_old["product_quantity"]-$unpdts_old["product_quantity_refunded"];
          if ( $qt3 > 0 )
          {
            echo $id_commande.' - '.$unpdts_old["product_name"] . ' - ' .$unpdts_old["product_id"].' - '.$unpdts_old["product_attribute_id"].' - '.$qt3.'<br />';
          }
        }
      }

      $req_dup_reg='SELECT DISTINCT od.id_order FROM `'.
              _DB_PREFIX_.'order_detail` od JOIN '._DB_PREFIX_.'orders o on (od.id_order=o.id_order) '
              . ' WHERE o.id_customer = "'.$old_order->id_customer.'" AND o.id_order <> "'.$id_commande.'" AND od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >= "220261" AND o.payment <> "X";';
      $T_lignes_dup_reg = Db::getInstance()->executeS($req_dup_reg);

      foreach($T_lignes_dup_reg as $T_ligne_commande_reg)
      {
        $old_order_reg=new Order($T_ligne_commande_reg['id_order']);
        $histo[] = $T_ligne_commande_reg['id_order'];

        $T_pdts_old_reg=$old_order_reg->getProducts();
        foreach($T_pdts_old_reg as $unpdts_old_reg)
        {
            /*$req_deta='SELECT DISTINCT id_order_detail FROM `'._DB_PREFIX_.'order_detail` WHERE id_order = "'.$new_order->id.'" AND product_attribute_id = "'.$unpdts_old_reg["product_attribute_id"].'";';

            $T_lignes_deta = Db::getInstance()->executeS($req_deta);
            $aup = true;
           // print_r($T_lignes_deta);
            foreach($T_lignes_deta as $T_ligne_deta)
            {
                if ( isset($T_ligne_deta['id_order_detail']) && !empty($T_ligne_deta['id_order_detail']) )
                {*/

                $aup = false;

                echo $T_ligne_commande_reg['id_order'].' - '.$unpdts_old_reg["product_name"] . ' - ' .$unpdts_old_reg["product_id"].' - '.$unpdts_old_reg["product_attribute_id"].' - '.($unpdts_old_reg["product_quantity"]-$unpdts_old_reg["product_quantity_refunded"]).'<br />';
                /*}
            }*/
            if ( $aup == true )
            {
              if ( strpos($unpdts_old_reg['product_name'], 'Conditionnement : plant') > 0 )
              {
                $qt4 = $unpdts_old_reg["product_quantity"]-$unpdts_old_reg["product_quantity_refunded"];
          if ( $qt4 > 0 )
          {
          
          echo '?? - '.$unpdts_old_reg["product_name"] . ' - ' .$unpdts_old_reg["product_id"].' - '.$unpdts_old_reg["product_attribute_id"].' - '.$qt4.'<br />';
          }
              }
            }
        }
      }
      //-*********************************************

      $histo[] = $id_commande;
    }
  }


  //$req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >="194180";';
  $req_dup='SELECT DISTINCT od.id_order, o.id_customer FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order = "210975" AND o.payment <> "X";';
  echo $req_dup.'<br />';
  $T_lignes_dup = Db::getInstance()->executeS($req_dup);

  $cpt = 0;
  $cmd_a_creer = array();
  foreach($T_lignes_dup as $T_ligne_commande)
  {
    $cpt++;

    /*$reqc='SELECT id_order FROM `ps_orders` o WHERE o.current_state = "39" AND o.id_customer = "'.$T_ligne_commande['id_customer'].'" AND o.payment = "X";';
    $T_lignes_reqc = Db::getInstance()->executeS($reqc);
    $testc = $T_lignes_reqc[0];
    if ( !empty($testc['id_order']) )
    {

    }
    else
    {*/
      dupliquer_commande($T_ligne_commande['id_order']);
    //}
  }

  echo $cpt.' COMMANDES CREES.<br /><br />';

  echo 'Email,ID commandes origines,ID commandes nouvelles<br />'."\n";
  foreach ($array_cmd as $mail => $cmds)
  {
    //if ( count($cmds['ancienne']) > 1 )
    //{
      echo $mail .',';
      foreach ($cmds['ancienne'] as $cl => $num)
      {
        if ( $cl > 0 )
        {
          echo ' - ';
        }
        echo $num;
      }
      echo ',';
      foreach ($cmds['nouvelle'] as $cl2 => $num2)
      {
        if ( $cl2 > 0 )
        {
          echo ' - ';
        }
        echo $num2;
      }
      echo '<br />'."\n";
    //}
  }
?>
