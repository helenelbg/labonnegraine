<?php

if (empty($sc_agent->id_employee))
{
    exit();
}

// RECUPERATION ACTUELLE CONFIG
$employee_settings = UISettings::load_ini_file();

// ECRASEMENT AVEC LES NOUVELLES DONNEES
$name = Tools::getValue('name', '');
$data = Tools::getValue('data', '');
if (!empty($name))
{
    $employee_settings[$name] = $data;
}

if ($name == 'all')
{
    $employee_settings = array();
}
if (substr($name, 0, 15) == 'cat_combination')
{
    $new_value = '';
    $parts = explode('|', $employee_settings[$name]);
    foreach ($parts as $i => $part)
    {
        $new_parts = '';
        if ($i > 0)
        {
            $new_value .= '|';
        }

        $fields = explode('-', $part);
        foreach ($fields as $j => $field)
        {
            if ($j > 0)
            {
                $new_parts .= '-';
            }

            list($name_field, $value_field) = explode(':', $field);
            if (!empty($name_field))
            {
                $new_parts .= $name_field.':'.$value_field;
            }
        }
        $new_value .= $new_parts;
    }
    $employee_settings[$name] = $new_value;
}

$employee_settings = UISettingsConvert::convert($employee_settings);

// ECRITURE DANS FICHIER INI
UISettings::write_ini_file($employee_settings, false);
