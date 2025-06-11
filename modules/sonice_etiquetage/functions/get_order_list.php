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


class OrderList extends SoNice_Etiquetage
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


    public function getList()
    {
        if (!Tools::getValue('listing_session_checkbox')) {
            die($this->l('Unable to retrieve orders IDs.'));
        }

        ob_start();

        $orders_list = Tools::getValue('listing_session_checkbox');

        if (!is_array($orders_list) || !count($orders_list)) {
            die($this->l('There is a problem with ID array.'));
        }

        $orders = SoColissimoPDF::getSelectedOrders($orders_list);

        $this->context->smarty->assign(array(
            'sne_orders' => $orders,
            'sne_labels' => SoColissimoPDF::getLabels(),
            'sne_token_order' => Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').
            (int)$this->ps15x && Validate::isLoadedObject($this->context->employee) ? $this->context->employee->id : 1),
            'sne_ps16x' => version_compare(_PS_VERSION_, '1.6', '>=') ? true : false
        ));

        $html = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/get_order_list.tpl');

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();
        die($callback.'('.Tools::jsonEncode(array('html' => $html, 'console' => $output)).')');
    }
}

$page = new OrderList();
$page->getList();
