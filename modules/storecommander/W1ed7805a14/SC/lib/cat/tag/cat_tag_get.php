<?php

    $tagsFilter = (bool) Tools::getValue('tagsFilter', false);
    $id_category = Tools::getValue('id_category');
    $default_id_lang = Configuration::get('PS_LANG_DEFAULT');
    $sqlLang = 'SELECT `iso_code`,`id_lang` FROM `'._DB_PREFIX_.'lang`';
    $listLang = Db::getInstance()->ExecuteS($sqlLang);
    $filter_params = Tools::getValue('filter_params', '');
    $langOption = '';
    foreach ($listLang as $list)
    {
        $langOption .= '<option value="'.$list['id_lang'].'">'.$list['iso_code'].'</option>';
    }

    function getTags()
    {
        global $tagsFilter,$id_category,$sql,$nblanguages,$filter_params;
        if ($tagsFilter)
        {
            $sql = 'SELECT t.id_tag,t.name,t.id_lang
                    FROM '._DB_PREFIX_.'product_tag pt
                    LEFT JOIN '._DB_PREFIX_.'tag t ON (pt.id_tag=t.id_tag)
                    LEFT JOIN '._DB_PREFIX_.'lang l ON (t.id_lang=l.id_lang)
                    WHERE pt.id_product IN (SELECT cp.id_product FROM '._DB_PREFIX_.'category_product cp WHERE cp.id_category='.(int) $id_category.')
                    AND t.id_tag > 0
                    GROUP BY t.id_tag
                    ORDER BY t.name 
                    LIMIT '.(int) _s('CAT_PROPERTIES_TAGS_LIMIT');
        }
        else
        {
            $sql = 'SELECT id_tag,name,id_lang 
                    FROM '._DB_PREFIX_.'tag';
            if (!empty($filter_params))
            {
                $filter_params = explode('|||', $filter_params);
                $filter_value = $filter_params[1];
                if (!empty($filter_value))
                {
                    $sql .= ' WHERE name LIKE "%'.pSQL($filter_value).'%"';
                }
            }
            $sql .= ' GROUP BY id_tag
                    ORDER BY name';
            if (empty($filter_value))
            {
                $sql .= ' LIMIT '.(int) _s('CAT_PROPERTIES_TAGS_LIMIT');
            }
        }
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            echo '<row id="'.$row['id_tag'].'">';
            echo '<cell>'.$row['id_tag'].'</cell>';
            echo '<cell>0</cell>';
            if ($nblanguages > 1)
            {
                echo '<cell>'.$row['id_lang'].'</cell>';
            }
            echo '<cell><![CDATA['.$row['name'].']]></cell>';
            echo '</row>';
        }
    }

    //XML HEADER
    //include XML Header (as response will be in xml format)

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rows parent="0">
<head>
<beforeInit>
<?php
    if ($nblanguages == 1)
    {
        echo '<call command="attachHeader"><param><![CDATA[#text_filter,#select_filter,#text_filter]]></param></call>';
    }
    else
    {
        echo '<call command="attachHeader"><param><![CDATA[#text_filter,,#select_filter_strict,#text_filter]]></param></call>';
    }
?>
</beforeInit>
<column id="id_tag" width="50" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<column id="used" width="50" type="ch" align="center" sort="str"><?php echo _l('Used'); ?></column>
<?php
    if ($nblanguages > 1)
    {
        echo '<column id="lang" width="50" type="coro" align="center" sort="str">'._l('Lang').$langOption.'</column>';
    }
?>
<column id="name" width="200" type="ed" align="left" sort="str"><?php echo _l('Name'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_tag').'</userdata>'."\n";
    getTags();
?>
</rows>