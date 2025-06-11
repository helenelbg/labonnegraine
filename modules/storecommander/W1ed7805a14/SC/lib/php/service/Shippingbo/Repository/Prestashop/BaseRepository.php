<?php

namespace Sc\Service\Shippingbo\Repository\Prestashop;

use Sc\Service\Shippingbo\Model\AdditionalRefs as SboAdditionalRefsModel;
use Sc\Service\Shippingbo\Model\PackComponent as SboPackComponentModel;
use Sc\Service\Shippingbo\Model\Product as SboProductModel;
use Sc\Service\Shippingbo\Model\ShopRelation as SboShopRelationModel;
use Sc\Service\Shippingbo\ShippingboService;

abstract class BaseRepository
{
    const PS_PRODUCT_TABLE_NAME = 'product';
    const PS_PRODUCT_ATTRIBUTE_TABLE_NAME = 'product_attribute';
    const PS_PACK_TABLE_NAME = 'pack';

    /**
     * @var string
     */
    public $sboAdditionalRefsTable;

    /**
     * @var string
     */
    public $sboProductsTable;
    /**
     * @var mixed
     */
    public $sboShopRelationTable;
    /**
     * @var ShippingboService
     */
    public $service;

    /**
     * @var mixed
     */
    protected $sboPackComponentTable;
    /**
     * @var mixed
     */
    protected $idLang;
    /**
     * @var \mysqli|\PDO|resource|null
     */
    protected $pdo;
    /**
     * @var mixed
     */
    public $sboShopRelationTablePrimary;
    /**
     * @var bool
     */
    protected $onlyCount;

    public function __construct(ShippingboService $service)
    {
        $this->sboProductsTable = SboProductModel::$definition['table'];
        $this->sboAdditionalRefsTable = SboAdditionalRefsModel::$definition['table'];
        $this->sboPackComponentTable = SboPackComponentModel::$definition['table'];
        $this->sboShopRelationTable = SboShopRelationModel::$definition['table'];
        $this->sboShopRelationTablePrimary = SboShopRelationModel::$definition['primary'];
        $this->service = $service;
        $this->idLang = $service->getScAgent()->getIdLang();
        $this->pdo = $service->getPdo();
        $this->logger = $service->getLogger();
    }

    /**
     * @desc : add product information to query
     *
     * @return \DbQuery
     */
    public function addPsLangImageParts(\DbQuery $dbQuery)
    {
        $combinationNameSubQuery = new \DbQuery();
        $combinationNameSubQuery
            ->select('GROUP_CONCAT(al.name SEPARATOR " ")')
            ->from('product_attribute', 'pa_sub')
            ->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa_sub.id_product_attribute')
            ->leftJoin('attribute', 'attr', 'attr.id_attribute = pac.id_attribute')
            ->leftJoin('attribute_lang', 'al', 'al.id_attribute = pac.id_attribute AND al.id_lang = :id_lang')
            ->leftJoin('attribute_group_lang', 'agl', ' agl.id_attribute_group = attr.id_attribute_group AND agl.id_lang = :id_lang')
            ->leftJoin('product_attribute_shop', 'pas', 'pas.id_product_attribute = pac.id_product_attribute AND pas.id_shop IN('.pInSQL($this->service->getConfigShopsForPdo()).')')
            ->where('pa_sub.id_product = p.id_product AND pa_sub.id_product_attribute =pa.id_product_attribute')
        ;
        $dbQuery
            ->select('pl.name')
            ->select('pl.id_lang')
            ->select('COALESCE(pai.id_image, i.id_image, 0) as id_image')
            ->select('('.$combinationNameSubQuery.') as combination_name')
            ->leftJoin('product_lang', 'pl', 'pl.id_product = p.id_product AND pl.id_lang = :id_lang')
            ->leftJoin('image_shop', 'i', 'i.id_product= p.id_product AND i.id_shop IN('.pInSQL($this->service->getConfigShopsForPdo()).') AND i.cover=1')
            ->leftJoin('product_attribute_image', 'pai', 'pai.id_product_attribute= pa.id_product_attribute')
            ->where('pl.id_shop IN('.pInSQL($this->service->getConfigShopsForPdo()).')');

        return $dbQuery;
    }

