<?php
$debugAW = 0;
$date_debut = $_GET['date_debut'];
$date_fin = $_GET['date_fin'];

$tabue = array(10,9,28,12,8,22,27,24,11,25,21,19,18,29,14,23,17,13,26,20);

$date_debut_format_array = explode("/", $date_debut);
$date_fin_format_array = explode("/", $date_fin);

$date_debut_format = $date_debut_format_array[2]."-".$date_debut_format_array[1]."-".$date_debut_format_array[0]." 00:00:00";
$date_fin_format = $date_fin_format_array[2]."-".$date_fin_format_array[1]."-".$date_fin_format_array[0]." 23:59:59";

$result_file = "";

//define('PS_ADMIN_DIR', getcwd());
//include(PS_ADMIN_DIR . '/config/config.inc.php');
/* Getting cookie or logout */
//require_once(dirname(__FILE__) . '/init.php');


$query_orders = "SELECT o.*, a.company, a.lastname, a.firstname, a2.id_country as country_deliv, a2.vat_number FROM ps_orders o,ps_address a,ps_address a2  WHERE  o.invoice_date BETWEEN '".$date_debut_format."' AND '".$date_fin_format."' AND o.id_address_invoice = a.id_address AND o.id_address_delivery = a2.id_address order by o.invoice_date";
$orders = Db::getInstance()->ExecuteS($query_orders);

    $debugAW_70730000_total = 0;
            $debugAW_70853000_total = 0;
            $debugAW_44571300_total = 0;
