<?php

namespace Sc\service\Shippingbo\Repository;

use Sc\Service\Shippingbo\Repository\Prestashop\BaseRepository;
use Sc\Service\Shippingbo\ShippingboService;

class StatsRepository
{
    private $shippingboService;

    public function __construct(ShippingboService $shippingboService)
    {
        $this->shippingboService = $shippingboService;
    }

    private function getScoreStatus(array $results, $sboType)
    {
        if ($results['ps'][$sboType]['error'] || $results['sbo'][$sboType]['error'])
        {
            return 'error';
        }
        if ($results['ps'][$sboType]['awaiting'] || $results['sbo'][$sboType]['awaiting'])
        {
            return 'awaiting';
        }

        return 'success';
    }

    /**
     * @param array|false $results
     *
     * @return string
     */
    private function getPercentage($results, $type)
    {
        $percentage = 1;
        if ($results && $results['ps'][$type]['all'] && $results['ps'][$type]['all'] != 0)
        {
            $percentage = abs(1 - ($results['ps'][$type]['missing'] + $results['sbo'][$type]['missing']) / ($results['ps'][$type]['all'] + $results['sbo'][$type]['all']));
        }

        return (int) ($percentage * 100).'%';
    }

    public function getOverview($results)
    {
        $results += [
            'sbo' => [
                'products' => [
                    'all' => null,
                ],
                'batches' => [
                    'all' => null,
                ],
                'packs' => [
                    'all' => null,
                ],
            ],
            'ps' => [
                'products' => [
                    'all' => null,
                ],
                'batches' => [
                    'all' => null,
                ],
                'packs' => [
                    'all' => null,
                ],
            ],
            'score' => [
                'products' => null,
                'batches' => null,
                'packs' => null,
            ],
        ];

        // -----------------------------
        // SBO
        // -----------------------------
        // PRODUCTS
        // SBO all products count
        $sboNbProductsQuery = new \DbQuery();
        $sboNbProductsQuery->select('COUNT('.$this->shippingboService->getShopRelationRepository()->sboShopRelationTablePrimary.')')
            ->from($this->shippingboService->getShopRelationRepository()->sboShopRelationTable)
            ->where('type_sbo IN("'.ShippingboService::SBO_PRODUCT_TYPE_PRODUCT.'","'.ShippingboService::SBO_PRODUCT_TYPE_ADDREF.'")')
            ->where('id_sbo IS NOT NULL');
        $stmt = $this->shippingboService->getPdo()->query($sboNbProductsQuery);
        $stmt->execute();
        $results['sbo']['products']['all'] = (int) $stmt->fetchColumn();

        // BATCHES
        // SBO all batches
        $sboNbBatchesQuery = new \DbQuery();
        $sboNbBatchesQuery->select('COUNT('.$this->shippingboService->getShopRelationRepository(true)->sboShopRelationTablePrimary.')')
            ->from($this->shippingboService->getShopRelationRepository(true)->sboShopRelationTable)
            ->where('type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_BATCH.'"')
            ->where('id_sbo IS NOT NULL');
        $stmt = $this->shippingboService->getPdo()->query($sboNbBatchesQuery);
        $results['sbo']['batches']['all'] = (int) $stmt->fetchColumn();

        // PACKS
        // SBO all packs count
        $sboNbPacksQuery = new \DbQuery();
        $sboNbPacksQuery->select('COUNT('.$this->shippingboService->getShopRelationRepository()->sboShopRelationTablePrimary.')')
            ->from($this->shippingboService->getShopRelationRepository()->sboShopRelationTable)
            ->where('type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_PACK.'"')
            ->where('id_sbo IS NOT NULL');

        $stmt = $this->shippingboService->getPdo()->query($sboNbPacksQuery);
        $results['sbo']['packs']['all'] = (int) $stmt->fetchColumn();

        // -----------------------------
        // PRESTASHOP
        // -----------------------------

        //PRODUCTS
        // PS all products count
        $psNbProductsQuery = new \DbQuery();
        $psNbProductsQuery->select('COUNT(DISTINCT(CONCAT(p.id_product,"-",COALESCE(pa.id_product_attribute,0))))')
            ->from(BaseRepository::PS_PRODUCT_TABLE_NAME, 'p')
            ->leftJoin(BaseRepository::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN('.pInSql($this->shippingboService->getConfigShopsForPdo()).')')
        ;
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
        {
            $psNbProductsQuery->where('p.product_type IN("'.ShippingboService::PS_PRODUCT_TYPE_PRODUCT.'","'.ShippingboService::PS_PRODUCT_TYPE_COMBINATIONS.'")');
        }
        else
        {
            $psNbProductsQuery->where('p.cache_is_pack = 0');
        }

        $stmt = $this->shippingboService->getPdo()->query($psNbProductsQuery);
        $results['ps']['products']['all'] = (int) $stmt->fetchColumn();

        // BATCHES
        // PS all batches count
        $psNbBatchesQuery = new \DbQuery();
        $psNbBatchesQuery->select('COUNT('.$this->shippingboService->getShopRelationRepository()->sboShopRelationTablePrimary.')')
            ->from($this->shippingboService->getShopRelationRepository()->sboShopRelationTable, 'shop_relation')
            ->innerJoin('product_shop', 'ps', 'ps.id_product = shop_relation.id_product AND ps.id_shop IN('.pInSql($this->shippingboService->getConfigShopsForPdo()).')')
            ->where('shop_relation.type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_BATCH.'"')
            ->where('shop_relation.id_product IS NOT NULL')
        ;
        $stmt = $this->shippingboService->getPdo()->query($psNbBatchesQuery);
        $results['ps']['batches']['all'] = (int) $stmt->fetchColumn();

        // PACKS
        // PS overall packs count
        $psNbPacksQuery = new \DbQuery();
        $psNbPacksQuery->select('COUNT('.$this->shippingboService->getShopRelationRepository()->sboShopRelationTablePrimary.')')
            ->from($this->shippingboService->getShopRelationRepository()->sboShopRelationTable, 'shop_relation')
            ->innerJoin('product_shop', 'ps', 'ps.id_product = shop_relation.id_product AND ps.id_shop IN('.pInSql($this->shippingboService->getConfigShopsForPdo()).')')
            ->where('shop_relation.type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_PACK.'"')
            ->where('shop_relation.id_product IS NOT NULL')
        ;
        $stmt = $this->shippingboService->getPdo()->query($psNbPacksQuery);
        $results['ps']['packs']['all'] = (int) $stmt->fetchColumn();

        // SCORE
        $results['score']['products'] = $this->getPercentage($results, 'products');
        $results['score']['products_status'] = $this->getScoreStatus($results, 'products');
        $results['score']['batches'] = $this->getPercentage($results, 'batches');
        $results['score']['batches_status'] = $this->getScoreStatus($results, 'batches');
        $results['score']['packs'] = $this->getPercentage($results, 'packs');
        $results['score']['packs_status'] = $this->getScoreStatus($results, 'packs');

        return $results;
    }

    public function getAll()
    {
        $results = [
            'sbo' => [
                'products' => [
                    'missing' => null,
                    'awaiting' => null,
                    'error' => null,
                    'locked' => null,
                ],
                'batches' => [
                    'missing' => null,
                    'awaiting' => null,
                    'error' => null,
                    'locked' => null,
                ],
                'packs' => [
                    'missing' => null,
                    'awaiting' => null,
                    'error' => null,
                    'locked' => null,
                ],
            ],
            'ps' => [
                'products' => [
                    'missing' => null,
                    'awaiting' => null,
                    'error' => null,
                ],
                'batches' => [
                    'missing' => null,
                    'awaiting' => null,
                    'error' => null,
                ],
                'packs' => [
                    'missing' => null,
                    'awaiting' => null,
                    'error' => null,
                ],
            ],
        ];

        // -----------------------------
        // SBO
        // -----------------------------
        // PRODUCTS
        // prepare base statement
        $sboStatsProducts = $this->shippingboService->getProductRepository(true)->getMissingSboQuery();
        $stmtSboBase = $this->shippingboService->getPdo()->prepare($sboStatsProducts);
        // prepare error statement
        $sboProductsWithErrorsQuery = $this->shippingboService->getProductRepository(true)->addSboErrorParts($sboStatsProducts);
        $stmtWithError = $this->shippingboService->getPdo()->prepare($sboProductsWithErrorsQuery);

        // SBO missing products
        $stmtSboBase->execute([
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['products']['missing'] = $stmtSboBase->fetchColumn();
        // SBO locked products
        $stmtSboBase->execute([
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_LOCKED,
        ]);
        $results['sbo']['products']['locked'] = $stmtSboBase->fetchColumn();
        // SBO awaiting products
        $stmtWithError->execute([
            ':has_error' => false,
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['products']['awaiting'] = $stmtWithError->fetchColumn();

        // SBO error products
        $stmtWithError->execute([
            ':has_error' => true,
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);

        $results['sbo']['products']['error'] = $stmtWithError->fetchColumn();

        // BATCHES
        // prepare base statement
        $sboStatsBatches = $this->shippingboService->getBatchRepository(true)->getMissingSboQuery();
        $stmtSboBase = $this->shippingboService->getPdo()->prepare($sboStatsBatches);
        // prepare error statement
        $sboBatchesWithErrorsQuery = $this->shippingboService->getBatchRepository(true)->addSboErrorParts($sboStatsBatches);
        $stmtWithError = $this->shippingboService->getPdo()->prepare($sboBatchesWithErrorsQuery);

        // SBO missing batches
        $stmtSboBase->execute([
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['batches']['missing'] = $stmtSboBase->fetchColumn();
        // SBO locked batches
        $stmtSboBase->execute([
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_LOCKED,
        ]);
        $results['sbo']['batches']['locked'] = $stmtSboBase->fetchColumn();
        // SBO awaiting batches
        $stmtWithError->execute([
            ':has_error' => false,
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['batches']['awaiting'] = $stmtWithError->fetchColumn();
        // SBO error batches
        $stmtWithError->execute([
            ':has_error' => true,
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['batches']['error'] = $stmtWithError->fetchColumn();
        // PACKS
        // prepare base statement
        $sboStatsPacks = $this->shippingboService->getPackRepository(true)->getMissingSboQuery();
        $stmtSboBase = $this->shippingboService->getPdo()->prepare($sboStatsPacks);
        // prepare error statement
        $sboPacksWithErrorsQuery = $this->shippingboService->getPackRepository(true)->addSboErrorParts($sboStatsPacks);
        $stmtWithError = $this->shippingboService->getPdo()->prepare($sboPacksWithErrorsQuery);

        // SBO missing packs
        $stmtSboBase->execute([
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['packs']['missing'] = $stmtSboBase->fetchColumn();
        // SBO locked packs
        $stmtSboBase->execute([
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_LOCKED,
        ]);
        $results['sbo']['packs']['locked'] = $stmtSboBase->fetchColumn();
        // SBO awaiting packs
        $stmtWithError->execute([
            ':has_error' => false,
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['packs']['awaiting'] = $stmtWithError->fetchColumn();
        // SBO error packs
        $stmtWithError->execute([
            ':has_error' => true,
            ':is_locked' => ShippingboService::SBO_PRODUCT_IS_UNLOCKED,
        ]);
        $results['sbo']['packs']['error'] = $stmtWithError->fetchColumn();

        // -----------------------------
        // PRESTASHOP
        // -----------------------------

        //PRODUCTS
        // prepare base statement
        $psStatsProducts = $this->shippingboService->getProductRepository(true)->getMissingPsQuery();
        $stmtPsBase = $this->shippingboService->getPdo()->prepare($psStatsProducts);
        // prepare error statement
        $sboProductsWithErrorsQuery = $this->shippingboService->getProductRepository(true)->addPsErrorParts($psStatsProducts);
        $stmtWithError = $this->shippingboService->getPdo()->prepare($sboProductsWithErrorsQuery);

        // PS missing products
        $stmtPsBase->execute();
        $results['ps']['products']['missing'] = $stmtPsBase->fetchColumn();
        // PS awaiting products
        $stmtWithError->execute([
            ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
            ':has_error' => false,
        ]);
        $results['ps']['products']['awaiting'] = $stmtWithError->fetchColumn();
        // PS error products
        $stmtWithError->execute([
            ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
            ':has_error' => true,
        ]);
        $results['ps']['products']['error'] = $stmtWithError->fetchColumn();

        // BATCHES
        // prepare base statement
        $psStatsBatches = $this->shippingboService->getBatchRepository(true)->getMissingPsQuery();
        $stmtPsBase = $this->shippingboService->getPdo()->prepare($psStatsBatches);
        // prepare error statement
        $sboBatchesWithErrorsQuery = $this->shippingboService->getBatchRepository(true)->addPsErrorParts($psStatsBatches);
        $stmtWithError = $this->shippingboService->getPdo()->prepare($sboBatchesWithErrorsQuery);

        // PS missing batches
        $stmtPsBase->execute();
        $results['ps']['batches']['missing'] = $stmtPsBase->fetchColumn();
        // PS awaiting batches
        $stmtWithError->execute([
            ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
            ':has_error' => false,
        ]);
        $results['ps']['batches']['awaiting'] = $stmtWithError->fetchColumn();
        // PS error batches
        $stmtWithError->execute([
            ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
            ':has_error' => true,
        ]);
        $results['ps']['batches']['error'] = $stmtWithError->fetchColumn();

        // PACKS
        // prepare base statement
        $psStatsPacks = $this->shippingboService->getPackRepository(true)->getMissingPsQuery();
        $stmtPsBase = $this->shippingboService->getPdo()->prepare($psStatsPacks);
        // prepare error statement
        $sboPacksWithErrorsQuery = $this->shippingboService->getPackRepository(true)->addPsErrorParts($psStatsPacks);
        $stmtWithError = $this->shippingboService->getPdo()->prepare($sboPacksWithErrorsQuery);
        // PS missing packs
        $stmtPsBase->execute();
        $results['ps']['packs']['missing'] = $stmtPsBase->fetchColumn();

        // PS awaiting packs
        $stmtWithError->execute([
            ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
            ':has_error' => false,
        ]);
        $results['ps']['packs']['awaiting'] = $stmtWithError->fetchColumn();
        // PS error packs
        $stmtWithError->execute([
            ':sku_max_length' => \Product::$definition['fields']['reference']['size'],
            ':has_error' => true,
        ]);
        $results['ps']['packs']['error'] = $stmtWithError->fetchColumn();

        return $results;
    }
}
