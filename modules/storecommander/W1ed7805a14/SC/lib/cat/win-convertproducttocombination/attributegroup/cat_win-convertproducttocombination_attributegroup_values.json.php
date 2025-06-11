<?php
if (!defined('STORE_COMMANDER')) {
    exit;
}
global $sc_agent;

try{
    $pdo = Db::getInstance()->getLink();

    // TODO 3 : a généraliser dans ScPdo par exemple ?
    if(Tools::getIsset('DEBUG')){
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    $shippingBoService= \Sc\Service\Shippingbo\ShippingboService::getInstance();
    $attributeGroupId = Tools::getValue('attribute_group_id', null);
    if(!$attributeGroupId)
        throw new Exception(_l('parameter needed'));
    $attributeValuesQuery = new DbQuery();
    $attributeValuesQuery
        ->select('a.id_attribute')
        ->select('al.name')
        ->from('attribute','a')
        ->leftJoin('attribute_lang', 'al', 'al.id_attribute = a.id_attribute AND al.id_lang = :id_lang')
        ->leftJoin('attribute_shop', 'as', 'as.id_attribute = a.id_attribute AND as.id_shop IN('.pInSQL($shippingBoService->getConfigShopsForPdo()).')')
        ->where('a.id_attribute_group = :id_attribute_group')
    ;
    $attributeValuesStatement = $pdo->prepare($attributeValuesQuery);
    $attributeValuesStatement->execute(array(
        ':id_lang' => $sc_agent->getIdLang(),
//        ':id_shop' => SCI::getSelectedShop(),
        ':id_attribute_group' => $attributeGroupId,
    ));
    $attributeValues = $attributeValuesStatement->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

$successMessage = '';
$response = array('state' => true, 'extra' => array('code' => 200, 'message' => $successMessage));
if (!empty($errors)) {
    $response['state'] = false;
    $response['extra']['code'] = 103;
    $response['extra']['message'] = '<ul style="padding-left:10px;"><li>' . implode('</li><li>', $errors) . '</li></ul>';
}
$response['extra']['content'] = $attributeValues;

// HEADER
header('Content-type: application/json');

exit(json_encode($response));
