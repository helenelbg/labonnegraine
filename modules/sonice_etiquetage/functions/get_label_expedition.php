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
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoPDF.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoPDF.php');
}


class ExpeditionList extends SoNice_Etiquetage
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
    }


    public function getList()
    {
        if (!Tools::getValue('id_session')) {
            die($this->l('Unable to retrieve session ID.'));
        }

        ob_start();

        $id_session = Tools::getValue('id_session');
        $for_session = (int)Tools::getValue('for_session', 0);
        $exp_list = SoColissimoPDF::getLabelForExpedition($id_session);
        $exp_done_list = SoColissimoPDF::getLabelGoneForExpedition($id_session);

        $this->context->smarty->assign(array(
            'sne_exp_list' => $exp_list,
            'sne_exp_done_list' => $exp_done_list,
            'for_session' => $for_session
        ));

        $html = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/get_exp.tpl');
        $html_done = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/function/get_exp_done.tpl');

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();
        die($callback.'('.Tools::jsonEncode(array('html' => $html, 'html_done' => $html_done, 'console' => $output)).')');
    }
}


$page = new ExpeditionList();
$page->getList();
