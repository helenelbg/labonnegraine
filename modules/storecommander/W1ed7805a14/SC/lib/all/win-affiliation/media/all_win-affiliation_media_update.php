<?php

    $id_banner = (int) Tools::getValue('gr_id', 0);
    $action = (Tools::getValue('action', ''));
    $value = (int) Tools::getValue('value', 0);
    $id_banners = (Tools::getValue('ids', ''));
    $is_connector = false;

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        if (!empty($id_banner))
        {
            $fields = array('name', 'active');
            $todo = array();
            foreach ($fields as $field)
            {
                if (isset($_POST[$field]))
                {
                    $val = Tools::getValue($field);
                    $todo[] = $field."='".psql(html_entity_decode($val))."'";
                }
            }
            if (count($todo))
            {
                $sql = 'UPDATE '._DB_PREFIX_.'scaff_banner SET '.join(' , ', $todo).' WHERE id_banner='.(int) $id_banner;
                Db::getInstance()->Execute($sql);
            }
        }
        $newId = Tools::getValue('gr_id');
        $action = 'update';
        $is_connector = true;
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'deleted')
    {
        if (!empty($id_banner))
        {
            $dir = _PS_IMG_DIR_.'/banner/';
            $banner = new SCAffBanner($id_banner);
            @unlink($dir.$banner->image);
            $banner->delete();

            /*$sql = "DELETE FROM "._DB_PREFIX_."scaff_banner
                    WHERE id_banner=".(int) $id_banner;
            Db::getInstance()->Execute($sql);*/
        }
        $newId = Tools::getValue('gr_id');
        $action = 'delete';
        $is_connector = true;
    }
    elseif ($action == 'mass_active')
    {
        if (!empty($id_banners))
        {
            $sql = 'UPDATE '._DB_PREFIX_."scaff_banner SET active='".(int) $value."' WHERE id_banner IN (".pInSQL($id_banners).')';
            Db::getInstance()->Execute($sql);
        }
    }
    elseif ($action == 'url')
    {
        $url = (Tools::getValue('url', ''));
        if (!empty($id_banner))
        {
            $sql = 'UPDATE '._DB_PREFIX_."scaff_banner SET url='".pSQL(urldecode($url))."' WHERE id_banner = '".(int)$id_banner."'";
            Db::getInstance()->Execute($sql);
        }
    }

    if ($is_connector)
    {
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
        echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
        echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
        echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
        echo '</data>';
    }
