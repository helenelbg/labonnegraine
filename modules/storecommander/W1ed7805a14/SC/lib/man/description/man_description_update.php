<?php

if (Tools::getValue('act', '') == 'man_description_update')
{
    $id_manufacturer = (int) Tools::getValue('id_manufacturer', '0');
    $id_lang = (int) Tools::getValue('id_lang', '0');

    #### SHORT DESCRIPTION
    $short_description = Tools::getValue('short_description', '');
    if (Tools::strlen(strip_tags($short_description)) > _s('MAN_SHORT_DESC_SIZE'))
    {
        exit('ERR|short_description_size');
    }
    if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
    {
        if (!Validate::isCleanHtml($short_description, (int) Configuration::get('PS_ALLOW_HTML_IFRAME')))
        {
            if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
            {
                exit('ERR|short_description_with_iframe');
            }
            else
            {
                exit('ERR|short_description_invalid');
            }
        }
    }
    elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (!Validate::isString($short_description))
        {
            exit('ERR|short_description_invalid');
        }
    }

    #### DESCRIPTION
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

    $sql = 'SELECT short_description, description FROM '._DB_PREFIX_."manufacturer_lang WHERE id_manufacturer=".(int) $id_manufacturer." AND id_lang=".(int) $id_lang;
    $oldvalues = Db::getInstance()->getRow($sql);
    $sql = 'UPDATE '._DB_PREFIX_."manufacturer SET date_upd=NOW() WHERE id_manufacturer=".(int) $id_manufacturer;
    Db::getInstance()->Execute($sql);
    $sql = 'UPDATE '._DB_PREFIX_."manufacturer_lang SET short_description='".psql($short_description, true)."',description='".psql($description, true)."' WHERE id_manufacturer=".(int) $id_manufacturer." AND id_lang=".(int) $id_lang;
    Db::getInstance()->Execute($sql);
    addToHistory('man_prop', 'modification', 'short_description', $id_manufacturer, $id_lang, _DB_PREFIX_.'manufacturer_lang', $short_description, $oldvalues['short_description']);
    addToHistory('man_prop', 'modification', 'description', $id_manufacturer, $id_lang, _DB_PREFIX_.'manufacturer_lang', $description, $oldvalues['description']);

    // PM Cache
    if (!empty($id_manufacturer))
    {
        ExtensionPMCM::clearFromIdsProduct($id_manufacturer);
    }
}
exit('OK');
