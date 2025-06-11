<?php

$id_lang = Tools::getValue('id_lang');

// FUNCTIONS

function missingLangGet($table)
{
    $return = array();

    if (!empty($table))
    {
        $index = 'id_'.$table;

        $only_active_lang = Tools::getValue('only_active_lang', false);
        if ($only_active_lang == 'true')
        {
            $only_active_lang = true;
        }
        else
        {
            $only_active_lang = false;
        }

        $nb_langs = count(Language::getLanguages($only_active_lang));

        // récupération des lignes qui n'ont aucune langue
        $sql = 'SELECT p.* FROM '._DB_PREFIX_.pSQL($table).' p WHERE p.id_'.$table.' NOT IN (SELECT pl.id_'.pSQL($table).' FROM '._DB_PREFIX_.pSQL($table).'_lang pl)';
        $res_all = Db::getInstance()->ExecuteS($sql);

        // récupération des lignes dont il manque au moins une langue
        $sql = 'SELECT p.*, COUNT(pl.id_lang) AS nb
        FROM '._DB_PREFIX_.pSQL($table).' p
        LEFT JOIN '._DB_PREFIX_.pSQL($table).'_lang pl ON (pl.id_'.pSQL($table).' = p.id_'.pSQL($table).') ';

        if ($only_active_lang)
        {
            $sql .= ' INNER JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = pl.id_lang)
            WHERE l.active = 1 ';
        }

        $sql .= ' GROUP BY p.id_'.pSQL($table);
        $res_partial = Db::getInstance()->ExecuteS($sql);

        // fusion des deux
        foreach ($res_all as $res)
        {
            $return[$res[$index]] = $res;
        }
        foreach ($res_partial as $res)
        {
            if (empty($return[$res[$index]]) && $res['nb'] < $nb_langs)
            {
                $return[$res[$index]] = $res;
            }
        }
    }

    return $return;
}

function missingLangMSGet($table, $fields = null)
{
    $return = array();

    if (!empty($table))
    {
        $index = 'id_'.$table;

        $only_active_lang = Tools::getValue('only_active_lang', false);
        if ($only_active_lang == 'true')
        {
            $only_active_lang = true;
        }
        else
        {
            $only_active_lang = false;
        }

        $nb_langs = count(Language::getLanguages($only_active_lang));

        if (is_array($fields) && count($fields) > 0)
        {
            $fields = implode(',p.', $fields);
            $fields = 'p.'.$fields;
        }
        else
        {
            $fields = 'p.id_'.$table;
        }

        // récupération des lignes qui n'ont aucune langue
        $sql = 'SELECT '.$fields.', s.id_shop, s.name AS shop_name
            FROM '._DB_PREFIX_.$table.' p
                INNER JOIN '._DB_PREFIX_.$table.'_shop ps ON (p.id_'.$table.' = ps.id_'.$table.')
                    INNER JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = ps.id_shop)
            WHERE
                p.id_'.$table.' NOT IN (SELECT pl.id_'.$table.' FROM '._DB_PREFIX_.$table.'_lang pl WHERE pl.id_shop = ps.id_shop)';
        $res_all = Db::getInstance()->ExecuteS($sql);

        foreach ($res_all as $res)
        {
            $return[$res[$index].'_'.$res['id_shop']] = $res;
        }
        $limit = 5000;
        $nbProduct = Db::getInstance()->getValue('SELECT COUNT(id_'.pSQL($table).') FROM '._DB_PREFIX_.pSQL($table));
        $nbiteration = ceil($nbProduct / $limit);

        for ($i = 1; $i <= $nbiteration; ++$i)
        {
            // récupération des lignes dont il manque au moins une langue
            $sql = 'SELECT p.*, s.id_shop, s.name AS shop_name, COUNT(pl.id_lang) AS nb 
            FROM '._DB_PREFIX_.pSQL($table).' p
            INNER JOIN '._DB_PREFIX_.pSQL($table).'_shop ps ON (p.id_'.pSQL($table).' = ps.id_'.pSQL($table).')
                INNER JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = ps.id_shop)
            LEFT JOIN '._DB_PREFIX_.pSQL($table).'_lang pl ON (pl.id_'.pSQL($table).' = p.id_'.pSQL($table).' AND pl.id_shop = ps.id_shop) ';

            if ($only_active_lang)
            {
                $sql .= ' INNER JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = pl.id_lang) 
                WHERE l.active = 1 ';
            }

            $p = $limit * $i - $limit;
            $sql .= ' GROUP BY p.id_'.$table.', s.id_shop 
            LIMIT '.(int) $p.','.(int) $limit;
            //echo $sql;
            $res_partial = Db::getInstance()->ExecuteS($sql);

            // fusion des deux

            foreach ($res_partial as $res)
            {
                if (empty($return[$res[$index].'_'.$res['id_shop']]) && $res['nb'] < $nb_langs)
                {
                    $return[$res[$index].'_'.$res['id_shop']] = $res;
                }
            }
        }

        return $return;
    }
}

function dbExecuteForeignKeyOff($sqlRequest, $return_insert_id = false)
{
    $sql = 'SET FOREIGN_KEY_CHECKS=0';
    Db::getInstance()->execute($sql);
    $return = Db::getInstance()->execute($sqlRequest);
    if (!empty($return_insert_id))
    {
        $return = Db::getInstance()->Insert_ID()."\n";
    }
    $sql = 'SET FOREIGN_KEY_CHECKS=1';
    Db::getInstance()->execute($sql);

    return $return;
}

// EXECUTE ACTION
require_once dirname(__FILE__).'/all_'.basename(dirname(__FILE__)).'_controls.php';
if (!empty($controls) && is_array($controls))
{
    $get_check = Tools::getValue('check');
    if (!empty($get_check) && array_key_exists($get_check, $controls))
    {
        if (file_exists(SC_TOOLS_DIR.basename(dirname(__FILE__)).'/actions/'.$get_check.'.php'))
        {
            require_once SC_TOOLS_DIR.basename(dirname(__FILE__)).'/actions/'.$get_check.'.php';
        }
        elseif (file_exists(dirname(__FILE__).'/actions/'.$get_check.'.php'))
        {
            require_once dirname(__FILE__).'/actions/'.$get_check.'.php';
        }
        else
        {
            exit(json_encode(array(
                    'results' => 'KO',
                    'contentType' => 'grid',
                    'content' => '',
                    'title' => _l('Action file not found'),
                    'contentJs' => '',
            )));
        }
    }
    else
    {
        exit(json_encode(array(
                'results' => 'KO',
                'contentType' => 'grid',
                'content' => '',
                'title' => _l('Control not found'),
                'contentJs' => '',
        )));
    }
}
