<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentMethod extends ObjectModel
{
    public $id;
    public $id_payment_method;
    public $name = null;
    public $description = null;

    public static $definition = array(
        'table' => 'aff_payment_methods',
        'primary' => 'id_payment_method',
        'fields' => array(
            'id_payment_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
        ),
    );

    public static function getPaymentMethods()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'aff_payment_methods`');
    }

    public static function getPaymentMethodFields($id = false)
    {
        if (!$id) {
            $id = (int)Tools::getValue('id_payment_method');
        }
        if ($id) {
            $sql = "SELECT * FROM `"._DB_PREFIX_."aff_payment_methods_fields` WHERE `id_payment_method`='".(int)$id."'";

            return Db::getInstance()->executeS($sql);
        }

        return false;
    }

    public static function getPaymentMethodsWithFields()
    {
        $return = array();
        foreach (PaymentMethod::getPaymentMethods() as $k => $pm) {
            $return[$k] = $pm;
            $return[$k]['fields'] = PaymentMethod::getPaymentMethodFields($pm['id_payment_method']);
        }

        return $return;
    }
}
