<?php

    function getLogs()
    {
        $sql = 'SELECT * 
                FROM '._DB_PREFIX_.'sc_queue_log
                ORDER BY date_add DESC';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            echo '<row id="'.$row['id_sc_queue_log'].'">';
            echo '<cell><![CDATA['.$row['id_sc_queue_log'].']]></cell>';
            echo '<cell><![CDATA['.$row['id_employee'].']]></cell>';
            echo '<cell><![CDATA['.$row['date_add'].']]></cell>';
            echo '<cell><![CDATA['.$row['name'].']]></cell>';
            echo '<cell><![CDATA['.$row['action'].']]></cell>';
            echo '<cell><![CDATA['.$row['row'].']]></cell>';
            echo '<cell><![CDATA['.print_r(json_decode($row['params']), true).']]></cell>';
            echo '</row>';
        }
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
    echo '<rows>';
    getLogs();
    echo '</rows>';
