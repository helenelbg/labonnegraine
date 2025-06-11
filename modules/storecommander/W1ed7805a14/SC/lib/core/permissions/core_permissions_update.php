<?php

$action = Tools::getValue('action', 'doProcessorUpdate');
switch ($action)
{
    case 'reset_pwd':
        $selection = Tools::getValue('selection');
        if (!$selection)
        {
            exit;
        }
        $selection = explode('_', $selection);
        list($itemType, $itemId) = $selection;

        $employees = array();
        if ($itemType == 'pr')
        {
            $employeesFromProfil = Db::getInstance()->executeS('SELECT id_employee
                                                            FROM `'._DB_PREFIX_.'employee`
                                                            WHERE `id_profile` = '.(int) $itemId);
            if (!$employeesFromProfil)
            {
                exit;
            }
            $employees = array_column($employeesFromProfil, 'id_employee');
        }
        else
        {
            $employees[] = (int) $itemId;
        }

        foreach ($employees as $employeeId)
        {
            $employee = new Employee((int) $employeeId);
            if (!($employee instanceof Employee) || !Validate::isLoadedObject($employee))
            {
                exit;
            }

            if (version_compare(_PS_VERSION_, '1.7.0.1', '>='))
            {
                $employee->removeResetPasswordToken();
                $employee->stampResetPasswordToken();
            }

            $pwd = Tools::passwdGen(10, 'RANDOM');
            $employee->passwd = Tools::encrypt($pwd);
            $employee->last_passwd_gen = date('Y-m-d H:i:s', time());

            if (!$employee->update())
            {
                exit(_l('The password of employee ID:%s could not be reseted', false, array($employee->id)));
            }
        }
        exit('OK');
    case 'add_mass':
    case 'delete_mass':
        $permissions = Tools::getValue('permissions', 0);
        $profil = Tools::getValue('profil', 0);
        if (!empty($permissions) && !empty($profil))
        {
            $permissions = explode(',', $permissions);

            if ($action == 'add_mass')
            {
                $value = 1;
            }
            else
            {
                $value = 0;
            }

            if (strpos($profil, 'pr_') !== false)
            {
                $_is = 'profil';
                $id = str_replace('pr_', '', $profil);
                foreach ($permissions as $permission)
                {
                    $permission = explode('#', $permission);
                    $local_permissions['profils'][$id][$permission[1]] = (int) $value;
                }
            }
            elseif (strpos($profil, 'em_') !== false)
            {
                $_is = 'employee';
                $id = str_replace('em_', '', $profil);
                foreach ($permissions as $permission)
                {
                    $permission = explode('#', $permission);
                    $local_permissions['employees'][$id][$permission[1]] = (int) $value;
                }
            }

            SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));
        }
        break;
    default:
        list($id_element, $id_permission) = explode('#', Tools::getValue('gr_id', '0#0'));
        $value = Tools::getValue('value', 0);

        $action = 0;
        if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated' && !empty($id_element) && !empty($id_permission))
        {
            if ($value != 0 && $value != 1)
            {
                $value = 0;
            }

            if (strpos($id_element, 'pr_') !== false)
            {
                $_is = 'profil';
                $id = str_replace('pr_', '', $id_element);
                $local_permissions['profils'][$id][$id_permission] = (int) $value;
            }
            elseif (strpos($id_element, 'em_') !== false)
            {
                $_is = 'employee';
                $id = str_replace('em_', '', $id_element);
                $local_permissions['employees'][$id][$id_permission] = (int) $value;
            }

            SCI::updateConfigurationValue('SC_PERMISSIONS', serialize($local_permissions));

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
        echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".Tools::getValue('gr_id')."'/>";
        echo '</data>';
}
