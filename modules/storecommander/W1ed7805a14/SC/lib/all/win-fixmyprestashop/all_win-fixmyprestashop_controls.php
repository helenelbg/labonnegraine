<?php

if (empty($controls))
{
    $controls = array();
}

$shop_tables = array(
    array('table' => 'attribute_group', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'attribute', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'carrier', 'vs_min' => '', 'vs_max' => ''),

    array('table' => 'category', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'cms_block', 'vs_min' => '', 'vs_max' => '1.6.1.0'),
    array('table' => 'cms', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'contact', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'country', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'currency', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'employee', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'feature', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'group', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'image', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'lang', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'manufacturer', 'vs_min' => '', 'vs_max' => ''),

    array('table' => 'referrer', 'vs_min' => '', 'vs_max' => '1.7.9.9'),
    array('table' => 'scene', 'vs_min' => '', 'vs_max' => '1.7.0.0'),
    array('table' => 'store', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'supplier', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'tax_rules_group', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'warehouse', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'webservice_account', 'vs_min' => '', 'vs_max' => ''),
    array('table' => 'zone', 'vs_min' => '', 'vs_max' => ''),
);

/*
 * Déclaration du contrôle
 * dans le tableau PHP des
 * contrôles
 */

if (file_exists(SC_TOOLS_DIR.basename(dirname(__FILE__)).'/controls.php'))
{
    require_once SC_TOOLS_DIR.basename(dirname(__FILE__)).'/controls.php';
}

$controls['CAT_PROD_MISSING_PRODUCT_LANG'] = array(
    'key' => 'CAT_PROD_MISSING_PRODUCT_LANG',
    'version_min' => '',
    'version_max' => '1.4',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Missing product information in ps_product_lang',
    'description' => 'List of id_product in ps_product but not in ps_product_lang',
);

$controls['TRP_CAR_VALID_REF'] = array(
    'key' => 'TRP_CAR_VALID_REF',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Transport',
    'section' => 'Carrier',
    'name' => 'Carrier with bad id_reference',
    'description' => 'List of carriers with bad id_reference or not valid',
);

$controls['CAT_PROD_MISSING_PRODUCT_LANG_MS'] = array(
    'key' => 'CAT_PROD_MISSING_PRODUCT_LANG_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Missing product information in ps_product_lang',
    'description' => 'List of id_product in ps_product_shop but not in ps_product_lang',

    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PROD_MISSING_PRODUCT_INFORMATION'] = array(
    'key' => 'CAT_PROD_MISSING_PRODUCT_INFORMATION',
    'version_min' => '',
    'version_max' => '1.4',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Missing product text information in ps_product',
    'description' => 'List of id_product in ps_product_lang but not in ps_product',
);

$controls['CAT_PROD_MISSING_PRODUCT_INFORMATION_MS'] = array(
    'key' => 'CAT_PROD_MISSING_PRODUCT_INFORMATION_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Missing product text information in ps_product_shop',
    'description' => 'List of id_product in ps_product_lang but not in ps_product_shop',
);

$controls['CAT_PROD_GHOST_CATEGORY_PRODUCT'] = array(
    'key' => 'CAT_PROD_GHOST_CATEGORY_PRODUCT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Ghost product in categories',
    'description' => 'List of deleted products still present in ps_category_product',
);

$controls['CAT_PROD_GHOST_PRODUCT_ATTRIBUTE'] = array(
    'key' => 'CAT_PROD_GHOST_PRODUCT_ATTRIBUTE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Ghost product in attributes',
    'description' => 'List of deleted products still present in ps_product_attribute',
);

$controls['CAT_PROD_GHOST_STOCK'] = array(
    'key' => 'CAT_PROD_GHOST_STOCK',
    'version_min' => '1.5',
    'version_max' => '1.7',
    'tools' => 'Catalog',
    'section' => 'Stock',
    'name' => 'Ghost product in stock',
    'description' => 'List of deleted products still present in ps_stock',
);

$controls['CAT_COMBI_GHOST_STOCK'] = array(
    'key' => 'CAT_COMBI_GHOST_STOCK',
    'version_min' => '1.5',
    'version_max' => '1.7',
    'tools' => 'Catalog',
    'section' => 'Stock',
    'name' => 'Ghost combination in stock',
    'description' => 'List of deleted combinations still present in ps_stock',
);

