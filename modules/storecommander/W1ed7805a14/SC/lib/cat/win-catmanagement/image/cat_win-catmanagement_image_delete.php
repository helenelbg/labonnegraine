<?php

$id_lang = (int) Tools::getValue('id_lang');
$ids = (Tools::getValue('ids', 0));
$idlist = explode(',', $ids);

if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
    require_once SC_PS_PATH_DIR.'images.inc.php';
}

foreach ($idlist as $id_category)
{
    $category = new Category($id_category);
    $category->deleteImage(true);
}
