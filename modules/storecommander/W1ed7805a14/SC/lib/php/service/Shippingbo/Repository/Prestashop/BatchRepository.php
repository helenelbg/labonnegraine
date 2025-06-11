<?php

namespace Sc\Service\Shippingbo\Repository\Prestashop;

use DateTimeImmutable;
use DateTimeZone;
use DbQuery;
use Exception;
use PDOStatement;
use Sc\Service\Shippingbo\Process\ShippingboImport;
use Sc\Service\Shippingbo\Repository\ShippingboRepository;
use Sc\Service\Shippingbo\ShippingboService;

class BatchRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @desc : truncate sbo product buffer table
     *
     * @return $this
     */
    public function clear()
    {
        $this->pdo->query('TRUNCATE `'._DB_PREFIX_.$this->sboAdditionalRefsTable.'`');

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getLastSyncedDate()
    {
        $query = 'SELECT MAX(updated_at) as last_sync FROM `'._DB_PREFIX_.$this->sboAdditionalRefsTable.'`';
        $lastSynced = $this->pdo->query($query)->fetchColumn();
        if (!$lastSynced)
        {
            return new DateTimeImmutable('now', new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
        }

        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $lastSynced, new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
    }

    /**
     * @desc : insert or update sbo additional references data in buffer table
     *
     * @return false|PDOStatement
     */
    public function setBufferStatement()
    {
        $queryPrepare = 'INSERT INTO `'._DB_PREFIX_.$this->sboAdditionalRefsTable.'` (`id`,`order_item_field`, `product_field`,`order_item_value`,`product_value`,`matched_quantity`,`created_at`,`updated_at`,`synced_at`) VALUES (:id,:order_item_field, :product_field,:order_item_value,:product_value,:matched_quantity,:created_at,:updated_at,:synced_at)
         ON DUPLICATE KEY UPDATE
            `order_item_field` = :order_item_field,
            `product_field` = :product_field,
            `order_item_value` = :order_item_value,
            `product_value` = :product_value,
            `matched_quantity` = :matched_quantity,
            `updated_at` = :updated_at,
            `synced_at` = :synced_at
        ';

        return $this->pdo->prepare($queryPrepare);
    }

    /**
     * @return string
     */
    public function getMissingSboQuery($full = false, $page = false)
    {
        $dbQuery = new DbQuery();

        if ($this->onlyCount)
        {
            $dbQuery->select("COUNT(CONCAT(p.id_product,'-',COALESCE(pa.id_product_attribute,0))) as count");
        }
        else
        {
            $dbQuery
                ->select("CONCAT('P#',COALESCE(p.id_product,0),'-A#',COALESCE(pa.id_product_attribute,0),'-SBO#',COALESCE(ps_relation.id_sbo,0)) as rowId")
                ->select('p.id_product')
                ->select('COALESCE(pa.id_product_attribute, 0) as id_product_attribute')
                ->select('p.active')
                ->select('COALESCE(pa.reference, p.reference,0) as reference')
                ->select('ps_relation.type_sbo')
                ->select('ps_relation.id_sbo')
                ->select('ps_relation.is_locked')
                ->select('pak.quantity')
                ->select('pak.id_product_pack')
                ->select('pak.id_product_item')
                ->select('ps_relation_component.id_sbo as id_component_sbo')
            ;
            $this->setPage($dbQuery, $page);
        }

        $dbQuery
            ->from(self::PS_PRODUCT_TABLE_NAME, 'p')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->leftJoin(self::PS_PACK_TABLE_NAME, 'pak', 'pak.id_product_pack = p.id_product')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_product = p.id_product')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation_component', 'ps_relation_component.id_product = pak.id_product_item')
            ->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN('.pInSql($this->service->getConfigShopsForPdo()).')')
            ->leftJoin($this->sboAdditionalRefsTable, 'sbo_addrefs', 'sbo_addrefs.order_item_value = ps_relation.id_sbo')
            ->where('ps_relation.type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_BATCH.'"')
            ->where('ps_relation.id_sbo IS NULL')
            ->where('ps_relation.is_locked = :is_locked')
        ;

        if ($full)
        {
            $dbQuery = $this->addPsLangImageParts($dbQuery);
        }

        return $dbQuery;
    }

    /**
     * @return string
     */
    public function getMissingPsQuery($page = false)
    {
        $dbQuery = new DbQuery();
        if ($this->onlyCount)
        {
            $dbQuery->select('COUNT(*)');
        }
        else
        {
            $dbQuery
                ->select('ps_relation.*')
                ->select('sbo_source.id as id_source_sbo')
                ->select('sbo_source.user_ref as source_ref')
                ->select('sbo_addrefs.order_item_value as user_ref')
                ->select('p_source.id_product as id_product_item')
                ->select('COALESCE(pa_source.id_product_attribute,0) as id_product_attribute_item')
                ->select('sbo_addrefs.product_value')
                ->select('sbo_addrefs.matched_quantity as matched_quantity');
            $this->setPage($dbQuery, $page);
        }

        $dbQuery->from($this->sboShopRelationTable, 'ps_relation')
            ->leftJoin($this->sboAdditionalRefsTable, 'sbo_addrefs', 'sbo_addrefs.id = ps_relation.id_sbo')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo.id = ps_relation.id_sbo')
            ->leftJoin($this->sboProductsTable, 'sbo_source', 'sbo_addrefs.product_value = sbo_source.id')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_source', 'sbo_source.user_ref = p_source.reference')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_source', 'sbo_source.user_ref = pa_source.reference')
            ->leftJoin(self::PS_PACK_TABLE_NAME, 'pak', 'ps_relation.id_product =  pak.id_product_pack')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_exists', 'ps_relation.id_product =  p_exists.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_exists', 'ps_relation.id_product_attribute = pa_exists.id_product_attribute')
            ->where('ps_relation.type_sbo = \''.ShippingboService::SBO_PRODUCT_TYPE_BATCH.'\'')
            ->where('(ps_relation.id_product IS NULL OR pak.id_product_pack IS NULL)')
            ->where('p_exists.id_product IS NULl')
        ;

        return $dbQuery;
    }

    /**
     * @return string
     */
    public function getUpdatedQuery($offset = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery
            ->select('ps_relation.*')
            ->select('sbo_source.id_sbo as id_source_sbo')
            ->select('sbo_addrefs.order_item_value as user_ref')
            ->select('p_source.id_product as id_product_item')
            ->select('COALESCE(pa_source.id_product_attribute,0) as id_product_attribute_item')
            ->select('sbo_addrefs.matched_quantity as matched_quantity')
            ->from($this->sboShopRelationTable, 'ps_relation')
            ->leftJoin($this->sboAdditionalRefsTable, 'sbo_addrefs', 'sbo_addrefs.id = ps_relation.id_sbo')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo.user_ref = sbo_addrefs.order_item_value')
            ->leftJoin($this->sboShopRelationTable, 'sbo_source', 'sbo_addrefs.product_value = sbo_source.id_sbo')
            ->leftJoin(self::PS_PACK_TABLE_NAME, 'pak', 'ps_relation.id_product =  pak.id_product_pack')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_exists', 'ps_relation.id_product =  p_exists.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_exists', 'ps_relation.id_product_attribute = pa_exists.id_product_attribute')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_source', 'sbo_source.id_product = p_source.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_source', 'sbo_source.id_product_attribute = pa_source.id_product_attribute')
            ->where('ps_relation.type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_BATCH.'"')
            ->where('(CONVERT(sbo_addrefs.updated_at) >= :updated_at  OR pak.id_product_pack IS NULL)');
        if ($offset)
        {
            $dbQuery->limit(ShippingboImport::NB_BATCH_IMPORT, $offset * ShippingboImport::NB_BATCH_IMPORT);
        }

        return $dbQuery->__toString();
    }

    /**
     * @return string[]
     */
    public function getExportColumns()
    {
        return [
            'product_id',
            'matched_quantity',
            'order_item_value',
        ];
    }

    protected function skuTooLongSubQuery()
    {
        return 'IF(LENGTH(sbo_addrefs.order_item_value) > :sku_max_length, true, false)';
    }

    protected function missingRefSubQuery()
    {
        return 'IF(COALESCE(pa.reference, p.reference,"") = "" , true,false)';
    }
}
