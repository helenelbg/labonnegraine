<?php

// HEADER
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/json'))
{
    header('Content-type: application/json');
}
else
{
    header('Content-type: text/json');
}

$mask = Tools::getValue('mask');
$selectedIds = Tools::getValue('selectedIds');
$customers = array();
$json = array();

// main
if (!empty($mask))
{
    echo json_encode(searchByName($mask));
}
elseif (!empty($selectedIds))
{
    echo json_encode(searchByIds($selectedIds));
}
exit;

/**
 * @param $mask
 * @param null $limit
 *
 * @return array
 *
 * @throws PrestaShopDatabaseException
 * @description recherche pour autocompletion
 */
function searchByName($mask, $limit = null)
{
    $json = array();
    $mask = pSQL($mask);
    $sql = "SELECT CONCAT(firstname,' ',lastname,' ', email) AS fullstring, email, firstname, lastname, active, id_customer 
            FROM "._DB_PREFIX_."customer
            WHERE CONCAT(firstname,' ',lastname,' ',email) LIKE '%".$mask."%'
    ";
    if ($limit)
    {
        $sql .= ' LIMIT 0, '.(int) $limit;
    }
    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    if (!empty($res))
    {
        foreach ($res as $customer)
        {
            $json['options'][] = array(
                'value' => $customer['id_customer'],
                'text' => $customer['firstname'].' '.strtoupper(
                        $customer['firstname']
                    ).' - '.$customer['email'],
            );
        }
    }

    return $json;
}

/**
 * @param $ids (format : dhtmlx5 grid.getSelected() )
 * @param null $limit
 *
 * @return array
 *
 * @throws PrestaShopDatabaseException
 * @description search customer emails for ids
 */
function searchByIds($ids, $limit = null)
{
    $sql = 'SELECT * 
            FROM '._DB_PREFIX_.'customer
            WHERE id_customer IN ('.pInSQL($ids).')
    ';
    if ($limit)
    {
        $sql .= ' LIMIT 0, '.(int) $limit;
    }

    return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
}
