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

class PsaffiliateGetaffiliatesdetailsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('getHasBeenReviewed') && Tools::getValue('ids_affiliate')) {
            $ids_affiliate = Tools::getValue('ids_affiliate');
            $data = array();
            $data['success'] = true;
            $result = Db::getInstance()->executeS('SELECT `id_affiliate` FROM `'._DB_PREFIX_.'aff_affiliates` WHERE `id_affiliate` IN ('.pSQL($ids_affiliate).') AND `has_been_reviewed`="0"');
            $data['result'] = $result;

            die(Tools::jsonEncode($data));
        }
    }
}
