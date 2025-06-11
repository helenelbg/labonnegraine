<?php

$id_objet = (int) Tools::getValue('gr_id', 0);

if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
{
    $fields = array('firstname', 'lastname', 'alias', 'address1', 'address2', 'postcode', 'city', 'id_state', 'id_country', 'phone', 'phone_mobile', 'other');
    $todo = array();
    foreach ($fields as $field)
    {
        if (isset($_POST[$field]))
        {
            $todo[] = $field."='".psql(Tools::getValue($field))."'";
            addToHistory('address', 'modification', $field, (int) $id_objet, 0, _DB_PREFIX_.'address', psql(Tools::getValue($field)));
        }
    }
    if (count($todo))
    {
        $sql = 'UPDATE '._DB_PREFIX_.'address SET '.join(' , ', $todo).' WHERE id_address='.(int) $id_objet;
        Db::getInstance()->Execute($sql);
    }
    $newId = Tools::getValue('gr_id');
    $action = 'update';
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
echo '</data>';
