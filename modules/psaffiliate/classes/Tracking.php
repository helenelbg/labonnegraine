<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Tracking extends ObjectModel
{
    public $id;
    public $id_tracking;
    public $id_affiliate;
    public $id_campaign;
    public $id_customer;
    public $ip;
    public $unique_visit;
    public $date;
    public $referral;
    public $url;
    public $commission = 0;

    public static $definition = array(
        'table' => 'aff_tracking',
        'primary' => 'id_tracking',
        'fields' => array(
            'id_tracking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_affiliate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_campaign' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'ip' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 48),
            'unique_visit' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'size' => 32),
            'date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'referral' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'url' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255),
            'commission' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
        ),
    );

    public function __construct($id = null)
    {
        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }
        $this->module = Module::getInstanceByName('psaffiliate');
        if (!class_exists('Psaffiliate')) {
            $this->moduleObj = Module::getInstanceByName('psaffiliate');
        } else {
            $this->moduleObj = new Psaffiliate;
        }
        $this->moduleObj->loadClasses('AffConf');
        parent::__construct($id);
    }

    public function registerSessionAffiliate($id_affiliate = false, $force = false, $id_campaign = false)
    {
        if (!$id_affiliate) {
            $id_affiliate = self::getIdAffiliateFromLink();
        }
        if ($id_affiliate && Validate::isInt($id_affiliate)) {
            if (!isset($this->context->cookie->id_session_affiliate) || $force) {
                $this->context->cookie->id_session_affiliate = $id_affiliate;
                $this->context->cookie->id_session_campaign = $id_campaign;
                $this->context->cookie->session_affiliate_started = time();
                if ($id_campaign) {
                    Campaign::setLastActive($id_campaign);
                }
            }
        }
    }

    public static function checkSessionAffiliate()
    {
        $context = Context::getContext();
        if (isset($context->cookie->session_affiliate_started)) {
            $now = time();
            $session_affiliate_started = (int)$context->cookie->session_affiliate_started;
            $max_difference = (float)AffConf::getConfig('days_remember_affiliate');
            $max_difference = $max_difference * 60 * 60 * 24;
            $difference = $now - $session_affiliate_started;
            if ($difference > $max_difference) {
                unset($context->cookie->session_affiliate_started);
                unset($context->cookie->id_session_affiliate);
                unset($context->cookie->id_session_campaign);
            } else {
                return false;
            }
        }

        return true;
    }

    public static function hasSessionAffiliate()
    {
        $context = Context::getContext();

        return isset($context->cookie->id_session_affiliate);
    }

    public function startTracking()
    {
        /* First we check if the user agent is Googlebot */
        if (strpos(Tools::strtolower($_SERVER['HTTP_USER_AGENT']), 'googlebot') !== FALSE) {
            return;
        }
        $cookie = $this->context->cookie;
        if ($cookie) {
            $override_previous_affiliate = (bool)AffConf::getConfig('override_previous_affiliate');
            $continue = $this->checkSessionAffiliate() || $override_previous_affiliate;
            $id_affiliate = self::getIdAffiliateFromLink();
            if ($continue && $id_affiliate && Validate::isInt($id_affiliate)) {
                $this->id_affiliate = $id_affiliate;
                $id_campaign = (int)Tools::getValue('id_campaign');
                if ($id_campaign) {
                    $this->moduleObj->loadClasses('Campaign');
                    if (!Campaign::campaignBelongsToAffiliate($id_campaign, $this->id_affiliate)) {
                        $id_campaign = 0;
                    }
                }
                $this->id_campaign = $id_campaign;
                if($this->context->cart->id) {
                    $this->moduleObj->associateCartToAffiliate($this->context->cart->id, $this->id_affiliate, $this->id_campaign);
                }
                if (isset($this->context->customer)) {
                    $this->id_customer = (int)$this->context->customer->id;
                    if ($this->moduleObj->getCustomerId($this->id_affiliate) != $this->id_customer) {
                        $this->moduleObj->loadClasses('Affiliate');
                        $affiliate = new Affiliate($id_affiliate);
                        if (Validate::isLoadedObject($affiliate)) {
                            $this->ip = Tools::getRemoteAddr();
                            $this->unique_visit = $this->checkIfUnique();
                            $this->date = date('Y-m-d H:i:s');
                            if (isset($_SERVER['HTTP_REFERER'])) {
                                $this->referral = pSQL($_SERVER['HTTP_REFERER']);
                            }
                            $current_url = (Tools::usingSecureMode() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                            $this->url = $current_url;
                            $rates = $affiliate->getRates(array('click', 'unique_click'));
                            $this->commission = $this->unique_visit ? $rates['per_unique_click'] : $rates['per_click'];
                            $this->add();
                        }
                    } else {
                        return false;
                    }
                }
                $this->registerSessionAffiliate(
                    $id_affiliate,
                    $override_previous_affiliate,
                    $id_campaign
                );
            }
        }
    }

    public function startTracking2($id_affiliate)
    {
        $cookie = $this->context->cookie;
        if ($cookie) {
            $override_previous_affiliate = (bool)AffConf::getConfig('override_previous_affiliate');
            $continue = $this->checkSessionAffiliate() || $override_previous_affiliate;
            
            if ($continue && $id_affiliate && Validate::isInt($id_affiliate)) {
                $this->id_affiliate = $id_affiliate;
                $id_campaign = (int)Tools::getValue('id_campaign');
                if ($id_campaign) {
                    $this->moduleObj->loadClasses('Campaign');
                    if (!Campaign::campaignBelongsToAffiliate($id_campaign, $this->id_affiliate)) {
                        $id_campaign = 0;
                    }
                }
                $this->id_campaign = $id_campaign;
                if($this->context->cart->id) {
                    $this->moduleObj->associateCartToAffiliate($this->context->cart->id, $this->id_affiliate, $this->id_campaign);
                }
                if (isset($this->context->customer)) {
                    $this->id_customer = (int)$this->context->customer->id;
                    if ($this->moduleObj->getCustomerId($this->id_affiliate) != $this->id_customer) {
                        $this->moduleObj->loadClasses('Affiliate');
                        $affiliate = new Affiliate($id_affiliate);
                        if (Validate::isLoadedObject($affiliate) && $affiliate->active == 1) {
                            $this->ip = Tools::getRemoteAddr();
                            $this->unique_visit = $this->checkIfUnique();
                            $this->date = date('Y-m-d H:i:s');
                            if (isset($_SERVER['HTTP_REFERER'])) {
                                $this->referral = pSQL($_SERVER['HTTP_REFERER']);
                            }
                            $current_url = (Tools::usingSecureMode() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                            $this->url = $current_url;
                            $rates = $affiliate->getRates(array('click', 'unique_click'));
                            $this->commission = $this->unique_visit ? $rates['per_unique_click'] : $rates['per_click'];
                            $this->add();
                        }
                        else
                        {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                $this->registerSessionAffiliate(
                    $id_affiliate,
                    $override_previous_affiliate,
                    $id_campaign
                );
            }
        }
    }

    public function startTracking3($id_affiliate, $id_customer)
    {            
                $this->id_affiliate = $id_affiliate;
                $id_campaign = (int)Tools::getValue('id_campaign');
                $this->id_campaign = $id_campaign;
                $override_previous_affiliate = (bool)AffConf::getConfig('override_previous_affiliate');

                    $this->id_customer = (int)$id_customer;
                    if ($this->moduleObj->getCustomerId($this->id_affiliate) != $this->id_customer) {
                        $this->moduleObj->loadClasses('Affiliate');
                        $affiliate = new Affiliate($id_affiliate);
                        if (Validate::isLoadedObject($affiliate)) {
                            $this->ip = Tools::getRemoteAddr();
                            $this->unique_visit = $this->checkIfUnique();
                            $this->date = date('Y-m-d H:i:s');
                            if (isset($_SERVER['HTTP_REFERER'])) {
                                $this->referral = pSQL($_SERVER['HTTP_REFERER']);
                            }
                            $current_url = (Tools::usingSecureMode() ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                            $this->url = $current_url;
                            $rates = $affiliate->getRates(array('click', 'unique_click'));
                            $this->commission = $this->unique_visit ? $rates['per_unique_click'] : $rates['per_click'];
                            $this->add();
                        }
                    } else {
                        return false;
                    }
            
    }

    public function checkIfUnique()
    {
        if ($this->hasSessionAffiliate()) {
            return false;
        } else {
            $date_selectfrom = date(
                'Y-m-d H:i:s',
                strtotime('-'.AffConf::getConfig('days_remember_affiliate').' day', time())
            );
            $select = "SELECT COUNT(*) FROM `"._DB_PREFIX_."aff_tracking` WHERE `ip`='".pSQL(Tools::getRemoteAddr())."' AND `date`>'".pSQL($date_selectfrom)."'";
            $return = Db::getInstance()->getValue($select);

            return !(bool)$return;
        }
    }

    public function toggleUnique()
    {
        $id_tracking = (int)Tools::getValue('id_tracking');
        if ($id_tracking) {
            $sql = Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."aff_tracking` SET `unique_visit` = (CASE WHEN `unique_visit`='1' THEN '0' WHEN `unique_visit`='0' THEN '1' END) WHERE `id_tracking`='".(int)$id_tracking."' LIMIT 1;");

            return $sql;
        }

        return false;
    }

    public static function getAffiliateTraffic($id_affiliate = false, $limit = false, $reverse = true)
    {
        $sql = "SELECT at.*";
        if (!$id_affiliate) {
            $sql .= ', CONCAT("#", af.`id_affiliate`, " - ", IF(af.`id_customer` <> 0, CONCAT(c.`firstname`, " ", c.`lastname`), CONCAT(af.`firstname`, " ", af.`lastname`))) as `affiliate_name`';
        }
        $sql .= " FROM `"._DB_PREFIX_."aff_tracking` at";
        if ($id_affiliate) {
            $sql .= " WHERE at.`id_affiliate`='".pSQL($id_affiliate)."'";
        } else {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'aff_affiliates` af on (af.`id_affiliate`=at.`id_affiliate`)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = af.`id_customer`)
            ';
        }
        if ($reverse) {
            $sql .= " ORDER BY at.`id_tracking` DESC";
        }
        if ($limit) {
            $sql .= " LIMIT ".(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getCampaignTraffic($id_campaign = false, $limit = false, $reverse = true)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."aff_tracking`";
        if ($id_campaign) {
            $sql .= " WHERE `id_campaign`='".(int)$id_campaign."'";
        }
        if ($reverse) {
            $sql .= " ORDER BY `id_tracking` DESC";
        }
        if ($limit) {
            $sql .= " LIMIT ".(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function getIdAffiliateFromLink()
    {
        $link_param = AffConf::getConfig('affiliate_id_parameter');
        $id_affiliate = Tools::getValue($link_param);
        if (!$id_affiliate) {
            $id_affiliate = Tools::getValue('aff');
            if (!$id_affiliate) {
                return 0;
            }
        }
        $aff_id_has_year_prefix = ctype_alpha($id_affiliate[0]);
        if ($aff_id_has_year_prefix) {
            $id_affiliate = Tools::substr($id_affiliate, 3);
            $id_affiliate = (int)$id_affiliate;
        }

        return $id_affiliate;
    }
}
