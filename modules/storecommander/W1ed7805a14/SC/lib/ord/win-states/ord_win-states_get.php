<?php
$idLang = (int) Tools::getValue('id_lang');
$idShop = (int) Tools::getValue('id_shop', SCI::getSelectedShop());

function getRowsFromDB($idShop)
{
    global $idLang;
    //recuperation des informations des etats
    $sql = '
        SELECT ps.*, psl.name AS state_name
        FROM '._DB_PREFIX_.'order_state ps
            LEFT JOIN '._DB_PREFIX_.'order_state_lang psl ON (ps.id_order_state=psl.id_order_state AND psl.id_lang='.(int) $idLang.')
            LEFT JOIN '._DB_PREFIX_.'module pm ON (ps.module_name=pm.name)
            LEFT JOIN '._DB_PREFIX_.'module_shop pms ON (pm.id_module=pms.id_module)
            LEFT JOIN '._DB_PREFIX_.'shop pmss ON (pmss.id_shop=pms.id_shop)
        ';

    if (is_numeric($idShop) && $idShop != 0)
    {
        $sql .= ' AND (pms.id_shop = '.$idShop.' OR ps.module_name = "")';
    }

    $orderStates = Db::getInstance()->ExecuteS($sql);

    return $orderStates;
}

function getOrderStatesInfos($idShop)
{
    $orderStates = getRowsFromDB($idShop);

    $infos = array();
    if (!empty($orderStates))
    {
        foreach ($orderStates as $key => $orderState)
        {
            //get history infos
            $sql = '
            SELECT DISTINCT(oh.id_order),COUNT(oh.id_order_state) as nb, MAX(oh.date_add) as lastUsed, o.id_shop
            FROM '._DB_PREFIX_.'order_history oh
            LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_order=oh.id_order)
                WHERE id_order_state = '.$orderState['id_order_state'].'
            ';
            if (is_numeric($idShop) && $idShop != 0)
            {
                $sql .= ' AND o.id_shop = '.$idShop;
            }
            $infos = Db::getInstance()->ExecuteS($sql);
            if (empty($infos))
            {
                continue;
            }
            $orderStates[$key]['nb'] = $infos[0]['nb'];
            $orderStates[$key]['lastUsed'] = $infos[0]['lastUsed'];
        }
    }

    return $orderStates;
}

function getXmlResults($idShop)
{
    $res = getOrderStatesInfos($idShop);
    $xml = '';
    //recuperation de la liste des boutiques
    if (!empty($res))
    {
        foreach ($res as $row)
        {
            $hidden = (int) SCI::getScIsDisplayableOrderState($row['id_order_state'], $idShop);
            $hiddenBgColor = '';
            // si toutes les boutiques et masqué pour ou moins une boutique
            if ($hidden === 2)
            {
                $hidden = 1;
                $hiddenBgColor = ' bgColor="#b389c5"';
            }
            //dump($row);
//            $cellColor = ($row['active'] == 0 || (date('Y-m-d H:i:s') > $row['date_to']) ? 'bgColor="'.$row['color'].'"' : '');
            $xml .= "<row id='".$row['id_order_state']."'>";
            $xml .= '<cell style="color:#999999">'.$row['id_order_state'].'</cell>';
            $xml .= '<cell style="color:#FFFFFF" bgColor="'.$row['color'].'"><![CDATA['.$row['state_name'].']]></cell>';
            $xml .= '<cell><![CDATA['.$row['module_name'].']]></cell>';
            $xml .= '<cell><![CDATA['.$row['nb'].']]></cell>';
            $xml .= '<cell><![CDATA['.$row['lastUsed'].']]></cell>';
            $xml .= '<cell'.$hiddenBgColor.'><![CDATA['.$hidden.']]></cell>';
            $xml .= '</row>';
        }
    }

    return $xml;
}

if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
//XML HEADER
$xml = getXmlResults($idShop);
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader">
                <param><![CDATA[#numeric_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter]]></param>
            </call>
        </beforeInit>
        <column id="id_state" width="45" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
        <column id="name" type="ro" align="left" sort="str"><?php echo _l('Order state'); ?></column>
        <column id="module" type="ro" align="left" sort="str"><?php echo _l('Module'); ?></column>
        <column id="used_by" type="ro" align="left" sort="str"><?php echo _l('Number of uses'); ?></column>
        <column id="lasUsed" type="ro" align="left" sort="str"><?php echo _l('Last used at'); ?></column>
        <column id="hidden" type="coro" align="left" sort="str"
                options="<?php array(1 => _l('No'), 0 => _l('Yes')); ?>"><?php echo _l('Display in Store Commander'); ?>
            <option value="1"><![CDATA[<?php echo _l('No'); ?>]]></option>
            <option value="0"><![CDATA[<?php echo _l('Yes'); ?>]]></option>
        </column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('orderstates_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
