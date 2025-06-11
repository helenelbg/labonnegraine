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
    $history->id_order_state = 3;
	$history->add();
	$history->changeIdOrderState(3, $cmd);
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
