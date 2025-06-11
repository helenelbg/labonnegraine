<?php

namespace Sc\Service\Shippingbo\Repository;

interface ShippingboRepositoryInterface
{
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function call($endpoint, $method = 'GET', $params = [], $timeout = 30);

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getProduct($id_product);

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getProducts($isPack = false, $lastCollect = false, $page = 0);

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getAdditionalRefs($lastCollect = false, $page = 0);
}
