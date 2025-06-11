<?php

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT DISTINCT id_product FROM '._DB_PREFIX_.'product_attribute';
        $res = Db::getInstance()->ExecuteS($sql);

        if (count($res))
        {
            $updated_products = array();
            foreach ($res as $r)
            {
                SCI::qtySumStockAvailable((int) $r['id_product']);
                $updated_products[$r['id_product']] = $r['id_product'];
            }
            if (!empty($updated_products))
            {
                ExtensionPMCM::clearFromIdsProduct($updated_products);
            }
        }
        echo 'Ok';
    }
    else
    {
        echo 'Bad Prestashop version';
    }
