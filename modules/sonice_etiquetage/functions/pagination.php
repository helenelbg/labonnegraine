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
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoPDF.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoPDF.php');
}


class pagination extends SoNice_Etiquetage
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



    public function paginate()
    {
        if (!Tools::getValue('row') || !Tools::getValue('direction')) {
            die($this->l('Unable to retrieve pagination informations.'));
        }

        ob_start();

        $row = Tools::getValue('row');
        $offset = (!Tools::getValue('offset')) ? 0 : Tools::getValue('offset');
        $direction = Tools::getValue('direction');
        $reverse_orders = $direction === 'page_last' ? true : false;

        if ($direction === 'page_previous') {
            $offset = (int)$offset - (int)$row * 2;
        } elseif ($direction === 'page_first') {
            $offset = 0;
        }

        $orders = SoColissimoPDF::getOrders(true, $offset, $row, $reverse_orders);

        $this->context->smarty->assign(
                array(
                    'sne_orders' => $orders,
                    'sne_labels' => SoColissimoPDF::getLabels(),
                )
        );

        $html = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/pagination.tpl');

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();
        die($callback.'('.Tools::jsonEncode(array('orders' => $orders, 'orders_table' => $html, 'console' => $output)).')');
    }
}



$page = new Pagination();
$page->paginate();
