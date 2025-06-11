<?php
/**
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2022 idnovate.com
*  @license   See above
*/

include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRule.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleFamily.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountDatabase.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleCondition.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleGroup.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleAction.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleMessage.php');

if (!function_exists('array_column')) {
    include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/array_column.php');
}

class QuantityDiscountRule extends ObjectModel
{
    protected static $_discountedProducts = array();
    protected static $executed = false;
    //protected static $inExecution = false;

    public $id_quantity_discount_rule;
    public $id_shop;
    public $name;
    public $active = true;
    public $description;
    public $id_family;
    public $code;
    public $code_prefix;
    public $date_from;
    public $date_to;
    public $quantity = 9999;
    public $quantity_per_user = 9999;
    public $priority = 0;
    public $execute_other_rules = 0;
    public $compatible_cart_rules = 0;
    public $compatible_qdp_rules = 1;
    public $apply_products_already_discounted = 1;
    public $modules_exceptions;
    public $highlight;
    public $date_add;
    public $date_upd;
    public $specific_price_output = null;

    public static $definition = array(
        'table' => 'quantity_discount_rule',
        'primary' => 'id_quantity_discount_rule',
        'multilang' => true,
        'fields' => array(
            'id_shop'                           => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            //Information
            'active'                            => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'description'                       => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65534),
            'id_family'                         => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'code'                              => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),
            'code_prefix'                       => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),
            'date_from'                         => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to'                           => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'priority'                          => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'execute_other_rules'               => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'compatible_cart_rules'             => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'compatible_qdp_rules'              => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'apply_products_already_discounted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'quantity'                          => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'quantity_per_user'                 => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'modules_exceptions'                => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65534),
            'highlight'                         => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'date_add'                          => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd'                          => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            //Lang fields
            'name'                              => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
        ),
    );

    public function __construct($id = null, $lang = null)
    {
        $this->context = Context::getContext();

        //Default code prefix
        $this->code_prefix = 'QD_';

        parent::__construct($id, $lang);

        $this->id_quantity_discount_rule = $this->id;
    }

    public function add($autodate = true, $null_values = false)
    {
        $this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;

        return parent::add($autodate, false);
    }

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        $this->condition_selectors = array('group', 'product', 'category', 'country', 'attribute', 'zone', 'manufacturer', 'carrier', 'supplier', 'order_state', 'gender', 'currency');
        $this->action_selectors = array('product', 'category', 'attribute', 'manufacturer', 'supplier');

        $result = Db::getInstance()->delete('quantity_discount_rule_condition', '`id_quantity_discount_rule` = '.(int)$this->id);
        foreach ($this->condition_selectors as $type) {
            $result &= Db::getInstance()->delete('quantity_discount_rule_condition_'.$type, '`id_quantity_discount_rule` = '.(int)$this->id);
        }

        $result &= Db::getInstance()->delete('quantity_discount_rule_action', '`id_quantity_discount_rule` = '.(int)$this->id);
        foreach ($this->action_selectors as $type) {
            $result &= Db::getInstance()->delete('quantity_discount_rule_action_'.$type, '`id_quantity_discount_rule` = '.(int)$this->id);
        }

        $result &= Db::getInstance()->delete('quantity_discount_rule_cart', '`id_quantity_discount_rule` = '.(int)$this->id);
        Db::getInstance()->delete('quantity_discount_rule_message_lang', '`id_quantity_discount_rule_message` IN (SELECT `id_quantity_discount_rule_message` FROM `'._DB_PREFIX_.'quantity_discount_rule_message` WHERE `id_quantity_discount_rule` = '.(int)$this->id.')');

        Db::getInstance()->delete('quantity_discount_rule_message', '`id_quantity_discount_rule` = '.(int)$this->id);
        $result &= Db::getInstance()->delete('quantity_discount_rule_order', '`id_quantity_discount_rule` = '.(int)$this->id);

        return $result;
    }

    public function getGroups($object = false)
    {
        $cache_key = 'QuantityDiscountRule::getGroups_'.(int)$this->id_quantity_discount_rule.'_'.(bool)$object;

        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS(
                'SELECT * FROM `'._DB_PREFIX_.'quantity_discount_rule_group` t
                WHERE `id_quantity_discount_rule` = '.(int)$this->id_quantity_discount_rule.'
                ORDER BY `id_quantity_discount_rule_group` ASC'
            );

            if ($object) {
                $result = ObjectModel::hydrateCollection('QuantityDiscountRuleGroup', $result);
            }

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    public function getActions($object = false)
    {
        $cache_key = 'QuantityDiscountRule::getActions_'.(int)$this->id_quantity_discount_rule.'_'.(bool)$object;

        if (!Cache::isStored($cache_key)) {
            $result = Db::getInstance()->executeS(
                'SELECT * FROM `'._DB_PREFIX_.'quantity_discount_rule_action` t
                WHERE `id_quantity_discount_rule` = '.(int)$this->id_quantity_discount_rule.'
                ORDER BY `id_type` ASC'
            );

            if ($object) {
                foreach ($result as &$row) {
                    $row = new QuantityDiscountRuleAction((int)$row['id_quantity_discount_rule_action']);
                }
            }

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

         return $result;
    }

    public function getMessages()
    {
        $cache_key = 'QuantityDiscountRule::getMessages_'.(int)$this->id_quantity_discount_rule;

        if (!Cache::isStored($cache_key)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'quantity_discount_rule_message` t
                WHERE `id_quantity_discount_rule` = '.(int)$this->id_quantity_discount_rule.'
                ORDER BY 1';

            $result = Db::getInstance()->executeS($sql);

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

         return $result;
    }

    public function getMessagesByHook($hookName)
    {
        if (!$hookName) {
            return;
        }

        $cache_key = 'QuantityDiscountRule::getMessagesByHook_'.(int)$this->id_quantity_discount_rule.'_'.$hookName;

        if (!Cache::isStored($cache_key)) {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'quantity_discount_rule_message` t
                WHERE `id_quantity_discount_rule` = '.(int)$this->id_quantity_discount_rule.'
                    AND `hook_name` = \''.pSQL($hookName).'\'';

            $result = Db::getInstance()->executeS($sql);

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

         return $result;
    }

    public static function getQuantityDiscountRulesByFamily($id_family = false, $codes = false, $highlight = false, $active = false)
    {
        if ($id_family && !(int)$id_family > 0) {
            return;
        }

        $cache_key = 'QuantityDiscountRule::getQuantityDiscountRulesByFamily_'.($id_family ? (int)$id_family : 'false').'_'.md5(json_encode($codes));

        if (!Cache::isStored($cache_key)) {
            $now = date('Y-m-d H:i:s');
            $sql = "SELECT *, qdrl.`name`, '' as id_customer
                FROM `"._DB_PREFIX_ ."quantity_discount_rule` qdr
                LEFT JOIN `"._DB_PREFIX_ ."quantity_discount_rule_lang` qdrl ON (qdr.`id_quantity_discount_rule` = qdrl.`id_quantity_discount_rule` AND qdrl.`id_lang` = ".Context::getContext()->language->id.")
                WHERE qdr.`active` = 1".
                ($id_family ? " AND `id_family` = ".(int)$id_family : "").
                ($codes ? " AND (`code` = '' OR code IN ('".implode('\',\'', $codes)."'))" : '').
                ($highlight ? " AND `code` != '' AND `highlight` = 1" : '').
                ($active ? " AND `date_from` <= '" . $now . "' AND `date_to` >= '" . $now  ."'" : "").
                (!$codes && !$highlight ? " AND `code` = ''" : '').
                " ORDER BY qdr.`priority` ASC, qdr.`id_quantity_discount_rule` ASC";

            $result = Db::getInstance()->ExecuteS($sql);

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    public static function getQuantityDiscountRulesWithCondition($id_type)
    {
        $sql = "SELECT qdr.`id_quantity_discount_rule`, qdrc.`id_quantity_discount_rule_condition`
            FROM `"._DB_PREFIX_ ."quantity_discount_rule` qdr
            INNER JOIN  `"._DB_PREFIX_ ."quantity_discount_rule_condition` qdrc ON (qdr.`id_quantity_discount_rule` = qdrc.`id_quantity_discount_rule`)
            WHERE qdrc.`id_type` = ".(int)$id_type
            ." AND qdr.`id_shop` = ".(int)Context::getContext()->shop->id;

        return Db::getInstance()->ExecuteS($sql);
    }

    public static function getQuantityDiscountRulesByFamilyForMessages($id_family, $hookName)
    {
        if (!(int)$id_family > 0) {
            return;
        }

        if (!(int)Context::getContext()->cart->id_lang) {
            $idLang = (int)Configuration::get('PS_LANG_DEFAULT');
        } else {
            $idLang = (int)Context::getContext()->cart->id_lang;
        }

        $cache_key = (int)$id_family.'_'.$hookName;

        if (!Cache::isStored($cache_key)) {
            $sql = "SELECT DISTINCT(qdr.`id_quantity_discount_rule`)
                FROM `"._DB_PREFIX_ ."quantity_discount_rule` qdr
                INNER JOIN `"._DB_PREFIX_."quantity_discount_rule_lang` qdrl
                    ON (qdr.`id_quantity_discount_rule` = qdrl.`id_quantity_discount_rule` AND qdrl.`id_lang` = ".$idLang.")
                INNER JOIN `"._DB_PREFIX_."quantity_discount_rule_message` qdrm
                    ON (qdrm.`id_quantity_discount_rule` = qdr.`id_quantity_discount_rule` AND qdrm.`hook_name` = '".$hookName."')
                INNER JOIN `"._DB_PREFIX_."quantity_discount_rule_message_lang` qdrml
                    ON (qdrm.`id_quantity_discount_rule_message` = qdrml.`id_quantity_discount_rule_message` AND qdrml.`id_lang` = ".$idLang.")
                WHERE qdr.`active` = 1
                    AND qdr.`id_shop` = ".(int)Context::getContext()->shop->id
                    ." AND `id_family` = ".(int)$id_family."
                ORDER BY qdr.`priority` ASC, qdr.`id_quantity_discount_rule` ASC";

            $result = Db::getInstance()->ExecuteS($sql);

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    public static function getNbProductsOrder($id)
    {
        return Db::getInstance()->getValue(
            'SELECT SUM(`product_quantity`)
            FROM `'._DB_PREFIX_.'order_detail`
            WHERE `id_order` = '.(int)$id
        );
    }

    public function getMessagesToDisplay($hookName)
    {
        $messages = array();
        foreach ($this->getMessagesByHook($hookName) as $message) {
            $messages[] = $message;
        };

        return $messages;
    }

    public function getHighlightedQuantityDiscountRules()
    {
        // Error with shipping cost and cache when executing getOrderTotal()
        $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();

        if (in_array('getCustomerCartRules', array_column($backtrace, 'function'))
            && in_array('CartController', array_column($backtrace, 'class'))
        ) {
            return array();
        }

        $context = $this->context; //Don't replace with Context::getContext()

        // If no products, return. We don't do this before because we have to remove cart rules as PS does
        // Don't do this! This function is called from Discount FrontController
        /*if (!Validate::isLoadedObject($context->cart)) {
            return array();
        }

        if (!$context->cart->nbProducts()) {
            return array();
        }*/

        //Get all rules and check if any of them should be applied
        $quantityDiscountRulesHighlight = array();
        foreach (QuantityDiscountRuleFamily::getQuantityDiscountRuleFamilies(true, $context->cart->id_shop) as $ruleFamily) {
            $quantityDiscountRules = $this->getQuantityDiscountRulesByFamily($ruleFamily['id_quantity_discount_rule_family'], false, true);

            if (is_array($quantityDiscountRules) && count($quantityDiscountRules)) {
                foreach ($quantityDiscountRules as $quantityDiscountRule) {
                    $quantityDiscountRuleObj = new QuantityDiscountRule((int)$quantityDiscountRule['id_quantity_discount_rule']);

                    if (!$quantityDiscountRuleObj->isQuantityDiscountRuleValid(null, true)) {
                        continue;
                    }

                    if (!$quantityDiscountRuleObj->compatibleCartRules()) {
                        continue;
                    }

                    if (!$quantityDiscountRuleObj->validateQuantityDiscountRuleConditions(false)) {
                        continue;
                    }

                    $quantityDiscountRule['id_cart_rule'] = $this->getIdCartRuleFromQuantityDiscountRuleFromThisCart((int)$quantityDiscountRule['id_quantity_discount_rule'], $context->cart->id);
                    $quantityDiscountRulesHighlight[] = $quantityDiscountRule;
                }
            }
        }

        return $quantityDiscountRulesHighlight;
    }

    public function createAndRemoveRules($code = null, $context = null, $cache = true, $force = false)
    {
        /*if (self::$inExecution) {
            return;
        }
        self::$inExecution = true;*/

        /* Avoid recursion */
        $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();

        /* Don't modify rules if it's an order modification from the BO */
        if (in_array('updateProductAction', array_column($backtrace, 'function'))
            || ($this->array_search_partial(array_column($backtrace, 'class'), 'OrderAmountUpdater'))
            || ($this->array_search_partial(array_column($backtrace, 'class'), 'EditOrderAddressHandler'))) {
            return false;
        }

        if (!$force && in_array('AddToCartFromUrlCartModuleFrontController', array_column($backtrace, 'class'))) {
            //$cache = false;
            return false;
        }

        if (!$force && in_array('PBPFrontCartController', array_column($backtrace, 'class'))) {
            //$cache = false;
            return false;
        }

        $cache = true;
        if (in_array('Obs_quickorderQuickorderModuleFrontController', array_column($backtrace, 'class'))) {
            $cache = false;
        }

        if (in_array('SizzlePromoShowModuleFrontController', array_column($backtrace, 'class'))) {
            $cache = false;
        }

        if (in_array('HsMultiAccessoriesAbstract', array_column($backtrace, 'class'))) {
            $cache = false;
        }

        //attributewizardpro
        if (in_array('add2cart', array_column($backtrace, 'function'))) {
            $cache = false;
        }

        //quickorder
        if (in_array('PCPCombinationsToCart', array_column($backtrace, 'function'))) {
            $cache = false;
        }

        if (self::$executed && $cache) {
            return false;
        }

        if (!$context) {
            $context = Context::getContext();
        }

        if (!Validate::isLoadedObject($context->cart)) {
            return false;
        }

        if ((int)$this->getLock($context->cart->id) !== 1) {
            return false;
        }

        self::$_discountedProducts = array();

        /*** PERFORMANCE ***/
        /*
        $cache_key = 'QuantityDiscountRule::createAndRemoveRules_'.(int)$context->cart->id.'_'.(int)Cart::getNbProducts($context->cart->id).'_'.$context->cart->getOrderTotal().'_'.(int)$context->cart->id_address_delivery.'_'.(int)$context->cart->getNbOfPackages().'_'.$context->cart->getTotalWeight().'_'.md5(serialize($context->cart->getProducts()));

        if (Cache::isStored($cache_key)) {
            self::$inExecution = false;

            return false;
        }

        Cache::store($cache_key, true);
        */

        //if (!$this->array_search_partial(array_column($backtrace, 'file'), 'hsmultiaccessoriespro')) {
            if (in_array('removeQuantityDiscountCartRule', array_column($backtrace, 'function'))
                //|| in_array('loadCarrier', array_column($backtrace, 'function')) //supercheckout
                || in_array('loadCarriers', array_column($backtrace, 'function'))
                //|| in_array('updateCarrier', array_column($backtrace, 'function')) //supercheckout
                //|| in_array('_processCarrier', array_column($backtrace, 'function')) // onepagecheckout
                || in_array('_setCarrierSelection', array_column($backtrace, 'function'))
                || (in_array('getCartRules', array_column($backtrace, 'function')) && !Module::isInstalled('onepagecheckoutps')) //PS 1.7.4 - Don't uncomment this to avoid problems with OPC
                || in_array('OrderFees', array_column($backtrace, 'class'))
                || in_array('getShoppingCart', array_column($backtrace, 'function'))
                || in_array('redirectToCheckout', array_column($backtrace, 'function')) // Paypal integration
                || in_array('shippingBlock', array_column($backtrace, 'function')) // IdxrOpc
                || in_array('syncQuantity', array_column($backtrace, 'function')) // hsmultiaccessoriespro
                || in_array('AdvancedDateOfDelivery', array_column($backtrace, 'class')) // AdvancedDateOfDelivery module
                || in_array('calculateCartRule', array_column($backtrace, 'function')) // Onw recursive
                || in_array('getProductsWithSeparatedGifts', array_column($backtrace, 'function'))
                //|| (Tools::getValue('method') == 'updateCarrierAndGetPayments') // Executed when carrier is changed
                || (version_compare(_PS_VERSION_, '1.6', '<') && in_array('processCarrier', array_column($backtrace, 'function')))
            ) {
                //self::$inExecution = false;
                $this->releaseLock($context->cart->id);
                return false;
            }

            foreach (array('carriercompare', 'productadditionalfeatures', 'attributegrid', 'imaximprimepedidosservidor', 'appagebuilder', 'phcarrito', 'freedeliverymanager') as $modules_exception) {
                foreach (array_column($backtrace, 'file') as $element) {
                    if (strpos($element, $modules_exception) !== false) {
                        //self::$inExecution = false;
                        $this->releaseLock($context->cart->id);
                        return false;
                    }
                }
            }

            self::$executed = true;
        //}

        //Kept the discount codes to apply again the rule
        $cartRulesCodes = array();
        if ($code = Tools::strtoupper($code)) {
            $cartRulesCodes[] = $code;
        }

        //Remove all quantity discount rules from current cart
        $quantityDiscountRulesAtCart = self::getQuantityDiscountRulesAtCart((int)$context->cart->id);
        $cartRulesRemoved = false;
        if (is_array($quantityDiscountRulesAtCart) && count($quantityDiscountRulesAtCart)) {
            $cartRulesRemoved = true;
            foreach ($quantityDiscountRulesAtCart as $quantityDiscountRuleAtCart) {
                //We save the discount code to apply it after
                if ($quantityDiscountRuleAtCart['code']) {
                    $cartRulesCodes[] = $quantityDiscountRuleAtCart['code'];
                }
                self::removeQuantityDiscountCartRule($quantityDiscountRuleAtCart['id_cart_rule'], (int)$context->cart->id);
            }
        }
        $cartRulesCodes = array_unique($cartRulesCodes);

        /* If no products, return. We don't do this before because we have to remove cart rules as PS does */
        if (!$context->cart->nbProducts()) {
            //self::$inExecution = false;
            $this->releaseLock($context->cart->id);

            return false;
        }

        //Get all rules and check if any of them should be applied
        $cartRulesCreated = false;
        foreach (QuantityDiscountRuleFamily::getQuantityDiscountRuleFamilies(true, $context->cart->id_shop) as $ruleFamily) {
            $cartRulesCreatedSameFamily = false;
            $quantityDiscountRules = $this->getQuantityDiscountRulesByFamily($ruleFamily['id_quantity_discount_rule_family'], $cartRulesCodes, false, true);
            if (is_array($quantityDiscountRules) && count($quantityDiscountRules)) {
                foreach ($quantityDiscountRules as $quantityDiscountRule) {
                    $quantityDiscountRuleObj = new QuantityDiscountRule((int)$quantityDiscountRule['id_quantity_discount_rule']);

                    //If there a rule created and current rule is not compatible with others
                    if ($cartRulesCreated && !$quantityDiscountRuleObj->compatible_qdp_rules) {
                        continue;
                    }

                    if (!$quantityDiscountRuleObj->isQuantityDiscountRuleValid($cartRulesCodes)) {
                        continue;
                    }

                    if (!$quantityDiscountRuleObj->compatibleCartRules()) {
                        continue;
                    }

                    if (!$quantityDiscountRuleObj->validateQuantityDiscountRuleConditions()) {
                        continue;
                    }

                    if ($this->calculateCartRule($quantityDiscountRuleObj)) {
                        $cartRulesCreated = true;
                        $cartRulesCreatedSameFamily = true;
                        if (!$quantityDiscountRuleObj->execute_other_rules) {
                            break;
                        }
                    }
                }
            }

            if ($cartRulesCreatedSameFamily && !$ruleFamily['execute_other_families']) {
                break;
            }
        }

        if ($cartRulesRemoved || $cartRulesCreated) {
            //self::$inExecution = false;
            $this->releaseLock($context->cart->id);

            return true;
            //die(json_encode(array('refresh' => true)));
        }

        if ($code) {
            //self::$inExecution = false;
            $this->releaseLock($context->cart->id);

            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                return $this->trans('You cannot use this voucher', array(), 'Shop.Notifications.Error');
            } else {
                return Tools::displayError('You cannot use this voucher');
            }
        }

        //self::$inExecution = false;
        $this->releaseLock($context->cart->id);

        return false;
    }

    public function calculateCartRule($quantityDiscountRule, $cart = null)
    {
        $virtual_context = Context::getContext()->cloneContext();

        // We need to force cache because if another module has called this function with $fullInfos = false, it returns simple array
        if (!$cart) {
            $cart = $this->context->cart;
        }
        $cartProducts = $cart->getProducts(true, false, null, true);

        $differentVatInCart = false;
        if (count(array_unique(array_column($cartProducts, 'rate'))) > 1) {
            $differentVatInCart = true;
        }

        $taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);

        $actions = $quantityDiscountRule->getActions(true);

        /* initialize vars */
        /* As for some actions can be more than 1, it cannot be defined inside the foreach */

        $minCoincidences = PHP_INT_MAX;
        $tempCartRule = array();
        $actionsBuyX = array();
        $cartProductsFilteredBuyX = array();
        $this->context->cookie->qdp_shipping_cost = null;

        $reductionAmount = 0;
        $tempCartRuleCounter = 0;

        foreach ($actions as $action) {
            switch ((int)$action->id_type) {
                /**
                 *
                 * Shipping cost - Fixed discount
                 *
                 */
                case 1:
                    if (!$action->reduction_amount) {
                        continue 2;
                    }

                    // if there is a PS free shipping rule, don't apply it
                    /*$cartRules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
                    foreach ($cartRules as $cartRule) {
                        if ($cartRule['free_shipping']) {
                            continue 3;
                        }
                    }*/

                    $shippingCostsByCarrier = array();

                    if (Tools::getValue('delivery_option')) {
                        $cart->setDeliveryOption(Tools::getValue('delivery_option'));
                        //Flush cache
                        $this->clearCartCache();
                    }

                    // TODO -> Replace all getTotalShippingCost with this function
                    if (Module::isInstalled('onepagecheckoutps')) {
                        $opc = Module::getInstanceByName('onepagecheckoutps');
                        if (Validate::isLoadedObject($opc)) {
                            if ($opc->active
                                && isset($opc->core)
                                && $opc->core->isVisible()
                                && !$cart->id_customer
                                && $cart->id_address_delivery) {
                                $address = new Address($cart->id_address_delivery);
                                $shippingCost = $cart->getPackageShippingCost(null, (int)$action->reduction_tax, null, null, State::getIdZone( $address->id_state));
                            } else {
                                $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                            }
                        } else {
                            $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                        }
                    } else {
                        $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                    }

                    //Free shipping
                    /*if (!$shippingCost) {
                        continue 2;
                    }*/

                    $carrierSelected = false;
                    if ($action->filter_by_carrier) {
                        $restrictionCarriers = $action->getSelectedAssociatedRestrictions('carrier');
                        $carrier = new Carrier((int)$cart->id_carrier);
                        if (in_array((int)$carrier->id_reference, array_column($restrictionCarriers['selected'], 'id_carrier'))) {
                            $carrierSelected = true;
                        }
                    } else {
                        $carrierSelected = true;
                    }

                    if ($carrierSelected) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = 0;
                        $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 0;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                        if ($action->filter_by_carrier) {
                            $tempCartRule[$tempCartRuleCounter]['carrier_restriction'] = array_column($restrictionCarriers['selected'], 'id_carrier');
                        }

                        $reductionAmountConverted = self::convertPriceFull($action->reduction_amount, new Currency((int)$action->reduction_currency), $this->context->currency);

                        $orderTotalOnlyProducts = $cart->getOrderTotal((int)$action->reduction_tax, Cart::ONLY_PRODUCTS);
                        //Flush cache
                        $this->clearCartCache();

                        $orderTotalOnlyProducts -= $this->getGiftProductsValue((int)$action->reduction_tax);

                        if ($shippingCost >= $reductionAmountConverted) {
                            if ($orderTotalOnlyProducts >= $reductionAmountConverted) {
                                /*
                                $orderTotalOnlyProducts 100€
                                $shippingCost 10€
                                $reductionAmountConverted 5€
                                Discount = 5€
                                */
                                $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmountConverted;
                                continue 2;
                            } else {
                                /*
                                $orderTotalOnlyProducts 4€
                                $shippingCost 10€
                                $reductionAmountConverted 5€
                                Discount = 5€
                                */
                                $shippingCostsByCarrier[(int)$cart->id_carrier] = $reductionAmountConverted;
                                $this->context->cookie->qdp_shipping_cost = json_encode($shippingCostsByCarrier);
                                unset($tempCartRule[$tempCartRuleCounter]);
                                continue 2;
                            }
                        } else {
                            if ($orderTotalOnlyProducts >= $reductionAmountConverted) {
                                /*
                                $orderTotalOnlyProducts 100€
                                $shippingCost 3€
                                $reductionAmountConverted 5€
                                Discount = 3€
                                */
                                $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $shippingCost;
                                continue 2;
                            } else {
                                /*
                                $orderTotalOnlyProducts 2€
                                $shippingCost 3€
                                $reductionAmountConverted 5€
                                Discount = 3€
                                */
                                $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 1;
                                continue 2;
                            }
                        }
                    }

                    break;

                /**
                 *
                 * Shipping cost - Percentage discount
                 *
                 */
                case 5:

                    if (!$action->reduction_percent) {
                        continue 2;
                    }

                    // if there is a PS free shipping rule, don't apply it
                    /*$cartRules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
                    foreach ($cartRules as $cartRule) {
                        if ($cartRule['free_shipping']) {
                            continue 3;
                        }
                    }*/

                    $shippingCostsByCarrier = array();

                    if (Tools::getValue('delivery_option')) {
                        $cart->setDeliveryOption(Tools::getValue('delivery_option'));
                        //Flush cache
                        $this->clearCartCache();
                    }

                    // TODO -> Replace all getTotalShippingCost with this function
                    if (Module::isInstalled('onepagecheckoutps')) {
                        $opc = Module::getInstanceByName('onepagecheckoutps');
                        if (Validate::isLoadedObject($opc)) {
                            if ($opc->active
                                && isset($opc->core)
                                && $opc->core->isVisible()
                                && !$cart->id_customer
                                && $cart->id_address_delivery) {
                                $address = new Address($cart->id_address_delivery);
                                $shippingCost = $cart->getPackageShippingCost(null, (int)$action->reduction_tax, null, null, State::getIdZone( $address->id_state));
                            } else {
                                $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                            }
                        } else {
                            $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                        }
                    } else {
                        $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                    }

                    //Free shipping
                    /*if (!$shippingCost) {
                        continue 2;
                    }*/

                    $carrierSelected = false;
                    if ($action->filter_by_carrier) {
                        $restrictionCarriers = $action->getSelectedAssociatedRestrictions('carrier');
                        $carrier = new Carrier($this->getCarrier($this->context));
                        if (in_array($carrier->id_reference, array_column($restrictionCarriers['selected'], 'id_carrier'))) {
                            $carrierSelected = true;
                        }
                    } else {
                        $carrierSelected = true;
                    }

                    if ($carrierSelected) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = 0;
                        $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 0;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                        if ($action->filter_by_carrier) {
                            $tempCartRule[$tempCartRuleCounter]['carrier_restriction'] = array_column($restrictionCarriers['selected'], 'id_carrier');
                        }

                        $reductionMaxAmountConverted = self::convertPriceFull($action->reduction_max_amount, new Currency((int)$action->reduction_currency), $this->context->currency);
                        $reductionAmount = ($shippingCost*$action->reduction_percent)/100;
                        $reductionAmount = ($reductionMaxAmountConverted && $reductionAmount > $reductionMaxAmountConverted) ? $reductionMaxAmountConverted : $reductionAmount;

                        $orderTotalOnlyProducts = $cart->getOrderTotal((int)$action->reduction_tax, Cart::ONLY_PRODUCTS);
                        //Flush cache
                        $this->clearCartCache();

                        $orderTotalOnlyProducts -= $this->getGiftProductsValue((int)$action->reduction_tax);

                        if ($shippingCost == $reductionAmount) {
                            $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 1;
                            continue 2;
                        } else {
                            if ($orderTotalOnlyProducts >= $reductionAmount) {
                                /*
                                $orderTotalOnlyProducts 100€
                                $shippingCost 3€
                                $reductionAmountConverted 5€
                                Discount = 3€
                                */
                                $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmount;
                                continue 2;
                            } else {
                                /*
                                $orderTotalOnlyProducts 2€
                                $shippingCost 3€
                                $reductionAmountConverted 5€
                                Discount = 3€
                                */
                                $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $orderTotalOnlyProducts;
                                continue 2;
                            }
                        }
                    }

                    break;

                /**
                 *
                 * Order amount - Fixed discount
                 *
                 */
                case 2:
                    if (!$action->reduction_amount) {
                        continue 2;
                    }

                    $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                    $orderTotal = $cart->getOrderTotal((int)$action->reduction_tax, Cart::ONLY_PRODUCTS);
                    //Flush cache
                    $this->clearCartCache();

                    $orderTotal -= $this->getGiftProductsValue((int)$action->reduction_tax);

                    /**
                     *
                     * Check if shipping is included in the amount to discount,
                     * because is possible that the amount to discount is higher that the product amount,
                     * so we need to know if we have to reduce only products or products + shipping
                     *
                     */
                    if ((int)$action->reduction_shipping) {
                        if ($orderTotal < $action->reduction_amount && $shippingCost < $action->reduction_amount) {
                            $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 1;
                            $maxDiscount = $action->reduction_amount - $shippingCost;
                        } else {
                            $maxDiscount = $orderTotal;
                        }
                    } else {
                        $maxDiscount = $orderTotal;
                    }

                    if ($maxDiscount) {
                        $maxDiscount = self::convertPriceFull($maxDiscount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = ($maxDiscount > $action->reduction_amount) ? $action->reduction_amount : $maxDiscount;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Order amount - Percentage discount
                 *
                 */
                case 3:
                    $orderTotal = $cart->getOrderTotal((int)$action->reduction_tax, Cart::ONLY_PRODUCTS);
                    //Flush cache
                    $this->clearCartCache();

                    /**
                     *
                     * Check if shipping is included in the amount to discount,
                     * because is possible that the amount to discount is higher that the product amount,
                     * so we need to know if we have to reduce only products or products + shipping
                     *
                     */
                    if ((int)$action->reduction_percent_shipping && $action->reduction_percent == 100) {
                        $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 1;
                        $totalAmount = $orderTotal;
                    } else {
                        if ((int)$action->reduction_percent_shipping) {
                            $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_percent_tax);
                            $totalAmount = $orderTotal + $shippingCost;
                        } else {
                            $shippingCost = 0;
                            $totalAmount = $orderTotal;
                        }

                        /** Remove discounts */
                        if (!$action->reduction_percent_discount) {
                            $totalAmount -= $cart->getOrderTotal((int)$action->reduction_percent_tax, Cart::ONLY_DISCOUNTS);
                            //Flush cache
                            $this->clearCartCache();
                        } else {
                            $totalAmount -= $this->getGiftProductsValue((int)$action->reduction_percent_tax);
                        }

                        /*if ($totalAmount > $orderTotal) {
                            $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 1;
                            $totalAmount -= $shippingCost;
                        }*/
                    }

                    if ($totalAmount) {
                        $totalAmount = self::convertPriceFull(($totalAmount*$action->reduction_percent)/100, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                        $orderTotalConverted = self::convertPriceFull($orderTotal, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                        $shippingCostConverted = self::convertPriceFull($shippingCost, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                        if ($totalAmount > $orderTotalConverted) {
                            $tempCartRule[$tempCartRuleCounter]['free_shipping'] = 1;
                            $totalAmount -= $shippingCostConverted;
                        }

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $totalAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $totalAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Buy X - Get Y with fixed discount
                 *
                 */
                case 6:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each));

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo*$action->products_nb_each);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min(($remainingTimesToApplyPromo*$action->apply_discount_to_nb), (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                if ($productPrice > $action->reduction_amount) {
                                    $reductionAmount += $action->reduction_amount*$timesToApplyPromoInThisProduct;
                                } else {
                                    $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                }

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Buy X - Get Y with percentage discount
                 *
                 */
                case 7:
                    $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();

                    $cache = true;
                    if (in_array('Obs_quickorderQuickorderModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    if (in_array('SizzlePromoShowModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    //quickorder
                    if (in_array('PCPCombinationsToCart', array_column($backtrace, 'function'))) {
                        $cache = false;
                    }

                    //attributewizardpro
                    if (in_array('add2cart', array_column($backtrace, 'function'))) {
                        $cache = false;
                    }

                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action, $cache);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action, $cache)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each));

                            //$quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo*$action->products_nb_each);
                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], (int)($productGrouped['cart_quantity']/$action->products_nb_each)*$action->products_nb_each);

                            foreach ($productGrouped['products'] as $product) {
                                $unitDiscount = $this->getDiscountedAmount($action, $product, true);
                                //$quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $product, $remainingTimesToApplyPromo*$action->products_nb_each);

                                $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                                $reductionProductMaxAmountConverted = self::convertPriceFull($action->reduction_product_max_amount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $unitDiscount = (($reductionProductMaxAmountConverted > 0 && $unitDiscountConverted > $reductionProductMaxAmountConverted) ? $reductionProductMaxAmountConverted : $unitDiscountConverted);

                                $timesToApplyPromoInThisProduct = min(($remainingTimesToApplyPromo*$action->apply_discount_to_nb), (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                //$reductionAmount += $unitDiscount*$timesToApplyPromoInThisProduct;
                                /*if ($differentVatInCart) {*/
                                    $reductionAmount = $unitDiscount*$timesToApplyPromoInThisProduct;

                                    $reductionAmount = Tools::ps_round(
                                        $reductionAmount,
                                        _PS_PRICE_COMPUTE_PRECISION_
                                    );

                                    if ($reductionAmount > 0) {
                                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                                        $tempCartRule[$tempCartRuleCounter]['reduction_product'] = (int)$product['id_product'];
                                        $tempCartRuleCounter++;
                                    }
                                /*} else {
                                    $reductionAmount += $unitDiscount*$timesToApplyPromoInThisProduct;
                                }*/

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    /*if (!$differentVatInCart && $reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }*/

                    break;

                /**
                 *
                 * Buy X - Get Y with fixed price
                 *
                 */
                case 8:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each));

                            //$quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo*$action->products_nb_each);
                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], (int)($productGrouped['cart_quantity']/$action->products_nb_each)*$action->products_nb_each);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min(($remainingTimesToApplyPromo*$action->apply_discount_to_nb), (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $reductionAmount += ($productPrice-$action->reduction_amount)*$timesToApplyPromoInThisProduct;

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Buy X - Get Y - Gift product (by product)
                 *
                 */
                case 31:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $action->group_products_by = 'product';
                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each))*(int)$action->apply_discount_to_nb;

                            //$quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo*$action->products_nb_each);
                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], (int)($productGrouped['cart_quantity']/$action->products_nb_each)*$action->products_nb_each);

                            foreach ($productGrouped['products'] as $key => $product) {
                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;

                                $tempCartRule[$key]['gift_product'] = (int)$product['id_product'];
                                $tempCartRule[$key]['gift_product_attribute'] = (int)$product['id_product_attribute'];
                                $tempCartRule[$key]['duplicate_rule'] = $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    break;

                /**
                 *
                 * All products after X - Fixed discount
                 *
                 */
                case 12:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ((int)$productGrouped['cart_quantity'] > (int)$action->products_nb_each) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']-(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']-(int)$action->products_nb_each));

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $productGrouped['cart_quantity'] + $remainingTimesToApplyPromo);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                if ($productPrice > $action->reduction_amount) {
                                    $reductionAmount += $action->reduction_amount*$timesToApplyPromoInThisProduct;
                                } else {
                                    $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                }

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

               /**
                 *
                 * All products after X - Percentage discount
                 *
                 */
                case 13:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']-(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']-(int)$action->products_nb_each));

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $productGrouped['cart_quantity'] + $remainingTimesToApplyPromo);

                            foreach ($productGrouped['products'] as $product) {
                                $unitDiscount = $this->getDiscountedAmount($action, $product, true);

                                $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                                $reductionProductMaxAmountConverted = self::convertPriceFull($action->reduction_product_max_amount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $unitDiscount = (($reductionProductMaxAmountConverted > 0 && $unitDiscountConverted > $reductionProductMaxAmountConverted) ? $reductionProductMaxAmountConverted : $unitDiscountConverted);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $reductionAmount += $unitDiscount*$timesToApplyPromoInThisProduct;

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * All products after X - Fixed price
                 *
                 */
                case 14:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']-(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']-(int)$action->products_nb_each));

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $product, $timesToApplyPromoInThisProduct);

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $reductionAmount += ($productPrice-$action->reduction_amount)*$timesToApplyPromoInThisProduct;

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Each group of X - Fixed discount
                 *
                 */
                case 15:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each));

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                if ($productPrice > $action->reduction_amount) {
                                    $reductionAmount += $action->reduction_amount*$timesToApplyPromoInThisProduct;
                                } else {
                                    $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                }

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Each group of X - Percentage discount
                 *
                 */
                case 16:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }
                    //error_log('productsGrouped : '.print_r($productsGrouped, true));

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ((int)$productGrouped['cart_quantity'] >= (int)$action->products_nb_each) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each))*(int)$action->products_nb_each;

                            $groupPrice = 0;
                            $groupAggregate = 0;

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);
                            
                           

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, true);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $groupAggregate += $timesToApplyPromoInThisProduct;
                                $groupPrice += $productPrice*$timesToApplyPromoInThisProduct;
                                
                                /********************* */
                                $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && ($productPrice*$timesToApplyPromoInThisProduct) > $action->reduction_max_amount) ? $action->reduction_max_amount : ($productPrice*$timesToApplyPromoInThisProduct));
                                $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                                $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                                $tempCartRule[$tempCartRuleCounter]['reduction_product'] = (int)$product['id_product'];
                                $tempCartRuleCounter++;
                                /********************* */

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }

                            $reductionAmount += $groupPrice;
                        }
                    }
                    //error_log('DISC : '.print_r($tempCartRule, true));

                    /*if ($reductionAmount > 0) {
                        $reductionAmount = self::convertPriceFull($reductionAmount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }*/

                    break;

                /**
                 *
                 * Each group of X - Fixed price
                 *
                 */
                case 17:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ((int)$productGrouped['cart_quantity'] >= (int)$action->products_nb_each) {
                            $timesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)($productGrouped['cart_quantity']/(int)$action->products_nb_each), (int)$action->nb_repetitions_custom) : (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each));

                            $remainingTimesToApplyPromo = $timesToApplyPromo*(int)$action->products_nb_each;

                            $groupPrice = 0;
                            $groupAggregate = 0;

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $groupAggregate += $timesToApplyPromoInThisProduct;
                                $groupPrice += $productPrice*$timesToApplyPromoInThisProduct;

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }

                            $groupPrice = self::convertPriceFull($groupPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                            $reductionAmount += $groupPrice-($action->reduction_amount)*$timesToApplyPromo;
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Each X-th after Y - Fixed discount
                 *
                 */
                case 18:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ((int)$productGrouped['cart_quantity'] >= (int)$action->products_nb_each + (int)$action->apply_discount_to_nb) {
                            $remainingTimesToApplyPromo = (($action->nb_repetitions == 'custom') ? min((int)((int)$productGrouped['cart_quantity']-(int)$action->apply_discount_to_nb)/(int)$action->products_nb_each, (int)$action->nb_repetitions_custom) : (int)((int)$productGrouped['cart_quantity']-(int)$action->apply_discount_to_nb)/(int)$action->products_nb_each);

                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo*$action->products_nb_each);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, $product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                if ($productPrice > $action->reduction_amount) {
                                    $reductionAmount += $action->reduction_amount*$timesToApplyPromoInThisProduct;
                                } else {
                                    $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                }

                                $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                if ($remainingTimesToApplyPromo <= 0) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmount;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Each X-th after Y - Percentage discount
                 *
                 */
                case 19:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    $previousMod = 0;

                    foreach ($productsGrouped as $productGrouped) {
                        $nbRepetitions = 0;
                        $productGroup = $productGrouped['products'];

                        switch ($action->nb_repetitions) {
                            case 'infinite':
                                while (count($productGrouped['products'])) {
                                    $product = array_shift($productGrouped['products']);

                                    $mod = (int)(($product['cart_quantity'] + $previousMod) % (int)$action->products_nb_each);

                                    if (($product['cart_quantity'] + $previousMod) >= (int)$action->products_nb_each) {
                                        $productPrice = $this->getDiscountedAmount($action, $product, true);

                                        //Check if computed price product is higher than fixed price, if not don't do anything
                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                        $reductionAmount += $productPrice*(int)(($product['cart_quantity']+$previousMod)/(int)$action->products_nb_each);

                                        $nbRepetitions++;
                                    }

                                    $previousMod = $mod;
                                }

                                break;
                            case 'custom':
                                $i = (int)$action->nb_repetitions_custom;
                                while (count($productGrouped['products']) && $i > 0) {
                                    $product = array_shift($productGrouped['products']);

                                    $mod = (int)($product['cart_quantity'] % (int)$action->products_nb_each);
                                    if (($product['cart_quantity'] + $previousMod) >= (int)$action->products_nb_each) {
                                        $productPrice = $this->getDiscountedAmount($action, $product, true);
                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                        $reductionAmount += $productPrice*min((int)(($product['cart_quantity']+$previousMod)/(int)$action->products_nb_each), $i);

                                        $i = $i - (int)(($product['cart_quantity']+$previousMod)/(int)$action->products_nb_each);

                                        $nbRepetitions++;
                                    }

                                    $previousMod = $mod;
                                }

                                break;
                        }

                        $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGroup, $nbRepetitions*$action->products_nb_each);
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Each X-th after Y - Fixed price
                 *
                 */
                case 20:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    $previousMod = 0;

                    foreach ($productsGrouped as $productGrouped) {
                        $nbRepetitions = 0;
                        $productGroup = $productGrouped['products'];

                        switch ($action->nb_repetitions) {
                            case 'infinite':
                                while (count($productGrouped['products'])) {
                                    $product = array_shift($productGrouped['products']);
                                    $mod = (int)(($product['cart_quantity'] + $previousMod) % (int)$action->products_nb_each);

                                    if (($product['cart_quantity'] + $previousMod) >= (int)$action->products_nb_each) {
                                        //Check if computed price product is higher than fixed price, if not don't do anything
                                        $productPrice = $this->getDiscountedAmount($action, $product, false);
                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                                        if ($productPrice - $action->reduction_amount > 0) {
                                            $reductionAmount += ($productPrice - $action->reduction_amount)*(int)(($product['cart_quantity']+$previousMod)/(int)$action->products_nb_each);
                                        }

                                        $nbRepetitions++;
                                    }

                                    $previousMod = $mod;
                                }
                                break;
                            case 'custom':
                                $i = (int)$action->nb_repetitions_custom;
                                while (count($productGrouped['products']) && $i > 0) {
                                    $product = array_shift($productGrouped['products']);
                                    $productPrice = $this->getDiscountedAmount($action, $product, false);

                                    $mod = (int)($product['cart_quantity'] % (int)$action->products_nb_each);

                                    if (($product['cart_quantity'] + $previousMod) >= (int)$action->products_nb_each) {
                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                                        if ($productPrice - $action->reduction_amount > 0) {
                                            $reductionAmount += ($productPrice - $action->reduction_amount)*min((int)(($product['cart_quantity']+$previousMod)/(int)$action->products_nb_each), $i);
                                            $i = $i - (int)(($product['cart_quantity']+$previousMod)/(int)$action->products_nb_each);
                                        }

                                        $nbRepetitions++;
                                    }

                                    $previousMod = $mod;
                                }
                                break;
                        }

                        $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGroup, $nbRepetitions*$action->products_nb_each);
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmount;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Each X spent (over Z) - Get Y (fixed discount)
                 *
                 */
                case 21:
                    $originalProducts = count($cartProducts);
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);
                    $filteredProducts = count($cartProductsFiltered);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $totalAmount = 0;

                    //Get products amount
                    foreach ($cartProductsFiltered as $product) {
                        if ((int)$action->apply_discount_to_special || !Product::getPriceStatic($product['id_product'], (int)$action->reduction_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, true, true, $product['cart_quantity'], false, (int)$cart->id_customer, (int)$cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_tax, true, $virtual_context, true, $product['id_customization']) > 0) {

                            $timesToApplyPromoInThisProduct = min((int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                            $productPrice = $this->getDiscountedAmount($action, $product, false);

                            //TODO check round in all places
                            $totalAmount += Tools::ps_round(
                                $productPrice*$timesToApplyPromoInThisProduct,
                                version_compare(_PS_VERSION_, '1.7.7', '>=') ? Context::getContext()->getComputingPrecision() : _PS_PRICE_COMPUTE_PRECISION_
                            );
                        }
                    }

                    //Remove discounts only if there isn't a product filter, as we can not know if a discount is for specific products
                    if ($originalProducts == $filteredProducts) {
                        $totalAmount -= $cart->getOrderTotal((int)$action->reduction_tax, Cart::ONLY_DISCOUNTS);
                        //Flush cache
                        $this->clearCartCache();
                    } else {
                        $totalAmount -= $this->getGiftProductsValue((int)$action->reduction_tax);
                    }

                    //Add shipping cost
                    if ((int)$action->reduction_shipping) {
                        $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                        $totalAmount += $shippingCost;
                    }

                    $totalAmount = self::convertPriceFull($totalAmount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                    //Subtract amount over
                    $totalAmount -= $action->reduction_buy_over;
                    $timesToApplyPromo = (int)($totalAmount/$action->reduction_amount);

                    if ($timesToApplyPromo > 0) {
                        $reductionAmount = $action->reduction_buy_amount * $timesToApplyPromo;

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_buy_amount_tax;
                    }

                    break;

                /**
                 *
                 * Each X spent (over Z) - Get free gift
                 *
                 */
                case 35:
                    $originalProducts = count($cartProducts);
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);
                    $filteredProducts = count($cartProductsFiltered);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $totalAmount = 0;

                    //Get products amount
                    foreach ($cartProductsFiltered as $product) {
                        if ((int)$action->apply_discount_to_special || !Product::getPriceStatic($product['id_product'], (int)$action->reduction_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, true, true, $product['cart_quantity'], false, (int)$cart->id_customer, (int)$cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_tax, true, $virtual_context, true, $product['id_customization']) > 0) {
                            $productPrice = $this->getDiscountedAmount($action, $product, false);

                            $totalAmount += $productPrice*$product['cart_quantity'];
                        }
                    }

                    //Remove discounts only if there isn't a product filter, as we can not know if a discount is for specific products
                    if ($originalProducts == $filteredProducts) {
                        $totalAmount -= $cart->getOrderTotal((int)$action->reduction_tax, Cart::ONLY_DISCOUNTS);
                        //Flush cache
                        $this->clearCartCache();
                    } else {
                        $totalAmount -= $this->getGiftProductsValue((int)$action->reduction_tax);
                    }

                    //Add shipping cost
                    if ((int)$action->reduction_shipping) {
                        $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_tax);
                        $totalAmount += $shippingCost;
                    }

                    $totalAmount = self::convertPriceFull($totalAmount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                    //Subtract amount over
                    $totalAmount -= $action->reduction_buy_over;

                    if ($totalAmount > 0) {
                        $timesToApplyPromo = (int)($totalAmount/$action->reduction_amount);

                        $tempCartRule[$tempCartRuleCounter]['gift_product'] = (int)$action->gift_product;
                        $tempCartRule[$tempCartRuleCounter]['gift_product_attribute'] = (int)$action->gift_product_attribute;
                        $tempCartRule[$tempCartRuleCounter]['duplicate_rule'] = (int)$timesToApplyPromo;
                    }

                    break;

                /**
                 *
                 * X spent (over Z) Get Y - Percentage discount
                 *
                 */
                case 26:
                    $originalProducts = count($cartProducts);
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);
                    $filteredProducts = count($cartProductsFiltered);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $totalAmount = 0;
                    //Get products amount
                    foreach ($cartProductsFiltered as $product) {
                        if ((int)$action->apply_discount_to_special || !Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, true, true, $product['cart_quantity'], false, (int)$cart->id_customer, (int)$cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, true, $virtual_context, true, $product['id_customization']) > 0) {
                            $productPrice = $this->getDiscountedAmount($action, $product, false);

                            $totalAmount += $productPrice*$product['cart_quantity'];
                        }
                    }

                    //Remove discounts only if there isn't a product filter, as we can not know if a discount is for specific products
                    if ($originalProducts == $filteredProducts) {
                        $totalAmount -= $cart->getOrderTotal((int)$action->reduction_percent_tax, Cart::ONLY_DISCOUNTS);
                        //Flush cache
                        $this->clearCartCache();
                    } else {
                        $totalAmount -= $this->getGiftProductsValue((int)$action->reduction_percent_tax);
                    }

                    //Add shipping cost
                    if ((int)$action->reduction_shipping) {
                        $shippingCost = $cart->getTotalShippingCost(null, (int)$action->reduction_percent_tax);
                        $totalAmount += $shippingCost;
                    }

                    $totalAmount = self::convertPriceFull($totalAmount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                    //Subtract amount over
                    $totalAmount -= $action->reduction_buy_over;

                    if ($totalAmount > 0) {
                        $reductionAmount = $totalAmount*($action->reduction_percent/100);

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Buy X
                 *
                 */
                case 22:
                case 23:
                    // TODO
                    // set cache to static and clear in createAndRemoveRules()
                    $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
                    $cache = true;
                    if (in_array('Obs_quickorderQuickorderModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    if (in_array('SizzlePromoShowModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    //quickorder
                    if (in_array('PCPCombinationsToCart', array_column($backtrace, 'function'))) {
                        $cache = false;
                    }

                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action, $cache);
                    if (!$cartProductsFiltered) {
                        $minCoincidences = 0;
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action, $cache)) {
                        $minCoincidences = 0;
                        continue 2;
                    }

                    $actionsBuyX[$action->id_quantity_discount_rule_action] = $action;
                    $cartProductsFilteredBuyX[$action->id_quantity_discount_rule_action] = $cartProductsFiltered;

                    $minCoincidencesGroup = 0;
                    foreach ($productsGrouped as $productGrouped) {
                        $minCoincidencesGroup += (int)($productGrouped['cart_quantity']/(int)$action->products_nb_each);
                    }

                    $minCoincidences = min($minCoincidences, $minCoincidencesGroup);

                    break;

                /**
                 *
                 * Product discount - Fixed discount
                 *
                 */
                case 27:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $action->group_products_by = 'all';
                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        $remainingTimesToApplyPromo = (int)$action->products_nb_each;
                        $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                        foreach ($productGrouped['products'] as $product) {
                            $productPrice = $this->getDiscountedAmount($action, $product, false);

                            $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                            $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                            if ($productPrice > $action->reduction_amount) {
                                $reductionAmount += $action->reduction_amount*$timesToApplyPromoInThisProduct;
                            } else {
                                $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                            }

                            $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                            if ($remainingTimesToApplyPromo <= 0) {
                                break;
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

               /**
                 *
                 * Product discount - Percentage discount
                 *
                 */
                case 28:
                    $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
                    $cache = true;

                    if (in_array('add2cart', array_column($backtrace, 'function'))) { //attributewizardpro
                        $cache = false;
                    }

                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action, $cache);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $action->group_products_by = 'all';
                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        $remainingTimesToApplyPromo = (int)$action->products_nb_each;
                        $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                        foreach ($productGrouped['products'] as $product) {
                            $unitDiscount = $this->getDiscountedAmount($action, $product, true);

                            $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                            $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                            $reductionProductMaxAmountConverted = self::convertPriceFull($action->reduction_product_max_amount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                            $unitDiscount = (($reductionProductMaxAmountConverted > 0 && $unitDiscountConverted > $reductionProductMaxAmountConverted) ? $reductionProductMaxAmountConverted : $unitDiscountConverted);

                            /*$unitDiscount = Tools::ps_round(
                                $unitDiscount,
                                version_compare(_PS_VERSION_, '1.7.7', '>=') ? Context::getContext()->getComputingPrecision() : _PS_PRICE_COMPUTE_PRECISION_
                            );*/

                            if ($differentVatInCart) {
                                $reductionAmount = $unitDiscount*$timesToApplyPromoInThisProduct;

                                $reductionAmount = Tools::ps_round(
                                    $reductionAmount,
                                    _PS_PRICE_COMPUTE_PRECISION_
                                );

                                if ($reductionAmount > 0) {
                                    $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                                    $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                                    $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                                    $tempCartRule[$tempCartRuleCounter]['reduction_product'] = (int)$product['id_product'];
                                    $tempCartRuleCounter++;
                                }
                            } else {
                                $reductionAmount += $unitDiscount*$timesToApplyPromoInThisProduct;
                            }

                            $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                            if ($remainingTimesToApplyPromo <= 0) {
                                break;
                            }
                        }
                    }

                    /* TODO - Round all prices at the end */
                    $reductionAmount = Tools::ps_round(
                        $reductionAmount,
                        _PS_PRICE_COMPUTE_PRECISION_
                    );

                    if (!$differentVatInCart && $reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Product discount - Fixed price
                 *
                 */
                case 29:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $action->group_products_by = 'all';
                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        $remainingTimesToApplyPromo = (int)$action->products_nb_each;
                        $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                        foreach ($productGrouped['products'] as $product) {
                            $productPrice = $this->getDiscountedAmount($action, $product, false);

                            $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                            $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                            $reductionAmount += ($productPrice - $action->reduction_amount) * $timesToApplyPromoInThisProduct;

                            $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                            if ($remainingTimesToApplyPromo <= 0) {
                                break;
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Gift a product
                 *
                 */
                case 30:
                    $tempCartRule[$tempCartRuleCounter]['gift_product'] = (int)$action->gift_product;
                    $tempCartRule[$tempCartRuleCounter]['gift_product_attribute'] = (int)$action->gift_product_attribute;
                    $tempCartRule[$tempCartRuleCounter]['duplicate_rule'] = (int)$action->apply_discount_to_nb;

                    break;

                /**
                 *
                 * Buy more than X units and get discount in all units (quantity discount) - Fixed discount
                 *
                 */
                case 32:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], (int)$productGrouped['cart_quantity']);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);
                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                if ($productPrice > $action->reduction_amount) {
                                    $reductionAmount += $action->reduction_amount*(int)$product['cart_quantity'];
                                } else {
                                    $reductionAmount += $productPrice*(int)$product['cart_quantity'];
                                }
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Buy more than X units and get discount in all units (quantity discount) - Percentage discount
                 *
                 */
                case 33:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], (int)$productGrouped['cart_quantity']);

                            foreach ($productGrouped['products'] as $product) {
                                $unitDiscount = $this->getDiscountedAmount($action, $product, true);

                                $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency), false);
                                $reductionProductMaxAmountConverted = self::convertPriceFull($action->reduction_product_max_amount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $unitDiscount = (($reductionProductMaxAmountConverted > 0 && $unitDiscountConverted > $reductionProductMaxAmountConverted) ? $reductionProductMaxAmountConverted : $unitDiscountConverted);

                                $reductionAmount += $unitDiscount*(int)$product['cart_quantity'];
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Buy more than X units and get discount in all units (quantity discount) - Fixed price
                 *
                 */
                case 34:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->products_nb_each)) {
                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], (int)$productGrouped['cart_quantity']);

                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);

                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $reductionAmount += ($productPrice-$action->reduction_amount)*(int)$product['cart_quantity'];
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                //Add a product with fixed price
                case 36:
                    if ($cart->updateQty((int)$action->apply_discount_to_nb, (int)$action->gift_product, (int)$action->gift_product_attribute, false, 'up', 0, null, false)) {
                        $cartProducts = $cart->getProducts();

                        Db::getInstance()->insert('quantity_discount_rule_cart_product', array(
                            'id_cart' => (int)$cart->id,
                            'id_quantity_discount_rule' => (int)$quantityDiscountRule->id,
                            'id_product' => (int)$action->gift_product,
                            'id_product_attribute' => (int)$action->gift_product_attribute,
                            'quantity' => (int)$action->apply_discount_to_nb,
                        ));

                        //Find product in cart
                        $key = $this->multiArraySearch($cartProducts, array('id_product' => (int)$action->gift_product, 'id_product_attribute' => (int)$action->gift_product_attribute));

                        $productPrice = $this->getDiscountedAmount($action, $cartProducts[$key], false);
                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = ($productPrice - $action->reduction_amount) * (int)$action->apply_discount_to_nb;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                        $tempCartRule[$tempCartRuleCounter]['id_action_type'] = 1;
                    }

                    break;

                /**
                 *
                 * Product discount - Fixed discount
                 *
                 */
                case 37:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if (!$this->compareValue(0, $productGrouped['price_with_reduction'], $action->spent_amount_from)
                            || !$this->compareValue(2, $productGrouped['price_with_reduction'], $action->spent_amount_to)) {
                            $reductionAmount += $action->reduction_amount*$productGrouped['cart_quantity'];
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                case 38:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, $productGrouped['price_with_reduction'], $action->spent_amount_from)
                            && $this->compareValue(2, $productGrouped['price_with_reduction'], $action->spent_amount_to)) {
                            $reductionAmount += $productGrouped['price_with_reduction']*($action->reduction_percent/100);
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                case 39:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);
                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as $productGrouped) {
                        if ($this->compareValue(0, $productGrouped['price_with_reduction'], $action->spent_amount_from)
                            && $this->compareValue(2, $productGrouped['price_with_reduction'], $action->spent_amount_to)) {
                            foreach ($productGrouped['products'] as $product) {
                                $productPrice = $this->getDiscountedAmount($action, $product, false);
                                $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                $reductionAmount += ($productPrice - $action->reduction_amount) * (int)$product['cart_quantity'];
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                case 40:
                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action);

                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    $action->group_products_by = 'all';
                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    foreach ($productsGrouped as &$productGrouped) {
                        //uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', SORT_DESC)));
                        unset($productGrouped);
                    }

                    $amount = 0;
                    $nbItems = 0;

                    foreach ($productsGrouped as $productGrouped) {
                        foreach ($productGrouped['products'] as $product) {
                            $unitDiscount = $this->getDiscountedAmount($action, $product, false);
                            $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                            $remainingAmount = ($action->reduction_amount > 0) ? (int)(($action->reduction_amount-$amount)/$unitDiscountConverted) : PHP_INT_MAX;
                            $timesToApplyPromoInThisProduct = min((int)$product['cart_quantity'], ($action->products_nb_each-$nbItems), $remainingAmount);

                            if ($timesToApplyPromoInThisProduct) {
                                $tempCartRule[$tempCartRuleCounter]['gift_product'] = (int)$product['id_product'];
                                $tempCartRule[$tempCartRuleCounter]['gift_product_attribute'] = (int)$product['id_product_attribute'];
                                $tempCartRule[$tempCartRuleCounter]['duplicate_rule'] = (int)$timesToApplyPromoInThisProduct;
                                $tempCartRule[$tempCartRuleCounter]['id_action_type'] = 2;
                                $tempCartRuleCounter++;

                                // We remove the product, it will be added later as a gift
                                $cart->updateQty($timesToApplyPromoInThisProduct, (int)$product['id_product'], (int)$product['id_product_attribute'], false, 'down', 0, null, false);

                                Db::getInstance()->insert('quantity_discount_rule_cart_product', array(
                                    'id_cart' => (int)$cart->id,
                                    'id_quantity_discount_rule' => (int)$quantityDiscountRule->id,
                                    'id_product' => (int)$product['id_product'],
                                    'id_product_attribute' => (int)$product['id_product_attribute'],
                                    'quantity' => (int)$timesToApplyPromoInThisProduct,
                                ));

                                $amount += $unitDiscountConverted*$timesToApplyPromoInThisProduct;
                                $nbItems += $timesToApplyPromoInThisProduct;
                            }
                        }
                    }

                    break;

                /**
                 *
                 * Get a discount on A - Fixed discount
                 *
                 */
                case 100:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    if ($action->group_products_by == 'product') {
                        $action->group_products_by = 'all';
                    }

                    if (!$cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action)) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action)) {
                        continue 2;
                    }

                    $productsGroupedBuyX = array();
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        $productsGroupedBuyX[$actionId] = $this->groupProducts($cart->id, $cartProductsFilteredBuyX[$actionId], $actionsBuyX[$actionId]);
                    }

                    //CAUTION! We have to sort products from action Buy X the other way, to start removing the ones that should be discounted later
                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    foreach (array_keys($actionsBuyX) as $actionId) {
                        uasort($cartProductsFilteredBuyX[$actionId], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_DESC : SORT_ASC)));
                    }

                    //Para cada accion de Buy X
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $productsRemoved = array();
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            //Borra cantidad X en productos de X
                            if (is_array($productsGroupedBuyX[$actionId])) {
                                foreach ($productsGroupedBuyX[$actionId] as $key => &$productGroupedBuyX) {
                                    if (is_array($productGroupedBuyX['products'])) {
                                        foreach ($productGroupedBuyX['products'] as $key2 => &$productBuyX) {
                                            $unitsToRemove = min($productBuyX['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                            $unitsRemoved += $unitsToRemove;

                                            $productsRemoved[$key2] = $unitsToRemove;

                                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGroupedBuyX['products'], $unitsToRemove);

                                            if ($productBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productGroupedBuyX['products'][$key2]);
                                            } else {
                                                $productBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($productGroupedBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productsGroupedBuyX[$actionId][$key]);
                                            } else {
                                                $productGroupedBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                                break 2;
                                            }
                                        }
                                        unset($productBuyX);
                                    }
                                }
                                unset($productGroupedBuyX);
                            }
                        }

                        //Borra cantidad X en productos de Y
                        if ($unitsRemoved) {
                            foreach ($productsGrouped as &$productGrouped) {
                                foreach ($productGrouped['products'] as $key2 => &$product) {
                                    if (in_array($key2, array_keys($productsRemoved))) {
                                        $product['cart_quantity'] -= $productsRemoved[$key2];
                                        $productGrouped['cart_quantity'] -= $productsRemoved[$key2];
                                    }
                                }
                                unset($product);
                            }
                            unset($productGrouped);

                            foreach ($productsGrouped as &$productGrouped) {
                                if ((int)$productGrouped['cart_quantity'] >= (int)$action->apply_discount_to_nb) {
                                    $remainingTimesToApplyPromo = min((int)$productGrouped['cart_quantity'], (int)$action->apply_discount_to_nb);
                                    $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                                    foreach ($productGrouped['products'] as &$product) {
                                        $productPrice = $this->getDiscountedAmount($action, $product, false);

                                        $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                        if ($productPrice > $action->reduction_amount) {
                                            $reductionAmount += $action->reduction_amount*$timesToApplyPromoInThisProduct;
                                        } else {
                                            $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                        }

                                        $product['cart_quantity'] -= $timesToApplyPromoInThisProduct;
                                        $productGrouped['cart_quantity'] -= $timesToApplyPromoInThisProduct;

                                        $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                        if ($remainingTimesToApplyPromo <= 0) {
                                            break 2;
                                        }
                                    }
                                }
                            }
                            unset($productGrouped);
                        }
                    }

                    if ($reductionAmount > 0) {
                        if (isset($tempCartRule[$tempCartRuleCounter]['reduction_amount'])) {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] += $reductionAmount;
                        } else {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmount;
                        }
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Get a discount on A - Percentage discount
                 *
                 */
                case 101:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    if ($action->group_products_by == 'product') {
                        $action->group_products_by = 'all';
                    }

                    $cache = true;
                    if (in_array('Obs_quickorderQuickorderModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    if (in_array('SizzlePromoShowModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    //quickorder
                    if (in_array('PCPCombinationsToCart', array_column($backtrace, 'function'))) {
                        $cache = false;
                    }

                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action, $cache);
                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action, $cache)) {
                        continue 2;
                    }

                    $productsGroupedBuyX = array();
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        $productsGroupedBuyX[$actionId] = $this->groupProducts($cart->id, $cartProductsFilteredBuyX[$actionId], $actionsBuyX[$actionId]);
                    }

                    //CAUTION! We have to sort products from action Buy X the other way, to start removing the ones that should be discounted later
                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                    }
                    unset($productGrouped);

                    foreach (array_keys($actionsBuyX) as $actionId) {
                        uasort($cartProductsFilteredBuyX[$actionId], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_DESC : SORT_ASC)));
                    }

                    $reductionAmount = 0;

                    //Para cada accion de Buy X
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $productsRemoved = array();
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            //Borra cantidad X en productos de X
                            if (is_array($productsGroupedBuyX[$actionId])) {
                                foreach ($productsGroupedBuyX[$actionId] as $productGroupedBuyXKey => &$productGroupedBuyX) {
                                    if (is_array($productGroupedBuyX['products'])) {
                                        foreach ($productGroupedBuyX['products'] as $productBuyXKey => &$productBuyX) {
                                            $unitsToRemove = min($productBuyX['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                            $unitsRemoved += $unitsToRemove;

                                            $productsRemoved[$productBuyXKey] = $unitsToRemove;

                                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGroupedBuyX['products'], $unitsToRemove);

                                            if ($productBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productGroupedBuyX['products'][$productBuyXKey]);
                                            } else {
                                                $productBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($productGroupedBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productsGroupedBuyX[$actionId][$productGroupedBuyXKey]);
                                            } else {
                                                $productGroupedBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                                break 2;
                                            }
                                        }
                                        unset($productBuyX);
                                    }
                                }
                                unset($productGroupedBuyX);
                            }
                        }

                        //Borra cantidad X en productos de Y
                        if ($unitsRemoved) {
                            foreach ($productsGrouped as &$productGrouped) {
                                foreach ($productGrouped['products'] as $key2 => &$product) {
                                    if (in_array($key2, array_keys($productsRemoved))) {
                                        $product['cart_quantity'] -= $productsRemoved[$key2];
                                        $productGrouped['cart_quantity'] -= $productsRemoved[$key2];
                                    }
                                }
                                unset($product);
                            }
                            unset($productGrouped);

                            $remainingTimesToApplyPromo = (int)$action->apply_discount_to_nb;
                            $productsGrouped = array_reverse($productsGrouped);
                            foreach ($productsGrouped as &$productGrouped) {
                                if ((int)$productGrouped['cart_quantity'] >= (int)$action->apply_discount_to_nb) {
                                    $remainingTimesToApplyPromo = min($remainingTimesToApplyPromo, (int)$productGrouped['cart_quantity']);
                                    $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                                    foreach ($productGrouped['products'] as $key => &$product) {
                                        $productPrice = $this->getDiscountedAmount($action, $product, true);

                                        $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                        //$reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                        if ($differentVatInCart) {
                                            $reductionAmount = $productPrice*$timesToApplyPromoInThisProduct;

                                            $reductionAmount = Tools::ps_round(
                                                $reductionAmount,
                                                _PS_PRICE_COMPUTE_PRECISION_
                                            );

                                            if ($reductionAmount > 0) {
                                                $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                                                $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                                                $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                                                $tempCartRule[$tempCartRuleCounter]['reduction_product'] = (int)$product['id_product'];
                                                $tempCartRuleCounter++;
                                            }
                                        } else {
                                            $reductionAmount += $productPrice*$timesToApplyPromoInThisProduct;
                                        }

                                        if ($product['cart_quantity'] == $timesToApplyPromoInThisProduct) {
                                            unset($productGrouped['products'][$key]);
                                        } else {
                                            $product['cart_quantity'] -= $timesToApplyPromoInThisProduct;
                                        }

                                        $productGrouped['cart_quantity'] -= $timesToApplyPromoInThisProduct;

                                        foreach ($productsGroupedBuyX as $aKey => &$productGroupedBuyX) {
                                            foreach ($productGroupedBuyX as $aKey2 => $value) {
                                                foreach ($value['products'] as $aKey3 => &$value2) {
                                                    if ($key == $aKey3) {
                                                        if ($value2['cart_quantity'] == $timesToApplyPromoInThisProduct) {
                                                            unset($productGroupedBuyX[$aKey2]['products'][$aKey3]);
                                                        } else {
                                                            $value2['cart_quantity'] -= $timesToApplyPromoInThisProduct;
                                                        }
                                                    }
                                                }
                                                unset($value2);
                                            }
                                        }
                                        unset($productGroupedBuyX);

                                        $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                        if ($remainingTimesToApplyPromo <= 0) {
                                            break 2;
                                        }
                                    }
                                    unset($product);
                                }
                            }
                            unset($productGrouped);
                        }
                    }

                    if (!$differentVatInCart && $reductionAmount > 0) {
                        $reductionMaxAmountConverted = self::convertPriceFull($action->reduction_max_amount, $this->context->currency, new Currency((int)$action->reduction_max_currency));
                        if (isset($tempCartRule[$tempCartRuleCounter]['reduction_amount'])) {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] += (($reductionMaxAmountConverted > 0 && $reductionAmount > $reductionMaxAmountConverted) ? $reductionMaxAmountConverted : $reductionAmount);
                        } else {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($reductionMaxAmountConverted > 0 && $reductionAmount > $reductionMaxAmountConverted) ? $reductionMaxAmountConverted : $reductionAmount);
                        }
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Get a discount on A - Fixed price
                 *
                 */
                case 102:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    if ($action->group_products_by == 'product') {
                        $action->group_products_by = 'all';
                    }

                    $cache = true;
                    if (in_array('Obs_quickorderQuickorderModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    if (in_array('SizzlePromoShowModuleFrontController', array_column($backtrace, 'class'))) {
                        $cache = false;
                    }

                    //quickorder
                    if (in_array('PCPCombinationsToCart', array_column($backtrace, 'function'))) {
                        $cache = false;
                    }

                    $cartProductsFiltered = $quantityDiscountRule->filterProducts($cartProducts, $action, $cache);
                    if (!$cartProductsFiltered) {
                        continue 2;
                    }

                    if (!$productsGrouped = $this->groupProducts($cart->id, $cartProductsFiltered, $action, $cache)) {
                        continue 2;
                    }

                    $productsGroupedBuyX = array();
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        $productsGroupedBuyX[$actionId] = $this->groupProducts($cart->id, $cartProductsFilteredBuyX[$actionId], $actionsBuyX[$actionId]);
                    }

                    //CAUTION! We have to sort products from action Buy X the other way, to start removing the ones that should be discounted later
                    foreach ($productsGrouped as &$productGrouped) {
                        uasort($productGrouped['products'], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                        unset($productGrouped);
                    }

                    /*
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        uasort($cartProductsFilteredBuyX[$actionId], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_DESC : SORT_ASC)));
                    }
                    */

                    //We do all this mess to avoid the problem when a product from "Get Y" is evaluated first in "Buy X"
                    foreach ($productsGroupedBuyX as $key => $productGroupedBuyX) {
                        foreach ($productGroupedBuyX as $key2 => $value) {
                            foreach ($value['products'] as $value2) {
                                foreach ($productsGrouped as $productGrouped) {
                                    if (isset($productGrouped['products'][$value2['id_product'].'-'.$value2['id_product_attribute']])) {
                                        $temp = $productsGroupedBuyX[$key][$key2]['products'][$value2['id_product'].'-'.$value2['id_product_attribute']];
                                        unset($productsGroupedBuyX[$key][$key2]['products'][$value2['id_product'].'-'.$value2['id_product_attribute']]);
                                        $productsGroupedBuyX[$key][$key2]['products'][$value2['id_product'].'-'.$value2['id_product_attribute']] = $temp;
                                    }
                                }
                            }
                        }
                        unset($productGroupedBuyX);
                    }

                    //Para cada accion de Buy X
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $productsRemoved = array();
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            //Borra cantidad X en productos de X
                            if (is_array($productsGroupedBuyX[$actionId])) {
                                foreach ($productsGroupedBuyX[$actionId] as $key => &$productGroupedBuyX) {
                                    if (is_array($productGroupedBuyX['products'])) {
                                        foreach ($productGroupedBuyX['products'] as $key2 => &$productBuyX) {
                                            $unitsToRemove = min($productBuyX['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                            $unitsRemoved += $unitsToRemove;

                                            $productsRemoved[$key2] = $unitsToRemove;

                                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGroupedBuyX['products'], $unitsToRemove);

                                            if ($productBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productGroupedBuyX['products'][$key2]);
                                            } else {
                                                $productBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($productGroupedBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productsGroupedBuyX[$actionId][$key]);
                                            } else {
                                                $productGroupedBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                                break 2;
                                            }
                                        }
                                        unset($productBuyX);
                                    }
                                }
                                unset($productGroupedBuyX);
                            }
                        }

                        //Borra cantidad X en productos de Y
                        if ($unitsRemoved) {
                            foreach ($productsGrouped as &$productGrouped) {
                                foreach ($productGrouped['products'] as $key2 => &$product) {
                                    if (in_array($key2, array_keys($productsRemoved))) {
                                        if ($product['cart_quantity'] == $productsRemoved[$key2]) {
                                            unset($productGrouped['products'][$key]);
                                        } else {
                                            $product['cart_quantity'] -= $productsRemoved[$key2];
                                        }

                                        $productGrouped['cart_quantity'] -= $productsRemoved[$key2];
                                    }
                                }
                                unset($product);
                            }
                            unset($productGrouped);

                            $remainingTimesToApplyPromo = (int)$action->apply_discount_to_nb;
                            $productsGrouped = array_reverse($productsGrouped);
                            foreach ($productsGrouped as &$productGrouped) {
                                if ($this->compareValue(0, (int)$productGrouped['cart_quantity'], (int)$action->apply_discount_to_nb)) {
                                    $remainingTimesToApplyPromo = min($remainingTimesToApplyPromo, (int)$productGrouped['cart_quantity']);
                                    $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGrouped['products'], $remainingTimesToApplyPromo);

                                    foreach ($productGrouped['products'] as $key => &$product) {
                                        $productPrice = $this->getDiscountedAmount($action, $product, false);

                                        $timesToApplyPromoInThisProduct = min($remainingTimesToApplyPromo, (int)$product['cart_quantity'], (($action->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX));

                                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                                        $reductionAmount += ($productPrice-$action->reduction_amount)*$timesToApplyPromoInThisProduct;

                                        if ($product['cart_quantity'] == $timesToApplyPromoInThisProduct) {
                                            unset($productGrouped['products'][$key]);
                                        } else {
                                            $product['cart_quantity'] -= $timesToApplyPromoInThisProduct;
                                        }

                                        $productGrouped['cart_quantity'] -= $timesToApplyPromoInThisProduct;

                                        foreach ($productsGroupedBuyX as $aKey => &$productGroupedBuyX) {
                                            foreach ($productGroupedBuyX as $aKey2 => $value) {
                                                foreach ($value['products'] as $aKey3 => &$value2) {
                                                    if ($key == $aKey3) {
                                                        if ($value2['cart_quantity'] == $timesToApplyPromoInThisProduct) {
                                                            unset($productGroupedBuyX[$aKey2]['products'][$aKey3]);
                                                        } else {
                                                            $value2['cart_quantity'] -= $timesToApplyPromoInThisProduct;
                                                        }
                                                    }
                                                }
                                                unset($value2);
                                            }
                                        }
                                        unset($productGroupedBuyX);

                                        $remainingTimesToApplyPromo -= $timesToApplyPromoInThisProduct;
                                        if ($remainingTimesToApplyPromo <= 0) {
                                            break 2;
                                        }
                                    }
                                    unset($product);
                                }
                            }
                            unset($productGrouped);
                        }
                    }

                    if ($reductionAmount > 0) {
                        if (isset($tempCartRule[$tempCartRuleCounter]['reduction_amount'])) {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] += $reductionAmount;
                        } else {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmount;
                        }
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                case 107:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    $productsGroupedBuyX = array();
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        $productsGroupedBuyX[$actionId] = $this->groupProducts($cart->id, $cartProductsFilteredBuyX[$actionId], $actionsBuyX[$actionId]);
                    }

                    //Para cada accion de Buy X
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $productsRemoved = array();
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            //Borra cantidad X en productos de X
                            if (is_array($productsGroupedBuyX[$actionId])) {
                                foreach ($productsGroupedBuyX[$actionId] as &$productGroupedBuyX) {
                                    if (is_array($productGroupedBuyX['products'])) {
                                        foreach ($productGroupedBuyX['products'] as $key => &$productBuyX) {
                                            $unitsToRemove = min($productBuyX['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                            $unitsRemoved += $unitsToRemove;

                                            $productsRemoved[$key] = $unitsToRemove;

                                            $this->addDiscountedProducts($action, $productGroupedBuyX['products'], $unitsToRemove);

                                            if ($productBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productGroupedBuyX['products'][$key]);
                                            } else {
                                                $productBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($productGroupedBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productsGroupedBuyX[$actionId]);
                                            } else {
                                                $productGroupedBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //Add product
                    if ($cart->updateQty((int)$action->apply_discount_to_nb*(int)$minCoincidences, (int)$action->gift_product, (int)$action->gift_product_attribute, false, 'up', 0, null, false)) {
                        $cartProducts = $cart->getProducts();

                        Db::getInstance()->insert('quantity_discount_rule_cart_product', array(
                            'id_cart' => (int)$cart->id,
                            'id_quantity_discount_rule' => (int)$quantityDiscountRule->id,
                            'id_product' => (int)$action->gift_product,
                            'id_product_attribute' => (int)$action->gift_product_attribute,
                            'quantity' => (int)$action->apply_discount_to_nb*(int)$minCoincidences,
                        ));

                        //Find product in cart
                        $key = $this->multiArraySearch($cartProducts, array('id_product' => (int)$action->gift_product, 'id_product_attribute' => (int)$action->gift_product_attribute));
                        $productPrice = $this->getDiscountedAmount($action, $cartProducts[$key], false);
                        $productPrice = self::convertPriceFull($productPrice, $this->context->currency, new Currency((int)$action->reduction_currency), false);

                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = ($productPrice - $action->reduction_amount)*(int)$action->apply_discount_to_nb*(int)$minCoincidences;
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                        $tempCartRule[$tempCartRuleCounter]['id_action_type'] = 1;
                    }
                    break;

                /**
                 *
                 * Gift a product
                 *
                 */
                case 103:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    $productsGroupedBuyX = array();
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        $productsGroupedBuyX[$actionId] = $this->groupProducts($cart->id, $cartProductsFilteredBuyX[$actionId], $actionsBuyX[$actionId]);
                    }

                    //Para cada accion de Buy X
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $productsRemoved = array();
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            //Borra cantidad X en productos de X
                            if (isset($productsGroupedBuyX[$actionId]) && is_array($productsGroupedBuyX[$actionId])) {
                                foreach ($productsGroupedBuyX[$actionId] as &$productGroupedBuyX) {
                                    if (isset($productGroupedBuyX['products']) && is_array($productGroupedBuyX['products'])) {
                                        foreach ($productGroupedBuyX['products'] as $key => &$productBuyX) {
                                            $unitsToRemove = min($productBuyX['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                            $unitsRemoved += $unitsToRemove;

                                            $productsRemoved[$key] = $unitsToRemove;

                                            $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $productGroupedBuyX['products'], $unitsToRemove);

                                            if ($productBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productGroupedBuyX['products'][$key]);
                                            } else {
                                                $productBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($productGroupedBuyX['cart_quantity'] == $unitsToRemove) {
                                                unset($productsGroupedBuyX[$actionId]);
                                            } else {
                                                $productGroupedBuyX['cart_quantity'] -= $unitsToRemove;
                                            }

                                            if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                                break 2;
                                            }
                                        }
                                        unset($productBuyX);
                                    }
                                }
                                unset($productGroupedBuyX);
                            }
                        }
                    }

                    $tempCartRule[$tempCartRuleCounter]['gift_product'] = (int)$action->gift_product;
                    $tempCartRule[$tempCartRuleCounter]['gift_product_attribute'] = (int)$action->gift_product_attribute;
                    $tempCartRule[$tempCartRuleCounter]['duplicate_rule'] = (int)$minCoincidences*(int)$action->apply_discount_to_nb;

                    break;

                /**
                 *
                 * Product pack - Fixed discount
                 *
                 */
                case 104:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    /*
                     * $cartProductsFilteredBuyX are the products that complies actions 24
                     * We could just do $action->reduction_amount*$minCoincidences, but we need to know the price of the product
                     * because is product price is lower than reduction_amount, we reduce the product price
                    */
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        uasort($cartProductsFilteredBuyX[$actionId], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                    }

                    /*
                     * For all the coincidences, we calculate the product pack amount (we get products regarding the quantity from each condition)
                    */
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $computedProductPackPrice = 0;
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            if (isset($cartProductsFilteredBuyX[$actionId]) && is_array($cartProductsFilteredBuyX[$actionId])) {
                                foreach ($cartProductsFilteredBuyX[$actionId] as $key => &$product) {
                                    $unitsToRemove = min($product['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                    $unitsRemoved += $unitsToRemove;

                                    $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $cartProductsFilteredBuyX[$actionId], $unitsToRemove);

                                    $unitDiscount = $this->getDiscountedAmount($action, $product, false);
                                    $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency));

                                    $computedProductPackPrice += $unitDiscountConverted;

                                    if ($product['cart_quantity'] == $unitsToRemove) {
                                        unset($cartProductsFilteredBuyX[$actionId][$key]);
                                    } else {
                                        $product['cart_quantity'] -= $unitsToRemove;
                                    }

                                    if ($actionsBuyX[$actionId]->products_n0b_each == $unitsRemoved) {
                                        break;
                                    }
                                }
                                unset($product);
                            }
                        }

                        if ($computedProductPackPrice > $action->reduction_amount) {
                            $reductionAmount += $action->reduction_amount;
                        } else {
                            $reductionAmount += $computedProductPackPrice;
                        }
                    }

                    if ($reductionAmount > 0) {
                        if (isset($tempCartRule[$tempCartRuleCounter]['reduction_amount'])) {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] += $reductionAmount;
                        } else {
                            $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = $reductionAmount;
                        }
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;

                /**
                 *
                 * Product pack - Percentage discount
                 *
                 */
                case 105:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    /*
                     * $cartProductsFilteredBuyX are the products that complies actions 24
                     * We could just do $action->reduction_amount*$minCoincidences, but we need to know the price of the product
                     * because is product price is lower than reduction_amount, we reduce the product price
                    */
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        uasort($cartProductsFilteredBuyX[$actionId], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                    }

                    /*
                     * For all the coincidences, we calculate the product pack amount (we get products regarding the quantity from each condition)
                    */
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $computedProductPackPrice = 0;
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            if (isset($cartProductsFilteredBuyX[$actionId]) && is_array($cartProductsFilteredBuyX[$actionId])) {
                                foreach ($cartProductsFilteredBuyX[$actionId] as $key => &$product) {
                                    $unitsToRemove = min($product['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                    $unitsRemoved += $unitsToRemove;

                                    $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $cartProductsFilteredBuyX[$actionId], $unitsToRemove);

                                    $unitDiscount = $this->getDiscountedAmount($action, $product, true)*$unitsToRemove;

                                    $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency));

                                    $reductionProductMaxAmountConverted = self::convertPriceFull($action->reduction_product_max_amount, $this->context->currency, new Currency((int)$action->reduction_currency));

                                    $reductionAmount += (($reductionProductMaxAmountConverted > 0 && $unitDiscountConverted > $reductionProductMaxAmountConverted) ? $reductionProductMaxAmountConverted : $unitDiscountConverted);
                                    if ($product['cart_quantity'] == $unitsToRemove) {
                                        unset($cartProductsFilteredBuyX[$actionId][$key]);
                                    } else {
                                        $product['cart_quantity'] -= $unitsToRemove;
                                    }

                                    if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                        break;
                                    }
                                }
                                unset($product);
                            }
                        }
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_percent_tax;
                    }

                    break;

                /**
                 *
                 * Product pack - Fixed price
                 *
                 */
                case 106:
                    if (!$minCoincidences) {
                        continue 2;
                    }

                    /*
                     * $cartProductsFilteredBuyX are the products that complies actions 24
                     * We could just do $action->reduction_amount*$minCoincidences, but we need to know the price of the product
                     * because is product price is lower than reduction_amount, we reduce the product price
                    */
                    foreach (array_keys($actionsBuyX) as $actionId) {
                        uasort($cartProductsFilteredBuyX[$actionId], $this->makeComparer(array($taxCalculationMethod ? 'price_with_reduction_without_tax' : 'price_with_reduction', $action->apply_discount_sort == 'cheapest' ? SORT_ASC : SORT_DESC)));
                    }

                    /*
                     * For all the coincidences, we calculate the product pack amount (we get products regarding the quantity from each condition)
                    */
                    for ($i = 0; $i < $minCoincidences; $i++) {
                        $computedProductPackPrice = 0;
                        foreach (array_keys($actionsBuyX) as $actionId) {
                            $unitsRemoved = 0;

                            if (isset($cartProductsFilteredBuyX[$actionId]) && is_array($cartProductsFilteredBuyX[$actionId])) {
                                foreach ($cartProductsFilteredBuyX[$actionId] as $key => &$product) {
                                    $unitsToRemove = min($product['cart_quantity'], $actionsBuyX[$actionId]->products_nb_each-$unitsRemoved);
                                    $unitsRemoved += $unitsToRemove;

                                    $quantityDiscountRule->addDiscountedProducts((int)$action->id_type, $cartProductsFilteredBuyX[$actionId], $unitsToRemove);

                                    $unitDiscount = $this->getDiscountedAmount($action, $product, false)*$unitsToRemove;

                                    $unitDiscountConverted = self::convertPriceFull($unitDiscount, $this->context->currency, new Currency((int)$action->reduction_currency));

                                    $computedProductPackPrice += $unitDiscountConverted;

                                    if ($product['cart_quantity'] == $unitsToRemove) {
                                        unset($cartProductsFilteredBuyX[$actionId][$key]);
                                    } else {
                                        $product['cart_quantity'] -= $unitsToRemove;
                                    }

                                    if ($actionsBuyX[$actionId]->products_nb_each == $unitsRemoved) {
                                        break;
                                    }
                                }
                                unset($product);
                            }
                        }

                        $reductionAmount += $computedProductPackPrice - $action->reduction_amount;
                    }

                    if ($reductionAmount > 0) {
                        $tempCartRule[$tempCartRuleCounter]['reduction_amount'] = (($action->reduction_max_amount > 0 && $reductionAmount > $action->reduction_max_amount) ? $action->reduction_max_amount : $reductionAmount);
                        $tempCartRule[$tempCartRuleCounter]['reduction_currency'] = (int)$action->reduction_currency;
                        $tempCartRule[$tempCartRuleCounter]['reduction_tax'] = (int)$action->reduction_tax;
                    }

                    break;
            }

            $tempCartRuleCounter++;
        }
       /* END OF ACTIONS */

        if (!empty($tempCartRule)) {
            $ruleApplied = false;
            foreach ($tempCartRule as $aTempCartRule) {
                if (!isset($aTempCartRule['duplicate_rule'])) {
                    $aTempCartRule['duplicate_rule'] = 1;
                }

                if ($aTempCartRule['duplicate_rule'] > 0) {
                    $ruleApplied = true;
                    $this->createCartRule($aTempCartRule, $quantityDiscountRule, $cart);
                }
            }

            if ($ruleApplied) {
                //We need to force cache cleaning to get rules
                if (defined('_TB_VERSION_')) {
                    //Thirty Bees
                    $cache_key = 'static';
                } else {
                    //PrestaShop
                    $cache_key = 'Cart';
                }

                // Set same address to all products to avoid order duplicates
                $this->setProductAddress();
                //$context->cart->autosetProductAddress();

                Cache::clean($cache_key.'::getCartRules_'.(int)$cart->id.'-'.CartRule::FILTER_ACTION_ALL);
                Cache::clean($cache_key.'::getCartRules_'.(int)$cart->id.'-'.CartRule::FILTER_ACTION_SHIPPING);
                Cache::clean($cache_key.'::getCartRules_'.(int)$cart->id.'-'.CartRule::FILTER_ACTION_REDUCTION);
                Cache::clean($cache_key.'::getCartRules_'.(int)$cart->id.'-'.CartRule::FILTER_ACTION_GIFT);
                if (version_compare(_PS_VERSION_, '1.5.4.0', '>=')) {
                    Cache::clean($cache_key.'::getCartRules_'.(int)$cart->id.'-'.CartRule::FILTER_ACTION_ALL_NOCAP);
                }

                Cache::clean($cache_key.'::getOrderedCartRulesIds_'.$cart->id.'-'.CartRule::FILTER_ACTION_ALL). '-ids';
                Cache::clean($cache_key.'::getOrderedCartRulesIds_'.$cart->id.'-'.CartRule::FILTER_ACTION_SHIPPING). '-ids';
                Cache::clean($cache_key.'::getOrderedCartRulesIds_'.$cart->id.'-'.CartRule::FILTER_ACTION_REDUCTION). '-ids';
                Cache::clean($cache_key.'::getOrderedCartRulesIds_'.$cart->id.'-'.CartRule::FILTER_ACTION_GIFT). '-ids';

                return true;
            }
        }

        return false;
    }

    public function createCartRule($tempCartRule, $quantityDiscountRule, $cart = null)
    {
        if (!$cart) {
            $cart = $this->context->cart;
        }

        //Before adding it, check if this rule is already applied to prevent simultaneous async calls
        /*if (!isset($tempCartRule['gift_product'])
            && $this->isAlreadyInCart((int)$cart->id, (int)$quantityDiscountRule->id_quantity_discount_rule)) {
            return false;
        }*/

        if (isset($tempCartRule['gift_product'])) {
            if (!$cart->updateQty($tempCartRule['duplicate_rule'], $tempCartRule['gift_product'], isset($tempCartRule['gift_product_attribute']) ? $tempCartRule['gift_product_attribute'] : 0, false, 'up', 0, null, false)) {
                return false;
            }
        }

        for ($i = 0; $i < $tempCartRule['duplicate_rule']; $i++) {
            if (!Db::getInstance()->insert('cart_rule', array(
                'id_customer' => (int)$cart->id_customer,
                'date_from' => $quantityDiscountRule->date_from,
                'date_to' => $quantityDiscountRule->date_to,
                'description' => pSQL($quantityDiscountRule->description),
                'quantity' => '1',
                'quantity_per_user' => '1',
                'priority' => '1',
                'partial_use' => '0',
                'code' => (isset($quantityDiscountRule->code) && $quantityDiscountRule->code) ? pSQL($quantityDiscountRule->code) : pSQL($quantityDiscountRule->code_prefix.Tools::strtoupper(Tools::passwdGen(12))),
                'minimum_amount' => '0',
                'minimum_amount_tax' => '0',
                'minimum_amount_currency' => '0',
                'minimum_amount_shipping' => '0',
                'country_restriction' => '0',
                'carrier_restriction' => isset($tempCartRule['carrier_restriction']) ? '1' : '0',
                'group_restriction' => '0',
                'cart_rule_restriction' => '0',
                'product_restriction' => isset($tempCartRule['reduction_product']) ? 1 : 0,
                'shop_restriction' => '0',
                'free_shipping' => isset($tempCartRule['free_shipping']) ? $tempCartRule['free_shipping'] : '0',
                'reduction_percent' => '0',
                'reduction_amount' => isset($tempCartRule['reduction_amount']) ? Tools::ps_round($tempCartRule['reduction_amount'], 2) : '0',
                'reduction_tax' => isset($tempCartRule['reduction_tax']) ? $tempCartRule['reduction_tax'] : '0',
                'reduction_currency' => isset($tempCartRule['reduction_currency']) ? $tempCartRule['reduction_currency'] : '0',
                'reduction_product' => isset($tempCartRule['reduction_product']) ? $tempCartRule['reduction_product'] : '',
                'gift_product' => isset($tempCartRule['gift_product']) ? $tempCartRule['gift_product'] : '0',
                'gift_product_attribute' => isset($tempCartRule['gift_product_attribute']) ? $tempCartRule['gift_product_attribute'] : '0',
                'highlight' => '0',
                'active' => 1,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s')
            ))) {
                if (isset($tempCartRule['gift_product'])) {
                    $cart->updateQty($tempCartRule['duplicate_rule'], $tempCartRule['gift_product'], isset($tempCartRule['gift_product_attribute']) ? $tempCartRule['gift_product_attribute'] : 0, false, 'up', 0, null, false);
                }
                return false;
            }

            $id = Db::getInstance()->Insert_ID();

            foreach (Language::getLanguages(false) as $lang) {
                if (!Db::getInstance()->insert('cart_rule_lang', array(
                    'id_cart_rule' => (int)$id,
                    'name' => pSQL(isset($quantityDiscountRule->name[$lang['id_lang']]) ? $quantityDiscountRule->name[$lang['id_lang']] : $quantityDiscountRule->name[Configuration::get('PS_LANG_DEFAULT')], false),
                    'id_lang' => (int)$lang['id_lang']
                ))) {
                    return false;
                }
            }

            if ($this->isMultishop()) {
                if (!Db::getInstance()->insert('cart_rule_shop', array(
                    'id_cart_rule' => (int)$id,
                    'id_shop' => (int)Context::getContext()->shop->id
                ))) {
                    return false;
                }
            }

            if (!Db::getInstance()->insert('quantity_discount_rule_cart', array(
                'id_cart' => (int)$cart->id,
                'id_quantity_discount_rule' => (int)$quantityDiscountRule->id,
                'id_cart_rule' => (int)$id,
                'id_action_type' => isset($tempCartRule['id_action_type']) ? (int)$tempCartRule['id_action_type'] : 0,
                'active' => 1
            ))) {
                return false;
            }

            if (isset($tempCartRule['reduction_product'])) {
                if (!Db::getInstance()->insert('cart_rule_product_rule_group', array(
                    'id_cart_rule' => (int)$id,
                    'quantity' => '1',
                ))) {
                    return false;
                }

                $cartRuleProductRuleGroup = Db::getInstance()->Insert_ID();

                if (!Db::getInstance()->insert('cart_rule_product_rule', array(
                    'id_product_rule_group' => (int)$cartRuleProductRuleGroup,
                    'type' => 'products',
                ))) {
                    return false;
                }

                $cartRuleProductRule = Db::getInstance()->Insert_ID();

                if (!Db::getInstance()->insert('cart_rule_product_rule_value', array(
                    'id_product_rule' => (int)$cartRuleProductRule,
                    'id_item' => $tempCartRule['reduction_product'],
                ))) {
                    return false;
                }
            }

            // Add the cart rule to the cart
            if (!Db::getInstance()->insert('cart_cart_rule', array(
                'id_cart_rule' => (int)$id,
                'id_cart' => (int)$cart->id
            ))) {
                return false;
            }

            //Create carrier restriction
            if (isset($tempCartRule['carrier_restriction'])) {
                foreach ($tempCartRule['carrier_restriction'] as $carrierRestriction) {
                    $category = (int) $carrierRestriction;
                    $values[] = "('$id', '$category')";
                }
                $values = implode(',', $values);

                $query = "INSERT INTO " ._DB_PREFIX_. "cart_rule_carrier (id_cart_rule, id_carrier) VALUES $values";
                Db::getInstance()->execute($query);
            }
        }

        //Flush cache
        $this->clearCartCache();

        return true;
    }

    public static function getQuantityDiscountRulesAtCart($id_cart, $id_quantity_discount_rule = null, $id_cart_rule = null)
    {
        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_quantity_discount_rule`, qdrc.`id_cart_rule`, qdr.`code`, qdrc.`id_action_type`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdr.`id_quantity_discount_rule` = qdrc.`id_quantity_discount_rule`)
            WHERE qdrc.`active` = 1
                AND `id_cart` = '.(int)$id_cart.
                ($id_quantity_discount_rule ? ' AND `id_quantity_discount_rule` = '.(int)$id_quantity_discount_rule : '').
                ($id_cart_rule ? ' AND `id_cart_rule` = '.(int)$id_cart_rule : '');

        return Db::getInstance()->executeS($sql);
    }

    public static function getQuantityDiscountRuleByCode($code)
    {
        if (!Validate::isCleanHtml($code)) {
            return false;
        }

        /*
        $sql = 'SELECT `id_quantity_discount_rule`
            FROM `'._DB_PREFIX_.'quantity_discount_rule`
            WHERE `code` = \''.pSQL($code).'\'
                AND `id_shop` = '.(int)Context::getContext()->shop->id;
        */

        $sql = 'SELECT `id_quantity_discount_rule`
            FROM `'._DB_PREFIX_.'quantity_discount_rule`
            WHERE `code` = \''.pSQL($code).'\'';

        return Db::getInstance()->getValue($sql);
    }

    public static function getIdCartRuleFromQuantityDiscountRuleFromThisCart($id_quantity_discount_rule, $id_cart)
    {
        if (!(int)$id_quantity_discount_rule || !(int)$id_quantity_discount_rule > 0) {
            return false;
        }

        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_cart_rule`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            WHERE
                qdrc.`active` = 1
                AND qdrc.`id_quantity_discount_rule` = '.(int)$id_quantity_discount_rule.'
                AND `id_cart` = '.(int)$id_cart;

        return Db::getInstance()->getValue($sql);
    }

    public static function cartRuleGeneratedByAQuantityDiscountRuleCode($code)
    {
        if (!Validate::isCleanHtml($code)) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_quantity_discount_rule`, qdrc.`id_cart_rule`, qdr.`code`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdr.`id_quantity_discount_rule` = qdrc.`id_quantity_discount_rule`)
            LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON (qdrc.`id_cart_rule` = cr.`id_cart_rule`)
            WHERE qdrc.`active` = 1 AND qdr.code <> "" AND cr.code = \''.pSQL($code).'\'';

        return Db::getInstance()->getValue($sql);
    }

    public static function cartRuleGeneratedByAQuantityDiscountRuleCodeWithoutCode($code)
    {
        if (!Validate::isCleanHtml($code)) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_quantity_discount_rule`, qdrc.`id_cart_rule`, qdr.`code`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdr.`id_quantity_discount_rule` = qdrc.`id_quantity_discount_rule`)
            LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON (qdrc.`id_cart_rule` = cr.`id_cart_rule`)
            WHERE qdrc.`active`= 1 AND cr.code = \''.pSQL($code).'\'';

        return Db::getInstance()->getValue($sql);
    }

    public static function isQuantityDiscountRule($id_cart_rule)
    {
        if (!(int)$id_cart_rule > 0) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_cart_rule`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            /*LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON (qdrc.`id_cart_rule` = cr.`id_cart_rule`)*/
            WHERE qdrc.`active` = 1 AND qdrc.`id_cart_rule` = '.(int)$id_cart_rule;

        return Db::getInstance()->getValue($sql);
    }

    public static function isQuantityDiscountRuleWithCode($id_cart_rule)
    {
        if (!(int)$id_cart_rule > 0) {
            return false;
        }

        $sql = 'SELECT qdr.`code`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdrc.`id_quantity_discount_rule` = qdr.`id_quantity_discount_rule`)
            WHERE qdrc.`active`= 1 AND qdrc.`id_cart_rule` = '.(int)$id_cart_rule;

        return Db::getInstance()->getValue($sql);
    }

    public static function isQuantityDiscountRuleFromThisCart($id_quantity_discount_rule, $id_cart)
    {
        if (!(int)$id_quantity_discount_rule || !(int)$id_quantity_discount_rule > 0) {
            return false;
        }

        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_quantity_discount_rule`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdr.`id_quantity_discount_rule` = qdrc.`id_quantity_discount_rule`)
            WHERE /*qdrc.`active`= 1 AND*/ qdrc.`id_cart_rule` = '.(int)$id_quantity_discount_rule.' AND `id_cart` = '.(int)$id_cart;

        return Db::getInstance()->getValue($sql);
    }

    public static function getIdCartFruleFromIdQuantityDiscountRuleFromThisCart($id_quantity_discount_rule, $id_cart)
    {
        if (!(int)$id_quantity_discount_rule || !(int)$id_quantity_discount_rule > 0) {
            return false;
        }

        if (!(int)$id_cart || !(int)$id_cart > 0) {
            return false;
        }

        $sql = 'SELECT qdrc.`id_cart_rule`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdr.`id_quantity_discount_rule` = qdrc.`id_quantity_discount_rule`)
            WHERE qdrc.`active`= 1 AND qdrc.`id_quantity_discount_rule` = '.(int)$id_quantity_discount_rule.' AND `id_cart` = '.(int)$id_cart;

        return Db::getInstance()->getValue($sql);
    }

    public static function duplicateCart($oldCart, $newCart)
    {
        $cartRulesInCart = self::getQuantityDiscountRulesAtCart((int)$oldCart->id);

        foreach ($cartRulesInCart as $cartRuleInCart) {
            if ((int)$cartRuleInCart['id_action_type'] == 1) {
                $sql = "
                    SELECT *
                    FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product` t
                    WHERE `id_cart` = ".(int)$oldCart->id
                        ." AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule']."
                    ORDER BY 1";

                $result = Db::getInstance()->executeS($sql);
                foreach ($result as $product) {
                    $newCart['cart']->updateQty((int)$product['quantity'], (int)$product['id_product'], (int)$product['id_product_attribute'], null, 'down', 0, null, false);
                }
            } elseif ((int)$cartRuleInCart['id_action_type'] == 2) {
                $sql =
                    "SELECT *
                    FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product` t
                    WHERE `id_cart` = ".(int)$oldCart->id
                        ." AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule']."
                    ORDER BY 1";

                $result = Db::getInstance()->executeS($sql);

                foreach ($result as $product) {
                    $newCart['cart']->updateQty((int)$product['quantity'], (int)$product['id_product'], (int)$product['id_product_attribute'], null, 'up', 0, null, false);
                }
            }
        }

        $query =
            'SELECT qdrc.*, qdr.`code`
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule` qdr ON (qdrc.`id_quantity_discount_rule` = qdr.`id_quantity_discount_rule`)
            LEFT JOIN `'._DB_PREFIX_.'quantity_discount_rule_family` qdrf ON (qdr.`id_family` = qdrf.`id_quantity_discount_rule_family`)
            WHERE `id_cart` = '.(int)$oldCart->id.'
                AND qdrc.`active` = 1 AND qdr.`active`
            ORDER BY qdrf.`priority` ASC, qdrf.`id_quantity_discount_rule_family` ASC, qdr.`priority` ASC, qdr.`id_quantity_discount_rule` ASC;'
        ;

        $result = Db::getInstance()->executeS($query);

        $cartRulesCreated = false;
        foreach ($result as $row) {
            $quantityDiscountRuleObj = new QuantityDiscountRule((int)$row['id_quantity_discount_rule']);

            if ($cartRulesCreated && !$quantityDiscountRuleObj->compatible_qdp_rules) {
                continue;
            }

            if (!$quantityDiscountRuleObj->isQuantityDiscountRuleValid(array($row['code']))) {
                continue;
            }

            if (!$quantityDiscountRuleObj->compatibleCartRules()) {
                continue;
            }

            if (!$quantityDiscountRuleObj->validateQuantityDiscountRuleConditions()) {
                continue;
            }

            if ($quantityDiscountRuleObj->calculateCartRule($quantityDiscountRuleObj, $newCart['cart'])) {
                $cartRulesCreated = true;
                if (!$quantityDiscountRuleObj->execute_other_rules) {
                    break;
                }
            }
        }
    }

    public static function removeQuantityDiscountCartRule($id_cart_rule, $id_cart)
    {
        if (!(int)$id_cart_rule || !(int)$id_cart || !(int)$id_cart_rule > 0 || !(int)$id_cart > 0) {
            return false;
        }

        if (!self::isQuantityDiscountRuleFromThisCart((int)$id_cart_rule, (int)$id_cart)) {
            return false;
        }

        $context = Context::getContext();
        if (!$context->cart) {
            return;
        }

        $cartRulesInCart = self::getQuantityDiscountRulesAtCart((int)$id_cart, null, (int)$id_cart_rule);

        foreach ($cartRulesInCart as $cartRuleInCart) {
            if ((int)$cartRuleInCart['id_action_type'] == 1) {
                $sql = "
                    SELECT *
                    FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product` t
                    WHERE `id_cart` = ".(int)$id_cart
                        ." AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule']."
                    ORDER BY 1";

                $result = Db::getInstance()->executeS($sql);
                foreach ($result as $product) {
                    $context->cart->updateQty((int)$product['quantity'], (int)$product['id_product'], (int)$product['id_product_attribute'], null, 'down', 0, null, false);
                }

                $sql =
                    "DELETE
                    FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product`
                    WHERE `id_cart` = ".(int)$id_cart
                        ." AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule'];

                Db::getInstance()->execute($sql);
            } elseif ((int)$cartRuleInCart['id_action_type'] == 2) {
                $sql =
                    "SELECT *
                    FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product` t
                    WHERE `id_cart` = ".(int)$id_cart
                        ." AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule']."
                    ORDER BY 1";

                $result = Db::getInstance()->executeS($sql);

                foreach ($result as $product) {
                    $context->cart->updateQty((int)$product['quantity'], (int)$product['id_product'], (int)$product['id_product_attribute'], null, 'up', 0, null, false);
                }

                Db::getInstance()->execute(
                    "DELETE
                    FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product`
                    WHERE `id_cart` = ".(int)$id_cart
                        ." AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule']
                );
            }
        }

        $cartRule = new CartRule((int)$id_cart_rule);
        if (!Validate::isLoadedObject($cartRule)) {
            return true;
        }

        if (self::getQuantityDiscountRuleByCode($cartRule->code)) {
            $cartRulesInCart = self::getQuantityDiscountRulesAtCart((int)$id_cart);
            foreach ($cartRulesInCart as $cartRuleInCart) {
                if ($cartRuleInCart['code'] == $cartRule->code) {
                    $cartRule = new CartRule((int)$cartRuleInCart['id_cart_rule']);
                    /*if (!Db::getInstance()->execute("DELETE
                            FROM `"._DB_PREFIX_."quantity_discount_rule_cart`
                            WHERE `id_cart` = ".(int)$id_cart
                            ." AND `id_cart_rule` = ".(int)$cartRuleInCart['id_cart_rule'])
                        || !$context->cart->removeCartRule((int)$cartRuleInCart['id_cart_rule'])
                        || !$cartRule->delete()) {
                        return false;
                    }*/
                    Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."quantity_discount_rule_cart`
                        SET `active` = 0
                            WHERE `id_cart_rule` = ".(int)$cartRuleInCart['id_cart_rule']);
                    Db::getInstance()->execute("DELETE
                        FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product`
                        WHERE `id_cart` = ".(int)$id_cart."
                            AND `id_quantity_discount_rule` = ".(int)$cartRuleInCart['id_quantity_discount_rule']);
                    $context->cart->removeCartRule((int)$cartRuleInCart['id_cart_rule']);

                    $cartRule->active = 0;
                    //$cartRule->code = Tools::strtoupper(Tools::passwdGen(12));
                    $cartRule->save();
                }
            }
        } else {
            /*if (!Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."quantity_discount_rule_cart`
                    WHERE `id_cart` = ".(int)$id_cart." AND `id_cart_rule` = ".(int)$id_cart_rule)
                || !$context->cart->removeCartRule((int)$id_cart_rule)
                || !$cartRule->delete()) {
                return false;
            }*/

            // We don't remove the record to clean the cart rule in cleanup process
            $sql = "UPDATE `"._DB_PREFIX_."quantity_discount_rule_cart`
                SET `active` = 0
                    WHERE `id_cart_rule` = ".(int)$id_cart_rule;

            Db::getInstance()->execute($sql);
            $sql = "DELETE FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product`
                    WHERE `id_cart` = ".(int)$id_cart."
                        AND `id_quantity_discount_rule` = ".(int)$id_cart_rule;

            Db::getInstance()->execute($sql);

            $context->cart->removeCartRule((int)$id_cart_rule);
            $cartRule->active = 0;

            //$cartRule->code = Tools::strtoupper(Tools::passwdGen(12));
            $cartRule->save();
        }

        return true;
    }

    public function isQuantityDiscountRuleValid($cartRulesCodes = null, $highlight = false)
    {
        if ($this->modules_exceptions) {
            $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
            foreach (explode(";", $this->modules_exceptions) as $modules_exception) {
                foreach (array_column($backtrace, 'file') as $element) {
                    if (strpos($element, $modules_exception) !== false) {
                        return false;
                    }
                }
            }
        }

        //Check Date
        $now = date('Y-m-d H:i:s');
        if (strtotime($now) <= strtotime($this->date_from)
            || strtotime($now) >= strtotime($this->date_to)) {
            return false;
        }

        //Check if it's not out of stock
        $times_used = Db::getInstance()->getValue(
            "SELECT count(distinct(o.`id_order`))
            FROM `"._DB_PREFIX_."orders` o
            LEFT JOIN "._DB_PREFIX_."order_cart_rule od ON o.id_order = od.id_order
            LEFT JOIN "._DB_PREFIX_."quantity_discount_rule_order qdro ON od.id_cart_rule = qdro.id_cart_rule
            WHERE qdro.id_quantity_discount_rule = ".(int)$this->id."
            AND ".(int)Configuration::get('PS_OS_ERROR')." != o.current_state"
        );

        if ($this->quantity != 0 && ($times_used >= $this->quantity)) {
            return false;
        }

        if ((int)$this->context->cart->id_customer) {
            $query =
                "SELECT count(distinct(o.`id_order`))
                FROM `"._DB_PREFIX_."orders` o
                LEFT JOIN `"._DB_PREFIX_."customer` c ON o.`id_customer` = c.`id_customer`
                LEFT JOIN `"._DB_PREFIX_."order_cart_rule` od ON o.`id_order` = od.`id_order`
                LEFT JOIN `"._DB_PREFIX_."quantity_discount_rule_order` qdro ON od.`id_cart_rule` = qdro.`id_cart_rule`
                WHERE c.`email` = '".$this->context->customer->email."'
                AND qdro.`id_quantity_discount_rule` = ".(int)$this->id."
                AND ".(int)Configuration::get('PS_OS_ERROR')." != o.current_state";

            $quantityUsed = Db::getInstance()->getValue($query);
            if ($quantityUsed + 1 > $this->quantity_per_user) {
                return false;
            }
        }

        if (!$highlight && $this->code && (!$cartRulesCodes || !is_array($cartRulesCodes) || !count($cartRulesCodes) || !in_array($this->code, $cartRulesCodes))) {
            return false;
        }

        if ((int)$this->id_shop != (int)$this->context->cart->id_shop) {
            return false;
        }

        return true;
    }

    public function isQuantityDiscountRuleValidForMessages()
    {
        //Check Date
        $now = date('Y-m-d H:i:s');
        if (strtotime($now) <= strtotime($this->date_from)
            || strtotime($now) >= strtotime($this->date_to)) {
            return false;
        }

        //Check if it's not out of stock
        $times_used = Db::getInstance()->getValue(
            "SELECT count(distinct(o.`id_order`))
            FROM `"._DB_PREFIX_."orders` o
            LEFT JOIN "._DB_PREFIX_."order_cart_rule od ON o.id_order = od.id_order
            LEFT JOIN "._DB_PREFIX_."quantity_discount_rule_order qdro ON od.id_cart_rule = qdro.id_cart_rule
            WHERE qdro.id_quantity_discount_rule = ".(int)$this->id."
            AND ".(int)Configuration::get('PS_OS_ERROR')." != o.current_state"
        );

        if ($this->quantity != 0 && ($times_used >= $this->quantity)) {
            return false;
        }

        if ((int)$this->context->cart->id_customer) {
            $quantityUsed = Db::getInstance()->getValue(
                "SELECT count(distinct(o.`id_order`))
                FROM `"._DB_PREFIX_."orders` o
                LEFT JOIN `"._DB_PREFIX_."customer` c ON o.`id_customer` = c.`id_customer`
                LEFT JOIN `"._DB_PREFIX_."order_cart_rule` od ON o.`id_order` = od.`id_order`
                LEFT JOIN `"._DB_PREFIX_."quantity_discount_rule_order` qdro ON od.`id_cart_rule` = qdro.`id_cart_rule`
                WHERE c.`email` = '".$this->context->customer->email."'
                AND qdro.`id_quantity_discount_rule` = ".(int)$this->id
            );
            if ($quantityUsed + 1 > $this->quantity_per_user) {
                return false;
            }
        }

        return true;
    }

    public function validateQuantityDiscountRuleConditions($saveInCache = true)
    {
        if (!isset($this->context->cart)) {
            return false;
        }

        $cache_key = 'QuantityDiscountRule::validateQuantityDiscountRuleConditions_'.(int)$this->id_quantity_discount_rule;

        if (!Cache::isStored($cache_key)) {
            $groupConditions = $this->getGroups(true);
            if (!$groupConditions) {
                $result = true;
                if ($saveInCache) {
                    Cache::store($cache_key, $result);
                }
                return $result;
            }

            $virtual_context = Context::getContext()->cloneContext();
            foreach ($groupConditions as $groupCondition) {
                $conditions = $groupCondition->getConditions();

                if (!$conditions) {
                    $result = true;
                    if ($saveInCache) {
                        Cache::store($cache_key, $result);
                    }
                    return $result;
                }

                foreach ($conditions as $condition) {
                    $groupValidationPassed = false;
                    $condition = new QuantityDiscountRuleCondition($condition['id_quantity_discount_rule_condition']);

                    switch ((int)$condition->id_type) {
                        /**
                         * Limit to a single customer
                         *
                         * Check if customer matches the condition customer
                         */
                        case 1:
                            if ((int)$this->context->cart->id_customer == (int)$condition->id_customer) {
                                $groupValidationPassed = true;
                            }

                            break;

                        /**
                         * Customer must be suscribed to newsletter
                         *
                         * Check if customer is/or not subscribed to newsletter
                         */
                        case 2:
                            if ((int)$this->context->cart->id_customer) {
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                if ((int)$customer->newsletter == (int)$condition->customer_newsletter) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /**
                         * Customer signed up between a date
                         *
                         * If condition date is by days, substract the number of days to now and check if customer subscribed before this date
                         * If condition date is by interval, check if customer signed up is between these dates
                         */
                        case 3:
                            $time_now = date('Y-m-d');

                            if ((int)$this->context->cart->id_customer) {
                                $customer = new Customer((int)$this->context->cart->id_customer);

                                if ($condition->customer_signedup_date_to == '0000-00-00 00:00:00') {
                                    $condition->customer_signedup_date_to = $time_now;
                                }

                                if (strtotime($condition->customer_signedup_date_from) <= strtotime($customer->date_add) &&
                                    strtotime($condition->customer_signedup_date_to) >= strtotime($customer->date_add)) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /**
                         * Customer and orders done
                         *
                         * If condition date is by days, get the orders from this day onwards.
                         * If condition date is by interval, get the orders from this interval.
                         */
                        case 4:
                            $time_now = date('Y-m-d H:i:s');

                            //if ((int)$this->context->cart->id_customer) {
                                $orderStates = $condition->getSelectedAssociatedRestrictions('order_state');
                                if ($condition->customer_orders_nb_days > 0) {
                                    $orders = $this->getOrdersIdByDateAndState(date('Y-m-d H:i:s', (strtotime("-".$condition->customer_orders_nb_days." days", strtotime($time_now)))), $time_now, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                } else {
                                    if ($condition->customer_orders_nb_date_to == '0000-00-00 00:00:00') {
                                        $condition->customer_orders_nb_date_to = $time_now;
                                    }

                                    $orders = $this->getOrdersIdByDateAndState($condition->customer_orders_nb_date_from, $condition->customer_orders_nb_date_to, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                }

                                $groupValidationPassed = $this->compareValue((int)$condition->customer_orders_nb_operator, (int)count($orders), (int)$condition->customer_orders_nb);

                                if ($groupValidationPassed) {
                                    $productsTotal = 0;

                                    if ($condition->customer_orders_nb_prod) {
                                        foreach ($orders as $order) {
                                            $order = new Order($order);
                                            $orderProducts = $order->getProducts();

                                            $productsFiltered = $this->filterProducts($orderProducts, $condition, false);
                                            foreach ($productsFiltered as $productFiltered) {
                                                $productsTotal += (int)$productFiltered['product_quantity'];
                                            }
                                        }
                                    }

                                    $groupValidationPassed = $this->compareValue((int)$condition->customer_orders_nb_prod_operator, (int)($productsTotal), (int)$condition->customer_orders_nb_prod);
                                }
                            //}

                            break;

                        /**
                         * Customer and amount spent
                         *
                         * If condition date is by days, get the orders from this day onwards.
                         * If condition date is by interval, get the orders from this interval.
                         *
                         * Acumulate amount and convert to currency
                         */
                        case 5:
                            if ((int)$this->context->cart->id_customer && (int)$condition->customer_orders_amount_orders > 0) {
                                $time_now = date('Y-m-d H:i:s');
                                $totalAmount = 0;
                                $orders = array();
                                $orderStates = $condition->getSelectedAssociatedRestrictions('order_state');
                                if ($condition->customer_orders_amount_days > 0) {
                                    $orders = $this->getOrdersIdByDateAndState(date('Y-m-d H:i:s', (strtotime("-".$condition->customer_orders_amount_days." days", strtotime($time_now)))), $time_now, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                } elseif ($condition->customer_orders_amount_date_to != '0000-00-00 00:00:00'
                                    || $condition->customer_orders_amount_date_from != '0000-00-00 00:00:00') {
                                    if ($condition->customer_orders_amount_date_to == '0000-00-00 00:00:00') {
                                        $condition->customer_orders_amount_date_to = $time_now;
                                    }
                                    $orders = $this->getOrdersIdByDateAndState($condition->customer_orders_amount_date_from, $condition->customer_orders_amount_date_to, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                } elseif ($condition->customer_orders_amount_interval) {
                                    switch ($condition->customer_orders_amount_interval) {
                                        case '4':
                                            $orders = $this->getOrdersIdByDateAndState(date('Y-m-d', strtotime('first day of last month')), date('Y-m-d', strtotime('last day of last month')), array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                            break;
                                    }
                                } else {
                                    // Condition not configured correctly
                                    break;
                                }

                                if ((int)$condition->customer_orders_amount_orders && (count($orders) > (int)$condition->customer_orders_amount_orders)) {
                                    $orders = array_slice($orders, -(int)$condition->customer_orders_amount_orders, (int)$condition->customer_orders_amount_orders, true);
                                }

                                foreach ($orders as $order) {
                                    $totalOrder = 0;

                                    $order = new Order((int)$order);

                                    // If there is any product filter in condition
                                    if ($condition->filter_by_product
                                          || $condition->filter_by_attribute
                                          || $condition->filter_by_feature
                                          || $condition->filter_by_category
                                          || $condition->filter_by_supplier
                                          || $condition->filter_by_manufacturer
                                          || $condition->filter_by_price
                                          || $condition->filter_by_stock) {
                                        $orderProducts = $order->getProducts();
                                        $productsFiltered = $this->filterProducts($orderProducts, $condition, false);
                                        if ($productsFiltered) {
                                            foreach ($productsFiltered as $productFiltered) {
                                                if ((int)$condition->customer_orders_amount_tax) {
                                                    $totalOrder += $productFiltered['unit_price_tax_incl'] * (int)$productFiltered['product_quantity'];
                                                } else {
                                                    $totalOrder += $productFiltered['unit_price_tax_excl'] * (int)$productFiltered['product_quantity'];
                                                }
                                            }

                                            /** Remove refunds */
                                            if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
                                                $orderDetails = $order->getOrderDetailList();
                                                foreach ($orderDetails as $orderDetailData) {
                                                    if ((int)$orderDetailData['product_id'] !== (int)$productFiltered['id_product'] || (int)$orderDetailData['product_attribute_id'] !== (int)$productFiltered['product_attribute_id']) {
                                                        continue;
                                                    }
                                                    if ((int)$condition->customer_orders_amount_tax) {
                                                        $totalOrder -= $orderDetailData['total_refunded_tax_incl'];
                                                    } else {
                                                        $totalOrder -= $orderDetailData['total_refunded_tax_excl'];
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if ((int)$condition->customer_orders_amount_tax) {
                                            $totalOrder += $order->total_products_wt;
                                            if ((int)$condition->customer_orders_amount_shipping) {
                                                $totalOrder += $order->total_shipping_tax_incl;
                                            }
                                        } else {
                                            $totalOrder += $order->total_products;
                                            if ((int)$condition->customer_orders_amount_shipping) {
                                                $totalOrder += $order->total_shipping_tax_excl;
                                            }
                                        }

                                        /** Remove discounts */
                                        if (!(int)$condition->customer_orders_amount_discount) {
                                            if ((int)$condition->customer_orders_amount_tax) {
                                                $totalOrder -= $order->total_discounts_tax_incl;
                                            } else {
                                                $totalOrder -= $order->total_discounts_tax_excl;
                                            }
                                        }

                                        /** Remove refunds */
                                        $orderDetails = $order->getOrderDetailList();
                                        foreach ($orderDetails as $orderDetailData) {
                                            if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
                                                if ((int)$condition->customer_orders_amount_tax) {
                                                    $totalOrder -= $orderDetailData['total_refunded_tax_incl'];
                                                } else {
                                                    $totalOrder -= $orderDetailData['total_refunded_tax_excl'];
                                                }
                                            } else {
                                                $orderSlip = OrderSlip::getProductSlipResume($orderDetailData['id_order_detail']);
                                                if ((int)$condition->customer_orders_amount_tax && $orderSlip['amount_tax_incl']) {
                                                    $totalOrder -= $orderSlip['amount_tax_incl'];
                                                } elseif($orderSlip['amount_tax_excl']) {
                                                    $totalOrder -= $orderSlip['amount_tax_excl'];
                                                }
                                            }
                                        }
                                    }

                                    // We convert amount to default currency using its own conversion rate
                                    $totalAmount += self::convertPriceWithConversionRate($totalOrder, $order->conversion_rate, true);
                                }

                                if ((int)$condition->customer_orders_amount_currency != Configuration::get('PS_CURRENCY_DEFAULT')) {
                                    $totalAmount = self::convertPriceFull($totalAmount, new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')), new Currency((int)$condition->customer_orders_amount_currency), false);
                                }

                                $groupValidationPassed = $this->compareValue((int)$condition->customer_orders_amount_operator, $totalAmount, $condition->customer_orders_amount);
                            }

                            break;

                        /**
                         * Only for first order
                         *
                         * Check if it's/or not the customer's first order
                         *
                         */
                        case 6:
                            if ((int)$this->context->cart->id_customer) {
                                $sql = 'SELECT COUNT(*) as nb
                                    FROM `'._DB_PREFIX_.'orders` o
                                    LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`)
                                    WHERE `email` = \''.$this->context->customer->email.'\'';

                                $firstOrder = Db::getInstance()->getValue($sql);

                                if (($condition->customer_first_order && !$firstOrder) || (!$condition->customer_first_order && $firstOrder)) {
                                    $groupValidationPassed = true;
                                }
                            } elseif ($condition->customer_first_order) {
                                $groupValidationPassed = true;
                            }

                            break;

                        /**
                         * Total cart amount
                         *
                         * Get the cart amount or only the amount from products without special price.
                         * Add shipping cost. Substract gift products.
                         *
                         */
                        case 8:
                            if ($condition->cart_amount > 0) {
                                $cartAmount = $condition->cart_amount;
                                if ((int)$condition->cart_amount_currency != $this->context->currency->id) {
                                    $cartAmount = self::convertPriceFull($cartAmount, new Currency((int)$condition->cart_amount_currency), $this->context->currency, false);
                                }

                                /** Get the cart amount or only the amount from products without special price. */
                                if (!(int)$condition->apply_discount_to_special) {
                                    $cartProducts = $this->context->cart->getProducts();
                                    $cartTotal = 0;
                                    foreach ($cartProducts as $cartProduct) {
                                        if (!($cartProduct['on_sale']
                                            || Product::getPriceStatic($cartProduct['id_product'], (int)$condition->cart_amount_tax, (isset($cartProduct['id_product_attribute']) ? (int)$cartProduct['id_product_attribute'] : null), 6, null, true, true, $cartProduct['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, ((Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') ? (int)$this->context->cart->id_address_invoice : (int)$cartProduct['id_address_delivery']), $specific_price_output, (int)$condition->cart_amount_tax, true, $virtual_context, true, $cartProduct['id_customization']) > 0)) {
                                            $cartTotal += Product::getPriceStatic($cartProduct['id_product'], (int)$condition->cart_amount_tax, (isset($cartProduct['id_product_attribute']) ? (int)$cartProduct['id_product_attribute'] : null), 6, null, false, true, $cartProduct['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, ((Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') ? (int)$this->context->cart->id_address_invoice : (int)$cartProduct['id_address_delivery']), $specific_price_output, (int)$condition->cart_amount_tax, true, $virtual_context, true, $cartProduct['id_customization'])*$cartProduct['cart_quantity'];
                                        }
                                    }
                                } else {
                                    $cartTotal = $this->context->cart->getOrderTotal((int)$condition->cart_amount_tax, Cart::ONLY_PRODUCTS);
                                    //Flush cache
                                    $this->clearCartCache();
                                }

                                /** Add shipping cost */
                                if ((int)$condition->cart_amount_shipping) {
                                    $cartTotal += $this->context->cart->getOrderTotal($condition->cart_amount_tax, Cart::ONLY_SHIPPING);
                                    //Flush cache
                                    $this->clearCartCache();
                                }

                                /** Remove discounts */
                                if (!(int)$condition->cart_amount_discount) {
                                    $cartTotal -= $this->context->cart->getOrderTotal($condition->cart_amount_tax, Cart::ONLY_DISCOUNTS);
                                    //Flush cache
                                    $this->clearCartCache();
                                } else {
                                    $cartTotal -= $this->getGiftProductsValue($condition->cart_amount_tax);
                                }

                                $cartTotal = Tools::ps_round($cartTotal, (int)$this->context->currency->decimals * 2);

                                $groupValidationPassed = $this->compareValue((int)$condition->cart_amount_operator, $cartTotal, $cartAmount);
                            }

                            break;

                        /**
                         * Cart weight
                         *
                         * Check the cart weight
                         *
                         */
                        case 9:
                            if ($condition->cart_weight > 0) {
                                $cartWeight = $this->context->cart->getTotalWeight();

                                $groupValidationPassed = $this->compareValue((int)$condition->cart_weight_operator, $cartWeight, $condition->cart_weight);
                            }

                            break;

                        /**
                         * Products in the cart
                         *
                         * Get all products from the cart. Remove those which don't meet the filters selected at the condition.
                         *
                         */
                        case 10:
                            $cartProducts = $this->context->cart->getProducts();
                            $cartProductsFiltered = $this->filterProducts($cartProducts, $condition);

                            if (!$cartProductsFiltered) {
                                $groupValidationPassed = true;

                                /** Quantity of selected products in cart */
                                $groupValidationPassed &= $this->compareValue((int)$condition->products_nb_operator, (int)0, (int)$condition->products_nb);

                                /** Number of different products from selected products in cart */
                                $groupValidationPassed &= $this->compareValue((int)$condition->products_nb_dif_operator, 0, (int)$condition->products_nb_dif);

                                /** Amount of selected products in cart */
                                if ((int)$condition->products_amount_currency != $this->context->currency->id) {
                                    $conditionProductsAmount = self::convertPriceFull($condition->products_amount, new Currency((int)$condition->products_amount_currency), $this->context->currency, false);
                                } else {
                                    $conditionProductsAmount = $condition->products_amount;
                                }
                                $groupValidationPassed &= $this->compareValue((int)$condition->products_operator, 0, $conditionProductsAmount);

                                /** Number of products from the same category in cart */
                                $groupValidationPassed &= $this->compareValue((int)$condition->products_nb_dif_cat_operator, 0, (int)$condition->products_nb_dif_cat);

                                if (!$groupValidationPassed) {
                                    break;
                                }
                            }

                            /**
                             * Check if all products from the cart must met the condition
                             */
                            if ((int)$condition->products_all_met && (count($cartProducts) != count($cartProductsFiltered))) {
                                break;
                            }

                            /** Quantity of selected products in cart */
                            if ((int)$condition->products_nb) {
                                $condition->group_products_by = 'all';
                                $productsGrouped = $this->groupProducts((int)$this->context->cart->id, $cartProductsFiltered, $condition);

                                $groupValidationPassed |= $this->compareValue((int)$condition->products_nb_operator, (int)$productsGrouped[0]['cart_quantity'], (int)$condition->products_nb);
                                if (!$groupValidationPassed) {
                                    break;
                                }
                            } else {
                                $groupValidationPassed = true;
                            }

                            /** Number of different products from selected products in cart */
                            if ((int)$condition->products_nb_dif) {
                                $condition->group_products_by = 'product';
                                $productsGrouped = $this->groupProducts((int)$this->context->cart->id, $cartProductsFiltered, $condition);

                                $groupValidationPassed &= $this->compareValue((int)$condition->products_nb_dif_operator, (int)count($productsGrouped), (int)$condition->products_nb_dif);
                            }

                            /** Amount of selected products in cart */
                            if ((int)$condition->products_amount) {
                                $cartAmount = 0;

                                foreach ($cartProductsFiltered as $cartProductFiltered) {
                                    if (isset($cartProductFiltered['productmega'])) {
                                        $productPrice = ((int)$condition->products_amount_tax ? $cartProductFiltered['price_without_reduction'] : $cartProductFiltered['price_with_reduction_without_tax'])*$cartProductFiltered['cart_quantity'];
                                    } else {
                                        // TODO: Error with Scalapay in multistore. It doesn't set context shop correctly
                                        // Move ALL getPriceStatic to a function with the exceptions
                                        $productPrice = Product::getPriceStatic($cartProductFiltered['id_product'], (int)$condition->products_amount_tax, (isset($cartProductFiltered['id_product_attribute']) ? (int)$cartProductFiltered['id_product_attribute'] : null), 6, null, false, true, $cartProductFiltered['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, ((Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') ? (int)$this->context->cart->id_address_invoice : (int)$cartProductFiltered['id_address_delivery']), $specific_price_output, (int)$condition->products_amount_tax, true, $virtual_context, true, $cartProductFiltered['id_customization'])*$cartProductFiltered['cart_quantity'];
                                        if (!$productPrice) {
                                            $productPrice = (int)$condition->products_amount_tax ? $cartProductFiltered['total_wt'] : $cartProductFiltered['total'];
                                        }
                                    }

                                    $cartAmount += $productPrice;
                                }

                                if ((int)$condition->products_amount_currency != $this->context->currency->id) {
                                    $conditionProductsAmount = self::convertPriceFull($condition->products_amount, new Currency((int)$condition->products_amount_currency), $this->context->currency, false);
                                } else {
                                    $conditionProductsAmount = $condition->products_amount;
                                }

                                $groupValidationPassed &= $this->compareValue((int)$condition->products_operator, $cartAmount, $conditionProductsAmount);
                            }

                            /** Number of products from the same category in cart */
                            if ((int)$condition->products_nb_dif_cat) {
                                $condition->group_products_by = 'category';
                                $productsGrouped = $this->groupProducts($this->context->cart->id, $cartProductsFiltered, $condition);

                                $groupCategoryValidationPassed = false;
                                foreach ($productsGrouped as $productGrouped) {
                                    $groupCategoryValidationPassed |= $this->compareValue((int)$condition->products_nb_dif_cat_operator, (int)$productGrouped['cart_quantity'], (int)$condition->products_nb_dif_cat);
                                }

                                $groupValidationPassed &= $groupCategoryValidationPassed;
                            }

                            break;

                        /**
                         * Delivery country
                         *
                         * Get the order address delivery and check if it matches condition country
                         */
                        case 11:
                            if ((int)$this->context->cart->id_address_delivery) {
                                $address = new Address($this->context->cart->id_address_delivery);
                                $conditionCountry = $condition->getSelectedAssociatedRestrictions('country');

                                if (count($conditionCountry['selected'])) {
                                    if (in_array($address->id_country, array_column($conditionCountry['selected'], 'id_country'))) {
                                        $groupValidationPassed = true;
                                        break;
                                    }
                                }
                            }

                            break;

                        /**
                         * Carrier
                         *
                         * Check if carrier matches with selected
                         */
                        case 12:
                            if ($id_carrier = $this->getCarrier($this->context)) {
                                $carrier = new Carrier((int)$id_carrier);
                                $conditionCarriers = $condition->getSelectedAssociatedRestrictions('carrier');

                                if (count($conditionCarriers['selected'])) {
                                    if (in_array($carrier->id_reference, array_column($conditionCarriers['selected'], 'id_carrier'))) {
                                        $groupValidationPassed = true;
                                    }
                                }
                            }

                            break;

                        /**
                         * Group selection
                         *
                         * Check if customer belongs to selected groups
                         */
                        case 13:
                            $conditionGroups = $condition->getSelectedAssociatedRestrictions('group');
                            if (count($conditionGroups['selected'])) {
                                if ((int)$this->context->cart->id_customer && $condition->customer_default_group) {
                                    $customer = new Customer((int)$this->context->cart->id_customer);
                                    if (in_array((int)$customer->id_default_group, array_column($conditionGroups['selected'], 'id_group'))) {
                                        $groupValidationPassed = true;
                                        break;
                                    }
                                } else {
                                    $customerGroups = Customer::getGroupsStatic((int)$this->context->cart->id_customer);
                                    foreach ($customerGroups as $customerGroup) {
                                        if (in_array($customerGroup, array_column($conditionGroups['selected'], 'id_group'))) {
                                            $groupValidationPassed = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            break;

                        /**
                         * Delivery zone
                         *
                         * Check if delivery zone matches with selected
                         */
                        case 18:
                            if ($this->context->cart->id_address_delivery) {
                                $id_zone = Address::getZoneById($this->context->cart->id_address_delivery);
                                $conditionZones = $condition->getSelectedAssociatedRestrictions('zone');

                                if (count($conditionZones['selected'])) {
                                    if (in_array($id_zone, array_column($conditionZones['selected'], 'id_zone'))) {
                                        $groupValidationPassed = true;
                                    }
                                }
                            }

                            break;

                        /**
                         * Membership
                         *
                         * Compare number of days of membership with defined
                         */
                        case 19:
                            if ((int)$this->context->cart->id_customer) {
                                $now = new DateTime(date('Y-m-d H:i:s'));
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                $diff = $now->diff(new DateTime(date($customer->date_add)))->format("%a");

                                $groupValidationPassed = $this->compareValue((int)$condition->customer_membership_operator, (int)$diff, (int)$condition->customer_membership);
                            }

                            break;

                        /**
                         * Birthday
                         *
                         * Get day/month of customer's birthday and compare with current day
                         */
                        case 20:
                            if ((int)$this->context->cart->id_customer) {
                                $now = date('m-d');
                                $customer = new Customer((int)$this->context->cart->id_customer);

                                if ($condition->customer_birthday && $now == date('m-d', strtotime($customer->birthday))) {
                                    $groupValidationPassed = true;
                                } elseif (!$condition->customer_birthday && $now != date('m-d', strtotime($customer->birthday))) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /*
                         * By gender
                         */
                        case 21:
                            if ((int)$this->context->cart->id_customer) {
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                $conditionGenders = $condition->getSelectedAssociatedRestrictions('gender');

                                if ($customer->id_gender) {
                                    if (count($conditionGenders['selected'])) {
                                        if (in_array($customer->id_gender, array_column($conditionGenders['selected'], 'id_gender'))) {
                                            $groupValidationPassed = true;
                                        }
                                    }
                                }
                            }

                            break;

                        /*
                         * By currency
                         */
                        case 22:
                            $conditionCurrencies = $condition->getSelectedAssociatedRestrictions('currency');

                            if (count($conditionCurrencies['selected'])) {
                                if (in_array($this->context->cart->id_currency, array_column($conditionCurrencies['selected'], 'id_currency'))) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /**
                         * Customer age
                         *
                         * Get day/month of customer's birthday and compare if it's between defined age
                         */
                        case 23:
                            if ((int)$this->context->cart->id_customer) {
                                $now = date('m-d');
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                $birthDate = $customer->birthday;
                                if ($birthDate && $birthDate != '0000-00-00') {
                                    $age = date_diff(date_create($birthDate), date_create('now'))->y;
                                    if ($age >= $condition->customer_years_from && $age <= $condition->customer_years_to) {
                                        $groupValidationPassed = true;
                                    }
                                }
                            }

                            break;

                        /**
                         * Delivery state
                         *
                         * Check if delivery state matches with selected
                         */
                        case 24:
                            if ((int)$this->context->cart->id_address_delivery) {
                                $address = new Address($this->context->cart->id_address_delivery);
                                $conditionState = $condition->getSelectedAssociatedRestrictions('state');

                                if (count($conditionState['selected'])) {
                                    if (in_array($address->id_state, array_column($conditionState['selected'], 'id_state'))) {
                                        $groupValidationPassed = true;
                                        break;
                                    }
                                }
                            }

                            break;

                        /**
                         * Customer has vat
                         *
                         * Get tax type for a product. If is higher than 0, customer has VAT. Please note that we take into consideration
                         * ID address, and that if one product has VAT we suppose that all products have VAT
                         */
                        case 25:
                            foreach ($this->context->cart->getProducts() as $product) {
                                if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                                    $address_id = (int)$this->context->cart->id_address_invoice;
                                } else {
                                    $address_id = (int)$product['id_address_delivery'];
                                } // Get delivery address of the product from the cart

                                if (!Address::addressExists($address_id)) {
                                    $address_id = null;
                                }

                                if (Tax::getProductTaxRate((int)$product['id_product'], (int)$address_id)) {
                                    $groupValidationPassed = true;
                                    break;
                                }
                            }

                            break;

                        /*
                         * Day of the week
                         *
                         * Get today and check if it's valid
                         */
                        case 26:
                            $schedule = json_decode($condition->schedule);
                            $dayOfWeek = date('w') - 1;
                            if ($dayOfWeek < 0) {
                                $dayOfWeek = 6;
                            }

                            if (is_array($schedule)) {
                                if (is_object($schedule[$dayOfWeek]) && $schedule[$dayOfWeek]->isActive === true) {
                                    if ($schedule[$dayOfWeek]->timeFrom <= date('H:i') && $schedule[$dayOfWeek]->timeTill > date('H:i')) {
                                        $groupValidationPassed = true;
                                    }
                                }
                            }

                            break;

                        /*
                         * URL string
                         */
                        case 27:
                            $qdp_url_string = json_decode(Context::getContext()->cookie->qdp_url_string, true);
                            if (isset($qdp_url_string[(int)$this->id_quantity_discount_rule])) {
                                $groupValidationPassed = true;
                            }

                            break;
                    }

                    if (!$groupValidationPassed) {
                        break;
                    }
                }

                /**
                 * Logical OR between each group of conditions
                 *
                 * If any of the group condition is valid, then rule must be applied
                 */
                if ($groupValidationPassed) {
                    $result = true;
                    if ($saveInCache) {
                        Cache::store($cache_key, $result);
                    }
                    return $result;
                }
            }
        } else {
            return Cache::retrieve($cache_key);
        }

        $result = false;
        if ($saveInCache) {
            Cache::store($cache_key, $result);
        }

        return $result;
    }

    public function validateCartRuleForMessages($id_product = null, $validateProducts = true)
    {
        $groupConditions = $this->getGroups(true);

        $groupValidationPassed = true;

        if ($groupConditions) {
            foreach ($groupConditions as $groupCondition) {
                $conditions = $groupCondition->getConditions();

                if (!$conditions) {
                    continue;
                }

                $groupValidationPassed = false;
                $case10 = false;
                $excludingCondition = false;

                foreach ($conditions as $condition) {
                    $groupValidationPassed = false;
                    $condition = new QuantityDiscountRuleCondition($condition['id_quantity_discount_rule_condition']);

                    switch ((int)$condition->id_type) {
                        /**
                         * Limit to a single customer
                         *
                         * Check if customer matches the condition customer
                         */
                        case 1:
                            if ((int)$this->context->cart->id_customer == (int)$condition->id_customer) {
                                $groupValidationPassed = true;
                            }

                            break;

                        /**
                         * Customer must be suscribed to newsletter
                         *
                         * Check if customer is/or not subscribed to newsletter
                         */
                        case 2:
                            if ((int)$this->context->cart->id_customer) {
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                if ((int)$customer->newsletter == (int)$condition->customer_newsletter) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /**
                         * Customer signed up between a date
                         *
                         * If condition date is by days, substract the number of days to now and check if customer subscribed before this date
                         * If condition date is by interval, check if customer signed up is between these dates
                         */
                        case 3:
                            $time_now = date('Y-m-d');

                            if ((int)$this->context->cart->id_customer) {
                                $customer = new Customer((int)$this->context->cart->id_customer);

                                if ($condition->customer_signedup_date_to == '0000-00-00 00:00:00') {
                                    $condition->customer_signedup_date_to = $time_now;
                                }

                                if (strtotime($condition->customer_signedup_date_from) <= strtotime($customer->date_add) &&
                                    strtotime($condition->customer_signedup_date_to) >= strtotime($customer->date_add)) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /**
                         * Customer and orders done
                         *
                         * If condition date is by days, get the orders from this day onwards.
                         * If condition date is by interval, get the orders from this interval.
                         */
                        case 4:
                            $time_now = date('Y-m-d H:i:s');

                            if ((int)$this->context->cart->id_customer) {
                                $orderStates = $condition->getSelectedAssociatedRestrictions('order_state');
                                if ($condition->customer_orders_nb_days > 0) {
                                    $orders = $this->getOrdersIdByDateAndState(date('Y-m-d H:i:s', (strtotime("-".$condition->customer_orders_nb_days." days", strtotime($time_now)))), $time_now, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                } else {
                                    if ($condition->customer_orders_nb_date_to == '0000-00-00 00:00:00') {
                                        $condition->customer_orders_nb_date_to = $time_now;
                                    }

                                    $orders = $this->getOrdersIdByDateAndState($condition->customer_orders_nb_date_from, $condition->customer_orders_nb_date_to, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                }

                                $groupValidationPassed = $this->compareValue((int)$condition->customer_orders_nb_operator, (int)count($orders), (int)$condition->customer_orders_nb);

                                if ($groupValidationPassed) {
                                    $productsTotal = 0;

                                    if ($condition->filter_by_product
                                        || $condition->filter_by_category
                                        || $condition->filter_by_attribute
                                        || $condition->filter_by_supplier
                                        || $condition->filter_by_manufacturer) {
                                        foreach ($orders as $order) {
                                            $order = new Order($order);
                                            $orderProducts = $order->getProducts();

                                            if ($productsFiltered = $this->filterProducts($orderProducts, $condition)) {
                                                foreach ($productsFiltered as $productFiltered) {
                                                    $productsTotal += (int)$productFiltered['product_quantity'];
                                                }
                                            }
                                        }
                                    }

                                    $groupValidationPassed = $this->compareValue((int)$condition->customer_orders_nb_prod_operator, (int)($productsTotal), (int)$condition->customer_orders_nb_prod);
                                }
                            }

                            break;

                        /**
                         * Customer and amount spent
                         *
                         * If condition date is by days, get the orders from this day onwards.
                         * If condition date is by interval, get the orders from this interval.
                         *
                         * Acumulate amount and convert to currency
                         */
                        case 5:
                            if ((int)$this->context->cart->id_customer && (int)$condition->customer_orders_amount_orders > 0) {
                                $time_now = date('Y-m-d H:i:s');
                                $totalAmount = 0;
                                $orders = array();
                                $orderStates = $condition->getSelectedAssociatedRestrictions('order_state');
                                if ($condition->customer_orders_amount_days > 0) {
                                    $orders = $this->getOrdersIdByDateAndState(date('Y-m-d H:i:s', (strtotime("-".$condition->customer_orders_amount_days." days", strtotime($time_now)))), $time_now, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                } else {
                                    if ($condition->customer_orders_amount_date_to == '0000-00-00 00:00:00') {
                                        $condition->customer_orders_amount_date_to = $time_now;
                                    }
                                    $orders = $this->getOrdersIdByDateAndState($condition->customer_orders_amount_date_from, $condition->customer_orders_amount_date_to, array_column($orderStates['selected'], 'id_order_state'), (int)$this->context->cart->id_customer);
                                }

                                if ((int)$condition->customer_orders_amount_orders && (count($orders) > (int)$condition->customer_orders_amount_orders)) {
                                    $orders = array_slice($orders, -(int)$condition->customer_orders_amount_orders, (int)$condition->customer_orders_amount_orders, true);
                                }

                                foreach ($orders as $order) {
                                    $totalOrder = 0;

                                    $order = new Order((int)$order);
                                    // If there is any product filter in condition
                                    if ($condition->filter_by_product
                                          || $condition->filter_by_attribute
                                          || $condition->filter_by_feature
                                          || $condition->filter_by_category
                                          || $condition->filter_by_supplier
                                          || $condition->filter_by_manufacturer
                                          || $condition->filter_by_price
                                          || $condition->filter_by_stock) {
                                        $orderProducts = $order->getProducts();
                                        $productsFiltered = $this->filterProducts($orderProducts, $condition, false);
                                        if ($productsFiltered) {
                                            foreach ($productsFiltered as $productFiltered) {
                                                if ((int)$condition->customer_orders_amount_tax) {
                                                    $totalOrder += $productFiltered['unit_price_tax_incl'] * (int)$productFiltered['product_quantity'];
                                                } else {
                                                    $totalOrder += $productFiltered['unit_price_tax_excl'] * (int)$productFiltered['product_quantity'];
                                                }
                                            }
                                        }
                                    } else {
                                        if ((int)$condition->customer_orders_amount_tax) {
                                            $totalOrder += $order->total_products_wt;
                                            if ((int)$condition->customer_orders_amount_shipping) {
                                                $totalOrder += $order->total_shipping_tax_incl;
                                            }
                                        } else {
                                            $totalOrder += $order->total_products;
                                            if ((int)$condition->customer_orders_amount_shipping) {
                                                $totalOrder += $order->total_shipping_tax_excl;
                                            }
                                        }

                                        /** Remove discounts */
                                        if (!(int)$condition->customer_orders_amount_discount) {
                                            if ((int)$condition->customer_orders_amount_tax) {
                                                $totalOrder -= $order->total_discounts_tax_incl;
                                            } else {
                                                $totalOrder -= $order->total_discounts_tax_excl;
                                            }
                                        }
                                    }

                                    // We convert amount to default currency using its own conversion rate
                                    $totalAmount += self::convertPriceWithConversionRate($totalOrder, $order->conversion_rate, true);
                                }

                                if ((int)$condition->customer_orders_amount_currency != Configuration::get('PS_CURRENCY_DEFAULT')) {
                                    $totalAmount = self::convertPriceFull($totalAmount, new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')), new Currency((int)$condition->customer_orders_amount_currency), false);
                                }

                                $groupValidationPassed = $this->compareValue((int)$condition->customer_orders_amount_operator, $totalAmount, $condition->customer_orders_amount);
                            }

                            break;

                        /**
                         * Only for first order
                         *
                         * Check if it's/or not the customer's first order
                         *
                         */
                        case 6:
                            if ((int)$this->context->cart->id_customer) {
                                $sql = 'SELECT COUNT(*) as nb
                                        FROM `'._DB_PREFIX_.'orders` o
                                        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`)
                                        WHERE `email` = \''.$this->context->customer->email.'\'';

                                $firstOrder = Db::getInstance()->getValue($sql);

                                if (($condition->customer_first_order && !$firstOrder) || (!$condition->customer_first_order && $firstOrder)) {
                                    $groupValidationPassed = true;
                                }
                            } elseif ($condition->customer_first_order) {
                                $groupValidationPassed = true;
                            }

                            break;

                        case 8:
                        case 9:
                            $groupValidationPassed = true;
                            break;
                        case 11:
                            if ((int)$this->context->cart->id_address_delivery) {
                                $address = new Address($this->context->cart->id_address_delivery);
                                $conditionCountry = $condition->getSelectedAssociatedRestrictions('country');

                                if (count($conditionCountry['selected'])) {
                                    if (in_array($address->id_country, array_column($conditionCountry['selected'], 'id_country'))) {
                                        $groupValidationPassed = true;
                                        break;
                                    }
                                }
                            }
                            break;

                        case 12:
                            $groupValidationPassed = true;
                            break;

                        case 18:
                            if ($this->context->cart->id_address_delivery) {
                                $id_zone = Address::getZoneById($this->context->cart->id_address_delivery);
                                $conditionZones = $condition->getSelectedAssociatedRestrictions('zone');

                                if (count($conditionZones['selected'])) {
                                    if (in_array($id_zone, array_column($conditionZones['selected'], 'id_zone'))) {
                                        $groupValidationPassed = true;
                                        break;
                                    }
                                }
                            }

                            break;

                        case 10:
                            if (!$validateProducts) {
                                $groupValidationPassed = true;
                                break;
                            }

                            $id_product = isset($id_product) ? $id_product : Tools::getValue('id_product');
                            if (isset($id_product) && $this->productIsInFilter($id_product, $condition)) {
                                $groupValidationPassed = true;
                                $case10 = true;
                            }

                            break;

                        /**
                         * Group selection
                         *
                         * Check if customer belongs to selected groups
                         */
                        case 13:
                            $conditionGroups = $condition->getSelectedAssociatedRestrictions('group');
                            $excludingCondition = true;
                            if ((int)$this->context->cart->id_customer && $condition->customer_default_group) {
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                if (count($conditionGroups['selected'])) {
                                    if (in_array((int)$customer->id_default_group, array_column($conditionGroups['selected'], 'id_group'))) {
                                        $groupValidationPassed = true;
                                        break;
                                    }
                                }
                            } else {
                                $customerGroups = Customer::getGroupsStatic((int)$this->context->cart->id_customer);
                                if (count($conditionGroups['selected'])) {
                                    foreach ($customerGroups as $customerGroup) {
                                        if (in_array($customerGroup, array_column($conditionGroups['selected'], 'id_group'))) {
                                            $groupValidationPassed = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            break;

                        /**
                         * Membership
                         *
                         * Compare number of days of membership with defined
                         */
                        case 19:
                            if ((int)$this->context->cart->id_customer) {
                                $now = new DateTime(date('Y-m-d H:i:s'));
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                $diff = $now->diff(new DateTime(date($customer->date_add)))->format("%a");

                                $groupValidationPassed = $this->compareValue((int)$condition->customer_membership_operator, (int)$diff, (int)$condition->customer_membership);
                            }

                            break;

                        /**
                         * Birthday
                         *
                         * Get day/month of customer's birthday and compare with current day
                         */
                        case 20:
                            if ((int)$this->context->cart->id_customer) {
                                $now = date('m-d');
                                $customer = new Customer((int)$this->context->cart->id_customer);

                                if ($now == date('m-d', strtotime($customer->birthday))) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /*
                         * By gender
                         */
                        case 21:
                            if ((int)$this->context->cart->id_customer) {
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                $conditionGenders = $condition->getSelectedAssociatedRestrictions('gender');

                                if ($customer->id_gender) {
                                    if (count($conditionGenders['selected'])) {
                                        if (in_array($customer->id_gender, array_column($conditionGenders['selected'], 'id_gender'))) {
                                            $groupValidationPassed = true;
                                        }
                                    }
                                }
                            }

                            break;

                        /*
                         * By currency
                         */
                        case 22:
                            $conditionCurrencies = $condition->getSelectedAssociatedRestrictions('currency');

                            if (count($conditionCurrencies['selected'])) {
                                if (in_array($this->context->cart->id_currency, array_column($conditionCurrencies['selected'], 'id_currency'))) {
                                    $groupValidationPassed = true;
                                }
                            }

                            break;

                        /**
                         * Customer age
                         */
                        case 23:
                            if ((int)$this->context->cart->id_customer) {
                                $now = date('m-d');
                                $customer = new Customer((int)$this->context->cart->id_customer);
                                $birthDate = $customer->birthday;
                                if ($birthDate && $birthDate != '0000-00-00') {
                                    $age = date_diff(date_create($birthDate), date_create('now'))->y;
                                    if ($age >= $condition->customer_years_from && $age <= $condition->customer_years_to) {
                                        $groupValidationPassed = true;
                                    }
                                }
                            }

                            break;

                        /**
                         * Customer has vat
                         */
                        case 25:
                            foreach ($this->context->cart->getProducts() as $product) {
                                if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                                    $address_id = (int)$this->context->cart->id_address_invoice;
                                } else {
                                    $address_id = (int)$product['id_address_delivery'];
                                } // Get delivery address of the product from the cart

                                if (!Address::addressExists($address_id)) {
                                    $address_id = null;
                                }

                                if (Tax::getProductTaxRate((int)$product['id_product'], (int)$address_id)) {
                                    $groupValidationPassed = true;
                                    break;
                                }
                            }

                            break;

                        /*
                         * Day of the week
                         *
                         * Get today and check if it's valid
                         */
                        case 26:
                            switch (date('w')) {
                                case 1:
                                    if ($condition->monday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;

                                case 2:
                                    if ($condition->tuesday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;

                                case 3:
                                    if ($condition->wednesday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;

                                case 4:
                                    if ($condition->thursday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;

                                case 5:
                                    if ($condition->friday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;

                                case 6:
                                    if ($condition->saturday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;

                                case 0:
                                    if ($condition->sunday) {
                                        $groupValidationPassed = true;
                                    }

                                    break;
                            }

                            break;
                    }

                    if (!$groupValidationPassed) {
                        break;
                    }
                }

                /*if (!$groupValidationPassed) {
                    continue;
                }*/
                //A condition group is valid
                if ($groupValidationPassed) {
                    break;
                }
            }

            if ($excludingCondition) {
                return $groupValidationPassed;
            }

            if (!$groupValidationPassed & !$case10) {
                return false;
            }

            if ($groupValidationPassed & $case10) {
                return true;
            }
        }

        if ($validateProducts) {
            $actions = $this->getActions(true);

            $groupValidationPassed = false;
            foreach ($actions as $action) {
                switch ((int)$action->id_type) {
                    case 6:
                    case 7:
                    case 8:
                    case 12:
                    case 13:
                    case 14:
                    case 15:
                    case 16:
                    case 17:
                    case 18:
                    case 19:
                    case 20:
                    case 21:
                    case 22:
                    case 23:
                    case 27:
                    case 28:
                    case 29:
                    case 31:
                    case 32:
                    case 33:
                    case 34:
                    case 37:
                    case 38:
                    case 39:
                    case 40:
                    case 100:
                    case 101:
                    case 102:
                        $id_product = isset($id_product) ? $id_product : Tools::getValue('id_product');
                        if (isset($id_product) && $this->productIsInFilter($id_product, $action)) {
                            $groupValidationPassed = true;
                        }

                        break;

                    case 30:
                    case 35:
                    case 103:
                    case 107:
                        $id_product = isset($id_product) ? $id_product : Tools::getValue('id_product');
                        if (isset($id_product) && $this->productIsInFilter($id_product, $action, true)) {
                            $groupValidationPassed = true;
                        }

                        break;

                    case 104:
                    case 105:
                    case 106:
                        $groupValidationPassed = false;

                        break;

                    default:
                        $groupValidationPassed = true;

                        break;
                }

                if ($groupValidationPassed) {
                    return $groupValidationPassed;
                }
            }
        }

        return $groupValidationPassed;

    }

    public function getDiscountedAmount($action, $product, $isPercentageAction)
    {
        //$cache_key = 'QuantityDiscountRule::getDiscountedAmount_'.(int)$product['id_product'].'_'.(int)$action->reduction_percent_tax.'_'.(bool)$action->apply_discount_to_regular_price.'_'.(int)$action->reduction_tax;

        //if (!Cache::isStored($cache_key)) {

        $virtual_context = Context::getContext()->cloneContext();

        if ($virtual_context->shop->id != $product['id_shop']) {
            $virtual_context->shop = new Shop((int)$product['id_shop']);
        }

        if (Module::isEnabled('attributewizardpro')) {
            if (!empty($product['instructions_id'])) {
                $productCombinations = array_filter(explode(',', $product['instructions_id']));
                $product['id_product_attribute'] = Product::getIdProductAttributeByIdAttributes($product['id_product'], $productCombinations);
            }
        }

        if ($isPercentageAction) {
            if (version_compare(_PS_VERSION_, '1.6.1', '>=')
                || isset($product['productmega'])) {
                if ((int)$action->reduction_percent_tax) {
                    $suffix = '';
                } else {
                    $suffix = '_without_tax';
                }

                if (isset($product['productmega'])) {
                    $unitDiscount = $product['price_without_reduction'.$suffix] * ($action->reduction_percent / 100);
                    $unitDiscount = max(array(0, $product['price_with_reduction'.$suffix] - ($product['price_without_reduction'.$suffix] - $unitDiscount)));
                } else {
                    if ($action->apply_discount_to_group_price) {
                        if ($action->apply_discount_to_regular_price) {
                            $product['price_with_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, true, $virtual_context, true, $product['id_customization']);
                            $product['price_without_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, false, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, false, $virtual_context, true, $product['id_customization']);
                        } else {
                            $product['price_with_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, true, $virtual_context, true, $product['id_customization']);
                            $product['price_without_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, false, $virtual_context, true, $product['id_customization']);
                        }
                    } else {
                        if ($action->apply_discount_to_regular_price) {
                            $product['price_with_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, !(int)$action->apply_discount_to_group_price, $virtual_context, true, $product['id_customization']);
                            $product['price_without_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, false, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, !(int)$action->apply_discount_to_group_price, $virtual_context, true, $product['id_customization']);
                        } else {
                            $product['price_with_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, !(int)$action->apply_discount_to_group_price, $virtual_context, true, $product['id_customization']);
                            $product['price_without_reduction'.$suffix] = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, !(int)$action->apply_discount_to_group_price, $virtual_context, true, $product['id_customization']);
                        }
                    }

                    $unitDiscount = $product['price_without_reduction'.$suffix] * ($action->reduction_percent / 100);
                    $unitDiscount = max(array(0, $product['price_with_reduction'.$suffix] - ($product['price_without_reduction'.$suffix] - $unitDiscount)));
                }
            } else {
                $productPrice = Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, (bool)!$action->apply_discount_to_regular_price, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, true, $virtual_context, true, $product['id_customization']);

                $unitDiscount = $productPrice*($action->reduction_percent/100);

                if ((bool)$action->apply_discount_to_regular_price) {
                    $unitDiscount = max(array(0, Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, true, $virtual_context, true, $product['id_customization']) - (Product::getPriceStatic($product['id_product'], (int)$action->reduction_percent_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, false, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_percent_tax, true, $virtual_context, true, $product['id_customization']) - $unitDiscount)));
                }
            }
        } else {
            if (version_compare(_PS_VERSION_, '1.6.1', '>=')
                || isset($product['productmega'])) {
                if ((int)$action->reduction_tax) {
                    if (isset($product['price_with_reduction'])) {
                        $unitDiscount = $product['price_with_reduction'];
                    } elseif (isset($product['price_with_reduction_tax_incl'])) {
                        $unitDiscount = $product['price_with_reduction_tax_incl'];
                    } else {
                        $unitDiscount = Product::getPriceStatic($product['id_product'], (int)$action->reduction_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_tax, true, $this->context, true, $product['id_customization']);
                    }
                } else {
                    if (isset($product['price_with_reduction_without_tax'])) {
                        $unitDiscount = $product['price_with_reduction_without_tax'];
                    } elseif (isset($product['price_with_reduction_tax_excl'])) {
                        $unitDiscount = $product['price_with_reduction_tax_excl'];
                    } else {
                        $unitDiscount = Product::getPriceStatic($product['id_product'], (int)$action->reduction_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_tax, true, $this->context, true, $product['id_customization']);
                    }
                }
            } else {
                $unitDiscount = Product::getPriceStatic($product['id_product'], (int)$action->reduction_tax, (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, $product['id_address'], $specific_price_output, (int)$action->reduction_tax, true, $this->context, true, $product['id_customization']);
            }
        }

        //    Cache::store($cache_key, $unitDiscount);

        return $unitDiscount;
        //} else {
        //    return Cache::retrieve($cache_key);
        //}
    }

    public function compatibleCartRules()
    {
        if (!$this->compatible_cart_rules) {
            $cartRules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_ALL, false);
            $quantityDiscountRulesAtCart = self::getQuantityDiscountRulesAtCart((int)$this->context->cart->id);

            if (is_array($cartRules) && is_array($quantityDiscountRulesAtCart) && count($cartRules) > count($quantityDiscountRulesAtCart)) {
                return false;
                //return Tools::displayError('This voucher is not combinable with an other voucher already in your cart');
            }
        }

        return true;
    }

    protected function compareValue($operator, $a, $b)
    {
        switch ((int)$operator) {
            case 0:
                if ($a < $b) {
                    return false;
                }
                break;
            case 1:
                if ($a != $b) {
                    return false;
                }
                break;
            case 2:
                if ($a > $b) {
                    return false;
                }
                break;
        }

        return true;
    }

    protected function filterProducts($cartProducts, $object, $use_cache = true)
    {
        $virtual_context = Context::getContext()->cloneContext();

        if (!is_array($cartProducts) || empty($cartProducts) || !is_object($object)) {
            return;
        }

        $backtrace = version_compare(PHP_VERSION, '5.3.6', '>=') ? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : debug_backtrace();
        if ($this->array_search_partial(array_column($backtrace, 'file'), 'hsmultiaccessoriespro')) {
            $use_cache = false;
        }

        $cache_key = 'QuantityDiscountRule::filterProducts_'.get_class($object).'_'.(int)$object->getId();
        if (!Cache::isStored($cache_key) || !$use_cache) {
            $cartRules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT, false);

            if ($object->filter_by_product) {
                $restrictionProducts = $object->getSelectedAssociatedRestrictions('product');
            }

            if ($object->filter_by_category) {
                $restrictionCategories = $object->getSelectedAssociatedRestrictions('category');
            }

            if ($object->filter_by_attribute) {
                $restrictionAttributes = $object->getSelectedAssociatedRestrictions('attribute');
            }

            if ($object->filter_by_feature) {
                $restrictionFeatures = $object->getSelectedAssociatedRestrictions('feature');
            }

            if ($object->filter_by_supplier) {
                $restrictionSuppliers = $object->getSelectedAssociatedRestrictions('supplier');
            }

            if ($object->filter_by_manufacturer) {
                $restrictionManufacturers = $object->getSelectedAssociatedRestrictions('manufacturer');
            }

            foreach ($cartProducts as $key => $cartProduct) {
                // Create 1 product for each productmega
                if (isset($cartProduct['productmega'])) {
                    foreach ($cartProduct['productmega'] as $productmega) {
                        $cartProduct['productmega'] = array();
                        $cartProduct['productmega'][] = $productmega;
                        $cartProducts[] = $cartProduct;
                    }

                    unset($cartProducts[$key]);
                }
            }

            foreach ($cartProducts as $key => $cartProduct) {
                /* Remove gift products */
                foreach ($cartRules as $cartRule) {
                    if ($cartRule['gift_product']) {
                        if (empty($cartProduct['gift']) && $cartProduct['id_product'] == $cartRule['gift_product'] && $cartProduct['id_product_attribute'] == $cartRule['gift_product_attribute']) {
                            if ($cartProduct['cart_quantity'] > 1) {
                                $cartProducts[$key]['cart_quantity']--;
                            } else {
                                unset($cartProducts[$key]);
                            }
                        }
                    }
                }
            }

            $productsBeforeFilter = count($cartProducts);

            foreach ($cartProducts as $key => &$cartProduct) {
                /** Check product */
                if ($object->filter_by_product && (!isset($restrictionProducts['selected']) || !in_array((int)$cartProduct['id_product'], array_column($restrictionProducts['selected'], 'id_product')))) {
                    unset($cartProducts[$key]);
                    continue;
                }

                /** Check categories */
                if ($object->filter_by_category) {
                    if ($object->products_default_category) {
                        if (!isset($restrictionCategories['selected']) || !in_array((int)$cartProduct['id_category_default'], array_column($restrictionCategories['selected'], 'id_category'))) {
                            unset($cartProducts[$key]);
                            continue;
                        }
                    } else {
                        $productIsInCategory = false;
                        $productCategories = array_keys(Product::getProductCategoriesFull((int)$cartProduct['id_product']));
                        foreach ($productCategories as $productCategory) {
                            if (isset($restrictionCategories['selected']) && in_array((int)$productCategory, array_column($restrictionCategories['selected'], 'id_category'))) {
                                $productIsInCategory = true;
                                break;
                            }
                        }

                        if (!$productIsInCategory) {
                            unset($cartProducts[$key]);
                            continue;
                        }
                    }
                }

                /** Check attributes */
                if ($object->filter_by_attribute) {
                    $product = new Product((int)$cartProduct['id_product']);

                    // TODO: check all places where id_product_attribute is used. It seems that id_product_attribute doesn't exist when this function is called from Condition
                    $idProductAttribute = null;
                    if (isset($cartProduct['id_product_attribute']) && $cartProduct['id_product_attribute']) {
                        $idProductAttribute = (int)$cartProduct['id_product_attribute'];
                    } elseif (isset($cartProduct['product_attribute_id']) && $cartProduct['product_attribute_id']) {
                        $idProductAttribute = (int)$cartProduct['product_attribute_id'];
                    }

                    $productHasCombination = false;
                    if ($idProductAttribute) {
                        // Group product attributes by attributes group
                        $combinations = array();
                        if (Module::isEnabled('attributewizardpro')) {
                            if (!empty($cartProduct['instructions_id'])) {
                                $productCombinations = array_filter(explode(',', $cartProduct['instructions_id']));
                                foreach ($productCombinations as $productCombination) {
                                    $idAttributeGroup = self::getIdAttributeGroupbyIdAttribute((int)$productCombination);
                                    if ($idAttributeGroup) {
                                        $combinations[$idAttributeGroup]['id_attribute'] = $productCombination;
                                    }
                                }
                            }
                        } else {
                            $productCombinations = $product->getAttributeCombinationsById($idProductAttribute, (int)$this->context->cart->id_lang);
                            foreach ($productCombinations as $productCombination) {
                                $combinations[$productCombination['id_attribute_group']][] = $productCombination['id_attribute'];
                            }
                        }

                        if ($combinations) {
                            // Group promo attributes selected by attributes group
                            $restrictionAttributesbyAttributeGroup = array();
                            foreach ($restrictionAttributes['selected'] as $restrictionAttribute) {
                                $idAttributeGroup = self::getIdAttributeGroupbyIdAttribute((int)$restrictionAttribute['id_attribute']);
                                if ($idAttributeGroup) {
                                    $restrictionAttributesbyAttributeGroup[$idAttributeGroup][] = $restrictionAttribute['id_attribute'];
                                }
                            }

                            // Product needs to have as much as different attribute groups as the number of attribute groups selected in the promotion
                            $productHasCombinationGroup = 0;
                            foreach ($restrictionAttributesbyAttributeGroup as $restrictionAttributebyAttributeGroupKey => $restrictionAttributebyAttributeGroup) {
                                if(is_array($combinations[$restrictionAttributebyAttributeGroupKey])){
									if (count(array_intersect($restrictionAttributebyAttributeGroup, $combinations[$restrictionAttributebyAttributeGroupKey]))) {
										$productHasCombinationGroup++;
										continue;
									}
								}
                            }

                            if (count($restrictionAttributesbyAttributeGroup) === $productHasCombinationGroup) {
                                $productHasCombination = true;
                            }
                        } elseif (in_array(999999, array_column($restrictionAttributes['selected'], 'id_attribute'))) {
                            $productHasCombination = true;
                        }
                    }

                    if (!$productHasCombination) {
                        unset($cartProducts[$key]);
                        continue;
                    }
                }

                /** Check features */
                if ($object->filter_by_feature) {
                    $productFeatures = Product::getFeaturesStatic((int)$cartProduct['id_product']);
                    $productHasFeature = false;
                    if (isset($productFeatures)) {
                        foreach ($productFeatures as $productFeature) {
                            //CAUTION! Inverse logic. If product has any of the features selected, is considered valid
                            if ((int)$productFeature['id_feature_value'] && in_array((int)$productFeature['id_feature_value'], array_column($restrictionFeatures['selected'], 'id_feature'))) {
                                $productHasFeature = true;
                                break;
                            }
                        }
                    } elseif (in_array(999999, array_column($restrictionFeatures['selected'], 'id_feature'))) {
                        $productHasFeature = true;
                    }

                    if (!$productHasFeature) {
                        unset($cartProducts[$key]);
                        continue;
                    }
                }

                /** Check supplier */
                if ($object->filter_by_supplier) {
                    if ((!(int)$cartProduct['id_supplier']  && !in_array(999999, array_column($restrictionSuppliers['selected'], 'id_supplier')))
                        || ((int)$cartProduct['id_supplier'] && !in_array((int)$cartProduct['id_supplier'], array_column($restrictionSuppliers['selected'], 'id_supplier')))) {
                        unset($cartProducts[$key]);
                        continue;
                    }
                }

                /** Check manufacturer */
                if ($object->filter_by_manufacturer) {
                    if ((!(int)$cartProduct['id_manufacturer'] && !in_array(999999, array_column($restrictionManufacturers['selected'], 'id_manufacturer')))
                        || ((int)$cartProduct['id_manufacturer'] && !in_array((int)$cartProduct['id_manufacturer'], array_column($restrictionManufacturers['selected'], 'id_manufacturer')))) {
                        unset($cartProducts[$key]);
                        continue;
                    }
                }

                /** Filter by stock */
                if ($object->filter_by_stock) {
                    $stock = (get_class($object) == 'QuantityDiscountRuleAction' ? $object->stock : $object->product_stock_amount);
                    $operator = (get_class($object) == 'QuantityDiscountRuleAction' ? $object->stock_operator : $object->product_stock_operator);
                    if (!$this->compareValue((int)$operator, StockAvailable::getQuantityAvailableByProduct((int)$cartProduct['id_product'], (int)$cartProduct['id_product_attribute']), $stock)) {
                        unset($cartProducts[$key]);
                        continue;
                    }
                }

                /** Discard products with special price if configured */



                if (!(int)$object->apply_discount_to_special && Product::getPriceStatic($cartProduct['id_product'], false, (isset($cartProduct['id_product_attribute']) ? (int)$cartProduct['id_product_attribute'] : null), 6, null, true, true, $cartProduct['cart_quantity'], false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, ((Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') ? (int)$this->context->cart->id_address_invoice : (int)$cartProduct['id_address_delivery']), $specific_price_output, (int)$object->reduction_tax, true, $virtual_context, true, $cartProduct['id_customization']) > 0) {
                    unset($cartProducts[$key]);
                    continue;
                }

                if (isset($cartProduct['productmega'])) {
                    foreach ($cartProduct['productmega'] as $productmega) {
                        $cartProduct['id_product_attribute'] = (isset($cartProduct['id_product_attribute']) ? (int)$cartProduct['id_product_attribute'] : null);
                        $cartProduct['cart_quantity'] = (int)$productmega['quantity'];
                        $cartProduct['price_without_reduction'] = $productmega['pricewt'];
                        $cartProduct['price_with_reduction'] = $productmega['pricewt'];
                        $cartProduct['price_with_reduction_without_tax'] = $productmega['price'];
                        $cartProduct['price_without_reduction_without_tax'] = $productmega['price'];
                    }
                } else {
                    if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                            $address_id = (int)$this->context->cart->id_address_invoice;
                        } else {
                            $address_id = (int)$this->context->cart->id_address_delivery;
                        }
                        if (!Address::addressExists($address_id)) {
                            $address_id = null;
                        }

                        $address = Address::initialize($address_id, true);

                        $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$cartProduct['id_product'], $this->context));
                        $product_tax_calculator = $tax_manager->getTaxCalculator();
                        if (isset($cartProduct['price_without_reduction'])) {
                            $cartProduct['price_without_reduction_without_tax'] = Tools::ps_round($product_tax_calculator->removeTaxes($cartProduct['price_without_reduction']), 6);
                        } elseif (isset($cartProduct['unit_price_tax_incl']))  {
                            $cartProduct['price_without_reduction_without_tax'] = Tools::ps_round($product_tax_calculator->removeTaxes($cartProduct['unit_price_tax_incl']), 6);
                        } else {
                            $cartProduct['price_without_reduction_without_tax'] = Tools::ps_round($product_tax_calculator->removeTaxes($cartProduct['price']), 6);
                        }

                        if (!isset($cartProduct['price_with_reduction'])) {
                            if (isset($cartProduct['price_with_reduction_tax_incl'])) {
                                $cartProduct['price_with_reduction'] = $cartProduct['price_with_reduction_tax_incl'];
                            } elseif (isset($cartProduct['unit_price_tax_incl'])) {
                                $cartProduct['price_with_reduction'] = $cartProduct['unit_price_tax_incl'];
                            } else {
                                $cartProduct['price_with_reduction'] = $cartProduct['price'];
                            }
                        }

                        if (!isset($cartProduct['price_with_reduction_without_tax'])) {
                            if (isset($cartProduct['price_with_reduction_tax_incl'])) {
                                $cartProduct['price_with_reduction_without_tax'] = $cartProduct['price_with_reduction_tax_excl'];
                            } elseif (isset($cartProduct['unit_price_tax_incl'])) {
                                $cartProduct['price_with_reduction_without_tax'] = $cartProduct['unit_price_tax_excl'];
                            } else {
                                $cartProduct['price_with_reduction_without_tax'] = $cartProduct['price'];
                            }
                        }
                    } else {
                        if (!isset($cartProduct['price_with_reduction'])) {
                            $cartProduct['price_with_reduction'] = $cartProduct['price'];
                        }

                        if (!isset($cartProduct['price_with_reduction_without_tax'])) {
                            $cartProduct['price_with_reduction_without_tax'] = $cartProduct['price_wt'];
                        }
                    }
                }

                /** Filter by price */
                if ($object->filter_by_price) {
                    if (isset($cartProduct['productmega'])) {
                        foreach ($cartProduct['productmega'] as $key2 => $productmega) {
                            if ((int)$object->product_price_from_tax) {
                                $price = $productmega['price_wt'];
                            } else {
                                $price = $productmega['price'];
                            }

                            $price = self::convertPriceFull($price, $this->context->currency, new Currency($object->product_price_from_currency), true);
                            if (!$this->compareValue(0, $price, $object->product_price_from)
                                || !$this->compareValue(2, $price, $object->product_price_to)) {
                                unset($cartProduct[$key2]);
                                continue;
                            }
                        }
                    } else {
                        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                            if ((int)$object->product_price_from_tax) {
                                $price = $cartProduct['price_with_reduction'];
                            } else {
                                $price = $cartProduct['price_with_reduction_without_tax'];
                            }
                        } else {
                            if ((int)$object->product_price_from_tax) {
                                $price = $cartProduct['price_wt'];
                            } else {
                                $price = $cartProduct['price'];
                            }
                        }

                        $price = self::convertPriceFull($price, $this->context->currency, new Currency($object->product_price_from_currency), true);
                        if (!$this->compareValue(0, $price, $object->product_price_from)
                            || !$this->compareValue(2, $price, $object->product_price_to)) {
                            unset($cartProducts[$key]);
                            continue;
                        }
                    }
                }
            }
            unset($cartProduct);

            if (isset($object->products_all_met) && $object->products_all_met && $productsBeforeFilter != count($cartProducts)) {
                $result = array();
            }

            if (count($cartProducts) && !$this->apply_products_already_discounted && get_class($object) == 'QuantityDiscountRuleAction') {
                // Get an array with id_product-id_product_attribute from $cartProducts
                $cartProductsLight = array();
                foreach ($cartProducts as $key => $cartProduct) {
                    $cartProductsLight[$key] = $cartProduct['id_product'].'-'.$cartProduct['id_product_attribute'];
                }

                foreach (self::$_discountedProducts as $idProductAndAttribute => $discountedProduct) {
                    $key2 = array_search($idProductAndAttribute, $cartProductsLight);

                    if ($key2 !== false) {
                        if ((int)$cartProducts[$key2]['cart_quantity'] > (int)$discountedProduct['quantity']) {
                            $cartProducts[$key2]['cart_quantity'] -= (int)$discountedProduct['quantity'];
                        } else {
                            unset($cartProducts[$key2]);
                        }
                    }
                }
            }

            $result = $cartProducts;

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    protected function productIsInFilter($id_product, $object, $gift_product = false)
    {
        if (!((int)$id_product)) {
            return false;
        }

        $cache_key = 'QuantityDiscountRule::productIsInFilter_'.(int)$id_product.'_'.get_class($object).'_'.(int)$object->getId().'_'.(int)$gift_product;

        if (!Cache::isStored($cache_key)) {
            $product = new Product((int)$id_product);

            /** Check product */
            if ($object->filter_by_product) {
                $restrictionProducts = $object->getSelectedAssociatedRestrictions('product');
                if (!isset($restrictionProducts['selected']) || !in_array((int)$id_product, array_column($restrictionProducts['selected'], 'id_product'))) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Check attributes */
            if ($object->filter_by_attribute) {
                $restrictionAttributes = $object->getSelectedAssociatedRestrictions('attribute');
                $product = new Product((int)$id_product);
                $combinations = array();

                if (Module::isEnabled('attributewizardpro')) {
                    // Attribute Wizard Pro compatibility
                    $result = Db::getInstance()->ExecuteS("SELECT * FROM `" . _DB_PREFIX_ . "awp_attribute_wizard_pro`");
                    $result = $result[0]['awp_attributes'];
                    $this->awp_attributes = $result != "" ? unserialize($result) : '';

                    $query = '
                        SELECT ag.`id_attribute_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name, a.`id_attribute`, al.`name` AS attribute_name,
                        a.`color` AS attribute_color, pa.`id_product_attribute`, stock.`quantity`, pa.`price`, pa.`ecotax`, pa.`weight`, pa.`default_on`, pa.`reference`
                        FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                        ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                        ' . Product::sqlStock('pa', 'pa') . '
                        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` pas ON pas.`id_product_attribute` = pa.`id_product_attribute`
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
                        LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
                        WHERE pa.`id_product` = ' . (int) ($product->id) . '
                        AND pas.id_shop = ' . (int) $this->context->shop->id . '
                        AND al.`id_lang` = ' . (int) $this->context->cookie->id_lang . '
                        AND agl.`id_lang` = ' . (int) $this->context->cookie->id_lang . '
                        ORDER BY agl.`public_name`, pa.id_product_attribute DESC, default_on ASC';

                    $attributesGroups = Db::getInstance()->ExecuteS($query);
                    if (Db::getInstance()->numRows()) {
                        $groups = array();

                        foreach ($attributesGroups AS $k => $row) {
                            $group_order = $this->isInGroup($row['id_attribute_group'], $this->awp_attributes);
                            if ($group_order == -1) {
                                continue;
                            }

                            $groups[$group_order]['attributes'][$row['id_attribute']] = array(
                                'id_attribute' => $row['id_attribute'],
                                'attribute_name' => $row['attribute_name'],
                            );

                            $groups[$group_order]['id_attribute_group'] = $row['id_attribute_group'];
                            $groups[$group_order]['name'] = $row['public_group_name'];

                            if (!isset($groups[$group_order]['attributes_quantity'][$row['id_attribute']])) {
                                $groups[$group_order]['attributes_quantity'][$row['id_attribute']] = 0;
                            }
                            $groups[$group_order]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
                        }

                        foreach ($groups as $group) {
                            foreach ($group['attributes'] as $attribute) {
                                $combinations[$group['id_attribute_group']][] = $attribute['id_attribute'];
                            }
                        }
                    }
                } else {
                    $productCombinations = $product->getAttributeCombinations((int)$this->context->cart->id_lang);
                    foreach ($productCombinations as $productCombination) {
                        $combinations[$productCombination['id_attribute_group']][] = $productCombination['id_attribute'];

                    }
                }

                $productHasCombination = false;
                if ($combinations) {
                    // Group promo attributes selected by attributes group
                    $restrictionAttributesbyAttributeGroup = array();
                    foreach ($restrictionAttributes['selected'] as $restrictionAttribute) {
                        $idAttributeGroup = self::getIdAttributeGroupbyIdAttribute((int)$restrictionAttribute['id_attribute']);
                        if ($idAttributeGroup) {
                            $restrictionAttributesbyAttributeGroup[$idAttributeGroup][] = $restrictionAttribute['id_attribute'];
                        }
                    }

                    // Product needs to have as much as different attribute groups as the number of attribute groups selected in the promotion
                    $productHasCombinationGroup = 0;
                    foreach ($restrictionAttributesbyAttributeGroup as $restrictionAttributebyAttributeGroupKey => $restrictionAttributebyAttributeGroup) {
						if(isset($combinations[$restrictionAttributebyAttributeGroupKey]) && is_array($combinations[$restrictionAttributebyAttributeGroupKey])){
							if (count(array_intersect($restrictionAttributebyAttributeGroup, $combinations[$restrictionAttributebyAttributeGroupKey]))) {
								$productHasCombinationGroup++;
								continue;
							}
                        }
                    }

                    if (count($restrictionAttributesbyAttributeGroup) === $productHasCombinationGroup) {
                        $productHasCombination = true;
                    }
                } elseif (in_array(999999, array_column($restrictionAttributes['selected'], 'id_attribute'))) {
                    $productHasCombination = true;
                }

                if (!$productHasCombination) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Check features */
            if ($object->filter_by_feature) {
                $restrictionFeatures = $object->getSelectedAssociatedRestrictions('feature');
                $productFeatures = Product::getFeaturesStatic((int)$id_product);

                $productHasFeature = false;

                if (isset($productFeatures)) {
                    foreach ($productFeatures as $productFeature) {
                        //CAUTION! Inverse logic. If product has any of the features selected, is considered valid
                        if ((int)$productFeature['id_feature_value'] && in_array((int)$productFeature['id_feature_value'], array_column($restrictionFeatures['selected'], 'id_feature'))) {
                            $productHasFeature = true;
                            break;
                        }
                    }
                } elseif (in_array(999999, array_column($restrictionFeatures['selected'], 'id_feature'))) {
                    $productHasFeature = true;
                }

                if (!$productHasFeature) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Check categories */
            if ($object->filter_by_category) {
                $restrictionCategories = $object->getSelectedAssociatedRestrictions('category');
                if ($object->products_default_category) {
                    if (!isset($restrictionCategories['selected']) || !in_array((int)$product->id_category_default, array_column($restrictionCategories['selected'], 'id_category'))) {
                        Cache::store($cache_key, false);
                        return false;
                    }
                } else {
                    $productIsInCategory = false;
                    $productCategories = array_keys(Product::getProductCategoriesFull((int)$id_product));
                    foreach ($productCategories as $productCategory) {
                        if (isset($restrictionCategories['selected']) && in_array((int)$productCategory, array_column($restrictionCategories['selected'], 'id_category'))) {
                            $productIsInCategory = true;
                            break;
                        }
                    }

                    if (!$productIsInCategory) {
                        Cache::store($cache_key, false);
                        return false;
                    }
                }
            }

            /** Check supplier */
            if ($object->filter_by_supplier) {
                $restrictionSuppliers = $object->getSelectedAssociatedRestrictions('supplier');
                if ((!(int)$product->id_supplier  && !in_array(999999, array_column($restrictionSuppliers['selected'], 'id_supplier')))
                    || ((int)$product->id_supplier && !in_array((int)$product->id_supplier, array_column($restrictionSuppliers['selected'], 'id_supplier')))) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Check manufacturer */
            if ($object->filter_by_manufacturer) {
                $restrictionManufacturers = $object->getSelectedAssociatedRestrictions('manufacturer');

                if ((!(int)$product->id_manufacturer  && !in_array(999999, array_column($restrictionManufacturers['selected'], 'id_manufacturer')))
                    || ((int)$product->id_manufacturer && !in_array((int)$product->id_manufacturer, array_column($restrictionManufacturers['selected'], 'id_manufacturer')))) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Filter by price */
            if ($object->filter_by_price) {
                $price = Product::getPriceStatic($id_product, (int)$object->product_price_from_tax, null, 6, null, false, true, 1, false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, null, $specific_price_output, (int)$object->product_price_from_tax, true, $this->context, true, null/*$product->id_customization*/);
                $price = self::convertPriceFull($price, $this->context->currency, new Currency($object->product_price_from_currency), true);
                if (!$this->compareValue(0, $price, $object->product_price_from)
                    || !$this->compareValue(2, $price, $object->product_price_to)) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Filter by stock */
            if ($object->filter_by_stock) {
                $stock = (get_class($object) == 'QuantityDiscountRuleAction' ? $object->stock : $object->product_stock_amount);
                $operator = (get_class($object) == 'QuantityDiscountRuleAction' ? $object->stock_operator : $object->product_stock_operator);
                if (!$this->compareValue((int)$operator, StockAvailable::getQuantityAvailableByProduct((int)$product->id), $stock)) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            if (get_class($object) == 'QuantityDiscountRuleAction' && $object->apply_discount_to_stock) {
                if (Configuration::get('PS_STOCK_MANAGEMENT') && !(int)StockAvailable::getQuantityAvailableByProduct((int)$product->id)) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            /** Discard products with special price if configured */
            $price = Product::getPriceStatic($id_product, false, null, 6, null, true, true, 1, false, (int)$this->context->cart->id_customer, (int)$this->context->cart->id, null, $specific_price_output, false, true, $this->context, true, null/*$product->id_customization*/);
            if (!(int)$object->apply_discount_to_special && $price > 0) {
                Cache::store($cache_key, false);
                return false;
            }

            /* Gift product */
            if ($gift_product && isset($object->gift_product) && (int)$object->gift_product) {
                if ($id_product != $object->gift_product) {
                    Cache::store($cache_key, false);
                    return false;
                }
            }

            Cache::store($cache_key, true);
            return true;
        } else {
            return Cache::retrieve($cache_key);
        }
    }

    protected function groupProducts($id_cart, $products, $object, $cache = true)
    {
        if (!is_array($products)) {
            return false;
        }

        $key = $object->group_products_by;
        if (!$key) {
            return false;
        }

        $cache_key = 'QuantityDiscountRule::groupProducts_'.(int)$id_cart.'_'.get_class($object).'_'.(int)$object->getId().'_'.$key;

        if (!$cache || !Cache::isStored($cache_key)) {
            switch ($key) {
                case 'product':
                    if ((int)$object->products_nb_same_attributes) {
                        $key = 'by_product';
                    } else {
                        $key = 'by_product_attribute';
                    }
                    break;

                case 'category':
                    if (!$object->filter_by_category || $object->products_default_category) {
                        $key = 'by_default_category';
                    } elseif ($object->filter_by_category) {
                        if ($object->products_default_category) {
                            $key = 'by_default_category';
                        } else {
                            $key = 'by_category';
                        }
                    }
                    break;

                case 'supplier':
                    $key = 'by_supplier';
                    break;

                case 'manufacturer':
                    $key = 'by_manufacturer';
                    break;

                case 'all':
                    $key = 'by_all';
                    break;
            }

            $productsGrouped = array();

            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                $address_id = (int)$this->context->cart->id_address_invoice;
            } else {
                $address_id = (int)$this->context->cart->id_address_delivery;
            }
            if (!Address::addressExists($address_id)) {
                $address_id = null;
            }

            $address = Address::initialize($address_id, true);

            foreach ($products as $product) {
                if (version_compare(_PS_VERSION_, '1.6.1', '<')) {
                    $quantity = (int)$product['cart_quantity'];
                    $product = Product::getProductProperties($this->context->language->id, $product);
                    $product['cart_quantity'] = $quantity;
                }

                if ($key == 'by_product'
                    || $key == 'by_attribute'
                    || $key == 'by_product_attribute'
                    || $key == 'by_default_category'
                    || $key == 'by_supplier'
                    || $key == 'by_manufacturer') {
                    switch ($key) {
                        case 'by_product':
                            $index = $product['id_product'];
                            break;
                        case 'by_attribute':
                            $index = $product['id_product_attribute'];
                            break;
                        case 'by_product_attribute':
                            $index = $product['id_product'].'-'.$product['id_product_attribute'];
                            break;
                        case 'by_default_category':
                            $index = $product['id_category_default'];
                            break;
                        case 'by_supplier':
                            $index = $product['id_supplier'];
                            break;
                        case 'by_manufacturer':
                            $index = $product['id_manufacturer'];
                            break;
                    }

                    if (isset($product['productmega'])) {
                        foreach ($product['productmega'] as $productmega) {
                            $index2 = $product['id_product'].'-'.$product['id_product_attribute'].'-'.$productmega['id_megacart'];

                            $productsGrouped[$index]['products'][$index2]['productmega'] = true;
                            $productsGrouped[$index]['products'][$index2]['id_product'] = (int)$product['id_product'];
                            $productsGrouped[$index]['products'][$index2]['id_product_attribute'] = (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null);
                            $productsGrouped[$index]['products'][$index2]['id_customization'] = (int)$product['id_customization'];

                            $productsGrouped[$index]['products'][$index2]['id_category_default'] = (int)$product['id_category_default'];

                            $productsGrouped[$index]['products'][$index2]['cart_quantity'] = (int)$productmega['quantity'];
                            $productsGrouped[$index]['products'][$index2]['price_without_reduction'] = $productmega['pricewt'];
                            $productsGrouped[$index]['products'][$index2]['price_with_reduction'] = $productmega['pricewt'];
                            $productsGrouped[$index]['products'][$index2]['price_with_reduction_without_tax'] = $productmega['price'];
                            $productsGrouped[$index]['products'][$index2]['price_without_reduction_without_tax'] = $productmega['price'];
                            $productsGrouped[$index]['products'][$index2]['quantity_available'] = $productmega['quantity_available'];

                            if (Module::isEnabled('attributewizardpro')) {
                                $productsGrouped[$index]['products'][$index2]['instructions_id'] = $productmega['instructions_id'];
                            }

                            if (isset($productsGrouped[$index]['price_without_reduction'])) {
                                $productsGrouped[$index]['price_without_reduction'] += $productmega['pricewt']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[$index]['price_without_reduction'] = $productmega['pricewt']*(int)$productmega['quantity'];
                            }

                            if (isset($productsGrouped[$index]['price_with_reduction'])) {
                                $productsGrouped[$index]['price_with_reduction'] += $productmega['pricewt']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[$index]['price_with_reduction'] = $productmega['pricewt']*(int)$productmega['quantity'];
                            }

                            if (isset($productsGrouped[$index]['price_with_reduction_without_tax'])) {
                                $productsGrouped[$index]['price_with_reduction_without_tax'] += $productmega['price']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[$index]['price_with_reduction_without_tax'] = $productmega['price']*(int)$productmega['quantity'];
                            }

                            if (isset($productsGrouped[$index]['price_without_reduction_without_tax'])) {
                                $productsGrouped[$index]['price_without_reduction_without_tax'] += $productmega['price']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[$index]['price_without_reduction_without_tax'] = $productmega['price']*(int)$productmega['quantity'];
                            }
                        }
                    } else {
                        $index2 = $product['id_product'].'-'.$product['id_product_attribute'];

                        $productsGrouped[$index]['products'][$index2]['id_product'] = (int)$product['id_product'];
                        $productsGrouped[$index]['products'][$index2]['id_product_attribute'] = (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null);
                        $productsGrouped[$index]['products'][$index2]['id_customization'] = (int)$product['id_customization'];
                        $productsGrouped[$index]['products'][$index2]['id_category_default'] = (int)$product['id_category_default'];

                        if (isset($productsGrouped[$index]['products'][$index2]['cart_quantity'])) {
                            $productsGrouped[$index]['products'][$index2]['cart_quantity'] += (int)$product['cart_quantity'];
                        } else {
                            $productsGrouped[$index]['products'][$index2]['cart_quantity'] = (int)$product['cart_quantity'];
                        }

                        $productsGrouped[$index]['products'][$index2]['quantity_available'] = ((isset($object->apply_discount_to_stock) && $object->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX);

                        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                            if (isset($product['price_without_reduction'])) {
                            $productsGrouped[$index]['products'][$index2]['price_without_reduction'] = $product['price_without_reduction'];
                            } else {
                                $productsGrouped[$index]['products'][$index2]['price_without_reduction'] = $product['price'];
                            }

                            if (isset($product['price_with_reduction'])) {
                            $productsGrouped[$index]['products'][$index2]['price_with_reduction'] = $product['price_with_reduction'];
                            } else {
                                $productsGrouped[$index]['products'][$index2]['price_with_reduction'] = $product['price'];
                            }

                            if (isset($product['price_with_reduction_without_tax'])) {
                            $productsGrouped[$index]['products'][$index2]['price_with_reduction_without_tax'] = $product['price_with_reduction_without_tax'];
                            } else {
                                $productsGrouped[$index]['products'][$index2]['price_with_reduction_without_tax'] = $product['price'];
                            }

                            $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context));
                            $product_tax_calculator = $tax_manager->getTaxCalculator();

                            if (isset($product['price_without_reduction'])) {
                            $price_without_reduction_without_tax = Tools::ps_round($product_tax_calculator->removeTaxes($product['price_without_reduction']), 6);
                            } else {
                                $price_without_reduction_without_tax = Tools::ps_round($product_tax_calculator->removeTaxes($product['price']), 6);
                            }
                            $productsGrouped[$index]['products'][$index2]['price_without_reduction_without_tax'] = $price_without_reduction_without_tax;
                        } else {
                            $productsGrouped[$index]['products'][$index2]['price_with_reduction'] = $product['price'];
                            $productsGrouped[$index]['products'][$index2]['price_with_reduction_without_tax'] = $product['price_wt'];
                            $price_without_reduction_without_tax = $product['price_wt'];
                        }

                        if (Module::isEnabled('attributewizardpro')) {
                            $productsGrouped[$index]['products'][$index2]['instructions_id'] = $product['instructions_id'];
                        }

                        if (isset($productsGrouped[$index]['price_without_reduction'])) {
                            $productsGrouped[$index]['price_without_reduction'] += $product['price_without_reduction']*(int)$product['cart_quantity'];
                        } else {
                            $productsGrouped[$index]['price_without_reduction'] = $product['price']*(int)$product['cart_quantity'];
                        }

                        if (isset($productsGrouped[$index]['price_with_reduction'])) {
                            if (isset($product['price_with_reduction'])) {
                                $productsGrouped[$index]['price_with_reduction'] += $product['price_with_reduction']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[$index]['price_with_reduction'] += $product['price']*(int)$product['cart_quantity'];
                            }
                        } else {
                            if (isset($product['price_with_reduction'])) {
                                $productsGrouped[$index]['price_with_reduction'] = $product['price_with_reduction']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[$index]['price_with_reduction'] = $product['price']*(int)$product['cart_quantity'];
                            }
                        }

                        if (isset($productsGrouped[$index]['price_with_reduction_without_tax'])) {
                            if (isset($product['price_with_reduction_without_tax'])) {
                                $productsGrouped[$index]['price_with_reduction_without_tax'] += $product['price_with_reduction_without_tax']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[$index]['price_with_reduction_without_tax'] += $product['price_wt']*(int)$product['cart_quantity'];
                            }
                        } else {
                            if (isset($product['price_with_reduction_without_tax'])) {
                                $productsGrouped[$index]['price_with_reduction_without_tax'] = $product['price_with_reduction_without_tax']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[$index]['price_with_reduction_without_tax'] = $product['price_wt']*(int)$product['cart_quantity'];
                            }
                        }

                        if (isset($productsGrouped[$index]['price_without_reduction_without_tax'])) {
                            $productsGrouped[$index]['price_without_reduction_without_tax'] += $price_without_reduction_without_tax*(int)$product['cart_quantity'];
                        } else {
                            $productsGrouped[$index]['price_without_reduction_without_tax'] = $price_without_reduction_without_tax*(int)$product['cart_quantity'];
                        }
                    }

                    $productsGrouped[$index]['products'][$index2]['rate'] = $product['rate'];
                    $productsGrouped[$index]['products'][$index2]['id_shop'] = (int)$product['id_shop'];
                    if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                        $productsGrouped[$index]['products'][$index2]['id_address'] = (int)$this->context->cart->id_address_invoice;
                    } else {
                        $productsGrouped[$index]['products'][$index2]['id_address'] = (int)$product['id_address_delivery']; // Get delivery address of the product from the cart
                    }

                    if (isset($productsGrouped[$index]['cart_quantity'])) {
                        $productsGrouped[$index]['cart_quantity'] += (int)$product['cart_quantity'];
                    } else {
                        $productsGrouped[$index]['cart_quantity'] = (int)$product['cart_quantity'];
                    }
                } elseif ($key == 'by_category') {
                    $productCategories = array_keys(Product::getProductCategoriesFull((int)$product['id_product']));
                    $productIsInCategory = false;
                    $categories = $object->getSelectedAssociatedRestrictions('category');

                    foreach ($productCategories as $productCategory) {
                        if (in_array((int)$productCategory, array_column($categories['selected'], 'id_category'))) {
                            $productIsInCategory[] = $productCategory;
                            continue;
                        }
                    }

                    if ($productIsInCategory) {
                        foreach ($productIsInCategory as $productCategory) {
                            if (isset($product['productmega'])) {
                                foreach ($product['productmega'] as $productmega) {
                                    $index2 = $product['id_product'].'-'.$product['id_product_attribute'].'-'.$productmega['id_megacart'];

                                    $productsGrouped[$productCategory]['products'][$index2]['productmega'] = true;
                                    $productsGrouped[$productCategory]['products'][$index2]['id_product'] = (int)$product['id_product'];
                                    $productsGrouped[$productCategory]['products'][$index2]['id_product_attribute'] = (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null);
                                    $productsGrouped[$productCategory]['products'][$index2]['id_customization'] = (int)$product['id_customization'];
                                    $productsGrouped[$productCategory]['products'][$index2]['id_category_default'] = (int)$product['id_category_default'];

                                    $productsGrouped[$productCategory]['products'][$index2]['cart_quantity'] = (int)$productmega['quantity'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_without_reduction'] = $productmega['pricewt'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_with_reduction'] = $productmega['pricewt'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_with_reduction_without_tax'] = $productmega['price'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_without_reduction_without_tax'] = $productmega['price'];
                                    $productsGrouped[$productCategory]['products'][$index2]['quantity_available'] = $productmega['quantity_available'];

                                    if (Module::isEnabled('attributewizardpro')) {
                                        $productsGrouped[$productCategory]['products'][$index2]['instructions_id'] = $productmega['instructions_id'];
                                    }

                                    if (isset($productsGrouped[$productCategory]['price_without_reduction'])) {
                                        $productsGrouped[$productCategory]['price_without_reduction'] += $productmega['pricewt']*(int)$productmega['quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_without_reduction'] = $productmega['pricewt']*(int)$productmega['quantity'];
                                    }

                                    if (isset($productsGrouped[$productCategory]['price_with_reduction'])) {
                                        $productsGrouped[$productCategory]['price_with_reduction'] += $productmega['pricewt']*(int)$productmega['quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_with_reduction'] = $productmega['pricewt']*(int)$productmega['quantity'];
                                    }

                                    if (isset($productsGrouped[$productCategory]['price_with_reduction_without_tax'])) {
                                        $productsGrouped[$productCategory]['price_with_reduction_without_tax'] += $productmega['price']*(int)$productmega['quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_with_reduction_without_tax'] = $productmega['price']*(int)$productmega['quantity'];
                                    }

                                    if (isset($productsGrouped[$productCategory]['price_without_reduction_without_tax'])) {
                                        $productsGrouped[$productCategory]['price_without_reduction_without_tax'] += $productmega['price']*(int)$productmega['quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_without_reduction_without_tax'] = $productmega['price']*(int)$productmega['quantity'];
                                    }
                                }
                            } else {
                                $index2 = $product['id_product'].'-'.$product['id_product_attribute'];


                                if (isset($productsGrouped[$productCategory]['products'][$index2]['cart_quantity'])) {
                                    $productsGrouped[$productCategory]['products'][$index2]['cart_quantity'] += (int)$product['cart_quantity'];
                                } else {
                                    $productsGrouped[$productCategory]['products'][$index2]['cart_quantity'] = (int)$product['cart_quantity'];
                                }

                                $productsGrouped[$productCategory]['products'][$index2]['id_product'] = (int)$product['id_product'];
                                $productsGrouped[$productCategory]['products'][$index2]['id_product_attribute'] = (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null);
                                $productsGrouped[$productCategory]['products'][$index2]['id_customization'] = (int)$product['id_customization'];
                                $productsGrouped[$productCategory]['products'][$index2]['id_category_default'] = (int)$product['id_category_default'];
                                $productsGrouped[$productCategory]['products'][$index2]['quantity_available'] = ((isset($object->apply_discount_to_stock) && $object->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX);

                                if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                    $productsGrouped[$productCategory]['products'][$index2]['price_without_reduction'] = $product['price_without_reduction'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_with_reduction'] = $product['price_with_reduction'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_with_reduction_without_tax'] = $product['price_with_reduction_without_tax'];

                                    $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context));
                                    $product_tax_calculator = $tax_manager->getTaxCalculator();
                                    $productsGrouped[$productCategory]['products'][$index2]['price_without_reduction_without_tax'] =
                                    $price_without_reduction_without_tax = Tools::ps_round($product_tax_calculator->removeTaxes($product['price_without_reduction']), 6);
                                    $productsGrouped[$productCategory]['products'][$index2]['price_without_reduction_without_tax'] = $price_without_reduction_without_tax;
                                } else {
                                    $productsGrouped[$productCategory]['products'][$index2]['price_with_reduction'] = $product['price'];
                                    $productsGrouped[$productCategory]['products'][$index2]['price_with_reduction_without_tax'] = $product['price_wt'];
                                    $price_without_reduction_without_tax = $product['price_wt'];
                                }

                                if (Module::isEnabled('attributewizardpro')) {
                                    $productsGrouped[$productCategory]['products'][$index2]['instructions_id'] = $product['instructions_id'];
                                }

                                if (isset($productsGrouped[$productCategory]['price_without_reduction'])) {
                                    $productsGrouped[$productCategory]['price_without_reduction'] += $product['price_without_reduction']*(int)$product['cart_quantity'];
                                } else {
                                    $productsGrouped[$productCategory]['price_without_reduction'] = $product['price_without_reduction']*(int)$product['cart_quantity'];
                                }

                                if (isset($productsGrouped[$productCategory]['price_with_reduction'])) {
                                    if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                        $productsGrouped[$productCategory]['price_with_reduction'] += $product['price_with_reduction']*(int)$product['cart_quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_with_reduction'] += $product['price']*(int)$product['cart_quantity'];
                                    }
                                } else {
                                    if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                        $productsGrouped[$productCategory]['price_with_reduction'] = $product['price_with_reduction']*(int)$product['cart_quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_with_reduction'] = $product['price']*(int)$product['cart_quantity'];
                                    }
                                }

                                if (isset($productsGrouped[$productCategory]['price_with_reduction_without_tax'])) {
                                    if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                        $productsGrouped[$productCategory]['price_with_reduction_without_tax'] += $product['price_with_reduction_without_tax']*(int)$product['cart_quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_with_reduction_without_tax'] += $product['price_wt']*(int)$product['cart_quantity'];
                                    }
                                } else {
                                    if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                        $productsGrouped[$productCategory]['price_with_reduction_without_tax'] = $product['price_with_reduction_without_tax']*(int)$product['cart_quantity'];
                                    } else {
                                        $productsGrouped[$productCategory]['price_with_reduction_without_tax'] = $product['price_wt']*(int)$product['cart_quantity'];
                                    }
                                }

                                if (isset($productsGrouped[$productCategory]['price_without_reduction_without_tax'])) {
                                    $productsGrouped[$productCategory]['price_without_reduction_without_tax'] += $price_without_reduction_without_tax*(int)$product['cart_quantity'];
                                } else {
                                    $productsGrouped[$productCategory]['price_without_reduction_without_tax'] = $price_without_reduction_without_tax*(int)$product['cart_quantity'];
                                }
                            }

                            $productsGrouped[$productCategory]['products'][$index2]['rate'] = $product['rate'];
                            $productsGrouped[$productCategory]['products'][$index2]['id_shop'] = (int)$product['id_shop'];
                            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                                $productsGrouped[$productCategory]['products'][$index2]['id_address'] = (int)$this->context->cart->id_address_invoice;
                            } else {
                                $productsGrouped[$productCategory]['products'][$index2]['id_address'] = (int)$product['id_address_delivery']; // Get delivery address of the product from the cart
                            }

                            if (isset($productsGrouped[$productCategory]['cart_quantity'])) {
                                $productsGrouped[$productCategory]['cart_quantity'] += (int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[$productCategory]['cart_quantity'] = (int)$product['cart_quantity'];
                            }
                        }
                    }
                } elseif ($key == 'by_all') {
                    if (isset($product['productmega'])) {
                        foreach ($product['productmega'] as $productmega) {
                            $index2 = $product['id_product'].'-'.$product['id_product_attribute'].'-'.$productmega['id_megacart'];

                            $productsGrouped[0]['products'][$index2]['productmega'] = true;
                            $productsGrouped[0]['products'][$index2]['id_product'] = (int)$product['id_product'];
                            $productsGrouped[0]['products'][$index2]['id_product_attribute'] = (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null);
                            $productsGrouped[0]['products'][$index2]['id_customization'] = (int)$product['id_customization'];
                            $productsGrouped[0]['products'][$index2]['id_category_default'] = (int)$product['id_category_default'];

                            $productsGrouped[0]['products'][$index2]['cart_quantity'] = (int)$productmega['quantity'];
                            $productsGrouped[0]['products'][$index2]['price_without_reduction'] = $productmega['pricewt'];
                            $productsGrouped[0]['products'][$index2]['price_with_reduction'] = $productmega['pricewt'];
                            $productsGrouped[0]['products'][$index2]['price_with_reduction_without_tax'] = $productmega['price'];
                            $productsGrouped[0]['products'][$index2]['price_without_reduction_without_tax'] = $productmega['price'];
                            $productsGrouped[0]['products'][$index2]['quantity_available'] = $productmega['quantity_available'];

                            if (Module::isEnabled('attributewizardpro')) {
                                $productsGrouped[0]['products'][$index2]['instructions_id'] = $productmega['instructions_id'];
                            }

                            if (isset($productsGrouped[0]['price_without_reduction'])) {
                                $productsGrouped[0]['price_without_reduction'] += $productmega['pricewt']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[0]['price_without_reduction'] = $productmega['pricewt']*(int)$productmega['quantity'];
                            }

                            if (isset($productsGrouped[0]['price_with_reduction'])) {
                                $productsGrouped[0]['price_with_reduction'] += $productmega['pricewt']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[0]['price_with_reduction'] = $productmega['pricewt']*(int)$productmega['quantity'];
                            }

                            if (isset($productsGrouped[0]['price_with_reduction_without_tax'])) {
                                $productsGrouped[0]['price_with_reduction_without_tax'] += $productmega['price']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[0]['price_with_reduction_without_tax'] = $productmega['price']*(int)$productmega['quantity'];
                            }

                            if (isset($productsGrouped[0]['price_without_reduction_without_tax'])) {
                                $productsGrouped[0]['price_without_reduction_without_tax'] += $productmega['price']*(int)$productmega['quantity'];
                            } else {
                                $productsGrouped[0]['price_without_reduction_without_tax'] = $productmega['price']*(int)$productmega['quantity'];
                            }
                        }
                    } else {
                        $index2 = $product['id_product'].'-'.$product['id_product_attribute'].'-'.$product['id_customization'];


                        if (isset($productsGrouped[0]['products'][$index2]['cart_quantity'])) {
                            $productsGrouped[0]['products'][$index2]['cart_quantity'] += (int)$product['cart_quantity'];
                        } else {
                            $productsGrouped[0]['products'][$index2]['cart_quantity'] = (int)$product['cart_quantity'];
                        }

                        $productsGrouped[0]['products'][$index2]['id_product'] = (int)$product['id_product'];
                        $productsGrouped[0]['products'][$index2]['id_product_attribute'] = (isset($product['id_product_attribute']) ? (int)$product['id_product_attribute'] : null);
                        $productsGrouped[0]['products'][$index2]['id_customization'] = (int)$product['id_customization'];
                        $productsGrouped[0]['products'][$index2]['id_category_default'] = (int)$product['id_category_default'];
                        $productsGrouped[0]['products'][$index2]['quantity_available'] = ((isset($object->apply_discount_to_stock) && $object->apply_discount_to_stock && Configuration::get('PS_STOCK_MANAGEMENT')) ? (int)$product['quantity_available'] : PHP_INT_MAX);

                        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                            $productsGrouped[0]['products'][$index2]['price_without_reduction'] = $product['price_without_reduction'];
                            $productsGrouped[0]['products'][$index2]['price_with_reduction'] = $product['price_with_reduction'];
                            $productsGrouped[0]['products'][$index2]['price_with_reduction_without_tax'] = $product['price_with_reduction_without_tax'];

                            $tax_manager = TaxManagerFactory::getManager($address, Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context));
                            $product_tax_calculator = $tax_manager->getTaxCalculator();
                            $price_without_reduction_without_tax = Tools::ps_round($product_tax_calculator->removeTaxes($product['price_without_reduction']), 6);
                            $productsGrouped[0]['products'][$index2]['price_without_reduction_without_tax'] = $price_without_reduction_without_tax;
                        } else {
                            $productsGrouped[0]['products'][$index2]['price_with_reduction'] = $product['price'];
                            $productsGrouped[0]['products'][$index2]['price_with_reduction_without_tax'] = $product['price_wt'];
                            $price_without_reduction_without_tax = $product['price_wt'];
                        }

                        if (Module::isEnabled('attributewizardpro')) {
                            $productsGrouped[0]['products'][$index2]['instructions_id'] = $product['instructions_id'];
                            }

                        if (isset($productsGrouped[0]['price_without_reduction'])) {
                            $productsGrouped[0]['price_without_reduction'] += $product['price_without_reduction']*(int)$product['cart_quantity'];
                        } else {
                            $productsGrouped[0]['price_without_reduction'] = $product['price_without_reduction']*(int)$product['cart_quantity'];
                        }

                        if (isset($productsGrouped[0]['price_with_reduction'])) {
                            if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                $productsGrouped[0]['price_with_reduction'] += $product['price_with_reduction']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[0]['price_with_reduction'] += $product['price']*(int)$product['cart_quantity'];
                            }
                        } else {
                            if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                $productsGrouped[0]['price_with_reduction'] = $product['price_with_reduction']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[0]['price_with_reduction'] = $product['price']*(int)$product['cart_quantity'];
                            }
                        }

                        if (isset($productsGrouped[0]['price_with_reduction_without_tax'])) {
                            if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                $productsGrouped[0]['price_with_reduction_without_tax'] += $product['price_with_reduction_without_tax']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[0]['price_with_reduction_without_tax'] += $product['price_wt']*(int)$product['cart_quantity'];
                            }
                        } else {
                            if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
                                $productsGrouped[0]['price_with_reduction_without_tax'] = $product['price_with_reduction_without_tax']*(int)$product['cart_quantity'];
                            } else {
                                $productsGrouped[0]['price_with_reduction_without_tax'] = $product['price_wt']*(int)$product['cart_quantity'];
                            }
                        }

                        if (isset($productsGrouped[0]['price_without_reduction_without_tax'])) {
                            $productsGrouped[0]['price_without_reduction_without_tax'] += $price_without_reduction_without_tax*(int)$product['cart_quantity'];
                        } else {
                            $productsGrouped[0]['price_without_reduction_without_tax'] = $price_without_reduction_without_tax*(int)$product['cart_quantity'];
                        }
                    }

                    $productsGrouped[0]['products'][$index2]['rate'] = $product['rate'];
                    $productsGrouped[0]['products'][$index2]['id_shop'] = (int)$product['id_shop'];
                    if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                        $productsGrouped[0]['products'][$index2]['id_address'] = (int)$this->context->cart->id_address_invoice;
                    } else {
                        $productsGrouped[0]['products'][$index2]['id_address'] = (int)$product['id_address_delivery']; // Get delivery address of the product from the cart
                    }

                    if (isset($productsGrouped[0]['cart_quantity'])) {
                        $productsGrouped[0]['cart_quantity'] += (int)$product['cart_quantity'];
                    } else {
                        $productsGrouped[0]['cart_quantity'] = (int)$product['cart_quantity'];
                    }
                }
            }

            $result = $productsGrouped;
            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    protected function addDiscountedProducts($action, $products, $quantity)
    {
        if (count($products) == count($products, COUNT_RECURSIVE)) {
            $products[] = $products;
        }

        foreach ($products as $product) {
            $key = $product['id_product'].'-'.$product['id_product_attribute'];
            if (!isset(self::$_discountedProducts[$key])) {
                self::$_discountedProducts[$key]['quantity'] = min((int)$product['cart_quantity'], (int)$quantity);
            } else {
                self::$_discountedProducts[$key]['quantity'] += min((int)$product['cart_quantity'], (int)$quantity);
            }

            $quantity -= min((int)$product['cart_quantity'], (int)$quantity);
            if (!$quantity) {
                break;
            }
        }
    }

    public static function getOrdersIdByDateAndState($date_from, $date_to, $id_order_states = null, $id_customer = null, $type = null)
    {
        if (Context::getContext()->customer->is_guest) {
            $customerClause = ' AND `email` = \''.Context::getContext()->customer->email.'\'';
        } else {
            $customerClause = ' AND o.`id_customer` = '.(int)$id_customer;
        }

        $sql = 'SELECT `id_order`
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`)
                WHERE DATE_ADD(o.`date_upd`, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND o.`date_upd` >= \''.pSQL($date_from).'\'
                    '.Shop::addSqlRestriction(false, 'o')
                    .($type ? ' AND `'.pSQL((string)$type).'_number` != 0' : '')
                    .$customerClause
                    .($id_order_states ? ' AND `current_state` IN ('.implode(',', array_map('intval', $id_order_states)).')' : '');

        $result = Db::getInstance()->executeS($sql);

        $orders = array();
        foreach ($result as $order) {
            $orders[] = (int)($order['id_order']);
        }

        return $orders;
    }

    public function isAlreadyInCart($id_cart, $id_quantity_discount_rule)
    {
        if (!(int)$id_cart || !(int)$id_quantity_discount_rule) {
            return false;
        }

        $sql = 'SELECT id_cart_rule
            FROM `'._DB_PREFIX_.'quantity_discount_rule_cart` qdrc
            WHERE qdrc.`active`= 1 AND `id_cart` = '.(int)$id_cart.'
                AND `id_quantity_discount_rule` = '.(int)$id_quantity_discount_rule;

        $result = Db::getInstance()->getRow($sql);

        if (isset($result['id_cart_rule'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        if ($table === null) {
            $table = self::$definition['table'];
        }

        $query = new DbQuery();
        $query->select('`id_'.bqSQL($table).'`');
        $query->from($table);
        if ($has_active_column) {
            $query->where('`active` = 1');
        }

        return (bool)Db::getInstance()->getValue($query);
    }

    public static function removeUnusedRules($id_quantity_discount_rule = null, $limit = null, $active = false)
    {
        $query = "SHOW TABLES LIKE '"._DB_PREFIX_."quantity_discount_rule_cart'";

        if (Db::getInstance()->executeS($query)) {
            $sql = 'SELECT `id_cart`, `id_quantity_discount_rule`, `id_cart_rule`
                    FROM `' . _DB_PREFIX_ . 'quantity_discount_rule_cart` qdrc
                    WHERE 1 = 1 ' .
                ($active ? '' : 'AND qdrc.`active` = 0') . ' 
                        AND qdrc.`id_cart_rule` NOT IN (SELECT `id_cart_rule` FROM `' . _DB_PREFIX_ . 'order_cart_rule`)' .
                ($id_quantity_discount_rule ? ' AND `id_quantity_discount_rule` = ' . (int)$id_quantity_discount_rule : '') .
                ' ORDER BY RAND() ' .
                ($limit ? ' LIMIT ' . (int)$limit : '');

            $result = Db::getInstance()->executeS($sql);

            foreach ($result as $rule) {
                try {
                    $cartRule = new CartRule((int)$rule['id_cart_rule']);
                    if (!Validate::isLoadedObject($cartRule)) {
                        continue;
                    }

                    $cart = new Cart((int)$rule['id_cart']);
                    if (!Validate::isLoadedObject($cart)) {
                        continue;
                    }

                    $sql = "DELETE FROM `" . _DB_PREFIX_ . "quantity_discount_rule_cart`
                        WHERE `id_cart` = " . (int)$cart->id . " AND `id_cart_rule` = " . (int)$rule['id_cart_rule'];

                    Db::getInstance()->execute($sql);

                    //Rule is removed in removeQuantityDiscountCartRule (be careful with gift products rule, because it removes the product twice)
                    /*
                    $sql = "DELETE FROM `"._DB_PREFIX_."quantity_discount_rule_cart_product`
                        WHERE `id_cart` = ".(int)$cart->id
                            ." AND `id_quantity_discount_rule` = ".(int)$rule['id_quantity_discount_rule'];

                    Db::getInstance()->execute($sql);

                    $cart->removeCartRule((int)$rule['id_cart_rule']);
                    */

                    $cartRule->delete();
                } catch (Exception $e) {
                    //Swallow the exception
                }
            }
        }

        return true;
    }

    public function getGiftProductsValue($with_taxes)
    {
        $products = $this->context->cart->getProducts();
        $cartRules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT, false);

        $amount = 0;

        /** Remove amount of gift products */
        foreach ($cartRules as $cartRule) {
            if ($cartRule['gift_product']) {
                foreach ($products as $product) {
                    if (empty($product['gift']) && $product['id_product'] == $cartRule['gift_product'] && $product['id_product_attribute'] == $cartRule['gift_product_attribute']) {
                        $amount += Tools::ps_round($product[$with_taxes ? 'price_wt' : 'price'], (int)$this->context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                    }
                }
            }
        }

        return $amount;
    }

        /**
     *
     * Convert amount from a currency to an other currency automatically
     * @param float $amount
     * @param Currency $currency_from if null we used the default currency
     * @param Currency $currency_to if null we used the default currency
     */
    public static function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null, $round = true)
    {
        if ($currency_from === null) {
            $currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_to === null) {
            $currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        if ($currency_from == $currency_to) {
            return (float)$amount;
        }

        if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
            $amount *= $currency_to->conversion_rate;
        } else {
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
            // Convert amount to default currency (using the old currency rate)
            $amount = $amount / $conversion_rate;
            // Convert to new currency
            $amount *= $currency_to->conversion_rate;
        }
        if ($round) {
            $amount = Tools::ps_round($amount, _PS_PRICE_DISPLAY_PRECISION_);
        }

        return $amount;
    }

    public static function convertPriceWithConversionRate($amount, $conversion_rate, $round = true)
    {
        $amount = $amount / $conversion_rate;

        if ($round) {
            $amount = Tools::ps_round($amount, _PS_PRICE_DISPLAY_PRECISION_);
        }

        return $amount;
    }

    // https://stackoverflow.com/a/16788610/1136132
    protected function makeComparer()
    {
        // Normalize criteria up front so that the comparer finds everything tidy
        $criteria = func_get_args();
        foreach ($criteria as $index => $criterion) {
            $criteria[$index] = is_array($criterion)
                ? array_pad($criterion, 3, null)
                : array($criterion, SORT_ASC, null);
        }

        $cache_key = 'QuantityDiscountRule::makeComparer'.'_'.$this->context->cart->id.'_'.$criteria[0][0].'_'.$criteria[0][1].'_'.$criteria[0][2];

        if (!Cache::isStored($cache_key)) {
            $result = function ($first, $second) use (&$criteria) {
                foreach ($criteria as $criterion) {
                    // How will we compare this round?
                    list($column, $sortOrder, $projection) = $criterion;
                    $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

                    // If a projection was defined project the values now
                    if ($projection) {
                        $lhs = call_user_func($projection, $first[$column]);
                        $rhs = call_user_func($projection, $second[$column]);
                    } else {
                        $lhs = $first[$column];
                        $rhs = $second[$column];
                    }

                    // Do the actual comparison; do not return if equal
                    if ($lhs < $rhs) {
                        return -1 * $sortOrder;
                    } else if ($lhs > $rhs) {
                        return 1 * $sortOrder;
                    }
                }

                return 0; // tiebreakers exhausted, so $first == $second
            };

            Cache::store($cache_key, $result);
        } else {
            $result = Cache::retrieve($cache_key);
        }

        return $result;
    }

    // https://stackoverflow.com/a/13943171/1136132
    /**
     * Multi-array search
     *
     * @param array $array
     * @param array $search
     * @return array
     */
    private function multiArraySearch($array, $search)
    {
        // Create the result array
        $result = array();

        // Iterate over each array element
        foreach ($array as $key => $value) {
            // Iterate over each search condition
            foreach ($search as $k => $v) {
                // If the array element does not meet the search condition then continue to the next element
                if (!isset($value[$k]) || $value[$k] != $v) {
                    continue 2;
                }
            }

            // Add the array element's key to the result array
            //$result[] = $key;
            // There should be only one coincidence
            return $key;
        }

        // Return the result array
        //return $result;
        return null;
    }

    public static function getNbObjects()
    {
        $sql = 'SELECT COUNT(qdr.`id_quantity_discount_rule`) AS nb
                FROM `'._DB_PREFIX_.'quantity_discount_rule` qdr
                WHERE `id_shop` = '.(int)Context::getContext()->shop->id;

        return (int)Db::getInstance()->getValue($sql);
    }

    public static function duplicateTableRecords($new_id, $old_id)
    {
        $tables = array(
            'quantity_discount_rule_action',
            'quantity_discount_rule_condition',
            'quantity_discount_rule_message',
        );

        $groupRelation = array();

        foreach ($tables as $table) {
            if ($table != 'quantity_discount_rule_message_lang') {
                $result = Db::getInstance()->executeS(
                    'SELECT *
                    FROM `'._DB_PREFIX_.$table.'`
                    WHERE `id_quantity_discount_rule` = '.(int)$old_id
                );
            }

            foreach ($result as $row) {
                $removedField = array_splice($row, 0, 1);

                $row['id_quantity_discount_rule'] = $new_id;

                Db::getInstance()->execute(
                    'INSERT INTO `'._DB_PREFIX_.$table.'` (`'.implode('`, `', array_keys($row)).'`)
                    VALUES (\''.implode('\', \'', $row).'\')'
                );

                $insertedId = Db::getInstance()->Insert_ID();

                if ($table == 'quantity_discount_rule_condition') {
                    // Conditions
                    $subtables = array(
                        'quantity_discount_rule_condition_attribute',
                        'quantity_discount_rule_condition_carrier',
                        'quantity_discount_rule_condition_category',
                        'quantity_discount_rule_condition_country',
                        'quantity_discount_rule_condition_currency',
                        'quantity_discount_rule_condition_gender',
                        'quantity_discount_rule_condition_group',
                        'quantity_discount_rule_condition_manufacturer',
                        'quantity_discount_rule_condition_order_state',
                        'quantity_discount_rule_condition_product',
                        'quantity_discount_rule_condition_state',
                        'quantity_discount_rule_condition_supplier',
                        'quantity_discount_rule_condition_zone',
                    );

                    foreach ($subtables as $subtable) {
                        $result2 = Db::getInstance()->executeS(
                            'SELECT *
                            FROM `'._DB_PREFIX_.$subtable.'`
                            WHERE `id_quantity_discount_rule_condition` = '.(int)$removedField['id_quantity_discount_rule_condition']
                        );

                        foreach ($result2 as $row2) {
                            $keys = array_keys($row2);

                            Db::getInstance()->execute(
                                'INSERT INTO `'._DB_PREFIX_.$subtable.'`
                                VALUES ('.(int)$insertedId.', '.(int)$row['id_quantity_discount_rule'].', '.(int)$row2[$keys[2]].')'
                            );
                        }
                    }

                    if (!isset($groupRelation[$row['id_quantity_discount_rule_group']])) {
                        $maxGroup = Db::getInstance()->getValue(
                            'SELECT MAX(id_quantity_discount_rule_group) + 1
                            FROM `'._DB_PREFIX_.'quantity_discount_rule_group`'
                        );

                        $groupRelation[$row['id_quantity_discount_rule_group']] = $maxGroup;

                        Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.'quantity_discount_rule_group`
                            VALUES ('.(int)$groupRelation[$row['id_quantity_discount_rule_group']].', '.(int)$row['id_quantity_discount_rule'].')'
                        );
                    }

                    Db::getInstance()->execute(
                        'UPDATE `'._DB_PREFIX_.'quantity_discount_rule_condition`
                        SET `id_quantity_discount_rule_group`= '.(int)$groupRelation[$row['id_quantity_discount_rule_group']].'
                        WHERE `id_quantity_discount_rule_condition` = '.(int)$insertedId
                    );
                } elseif ($table == 'quantity_discount_rule_action') {
                    // Actions
                    $subtables = array(
                        'quantity_discount_rule_action_attribute',
                        'quantity_discount_rule_action_category',
                        'quantity_discount_rule_action_manufacturer',
                        'quantity_discount_rule_action_product',
                        'quantity_discount_rule_action_supplier',
                    );

                    foreach ($subtables as $subtable) {
                        $result2 = Db::getInstance()->executeS(
                            'SELECT *
                            FROM `'._DB_PREFIX_.$subtable.'`
                            WHERE `id_quantity_discount_rule_action` = '.(int)$removedField['id_quantity_discount_rule_action']
                        );

                        foreach ($result2 as $row2) {
                            $keys = array_keys($row2);

                            Db::getInstance()->execute(
                                'INSERT INTO `'._DB_PREFIX_.$subtable.'`
                                VALUES ('.(int)$insertedId.', '.(int)$row['id_quantity_discount_rule'].', '.(int)$row2[$keys[2]].')'
                            );
                        }
                    }
                } elseif ($table == 'quantity_discount_rule_message') {
                    // Messages
                    $result2 = Db::getInstance()->executeS(
                        'SELECT *
                        FROM `'._DB_PREFIX_.'quantity_discount_rule_message_lang`
                        WHERE `id_quantity_discount_rule_message` = '.(int)$removedField['id_quantity_discount_rule_message']
                    );

                    foreach ($result2 as $row2) {
                        Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.'quantity_discount_rule_message_lang` (`id_quantity_discount_rule_message`, `id_lang`, `message`)
                            VALUES ('.(int)$insertedId.', '.(int)$row2['id_lang'].', \''.$row2['message'].'\')'
                        );
                    }
                }
            }
        }

        return true;
    }

    private function array_search_partial($haystack, $needle)
    {
        foreach ($haystack as $index => $string) {
            if (strpos($string, $needle) !== false) {
                return $index;
            }
        }
    }

    public function getCarrier($context)
    {
        if (Tools::getValue('delivery_option')) {
            $context->cart->setDeliveryOption(Tools::getValue('delivery_option'));
            //Flush cache
            $this->clearCartCache();
        }

        if ($context->cart->id_carrier) {
            return $context->cart->id_carrier;
        }

        $cart_delivery_options = $this->context->cart->getDeliveryOption();
        foreach ((array)$cart_delivery_options as $cart_delivery_option) {
            return rtrim($cart_delivery_option, ',');
        }

        return 0;
    }

    public function getLock($id)
    {
        $query = 'SELECT GET_LOCK("'.$id.'", 0);';

        return Db::getInstance()->getValue($query);
    }

    public function releaseLock($id)
    {
        $query = 'SELECT RELEASE_LOCK("'.$id.'");';

        return Db::getInstance()->getValue($query);
    }

    protected function clearCartCache()
    {
        if (version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
            Cache::clear();
            Cart::resetStaticCache();
            CartRule::resetStaticCache();
        } else {
            Cache::clean('*');
        }
    }

    // Attribute Wizard Pro compatibility
    protected static function getIdAttributeGroupbyIdAttribute($productCombination)
    {
        if (!$productCombination) {
            return false;
        }

        $sql = 'SELECT `id_attribute_group`
                FROM `' . _DB_PREFIX_ . 'attribute`
                WHERE id_attribute = ' . (int) $productCombination;

        $id_attribute_group = Db::getInstance()->getValue($sql);

        return (int)$id_attribute_group;
    }


    // Attribute Wizard Pro compatibility
    public function isInGroup($id_ag, $groups)
    {
        foreach ($groups as $order => $ag) {
            if ($ag['id_attribute_group'] == $id_ag) {
                return $order;
            }
        }
        return -1;
    }

    public function setProductAddress()
    {
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'cart_product`
            SET `id_address_delivery` = ' . (int) $this->context->cart->id_address_delivery . '
            WHERE `id_cart` = ' . (int) $this->context->cart->id . ';';
        Db::getInstance()->execute($sql);

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'customization`
            SET `id_address_delivery` = ' . (int) $this->context->cart->id_address_delivery . '
            WHERE `id_cart` = ' . (int) $this->context->cart->id . ';';

        Db::getInstance()->execute($sql);
    }
}
