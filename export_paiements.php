<?php

$date_debut = $_GET['date_debut'];
$date_fin = $_GET['date_fin'];
$date_debut_format_array = explode("/", $date_debut);
$date_fin_format_array = explode("/", $date_fin);

$date_debut_format = $date_debut_format_array[2] . "-" . $date_debut_format_array[1] . "-" . $date_debut_format_array[0] . " 00:00:00";
$date_fin_format = $date_fin_format_array[2] . "-" . $date_fin_format_array[1] . "-" . $date_fin_format_array[0] . " 23:59:59";

$result_file = "";
//define('PS_ADMIN_DIR', getcwd());
//include(PS_ADMIN_DIR . '/config/config.inc.php');
/* Getting cookie or logout */
//require_once(dirname(__FILE__) . '/init.php');

$query_orders = "SELECT o.*, a.company, a.lastname, a.firstname, a2.id_country as country_deliv, a2.vat_number FROM ps_orders o,ps_address a,ps_address a2  WHERE o.module <> '1' AND o.invoice_date BETWEEN '" . $date_debut_format . "' AND '" . $date_fin_format . "' AND o.id_address_invoice = a.id_address AND o.id_address_delivery = a2.id_address ";
$orders = Db::getInstance()->ExecuteS($query_orders);
//echo $query_orders;
foreach ($orders as $order)
{

    $paiement_COMPTE = "";
    if($order['module'] == "payline")
    {
        $paiement_COMPTE = ";51122000";
        $order['payment'] = "Carte bancaire";
    }
    elseif($order['module'] == "payplug")
    {
        $paiement_COMPTE = ";51126000";
        $order['payment'] = "Payplug Carte bancaire";
    }
    elseif($order['module'] == "PayPlug")
    {
        $paiement_COMPTE = ";51126000";
        $order['payment'] = "Payplug Carte bancaire";
    }
    elseif($order['module'] == "Scalapay Payment Method")
    {
        $paiement_COMPTE = ";51127000";
        $order['payment'] = "Scalapay Carte bancaire";
    }
    elseif($order['module'] == "cheque")
    {
        $paiement_COMPTE = ";51121000";
        $order['payment'] = "Cheque";
    }
    elseif($order['module'] == "paypal")
    {
        $paiement_COMPTE = ";51123000";
        $order['payment'] = "Paypal";
    }
    elseif($order['module'] == "bankwire")
    {
        $paiement_COMPTE = ";51124000";
        $order['payment'] = "Virement";
    }
    elseif($order['module'] == "mandatadministratif")
    {
        $paiement_COMPTE = ";51125000";
        $order['payment'] = "Mandat administratif";
    }
    else
    {
        $paiement_COMPTE = ";51125000";
        $order['payment'] = "Autres (".$order['module'].")";
    }








    $debut_commun = "";
    $debut_commun .= "TER;".$order['invoice_number'].";".dateFormat($order['invoice_date']).";";
    $debut_commun .= "Règlement ".$order['payment']. " ";
    if(trim($order['company']) != "")
    {
        $debut_commun .= $order['company']." ";
    }
    $debut_commun .= $order['lastname']." ".$order['firstname'].";";
   // $debut_commun .= $order['id_customer'].";";

    $order_obj = new Order($order['id_order']);

    if(intval($order_obj->getCurrentState()) != 6 && intval($order_obj->getCurrentState()) != 8 && intval($order_obj->getCurrentState()) != 1 && intval($order_obj->getCurrentState()) != 10 && intval($order_obj->getCurrentState()) != 11)
    {
        //Type de paiement
        $result_file .= $debut_commun;
        $result_file .= $order['id_customer'].";";
        $result_file .= "41100000;;".$order['total_paid_real']."\n";
        $result_file .= $debut_commun;
        $result_file .= $paiement_COMPTE.";".$order['total_paid_real'].";\n";
        //Remboursements
        if (intval($order_obj->getCurrentState()) == 7)
        {
            $result_file .= $debut_commun;
            $result_file .= $order['id_customer'].";";
            $result_file .= "41100000;".$order['total_paid_real'].";\n";
            $result_file .= $debut_commun;
            $result_file .= $paiement_COMPTE.";;".$order['total_paid_real']."\n";
        }
    }
}
$file_name = "exports_compta/export_paiements_du_".$date_debut_format_array[0] . "-" . $date_debut_format_array[1] . "-" . $date_debut_format_array[2]."_au_".$date_fin_format_array[0] . "-" . $date_fin_format_array[1] . "-" . $date_fin_format_array[2].".csv";
$file = fopen($file_name,"w+");
fwrite($file,utf8_decode($result_file));
fclose($file);
/*header('Content-Type: application/csv-tab-delimited-table');
header('Content-disposition: filename=' . $file_name);

echo utf8_decode($result_file);*/
//exit;


/*function dateFormat($date)
{
    $explode_test = explode(' ', $date);
    $explode_date = explode("-", $explode_test[0]);
    return $explode_date[2] . '/' . $explode_date[1] . "/" . $explode_date[0];
}*/

?>
