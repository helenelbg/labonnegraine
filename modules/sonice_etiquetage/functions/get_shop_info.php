<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   sonice_etiquetage
 * @author    debuss-a
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice_etiquetage@common-services.com
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
}

if (Tools::getValue('token') !== Configuration::get('SONICE_ETQ_TOKEN', null, null, null)) {
    die('Wrong token');
}

function psInfo()
{
    $prestashop_info = '';
    $ps15x = version_compare(_PS_VERSION_, '1.5', '>=');

    if ($ps15x) {
        $sort = 'ORDER by `name`,`id_shop`';
    } else {
        $sort = 'ORDER by `name`';
    }

    $results = Db::getInstance()->executeS(
        'SELECT *
            FROM `'._DB_PREFIX_.'configuration`
            WHERE `name` LIKE "PS_%"
            OR `name` LIKE "SONICE_ETQ_%" '.
        pSQL($sort)
    );
    $ps_configuration = null;

    foreach ($results as $result) {
        if (preg_match('/KEY|EMAIL|PASSWORD|PASSWD|CONTEXT_DATA/', $result['name'])) {
            continue;
        }

        $value = $result['value'];

        if (base64_encode(base64_decode($value, true)) === $value) {
            $value = base64_decode($value, true);
        }

        if (@serialize(@unserialize($value)) == $value) {
            $value = '<div class="print_r">'.print_r(unserialize($value), true).'</div>';
        } else {
            $value = Tools::strlen($result['value']) > 128 ?
                Tools::substr($result['value'], 0, 128).'...' : $result['value'];
        }

        if ($ps15x) {
            $ps_configuration .= sprintf(
                '%-50s %03d %03d : %s'."\n",
                $result['name'],
                $result['id_shop'],
                $result['id_shop_group'],
                $value
            );
        } else {
            $ps_configuration .= sprintf('%-50s : %s'."\n", $result['name'], $value);
        }
    }

    $prestashop_info .= '<h1>Prestashop</h1>';
    $prestashop_info .= '<pre>';
    $prestashop_info .= 'Version: '._PS_VERSION_."\n\n";

    $prestashop_info .= "\n";
    $prestashop_info .= $ps_configuration;

    $prestashop_info .= '</pre>'."\n\n";

    return $prestashop_info;
}

function dbInfo()
{
    $tables_to_check = array(
        _DB_PREFIX_.'sonice_etq_label',
        _DB_PREFIX_.'sonice_etq_session',
        _DB_PREFIX_.'sonice_etq_session_detail'
    );

    $query = Db::getInstance()->executeS('SHOW TABLES');
    $tables = array();
    foreach ($query as $rows) {
        foreach ($rows as $t) {
            $tables[$t] = 1;
        }
    }

    $not_existing_tables = array();
    foreach ($tables_to_check as $to_check) {
        if (!isset($tables[$to_check])) {
            $not_existing_tables[] = $to_check;
            ConfigureMessage::error(
                $this->l('The table').' `'.$to_check.'` '.$this->l('was not found in your database.')
            );
        }
    }

    $tables_column = array();
    foreach ($tables_to_check as $to_check) {
        if (in_array($to_check, $not_existing_tables)) {
            $tables_column[] = null;
            continue;
        }

        $result = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.pSQL($to_check).'`');
        foreach ($result as $id => $column) {
            $result[$id] = $column['Field'];
        }
        $tables_column[] = $result;
    }

    $db_info = '<h1>Database</h1>';
    $db_info .= '<pre>';
    foreach ($tables_to_check as $id => $to_check) {
        $db_info .= 'SHOW COLUMNS FROM `'.$to_check.'` : '.(
            in_array($to_check, $not_existing_tables) ? 'N/A<br>' : print_r($tables_column[$id], true)
            );
    }
    $db_info .= '</pre>';

    return ($db_info);
}

switch (Tools::getValue('info')) {
    case 'PHP':
        ob_start();
        try {
            @phpinfo(INFO_ALL & ~INFO_CREDITS & ~INFO_LICENSE & ~INFO_ENVIRONMENT & ~INFO_VARIABLES);
        } catch (Exception $excp) {
            echo 'phpinfo()  has been disabled  for security reasons. '.$excp->getMessage();
        }
        $phpinfos = ob_get_clean();
        $phpinfos = preg_replace(
            '/(a:link.*)|(body, td, th, h1, h2.*)|(img.*)|(td, th.*)|(a:hover.*)|(class="center")/',
            '',
            $phpinfos
        );
        die(Tools::jsonEncode($phpinfos));
        break;

    case 'PS':
        $ps = array(
            'psinfo_str' => psInfo(),
            'dbinfo_str' => dbInfo()
        );

        die(Tools::jsonEncode(implode('', $ps)));
        break;

    default:
        die('Missing info value in Ajax request...');
        break;
}
