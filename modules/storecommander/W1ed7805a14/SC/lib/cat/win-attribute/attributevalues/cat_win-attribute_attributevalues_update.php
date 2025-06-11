<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_attribute = (int) Tools::getValue('gr_id', 0);
    $id_attribute_group = (int) Tools::getValue('id_attribute_group');
    $iscolor = (int) Tools::getValue('iscolor');
    $action = Tools::getValue('action', 0);

    if (!empty($action) && $action == 'position')
    {
        $todo = array();
        $positions = (string) Tools::getValue('positions', null);
        if ($positions)
        {
            $row = explode(',', $positions);
            foreach ($row as $position => $id_attribute)
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'attribute SET position='.(int) $position.' WHERE id_attribute='.(int) $id_attribute);
            }
        }
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'inserted')
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>='))
        {
            $newattr = new ProductAttribute();
        }
        else
        {
            $newattr = new Attribute();
        }
        $newattr->id_attribute_group = $id_attribute_group;
        $newattr->color = '#000000';
        if (version_compare(_PS_VERSION_, '1.5.0', '>='))
        {
            foreach ($languages as $lang)
            {
                $newattr->name[$lang['id_lang']] = 'new';
            }
            $newattr->id_shop_list = Shop::getShops(true, null, true);
        }
        $newattr->save();
        $newId = $newattr->id;
        $action = 'insert';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'duplicated')
    {
        $groups = explode(',', Tools::getValue('groups', ''));
        foreach ($groups as $id_group)
        {
            if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
            {
                $last_position = (int) Db::getInstance()->getValue('SELECT DISTINCT position FROM '._DB_PREFIX_.'attribute_group ORDER BY position DESC');
                $new_position = $last_position + 1;
                $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group (is_color_group,position) VALUES (0, '.(int) $new_position.')';
            }
            else
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group (is_color_group) VALUES (0)';
            }
            Db::getInstance()->Execute($sql);
            $newGroupID = Db::getInstance()->Insert_ID();
            $sql = '
            SELECT * FROM '._DB_PREFIX_.'attribute_group ag
            LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group=ag.id_attribute_group)
            WHERE ag.id_attribute_group='.(int) $id_group;
            $grouplang = Db::getInstance()->ExecuteS($sql);
            $k = 0;
            foreach ($grouplang as $g)
            {
                if ($k == 0)
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'attribute_group SET is_color_group='.(int) $g['is_color_group'].', group_type='.(int) $g['group_type'].' WHERE id_attribute_group='.(int) $newGroupID;
                    }
                    else
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'attribute_group SET is_color_group='.(int) $g['is_color_group'].' WHERE id_attribute_group='.(int) $newGroupID;
                    }
                    Db::getInstance()->Execute($sql);
                }
                $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group_lang (id_attribute_group,id_lang,name,public_name) VALUES ('.(int) $newGroupID.','.(int) $g['id_lang'].",'".psql($g['name'])."','".psql($g['public_name'])."')";
                Db::getInstance()->Execute($sql);
                ++$k;
            }
            if (version_compare(_PS_VERSION_, '1.5.0', '>='))
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_group_shop (id_attribute_group,id_shop) VALUES ('.(int) $newGroupID.','.SCI::getSelectedShopActionList(true).')';
                Db::getInstance()->Execute($sql);
            }
            $sql = '
            SELECT * FROM '._DB_PREFIX_.'attribute a
            LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute=a.id_attribute)
            WHERE a.id_attribute_group='.(int) $id_group;
            $attributes = Db::getInstance()->ExecuteS($sql);
            $inserted = array();
            foreach ($attributes as $a)
            {
                if (!in_array($a['id_attribute'], $inserted))
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
                    {
                        $last_position = (int) Db::getInstance()->getValue('SELECT DISTINCT position FROM '._DB_PREFIX_.'attribute WHERE id_attribute_group = '.(int) $newGroupID.' ORDER BY position DESC');
                        $new_position = $last_position + 1;
                        $sql = 'INSERT INTO '._DB_PREFIX_.'attribute (id_attribute_group,color,position) VALUES ('.(int) $newGroupID.",'".$a['color']."',".(int) $new_position.')';
                    }
                    else
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'attribute (id_attribute_group,color) VALUES ('.(int) $newGroupID.",'".$a['color']."')";
                    }
                    Db::getInstance()->Execute($sql);
                    $newAttributeID = Db::getInstance()->Insert_ID();
                    if (version_compare(_PS_VERSION_, '1.5.0', '>='))
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_shop (id_attribute,id_shop) VALUES ('.(int) $newAttributeID.','.SCI::getSelectedShopActionList(true).')';
                        Db::getInstance()->Execute($sql);
                    }
                    $inserted[] = $a['id_attribute'];
                    if (file_exists(_PS_COL_IMG_DIR_.$a['id_attribute'].'.jpg'))
                    {
                        @copy(_PS_COL_IMG_DIR_.$a['id_attribute'].'.jpg', _PS_COL_IMG_DIR_.$newAttributeID.'.jpg');
                    }
                }
                $sql = 'INSERT INTO '._DB_PREFIX_.'attribute_lang (id_attribute,id_lang,name) VALUES ('.$newAttributeID.",'".$a['id_lang']."','".psql($a['name'])."')";
                Db::getInstance()->Execute($sql);
            }
        }
        $action = 'duplicate';
        $newId = 0;
        $_POST['gr_id'] = 0;
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $fields = array('color', 'color2', 'sc_active');
        $fields_lang = array();
        $idlangByISO = array();
        $todo = array();
        $todo_lang = array();
        foreach ($languages as $lang)
        {
            $fields_lang[] = 'name¤'.$lang['iso_code'];
            $idlangByISO[$lang['iso_code']] = $lang['id_lang'];
        }
        foreach ($fields as $field)
        {
            if (isset($_GET[$field]) || isset($_POST[$field]))
            {
                if ($field == 'color2')
                {
                    $todo[] = "color='".psql(html_entity_decode(Tools::getValue('color2')))."'";
                    addToHistory('attribute', 'modification', 'color', (int) $id_attribute, $id_lang, _DB_PREFIX_.'attribute', psql(Tools::getValue('color2')));
                }
                else
                {
                    $todo[] = $field."='".psql(html_entity_decode(Tools::getValue($field)))."'";
                    addToHistory('attribute', 'modification', $field, (int) $id_attribute, $id_lang, _DB_PREFIX_.'attribute', psql(Tools::getValue($field)));
                }
            }
        }
        foreach ($fields_lang as $field)
        {
            if (isset($_GET[$field]) || isset($_POST[$field]))
            {
                $tmp = explode('¤', $field);
                $fname = $tmp[0];
                $flang = $tmp[1];
                $todo_lang[] = array($fname."='".psql(Tools::htmlentitiesDecodeUTF8(Tools::getValue($field)), true)."'", $idlangByISO[$flang], psql(html_entity_decode(Tools::getValue($field))));
                addToHistory('attribute', 'modification', $fname, (int) $id_attribute, $idlangByISO[$flang], _DB_PREFIX_.'attribute_lang', psql(Tools::getValue($field)));
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'attribute SET '.join(' , ', $todo).' WHERE id_attribute='.(int) $id_attribute;
            Db::getInstance()->Execute($sql);
        }
        if (count($todo_lang))
        {
            foreach ($todo_lang as $tlang)
            {
                $sqltest = 'SELECT * FROM '._DB_PREFIX_.'attribute_lang WHERE id_attribute='.(int) $id_attribute.' AND id_lang='.(int) $tlang[1];
                $test = Db::getInstance()->ExecuteS($sqltest);
                if (count($test) == 0)
                {
                    $sqlinsert = 'INSERT INTO '._DB_PREFIX_.'attribute_lang VALUES ('.(int) $id_attribute.','.(int) $tlang[1].",'".$tlang[2]."')";
                    Db::getInstance()->Execute($sqlinsert);
                }
                else
                {
                    $sql2 = 'UPDATE '._DB_PREFIX_.'attribute_lang SET '.$tlang[0].' WHERE id_attribute='.(int) $id_attribute.' AND id_lang='.(int) $tlang[1];
                    Db::getInstance()->Execute($sql2);
                }
            }
        }
        $newId = Tools::getValue('gr_id');
        $action = 'update';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'deleted')
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>='))
        {
            $attribute = new ProductAttribute($id_attribute, $id_lang);
        }
        else
        {
            $attribute = new Attribute($id_attribute, $id_lang);
        }
        $attribute->delete();

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $result = Db::getInstance()->executeS('SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_attribute = '.(int) $id_attribute);
            foreach ($result as $row)
            {
                $combination = new Combination($row['id_product_attribute']);
                $combination->delete();
            }

            // Delete associated restrictions on cart rules
            CartRule::cleanProductRuleIntegrity('attributes', $id_attribute);

            /* Reinitializing position */
            $attribute->cleanPositions((int) $attribute->id_attribute_group);

            $sql2 = 'DELETE FROM '._DB_PREFIX_.'attribute WHERE id_attribute='.(int) $id_attribute;
            Db::getInstance()->Execute($sql2);
            if (version_compare(_PS_VERSION_, '8.0.0', '<'))
            {
                $sql2 = 'DELETE FROM '._DB_PREFIX_.'attribute_impact WHERE id_attribute='.(int) $id_attribute;
                Db::getInstance()->Execute($sql2);
            }
            $sql2 = 'DELETE FROM '._DB_PREFIX_.'attribute_lang WHERE id_attribute='.(int) $id_attribute;
            Db::getInstance()->Execute($sql2);
            $sql2 = 'DELETE FROM '._DB_PREFIX_.'attribute_shop WHERE id_attribute='.(int) $id_attribute;
            Db::getInstance()->Execute($sql2);
        }

        if (file_exists(_PS_COL_IMG_DIR_.$id_attribute.'.jpg'))
        {
            @unlink(_PS_COL_IMG_DIR_.$id_attribute.'.jpg');
        }
        $newId = Tools::getValue('gr_id');
        $action = 'delete';
    }
    elseif (isset($_GET['action']) && trim($_GET['action']) == 'merge')
    {
        $attrlist = explode(',', Tools::getValue('attrlist', 0));
        sort($attrlist);
        $id_attribute = array_shift($attrlist);
        foreach ($attrlist as $id)
        {
            $sql = 'UPDATE '._DB_PREFIX_.'product_attribute_combination SET id_attribute='.$id_attribute.' WHERE id_attribute='.(int) $id;
            Db::getInstance()->Execute($sql);
            if (version_compare(_PS_VERSION_, '8.0.0', '<'))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'attribute_impact WHERE id_attribute='.(int) $id;
                Db::getInstance()->Execute($sql);
            }
            $sql = 'DELETE FROM '._DB_PREFIX_.'attribute_lang WHERE id_attribute='.(int) $id;
            Db::getInstance()->Execute($sql);
            $sql = 'DELETE FROM '._DB_PREFIX_.'attribute WHERE id_attribute='.(int) $id;
            Db::getInstance()->Execute($sql);
        }
        echo 'OK:'.$id_attribute;
        exit;
    }

    // PM Cache
    if (!empty($updated_products))
    {
        ExtensionPMCM::clearFromIdsAttribute($id_attribute);
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
