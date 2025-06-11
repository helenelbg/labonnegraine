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
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SoColissimoPDF.php'));
} else {
    require_once(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SoColissimoPDF.php');
}


class SoColissimoSession
{

    public $id;
    public $alias;
    public $from;
    public $to;
    public $orders = array();

    const SCE_TABLE = 'sonice_etq_session';
    const SCE_TABLE_DETAIL = 'sonice_etq_session_detail';



    public function __construct($id_session = null)
    {
        if ($id_session) {
            $current_session = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.self::SCE_TABLE.'` WHERE `id_session` = '.(int)$id_session);

            if ($current_session) {
                $this->id = $current_session['id_session'];
                $this->alias = $current_session['alias'];
                $this->from = $current_session['from'];
                $this->to = $current_session['to'];
            }
        }
    }

    public static function checkIdShop()
    {
        $query = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.self::SCE_TABLE.'`');

        if ($query) {
            $fields = array();
            foreach ($query as $q) {
                $fields[] = $q['Field'];
            }

            if (!in_array('id_shop', $fields)) {
                Db::getInstance()->execute(
                    'ALTER TABLE `'._DB_PREFIX_.self::SCE_TABLE.'` ADD  `id_shop` INT(11) DEFAULT NULL'
                );
            }
        }
    }



    public function update()
    {
        $sql = 'INSERT INTO `'._DB_PREFIX_.self::SCE_TABLE.'` (`id_session`, `alias`, `from`, `to`)
                VALUES ('.(int)$this->id.',"'.pSQL($this->alias).'", "'.pSQL($this->from).'", "'.pSQL($this->to).'")
                ON DUPLICATE KEY UPDATE `alias`=VALUES(`alias`), `from`=VALUES(`FROM`), `to`=VALUES(`to`)';

        $result = Db::getInstance()->execute($sql);

        return ($result);
    }


    /**
     * @param null $id_shop
     * @return bool
     */
    public function create($id_shop = null)
    {
        self::checkIdShop();

        $sql = 'INSERT INTO '._DB_PREFIX_.self::SCE_TABLE.' (`alias`, `from`, `to`, `id_shop`)
                VALUES ("'.pSQL($this->alias).'", "'.pSQL($this->from).'", "'.pSQL($this->to).'", '.(int)$id_shop.')';

        $result = Db::getInstance()->execute($sql);

        return ($result);
    }



    public static function getAllSessionOrders()
    {
        $data = array();
        $result = Db::getInstance()->executeS('SELECT `id_order` FROM `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'`');

        foreach ($result as $id) {
            array_push($data, $id['id_order']);
        }

        return ($data);
    }



    /**
     * Return a table containing orders without a session assigned
     *
     * @return array
     */
    public static function getAvaibleOrders()
    {
        $orders = SoColissimoPDF::getOrders(true);
        $session_orders = SoColissimoSession::getAllSessionOrders();
        $available_orders = array(
            'total' => $orders['total'],
            'pages' => $orders['pages']
        );

        if (!is_array($orders['orders'])) {
            $orders['orders'] = array();
        }

        foreach ($orders['orders'] as $order) {
            if (!in_array($order['id_order'], $session_orders)) {
                $current_order = new Order((int)$order['id_order']);
                if (!Validate::isLoadedObject($current_order)) {
                    continue;
                }

                $order['qty'] = count($current_order->getProductsDetail());
                $order['weight'] = $current_order->getTotalWeight();
                $order['reference'] = (version_compare(_PS_VERSION_, '1.5', '>=')) ? $current_order->reference : '';
                $order['date'] = Tools::substr($current_order->date_add, 0, 10);

                $available_orders['orders'][] = $order;
            } else {
                $available_orders['total'] -= 1;
            }
        }

        $available_orders['pages'] = ceil($available_orders['total'] / 20);

        return ($available_orders);
    }



    public static function getSessions($limit = null, $id_shop = 1)
    {
        self::checkIdShop();

        $sql = 'SELECT *
                FROM '._DB_PREFIX_.self::SCE_TABLE.'
                WHERE `inter` = 0
                AND `id_shop` IS NULL
                '.($id_shop ? 'OR `id_shop` = '.(int)$id_shop : '').'
                ORDER BY `id_session` DESC '.($limit ? 'LIMIT '.(int)$limit : '');

        $result = Db::getInstance()->executeS($sql);

        return (array_reverse($result));
    }



    public static function getOrdersStatic($id_session)
    {
        if (!$id_session) {
            return (false);
        }

        $data = array();
        $result = Db::getInstance()->executeS(
            'SELECT `id_order`
            FROM `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'`
            WHERE `id_session` = '.(int)$id_session
        );

        foreach ($result as $id) {
            array_push($data, $id['id_order']);
        }

        return ($data);
    }



    public static function setOrderWeightStatic($id_order, $weight)
    {
        if (!Validate::isInt($id_order) || !Validate::isFloat($weight)) {
            return (false);
        }

        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'`
            SET `weight` = '.(float)$weight.'
            WHERE `id_order` = '.(int)$id_order
        ));
    }



    public static function setOrderProductWeightStatic($id_order_detail, $weight)
    {
        if (!Validate::isInt($id_order_detail) || !Validate::isFloat($weight)) {
            return (false);
        }

        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'order_detail`
            SET `product_weight` = '.(float)$weight.'
            WHERE `id_order_detail` = '.(int)$id_order_detail
        ));
    }



    public static function getOrderWeightStatic($id_order)
    {
        return ((float)Db::getInstance()->getValue(
            'SELECT `weight`
            FROM `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'`
            WHERE `id_order` = '.(int)$id_order
        ));
    }



    public static function getSessionStatic($id_session = null)
    {
        if (!$id_session) {
            return (false);
        }

        $session_orders = self::getOrdersStatic($id_session);

        $orders = array();
        if (is_array($session_orders) && count($session_orders)) {
            foreach ($session_orders as $order) {
                $current_order = new Order((int)$order);
                if (!Validate::isLoadedObject($current_order)) {
                    continue;
                }

                $customer = new Customer($current_order->id_customer);
                if (!Validate::isLoadedObject($customer)) {
                    continue;
                }

                $address = new Address($current_order->id_address_delivery);
                if (!Validate::isLoadedObject($address)) {
                    continue;
                }

                $info = array();
                $info['id_order'] = $current_order->id;
                $info['customer_firstname'] = $customer->firstname;
                $info['customer_lastname'] = $customer->lastname;
                $info['address_id'] = $address->id;
                $info['address_alias'] = $address->alias;
                $info['address_address1'] = $address->address1;
                $info['address_postcode'] = $address->postcode;
                $info['address_city'] = $address->city;
                $info['address_country'] = $address->country;
                $info['qty'] = count($current_order->getProductsDetail());

                $weight = $current_order->getTotalWeight();
                if (Tools::substr(self::getOrderWeightStatic($current_order->id), 0, 4)) {
                    $info['weight'] = Tools::substr(self::getOrderWeightStatic($current_order->id), 0, 4);
                } else {
                    $info['weight'] = $weight ? $weight : Tools::substr(self::getOrderWeightStatic($current_order->id), 0, 4);
                }

                $info['reference'] = (version_compare(_PS_VERSION_, '1.5', '>=')) ? $current_order->reference : '';
                $info['date'] = Tools::substr($current_order->date_add, 0, 10);

                $product_list = $current_order->getProductsDetail();
                $product_array = array();
                foreach ($product_list as $product) {
                    $product_array[] = array(
                        'id_order_detail' => $product['id_order_detail'],
                        'product_id' => $product['product_id'],
                        'product_attribute_id' => $product['product_attribute_id'],
                        'reference' => (version_compare(_PS_VERSION_, '1.5', '>=')) ? $product['reference'] : '',
                        'product_name' => $product['product_name'],
                        'product_quantity' => $product['product_quantity'],
                        'weight' => $product['product_weight']
                    );
                }
                $info['products'] = $product_array;

                $orders[] = $info;
            }
        }

        return ($orders);
    }



    public static function getSessionNameByID($id_session)
    {
        if (!$id_session) {
            return (false);
        }

        $session = new SoColissimoSession((int)$id_session);

        return ($session->alias);
    }



    public static function deleteSessionByID($id_session)
    {
        if (!$id_session) {
            return (false);
        }

        $result = true;

        // Delete label PDF/PNG
        $sql = 'SELECT etq.`parcel_number`
				FROM `'._DB_PREFIX_.'sonice_etq_label` etq, `ps_sonice_etq_session_detail` det
				WHERE det.`id_session` = '.(int)$id_session.'
				AND det.`id_order` = etq.`id_order`';
        $labels = Db::getInstance()->executeS($sql);

        foreach ($labels as $label) {
            if (file_exists(dirname(__FILE__).'/../download/'.$label['parcel_number'].'.pdf')) {
                unlink(dirname(__FILE__).'/../download/'.$label['parcel_number'].'.pdf');
            } elseif (file_exists(dirname(__FILE__).'/../download/'.$label['parcel_number'].'.png')) {
                unlink(dirname(__FILE__).'/../download/'.$label['parcel_number'].'.png');
            }
        }

        // Delete session
        $sql = 'DELETE FROM `'._DB_PREFIX_.self::SCE_TABLE.'` WHERE `id_session` = '.(int)$id_session;
        $result &= Db::getInstance()->execute($sql);

        // Delete session orders
        $sql = 'DELETE FROM `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'` WHERE `id_session` = '.(int)$id_session;
        $result &= Db::getInstance()->execute($sql);

        return ($result);
    }



    public static function fusionSession($id_session, $id_fusion)
    {
        if (!$id_session || !$id_fusion) {
            return (false);
        }

        $result_fusion = Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'`
            SET `id_session` = '.(int)$id_session.'
            WHERE `id_session` = '.(int)$id_fusion
        );

        if ($result_fusion) {
            $result_fusion = Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.self::SCE_TABLE.'`
                WHERE `id_session` = '.(int)$id_fusion
            );
        }

        return ($result_fusion);
    }

    public static function getOrderSession($id_order = 0)
    {
        return Db::getInstance()->getRow(
            'SELECT sesd.`id_session`, ses.`alias`
			FROM `'._DB_PREFIX_.self::SCE_TABLE_DETAIL.'` AS sesd, `'._DB_PREFIX_.self::SCE_TABLE.'` AS ses
			WHERE sesd.`id_order` = '.(int)$id_order.'
			AND sesd.`id_session` = ses.`id_session`'
        );
    }


    /**
     * Check if an order is in a session.
     *
     * @param integer $id_order
     * @return boolean
     */
    public static function isInSession($id_order = null)
    {
        if (!$id_order) {
            return false;
        }

        return (bool)Db::getInstance()->getValue(
            'SELECT `id_session`
			FROM '._DB_PREFIX_.self::SCE_TABLE_DETAIL.'
			WHERE `id_order` = '.(int)$id_order
        );
    }
}
