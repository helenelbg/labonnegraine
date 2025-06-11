<?php

namespace Sc\Service\Shippingbo\Model;

use Sc\Service\ServiceModel;

class AdditionalRefs
{
    public static $definition = [
        'table' => SC_DB_PREFIX.'service_shippingbo_additional_refs_buffer',
        'primary' => 'id_'.SC_DB_PREFIX.'service_shippingbo_additional_refs_buffer',
        'fields' => [
            'id' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'created_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
            'updated_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
            'order_item_field' => ['type' => ServiceModel::TYPE_STRING],
            'product_field' => ['type' => ServiceModel::TYPE_STRING],
            'order_item_value' => ['type' => ServiceModel::TYPE_STRING],
            'product_value' => ['type' => ServiceModel::TYPE_STRING],
            'matched_quantity' => ['type' => ServiceModel::TYPE_STRING],
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
                      `order_item_field` VARCHAR(255) NOT NULL,
                      `product_field` VARCHAR(255) NOT NULL,
                      `order_item_value` VARCHAR(255) NOT NULL,
                      `product_value` VARCHAR(255) NOT NULL,
                      `matched_quantity` VARCHAR(255) NOT NULL,
                      `created_at` datetime NOT NULL COMMENT "UTC",
                      `updated_at` datetime NOT NULL COMMENT "UTC",
                      `synced_at` datetime NOT NULL COMMENT "UTC date row insertion",
                      PRIMARY KEY (`'.self::$definition['primary'].'`),
                      UNIQUE KEY `order_item_value` (`order_item_value`),
                      INDEX `id` (`id`) USING BTREE,
                      INDEX `product_value` (`product_value`) USING BTREE,
                      INDEX `matched_quantity` (`matched_quantity`) USING BTREE,
                      INDEX `synced_at` (`synced_at`) USING BTREE
                    ) ENGINE=InnoDb DEFAULT CHARACTER SET utf8 COLLATE=utf8_general_ci;'
        );
    }
}
