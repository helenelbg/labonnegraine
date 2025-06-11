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

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_2_1_6($module)
{
    $pages = array('home_page','category_page','product_page','quick_view_page','added_popup_page','cart_page','order_conf','cms_page','contact_page');
    foreach($pages as $page)
    {
        if(($position = Configuration::get('ETS_CS_'.Tools::strtoupper($page).'_POSITIONS')))
        {
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_POSITIONS',$position.',specificproducts');
        }
    }
    $module->_unregisterHooks();
    return true;
}