<?php

namespace Sc\Service;

require 'ServiceModel.php';
require 'ConfigurationModel.php';
require 'ScServiceInterface.php';

use Configuration;
use Context;
use DateTime;
use DateTimeImmutable;
use Db;
use Exception;
use IntlDateFormatter;
use PDO;
use Sc\ScLogger\ScLogger;
use Sc\Service\ConfigurationModel as serviceConfigurationModel;
use SCI;

abstract class ScService implements ScServiceInterface
{
//    public static $instance = null;
    /**
     * @var ScService
     */
    protected static $instance;

    private static $psFolders = [];
    /**
     * @var PDO|resource|null
     */
    protected $pdo;
    /**
     * @var ScLogger
     */
    protected $logger;
    /**
     * @var \SC_Agent
     */
    protected $sc_agent;
    public $serviceName;
    /**
     * @var array|string[]
     */
    protected $configDefinition;
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var false
     */
    protected $debug;
    /**
     * @var array
     */
    private $config;

    public function __construct()
    {
        static::$instance = $this;
    }

    /**
     * instantiate Dynamic class and register default configuration.
     *
     * @param $serviceName : snake_case
     */
    public static function autoRegister($serviceName)
    {
        $serviceName = ucfirst(str_replace('_', '', ucwords($serviceName, '_')));
        $classFile = $serviceName.'/'.$serviceName.'Service.php';
        if (file_exists(__DIR__.'/'.$classFile))
        {
            require_once $serviceName.'/'.$serviceName.'Service.php';
            $serviceClassName = 'Sc\\Service\\'.$serviceName.'\\'.$serviceName.'Service';
            static::createTablesIfNeeded();
            $service = new $serviceClassName();
            $service->createTablesIfNeeded();
            if (!$service->isActive())
            {
                $service->unregister();
                $service->uninstall();

                return false;
            }
            else
            {
                $service->setNeededPSConfig();
                $service->setLogger(new ScLogger($serviceName));
                $service->register();

                return $service;
            }
        }

        return false;
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * @param DateTimeImmutable|DateTime $dateTime
     *
     * @return bool|string
     */
    public static function getLocaleDate($dateTime, $formatPattern)
    {
        // date
        $timezone = in_array(Configuration::get('PS_TIMEZONE'), timezone_identifiers_list()) ? Configuration::get('PS_TIMEZONE') : null;
        $formatter = new IntlDateFormatter(Context::getContext()->language->locale, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT, $timezone);
        $formatter->setPattern($formatPattern);

        return $formatter->format($dateTime->getTimestamp());
    }

    public static function exists($serviceName, $mustBeActive = false)
    {
        $serviceName = ucfirst(str_replace('_', '', ucwords($serviceName, '_')));
        $classFile = $serviceName.'/'.$serviceName.'Service.php';
        $exists = file_exists(__DIR__.'/'.$classFile);
        if ($exists && $mustBeActive)
        {
            $serviceClassName = 'Sc\\Service\\'.$serviceName.'\\'.$serviceName.'Service';
            /* @var ScServiceInterface $serviceClassName */
            try
            {
                $service = $serviceClassName::getInstance();
            }
            catch (Exception $e)
            {
                return false;
            }

            return $service->isActive();
        }

        return $exists;
    }

    protected static function createTablesIfNeeded()
    {
        serviceModel::createTablesIfNeeded();
        serviceConfigurationModel::createTablesIfNeeded();
    }

    public function unregister()
    {
        $stmt = $this->getPdo()->prepare('SELECT id_service FROM `'._DB_PREFIX_.serviceModel::$definition['table'].'` WHERE name = :name');
        $stmt->execute([':name' => $this->serviceName]);
        $serviceId = $stmt->fetch(PDO::FETCH_COLUMN);

        $stmtRemoveService = $this->getPdo()->prepare('DELETE FROM `'._DB_PREFIX_.serviceModel::$definition['table'].'` WHERE id_service = :id_service');

        $stmtRemoveConfigurations = $this->getPdo()->prepare('DELETE FROM `'._DB_PREFIX_.ConfigurationModel::$definition['table'].'` WHERE id_service = :id_service');

        $this->getPdo()->beginTransaction();

        if (!$stmtRemoveService->execute([':id_service' => $serviceId]))
        {
            $this->getPdo()->rollback();

            return false;
        }

        if (!$stmtRemoveConfigurations->execute([':id_service' => $serviceId]))
        {
            $this->getPdo()->rollback();

            return false;
        }
        $this->getPdo()->commit();

        return true;
    }

    public function uninstall()
    {
//        $this->unregister();
//        foreach(static::$psFolders as $key => $folder){
//            dirRemove(realpath(__DIR__.'/../../../'.$folder));
//        }
//        $serviceName = ucFirst(str_replace('_', '', ucwords($this->serviceName, '_')));
//        dirRemove(__DIR__ . '/'.$serviceName);
    }

    public function register()
    {
        $this->getPdo()->beginTransaction();
        // register service
        $stmtService = $this->getPdo()->prepare('INSERT IGNORE INTO `'._DB_PREFIX_.serviceModel::$definition['table'].'` (`name`,`created_at`) VALUES(:service_name,:created_at) ');
        $stmtService->execute([':service_name' => $this->serviceName, ':created_at' => date('Y-m-d H:i:s')]);

        $stmt = $this->getPdo()->prepare('SELECT id_service FROM `'._DB_PREFIX_.serviceModel::$definition['table'].'` WHERE name = :name');
        $stmt->execute([':name' => $this->serviceName]);
        $serviceId = $stmt->fetch(PDO::FETCH_COLUMN);

        // register service configuration
        $stmtConfigs = $this->getPdo()->prepare('INSERT IGNORE INTO `'._DB_PREFIX_.serviceConfigurationModel::$definition['table'].'` (`id_service`,`name`,`value`,`type`,`created_at`,`updated_at`) VALUES(:id_service,:name,:value,:type,:created_at,:updated_at) ');

        foreach ($this->configDefinition as $name => $data)
        {
            $dateTime = date('Y-m-d H:i:s');
            $stmtConfigs->bindParam(':id_service', $serviceId, PDO::PARAM_INT);
            $stmtConfigs->bindParam(':name', $name);
            $stmtConfigs->bindParam(':value', $data['value']);
            $stmtConfigs->bindParam(':type', $data['type']);
            $stmtConfigs->bindParam(':created_at', $dateTime);
            $stmtConfigs->bindParam(':updated_at', $dateTime);
            $stmtConfigs->bindParam(':created_at', $dateTime);
            $stmtConfigs->execute();
        }

        if ($this->getPdo()->inTransaction())
        {
            $this->getPdo()->commit();
        }
        $this->logger->setFilesToKeep($this->getConfigValue('logFilesToKeep'));
    }

    public function getConfig($refresh = false)
    {
        if (!$this->config || $refresh)
        {
            $stmtService = $this->getPdo()->prepare('SELECT config.* FROM `'._DB_PREFIX_.serviceModel::$definition['table'].'` service INNER JOIN `'._DB_PREFIX_.serviceConfigurationModel::$definition['table'].'` config ON config.id_service = service.id_service AND service.name = :service_name ');
            $stmtService->execute([':service_name' => $this->serviceName]);
            $results = $stmtService->fetchAll(PDO::FETCH_ASSOC);
            $this->config = array_column((array) $results, null, 'name');
            foreach ($this->config as $key => $config)
            {
                if (isset($config['type']) && $config['type'] === 'password')
                {
                    $this->config[$key]['value'] = SCI::decrypt($this->config[$key]['value']);
                }
            }
        }

        return $this->config;
    }

    /**
     * @param array $params
     *
     * @return $this|mixed
     *
     * @throws Exception
     */
    public function setConfig($params)
    {
        try
        {
            $this->getPdo()->beginTransaction();
            $stmt = $this->getPdo()->prepare('SELECT id_service FROM `'._DB_PREFIX_.serviceModel::$definition['table'].'` WHERE name = :name');
            $stmt->execute([':name' => $this->serviceName]);
            $serviceId = $stmt->fetchColumn();
            $serviceConfigTableName = _DB_PREFIX_.serviceConfigurationModel::$definition['table'];

            $sql = <<<SQL
    INSERT INTO `{$serviceConfigTableName}` (id_service, name, value, created_at, updated_at) VALUES (:id_service, :name, :value,:created_at,:updated_at)
    ON DUPLICATE KEY UPDATE
                `value` = :value,
                `updated_at` = :updated_at
SQL;
            $stmtService = $this->getPdo()->prepare($sql);
            $stmtService->bindValue(':id_service', $serviceId);
            foreach ($params as $key => $value)
            {
                $stmtService->bindValue(':name', $key);
                $stmtService->bindValue(':value', $value);
                $stmtService->bindValue(':created_at', date('Y-m-d H:i:s'));
                $stmtService->bindValue(':updated_at', date('Y-m-d H:i:s'));
                $stmtService->execute();
            }
            if ($this->getPdo()->inTransaction())
            {
                $this->getPdo()->commit();
            }
            $this->getConfig(true);

            return $this;
        }
        catch (Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function isActive()
    {
        $isActive = $this->getPdo()->query('SELECT active FROM `'._DB_PREFIX_.serviceModel::$definition['table'].'`', PDO::FETCH_UNIQUE);

        return (bool) $isActive->fetch(PDO::FETCH_COLUMN);
    }

    public function checkConfig($paramName = false)
    {
        $config = $this->getConfig(true);

        if (empty($config))
        {
            throw new Exception('Unable to get config from database');
        }
        if ($paramName)
        {
            $paramValue = $config[$paramName]['value'];

            return isset($paramValue) ? $paramValue : false;
        }
        else
        {
            foreach ($this->getConfigDefinition(true) as $key => $requiredParam)
            {
                if (!isset($config[$key]) or empty($config[$key]['value']))
                {
                    throw new Exception('Required field '.$key.' is invalid');
                }
            }
        }

        return true;
    }

    /**
     * @return array|string[]
     */
    public function getConfigDefinition($onlyRequired = false)
    {
        $configParams = $this->configDefinition;
        if ($onlyRequired)
        {
            $requiredConfigParams = $configParams;
            foreach ($requiredConfigParams as $key => $requiredConfigParam)
            {
                if (!(bool) $requiredConfigParam['required'])
                {
                    unset($requiredConfigParams[$key]);
                }
            }
            $configParams = $requiredConfigParams;
        }

        return $configParams;
    }

    /**
     * @param array|string[] $configDefinition
     */
    public function setConfigDefinition($configDefinition)
    {
        $this->configDefinition = $configDefinition;

        return $this;
    }

    /**
     * @return $this
     */
    public function addError(Exception $exception)
    {
        $this->errors[] = $exception->getMessage();
        $this->getLogger()->error($exception->getMessage());

        return $this;
    }

    /**
     * @desc : send response as json
     *
     * @param string   $successMessage
     * @param array    $extra
     * @param string[] $headers
     *
     * @return false|int|string
     */
    public function sendResponse($successMessage = 'success', $extra = [], $headers = ['Content-Type' => 'application/json; charset=utf-8'])
    {
        foreach ($headers as $key => $value)
        {
            header($key.': '.$value);
        }
        $response = $this->getResponse($successMessage, $extra);

        echo json_encode($response);
        exit;
    }

    public function getResponse($successMessage, $extra)
    {
        $response = ['state' => true, 'extra' => ['code' => 200, 'message' => $successMessage]];
        if (!empty($this->errors))
        {
            $response['state'] = false;
            $response['extra']['code'] = 103;
            $response['extra']['message'] = '<ul style="padding-left:10px;"><li>'.implode('</li><li>', $this->errors).'</li></ul>';
        }
        $response['extra'] = array_merge($response['extra'], $extra);

        return $response;
    }

    /**
     * @return false
     */
    public function getConfigValue($key)
    {
        $config = $this->getConfig(true);

        return isset($config[$key]) ? $config[$key]['value'] : false;
    }

    public function getConfigShopsForPdo($asArray = false)
    {
        $importToShop = $this->getConfigValue('importToShop');
        if (!isset($importToShop) or $importToShop === '')
        {
            throw new Exception('no shop configured for '.$this->serviceName);
        }
        if ($asArray)
        {
            return explode(',', $importToShop);
        }

        return $importToShop;
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        if (!isTable(serviceModel::$definition['table']))
        {
            return false;
        }
        $stmt = $this->getPdo()->prepare('SELECT * FROM  `'._DB_PREFIX_.serviceModel::$definition['table'].'` sc_service WHERE sc_service.name = :service_name');
        $stmt->execute([':service_name' => $this->serviceName]);

        return $stmt->rowCount() > 0;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param ScLogger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getScAgent()
    {
        if (!$this->sc_agent)
        {
            $this->sc_agent = \SC_Agent::getInstance();
        }

        return $this->sc_agent;
    }

    /**
     * @return PDO|resource|null
     */
    public function getPdo()
    {
        if (!$this->pdo)
        {
            $this->pdo = Db::getInstance()->getLink();
        }

        return $this->pdo;
    }

    /**
     * @param PDO|resource|null $pdo
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }
}
