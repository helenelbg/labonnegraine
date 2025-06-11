<?php
ini_set("memory_limit", "1024M");
set_time_limit(0);
define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(PS_ADMIN_DIR . '/../config/config.inc.php');
include(PS_ADMIN_DIR . '/../init.php');
require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');
include('./../tools/fpdi/fpdi.php');
//Module::getInstanceByName('sonice_etiquetage');

//include_once('./../modules/sonice_etiquetage/classes/SoColissimoSession.php');

/* Header can't be included, so cookie must be created here */
$cookie = new Cookie('psAdmin');
if (!$cookie->id_employee)
{
    Tools::redirectAdmin('login.php');
}

$explode_id_orders = explode('-', $_GET['deliveryslipsadmin']);

/* DEBUT SMS */
$implode_sms = implode(',', $explode_id_orders);
$close_zone_rosiers = '(od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';
$id_order_state = 2;
$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` IN ('.$implode_sms.') AND ('.$close_zone_rosiers.');';

$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

$commande = array();
foreach ($result as $prod) {
    /*echo '<pre>';
    print_r($prod);
    echo '</pre>';*/
    $exp = explode(' (', $prod['product_name']);
    if ( !isset($commande[$prod['product_id']]))
    {
        $commande[$prod['product_id']] = array('name' => $exp[0], 'qt' => ($prod['product_quantity'] - $prod['product_quantity_refunded']));
    }
    else 
    {
        $commande[$prod['product_id']]['qt'] += ($prod['product_quantity'] - $prod['product_quantity_refunded']);
    }
}

$cmd_final = '';
foreach ($commande as $final) {
    if ( !empty($cmd_final) )
    {
        $cmd_final .= ', ';
    }
    else 
    {
        $cmd_final .= 'Nouvelle commande La Bonne Graine : ';
    }
    $cmd_final .= $final['qt'].' x '.$final['name'];
}

$service_plan_id = "eb0dde1121c048809c6535042cc2b525";
$bearer_token = "ed5a0f1718a94e818cf7b29523c76435";

//Any phone number assigned to your API
//$send_from = "447537404817";
$send_from = "LBG";
//May be several, separate with a comma ,
$recipient_phone_numbers = "+33679050551"; 
//$recipient_phone_numbers = "+33676900032"; 
$message = $cmd_final;

// Check recipient_phone_numbers for multiple numbers and make it an array.
if(stristr($recipient_phone_numbers, ',')){
  $recipient_phone_numbers = explode(',', $recipient_phone_numbers);
}else{
  $recipient_phone_numbers = [$recipient_phone_numbers];
}

// Set necessary fields to be JSON encoded
$content = [
  'to' => array_values($recipient_phone_numbers),
  'from' => $send_from,
  'body' => $message
];

$data = json_encode($content);

$ch = curl_init("https://us.sms.api.sinch.com/xms/v1/{$service_plan_id}/batches");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
curl_setopt($ch, CURLOPT_XOAUTH2_BEARER, $bearer_token);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);

$vars = [
    '{contenu}' => $message,
];

Mail::Send(
    1,
    'rosiers',
    /*Context::getContext()->getTranslator()->trans(
        'Your guest account has been transformed into a customer account',
        [],
        'Emails.Subject',
        $language->locale
    ),*/
    'Nouvelle commande de rosiers',
    $vars,
    'guillaume.amary.lbg@gmail.com',
    'Julie Dorchies',
    null,
    null,
    null,
    null,
    _PS_MAIL_DIR_,
    false,
    1
);

/*if(curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo $result;
}*/
curl_close($ch);
/* FIN SMS */


foreach ($explode_id_orders as $cmd)
{
	error_log($cmd);
    $commande = new Order($cmd);
    $carrier = new Carrier($commande->id_carrier);

    $urlCarrier = $carrier->url;

    /*if(stristr($urlCarrier, 'colissimo'))
    {

        $sessions = SoColissimoSession::getSessions();
        $dateToday = ucfirst(strftime('%A %d %B %Y'));
        $sessionTrouve = false;
        //$dateToday ="Jeudi 27 Octobre 2016";

        if(!empty($sessions))
        {
           foreach($sessions as $key => $value)
           {
               //si une session � �t� trouv� => fusionner la session
               if(strtoupper(trim($value['alias'])) == strtoupper(trim($dateToday)))
               {
                   $id_session_trouve = $value['id_session'];
                   $sessionTrouve = true;
               }
           }
        }

        if($sessionTrouve)
        {
            $session = new SoColissimoSession($id_session_trouve);
        }
        else
        {
            $session = new SoColissimoSession();
            $session->alias = $dateToday;
            $session->from = date('Y-m-d H:i:s');
            //$session->from = '2016-10-27 17:13:22';

            $create_session = $session->create(1);
            $id_session_create = Db::getInstance()->Insert_ID();

            $session = new SoColissimoSession($id_session_create);
        }

        //ajout de la commande � la session
        $id_bdd = Db::getInstance()->execute(' SELECT id_session FROM `' . _DB_PREFIX_ .'sonice_etq_session_detail WHERE id_order = "'.$commande->id.'"');
        if(empty($id_session_bdd))
        {
            $sql_insert =  DB::getInstance()->insert('sonice_etq_session_detail', array(
                'id_session'=> $session->id,
                'id_order' => $commande->id,
                'weight' => $commande->getTotalWeight()
           ));
        }

    }*/
       
	//$commande->setCurrentState(3);
	$history = new OrderHistory();
	$history->id_order = $cmd;
    $history->id_order_state = 18;
	$history->add();
	$history->changeIdOrderState(18, $cmd);
}

