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
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoSession.php');
}


class SoNiceEtiquetageModifyWeight extends SoNice_Etiquetage
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



    public function modify()
    {
        ob_start();

        $id_order = Tools::getValue('id_order');
        $element = Tools::getValue('class');
        $new_val = Tools::getValue('new_val');

        if (!Validate::isInt($id_order)) {
            die($this->l('No address ID received.'));
        }
        if (!isset($element) || !Tools::strlen($element)) {
            die($this->l('No class element received.'));
        }
        if (!isset($new_val) || !Tools::strlen($new_val)) {
            die($this->l('No new value for the address received.'));
        }

        $result = 'false';
        switch ($element) {
            case 'weight':
                if (!Validate::isFloat($new_val)) {
                    die($this->l('The new weight is not correct.'));
                }

                $result = SoColissimoSession::setOrderWeightStatic($id_order, $new_val);
                break;

            case 'weight_product':
                if (!Validate::isFloat($new_val)) {
                    die($this->l('The new weight is not correct.'));
                }

                $result = SoColissimoSession::setOrderProductWeightStatic($id_order, $new_val);
                break;

            default:
                echo $this->l('No modification was done because it did not correspond to a possible element.');
                break;
        }

        if (!$result) {
            echo '<pre>'.($result).'</pre>';
        }

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array('console' => $output)).')');
    }
}



$create = new SoNiceEtiquetageModifyWeight();
$create->modify();
