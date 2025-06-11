<?php

$id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));
$config = Tools::getValue('config', null);

if (!empty($config))
{
    ## recherche produit par code
    $id_product = null;
    $id_product_attribute = null;
    $combination_detail = null;
    $new_row = array();
    $sql = 'SELECT id_product,id_product_attribute
                    FROM '._DB_PREFIX_.'product_attribute 
                    WHERE ean13 = "'.pSQL($config['code']).'"';
    $res = Db::getInstance()->getRow($sql);
    if (!empty($res))
    {
        $id_product = (int) $res['id_product'];
        $id_product_attribute = (int) $res['id_product_attribute'];
        $combination_detail = SCI::cachingAttributeName($id_lang, $id_product_attribute);
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
            $product = new Product((int) $id_product, false, (int) $id_lang, (int) SCI::getSelectedShop());
        }
        else
        {
            $product = new Product((int) $id_product, false, (int) $id_lang);
        }
        $quantity = SCI::getProductQty($id_product, $id_product_attribute);
        $row_id = (int) $id_product.'_'.(int) $id_product_attribute.'_'.(int) $product->id_category_default;

        $modification = null;
        switch ($config['action']) {
            case 'stock_add':
                $modification = '+ '.$config['process_value'];
                $stock_after = $quantity + $config['process_value'];
                break;
            case 'stock_replace':
                $modification = '== '.$config['process_value'];
                $stock_after = $config['process_value'];
                break;
            default:
                $modification = '- '.$config['process_value'];
                $stock_after = $quantity - $config['process_value'];
        }

        $new_row = array(
            'id' => $row_id,
            'id_product' => (int) $id_product,
            'id_product_attribute' => (int) $id_product_attribute,
            'id_category_default' => (int) $product->id_category_default,
            'ean13' => $config['code'],
            'name' => $product->name.(!empty($combination_detail) ? '<br/>'.$combination_detail[$id_product_attribute] : ''),
            'stock_before' => (int) $quantity,
            'modification' => $modification,
            'stock_after' => (int) $stock_after,
            'validate' => $row_id,
            'delete' => $row_id,
        );
    }
    exit(json_encode($new_row));
}
