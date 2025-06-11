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
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoSession.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoContext.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoSession.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoContext.php');
}


class SoNiceEtiquetageUseSession extends SoNice_Etiquetage
{

    public function __construct()
    {
        parent::__construct();

        if (Tools::getValue('debug')) {
            $this->debug = true;
        }

        if ($this->debug) {
            @ini_set('display_errors', 'on');
            @define('_PS_DEBUG_SQL_', true);
            @error_reporting(E_ALL | E_STRICT);
        }

        SoColissimoContext::restore($this->context);
    }

    public function load()
    {
        ob_start();

        $id_session = Tools::getValue('id_session');

        if (!$id_session) {
            die($this->l('No id_session received.'));
        }

        $this->context->smarty->assign(
                array(
                    'sne_labels_available' => SoColissimoSession::getSessionStatic($id_session),
                    'sne_token_order' => Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)(Validate::isLoadedObject($this->context->employee) ? $this->context->employee->id : 1))
                )
        );

        $html = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/listing_orders.tpl');
        $alias = SoColissimoSession::getSessionNameByID($id_session);

        Configuration::updateValue(
            'SONICE_ETQ_LAST_SESSION_USED',
            $id_session,
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        header('Content-Type: application/javascript');
        die($callback.'('.Tools::jsonEncode(array('console' => $output, 'alias' => $alias, 'html' => $html)).')');
    }
}

$create = new SoNiceEtiquetageUseSession();
$create->load();
