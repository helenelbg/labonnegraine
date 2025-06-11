<?php

namespace Sc\Service\Shippingbo\Repository\Prestashop;

use DateTimeImmutable;
use DateTimeZone;
use DbQuery;
use Exception;
use PDOStatement;
use PrestaShopException;
use Product;
use Sc\Service\Shippingbo\Process\ShippingboImport;
use Sc\Service\Shippingbo\Repository\ShippingboRepository;
use Sc\Service\Shippingbo\ShippingboService;

class ProductRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @desc : truncate sbo product buffer table
     *
     * @return $this
     */
    public function clear()
    {
        $this->pdo->prepare('TRUNCATE `'._DB_PREFIX_.$this->sboProductsTable.'`')->execute();

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getLastSyncedDate()
    {
        $lastSynced = $this->pdo->query('SELECT MAX(updated_at) as last_sync FROM `'._DB_PREFIX_.$this->sboShopRelationTable.'`')->fetchColumn();
        if (!$lastSynced)
        {
            return new DateTimeImmutable('now', new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
        }

        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $lastSynced, new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
    }

    // INSERT/UPDATE

    /**
     * @desc :  insert or update sbo product data in buffer table
     *
     * @return PDOStatement
     */
    public function setBufferStatement()
    {
        $queryPrepare = 'INSERT INTO `'._DB_PREFIX_.$this->sboProductsTable.'` (`id`,`user_ref`,`is_pack`,`title`,`weight`,`height`, `length`,`width`,`updated_at`,`synced_at`) VALUES (:id,:user_ref,:is_pack,:title,:weight,:height,:length,:width,:updated_at,:synced_at)
        ON DUPLICATE KEY UPDATE
            `user_ref` = :user_ref,
            `weight` =  CASE WHEN `weight`="" or `weight` IS NULL THEN :weight END,
            `height` =  CASE WHEN `height`="" or `height` IS NULL THEN :height END,
            `length` =  CASE WHEN `length`="" or `length` IS NULL THEN :length END,
            `width` =  CASE WHEN `width`="" or `width` IS NULL THEN :width END,
            `title` =  CASE WHEN `title`="" or `title` IS NULL THEN :title END,
            `updated_at` = :updated_at,
            `synced_at` = :synced_at
        ';

        return $this->pdo->prepare($queryPrepare);
    }

    /**
     * @param $includeLocked
     *
     * @return string
     */
    public function getMissingSboQuery($full = false, $page = false)
    {
        $dbQuery = new DbQuery();

        if ($this->onlyCount)
        {
            $dbQuery->select("COUNT(DISTINCT(CONCAT(p.id_product,'-',COALESCE(pa.id_product_attribute,0)))) as count");
        }
        else
        {
            $dbQuery
                ->select("DISTINCT(CONCAT('P#',COALESCE(p.id_product,0),'-A#',COALESCE(pa.id_product_attribute,0),'-SBO#',COALESCE(ps_relation.id_sbo,0))) as rowId")
                ->select('p.id_product')
                ->select('p.width')
                ->select('p.height')
                ->select('p.weight')
                ->select('p.depth as length')
                ->select('p.active')
                ->select('COALESCE(pa.id_product_attribute, 0) as id_product_attribute')
                ->select('COALESCE(pa.reference, p.reference,0) as reference')
                ->select('IF(pa.id_product_attribute != 0,pa.ean13,p.ean13) as ean13')
                ->select('IF(ps_relation.is_locked IS NOT NULL,ps_relation.is_locked,true) as is_locked')
                ->select('ps_relation.type_sbo')
                ->select('ps_relation.id_sbo')
                ->groupBy('rowId')
            ;

            $this->setPage($dbQuery, $page);
        }
        $dbQuery
            ->from(self::PS_PRODUCT_TABLE_NAME, 'p')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->innerJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_product = p.id_product AND ps_relation.id_product_attribute = COALESCE(pa.id_product_attribute,0) AND ps_relation.id_sbo IS NULL')
            ->innerJoin('product_shop', 'ps', 'ps.id_product = p.id_product AND ps.id_shop IN('.pInSql($this->service->getConfigShopsForPdo()).')')
            ->where('ps_relation.id_sbo IS NULL')
            ->where('ps_relation.is_locked = :is_locked')
        ;

        if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
        {
            $dbQuery
                ->select('p.product_type')
                ->where('p.product_type IN("'.ShippingboService::PS_PRODUCT_TYPE_PRODUCT.'","'.ShippingboService::PS_PRODUCT_TYPE_COMBINATIONS.'")')
            ;
        }
        else
        {
            $dbQuery->where('p.cache_is_pack = 0');
        }

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
                ->select('p.id_product')
                ->select('pa.id_product_attribute')
                ->select('COALESCE(sbo_addrefs.order_item_value, sbo.user_ref) as user_ref')
                ->select('COALESCE(sbo_addref_source.title,sbo.title) as title')
                ->select('p.active')
                ->select('sbo.width')
                ->select('sbo.height')
                ->select('sbo.length')
                ->select('sbo.weight')
                ->select('ps_relation.type_sbo')
                ->select('ps_relation.id_sbo')
                ->select('IF(p.id_product IS NULL, false,true) as is_related');
            $this->setPage($dbQuery, $page);
        }
        $dbQuery->from($this->sboShopRelationTable, 'ps_relation')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo.id = ps_relation.id_sbo')
            ->leftJoin($this->sboAdditionalRefsTable, 'sbo_addrefs', 'sbo_addrefs.id = ps_relation.id_sbo')
            ->leftJoin($this->sboProductsTable, 'sbo_addref_source', 'sbo_addrefs.product_value = sbo_addref_source.id')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'ps_relation.id_product = p.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->where('ps_relation.type_sbo IN("'.ShippingboService::SBO_PRODUCT_TYPE_PRODUCT.'","'.ShippingboService::SBO_PRODUCT_TYPE_ADDREF.'")')
            ->where('p.id_product IS NULL');

        return $dbQuery;
    }

    /**
     * @return string
     */
    public function getUpdatedQuery($offset = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery
            ->select('p.id_product')
            ->select('pa.id_product_attribute')
            ->select('COALESCE(sbo_addrefs.order_item_value, sbo.user_ref) as user_ref')
            ->select('COALESCE(sbo_addref_source.title,sbo.title) as title')
            ->select('sbo.width')
            ->select('sbo.height')
            ->select('sbo.length')
            ->select('sbo.weight')
            ->select('ps_relation.type_sbo')
            ->select('ps_relation.id_sbo')
            ->select('IF(p.id_product IS NULL, false,true) as is_related')
            ->from($this->sboShopRelationTable, 'ps_relation')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo.id = ps_relation.id_sbo')
            ->leftJoin($this->sboAdditionalRefsTable, 'sbo_addrefs', 'sbo_addrefs.id = ps_relation.id_sbo')
            ->leftJoin($this->sboProductsTable, 'sbo_addref_source', 'sbo_addrefs.product_value = sbo_addref_source.id')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'ps_relation.id_product = p.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->where('ps_relation.type_sbo IN("'.ShippingboService::SBO_PRODUCT_TYPE_PRODUCT.'","'.ShippingboService::SBO_PRODUCT_TYPE_ADDREF.'")')
            ->where('CONVERT(sbo.updated_at,datetime) >= :updated_at')
        ;
        if ($offset)
        {
            $dbQuery->limit(ShippingboImport::NB_BATCH_IMPORT, $offset * ShippingboImport::NB_BATCH_IMPORT);
        }

        return $dbQuery->__toString();
    }

    /**
     * @return string[]
     */
    public function getExportColumns($filtered = false)
    {
        $columnsSettingsExport = (array) json_decode($this->service->getConfigValue('defaultDataExport'));

        return array_keys(array_filter($columnsSettingsExport));
    }

    /**
     * @throws PrestaShopException
     */
    public function saveProduct($productInfos, $productExists, $productName, $ref, $is_pack, $id_lang)
    {
        $product = new Product($productExists ? $productInfos['id_product'] : null, null, $id_lang);

        $shopsUnitConversion = json_decode($this->service->getConfigValue('unitConversion'), true);
        // interception du product->save
        try
        {
            if (!$productExists)
            {
                $product->name[$id_lang] = $productName;
                $product->link_rewrite[$id_lang] = link_rewrite($productName);
                $product->active = false;
            }

            $product->reference = $productInfos[$ref];
            $product->width = $this->service->getImportProcess()->fieldsToImport['width'] && isset($productInfos['width']) ? $productInfos['width'] / $shopsUnitConversion['dimension'] : null;
            $product->height = $this->service->getImportProcess()->fieldsToImport['height'] && isset($productInfos['height']) ? $productInfos['height'] / $shopsUnitConversion['dimension'] : null;
            $product->depth = $this->service->getImportProcess()->fieldsToImport['length'] && isset($productInfos['length']) ? $productInfos['length'] / $shopsUnitConversion['dimension'] : null;
            $product->weight = $this->service->getImportProcess()->fieldsToImport['weight'] && isset($productInfos['weight']) ? $productInfos['weight'] / $shopsUnitConversion['weight'] : null;
            $product->cache_is_pack = $is_pack;
            $product->id_shop_list = explode(',', $this->service->getConfigValue('importToShop'));
            if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
            {
                $product->product_type = $is_pack ? 'pack' : 'standard';
            }
            $product->save();
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }
        finally
        {
            return $product;
        }
    }

    protected function skuTooLongSubQuery()
    {
        return 'IF(LENGTH(COALESCE(sbo_addrefs.order_item_value,sbo.user_ref)) > :sku_max_length, true, false)';
    }

    protected function missingRefSubQuery()
    {
        return 'IF(COALESCE(pa.reference, p.reference,"") = "" , true,false)';
    }
}
