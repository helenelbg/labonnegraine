<?php

$action = Tools::getValue('action', '');
$return = 'ERROR: Try again later';
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows'))
{
    if (_PS_MAGIC_QUOTES_GPC_)
    {
        $_POST['rows'] = Tools::getValue('rows');
    }
    $rows = json_decode($_POST['rows']);

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = '';
        $log_ids = array();
        $date = date('Y-m-d H:i:s');
        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, !empty($row->params) ? $row->params : array(), !empty($row->callback) ? $row->callback : null, $date);
            $log_ids[$num] = $id;
        }

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

                if (!empty($action))
                {
                    $id_product = $gr_id;
                    $updated_products[] = $id_product;
                    $sql = array();

                    switch ($action)
                    {
                        case 'dissociate':
                            $selected_suppliers = (string) Tools::getValue('selected_suppliers', null);
                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product 
                                                            SET id_supplier = 0, date_upd = NOW() 
                                                            WHERE id_product = '.(int) $id_product);
                            Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'product_supplier
                                                            WHERE id_product = '.(int) $id_product.'
                                                            AND id_supplier IN ('.pInSQL($selected_suppliers).')');

                            break;
                        case 'update':
                            $id_supplier = (int) Tools::getValue('id_supplier');
                            $reference = (string) Tools::getValue('reference');
                            $supplier_reference = (string) Tools::getValue('supplier_reference');
                            $active = (int) Tools::getValue('active');
                            if (Tools::isSubmit('id_supplier') && !Tools::isSubmit('reference'))
                            {
                                $already_default_supplier = (int) Db::getInstance()->getValue('SELECT COUNT(id_product) 
                                                                                                    FROM '._DB_PREFIX_.'product 
                                                                                                    WHERE id_product = '.(int) $id_product.' 
                                                                                                    AND id_supplier = '.(int) $id_supplier);
                                if ($already_default_supplier == 0)
                                {
                                    $sql[] = 'UPDATE '._DB_PREFIX_.'product
                                                SET id_supplier = '.(int) $id_supplier.', date_upd = NOW()
                                                WHERE id_product = '.(int) $id_product;
                                }
                                
                                $sql[] = 'INSERT IGNORE INTO '._DB_PREFIX_.'product_supplier (`id_product`,`id_product_attribute`,`id_supplier`)
                                            VALUE ('.(int) $id_product.',0,'.(int) $id_supplier.')';
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $sql[] = 'UPDATE '._DB_PREFIX_.'product_shop
                                                SET date_upd = NOW()
                                                WHERE id_product = '.(int) $id_product.'
                                                AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                                }

                                foreach ($sql as $rowSql)
                                {
                                    Db::getInstance()->execute($rowSql);
                                }
                            }

                            if (Tools::isSubmit('reference'))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'product
                                        SET date_upd = NOW(), reference = "'.pSQL($reference).'" 
                                        WHERE id_product = '.(int) $id_product;
                                Db::getInstance()->Execute($sql);
                            }

                            if (Tools::isSubmit('supplier_reference'))
                            {
                                $sql = 'UPDATE '._DB_PREFIX_.'product_supplier
                                        SET product_supplier_reference = "'.pSQL($supplier_reference).'" 
                                        WHERE id_product = '.(int) $id_product.' AND id_supplier='.(int) $id_supplier;
                                Db::getInstance()->Execute($sql);
                            }

                            if (Tools::isSubmit('active'))
                            {
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $sql = 'UPDATE '._DB_PREFIX_.'product_shop
                                            SET active = '.(int) $active.' 
                                            WHERE id_product = '.(int) $id_product.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
                                    Db::getInstance()->Execute($sql);
                                }
                                else
                                {
                                    $sql = 'UPDATE '._DB_PREFIX_.'product
                                            SET date_upd = NOW(), active = '.(int) $active.' 
                                            WHERE id_product = '.(int) $id_product;
                                    Db::getInstance()->Execute($sql);
                                }
                            }
                            break;
                    }
                }

                QueueLog::delete($log_ids[$num]);
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
