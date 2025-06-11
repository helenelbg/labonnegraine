<?php

//header('Content-Type: application/json; charset=utf-8');

$action = Tools::getValue('action', '0'); // type d'action a effectuer (update/delete/Add,etc.)
$featureId = (int) Tools::getValue('featureId', null); // feature cible
$featureValueIds = (string) Tools::getValue('featureValueId', 0);
$customValues = json_decode(Tools::getValue('customValues', '[]'), true); // données à traiter
$idsLang = array();
foreach ($languages as $r_lang)
{
    $idsLang[] = (int) $r_lang['id_lang'];
}
$productIds = Tools::getValue('productIds', '');
$productIdsArray = explode(',', $productIds);
$productId = end($productIdsArray);

$positions = Tools::getValue('positions', null);
$positionsArray = explode(',', $positions);

if (!$featureId)
{
    exit(json_encode(array(
        'error' => true,
        'message' => 'no featureId provided',
    )));
}

switch ($action){
    case 'update':
        multipleFeaturesCustomValuesUpdate($customValues, $productId);
        break;
    case 'delete':
        multipleFeaturesCustomValuesDelete($featureValueIds, $productId);
        break;
    case 'add':
        multipleFeaturesCustomValuesAdd($featureId, $productId, $idsLang);
        break;
    case 'position':
        multipleFeaturesCustomValuesPosition($positionsArray, $featureId, $productId);
        break;
}

/**
 * @param $customValues
 * @param $productId
 */
function multipleFeaturesCustomValuesUpdate($customValues, $productId)
{
    $featureValueId = $customValues['id'];
    unset($customValues['id']);
    foreach ($customValues as $key => $customValue)
    {
        $customValue = str_replace(str_split('[^<>={}]*$'), '',$customValue);
        $isoLang = str_replace('name_', '', $key);
        $idLang = Language::getIdByIso($isoLang);
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'feature_value_lang` SET `value` = "'.pSQL($customValue).'" WHERE `id_feature_value`= '.(int) $featureValueId.' AND `id_lang` = '.(int) $idLang);
    }

    if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
    {
        $product = new Product((int) $productId);
        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
    }
}

/**
 * @param $featureValueIds
 * @param $productId
 */
function multipleFeaturesCustomValuesDelete($featureValueIds, $productId)
{
    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'feature_value_lang` WHERE `id_feature_value` IN ('.pInSQL($featureValueIds).')');
    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'feature_value` WHERE `id_feature_value` IN ('.pInSQL($featureValueIds).')');
    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE `id_feature_value` IN ('.pInSQL($featureValueIds).')');

    if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
    {
        $product = new Product((int) $productId);
        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
    }
}

/**
 * @param $featureId
 * @param $featureValueId
 * @param $productId
 * @param $idsLang
 */
function multipleFeaturesCustomValuesAdd($featureId, $productId, $idsLang)
{
    if (!empty($productId))
    {
        // pas de custom values + predifined values
        $sql = 'DELETE fp FROM `'._DB_PREFIX_.'feature_product` fp INNER JOIN `'
            ._DB_PREFIX_.'feature_value` fv ON fv.`id_feature_value` = fp.`id_feature_value`
            WHERE fp.`id_product`= '.$productId.' AND fp.`id_feature`='.$featureId.' AND fv.`custom` = 0;';
        Db::getInstance()->Execute($sql);

        // ajout dans feature_value
        $sql = 'INSERT INTO `'._DB_PREFIX_."feature_value` (id_feature,custom)
                VALUES ('".(int) $featureId."','1')";
        Db::getInstance()->Execute($sql);
        $featureValueId = Db::getInstance()->Insert_ID();

        // ajout dans feature_product

        $sql = 'INSERT INTO `'._DB_PREFIX_."feature_product` (id_feature,id_product,id_feature_value)
            VALUES ('".(int) $featureId."','".(int) $productId."','".(int) $featureValueId."')";
        Db::getInstance()->Execute($sql);
        addToHistory('cat_prop_multiplefeature', 'modification', 'feature_value_custom', $productId, (int) Configuration::get('PS_LANG_DEFAULT'), 'feature_product', $featureValueId, null, (int) SCI::getSelectedShop());

        // ajout dans feature_value_lang
        foreach ($idsLang as $idLang)
        {
            $sql = 'INSERT INTO `'._DB_PREFIX_."feature_value_lang` (id_feature_value,id_lang)
                VALUES ('".(int) $featureValueId."',".$idLang.')';
            Db::getInstance()->Execute($sql);
        }

        if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
        {
            $product = new Product((int) $productId);
            SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
        }
        exit(json_encode(array(
            'featureValueId' => $featureValueId,
        )));
    }
}

/**
 * @param $positionsArray
 * @param $featureId
 * @param $productId
 */
function multipleFeaturesCustomValuesPosition($positionsArray, $featureId, $productId)
{
    foreach ($positionsArray as $key => $featureValueId)
    {
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'feature_product` SET `position` = '.pSQL($key).' WHERE `id_feature`='.
            $featureId.' AND `id_feature_value`= '
            .$featureValueId.' AND id_product = '.(int)$productId);
    }

    if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
    {
        $product = new Product((int) $productId);
        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
    }
}
