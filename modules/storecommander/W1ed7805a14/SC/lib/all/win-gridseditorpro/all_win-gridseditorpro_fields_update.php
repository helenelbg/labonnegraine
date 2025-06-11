<?php

$action = Tools::getValue('action');
$type = str_replace('type_', '', Tools::getValue('type', 'products'));

$name_lang = (int) Tools::getValue('id_lang', 0);
$iso = 'en';
if (strtolower(Language::getIsoById($name_lang)) == 'fr')
{
    $iso = 'fr';
}

require_once dirname(__FILE__).'/all_win-gridseditorpro_function.php';
require dirname(__FILE__).'/../win-gridseditor/all_win-gridseditor_tools.php';

$types_list = array('products', 'combinations', 'combinationmultiproduct', 'customers', 'orders', 'productsort', 'msproduct', 'mscombination', 'image', 'propspeprice', 'winspeprice','proppackproduct', 'propsupplier', 'gmapartner', 'categories', 'cms');

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

    // CREATE FILE IF NOT EXIST
    if (!file_exists($file))
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
<extension>
  <xml_version><![CDATA['.SC_EXTENSION_VERSION.']]></xml_version>
  <grids></grids>
  <fields></fields>
</extension>';
        file_put_contents($file, $content);
    }

    if ($action == 'update')
    {
        $name = Tools::getValue('name');
        $field_updated = Tools::getValue('field', '');
        $value_updated = Tools::getValue('value', '');
        $newvalue = Tools::getValue('newvalue', '');

        $update = true;

        if (empty($name))
        {
            $update = false;
        }
        if (empty($field_updated))
        {
            $update = false;
        }
        if (empty($value_updated))
        {
            $update = false;
        }

        if (in_array($value_updated, array('celltype', 'name')) && empty($newvalue))
        {
            $update = false;
        }

        if ($value_updated == 'refreshcombi')
        {
            $value_updated = 'forceUpdateCombinationsGrid';
        }

        if ($update)
        {
            // UPDATE FIELD
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($file);

            $nodeFieldList = $dom->getElementsByTagname('field');
            foreach ($nodeFieldList as $nodeField)
            {
                $nodeName = $nodeField->getElementsByTagname('name')->item(0);
                if ($nodeName->nodeValue == $field_updated)
                {
                    if ($value_updated == 'name')
                    {
                        $updatedNode = $nodeField->getElementsByTagname('fr')->item(0);
                        $updatedNode->nodeValue = '';
                        $v = $updatedNode->ownerDocument->createCDATASection($newvalue);
                        $updatedNode->appendChild($v);

                        $updatedNodeBIS = $nodeField->getElementsByTagname('en')->item(0);
                        $updatedNodeBIS->nodeValue = '';
                        $v = $updatedNodeBIS->ownerDocument->createCDATASection($newvalue);
                        $updatedNodeBIS->appendChild($v);
                    }
                    else
                    {
                        $updatedNode = $nodeField->getElementsByTagname($value_updated)->item(0);
                        $updatedNode->nodeValue = '';
                        $v = $updatedNode->ownerDocument->createCDATASection($newvalue);
                        $updatedNode->appendChild($v);
                    }
                    $in_file = true;
                }
            }
            $dom->save($file);

            $content = file_get_contents($file);
            $content = str_replace('<grids/>', '<grids></grids>', $content);
            $content = str_replace('<fields/>', '<fields></fields>', $content);
            file_put_contents($file, $content);
        }
    }
    elseif ($action == 'insert')
    {
        $name = Tools::getValue('name');
        if (!empty($name))
        {
            $content = file_get_contents($file);

            // CHECK AND UPDATE NAME
            $name = testNameField($name, $content);

            // ADD NEW GRID
            addNewField($type, $content, $name);
        }
    }
    elseif ($action == 'delete')
    {
        $ids = Tools::getValue('ids', '');
        if (!empty($ids))
        {
            $ids = explode(',', $ids);

            // UPDATE LIST
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->load($file);

            $nodeFieldList = $dom->getElementsByTagname('field');
            foreach ($nodeFieldList as $nodeField)
            {
                $nodeName = $nodeField->getElementsByTagname('name')->item(0);
                if (in_array($nodeName->nodeValue, $ids))
                {
                    $nodeField->parentNode->removeChild($nodeField);
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
        $fields = Tools::getValue('fields', array());

        $values = array(
            'definition' => '',
            'definitionLang' => '',
            'exportProcessProduct' => '',
            'addInCombiFields' => '',
            'exportProcessInitRowVars' => '',
        );

        if (file_exists($filename))
        {
            $xml_conf = simplexml_load_file($filename);
            foreach ($values as $key => $val)
            {
                $values[$key] = $xml_conf->{$key};
            }
        }

        if (is_array($fields))
        {
            $values['definition'] = '';
            foreach ($fields as $field)
            {
                $values['definition'] .= '$array[\''.str_replace("'", "\'", $field[1]).'\']="'.$field[0].'";'."\n";
            }
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<exportcsv>
    <definition><![CDATA['.(string) $values['definition'].']]></definition>
    <definitionLang><![CDATA['.(string) $values['definitionLang'].']]></definitionLang>
    <exportProcessProduct><![CDATA['.(string) $values['exportProcessProduct'].']]></exportProcessProduct>
    <addInCombiFields><![CDATA['.(string) $values['addInCombiFields'].']]></addInCombiFields>
    <exportProcessInitRowVars><![CDATA['.(string) $values['exportProcessInitRowVars'].']]></exportProcessInitRowVars>
</exportcsv>';
        file_put_contents($filename, $content);
    }
}
elseif (!empty($type) && $type == 'productimport' && !empty($action))
{
    $filename = getConfXmlName($type);
    if ($action == 'update')
    {
        $fields = Tools::getValue('fields', array());

        $values = array(
            'definition' => '',
            'definitionForLangField' => '',
            'definitionForOptionField' => '',
            'importProcessProduct' => '',
            'importProcessCombination' => '',
            'importMappingLoadMappingOption' => '',
            'importMappingPrepareGrid' => '',
            'importMappingCheckGrid' => '',
            'importMappingFillCombo' => '',
            'importProcessIdentifier' => '',
            'importProcessInitRowVars' => '',
            'importProcessImageUpdate' => '',
            'importProcessAfterCreateAll' => '',
        );

        if (file_exists($filename))
        {
            $xml_conf = simplexml_load_file($filename);
            foreach ($values as $key => $val)
            {
                $values[$key] = $xml_conf->{$key};
            }
        }

        if (is_array($fields))
        {
            $values['definition'] = '';
            foreach ($fields as $field)
            {
                $values['definition'] .= '$array[\''.str_replace("'", "\'", $field[1]).'\']="comboDBField.put(\''.$field[0].'\',\''.str_replace("'", "\'", $field[1]).'\');";'."\n";
            }
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<importcsv>
    <definition><![CDATA['.(string) $values['definition'].']]></definition>
    <definitionForLangField><![CDATA['.(string) $values['definitionForLangField'].']]></definitionForLangField>
    <definitionForOptionField><![CDATA['.(string) $values['definitionForOptionField'].']]></definitionForOptionField>
    <importProcessProduct><![CDATA['.(string) $values['importProcessProduct'].']]></importProcessProduct>
    <importProcessCombination><![CDATA['.(string) $values['importProcessCombination'].']]></importProcessCombination>
    <importMappingLoadMappingOption><![CDATA['.(string) $values['importMappingLoadMappingOption'].']]></importMappingLoadMappingOption>
    <importMappingPrepareGrid><![CDATA['.(string) $values['importMappingPrepareGrid'].']]></importMappingPrepareGrid>
    <importMappingCheckGrid><![CDATA['.(string) $values['importMappingCheckGrid'].']]></importMappingCheckGrid>
    <importMappingFillCombo><![CDATA['.(string) $values['importMappingFillCombo'].']]></importMappingFillCombo>
    <importProcessIdentifier><![CDATA['.(string) $values['importProcessIdentifier'].']]></importProcessIdentifier>
    <importProcessInitRowVars><![CDATA['.(string) $values['importProcessInitRowVars'].']]></importProcessInitRowVars>
    <importProcessAfterCreateAll><![CDATA['.(string) $values['importProcessAfterCreateAll'].']]></importProcessAfterCreateAll>
    <importProcessImageUpdate><![CDATA['.(string) $values['importProcessImageUpdate'].']]></importProcessImageUpdate>
</importcsv>';
        file_put_contents($filename, $content);
    }
}
elseif (!empty($type) && $type == 'customersimport' && !empty($action))
{
    $filename = getConfXmlName($type);
    if ($action == 'update')
    {
        $fields = Tools::getValue('fields', array());

        $values = array(
            'definition' => '',
            'importProcessCustomer' => '',
            'importProcessCustomerAfter' => '',
            'importProcessAfterCreateAll' => '',
            'importMappingPrepareGrid' => '',
            'importMappingCheckGrid' => '',
            'importMappingFillCombo' => '',
        );

        if (file_exists($filename))
        {
            $xml_conf = simplexml_load_file($filename);
            foreach ($values as $key => $val)
            {
                $values[$key] = $xml_conf->{$key};
            }
        }

        if (is_array($fields))
        {
            $values['definition'] = '';
            foreach ($fields as $field)
            {
                $values['definition'] .= '$array[\''.str_replace("'", "\'", $field[1]).'\']="comboDBField.put(\''.$field[0].'\',\''.str_replace("'", "\'", $field[1]).'\');";'."\n";
            }
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>
<importcsv>
    <definition><![CDATA['.(string) $values['definition'].']]></definition>
    <importProcessCustomer><![CDATA['.(string) $values['importProcessCustomer'].']]></importProcessCustomer>
    <importProcessCustomerAfter><![CDATA['.(string) $values['importProcessCustomerAfter'].']]></importProcessCustomerAfter>
    <importProcessAfterCreateAll><![CDATA['.(string) $values['importProcessAfterCreateAll'].']]></importProcessAfterCreateAll>
    <importMappingPrepareGrid><![CDATA['.(string) $values['importMappingPrepareGrid'].']]></importMappingPrepareGrid>
    <importMappingCheckGrid><![CDATA['.(string) $values['importMappingCheckGrid'].']]></importMappingCheckGrid>
    <importMappingFillCombo><![CDATA['.(string) $values['importMappingFillCombo'].']]></importMappingFillCombo>
</importcsv>';
        file_put_contents($filename, $content);
    }
}
