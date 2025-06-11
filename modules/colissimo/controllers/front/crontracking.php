<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ColissimoCrontrackingModuleFrontController
 */
class ColissimoCrontrackingModuleFrontController extends ModuleFrontController
{
    
    /** @var Colissimo $module */
    public $module;


    
     /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function postProcess()
    {
        $hash = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
        if (!empty($hash) && Tools::getValue('secure_key') === $hash) {
            $labelsToUpdate = $this->module->getOrdersToTrack();
            if ($labelsToUpdate) {
                foreach ($labelsToUpdate as $labelId) {
                    $colissimoLabel = new ColissimoLabel((int) $labelId);
                    $result = $this->module->updateOrderTracking($colissimoLabel);
                    $success[] = $result;
                }
                $this->module->logger->setChannel('Crontracking');
                foreach ($success as $row){
                    $this->module->logger->info($row);
                }
                die();
            }
        } else {
            die();
        }
    }
}
