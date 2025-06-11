<?php

namespace Sc\Service\Shippingbo\Repository\Prestashop;

use Pack;
use Product;
use Sc\Service\Shippingbo\Model\ShopRelation as SboShopRelationModel;
use Sc\Service\Shippingbo\Repository\ShippingboRepository;
use Sc\Service\Shippingbo\ShippingboService;

class ShopRelationRepository extends BaseRepository
{
    /**
     * @var ProductRepository
     */
    public $product;
    /**
     * @var BatchRepository
     */
    public $batch;
    /**
     * @var PackRepository
     */
    public $pack;
    /**
     * @var ShippingboRepository
     */
    public $shippingbo;

    /**
     * @return $this
     */
    public function clear()
    {
        $this->pdo->query('TRUNCATE `'._DB_PREFIX_.$this->sboShopRelationTable.'`');

        return $this;
    }

    /**
     * @return $this
     */

    /**
     * @desc :  insert or update sbo product data in buffer table
     *
     * @return \PDOStatement
     */
    public function setStatement()
    {
        $queryPrepare = 'INSERT INTO `'._DB_PREFIX_.$this->sboShopRelationTable.'` (`id_sbo`,`id_product`,`id_product_attribute`,`id_sbo_source`,`is_locked`,`type_sbo`,`created_at`,`updated_at`) VALUES (:id_sbo,:id_product,:id_product_attribute,:id_sbo_source,:is_locked,:type_sbo,:created_at,:updated_at)
        ON DUPLICATE KEY UPDATE
            `id_sbo` =  CASE WHEN `id_sbo` IS NULL THEN :id_sbo ELSE `id_sbo` END,
            `id_product` =  CASE WHEN `id_product` IS NULL THEN :id_product ELSE `id_product` END,
            `id_product_attribute` =  CASE WHEN :id_product_attribute IS NULL THEN `id_product_attribute` ELSE :id_product_attribute END,
            `id_sbo_source` =  CASE WHEN `id_sbo_source` IS NULL THEN :id_sbo ELSE `id_sbo_source` END,
            `is_locked` = CASE WHEN :is_locked IS NULL THEN `is_locked` ELSE :is_locked END,
            `type_sbo` = CASE WHEN :type_sbo IS NULL THEN `type_sbo` ELSE :type_sbo END,
            `updated_at` = :updated_at
';

        return $this->pdo->prepare($queryPrepare);
    }

    /**
     * @return false|\mysqli_stmt|\PDOStatement
     */
    public function removeShopRelationStatement()
    {
        $queryPrepare = 'DELETE FROM `'._DB_PREFIX_.$this->sboShopRelationTable.'`
            WHERE '.$this->sboShopRelationTablePrimary.'=:id
';

        return $this->pdo->prepare($queryPrepare);
    }

    /**
     * get all id_product and id_product_attribute not present in ps relation table with
     *  * bool match_found field : if product or product_attribute reference is found in ps_relation table (join on buffer tables to find reference)
     *  * bool is_related field : if id_product+id_product_attribute is already associated to shippingbo id.
     *
     * @return string
     */
    public function getMissingFromShopProductsQuery()
    {
        // Sous requete permettant de récupérer tous les produits de PS qui ne sont pas dans la table relation
        // id_sbo
        // id_product
        // id_product_attribute
        // reference
        // is_related
        // is_batch (sous requete getSboIsBatch() en fonction du nombre de produits source qui compose le pack)
        // is_addref (en fonction de is_batch et quantité = 1)
        $psQuery = new \DbQuery();
        $psQuery
            ->select('p.id_product')
            ->select('pa.id_product_attribute')
            ->select('ps_relation.id_sbo')
            ->select('ps_relation.id_product as relation_product_id')
            ->from(self::PS_PRODUCT_TABLE_NAME, 'p')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_product = p.id_product AND ps_relation.id_product_attribute = COALESCE(pa.id_product_attribute,0) ')
            ->having('ps_relation.id_sbo IS NULL')
            ->having('relation_product_id IS NULL')
            ;

        BaseRepository::addGuessedSboType($psQuery);

        return $psQuery->__toString();
    }

