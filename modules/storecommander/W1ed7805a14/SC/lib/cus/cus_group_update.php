<?php

    $action = Tools::getValue('action');
    $name = Tools::getValue('name');
    $id_lang = Tools::getValue('id_lang');

    $id = 0;

    if (!empty($action) && $action == 'insert' && !empty($name))
    {
        $group = new Group();
        if (!Validate::isGenericName($name))
        {
            echo 0;
            exit;
        }
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && version_compare(_PS_VERSION_, '1.6.0.0', '<'))
        {
            $group->name = $name;
        }
        else
        {
            $group->name = array($id_lang => $name);
        }
        if (SCMS)
        {
            $group->id_shop_list = SCI::getSelectedShopActionList();
        }
        $group->date_add = date('Y-m-d H:i:s');
        $group->date_upd = date('Y-m-d H:i:s');
        $group->price_display_method = '0';
        $group->save();
        $id = $group->id;
    }

    echo $id;
