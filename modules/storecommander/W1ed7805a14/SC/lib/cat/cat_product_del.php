<?php

    if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
    {
        require_once SC_PS_PATH_DIR.'images.inc.php';
    }

    $productlist = Tools::getValue('productlist', '');
    if ($productlist != '')
    {
        $productlistarray = explode(',', $productlist);
        foreach ($productlistarray as $idproduct)
        {
            $product = new Product((int) $idproduct);
            if (SCMS)
            {
                $id_shop_list_array = Product::getShopsByProduct($product->id);
                $id_shop_list = array();
                foreach ($id_shop_list_array as $array_shop)
                {
                    $id_shop_list[] = $array_shop['id_shop'];
                }
                $product->id_shop_list = $id_shop_list;
            }
            $product->delete();
            addToHistory('catalog_tree', 'delete', 'product', (int) $product->id, null, _DB_PREFIX_.'product', null, null);
        }
    }
