<?php
  // Création des commandes patates douces
die;
  if (!isset($_GET['secure']) && $_GET['secure'] != '1548dfs5656' )
  {
    die();
  }

  ini_set('memory_limit', '-1');
  include("../config/config.inc.php");

/*
  $reqc='SELECT DISTINCT(o.id_order) FROM `ps_orders` o LEFT JOIN ps_order_history oh ON o.id_order = oh.id_order WHERE oh.id_order_state = 39;';
  echo  $reqc;
  $T_lignes_reqc = Db::getInstance()->executeS($reqc);
  foreach($T_lignes_reqc as $T_ligne_c)
{  
  $reqp='SELECT id_order_detail, product_id, pa.reference, p.reference as refp FROM `ps_order_detail` od LEFT JOIN ps_product p ON od.product_id = p.id_product LEFT JOIN ps_product_attribute pa ON od.product_attribute_id = pa.id_product_attribute WHERE od.id_order = "'.$T_ligne_c['id_order'].'";';

      $T_lignes_reqp = Db::getInstance()->executeS($reqp);
      foreach($T_lignes_reqp as $T_ligne_p)
    {

      $queryProductAttribute = "SELECT ps_product_attribute.id_product_attribute FROM ps_product_attribute 
                                          INNER JOIN ps_product_attribute_combination on ps_product_attribute_combination.id_product_attribute = ps_product_attribute.id_product_attribute
                                          WHERE id_product = '".$T_ligne_p["product_id"]."' 
                                          AND ps_product_attribute_combination.id_attribute = '10512'";
                $attributeRef = Db::getInstance()->ExecuteS($queryProductAttribute);
                $updateRef = !empty($attributeRef[0]['id_product_attribute']) ? $attributeRef[0]['id_product_attribute'] : '';
                if(!empty($updateRef)) {
                    
               if ( empty($T_ligne_p["reference"]))
               {
                $T_ligne_p["reference"] = $T_ligne_p["refp"];
               }


                  $req_updeta='UPDATE `'._DB_PREFIX_.'order_detail` SET product_attribute_id = "'.$updateRef.'", product_reference = "'.$T_ligne_p["reference"].'" WHERE id_order_detail = "'.$T_ligne_p["id_order_detail"].'";';
                  //$req_updeta='UPDATE `'._DB_PREFIX_.'order_detail` SET product_reference = "'.$T_ligne_p["reference"].'" WHERE id_order_detail = "'.$T_ligne_p["id_order_detail"].'";';
echo $req_updeta.'<br />';
Db::getInstance()->executeS($req_updeta);
}
    }
}

die;
  function deleteorderbyid($id)
    {
            $shopid = 1;
            $thisorder = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_cart FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $id . ' AND id_shop = ' . $shopid);
            if (isset($thisorder[0])) {
                $q = 'DELETE a,b FROM ' . _DB_PREFIX_ . 'order_return AS a LEFT JOIN ' . _DB_PREFIX_ . 'order_return_detail AS b ON a.id_order_return = b.id_order_return WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                // deleting order_slip
                $q = 'DELETE a,b FROM ' . _DB_PREFIX_ . 'order_slip AS a LEFT JOIN ' . _DB_PREFIX_ . 'order_slip_detail AS b ON a.id_order_slip = b.id_order_slip WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart="' . $thisorder[0]['id_cart'] . '" AND id_shop = "' . $shopid . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_history WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_detail_tax WHERE id_order_detail IN (SELECT id_order_detail FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order ="' . $id . '")';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order="' . $id . '" AND id_shop = "' . $shopid . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference IN (SELECT reference FROM ' . _DB_PREFIX_ . 'orders WHERE id_order="' . $id . '")';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'orders WHERE id_order="' . $id . '" AND id_shop = "' . $shopid . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_carrier WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_invoice_tax WHERE id_order_invoice IN (SELECT id_order_invoice FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order="' . $id . '")';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_invoice_payment WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_cart_rule WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }


            }
    }
    $reqc='SELECT id_order FROM `ps_orders` o WHERE o.id_order >= 218853 AND o.current_state = "6" AND o.payment = "X";';
    $T_lignes_reqc = Db::getInstance()->executeS($reqc);
    foreach($T_lignes_reqc as $T_ligne_c)
  {
      $reqp='SELECT reference FROM `ps_order_detail` od LEFT JOIN ps_product_attribute pa ON od.product_attribute_id = pa.id_product_attribute WHERE od.id_order = "'.$T_ligne_c['id_order'].'";';
      $T_lignes_reqp = Db::getInstance()->executeS($reqp);
      foreach($T_lignes_reqp as $T_ligne_p)
    {

    }
  }


  die;*/


  $array_cmd = array();
  $histo = array();

  function dupliquer_commande($id_commande)
  {
    global $histo;
    if ( !in_array($id_commande, $histo) )
    {
      $old_order=new Order($id_commande);

      $id_transporteur_livraison=$old_order->id_carrier;

      $id_produit_fictif=1972;
      $id_declinaison_fictive=0;
      $etat_commande=22; //3 preparation en cours

      $T_pdts_old=$old_order->getProducts();

      $temp_custo = new Customer($old_order->id_customer);

      Context::getContext()->customer=new Customer($old_order->id_customer);
      $new_cart=new Cart();
      $new_cart->id_customer=$old_order->id_customer;
      $new_cart->id_address_delivery =$old_order->id_address_delivery;
      $new_cart->id_address_invoice = $old_order->id_address_invoice;
      $new_cart->id_lang = 2;
      $new_cart->id_cart=1;
      $new_cart->id_currency=1;
      $new_cart->recyclable = 0;
      $new_cart->gift = 0;
      $new_cart->id_carrier=$id_transporteur_livraison;
      $new_cart->payment=0;
      $new_cart->module=0;
      $new_cart->total_paid=0;
      $new_cart->total_paid_real=0;
      $new_cart->total_products=0;
      $new_cart->total_products_wt=0;
      $new_cart->conversion_rate=0;
      $new_cart->total_products_wt=0;

      $new_cart->add();

      Context::getContext()->cart=$new_cart;

      $T_pdts_old=$old_order->getProducts();

      foreach($T_pdts_old as $unpdts_old)
      {
        if ( strpos($unpdts_old['product_name'], 'Conditionnement : plant') > 0 )
        {
          //$new_cart->updateQty($unpdts_old["product_quantity"], $id_produit_fictif, $unpdts_old["product_attribute_id"]);
          $qt1 = $unpdts_old["product_quantity"]-$unpdts_old["product_quantity_refunded"];

          if ( $qt1 > 0 )
          {
            $new_cart->updateQty($qt1, $unpdts_old["product_id"], $unpdts_old["product_attribute_id"]);
            $new_cart->update();
          }
        }
      }

      $req_dup_reg='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE o.id_customer = "'.$old_order->id_customer.'" AND o.id_order <> "'.$id_commande.'" AND od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >="194180";';

      $T_lignes_dup_reg = Db::getInstance()->executeS($req_dup_reg);

      foreach($T_lignes_dup_reg as $T_ligne_commande_reg)
      {
        $old_order_reg=new Order($T_ligne_commande_reg['id_order']);
        $histo[] = $T_ligne_commande_reg['id_order'];

        $T_pdts_old_reg=$old_order_reg->getProducts();
        foreach($T_pdts_old_reg as $unpdts_old_reg)
        {
          if ( strpos($unpdts_old_reg['product_name'], 'Conditionnement : plant') > 0 )
          {
            //$new_cart->updateQty($unpdts_old_reg["product_quantity"], $id_produit_fictif, $unpdts_old_reg["product_attribute_id"]);
            $qt2 = $unpdts_old_reg["product_quantity"]-$unpdts_old_reg["product_quantity_refunded"];
            if ( $qt2 > 0 )
            {
              $new_cart->updateQty($qt2, $unpdts_old_reg["product_id"], $unpdts_old_reg["product_attribute_id"]);
              $new_cart->update();
            }
          }
        }
      }

      do
      $reference = Order::generateReference();
      while(Order::getByReference($reference)->count());


      $new_order=new Order();
      $new_order->reference = $reference;
      $new_order->id_customer=$old_order->id_customer;
      $new_order->id_cart=$new_cart->id;
      $new_order->id_currency=1;
      $new_order->id_carrier=$id_transporteur_livraison;
      $new_order->payment="X";
      $new_order->module="1";
      $new_order->total_paid=0;
      $new_order->total_paid_real=0;
      $new_order->total_products=0;
      $new_order->total_products_wt=0;
      $new_order->conversion_rate=1;
      $new_order->id_shop = 1;
      $new_order->id_lang = 2;
      $new_order->valid = 1;
      $new_order->secure_key = md5(uniqid(rand(), true));
      $new_order->id_address_delivery=$old_order->id_address_invoice;
      $new_order->id_address_invoice=$old_order->id_address_invoice;
      $new_order->id_customer=$old_order->id_customer;
      $new_order->current_state=$etat_commande;
      $new_order->add();

      $history = new OrderHistory();
	$history->id_order = $new_order->id;
    $history->id_order_state = $etat_commande;
	$history->add();
	$history->changeIdOrderState($etat_commande, $new_order->id);

      //----------- BOUCLE LES LIGNE ************
      foreach($T_pdts_old as $unpdts_old)
      {
        if ( strpos($unpdts_old['product_name'], 'Conditionnement : plant') > 0 )
        {
          $qt3 = $unpdts_old["product_quantity"]-$unpdts_old["product_quantity_refunded"];
          if ( $qt3 > 0 )
          {
            $new_order_detail=new OrderDetail();
            $new_order_detail->id_order=$new_order->id;
            //$new_order_detail->product_id=$id_produit_fictif;
            $new_order_detail->product_id=$unpdts_old["product_id"];
            $new_order_detail->id_order_invoice=0;
            $new_order_detail->id_shop=0;
            $new_order_detail->id_warehouse =0;
            $new_order_detail->product_price=0;
            $new_order_detail->product_attribute_id=$unpdts_old["product_attribute_id"];
            $new_order_detail->product_name=$unpdts_old["product_name"];
            $new_order_detail->product_quantity=$qt3;
            $new_order_detail->add();
          }
        }
      }

      $req_dup_reg='SELECT DISTINCT od.id_order FROM `'.
              _DB_PREFIX_.'order_detail` od JOIN '._DB_PREFIX_.'orders o on (od.id_order=o.id_order) '
              . ' WHERE o.id_customer = "'.$old_order->id_customer.'" AND o.id_order <> "'.$id_commande.'" AND o.id_order <> "'.$new_order->id.'" AND od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >="194180";';
      $T_lignes_dup_reg = Db::getInstance()->executeS($req_dup_reg);

      foreach($T_lignes_dup_reg as $T_ligne_commande_reg)
      {
        $old_order_reg=new Order($T_ligne_commande_reg['id_order']);
        $histo[] = $T_ligne_commande_reg['id_order'];

        $T_pdts_old_reg=$old_order_reg->getProducts();
        foreach($T_pdts_old_reg as $unpdts_old_reg)
        {
            $req_deta='SELECT DISTINCT id_order_detail FROM `'._DB_PREFIX_.'order_detail` WHERE id_order = "'.$new_order->id.'" AND product_attribute_id = "'.$unpdts_old_reg["product_attribute_id"].'";';

            $T_lignes_deta = Db::getInstance()->executeS($req_deta);
            $aup = true;
           // print_r($T_lignes_deta);
            foreach($T_lignes_deta as $T_ligne_deta)
            {
                if ( isset($T_ligne_deta['id_order_detail']) && !empty($T_ligne_deta['id_order_detail']) )
                {

                $aup = false;
                $req_updeta='UPDATE `'._DB_PREFIX_.'order_detail` SET product_quantity = product_quantity + '.($unpdts_old_reg["product_quantity"]-$unpdts_old_reg["product_quantity_refunded"]).' WHERE id_order = "'.$new_order->id.'" AND product_attribute_id = "'.$unpdts_old_reg["product_attribute_id"].'";';

                Db::getInstance()->executeS($req_updeta);
                }
            }
            if ( $aup == true )
            {
              if ( strpos($unpdts_old_reg['product_name'], 'Conditionnement : plant') > 0 )
              {
                $qt4 = $unpdts_old_reg["product_quantity"]-$unpdts_old_reg["product_quantity_refunded"];
          if ( $qt4 > 0 )
          {
          $new_order_detail=new OrderDetail();
          $new_order_detail->id_order=$new_order->id;
          //$new_order_detail->product_id=$id_produit_fictif;
          $new_order_detail->product_id=$unpdts_old_reg["product_id"];
          $new_order_detail->id_order_invoice=0;
          $new_order_detail->id_shop=0;
          $new_order_detail->id_warehouse =0;
          $new_order_detail->product_price=0;
          $new_order_detail->id_shop=0;
          $new_order_detail->id_shop=0;
          $new_order_detail->product_attribute_id=$unpdts_old_reg["product_attribute_id"];
          $new_order_detail->product_name=$unpdts_old_reg["product_name"];
          $new_order_detail->product_quantity=$qt4;
          $new_order_detail->add();
          }
              }
            }
        }
      }
      //-*********************************************

      $order_carrier = new OrderCarrier();
      $order_carrier->id_order = $new_order->id;
      $order_carrier->id_carrier = $id_transporteur_livraison;
      $order_carrier->weight = 0;
      $order_carrier->shipping_cost_tax_excl = 0;
      $order_carrier->shipping_cost_tax_incl = 0;
      $order_carrier->add();

      $id_cart=$new_cart->id;

      $req_testcoli='SELECT * FROM `'._DB_PREFIX_.'colissimo_order` WHERE id_order = "'.$old_order->id.'";';
      $testcoliRangee = Db::getInstance()->executeS($req_testcoli);
      $testcoli = $testcoliRangee[0];
      if ( !empty($testcoli['id_order']) )
      {
        $req_colissimo="INSERT INTO `ps_colissimo_order` (id_order, id_colissimo_service, id_colissimo_pickup_point, migration, ddp, ddp_cost, hidden)
            VALUE ('".$new_order->id."', '".$testcoli['id_colissimo_service']."', '".$testcoli['id_colissimo_pickup_point']."', '".$testcoli['migration']."', '".$testcoli['ddp']."', '".$testcoli['ddp_cost']."', '".$testcoli['hidden']."')";
        Db::getInstance()->executeS($req_colissimo);
      }

      $req_testdpd='SELECT * FROM `'._DB_PREFIX_.'dpdfrance_shipping` WHERE id_cart = "'.$old_order->id_cart.'";';
      $testdpdRangee = Db::getInstance()->executeS($req_testdpd);
      $testdpd = $testdpdRangee[0];
      if ( !empty($testdpd['id_cart']) )
      {
        $req_dpd="INSERT INTO `ps_dpdfrance_shipping` (id_cart, id_customer, id_carrier, service, relay_id,company, address1, address2,postcode, city,id_country,gsm_dest)
            VALUE ('".$new_cart->id."', '".$testdpd['id_customer']."', '".$testdpd['id_carrier']."', '".$testdpd['service']."', '".$testdpd['relay_id']."', '".$testdpd['company']."', '".$testdpd['address1']."', '".$testdpd['address2']."', '".$testdpd['postcode']."', '".$testdpd['city']."', '".$testdpd['id_country']."', '".$testdpd['gsm_dest']."')";
        Db::getInstance()->executeS($req_dpd);
      }

      $array_cmd[$temp_custo->email]['ancienne'][] = $id_commande;
      $array_cmd[$temp_custo->email]['nouvelle'][] = $new_order->id;

      $histo[] = $id_commande;
    }
  }


  //$req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >="194180";';
  $req_dup='SELECT DISTINCT od.id_order, o.id_customer FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >= "194306";';
  echo $req_dup.'<br />';
  $T_lignes_dup = Db::getInstance()->executeS($req_dup);

  $cpt = 0;
  $cmd_a_creer = array();
  foreach($T_lignes_dup as $T_ligne_commande)
  {
    $cpt++;

    $reqc='SELECT id_order FROM `ps_orders` o WHERE o.current_state = "22" AND o.id_customer = "'.$T_ligne_commande['id_customer'].'" AND o.payment = "X";';
    $T_lignes_reqc = Db::getInstance()->executeS($reqc);
    $testc = $T_lignes_reqc[0];
    if ( !empty($testc['id_order']) )
    {

    }
    else
    {
      dupliquer_commande($T_ligne_commande['id_order']);
    }
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
