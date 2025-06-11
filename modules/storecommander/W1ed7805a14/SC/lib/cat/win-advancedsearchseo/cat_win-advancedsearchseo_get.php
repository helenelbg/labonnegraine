<?php
$id_lang = (int) Tools::getValue('id_lang');
$filters = (string) Tools::getValue('filters', null);
$data = array();

function getFiltersConditions($filters = null)
{
    $condition = array();
    $filters = json_decode($filters, true);

    if (empty($filters))
    {
        return '';
    }

    foreach ($filters as $field => $filter)
    {
        $query = $filter['query'];
        switch ($query) {
            case '!!':
                $condition[] = '(`'.bqSQL($field).' IS NULL OR `'.bqSQL($field).'` = "")';
                break;
            default:
                if (!empty($query))
                {
                    $condition[] = '`'.bqSQL($field).'` LIKE "%'.bqSQL($query).'%"';
                }
        }
    }

    if (!empty($condition))
    {
        return ' AND '.implode(' AND ', $condition);
    }

    return '';
}

if ($id_lang)
{
    $filtersConditions = getFiltersConditions($filters);
    $advSearchModuleInstance = Module::getInstanceByName('pm_advancedsearch4');
    $sql = 'SELECT *
        FROM '._DB_PREFIX_.'pm_advancedsearch_seo_lang 
        WHERE id_lang = '.(int) $id_lang
        .$filtersConditions.
        ' ORDER BY id_seo ASC';
    if (empty($filtersConditions))
    {
        $sql .= ' LIMIT 5000';
    }
    $data = Db::getInstance()->executeS($sql);
}
$xml = array();
if ($data)
{
    foreach ($data as $row_data)
    {
        $row_xml = array();

        $row_xml[] = '<cell>'.(int) $row_data['id_seo'].'</cell>';
        $row_xml[] = '<cell><![CDATA['.$row_data['title'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row_data['description'].']]></cell>';
        if (version_compare($advSearchModuleInstance->version, '5.0.0', '>='))
        {
            $row_xml[] = '<cell><![CDATA['.$row_data['footer_description'].']]></cell>';
        }
        $row_xml[] = '<cell><![CDATA['.$row_data['seo_url'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row_data['meta_title'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row_data['meta_description'].']]></cell>';
        $row_xml[] = '<cell><![CDATA['.$row_data['meta_keywords'].']]></cell>';
        $xml[] = '<row id="'.(int) $row_data['id_seo'].'_'.(int) $row_data['id_lang'].'">'.implode("\r\n\t", $row_xml).'</row>';
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
                <param><![CDATA[#numeric_filter,#text_filter,#text_filter,<?php if (version_compare($advSearchModuleInstance->version, '5.0.0', '>=')) { ?>#text_filter,<?php } ?>#text_filter,#text_filter,#text_filter,#text_filter]]></param>
            </call>
        </afterInit>
        <column id="id_seo" width="60" type="ro" align="left" sort="int"><?php echo _l('ID SEO page'); ?></column>
        <column id="title" width="200" type="ed" align="left" sort="str"><?php echo _l('title'); ?></column>
        <column id="description" width="200" type="wysiwyg" align="left" sort="str"><?php echo _l('Description'); ?></column>
        <?php if (version_compare($advSearchModuleInstance->version, '5.0.0', '>=')) { ?>
            <column id="footer_description" width="200" type="wysiwyg" align="left" sort="str"><?php echo _l('Footer description'); ?></column>
        <?php } ?>
        <column id="seo_url" width="200" type="ed" align="left" sort="str"><?php echo _l('SEO url'); ?></column>
        <column id="meta_title" width="200" type="ed" align="left" sort="str"><?php echo _l('META title'); ?></column>
        <column id="meta_description" width="200" type="txttxt" align="left" sort="str"><?php echo _l('META description'); ?></column>
        <column id="meta_keywords" width="200" type="ed" align="left" sort="str"><?php echo _l('META keywords'); ?></column>
    </head>
    <?php
    echo $xml;
    ?>
</rows>