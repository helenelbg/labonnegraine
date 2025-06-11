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
 * @license   Commercial License
 *
 * Started on 13-Aug-15 11:46 AM
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
}

require_once _PS_MODULE_DIR_.'sonice_etiquetage/tools/PDFMerger.php';

@ini_set('display_errors', 'on');
@error_reporting(E_ALL|E_STRICT);

ob_start();

$base_path = _PS_MODULE_DIR_.'sonice_etiquetage/download/';
$base_url = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/sonice_etiquetage/download/';
$parcel_numbers = Tools::getValue('parcel_numbers');
$urls = array();
$pdfs = new PDFMerger();

if (is_array($parcel_numbers) && count($parcel_numbers)) {
    foreach ($parcel_numbers as $parcel_number) {
        if (file_exists($base_path.$parcel_number.'_CN23.pdf')) {
            $pdfs->addPDF($base_path.$parcel_number.'_CN23.pdf');
//            $urls[] = $base_url.$parcel_number.'_CN23.pdf';
        }
    }
}

$pdf_file_name = 'merged_pdf_CN23_'.time().'.pdf';
$pdfs->merge('file', $base_path.$pdf_file_name);

$urls[0] = $base_url.$pdf_file_name;

$callback = Tools::getValue('callback');
$output = ob_get_clean();

die($callback.'('.Tools::jsonEncode(array(
    'console' => $output,
    'result' => true,
    'urls' => $urls
)).')');
