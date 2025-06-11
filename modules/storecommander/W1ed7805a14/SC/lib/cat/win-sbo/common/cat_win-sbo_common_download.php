<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;
$pdo = Db::getInstance()->getLink();
$shippingboService = ShippingboService::getInstance();

try
{
    $shopsUnitConversion = json_decode($shippingboService->getConfigValue('unitConversion'), true);

    $date = $shippingboService->getLocaleDate(new DateTimeImmutable('now'), 'yyyy_MM_dd_H_mm_ss');

    $sboType = Tools::getValue('sboType', null);
    switch ($sboType) {
    case ShippingboService::SBO_PRODUCT_TYPE_PACK_COMPONENT:
        $shippingboService->getCollectProcess()->start();
        $shippingboService->getMatchProcess()->start();
        $repository = $shippingboService->getPackRepository();
        $filename = _l('pack components');
        break;
    case ShippingboService::SBO_PRODUCT_TYPE_PACK:
        $shippingboService->getCollectProcess()->start();
        $shippingboService->getMatchProcess()->start();
        $repository = $shippingboService->getPackRepository();
        $filename = _l('packs');
        break;
    case ShippingboService::SBO_PRODUCT_TYPE_BATCH:
        $shippingboService->getCollectProcess()->start();
        $shippingboService->getMatchProcess()->start();
        $repository = $shippingboService->getBatchRepository();
        $filename = _l('batches');
        break;
    default:
        $repository = $shippingboService->getProductRepository();
        $filename = _l('products');
}
    $filename = str_replace(' ', '_', $filename);

    if ($sboType === ShippingboService::SBO_PRODUCT_TYPE_PACK_COMPONENT)
    {
        $columns = $repository->getExportComponentsColumns();
        $query = $repository->getMissingComponentsSboQuery();
        $sboDiffStatement = $pdo->prepare($query);

        $sboDiffStatement->execute([
        ':id_shop' => $shippingboService->getConfigShopsForPdo(), // TODO 1 : get config shops
    ]);
    }
    else
    {
        $columns = $repository->getExportColumns();
        $baseQuery = $repository->getMissingSboQuery(true);
        $sboDiffStatement = $pdo->prepare($repository->addSboErrorParts($baseQuery));
        $sboDiffStatement->execute([
        ':id_lang' => $shippingboService->getScAgent()->getIdLang(),
        ':id_shop' => $shippingboService->getConfigShopsForPdo(), // TODO 1 : get config shops
        ':is_locked' => false,
        ':has_error' => false,
    ]);
    }

    $filteredColumns = array_filter($columns);

    $fp = fopen('php://output', 'wb');
    fputcsv($fp, $filteredColumns);
    foreach ($sboDiffStatement->fetchAll(PDO::FETCH_ASSOC) as $line)
    {
        $values = [];
        $columnsIndexes = array_flip($filteredColumns);
        if (empty($columnsIndexes))
        {
            exit('No fields to export, please verify your export configuration in Settings');
        }
        foreach ($filteredColumns as $colName)
        {
            $index = isset($columnsIndexes[$colName]) ? $columnsIndexes[$colName] : false;
            switch ($colName) {
            case 'product_id': //batch
                $values[$index] = $line['id_component_sbo'] ?: _l('Product is missing, please try first to import missing products file to Shippingbo');
                break;
            case 'matched_quantity': //batch
                $values[$index] = $line['quantity'];
                break;
            case 'order_item_value': //batch
                $values[$index] = $line['reference'];
                break;
            case 'userRef': //product/pack
                $values[$index] = $line['reference'];
                break;
            case 'ean13': //product/pack
                $values[$index] = $line['ean13'];
                break;
            case 'title': //product/pack
                $name = $line['name'];
                if ($line['combination_name'])
                {
                    $name .= ' - '.$line['combination_name'];
                }
                $values[$index] = $name;
                break;
            case 'pictureUrl': //product/pack
                $values[$index] = $line['id_image'] ? Tools::getShopDomainSsl(true).__PS_BASE_URI__.'img/p/'.getImgPath($line['id_product'], $line['id_image'], $size = '', $format = 'jpg') : '';
                break;
            case 'weight': //product
                $values[$index] = $line['weight'] * $shopsUnitConversion['weight'];
                break;
            case 'height': //product
                $values[$index] = $line['height'] * $shopsUnitConversion['dimension'];
                break;
            case 'length': //product
                $values[$index] = $line['length'] * $shopsUnitConversion['dimension'];
                break;
            case 'width': //product
                $values[$index] = $line['width'] * $shopsUnitConversion['dimension'];
                break;
            case 'pack_product_ref': //pack
                $values[$index] = $line['pack_product_ref'];
                break;
            case 'component_product_ref': //pack
                $values[$index] = $line['component_product_ref'];
                break;
            case 'quantity': //pack
                $values[$index] = $line['quantity'];
                break;
            default:
                $values[$index] = '';
        }
        }

        fputcsv($fp, array_values($values));
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="missing_sbo_'.$filename.'_'.$date.'.csv"');

    fclose($fp);
}
catch (Exception $e)
{
    $shippingboService->sendResponse($e->getMessage());
}
