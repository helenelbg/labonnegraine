<?php
if (!defined('STORE_COMMANDER')) {
    exit;
}
global $sc_agent;
$errors = array();
$successMessage = _l('combinations created successfully');

$newProductName = Tools::getValue('new_product_name', '');
$convertData =json_decode(Tools::getValue('convert_data', '[]'));
$defaultProductId =(int) Tools::getValue('default_product_id', null);
$productToConvertIds = array_keys((array) $convertData);

$cat_id = Tools::getValue('idc', '');
$currentCatIsSegment = strpos($cat_id,'seg_') === 0;

try {
    // create recipient product
    $product = new Product(null, false, $sc_agent->id_lang, SCI::getSelectedShop());
    $product->name[$sc_agent->getIdLang()] = $newProductName;
    $product->link_rewrite[$sc_agent->getIdLang()] =link_rewrite($newProductName);
    $product->save();

    // add each product to
    foreach ($productToConvertIds as $productToConvertId) {
        $productToConvert = new Product($productToConvertId, false, $sc_agent->id_lang, SCI::getSelectedShop());
        $params = array(
            'price' => 0,
            'weight' => $productToConvert->weight,
            'unit_impact' => 0,
            'ecotax' => 0,
            'id_images' => array(),
            'reference' => $productToConvert->reference,
            'ean13' => $productToConvert->ean13,
            'default' => $productToConvertId === $defaultProductId?true: false,
            'location' => null,
            'upc' => $productToConvert->upc,
            'minimal_quantity' => 1,
            'id_shop_list' => (array) SCI::getSelectedShop(),
        );
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
        {
            $params += array(
                'available_date' => $productToConvert->available_date,
                'quantity' => 0,
                'isbn' => $productToConvert->isbn,
            );
            unset($params['id_shop']);
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            $params += array(
                'low_stock_threshold' => null,
                'low_stock_alert' => false,
                'mpn' => $productToConvert->mpn,
            );
        }

        $combinationId = call_user_func_array(array($product,'addAttribute'), $params);
        $productToConvert->delete();

        $idProductAttributes = (array) $convertData->$productToConvertId;

        $combination = new Combination($combinationId);
        $combination->setAttributes($idProductAttributes);
    }
    // remove products from manual segments
    $removeFromSegmentsQuery = Db::getInstance()->query('DELETE FROM '._DB_PREFIX_.'sc_segment_element WHERE id_element IN ('.pInSql(implode(',',array_values($productToConvertIds))).')');

    if($currentCatIsSegment && $cat_id){
        $id_segment = str_replace('seg_','',$cat_id);
        ScSegmentElement::addProduct($id_segment,$product);
    } else if($cat_id != ''){
        $product->addToCategories(array($cat_id));
    }


} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

$response = array('state' => true, 'extra' => array('code' => 200, 'message' => $successMessage));
if (!empty($errors)) {
    $response['state'] = false;
    $response['extra']['code'] = 103;
    $response['extra']['message'] = '<ul style="padding-left:10px;"><li>' . implode('</li><li>', $errors) . '</li></ul>';
}

exit(json_encode($response));