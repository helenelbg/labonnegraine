<?php

namespace Sc\Service\Shippingbo\Model;

use Sc\Service\ServiceModel;

class Product
{
    public static $definition = [
        'table' => SC_DB_PREFIX.'service_shippingbo_product_buffer',
        'primary' => 'id_'.SC_DB_PREFIX.'service_shippingbo_product_buffer',
        'fields' => [
            'id' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'is_pack' => ['type' => ServiceModel::TYPE_INT],
            'user_ref' => ['type' => ServiceModel::TYPE_STRING],
            'title' => ['type' => ServiceModel::TYPE_STRING],
            'weight' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'height' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'length' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'width' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'stock' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
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
                      `user_ref` VARCHAR(255) NOT NULL,
                      `is_pack` INT(1) NOT NULL,
                      `title` VARCHAR(255) NULL DEFAULT NULL,
                      `weight` INT(11) DEFAULT NULL,
                      `height` INT(11) DEFAULT NULL,
                      `length` INT(11) DEFAULT NULL,
                      `width` INT(11) DEFAULT NULL,
                      `stock` INT(11) DEFAULT NULL,
                      `updated_at` datetime NOT NULL COMMENT "UTC",
                      `synced_at` datetime NOT NULL COMMENT "UTC date row insertion",
                    PRIMARY KEY (`'.self::$definition['primary'].'`),
                    UNIQUE INDEX `id` (`id`),
                    INDEX `updated_at` (`updated_at`) USING BTREE,
                    INDEX `synced_at` (`synced_at`) USING BTREE,
                    INDEX `is_pack` (`is_pack`) USING BTREE
                    ) ENGINE=InnoDb DEFAULT CHARACTER SET utf8 COLLATE=utf8_general_ci;'
        );
    }
}
