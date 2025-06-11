<?php

use Sc\Service\Shippingbo\ShippingboService;

if (!defined('STORE_COMMANDER'))
{
    exit;
}
$pdo = Db::getInstance()->getLink();
$successMessage = _l('Done');
$response = ['state' => true, 'extra' => ['code' => 200, 'message' => $successMessage]];
/** @var ShippingboService $shippingBoService */
$shippingBoService = ShippingboService::getInstance();
try
{
    if (Tools::getValue('action') == 'is_locked')
    {
        $shippingBoService->getShopRelationRepository()->updateWithLinkedItems(
            [
                'id_sbo' => null,
                'id_product' => Tools::getValue('id_product', 0),
                'id_product_attribute' => null,
                'id_sbo_source' => null,
                'type_sbo' => null,
                'is_locked' => (bool) Tools::getValue('value'),
            ]
        );
    }
    elseif (Tools::getValue('action') == 'reference')
    {
        if (Tools::getValue('id_product_attribute') != 0)
        {
            $combination = new Combination((int) Tools::getValue('id_product_attribute'), null, explode(',', $shippingBoService->getConfigValue('importToShop'))); // TODO 1 : get config shops
            $combination->id_product = Tools::getValue('id_product', 0);
            $combination->reference = Tools::getValue('value', 0);
            if (!$combination->minimal_quantity)
            {
                $combination->minimal_quantity = 1;
            }
            $combination->save();
        }
        else
        {
            $product = new Product(Tools::getValue('id_product', 0), false, $shippingBoService->getScAgent()->getIdLang(), $shippingBoService->getConfigShopsForPdo(true));
            $product->reference = Tools::getValue('value', 0);
            if (!$product->price)
            {
                $product->price = 0;
            }
            $product->save();
        }
    }
}
catch (Exception $e)
{
    $shippingBoService->addError($e);
}
finally
{
    $shippingBoService->sendResponse();
}
