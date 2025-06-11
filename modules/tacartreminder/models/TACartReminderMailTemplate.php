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
 * TACartReminderMailTemplate Object Model Class
 * Use for store & retrieve all email template
 * EmailTemplate is a email model
 * EmailTemplate id is linked to TACartReminderRule
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderMailTemplate extends ObjectModel
{
    /**
     * @var int Id
     */
    public $id;

    /**
     * @var int Id
     */
    public $id_mail_template;

    /**
     * @var string Name
     */
    public $name;

    /**
     * @var string Title
     */
    public $title;

    /**
     * @var string Subject mail
     */
    public $subject;

    /**
     * @var string content html
     */
    public $content_html;

    /**
     * @var string content txt
     */
    public $content_txt;

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
        'table' => 'ta_cartreminder_mail_template',
        'primary' => 'id_mail_template',
        'multilang' => true,
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 128,
            ],
            'title' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'required' => true,
                'size' => 256,
            ],
            'subject' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isMailSubject',
                'required' => true,
                'size' => 256,
            ],
            'content_html' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'size' => 3999999999999,
            ],
            'content_txt' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'size' => 3999999999999,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
            ],
        ],
    ];

    public function delete($check = true)
    {
        if ($check) {
            $rules_use_mail = TACartReminderRule::getRulesByMailTemplate($this->id);
            if ($rules_use_mail && count($rules_use_mail) > 0) {
                return false;
            }
        }
        if (!parent::delete()
            || !Db::getInstance()->delete(
                self::$definition['table'] . '_shop',
                '`' . self::$definition['primary'] . '`=' . (int) $this->id
            )
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get all mails in a given language
     *
     * @param int $id_lang
     *                     Language id
     *
     * @return array Mails
     */
    public static function getMailTemplates($id_lang, $order_by = null, $order_way = null, $filters = [])
    {
        if (empty($order_by)) {
            $order_by = 'name';
        } else {
            $order_by = Tools::strtolower($order_by);
        }
        $ml_alias = [
            'subject',
            'content_html',
            'content_txt',
        ];
        if (empty($order_by)) {
            $order_by = 'm.name';
        } else {
            if (in_array(Tools::strtolower($order_by), $ml_alias)) {
                $order_by = 'ml.' . $order_by;
            } else {
                $order_by = 'm.' . $order_by;
            }
        }
        $sql_filter = '';
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if (in_array(Tools::strtolower($field), $ml_alias)) {
                    $sql_filter = ' AND ml.' . $field . ' like \'%' . $value . '%\' ';
                } else {
                    $sql_filter = ' AND m.' . $field . ' like \'%' . $value . '%\' ';
                }
            }
        }
        if (empty($order_way)) {
            $order_way = 'ASC';
        }
        $sqlshop = '';
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL) {
            $sqlshop = 'INNER JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_shop s ON s.' .
                self::$definition['primary'] . ' = m.' . self::$definition['primary'];
            $sqlshop .= ' AND s.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')';
        }
        $sql = '
		SELECT m.`id_mail_template`,m.`name`,m.`date_add`,m.`date_upd`,
		ml.`subject`,ml.`content_html`, ml.`content_txt`
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` m
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template_lang` ml
				ON (m.`id_mail_template` = ml.`id_mail_template`
				AND ml.`id_lang` = ' . (int) $id_lang . ')' . $sqlshop .
            'WHERE 1 ' . $sql_filter;
        $sql .= ' GROUP BY m.`id_mail_template`';
        $sql .= ' ORDER BY ' . $order_by . ' ' . $order_way;
        $cache_id = 'TACartReminderMailTemplate::getMails_' . md5($sql);
        if (!Cache::isStored($cache_id)) {
            $mails = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $mails);
        }
        $mails = Cache::retrieve($cache_id);

        return $mails;
    }
}
