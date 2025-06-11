<?php

$action = Tools::getValue('action');

if (isset($action) && $action)
{
    switch ($action) {
        case 'mapping_saveas':
            $filename = str_replace('.map.xml', '', Tools::getValue('filename'));
            $mapping_list = urldecode(Tools::getValue('mapping'));
            $mapping_list = json_decode($mapping_list, true);
            $xml_content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<mapping version="'.SC_EXPORT_VERSION.'">'."\n";
            $contentArray = array();
            foreach ($mapping_list as $row_id => $mapping)
            {
                $contentArray[$row_id][] = '<field>';
                foreach ($mapping as $field => $value)
                {
                    $contentArray[$row_id][] = '<'.$field.'><![CDATA['.$value.']]></'.$field.'>';
                }
                $contentArray[$row_id][] = '</field>'."\n";
                $contentArray[$row_id] = implode('', $contentArray[$row_id]);
            }
            ksort($contentArray);

            $xml_content .= implode('', $contentArray).'</mapping>';
            file_put_contents(SC_TOOLS_DIR.'cat_export/'.$filename.'.map.xml', $xml_content);
            echo _l('Data saved!');
            break;
        case 'mapping_delete':
            $filename = str_replace('.map.xml', '', Tools::getValue('filename'));
            @unlink(SC_TOOLS_DIR.'cat_export/'.$filename.'.map.xml');
            echo _l('File deleted');
            break;
    }
}
