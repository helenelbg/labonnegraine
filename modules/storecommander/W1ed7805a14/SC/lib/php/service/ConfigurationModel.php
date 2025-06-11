<?php

namespace Sc\Service;

class ConfigurationModel
{
    /**
     * @var array
     */
    public static $definition = [
        'table' => SC_DB_PREFIX.'service_configuration',
        'primary' => 'id_service_configuration',
        'fields' => [
            'id_service' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'name' => ['type' => ServiceModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'value' => ['type' => ServiceModel::TYPE_STRING, 'validate' => 'isGenericName'],
            'type' => ['type' => ServiceModel::TYPE_STRING, 'validate' => 'isGenericName'],
            'created_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true, 'size' => 11],
            'updated_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true, 'size' => 11],
        ],
    ];

    /**
     * @return void
     */
    public static function createTablesIfNeeded()
    {
        $pdo = \Db::getInstance()->getLink();
        $pdo->query(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'` (
                  `id_service_configuration` int(11) NOT NULL AUTO_INCREMENT,
                  `id_service` int(11) NOT NULL,
                  `name` VARCHAR(255) NOT NULL,
                  `value` VARCHAR(255) NULL,
                  `type` VARCHAR(255) NULL DEFAULT "standard",
                  `created_at` datetime NOT NULL COMMENT "configuration creation date",
                  `updated_at` datetime NOT NULL COMMENT "configuration update date",
                  PRIMARY KEY (`id_service_configuration`),
                  UNIQUE KEY `name` (`id_service`,`name`)
                ) ENGINE=InnoDb DEFAULT CHARACTER SET utf8 COLLATE=utf8_general_ci;');
    }
}
