<?php

namespace Sc\Service\Shippingbo\Model;

use Sc\Service\ServiceModel;

/**
 * Table de relation entre les données de la boutique et les données Shippingbo.
 */
class ShopRelation
{
    public static $definition = [
        'table' => SC_DB_PREFIX.'service_shippingbo_shop_relation',
        'primary' => 'id_'.SC_DB_PREFIX.'service_shippingbo_shop_relation',
        'fields' => [
            'id_sbo' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'id_product' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'id_product_attribute' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'id_sbo_source' => ['type' => ServiceModel::TYPE_INT, 'required' => false],
            'type_sbo' => ['type' => ServiceModel::TYPE_STRING, 'required' => true],
            'is_locked' => ['type' => ServiceModel::TYPE_INT, 'required' => true],
            'created_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
            'updated_at' => ['type' => ServiceModel::TYPE_DATE, 'required' => true],
        ],
    ];

    public static function createTablesIfNeeded()
    {
        $pdo = \Db::getInstance()->getLink();
        $pdo->query(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::$definition['table'].'` (
                      `'.self::$definition['primary'].'` int(11) NOT NULL AUTO_INCREMENT,
                      `id_sbo` int(11) NULL DEFAULT NULL,
                      `id_product` int(11) NULL DEFAULT NULL,
                      `id_product_attribute` int(11) NULL DEFAULT NULL,
                      `id_sbo_source` int(11) NULL DEFAULT NULL COMMENT "id_sbo produit source pour les lots et references additionnelles",
                      `type_sbo` VARCHAR(255) NOT NULL,
                      `is_locked` int(1) NULL DEFAULT 1,
                      `created_at` datetime NOT NULL COMMENT "UTC",
                      `updated_at` datetime NOT NULL COMMENT "UTC",
                      PRIMARY KEY (`'.self::$definition['primary'].'`),
                      UNIQUE KEY `ps_product` (`id_product`,`id_product_attribute`),
                      UNIQUE KEY `sbo_product` (`id_sbo`),
                      INDEX `type_sbo` (`type_sbo`) USING BTREE,
                      INDEX `is_locked` (`is_locked`) USING BTREE
                    ) ENGINE=InnoDb DEFAULT CHARACTER SET utf8 COLLATE=utf8_general_ci;'
        );
    }
}
