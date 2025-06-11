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
 * Performance audit
 *
 * @category models
 *
 * @example TACartReminderRuleMatchCache::get(id_cart, return_jc)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderRuleMatchCache extends ObjectModel
{
    /**
     * @var int id
     *          indexed column
     */
    public $id_cart;
    /**
     * @var int id_customer
     *
     * @index column
     */
    public $return_jc;
    /**
     * @var date date_check is last date check,
     *           cart date_upd is greater the cache is greater the cache is initialised
     */
    public $date_check;
    /**
     * @var string json_result persistence(used encode to set or decode to get)
     */
    public $result;

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
        'table' => 'ta_cartreminder_rule_match_cache',
        'primary' => 'id_rule_match_cache',
        'fields' => [
            'id_cart' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'return_jc' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'result' => [
                'type' => self::TYPE_STRING,
                'size' => 3999999999999,
            ],
            'date_check' => [
                'type' => self::TYPE_DATE,
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
     * Cache the result
     *
     * @param int $id_cart
     * @param bool $return_jc not used
     * @param date $cart_date_upd
     * @param $result (int) for id_rule if not false
     */
    public static function set($id_cart, $return_jc, $result)
    {
        $cache = new TACartReminderRuleMatchCache();
        $cache->id_cart = (int) $id_cart;
        $cache->return_jc = (int) $return_jc;
        $cache->date_check = date('Y-m-d H:i:s');
        // $cache->result = Tools::jsonEncode($result);
        $cache->result = (int) $result;
        $cache->add();
    }

    /**
     * Retrieve Cache
     *
     * @param $id_cart
     * @param $return_jc
     *
     * @return TACartReminderRuleMatchCache
     */
    public static function get($id_cart, $return_jc, $cart_date_upd = null)
    {
        if (!isset($cart_date_upd)) {
            $cart = new Cart($id_cart);
            $cart_date_upd = $cart->date_upd;
        }
        $cache_rule = new TACartReminderRuleMatchCache();
        $cache_row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT c.* FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_match_cache` c
            WHERE c.`id_cart` = ' . (int) $id_cart . ' AND c.return_jc=' . (int) $return_jc
        );

        if ($cache_row) {
            $cache_rule->id = (int) $cache_row['id_rule_match_cache'];
            $time_cart_upd = strtotime($cart_date_upd);
            $time_check_upd = strtotime($cart_date_upd);
            $time_left = ($time_cart_upd - $time_check_upd);
            if ($time_left > 0) {
                return $cache_rule;
            }
            foreach ($cache_row as $key => $value) {
                if (property_exists($cache_rule, $key)) {
                    $cache_rule->{$key} = $value;
                }
            }
            // $cache_rule->result = Tools::jsonEncode($cache_rule->result);
            $cache_rule->result = (int) $cache_rule->result;
        }

        return $cache_rule;
    }

    /**
     * @param $id_cart
     *
     * @return bool
     */
    public static function clean($id_cart)
    {
        return Db::getInstance()->delete(
            'ta_cartreminder_rule_match_cache',
            '`id_cart` = ' . (int) $id_cart
        );
    }

    // remove cache depending ttl 1 days
    public static function cleanCacheTTL()
    {
        $date_ttl_delete = date('Y-m-d H:i:s', strtotime('-1 day'));

        return Db::getInstance()->delete(
            'ta_cartreminder_rule_match_cache',
            '`date_check` < \'' . pSQL($date_ttl_delete) . '\''
        );
    }

    public static function cleanAll()
    {
        return Db::getInstance()->delete(
            'ta_cartreminder_rule_match_cache'
        );
    }
}
