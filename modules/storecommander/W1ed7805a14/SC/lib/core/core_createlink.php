<?php

    $QuickAccess = new QuickAccess();
    $tmp = array();
    foreach ($languages as $lang)
    {
        $tmp[$lang['id_lang']] = 'Store Commander';
    }
    $QuickAccess->name = $tmp;
    $QuickAccess->link = 'SC/index.php';
    $QuickAccess->new_window = true;
    $QuickAccess->add();
    echo _l('The shortcut has been created. The installation is finished you can now use Store Commander!', 1);
