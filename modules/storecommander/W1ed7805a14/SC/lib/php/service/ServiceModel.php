<?php

namespace Sc\Service;

class ServiceModel
{
    const TYPE_INT = 1;
    const TYPE_BOOL = 2;
    const TYPE_STRING = 3;
    const TYPE_FLOAT = 4;
    const TYPE_DATE = 5;
    const TYPE_HTML = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL = 8;

    /**
     * @var array
     */
    public static $definition = [
        'table' => SC_DB_PREFIX.'service',
        'primary' => 'id_service',
        'fields' => [
            'id' => ['type' => self::TYPE_INT, 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'active' => ['type' => self::TYPE_INT, 'required' => true, 'default' => 0],
            'created_at' => ['type' => self::TYPE_DATE, 'required' => true, 'size' => 11],
        ],
    ];

    /**
     * @return void
     */
    public static function createTablesIfNeeded()
    {
        $pdo = \Db::getInstance()->getLink();
        $pdo->query('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'` (
                      `id_service` int(11) NOT NULL AUTO_INCREMENT,
                      `name` VARCHAR(255) NOT NULL,
                      `active` int(1) NOT NULL,
                      `created_at` datetime NOT NULL COMMENT "service creation date",
                      PRIMARY KEY (`id_service`),
                      UNIQUE KEY `name` (`name`)
                    ) ENGINE=InnoDb DEFAULT CHARACTER SET utf8 COLLATE=utf8_general_ci;');
    }
}
