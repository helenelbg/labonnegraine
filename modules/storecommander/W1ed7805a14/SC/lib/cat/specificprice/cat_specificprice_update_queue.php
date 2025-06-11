<?php

@error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', 'ON');

$id_lang = Tools::getValue('id_lang', '0');
//$action = Tools::getValue('action', '');
//$field = Tools::getValue('field', '');
//$todo = Tools::getValue('todo', '');
$rows = (object) json_decode(Tools::getValue('rows', array()));
$return = 'ERROR: Try again later';

// FUNCTIONS
$debug = false;
$extraVars = '';
$updated_products = array();
$return_datas = array();

// Récupération de toutes les modifications à effectuer
if (Tools::getValue('rows'))
{
    $notUpdatedPrices = array();
    $callbacks = array();
    if (count($rows) > 0)
    {
        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        foreach ($rows as $num => $row)
        {
            $log_ids[$num] = QueueLog::add(
                $row->name,
                $row->entityId,
                $row->action,
                (!empty($row->params) ? $row->params : array()),
                (!empty($row->callback) ? $row->callback : null),
                date('Y-m-d H:i:s')
            );
        }

        // Deuxième boucle pour effectuer les
        // actions les une après les autres
        foreach ($rows as $num => $row)
        {
            $params = json_decode($row->params);
            if (!empty($log_ids[$num]))
            {
                $rowId = $row->rowId;
                $action = $row->action;
                $todo = $params->todo;
                $field = $params->field;
                switch ($field) {
                    case 'edit_specificprices':
                        $todo = str_replace(',', '.', $todo);
                        $price_type = strpos($todo, '%') ? 'percentage' : 'amount';
                        $operator = trim($todo[0], ' ') === '-' ? '-' : '+';
                        $cleanedValue = (float) trim($todo, $operator);
                        $value = 'price'.$operator.$cleanedValue;
                        if ($price_type === 'percentage')
                        {
                            $value = 'price'.$operator.'(price*('.$cleanedValue.'/100))';
                        }
                        $fixedPrice = Db::getInstance()->getRow('SELECT id_product FROM '._DB_PREFIX_.'specific_price WHERE id_specific_price='.(int) $rowId.' AND price  > -1');
                        if ($fixedPrice)
                        {
                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'specific_price SET price='.$value.' WHERE id_specific_price='.(int) $rowId);
                            $updated_products[] = (int) $fixedPrice['id_product'];
                            $callbacks[] = trim($row->callback, ';');
                        }
                        break;
                }
            }

            QueueLog::delete(($log_ids[$num]));
        }

        // PM Cache
        if (!empty($updated_products))
        {
            ExtensionPMCM::clearFromIdsProduct($updated_products);
        }

        // RETURN
        $return = json_encode(array('callback' => implode(';', array_unique($callbacks))));
    }
}

echo $return;
