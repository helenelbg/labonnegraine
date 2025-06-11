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
class Ets_Participation extends ObjectModel
{
    /**
     * @var int
     */
    public $id_customer;
    /**
     * @var string
     */
    public $datetime_added;
    /**
     * @var string
     */
    public $status;
    /**
     * @var string
     */
    public $program;
    /**
     * @var int
     */
    public $id_shop;
    public $intro;
    public static $definition = array(
        'table' => 'ets_am_participation',
        'primary' => 'id_ets_am_participation',
        'multilang_shop' => true,
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'datetime_added' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'allow_null' => true
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'program' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'intro' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            )
        )
    );
    /**
     * Ets_Participation constructor.
     * @param null $id_item
     * @param null $id_lang
     * @param null $id_shop
     * @param Context|null $context
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
    }
    public function isExists()
    {
        if (!$this->program)
            return false;
        return Db::getInstance()->getValue("SELECT program FROM `" . _DB_PREFIX_ . "ets_am_participation` WHERE `id_customer` = " . (int)$this->id_customer . " AND `id_shop` = " . (int)$this->id_shop . " AND `program` = '" . pSQL($this->program) . "'");
    }
    public static function getProgramRegistered($id_customer, $program)
    {
        return Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_participation` WHERE id_customer = " . (int)$id_customer . " AND program = '" . pSQL($program) . "'");
    }
    public static function getApplicationById($id)
    {
        $sql = "SELECT p.id_ets_am_participation as id,customer.id_customer as id_customer, customer.firstname as firstname, customer.lastname as lastname, customer.email as email, customer.birthday as birthday, customer.date_add as date_add, p.status as status,  p.intro as intro
            FROM `" . _DB_PREFIX_ . "ets_am_participation` p
            LEFT JOIN `" . _DB_PREFIX_ . "customer` customer ON p.id_customer = customer.id_customer
            WHERE p.id_ets_am_participation = " . (int)$id;
        $result = Db::getInstance()->getRow($sql);
        return $result;
    }
    public static function actionProgramUser($id_user, $program, $action, $reason = null)
    {
        $trans = Module::getInstanceByName('ets_affiliatemarketing')->getTranslates();
        $module = Module::getInstanceByName('ets_affiliatemarketing');
        $status_program = 0;
        $status_app = 0;
        $program_name = '';
        if ($program == 'loy') {
            $program_name = $trans['loyalty_program'];
        } elseif ($program == 'ref') {
            $program_name = $trans['referral_program'];
        } elseif ($program == 'aff') {
            $program_name = $trans['affiliate_program'];
        } elseif ($program == 'anr') {
            $program_name = $trans['referral_and_affiliate_program'];
        }
        switch ($action) {
            case 'approve':
                $status_program = 1;
                $status_app = 1;
                break;
            case 'decline':
                $status_program = -1;
                $status_app = -2;
                break;
            case 'suspend':
                $status_program = -1;
                $status_app = -1;
                break;
        }
        $context = Context::getContext();
        $app = Db::getInstance()->getRow("SELECT * FROM `" . _DB_PREFIX_ . "ets_am_participation` WHERE id_customer = " . (int)$id_user . " AND program = '" . pSQL($program) . "'");
        if ($app && (int)$app['id_ets_am_participation']) {
            $p = new Ets_Participation((int)$app['id_ets_am_participation']);
            $p->status = $status_program;
            $p->update();
        }
        $user = Ets_User::getUserByCustomerId((int)$id_user);
        if ($user && isset($user[$program])) {
            $user = new Ets_User($user['id_ets_am_user']);
            $user->{$program} = $status_app;
            $user->id_shop = $context->shop->id;
            $user->update();
        } else {
            if ($status_app !== 0) {
                $user = new Ets_User();
                $user->id_customer = $id_user;
                $user->{$program} = $status_app;
                $user->id_shop = $context->shop->id;
                $user->status = 1;
                $user->add();
            }
        }
        if ($action == 'approve') {
            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_RES_REG')) {
                $customer = Ets_User::getUserByCustomerId($id_user);
                $data = array(
                    '{title}' => 'Your account is approved to use services of affiliate marketing',
                    '{username}' => $customer['firstname'] . ' ' . $customer['lastname'],
                    '{email}' => $customer['email'],
                    '{status}' => 'Approved',
                    '{reason}' => $reason,
                    '{program}' => $program_name
                );
                $subjects = array(
                    'translation' => $module->l('Your application was approved', 'ets_participation'),
                    'origin' => 'Your application was approved',
                    'specific' => 'ets_participation'
                );
                Ets_aff_email::send(0, 'application_approved', $subjects, $data, array('customer' => trim($customer['email'])));
            }
        } elseif ($action == 'decline') {
            if ((int)Configuration::get('ETS_AM_ENABLED_EMAIL_DECLINE_APP')) {
                $customer = Ets_User::getUserByCustomerId($id_user);
                $data = array(
                    '{title}' => 'Your application declined',
                    '{username}' => $customer['firstname'] . ' ' . $customer['lastname'],
                    '{email}' => $customer['email'],
                    '{status}' => 'Approved',
                    '{reason}' => $reason,
                    '{program}' => $program_name,
                    '{date_declined}' => date('Y-m-d H:i:s')
                );
                $subjects = array(
                    'translation' => $module->l('Your application was declined', 'ets_participation'),
                    'origin' => 'Your application was declined',
                    'specific' => 'ets_participation'
                );
                Ets_aff_email::send(0, 'application_declined', $subjects, $data, array('customer' => trim($customer['email'])));
            }
        }
        self::_clearCache();
        return true;
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
    public function update($null_values = false)
    {
        if(parent::update($null_values))
        {
            self::_clearCache();
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
        $aff->_clearCache('*',$aff->_getCacheId('list_app',false));
        return true;
    }
}