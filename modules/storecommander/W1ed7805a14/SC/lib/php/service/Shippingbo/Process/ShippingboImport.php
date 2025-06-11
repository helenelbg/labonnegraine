<?php

namespace Sc\Service\Shippingbo\Process;

use Exception;
use PDO;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use Sc\service\Process\ProcessInterface;
use Sc\service\Process\Traits\ProcessWithPaginationTrait;
use Sc\Service\Shippingbo\Repository\Prestashop\SegmentRepository;
use Sc\Service\Shippingbo\Repository\Prestashop\ShopRelationRepository;
use Sc\Service\Shippingbo\ShippingboService;

class ShippingboImport implements ProcessInterface
{
    use ProcessWithPaginationTrait;
    const PRODUCT_NAME_TYPE_SKU = 'logistic_sku';
    const PRODUCT_NAME_TYPE_TITLE = 'logistic_title';

    const NB_BATCH_IMPORT = 50;

    /**
     * @var SegmentRepository
     */
    public $segment = null;
    /**
     * @var ShippingboService
     */
    private $service;
    /**
     * @var false|mixed
     */
    protected $segmentType = SegmentRepository::TYPE_PENDING;
    /**
     * @var ShippingboCollect|ShopRelationRepository
     */
    protected $collect;

    /**
     * @var int
     */
    protected $nbProductsImported;
    /**
     * @var int
     */
    protected $nbPacksImported;
    /**
     * @var int
     */
    protected $nbBatchesImported;

    /**
     * @var int
     */
    protected $nbProductsUpdated;
    /**
     * @var int
     */
    protected $nbBatchesUpdated;
    /**
     * @var int
     */
    protected $nbPacksUpdated;
    /**
     * @var false|mixed
     */
    private $productNameType = self::PRODUCT_NAME_TYPE_TITLE;

    /**
     * @var array|bool[]
     */
    public $fieldsToImport = ['width' => true, 'height' => true, 'length' => true, 'weight' => true];
    /**
     * @var mixed
     */
    public $type;
    /**
     * @var SegmentRepository
     */
    public $lastCreatedSegment;

    public function __construct(ShippingboService $service)
    {
        $this->service = $service;
        $this->collect = $service->getCollectProcess();
        $this->nbProductsImported = 0;
        $this->nbPacksImported = 0;
        $this->nbBatchesImported = 0;
        $this->nbBatchesUpdated = 0;
        $this->nbProductsUpdated = 0;
        $this->nbPacksUpdated = 0;
    }

    /**
     * @desc : create specific segments if needed
     *
     * @return SegmentRepository
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getSegment()
    {
        if (!$this->segment)
        {
            // Création segment de base
            $sboRootSegmentId = SegmentRepository::getRootSegment($this->getService()->getScAgent());
            switch ($this->segmentType)
            {
                case 'pending':
                    $idSegment = SegmentRepository::getPendingSegmentId($sboRootSegmentId, $this->service->getScAgent());
                    break;
                default:
                    $idSegment = SegmentRepository::getInstantSboSegmentId($sboRootSegmentId, $this->service->getScAgent());
            }
            $segment = new SegmentRepository($this->getService()->getScAgent());
            $segment->id = $idSegment;
            $this->segment = $segment;
            $this->lastCreatedSegment = $this->segment;
        }

        return $this->segment;
    }

    /**
     * @desc : start import data to Prestashop Db depending on $type provided
     *
     * @return $this
     *
     * @throws Exception
     */
    private function importToPS($type)
    {
        switch ($type)
        {
            case 'products':
                $this->importProductsToPS();
                $this->getService()->getLogger()->info($this->getNbProductsImported().' product(s) imported');
                break;
            case 'batches':
                $this->importBatchesToPS();
                $this->getService()->getLogger()->info($this->getNbBatchesImported().' additional reference(s) imported');

                break;
            case 'packs':
                $this->importPacksToPS();
                $this->getService()->getLogger()->info($this->getNbPacksImported().' pack(s) imported');
                break;
        }

        return $this;
    }

