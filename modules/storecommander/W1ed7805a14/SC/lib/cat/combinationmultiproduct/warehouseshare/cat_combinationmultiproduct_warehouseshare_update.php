<?php

$idlist = Tools::getValue('idlist', '');
$idlist_arr = explode(',', $idlist);
$id_product_list = array();
$idlist = array();
foreach ($idlist_arr as $row)
{
    list($id_product, $id_product_attribute) = explode('_', $row);
    $idlist[] = (int) $id_product_attribute;
    $id_product_list[] = (int) $id_product;
}
$idlist = implode(',', $idlist);
$action = Tools::getValue('action', '');
$id_lang = Tools::getValue('id_lang', '0');
$id_warehouse = Tools::getValue('id_warehouse', '0');
$id_actual_warehouse = SCI::getSelectedWarehouse();
$value = Tools::getValue('value', '0');

$multiple = false;
if (strpos($idlist, ',') !== false)
{
    $multiple = true;
}

$ids = explode(',', $idlist);

if ($action != '' && !empty($id_warehouse) && !empty($idlist))
{
    switch ($action) {
        // Modification de location pour le warehouse passé en params
        // pour un ou plusieurs products passés en params
        case 'location':
            foreach ($ids as $id)
            {
                if (!empty($value))
                {
                    if (SCMS)
                    {
                        $combination = new Combination((int) $id, (int) $id_lang, (int) SCI::getSelectedShop());
                    }
                    else
                    {
                        $combination = new Combination($id);
                    }
                    $check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $combination->id_product, (int) $id, (int) $id_warehouse);
                    if (empty($check_in_warehouse))
                    {
                        $new = new WarehouseProductLocation();
                        $new->id_product = (int) $combination->id_product;
                        $new->id_product_attribute = $id;
                        $new->id_warehouse = (int) $id_warehouse;
                        $new->location = $value;
                        $new->save();
                    }
                    else
                    {
                        $new = new WarehouseProductLocation($check_in_warehouse);
                        $new->location = $value;
                        $new->save();
                    }
                }
            }
        break;
        // Modification de present pour le warehouse passé en params
        // pour une ou plusieurs déclinaisons passées en params
        case 'present':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            foreach ($ids as $id)
            {
                if ($value == '1')
                {
                    if (SCMS)
                    {
                        $combination = new Combination((int) $id, (int) $id_lang, (int) SCI::getSelectedShop());
                    }
                    else
                    {
                        $combination = new Combination($id);
                    }
                    $check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $combination->id_product, (int) $id, (int) $id_warehouse);
                    if (empty($check_in_warehouse))
                    {
                        $new = new WarehouseProductLocation();
                        $new->id_product = (int) $combination->id_product;
                        $new->id_product_attribute = $id;
                        $new->id_warehouse = (int) $id_warehouse;
                        $new->save();
                    }
                }
                elseif (empty($value))
                {
                    if (SCMS)
                    {
                        $combination = new Combination((int) $id, (int) $id_lang, (int) SCI::getSelectedShop());
                    }
                    else
                    {
                        $combination = new Combination($id);
                    }
                    $check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $combination->id_product, (int) $id, (int) $id_warehouse);
                    if (!empty($check_in_warehouse))
                    {
                        $sql = 'DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
                        WHERE id_warehouse_product_location = "'.(int) $check_in_warehouse.'"';
                        Db::getInstance()->execute($sql);
                    }
                }
            }
        break;
        // Modification de present
        // pour un ou plusieurs warehouses passés en params
        // pour un ou plusieurs products passés en params
        case 'mass_present':
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            $warehouses = explode(',', $id_warehouse);
            foreach ($warehouses as $id_warehouse)
            {
                foreach ($ids as $id)
                {
                    if ($value == '1')
                    {
                        if (SCMS)
                        {
                            $combination = new Combination((int) $id, (int) $id_lang, (int) SCI::getSelectedShop());
                        }
                        else
                        {
                            $combination = new Combination($id);
                        }
                        $check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $combination->id_product, (int) $id, (int) $id_warehouse);
                        if (empty($check_in_warehouse))
                        {
                            $new = new WarehouseProductLocation();
                            $new->id_product = (int) $combination->id_product;
                            $new->id_product_attribute = $id;
                            $new->id_warehouse = (int) $id_warehouse;
                            $new->save();
                        }
                    }
                    elseif (empty($value))
                    {
                        if (SCMS)
                        {
                            $combination = new Combination((int) $id, (int) $id_lang, (int) SCI::getSelectedShop());
                        }
                        else
                        {
                            $combination = new Combination($id);
                        }
                        $check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $combination->id_product, (int) $id, (int) $id_warehouse);
                        if (!empty($check_in_warehouse))
                        {
                            $sql = 'DELETE FROM `'._DB_PREFIX_.'warehouse_product_location`
                            WHERE id_warehouse_product_location = "'.(int) $check_in_warehouse.'"';
                            Db::getInstance()->execute($sql);
                        }
                    }
                }
            }
        break;
    }

    if (!empty($id_product_list))
    {
        ExtensionPMCM::clearFromIdsProduct($id_product_list);
    }
}
