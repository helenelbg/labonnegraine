<?php

$id_lang = (int) Tools::getValue('id_lang');
$ids = (string) Tools::getValue('ids', 0);
$idlist = explode(',', $ids);

foreach ($idlist as $id_category)
{
    $filepath = _PS_CAT_IMG_DIR_.(int) $id_category.'_thumb.jpg';
    if (file_exists($filepath))
    {
        unlink($filepath);
    }
    $filepath = _PS_TMP_IMG_DIR_.'category_'.(int) $id_category.'-thumb.jpg';
    if (file_exists($filepath))
    {
        unlink($filepath);
    }
}
