<?php
if (!defined('STORE_COMMANDER')) { exit; }

if ($_GET['ajax'] == 1)
{
    $action = Tools::getValue('action');

    if ($action == 'get_manufacturers' )
    {
        $type = Tools::getValue('type');

        $sql = 'SELECT DISTINCT upe.id_ukooparts_manufacturer as id, upm.name FROM ' . _DB_PREFIX_ . 'ukooparts_engine upe
                    INNER JOIN ' . _DB_PREFIX_ . 'ukooparts_manufacturer upm ON (upe.id_ukooparts_manufacturer = upm.id_ukooparts_manufacturer)
                    WHERE id_ukooparts_engine_type='.(int) $type.'
                    ORDER BY name';
        $res = Db::getInstance()->ExecuteS($sql);

        $return = array();
        if (empty($res)) {
            $return[] = array('id' => 0, 'label' => _l('No manufacturer found'));
        } else {
            foreach ($res as $manu) {
                $return[] = array(
                    'id' => $manu['id'],
                    'label' => $manu['name'],
                );
            }
            exit(json_encode($return));
        }
    }
    elseif ($action == 'get_engines' ) {
        $type = Tools::getValue('type');
        $manu = Tools::getValue('manufacturer');

        $sql = 'SELECT id_ukooparts_engine AS id, model, year_start, year_end
                FROM ' . _DB_PREFIX_ . 'ukooparts_engine upe
                WHERE id_ukooparts_engine_type = ' . (int)$type . '
                AND id_ukooparts_manufacturer = ' . (int)$manu
                //.' AND model LIKE "%' . $search . '%"
                .' ORDER BY model, id ASC';
        $res = Db::getInstance()->ExecuteS($sql);

        $return = array();
        if (empty($res)) {
            $return[] = array('id' => 0, 'label' => _l('No model found'));
        } else {
            foreach ($res as $model) {
                $return[] = array(
                    'id' => $model['id'],
                    'label' => $model['model'] . ' : ' . $model['year_start'] . ' - ' . $model['year_end'],
                );
            }
            exit(json_encode($return));
        }
    }
}
