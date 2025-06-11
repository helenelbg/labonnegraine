<?php

$action = Tools::getValue('action');
$id_lang = (int) Tools::getValue('id_lang');
$languages = Language::getLanguages(!_s('CAT_PROD_LANGUAGE_ALL'));

$return = '';
switch ($action) {
    case 'group_feature':
        $sql = 'SELECT * FROM '._DB_PREFIX_.'feature_lang';
        $rows = Db::getInstance()->ExecuteS($sql);
        $names = array();
        foreach ($rows as $row)
        {
            $names[$row['id_feature']][$row['id_lang']]['name'] = $row['name'];
        }

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $features = Feature::getFeatures($id_lang, false);
        }
        else
        {
            $features = Feature::getFeatures($id_lang);
        }

        $return .= 'ID';
        foreach ($languages as $lang)
        {
            $return .= "\t"._l('Name').' '.strtoupper($lang['iso_code']);
        }
        $return .= "\n";

        foreach ($features as $row)
        {
            $return .= $row['id_feature'];
            foreach ($languages as $lang)
            {
                $return .= "\t".$names[$row['id_feature']][$lang['id_lang']]['name'];
            }
            $return .= "\n";
        }
        break;
    case 'feature_value':
        $sql = 'SELECT fvl.* 
                FROM '._DB_PREFIX_.'feature_value fv
                RIGHT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON fvl.id_feature_value = fv.id_feature_value
                WHERE fv.custom = 0 
                ORDER BY fvl.value';
        $feature_values = Db::getInstance()->ExecuteS($sql);
        $names = array();
        foreach ($feature_values as $row)
        {
            $names[$row['id_feature_value']][$row['id_lang']] = $row['value'];
        }

        $index_col = array();
        $index_col[] = 'ID';
        foreach ($languages as $lang)
        {
            $index_col[] = _l('Name').' '.strtoupper($lang['iso_code']);
        }
        $index_col = implode("\t", $index_col);

        $data_row = array();
        foreach ($names as $id_feature_value => $row)
        {
            $tmp = array();
            $tmp[] = $id_feature_value;
            foreach ($languages as $lang)
            {
                $tmp[] = $row[$lang['id_lang']];
            }
            $data_row[] = implode("\t", $tmp);
        }
        $data_row = implode("\n", $data_row);

        $return = implode("\n", array($index_col, $data_row));
        break;
    case 'group_attribute':
        $sql = 'SELECT * FROM '._DB_PREFIX_.'attribute_group_lang';
        $rows = Db::getInstance()->ExecuteS($sql);
        $names = array();
        foreach ($rows as $row)
        {
            $names[$row['id_attribute_group']][$row['id_lang']]['name'] = $row['name'];
            $names[$row['id_attribute_group']][$row['id_lang']]['public_name'] = $row['public_name'];
        }

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $groups = Db::getInstance()->executeS('
                SELECT DISTINCT agl.`name`, ag.*, agl.*
                FROM `'._DB_PREFIX_.'attribute_group` ag
                LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
                    ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int) $id_lang.')
                ORDER BY `position` ASC');
        }
        else
        {
            $groups = AttributeGroup::getAttributesGroups($id_lang);
        }

        $return .= 'ID';
        foreach ($languages as $lang)
        {
            $return .= "\t"._l('Name').' '.strtoupper($lang['iso_code']);
            $return .= "\t"._l('Public name').' '.strtoupper($lang['iso_code']);
        }
        $return .= "\n";

        foreach ($groups as $row)
        {
            $return .= $row['id_attribute_group'];
            foreach ($languages as $lang)
            {
                $return .= "\t".$names[$row['id_attribute_group']][$lang['id_lang']]['name'];
                $return .= "\t".$names[$row['id_attribute_group']][$lang['id_lang']]['public_name'];
            }
            $return .= "\n";
        }
        break;
    case 'attribute_value':
        $sql = 'SELECT al.*,a.*
                FROM '._DB_PREFIX_.'attribute a 
                RIGHT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute=a.id_attribute)';
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sql .= ' ORDER BY a.position ASC, al.name ASC ';
        }
        $rows = Db::getInstance()->ExecuteS($sql);
        $names = $attr = array();
        foreach ($rows as $row)
        {
            $attr[$row['id_attribute']] = $row['id_attribute'];
            $names[$row['id_attribute']][$row['id_lang']] = $row['name'];
        }

        $return .= 'ID';
        foreach ($languages as $lang)
        {
            $return .= "\t"._l('Name').' '.strtoupper($lang['iso_code']);
        }
        $return .= "\n";

        foreach ($attr as $id_attribute)
        {
            $return .= $id_attribute;
            foreach ($languages as $lang)
            {
                $return .= "\t".$names[$id_attribute][$lang['id_lang']];
            }
            $return .= "\n";
        }
        break;
}

echo rtrim($return);
