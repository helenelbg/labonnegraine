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
class Ets_User extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_user;
    /**
     * @var int
     */
    public $id_customer;
    /**
     * @var int
     */
    public $ref;
    /**
     * @var int
     */
    public $aff;
    /**
     * @var int
     */
    public $loy;
    /**
     * @var int
     */
    public $status;
    public $id_shop;
    public static $definition = array(
        'table' => 'ets_am_user',
        'primary' => 'id_ets_am_user',
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
            ),
            'loy' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'ref' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'aff' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
            ),
        )
    );
    public static function getUserByCustomerId($id_customer)
    {
        return Db::getInstance()->getRow("SELECT user.*, customer.email as email,customer.id_lang 
            FROM `" . _DB_PREFIX_ . "ets_am_user` user
            LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON user.id_customer = customer.id_customer
            WHERE user.id_customer = " . (int)$id_customer);
    }
    public static function processActionStatus($id_customer, $action)
    {
        $context = Context::getContext();
        $user = self::getUserByCustomerId($id_customer);
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $actions = array(
            array(
                'label' => $trans['View'],
                'href' => $context->link->getAdminLink('AdminModules', true) . '&configure=ets_affiliatemarketing&tabActive=reward_users&id_reward_users=' . (int)$id_customer . '&viewreward_users',
                'icon' => 'search',
                'class' => '',
                'action' => '',
                'id' => '',
            )
        );
        if ($action == 'active') {
            $actions[] = array(
                'label' => $trans['suspend'],
                'class' => 'js-action-user-reward',
                'action' => 'decline',
                'id' => $id_customer,
                'icon' => 'times'
            );
            if ($user) {
                $res = Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_user` SET status = 1 WHERE id_customer = " . (int)$id_customer . " AND id_shop = " . (int)$context->shop->id);
                return array(
                    'success' => $res,
                    'actions' => $actions
                );
            }
            return array(
                'success' => true,
                'actions' => $actions
            );
        } elseif ($action == 'decline') {
            $actions[] = array(
                'label' => $trans['Active'],
                'class' => 'js-action-user-reward',
                'action' => 'active',
                'id' => $id_customer,
                'icon' => 'check'
            );
            if ($user) {
                $res = Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "ets_am_user` SET status = -1 WHERE id_customer = " . (int)$id_customer . " AND id_shop = " . (int)$context->shop->id);
                return array(
                    'success' => $res,
                    'actions' => $actions
                );
            } else {
                $u = new Ets_User();
                $u->id_customer = $id_customer;
                $u->status = -1;
                $u->id_shop = $context->shop->id;
                $u->add();
                return array(
                    'success' => true,
                    'actions' => $actions
                );
            }
        }
        return array(
            'success' => false,
            'actions' => array()
        );
    }
    public static function getTypeAlert($id_customer,$program)
    {
        $programs = array('loy', 'ref', 'aff');
        $alert_type ='';
        if(in_array($program,$programs))
        {
            if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_user` WHERE status=-1 AND id_customer='.(int)$id_customer))
                $alert_type = 'account_banned';
            elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_user` WHERE '.pSQL($program).'=-1 AND id_customer='.(int)$id_customer))
                $alert_type = 'program_banned';
            elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_user` WHERE '.pSQL($program).'=1 AND id_customer='.(int)$id_customer))
                $alert_type = 'registered';
            elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_user` WHERE '.pSQL($program).'=-2 AND id_customer='.(int)$id_customer))
                $alert_type = 'program_decline';
            elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_participation` WHERE program ="'.pSQL($program).'" AND status=0 AND id_customer='.(int)$id_customer))
                $alert_type = 'register_success';
            elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_participation` WHERE program ="'.pSQL($program).'" AND status=1 AND id_customer='.(int)$id_customer))
                $alert_type = 'registered';
            elseif(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_am_participation` WHERE program ="'.pSQL($program).'" AND status < 0 AND id_customer='.(int)$id_customer))
                $alert_type = 'program_banned';
        }
        return $alert_type;
    }
    public static function searchCustomer($query)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE
            active=1 AND id_shop="'.(int)Context::getContext()->shop->id.'" AND ( 
             firstname like "%'.pSQL($query).'%" 
            OR lastname like "%'.pSQL($query).'%" 
            OR CONCAT(firstname," ",lastname) like "%'.pSQL($query).'%" 
            OR email like "%'.pSQL($query).'%"
            '.(Validate::isInt($query) ? ' OR id_customer= '.(int)$query : '').'
            )');
    }
    public static function addUserReward($id_customer,$customer_loyalty=false,$customer_referral=false,$customer_affiliate=false)
    {
        $aff_customer_loyalty = !Configuration::get('ETS_AM_LOYALTY_REGISTER') ? 0 : $customer_loyalty;
        $aff_customer_referral = !Configuration::get('ETS_AM_REF_REGISTER_REQUIRED') ? 0 : $customer_referral;
        $aff_customer_affiliate = !Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED') ? 0 : $customer_affiliate;
        $sql = 'SELECT * FROM (
            SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_am_participation` WHERE id_shop = ' . (int)Context::getContext()->shop->id . '  AND (0 ' . ($aff_customer_affiliate ? ' OR program="aff"' : '') . ($aff_customer_loyalty ? ' OR program="loy"' : '') . ($aff_customer_referral ? ' OR program="ref"' : '') . ')
        UNION
            SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_am_reward` r WHERE id_shop = ' . (int)Context::getContext()->shop->id . '  AND (0 ' . ($aff_customer_affiliate ? ' OR program="aff"' : '') . ($aff_customer_loyalty ? ' OR program="loy"' : '') . ($aff_customer_referral ? ' OR program="ref"' : '') . ')
        ' . ($aff_customer_referral ? '
            UNION
            SELECT id_parent as id_customer FROM `' . _DB_PREFIX_ . 'ets_am_sponsor` s WHERE s.`level` = 1 AND id_shop = ' . (int)Context::getContext()->shop->id : '') . '
        UNION
            SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_am_user` WHERE id_shop = ' . (int)Context::getContext()->shop->id . ' AND ( 0 ' . ($aff_customer_affiliate ? ' OR aff=1' : '') . ($aff_customer_loyalty ? ' OR loy=1' : '') . ($aff_customer_referral ? ' OR ref=1' : '') . ')
        )  app WHERE app.id_customer=' . (int)$id_customer;
        if (!Db::getInstance()->getRow($sql)) {
            if (!Db::getInstance()->getValue('SELECT id_customer FROM `' . _DB_PREFIX_ . 'ets_am_user` WHERE id_customer="' . (int)$id_customer . '" AND id_shop = ' . (int)Context::getContext()->shop->id)) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_am_user` (id_customer,loy,ref,aff,status,id_shop) VALUES("' . (int)$id_customer . '","' . (int)$customer_loyalty . '","' . (int)$customer_referral . '","' . (int)$customer_affiliate . '","1","' . (int)Context::getContext()->shop->id . '")');
                $data = array(
                    'id_customer' => $id_customer,
                    'datetime_added' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'program' => 'aff',
                    'id_shop' => Context::getContext()->shop->id,
                    'intro' => '',
                );
                if ($aff_customer_loyalty) {
                    $data['program'] = 'loy';
                    Db::getInstance()->insert('ets_am_participation', $data, true);
                }
                if ($aff_customer_affiliate) {
                    $data['program'] = 'aff';
                    Db::getInstance()->insert('ets_am_participation', $data, true);
                }
                if ($aff_customer_referral) {
                    $data['program'] = 'ref';
                    Db::getInstance()->insert('ets_am_participation', $data, true);
                }
            } else {
                $set_value = '';
                if ($aff_customer_loyalty || !Configuration::get('ETS_AM_LOYALTY_REGISTER'))
                    $set_value .= ' loy = 1,';
                if ($aff_customer_affiliate || !Configuration::get('ETS_AM_AFF_REGISTER_REQUIRED'))
                    $set_value .= ' aff = 1,';
                if ($aff_customer_referral || !Configuration::get('ETS_AM_REF_REGISTER_REQUIRED'))
                    $set_value .= ' ref = 1,';
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_am_user` SET ' . trim($set_value, ',') . ' WHERE  id_customer="' . (int)$id_customer . '" AND id_shop="' . (int)Context::getContext()->shop->id . '"');
            }
            return true;
        }
        return false;
    }
    public function update($null_values = false)
    {
        if(parent::update($null_values))
        {
            self::_clearCache();
        }
        return false;
    }

    public function add($auto_date= true,$null_values = false)
    {
        if(parent::add($auto_date,$null_values))
        {
            self::_clearCache();
            return true;
        }

        return false;
    }
    public function delete()
    {
        if(parent::delete())
        {
            self::_clearCache();
            return true;
        }
        return false;
    }

    public static function _clearCache()
    {
        /** @var Ets_affiliatemarketing $aff */
        $aff = Module::getInstanceByName('ets_affiliatemarketing');
        $aff->_clearCache('*',$aff->_getCacheId('dashboard',false));
        return true;
    }
}