<?php
  ini_set('memory_limit', '-1');
  include("../config/config.inc.php");

  $groupe = array(
    '3857' => '1',
    '3858' => '2'
  );
  $patates_douces = array(3857,3858);

  function categorisation($id_product)
  {
    global $groupe;

    /*$req = 'SELECT * FROM ps_category_product WHERE id_product = "'.$id_product.'";';
    $T_lignes_p = Db::getInstance()->executeS($req);
    foreach($T_lignes_p as $T_ligne_p)
    {
        if ( isset($groupe[$T_ligne_p['id_category']]) && !empty($groupe[$T_ligne_p['id_category']]) )
        {
            return $groupe[$T_ligne_p['id_category']];
        }
    }
    //echo '$id_product : '.$id_product.'<br />';
    return 'autres';*/
    return $groupe[$id_product];
  }

  $req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_id IN (3857,3858) AND o.payment = "X" AND o.date_add >= "2024-06-27 00:00:00";';
  $T_lignes_dup = Db::getInstance()->executeS($req_dup);

  $cpt = 0;
  $cmd_a_creer = array();
  $cmd_ignore = array();
  foreach($T_lignes_dup as $T_ligne_commande)
  {
    $cpt++;

    $id_commande = $T_ligne_commande['id_order'];
    if ( !in_array($id_commande, $cmd_ignore) )
    {
        $order=new Order($id_commande);
        $custo = new Customer($order->id_customer);

        $keyEC = $id_commande;
        $req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od WHERE (0);';
        $T_lignes_dup = Db::getInstance()->executeS($req_dup);
        foreach($T_lignes_dup as $T_ligne_commande)
        {
            $keyEC .= '-'.$T_ligne_commande['id_order'];
        }

        $keyEC .= ';'.$custo->email;

        $T_pdts=$order->getProducts();
        foreach($T_pdts as $unpdts)
        {
            if ( in_array($unpdts['product_id'], $patates_douces) )
            {
                if (!isset($cmd_a_creer[$keyEC][categorisation($unpdts["product_id"])]))
                {
                    $cmd_a_creer[$keyEC][categorisation($unpdts["product_id"])] = 0;
                }
                $cmd_a_creer[$keyEC][categorisation($unpdts["product_id"])] += $unpdts["product_quantity"] - $unpdts["product_quantity_refunded"];
            }
        }
        $cmd_ignore[] = $id_commande;

                $req_dup1='SELECT DISTINCT od.id_order FROM `ps_order_detail` od WHERE (0);';
        $T_lignes_dup1 = Db::getInstance()->executeS($req_dup1);
        foreach($T_lignes_dup1 as $T_ligne_commande1)
        {
            $id_commande1 = $T_ligne_commande1['id_order'];
            if ( !in_array($id_commande1, $cmd_ignore) )
            {
                $order1=new Order($id_commande1);
                $T_pdts1=$order1->getProducts();
                foreach($T_pdts1 as $unpdts1)
                {
                    if ( in_array($unpdts1['product_id'], $patates_douces) )
                    {
                        if (!isset($cmd_a_creer[$keyEC][categorisation($unpdts1["product_id"])]))
                        {
                            $cmd_a_creer[$keyEC][categorisation($unpdts1["product_id"])] = 0;
                        }
                        $cmd_a_creer[$keyEC][categorisation($unpdts1["product_id"])] += $unpdts1["product_quantity"] - $unpdts1["product_quantity_refunded"];
                    }
                }
                $cmd_ignore[] = $id_commande1;
            }
        }
    }
  }
  /*echo '<pre>';
  print_r($cmd_a_creer);
  echo '</pre>';*/

  echo 'id_cmd;email;PLANT DE POIREAU ARMOR;PLANT DE POIREAU FARINTO<br />'."\n";
  foreach($cmd_a_creer as $cmd => $li)
  {
    echo $cmd.';'.$li[1].';'.$li[2].'<br />'."\n";
  }
