<?php

class EntityManager_9a5be93 extends \Doctrine\ORM\EntityManager implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager|null wrapped object, if the proxy is initialized
     */
    private $valueHolder5adec = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer883ba = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties2ae93 = [
        
    ];

    public function getConnection()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getConnection', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getConnection();
    }

    public function getMetadataFactory()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getMetadataFactory', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getMetadataFactory();
    }

    public function getExpressionBuilder()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getExpressionBuilder', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getExpressionBuilder();
    }

    public function beginTransaction()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'beginTransaction', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->beginTransaction();
    }

    public function getCache()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getCache', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getCache();
    }

    public function transactional($func)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'transactional', array('func' => $func), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->transactional($func);
    }

    public function wrapInTransaction(callable $func)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'wrapInTransaction', array('func' => $func), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->wrapInTransaction($func);
    }

    public function commit()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'commit', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->commit();
    }

    public function rollback()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'rollback', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->rollback();
    }

    public function getClassMetadata($className)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getClassMetadata', array('className' => $className), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getClassMetadata($className);
    }

    public function createQuery($dql = '')
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'createQuery', array('dql' => $dql), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->createQuery($dql);
    }

    public function createNamedQuery($name)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'createNamedQuery', array('name' => $name), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->createNamedQuery($name);
    }

    public function createNativeQuery($sql, \Doctrine\ORM\Query\ResultSetMapping $rsm)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'createNativeQuery', array('sql' => $sql, 'rsm' => $rsm), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'createNamedNativeQuery', array('name' => $name), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->createNamedNativeQuery($name);
    }

    public function createQueryBuilder()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'createQueryBuilder', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->createQueryBuilder();
    }

    public function flush($entity = null)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'flush', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->flush($entity);
    }

    public function find($className, $id, $lockMode = null, $lockVersion = null)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'find', array('className' => $className, 'id' => $id, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->find($className, $id, $lockMode, $lockVersion);
    }

    public function getReference($entityName, $id)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getReference', array('entityName' => $entityName, 'id' => $id), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getPartialReference', array('entityName' => $entityName, 'identifier' => $identifier), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getPartialReference($entityName, $identifier);
    }

    public function clear($entityName = null)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'clear', array('entityName' => $entityName), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->clear($entityName);
    }

    public function close()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'close', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->close();
    }

    public function persist($entity)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'persist', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->persist($entity);
    }

    public function remove($entity)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'remove', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->remove($entity);
    }

    public function refresh($entity)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'refresh', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->refresh($entity);
    }

    public function detach($entity)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'detach', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->detach($entity);
    }

    public function merge($entity)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'merge', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->merge($entity);
    }

    public function copy($entity, $deep = false)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'copy', array('entity' => $entity, 'deep' => $deep), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'lock', array('entity' => $entity, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->lock($entity, $lockMode, $lockVersion);
    }

    public function getRepository($entityName)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getRepository', array('entityName' => $entityName), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getRepository($entityName);
    }

    public function contains($entity)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'contains', array('entity' => $entity), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->contains($entity);
    }

    public function getEventManager()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getEventManager', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getEventManager();
    }

    public function getConfiguration()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getConfiguration', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getConfiguration();
    }

    public function isOpen()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'isOpen', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->isOpen();
    }

    public function getUnitOfWork()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getUnitOfWork', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getHydrator', array('hydrationMode' => $hydrationMode), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'newHydrator', array('hydrationMode' => $hydrationMode), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->newHydrator($hydrationMode);
    }

    public function getProxyFactory()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getProxyFactory', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getProxyFactory();
    }

    public function initializeObject($obj)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'initializeObject', array('obj' => $obj), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->initializeObject($obj);
    }

    public function getFilters()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'getFilters', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->getFilters();
    }

    public function isFiltersStateClean()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'isFiltersStateClean', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->isFiltersStateClean();
    }

    public function hasFilters()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'hasFilters', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return $this->valueHolder5adec->hasFilters();
    }

    /**
     * Constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;

        $reflection = $reflection ?? new \ReflectionClass(__CLASS__);
        $instance   = $reflection->newInstanceWithoutConstructor();

        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $instance, 'Doctrine\\ORM\\EntityManager')->__invoke($instance);

        $instance->initializer883ba = $initializer;

        return $instance;
    }

    protected function __construct(\Doctrine\DBAL\Connection $conn, \Doctrine\ORM\Configuration $config, \Doctrine\Common\EventManager $eventManager)
    {
        static $reflection;

        if (! $this->valueHolder5adec) {
            $reflection = $reflection ?? new \ReflectionClass('Doctrine\\ORM\\EntityManager');
            $this->valueHolder5adec = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $this, 'Doctrine\\ORM\\EntityManager')->__invoke($this);

        }

        $this->valueHolder5adec->__construct($conn, $config, $eventManager);
    }

    public function & __get($name)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, '__get', ['name' => $name], $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        if (isset(self::$publicProperties2ae93[$name])) {
            return $this->valueHolder5adec->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder5adec;

            $backtrace = debug_backtrace(false, 1);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $realInstanceReflection->getName(),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder5adec;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __set($name, $value)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, '__set', array('name' => $name, 'value' => $value), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder5adec;

            $targetObject->$name = $value;

            return $targetObject->$name;
        }

        $targetObject = $this->valueHolder5adec;
        $accessor = function & () use ($targetObject, $name, $value) {
            $targetObject->$name = $value;

            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __isset($name)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, '__isset', array('name' => $name), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder5adec;

            return isset($targetObject->$name);
        }

        $targetObject = $this->valueHolder5adec;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __unset($name)
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, '__unset', array('name' => $name), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolder5adec;

            unset($targetObject->$name);

            return;
        }

        $targetObject = $this->valueHolder5adec;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);

            return;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $accessor();
    }

    public function __clone()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, '__clone', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        $this->valueHolder5adec = clone $this->valueHolder5adec;
    }

    public function __sleep()
    {
        $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, '__sleep', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;

        return array('valueHolder5adec');
    }

    public function __wakeup()
    {
        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $this, 'Doctrine\\ORM\\EntityManager')->__invoke($this);
    }

    public function setProxyInitializer(\Closure $initializer = null) : void
    {
        $this->initializer883ba = $initializer;
    }

    public function getProxyInitializer() : ?\Closure
    {
        return $this->initializer883ba;
    }

    public function initializeProxy() : bool
    {
        return $this->initializer883ba && ($this->initializer883ba->__invoke($valueHolder5adec, $this, 'initializeProxy', array(), $this->initializer883ba) || 1) && $this->valueHolder5adec = $valueHolder5adec;
    }

    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolder5adec;
    }

    public function getWrappedValueHolderValue()
    {
        return $this->valueHolder5adec;
    }
}
