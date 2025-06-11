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
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoSession.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoTools.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoSession.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoTools.php');
}


class SoNiceEtiquetageUpdateSession extends SoNice_Etiquetage
{

    public $tare_conf;

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
        $this->tare_conf = unserialize(Configuration::get('SONICE_ETQ_TARE'));

        if (Tools::strtolower(Configuration::get('PS_WEIGHT_UNIT')) == 'g') {
            $this->tare_conf = array_map(function ($tare) { return $tare * 1000; }, $this->tare_conf);
        }
    }



    public function update()
    {
        ob_start();

        $id_session = Tools::getValue('id_session');
        $orders = Tools::getValue('listing_checkbox') ? Tools::getValue('listing_checkbox') :
            (Tools::getValue('listing_session_checkbox') ? Tools::getValue('listing_session_checkbox') : null);
        $action = Tools::getValue('action');

        if (!$id_session || !is_array($orders) || !Tools::strlen($action)) {
            die($this->l('No id_session or orders received.'));
        }

        $html = '';
        $warning = false;
        $session = new SoColissimoSession((int)$id_session);

        $employee_id = new Cookie('sonice_current_employee');

        switch ($action) {
            case ('add'):
                $tpl_data = array();
                foreach ($orders as $order) {
                    $weight = new Order((int)$order);
                    if (!Validate::isLoadedObject($weight)) {
                        continue;
                    }

                    Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.SoColissimoSession::SCE_TABLE_DETAIL.'`
						VALUES (
							0,'.
                            (int)$id_session.', '.
                            (int)$order.', '.
                            (float)$this->_getTaredWeight((float)$weight->getTotalWeight()).')'
                    );

                    $customer = new Customer($weight->id_customer);
                    if (!Validate::isLoadedObject($customer)) {
                        continue;
                    }

                    $address = new Address($weight->id_address_delivery);
                    if (!Validate::isLoadedObject($address)) {
                        continue;
                    }

                    $products_detail = $weight->getProductsDetail();
                    $product_counter = 0;
                    foreach ($products_detail as $detail) {
                        $product_counter += (int)$detail['product_quantity'];
                    }

                    $info = array();
                    $info['id_order'] = $weight->id;
                    $info['customer_firstname'] = $customer->firstname;
                    $info['customer_lastname'] = $customer->lastname;
                    $info['address_id'] = $address->id;
                    $info['address_alias'] = $address->alias;
                    $info['address_address1'] = $address->address1;
                    $info['address_postcode'] = $address->postcode;
                    $info['address_city'] = $address->city;
                    $info['address_country'] = $address->country;
                    $info['qty'] = $product_counter;
                    $info['weight'] = SoColissimoSession::getOrderWeightStatic($weight->id);
                    $info['reference'] = isset($weight->reference) ? $weight->reference : null;
                    $info['date'] = Tools::substr($weight->date_add, 0, 10);

                    $product_array = array();
                    foreach ($products_detail as $product) {
                        $product_array[] = array(
                            'id_order_detail' => $product['id_order_detail'],
                            'product_id' => $product['product_id'],
                            'product_attribute_id' => $product['product_attribute_id'],
                            'reference' => isset($product['reference']) ? $product['reference'] : null,
                            'product_name' => $product['product_name'],
                            'product_quantity' => $product['product_quantity'],
                            'weight' => (float)$product['product_weight'] * (int)$product['product_quantity']
                        );
                    }
                    $info['products'] = $product_array;

                    $tpl_data[] = $info;
                }

                $first_order = new Order((int)$orders[0]);
                if (Validate::isLoadedObject($first_order)) {
                    $last_order = new Order((int)end($orders));
                    if (Validate::isLoadedObject($last_order)) {
                        $session->from = ($first_order->date_add < $session->from || $session->from == '0000-00-00 00:00:00') ? $first_order->date_add : $session->from;
                        $session->to = ($last_order->date_add > $session->to) ? $last_order->date_add : $session->to;
                    }
                }

                // Warning
                // If more than ~70 orders in session then PDF417 creation will crash because too much data to handle
                // If more than 50 orders, send a warning to ask merchant to create a new session for new orders
                $warning_row_number = Db::getInstance()->getValue('
					SELECT COUNT(`id_session_detail`)
					FROM `'._DB_PREFIX_.'sonice_etq_session_detail`
					WHERE `id_session` = '.(int)$id_session
                );
                if ($warning_row_number >= 50) {
                    $warning = $this->l('Your session contains more than 70 orders, you should create a new session from now for your new orders else it wont be possible to print the deposit slip.');
                }

                $this->context->smarty->assign(
                        array(
                            'sne_labels_available' => $tpl_data,
                            'sne_token_order' => Tools::getAdminToken(
                                'AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').
                                (int)$employee_id->variable_name ? $employee_id->variable_name : 1
                            )
                        )
                );

                $html = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/listing_orders.tpl');
                break;

            case ('delete'):
                $sql = 'DELETE FROM '._DB_PREFIX_.SoColissimoSession::SCE_TABLE_DETAIL.'
                        WHERE `id_order` IN ('.implode(', ', array_map(array('SoColissimoTools', 'arrayMapCastInteger'), $orders)).')';

                Db::getInstance()->execute($sql);
                break;

            default:
                die($this->l('No action given'));
        }

        $result = $session->update();

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array('console' => $output, 'result' => $result, 'warning' => $warning, 'html' => $html)).')');
    }



    private function _getTaredWeight($weight)
    {
        if (!is_array($this->tare_conf) || !count($this->tare_conf)) {
            return ($weight);
        }

        foreach ($this->tare_conf as $tare) {
            if ($weight >= (float)$tare['from'] && $weight < $tare['to']) {
                $weight += (float)$tare['weight'];
                break;
            }
        }

        return ($weight);
    }
}



$create = new SoNiceEtiquetageUpdateSession();
$create->update();
