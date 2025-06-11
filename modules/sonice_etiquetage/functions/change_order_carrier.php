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


class SoNiceEtiquetageChangeOrderCarrier extends SoNice_Etiquetage
{



    public function change()
    {
        $id_order = Tools::getValue('id_order', false);
        $id_carrier = Tools::getValue('id_carrier', false);

        if (!$id_order || !$id_carrier) {
            die($this->l('No id_order or id_carrier received.'));
        }

        $order = new Order($id_order);

        if (!Validate::isLoadedObject($order)) {
            die($this->l('Unable to load the order #').$id_order);
        }

        $module_conf = unserialize(Configuration::get('SONICE_ETQ_CONF'));

        /* Change ID in order */
        $order->id_carrier = $id_carrier;
        $order->update();

        /* Change ID in So Colissimo Info */
        if ($module_conf['compatibility']) {
            $delivery_mode = Db::getInstance()->getValue(
                'SELECT `delivery_mode`
                FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
                WHERE `id_cart` = '.(int)$order->id_cart
            );
        } else {
            $delivery_mode = Db::getInstance()->getValue(
                'SELECT `type`
                FROM `'._DB_PREFIX_.'so_delivery`
                WHERE `cart_id` = '.(int)$order->id_cart
            );
        }

        if ($delivery_mode) {
            if ($module_conf['compatibility']) {
                Db::getInstance()->execute(
                    'DELETE IGNORE FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
                    WHERE `id_cart` = '.(int)$order->id_cart
                );
            } else {
                Db::getInstance()->execute(
                    'DELETE IGNORE FROM `'._DB_PREFIX_.'so_delivery`
                    WHERE `cart_id` = '.(int)$order->id_cart
                );
            }
        }

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array('console' => $output)).')');
    }
}



$order_carrier_changer = new SoNiceEtiquetageChangeOrderCarrier();
$order_carrier_changer->change();
