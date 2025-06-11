<?php

$type = Tools::getValue('type', null);
$config = Tools::getValue('config', null);
$error = false;
$res = array(
    'error' => 0,
    'message' => '',
    'pdt_updated' => array(),
);

function getStockAvailableTimeT($id_product, $id_product_attribute = 0)
{
    $result = array(
        'id_stock_available' => null,
        'quantity' => 0,
    );
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $result_db = Db::getInstance()->getRow('SELECT id_stock_available,quantity
                                            FROM `'._DB_PREFIX_.'stock_available`
                                            WHERE id_product = '.(int) $id_product.'
                                            AND id_product_attribute = '.(int) $id_product_attribute.'
                                            AND id_shop = '.(int) SCI::getSelectedShop());
        if ($result_db)
        {
            $result['id_stock_available'] = (int) $result_db['id_stock_available'];
            $result['quantity'] = (int) $result_db['quantity'];
        }
    }
    else
    {
        if ($id_product_attribute)
        {
            $result['quantity'] = (int) Db::getInstance()->getValue('SELECT quantity
                                            FROM `'._DB_PREFIX_.'product`
                                            WHERE id_product = '.(int) $id_product);
        }
        else
        {
            $result['quantity'] = (int) Db::getInstance()->getValue('SELECT quantity
                                            FROM `'._DB_PREFIX_.'product_attribute`
                                            WHERE id_product = '.(int) $id_product.'
                                            AND id_product_attribute = '.(int) $id_product_attribute);
        }
    }

    return $result;
}

if (!empty($config) && !empty($type))
{
    switch ($type){
        case 'ref':
            $id_product_attribute = null;
            $sql = 'SELECT id_product,id_product_attribute
                    FROM '._DB_PREFIX_.'product_attribute 
                    WHERE ean13 = "'.pSQL($config['code']).'"';
            $res = Db::getInstance()->getRow($sql);
            if (!empty($res))
            {
                $id_product = (int) $res['id_product'];
                $id_product_attribute = (int) $res['id_product_attribute'];
            }
            else
            {
                $sql = 'SELECT id_product
                        FROM '._DB_PREFIX_.'product 
                        WHERE ean13 = "'.pSQL($config['code']).'"';
                $id_product = Db::getInstance()->getValue($sql);
            }
            if (!empty($id_product))
            {
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $product = new Product((int) $id_product, false, null, (int) SCI::getSelectedShop());
                }
                else
                {
                    $product = new Product((int) $id_product);
                }

                $process_value = str_replace(' ', '', $config['process_value']);
                $time_t_stock_available = getStockAvailableTimeT($id_product, $id_product_attribute);

                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $process = SCI::updateQuantity($id_product, $id_product_attribute, $process_value, (int) SCI::getSelectedShop());
                }
                else
                {
                    $process = SCI::updateQuantity($id_product, $id_product_attribute, $process_value, null);
                }
                if ($process)
                {
                    $delta = $process_value * 1;
                    $new_qty = $time_t_stock_available['quantity'] + $delta;
                    addToHistory('cat_win-barcode', 'modification', 'quantity', (int) $id_product, 0, _DB_PREFIX_.'stock_available', (int) $new_qty, (int) $time_t_stock_available['quantity'], SCI::getSelectedShop());
                    if (version_compare(_PS_VERSION_, '1.7.2.0', '>='))
                    {
                        $id_stock_available = $time_t_stock_available['id_stock_available'];
                        if ($id_stock_available == null)
                        {
                            $sv = getStockAvailableTimeT($id_product, $id_product_attribute);
                            $id_stock_available = (int) $sv['id_stock_available'];
                        }
                        $sign = 1;
                        $reason = (int) SCI::getConfigurationValue('PS_STOCK_MVT_INC_EMPLOYEE_EDITION');
                        if ($delta < 0)
                        {
                            $sign = -1;
                        }
                        $stockMvt = new StockMvt();
                        $stockMvt->id_stock = $id_stock_available;
                        $stockMvt->id_stock_mvt_reason = SCI::getStockMvtEmployeeReasonId($sign);
                        $stockMvt->id_employee = (int) $sc_agent->id_employee;
                        $stockMvt->employee_lastname = $sc_agent->lastname;
                        $stockMvt->employee_firstname = $sc_agent->firstname;
                        $stockMvt->physical_quantity = (int) str_replace(array('-', '+'), '', $process_value);
                        $stockMvt->date_add = date('Y-m-d H:i:s');
                        $stockMvt->sign = $sign;
                        $stockMvt->price_te = 0;
                        $stockMvt->last_wa = 0;
                        $stockMvt->current_wa = 0;
                        $stockMvt->add();
                    }
                    $prd = implode('_', array($id_product, $id_product_attribute, $product->id_category_default));
                    $res['pdt_updated'][] = $prd;
                }
                else
                {
                    $error = 1;
                }
            }
            else
            {
                $error = 2;
            }
            break;
        default:
            foreach ($config as $row)
            {
                list($id_product, $id_product_attribute, $id_category_default) = explode('_', $row['code']);
                $id_product_attribute = ($id_product_attribute > 0 ? $id_product_attribute : null);
                $process_value = str_replace(' ', '', $row['process_value']);

                $action_replace = strpos($process_value, '==');
                $time_t_stock_available = getStockAvailableTimeT($id_product, $id_product_attribute);

                if ($action_replace !== false)
                {
                    $new_qty = (int) trim(str_replace('==', '', $process_value));
                    $process = true;
                    try
                    {
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            SCI::setQuantity($id_product, $id_product_attribute, $new_qty, (int) SCI::getSelectedShop());
                        }
                        else
                        {
                            SCI::setQuantity($id_product, $id_product_attribute, $new_qty);
                        }
                        addToHistory('cat_win-barcode', 'modification', 'quantity', (int) $id_product, 0, _DB_PREFIX_.'stock_available', (int) $new_qty, (int) $time_t_stock_available['quantity'], SCI::getSelectedShop());
                        if (version_compare(_PS_VERSION_, '1.7.2.0', '>='))
                        {
                            $id_stock_available = $time_t_stock_available['id_stock_available'];
                            if ($id_stock_available == null)
                            {
                                $sv = getStockAvailableTimeT($id_product, $id_product_attribute);
                                $id_stock_available = (int) $sv['id_stock_available'];
                            }
                            $delta = $new_qty - $time_t_stock_available['quantity'];
                            $sign = 1;
                            if ($delta < 0)
                            {
                                $sign = -1;
                                $delta = $delta * -1;
                            }

                            $stockMvt = new StockMvt();
                            $stockMvt->id_stock = (int) $id_stock_available;
                            $stockMvt->id_stock_mvt_reason = SCI::getStockMvtEmployeeReasonId($sign);
                            $stockMvt->id_employee = (int) $sc_agent->id_employee;
                            $stockMvt->employee_lastname = $sc_agent->lastname;
                            $stockMvt->employee_firstname = $sc_agent->firstname;
                            $stockMvt->physical_quantity = (int) $delta;
                            $stockMvt->date_add = date('Y-m-d H:i:s');
                            $stockMvt->sign = $sign;
                            $stockMvt->price_te = 0;
                            $stockMvt->last_wa = 0;
                            $stockMvt->current_wa = 0;
                            $stockMvt->add();
                        }
                    }
                    catch (Exception $e)
                    {
                        $error = $e->getMessage();
                        $process = false;
                    }
                }
                else
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $process = SCI::updateQuantity($id_product, $id_product_attribute, $process_value, (int) SCI::getSelectedShop());
                    }
                    else
                    {
                        $process = SCI::updateQuantity($id_product, $id_product_attribute, $process_value);
                    }

                    $delta = $process_value * 1;
                    $new_qty = $time_t_stock_available['quantity'] + $delta;
                    addToHistory('cat_win-barcode', 'modification', 'quantity', (int) $id_product, 0, _DB_PREFIX_.'stock_available', (int) $new_qty, (int) $time_t_stock_available['quantity'], SCI::getSelectedShop());
                    if (version_compare(_PS_VERSION_, '1.7.2.0', '>='))
                    {
                        $id_stock_available = $time_t_stock_available['id_stock_available'];
                        if ($id_stock_available == null)
                        {
                            $sv = getStockAvailableTimeT($id_product, $id_product_attribute);
                            $id_stock_available = (int) $sv['id_stock_available'];
                        }
                        $sign = 1;
                        if ($delta < 0)
                        {
                            $sign = -1;
                        }
                        $stockMvt = new StockMvt();
                        $stockMvt->id_stock = (int) $id_stock_available;
                        $stockMvt->id_stock_mvt_reason = SCI::getStockMvtEmployeeReasonId($sign);
                        $stockMvt->id_employee = (int) $sc_agent->id_employee;
                        $stockMvt->employee_lastname = $sc_agent->lastname;
                        $stockMvt->employee_firstname = $sc_agent->firstname;
                        $stockMvt->physical_quantity = (int) str_replace(array('-', '+'), '', $process_value);
                        $stockMvt->date_add = date('Y-m-d H:i:s');
                        $stockMvt->sign = $sign;
                        $stockMvt->price_te = 0;
                        $stockMvt->last_wa = 0;
                        $stockMvt->current_wa = 0;
                        $stockMvt->add();
                    }
                }

                if ($process)
                {
                    $res['pdt_updated'][] = $row['code'];
                }
                else
                {
                    $error = 3;
                    break;
                }
            }
    }
    if (empty($error))
    {
        $res['error'] = 0;
        $res['message'] = _l('Products updated successfully');
    }
    else
    {
        $res['error'] = $error;
        $res['message'] = _l('Error during product update');
    }
    exit(json_encode($res));
}
