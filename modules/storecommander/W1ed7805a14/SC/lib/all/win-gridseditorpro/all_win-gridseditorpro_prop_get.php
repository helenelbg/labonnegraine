<?php
require_once dirname(__FILE__).'/all_win-gridseditorpro_function.php';

    $property = Tools::getValue('prop', '');
    $field = Tools::getValue('field', '');
    $type = str_replace('type_', '', Tools::getValue('type', 'products'));

    $filename = getConfXmlName($type);

?><textarea id="advproperty" wrap="off" style="width:99%;height:99%;-moz-tab-size:1; -o-tab-size:1; tab-size:1; "><?php
    if (file_exists($filename))
    {
        if ($type == 'productimport' || $type == 'productexport' || $type == 'customersimport')
        {
            $xml_conf = simplexml_load_file($filename);
            echo (string) $xml_conf->{$property};
        }
        else
        {
            if ($property == 'select_options')
            {
                $property = 'options';
            }

            $grids_xml_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml');
            foreach ($grids_xml_conf->fields->field as $f)
            {
                if ((string) $f->name == $field)
                {
                    echo (string) $f->{$property};
                }
            }
        }
    }
?></textarea>

<script src="<?php echo SC_JQUERY; ?>" type="text/javascript"></script>
<script>
function getFieldValue()
{
    return $("#advproperty").val();
}
</script>
