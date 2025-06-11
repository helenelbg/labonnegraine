<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
$action = Tools::getValue('action', '');

$return = 'ERROR: Try again later';

// FUNCTIONS
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows') || $action == 'insert')
{
    if ($action != 'insert')
    {

        if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = Tools::getValue('rows');
        $rows = json_decode($_POST["rows"]);
    }
    else
    {
        $rows = array();
        $rows[0] = new stdClass();
        $rows[0]->name = Tools::getValue('act', '');
        $rows[0]->action = Tools::getValue('action', '');
        $rows[0]->row = Tools::getValue('gr_id', '');
        $rows[0]->callback = Tools::getValue('callback', '');
        $rows[0]->params = $_POST;
    }

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = '';

        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        $date = date('Y-m-d H:i:s');
        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params) ? $row->params : array()), (!empty($row->callback) ? $row->callback : null), $date);
            $log_ids[$num] = $id;
        }

        // Deuxième boucle pour effectuer les
        // actions les une après les autres
        foreach ($rows as $num => $row)
        {
            if (!empty($log_ids[$num]))
            {
                $gr_id = (int) $row->row;
                $action = $row->action;

                if (!empty($row->callback))
                {
                    $callbacks .= $row->callback.';';
                }

                if ($action != 'insert')
                {
                    $_POST = array();
                    $_POST = (array) json_decode($row->params);
                }

                if (!empty($action) && $action == 'update' && !empty($gr_id))
                {
                    $id_lang = (int) Tools::getValue('id_lang');
                    $id_productList = explode(',', Tools::getValue('id_product', ''));
                    $id_feature = $gr_id;
                    $id_feature_value = (int) Tools::getValue('id_feature_value', 0);

                    if (!empty($id_productList) && count($id_productList) == 1)
                    {
                        $updated_products[$id_productList[0]] = $id_productList[0];
                    }
                    elseif (!empty($id_productList) && count($id_productList) > 1)
                    {
                        $updated_products = array_merge($updated_products, $id_productList);
                    }

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
                                addToHistory('cat_prop_feature', 'modification', 'feature_value', $id_product, $id_lang, 'feature_product', $id_feature_value, $id_feature_value_OLD, (int) SCI::getSelectedShop());
                            }
                            else
                            {
                                $sql = 'INSERT INTO '._DB_PREFIX_.'feature_product (id_feature_value,id_feature,id_product) VALUES ('.(int) $id_feature_value.','.(int) $id_feature.','.(int) $id_product.')';
                                Db::getInstance()->Execute($sql);
                                addToHistory('cat_prop_feature', 'modification', 'feature_value', $id_product, $id_lang, 'feature_product', $id_feature_value, null, (int) SCI::getSelectedShop());
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
                            addToHistory('cat_prop_feature', 'modification', 'feature_value', $id_product, $id_lang, 'feature_product', '', $id_feature_value_OLD, (int) SCI::getSelectedShop());
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
                                    addToHistory('cat_prop_feature', 'modification', 'feature_value', $id_product, $id_lang, 'feature_product', $id_value, $id_feature_value_OLD, (int) SCI::getSelectedShop());
                                }
                                else
                                {
                                    $sql = 'INSERT INTO '._DB_PREFIX_.'feature_product (id_feature_value,id_feature,id_product) VALUES ('.(int) $id_value.','.(int) $id_feature.','.(int) $id_product.')';
                                    Db::getInstance()->Execute($sql);
                                    addToHistory('cat_prop_feature', 'modification', 'feature_value', $id_product, $id_lang, 'feature_product', $id_value, null, (int) SCI::getSelectedShop());
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
                }

                QueueLog::delete(($log_ids[$num]));
            }
        }

        if (!empty($updated_products))
        {
            if (_s('CAT_APPLY_ALL_CART_RULES'))
            {
                SpecificPriceRule::applyAllRules($updated_products);
            }
            // PM Cache
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => $callbacks));
    }
}
echo $return;