    /**
     * Sous requête permettant de récupérer tous les produits présents dans table tampon produits sbo sans liaison avec produits/décli PS.
     *
     * @return string
     */
    public function getMissingProductsFromBufferQuery()
    {
        $sboQuery = new \DbQuery();
        $sboQuery
            ->select('ps_relation.'.$this->sboShopRelationTablePrimary.' as id_ps_relation')
            ->select('sbo.id as id_sbo')
            ->select('p.id_product')
            ->select('pa.id_product_attribute')
            ->select('NULL AS id_sbo_source')
            ->select('sbo.user_ref as reference')
            ->select('IF(ps_relation.id_sbo IS NOT NULL, true, false) as is_related')
            ->select('IF(p_exists.id_product IS NULL, true, false) as product_removed')
            ->select('IF(pa_exists.id_product_attribute IS NULL, true, false) as product_attribute_removed')
            ->from($this->sboProductsTable, 'sbo')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_sbo = sbo.id')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'ps_relation.id_product = p.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product = p.id_product')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_exists', 'p_exists.id_product = ps_relation.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_exists', 'pa_exists.id_product_attribute = ps_relation.id_product_attribute')
            ->having('is_related = false')
        ;

        return $sboQuery->__toString();
    }

    /**
     * @return string
     */
    public function getMissingAddrefsFromBufferQuery()
    {
        $sboAddRefsQuery = new \DbQuery();
        $sboAddRefsQuery
            ->select('ps_relation.'.$this->sboShopRelationTablePrimary.' as id_ps_relation')
            ->select('sbo_addrefs.id as id_sbo')
            ->select('ps_relation.id_product AS id_product')
            ->select('sbo_addrefs.product_value AS id_sbo_source')
            ->select('ps_relation.id_product_attribute AS id_product_attribute')
            ->select('sbo_addrefs.order_item_value as reference')
            ->select('IF(ps_relation.'.SboShopRelationModel::$definition['primary'].' IS NOT NULL AND ps_relation.id_sbo IS NOT NULL AND ps_relation.id_product IS NOT NULL, true,false) as is_related')
            ->select('sbo.is_pack')
            ->from($this->sboAdditionalRefsTable, 'sbo_addrefs')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'ps_relation.id_sbo = sbo_addrefs.id')
            ->leftJoin($this->sboProductsTable, 'sbo', 'sbo_addrefs.product_value = sbo.id')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p_exists', 'p_exists.id_product = ps_relation.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa_exists', 'pa_exists.id_product_attribute = ps_relation.id_product_attribute')
            ->having('is_related = false')
        ;

        return $sboAddRefsQuery->__toString();
    }

    /**
     * @return string
     */
    public function buildProductBufferMissingMatchQuery()
    {
        $dbQuery = new \DbQuery();
        $dbQuery
            ->select('sbo.id as id_sbo')
            ->select('COALESCE(pa.id_product,p.id_product) as id_product')
            ->select('COALESCE(pa.id_product_attribute,0) as id_product_attribute')
            ->select('sbo.id as id_sbo_source')
            ->select('sbo.user_ref as reference')
            ->select('IF(sbo.is_pack, "'.ShippingboService::SBO_PRODUCT_TYPE_PACK.'","'.ShippingboService::SBO_PRODUCT_TYPE_PRODUCT.'") as type_sbo')
            ->from($this->sboProductsTable, 'sbo')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'sbo.user_ref = p.reference')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'sbo.user_ref = pa.reference')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'p.id_product=ps_relation.id_product AND COALESCE(pa.id_product_attribute,0)=ps_relation.id_product_attribute')
            ->where('ps_relation.id_sbo IS NULL')
        ;

        return $dbQuery->__toString();
    }

    /**
     * @return string
     */
    public function buildAddrefBufferMissingMatchQuery()
    {
        $dbQuery = new \DbQuery();
        $dbQuery
            ->select('sbo_addrefs.id as id_sbo')
            ->select('COALESCE(pa.id_product,p.id_product) as id_product')
            ->select('COALESCE(pa.id_product_attribute,0) as id_product_attribute')
            ->select('sbo_addrefs.product_value as id_sbo_source')
            ->select('sbo_addrefs.order_item_value as reference')
            ->select('IF(sbo_addrefs.matched_quantity > 1, "'.ShippingboService::SBO_PRODUCT_TYPE_BATCH.'","'.ShippingboService::SBO_PRODUCT_TYPE_ADDREF.'") as type_sbo')
            ->from($this->sboAdditionalRefsTable, 'sbo_addrefs')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'sbo_addrefs.order_item_value = p.reference')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'sbo_addrefs.order_item_value = pa.reference')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'p.id_product=ps_relation.id_product AND COALESCE(pa.id_product_attribute,0)=ps_relation.id_product_attribute')
            ->where('ps_relation.id_sbo IS NULL')
        ;

        return $dbQuery->__toString();
    }

    /**
     * @desc remove relation in shop relation table if related PS product has been removed
     *
     * @return string
     */
    public function relationsToRemoveQuery()
    {
        $dbQuery = new \DbQuery();
        $dbQuery
            ->select('ps_relation.*')
            ->select('p.id_product')
            ->from($this->sboShopRelationTable, 'ps_relation')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'p.id_product = ps_relation.id_product')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa', 'pa.id_product_attribute = ps_relation.id_product_attribute')
            ->having('(p.id_product IS NULL) OR ps_relation.type_sbo != guessed_sbo_type')
        ;

        BaseRepository::addGuessedSboType($dbQuery);

        return $dbQuery->__toString();
    }

    /**
     * @desc remove relation in shop relation table if related PS product has been removed
     *
     * @return string
     */
    public function relationsMissingAttributesQuery()
    {
        $dbQuery = new \DbQuery();
        $dbQuery
            ->select('pa.id_product_attribute')
            ->from(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'pa')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'p', 'p.id_product = pa.id_product')
            ->leftJoin($this->sboShopRelationTable, 'ps_relation', 'p.id_product = ps_relation.id_product AND pa.id_product_attribute = ps_relation.id_product_attribute')
            ->where('ps_relation.id_product_attribute IS NULL')
            ->where('p.id_product=:id_product')
        ;

        return $dbQuery->__toString();
    }

    /**
     * recursivelv set relation in case of packs or batches.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function updateWithLinkedItems($params)
    {
        try
        {
            $now = new \DateTimeImmutable(null, new \DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
            $shopRelationStatement = $this->setStatement();

            // 1. UPDATE PRODUCT
            $shopRelationStatement->execute([
                'id_sbo' => $params['id_sbo'],
                'id_product' => $params['id_product'],
                'id_product_attribute' => 0,
                'id_sbo_source' => $params['id_sbo_source'],
                'is_locked' => (bool) $params['is_locked'],
                'type_sbo' => $params['type_sbo'],
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]);

            // 2. UPDATE COMBINATIONS locked status
            $updateCombinationsLock = $this->pdo->prepare('UPDATE '._DB_PREFIX_.$this->sboShopRelationTable.' SET is_locked=:is_locked WHERE id_product = :id_product');
            $updateCombinationsLock->execute([
                ':is_locked' => (bool) $params['is_locked'],
                ':id_product' => $params['id_product'],
            ]);

            // 3. UPDATE ps_pack LINKED ITEM
            $pack = new \Pack((int) $params['id_product']);
            if (!empty($pack))
            {
                foreach ($pack->getItems($params['id_product'], $this->service->getScAgent()->getIdLang()) as $packItem)
                {
                    $this->updateWithLinkedItems([
                        'id_sbo' => null,
                        'id_product' => $packItem->id,
                        'id_product_attribute' => $packItem->id_product_attribute ?: 0,
                        'id_sbo_source' => null,
                        'is_locked' => false,
                        'type_sbo' => ShippingboService::SBO_PRODUCT_TYPE_PRODUCT,
                    ]);
                }
            }
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $id
     * @param $type  : id_product/id_product_attribute/id_sbo
     *
     * @return mixed
     */
    public function getOneByIdProduct($id_product, $id_product_attribute = 0)
    {
        $dbQuery = new \DbQuery();
        $dbQuery
            ->select('ps_relation.*')
            ->from($this->sboShopRelationTable, 'ps_relation')
            ->where('id_product = :id_product')
            ->where('id_product_attribute = :id_product_attribute')
        ;

        $stmt = $this->pdo->prepare($dbQuery);
        $stmt->execute([':id_product' => $id_product, ':id_product_attribute' => $id_product_attribute]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
