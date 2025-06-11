<?php
$main_selected_id_lang = (int) Tools::getValue('main_selected_id_lang');
$page_ids = (string) Tools::getValue('page_ids', null);
$data = array();
if ($page_ids)
{
    $page_ids = preg_replace('#(_[0-9])#', '', $page_ids); ## suppression de l'id_lang
    $advSearchModuleInstance = Module::getInstanceByName('pm_advancedsearch4');
    $sql = 'SELECT pm.*, l.id_lang, UPPER(l.iso_code) as iso_lang
            FROM '._DB_PREFIX_.'pm_advancedsearch_seo_lang pm 
            LEFT JOIN '._DB_PREFIX_.'lang l ON l.id_lang = pm.id_lang
            WHERE pm.id_seo IN ('.pInSQL($page_ids).') 
            AND l.id_lang != '.$main_selected_id_lang.'
            ORDER BY pm.id_seo ASC, l.iso_code';
    $data = Db::getInstance()->executeS($sql);
}
$xml = array();
if ($data)
{
    foreach ($data as $row_data)
    {
        $row_xml = array();

        $row_xml[] = '<cell>'.(int) $row_data['id_seo'].'</cell>';
        $row_xml[] = '<cell><![CDATA['.$row_data['iso_lang'].']]></cell>';
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
                <param><![CDATA[#numeric_filter,#select_filter,#text_filter,#text_filter,<?php if (version_compare($advSearchModuleInstance->version, '5.0.0', '>=')) { ?>#text_filter,<?php } ?>#text_filter,#text_filter,#text_filter,#text_filter]]></param>
            </call>
        </afterInit>
        <column id="id_seo" width="60" type="ro" align="left" sort="int"><?php echo _l('ID SEO page'); ?></column>
        <column id="iso_lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang'); ?></column>
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