    /**
     * @param $type
     *
     * @return $this|array|false
     *
     * @throws Exception
     */
    public function updatePS($type)
    {
        switch ($type)
        {
            case 'products':
                return $this->updatePsProducts($this->getService()->getProductRepository()->getLastSyncedDate());
            case 'batches':
                return $this->updatePsBatches($this->getService()->getBatchRepository()->getLastSyncedDate());
            case 'packs':
                return $this->updatePsPacks($this->getService()->getPackRepository()->getLastSyncedDate());
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getNbProductsImported()
    {
        return $this->nbProductsImported;
    }

    /**
     * @return int
     */
    public function getNbPacksImported()
    {
        return $this->nbPacksImported;
    }

    /**
     * @return int
     */
    public function getNbBatchesImported()
    {
        return $this->nbBatchesImported;
    }

    /**
     * [IMPORT].
     *
     * @param false|mixed $productNameType
     */
    public function setProductNameType($productNameType)
    {
        if ($productNameType)
        {
            $this->productNameType = $productNameType;
        }

        return $this;
    }

    /**
     * @param false|mixed $segmentType
     */
    public function setSegmentType($segmentType = null)
    {
        if ($segmentType)
        {
            $this->segmentType = $segmentType;
        }

        return $this;
    }

    /**
     * @param array|bool[] $fieldsToImport
     */
    public function setFieldsToImport($fieldsToImport = null)
    {
        if ($fieldsToImport)
        {
            $this->fieldsToImport = $fieldsToImport;
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function startImport()
    {
        $this
            ->importToPS('products')
            ->importToPS('batches')
            ->importToPS('packs')
        ;
        $this->segment = null;

        return $this;
    }

    /**
     * [IMPORT].
     *
     * @return void
     *
     * @throws Exception
     */
    private function importProductsToPS()
    {
        $productsStatement = $this->getService()->getPdo()->query($this->getService()->getProductRepository()->getMissingPsQuery());
        $this->nbProductsImported = $productsStatement->rowCount();
        // get all imported SKUS with product information
        foreach ($productsStatement->fetchAll(PDO::FETCH_ASSOC) as $productInfos)
        {
            $product = $this->replaceProduct($productInfos, 'product', 'user_ref');
            $this->getSegment()->addProduct($product);
        }
    }

    /**
     * @param $lastModified
     *
     * @return array|false
     *
     * @throws PrestaShopException
     */
    private function updatePsProducts($lastModified)
    {
        $productsStatement = $this->getService()->getPdo()->prepare($this->getService()->getProductRepository()->getUpdatedQuery());
        $productsStatement->execute([
            ':updated_at' => $lastModified->format('Y-m-d H:i:s'),
        ]);
        $this->nbProductsUpdated = $productsStatement->rowCount();
        // get all imported SKUS with product information
        foreach ($updates = $productsStatement->fetchAll(PDO::FETCH_ASSOC) as $productInfos)
        {
            $this->replaceProduct($productInfos, 'product', 'user_ref');
        }

        return $updates;
    }

    /**
     * [IMPORT].
     *
     * @return void
     *
     * @throws Exception
     */
    private function importBatchesToPS()
    {
        $this->getService()->getPdo()->beginTransaction();
        $batchesStatement = $this->getService()->getPdo()->prepare($this->getService()->getBatchRepository()->getMissingPsQuery());
        $batchesStatement->execute();
        $this->nbBatchesImported += $batchesStatement->rowCount();
        foreach ($batchesStatement->fetchAll(PDO::FETCH_ASSOC) as $productInfos)
        {
            $product = $this->replaceProduct($productInfos, 'batch', 'user_ref');
            $this->getSegment()->addProduct($product);
            $this->replacePsPack($product->id, $productInfos['id_product_item'], $productInfos['id_product_attribute_item'], $productInfos['matched_quantity']);
        }

        if ($this->getService()->getPdo()->inTransaction())
        {
            $this->getService()->getPdo()->commit();
        }
    }

    /**
     * @param $lastModified
     *
     * @return array|false
     *
     * @throws PrestaShopException
     */
    private function updatePsBatches($lastModified)
    {
        $batchesStatement = $this->getService()->getPdo()->prepare($this->getService()->getBatchRepository()->getUpdatedQuery());
        $batchesStatement->execute([
            ':updated_at' => $lastModified->format('Y-m-d H:i:s'),
        ]);

        $this->nbBatchesUpdated = $batchesStatement->rowCount();
        // get all imported SKUS with product information
        foreach ($updates = $batchesStatement->fetchAll(PDO::FETCH_ASSOC) as $productInfos)
        {
            $product = $this->replaceProduct($productInfos, 'batch', 'user_ref');
            $this->replacePsPack($product->id, $productInfos['id_product_item'], $productInfos['id_product_attribute_item'], $productInfos['matched_quantity']);
        }

        return $updates;
    }

    /**
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function importPacksToPS()
    {
        $packsStatement = $this->getService()->getPdo()->prepare($this->getService()->getPackRepository()->getMissingPsQuery());
        $packsStatement->execute();
        $this->nbPacksImported += $packsStatement->rowCount() ?: 0;
        $this->nbPacksImported += $packsStatement->rowCount() ?: 0;
        $packComponentsPrepareStatement = $this->getService()->getPdo()->prepare($this->getService()->getPackRepository()->getComponentsQuery());
        foreach ($packsStatement->fetchAll(PDO::FETCH_ASSOC) as $productInfos)
        {
            // creation/récupération produit
            $product = $this->replaceProduct($productInfos, 'pack', 'user_ref');

            $this->getSegment()->addProduct($product);
            // récupérer les produits liés dans buffer pack
            $packComponentsPrepareStatement->execute([':id_product' => $product->id]);

            foreach ($packComponentsPrepareStatement->fetchAll(PDO::FETCH_ASSOC) as $packComponent)
            {
                // add pack to db
                $this->replacePsPack($product->id, $packComponent['id_product'], $packComponent['id_product_attribute'], $packComponent['quantity']);
            }
        }
    }

    /**
     * @param $lastModified
     * @return array|false
     * @throws PrestaShopException
     */
    private function updatePsPacks($lastModified)
    {
        $packsStatement = $this->getService()->getPdo()->prepare($this->getService()->getPackRepository()->getUpdatedQuery());
        $packsStatement->execute([
            ':updated_at' => $lastModified->format('Y-m-d H:i:s'),
        ]);
        $this->nbPacksUpdated = $packsStatement->rowCount();
        // get all imported SKUS with product information
        foreach ($updates = $packsStatement->fetchAll(PDO::FETCH_ASSOC) as $productInfos)
        {
            $this->replaceProduct($productInfos, 'pack', 'user_ref');
            $this->replacePsPack($productInfos['id_product_pack'], $productInfos['id_product_item'], $productInfos['id_product_attribute_item'], $productInfos['quantity']);
        }

        return $updates;
    }

    /**     * [IMPORT].
     *
     * @param $productInfos : product array
     * @param $type : product type ('product','batch', 'pack')
     * @param $ref : reference index to use from $productInfos
     *
     * @return Product
     *
     * @throws PrestaShopException
     */
    protected function replaceProduct($productInfos, $type, $ref)
    {
        $productExists = isset($productInfos['id_product']);
        $productName = ($this->productNameType === self::PRODUCT_NAME_TYPE_SKU) ? ucfirst(_l($type, 1)).' '.$productInfos[$ref] : utf8_decode($productInfos['title']);
        $is_pack = in_array($type, [ShippingboService::SBO_PRODUCT_TYPE_BATCH, ShippingboService::SBO_PRODUCT_TYPE_PACK]);

        return $this->getService()->getProductRepository()->saveProduct($productInfos, $productExists, $productName, $ref, $is_pack, $this->service->getScAgent()->getIdLang());
    }

    /**
     * [IMPORT].
     *
     * @desc : add packs information to PS
     *
     * @return bool
     */
    public function replacePsPack($product_id, $id_product_item, $id_product_attribute_item, $quantity)
    {
        $stmtPack = $this->getService()->getPackRepository()->setPsStatement();
        $packParams = [':id_product_pack' => $product_id, ':id_product_item' => $id_product_item, ':id_product_attribute_item' => $id_product_attribute_item, ':quantity' => $quantity];

        return $stmtPack->execute($packParams);
    }

    /**
     * @return ShippingboService
     */
    public function getService()
    {
        return $this->service;
    }

    public function getProcessMessageForIteration($iteration, $countProcessed, $method, $methodArguments)
    {
        $totalProcessed = ($iteration + 1) * $countProcessed;
        if ($totalProcessed)
        {
            return _l('%s updates from Shippingbo', 0, [$totalProcessed]);
        }

        return _l('No update from Shippingbo');
    }

    /**
     * @param $message
     *
     * @return string
     */
    public function getProcessMessageCompleted($message)
    {
        return $message;
    }
}