foreach($orders as $order)
{
    $debugAW_70730000 = 0;
            $debugAW_70853000 = 0;
            $debugAW_44571300 = 0;
            $debugAW_70853000_reduc = 0;
            $debugAW_44571300_tvareduc = 0;

$colonne1 = 0;
$colonne2 = 0;
    //R�cup�ration des zones
    $query_zone = "SELECT id_zone FROM ps_country WHERE id_country = '".$order['country_deliv']."';";
    $zone_array = Db::getInstance()->ExecuteS($query_zone);
    $id_zone = $zone_array[0]['id_zone'];

    //Calcul TVA
    $total_tva_produits = floatval($order['total_products_wt'])-floatval($order['total_products']);

    //R�cup�ration des r�ductions
    $reduc_20 = 0;    $reduc_10 = 0;    $montant_reduc_20 = 0;    $montant_reduc_10 = 0; $discountShippingAlone = 0;

    $query_cartrule = "SELECT id_cart_rule, value, value_tax_excl FROM ps_order_cart_rule WHERE id_order = '".$order['id_order']."';";
    if($cartrules = Db::getInstance()->ExecuteS($query_cartrule)) {
        foreach($cartrules as $onerule) {
            $query_freeship = "SELECT free_shipping  FROM ps_cart_rule WHERE id_cart_rule = '".$onerule['id_cart_rule']."';";
            $freeship_array = Db::getInstance()->ExecuteS($query_freeship);
            $freeshipping = $freeship_array[0]['free_shipping'];

            if($freeshipping == 1) {
                //$reduc_20 += $onerule['value_tax_excl'];
                $reduc_20 += round(($onerule['value']/$ratetwenty), 2);
                $montant_reduc_20 += $onerule['value'];
            }
            else {
                //$reduc_10 += $onerule['value_tax_excl'];
                $reduc_10 += round(($onerule['value']/$rateten), 2);
                $montant_reduc_10 += $onerule['value'];
                $discountShippingAlone += $onerule['value'];
            }
        }
    }

    //Taxes � 10 et 20


    $tvatwenty = 0; $tvatwentyamount = 0;
    $tvaten = 0; $tvatenamount = floatval($order['total_products_wt']);
    $tvazero = 0; $tvazeroamount = 0;
    $total_products_wt = 0;
    $rateten = 0;
    $ratetwenty = 0;

    $query_prod = "SELECT id_order_detail, product_price, tax_rate, total_price_tax_excl, total_price_tax_incl FROM ps_order_detail WHERE id_order = '".$order['id_order']."';";
    $prod_array = Db::getInstance()->ExecuteS($query_prod);
    foreach($prod_array as $id){

        $query_detail = "SELECT id_tax, total_amount FROM ps_order_detail_tax WHERE id_order_detail = '".$id['id_order_detail']."';";
        $detail_array = Db::getInstance()->ExecuteS($query_detail);

        $query_group = "SELECT id_tax_rules_group FROM ps_tax_rule WHERE id_tax = '".$detail_array[0]['id_tax']."' LIMIT 0,1;";
        $detail_group = Db::getInstance()->ExecuteS($query_group);

        $query_taxec = "SELECT rate FROM ps_tax WHERE id_tax = '".$detail_array[0]['id_tax']."' LIMIT 0,1;";
        $detail_taxec = Db::getInstance()->ExecuteS($query_taxec);

        /*if($detail_array[0]['id_tax'] == "1") {
            $tvatenamount -= $id['total_price_tax_incl'];
            $tvatwentyamount += $id['total_price_tax_incl'];
        }
        else if($detail_array[0]['id_tax'] == "2") {

        }*/
        if($detail_group[0]['id_tax_rules_group'] == "1") {
          $ratetwenty = ($detail_taxec[0]['rate']/100)+1;
            $tvatenamount -= $id['total_price_tax_incl'];
            $tvatwentyamount += $id['total_price_tax_incl'];
        }
        else if($detail_group[0]['id_tax_rules_group'] == "2") {
          $rateten = ($detail_taxec[0]['rate']/100)+1;
        }
        else
        {
            $tvatenamount -= $id['total_price_tax_incl'];
            $tvazeroamount += $id['total_price_tax_incl'];
        }
    }

    if ( $tvatwentyamount > 0 )
    {
        $tvatwenty = round($tvatwentyamount - ($tvatwentyamount / $ratetwenty),2);
    }
    if ( $tvatenamount > 0 )
    {
        $tvaten = round($tvatenamount - ($tvatenamount / $rateten),2);
    }

    $tvatenamount -= $tvaten;
    $tvatwentyamount -=  $tvatwenty;

    $total_tva_transport = (floatval($order['total_shipping'])-(floatval($order['total_shipping'])/(1+(floatval($order['carrier_tax_rate'])/100))));
    $order['total_transport'] = floatval($order['total_shipping'])/(1+(floatval($order['carrier_tax_rate'])/100));
    $total_tva = $total_tva_produits+$total_tva_transport;

    $debut_commun = "";

    $debut_commun .= "VEN;".$order['invoice_number'].";".dateFormat($order['invoice_date']).";";
    if(trim($order['company']) != "")
    {
        $debut_commun .= $order['company']." ";
    }
    $debut_commun .= $order['lastname']." ".$order['firstname'].";";
    $result_file .= $debut_commun;

    //Ligne montant total
    $result_file .= $order['id_customer'].";";
    $result_file .= "41100000;";
    $result_file .= round($order['total_paid'],2).";\n";
    $colonne1 += round($order['total_paid'],2);

    //Ligne produits
    $result_file .= $debut_commun;

    $code_shipping = "";
    $test_export = 0;

    if($id_zone == "1")// && $id_zone != "7")
{
    $result_file .= ";70780000;";
    $code_shipping = ";70853000";
}
elseif($id_zone == "7")
{
    if(trim($order['vat_number']) != "")
    {
        $test_export = 1;
        $result_file .= ";70791000;";
        $code_shipping = ";70889200";
    }
    else
    {
            //Calcul TVA si vide
        if($total_tva == 0)
        {
            $order['total_products'] = round(floatval($order['total_products_wt'])/(1+0.1),2);
            $total_tva_produits = round(floatval($order['total_products_wt'])-floatval($order['total_products']),2);
            $total_tva_transport = round(floatval($order['total_shipping'])-(floatval($order['total_shipping'])/(1+(floatval($order['carrier_tax_rate'])/100))),2);
            $total_tva = $total_tva_produits+$total_tva_transport;
        }
        $result_file .= ";70780000;";
            /*if ( $total_tva_transport != 0 )
            {*/
               $code_shipping = ";70853000";
            /*}
            else
            {
               $code_shipping = ";70850000";
           }*/
       }
   }
   else
   {
     if ( in_array($id_zone, $tabue) )
     {
       $result_file .= ";707911000;";
       $code_shipping = ";70889210";
     }
     else
     {
    $test_export = 1;
    $result_file .= ";70790000;";
    $code_shipping = ";70889100";
  }
}
$taxe = 0;
if(floatval($order['total_discounts']) != 0)
{
    if(floatval($total_tva) == 0)
    {
            //$result_file .= round($order['total_discounts_tax_excl'],2);
        $result_file .= round($discountShippingAlone,2);
        $colonne1 += round($discountShippingAlone,2);
     }
    else
    {
            //R�cup�ration de la taxe associ�e
        /* $query_tax = "SELECT tax_rate FROM ps_order_tax WHERE id_order = '".$order['id_order']."';";
        $taxe_array = Db::getInstance()->ExecuteS($query_tax);
        $taxe = $taxe_array[0]['tax_rate'];
        $taxe = round(floatval($taxe)/100 * floatval($order['total_discounts_tax_excl']),2); */

        if ( $test_export == 0 )
        {
               //$result_file .=  round(floatval($order['total_discounts_tax_excl']) - $taxe,2);
            $result_file .=  round($reduc_10,2);
            $colonne1 +=  round($reduc_10,2);
        }
        elseif ( $test_export == 1 )
        {
               //$result_file .=  round(floatval($order['total_discounts_tax_excl']),2);
            $result_file .=  round($montant_reduc_10,2);
            $colonne1 += round($montant_reduc_10,2);
        }
    }
}
$result_file .= ";";
if ( $test_export == 0 )
{
 $result_file .= round($tvatenamount,2)."\n"; //$order['total_products']
            $colonne2 += round($tvatenamount,2);
}
elseif ( $test_export == 1 )
{
   //$result_file .= $order['total_products']+$total_tva_produits."\n";
   $result_file .= round($tvatenamount,2)+$tvaten+round($tvatwentyamount,2)+$tvatwenty."\n"; //$order['total_products']
            $colonne2 += round($tvatenamount,2)+$tvaten+round($tvatwentyamount,2)+$tvatwenty;
}


    //Ligne TVA 20%
if($tvatwenty != 0 && $test_export == 0) {
    $result_file .= $debut_commun;

    $result_file .= ";70730000;";

    $result_file .= ";";
    /*if ( $test_export == 0 )
    {*/
        $result_file .= round($tvatwentyamount,2)."\n";
    $debugAW_70730000 = round($tvatwentyamount,2);
        $colonne2 += round($tvatwentyamount,2);
    /*}
    elseif ( $test_export == 1 )
    {
        $result_file .= round($tvatwentyamount,2)+$tvatwenty."\n";
        $debugAW_70730000 = round($tvatwentyamount,2)+$tvatwenty;
        $colonne2 += round($tvatwentyamount,2)+$tvatwenty;
    }*/

}
if ( round($tvazeroamount,2) != 0 )
{
     $result_file .= $debut_commun;
      //$result_file .= ';70975000;';
      $result_file .= ';70790000;';
    $result_file .= ";";
        $result_file .= round($tvazeroamount,2)."\n";
        $colonne2 += round($tvazeroamount,2);

}

   /* //Ligne R�duction
    if(floatval($order['total_discounts']) != 0)
    {
        $result_file .= $debut_commun;
        $result_file .= "7097;";
        $result_file .= $order['total_discounts'].";\n";
    }*/

    //Ligne Port
    if(floatval($order['total_transport']) != 0)
    {
        $result_file .= $debut_commun;
        $result_file .= $code_shipping.";";
        if($reduc_20) {
            if ( $test_export == 0 )
            {
                $result_file .= round($reduc_20,2).";";
                $debugAW_70853000_reduc = round($reduc_20,2);
                $colonne1 += round($reduc_20,2);
            }
            elseif ( $test_export == 1 )
            {
                $result_file .= round($montant_reduc_20,2).";";
                $debugAW_70853000_reduc = round($montant_reduc_20,2);
                $colonne1 += round($montant_reduc_20,2);
            }
        }
        else {
            $result_file .= ';';
        }
        if ( $test_export == 0 )
        {
           $result_file .= round($order['total_transport'],2)."\n";
           $debugAW_70853000 = round($order['total_transport'],2);
                $colonne2 += round($order['total_transport'],2);
       }
       elseif ( $test_export == 1 )
       {
           $result_file .= round($order['total_transport']+$total_tva_transport,2)."\n";
           $debugAW_70853000 = round($order['total_transport']+$total_tva_transport,2);
                $colonne2 += round($order['total_transport']+$total_tva_transport,2);
       }
   }

    //Ligne TVA
   $tva_reduc_1 = 0;
   $tva_reduc_2 = 0;
   if($reduc_10) {
    $tva_reduc_1 = $montant_reduc_10 - $reduc_10;
}

if($reduc_20)
{
  $tva_reduc_2 = $montant_reduc_20 - $reduc_20;
}

if(floatval($total_tva) != 0 && $test_export == 0)
{
  if ( in_array($id_zone, $tabue) )
  {
    $result_file .= $debut_commun;
    $result_file .= ";4453100;";

    if(($tva_reduc_1+$tva_reduc_2) != 0)
    {
      $result_file .= round($tva_reduc_1+$tva_reduc_2,2).";";
      $colonne1 += round($tva_reduc_1+$tva_reduc_2,2);
    }
    else
    {
        $result_file .= ';';
    }
    $result_file .= round($total_tva,2)."\n";
    $colonne2 += round($total_tva,2);

  }  
  else
  {
  if ( floatval($total_tva_transport) != 0 )
  {
    $result_file .= $debut_commun;
    $result_file .= ";44571800;";
    if($taxe != 0)
    {
      $result_file .= round($taxe,2).';';
      $colonne1 += round($taxe,2);
    }
    if($tva_reduc_1 != 0)
    {
      $result_file .= round($tva_reduc_1,2).";";
      $colonne1 += round($tva_reduc_1,2);
    }
    else
    {
        $result_file .= ';';
    }
    $result_file .= round($tvaten,2)."\n";
    $colonne2 += round($tvaten,2);

    $result_file .= $debut_commun;
    $result_file .= ";44571300;";

    if($tva_reduc_2 != 0)
    {
      $result_file .= round($tva_reduc_2,2).";";
      $debugAW_44571300_tvareduc = round($tva_reduc_2,2);
      $colonne1 += round($tva_reduc_2,2);
    }
    else
    {
      $result_file .= ';';
    }
    $debugAW_44571300 = round(($order['total_shipping'] - round($order['total_transport'],2)) + $tvatwenty,2);
    $result_file .= round(($order['total_shipping'] - round($order['total_transport'],2)) + $tvatwenty,2); //round($total_tva_transport,2)
    $colonne2 +=round( ($order['total_shipping'] - round($order['total_transport'],2)) + $tvatwenty,2);

    /* CALCUL TEST TVA 20% */
    /* (HT PRODUITS 20% + HT EXPEDITION) * 0.2 DEVRAIT ETRE EGAL A TOTAL TVA 20%*/
    $test20 = ($debugAW_70730000 + $debugAW_70853000) * 0.2;

    $debugAW_70730000 = 0;
    $debugAW_70853000 = 0;
    $debugAW_44571300 = 0;
    $debugAW_70853000_reduc = 0;
    $debugAW_44571300_tvareduc = 0;
    /* FIN CALCUL TEST TVA 20% */
    $result_file .= "\n";
  }
  else
  {
    $result_file .= $debut_commun;
    $result_file .= ";44571800;";
    if($taxe != 0)
    {
        $result_file .= round($taxe,2);
        $colonne1 += round($taxe,2);
    }
    if($order['total_discounts'] != 0) {
        $result_file .= round($tva_reduc_1,2).";";
        $colonne1 += round($tva_reduc_1,2);
    }
    else {
        $result_file .= ";";
    }
    $result_file .= round($tvaten,2)."\n";
    $colonne2 += round($tvaten,2);

    if ( $tvatwenty > 0 )
    {
      $result_file .= $debut_commun;
      $result_file .= ";44571300;";
      if($tva_reduc_2 != 0)
      {
        $result_file .= round($tva_reduc_2,2).";";
        $debugAW_44571300_tvareduc = round($tva_reduc_2,2);
        $colonne1 += round($tva_reduc_2,2);
      }
      else
      {
        $result_file .= ';';
      }
      $result_file .= round($tvatwenty,2); //round($total_tva_transport,2)
      $debugAW_44571300 = round($tvatwenty,2);
      $colonne2 +=round($tvatwenty,2);

      /* CALCUL TEST TVA 20% */
      /* (HT PRODUITS 20% + HT EXPEDITION) * 0.2 DEVRAIT ETRE EGAL A TOTAL TVA 20%*/
      $test20 = ($debugAW_70730000 + $debugAW_70853000) * 0.2;

      $debugAW_70730000 = 0;
      $debugAW_70853000 = 0;
      $debugAW_44571300 = 0;
      $debugAW_70853000_reduc = 0;
      $debugAW_44571300_tvareduc = 0;
      /* FIN CALCUL TEST TVA 20% */
      $result_file .= "\n";
    }
  }
  }
}

        $ecart = round($colonne1 - $colonne2,2);
        if ( $ecart > 0 )
        {
            $result_file .= $debut_commun;
            $result_file .= ";65800000;;";
             $result_file .= abs($ecart).";"."\n";
        }
        else if ( $ecart < 0 )
        {
            $result_file .= $debut_commun;
            $result_file .= ";75800000;";
             $result_file .= abs($ecart).";"."\n";
        }


}

