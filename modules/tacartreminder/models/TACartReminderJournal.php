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
 * TACartReminderJournal Object Model Class
 * Main class trace all cart remind, or perform a reminder
 * contain many function to retrieve a journal by cart, customer, ..
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderJournal extends ObjectModel
{
    /**
     * @var int Id journal autoincrement
     */
    public $id;

    /**
     * @var int Shop
     */
    public $id_shop;

    /**
     * @var int cart
     */
    public $id_cart;

    /**
     * @var int id customer
     */
    public $id_customer;

    /**
     * @var string customer email
     */
    public $email;

    /**
     * @var int id rule
     */
    public $id_rule;

    /**
     * @var int id order
     */
    public $id_order;

    /**
     * @var int id rule
     */
    public $id_cart_rule;

    /**
     * @var string rule name
     */
    public $rule_name;

    /**
     * @var string ENUM('CANCELED', 'RUNNING', 'FINISHED') state of reminder
     */
    public $state;

    /**
     * @var string Object creation date
     */
    public $date_add;

    /**
     * @var string Object update date
     */
    public $date_upd;

    /**
     * @var string Last date update cart for the customer, if cart is deleted
     */
    public $date_upd_cart;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ta_cartreminder_journal',
        'primary' => 'id_journal',
        'fields' => [
            'id_shop' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_cart' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_customer' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_rule' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'id_cart_rule' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ],
            'id_order' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'required' => true,
            ],
            'rule_name' => [
                'type' => self::TYPE_STRING,
            ],
            'state' => [
                'type' => self::TYPE_STRING,
                'values' => [
                    'CANCELED',
                    'RUNNING',
                    'FINISHED',
                ],
                'default' => 'RUNNING',
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE,
            ],
            'date_upd_cart' => [
                'type' => self::TYPE_DATE,
            ],
        ],
    ];

    /**
     * Remove journal, journal_reminder, journal_message
     *
     * @param string $autodate
     * @param string $null_values
     *
     * @return bool true if all result is OK
     */
    public function delete($autodate = true, $null_values = false)
    {
        $res = parent::delete($autodate, $null_values);
        $res &= Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder
										    WHERE id_journal=' . (int) $this->id
        );
        $res &= Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal_message
										    WHERE id_journal=' . (int) $this->id
        );

        return $res;
    }

    /**
     * Get journal reminder for this journal
     *
     * @return array journal reminders
     */
    public function getJournalReminders()
    {
        $journal_reminders = Db::getInstance()->executeS(
            '
		SELECT jr.*,rr.*, e.id_employee, e.firstname as e_firstname, e.lastname as e_lastname
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
		INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_journal = jr.id_journal
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` rr ON rr.id_reminder = jr.id_reminder
		LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON e.id_employee = jr.id_employee
		WHERE j.id_journal = ' . (int) $this->id . '
		ORDER BY rr.`position`,jr.`date_add` ASC'
        );

        return $journal_reminders;
    }

    /**
     * Get journal Reminder
     *
     * @param int $id_reminder
     */
    public function getJournalReminder($id_reminder)
    {
        $journal_reminder = Db::getInstance()->getRow(
            '
		SELECT jr.*, e.id_employee, e.firstname as e_firstname, e.lastname as e_lastname
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
		INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_journal = jr.id_journal
		LEFT JOIN `' . _DB_PREFIX_ . 'employee` e ON e.id_employee = jr.id_employee
		WHERE j.id_journal = ' . (int) $this->id . ' AND jr.id_reminder = ' . (int) $id_reminder
        );

        return $journal_reminder;
    }

    /**
     * Get journal running by customer
     *
     * @param string $customer_email
     * @param int $id_shop
     *
     * @return TACartReminderJournal object
     */
    public static function getRunningByCustomer($customer_email, $id_shop = 1)
    {
        $journal = new TACartReminderJournal();
        $sql = '
		SELECT j.* FROM  `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
		WHERE j.`email`=\'' . pSQL($customer_email) . '\' AND j.`id_shop`=' . (int) $id_shop . ' AND j.`state`=\'RUNNING\'';
        $journal_row = Db::getInstance()->getRow($sql);
        if ($journal_row) {
            $journal->id = (int) $journal_row['id_journal'];
            foreach ($journal_row as $key => $value) {
                if (property_exists($journal, $key)) {
                    $journal->{$key} = $value;
                }
            }
        }

        return $journal;
    }

    /**
     * Get all running journal by shop
     *
     * @param int $id_shop
     *
     * @return array journal
     */
    public static function getRunnings($id_shop = 1)
    {
        $sql = '
		SELECT j.* FROM  `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
		WHERE  j.`id_shop`=' . (int) $id_shop . ' AND j.`state`=\'RUNNING\'';
        $result = Db::getInstance()->executeS($sql, true, false);

        return $result;
    }

    /**
     * Get all journal running by rule
     *
     * @param int $id_rule
     *
     * @return array journals
     */
    public static function getRunningsByRule($id_rule)
    {
        $sql = '
		SELECT j.* FROM  `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
		WHERE  j.`state`=\'RUNNING\' AND j.`id_rule` = ' . (int) $id_rule;
        $journals = Db::getInstance()->executeS($sql, true, false);

        return $journals;
    }

    /**
     * Return manual reminder to do
     *
     * @param bool $count
     *                    if true return int
     *
     * @return number|array result
     */
    public static function getManualToDo($count = false)
    {
        $sql = '
		SELECT ' . ($count ? 'COUNT(*) as nb' : 'jr.*') . ' FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
		INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_journal = jr.id_journal
		INNER JOIN `' . _DB_PREFIX_ . 'cart` c ON c.id_cart = j.id_cart ' . Shop::addSqlAssociation('cart', 'c') . '
		WHERE j.`state`=\'RUNNING\' AND jr.`manual_process`=1
		AND (jr.`date_performed` <= \'0000-00-00 00:00:00\' or jr.date_performed IS NULL)';
        if ($count) {
            $count_row = Db::getInstance()->getRow($sql);

            return (int) $count_row['nb'];
        } else {
            $result = Db::getInstance()->executeS($sql);

            return $result;
        }
    }

    /**
     * Get Last Journal by customer
     *
     * @param string $customer_email
     * @param int $id_shop
     *
     * @return TACartReminderJournal object
     */
    public static function getLastByCustomer($customer_email, $id_shop = 1)
    {
        $journal = new TACartReminderJournal();
        $sql = '
		SELECT j.* FROM  `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
		WHERE j.`email`=\'' . pSQL($customer_email) . '\' AND j.`id_shop`=' . (int) $id_shop . ' ORDER BY date_upd DESC';
        $journal_row = Db::getInstance()->getRow($sql);
        if ($journal_row) {
            $journal->id = (int) $journal_row['id_journal'];
            foreach ($journal_row as $key => $value) {
                if (property_exists($journal, $key)) {
                    $journal->{$key} = $value;
                }
            }
        }

        return $journal;
    }

    /**
     * Close journal
     *
     * @param string $message
     * @param int $id_employee
     * @param string $state_close
     *                            'CANCELED','FINISHED'
     *
     * @return bool
     */
    public function close($message, $id_employee = null, $state_close = 'CANCELED')
    {
        $this->state = $state_close;
        if ($state_close == 'CANCELED' && (!isset($this->email) || empty($this->email))) {
            // customer can be delete during process keep constraint email required
            $this->email = 'customernotexist@scr.com';
        }
        if ($this->update()) {
            $mess = new TACartReminderMessage();
            $mess->id_journal = (int) $this->id;
            $mess->message = (string) $message;
            if (!isset($id_employee) || !$id_employee) {
                $mess->is_system = true;
            }
            $mess->add();

            return true;
        }

        return false;
    }

    /**
     * Return last launched reminder
     *
     * @return hash journal_reminder result
     */
    public function getLastPerformedReminder()
    {
        $journal_reminder = Db::getInstance()->getRow(
            '
		SELECT jr.*
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
		WHERE jr.id_journal = ' . (int) $this->id . '
		ORDER BY jr.date_performed DESC'
        );

        return $journal_reminder;
    }

    /**
     * Return last performed reminder by a customer
     *
     * @param string $customer_email
     * @param int $id_shop
     *
     * @return hash journal_reminder result
     */
    public static function getLastPerformedReminderByCustomer($customer_email, $id_shop = 1)
    {
        $journal_reminder = Db::getInstance()->getRow(
            '
		SELECT jr.*
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
		INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_journal = jr.id_journal
		WHERE j.`email`=\'' . pSQL($customer_email) . '\' AND j.`id_shop`=' . (int) $id_shop . '
		  ORDER BY jr.date_performed DESC'
        );

        return $journal_reminder;
    }

    /**
     * return the reminder to launch
     *
     * @return bool false if not|array reminder
     */
    public function getReminderToLaunch()
    {
        if ($this->state == 'RUNNING') {
            $reminders = TACartReminderRule::getRemindersByRule($this->id_rule); /* reminder order by position */
            $last_performed = [];
            foreach ($reminders as $reminder) {
                $performed = false;
                $journal_reminder = $this->getJournalReminder((int) $reminder['id_reminder']);
                if ($journal_reminder && (int) $journal_reminder['id_reminder']) {
                    if ((int) $reminder['manual_process'] && !(int) $journal_reminder['performed']) {
                        return false;
                    }
                    $performed = true;
                    $last_performed = $journal_reminder;
                }
                if (!$performed && isset($last_performed['id_reminder']) && (int) $last_performed['id_reminder']) {
                    $time_now = time();
                    $last_perfomed_time = strtotime((string) $last_performed['date_performed']);
                    $time_launch = $last_perfomed_time + ((float) $reminder['nb_hour'] * 3600);
                    if ($time_launch <= $time_now) {
                        return $reminder;
                    } else {
                        return false;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Running reminder
     *
     * @param unknown $id_reminder
     *                             executed
     */
    public static function getJRRunningByExecuted($id_reminder)
    {
        $sql = '
		SELECT jr.id_journal,rr.*, cu.firstname, cu.lastname, cu.email, cu.id_customer,
				ca.id_cart as id_cart, ca.date_add as date_add_cart, 
				jr.date_upd as date_launched
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
		INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_journal = jr.id_journal
		INNER JOIN `' . _DB_PREFIX_ . 'cart` ca ON ca.id_cart = j.id_cart
		INNER JOIN `' . _DB_PREFIX_ . 'customer` cu ON cu.id_customer = ca.id_customer
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` rr ON rr.id_reminder = jr.id_reminder
		WHERE rr.id_reminder = ' . (int) $id_reminder . ' AND j.`state` = \'RUNNING\'
		ORDER BY ca.`date_upd` desc';
        $journal_reminders = Db::getInstance()->executeS($sql);

        return $journal_reminders;
    }

    /**
     * Return Journal by cart
     *
     * @param int $id_cart
     *
     * @return TACartReminderJournal
     */
    public static function getWithCart($id_cart)
    {
        $journal = new TACartReminderJournal();
        $journal_row = Db::getInstance()->getRow(
            '
		SELECT j.*
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
		WHERE j.id_cart = ' . (int) $id_cart
        );
        if ($journal_row) {
            $journal->id = (int) $journal_row['id_journal'];
            foreach ($journal_row as $key => $value) {
                if (property_exists($journal, $key)) {
                    $journal->{$key} = $value;
                }
            }
        }

        return $journal;
    }

    /**
     * Execute a reminder(create journal if not exist
     *
     * @param int $id_cart
     * @param int $id_reminder
     * @param string $message
     * @param string $id_employee
     * @param string $type_perform
     *                             INIT / DONE / FINISH
     *
     * @throws PrestaShopException
     */
    public static function performReminder(
        $cart,
        $id_reminder,
        $message = '',
        $id_employee = null,
        $rule = null,
        $type_perform = 'INIT'
    ) {
        if (!is_object($cart)) {
            if (is_array($cart) && isset($cart['id_cart'])) {
                $id_cart = (int) $cart['id_cart'];
            } elseif ((int) $cart) {
                $id_cart = (int) $cart;
            } else {
                throw new PrestaShopException(Tools::displayError('Impossible to load cart'));
            }
            $cart = new Cart($id_cart);
        }
        $cart_context = TACartReminderTools::buildContextByCart($cart);
        $module_instance = new TACartReminder();
        $report = '';
        // for security reselect reminder to launch
        $row = Db::getInstance()->getRow(
            'SELECT r.position as reminder_position, r.id_mail_template, tpl.name as modele_email,
					r.manual_process, r.`admin_mails`, ru.`cart_rule_nbday_validity`,
					ru.id_rule, ru.create_cart_rule, ru.id_cart_rule, ru.name as rule_name,
					cu.id_customer, cu.firstname as customer_firstname,
					cu.lastname as customer_lastname, cu.email as customer_email,' .
            ((version_compare(_PS_VERSION_, '1.5.4', '<') === true) ? 'ca.id_lang' : 'cu.id_lang') . ' as customer_lang,
            ca.id_shop,j.id_journal, j.id_cart_rule as new_id_cart_rule, jr.id_reminder as jr_id_reminder,
            jr.manual_process as jr_manual_process
            FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
            LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` tpl ON r.id_mail_template = tpl.id_mail_template
            INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_rule` ru ON r.id_rule = ru.id_rule, `' . _DB_PREFIX_ . 'cart` ca
            INNER JOIN `' . _DB_PREFIX_ . 'customer` cu ON cu.id_customer = ca.id_customer
            LEFT JOIN  `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_cart = ca.id_cart
            LEFT JOIN  `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
            ON jr.id_journal = j.id_journal AND jr.id_reminder = ' . (int) $id_reminder . '
            WHERE r.id_reminder = ' . (int) $id_reminder . ' AND ca.id_cart = ' . (int) $cart->id,
            false
        );

        if ($row) {
            // Reminder peut changer entre manual et auto ainsi on se base sur le journal et non la table de reference
            $manual_process = ((int) $row['jr_id_reminder'] ?
                (int) $row['jr_manual_process'] : (int) $row['manual_process']);
            if (!$rule || !is_object($rule)) {
                $rule = new TACartReminderRule((int) $row['id_rule']);
            }
            if ((int) $row['reminder_position'] > 1) {
                $sql = 'SELECT count(*) as exist
							FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
							INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
							ON r.id_reminder = jr.id_reminder
							WHERE r.`position` < ' . (int) $row['reminder_position'];
                $reminderbeforeislaunch = Db::getInstance()->getRow($sql);
                if (!$reminderbeforeislaunch || !((int) $reminderbeforeislaunch['exist'] > 0)) {
                    throw new PrestaShopException(sprintf(Tools::displayError('Cannot launch reminder position %1$s before reminder position %2$s'), (int) $row['reminder_position'], (int) $row['reminder_position'] - 1));
                }
            }
            $cart_rule = null;
            if ((int) $row['reminder_position'] == 1 && !(int) $row['id_journal']) {
                // create carte rule the rule have cart rule & is first reminder
                if ((int) $row['create_cart_rule'] && (int) $row['id_cart_rule']) {
                    $cart_rule = new CartRule((int) $row['id_cart_rule']);
                    if (Validate::isLoadedObject($cart_rule) && $cart_rule->id) {
                        unset($cart_rule->id);
                        if (Shop::isFeatureActive()) {
                            $time_zone_shop = Configuration::get('TIMEZONE', null, null, $row['id_shop']);
                            // Obtenez le fuseau horaire de la boutique
                            $timezone = $time_zone_shop;
                            $timezoneObject = new DateTimeZone($timezone);
                            $dateFrom = new DateTime('now', $timezoneObject);
                            $cart_rule->date_from = $dateFrom->format('Y-m-d H:i:s');
                            $dateTo = new DateTime('now', $timezoneObject);
                            $dateTo->modify('+' . (int) $row['cart_rule_nbday_validity'] . ' day');
                            $dateTo->setTime(23, 59, 59);
                            $cart_rule->date_to = $dateTo->format('Y-m-d H:i:s');
                        } else {
                            $cart_rule->date_from = date('Y-m-d H:i:s');
                            $cart_rule->date_to = date(
                                'Y-m-d 23:59:59', // Définit directement l'heure à 23:59:59
                                mktime(
                                    0, // Heure
                                    0, // Minute
                                    0, // Seconde
                                    date('m'), // Mois
                                    date('d') + (int) $row['cart_rule_nbday_validity'], // Jour
                                    date('Y') // Année
                                )
                            );
                        }
                        // check multi shop enable
                        $cart_rule->id_customer = $row['id_customer'];
                        $prefix = Configuration::get('TA_CARTR_CR_PREFIX', null, null, (int) $cart->id_shop);
                        $code_format = Configuration::get('TA_CARTR_CODE_FORMAT', null, null, (int) $cart->id_shop);
                        if (empty($code_format)) {
                            $code_format = 'LLLNLNLNLNLNLNLNLNLL';
                        }
                        $code = self::generateCode($prefix, $code_format, true);
                        $cart_rule->code = $code;
                        $cart_rule->quantity = 1;
                        $cart_rule->active = 1;
                        if (!$cart_rule->add()) {
                            throw new PrestaShopException(sprintf(Tools::displayError('Impossible to add cart rule')));
                        }
                        CartRule::copyConditions((int) $row['id_cart_rule'], $cart_rule->id);
                        $report .= sprintf(
                            $module_instance->l('Cart rule created, id:%1$s code:%2$s'),
                            $cart_rule->id,
                            $code
                        );
                        $report .= "\r\n";
                        $row['new_id_cart_rule'] = (int) $cart_rule->id;
                    } else {
                        throw new PrestaShopException(sprintf(Tools::displayError('Can\'t load CartRule object, cart rule id %s is there?'), $row['id_cart_rule']));
                    }
                }
            }
            $unscibe_url = TACartReminderTools::getUnscribeUrl($cart, $cart_context);
            $a_cr = (int) Configuration::get('TA_CARTR_AUTO_ADD_CR');
            $cart_recover_url = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 3, $a_cr);
            $cart_recover_url_s1 = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 1, $a_cr);
            $cart_recover_url_s2 = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 2, $a_cr);
            $cart_recover_url_no_coupon_s0 = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 0, 0);
            $cart_recover_url_no_coupon_s1 = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 1, 0);
            $cart_recover_url_no_coupon_s2 = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 2, 0);
            $cart_recover_url_no_coupon_s3 = TACartReminderTools::getCartRecoverUrl($id_reminder, $cart, $cart_context, 3, 0);
            // TODO Improve Folow CacheGrind ools::getContentCart cache summary details gain 37% to 34% in
            $cart_products_html = TACartReminderTools::getContentCart($cart);
            $template_vars = [
                '{customer_firstname}' => $row['customer_firstname'],
                '{customer_lastname}' => $row['customer_lastname'],
                '{unscribe_link_start}' => '<a href="' . $unscibe_url . '">',
                '{unscribe_link_end}' => '</a>',
                '{unscribe_url}' => $unscibe_url,
                '{cart_url}' => $cart_recover_url,
                '{cart_url_s1}' => $cart_recover_url_s1,
                '{cart_url_s2}' => $cart_recover_url_s2,
                '{cart_url_s3}' => $cart_recover_url,
                '{cart_url_no_coupon_s0}' => $cart_recover_url_no_coupon_s0,
                '{cart_url_no_coupon_s1}' => $cart_recover_url_no_coupon_s1,
                '{cart_url_no_coupon_s2}' => $cart_recover_url_no_coupon_s2,
                '{cart_url_no_coupon_s3}' => $cart_recover_url_no_coupon_s3,
                '{cart_link_start}' => '<a href="' . $cart_recover_url . '">',
                '{cart_link_end}' => '</a>',
                '{cart_products}' => $cart_products_html,
                '{cart_products_txt}' => TACartReminderTools::getContentCart($cart, null, 'txt'),
                '{voucher_code}' => '',
                '{voucher_expirate_date}' => '',
            ];
            if ($row['create_cart_rule'] && (int) $row['new_id_cart_rule']) {
                if (!isset($cart_rule)) {
                    $cart_rule = new CartRule((int) $row['new_id_cart_rule']);
                }
                $template_vars['{voucher_code}'] = $cart_rule->code;
                $template_vars['{voucher_expirate_date}'] = Tools::displayDate($cart_rule->date_to, null);
            }
            if ($manual_process && $type_perform == 'INIT') {
                $link = new Link();
                $template_vars['{link_admin_live_cart_reminder}'] = $link->getAdminLink('AdminLiveCartReminder', false);
                $template_vars['{rule_name}'] = $rule->name;
                $admin_mails = (string) $row['admin_mails'];
                if (strpos($admin_mails, ',') !== false) {
                    $admin_mails = explode(',', $admin_mails);
                }
                try {
                    $cart_price_display = TACartReminderTools::getCartPriceDisplay($cart);
                    $shop_langdefault_id = (int) Configuration::get('PS_LANG_DEFAULT', null, null, (int) $cart->id_shop);
                    @Mail::Send(
                        $shop_langdefault_id,
                        //'new_cart_manual_process',
                        $row['modele_email'],
                        sprintf(
                            $module_instance->l(
                                'Reminder To Do / cart %1$s / %2$s / %3$s %4$s',
                                'tacartreminderjournal'
                            ),
                            (int) $cart->id,
                            $cart_price_display,
                            $row['customer_firstname'],
                            $row['customer_lastname']
                        ),
                        $template_vars,
                        $admin_mails,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $module_instance->getLocalPath() . 'mails/',
                        false,
                        $cart->id_shop
                    );
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } elseif (!$manual_process && (int) $row['id_mail_template']) {
                $mail_template = new TACartReminderMailTemplate((int) $row['id_mail_template']);
                $uid_track_read = (string) self::generateCode();
                if (Validate::isLoadedObject($mail_template) && $mail_template->id) {
                    $message_title = '';
                    $id_lang = (int) $row['customer_lang'];
                    if (isset($mail_template->title[$id_lang]) && !empty($mail_template->title[$id_lang])) {
                        $message_title = $mail_template->title[$id_lang];
                    }
                    $img_hack_read = TACartReminderTools::getImageHack($id_reminder, $uid_track_read);
                    if (!isset($mail_template->subject[$id_lang])
                        || empty($mail_template->content_html[$id_lang])
                        || empty($mail_template->content_txt[$id_lang])) {
                        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT', null, null, (int) $cart->id_shop);
                        $report .= sprintf(
                            $module_instance->l('The customer lang: %1$s is unknown or the subject, html, txt is empty for this lang.'),
                            $cart_rule->id,
                            $code
                        );
                        $report .= "\r\n";
                        $report .= sprintf(
                            $module_instance->l('The ID lang email has been changed to default lang: %1$s'),
                            $id_lang
                        );
                        $report .= "\r\n";
                    }
                    $subject = (string) $mail_template->subject[$id_lang];
                    $subject = str_replace('{customer_firstname}', $row['customer_firstname'], $subject);
                    $subject = str_replace('{customer_lastname}', $row['customer_lastname'], $subject);
                    $content_html = (string) $mail_template->content_html[$id_lang] . $img_hack_read;
                    $content_html = str_replace('{message_title}', $message_title, $content_html);
                    $content_html = str_replace('{message_title}', $message_title, $content_html);
                    $content_html = str_replace('{cart_products}', $cart_products_html, $content_html);
                    $content_txt = (string) $mail_template->content_txt[$id_lang];
                    $content_txt = str_replace('{message_title}', $message_title, $content_txt);
                    if (TACartReminderTools::FORCE_USE_STD_PRESTASHOP_FUNCTION) {
                        // replace in content_html and content_txt all template vars
                        foreach ($template_vars as $key => $value) {
                            $content_html = str_replace($key, $value, $content_html);
                            $content_txt = str_replace($key, $value, $content_txt);
                        }
                        $template_vars['content_html'] = TACartReminderTools::cartCSSToInline($content_html);
                        $template_vars['content_txt'] = $content_txt;
                        $mail_sended = @Mail::Send(
                            $cart->id_lang,
                            //'generic_template',
                            $row['modele_email'],
                            $subject,
                            $template_vars,
                            (string) $row['customer_email'],
                            (string) $row['customer_firstname'],
                            null,
                            null,
                            null,
                            null,
                            _PS_MODULE_DIR_ . 'tacartreminder/mails/',
                            false,
                            $cart->id_shop
                        );
                    } else {
                        $mail_sended = @TACartReminderTools::send(
                            $id_lang,
                            $content_txt,
                            $content_html,
                            $subject,
                            $template_vars,
                            (string) $row['customer_email'],
                            (string) $row['customer_firstname'],
                            (int) $row['id_shop']
                        );
                    }
                    if ($mail_sended) {
                        $report .= sprintf(
                            $module_instance->l('Email name: $1%s; is sent to $2%s.'),
                            $mail_template->name,
                            $row['customer_email']
                        );
                        $report .= "\r\n";
                    } else {
                        $report .= sprintf(
                            $module_instance->l('Error when sending email: $1%s; sent to $2%s'),
                            $mail_template->name,
                            $row['customer_email']
                        );
                        $report .= "\r\n";
                    }
                }
            } elseif (!$manual_process && !(int) $row['id_mail_template']) {
                throw new PrestaShopException(sprintf(Tools::displayError('Can\'t load MailTemplate object, the mail template id %s is there?'), $row['id_mail_template']));
            }
            $journal = new TACartReminderJournal((int) $row['id_journal']);
            $state = 'RUNNING';
            if (!$journal->id) {
                if ($row['create_cart_rule'] && (int) $row['id_cart_rule']) {
                    $journal->id_cart_rule = $cart_rule->id;
                }
                $journal->id_shop = (int) $row['id_shop'];
                $journal->id_cart = (int) $cart->id;
                $journal->date_upd_cart = $cart->date_upd;
                $journal->id_customer = (int) $row['id_customer'];
                $journal->email = (string) $row['customer_email'];
                $journal->rule_name = (string) $row['rule_name'];
                $journal->state = $state;
                $journal->id_rule = (int) $row['id_rule'];
                $journal->add();
            }
            if ((int) $journal->id) {
                $data_reminder = [
                    'id_journal' => $journal->id,
                    'id_reminder' => (int) $id_reminder,
                    'uid_track_read' => pSQL(isset($uid_track_read) ? $uid_track_read : ''),
                    'manual_process' => $manual_process,
                    'isopen' => 0,
                    'isclick' => 0,
                    'date_add' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                ];
                if (!$manual_process) {
                    $data_reminder['mail_name'] = pSQL($mail_template->name);
                    $data_reminder['id_mail_template'] = (int) $mail_template->id;
                }
                if (!($manual_process && $type_perform == 'INIT')) {
                    $data_reminder['date_performed'] = date('Y-m-d H:i:s');
                    $data_reminder['performed'] = 1;
                }
                if ($type_perform == 'DONE' && (int) $id_employee) {
                    $data_reminder['id_employee'] = (int) $id_employee;
                }
                if (isset($id_employee) && (int) $id_employee) {
                    $employee = new Employee($id_employee);
                    if (Validate::isLoadedObject($employee) && $employee->id) {
                        $mess = new TACartReminderMessage();
                        $mess->id_reminder = (int) $id_reminder;
                        $mess->id_journal = (int) $journal->id;
                        if ($manual_process && $type_perform == 'DONE') {
                            $mess->message = (string) $module_instance->l('The reminder was flagged as completed.');
                        } elseif ($manual_process && $type_perform == 'FINISH') {
                            $mess->message =
                                $module_instance->l('The reminder was flagged as completed and the cart reminder was marked as completed.');
                        } elseif (empty($message)) {
                            $mess->message =
                                $module_instance->l('The reminder was generated manually by the employee.');
                        } else {
                            $mess->message = (string) $message;
                        }
                        $mess->id_employee = $id_employee;
                        $mess->add();
                    }
                }
                if (!(int) $row['jr_id_reminder']) {
                    Db::getInstance()->insert(
                        'ta_cartreminder_journal_reminder',
                        $data_reminder,
                        false,
                        true,
                        Db::INSERT_IGNORE
                    );
                } else {
                    unset($data_reminder['date_add']);
                    unset($data_reminder['id_reminder']);
                    unset($data_reminder['id_journal']);
                    Db::getInstance()->update(
                        'ta_cartreminder_journal_reminder',
                        $data_reminder,
                        '`id_reminder` = ' . (int) $id_reminder . ' AND `id_journal` = ' . (int) $journal->id
                    );
                }
                if ($rule->isLastReminder((int) $id_reminder) || $type_perform == 'FINISH') {
                    $state = 'FINISHED';
                }
                if (isset($data_reminder['performed'])
                    && (int) $data_reminder['performed']
                    && $state == 'FINISHED' && $journal->id) {
                    $journal->state = 'FINISHED';
                    $journal->update();
                }
            } else {
                throw new PrestaShopException(Tools::displayError('Error to save journal object'));
            }
        } else {
            throw new PrestaShopException(sprintf(Tools::displayError('No reminder found cart:%1$s and reminder:%2$s '), $cart->id, $id_reminder));
        }
    }

    /**
     * Generate code by pattern LNLNLNL
     *
     * @param string $prefix
     * @param number $length
     * @param string $cardrulecheck
     *
     * @return string
     */
    public static function generateCode($prefix = '', $code_format = 'LNLLLLNNNN', $cardrulecheck = false)
    {
        $code = '';
        $possible_number = '0123456789';
        $possible_letter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxlength_number = Tools::strlen($possible_number);
        $maxlength_letter = Tools::strlen($possible_letter);
        $code_format_char_arr = str_split($code_format);
        foreach ($code_format_char_arr as $code_format_char) {
            $char = '';
            if ($code_format_char == 'L') {
                $char = Tools::substr($possible_letter, mt_rand(0, $maxlength_letter - 1), 1);
            }
            if ($code_format_char == 'N') {
                $char = Tools::substr($possible_number, mt_rand(0, $maxlength_number - 1), 1);
            }
            $code .= $char;
        }
        /* test si le code existe */
        if ($cardrulecheck) {
            $id_cart_rule = CartRule::getIdByCode($prefix . $code);
            if ($id_cart_rule && (int) $id_cart_rule > 0) {
                return self::generateCode($prefix, $code_format . 'L', $cardrulecheck);
            }
        }

        return $prefix . $code;
    }

    /**
     * Return journals depending on customer
     *
     * @param string $customer_email
     * @param int $id_shop
     * @param string $state
     *
     * @return unknown
     */
    public static function getJournalsByCustomer($customer_email, $id_shop, $state = '')
    {
        $journals = Db::getInstance()->executeS(
            '
				SELECT j.*
				FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
				WHERE j.`email` = \'' . pSQL($customer_email) . '\'
					AND j.`id_shop` = ' . (int) $id_shop . (!empty($state) ? ' AND j.`state`=\'' . pSQL($state) . '\'' : '')
        );

        return $journals;
    }

    /**
     * Indicate reminder is opened in email
     *
     * @param string $uid_track_read
     * @param int $id_reminder
     *
     * @return true if reminder update
     */
    public static function markReminderIsOpen($uid_track_read, $id_reminder)
    {
        if (isset($uid_track_read) && !empty($uid_track_read) && (int) $id_reminder) {
            $data_reminder = [
                'isopen' => 1,
                'date_upd' => date('Y-m-d H:i:s'),
            ];
            $result = Db::getInstance()->update(
                'ta_cartreminder_journal_reminder',
                $data_reminder,
                '`id_reminder` = ' . (int) $id_reminder . ' AND `uid_track_read` = \'' . pSQL($uid_track_read) . '\''
            );

            return $result;
        }

        return false;
    }

    /**
     * Indicate reminder is clicked in email
     *
     * @param int $id_journal
     * @param int $id_reminder
     *
     * @return bool if reminder is update
     */
    public static function markReminderIsClick($id_journal, $id_reminder)
    {
        if ((int) $id_reminder && (int) $id_journal) {
            // Indicate reminder is read
            $data_read_reminder = [
                'isopen' => 1,
                'date_upd' => date('Y-m-d H:i:s'),
            ];
            $result = Db::getInstance()->update(
                'ta_cartreminder_journal_reminder',
                $data_read_reminder,
                '`id_reminder` = ' . (int) $id_reminder . ' AND `id_journal` = ' . (int) $id_journal
            );

            // Indicate reminder is click
            $data_reminder = [
                'isclick' => 1,
                'date_upd' => date('Y-m-d H:i:s'),
            ];
            $result &= Db::getInstance()->update(
                'ta_cartreminder_journal_reminder',
                $data_reminder,
                '`id_reminder` = ' . (int) $id_reminder . ' AND `id_journal` = ' . (int) $id_journal
            );

            return $result;
        }

        return false;
    }

    /**
     * Clean journal running if all reminder is performed
     *
     * @throws PrestaShopDatabaseException
     */
    public static function cleanJournal()
    {
        Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal j
				INNER JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder jr on jr.id_journal = j.id_journal
				WHERE jr.id_reminder = ALL  (SELECT id_reminder FROM ' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder r
										 	 WHERE r.id_rule = j.id_rule) AND j.`state` = \'RUNNING\''
        );
    }

    /**
     * This function auto detect manual reminder is not accomplish but
     * employee working with that
     * so autoaccomplish
     */
    public function smartAccomplishManualReminders()
    {
        $journal_reminders = $this->getJournalReminders();
        foreach ($journal_reminders as $journal_reminder) {
            if ($journal_reminder && (int) $journal_reminder['id_reminder']
                && (int) $journal_reminder['id_employee'] && !(int) $journal_reminder['performed']
                && (int) $journal_reminder['manual_process']) {
                $data_jreminder = [
                    'performed' => 1,
                    'date_performed' => date('Y-m-d H:i:s'),
                    'date_upd' => date('Y-m-d H:i:s'),
                ];
                Db::getInstance()->update(
                    'ta_cartreminder_journal_reminder',
                    $data_jreminder,
                    '`id_reminder` = ' . (int) $journal_reminder['id_reminder'] . ' AND `id_journal` = ' . (int) $this->id
                );
            }
        }
    }

    /**
     * Test the reminder is present in journal
     *
     * @param int $id_cart
     * @param int $id_reminder
     *
     * @return bool if is present
     */
    public static function isPresentINJR($id_cart, $id_reminder)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr
				INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_journal = jr.id_journal
				WHERE j.id_cart = ' . (int) $id_cart . ' AND jr.id_reminder=' . (int) $id_reminder;
        $result = Db::getInstance()->executeS($sql, true, false);

        return count($result) > 0;
    }

    /**
     * Process ordered, the reminder permit a order
     *
     * @param int $id_order
     */
    public function toOrdered($id_order)
    {
        $module_instance = new TACartReminder();
        if ($this->id && !(int) $this->id_order) {
            $this->id_order = (int) $id_order;
            $this->state = 'FINISHED';
            $this->update();
            $mess = new TACartReminderMessage();
            $mess->id_journal = (int) $this->id;
            $mess->message = (string) sprintf(
                $module_instance->l('Congratulations on your new order! Order ID: %1$s.', 'tacartreminderjournal'),
                (int) $id_order
            );
            $mess->is_system = true;
            $mess->add();
            $journal_reminder = $this->getLastPerformedReminder();
            if ($journal_reminder && isset($journal_reminder['id_journal'])
                && (int) $journal_reminder['id_journal'] && (int) $journal_reminder['id_reminder']) {
                $id_reminder = (int) $journal_reminder['id_reminder'];
                $data_reminder = [
                    'id_order' => (int) $id_order,
                    'date_upd' => date('Y-m-d H:i:s'),
                ];
                Db::getInstance()->update(
                    'ta_cartreminder_journal_reminder',
                    $data_reminder,
                    '`id_reminder` = ' . (int) $id_reminder . ' AND `id_journal` = ' . (int) $this->id
                );
            }
            $this->smartAccomplishManualReminders();
        }
    }
}
