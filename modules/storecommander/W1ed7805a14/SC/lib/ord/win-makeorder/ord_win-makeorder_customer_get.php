<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_shop = (int) Tools::getValue('id_shop');
$filter_params = Tools::getValue('filter_params');

function getRowsFromDB()
{
    global $id_lang,$id_shop,$filter_params;

    $where = '';
    if (SCMS)
    {
        if (!empty($id_shop) && $id_shop != 'all')
        {
            $where .= " AND id_shop = '".(int) $id_shop."' ";
        }
    }
    if (!empty($filter_params))
    {
        $filter_params = explode(',', $filter_params);
        foreach ($filter_params as $filter_param)
        {
            list($field, $value) = explode('|||', $filter_param);
            if (!empty($value))
            {
                if ($field == 'id_customer')
                {
                    $where .= " AND id_customer=".(int) $value." ";
                }
                elseif ($field == 'firstname')
                {
                    $where .= " AND LOWER(firstname) LIKE '%".pSQL(strtolower($value))."%' ";
                }
                elseif ($field == 'lastname')
                {
                    $where .= " AND LOWER(lastname) LIKE '%".pSQL(strtolower($value))."%' ";
                }
                elseif ($field == 'email')
                {
                    $where .= " AND LOWER(email) LIKE '%".pSQL(strtolower($value))."%' ";
                }
                elseif ($field == 'company')
                {
                    $where .= " AND LOWER(company) LIKE '%".pSQL(strtolower($value))."%' ";
                }
            }
        }
    }

    $sql = '
        SELECT *
        FROM '._DB_PREFIX_.'customer
        WHERE 1 
        AND active = 1 
        AND deleted = 0
         '.$where.'
        ORDER BY date_add DESC
        LIMIT 100';
    $res = Db::getInstance()->ExecuteS($sql);
    $xml = '';
    if (!empty($res))
    {
        foreach ($res as $row)
        {
            $xml .= '<row id="'.$row['id_customer'].'">';
            $xml .= '    <cell>'.$row['id_customer'].'</cell>';
            $xml .= '    <cell><![CDATA['.$row['firstname'].']]></cell>';
            $xml .= '    <cell><![CDATA['.$row['lastname'].']]></cell>';
            $xml .= '    <cell><![CDATA['.$row['email'].']]></cell>';
            $xml .= '    <cell><![CDATA['.$row['company'].']]></cell>';
            $xml .= '</row>';
        }
    }

    return $xml;
}

//XML HEADER
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

$xml = getRowsFromDB();
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
        </beforeInit>
        <column id="id_customer" width="45" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
        <column id="firstname" width="160" type="ro" align="left" sort="str"><?php echo _l('Firstname'); ?></column>
        <column id="lastname" width="160" type="ro" align="left" sort="str"><?php echo _l('Lastname'); ?></column>
        <column id="email" width="300" type="ro" align="left" sort="str"><?php echo _l('Email'); ?></column>
        <column id="company" width="120" type="ro" align="left" sort="str"><?php echo _l('Company'); ?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('makeOrder_customer_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
