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
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoPDF.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/classes/api/ColissimoDepositSlipRequest.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoPDF.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoSession.php');
    require_once(dirname(__FILE__).'/../classes/api/ColissimoDepositSlipRequest.php');
}

//if (!class_exists('TCPDF') && isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
//    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/tools/tcpdf/tcpdf.php'));
//    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/tools/tcpdf/include/barcodes/pdf417.php'));
//    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/tools/tcpdf/tcpdf_barcodes_2d.php'));
//} elseif (!class_exists('TCPDF')) {
//    require_once(dirname(__FILE__).'/../tools/tcpdf/tcpdf.php');
//    require_once(dirname(__FILE__).'/../tools/tcpdf/include/barcodes/pdf417.php');
//    require_once(dirname(__FILE__).'/../tools/tcpdf/tcpdf_barcodes_2d.php');
//}

class SoNiceEtiquetageGenerateToday extends SoNice_Etiquetage
{

    protected $conf;

    public function __construct()
    {
        parent::__construct();

        if (Tools::getValue('debug')) {
            $this->debug = true;
            @ini_set('display_errors', 'on');
            @error_reporting(E_ALL | E_STRICT);
        }

        SoColissimoContext::restore($this->context);

//        $this->conf = unserialize(Configuration::get(
//            'SONICE_ETQ_CONF',
//            null,
//            $this->context->shop->id_shop_group,
//            $this->context->shop->id
//        ));
    }



    public function generateToday()
    {
        ob_start();

        try {
            $checkbox = (array)SoColissimoTools::arrayColumn(Db::getInstance()->executeS(
                'SELECT DISTINCT `parcel_number`
                FROM `'._DB_PREFIX_.'sonice_etq_label`
                WHERE `id_order` IN ('.pSQL(implode(', ', array_merge(array(0), Tools::getValue('orders', array())))).')
                AND `sent` = 1
                ORDER BY `id_order` DESC'
            ), 'parcel_number');

            $deposit_slip = new ColissimoDepositSlipRequest($checkbox);
            $deposit_slip_response = $deposit_slip->create();
            $deposit_slip_response->saveFile();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array(
            'console' => $output,
            'result' => isset($deposit_slip_response) && $deposit_slip_response->getDownloadLink(),
            'url' => isset($deposit_slip_response) ? $deposit_slip_response->getDownloadLink() : ''
        )).')');
    }
}



$label = new SoNiceEtiquetageGenerateToday();
$label->generateToday();
