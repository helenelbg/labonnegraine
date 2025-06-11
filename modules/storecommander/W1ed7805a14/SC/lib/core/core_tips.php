<?php

$action = Tools::getValue('action', null);

if (!empty($action))
{
    switch ($action) {
        case 'disable':
            $id_employee = Tools::getValue('employee', null);
            if (!empty($id_employee))
            {
                $tip_setting = (array) json_decode(SCI::getConfigurationValue('SC_TIP_LAST_READED'), true);
                $tip_setting[$id_employee]['disable'] = true;
                if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
                {
                    Configuration::updateGlobalValue('SC_TIP_LAST_READED', json_encode($tip_setting));
                }
                else
                {
                    Configuration::updateValue('SC_TIP_LAST_READED', json_encode($tip_setting));
                }
                exit(_l('Tips disabled', 1));
            }
            break;
    }
}
