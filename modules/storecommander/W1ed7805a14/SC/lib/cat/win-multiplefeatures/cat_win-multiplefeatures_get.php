<?php
//vars
$featureId = (int) Tools::getValue('featureId');
$isoList = array();
foreach ($languages as $r_lang)
{
    $isoList[] = strtolower($r_lang['iso_code']);
}
// pour pm_multipleFeatures ou dev spécifique permettant de gérer la position des features
$hasPosition = isField('position', 'feature_product') ? true : false;
// sql query
$featureValues = getFeaturesFromDb($featureId, $hasPosition);
//header
$contentType = stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') ? 'application/xhtml+xml' : 'text/xml';
header('Content-type: '.$contentType);
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
    <rows>
        <head>
            <column id="id" width="40" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
            <?php echo getNameColumnForEachLang($isoList); ?>
            <afterInit>
                <call command="enableHeaderMenu"></call>
                <call command="attachHeader"><param><![CDATA[<?php echo getGridFilter($isoList); ?>]]></param></call>
            </afterInit>
        </head>
        <?php echo getXmlContent($featureValues); ?>
    </rows>



<?php
/**
 * @param $featureValues
 *
 * @return string
 */
function getXmlContent($featureValues)
{
    $xml = '';
    $valueCells = array();
    foreach ($featureValues as $values)
    {
        if (!isset($valueCells[$values['id_feature_value']]))
        {
            $valueCells[$values['id_feature_value']] = '';
        }
        $valueCells[$values['id_feature_value']] .= "<cell><![CDATA[{$values['value']}]]></cell>";
    }
    foreach ($valueCells as $key => $valueCell)
    {
        $xml .= <<<ROW
<row id="{$key}">
<cell><![CDATA[{$key}]]></cell>
{$valueCell}
</row>
ROW;
    }

    return $xml;
}

/**
 * @param $isoList
 *
 * @return string
 */
function getNameColumnForEachLang($isoList)
{
    $xml = '';
    foreach ($isoList as $iso)
    {
        $name = _l('Name').' ('._l($iso).')';
        $xml .= <<<XML
<column id="name_{$iso}" width="200" type="edtxt" align="left" sort="str">{$name}</column>
XML;
    }

    return $xml;
}

/**
 * @param $isoList
 *
 * @return string
 */
function getGridFilter($isoList)
{
    $gridFilter = array('#text_filter');
    foreach ($isoList as $iso)
    {
        $gridFilter[] = '#select_filter';
    }

    return implode(',', $gridFilter);
}

/**
 * @param $featureId
 * @param $hasPosition
 *
 * @return array|bool|mysqli_result|PDOStatement|resource|null
 *
 * @throws PrestaShopDatabaseException
 */
function getFeaturesFromDb($featureId, $hasPosition)
{
    $sqlSelect = 'SELECT v.id_feature_value, vl.value, vl.id_lang, l.* ';
    $sqlFrom = 'FROM `'._DB_PREFIX_.'feature_value` v ';
    $sqlJoin = 'INNER JOIN `'._DB_PREFIX_.'feature_value_lang` vl ON (v.`id_feature_value` = vl.`id_feature_value`) ';
    $sqlJoin .= 'LEFT JOIN '._DB_PREFIX_.'feature_product fp ON fp.id_feature_value = v.id_feature_value ';
    $sqlJoin .= 'LEFT JOIN '._DB_PREFIX_.'lang l ON l.id_lang = vl.id_lang ';
    $sqlWhere = 'WHERE v.`id_feature` = '.(int) $featureId.' AND v.`custom` = 1 ';
    $sqlOrder = 'ORDER BY '.($hasPosition ? 'fp.position, ' : '').'l.id_lang ASC ';
    addSqlFilter($sqlJoin, $sqlWhere).' ';
    $sql = $sqlSelect.$sqlFrom.$sqlJoin.$sqlWhere.$sqlOrder;

    return Db::getInstance()->ExecuteS($sql);
}

/**
 * @param $filter
 *
 * @return string
 */
function addSqlFilter(&$sqlJoin, &$sqlWhere)
{
    $productsIds = Tools::getValue('productIds', null);
    if ($productsIds)
    {
        $sqlWhere .= 'AND fp.id_product IN ('.pInSQL($productsIds).') ';
    }
}
