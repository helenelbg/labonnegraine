<?php

if (Tools::getValue('act', '') == 'cat_description_update')
{
    $id_product = Tools::getValue('id_product', '0');
    $id_lang = Tools::getValue('id_lang', '0');
    $description_short = Tools::getValue('description_short', '');
    if (countChars($description_short) > _s('CAT_SHORT_DESC_SIZE'))
    {
        exit('ERR|description_short_size');
    }
    if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
    {
        if (!Validate::isCleanHtml($description_short, (int) Configuration::get('PS_ALLOW_HTML_IFRAME')))
        {
            if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
            {
                exit('ERR|description_short_with_iframe');
            }
            else
            {
                exit('ERR|description_short_invalid');
            }
        }
    }
    elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (!Validate::isString($description_short))
        {
            exit('ERR|description_short_invalid');
        }
    }

    $description = Tools::getValue('description', '');
    if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
    {
        if (!Validate::isCleanHtml($description, (int) Configuration::get('PS_ALLOW_HTML_IFRAME')))
        {
            if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
            {
                exit('ERR|description_with_iframe');
            }
            else
            {
                exit('ERR|description_invalid');
            }
        }
    }
    elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (!Validate::isString($description))
        {
            exit('ERR|description_invalid');
        }
    }

    $sql = 'SELECT description_short, description FROM '._DB_PREFIX_."product_lang WHERE id_product=" .(int) $id_product . " AND id_lang=" .(int) $id_lang;
    $oldvalues = Db::getInstance()->getRow($sql);
    Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET date_upd=NOW(),indexed=0 WHERE id_product=".(int) $id_product);
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd=NOW(),indexed=0 WHERE id_product='.(int) $id_product.' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
    }

    $description_short = preg_replace('#([^>])&nbsp;#ui', '$1 ', $description_short);
    $description = preg_replace('#([^>])&nbsp;#ui', '$1 ', $description);

    $sql = 'UPDATE '._DB_PREFIX_."product_lang SET description_short='".psql($description_short, true)."',description='".psql($description, true)."' WHERE id_product=" .(int) $id_product . " AND id_lang=" .(int) $id_lang;
    if (SCMS)
    {
        $sql .= ' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')';
    }

    if (_s('APP_COMPAT_HOOK'))
    {
        $product = new Product((int) $id_product);
        SCI::hookExec('updateProduct', array('id_product' => (int) $product->id, 'product' => $product));
    }

    Db::getInstance()->Execute($sql);
    addToHistory('cat_prop', 'modification', 'description_short', $id_product, (int) $id_lang, _DB_PREFIX_.'product_lang', $description_short, $oldvalues['description_short']);
    addToHistory('cat_prop', 'modification', 'description', $id_product, (int) $id_lang, _DB_PREFIX_.'product_lang', $description, $oldvalues['description']);

    // PM Cache
    if (!empty($id_product))
    {
        ExtensionPMCM::clearFromIdsProduct($id_product);
    }
}
exit('OK');
