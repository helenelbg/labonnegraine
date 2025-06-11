<?php

if (!empty($sc_agent->id_employee))
{
    $sql = 'SELECT sc_note FROM '._DB_PREFIX_.'employee WHERE id_employee = '.(int) $sc_agent->id_employee;
    $note = Db::getInstance()->getValue($sql);
    echo $note;
}