    /**
     * @return \DbQuery
     */
    protected static function getSboIsBatch()
    {
        $dbQuery = new \DbQuery();
        $dbQuery
            ->select('IF(sub_sbo.is_pack IS NULL AND COUNT(DISTINCT sub_pak.id_product_item) = 1 AND MAX(sub_pak.quantity) > 1,  true,  false) AS is_sbo_batch')
            ->from(self::PS_PACK_TABLE_NAME, 'sub_pak')
            ->leftJoin(self::PS_PRODUCT_TABLE_NAME, 'sub_p', 'sub_p.id_product = sub_pak.id_product_pack')
            ->leftJoin(self::PS_PRODUCT_ATTRIBUTE_TABLE_NAME, 'sub_pa', 'sub_pa.id_product = sub_p.id_product')
            ->leftJoin(SboProductModel::$definition['table'], 'sub_sbo', 'sub_sbo.user_ref = sub_p.reference OR sub_sbo.user_ref = sub_pa.reference')
            ->leftJoin(SboAdditionalRefsModel::$definition['table'], 'sub_sbo_addrefs', 'sub_sbo_addrefs.order_item_value = sub_sbo.user_ref')
            ->where('sub_p.id_product = p.id_product')
            ->groupBy('sub_pak.id_product_pack');

        return $dbQuery;
    }

    public static function addGuessedSboType(&$dbQuery)
    {
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
        {
            $dbQuery->select('IF(IF(p.product_type = "'.ShippingboService::PS_PRODUCT_TYPE_PACK.'", true, false), IF(('.self::getSboIsBatch().') = 1,"batch","pack"), "product") as guessed_sbo_type');
        }
        else
        {
            $dbQuery->select('IF(IF(p.cache_is_pack = 1, true, false), IF(('.self::getSboIsBatch().') = 1,"batch","pack"), "product") as guessed_sbo_type');
        }
    }

    /**
     * build select.
     *
     * @return string
     */
    public static function getSelectForGuessedSboType()
    {
        $dbQuery = new \DbQuery();
        $dbQuery->from('dummy'); // pas top d'ajouter le from dummy pour le virer apres, sinon dbquery en erreur :(
        self::addGuessedSboType($dbQuery);
        $select = $dbQuery->__toString();
        $select = str_replace('FROM `'._DB_PREFIX_.'dummy`', '', $select);
        $select = str_replace(' as guessed_sbo_type', '', $select);

        return '('.$select.')';
    }

    public function addSboErrorParts(\DbQuery $dbQuery)
    {
        if ($this->onlyCount)
        {
            $dbQuery
                ->where('(('.$this->missingRefSubQuery().') OR ('.$this->duplicateRefSubQuery().')) = :has_error')
            ;
        }
        else
        {
            $dbQuery
                ->select('('.$this->missingRefSubQuery().') as missing_ref')
                ->select('('.$this->duplicateRefSubQuery().') as duplicate_ref')
                ->having('(missing_ref OR duplicate_ref) = :has_error')
            ;
        }

        return $dbQuery;
    }

    public function addPsErrorParts(\DbQuery $dbQuery)
    {
        $dbQuery
            ->select('('.$this->skuTooLongSubQuery().') as sku_too_long')
            ->having('sku_too_long = :has_error')
        ;

        return $dbQuery;
    }

    public function setPage(\DbQuery $dbQuery, $page = false)
    {
        if ($page !== false)
        {
            $offset = $page * $this->service->getGridResultsPerPage();
            $dbQuery->limit($this->service->getGridResultsPerPage(), $offset);
        }

        return $dbQuery;
    }

    protected function duplicateRefSubQuery()
    {
        return 'IF((SELECT count(COALESCE(pa2.reference, p2.reference,"")) FROM '._DB_PREFIX_.'product p2 LEFT JOIN '._DB_PREFIX_.'product_attribute pa2 ON pa2.id_product = p2.id_product WHERE COALESCE(pa.reference, p.reference,"") = COALESCE(pa2.reference, p2.reference,"")) > 1, true, false)';
    }

    public function setCountMode($enable)
    {
        $this->onlyCount = (bool) $enable;

        return $this;
    }
}
