<?php

ini_set('display_errors', 1);

$id_lang = (int) Tools::getValue('id_lang');
$action = Tools::getValue('action', '');
$id_parent = (int) Tools::getValue('id_parent', 0);

/*
 * ACTION
*/
if (!empty($action) && $action == 'insert')
{
    $newsegment = new ScSegment();
    $newsegment->id_parent = $id_parent;
    $newsegment->name = _l('New segment');
    $newsegment->type = 'manual';
    $newsegment->description = _l('Created by %s %s on %s', false, array($sc_agent->firstname, $sc_agent->lastname, date('Y-m-d H:i:s')))."\n\n";
    $newsegment->add();

    echo $newsegment->id;
}
if (!empty($action) && $action == 'update')
{
    $id_segment = (Tools::getValue('id_segment', 0));
    $field = Tools::getValue('field', '');
    $value = Tools::getValue('value', '');
    $positions = Tools::getValue('positions');

    if (!empty($id_segment))
    {
        $err = false;

        if ($field == 'name' && empty($value))
        {
            $err = true;
        }
        if ($field == 'type' && empty($value))
        {
            $err = true;
        }

        if (!$err)
        {
            $newsegment = new ScSegment($id_segment);
            $newsegment->{$field} = $value;
            $newsegment->save();
        }
    }
    if (!empty($positions))
    {
        $segs = explode(',', $positions);
        foreach ($segs as $seg)
        {
            list($id_segment, $position) = explode('|', $seg);
            if (!empty($id_segment))
            {
                $newsegment = new ScSegment($id_segment);
                $newsegment->position = $position;
                $newsegment->save();
            }
        }
    }
}
if (!empty($action) && $action == 'delete')
{
    $ids = (Tools::getValue('ids', 0));
    $delete_children = Tools::getValue('delete_children', '0');
    if ($delete_children == 'true')
    {
        $delete_children = 1;
    }
    elseif ($delete_children == 'false')
    {
        $delete_children = 0;
    }

    function deleteChildren($id_parent)
    {
        if (!empty($id_parent))
        {
            $sql = 'SELECT *
                    FROM '._DB_PREFIX_."sc_segment
                    WHERE id_parent = '".pSQL($id_parent)."'";
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                deleteChildren($row['id_segment']);

                $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment_element
                    WHERE id_segment = " .(int) $row['id_segment'];
                Db::getInstance()->Execute($sql);

                $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment
                    WHERE id_segment = " .(int) $row['id_segment'];
                Db::getInstance()->Execute($sql);
            }
        }
    }

    if ($delete_children)
    {
        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'sc_segment
                    WHERE id_segment IN ('.pInSQL($ids).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            // DELETE CHILDREN
            deleteChildren($row['id_segment']);

            // DELETE SEGMENT
            $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment_element
                    WHERE id_segment = " .(int) $row['id_segment'];
            Db::getInstance()->Execute($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment
                    WHERE id_segment = " .(int) $row['id_segment'];
            Db::getInstance()->Execute($sql);
        }
    }
    else
    {
        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'sc_segment
                    WHERE id_segment IN ('.pInSQL($ids).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $id_parent = $row['id_parent'];

            // MOVE CHILDREN
            $sql = 'UPDATE '._DB_PREFIX_."sc_segment
                    SET id_parent = " .(int) $id_parent . "
                WHERE id_parent = '".pSQL($row['id_segment'])."'";
            Db::getInstance()->Execute($sql);

            // DELETE SEGMENT
            $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment_element
                    WHERE id_segment = " .(int) $row['id_segment'];
            Db::getInstance()->Execute($sql);

            $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment
                    WHERE id_segment = " .(int) $row['id_segment'];
            Db::getInstance()->Execute($sql);
        }
    }
}
if (!empty($action) && $action == 'duplicate')
{
    $ids = (Tools::getValue('ids', 0));
    $duplicate_content = Tools::getValue('duplicate_content', '0');
    if ($duplicate_content == 'true')
    {
        $duplicate_content = 1;
    }
    elseif ($duplicate_content == 'false')
    {
        $duplicate_content = 0;
    }

    $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'sc_segment
                    WHERE id_segment IN ('.pInSQL($ids).')';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row)
    {
        $newsegment = new ScSegment();
        $newsegment->name = $row['name'].' -copy';
        $newsegment->type = $row['type'];
        $newsegment->auto_file = $row['auto_file'];
        $newsegment->auto_params = $row['auto_params'];
        $newsegment->access = $row['access'];
        $newsegment->description = pSQL($row['description']);
        $newsegment->id_parent = $row['id_parent'];
        $newsegment->save();

        $id = $newsegment->id;

        if ($duplicate_content)
        {
            $sql = 'SELECT *
                    FROM '._DB_PREFIX_."sc_segment_element
                    WHERE id_segment = " .(int) $row['id_segment'];
            $elements = Db::getInstance()->ExecuteS($sql);
            foreach ($elements as $element)
            {
                $sql = 'INSERT INTO '._DB_PREFIX_."sc_segment_element (id_segment, id_element, type_element)
                    VALUES (" .(int) $id . "," .(int) $element['id_element'] . ",'".pSQL($element['type_element'])."')";
                Db::getInstance()->Execute($sql);
            }
        }
    }
}
if (!empty($action) && $action == 'update_properties')
{
    $id_segment = (Tools::getValue('id_segment', 0));
    $access = Tools::getValue('access', '-');
    $description = Tools::getValue('description', '');
    $auto_file = Tools::getValue('auto_file', '');
    $auto_params = Tools::getValue('auto_params', array());
    $use_filters = Tools::getValue('use_filters', 0);

    if (!empty($id_segment))
    {
        $newsegment = new ScSegment($id_segment);
        $newsegment->access = $access;
        $newsegment->description = pSQL($description);
        if (!empty($auto_file))
        {
            $newsegment->auto_file = $auto_file;
        }
        if (!empty($auto_params))
        {
            $params = array();
            foreach ($auto_params as $auto_param)
            {
                $params[$auto_param['name']] = $auto_param['value'];
            }
            $params['use_filters'] = $use_filters;
            $newsegment->auto_params = serialize($params);
        }
        $newsegment->save();
    }
}
if (!empty($action) && $action == 'merge')
{
    $ids = (Tools::getValue('ids', 0));
    $name = Tools::getValue('name', _l('Merged segments'));

    if (!empty($ids) && !empty($name))
    {
        $access = array();
        $elements = array();

        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'sc_segment
                    WHERE id_segment IN ('.pInSQL($ids).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $exp = explode('-', $row['access']);
            foreach ($exp as $a)
            {
                if (!empty($a))
                {
                    $access[$a] = $a;
                }
            }
        }

        $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'sc_segment_element
                    WHERE id_segment IN ('.pInSQL($ids).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $elements[$row['id_element'].'/'.$row['type_element']] = $row['id_element'].'/'.$row['type_element'];
        }

        $newsegment = new ScSegment();
        $newsegment->id_parent = 0;
        $newsegment->name = $name;
        $newsegment->type = 'manual';
        if (!empty($access))
        {
            $newsegment->access = '-'.implode('-', $access).'-';
        }
        $newsegment->add();

        $newId = $newsegment->id;

        foreach ($elements as $element)
        {
            if (!empty($element))
            {
                list($id_element, $type_element) = explode('/', $element);

                $newelement = new ScSegmentElement();
                $newelement->id_segment = $newId;
                $newelement->id_element = $id_element;
                $newelement->type_element = $type_element;
                $newelement->add();
            }
        }
    }
}
if (!empty($action) && $action == 'copy_access')
{
    $id_from = (int) Tools::getValue('id_from', 0);
    $id_to = (int) Tools::getValue('id_to', 0);
    if (!empty($id_from) && !empty($id_to))
    {
        $from_segment = new ScSegment($id_from);

        $to_segment = new ScSegment($id_to);
        $to_segment->access = $from_segment->access;
        $to_segment->save();

        echo $id_to;
    }
}
