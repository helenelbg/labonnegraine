<?php

require_once dirname(__FILE__).'/all_win-gridseditorpro_function.php';
$action = Tools::getValue('action');
$type = str_replace('type_', '', Tools::getValue('type', 'products'));

$name_lang = (int) Tools::getValue('id_lang', 0);
$iso = 'en';
if (strtolower(Language::getIsoById($name_lang)) == 'fr')
{
    $iso = 'fr';
}

require dirname(__FILE__).'/../win-gridseditor/all_win-gridseditor_tools.php';

$types_list = array('products', 'combinations', 'combinationmultiproduct', 'customers', 'orders', 'productsort', 'msproduct', 'mscombination', 'image', 'propspeprice', 'winspeprice', 'proppackproduct','propsupplier', 'gmapartner', 'categories', 'cms');

if (!empty($type) && in_array($type, $types_list) && !empty($action))
{
    $file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';

    if ($type == 'products')
    {
        $type_temp = 'product';
    }
    elseif ($type == 'customers')
    {
        $type_temp = 'customer';
    }
    elseif ($type == 'orders')
    {
        $type_temp = 'order';
    }
    elseif ($type == 'combinations')
    {
        $type_temp = 'combination';
    }
    elseif ($type == 'combinationmultiproduct')
    {
        $type_temp = 'combinationmultiproduct';
    }
    elseif ($type == 'categories')
    {
        $type_temp = 'category';
    }
    else
    {
        $type_temp = $type;
    }

    if ($action == 'update')
    {
        $id = Tools::getValue('id');
        $field = Tools::getValue('field');
        $field_value = Tools::getValue('field_value', '');

        $update = true;

        if (empty($id))
        {
            $update = false;
        }

        if (empty($field))
        {
            $update = false;
        }

        if ($field == 'select_options')
        {
            $field = 'options';
        }

        if ($update)
        {
            // UPDATE FIELD
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($file);

            $field_value = cleanScript($field_value);

            $nodeFieldList = $dom->getElementsByTagname('field');
            foreach ($nodeFieldList as $nodeField)
            {
                $nodeName = $nodeField->getElementsByTagname('name')->item(0);
                if ($nodeName->nodeValue == $id)
                {
                    $nodeFieldValue = $nodeField->getElementsByTagname($field);
                    if ($nodeFieldValue->length > 0)
                    {
                        $updatedNode = $nodeFieldValue->item(0);
                        $updatedNode->nodeValue = '';
                        $v = $updatedNode->ownerDocument->createCDATASection($field_value);
                        $updatedNode->appendChild($v);
                    }
                    else
                    {
                        $newElement = $dom->createElement($field);
                        $v = $newElement->ownerDocument->createCDATASection($field_value);
                        $newElement->appendChild($v);

                        $nodeField->appendChild($newElement);
                    }
                }
            }
            $dom->save($file);

            $content = file_get_contents($file);
            $content = str_replace('<grids/>', '<grids></grids>', $content);
            $content = str_replace('<fields/>', '<fields></fields>', $content);
            file_put_contents($file, $content);
        }
    }
}
elseif (!empty($type) && $type == 'productexport' && !empty($action))
{
    $filename = getConfXmlName($type);
    if ($action == 'update')
    {
        $field = Tools::getValue('field');
        $field_value = Tools::getValue('field_value', '');
        $field_value = cleanScript($field_value);

        $xml_conf = simplexml_load_file($filename);

        if (!empty($field))
        {
            $xml_conf->{$field} = $field_value;
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<exportcsv>
    <definition><![CDATA['.(string) $xml_conf->definition.']]></definition>
    <definitionForOptionTwoField><![CDATA['.(string) $xml_conf->definitionForOptionTwoField.']]></definitionForOptionTwoField>
    <definitionLang><![CDATA['.(string) $xml_conf->definitionLang.']]></definitionLang>
    <exportProcessProduct><![CDATA['.(string) $xml_conf->exportProcessProduct.']]></exportProcessProduct>
    <exportMappingPrepareGrid><![CDATA['.(string) $xml_conf->exportMappingPrepareGrid.']]></exportMappingPrepareGrid>
    <exportMappingCheckGrid><![CDATA['.(string) $xml_conf->exportMappingCheckGrid.']]></exportMappingCheckGrid>
    <exportMappingFillCombo><![CDATA['.(string) $xml_conf->exportMappingFillCombo.']]></exportMappingFillCombo>
    <addInCombiFields><![CDATA['.(string) $xml_conf->addInCombiFields.']]></addInCombiFields>
    <exportProcessInitRowVars><![CDATA['.(string) $xml_conf->exportProcessInitRowVars.']]></exportProcessInitRowVars>
</exportcsv>';
        file_put_contents($filename, $content);
    }
}
elseif (!empty($type) && $type == 'productimport' && !empty($action))
{
    $filename = getConfXmlName($type);
    if ($action == 'update')
    {
        $field = Tools::getValue('field');
        $field_value = Tools::getValue('field_value', '');
        $field_value = cleanScript($field_value);

        $xml_conf = simplexml_load_file($filename);

        if (!empty($field))
        {
            $xml_conf->{$field} = $field_value;
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<importcsv>
    <definition><![CDATA['.(string) $xml_conf->definition.']]></definition>
    <definitionForLangField><![CDATA['.(string) $xml_conf->definitionForLangField.']]></definitionForLangField>
    <definitionForOptionField><![CDATA['.(string) $xml_conf->definitionForOptionField.']]></definitionForOptionField>
    <importProcessProduct><![CDATA['.(string) $xml_conf->importProcessProduct.']]></importProcessProduct>
    <importProcessCombination><![CDATA['.(string) $xml_conf->importProcessCombination.']]></importProcessCombination>
    <importMappingLoadMappingOption><![CDATA['.(string) $xml_conf->importMappingLoadMappingOption.']]></importMappingLoadMappingOption>
    <importMappingPrepareGrid><![CDATA['.(string) $xml_conf->importMappingPrepareGrid.']]></importMappingPrepareGrid>
    <importMappingCheckGrid><![CDATA['.(string) $xml_conf->importMappingCheckGrid.']]></importMappingCheckGrid>
    <importMappingFillCombo><![CDATA['.(string) $xml_conf->importMappingFillCombo.']]></importMappingFillCombo>
    <importProcessIdentifier><![CDATA['.(string) $xml_conf->importProcessIdentifier.']]></importProcessIdentifier>
    <importProcessInitRowVars><![CDATA['.(string) $xml_conf->importProcessInitRowVars.']]></importProcessInitRowVars>
    <importProcessAfterCreateAll><![CDATA['.(string) $xml_conf->importProcessAfterCreateAll.']]></importProcessAfterCreateAll>
    <importProcessImageUpdate><![CDATA['.(string) $xml_conf->importProcessImageUpdate.']]></importProcessImageUpdate>
</importcsv>';
        file_put_contents($filename, $content);
    }
}
elseif (!empty($type) && $type == 'customersimport' && !empty($action))
{
    $filename = getConfXmlName($type);
    if ($action == 'update')
    {
        $field = Tools::getValue('field');
        $field_value = Tools::getValue('field_value', '');
        $field_value = cleanScript($field_value);

        $xml_conf = simplexml_load_file($filename);

        if (!empty($field))
        {
            $xml_conf->{$field} = $field_value;
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<importcsv>
    <definition><![CDATA['.(string) $xml_conf->definition.']]></definition>
    <importProcessCustomer><![CDATA['.(string) $xml_conf->importProcessCustomer.']]></importProcessCustomer>
    <importProcessCustomerAfter><![CDATA['.(string) $xml_conf->importProcessCustomerAfter.']]></importProcessCustomerAfter>
    <importProcessAfterCreateAll><![CDATA['.(string) $xml_conf->importProcessAfterCreateAll.']]></importProcessAfterCreateAll>
    <importMappingPrepareGrid><![CDATA['.(string) $xml_conf->importMappingPrepareGrid.']]></importMappingPrepareGrid>
    <importMappingCheckGrid><![CDATA['.(string) $xml_conf->importMappingCheckGrid.']]></importMappingCheckGrid>
    <importMappingFillCombo><![CDATA['.(string) $xml_conf->importMappingFillCombo.']]></importMappingFillCombo>
</importcsv>';
        file_put_contents($filename, $content);
    }
}
exit('OK');
