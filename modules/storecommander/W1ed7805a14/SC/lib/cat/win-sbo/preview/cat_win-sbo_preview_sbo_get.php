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
    $id_lang = (int) Tools::getValue('id_lang');
    $sboType = Tools::getValue('sboType', null);
    $tabId = Tools::getValue('tabId', 'error');
    $totalCount = (int) Tools::getValue('totalCount', 0);

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

    $baseQuery = $repository->getMissingSboQuery(true, $posStart / ShippingboService::GRID_RESULTS_PER_PAGE);
    $results = [];
    $xml = [];
    if ($totalCount)
    {// no request if empty
        switch ($tabId) {
            case 'awaiting':
                $stmt = $pdo->prepare($repository->addSboErrorParts($baseQuery));
                $stmt->execute([
                    ':id_lang' => $shippingboService->getScAgent()->getIdLang(),
                    ':id_shop' => $shippingboService->getConfigShopsForPdo(),
                    ':is_locked' => false,
                    ':has_error' => false,
                ]);
                $displayGroupPosition = [
                    GridFactory::STATUS_NO_ERROR,
                ];
                break;
            case 'locked':
                $stmt = $pdo->prepare($baseQuery);
                $stmt->execute([
                    ':id_lang' => $shippingboService->getScAgent()->getIdLang(),
                    ':id_shop' => $shippingboService->getConfigShopsForPdo(), // TODO 1 : get config shops
                    ':is_locked' => true,
                ]);
                $displayGroupPosition = [
                    GridFactory::STATUS_IS_LOCKED,
                ];
                break;
            default:
                $stmt = $pdo->prepare($repository->addSboErrorParts($baseQuery));
                $stmt->execute([
                    ':id_lang' => $shippingboService->getScAgent()->getIdLang(),
                    ':id_shop' => $shippingboService->getConfigShopsForPdo(), // TODO 1 : get config shops
                    ':is_locked' => false,
                    ':has_error' => true,
                ]);
                $displayGroupPosition = [
                    GridFactory::STATUS_MISSING_REFERENCE,
                    GridFactory::STATUS_DUPLICATE_REFERENCE,
                ];
        }

        $gridFactory = $shippingboService->getGridFactory($sboType)
            ->setDisplayGroupPosition($displayGroupPosition)
            ->setColumnsToDisplay([
                'name',
                'id_product',
                'id_product_attribute',
                'active',
                'is_locked',
                'reference',
                'statusLabel',
                'groupPosition',
            ]);

        $i = 1;
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $key => $row)
        {
            $row_xml = [];
            foreach ($gridFactory->processColumns($row) as $column)
            {
                $row_xml[] = '<cell class="'.$column['cellClass'].'"><![CDATA['.$column['value'].']]></cell>';
            }
            $xml[] = '<row id="'.$row['rowId'].'">'.implode("\r\n\t", $row_xml).'</row>';
        }
    }
    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";

    $totalCountAttribute = '';
    if ($posStart === 0)
    {
        $totalCountAttribute = 'total_count="'.$totalCount.'"'; // fonctionnement dhtmlx 5 : attribut present uniquement sur premiere page (?!)
    }
}
catch (\Error $e)
{
    $shippingboService->getLogger()->error($e->getMessage());
}

    ?>

<rows <?php echo $totalCountAttribute; ?> pos="<?php echo (int) $posStart; ?>">
    <?php if ($posStart === 0){ ?>
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#text_filter,#numeric_filter,#numeric_filter,#select_filter,#select_filter,#text_filter,#select_filter]]></param></call>
        </beforeInit>
        <column id="name" width="280" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
        <column id="id_product" width="60" type="ro" align="left" sort="int"><?php echo _l('id prod.'); ?></column>
        <column id="id_product_attribute" width="60" type="ro" align="left" sort="str"><?php echo _l('id prod. attr.'); ?></column>
        <column id="active" width="70" type="ro" align="left"><?php echo _l('Active'); ?></column>
        <column id="is_locked" type="coro" width="100" align="left"><?php echo _l('Shippingbo synchronization'); ?>
            <option value="<?php echo Sc\Service\Shippingbo\ShippingboService::SBO_PRODUCT_IS_UNLOCKED; ?>"><?php echo _l('Yes'); ?></option>
            <option value="<?php echo Sc\Service\Shippingbo\ShippingboService::SBO_PRODUCT_IS_LOCKED; ?>"><?php echo _l('No'); ?></option>
            </column>
        <column id="reference" width="200" type="ed" align="left" sort="str"><?php echo _l('Reference e-shop'); ?></column>
        <column id="statusLabel" width="200" type="ro" align="left" sort="str"><?php echo _l('Status'); ?></column>
        <column id="groupPosition" width="70" type="ro" hidden="true"></column>
    </head>
    <?php } ?>
    <?php echo implode("\r\n", $xml); ?>
</rows>
