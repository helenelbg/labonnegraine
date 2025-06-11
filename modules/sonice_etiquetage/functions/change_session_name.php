<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * @package   sonice_etiquetage
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @license   Commercial license
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoSession.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoSession.php');
}


class SoNiceEtiquetageChangeSessionName extends SoNice_Etiquetage
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



    public function change()
    {
        ob_start();

        $name = Tools::getValue('name');
        $id_session = Tools::getValue('id_session');

        if (!$id_session || !$name) {
            die($this->l('No id_session or name received.'));
        }

        $session = new SoColissimoSession((int)$id_session);
        $session->alias = $name;
        $result = $session->update();

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array('console' => $output, 'result' => $result)).')');
    }
}



$create = new SoNiceEtiquetageChangeSessionName();
$create->change();
