<?php

namespace Sc\Service;

use Db;

/**
 * Store Commande usage management
 */
class Usage
{
    protected $tableName = 'storecom_usage';
    protected $allowedInterfaces = ['`all`', '`cat`', '`cms`', '`core`', '`cus`', '`cusm`', '`man`', '`ord`', '`ser`', '`sup`', '`ork`'];
    protected $currentDbUsages = [];

    public function __construct()
    {
        $this->currentDbUsages = $this->getCurrentDbUsages();
    }

    /**
     * @param $snapshot
     * @return void
     */
    public function save($snapshot = [])
    {
        if(empty($snapshot))
        {
            return;
        }

        $this->setMultipleUsages($snapshot);

        foreach ($this->currentDbUsages as $interface => $data)
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'storecom_usage SET `'.bqSQL($interface).'`='.(empty($data) ? 'NULL' : '"'.pSQL(json_encode($data)).'"').' WHERE id_storecom_usage = 1');
        }
    }

    public function getForApi()
    {
        ## coalesce(....) IS NOT NULL get data only if one of the allowedInterface column is not null
        $data = Db::getInstance()->getRow('SELECT * 
                                                    FROM `'._DB_PREFIX_.$this->tableName.'` 
                                                    WHERE id_storecom_usage = 1 
                                                    AND coalesce('.pSQL(implode(', ', $this->allowedInterfaces)).') IS NOT NULL');
        if ($data)
        {
             unset($data['id_storecom_usage']);
        }

        return $data;
    }

    /**
     * Reset usage into database
     * @return void
     */
    public function reset()
    {
        $todo = implode('=NULL, ', $this->allowedInterfaces).'=NULL';
        $sql = 'UPDATE `'._DB_PREFIX_.$this->tableName.'` SET '.pSQL($todo).' WHERE id_storecom_usage = 1';
        Db::getInstance()->execute($sql);
    }


    /**
     * Return usages already in database
     * @return array
     */
    protected function getCurrentDbUsages()
    {
        $interface_data = Db::getInstance()->getRow('SELECT '.pSQL(implode(',',$this->allowedInterfaces)).' 
                                                            FROM '._DB_PREFIX_.'storecom_usage 
                                                            WHERE id_storecom_usage = 1');
        if(!$interface_data)
        {
            return [];
        }

        $usagesInDb = [];
        foreach ($interface_data as $interface => $data)
        {
            $usagesInDb[$interface] = json_decode($data, true);
            if ($usagesInDb[$interface] === null)
            {
                $usagesInDb[$interface] = [];
            }
        }

        return $usagesInDb;
    }

    /**
     * @param $data
     * @return void
     */
    protected function setMultipleUsages($data)
    {
        foreach ($data as $interface => $actions)
        {
            foreach ($actions as $action => $value)
            {
                if (array_key_exists($action, $this->currentDbUsages[$interface]))
                {
                    $this->currentDbUsages[$interface][$action] += $value;
                }
                else
                {
                    $this->currentDbUsages[$interface][$action] = $value;
                }
            }
        }
    }
}