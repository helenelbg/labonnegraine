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
 * TACartReminderCleans is class tools permit to clean data
 * Event to clean data?
 * clean data if cart is ordered
 * no reminder to launch, all reminder is performed
 * cart as deleted by employee
 * cartRule expired and time laps ok
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderCleans
{
    /**
     * Check if reminder running is ordered
     * if reminder is ordered also this reminder is close
     */
    public static function cleanIsOrdered()
    {
        $journals_to_ordered = Db::getInstance()->executeS(
            'SELECT j.id_journal,o.id_order FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
				INNER JOIN `' . _DB_PREFIX_ . 'cart` c on c.`id_cart` = j.`id_cart`
				INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON o.`id_customer` = j.`id_customer`
				INNER JOIN `' . _DB_PREFIX_ . 'cart` co ON o.`id_cart` = co.`id_cart`
				WHERE j.`state` = \'RUNNING\' AND c.`date_upd` <= co.`date_upd`'
        );
        foreach ($journals_to_ordered as $journal_row) {
            if ((int) $journal_row['id_journal'] && (int) $journal_row['id_order']) {
                $journal_running = new TACartReminderJournal((int) $journal_row['id_journal']);
                $journal_running->toOrdered((int) $journal_row['id_order']);
            }
        }
    }

    /**
     * System message
     */
    public static function countJournalMessageSystem($type = 'all')
    {
        $sql_like = '';
        if ($type == 'cart_expirate') {
            $sql_like = ' AND `message` LIKE  \'%Reminder canceled, last update cart is%\'';
        }
        $count_system_message = Db::getInstance()->getValue(
            'SELECT count(*) FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal_message` jm
            WHERE jm.`is_system` =1' . $sql_like
        );

        return $count_system_message;
    }

    /**
     * Check if all reminder is performed
     * if true the journal is closed
     */
    public static function noReminderToLaunch()
    {
        $module_instance = new TACartReminder();
        $journals_with_no_reminder = Db::getInstance()->executeS(
            'SELECT DISTINCT j.`id_journal` FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
            INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder` jr on jr.`id_journal` = j.`id_journal`
            WHERE j.`state` = \'RUNNING\' AND jr.`performed` = 1 AND jr.`id_reminder` = ALL
            (SELECT id_reminder FROM ' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder r
            WHERE r.`id_rule` = j.`id_rule`)'
        );
        foreach ($journals_with_no_reminder as $journal_row) {
            $journal_running = new TACartReminderJournal((int) $journal_row['id_journal']);
            $message = (string) $module_instance->l('All reminders have been launched. The reminder is finished.');
            $journal_running->close($message, null, 'FINISHED');
        }
    }

    /**
     * Check if cart not exist the journal is closed
     */
    public static function cartNotExist()
    {
        $module_instance = new TACartReminder();
        $journals_with_no_cart = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal j
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON c.`id_cart` = j.`id_cart`
            WHERE j.`state` = \'RUNNING\' AND c.`id_cart` IS NULL'
        );
        foreach ($journals_with_no_cart as $journal_row) {
            $journal_running = new TACartReminderJournal((int) $journal_row['id_journal']);
            $message = (string) $module_instance->l('The cart does not exist. The reminder is canceled.');
            $journal_running->close($message);
        }
    }

    /**
     * Clean if customer not exist in the cart
     */
    public static function customerNotExist()
    {
        $module_instance = new TACartReminder();
        $journals_with_no_customer = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal j
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON c.`id_cart` = j.`id_cart`
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` cust ON cust.`id_customer` = c.`id_customer`
            WHERE j.`state` = \'RUNNING\' AND cust.`id_customer` IS NULL'
        );
        foreach ($journals_with_no_customer as $journal_row) {
            $journal_running = new TACartReminderJournal((int) $journal_row['id_journal']);
            $message = (string) $module_instance->l('The customer not exist perhaps deleted. The reminder is canceled.');
            $journal_running->close($message);
        }
    }

    /**
     * Clean if last update cart is expirate
     */
    public static function cartExpirate()
    {
        $module_instance = new TACartReminder();
        $journals_with_cart_expirated = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal j
            LEFT JOIN `' . _DB_PREFIX_ . 'cart` c ON c.`id_cart` = j.`id_cart`
            WHERE  j.`state` = \'RUNNING\'
            AND c.`date_upd` < DATE_SUB(NOW(),INTERVAL ' . Configuration::get('TA_CARTR_STOPREMINDER_NB_HOUR') . ' HOUR)'
        );
        foreach ($journals_with_cart_expirated as $journal_row) {
            $journal_running = new TACartReminderJournal((int) $journal_row['id_journal']);
            $message = sprintf(
                $module_instance->l('Reminder canceled, last update cart is %1s expirate.'),
                $journal_row['date_upd']
            );
            $message .= sprintf(
                $module_instance->l('And your configuration to not remind is %1s hour'),
                (int) Configuration::get('TA_CARTR_STOPREMINDER_NB_HOUR')
            );
            $journal_running->close($message);
        }
    }

    /**
     * Clean cart rule generated by the module
     * if date_to exeeds the tim set in setting
     */
    public static function cartRule()
    {
        $cart_rule_to_deletes = Db::getInstance()->executeS(
            'SELECT cr.`id_cart_rule` FROM ' . _DB_PREFIX_ . 'ta_cartreminder_journal j
				INNER JOIN ' . _DB_PREFIX_ . 'cart_rule cr on cr.`id_cart_rule` = j.`id_cart_rule`
				where (DATE_SUB(CURDATE(),INTERVAL ' . Configuration::get('TA_CARTR_CLEANCARTRULE_NB_DAY') . ' DAY)) > cr.`date_to` 
				AND cr.`id_cart_rule` NOT IN (SELECT `id_cart_rule` FROM ' . _DB_PREFIX_ . 'order_cart_rule)'
        );
        foreach ($cart_rule_to_deletes as $cart_rule_to_delete) {
            $cart_rule = new CartRule((int) $cart_rule_to_delete['id_cart_rule']);
            $cart_rule->delete();
        }
    }
}
