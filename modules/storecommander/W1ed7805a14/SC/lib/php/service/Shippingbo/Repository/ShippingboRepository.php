<?php

namespace Sc\Service\Shippingbo\Repository;

use Sc\service\Process\Traits\ProcessWithPaginationTrait;
use Sc\Service\ScServiceInterface;
use shared\ScLogger\Traits\ScLoggerTrait;

class ShippingboRepository implements ShippingboRepositoryInterface
{
    use ProcessWithPaginationTrait;
    use ScLoggerTrait;

    const SERVER_TIMEZONE = 'UTC';
    const PROPAGATION_TRESHOLD = '30sec';

    /**
     * @var array|mixed[]
     */
    protected $config;
    /**
     * @var ScServiceInterface
     */
    private $service;

    public function __construct(ScServiceInterface $service)
    {
        $this->service = $service;
        $this->logger = $service->getLogger();
        $this->config = $service->getConfig();
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function call($endpoint, $method = 'GET', $params = [], $timeout = 30)
    {
        $headers = [
            'Content-Type: application/json',
            'X-API-USER: '.$this->config['apiUser']['value'],
            'X-API-TOKEN: '.$this->config['apiToken']['value'],
            'X-API-VERSION: '.$this->config['apiVersion']['value'],
        ];
        $url = $this->getConfig()['apiUrl']['value'].$endpoint;

        return $this->get($url, $method, $params, $headers, $timeout);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getProduct($id_product)
    {
        return $this->call('/products/'.$id_product);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getProducts($isPack = false, $lastCollect = false, $page = 0)
    {
        $params = [
            'is_pack' => $isPack ? 'true' : 'false',
            'offset' => $page * $this->getBatchSize(),
        ];
        if ($lastCollect)
        {
            $params['search']['updated_at__gt'] = $lastCollect->modify('-'.self::PROPAGATION_TRESHOLD)->format('c');
        }
        return $this->call('/products', 'GET', $params);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getAdditionalRefs($lastCollect = false, $page = 0)
    {
        $params = [
            'offset' => $page * $this->getMaxPage(),
        ];
        if ($lastCollect)
        {
            $params['search']['updated_at__gt'] = $lastCollect->format('c');
        }

        return $this->call('/order_item_product_mappings', 'GET', $params);
    }

    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ScServiceInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return false|mixed
     *
     * @throws \Exception
     */
    private function get($url, $method, $params = [], $headers = [], $timeout = 30)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : $this->getBatchSize();
        $params['offset'] = isset($params['offset']) ? $params['offset'] : 0;

        $entries = json_decode(sc_file_get_contents($url, $method, $params, $headers, $timeout), true);
        if (!empty($entries))
        {
            $entries = reset($entries);
        }

        if (isset($entries['message']))
        {
            $message = _l('Shippingbo API Error : %s. Please verify api configuration.', null, [$entries['message']]);
            if ($entries['message'] === '403 FORBIDDEN')
            {
                $message = _l('Authentication problem: please <a href="%s">check Shippingbo connection settings</a> If the problem persists, please contact the Shippingbo team.', true, ['#sbo_settings']);
            }
            throw new \Exception($message);
        }
//        $this->getLogger()->info($method.' '.$url.' - '.json_encode($params));

        return $entries;
    }
}
