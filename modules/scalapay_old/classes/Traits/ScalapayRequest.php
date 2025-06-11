<?php

namespace Scalapay\Traits;

trait ScalapayRequest
{
    protected function getApiAuth($product)
    {
        switch ($product) {
            case \Scalapay::PRODUCT_PAY_IN_4:
                $production = \Configuration::get(\Scalapay::SCALAPAY_PAY_IN_4_LIVE_MODE_ENABLED);
                break;
            case \Scalapay::PRODUCT_PAY_LATER:
                $production = \Configuration::get(\Scalapay::SCALAPAY_PAY_LATER_LIVE_MODE_ENABLED);
                break;
            case \Scalapay::PRODUCT_PAY_IN_3:
            default:
                $production = \Configuration::get(\Scalapay::SCALAPAY_PAY_IN_3_LIVE_MODE_ENABLED);
                break;
        }

        $token = \Configuration::get(\Scalapay::SCALAPAY_TEST_KEY);
        $url = \Configuration::get(\Scalapay::SCALAPAY_TEST_URL);
        if ($production) {
            $token = \Configuration::get(\Scalapay::SCALAPAY_LIVE_KEY);
            $url = \Configuration::get(\Scalapay::SCALAPAY_LIVE_URL);
        }

        return array(
            "url" => $url,
            "token" => $token,
        );
    }

    public function doRequest($product, $method, $path, $data = array())
    {
        $auth = $this->getApiAuth($product);

        $curl = curl_init();

        $config = array(
            CURLOPT_URL => "{$auth["url"]}$path",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Bearer {$auth["token"]}",
            ),
        );

        if ($data) {
            $config[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $config);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return array("data" => json_decode($response, true), "info" => $info);
    }
}
