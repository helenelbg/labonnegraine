<?php

namespace Sc\Service;

use Exception;

interface ScServiceInterface
{
    /**
     * @return mixed
     */
    public function unregister();

    /**
     * @return mixed
     */
    public function register();

    /**
     * @return mixed
     */
    public function getConfig($refresh = false);

    /**
     * @return mixed
     */
    public function setConfig($params);

    /**
     * @return mixed
     */
    public function isActive();

    /**
     * @return mixed
     */
    public function checkConfig($paramName = false);

    /**
     * @return array|string[]
     */
    public function getConfigDefinition($onlyRequired = false);

    /**
     * @param array|string[] $configParams
     */
    public function setConfigDefinition($configParams);

    /**
     * @return mixed
     */
    public function addError(Exception $exception);

    /**
     * @desc : send response as json
     *
     * @param string $successMessage
     * @param array  $extra
     *
     * @return void
     */
    public function sendResponse($successMessage = 'success', $extra = []);

    /**
     * @return mixed
     */
    public function getConfigValue($key);

    public static function setNeededPSConfig();
}
