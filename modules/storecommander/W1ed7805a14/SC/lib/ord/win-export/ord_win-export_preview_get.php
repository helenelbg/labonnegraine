<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportOrders_ACTIVE') || (int)SC_ExportOrders_ACTIVE !== 1)
{
    exit;
}

$filterId = (int) Tools::getValue(ExportOrderFilter::$definition['primary']);

$orderFilter = new ExportOrderFilter($filterId);
$data = $orderFilter->getDisplayOrderFilter($sc_agent->id_lang);

if (empty($data))
{
    $msg = _l('No data corresponding to the selected filter');
    exitWithXmlMessageForGrid($msg);
}

try {
    $fillTaxResult = $orderFilter->fillTax(array_column($data, 'Order_Id'));
} catch (Exception $e) {
    $fillTaxResult = false;
}
if (!$fillTaxResult)
{
    $msg = _l('Something is wrong during filling taxes');
    exitWithXmlMessageForGrid($msg);
}

$numberFound = count($data);
$idLangFr = LanguageCore::getIdByIso("fr");
$lang_QC = ($sc_agent->id_lang==$idLangFr ? 2 : 1);

$columns = $data[0];
unset($columns['temp_id_order']);
$tmp = array();
foreach($columns as $alias => $void){
    $filter = "text_filter";
    $width = 150;
    $sort = 150;
    if(preg_match('#(_[iI][dD])|(_[iI][dD]_)|([iI][dD]_)#',$alias))
    {
        $filter = 'numeric_filter';
        $width = 50;
        $sort = 'int';
    }
    $tmp[$alias] = array(
        'title' => ExportOrderFields::getFieldsTranslation($alias, $lang_QC),
        'filter' => '#'.$filter,
        'width' => $width,
        'sort' => $sort,
    );
}
$columns = $tmp;

$xml = array();
if ($data)
{
    $data = array_slice($data, 0, ExportOrderFilter::$preview_limit);
    foreach ($data as $k => $row)
    {
        $row_xml = array();
        foreach($row as $alias => $value) {
            if(!$alias or !isset($columns[$alias])){
                $row_xml[] = '<cell></cell>';
                continue;
            }
            $columnInfo = $columns[$alias];
            switch($columnInfo['filter']) {
                case '#numeric_filter':
                    $row_xml[] = '<cell>' . (int)$value . '</cell>';
                    break;
                default:
                    $row_xml[] = '<cell><![CDATA[' . $value . ']]></cell>';
            }
        }
        $xml[] = '<row id="'.(int) $k.'">'.implode("\r\n\t", $row_xml).'</row>';
    }
}
$xml = implode("\r\n", $xml);

if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<rows id="0">
    <head>
        <afterInit>
            <call command="attachHeader">
                <param><![CDATA[<?php echo implode(',',array_column($columns,'filter')); ?>]]></param>
            </call>
        </afterInit>
        <?php foreach($columns as $alias => $column) { ?>
            <column id="<?php echo $alias; ?>" width="<?php echo $column['width']; ?>" type="ed" align="left" sort="<?php echo $column['sort']; ?>"><?php echo $column['title']; ?></column>
        <?php } ?>
    </head>
    <?php
    echo '<userdata name="numberFound">'.$numberFound.'</userdata>'."\n";
    echo $xml;
    ?>
</rows>