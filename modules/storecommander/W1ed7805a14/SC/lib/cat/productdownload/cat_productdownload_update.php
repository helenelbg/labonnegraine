<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_objet = (int) Tools::getValue('gr_id', 0);

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $fields = array('display_filename', 'filename', 'date_expiration', 'nb_days_accessible', 'nb_downloadable', 'active');
        $todo = array();
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                if ($field == 'date_expiration' && (!Tools::getValue($field) || Tools::getValue($field) == '0000-00-00'))
                {
                    $todo[] = $field.'=NULL';
                }
                elseif (($field == 'nb_days_accessible' || $field == 'nb_downloadable') && (!Tools::getValue($field) || !is_numeric(Tools::getValue($field))))
                {
                    $todo[] = $field.'=NULL';
                }
                else
                {
                    $todo[] = $field."='".psql(Tools::getValue($field))."'";
                }
                addToHistory('product_download', 'modification', $field, (int) $id_objet, 0, _DB_PREFIX_.'product_download', psql(Tools::getValue($field)));
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'product_download SET '.join(' , ', $todo).' WHERE id_product_download='.(int) $id_objet;
            Db::getInstance()->Execute($sql);
        }
        $newId = Tools::getValue('gr_id');
        $action = 'update';

        $id_product = (int) Tools::getValue('id_product');
        // PM Cache
        if (!empty($id_product))
        {
            ExtensionPMCM::clearFromIdsProduct($id_product);
        }
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'deleted')
    {
        $download = new ProductDownload((int) ($id_objet));
        $id_product = $download->id_product;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            @unlink(_PS_DOWNLOAD_DIR_.'/'.$download->filename);
        }
        else
        {
            @unlink(_PS_DOWNLOAD_DIR_.'/'.$download->physically_filename);
        }
        $download->delete();

        $product = new Product($id_product);
        $product->is_virtual = 0;
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
            $product->product_type = PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_STANDARD;
        }
        $product->save();

        $newId = Tools::getValue('gr_id');
        $action = 'delete';
    }

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