$controls['CAT_PROD_WITHOUT_CATEGORY'] = array(
    'key' => 'CAT_PROD_WITHOUT_CATEGORY',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products without category',
    'description' => 'List of products without category (not present in ps_category_product)',

    'segment_params' => array(
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PROD_WITHOUT_DEFAULT_CATEGORY'] = array(
    'key' => 'CAT_PROD_WITHOUT_DEFAULT_CATEGORY',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products without default category',
    'description' => 'List of products with id_category_default = 0 or null',

    'segment_params' => array(
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE'] = array(
    'key' => 'CAT_PROD_PRODUCT_WITHOUT_LINK_REWRITE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products without link rewrite',
    'description' => 'List of products without link rewrite',

    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_ATTR_MISSING_ATTRIBUTE_LANG'] = array(
    'key' => 'CAT_ATTR_MISSING_ATTRIBUTE_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Attributes',
    'name' => 'Missing translations in ps_attribute_lang',
    'description' => 'List of id_attribute in ps_attribute but not in ps_attribute_lang',
);

$controls['CAT_ATTR_GHOST_ATTRIBUTE'] = array(
    'key' => 'CAT_ATTR_GHOST_ATTRIBUTE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Attributes',
    'name' => 'Missing attribute row in ps_attribute',
    'description' => 'List of attribute in ps_attribute_lang but not in ps_attribute',
);

$controls['CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG'] = array(
    'key' => 'CAT_ATTR_MISSING_GROUP_ATTRIBUTE_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Attributes',
    'name' => 'Missing translations in ps_attribute_group_lang',
    'description' => 'List of id_attribute_group in ps_attribute_group but not in ps_attribute_group_lang',
);

$controls['CAT_ATTR_GHOST_GROUP_ATTRIBUTE'] = array(
    'key' => 'CAT_ATTR_GHOST_GROUP_ATTRIBUTE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Attributes',
    'name' => 'Missing attribute group row in ps_attribute_group',
    'description' => 'List of attribute group in ps_attribute_group_lang but not in ps_attribute_group',
);

$controls['TRP_CAR_MISSING_CARRIER_LANG'] = array(
    'key' => 'TRP_CAR_MISSING_CARRIER_LANG',
    'version_min' => '',
    'version_max' => '1.4',
    'tools' => 'Transport',
    'section' => 'Carrier',
    'name' => 'Missing translations in ps_carrier_lang',
    'description' => 'List of id_carrier in ps_carrier but not in ps_carrier_lang',
);

$controls['TRP_CAR_MISSING_CARRIER_LANG_MS'] = array(
    'key' => 'TRP_CAR_MISSING_CARRIER_LANG_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Transport',
    'section' => 'Carrier',
    'name' => 'Missing translations in ps_carrier_lang',
    'description' => 'List of id_carrier in ps_carrier_shop but not in ps_carrier_lang',
);

$controls['TRP_CAR_GHOST_CARRIER'] = array(
    'key' => 'TRP_CAR_GHOST_CARRIER',
    'version_min' => '',
    'version_max' => '1.4',
    'tools' => 'Transport',
    'section' => 'Carrier',
    'name' => 'Missing carrier row in ps_carrier',
    'description' => 'List of carrier in ps_carrier_lang but not in ps_carrier',
);

$controls['TRP_CAR_GHOST_CARRIER_MS'] = array(
    'key' => 'TRP_CAR_GHOST_CARRIER_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Transport',
    'section' => 'Carrier',
    'name' => 'Missing carrier row in ps_carrier_shop',
    'description' => 'List of carrier in ps_carrier_lang but not in ps_carrier_shop',
);

$controls['CAT_CAT_MISSING_CAT_LANG'] = array(
    'key' => 'CAT_CAT_MISSING_CAT_LANG',
    'version_min' => '',
    'version_max' => '1.4',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Missing translations in ps_category_lang',
    'description' => 'List of id_category in ps_category but not in ps_category_lang',
);

$controls['CAT_CAT_MISSING_CAT_LANG_MS'] = array(
    'key' => 'CAT_CAT_MISSING_CAT_LANG_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Missing translations in ps_category_lang',
    'description' => 'List of id_category in ps_category_shop but not in ps_category_lang',
);

$controls['CAT_CAT_GHOST_CAT'] = array(
    'key' => 'CAT_CAT_GHOST_CAT',
    'version_min' => '',
    'version_max' => '1.4',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Missing category row in ps_category',
    'description' => 'List of category in ps_category_lang but not in ps_category',
);

$controls['CAT_CAT_GHOST_CAT_MS'] = array(
    'key' => 'CAT_CAT_GHOST_CAT_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Missing category row in ps_category_shop',
    'description' => 'List of category in ps_category_lang but not in ps_category_shop',
);

$controls['CAT_CAT_GHOST_CAT_PROD'] = array(
    'key' => 'CAT_CAT_GHOST_CAT_PROD',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Ghost categories in ps_category_product',
    'description' => 'List of deleted categories still present in ps_category_product',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_ATTR_ASSOCIATION_SHOP'] = array(
    'key' => 'CAT_ATTR_ASSOCIATION_SHOP',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'Combinations not associated to their default shop',
    'description' => 'List of combinations with no association in ps_product_attribute_shop for their default shop',
);

$controls['CAT_CAT_GHOST_CAT_PROD_PROD_DUP'] = array(
    'key' => 'CAT_CAT_GHOST_CAT_PROD_PROD_DUP',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Duplicate products in ps_category_product',
    'description' => 'List of duplicate products present in ps_category_product',
);

$controls['CAT_CAT_GHOST_PARENT'] = array(
    'key' => 'CAT_CAT_GHOST_PARENT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Ghost category parents in ps_category',
    'description' => 'List of deleted categories still present in id_parent in ps_category',
);

$controls['TRP_CTY_MISSING_COUNTRY_LANG'] = array(
    'key' => 'TRP_CTY_MISSING_COUNTRY_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Transport',
    'section' => 'Country',
    'name' => 'Missing translations in ps_country_lang',
    'description' => 'List of id_country in ps_country but not in ps_country_lang',
);

$controls['CAT_PRODUCT_ASSOCIATION_SHOP'] = array(
    'key' => 'CAT_PRODUCT_ASSOCIATION_SHOP',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products without default shop association',
    'description' => 'List of products where id_shop_default not in ps_product_shop',
);

$controls['TRP_CTY_GHOST_COUNTRY'] = array(
    'key' => 'TRP_CTY_GHOST_COUNTRY',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Transport',
    'section' => 'Country',
    'name' => 'Missing country row in ps_country',
    'description' => 'List of country in ps_country_lang but not in ps_country',
);

$controls['CAT_FEA_MISSING_FEATURE_LANG'] = array(
    'key' => 'CAT_FEA_MISSING_FEATURE_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Missing translations in ps_feature_lang',
    'description' => 'List of id_feature in ps_feature but not in ps_feature_lang',
);

$controls['CAT_FEA_GHOST_FEATURE'] = array(
    'key' => 'CAT_FEA_GHOST_FEATURE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Missing feature row in ps_feature',
    'description' => 'List of feature in ps_feature_lang but not in ps_feature',
);

$controls['CAT_FEA_GHOST_CAT_PROD'] = array(
    'key' => 'CAT_FEA_GHOST_CAT_PROD',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Ghost features in ps_feature_product',
    'description' => 'List of deleted features still present in ps_feature_product',
);

$controls['CAT_PRODUCT_IMG_POSITIONS'] = array(
    'key' => 'CAT_PRODUCT_IMG_POSITIONS',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Check product image position',
    'description' => 'List of rows in ps_image with position error',
);

$controls['CAT_FEA_MISSING_FEATURE_VALUE_LANG'] = array(
    'key' => 'CAT_FEA_MISSING_FEATURE_VALUE_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Missing translations in ps_feature_value_lang',
    'description' => 'List of id_feature_value in ps_feature_value but not in ps_feature_value_lang',
);

$controls['CAT_FEA_GHOST_FEATURE_VALUE'] = array(
    'key' => 'CAT_FEA_GHOST_FEATURE_VALUE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Missing feature value row in ps_feature_value',
    'description' => 'List of feature value in ps_feature_value_lang but not in ps_feature_value',
);

$controls['CUS_GRP_MISSING_GROUP_LANG'] = array(
    'key' => 'CUS_GRP_MISSING_GROUP_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Customer',
    'section' => 'Group',
    'name' => 'Missing translations in ps_group_lang',
    'description' => 'List of id_group in ps_group but not in ps_group_lang',
);

$controls['CAT_PRODUCT_TAX_DELETED'] = array(
    'key' => 'CAT_PRODUCT_TAX_DELETED',
    'version_min' => '1.6.0.10',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products with deleted tax',
    'description' => 'List of rows in ps_product with id_tax_rules_group on deleted = 1',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CUS_GRP_GHOST_GROUP'] = array(
    'key' => 'CUS_GRP_GHOST_GROUP',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Customer',
    'section' => 'Group',
    'name' => 'Missing feature value row in ps_group',
    'description' => 'List of groups in ps_group_lang but not in ps_group',
);

$controls['CAT_MAN_MISSING_MANUFACTURER_LANG'] = array(
    'key' => 'CAT_MAN_MISSING_MANUFACTURER_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Manufacturer',
    'name' => 'Missing translations in ps_manufacturer_lang',
    'description' => 'List of id_manufacturer in ps_manufacturer but not in ps_manufacturer_lang',
);

$controls['CAT_MAN_GHOST_MANUFACTURER'] = array(
    'key' => 'CAT_MAN_GHOST_MANUFACTURER',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Manufacturer',
    'name' => 'Missing manufacturer row in ps_manufacturer',
    'description' => 'List of manufacturer in ps_manufacturer_lang but not in ps_manufacturer',
);

$controls['CAT_SUP_MISSING_SUPPLIER_LANG'] = array(
    'key' => 'CAT_SUP_MISSING_SUPPLIER_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Supplier',
    'name' => 'Missing translations in ps_supplier_lang',
    'description' => 'List of id_supplier in ps_supplier but not in ps_supplier_lang',
);

$controls['CAT_SUP_GHOST_SUPPLIER'] = array(
    'key' => 'CAT_SUP_GHOST_SUPPLIER',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Supplier',
    'name' => 'Missing supplier row in ps_supplier',
    'description' => 'List of supplier in ps_supplier_lang but not in ps_supplier',
);

$controls['CAT_TAX_MISSING_TAX_LANG'] = array(
    'key' => 'CAT_TAX_MISSING_TAX_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Taxes',
    'name' => 'Missing translations in ps_tax_lang',
    'description' => 'List of id_tax in ps_tax but not in ps_tax_lang',
);

$controls['CAT_TAX_GHOST_TAX'] = array(
    'key' => 'CAT_TAX_GHOST_TAX',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Taxes',
    'name' => 'Missing tax row in ps_tax',
    'description' => 'List of tax in ps_tax_lang but not in ps_tax',
);

$controls['GEN_ATH_MISSING_ATTACHMENT_LANG'] = array(
    'key' => 'GEN_ATH_MISSING_ATTACHMENT_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'General',
    'section' => 'Attachment',
    'name' => 'Missing translations in ps_attachment_lang',
    'description' => 'List of id_attachment in ps_attachment but not in ps_attachment_lang',
);

$controls['GEN_ATH_GHOST_ATTACHMENT'] = array(
    'key' => 'GEN_ATH_GHOST_ATTACHMENT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'General',
    'section' => 'Attachment',
    'name' => 'Missing attachment row in ps_attachment',
    'description' => 'List of attachment in ps_attachment_lang but not in ps_attachment',
);

$controls['GEN_CMS_MISSING_CMS_BLOCK_LANG'] = array(
    'key' => 'GEN_CMS_MISSING_CMS_BLOCK_LANG',
    'version_min' => '1.5',
    'version_max' => '1.6.1.0',
    'tools' => 'CMS',
    'section' => 'Block',
    'name' => 'Missing translations in ps_cms_block_lang',
    'description' => 'List of id_cms_block in ps_cms_block but not in ps_cms_block_lang',
);

$controls['GEN_CMS_GHOST_CMS_BLOCK'] = array(
    'key' => 'GEN_CMS_GHOST_CMS_BLOCK',
    'version_min' => '1.5',
    'version_max' => '1.6.1.0',
    'tools' => 'CMS',
    'section' => 'Block',
    'name' => 'Missing cms block row in ps_cms_block',
    'description' => 'List of cms block in ps_cms_block_lang but not in ps_cms_block',
);

$controls['GEN_CMS_MISSING_CMS_CATEGORY_LANG'] = array(
    'key' => 'GEN_CMS_MISSING_CMS_CATEGORY_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'CMS',
    'section' => 'Category',
    'name' => 'Missing translations in ps_cms_category_lang',
    'description' => 'List of id_cms_category in ps_cms_category but not in ps_cms_category_lang',
);

$controls['GEN_CMS_GHOST_CMS_CATEGORY'] = array(
    'key' => 'GEN_CMS_GHOST_CMS_CATEGORY',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'CMS',
    'section' => 'Category',
    'name' => 'Missing cms category row in ps_cms_category',
    'description' => 'List of cms category in ps_cms_category_lang but not in ps_cms_category',
);

$controls['GEN_CMS_MISSING_CMS_LANG'] = array(
    'key' => 'GEN_CMS_MISSING_CMS_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'CMS',
    'section' => 'CMS',
    'name' => 'Missing translations in ps_cms_lang',
    'description' => 'List of id_cms in ps_cms but not in ps_cms_lang',
);

$controls['GEN_CMS_GHOST_CMS'] = array(
    'key' => 'GEN_CMS_GHOST_CMS',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'CMS',
    'section' => 'CMS',
    'name' => 'Missing CMS row in ps_cms',
    'description' => 'List of CMS in ps_cms_lang but not in ps_cms',
);

$controls['CMD_STA_MISSING_ORDER_STATE_LANG'] = array(
    'key' => 'CMD_STA_MISSING_ORDER_STATE_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Order',
    'section' => 'Status',
    'name' => 'Missing translations in ps_order_state_lang',
    'description' => 'List of id_order_state in ps_order_state but not in ps_order_state_lang',
);

$controls['CMD_STA_GHOST_ORDER_STATE'] = array(
    'key' => 'CMD_STA_GHOST_ORDER_STATE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Order',
    'section' => 'Status',
    'name' => 'Missing order state row in ps_order_state',
    'description' => 'List of order states in ps_order_state_lang but not in ps_order_state',
);

$controls['MUL_DAT_GHOST_SHOP'] = array(
    'key' => 'MUL_DAT_GHOST_SHOP',
    'version_min' => '1.5.0.0',
    'version_max' => '',
    'tools' => 'Multistores',
    'section' => 'Database',
    'name' => 'Missing rows in ps_[...]',
    'description' => 'List of elements in ps_[...]_shop but not in ps_[...]. Elements will be put back in the default shop',
);

$controls['MUL_DAT_MISSING_SHOP'] = array(
    'key' => 'MUL_DAT_MISSING_SHOP',
    'version_min' => '1.5.0.0',
    'version_max' => '',
    'tools' => 'Multistores',
    'section' => 'Database',
    'name' => 'Missing rows in ps_[...]_shop',
    'description' => 'List of elements in ps_[...] but not in ps_[...]_shop',
);

$controls['CAT_STK_GHOST_STOCK_AVAILABLE'] = array(
    'key' => 'CAT_STK_GHOST_STOCK_AVAILABLE',
    'version_min' => '1.5.0.0',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Stock',
    'name' => 'Ghost product or combination in ps_stock_available',
    'description' => 'List of elements in ps_stock_available but not in ps_product or ps_product_attribute',
);

$controls['CAT_PROD_MISSING_COVER_IMAGE'] = array(
    'key' => 'CAT_PROD_MISSING_COVER_IMAGE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Product with no cover image',
    'description' => 'List of products with images but no cover',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PROD_CUSTOM_FEATURE'] = array(
    'key' => 'CAT_PROD_CUSTOM_FEATURE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products with same custom feature value',
    'description' => 'List of products using the same feature value',
);

$controls['CAT_PROD_DESC_HIDDEN_CARAC'] = array(
    'key' => 'CAT_PROD_DESC_HIDDEN_CARAC',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products with hidden characters',
    'description' => 'List of products with hidden characters in descriptions',
    'segment_params' => array(
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PROD_DIFF_ADVANCEDSTOCKS_MODE'] = array(
    'key' => 'CAT_PROD_DIFF_ADVANCEDSTOCKS_MODE',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products with different Advanced Stocks Mgmt. (ASM) modes',
    'description' => 'List of products using different Advanced Stocks Management modes in different shops',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '2',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE'] = array(
    'key' => 'CAT_ATTR_GHOST_ATTRIBUTE_IN_WAREHOUSE',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Attributes',
    'name' => 'Ghost combination in warehouse',
    'description' => 'List of combinations in ps_warehouse_product_location but not in ps_product_attribute',
);

$controls['CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE'] = array(
    'key' => 'CAT_PROD_PRODUCT_WITH_COMBI_IN_WAREHOUSE',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Alone row for a product, with combination, in warehouse',
    'description' => 'List of rows in ps_warehouse_product_location for useless information about products with combinations',
);

$controls['CAT_SUP_GHOST_PRODUCT_COMBI_SUPPLIER'] = array(
    'key' => 'CAT_SUP_GHOST_PRODUCT_COMBI_SUPPLIER',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Supplier',
    'name' => 'Ghost products/combinations in product_supplier',
    'description' => 'List of products/combinations in ps_product_supplier but not in ps_product/ps_product_attribute',
);

$controls['CAT_CAT_WRONG_PARENT'] = array(
    'key' => 'CAT_CAT_WRONG_PARENT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Categories with wrong tree structure',
    'description' => 'List of categories with a wrong tree structure',
);

$controls['CAT_PROD_NOT_SAME_ATTRIBUTES'] = array(
    'key' => 'CAT_PROD_NOT_SAME_ATTRIBUTES',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Attributes',
    'name' => 'Products with not same attributes groups',
    'description' => 'List of products with combinations with not same attributes groups. Once the errors have been detected, (1) click on a product. (2 you can then delete groups of attributes that are obsolete or irrelevant for this product. (3) Click on "Save" in order for Store Commander to create combinations correctly. (4) Beware that attributes values by default are created as and when necessary ; you will then need to re-enter appropriate values for these combinations.',
);

$controls['CAT_PROD_LANG_WITH_EMPTY_SHOP'] = array(
    'key' => 'CAT_PROD_LANG_WITH_EMPTY_SHOP',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Rows in product_lang with empty id_shop',
    'description' => 'List of rows in product_lang table with id_shop = 0',
);

$controls['MUL_DAT_EMPTY_ID_SHOP'] = array(
    'key' => 'MUL_DAT_EMPTY_ID_SHOP',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Multistores',
    'section' => 'Database',
    'name' => 'Rows with empty id_shop',
    'description' => 'List of elements with empty id_shop in ps_[...]',
);

$controls['MUL_DAT_LANG_EMPTY_ID_SHOP'] = array(
    'key' => 'MUL_DAT_LANG_EMPTY_ID_SHOP',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Multistores',
    'section' => 'Database',
    'name' => 'Rows ps_[...]_lang with empty id_shop',
    'description' => 'List of elements with empty id_shop in ps_[...]_lang',
);

$controls['CAT_PROD_WITHOUT_DEFAULT_COMBI'] = array(
    'key' => 'CAT_PROD_WITHOUT_DEFAULT_COMBI',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products without default combination',
    'description' => 'List of products without default combination',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_AND_PROD_NOT_SHARING_SHOP'] = array(
    'key' => 'CAT_AND_PROD_NOT_SHARING_SHOP',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products and categories not sharing shops',
    'description' => 'List of products and categories not sharing shops',
);

$controls['CAT_COMBI_GHOST_MS'] = array(
    'key' => 'CAT_COMBI_GHOST_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'Missing combination row in ps_product_attribute_shop',
    'description' => 'List of combination in ps_product_attribute but not in ps_product_attribute_shop',
);

$controls['CAT_COMBI_PRODUCT_NOT_SHOP_SHARED'] = array(
    'key' => 'CAT_COMBI_PRODUCT_NOT_SHOP_SHARED',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'Combinations with product not shared with the shop',
    'description' => 'List of products not shared with a shop when at least one combination of that product is shared with that shop',
);

$controls['CAT_COMBI_WITHOUT_STOCK_ROW'] = array(
    'key' => 'CAT_COMBI_WITHOUT_STOCK_ROW',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'Combinations without row in ps_stock',
    'description' => 'List of combinations shared in a warehouse but without row in ps_stock for it',
);

$controls['CAT_PRODUCT_WITHOUT_STOCK_ROW'] = array(
    'key' => 'CAT_PRODUCT_WITHOUT_STOCK_ROW',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products without row in ps_stock',
    'description' => 'List of products shared in a warehouse but without row in ps_stock for it',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PRODUCT_IMG_SHOP_EMPTY_PRODUCT'] = array(
    'key' => 'CAT_PRODUCT_IMG_SHOP_EMPTY_PRODUCT',
    'version_min' => '1.6.1',
    'version_max' => '',

    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'image_shop without id_product',
    'description' => 'List of row in ps_image_shop without id_product',
);

$controls['CAT_PROD_MISSING_IMAGE_LANG'] = array(
    'key' => 'CAT_PROD_MISSING_IMAGE_LANG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Missing image information in ps_image_lang',
    'description' => 'List of id_image in ps_image but not in ps_image_lang',
);

$controls['CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE'] = array(
    'key' => 'CAT_STK_TOO_MUCH_COMBI_STOCK_AVAILABLE',
    'version_min' => '1.5.0.0',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Stock',
    'name' => 'Wrong combi. qty in ps_stock_available',
    'description' => 'List of combinations in advanced stock but with a wrong qty in ps_stock_available',
);

$controls['CAT_PROD_GHOST_MS'] = array(
    'key' => 'CAT_PROD_GHOST_MS',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Missing products row in ps_product_shop',
    'description' => 'List of products in ps_product but not in ps_product_shop',
);

$controls['CAT_FEA_DOUBLE_PRODUCT'] = array(
    'key' => 'CAT_FEA_DOUBLE_PRODUCT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Row in two copies in ps_feature_product',
    'description' => "List of rows in two copies in ps_feature_product (Caution ! Don't fix errors when you have multiple features module)",
);

$controls['CAT_FEA_FEATURE_VALUE_NOTEXIST'] = array(
    'key' => 'CAT_FEA_FEATURE_VALUE_NOTEXIST',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'feature_product with id_feature_value not existing',
    'description' => 'List of id_feature_value not existing but in ps_feature_product',
);

$controls['CAT_PROD_TODO_IMPORT_CATEGORY'] = array(
    'key' => 'CAT_PROD_TODO_IMPORT_CATEGORY',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Category',
    'name' => 'Products presents in categories TODO.csv',
    'description' => 'List of categories TODO.csv which contains products',
);

$controls['CAT_PRODUCT_DUPLICATE_REFERENCE'] = array(
    'key' => 'CAT_PRODUCT_DUPLICATE_REFERENCE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'product sharing same reference',
    'description' => 'List of id_product with same reference',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PRODUCT_DUPLICATE_NAME'] = array(
    'key' => 'CAT_PRODUCT_DUPLICATE_NAME',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'products sharing the same name',
    'description' => 'List of products with the same name',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PRODUCT_DUPLICATE_UPC'] = array(
    'key' => 'CAT_PRODUCT_DUPLICATE_UPC',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'product sharing same UPC',
    'description' => 'List of id_product with same UPC',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PRODUCT_DUPLICATE_EAN'] = array(
    'key' => 'CAT_PRODUCT_DUPLICATE_EAN',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'product sharing same EAN13',
    'description' => 'List of id_product with same EAN13',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_COMBI_DUPLICATE_REFERENCE'] = array(
    'key' => 'CAT_COMBI_DUPLICATE_REFERENCE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'combination sharing same reference',
    'description' => 'List of id_product_attribute with same reference',
);

$controls['CAT_COMBI_DUPLICATE_UPC'] = array(
    'key' => 'CAT_COMBI_DUPLICATE_UPC',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'combination sharing same UPC',
    'description' => 'List of id_product_attribute with same UPC',
);

$controls['CAT_COMBI_DUPLICATE_EAN'] = array(
    'key' => 'CAT_COMBI_DUPLICATE_EAN',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Combination',
    'name' => 'combination sharing same EAN13',
    'description' => 'List of id_product_attribute with same EAN13',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PRODUCT_COMBI_DUPLICATE_EAN'] = array(
    'key' => 'CAT_PRODUCT_COMBI_DUPLICATE_EAN',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'product sharing same EAN13 with combination',
    'description' => 'List of product/combination with same EAN13',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);
$controls['CAT_PRODUCT_COMBI_DUPLICATE_REFERENCE'] = array(
    'key' => 'CAT_PRODUCT_COMBI_DUPLICATE_REFERENCE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'product sharing same reference with combination',
    'description' => 'List of product/combination with same reference',
);
$controls['CAT_PRODUCT_COMBI_DUPLICATE_UPC'] = array(
    'key' => 'CAT_PRODUCT_COMBI_DUPLICATE_UPC',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'product sharing same UPC with combination',
    'description' => 'List of product/combination with same upc',
);

$controls['CAT_PRODUCT_VALIDATE_UPC_FORMAT'] = array(
    'key' => 'CAT_PRODUCT_VALIDATE_UPC_FORMAT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products with bad format UPC',
    'description' => 'List of products with bad format UPC (UPC should be 12 numeric digits)',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PRODUCT_VALIDATE_EAN_FORMAT'] = array(
    'key' => 'CAT_PRODUCT_VALIDATE_EAN_FORMAT',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'Products with bad format EAN',
    'description' => 'List of products with bad format EAN (EAN should be 13 numeric digits + validation of the check digit)',
    'segment_params' => array(
        'value_separator' => '-',
        'index_of_value_to_get' => '1',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CUS_DB_DUPLICATE_EMAIL'] = array(
    'key' => 'CUS_DB_DUPLICATE_EMAIL',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Customer',
    'section' => 'Database',
    'name' => 'customers sharing same email',
    'description' => 'List of customers with same email',
    'segment_params' => array(
        'access' => '-customers-',
        'element_type' => 'customer',
    ),
);

$controls['CAT_SEO_DUPLICATE_META_DESC'] = array(
    'key' => 'CAT_SEO_DUPLICATE_META_DESC',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'SEO',
    'name' => 'products sharing same meta description',
    'description' => 'List of products with same meta description',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);
$controls['CAT_SEO_DUPLICATE_META_TITLE'] = array(
    'key' => 'CAT_SEO_DUPLICATE_META_TITLE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'SEO',
    'name' => 'products sharing same meta title',
    'description' => 'List of products with same meta title',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);

$controls['CAT_PROD_EMPTY_CUSTOM_FEATURE'] = array(
    'key' => 'CAT_PROD_EMPTY_CUSTOM_FEATURE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Feature',
    'name' => 'Products with empty custom feature value',
    'description' => 'List of products using an empty custom feature value',
);

$controls['CUS_CHECK_RGPD'] = array(
    'key' => 'CUS_CHECK_RGPD',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Customer',
    'section' => 'GDPR',
    'name' => 'Check customers for GDPR',
    'description' => "List of customers created over X months without any orders and with last connection over Y months <a href=\"#\" onclick=\"openSettingsWindow('Application','FixMyPrestashop','APP_FIX_CHECK_RGPD_MONTH');return false;\">(choose X in Settings)</a><a href=\"#\" onclick=\"openSettingsWindow('Application','FixMyPrestashop','APP_FIX_CHECK_RGPD_LASTCONN_MONTH');return false;\">(choose Y in Settings)</a>",
    'segment_params' => array(
        'access' => '-customers-',
        'element_type' => 'customer',
    ),
);

$controls['SEC_FIL_INSTALL_LICENCES'] = array(
    'key' => 'SEC_FIL_INSTALL_LICENCES',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if INSTALL.txt , LICENCES and readme_override.txt files were deleted',
    'description' => 'If present these files can generate security issues',
);

$controls['SEC_SMARTY_CACHE'] = array(
    'key' => 'SEC_SMARTY_CACHE',
    'version_min' => '1.6.0.10',
    'version_max' => '1.7.8.7',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Verify your shop vulnerability to CVE-2022-31181',
    'description' => 'Check the vulnerability of your shop following a critical security flaw (PrestaShop versions 1.6.x to 1.7.8.7)',
);
$controls['SEC_BLOCKWISHLIST'] = array(
    'key' => 'SEC_BLOCKWISHLIST',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Verify your shop vulnerability to CVE-2022-31101',
    'description' => 'Check the vulnerability of your shop following a critical security flaw  (blockwishlist module versions < 2.1.1)',
);

$controls['SEC_FIL_DOCS_FILES'] = array(
    'key' => 'SEC_FIL_DOCS_FILES',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if some files in docs directory were deleted',
    'description' => 'If present these files can generate security issues',
);

$controls['SEC_FIL_GIT_FILES'] = array(
    'key' => 'SEC_FIL_GIT_FILES',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if some .git folder in /classes or /override are presents',
    'description' => 'If presents and accessible from outside these files can generate security issues.',
);

$controls['SEC_FIL_ROBOTS'] = array(
    'key' => 'SEC_FIL_ROBOTS',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if robots.txt is present',
    'description' => 'To optimise SEO',
);

$controls['SEC_PWD_SAME_PASSWORD'] = array(
    'key' => 'SEC_PWD_SAME_PASSWORD',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Password',
    'name' => 'Customer and employee with same password',
    'description' => 'Check if a customer and a employee have the same password',
);
$controls['SEC_PWD_EMAIL_TEMPLATE_PASSWORD'] = array(
    'key' => 'SEC_PWD_EMAIL_TEMPLATE_PASSWORD',
    'version_min' => '1.5',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Password',
    'name' => '{passwd} tag present in email template',
    'description' => 'Check if email templates have password tag for security reason',
);

$controls['SEC_SER_PHP_VERSION'] = array(
    'key' => 'SEC_SER_PHP_VERSION',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Server',
    'name' => 'Check PHP version',
    'description' => 'Check if php version on your server is good',
);
$controls['SEC_FIL_PHPUNIT_BREACH'] = array(
    'key' => 'SEC_FIL_PHPUNIT_BREACH',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if phpunit folder exists',
    'description' => 'If vendor/phpunit exists it can generate security issues',
);
$controls['SEC_FRONT_CUSTOMER_FORM_BREACH'] = array(
    'key' => 'SEC_FRONT_CUSTOMER_FORM_BREACH',
    'version_min' => '1.7.0.0',
    'version_max' => '1.7.6.3',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Verify your shop vulnerability to CVE-2020-5250',
    'description' => 'Verify your shop vulnerability to a critical data leak (PrestaShop version 1.7.0.0 to 1.7.6.3)',
);
$controls['CAT_SEO_IMAGE_COMPRESSION'] = array(
    'key' => 'CAT_SEO_IMAGE_COMPRESSION',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'SEO',
    'name' => 'Image compression',
    'description' => 'Check if image compression is enable on your shop',
);
$controls['SEC_FIL_DOCKER'] = array(
    'key' => 'SEC_FIL_DOCKER',
    'version_min' => '1.7.0.0',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if .docker folder is present',
    'description' => 'If present, folder contained can generate security issues',
);
$controls['GEN_DB_MANY_PAGENOTFOUND'] = array(
    'key' => 'GEN_DB_MANY_PAGENOTFOUND',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'General',
    'section' => 'Database',
    'name' => array('Number of lines in ps_pagenotfound (+%s)', array('10000')),
    'description' => 'Check number of lines in ps_pagenotfound table',
);
$controls['GEN_DB_MANY_LOG'] = array(
    'key' => 'GEN_DB_MANY_LOG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'General',
    'section' => 'Database',
    'name' => array('Number of lines in ps_log (+%s)', array('10000')),
    'description' => 'Check number of lines in ps_log table',
);
$controls['CUS_DB_UNUSED_CART_OVER_TWO_YEAR'] = array(
    'key' => 'CUS_DB_UNUSED_CART_OVER_TWO_YEAR',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Customer',
    'section' => 'Database',
    'name' => 'Abandoned carts',
    'description' => 'Check abandoned carts over 2 years old ',
);
$controls['CUS_DB_CART_RULE_OBSOLETE'] = array(
    'key' => 'CUS_DB_CART_RULE_OBSOLETE',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Customer',
    'section' => 'Database',
    'name' => 'Obsolete cart rules',
    'description' => 'Check obsolete cart rules',
);
$controls['GEN_SER_SC_VERSION'] = array(
    'key' => 'GEN_SER_SC_VERSION',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Server',
    'name' => 'Store Commander version',
    'description' => 'Check if Store Commander version is up to date',
);
$controls['CAT_PROD_DUPLICATE_COMBINATION'] = array(
    'key' => 'CAT_PROD_DUPLICATE_COMBINATION',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => 'products with duplicate combinations',
    'description' => 'List of products with duplicate combinations',
    'segment_params' => array(
        'value_separator' => '_',
        'index_of_value_to_get' => '0',
        'access' => '-catalog-',
        'element_type' => 'product',
    ),
);
$controls['SEC_ADMIN_PAGINATION_SQL_BREACH'] = array(
    'key' => 'SEC_ADMIN_PAGINATION_SQL_BREACH',
    'version_min' => '1.7.5.0',
    'version_max' => '1.7.8.1',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => array('Verify your shop vulnerability to %s', array('CVE-2021-43789')),
    'description' => 'Verify your shop vulnerability to a critical security breach (PrestaShop version 1.7.5.0 to 1.7.8.1)',
);

$controls['SEC_TWIG_BREACH'] = array(
    'key' => 'SEC_TWIG_BREACH',
    'version_min' => '1.7.0.0',
    'version_max' => '1.7.8.2',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => array('Verify your shop vulnerability to %s', array('CVE-2022-21686')),
    'description' => 'Verify your shop vulnerability to a security breach (PrestaShop version 1.7.0.0 to 1.7.8.2)',
);
$controls['SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS'] = array(
    'key' => 'SEC_DB_EMPLOYEE_NO_LOGON_3MONTHS',
    'version_min' => '1.4',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Database',
    'name' => 'Employees not logged in for 3 months',
    'description' => 'List of employees not logged in for 3 months',
);
$controls['SEC_UNINSTALLED_MODULES'] = array(
    'key' => 'SEC_UNINSTALLED_MODULES',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Modules',
    'name' => 'List of uninstalled modules',
    'description' => 'List of uninstalled modules to delete for secyrity purpose',
);

$controls['SEC_DISABLED_MODULES'] = array(
    'key' => 'SEC_DISABLED_MODULES',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Modules',
    'name' => 'List of disabled modules',
    'description' => 'List of disabled modules to delete for secyrity purpose',
);

$notValid = Db::getInstance()->getValue('SELECT id_module
                                                FROM `'._DB_PREFIX_.'module`
                                                WHERE `name` ="lgseoredirect"');
if ($notValid)
{
    $controls['SEC_MODULE_LGSEOREDIRECT'] = array(
        'key' => 'SEC_MODULE_LGSEOREDIRECT',
        'version_min' => '',
        'version_max' => '',
        'tools' => 'Security',
        'section' => 'Modules',
        'name' => array('Check if module %s contains a critical security breach', array('lgseoredirect')),
        'description' => 'Verify if the module version contains a critical security breach',
    );
}

$notValid = Db::getInstance()->getValue('SELECT id_module
                                                FROM `'._DB_PREFIX_.'module`
                                                WHERE `name` ="productcomments"');
if ($notValid)
{
    $controls['SEC_MODULE_PRODUCTCOMMENTS'] = array(
        'key' => 'SEC_MODULE_PRODUCTCOMMENTS',
        'version_min' => '',
        'version_max' => '',
        'tools' => 'Security',
        'section' => 'Modules',
        'name' => array('Check if module %s contains a critical security breach', array(_l('productcomments'))),
        'description' => 'Verify if the module version contains a critical security breach',
    );
}

$controls['SEC_MODULE_SCAFFILIATION'] = array(
    'key' => 'SEC_MODULE_SCAFFILIATION',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Modules',
    'name' => array('Check module %s', array(_l('Affiliation program'))),
    'description' => array('Verify if the module version of %s is up to date', array(_l('Affiliation program'))),
);
$controls['SEC_MODULE_SCEXPORTCUSTOMERS'] = array(
    'key' => 'SEC_MODULE_SCEXPORTCUSTOMERS',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Modules',
    'name' => array('Check module %s', array(_l('Customers export'))),
    'description' => array('Verify if the module version of %s is up to date', array(_l('Customers export'))),
);
$controls['SEC_MODULE_SCPDFCATALOG'] = array(
    'key' => 'SEC_MODULE_SCPDFCATALOG',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Modules',
    'name' => array('Check module %s', array(_l('PDF Catalog'))),
    'description' => array('Verify if the module version of %s is up to date', array(_l('PDF Catalog'))),
);
$controls['SEC_MODULE_SCQUICKACCOUNTING'] = array(
    'key' => 'SEC_MODULE_SCQUICKACCOUNTING',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Modules',
    'name' => array('Check module %s', array(_l('Order Export Pro'))),
    'description' => array('Verify if the module version of %s is up to date', array(_l('Order Export Pro'))),
);
$controls['CAT_PROD_WITHOUT_PRODUCT_TYPE'] = array(
    'key' => 'CAT_PROD_WITHOUT_PRODUCT_TYPE',
    'version_min' => '1.7.8',
    'version_max' => '',
    'tools' => 'Catalog',
    'section' => 'Product',
    'name' => array('Check products without default type'),
    'description' => array('Verify if products have a default type'),
);

$controls['SEC_CUSTOMER_DEFAULT_INSTALL'] = array(
    'key' => 'SEC_CUSTOMER_DEFAULT_INSTALL',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Customer',
    'name' => array('Check if dummy customer account present'),
    'description' => array('Verify if the default first customer created by %s installation is still present', array('PrestaShop')),
);

$controls['SEC_ADMIN_FOLDER'] = array(
    'key' => 'SEC_ADMIN_FOLDER',
    'version_min' => '',
    'version_max' => '',
    'tools' => 'Security',
    'section' => 'Files',
    'name' => 'Check if your admin folder is secure',
    'description' => 'Verify if admin folder is not compromised or not secure enough',
);

$notValid = Db::getInstance()->getValue('SELECT id_module
                                                FROM `'._DB_PREFIX_.'module`
                                                WHERE `name` ="paypal"
                                                AND version BETWEEN "3.12.0" AND "3.16.3"');
if ($notValid)
{
    $controls['SEC_MODULE_PAYPAL'] = array(
        'key' => 'SEC_MODULE_PAYPAL',
        'version_min' => '',
        'version_max' => '',
        'tools' => 'Security',
        'section' => 'Modules',
        'name' => array('Check if module %s contains a critical security breach', array('PayPal')),
        'description' => 'Verify if the module version contains a critical security breach',
    );
}
