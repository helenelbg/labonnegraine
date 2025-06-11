<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $gr_id = (Tools::getValue('gr_id', 0));

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        if (SCMS)
        {
            list($id_category, $id_lang, $id_shop) = explode('_', $gr_id);
        }
        else
        {
            list($id_category, $id_lang) = explode('_', $gr_id);
        }

        $fields = array('link_rewrite', 'meta_title', 'meta_description', 'meta_keywords');
        $todo = array();
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $val = Tools::getValue($field);
                if ($field != 'link_rewrite' || ($field == 'link_rewrite' && !empty($val)))
                {
                    if ($field == 'link_rewrite')
                    {
                        $val = link_rewrite($val, Language::getIsoById($id_lang));
                    }
                    $todo[] = $field."='".pSQL(html_entity_decode($val))."'";
                }
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'category_lang SET '.join(' , ', $todo).' WHERE id_category='.(int) $id_category.' AND id_lang='.(int) $id_lang.' ';
            if (SCMS)
            {
                $sql .= ' AND id_shop='.(int) $id_shop;
            }
            Db::getInstance()->Execute($sql);
        }

        $newId = Tools::getValue('gr_id');
        $action = 'update';

        // PM Cache
        if (!empty($id_category))
        {
            ExtensionPMCM::clearFromIdsCategory($id_category);
        }
    }
    //PS_ALLOW_ACCENTED_CHARS_URL

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<data>';
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".$newId."'/>";
    echo '</data>';
