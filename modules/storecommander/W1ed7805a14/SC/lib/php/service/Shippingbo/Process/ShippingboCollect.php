<?php

namespace Sc\Service\Shippingbo\Process;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DbQuery;
use Exception;
use PDO;
use Sc\service\Process\ProcessInterface;
use Sc\service\Process\Traits\ProcessWithPaginationTrait;
use Sc\Service\Shippingbo\Repository\ShippingboRepository;
use Sc\Service\Shippingbo\ShippingboService;

class ShippingboCollect implements ProcessInterface
{
    use ProcessWithPaginationTrait;
    /**
     * @var ShippingboRepository
     */
    protected $api;
    /**
     * @var ShippingboService
     */
    private $service;
    /**
     * @var int
     */
    private $nbProductsCollected;
    /**
     * @var int
     */
    private $nbPacksCollected;
    /**
     * @var int
     */
    private $nbAddRefsCollected;
    /**
     * @var int|null
     */
    private $nbPackComponentsCollected;

    public function __construct(ShippingboService $service)
    {
        $this->service = $service;
        $this->nbProductsCollected = 0;
        $this->nbPacksCollected = 0;
        $this->nbAddRefsCollected = 0;
        $this->nbPackComponentsCollected = 0;
    }

    /**
     * @desc : get last synchronization date with sbo data
     *
     * @return DateTime|false
     *
     * @throws Exception
     */
    public function getStartDate()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('CONVERT(MAX(COALESCE(sbo_addrefs.updated_at, sbo.updated_at)),datetime) as lastUpdate')
            ->from($this->getService()->getShopRelationRepository()->sboProductsTable, 'sbo')
            ->leftJoin($this->getService()->getShopRelationRepository()->sboAdditionalRefsTable, 'sbo_addrefs', 'sbo.id = sbo_addrefs.product_value');
        $lastCollectPsUpdate = $this->getService()->getPdo()->query($dbQuery);
        if ($lastCollectPsUpdate && $lastCollectPsUpdate->rowCount() > 0)
        {
            return DateTime::createFromFormat('Y-m-d H:i:s', $lastCollectPsUpdate->fetch(PDO::FETCH_ASSOC)['lastUpdate'], new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
        }

        return false;
    }

    /**
     * @return ShippingboService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @descr on tente de devnier une volumetrie approximative sans endpoint Api pour récupérer le total des produits dans SBO
     *
     * @return bool|int
     *
     * @throws Exception
     */
    public function getTotalProducts()
    {
        $shippingboRepository = $this->getService()->getShippingboRepository();
        $shippingboRepository->setBatchSize(1);
        $steps = [1000, 2000, 3000, 5000, 7000, 10000, 20000, 30000, 50000, 70000, 100000];
        $nbProducts = PHP_INT_MAX;
        foreach ($steps as $key => $stepQuantity)
        {
            $isLessThan = empty($shippingboRepository->getProducts(false, null, $stepQuantity));
            if ($isLessThan)
            {
                $nbProducts = $stepQuantity;
                break;
            }
        }

        return $nbProducts;
    }

