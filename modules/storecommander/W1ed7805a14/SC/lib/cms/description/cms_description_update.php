<?php

if (Tools::getValue('act', '') == 'cms_description_update')
{
    $id_cms = Tools::getValue('id_cms', '0');
    $id_lang = Tools::getValue('id_lang', '0');
    $id_shop = Tools::getValue('id_shop', 0);
    $content = Tools::getValue('content', '');

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (!Validate::isCleanHtml($content, (int) Configuration::get('PS_ALLOW_HTML_IFRAME')))
        {
            if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
            {
                exit('ERR|content_with_iframe');
            }
            else
            {
                exit('ERR|content_invalid');
            }
        }
    }

    $sql = 'SELECT content FROM '._DB_PREFIX_."cms_lang WHERE id_cms='".(int) $id_cms."' AND id_lang='".(int) $id_lang."'";
    $oldvalues = Db::getInstance()->getRow($sql);

    $sql = 'UPDATE '._DB_PREFIX_."cms_lang SET content='".pSQL($content, true)."' WHERE id_cms='".(int) $id_cms."' AND id_lang='".(int) $id_lang."'";
    if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop))
    {
        $sql .= ' AND id_shop = '.(int) $id_shop;
    }

    if (!Db::getInstance()->Execute($sql))
    {
        exit('ERR|process');
    }
    addToHistory('cms_prop', 'modification', 'content', $id_cms, (int) $id_lang, _DB_PREFIX_.'cms_lang', $content, $oldvalues['content']);
}
exit('OK');
