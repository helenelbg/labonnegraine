<?php
if (!defined('STORE_COMMANDER')) {
    exit;
}
global $sc_agent;
$attributeGroups = AttributeGroup::getAttributesGroups($sc_agent->getIdLang());
$attributeGroups = array_column($attributeGroups, null, 'name');
uksort($attributeGroups, function ($a, $b)
{
    return strnatcmp(Tools::replaceAccentedChars(strtolower($a)), Tools::replaceAccentedChars(strtolower($b)));
});
?>

<rows>
 <head>
    <column id="id" width="50" type="ro" align="left" color="white" sort="int"><?php echo _l('Id'); ?></column>
    <column id="name" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
    <column id="select" type="ro" align="left"></column>
    <afterInit>
     <call command="attachHeader">
         <param>#numeric_filter,#text_filter,</param>
     </call>
    </afterInit>
 </head>
    <?php foreach($attributeGroups as $attributeGroup){ ?>
        <row id="<?php echo $attributeGroup['id_attribute_group']?>">
             <cell><![CDATA[<?php echo $attributeGroup['id_attribute_group']?>]]></cell>
             <cell><![CDATA[<?php echo $attributeGroup['name']?>]]></cell>
            <cell></cell>
        </row>
    <?php } ?>
</rows>