$array_invoices = array();
$array_invoices = getInvoicesCollectionOrders($explode_id_orders);
$context = Context::getContext();

foreach ($array_invoices as $id_invoice)
{
    $pdf = new PDF($id_invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
    $pdf->render('F', PS_ADMIN_DIR.'/../temp/');
}

$path = "../temp/";

$pdf_entete = new FPDI();

$pdf_entete->setPrintHeader(false);
$pdf_entete->setPrintFooter(false);

//set margins
$pdf_entete->SetMargins(0, 0, 0);
$pdf_entete->SetHeaderMargin(0);
$pdf_entete->SetFooterMargin(0);

//set auto page breaks
$pdf_entete->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf_entete->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set image scale factor
$pdf_entete->setImageScale(PDF_IMAGE_SCALE_RATIO);


$files = scandir($path);
foreach ($files as $file)
{
    if ($file != '.' && $file != '..')
    {
//        echo '<br>p : '.$path.$file;
        $pagecount = $pdf_entete->setSourceFile($path . $file);


        $_y = $y = 0;
        $_x = $x = 0;

        for ($n = 1; $n <= $pagecount; $n++)
        {
            $tplidx = $pdf_entete->ImportPage($n);
            $specs = $pdf_entete->getTemplateSize($tplidx);

            $pdf_entete->addPage('P');

            $x = $_x;
            $y = $_y;
            $size = $pdf_entete->useTemplate($tplidx, $x, $y);
        }

        $pdf_entete->setSourceFile('../CGV_2019-06-Verso.pdf');
        $tplidx = $pdf_entete->importPage(1);

        $pdf_entete->addPage('P');
        $pdf_entete->useTemplate($tplidx);

        if(($pagecount+1)%2 > 0)
        {
            $pdf_entete->addPage('P');
        }

        @unlink($path.$file);
    }
}
$pdf_entete->Output('deliveries.pdf');


function getInvoicesCollectionOrders($array_orders)
{
    $order_invoice_list = Db::getInstance()->executeS('
			SELECT oi.*
			FROM `' . _DB_PREFIX_ . 'order_invoice` oi
			LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE o.id_order IN (' . implode(',', $array_orders) . ')
			' . Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o') . '
			ORDER BY oi.id_order ASC
		');

    return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
}
