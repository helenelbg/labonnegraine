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

class SoColissimoContext
{

    public static function restore(&$context, $shop = null)
    {
        if (!version_compare(_PS_VERSION_, '1.5', '>=') || !Shop::isFeatureActive()) {
            return (true);
        }

        if ($context instanceof Context && !$context->controller instanceof Controller) {
            $context->controller = new FrontController();
        }

        $stored_contexts = unserialize(Configuration::getGlobalValue('SONICE_ETQ_CONTEXT'));

        $context_key = null;
        if ($shop instanceof Shop) {
            $context_key = self::getKey($shop);
        } elseif (Tools::getValue('context_key')) {
            $context_key = Tools::getValue('context_key');
        } elseif (isset($context->shop) && Validate::isLoadedObject($context->shop)) {
            $context_key = self::getKey($context->shop);
        }

        if (!is_array($stored_contexts) || !count($stored_contexts) || !$context_key) {
            printf('%s(#%d): Wrong context, please configure your module first', basename(__FILE__), __LINE__);
            return (false);
        }

        if (!isset($context->employee) || !Validate::isLoadedObject($context->employee)) {
            // Keep current employee else use the last one who configure the module
            $context->employee = new Employee($stored_contexts[$context_key]['employee']);
        }
        $context->shop = new Shop($stored_contexts[$context_key]['shop']);

        return (true);
    }



    public static function save(Context $context, $employee = null)
    {
        if (!version_compare(_PS_VERSION_, '1.5', '>=') || !Shop::isFeatureActive()) {
            return (true);
        }

        $stored_contexts = unserialize(Configuration::getGlobalValue('SONICE_ETQ_CONTEXT'));

        // Fix older way to save context in SoNice Etiquetage 1.0
        if (!is_array($stored_contexts) || $stored_contexts instanceof Shop) {
            $stored_contexts = array();
        }

        $so_colissimo_context_data = array();

        if (Validate::isLoadedObject($employee)) {
            $context->employee = $employee;
        }

        $context_key = self::getKey($context->shop);
        if (!$context_key) {
            return (false);
        }

        $so_colissimo_context_data['shop'] = $context->shop->id;
        $so_colissimo_context_data['employee'] = $context->employee->id;
        $stored_contexts[$context_key] = $so_colissimo_context_data;

        return (Configuration::updateGlobalValue('SONICE_ETQ_CONTEXT', serialize($stored_contexts)));
    }



    public static function getKey($shop)
    {
        if (!$shop instanceof Shop) {
            return (false);
        }

        $id_shop = (int)$shop->id;
        $id_shop_group = (int)$shop->id_shop_group;

        // create a short key
        $context_key = dechex(crc32(sprintf('%02d_%02d', $id_shop, $id_shop_group)));

        return ($context_key);
    }
}
