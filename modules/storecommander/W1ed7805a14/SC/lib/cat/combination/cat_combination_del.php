<?php

    $idpa_array = explode(',', Tools::getValue('id_product_attribute', '0'));
    $id_product = Tools::getValue('id_product', '0');

    function checkDefaultAttributes($id_product)
    {
        $row = Db::getInstance()->getRow('
        SELECT id_product, id_product_attribute
        FROM `'._DB_PREFIX_.'product_attribute`
        WHERE `default_on` = 1 AND `id_product` = '.(int) ($id_product));
        if ($row)
        {
            return (int) ($row['id_product_attribute']);
        }

        $mini = Db::getInstance()->getRow('
        SELECT MIN(pa.id_product_attribute) as `id_attr`
        FROM `'._DB_PREFIX_.'product_attribute` pa
        WHERE `id_product` = '.(int) ($id_product));
        if (!$mini)
        {
            return 0;
        }

        if (!Db::getInstance()->Execute('
            UPDATE `'._DB_PREFIX_.'product_attribute`
            SET `default_on` = 1
            WHERE `id_product_attribute` = '.(int) ($mini['id_attr'])))
        {
            return 0;
        }

        return (int) ($mini['id_attr']);
    }

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shops = SCI::getSelectedShopActionList(false);

        $p = new Product($id_product);
        foreach ($idpa_array as $id_product_attribute)
        {
            if (is_numeric($id_product_attribute) && $id_product_attribute)
            {
                $c = new Combination($id_product_attribute);
                if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
                {
                    $c->id_shop_list = Shop::getShops(false, null, true);
                }
                else
                {
                    $c->id_shop_list = SCI::getSelectedShopActionList(false);
                }
                $c->delete();
                foreach ($shops as $shop)
                {
                    StockAvailable::removeProductFromStockAvailable((int) $id_product, (int) $id_product_attribute, $shop);
                }

                SCI::deleteCombinationStock((int) $id_product_attribute, (int) $id_product);
            }
        }

        $p->checkDefaultAttributes();
        if (!$p->hasAttributes())
        {
            $p->cache_default_attribute = 0;
            $p->update();
        }
        else
        {
            Product::updateDefaultAttribute((int) $id_product);
        }

        SCI::qtySumStockAvailable($id_product);
    }
    else
    {
        foreach ($idpa_array as $id_product_attribute)
        {
            if (is_numeric($id_product_attribute))
            {
                $sql = 'DELETE FROM '._DB_PREFIX_."product_attribute WHERE id_product_attribute=" .(int) $id_product_attribute;
                Db::getInstance()->Execute($sql);
                $sql = 'DELETE FROM '._DB_PREFIX_."product_attribute_combination WHERE id_product_attribute=" .(int) $id_product_attribute;
                Db::getInstance()->Execute($sql);
                $sql = 'DELETE FROM `'._DB_PREFIX_.'cart_product` WHERE `id_product_attribute` = '.(int) $id_product_attribute;
                Db::getInstance()->Execute($sql);
                $sql = 'DELETE FROM '._DB_PREFIX_."product_attribute_image WHERE id_product_attribute=" .(int) $id_product_attribute;
                Db::getInstance()->Execute($sql);

                if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                {
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stock_mvt` WHERE `id_product_attribute` > 0 AND `id_product_attribute` IN ('.join(',', $attributes).')');
                }
                if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
                {
                    SCI::hookExec('deleteProductAttribute', array('id_product_attribute' => (int) $id_product_attribute, 'id_product' => (int) $id_product, 'deleteAllAttributes' => false));
                }
                elseif (_s('APP_COMPAT_EBAY'))
                {
                    Configuration::updateValue('EBAY_SYNC_LAST_PRODUCT', min(Configuration::get('EBAY_SYNC_LAST_PRODUCT'), (int) $id_product));
                }
            }
        }

        $default_id = checkDefaultAttributes((int) $id_product);
        Db::getInstance()->Execute('
            UPDATE `'._DB_PREFIX_.'product`
            SET `cache_default_attribute` ='.(int) $default_id.'
            WHERE `id_product` = '.(int) $id_product);
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            Db::getInstance()->Execute('
                UPDATE `'._DB_PREFIX_.'product_shop`
                SET `cache_default_attribute` ='.(int) $default_id.'
                WHERE `id_product` = '.(int) $id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true, $id_product).')');
        }

        Db::getInstance()->Execute('
            UPDATE `'._DB_PREFIX_.'product`
            SET `quantity` =
                (
                SELECT SUM(`quantity`)
                FROM `'._DB_PREFIX_.'product_attribute`
                WHERE `id_product` = '.(int) $id_product.'
                )
            WHERE `id_product` = '.(int) $id_product);
    }

if (!empty($id_product))
{
    ExtensionPMCM::clearFromIdsProduct($id_product);
}
