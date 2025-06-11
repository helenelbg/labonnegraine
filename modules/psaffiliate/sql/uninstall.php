<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

/**
 * In some cases you should not drop the tables.
 * Maybe the merchant will just try to reset the module
 * but does not want to loose all of the data associated to the module.
 */
$tables_to_drop = array(
    "affiliates",
    "affiliates_meta",
    "banners",
    "campaigns",
    "commission",
    "configuration",
    "configuration_lang",
    "custom_fields",
    "custom_fields_lang",
    "payments",
    "payment_methods",
    "payment_methods_fileds",
    "sales",
    "texts",
    "tracking",
    "category_rates",
    "product_rates",
    "cart",
);
$sql = array();
foreach ($tables_to_drop as $table) {
    $sql[] = "DROP TABLE IF EXISTS `"._DB_PREFIX_."aff_".$table."`";
}
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
