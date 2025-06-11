<?php

$action = Tools::getValue('action', '0');
$tag_list = Tools::getValue('tag_list', '0');
$value = Tools::getValue('value', '');
$product_list = Tools::getValue('product_list', '0');
$default_id_lang = Configuration::get('PS_LANG_DEFAULT');
$sqlLang = 'SELECT `iso_code`,`id_lang` FROM `'._DB_PREFIX_.'lang`';
$listLang = Db::getInstance()->ExecuteS($sqlLang);
$current_id_lang = Tools::getValue('id_lang', '0');
$cacheLang = array();
foreach ($listLang as $list)
{
    $cacheLang[$list['iso_code']] = $list['id_lang'];
}

if (Tools::getValue('act', '') == 'cat_tag_update')
{
    if ($action == 'add')
    {
        $linktoproduct = Tools::getValue('linktoproduct', '0');
        $string = explode("\n", $value);
        $data = array();
        $id_taglist = array();
        foreach ($string as $str)
        {
            $str = trim($str);
            if ($str == '')
            {
                continue;
            }
            $res = explode(',', $str);
            $name = trim($res[0]);
            $lang = $default_id_lang;
            if (count($res) > 1 && sc_array_key_exists(trim($res[1]), $cacheLang))
            {
                $lang = $cacheLang[trim($res[1])];
            }
            $data[] = array(
                    'lang' => (int) $lang,
                    'name' => $name, );
        }
        foreach ($data as $key => $val)
        {
            $tagSql = 'SELECT `id_tag` FROM `'._DB_PREFIX_."tag` WHERE `name`='".psql($val['name'])."' AND `id_lang`=".(int) $val['lang'];
            $tagSel = Db::getInstance()->getValue($tagSql);
            if ($tagSel == false)
            {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'tag` (id_lang,name) VALUES ('.(int) $val['lang'].",'".psql($val['name'])."')";
                Db::getInstance()->Execute($sql);
                $id_taglist[] = Db::getInstance()->Insert_ID();
            }
            else
            {
                $id_taglist[] = (int)$tagSel;
            }
        }
        if ($linktoproduct)
        {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_tag` IN ('.pInSQL(join(',', $id_taglist)).') AND `id_product` IN ('.pInSQL($product_list).')';
            Db::getInstance()->Execute($sql);
            $sqlstr = array();
            $product_list = explode(',', $product_list);
            foreach ($id_taglist as $id_tag)
            {
                foreach ($product_list as $id_product)
                {
                    if ($id_product != 0 && $id_tag != 0)
                    {
                        $sqlstr[] = '('.$id_product.','.$id_tag.')';
                    }
                }
            }
            if (count($sqlstr))
            {
                $sqlstr = array_unique($sqlstr);
                $sql = 'INSERT INTO `'._DB_PREFIX_.'product_tag` (id_product,id_tag) VALUES '.psql(join(',', $sqlstr));
                Db::getInstance()->Execute($sql);
            }
        }
    }

    if ($action == 'delete')
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'tag` WHERE `id_tag` IN ('.pInSQL($tag_list).')';
        Db::getInstance()->Execute($sql);
        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_tag` IN ('.pInSQL($tag_list).')';
        Db::getInstance()->Execute($sql);
    }

    if ($action == 'update')
    {
        $product_listarray = explode(',', $product_list);
        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_tag` IN ('.pInsql($tag_list).') AND `id_product` IN ('.pInSQL($product_list).')';
        Db::getInstance()->Execute($sql);
        $sqlstr = '';
        foreach ($product_listarray as $id_product)
        {
            if ($tag_list != 0 && $id_product != 0)
            {
                if (version_compare(_PS_VERSION_, '1.6.1.0', '>='))
                {
                    $sqlstr .= '('.$tag_list.','.$id_product.','.$current_id_lang.'),';
                }
                else
                {
                    $sqlstr .= '('.$tag_list.','.$id_product.'),';
                }
            }
        }
        $sqlstr = trim($sqlstr, ',');
        if ($value == 1 && $sqlstr != '')
        {
            if (version_compare(_PS_VERSION_, '1.6.1.0', '>='))
            {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'product_tag` (id_tag,id_product,id_lang) VALUES '.psql($sqlstr);
            }
            else
            {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'product_tag` (id_tag,id_product) VALUES '.psql($sqlstr);
            }
            Db::getInstance()->Execute($sql);
        }

        if (!empty($product_list))
        {
            //update date_upd
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd = NOW() WHERE id_product IN ('.pInSQL($product_list).')');
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd = NOW() WHERE id_product IN ('.pInSQL($product_list).') AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
            }
            // PM Cache
            ExtensionPMCM::clearFromIdsProduct($product_list);
        }
    }

    if ($action == 'updateName')
    {
        $sql = 'UPDATE '._DB_PREFIX_."tag SET `name`='".psql($value)."' WHERE id_tag=".(int) $tag_list;
        Db::getInstance()->Execute($sql);
    }

    if ($action == 'updateLang')
    {
        $sql = 'UPDATE '._DB_PREFIX_."tag SET `id_lang`='".psql($value)."' WHERE id_tag=".(int) $tag_list;
        Db::getInstance()->Execute($sql);
    }

    if ($action == 'addSeltag')
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_tag` = 0 OR `id_product` = 0';
        Db::getInstance()->Execute($sql);
        $sqlstr = array();
        $product_list = explode(',', $product_list);
        $id_taglist = explode(',', $tag_list);
        foreach ($product_list as $id_product)
        {
            foreach ($id_taglist as $id_tag)
            {
                if ($id_product != 0 && $id_tag != 0)
                {
                    $sqlstr[] = '('.$id_product.','.$id_tag.')';
                }
            }
        }
        $sqlstr = join(',', array_unique($sqlstr));
        if ($sqlstr != '')
        {
            $sql = 'INSERT INTO `'._DB_PREFIX_.'product_tag` (id_product,id_tag) VALUES '.psql($sqlstr);
            Db::getInstance()->Execute($sql);
        }
    }

    if ($action == 'deleteSeltag')
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'product_tag` WHERE `id_tag` IN ('.pInsql($tag_list).') AND `id_product` IN ('.pInSQL($product_list).')';
        Db::getInstance()->Execute($sql);
    }
}
