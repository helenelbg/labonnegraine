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
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoPDF.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoPDF.php');
}


class SetOrderAsSent extends SoNice_Etiquetage
{

    public function __construct()
    {
        parent::__construct();

        if (Tools::getValue('debug')) {
            $this->debug = true;
            @ini_set('display_errors', 'on');
            @define('_PS_DEBUG_SQL_', true);
            @error_reporting(E_ALL | E_STRICT);
        }

        SoColissimoContext::restore($this->context);
    }



    public function setOrderAsSentState()
    {
        ob_start();

        $success = true;
        $employee_id = new Cookie('sonice_current_employee');

        // TODO use $parcel_number to simplify all
        // $parcel_number = (array)Tools::getValue('parcel_number', Tools::getValue('exp_checkbox', false));
        $parcel_number = Tools::getValue('parcel_number', false);
        $parcel_number_array = Tools::getValue('exp_checkbox', false);

        if ($parcel_number && Tools::strlen($parcel_number)) {
            $success = SoColissimoPDF::setParcelAsSent($parcel_number);
        } elseif ($parcel_number_array && is_array($parcel_number_array) && count($parcel_number_array)) {
            foreach ($parcel_number_array as $parcel) {
                $success = SoColissimoPDF::setParcelAsSent($parcel);
            }
        } else {
            die($this->l('Unable to retrieve parcel number.'));
        }

        if (!$success) {
            echo $this->l('A problem happened with this parcel number :').' '.$parcel_number;
        } else {
            $module_conf = unserialize(Configuration::get('SONICE_ETQ_CONF'));

            if ($parcel_number && Tools::strlen($parcel_number)) {
                $order = new Order(SoColissimoPDF::getIdOrderByParcelNumber($parcel_number));

                if (Validate::isLoadedObject($order) && $module_conf && isset($module_conf['new_order_state_send']) &&
                    $module_conf['new_order_state_send'] > 0) {
//                    $order->setCurrentState((int)$module_conf['new_order_state_send'], (int)$employee_id->variable_name);
                    $this->setOrderState($order, (int)$module_conf['new_order_state_send'], (int)$employee_id->variable_name);
                }
            } elseif ($parcel_number_array && is_array($parcel_number_array) && count($parcel_number_array)) {
                foreach ($parcel_number_array as $parcel) {
                    $order = new Order(SoColissimoPDF::getIdOrderByParcelNumber($parcel));

                    if (Validate::isLoadedObject($order) && $module_conf && isset($module_conf['new_order_state_send']) &&
                        $module_conf['new_order_state_send'] > 0) {
//                        $order->setCurrentState((int)$module_conf['new_order_state_send'], (int)$employee_id->variable_name);
                        $this->setOrderState($order, (int)$module_conf['new_order_state_send'], (int)$employee_id->variable_name);
                    }
                }
            }
        }

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        if (Tools::strlen($output) < 3) {
            $output = null;
        }

        die($callback.'('.Tools::jsonEncode(array('console' => $output, 'success' => $success)).')');
    }

    private function setOrderState(Order $order, $id_order_state, $id_employee = 0)
    {
        if (empty($id_order_state)) {
            return false;
        }

        $history = new OrderHistory();
        $history->id_order = (int)$this->id;
        $history->id_employee = (int)$id_employee;
        $history->changeIdOrderState((int)$id_order_state, $order);

        $res = Db::getInstance()->getRow(
            'SELECT `invoice_number`, `invoice_date`, `delivery_number`, `delivery_date`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$order->id
        );
        $order->invoice_date = $res['invoice_date'];
        $order->invoice_number = $res['invoice_number'];
        $order->delivery_date = $res['delivery_date'];
        $order->delivery_number = $res['delivery_number'];
        $order->update();

        return $history->addWithemail(true, array(
            '{followup}' => 'http://www.colissimo.fr/portail_colissimo/suivreResultat.do?parcelnumber='.
                $order->shipping_number
        ));
    }
}

$page = new SetOrderAsSent();
$page->setOrderAsSentState();
