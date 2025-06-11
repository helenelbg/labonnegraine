<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\Repository\Prestashop\SegmentRepository;
use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$section = Tools::getValue('section', null);
try
{
//    if (!$section) {
//        throw new Exception(_l('Something went wrong'));
//    }

    if ($section === 'api')
    {
        $params = [
        'apiUser' => Tools::getValue('apiUser', null),
        'apiVersion' => Tools::getValue('apiVersion', 1),
    ];
        if (Tools::getValue('apiToken', '') != '')
        {
            $params['apiToken'] = SCI::encrypt(Tools::getValue('apiToken', null));
        }
        $shippingBoService->setConfig($params);
        if ($shippingBoService->checkApiConfig())
        {
            $shippingBoService->sendResponse(_l('API settings saved'));
        }
        else
        {
            throw new Exception(_l('Something went wrong, please verify your settings'));
        }
    }
    elseif ($section === 'shops')
    {
        $enabledShopsConfig = Tools::getValue('shops', Configuration::get('PS_SHOP_DEFAULT'));
        $unitCoefficientConfig = Tools::getValue('coeff', null);
        if (!$unitCoefficientConfig)
        {
            throw new Exception(_l('Missing conversion units settings'));
        }

        $enabledShopIds = implode(',', array_keys(array_filter($enabledShopsConfig)));
        $params = [
        'importToShop' => $enabledShopIds,
        'unitConversion' => json_encode($unitCoefficientConfig),
    ];

        $shippingBoService->setConfig($params);
        if ($shippingBoService->checkConfig())
        {
            $shippingBoService->sendResponse(_l('Shops settings saved'));
        }
        else
        {
            throw new Exception(_l('Something went wrong, please verify your settings'));
        }
    }
    elseif ($section === 'import')
    {
        $params = [
        'defaultDataImport' => json_encode(Tools::getValue('fields_import', $shippingBoService->getDefaultDataImport())),
    ];

        $shippingBoService->setConfig($params);
        if ($shippingBoService->checkConfig())
        {
            $shippingBoService->sendResponse(_l('Import settings saved'));
        }
        else
        {
            throw new Exception(_l('Something went wrong, please verify your settings'));
        }
    }
    elseif ($section === 'export')
    {
        $params = [
        'defaultDataExport' => json_encode(Tools::getValue('fields_export', $shippingBoService->getDefaultDataExport())),
    ];

        $shippingBoService->setConfig($params);
        if ($shippingBoService->checkConfig())
        {
            $shippingBoService->sendResponse(_l('Export settings saved'));
        }
        else
        {
            throw new Exception(_l('Something went wrong, please verify your settings'));
        }
    }
    elseif ($section === 'logs')
    {
        $params = [
        'logFilesToKeep' => (int) Tools::getValue('logFilesToKeep', 10),
    ];

        $shippingBoService->setConfig($params);
        if ($shippingBoService->checkConfig())
        {
            $shippingBoService->sendResponse(_l('Export settings saved'));
        }
        else
        {
            throw new Exception(_l('Something went wrong, please verify your settings'));
        }
    }
    elseif ($section === 'advanced')
    {
        $clearSboBuffer = Tools::getValue('clear_sbo_buffer');
        $clearSboRelation = Tools::getValue('clear_sbo_relation');
        $clearSboSegment = Tools::getValue('clear_sbo_segment');
        $clearSboService = Tools::getValue('clear_sbo_service');

        $returnExtra = [];
        if ($clearSboBuffer)
        {
            $shippingBoService->getLogger()->debug('Clearing Sbo buffer tables');
            $shippingBoService->setConfig(['lastSyncedAt' => null]);
            $shippingBoService->getProductRepository()->clear();
            $shippingBoService->getBatchRepository()->clear();
            $shippingBoService->getPackRepository()->clear();
            $shippingBoService->getLogger()->debug('Sbo buffer tables cleared');
        }
        if ($clearSboRelation)
        {
            $shippingBoService->getLogger()->debug('Clearing Sbo relation table');
            $shippingBoService->getShopRelationRepository()->clear();
            $shippingBoService->getLogger()->debug('Sbo relation table cleared');
        }
        if ($clearSboSegment)
        {
            $shippingBoService->getLogger()->debug('Clearing Sbo segments and removing related products');
            SegmentRepository::clearSegment();
            $shippingBoService->getLogger()->debug('Sbo segments and related products removed');
            $returnExtra = ['code' => 205, 'action' => 'cat_tree#refresh'];
        }
        if ($clearSboService)
        {
            $shippingBoService->getLogger()->debug('Clearing Sbo service');
            $shippingBoService->unregister();
            $shippingBoService->getLogger()->debug('Sbo service cleared');
            $shippingBoService->sendResponse(_l('To configure Shippingbo service, please open Shippingbo Management window'), [
                'callback' => [
                    'functionName' => 'dhxMenu.callEvent',
                    'params' => [
                        'onClick', ['cat_sbo'],
                    ],
                ],
                'code' => 205,
            ]);
        }
        $shippingBoService->sendResponse(_l('Ok'), $returnExtra);
    }
}
catch (Exception $e)
{
    $shippingBoService->addError($e)->sendResponse();
}
