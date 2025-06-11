<?php

    if (!defined('SC_DIR'))
    {
        exit;
    }

    $action = Tools::getValue('action');

    switch ($action){
        case 'deleteall':
            Db::getInstance()->Execute('TRUNCATE`'._DB_PREFIX_.'pagenotfound`');
            exit('Ok');
            break;
    }
