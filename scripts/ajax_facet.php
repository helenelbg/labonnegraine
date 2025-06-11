<?php

// script de test

include("../config/config.inc.php");
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

$html = '';

$categoryId = 18;
$featureIds = [2437];

$products = getProductsByCategoryAndFeatures($categoryId, $featureIds);

foreach ($products as $product) {
	$id_product = $product['id_product'];
	$product_obj = new Product($id_product);
	
	$id_image = Product::getCover($id_product)['id_image'];
	
	$product['id_product_attribute'] = 0;
	$product['url'] = $product['link_rewrite'];
	$product['link'] = $product['link_rewrite'];
	$product['has_discount'] = 0;
	$product['discount_type'] = 0;
	$product['regular_price'] = 0;
	$product['discount_percentage'] = 0;
	$product['discount_amount_to_display'] = 0;
	$product['price'] = 0;
	$product['prix_min'] = 0;
    $link = Context::getContext()->link;

	$retriever = new ImageRetriever($link);
	$cover = $retriever->getImage($product_obj, $id_image);
	$product['cover'] = $cover;
	//product_obj->getImages();

	Context::getContext()->smarty->assign(array(
		'product' => $product,
		'link' => $link,
	));
	$html .= Context::getContext()->smarty->fetch('catalog/_partials/miniatures/product.tpl');
}

echo $html;

function getProductsByCategoryAndFeatures($categoryId, $featureIds) {
    $products = array();

    $categoryId = (int)$categoryId;

    $featureIdsList = implode(',', array_map('intval', $featureIds));

    $sql = "SELECT p.*, pl.*
            FROM `" . _DB_PREFIX_ . "product` p
            LEFT JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (p.`id_product` = pl.`id_product`)
            LEFT JOIN `" . _DB_PREFIX_ . "category_product` cp ON (p.`id_product` = cp.`id_product`)
            LEFT JOIN `" . _DB_PREFIX_ . "feature_product` fp ON (p.`id_product` = fp.`id_product`)
            WHERE cp.`id_category` = $categoryId
            AND fp.`id_feature_value` IN ($featureIdsList)
            GROUP BY p.`id_product`";

    $products = Db::getInstance()->executeS($sql);

    return $products;
	
}
