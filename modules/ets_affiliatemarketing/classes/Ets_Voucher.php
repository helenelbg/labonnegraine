<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
 
if (!defined('_PS_VERSION_')) {
    exit();
}
class Ets_Voucher extends ObjectModel
{
    protected static $instance;
    /**
     * @var int
     */
    public $id_ets_am_voucher;
    /**
     * @var int
     */
    public $id_cart_rule;
    /**
     * @var int
     */
    public $id_customer;
    /**
     * @var int
     */
    public $id_product;
    /**
     * @var int
     */
    public $id_cart;
    public static $definition = array(
        'table' => 'ets_am_voucher',
        'primary' => 'id_ets_am_voucher',
        'fields' => array(
            'id_cart_rule' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_product' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_cart' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            )
        )
    );
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_Voucher();
        }
        return self::$instance;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_affiliatemarketing', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    /**
     * @param $id_customer
     * @param $id_product
     * @return bool
     */
    public static function canAddAffiliatePromoCode($id_product, $id_customer, $check_popup = false)
    {
        $context = Context::getContext();
        if (!isset($context->cart->id)) {
            if ($context->cookie->id_cart)
                $context->cart = new Cart($context->cookie->id_cart);
            else
                return $check_popup;
        }
        $added_cart = false;
        if (!isset($context->currency) && $context->cart->id_currency)
            $context->currency = new Currency($context->cart->id_currency);
        if ($products = $context->cart->getProducts()) {
            foreach ($products as $product)
                if ($product['id_product'] == $id_product)
                    $added_cart = true;
        }
        if ((Configuration::get('ETS_AM_AFF_VOUCHER_SELLER') || $id_customer != $context->customer->id) && ($added_cart || $check_popup)) {
            if (!Ets_Affiliate::isActive($id_customer)) {
                return false;
            }
            if (Configuration::get('ETS_AM_AFF_FIST_PRODUCT') && $context->customer->id) {
                $sql = "SELECT * 
                        FROM `" . _DB_PREFIX_ . "ets_am_voucher` 
                        WHERE id_customer = " . (int)$context->customer->id . " 
                        AND id_product= " . (int)$id_product;
                $result = Db::getInstance()->getRow($sql);
                if ($result) {
                    return false;
                }
            }
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'cart_rule` cr
            INNER JOIN `' . _DB_PREFIX_ . 'cart_cart_rule` ccr ON (cr.id_cart_rule =ccr.id_cart_rule)
            WHERE ccr.id_cart="' . (int)$context->cookie->id_cart . '" AND cr.reduction_product="' . (int)$id_product . '"';
            if (Db::getInstance()->getRow($sql) && !$check_popup)
                return false;
            else
                return true;
        }
        return false;
    }
    public static function getCartRuleAff(){
        return Db::getInstance()->executeS('SELECT ccr.id_cart_rule,v.id_product FROM `'._DB_PREFIX_.'cart_cart_rule` ccr
        INNER JOIN `'._DB_PREFIX_.'ets_am_voucher_combination` vc ON (vc.id_cart_rule = ccr.id_cart_rule AND vc.type="aff")
        INNER JOIN `'._DB_PREFIX_.'ets_am_voucher` v ON (v.id_cart_rule=ccr.id_cart_rule)     
        WHERE ccr.id_cart="'.(int)Context::getContext()->cart->id.'"
        ');
    }
    public static function checkFirstRuleAff($id_cart_rule,$id_product)
    {
        return !Db::getInstance()->getRow('SELECT ccr.id_cart_rule FROM `'._DB_PREFIX_.'cart_cart_rule` ccr
        INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.id_cart=ccr.id_cart) 
        INNER JOIN `'._DB_PREFIX_.'ets_am_voucher_combination` vc ON (vc.id_cart_rule = ccr.id_cart_rule AND vc.type="aff")
        INNER JOIN `'._DB_PREFIX_.'ets_am_voucher` v ON (v.id_cart_rule=ccr.id_cart_rule)     
        WHERE ccr.id_cart !="'.(int)Context::getContext()->cart->id.'" AND v.id_product="'.(int)$id_product.'" AND ccr.id_cart_rule!="'.(int)$id_cart_rule.'"
        ');
    }
    public static function addVoucherToCustomer($id_customer)
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_am_voucher` set id_customer="'.(int)$id_customer.'" WHERE id_cart_rule IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE id_cart="'.(int)Context::getContext()->cart->id.'")');
    }
    public function add($auto_date = true, $null_values = false)
    {
        parent::add($auto_date, $null_values);
    }
    public static function AddCartRuleCombination($cartRuleObj)
    {
        $use_other_voucher = (int)Configuration::get('ETS_AM_SELL_USE_OTHER_VOUCHER');
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_am_voucher_combination` (id_cart_rule,type,use_other_voucher) VALUES("'.(int)$cartRuleObj->id.'","sell","'.(int)$use_other_voucher.'")');
    }
    public static function hasOtherVoucherInCart()
    {
        $sql ='SELECT ccr.id_cart_rule FROM `'._DB_PREFIX_.'cart_cart_rule` ccr
        LEFT JOIN `'._DB_PREFIX_.'ets_am_voucher_combination` avc ON (avc.id_cart_rule = ccr.id_cart_rule)
        WHERE avc.id_cart_rule is null AND ccr.id_cart = "'.(int)Context::getContext()->cart->id.'"';
        return Db::getInstance()->getValue($sql);
    }
    public static function checkUseOtherVoucher($id_cart_rule)
    {
        if($cart_rule = Db::getInstance()->getRow('SELECT use_other_voucher FROM `'._DB_PREFIX_.'ets_am_voucher_combination` WHERE id_cart_rule='.(int)$id_cart_rule))
        {
            if(Ets_Voucher::hasOtherVoucherInCart())
            {
                return $cart_rule['use_other_voucher'];
            }
            return true;
        }
        else
        {
            $sql ='SELECT ccr.id_cart_rule FROM `'._DB_PREFIX_.'cart_cart_rule` ccr
            INNER JOIN `'._DB_PREFIX_.'ets_am_voucher_combination` avc ON (avc.id_cart_rule = ccr.id_cart_rule AND avc.use_other_voucher=0)
            WHERE ccr.id_cart = "'.(int)Context::getContext()->cart->id.'"';
            return Db::getInstance()->getValue($sql) ? false : true;
        }
        return true;
    }
    public function checkCartRuleValidity($code)
    {
        $context = Context::getContext();
        $error = '';
        $code = Validate::isCleanHtml($code) ? $code : '';
        if($code && Module::isEnabled('ets_promotion'))
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_pr_rule` r
            INNER JOIN `'._DB_PREFIX_.'ets_pr_action_rule` ar ON (r.id_ets_pr_rule = ar.id_ets_pr_rule)
            WHERE r.active=1 AND ar.code="'.pSQL($code).'"';
            if(Db::getInstance()->getRow($sql))
                return true;
        }
        if($code && ($id_cart_rule = CartRule::getIdByCode($code))){
            $id_sponsor = Db::getInstance()->getValue('SELECT id_customer FROM `'._DB_PREFIX_.'ets_am_cart_rule_seller` WHERE id_cart_rule="'.(int)$id_cart_rule.'"');
            $sponsor_exists = (int)Db::getInstance()->getValue("SELECT COUNT(*) as total FROM `"._DB_PREFIX_."ets_am_sponsor` WHERE id_customer = ".(int)$context->customer->id." AND id_shop = ".(int)$context->shop->id.' AND id_parent!='.(int)$id_sponsor.' AND level=1');
            $sql = "SELECT COUNT(*) as total FROM `"._DB_PREFIX_."ets_am_sponsor` WHERE id_parent = ".(int)$context->customer->id." AND id_shop = ".(int)$context->shop->id;
            $parent_exists = (int)Db::getInstance()->getValue($sql);
            if($id_sponsor && ($sponsor_exists || $parent_exists ||  $id_sponsor==$context->customer->id))
            {
                $error = $this->l('You cannot use this voucher');
            }
            else
            {
                $voucherCode = null;
                if(!self::canUseCartRule($context->cart->id, $id_cart_rule, $voucherCode)){
                    $error = sprintf($this->l('Cannot use voucher code %s with others voucher code'), $voucherCode);
                }
                elseif(!Ets_Voucher::checkUseOtherVoucher($id_cart_rule))
                {
                    $error = sprintf($this->l('Cannot use voucher code %s with others voucher code'), $voucherCode);
                }
            }
        }
        else{
            $error = $this->l('Your voucher code does not exist');
        }
        if($error){
            if(isset($id_cart_rule) && $id_cart_rule)
                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE id_cart='.(int)$context->cart->id.' AND id_cart_rule='.(int)$id_cart_rule);
            die(json_encode(array(
                'errors' => array($error),
                'hasError' => true,
                'quantity' => null,
            )));
        }
    }
    public static function canUseCartRule($id_cart, $id_cart_rule, &$voucherCode)
    {
        $ETS_AM_CAN_USE_OTHER_VOUCHER = (int)Configuration::get('ETS_AM_CAN_USE_OTHER_VOUCHER');
        if($ETS_AM_CAN_USE_OTHER_VOUCHER)
            return true;

        if(!Context::getContext()->customer || !Context::getContext()->customer->isLogged()){
            return true;
        }
        $hasOtherCartRule = false;
        if((int)Db::getInstance()->getValue("SELECT r.id_voucher FROM `"._DB_PREFIX_."ets_am_reward_usage` r JOIN `"._DB_PREFIX_."cart_rule` c ON r.id_voucher=c.id_cart_rule WHERE r.id_customer=".(int)Context::getContext()->customer->id.' AND r.id_voucher='.(int)$id_cart_rule))
        {
            $id_other_cart_rule = (int)Db::getInstance()->getValue("SELECT ccr.id_cart_rule FROM `"._DB_PREFIX_."cart_cart_rule` ccr WHERE id_cart=".(int)$id_cart." AND id_cart_rule !=".(int)$id_cart_rule);
            if($id_other_cart_rule)
                $hasOtherCartRule = true;
        }
        elseif($id_other_cart_rule = (int)Db::getInstance()->getValue("SELECT ccr.id_cart_rule FROM `"._DB_PREFIX_."cart_cart_rule` ccr WHERE id_cart=".(int)$id_cart." AND id_cart_rule IN (SELECT r.id_voucher FROM `"._DB_PREFIX_."ets_am_reward_usage` r JOIN `"._DB_PREFIX_."cart_rule` c ON r.id_voucher=c.id_cart_rule WHERE r.id_customer=".(int)Context::getContext()->customer->id.')'))
            $hasOtherCartRule = true;
        if($hasOtherCartRule){
            $cartRule = new CartRule($id_other_cart_rule);
            $voucherCode = $cartRule->code;
            return false;
        }
        return true;
    }
    public static function getVoucherCodeByIDCustomer($id_customer)
    {
        return Db::getInstance()->getValue('SELECT cr.code FROM `'._DB_PREFIX_.'cart_rule` cr 
        INNER JOIN `'._DB_PREFIX_.'ets_am_cart_rule_seller` crs ON (cr.id_cart_rule=crs.id_cart_rule)
        WHERE crs.id_customer ='.(int)$id_customer.' AND cr.quantity>0 AND cr.active=1 AND (cr.date_from="0000-00-00 00:00:00" OR cr.date_from <= "'.pSQL(date('Y-m-d H:i:s')).'") AND (cr.date_to="0000-00-00 00:00:00" OR cr.date_to >= "'.pSQL(date('Y-m-d H:i:s')).'")');
    }
    public static function addCartRuleToCustomer($id_customer,$cartRule)
    {
        $sql = 'INSERT INTO `'._DB_PREFIX_.'ets_am_cart_rule_seller` (id_customer,id_cart_rule,code,date_added) VALUES("'.(int)$id_customer.'","'.(int)$cartRule->id.'","'.pSQL($cartRule->code).'","'.pSQL(date('Y-m-d')).'")';
        return Db::getInstance()->execute($sql);
    }
    public static function getCartRuleSAddByCustomerSeller()
    {
       return Db::getInstance()->getValue('SELECT cr.id_cart_rule FROM `'._DB_PREFIX_.'cart_rule` cr 
        INNER JOIN `'._DB_PREFIX_.'ets_am_cart_rule_seller` crs ON (cr.id_cart_rule=crs.id_cart_rule)');
    }
}
