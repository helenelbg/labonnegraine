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
 * TACartReminderMessage is ObjectMode for crud operation
 * Use for store message & retrieve message of notification, employee comment a remind
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderMessage extends ObjectModel
{
    /**
     * @var int id_message
     */
    public $id;
    /**
     * @var int id_journal
     */
    public $id_journal;
    /**
     * @var int id_reminder
     */
    public $id_reminder;
    /**
     * @var int id_employee(0 if not)
     */
    public $id_employee;
    /**
     * @var bool is_system
     */
    public $is_system;
    /**
     * @var string message html
     */
    public $message;
    /**
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
        'table' => 'ta_cartreminder_journal_message',
        'primary' => 'id_message',
        'fields' => [
            'id_journal' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_reminder' => [
                'type' => self::TYPE_INT,
                'required' => false,
            ],
            'id_employee' => [
                'type' => self::TYPE_INT,
                'required' => false,
            ],
            'is_system' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'message' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'required' => true,
                'size' => 1600,
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
     * Get messages in journal
     *
     * @param $id_journal
     * @param null $id_reminder
     * @param null $is_system
     * @param bool|false $count
     *
     * @return int
     */
    public static function getMessages($id_journal, $id_reminder = null, $is_system = null, $count = false)
    {
        $sql = '
		SELECT ' . ($count ? 'count(*) as nb' : 'm.`date_add`, m.`id_reminder`, m.`is_system`, m.`message`,
				e.`firstname` AS efirstname, e.`lastname` AS elastname') . '
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_message` m
		LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON e.id_employee = m.id_employee
		WHERE m.`id_journal` = ' . (int) $id_journal .
            (isset($id_reminder) ? ' AND m.`id_reminder` = ' . (int) $id_reminder : '') .
            (isset($is_system) ? ' AND m.`is_system` = ' . (int) $is_system : '') .
            ' ORDER BY m.`date_add` DESC';
        if ($count) {
            $result = Db::getInstance()->getRow($sql);

            return (int) $result['nb'];
        }
        $messages = Db::getInstance()->executeS($sql);

        return $messages;
    }

    /**
     * Get message information by id_message
     *
     * @param $id_message
     *
     * @return array result row
     */
    public static function getMessageInfo($id_message)
    {
        $message = Db::getInstance()->getRow(
            '
		SELECT m.`date_add`, m.`id_reminder`, m.`is_system`, m.`message`,
		e.`firstname` AS efirstname, e.`lastname` AS elastname
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_message` m
		LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON e.id_employee = m.id_employee
		WHERE m.`id_message` = ' . (int) $id_message
        );

        return $message;
    }
}
