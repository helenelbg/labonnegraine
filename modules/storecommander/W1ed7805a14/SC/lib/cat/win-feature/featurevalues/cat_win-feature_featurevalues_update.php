<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_feature_value = (int) Tools::getValue('gr_id', 0);
    $id_feature = (int) Tools::getValue('id_feature');
    $action = Tools::getValue('action');
    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'inserted')
    {
        $newFV = new FeatureValue();
        $newFV->id_feature = $id_feature;
        if (version_compare(_PS_VERSION_, '1.5.0', '>='))
        {
            foreach ($languages as $lang)
            {
                $newFV->value[$lang['id_lang']] = 'new';
            }
        }
        $newFV->save();
        $newId = $newFV->id;
        $action = 'insert';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $fields = array();
        $fields_lang = array();
        $idlangByISO = array();
        $todo = array();
        $todo_lang = array();
        foreach ($languages as $lang)
        {
            $fields_lang[] = 'value¤'.$lang['iso_code'];
            $idlangByISO[$lang['iso_code']] = $lang['id_lang'];
        }
        foreach ($fields as $field)
        {
            if (isset($_GET[$field]) || isset($_POST[$field]))
            {
                $todo[] = '`'.bqSQL($field)."`='".psql(html_entity_decode(Tools::getValue($field)))."'";
                addToHistory('feature_value', 'modification', $field, (int) $id_feature_value, $id_lang, _DB_PREFIX_.'feature_value', psql(Tools::getValue($field)));
            }
        }
        foreach ($fields_lang as $field)
        {
            if (isset($_GET[$field]) || isset($_POST[$field]))
            {
                $tmp = explode('¤', $field);
                $fname = $tmp[0];
                $flang = $tmp[1];
                $todo_lang[] = array('`'.bqSQL($fname)."`='".psql(trim(html_entity_decode(Tools::getValue($field))), true)."'", $idlangByISO[$flang], psql(html_entity_decode(Tools::getValue($field))));
                addToHistory('feature_value', 'modification', $fname, (int) $id_feature_value, $idlangByISO[$flang], _DB_PREFIX_.'feature_value_lang', psql(Tools::getValue($field)));
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'feature_value SET '.join(' , ', $todo).' WHERE id_feature_value='.(int) $id_feature_value;
            Db::getInstance()->Execute($sql);
        }
        if (count($todo_lang))
        {
            foreach ($todo_lang as $tlang)
            {
                $sqltest = 'SELECT * FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value='.(int) $id_feature_value.' AND id_lang='.(int) $tlang[1];
                $test = Db::getInstance()->ExecuteS($sqltest);
                if (count($test) == 0)
                {
                    $sqlinsert = 'INSERT INTO '._DB_PREFIX_."feature_value_lang VALUES (" .(int) $id_feature_value . "," .(int) $tlang[1] . ",'".$tlang[2]."')";
                    Db::getInstance()->Execute($sqlinsert);
                }
                else
                {
                    $sql2 = 'UPDATE '._DB_PREFIX_.'feature_value_lang SET '.$tlang[0].' WHERE id_feature_value='.(int) $id_feature_value.' AND id_lang='.(int) $tlang[1];
                    Db::getInstance()->Execute($sql2);
                }
            }
        }

        // PM Cache
        if (!empty($id_feature_value))
        {
            ExtensionPMCM::clearFromIdsFeatureValue($id_feature_value);
        }

        $newId = Tools::getValue('gr_id');
        $action = 'update';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'deleted')
    {
        $FV = new FeatureValue($id_feature_value, $id_lang);
        $FV->delete();

        // PM Cache
        if (!empty($id_feature_value))
        {
            ExtensionPMCM::clearFromIdsFeatureValue($id_feature_value);
        }

        $newId = Tools::getValue('gr_id');
        $action = 'delete';
    }
    elseif (!empty($action) && trim($action) == 'merge')
    {
        $featlist = explode(',', Tools::getValue('featlist', 0));
        sort($featlist);
        $id_feature = array_shift($featlist);
        foreach ($featlist as $id)
        {
            $sql = 'UPDATE '._DB_PREFIX_.'feature_product SET id_feature_value='.(int) $id_feature.' WHERE id_feature_value='.(int) $id;
            Db::getInstance()->Execute($sql);
            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value='.(int) $id;
            Db::getInstance()->Execute($sql);
            $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id;
            Db::getInstance()->Execute($sql);
        }
    }

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<data>';
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".$newId."'/>";
    echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
    echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
    echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
    echo '</data>';
