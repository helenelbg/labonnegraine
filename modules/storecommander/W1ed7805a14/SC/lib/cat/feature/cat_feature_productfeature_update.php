<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_productList = explode(',', Tools::getValue('id_product', ''));
    $id_feature = (int) Tools::getValue('gr_id', 0);
    $id_feature_value = (int) Tools::getValue('id_feature_value', 0);

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        foreach ($id_productList as $id_product)
        {
            $sql = 'SELECT id_feature_value FROM '._DB_PREFIX_.'feature_product WHERE id_feature='.(int) $id_feature.' AND id_product='.(int) $id_product;
            $fv = Db::getInstance()->getRow($sql);
            $id_feature_value_OLD = (int) $fv['id_feature_value'];

            if ($id_feature_value > 0 && $id_feature != 0 && $id_product != 0)
            {
                // if custom value exists...
                $sql = 'SELECT custom FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id_feature_value_OLD.' AND id_feature='.(int) $id_feature;
                $fv = Db::getInstance()->getRow($sql);
                if ($fv['custom'])
                {
                    // ...delete it
                    $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value='.(int) $id_feature_value_OLD;
                    Db::getInstance()->Execute($sql);
                    $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id_feature_value_OLD;
                    Db::getInstance()->Execute($sql);
                }
                if ($id_feature_value_OLD)
                {
                    $sql = 'UPDATE '._DB_PREFIX_.'feature_product SET id_feature_value='.(int) $id_feature_value.' WHERE id_feature='.(int) $id_feature.' AND id_product='.(int) $id_product.' AND id_feature_value='.(int) $id_feature_value_OLD;
                    Db::getInstance()->Execute($sql);
                }
                else
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_.'feature_product (id_feature_value,id_feature,id_product) VALUES ('.(int) $id_feature_value.','.(int) $id_feature.','.(int) $id_product.')';
                    Db::getInstance()->Execute($sql);
                }
            }
            if ($id_feature_value == -1)
            { // delete
                // if custom value exists...
                $sql = 'SELECT custom FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id_feature_value_OLD.' AND id_feature='.(int) $id_feature;
                $fv = Db::getInstance()->getRow($sql);
                if ($fv['custom'])
                {
                    // ...delete it
                    $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value_lang WHERE id_feature_value='.(int) $id_feature_value_OLD;
                    Db::getInstance()->Execute($sql);
                    $sql = 'DELETE FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id_feature_value_OLD;
                    Db::getInstance()->Execute($sql);
                }
                // delete feature_value for product
                $sql = 'DELETE FROM '._DB_PREFIX_.'feature_product WHERE id_feature_value='.(int) $id_feature_value_OLD.' AND id_feature='.(int) $id_feature.' AND id_product='.(int) $id_product;
                Db::getInstance()->Execute($sql);
            }
            if ($id_feature_value == -2)
            { // custom
                $sql = 'SELECT custom FROM '._DB_PREFIX_.'feature_value WHERE id_feature_value='.(int) $id_feature_value_OLD.' AND id_feature='.(int) $id_feature;
                $fv = Db::getInstance()->getRow($sql);
                if ($fv['custom'])
                {
                    foreach ($languages as $lang)
                    {
                        $custom = Tools::getValue('custom_'.$lang['iso_code'], '');
                        $sql = 'UPDATE '._DB_PREFIX_."feature_value_lang SET value='".psql($custom)."' WHERE id_feature_value=".(int) $id_feature_value_OLD.' AND id_lang='.(int) $lang['id_lang'];
                        Db::getInstance()->Execute($sql);
                    }
                }
                else
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_.'feature_value (id_feature,custom) VALUES ('.(int) $id_feature.',1)';
                    Db::getInstance()->Execute($sql);
                    $id_value = Db::getInstance()->Insert_ID();
                    foreach ($languages as $lang)
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'feature_value_lang (id_feature_value,id_lang,value) VALUES ('.(int) $id_value.','.(int) $lang['id_lang'].",'')";
                        Db::getInstance()->Execute($sql);
                    }
                    if ($id_feature_value_OLD)
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'feature_product SET id_feature_value='.(int) $id_value.' WHERE id_feature='.(int) $id_feature.' AND id_product='.(int) $id_product;
                        Db::getInstance()->Execute($sql);
                    }
                    else
                    {
                        $sql = 'INSERT INTO '._DB_PREFIX_.'feature_product (id_feature_value,id_feature,id_product) VALUES ('.(int) $id_value.','.(int) $id_feature.','.(int) $id_product.')';
                        Db::getInstance()->Execute($sql);
                    }
                }
            }
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd=NOW() WHERE id_product='.(int) $id_product);
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product='.(int) $id_product.' AND id_shop='.(int) SCI::getSelectedShop());
            }
            if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
            {
                $product = new Product((int) $id_product);
                SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
            }
            elseif (_s('APP_COMPAT_EBAY'))
            {
                Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $id_product));
            }
        }
        $newId = Tools::getValue('gr_id');
        $action = 'update';
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
