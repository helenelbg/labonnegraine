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
 * TACartReminderRule is object Model for crud operation on rule object
 * Use for store a rule with many condition
 * A cart must match a rule to be remind
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../tools/TACartReminderTools.php';

class TACartReminderRule extends ObjectModel
{
    /**
     * The rule id is auto increment persist
     *
     * @var in id rule
     */
    public $id;
    /**
     * @var string Name
     */
    public $name;
    /**
     * @var date date from
     */
    public $date_from;
    /**
     * @var date date to
     */
    public $date_to;
    /**
     * @var bool create cart_rule
     */
    public $create_cart_rule;
    /**
     * @var int cart rule id
     */
    public $id_cart_rule;
    /**
     * @var int nbday validity
     */
    public $cart_rule_nbday_validity;
    /**
     * @var bool active
     */
    public $status;
    /**
     * @var bool force_reminder
     */
    public $force_reminder;
    /**
     * @var position priority check
     */
    public $position = 0;
    /**
     * @var string Object creation date
     */
    public $date_add;
    /**
     * @var string Object update date
     */
    public $date_upd;
    /**
     * type available :list,integer,string, bool, price *
     */
    public static $rel_condition_typevalue = [
        'cart_product' => 'list',
        'cart_product_stockavailable' => 'integer',
        'cart_product_stockavailable_forall' => 'integer',
        'cart_product_manufacturer' => 'list',
        'cart_product_supplier' => 'list',
        'cart_product_quantity_total' => 'integer',
        'cart_category' => 'list',
        'cart_amount' => 'price', /* total product ht with price reduction without voucher */
        'customer_lang' => 'list',
        'customer_email' => 'string',
        'customer_optin' => 'bool',
        'customer_newsletter' => 'bool',
        'customer_gender' => 'list',
        'customer_age' => 'integer',
        'customer_order_count' => 'integer',
        'customer_rule_already_applied' => 'bool',
        'customer_registration_date' => 'integer',
        'address_country' => 'list',
        'customer_group' => 'list',
    ];
    public static $map_compare = [
        '=' => 'isEqual',
        '<' => 'isLessThan',
        '<=' => 'isLessOrEqualThan',
        '>' => 'isGreaterThan',
        '>=' => 'isGreaterOrEqualThan',
        '<>' => 'isDifferent',
        'contain' => 'contain',
        'not_contain' => 'notContain',
        'match' => 'match',
    ];
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ta_cartreminder_rule',
        'primary' => 'id_rule',
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
            ],
            'date_from' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false,
            ],
            'date_to' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'required' => false,
            ],
            'id_cart_rule' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ],
            'cart_rule_nbday_validity' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ],
            'create_cart_rule' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ],
            'force_reminder' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ],
            'status' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ],
            'position' => [
                'type' => self::TYPE_INT,
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
     * Get all rules
     *
     * @param bool $active
     *                     Returns only active rules when true
     *
     * @return array Rules
     */
    public static function getRules(
        $active = false,
        $order_by = null,
        $order_way = null,
        $filters = [],
        $available_now = false,
        $shop_id = 0
    ) {
        if (empty($order_by)) {
            $order_by = 'position';
        } else {
            $order_by = 'r.' . Tools::strtolower($order_by);
        }
        $sql_filter = '';
        foreach ($filters as $field => $value) {
            if ((is_array($value) && (!empty($value[0]) || !empty($value[0])))
                || (!is_array($value) && !empty($value))
            ) {
                if ($field == 'date_from' || $field == 'date_to') {
                    $sql_filter .= (!empty($value[0]) ? ' AND r.' . $field . ' >= \'' . $value[0] . '\'' : '') .
                        (!empty($value[1]) ? ' AND r.' . $field . ' <= \'' . $value[1] . '\'' : '');
                } elseif (!is_array($value)) {
                    $sql_filter .= ' AND r.' . $field . ' like \'%' . $value . '%\' ';
                }
            }
        }
        if ($available_now) {
            $sql_filter .= ' AND (date_from IS NULL 
            OR date_from = \'0000-00-00 00:00:00\'
            OR date_from <= \'' . date('Y-m-d H:i:s') . '\' )
            AND (date_to IS NULL OR date_to = \'0000-00-00 00:00:00\'
            OR date_to >= \'' . date('Y-m-d H:i:s') . '\' ) ';
        }
        if (empty($order_way)) {
            $order_way = 'ASC';
        }
        $sqlshop = '';
        if (Shop::isFeatureActive() && (Shop::getContext() != Shop::CONTEXT_ALL || (int) $shop_id)) {
            if (!(int) $shop_id) {
                $sqlshop = ' INNER JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_shop s
    					ON s.' . self::$definition['primary'] . ' = r.' . self::$definition['primary'];
                $sqlshop .= ' AND s.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')';
            } else {
                $sqlshop = ' INNER JOIN ' . _DB_PREFIX_ . self::$definition['table'] . '_shop s
    					ON s.' . self::$definition['primary'] . ' = r.' . self::$definition['primary'];
                $sqlshop .= ' AND s.id_shop = ' . $shop_id;
            }
        }
        $sql = '
		SELECT r.*
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule` r
		' . $sqlshop . '
		WHERE 1 ';
        if ($active) {
            $sql .= ' AND r.`status` = 1 ';
        }
        $sql .= $sql_filter;
        $sql .= ' GROUP BY r.`id_rule`';
        $sql .= ' ORDER BY ' . $order_by . ' ' . $order_way;
        $cache_id = 'TACartReminderRule::getRules_' . md5($sql);
        if (!Cache::isStored($cache_id)) {
            $rules = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $rules);
        }
        $rules = Cache::retrieve($cache_id);

        return $rules;
    }

    /**
     * Get all group conditions present in a rule
     * Group condition contain also many conditions
     *
     * @example array['id_condition_group']['conditions']
     *
     * @return array of conditions
     */
    public function getGroupConditions()
    {
        if (!Validate::isLoadedObject($this)) {
            return [];
        }
        $groups = [];
        $result = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition WHERE id_rule = ' . (int) $this->id
        );
        foreach ($result as $row) {
            if (!isset($groups[$row['id_groupcondition']])) {
                $groups[$row['id_groupcondition']] = [
                    'id_groupcondition' => $row['id_groupcondition'],
                ];
            }
            $groups[$row['id_groupcondition']]['conditions'] = $this->getConditions($row['id_groupcondition']);
        }

        return $groups;
    }

    /**
     * @example ['type' => ? , 'values' => ?]
     *
     * @param $id_groupcondition
     *
     * @return array of conditions
     */
    public function getConditions($id_groupcondition)
    {
        if (!Validate::isLoadedObject($this)) {
            return [];
        }
        $conditions = [];
        $results = Db::getInstance()->executeS(
            '
		SELECT *
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition` rc
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition_value` rcv ON rc.id_condition = rcv.id_condition
		WHERE rc.id_groupcondition = ' . (int) $id_groupcondition
        );
        foreach ($results as $row) {
            if (!isset($conditions[$row['id_condition']])) {
                $conditions[$row['id_condition']] = [
                    'type' => $row['type'],
                    'values' => [],
                    'typevalue' => '',
                    'value' => '',
                    'sign' => '',
                ];
            }
            $conditions[$row['id_condition']]['values'][] = $row['id_item'];
            $conditions[$row['id_condition']]['type'] = $row['type'];
            $conditions[$row['id_condition']]['typevalue'] = $row['typevalue'];
            $conditions[$row['id_condition']]['value'] = $row['value'];
            $conditions[$row['id_condition']]['sign'] = $row['sign'];
        }

        return $conditions;
    }

    /**
     * get all reminders in the rule
     *
     * @return array
     */
    public function getReminders()
    {
        if (!Validate::isLoadedObject($this)) {
            return [];
        }
        $reminders = self::getRemindersByRule($this->id);

        return $reminders;
    }

    /**
     * Get all reminder
     *
     * @param $id_rule
     *
     * @return array
     */
    public static function getRemindersByRule($id_rule)
    {
        $reminders = Db::getInstance()->executeS(
            '
		SELECT r.*,mt.name as mail_template_name FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` mt ON mt.id_mail_template = r.id_mail_template
		WHERE r.id_rule = ' . (int) $id_rule . '
		ORDER BY r.`position` ASC'
        ); /* order position is important */

        return $reminders;
    }

    /**
     * Get reminder with all mail template information
     *
     * @param $id_reminder
     *
     * @return mixed
     */
    public static function getReminder($id_reminder)
    {
        $reminder = Db::getInstance()->getRow(
            'SELECT r.*,mt.name as mail_template_name FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` mt ON mt.id_mail_template = r.id_mail_template
		WHERE r.id_reminder = ' . (int) $id_reminder
        );

        return $reminder;
    }

    /**
     * Get all rules use a email template
     *
     * @param $id_mail_template
     *
     * @return mixed
     */
    public static function getRulesByMailTemplate($id_mail_template)
    {
        $rules = Db::getInstance()->executeS(
            'SELECT r.* FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule` r
		INNER JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` rr ON r.id_rule = rr.id_rule
		WHERE rr.id_mail_template = ' . (int) $id_mail_template . '
		GROUP BY r.id_rule'
        );

        return $rules;
    }

    /**
     * Get first reminder in the rule
     *
     * @return mixed
     */
    public function getFirstReminder()
    {
        $reminder = Db::getInstance()->getRow(
            '
		SELECT r.*,mt.name as mail_template_name FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` mt ON mt.id_mail_template = r.id_mail_template
		WHERE r.id_rule = ' . $this->id . '
		ORDER BY position ASC'
        );

        return $reminder;
    }

    /**
     * Get the Last reminder
     *
     * @return mixed
     */
    public function getLastReminder()
    {
        $reminder = Db::getInstance()->getRow(
            '
		SELECT r.*,mt.name as mail_template_name
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` mt ON mt.id_mail_template = r.id_mail_template
		WHERE r.id_rule = ' . $this->id . '
		ORDER BY position DESC'
        );

        return $reminder;
    }

    /**
     * Check if the reminder is the last reminder
     *
     * @param $id_reminder
     *
     * @return bool
     */
    public function isLastReminder($id_reminder)
    {
        $reminder = Db::getInstance()->getRow(
            '
		SELECT id_reminder
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder` r
		LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_mail_template` mt ON mt.id_mail_template = r.id_mail_template
		WHERE r.id_rule = ' . $this->id . '
		ORDER BY position DESC'
        );
        if ((int) $reminder['id_reminder'] == (int) $id_reminder) {
            return true;
        }

        return false;
    }

    /**
     * Move a rule
     *
     * @param bool $way
     *                  Up (1) or Down (0)
     * @param int $position
     *
     * @return bool Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            '
			SELECT r.`position`, r.`id_rule`
			FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule` r
			WHERE r.`id_rule` = ' . (int) Tools::getValue('id', 1) . '
			ORDER BY r.`position` ASC'
        )
        ) {
            return false;
        }
        foreach ($res as $rule) {
            if ((int) $rule['id_rule'] == (int) $this->id) {
                $moved_rule = $rule;
            }
        }
        if (!isset($moved_rule) || !isset($position)) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'ta_cartreminder_rule`
            SET `position`= `position` ' . ($way ? '- 1' : '+ 1') .
            ' WHERE `position`' .
            ($way ? ' > ' . (int) $moved_rule['position'] .
                ' AND `position` <= ' . (int) $position : ' < ' . (int) $moved_rule['position'] .
                ' AND `position` >= ' . (int) $position)
        ) && Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'ta_cartreminder_rule`
            SET `position` = ' . (int) $position .
            ' WHERE `id_rule`=' . (int) $moved_rule['id_rule']
        );
    }

    /**
     * @see ObjectModel::add
     *
     * @param bool|true $autodate
     * @param bool|false $null_values
     *
     * @return mixed
     */
    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0) {
            $this->position = self::getHigherPosition() + 1;
        }
        $return = parent::add($autodate, $null_values);
        TACartReminderRuleMatchCache::cleanAll();

        return $return;
    }

    /**
     * Update a rule clean all cache
     *
     * @see ObjectModel::update()
     *
     * @return bool succeed
     */
    public function update($null_values = false)
    {
        if ($result = parent::update($null_values)) {
            TACartReminderRuleMatchCache::cleanAll();
        }

        return $result;
    }

    /**
     * Delete a rule, update position
     *
     * @return bool succeed
     */
    public function delete()
    {
        $result = Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition` WHERE `id_rule` = ' . $this->id
        );
        if ($result) {
            $result &= Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition`
					WHERE `id_groupcondition`
					NOT IN (SELECT `id_groupcondition` FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition`)'
            );
        }
        if ($result) {
            $result &= Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition_value`
					WHERE `id_condition`
					NOT IN (SELECT `id_condition` FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition`)'
            );
        }
        if ($result) {
            $result &= Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder`
					WHERE `id_rule` = ' . $this->id
            );
        }
        if ($result) {
            $result &= parent::delete();
        }
        if ($result) {
            $result &= Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_shop` WHERE `id_rule` = ' . $this->id
            );
        }
        if ($result) {
            $result &= self::cleanPositions();
        }
        $journal_rows = TACartReminderJournal::getRunningsByRule($this->id);
        foreach ($journal_rows as $journal_row) {
            $journal = new TACartReminderJournal((int) $journal_row['id_journal']);
            $journal->state = 'CANCELED';
            $journal->update();
            $mess = new TACartReminderMessage();
            $mess->id_journal = (int) $journal->id;
            $mess->message = (string) sprintf(
                'The rule id %1$s hast been deleted, the reminder is canceled.',
                (int) $this->id
            );
            $mess->is_system = true;
            $mess->add();
        }
        TACartReminderRuleMatchCache::cleanAll();

        return $result;
    }

    /**
     * Reorder rule position
     * Call it after deleting a rule.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        $return = true;
        $sql = '
			SELECT `id_rule`
			FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule`
			ORDER BY `position`';
        $result = Db::getInstance()->executeS($sql);
        $i = 0;
        foreach ($result as $value) {
            $return = Db::getInstance()->execute(
                '
				UPDATE `' . _DB_PREFIX_ . 'ta_cartreminder_rule`
				SET `position` = ' . (int) $i++ . '
				WHERE `id_rule` = ' . (int) $value['id_rule']
            );
        }

        return $return;
    }

    /**
     * getHigherPosition
     *
     * Get the higher rule position
     *
     * @return int $position
     */
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule`';
        $position = Db::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }

    /**
     * Check if cart is applicable for a rule
     * This function is only use on demand, cache is store else
     *
     * @param $rule
     * @param $cart
     * @param int/Customer $customer
     * @param array $addresses
     * @param int $id_lang
     * @param bool|false $return_jc
     *
     * @return array|bool
     *
     * @throws PrestaShopException
     */
    public static function isApplicableRule(
        $rule,
        $cart,
        $customer = null,
        $addresses = null,
        $id_lang = null,
        $return_jc = false
    ) {
        $module_instance = new TACartReminder();
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }
        if (!is_object($rule)) {
            if ((int) $rule) {
                $rule = new TACartReminderRule((int) $rule);
            } else {
                throw new PrestaShopException('Invalid rule vars');
            }
        }
        if (!is_object($cart)) {
            if ((int) $cart) {
                $cart = new Cart((int) $cart);
            } else {
                throw new PrestaShopException('Invalid cart vars');
            }
        }
        if (Validate::isLoadedObject($cart) && $cart->id && ((int) $cart->id_customer)) {
            if (!isset($customer) || !$customer) {
                $customer = new Customer($cart->id_customer);
            }
            if (!Validate::isLoadedObject($customer) || !((int) $customer->id)) {
                return false;
            }
            if (!isset($addresses) || !$addresses) {
                $addresses = $customer->getAddresses($id_lang);
            }
            $checkrule = [];
            if ($return_jc) {
                $checkrule['rule'] = $rule;
                $checkrule['success'] = false;
                $checkrule['nbsuccess'] = 0;
                $checkrule['group_success'] = null;
                $checkrule['cg'] = [];
            }
            $group_conditions = $rule->getGroupConditions();
            if (!count($group_conditions)) {
                if ($return_jc) {
                    $checkrule['success'] = true;
                    $checkrule['nbsuccess'] = 0;
                    $checkrule['cg'] = [];
                } else {
                    return true;
                }
            }
            $cptgroup = -1;
            foreach ($group_conditions as $group_condition) {
                ++$cptgroup;
                $cptok = 0;
                if (isset($group_condition['conditions']) && count($group_condition['conditions'])) {
                    foreach ($group_condition['conditions'] as $condition) {
                        switch ($condition['type']) {
                            case 'cart_product':
                                $sql = 'SELECT *' . ($return_jc ? ',pl.name' : '') . '
												FROM `' . _DB_PREFIX_ . 'cart_product` cp
												' . ($return_jc ? ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
												ON pl.id_product = cp.id_product AND pl.id_lang=' . $id_lang : '') . '
												WHERE cp.`id_cart` = ' . (int) $cart->id . '
												AND cp.`id_product` IN (' .
                                    implode(',', array_map('intval', $condition['values'])) .
                                    ')' .
                                    ($return_jc ? ' GROUP BY cp.`id_product`' : '');
                                $cart_products = Db::getInstance()->executeS(
                                    'SELECT *' . ($return_jc ? ',pl.name' : '') . '
												FROM `' . _DB_PREFIX_ . 'cart_product` cp
												' . ($return_jc ?
                                        ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                                            ON pl.id_product = cp.id_product AND pl.id_lang=' . $id_lang : '') .
                                    ' WHERE cp.`id_cart` = ' . (int) $cart->id . ' AND cp.`id_product` IN (' .
                                    implode(',', array_map('intval', $condition['values'])) . ')' .
                                    ($return_jc ? ' GROUP BY cp.`id_product`' : '')
                                );
                                $result = ($cart_products && count($cart_products) >= 1);
                                if ($return_jc) {
                                    $value1 = '';
                                    foreach ($cart_products as $product) {
                                        $value1 .= $product['name'] . ', ';
                                    }
                                    if (!empty($value1)) {
                                        $value1 = Tools::substr($value1, 0, -2);
                                    }
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_product_quantity_total':
                                $products = $cart->getProducts();
                                $result = false;
                                $qty_total = 0;
                                foreach ($products as $product) {
                                    $qty_total += (int) $product['quantity'];
                                }
                                $result = self::matchConditionCompare(
                                    $qty_total,
                                    (int) $condition['value'],
                                    $condition['sign']
                                );
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $qty_total,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_product_stockavailable':
                                $products = $cart->getProducts();
                                $current_stock = 0;
                                $result = false;
                                $value1 = '';
                                foreach ($products as $product) {
                                    $current_stock = (int) $product['stock_quantity'];
                                    $match = self::matchConditionCompare(
                                        $current_stock,
                                        (int) $condition['value'],
                                        $condition['sign']
                                    );
                                    if ($match && !$result) {
                                        $result = true;
                                    }
                                    if ($result && !$return_jc) {
                                        break;
                                    }
                                    if ($return_jc) {
                                        if ($match) {
                                            $value1 .= '<tr class="ok"><td>' .
                                                $product['name'] .
                                                '</td><td style="text-align:right">' .
                                                $current_stock . '</td></tr>';
                                        } else {
                                            $value1 .= '<tr class="ko"><td>' .
                                                $product['name'] .
                                                '</td><td style="text-align:right">' .
                                                $current_stock .
                                                '</td></tr>';
                                        }
                                    }
                                }
                                if ($return_jc) {
                                    $value1 = '<table>' . $value1 . '</table>';
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_order_count':
                                $sql = 'SELECT count(*) as nb FROM `' . _DB_PREFIX_ . 'orders` o
									WHERE o.`valid` = 1 AND o.`id_customer` = ' . (int) $cart->id_customer .
                                    ' AND o.`id_shop` = ' . (int) $cart->id_shop;
                                $order_nb_row = Db::getInstance()->getRow($sql);
                                $order_nb = (int) $order_nb_row['nb'];
                                $match = self::matchConditionCompare(
                                    $order_nb,
                                    (int) $condition['value'],
                                    $condition['sign']
                                );
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $match,
                                        $order_nb,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$match && !$return_jc) {
                                    continue 3;
                                } elseif ($match) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_product_stockavailable_forall':
                                $products = $cart->getProducts();
                                $current_stock = 0;
                                $cpttomatch = 0;
                                $value1 = '';
                                foreach ($products as $product) {
                                    $current_stock = $product['stock_quantity'];
                                    $match = self::matchConditionCompare(
                                        $current_stock,
                                        (int) $condition['value'],
                                        $condition['sign']
                                    );
                                    if ($match) {
                                        ++$cpttomatch;
                                    } elseif (!$return_jc) {
                                        break;
                                    }
                                    if ($return_jc) {
                                        if ($match) {
                                            $value1 .= '<tr class="ok"><td>' . $product['name'] .
                                                '</td><td style="text-align:right">' . $current_stock . '</td></tr>';
                                        } else {
                                            $value1 .= '<tr class="ko"><td>' . $product['name'] .
                                                '</td><td style="text-align:right">' . $current_stock . '</td></tr>';
                                        }
                                    }
                                }
                                $result = false;
                                if (count($products) == $cpttomatch) {
                                    $result = true;
                                }
                                if ($return_jc) {
                                    $value1 = '<table>' . $value1 . '</table>';
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_category':
                                $cart_categories = Db::getInstance()->executeS(
                                    'SELECT distinct catp.id_category' . ($return_jc ? ',cl.name' : '') . '
												FROM `' . _DB_PREFIX_ . 'cart_product` cp
												INNER JOIN `' . _DB_PREFIX_ . 'category_product` catp ON
												cp.id_product = catp.id_product
												' . ($return_jc ?
                                        ' LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                                        ON cl.id_category = catp.id_category AND cl.id_lang=' . $id_lang : '') .
                                    ' WHERE cp.`id_cart` = ' . (int) $cart->id .
                                    ' AND catp.`id_category` IN (' .
                                    implode(',', array_map('intval', $condition['values'])) .
                                    ')'
                                );
                                $result = $cart_categories && count($cart_categories) >= 1;
                                if ($return_jc) {
                                    $value1 = '';
                                    foreach ($cart_categories as $cart_category) {
                                        $value1 .= $cart_category['name'] . ', ';
                                    }
                                    if (!empty($value1)) {
                                        $value1 = Tools::substr($value1, 0, -2);
                                    }
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_product_manufacturer':
                                $cart_manufacturers = Db::getInstance()->executeS(
                                    'SELECT distinct p.id_manufacturer' . ($return_jc ? ',m.name' : '') . '
												FROM `' . _DB_PREFIX_ . 'cart_product` cp
												INNER JOIN `' . _DB_PREFIX_ . 'product` p ON
												p.id_product = cp.id_product
												' . ($return_jc ?
                                        ' LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m
                                        ON p.id_manufacturer = m.id_manufacturer' : '') .
                                    ' WHERE cp.`id_cart` = ' . (int) $cart->id .
                                    ' AND p.`id_manufacturer` IN (' .
                                    implode(',', array_map('intval', $condition['values'])) .
                                    ')'
                                );
                                $result = $cart_manufacturers && count($cart_manufacturers) >= 1;
                                if ($return_jc) {
                                    $value1 = '';
                                    foreach ($cart_manufacturers as $cart_manufacturer) {
                                        $value1 .= $cart_manufacturer['name'] . ', ';
                                    }
                                    if (!empty($value1)) {
                                        $value1 = Tools::substr($value1, 0, -2);
                                    }
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_product_supplier':
                                $cart_suppliers = Db::getInstance()->executeS(
                                    'SELECT distinct ps.id_supplier' . ($return_jc ? ',s.name' : '') . '
                                        FROM `' . _DB_PREFIX_ . 'cart_product` cp
                                            INNER JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON
                                                ps.id_product = cp.id_product
                                    ' . ($return_jc ?
                                    ' INNER JOIN `' . _DB_PREFIX_ . 'supplier` s
										    ON ps.id_supplier = s.id_supplier' : '') .
                                    ' WHERE cp.`id_cart` = ' . (int) $cart->id .
                                    ' AND ps.`id_supplier` IN (' .
                                    implode(',', array_map('intval', $condition['values'])) .
                                    ')'
                                );
                                $result = $cart_suppliers && count($cart_suppliers) >= 1;
                                if ($return_jc) {
                                    $value1 = '';
                                    foreach ($cart_suppliers as $cart_supplier) {
                                        $value1 .= $cart_supplier['name'] . ', ';
                                    }
                                    if (!empty($value1)) {
                                        $value1 = Tools::substr($value1, 0, -2);
                                    }
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'cart_amount':
                                Context::getContext()->customer = new Customer((int) $cart->id_customer);
                                Context::getContext()->currency = new Currency(
                                    (int) Configuration::get('PS_CURRENCY_DEFAULT')
                                );
                                Context::getContext()->cart = $cart;
                                /* Total product HT without voucher */
                                $amount = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                                $result = self::matchConditionCompare(
                                    $amount,
                                    (float) $condition['value'],
                                    $condition['sign']
                                );
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $amount,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_age':
                                if (isset($customer->birthday) && !empty($customer->birthday)
                                    && $customer->birthday != '0000-00-00') {
                                    $birthday = $customer->birthday;
                                    $birth_date = explode('-', $birthday);
                                    $age = (date(
                                        'md',
                                        date('U', mktime(0, 0, 0, $birth_date[1], $birth_date[2], $birth_date[0]))
                                    ) > date('md')
                                        ? ((date('Y') - $birth_date[0]) - 1)
                                        : (date('Y') - $birth_date[0]));
                                    $result = self::matchConditionCompare(
                                        $age,
                                        (int) $condition['value'],
                                        $condition['sign']
                                    );
                                    if ($return_jc) {
                                        self::journalCondition(
                                            $checkrule['cg'][$cptgroup],
                                            $condition['type'],
                                            $result,
                                            $age,
                                            $condition['value'],
                                            $condition['sign']
                                        );
                                    }
                                    if (!$result && !$return_jc) {
                                        continue 3;
                                    } elseif ($result) {
                                        ++$cptok;
                                    }
                                } else {
                                    if ($return_jc) {
                                        self::journalCondition(
                                            $checkrule['cg'][$cptgroup],
                                            $condition['type'],
                                            false,
                                            $module_instance->l('Unknown', 'tacartreminderrule'),
                                            $condition['value'],
                                            $condition['sign']
                                        );
                                    } else {
                                        continue 3;
                                    }
                                }
                                break;
                            case 'customer_registration_date':
                                $ts_now = time();
                                $ts_date_add = strtotime($customer->date_add);
                                $registration_day_last = round(($ts_now - $ts_date_add) / 86400);
                                $match = self::matchConditionCompare(
                                    $registration_day_last,
                                    (int) $condition['value'],
                                    $condition['sign']
                                );
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $match,
                                        $registration_day_last,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$match && !$return_jc) {
                                    continue 3;
                                } elseif ($match) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_lang':
                                $id_lang_customer = ((isset($customer->id_lang)
                                    && (int) $customer->id_lang) ? (int) $customer->id_lang : (int) $cart->id_lang);
                                $result = in_array((string) $id_lang_customer, $condition['values']);
                                if ($return_jc) {
                                    $language_customer = new Language((int) $id_lang_customer);
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $language_customer->iso_code
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_gender':
                                $result = in_array((string) $customer->id_gender, $condition['values']);
                                if ($return_jc) {
                                    $gender = new Gender($customer->id_gender, $id_lang);
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        (string) $gender->name
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_group':
                                $cust_groups = Db::getInstance()->executeS(
                                    'SELECT * FROM `' . _DB_PREFIX_ . 'customer_group` cg' .
                                    ($return_jc ?
                                        ' LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl ON
                                        gl.id_group = cg.id_group AND gl.id_lang=' . $id_lang : '') . '
												WHERE cg.`id_customer` = ' . (int) $customer->id . '
												AND cg.`id_group` IN (' .
                                    implode(',', array_map('intval', $condition['values'])) . ')'
                                );
                                $result = $cust_groups && count($cust_groups) >= 1;
                                if ($return_jc) {
                                    $value1 = '';
                                    foreach ($cust_groups as $cust_group) {
                                        $value1 .= $cust_group['name'] . ', ';
                                    }
                                    if (!empty($value1)) {
                                        $value1 = Tools::substr($value1, 0, -2);
                                    }
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value1
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_email':
                                $result = self::matchConditionCompare(
                                    $customer->email,
                                    $condition['value'],
                                    $condition['sign']
                                );
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $customer->email,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_optin':
                                $result = ((int) $customer->optin == (int) $condition['value']);
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $customer->optin,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_rule_already_applied':
                                $sql = 'SELECT count(*) as nb FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j
									WHERE j.`id_customer` = ' . (int) $cart->id_customer .
                                    ' AND j.`id_rule` = ' . (int) $rule->id .
                                    ' AND (j.`state` != \'RUNNING\')';
                                $nb_present_row = Db::getInstance()->getRow($sql);
                                $value = (int) $nb_present_row['nb'] > 0;
                                $result = ($value == (int) $condition['value']);
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $value,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'customer_newsletter':
                                $result = ((int) $customer->newsletter == (int) $condition['value']);
                                if ($return_jc) {
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $result,
                                        $customer->newsletter,
                                        $condition['value'],
                                        $condition['sign']
                                    );
                                }
                                if (!$result && !$return_jc) {
                                    continue 3;
                                } elseif ($result) {
                                    ++$cptok;
                                }
                                break;
                            case 'address_country':
                                $foundaddr = false;
                                $value1 = '';
                                foreach ($addresses as $address) {
                                    if (in_array((string) $address['id_country'], $condition['values'])) {
                                        $foundaddr = true;
                                        if ($return_jc) {
                                            $country = new Country((int) $address['id_country'], (int) $id_lang);
                                            $value1 .= $country->name . ', ';
                                        }
                                    }
                                }
                                if ($return_jc) {
                                    if (!empty($value1)) {
                                        $value1 = Tools::substr($value1, 0, -2);
                                    }
                                    self::journalCondition(
                                        $checkrule['cg'][$cptgroup],
                                        $condition['type'],
                                        $foundaddr,
                                        $value1
                                    );
                                }
                                if (!$foundaddr && !$return_jc) {
                                    continue 3;
                                } elseif ($foundaddr) {
                                    ++$cptok;
                                }
                                break;
                            default:
                                break;
                        }
                    }
                    if ($return_jc && count($checkrule['cg'][$cptgroup]['conditions']) == $cptok) {
                        $checkrule['success'] = true;
                        ++$checkrule['nbsuccess'];
                        $checkrule['group_success'] = $cptgroup;
                    }
                }
                if ($return_jc) {
                    $checkrule['cg'][$cptgroup]['cptok'] = $cptok;
                } else {
                    return true;
                }
            }
        } else {
            // throw new PrestaShopException('Invalid cart');
            return false;
        }
        if ($return_jc) {
            return $checkrule;
        } else {
            return false;
        }
    }

    /**
     * get Applicable rule for cart
     *
     * @param int $cart_id
     * @param TACartReminderJournal $current_journal
     * @param bool if $return_jc return Rapport execution
     *
     * @return TACartReminderRule $rule or rapport if return_jc is true
     */
    public static function getApplicableRule(
        $id_cart,
        $current_journal = null,
        $return_jc = false,
        $cart_date_upd = null
    ) {
        // get cache only if no rapport wanted
        if (!$return_jc
            && (int) $id_cart
            && $cache = TACartReminderRuleMatchCache::get($id_cart, $return_jc, $cart_date_upd)
        ) {
            if ($cache->id) {
                if ((int) $cache->result) {
                    $rule = new TACartReminderRule((int) $cache->result);
                    if ((int) $rule->id) {
                        return $rule;
                    }
                } else {
                    return false;
                }
            }
        }
        $cache = new TACartReminderRuleMatchCache();
        $cart = new Cart((int) $id_cart);
        $rules = self::getRules(true, null, null, [], true, (int) $cart->id_shop);
        $checkrules = [];
        $module_instance = null;
        if ($return_jc) {
            $module_instance = new TACartReminder();
            $checkrules['common_condition'] = [];
            $checkrules['rules'] = [];
            $checkrules['rule_id_selected'] = (isset($current_journal) && $current_journal
            && $current_journal->id ? $current_journal->id_rule : 0);
        }
        if (count($rules)) {
            if (Validate::isLoadedObject($cart) && ((int) $cart->id_customer)) {
                $customer = new Customer((int) $cart->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    /* Check common condition */
                    $unsubscribed = TACartReminderCustomerUnsubscribe::exist(
                        (int) $customer->email,
                        (int) $cart->id_shop
                    );
                    $common_condition_success = (!$unsubscribed);
                    if (!isset($current_journal) || !$current_journal || !$current_journal->id) {
                        if ($return_jc) {
                            $check_after_reminder_msg =
                                $module_instance->l('No cart reminder registered.', 'tacartreminderrule');
                        }
                        $reminder = TACartReminderJournal::getLastPerformedReminderByCustomer(
                            $customer->email,
                            (int) $cart->id_shop
                        );
                        $check_after_reminder = false;
                        if ($reminder && (int) $reminder['id_reminder'] > 0) {
                            $time_now = time();
                            $time_last_upd_cart = strtotime((string) $reminder['date_performed']);
                            $delta_time_day = (($time_now - $time_last_upd_cart) / (60 * 60 * 24));
                            $delta_time_day = (int) $delta_time_day;
                            if ((int) $delta_time_day >=
                                (int) Configuration::get(
                                    'TA_CARTR_AFTERREMINDER_NB_DAY',
                                    null,
                                    null,
                                    (int) $cart->id_shop
                                )) {
                                $check_after_reminder = true;
                            }
                            if ($return_jc) {
                                $check_after_reminder_msg = sprintf(
                                    $module_instance->l('Last cart reminder : %1$s, diff with current day : %2$s, delay set : %3$s', 'tacartreminderrule'),
                                    Tools::displayDate($reminder['date_performed']),
                                    $delta_time_day,
                                    (int) Configuration::get(
                                        'TA_CARTR_AFTERREMINDER_NB_DAY',
                                        null,
                                        null,
                                        (int) $cart->id_shop
                                    )
                                );
                            }
                        } else {
                            $check_after_reminder = true;
                        }
                        if ($return_jc) {
                            $checkrules['common_conditions']['afterreminder']['test'] = $check_after_reminder;
                            $checkrules['common_conditions']['afterreminder']['info'] = $check_after_reminder_msg;
                        }
                    } else {
                        $check_after_reminder = true;
                    }
                    $common_condition_success = (!$unsubscribed) && $check_after_reminder;
                    if ($return_jc) {
                        $checkrules['common_conditions']['is_not_unsubscribed']['test'] = !$unsubscribed;
                        $checkrules['common_condition_success'] = $common_condition_success;
                    }
                    $addresses = $customer->getAddresses(Context::getContext()->language->id);
                    $cptrule = 0;
                    if ($common_condition_success || $return_jc) {
                        foreach ($rules as $rule) {
                            $rule = new TACartReminderRule((int) $rule['id_rule']);
                            $checkrule = self::isApplicableRule(
                                $rule,
                                $cart,
                                $customer,
                                $addresses,
                                Context::getContext()->language->id,
                                $return_jc
                            );
                            if ($return_jc) {
                                $checkrules['rules'][$cptrule] = $checkrule;
                            }
                            if ($return_jc) {
                                /* success and not select -> select rule */
                                if ($common_condition_success && $checkrules['rules'][$cptrule]['success']
                                    && (!isset($checkrules['rule_id_selected']) || !(int) $checkrules['rule_id_selected'])
                                ) {
                                    $checkrules['rule_id_selected'] = $rule->id;
                                }
                            } elseif ($checkrule) {
                                $cache->set($id_cart, $return_jc, (int) $rule->id);

                                return $rule;
                            }
                            ++$cptrule;
                        }
                    }
                }
            }
        }
        if ($return_jc) {
            // $cache->set($id_cart, $return_jc, $checkrules);
            return $checkrules;
        } else {
            $cache->set($id_cart, $return_jc, false);
        }

        return false;
    }

    /**
     * Get all rules use a id cart rule
     * Necessary if you assure if cart rule model is not deleted
     *
     * @param $id_cart_rule
     *
     * @return mixed
     */
    public static function getRulesUseCartRule($id_cart_rule)
    {
        $cart_rules = Db::getInstance()->executeS(
            '
		SELECT r.*
		FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule` r
		WHERE r.id_cart_rule = ' . $id_cart_rule
        );

        return $cart_rules;
    }

    /**
     * Compare $value1 $value2
     * example $value1 > $value2
     *
     * @param $value1
     * @param $value2
     * @param $sign
     *
     * @return mixed
     */
    public static function matchConditionCompare($value1, $value2, $sign)
    {
        return call_user_func_array(
            [
                'TACartReminderTools',
                (string) self::$map_compare[$sign],
            ],
            [
                $value1,
                $value2,
            ]
        );
    }

    /**
     * Store journal condition to analyse that why a rule is applicable or not applicable
     *
     * @param $journal_cg
     * @param $condion_type
     * @param $result
     * @param string $value1
     * @param string $value2
     * @param string $sign
     */
    private static function journalCondition(
        &$journal_cg,
        $condion_type,
        $result,
        $value1 = '',
        $value2 = '',
        $sign = ''
    ) {
        $journal_c = [];
        $journal_c['condition_type'] = $condion_type;
        $journal_c['condition_typevalue'] = self::$rel_condition_typevalue[(string) $condion_type];
        $journal_c['condition_result'] = $result;
        $journal_c['value1'] = (string) $value1;
        $journal_c['value2'] = (string) $value2;
        $journal_c['sign'] = (string) $sign;
        $journal_cg['conditions'][] = $journal_c;
    }
}
