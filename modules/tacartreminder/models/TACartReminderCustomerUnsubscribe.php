<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 *
 *  CustomerUnscribe Object Model
 *  Use for CRUD customer unsubribed(list, add, update, delete)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderCustomerUnsubscribe extends ObjectModel
{
    /**
     * Id customer unsuscribed to cart reminder
     *
     * @var int Customer ID
     */
    public $id_customer;

    /**
     * customer email unsuscribed to cart reminder
     *
     * @var string Customer Email
     */
    public $email;

    /**
     * Compatible multi-shop
     *
     * @var int Shop
     */
    public $id_shop;

    /**
     * Date customer unsuscribed at cart reminder
     *
     * @var string Object creation date
     */
    public $date_add;

    /**
     * @var string Object update date
     */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ta_cartreminder_customer_unsubscribe',
        'primary' => 'id_unsubscribe',
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_customer' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
            ],
        ],
    ];

    /**
     * Check if customer already subscribed
     *
     * @param string $customer_email
     * @param number $id_shop
     *
     * @return bool true if unsubscribed, false if not present
     */
    public static function exist($customer_email, $id_shop = 1)
    {
        $result = Db::getInstance()->getRow(
            '
		SELECT id_unsubscribe
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_customer_unsubscribe` c
		WHERE c.`email` = \'' . $customer_email . '\' AND c.`id_shop`=' . (int) $id_shop
        );

        return (bool) isset($result['id_unsubscribe']);
    }
}
