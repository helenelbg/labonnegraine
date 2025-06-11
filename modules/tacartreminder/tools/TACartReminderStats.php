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
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderStats
{
    /**
     * Summary Statistic
     *
     * @param string $from
     *                     eg:0000-00-00 00:00:00
     * @param string $to
     *                   eg:0000-00-00 00:00:00
     * @param string $granularity
     *                            eg:4 year, 10 day, ...
     *
     * @return array|false $result count_cart_remondrers, count_orders, total_sales
     */
    public static function getOrders($from, $to, $granularity = 0)
    {
        $sql = 'SELECT	j.`date_add`,' . ($granularity ? 'LEFT(j.date_add, ' . (int) $granularity . ') as date_gran,' : '') .
            'COUNT(j.`id_journal`) as count_cart_reminders,
						SUM(IF(o.`id_order` is NULL,0,1)) as count_orders,
						SUM(IF(o.`total_paid` is NULL,0,o.total_paid_tax_excl / o.conversion_rate)) as total_sales
				FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
				LEFT JOIN `' . _DB_PREFIX_ . 'orders` o  ON o.`id_order` = j.`id_order`
			    LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON j.`id_shop` = shop.`id_shop`' .
            Shop::addSqlRestriction(false, 'j', 'shop') .
            ' WHERE j.`date_add` BETWEEN \'' . $from . '\' AND \'' . $to . '\'';
        $date_from_gadd = '';
        if ((int) $granularity > 0) {
            $date_from_gadd = ($granularity != 42
                ? 'LEFT(j.date_add, ' . (int) $granularity . ')'
                : ' IFNULL(MAKEDATE(YEAR(j.date_add),DAYOFYEAR(j.date_add)-WEEKDAY(j.date_add)),
                CONCAT(YEAR(j.date_add),"-01-01*"))');
        }
        $sql .= empty($date_from_gadd) ? '' : ' GROUP BY ' . $date_from_gadd;
        $sql .= ' ORDER BY j.date_add';
        if ($granularity > 0) {
            $parsing_result = [];
            $result = Db::getInstance()->executeS($sql);
            foreach ($result as $row) {
                $parsing_result[$row['date_gran']] = $row;
            }

            return $parsing_result;
        } else {
            $result = Db::getInstance()->getRow($sql);
            if ($result['count_orders'] === null) {
                $result['count_orders'] = 0;
            }
            if ($result['total_sales'] === null) {
                $result['total_sales'] = 0;
            }
        }

        return $result;
    }

    /**
     * Mail Statistic
     *
     * @param string $from
     *                     eg:0000-00-00 00:00:00
     * @param string $to
     *                   eg:0000-00-00 00:00:00
     *
     * @return array|false $result name, nb_send, nb_click, nb_open
     */
    public static function getMail($from, $to, $summary = false)
    {
        $sql = 'SELECT	mt.`name`,
						COUNT(jr.id_journal) as nb_send,
						IFNULL(SUM(jr.isclick), 0) as nb_click,
						IFNULL(SUM(jr.isopen), 0) as nb_open
				FROM ' . _DB_PREFIX_ . 'ta_cartreminder_mail_template mt
				LEFT JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder jr
						ON jr.id_mail_template = mt.id_mail_template
						AND jr.date_performed BETWEEN \'' . $from . '\' AND \'' . $to . '\'
				LEFT JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal j  ON jr.id_journal = j.id_journal
			    LEFT JOIN ' . _DB_PREFIX_ . 'shop shop ON j.id_shop = shop.id_shop' .
            Shop::addSqlRestriction(false, 'j', 'shop') . ' ' . (!$summary ? ' GROUP BY mt.id_mail_template' : '') .
            ' ORDER BY mt.`name`';
        if ($summary) {
            $result = Db::getInstance()->getRow($sql);
        } else {
            $result = Db::getInstance()->executeS($sql);
        }

        return $result;
    }

    /**
     * Rule Statistic
     *
     * @param string $from
     *                     eg:0000-00-00 00:00:00
     * @param string $to
     *                   eg:0000-00-00 00:00:00
     *
     * @return array|false $result name, cart_reminder_nb, order_sum_nb, order_sum_paid
     */
    public static function getRule($from, $to)
    {
        $sql = 'SELECT r.`name`,
				COUNT(j.`id_journal`) as cart_reminder_nb,
				SUM(IF(o.`id_order` is NULL,0,1)) as order_sum_nb,
				SUM(IF(o.`total_paid` is NULL,0,o.total_paid_tax_excl / o.conversion_rate)) as order_sum_paid
				FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule` r
				LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
						ON j.`id_rule` = r.`id_rule`
						AND j.`date_add` BETWEEN \'' . $from . '\' AND \'' . $to . '\'
				LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = j.id_order ' .
            Shop::addSqlRestriction(false, 'j', 'shop') . ' GROUP BY r.`id_rule` ORDER BY r.`name`';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    /**
     * Employee Statistic
     *
     * @param string $from
     *                     eg:0000-00-00 00:00:00
     * @param string $to
     *                   eg:0000-00-00 00:00:00
     *
     * @return array|false $result firstname, lastname, cart_reminder_nb, order_sum_nb, order_sum_paid
     */
    public static function getEmployee($from, $to)
    {
        $sql = 'SELECT e.`firstname`,
					   e.`lastname`,
					   COUNT(j.`id_journal`) as cart_reminder_nb,
				       SUM(IF(o.`id_order` is NULL,0,1)) as order_sum_nb,
				       SUM(IF(o.`total_paid` is NULL,0,o.total_paid_tax_excl / o.conversion_rate)) as order_sum_paid 
				FROM ' . _DB_PREFIX_ . 'employee e
				LEFT JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder jr
						ON jr.id_employee = e.id_employee
						AND jr.manual_process = 1
						AND jr.`date_performed` BETWEEN \'' . $from . '\' AND \'' . $to . '\'
				LEFT JOIN ' . _DB_PREFIX_ . 'ta_cartreminder_journal j ON j.`id_journal` = jr.`id_journal`
				LEFT JOIN ' . _DB_PREFIX_ . 'orders o on o.`id_order` = jr.`id_order`
				GROUP BY e.`id_employee`
				ORDER BY e.`firstname`';
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }
}
