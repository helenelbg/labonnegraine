<?php

$id_lang = (int) Tools::getValue('id_lang', 0);
$id_cart_rule = (int) Tools::getValue('gr_id', 0);
$action = Tools::getValue('action');

if (!empty($action) && $action == 'delete')
{
    $ids_array = explode(',', Tools::getValue('ids'));
    if (!empty($ids_array))
    {
        foreach ($ids_array as $id_cartrules)
        {
            if (!empty($id_cartrules))
            {
                $cartrule = new CartRule((int) $id_cartrules);
                $cartrule->delete();
            }
        }
    }
}
elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
{
    $fields = array('active', 'date_from', 'date_to', 'quantity', 'quantity_per_user');
    $todo = array();
    foreach ($fields as $field)
    {
        if (isset($_GET[$field]) || isset($_POST[$field]))
        {
            $todo[] = $field."='".psql(Tools::getValue($field))."'";
        }
    }
    if (count($todo))
    {
        $sql = 'UPDATE '._DB_PREFIX_.'cart_rule SET '.join(' , ', $todo).' WHERE id_cart_rule='.(int) $id_cart_rule;
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
echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
echo '</data>';
