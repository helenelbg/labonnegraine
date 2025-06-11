<?php

namespace Sc\Service\Shippingbo\Process;

use Sc\service\Process\ProcessInterface;
use Sc\service\Process\Traits\ProcessWithPaginationTrait;
use Sc\Service\Shippingbo\Model\ShopRelation as SboShopRelationModel;
use Sc\Service\Shippingbo\Repository\ShippingboRepository;
use Sc\Service\Shippingbo\ShippingboService;

class ShippingboMatch implements ProcessInterface
{
    use ProcessWithPaginationTrait;
    /**
     * @var ShippingboService
     */
    private $service;

    public function __construct(ShippingboService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws \Exception
     */
    public function start()
    {
        $this->removeRelations();
        $this->addMissingRelations();
        $this->matchProducts();

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function addMissingRelations()
    {
        // récupérer les produits présents dans PS non présents dans la table relation
        // * les produits de type standard
        // * les produits de type combinations
        // * les produits de type pack :
        //   * si plus d'un produit source dans la composition du pack → pack
        //   * si un seul produit source dans la composition du pack et quantités > 1 → batch
        //   * si un seul produit source dans la composition du pack et quantités = 1 → additional_reference
        $time_pre = microtime(true);
        $shopProductRelationMissingQuery = $this->getService()->getPdo()->query($this->getService()->getShopRelationRepository()->getMissingFromShopProductsQuery());
        $shopProductRelationMissing = $shopProductRelationMissingQuery ? $shopProductRelationMissingQuery->fetchAll(\PDO::FETCH_ASSOC) : [];

        $sboShopRelationStmt = $this->getService()->getShopRelationRepository()->setStatement();
        $this->getService()->getPdo()->beginTransaction();
        foreach ($shopProductRelationMissing as $match)
        {
            $now = new \DateTimeImmutable(null, new \DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));

            // update Db
            $sboShopRelationStmt->execute([
                'id_sbo' => $match['id_sbo'],
                'id_product' => $match['id_product'],
                'id_product_attribute' => $match['id_product_attribute'] ?: 0,
                'id_sbo_source' => isset($match['id_sbo_source']) ? $match['id_sbo_source'] : null,
                'is_locked' => !$match['id_sbo'],
                'type_sbo' => $match['guessed_sbo_type'],
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]);
        }
        if ($this->getService()->getPdo()->inTransaction())
        {
            $this->getService()->getPdo()->commit();
        }
        $time_post = microtime(true);
        $this->getService()->getLogger()->debug('addMissingRelations() in '.(($time_post - $time_pre) / 10000).'s');

        return $shopProductRelationMissing;
    }

    /**
     * @return ShippingboService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @throws \Exception
     */
    public function matchProducts()
    {
        $time_pre = microtime(true);
        // on traite les deux tables produit et add_ref séparément
        $matchProductBufferMissing = $this->getService()->getPdo()->query($this->getService()->getShopRelationRepository()->buildProductBufferMissingMatchQuery())->fetchAll(\PDO::FETCH_ASSOC);

        $matchAddrefBufferMissing = $this->getService()->getPdo()->query($this->getService()->getShopRelationRepository()->buildAddrefBufferMissingMatchQuery())->fetchAll(\PDO::FETCH_ASSOC);

        $matchAllMissing = array_merge($matchProductBufferMissing, $matchAddrefBufferMissing);

        $sboShopRelationStmt = $this->getService()->getShopRelationRepository()->setStatement();
        $this->getService()->getPdo()->beginTransaction();
        foreach ($matchAllMissing as $match)
        {
            $now = new \DateTimeImmutable(null, new \DateTimeZone(ShippingboRepository::SERVER_TIMEZONE));
            // update Db
            $sboShopRelationStmt->execute([
                'id_sbo' => $match['id_sbo'],
                'id_product' => $match['id_product'],
                'id_product_attribute' => $match['id_product'] ? $match['id_product_attribute'] : null,
                'id_sbo_source' => $match['id_sbo_source'],
                'is_locked' => false,
                'type_sbo' => $match['type_sbo'],
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ]);
        }
        if ($this->getService()->getPdo()->inTransaction())
        {
            $this->getService()->getPdo()->commit();
        }
        $time_post = microtime(true);
        $this->getService()->getLogger()->debug('matchProducts() in '.(($time_post - $time_pre) / 10000).'s');
    }

    /**
     * @desc remove relation if product removed
     *
     * @return void
     */
    public function removeRelations()
    {
        $time_pre = microtime(true);
        $relationsToRemove = $this->getService()->getPdo()->query($this->getService()->getShopRelationRepository()->relationsToRemoveQuery())->fetchAll(\PDO::FETCH_ASSOC);

        $sboShopRelationStmt = $this->getService()->getShopRelationRepository()->removeShopRelationStatement();
        $this->getService()->getPdo()->beginTransaction();
        foreach ($relationsToRemove as $relationToRemove)
        {
            $sboShopRelationStmt->execute([
                'id' => $relationToRemove[SboShopRelationModel::$definition['primary']],
            ]);
        }
        if ($this->getService()->getPdo()->inTransaction())
        {
            $this->getService()->getPdo()->commit();
        }
        $time_post = microtime(true);
        $this->getService()->getLogger()->debug('removeRelations() in '.(($time_post - $time_pre) / 10000).'s');
    }

    public function getProcessMessageForIteration($iteration, $countProcessed, $method, $methodArguments)
    {
        $totalProcessed = ($iteration + 1) * $countProcessed;
        switch ($method){
            case 'removeRelations':
                $hasSomeMessage = _l('%s relation(s) removed', 0, [$totalProcessed]);
                $hasNoneMessage = _l('no relations to remove', 0, [$totalProcessed]);
                break;
            case 'addMissingRelations':
                $hasSomeMessage = _l('%s relation(s) added', 0, [$totalProcessed]);
                $hasNoneMessage = _l('no relations to add', 0, [$totalProcessed]);
                break;
            case 'matchProducts':
                $hasSomeMessage = _l('%s match(es) found', 0, [$totalProcessed]);
                $hasNoneMessage = _l('no match found', 0, [$totalProcessed]);
                break;
        }
        if ($totalProcessed)
        {
            return $hasSomeMessage;
        }

        return $hasNoneMessage;
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
