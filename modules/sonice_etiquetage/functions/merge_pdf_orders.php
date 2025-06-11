<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   sonice_etiquetage
 * @author    debussa
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice_etiquetage@common-services.com
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
}

require_once _PS_MODULE_DIR_.'sonice_etiquetage/tools/PDFMerger.php';
require_once _PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoTools.php';

$orders = Tools::getValue('orders');

if (!$orders) {
    die('No id_order received');
}

$pdfs = new PDFMerger();
$label_directory = _PS_MODULE_DIR_.'sonice_etiquetage/download/';
$parcel_numbers = SoColissimoTools::arrayColumn(Db::getInstance()->executeS(
    'SELECT
        sel1.`parcel_number`
    FROM
        `'._DB_PREFIX_.'sonice_etq_label` sel1
    WHERE
        sel1.`id_order` IN ('.pSQL(implode(', ', $orders)).')
    AND sel1.`id_label` IN (
        SELECT
            MAX(sel2.`id_label`)
        FROM
            `'._DB_PREFIX_.'sonice_etq_label` sel2
        WHERE
            sel1.`id_order` = sel2.`id_order`
    )'
), 'parcel_number');

if (is_array($parcel_numbers) && count($parcel_numbers) == 1) {
    die(Tools::getValue('callback').'('.Tools::jsonEncode(array(
            'url' => __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/sonice_etiquetage/download/'.$parcel_numbers[0].'.pdf'
        )).')');
}

foreach ($parcel_numbers as $parcel_number) {
    $pdfs->addPDF($label_directory.$parcel_number.'.pdf');
}

$pdf_file_name = 'merged_pdf_'.time().'.pdf';

if ($pdfs->merge('file', $label_directory.$pdf_file_name)) {
    die(Tools::getValue('callback').'('.Tools::jsonEncode(array(
        'url' => __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/sonice_etiquetage/download/'.$pdf_file_name
    )).')');
}

die(Tools::getValue('callback').'('.Tools::jsonEncode(array('url' => false)).')');
