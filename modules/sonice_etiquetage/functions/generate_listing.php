<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * @package   sonice_etiquetage
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @license   Commercial license
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoSession.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoSession.php');
}

if (!class_exists('TCPDF') && isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/tools/tcpdf/tcpdf.php'));
} elseif (!class_exists('TCPDF')) {
    require_once(dirname(__FILE__).'/../tools/tcpdf/tcpdf.php');
}

class SoNiceEtiquetageGenerateListing extends SoNice_Etiquetage
{



    public function __construct()
    {
        parent::__construct();

        if (Tools::getValue('debug')) {
            $this->debug = true;
        }

        if ($this->debug) {
            @ini_set('display_errors', 'on');
            @define('_PS_DEBUG_SQL_', true);
            @error_reporting(E_ALL | E_STRICT);
        }

        SoColissimoContext::restore($this->context);
    }



    public function generateListing()
    {
        ob_start();

        $checkbox = Tools::getValue('checkbox');
        $id_session = Tools::getValue('id_session') ? Tools::getValue('id_session') : '0';

        if (!is_array($checkbox) || !count($checkbox)) {
            die($this->l('Impossible to retrieve the id order array.'));
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Common-Services');
        $pdf->SetTitle('Session - '.$id_session);
        $pdf->SetSubject('Listing de la session '.$id_session);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage('L', 'A4');
        $pdf->SetFont('helvetica', '', 8);

        $datas = array();
        foreach ($checkbox as $id) {
            $order = new Order((int)$id);
            if (!Validate::isLoadedObject($order)) {
                continue;
            }

            $customer = new Customer((int)$order->id_customer);
            if (!Validate::isLoadedObject($customer)) {
                continue;
            }

            $address = new Address((int)$order->id_address_delivery);
            if (!Validate::isLoadedObject($address)) {
                continue;
            }

            $product_list = $order->getProductsDetail();

            foreach ($product_list as $key => $product) {
                $list = array(
                    $key == 0 ? $order->id : '',
                    ($key == 0 && $this->ps15x) ? $order->reference : '',
                    $key == 0 ? sprintf('%s %s', $customer->firstname, $customer->lastname) : '',
                    $key == 0 ? mb_convert_encoding(sprintf('%s, %s %s', $address->address1, $address->postcode, $address->city), 'ISO-8859-1') : '',
                    '(#'.$product['product_reference'].') '.mb_convert_encoding($product['product_name'], 'ISO-8859-1'),
                    $product['product_quantity'],
                    (count($product_list) > 1) ?
                        Tools::substr($product['product_weight'], 0, 4) : Tools::substr(SoColissimoSession::getOrderWeightStatic($order->id), 0, 4)
                );
                array_push($datas, $list);
            }
        }

        $this->context->smarty->assign(array(
            'sne_orders' => $datas,
            'date_session' => date('d/m/Y').' #'.$id_session
        ));

        $tbl = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/generate_listing.tpl');

        $token = Configuration::get(
            'SONICE_ETQ_TOKEN',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if (!$token) {
            $token = md5(_COOKIE_KEY_.date('Y-m-d H:i:s'));
        }

        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdf->Output($this->path.'download/ListingOrder'.$id_session.'_'.$token.'.pdf', 'F');
        $pdf->Output($this->path.'download/ListingOrder'.$id_session.'_'.$token.'.pdf', 'S');

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array(
            'console' => $output,
            'result' => true,
            'url' => $this->url.'download/ListingOrder'.$id_session.'_'.$token.'.pdf')).')'
        );
    }
}



$label = new SoNiceEtiquetageGenerateListing();
$label->generateListing();
