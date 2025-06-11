<?php
  ini_set('memory_limit', '-1');
  include("../config/config.inc.php");

  $groupe = array(
    '21' => '1',
    '71' => '1',
    '77' => '2',
    '195' => '3',
    '62' => '4',
    '46' => '5',
    '69' => '6',
    '45' => '7',
    '357' => '8',
    '390' => '9'
  );

  function categorisation($id_product)
  {
    global $groupe;

    $req = 'SELECT * FROM ps_category_product WHERE id_product = "'.$id_product.'";';
    $T_lignes_p = Db::getInstance()->executeS($req);
    foreach($T_lignes_p as $T_ligne_p)
    {
        if ( isset($groupe[$T_ligne_p['id_category']]) && !empty($groupe[$T_ligne_p['id_category']]) )
        {
            return $groupe[$T_ligne_p['id_category']];
        }
    }
    //echo '$id_product : '.$id_product.'<br />';
    return 'autres';
  }

  $req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10) AND od.id_order >= "220261" AND o.payment <> "X";';
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
        $req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE o.id_order <> "'.$id_commande.'" AND o.id_customer = "'.$order->id_customer.'" AND od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10,39) AND od.id_order >= "220261" AND o.payment <> "X";';
        //$req_dup='SELECT DISTINCT od.id_order FROM `ps_order_detail` od WHERE (0);';
        $T_lignes_dup = Db::getInstance()->executeS($req_dup);
        foreach($T_lignes_dup as $T_ligne_commande)
        {
            $keyEC .= '-'.$T_ligne_commande['id_order'];
        }

        $keyEC .= ';'.$custo->email;

        $T_pdts=$order->getProducts();
        foreach($T_pdts as $unpdts)
        {
            if ( strpos($unpdts['product_name'], 'Conditionnement : plant') > 0 )
            {
                if (!isset($cmd_a_creer[$keyEC][categorisation($unpdts["product_id"])]))
                {
                    $cmd_a_creer[$keyEC][categorisation($unpdts["product_id"])] = 0;
                }
                $cmd_a_creer[$keyEC][categorisation($unpdts["product_id"])] += $unpdts["product_quantity"] - $unpdts["product_quantity_refunded"];
            }
        }
        $cmd_ignore[] = $id_commande;

        $req_dup1='SELECT DISTINCT od.id_order FROM `ps_order_detail` od JOIN ps_orders o on (od.id_order=o.id_order) WHERE o.id_order <> "'.$id_commande.'" AND o.id_customer = "'.$order->id_customer.'" AND od.product_name LIKE "%Conditionnement : plant%" AND o.current_state NOT IN (1,6,7,8,10,39) AND od.id_order >= "220261" AND o.payment <> "X";';

                //$req_dup1='SELECT DISTINCT od.id_order FROM `ps_order_detail` od WHERE (0);';
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
                    if ( strpos($unpdts1['product_name'], 'Conditionnement : plant') > 0 )
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

  echo 'id_cmd;email;aubergines-poivrons-piments;tomates;concombres/cornichons;melons/pasteques;courgettes;patissons;courges/potirons;plants greffes;plants patates douces<br />'."\n";
  foreach($cmd_a_creer as $cmd => $li)
  {
    echo $cmd.';'.$li[1].';'.$li[2].';'.$li[3].';'.$li[4].';'.$li[5].';'.$li[6].';'.$li[7].';'.$li[8].';'.$li[9].'<br />'."\n";
  }
