<?php

namespace Sc\Service\Shippingbo\Model;

use Sc\Service\ServiceModel;

class PackComponent
{
    public static $definition = [
        'table' => SC_DB_PREFIX.'service_shippingbo_pack_component_buffer',
        'primary' => 'id_'.SC_DB_PREFIX.'service_shippingbo_pack_component_buffer',
        'fields' => [
            'id' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'quantity' => ['type' => ServiceModel::TYPE_STRING],
            'pack_product_id' => ['type' => ServiceModel::TYPE_INT],
            'component_product_id' => ['type' => ServiceModel::TYPE_INT],
            'created_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
            'updated_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
            'synced_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
        ],
    ];

    public static function createTablesIfNeeded()
    {
        $pdo = \Db::getInstance()->getLink();
        $pdo->query(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'` (
                      `'.self::$definition['primary'].'` int(11) NOT NULL AUTO_INCREMENT,
                      `id` int(11) NOT NULL,
                      `quantity` VARCHAR(255) NOT NULL,
                      `pack_product_id` VARCHAR(255) NOT NULL,
                      `component_product_id` VARCHAR(255) NOT NULL,
                      `created_at` datetime NOT NULL COMMENT "UTC",
                      `updated_at` datetime NOT NULL COMMENT "UTC",
                      `synced_at` datetime NOT NULL COMMENT "UTC date row insertion",
                      PRIMARY KEY (`'.self::$definition['primary'].'`),
                      UNIQUE KEY `pack_product_id` (`pack_product_id`,`component_product_id`),
                      INDEX `id` (`id`) USING BTREE,
                      INDEX `component_product_id` (`component_product_id`) USING BTREE,
                      INDEX `quantity` (`quantity`) USING BTREE,
                      INDEX `synced_at` (`synced_at`) USING BTREE
                    ) ENGINE=InnoDb DEFAULT CHARACTER SET utf8 COLLATE=utf8_general_ci;'
        );
    }
}
