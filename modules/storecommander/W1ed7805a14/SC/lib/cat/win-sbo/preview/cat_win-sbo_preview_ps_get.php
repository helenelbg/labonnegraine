<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\service\Shippingbo\GridFactory\GridFactory;
use Sc\Service\Shippingbo\ShippingboService;

$shippingboService = ShippingboService::getInstance();
try
{
    $sboType = Tools::getValue('sboType', null);
    $tabId = Tools::getValue('tabId', null);
    $totalCount = Tools::getValue('totalCount', null);
    $totalCount = $totalCount === 'null' ? null : $totalCount;
    $posStart = (int) Tools::getValue('posStart', 0);

    $pdo = Db::getInstance()->getLink();

    switch ($sboType) {
        case 'packs':
            $repository = $shippingboService->getPackRepository();
            break;
        case 'batches':
            $repository = $shippingboService->getBatchRepository();
            break;
        default:
            $repository = $shippingboService->getProductRepository();
    }

    $baseQuery = $repository->getMissingPsQuery($posStart / ShippingboService::GRID_RESULTS_PER_PAGE);

    $results = [];
    if ($totalCount)
    {// no request if empty
        switch ($tabId) {
            case 'awaiting':
                $stmt = $pdo->prepare($repository->addPsErrorParts($baseQuery));
                $stmt->execute([
                    ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
                    ':has_error' => false,
                ]);
                $displayGroupPosition = [
                    GridFactory::STATUS_NO_ERROR,
                ];
                break;
            default:
                $stmt = $pdo->prepare($repository->addPsErrorParts($baseQuery));
                $stmt->execute([
                    ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
                    ':has_error' => true,
                ]);
                $displayGroupPosition = [
                    GridFactory::STATUS_SKU_TOO_LONG,
                ];
        }

        // configure column processor
        $gridFactory = $shippingboService->getGridFactory($sboType)
            ->setDisplayGroupPosition($displayGroupPosition)
            ->setColumnsToDisplay([
                'id_sbo',
                'user_ref',
                'type_sbo',
                'statusLabel',
                'title',
                'width',
                'height',
                'length',
                'weight',
                'groupLabel',
                'groupPosition',
            ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // build rows
    $xml = [];

    foreach ($results as $row)
    {
        $row_xml = [];
        foreach ($gridFactory->processColumns($row) as $key => $column)
        {
            $row_xml[] = '<cell scope="'.$key.'" class="'.$column['cellClass'].'"><![CDATA['.$column['value'].']]></cell>';
        }
        $xml[] = '<row id="'.$row['id_sbo'].'">
        <userdata name="id_sbo">'.(int) $row['id_sbo'].'</userdata>
        '.implode("\r\n\t", $row_xml).'</row>';
    }

    // send header
    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }

    // build xml
    echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $totalCountAttribute = '';
    if ($posStart === 0)
    {
        $totalCountAttribute = 'total_count="'.$totalCount.'"'; // fonctionnement dhtmlx 5 : attribut present uniquement sur premiere page (?!)
    }
}
catch (\Exception $e)
{
    $shippingboService->getLogger()->error($e->getMessage());
}

?>
<rows pos="<?php echo (int) $posStart; ?>" <?php echo $totalCountAttribute; ?>>
    <head>
        <afterInit>
            <call command="attachHeader">
                <param>#numeric_filter,#text_filter,#text_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter</param>
            </call>
        </afterInit>
        <column id="id_sbo" width="70" type="ro" align="left"
                sort="int"><?php echo _l('Id ShippingBo'); ?></column>
        <column id="user_ref" width="130" type="ro" align="left"
                sort="str"><?php echo _l('SKU logistic'); ?></column>
        <column id="type_shippingbo" width="70" type="ro" align="left"
                sort="str"><?php echo _l('Type Shippingbo'); ?></column>
        <column id="statusLabel" width="200" type="ro" align="left" sort="str"><?php echo _l('Status'); ?></column>
        <column id="name" width="130" type="ro" align="left" sort="str"><?php echo _l('Title logistic'); ?></column>
        <column id="width" width="70" type="ro" align="left"
                sort="str"><?php echo _l('Width').' ('._l('cm').')'; ?></column>
        <column id="height" width="70" type="ro" align="left"
                sort="str"><?php echo _l('Height').' ('._l('cm').')'; ?></column>
        <column id="length" width="70" type="ro" align="left"
                sort="str"><?php echo _l('Length').' ('._l('cm').')'; ?></column>
        <column id="weight" width="70" type="ro" align="left"
                sort="str"><?php echo _l('Weight').' ('._l('gram').')'; ?></column>
        <column id="groupLabel" width="70" type="ro" hidden="true"></column>
        <column id="groupPosition" width="70" type="ro" hidden="true"></column>
    </head>
    <?php
    echo implode("\r\n", $xml);
?>
</rows>
