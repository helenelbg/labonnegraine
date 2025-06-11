<?php

    $id_lang = (int) Tools::getValue('id_lang', 1);
    $profil = Tools::getValue('id', 0);

    $_is = 'profil';

    if (strpos($profil, 'pr_') !== false)
    {
        $_is = 'profil';
        $id = str_replace('pr_', '', $profil);
        if (!empty($local_permissions['profils'][$id]))
        {
            unset($local_permissions['profils'][$id]);
        }
    }
    elseif (strpos($profil, 'em_') !== false)
    {
        $_is = 'employee';
        $id = str_replace('em_', '', $profil);
        if (!empty($local_permissions['employees'][$id]))
        {
            unset($local_permissions['employees'][$id]);
        }
    }

    SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));
