<?php

    foreach ($languages as $lang)
    {
        $sql = 'SELECT pl1.id_product FROM '._DB_PREFIX_.'product_lang pl1 WHERE NOT EXISTS (SELECT * FROM '._DB_PREFIX_.'product_lang pl2 WHERE pl1.id_product=pl2.id_product AND id_lang='.(int) $lang['id_lang'].')';
        $res = Db::getInstance()->ExecuteS($sql);
        if (count($res))
        {
            foreach ($res as $p)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'product_lang (id_product,id_lang) VALUES ('.(int) ($p['id_product']).','.(int) $lang['id_lang'].')';
                Db::getInstance()->Execute($sql);
            }
        }

        $sql = 'SELECT il1.id_image FROM '._DB_PREFIX_.'image_lang il1 WHERE il1.id_lang='.(int) $lang['id_lang'].' AND NOT EXISTS (SELECT * FROM '._DB_PREFIX_.'image_lang il2 WHERE il1.id_image=il2.id_image AND id_lang='.(int) $lang['id_lang'].')';
        $res = Db::getInstance()->ExecuteS($sql);
        if (count($res))
        {
            foreach ($res as $p)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_.'image_lang (id_image,id_lang) VALUES ('.(int) ($p['id_image']).','.(int) $lang['id_lang'].')';
                Db::getInstance()->Execute($sql);
            }
        }
    }

    echo 'Ok';
