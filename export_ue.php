<?php
    $date_debut = $_GET['date_debut'];
    $date_fin = $_GET['date_fin'];
    $date_debut_format_array = explode("/", $date_debut);
    $date_fin_format_array = explode("/", $date_fin);

    $date_debut_format = $date_debut_format_array[2] . "-" . $date_debut_format_array[1] . "-" . $date_debut_format_array[0] . " 00:00:00";
    $date_fin_format = $date_fin_format_array[2] . "-" . $date_fin_format_array[1] . "-" . $date_fin_format_array[0] . " 23:59:59";

    $result_file = "";

    //$query_orders = "SELECT o.*, a.company, a.lastname, a.firstname, a2.id_country as country_deliv, a2.vat_number FROM ps_orders o,ps_address a,ps_address a2  WHERE o.module <> '1' AND o.invoice_date BETWEEN '" . $date_debut_format . "' AND '" . $date_fin_format . "' AND o.id_address_invoice = a.id_address AND o.id_address_delivery = a2.id_address ";

    $tab_pays = array();

    $query_orders = "SELECT c.name, od.id_order_detail, od.total_price_tax_incl, od.total_price_tax_excl FROM `ps_orders` o LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order) LEFT JOIN ps_address a ON (o.id_address_delivery = a.id_address) LEFT JOIN ps_country_lang c ON (a.id_country = c.id_country) WHERE o.current_state NOT IN (1,6,7,8,9,10,11,15,17,18,19,22,26,27,33,35,37) AND a.id_country IN (1,2,3,45,40,20,6,41,7,9,35,26,10,42,46,12,37,13,14,15,36,38,43,18,16) AND o.`date_add` BETWEEN CAST('" . $date_debut_format . "' AS DATE) AND CAST('" . $date_fin_format . "' AS DATE) AND c.id_lang = 1";

    $orders = Db::getInstance()->ExecuteS($query_orders);
    foreach ($orders as $order)
    {     
        $query_detail = "SELECT id_tax, total_amount FROM ps_order_detail_tax WHERE id_order_detail = '".$order['id_order_detail']."';";
        $detail_array = Db::getInstance()->ExecuteS($query_detail);

        $query_taxec = "SELECT rate FROM ps_tax WHERE id_tax = '".$detail_array[0]['id_tax']."' LIMIT 0,1;";
        $detail_taxec = Db::getInstance()->ExecuteS($query_taxec);
        
        if ( !isset($tab_pays[$order['name']][$detail_taxec[0]['rate']]) )
        {
            $tab_pays[$order['name']][$detail_taxec[0]['rate']] = 0;
        }
        //$tab_pays[$order['name']][$detail_taxec[0]['rate']] += $order['total_price_tax_incl'] - $order['total_price_tax_excl'];
        $tab_pays[$order['name']][$detail_taxec[0]['rate']] += $order['total_price_tax_incl'];
    }

    $query_orders2 = "SELECT c.name, o.total_shipping_tax_excl, o.total_shipping_tax_incl, o.carrier_tax_rate FROM `ps_orders` o LEFT JOIN ps_address a ON (o.id_address_delivery = a.id_address) LEFT JOIN ps_country_lang c ON (a.id_country = c.id_country) WHERE o.current_state NOT IN (1,6,7,8,9,10,11,15,17,18,19,22,26,27,33,35,37) AND a.id_country IN (1,2,3,45,40,20,6,41,7,9,35,26,10,42,46,12,37,13,14,15,36,38,43,18,16) AND o.`date_add` BETWEEN CAST('" . $date_debut_format . "' AS DATE) AND CAST('" . $date_fin_format . "' AS DATE) AND c.id_lang = 1";

    $orders2 = Db::getInstance()->ExecuteS($query_orders2);
    foreach ($orders2 as $order2)
    {            
        if ( !isset($tab_pays[$order2['name']][$order2['carrier_tax_rate']]) )
        {
            $tab_pays[$order2['name']][$order2['carrier_tax_rate']] = 0;
        }
        //$tab_pays[$order2['name']][$order2['carrier_tax_rate']] += $order2['total_shipping_tax_incl'] - $order2['total_shipping_tax_excl'];
        $tab_pays[$order2['name']][$order2['carrier_tax_rate']] += $order2['total_shipping_tax_incl'];
    }

    foreach ($tab_pays as $pays => $rateEC)
    {  
        foreach ($rateEC as $rateTaux => $val)
        {
            if ( !empty($rateTaux) && $rateTaux != '0.000' )
            {
                $debut_commun = "";
                $result_file .= $debut_commun;
                $result_file .= $pays.";".$rateTaux.";".$val."\n";
            }
        }  
    }

    $file_name = "exports_compta/export_CA_UE_du_".$date_debut_format_array[0] . "-" . $date_debut_format_array[1] . "-" . $date_debut_format_array[2]."_au_".$date_fin_format_array[0] . "-" . $date_fin_format_array[1] . "-" . $date_fin_format_array[2].".csv";
    $file = fopen($file_name,"w+");
    fwrite($file,utf8_decode($result_file));
    fclose($file);

?>