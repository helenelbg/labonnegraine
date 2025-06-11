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

function upgrade_module_2_3_4()
{
  $pageNames = [
    'order_conf',
    'home_page',
    'product_page',
    'category_page',
    'contact_page',
    'cms_page',
    'quick_view_page',
    'added_popup_page',
    'cart_page'
  ];
  foreach ($pageNames as $pageName) {
    $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($pageName) . '_LAYOUT');
    $layout = ('list' == $layout || 'tab' == $layout) ? $layout : 'list';
    $mode = Configuration::get('ETS_CS_' . Tools::strtoupper($pageName) . '_MODE');
    $mode = ('grid' == $mode || 'slide' == $mode) ? $mode : 'grid';
    Configuration::updateGlobalValue('ETS_CS_' . Tools::strtoupper($pageName) . '_LAYOUT', $layout . $mode);
    Configuration::deleteByName('ETS_CS_' . Tools::strtoupper($pageName) . '_MODE');
  }
  return true;
}