<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_category = (int) Tools::getValue('id_category', 0);
$row = explode(';', Tools::getValue('positions'));
$todo = array();
$updated_products = array();
foreach ($row as $v)
{
    if ($v != '')
    {
        $pos = explode(',', $v);
        $todo[] = 'UPDATE '._DB_PREFIX_.'category_product SET position='.((int) $pos[1]).' WHERE id_category='.(int) $id_category.' AND id_product='.(int) $pos[0];
        $updated_products[(int) $pos[0]] = (int) $pos[0];
    }
}
foreach ($todo as $task)
{
    Db::getInstance()->Execute($task);
}

// PM Cache
if (!empty($updated_products))
{
    ExtensionPMCM::clearFromIdsProduct($updated_products);
}
