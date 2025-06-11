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
class Ets_Banner extends ObjectModel
{
    public $id_ets_am_banner;
    public $id_sponsor;
    public $datetime_added;
    public $img;
    public static $definition = array(
        'table' => 'ets_am_banner',
        'primary' => 'id_ets_am_banner',
        'fields' => array(
            'id_ets_am_banner' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ), 'id_sponsor' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'datetime_added' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDateFormat',
                'allow_null' => true
            ),
            'img' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            )
        )
    );
    /**
     * @param $id_customer
     * @return array|bool|null|object|string
     */
    public static function getBanerByIdCustomer($id_customer)
    {
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_banner` WHERE id_sponsor = " . (int)$id_customer;
        $banner = Db::getInstance()->getRow($sql);
        if (!$banner) {
            $banner = Configuration::get('ETS_AM_REF_DEFAULT_BANNER');
        }
        return $banner;
    }
    /**
     * @param $id_customer
     * @param $img
     * @param string $param
     * @return string
     */
    public static function renderBannerCode($id_customer, $img)
    {
        return array(
            'link_img' => Ets_AM::getBaseUrl() . '?refs=' . (int)$id_customer,
            'src_img' => $img
        );
    }
    /**
     * @param $id_customer
     * @return bool|string
     */
    public static function deleteBanner($id_customer)
    {
        $sql = "SELECT * FROM `" . _DB_PREFIX_ . "ets_am_banner` WHERE id_sponsor = " . (int)$id_customer;
        $exists = Db::getInstance()->getRow($sql);
        if ($exists) {
            $sql = "DELETE FROM `" . _DB_PREFIX_ . "ets_am_banner` WHERE id_sponsor = " . (int)$id_customer;
            Db::getInstance()->execute($sql);
            if (file_exists( EAM_PATH_IMAGE_BANER . $exists['img']))
                @unlink( EAM_PATH_IMAGE_BANER . $exists['img']);
            $banner_default = Configuration::get('ETS_AM_REF_DEFAULT_BANNER');
            if ($banner_default === false) {
                $banner_default = '';
            }
            if ($banner_default) {
                return  _PS_ETS_EAM_IMG_ . $banner_default;
            }
            return '';
        }
        return false;
    }
    /**
     * @param $delay_setting
     * @param null $context
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function showPopupBanner($delay_setting)
    {
        $delay_setting = (int)$delay_setting;
        $context = Context::getContext();
        $now = date('Y-m-d');
        $groups_showed = Configuration::get('ETS_AM_REF_INTRO_CUSTOMER_GROUP');
        $showed = false;
        //Check
        $enable_disallow_invited = (int)Configuration::get('ETS_AM_REF_INTRO_DISPLAY_JUST_ONCE');
        if ($enable_disallow_invited && $context->customer) {
            $sql = "SELECT COUNT(*) as total FROM `" . _DB_PREFIX_ . "ets_am_invitation` WHERE id_sponsor = " . (int)$context->customer->id;
            $total_invitation = Db::getInstance()->executeS($sql)[0]['total'];
            if ($total_invitation > 0) {
                return false;
            }
        }
        //CHECK GROUP USER CAN SEE POPUP
        if (!$groups_showed || $groups_showed == 'ALL') {
            $showed = true;
        } else {
            $groups_showed = explode(',', $groups_showed);
            if ($context->customer && in_array((int)$context->customer->id_default_group, $groups_showed)) {
                $showed = true;
            }
        }
        if ($showed) {
            if (!$delay_setting) {
                $date_showed = $context->cookie->__get('eam_ref_delay_popup');
                if (!$date_showed) {
                    $context->cookie->__set('eam_ref_delay_popup', $now);
                    return true;
                }
                return false;
            } else {
                if ($date_showed = $context->cookie->__get('eam_ref_delay_popup')) {
                    $date_showed = date_create($date_showed);
                    $date_now = date_create($now);
                    $diff = date_diff($date_showed, $date_now);
                    $days = $diff->format('%a');
                    if ($days >= $delay_setting) {
                        $context->cookie->__set('eam_ref_delay_popup', $now);
                        return true;
                    }
                    return false;
                } else {
                    $context->cookie->__set('eam_ref_delay_popup', $now);
                    return true;
                }
            }
        }
        return false;
    }
}