    /**
     * @param int $page : current page for api paginated calls
     *
     * @return array
     *
     * @throws Exception
     */
    public function collectProducts($isPack, $lastDate, $page = 0)
    {
        $shippingboRepository = $this->getService()->getShippingboRepository();
        $shippingboRepository->setBatchSize($this->getBatchSize());
        $products = $shippingboRepository->getProducts($isPack, $lastDate, $page);

        if ($products)
        {
            if ($isPack)
            {
                $this->nbPacksCollected += count($products);
            }
            else
            {
                $this->nbProductsCollected += count($products);
            }
        }
        // insertion/update dans la table buffer
        $productUpdateStatement = $this->getService()->getProductRepository()->setBufferStatement();
        if (!empty($products))
        {
            $this->getService()->getPdo()->beginTransaction();
            foreach ($products as $product)
            {
                $synced_at = new DateTimeImmutable(null, new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
                $updated_at = new DateTimeImmutable($product['updated_at'], new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
                // add product to buffer table
                $productUpdateStatement->execute([
                    'id' => $product['id'],
                    'user_ref' => $product['user_ref'],
                    'is_pack' => (int) $isPack,
                    'title' => $product['title'],
                    'weight' => $product['weight'],
                    'height' => $product['height'],
                    'length' => $product['length'],
                    'width' => $product['width'],
                    'updated_at' => $updated_at->format('Y-m-d H:i:s'),
                    'synced_at' => $synced_at->format('Y-m-d H:i:s'),
                ]);
            }
            if ($this->getService()->getPdo()->inTransaction())
            {
                $this->getService()->getPdo()->commit();
            }
        }

        return $products;
    }

    /**
     * @param $page : current page for api paginated calls
     *
     * @return array|false
     *
     * @throws Exception
     */
    public function collectAdditionalRefs($lastDate, $page = 0)
    {
        // récupération des données SBO via api
        $shippingboRepository = $this->getService()->getShippingboRepository();
        $additionalReferences = $shippingboRepository->getAdditionalRefs($lastDate, $page);
        if ($additionalReferences)
        {
            $this->nbAddRefsCollected += count($additionalReferences);
        }
        // insertion/update dans la table buffer
        $additionalRefsUpdateStatement = $this->getService()->getBatchRepository()->setBufferStatement();
        if (!empty($additionalReferences))
        {
            $this->getService()->getPdo()->beginTransaction();
            // reformatage des données
            foreach ($additionalReferences as $additionalReference)
            {
                $updated_at = new DateTimeImmutable($additionalReference['created_at']);
                $additionalReference['created_at'] = $updated_at->format('Y-m-d H:i:s');
                $updated_at = new DateTimeImmutable($additionalReference['updated_at']);
                $additionalReference['updated_at'] = $updated_at->format('Y-m-d H:i:s');
                $synced_at = new DateTimeImmutable(null, new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
                $additionalReference['synced_at'] = $synced_at->format('Y-m-d H:i:s');
                $additionalRefsUpdateStatement->execute($additionalReference);
            }

            if ($this->getService()->getPdo()->inTransaction())
            {
                $this->getService()->getPdo()->commit();
            }
        }

        return $additionalReferences;
    }

    /**
     * TODO : A revoir si nouveau endpoint api (un appel pour chaque produit : lourd).
     *
     * @return array|false
     *
     * @throws Exception
     */
    public function collectPackComponents($lastDate)
    {
        $packComponentUpdateStatement = $this->getService()->getPackRepository()->setBufferStatement();
        $packComponentGetStatement = $this->getService()->getPdo()->query($this->service->getPackRepository()->getSboComponentsQuery($lastDate));
        $packs = $packComponentGetStatement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($packs as $sboProduct)
        {
            // récupération des données SBO via api
            $productDetail = $this->getService()->getShippingboRepository()->getProduct($sboProduct['id']);
            $created_at = new DateTimeImmutable($productDetail['created_at']);
            $updated_at = new DateTimeImmutable($productDetail['updated_at']);
            if (isset($productDetail['pack_components']))
            {
                // insertion/update dans la table buffer
                $this->nbPackComponentsCollected += count($productDetail['pack_components']);
                foreach ($productDetail['pack_components'] as $packComponent)
                {
                    $synced_at = new DateTimeImmutable(null, new DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
                    $packComponentUpdateStatement->execute([
                        ':id' => $packComponent['id'],
                        ':quantity' => $packComponent['quantity'],
                        ':pack_product_id' => $packComponent['pack_product_id'],
                        ':component_product_id' => $packComponent['component_product_id'],
                        ':created_at' => $created_at->format('Y-m-d H:i:s'),
                        ':updated_at' => $updated_at->format('Y-m-d H:i:s'),
                        ':synced_at' => $synced_at->format('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        return $packs;
    }

    /**
     * @desc : launch sync by name
     *
     * @throws Exception
     */
    public function get($type, $lastCollectDate, $page = 0)
    {
        switch ($type) {
            case 'products':
                return $this->collectProducts(false, $lastCollectDate, $page);
            case 'packs':
                return $this->collectProducts(true, $lastCollectDate, $page);
            case 'additional_references':
                return $this->collectAdditionalRefs($lastCollectDate, $page);
            case 'pack_components':
                return $this->collectPackComponents($lastCollectDate, $page);
        }

        return [];
    }

    public function getProcessMessageForIteration($iteration, $countProcessed, $method, $methodArguments)
    {
        if ($countProcessed === $this->getBatchSize())
        {
            $totalProcessed = ($iteration + 1) * $this->getBatchSize();
        }
        else
        {
            $totalProcessed = ($iteration) * $this->getBatchSize() + $countProcessed;
        }
        if ($totalProcessed)
        {
            return _l('%s %s collected', 0, [$totalProcessed, str_replace('_', ' ', $methodArguments[0])]);
        }

        return _l('No '.$methodArguments[0].' found');
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