/*
* ============================================== GESTION DES AVOIRS ======================================================
* DEBUT
* Andy - Anjou Web
* 30/04/2021
*/

// $result_file .= "\n===================== AVOIRS ==========================\n\n";

$query_orders = "SELECT c.*, s.*, o.invoice_number, a.id_country as country_deliv FROM `ps_order_slip` s INNER JOIN ps_customer c ON s.id_customer = c.id_customer INNER JOIN ps_orders o ON o.id_order = s.id_order INNER JOIN ps_address a ON a.id_customer = o.id_customer WHERE s.`date_add` BETWEEN '".$date_debut_format."' AND '".$date_fin_format."' AND o.id_address_delivery = a.id_address";
$orders = Db::getInstance()->ExecuteS($query_orders);

    $debugAW_70730000_total = 0;
            $debugAW_70853000_total = 0;
            $debugAW_44571300_total = 0;
foreach($orders as $order)
{
    $debugAW_70730000 = 0;
            $debugAW_70853000 = 0;
            $debugAW_44571300 = 0;
            $debugAW_70853000_reduc = 0;
            $debugAW_44571300_tvareduc = 0;

$colonne1 = 0;
$colonne2 = 0;
    //R�cup�ration des zones
    $query_zone = "SELECT id_zone FROM ps_country WHERE id_country = '".$order['country_deliv']."';";
    $zone_array = Db::getInstance()->ExecuteS($query_zone);
    $id_zone = $zone_array[0]['id_zone'];

    //Calcul TVA
    $prixTotProduitTTC = floatval($order['amount']) - floatval($order['shipping_cost_amount']);
    $total_tva_produits = $prixTotProduitTTC - ($prixTotProduitTTC / $rateten);

    //R�cup�ration des r�ductions
    $reduc_20 = 0;    $reduc_10 = 0;    $montant_reduc_20 = 0;    $montant_reduc_10 = 0; $discountShippingAlone = 0;

    $query_cartrule = "SELECT id_cart_rule, value, value_tax_excl FROM ps_order_cart_rule WHERE id_order = '".$order['id_order']."';";
    if($cartrules = Db::getInstance()->ExecuteS($query_cartrule)) {
        foreach($cartrules as $onerule) {
            $query_freeship = "SELECT free_shipping  FROM ps_cart_rule WHERE id_cart_rule = '".$onerule['id_cart_rule']."';";
            $freeship_array = Db::getInstance()->ExecuteS($query_freeship);
            $freeshipping = $freeship_array[0]['free_shipping'];

            if($freeshipping == 1) {
                //$reduc_20 += $onerule['value_tax_excl'];
                $reduc_20 += round(($onerule['value']/$ratetwenty), 2);
                $montant_reduc_20 += $onerule['value'];
            }
            else {
                //$reduc_10 += $onerule['value_tax_excl'];
                $reduc_10 += round(($onerule['value']/$rateten), 2);
                $montant_reduc_10 += $onerule['value'];
                $discountShippingAlone += $onerule['value'];
            }
        }
    }

    //Taxes � 10 et 20


    $tvatwenty = 0; $tvatwentyamount = 0;
    $tvaten = 0; $tvatenamount = floatval($order['amount']) - floatval($order['shipping_cost_amount']);
    $tvazero = 0; $tvazeroamount = 0;
    $total_products_wt = 0;

    // $query_prod = "SELECT id_order_detail, product_price, tax_rate, total_price_tax_excl, total_price_tax_incl FROM ps_order_detail WHERE id_order = '".$order['id_order']."';";
    $query_prod = "SELECT id_order_detail, amount_tax_incl FROM ps_order_slip_detail WHERE id_order_slip = '".$order['id_order_slip']."';";
    $prod_array = Db::getInstance()->ExecuteS($query_prod);
    foreach($prod_array as $id){

        $query_detail = "SELECT id_tax, total_amount FROM ps_order_detail_tax WHERE id_order_detail = '".$id['id_order_detail']."';";
        $detail_array = Db::getInstance()->ExecuteS($query_detail);

        $query_group = "SELECT id_tax_rules_group FROM ps_tax_rule WHERE id_tax = '".$detail_array[0]['id_tax']."' LIMIT 0,1;";
        $detail_group = Db::getInstance()->ExecuteS($query_group);

        $query_taxec = "SELECT rate FROM ps_tax WHERE id_tax = '".$detail_array[0]['id_tax']."' LIMIT 0,1;";
        $detail_taxec = Db::getInstance()->ExecuteS($query_taxec);

        if($detail_group[0]['id_tax_rules_group'] == "1") {
          $ratetwenty = ($detail_taxec[0]['rate']/100)+1;
            $tvatenamount -= $id['amount_tax_incl'];
            $tvatwentyamount += $id['amount_tax_incl'];
        }
        else if($detail_group[0]['id_tax_rules_group'] == "2") {
          $rateten = ($detail_taxec[0]['rate']/100)+1;
        }
        else
        {
            $tvatenamount -= $id['amount_tax_incl'];
            $tvazeroamount += $id['amount_tax_incl'];
        }
    }
    if ( $tvatwentyamount > 0 )
    {
        $tvatwenty = round($tvatwentyamount - ($tvatwentyamount / $ratetwenty),2);
    }
    if ( $tvatenamount > 0 )
    {
        $tvaten = round($tvatenamount - ($tvatenamount / $rateten),2);
    }

    $tvatenamount -= $tvaten;
    $tvatwentyamount -=  $tvatwenty;


    $total_tva_transport = floatval($order['shipping_cost_amount']) / $ratetwenty;
    $order['total_transport'] = floatval($order['total_shipping'])/(1+(floatval($order['carrier_tax_rate'])/100));
    $total_tva = $total_tva_produits+$total_tva_transport;

    $debut_commun = "";

    $debut_commun .= "VEN;".$order['invoice_number'].";".dateFormat($order['date_add']).";";
    if(trim($order['company']) != "")
    {
        $debut_commun .= $order['company']." ";
    }
    $debut_commun .= $order['lastname']." ".$order['firstname'].";";
    $result_file .= $debut_commun;

    //Ligne montant total
    $result_file .= $order['id_customer'].";";
    $result_file .= "41100000;";
    $result_file .= "-".round($order['amount'],2).";\n";
    $colonne1 += round($order['amount'],2);

    //Ligne produits
    $result_file .= $debut_commun;

    $code_shipping = "";
    $test_export = 0;



    if($id_zone == "1")// && $id_zone != "7")
    {
        $result_file .= ";70780000;";
        $code_shipping = ";70853000";
    }
    elseif($id_zone == "7")
    {
        if(trim($order['vat_number']) != "")
        {
            $test_export = 1;
            $result_file .= ";70791000;";
            $code_shipping = ";70889200";
        }
        else
        {
                //Calcul TVA si vide
            if($total_tva == 0)
            {
                $order['total_products'] = round(floatval($order['total_products_wt'])/(1+0.1),2);
                $total_tva_produits = round(floatval($order['total_products_wt'])-floatval($order['total_products']),2);
                $total_tva_transport = round(floatval($order['total_shipping'])-(floatval($order['total_shipping'])/(1+(floatval($order['carrier_tax_rate'])/100))),2);
                $total_tva = $total_tva_produits+$total_tva_transport;
            }
            $result_file .= ";70780000;";
                /*if ( $total_tva_transport != 0 )
                {*/
               $code_shipping = ";70853000";
                /*}
                else
                {
                   $code_shipping = ";70850000";
               }*/
       }
   }
   else
   {

      if ( in_array($id_zone, $tabue) )
      {
        $result_file .= ";707911000;";
        $code_shipping = ";70889210";
      }
      else
      {
        $test_export = 1;
        $result_file .= ";70790000;";
        $code_shipping = ";70889100";
      }
    }


$taxe = 0;

// if(floatval($order['total_discounts']) != 0)
// {
//     if(floatval($total_tva) == 0)
//     {
//             //$result_file .= round($order['total_discounts_tax_excl'],2);
//
//         $result_file .= round($discountShippingAlone,2);
//         $colonne1 += round($discountShippingAlone,2);
//      }
//     else
//     {
//             //R�cup�ration de la taxe associ�e
//
//         if ( $test_export == 0 )
//         {
//                //$result_file .=  round(floatval($order['total_discounts_tax_excl']) - $taxe,2);
//             $result_file .=  round($reduc_10,2);
//             $colonne1 +=  round($reduc_10,2);
//         }
//         elseif ( $test_export == 1 )
//         {
//                //$result_file .=  round(floatval($order['total_discounts_tax_excl']),2);
//             $result_file .=  round($montant_reduc_10,2);
//             $colonne1 += round($montant_reduc_10,2);
//         }
//     }
// }

$result_file .= ";";

if ( $test_export == 0 )
{
 $result_file .= "-".round($tvatenamount,2)."\n"; //$order['total_products']
            $colonne2 += round($tvatenamount,2);
}
elseif ( $test_export == 1 )
{
//    $result_file .= "-".round($tvatenamount,2)+$tvaten+round($tvatwentyamount,2)+$tvatwenty."\n"; //$order['total_products']
//             $colonne2 += round($tvatenamount,2)+$tvaten+round($tvatwentyamount,2)+$tvatwenty;
   $result_file .= "-".round($order['amount'],2)."\n"; //$order['total_products']
            $colonne2 += round($order['amount'],2);
}



    //Ligne TVA 20%
if($tvatwenty != 0 && $test_export == 0) {
    $result_file .= $debut_commun;

    $result_file .= ";70730000;";

    $result_file .= ";";
        $result_file .= "-".round($tvatwentyamount,2)."\n";
    $debugAW_70730000 = round($tvatwentyamount,2);
        $colonne2 += round($tvatwentyamount,2);


}
if ( round($tvazeroamount,2) != 0 )
{
     $result_file .= $debut_commun;
      //$result_file .= ';70975000;';
      $result_file .= ';70790000;';
    $result_file .= ";";
        $result_file .= "-".round($tvazeroamount,2)."\n";
        $colonne2 += round($tvazeroamount,2);

}

   /* //Ligne R�duction    }*/

    //Ligne Port
    if(floatval($order['shipping_cost_amount']) != 0)
    {
        $result_file .= $debut_commun;
        $result_file .= $code_shipping.";";
        if($reduc_20) {
            if ( $test_export == 0 )
            {
                $result_file .= "-".round($reduc_20,2).";";
                $debugAW_70853000_reduc = round($reduc_20,2);
                $colonne1 += round($reduc_20,2);
            }
            elseif ( $test_export == 1 )
            {
                $result_file .= "-".round($montant_reduc_20,2).";";
                $debugAW_70853000_reduc = round($montant_reduc_20,2);
                $colonne1 += round($montant_reduc_20,2);
            }
        }
        else {
            $result_file .= ';';
        }
        if ( $test_export == 0 )
        {
           $result_file .= "-".round($order['shipping_cost_amount'] / $ratetwenty,2)."\n";
           $debugAW_70853000 = round($order['shipping_cost_amount'] / $ratetwenty,2);
                $colonne2 += round($order['shipping_cost_amount'] / $ratetwenty,2);
       }
       elseif ( $test_export == 1 )
       {
           $result_file .= "-".round($order['shipping_cost_amount'] / $ratetwenty+$total_tva_transport,2)."\n";
           $debugAW_70853000 = round($order['shipping_cost_amount'] / $ratetwenty+$total_tva_transport,2);
                $colonne2 += round($order['shipping_cost_amount'] / $ratetwenty+$total_tva_transport,2);
       }
   }

    //Ligne TVA
   $tva_reduc_1 = 0;
   $tva_reduc_2 = 0;
   if($reduc_10) {
    $tva_reduc_1 = $montant_reduc_10 - $reduc_10;
}

if($reduc_20) {
    $tva_reduc_2 = $montant_reduc_20 - $reduc_20;
}


if(floatval($total_tva) != 0 && $test_export == 0)
{
    if ( in_array($id_zone, $tabue) )
  {
    $result_file .= $debut_commun;
    $result_file .= ";4453100;";

    if(($tva_reduc_1+$tva_reduc_2) != 0)
    {
      $result_file .= round($tva_reduc_1+$tva_reduc_2,2).";";
      $colonne1 += round($tva_reduc_1+$tva_reduc_2,2);
    }
    else
    {
        $result_file .= ';';
    }
    $result_file .= round($total_tva,2)."\n";
    $colonne2 += round($total_tva,2);

  }  
  else 
  {
    if ( floatval($total_tva_transport) != 0 )
    {

        $result_file .= $debut_commun;
        $result_file .= ";44571800;";
        if($taxe != 0)
        {
            $result_file .= "-".round($taxe,2).';';
                $colonne1 += round($taxe,2);
        }
        if($tva_reduc_1 != 0) {
            $result_file .= "-".round($tva_reduc_1,2).";";
                $colonne1 += round($tva_reduc_1,2);
        }
        else {
            $result_file .= ';';
        }
        $result_file .= "-".round($tvaten,2)."\n";
                $colonne2 += round($tvaten,2);


        $result_file .= $debut_commun;
        $result_file .= ";44571300;";
        /* if($taxe != 0)
        {
            $result_file .= round($taxe,2);
        } */
        if($tva_reduc_2 != 0) {
            $result_file .= "-".round($tva_reduc_2,2).";";
            $debugAW_44571300_tvareduc = round($tva_reduc_2,2);
                $colonne1 += round($tva_reduc_2,2);
        }
        else {
            $result_file .= ';';
        }
        $debugAW_44571300 = round(($order['total_shipping'] - round($order['total_transport'],2)) + $tvatwenty,2);
        $result_file .= "-".round($order['shipping_cost_amount'] - $order['shipping_cost_amount'] / $ratetwenty,2); //round($total_tva_transport,2)
                $colonne2 +=round($order['shipping_cost_amount'] - $order['shipping_cost_amount'] / $ratetwenty,2);

        /* CALCUL TEST TVA 20% */
        /* (HT PRODUITS 20% + HT EXPEDITION) * 0.2 DEVRAIT ETRE EGAL A TOTAL TVA 20%*/
        $test20 = ($debugAW_70730000 + $debugAW_70853000) * 0.2;
        /* if ($debugAW && round(($test20 - round(($order['total_shipping'] - round($order['total_transport'],2)) + $tvatwenty,2)),2) != 0 )
        { */
           // $result_file .= ';'.$debugAW_70730000.';'.$debugAW_70853000.';'.$debugAW_44571300.';'.$debugAW_70853000_reduc.';'.$debugAW_44571300_tvareduc.';'.$test20.';'.round(($test20 - $debugAW_44571300),2).';'.($debugAW_70730000_total+=$debugAW_70730000).';'.($debugAW_70853000_total+=$debugAW_70853000).';'.($debugAW_44571300_total+=$debugAW_44571300);
//         }

         $debugAW_70730000 = 0;
         $debugAW_70853000 = 0;
         $debugAW_44571300 = 0;
            $debugAW_70853000_reduc = 0;
            $debugAW_44571300_tvareduc = 0;
        /* FIN CALCUL TEST TVA 20% */
        $result_file .= "\n";
    }
    else
    {

        $result_file .= $debut_commun;
        $result_file .= ";44571800;";
        if($taxe != 0)
        {
            $result_file .= "-".round($taxe,2);
            $colonne1 += round($taxe,2);
        }
        if($order['total_discounts'] != 0) {
            $result_file .= "-".round($tva_reduc_1,2).";";
            $colonne1 += round($tva_reduc_1,2);
        }
        else {
            $result_file .= ";";
        }
        $result_file .= "-".round($tvaten,2)."\n";
        $colonne2 += round($tvaten,2);

        if ( $tvatwenty > 0 )
        {
            $result_file .= $debut_commun;
            $result_file .= ";44571300;";
            if($tva_reduc_2 != 0) {
                $result_file .= "-".round($tva_reduc_2,2).";";
                $debugAW_44571300_tvareduc = round($tva_reduc_2,2);
                $colonne1 += round($tva_reduc_2,2);
            }
            else {
                $result_file .= ';';
            }
            $result_file .= "-".round($tvatwenty,2); //round($total_tva_transport,2)
            $debugAW_44571300 = round($tvatwenty,2);
            $colonne2 +=round($tvatwenty,2);

            /* CALCUL TEST TVA 20% */
            /* (HT PRODUITS 20% + HT EXPEDITION) * 0.2 DEVRAIT ETRE EGAL A TOTAL TVA 20%*/
            $test20 = ($debugAW_70730000 + $debugAW_70853000) * 0.2;
            /* if ($debugAW && round(($test20 - $tvatwenty),2) != 0 )
            { */
               // $result_file .= ';'.$debugAW_70730000.';'.$debugAW_70853000.';'.$debugAW_44571300.';'.$debugAW_70853000_reduc.';'.$debugAW_44571300_tvareduc.';'.$test20.';'.round($test20 - $debugAW_44571300,2).';'.($debugAW_70730000_total+=$debugAW_70730000).';'.($debugAW_70853000_total+=$debugAW_70853000).';'.($debugAW_44571300_total+=$debugAW_44571300);
//             }
            $debugAW_70730000 = 0;
            $debugAW_70853000 = 0;
            $debugAW_44571300 = 0;
            $debugAW_70853000_reduc = 0;
            $debugAW_44571300_tvareduc = 0;
            /* FIN CALCUL TEST TVA 20% */
            $result_file .= "\n";
        }
    }
}
}

        $ecart = round($colonne1 - $colonne2,2);
        if ( $ecart > 0 )
        {
            $result_file .= $debut_commun;
            $result_file .= ";65800000;;";
             $result_file .= abs($ecart).";"."\n";
        }
        else if ( $ecart < 0 )
        {
            $result_file .= $debut_commun;
            $result_file .= ";75800000;";
             $result_file .= "-".abs($ecart).";"."\n";
        }
}

/*
* ============================================== GESTION DES AVOIRS ======================================================
* FIN
*/
//

$file_name = "exports_compta/export_compta_du_".$date_debut_format_array[0] . "-" . $date_debut_format_array[1] . "-" . $date_debut_format_array[2]."_au_".$date_fin_format_array[0] . "-" . $date_fin_format_array[1] . "-" . $date_fin_format_array[2].".csv";
$file = fopen($file_name,"w+");
fwrite($file,utf8_decode($result_file));
fclose($file);

function dateFormat($date)
{
    $explode_test = explode(' ', $date);
    $explode_date = explode("-", $explode_test[0]);
    return $explode_date[2].'/'.$explode_date[1]."/".$explode_date[0];
}

?>