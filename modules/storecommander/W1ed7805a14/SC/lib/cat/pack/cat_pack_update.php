<?php

$id_lang = (int) Tools::getValue('id_lang');
$action = Tools::getValue('action', '');
$id_pack = (int) Tools::getValue('id_pack');

if (!empty($action))
{
    switch ($action){
        case 'combi_present':
            $id_product = (int) Tools::getValue('id_product');
            $id_product_attribute = (int) Tools::getValue('id_product_attribute');
            $value = Tools::getValue('value');
            if ($value == 'true')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            if (!empty($value))
            {
                $sql = 'SELECT * FROM '._DB_PREFIX_."pack WHERE id_product_pack=".(int) $id_pack." AND id_product_item=".(int) $id_product." AND id_product_attribute_item=".(int) $id_product_attribute;
                $res = Db::getInstance()->ExecuteS($sql);
                if (empty($res[0]['id_product_attribute_item']))
                {
                    $sql = 'INSERT INTO '._DB_PREFIX_."pack (id_product_pack,id_product_item,id_product_attribute_item,quantity)
                        VALUES (".(int) $id_pack.",".(int) $id_product.",".(int) $id_product_attribute.",'1')";
                    Db::getInstance()->Execute($sql);
                }
            }
            else
            {
                $sql = 'DELETE FROM '._DB_PREFIX_."pack WHERE id_product_pack=".(int) $id_pack." AND id_product_item=".(int) $id_product." AND id_product_attribute_item=".(int) $id_product_attribute;
                Db::getInstance()->Execute($sql);
            }
            break;
        case 'quantity':
            $id_product = (int) Tools::getValue('id_product');
            $id_product_attribute = (int) Tools::getValue('id_product_attribute', 0);
            $value = (int) Tools::getValue('value');

            $sql = 'SELECT id_product_item FROM '._DB_PREFIX_."pack WHERE id_product_pack=".(int) $id_pack." AND id_product_item=".(int) $id_product." AND id_product_attribute_item=".(int) $id_product_attribute;
            $res = Db::getInstance()->ExecuteS($sql);
            if (!empty($res[0]['id_product_item']))
            {
                $sql = 'UPDATE '._DB_PREFIX_."pack SET quantity='".(int) $value."' WHERE id_product_pack=".(int) $id_pack." AND id_product_item=".(int) $id_product." AND id_product_attribute_item=".(int) $id_product_attribute;
                Db::getInstance()->Execute($sql);
            }
            break;
        case 'delete':
            $id_product = (int) Tools::getValue('id_product');

            $sql = 'DELETE FROM '._DB_PREFIX_."pack WHERE id_product_pack=".(int) $id_pack." AND id_product_item=".(int) $id_product;
            Db::getInstance()->Execute($sql);

            break;
        case 'insert':
            $id_product = (int) Tools::getValue('id_product');
            $id_product_attribute = (int) Tools::getValue('id_product_attribute');

            if (!empty($id_pack) && !empty($id_product))
            {
                $sql = 'INSERT INTO '._DB_PREFIX_."pack (id_product_pack,id_product_item,id_product_attribute_item,quantity)
                        VALUES (".(int) $id_pack.",".(int) $id_product.",".(int) $id_product_attribute.",'1')";
                Db::getInstance()->Execute($sql);
            }
            break;
    }
    $nb_item_in_pack = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'pack WHERE id_product_pack='.$id_pack);
    if ($nb_item_in_pack >= 1)
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET cache_is_pack=1'.(version_compare(_PS_VERSION_, '1.7.8.0', '>=') ? ', product_type="'.PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_PACK.'"' : '').' WHERE id_product = '.(int) $id_pack);
    }
    else
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET cache_is_pack=0'.(version_compare(_PS_VERSION_, '1.7.8.0', '>=') ? ', product_type="'.PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_STANDARD.'"' : '').' WHERE id_product = '.(int) $id_pack);
    }
}
