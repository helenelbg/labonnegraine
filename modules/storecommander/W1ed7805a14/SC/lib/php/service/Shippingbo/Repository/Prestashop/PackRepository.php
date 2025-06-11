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

class PackRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @desc : truncate sbo product buffer table
     *
     * @return $this
     */
    public function clear()
    {
        $this->pdo->query('TRUNCATE `'._DB_PREFIX_.$this->sboPackComponentTable.'`');

        return $this;
    }

    /**
     * @desc : insert or update sbo pack component data in buffer table
     * No endpoint for this on shippingbo api, the linked product will be triggered as updated,
     * so other entries on this table can be updated even if not modified in shippingbo interface
     *
     * @return false|PDOStatement
     */
    public function setBufferStatement()
    {
        $packQueryPrepare = 'INSERT INTO `'._DB_PREFIX_.$this->sboPackComponentTable.'` (`id`,`quantity`, `pack_product_id`,`component_product_id`,`created_at`,`updated_at`, `synced_at`) VALUES (:id,:quantity,:pack_product_id,:component_product_id,:created_at,:updated_at, :synced_at)
         ON DUPLICATE KEY UPDATE
            `quantity` = :quantity,
            `pack_product_id` = :pack_product_id,
            `component_product_id` = :component_product_id,
            `updated_at` = :updated_at,
            `synced_at` = :synced_at
        ';

        return $this->pdo->prepare($packQueryPrepare);
    }

    /**
     * @throws Exception
     */
    public function getLastSyncedDate()
    {
        $lastSynced = $this->pdo->query('SELECT MAX(updated_at) as last_sync FROM `'._DB_PREFIX_.$this->sboPackComponentTable.'`')->fetchColumn();
        if (!$lastSynced)
        {
            return new DateTimeImmutable('now', new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
        }

        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $lastSynced, new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
    }

    /**
     * @desc : insert/update PS native pack information statement
     *
     * @return false|PDOStatement
     */
    public function setPsStatement()
    {
        $packQueryPrepare = 'INSERT INTO `'._DB_PREFIX_.self::PS_PACK_TABLE_NAME.'` (`id_product_pack`,`id_product_item`,`id_product_attribute_item`,`quantity`) VALUES (:id_product_pack,:id_product_item,:id_product_attribute_item,:quantity)
         ON DUPLICATE KEY UPDATE
            `quantity` = :quantity
        ';

        return $this->pdo->prepare($packQueryPrepare);
    }

    /**
     * @return string
     */
    public function getMissingPsQuery($page = false)
    {
        $dbQuery = new DbQuery();

        $nbComponentsSubQuery = new DbQuery();

        if ($this->onlyCount)
        {
            $dbQuery->select('COUNT(DISTINCT(ps_relation.'.$this->sboShopRelationTablePrimary.'))');
        }
        else
        {
            $nbComponentsSubQuery
                ->select('COUNT(DISTINCT(sbo_pak.component_product_id)) as nb_components')
                ->from($this->sboPackComponentTable, 'sbo_pak')
                ->where('sbo_pak.pack_product_id = ps_relation.id_sbo')
            ;
            $dbQuery
                ->select('ps_relation.*')
                ->select('('.$nbComponentsSubQuery.') as nb_components')
                ->select('sbo.user_ref')
                ->select('sbo.title')
            ;
            $this->setPage($dbQuery, $page);
        }
        $dbQuery->from($this->sboShopRelationTable, 'ps_relation')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo.id = ps_relation.id_sbo')
            ->leftJoin(self::PS_PACK_TABLE_NAME, 'pak', 'ps_relation.id_product =  pak.id_product_pack')
            ->where('ps_relation.type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_PACK.'"')
            ->where('(ps_relation.id_product IS NULL)')
        ;

        return $dbQuery;
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
                ->select("DISTINCT(CONCAT('P#',COALESCE(p.id_product,0),'-A#',COALESCE(pa.id_product_attribute,0),'-SBO#',COALESCE(ps_relation.id_sbo,0))) as rowId")
                ->select('p.id_product')
                ->select('COALESCE(pa.id_product_attribute, 0) as id_product_attribute')
                ->select('p.ean13')
                ->select('p.active')
                ->select('COALESCE(pa.reference, p.reference, 0) as reference')
                ->select('ps_relation.type_sbo')
                ->select('ps_relation.id_sbo')
                ->select('ps_relation.is_locked')
            ;
            $this->setPage($dbQuery, $page);
        }
        $dbQuery
            ->from(self::PS_PRODUCT_TABLE_NAME, 'p')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_product = p.id_product')
            ->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN('.pInSql($this->service->getConfigShopsForPdo()).')')
            ->where('type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_PACK.'"')
            ->where('id_sbo IS NULL')
            ->where('ps_relation.is_locked = :is_locked');

        if ($full)
        {
            $dbQuery = $this->addPsLangImageParts($dbQuery);
        }

        return $dbQuery;
    }

    /**
     * @desc : get product components information
     *
     * @return DbQuery
     */
    public function getComponentsQuery()
    {
        $dbQuery = new DbQuery();
        $dbQuery
            ->select('p_linked.id_product')
            ->select('COALESCE(pa_linked.id_product_attribute,0) as id_product_attribute')
            ->select('sbo_pak.quantity')
            ->from($this->sboPackComponentTable, 'sbo_pak')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo_pak.pack_product_id = sbo.id')
            ->leftJoin($this->sboProductsTable, 'sbo_linked', 'sbo_pak.component_product_id = sbo_linked.id')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'sbo.user_ref = p.reference')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_linked', 'sbo_linked.user_ref = p_linked.reference')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_linked', 'sbo_linked.user_ref = pa_linked.reference')
            ->where('sbo.is_pack = 1')
            ->where('p.id_product = :id_product');

        return $dbQuery;
    }

    /**
     * @desc get all sbo products from buffer table and check if product is linked or not in PS
     * depending on sbo product user_ref or sbo additional ref order_item_value
     *
     * @param bool|DateTimeImmutable $lastCollect
     *
     * @return DbQuery
     */
    public function getSboComponentsQuery($lastCollect = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('sbo.*')
            ->select('IF(p.id_product IS NOT NULL OR pa.id_product_attribute IS NOT NULL, true, false) as exists_in_ps')
            ->from($this->sboProductsTable, 'sbo')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'sbo.user_ref = p.reference')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'sbo.user_ref = pa.reference')
            ->groupBy('sbo.id')
            ->where('sbo.is_pack = 1');
        if ($lastCollect)
        {
            $dbQuery->where('sbo.updated_at > "'.$lastCollect->format('Y-m-d H:i:s').'"');
        }

        return $dbQuery;
    }

    /**
     * @return string
     */
    public function getMissingComponentsSboQuery($full = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('COALESCE(pa.reference,p.reference,0) as pack_product_ref')
            ->select('COALESCE(pa_comp.reference,p_comp.reference,0) as component_product_ref')
            ->select('pak.quantity')
            ->select('sbo.id')
            ->from(self::PS_PACK_TABLE_NAME, 'pak')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'pak.id_product_pack = p.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'p.id_product = pa.id_product')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_product = p.id_product')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_comp', 'pak.id_product_item = p_comp.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_comp', 'pak.id_product_attribute_item = pa_comp.id_product_attribute')
            ->leftJoin($this->sboProductsTable, 'sbo', 'p.reference = sbo.user_ref OR pa.reference = sbo.user_ref')
            ->where('ps_relation.type_sbo = "'.ShippingboService::SBO_PRODUCT_TYPE_PACK.'"')
            ->where('ps_relation.is_locked != 1')
            ->having('sbo.id IS NULL');
        if ($full)
        {
            $dbQuery = $this->addPsLangImageParts($dbQuery);
        }

        return $dbQuery->__toString();
    }

    /**
     * @return string
     */
    public function getUpdatedQuery($offset = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery
            ->select('pak.id_product_pack')
            ->select('pak.id_product_item')
            ->select('pak.id_product_attribute_item')
            ->select('sbo_pack_components.quantity as quantity')
            ->from($this->sboPackComponentTable, 'sbo_pack_components')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'sbo_pack_components.component_product_id =  ps_relation.id_sbo')
            ->leftJoin(self::PS_PACK_TABLE_NAME, 'pak', 'ps_relation.id_product = pak.id_product_item')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', ' pak.id_product_item = p.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', ' pa.id_product_attribute = pak.id_product_attribute_item')
            ->where('(CONVERT(sbo_pack_components.updated_at, datetime) >= :updated_at)')
            ->where('pid_product IS NOT NULL');

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
            'userRef',
            'ean13',
            'title',
            'pictureUrl',
        ];
    }

    /**
     * @return string[]
     */
    public function getExportComponentsColumns()
    {
        return [
            'pack_product_ref',
            'component_product_ref',
            'quantity',
        ];
    }

    protected function skuTooLongSubQuery()
    {
        return 'IF(LENGTH(sbo.user_ref) > :sku_max_length, true, false)';
    }

    protected function missingRefSubQuery()
    {
        return 'IF(COALESCE(pa.reference, p.reference,"") = "" , true,false)';
    }
}
