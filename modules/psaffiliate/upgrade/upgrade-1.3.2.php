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

function upgrade_module_1_3_2($module)
{
    $res = Db::getInstance()->insert(
        'aff_configuration',
        array('name' => 'affiliate_id_parameter', 'value' => 'aff'),
        false,
        false,
        Db::REPLACE
    );
    $res &= Db::getInstance()->insert(
        'aff_configuration',
        array('name' => 'affiliate_link_type', 'value' => '0'),
        false,
        false,
        Db::REPLACE
    );

    return (bool)$res;
}
