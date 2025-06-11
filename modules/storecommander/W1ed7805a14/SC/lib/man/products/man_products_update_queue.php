<?php

$action = Tools::getValue('action', '');
$return = 'ERROR: Try again later';
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows'))
{

        if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = Tools::getValue('rows');
        $rows = json_decode($_POST["rows"]);

    if (is_array($rows) && count($rows) > 0)
    {
        $callbacks = '';
        $log_ids = array();
        $date = date('Y-m-d H:i:s');
        foreach ($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params) ? $row->params : array()), (!empty($row->callback) ? $row->callback : null), $date);
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
                if (!empty($action) && $action == 'update')
                {
                    $id_product = $gr_id;
                    $updated_products[] = $id_product;
                    $id_manufacturer = (int) Tools::getValue('id_manufacturer', false);
                    $reference = (string) Tools::getValue('reference');

                    if(Tools::isSubmit('id_manufacturer')){
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET id_manufacturer = '.(int) $id_manufacturer.', date_upd = NOW() WHERE id_product = '.(int) $id_product);
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd = NOW() WHERE id_product = '.(int) $id_product.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
                            }
                    }
                    if(Tools::isSubmit('reference')){
                        $sql = 'UPDATE ' . _DB_PREFIX_ . 'product
                                SET date_upd = NOW(), reference = "' . pSQL($reference) . '" 
                                WHERE id_product = ' . (int) $id_product;
                        Db::getInstance()->Execute($sql);
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
