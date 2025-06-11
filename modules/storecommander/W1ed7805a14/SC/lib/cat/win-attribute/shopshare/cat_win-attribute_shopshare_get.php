<?php

    $idlist = Tools::getValue('idlist', 0);
    $id_lang = (int) Tools::getValue('id_lang');
    $cntAttributes = count(explode(',', $idlist));
    $used = array();

    function getRows()
    {
        global $idlist,$id_lang,$used, $cntAttributes,$sc_agent;

        $multiple = false;
        if ($cntAttributes > 1)
        {
            $multiple = true;
        }

        $sql = 'SELECT s.*
                    FROM '._DB_PREFIX_.'shop s
                    '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '')."
                    WHERE s.deleted != '1'
                    ORDER BY s.id_shop_group ASC, s.name ASC";
        $res = Db::getInstance()->ExecuteS($sql);

        if (!$multiple)
        {
            $attribute_shop = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'attribute_group_shop WHERE id_attribute_group = '.(int) $idlist);
            foreach ($res as $shop)
            {
                $used[$shop['id_shop']] = array(0, 0, '', '', 0, '');

                foreach ($attribute_shop as $row)
                {
                    if ($shop['id_shop'] == $row['id_shop'])
                    {
                        $used[$shop['id_shop']][0] = 1;
                    }
                }
            }
        }
        else
        {
            foreach ($res as $shop)
            {
                $used[$shop['id_shop']] = array(0, 0, 'DDDDDD', 'DDDDDD', 0, 'DDDDDD');
                $nb_present = 0;
                $nb_active = 0;

                $sql2 = 'SELECT *
                        FROM '._DB_PREFIX_.'attribute_group_shop 
                        WHERE id_attribute_group IN ('.pInSQL($idlist).')
                        AND id_shop = '.(int) $shop['id_shop'];
                $res2 = Db::getInstance()->ExecuteS($sql2);
                foreach ($res2 as $attribute)
                {
                    if (!empty($attribute['id_attribute_group']))
                    {
                        ++$nb_present;
                    }
                }

                if ($nb_present == $cntAttributes)
                {
                    $used[$shop['id_shop']][0] = 1;
                    $used[$shop['id_shop']][2] = '7777AA';
                }
                elseif ($nb_present < $cntAttributes && $nb_present > 0)
                {
                    $used[$shop['id_shop']][2] = '777777';
                }
                if ($nb_active == $cntAttributes)
                {
                    $used[$shop['id_shop']][1] = 1;
                    $used[$shop['id_shop']][3] = '7777AA';
                }
                elseif ($nb_active < $cntAttributes && $nb_active > 0)
                {
                    $used[$shop['id_shop']][3] = '777777';
                }
            }
        }

        foreach ($res as $row)
        {
            echo '<row id="'.$row['id_shop'].'">';
            echo '<cell><![CDATA['.$row['name'].']]></cell>';
            echo '<cell style="background-color:'.((!empty($used[$row['id_shop']][2])) ? '#'.$used[$row['id_shop']][2] : '').'">'.((!empty($used[$row['id_shop']][0])) ? '1' : '0').'</cell>';
            echo '</row>';
        }
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
?>
<rows>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_prop_shopshare_grid').'</userdata>'."\n";
    if (!empty($idlist))
    {
        getRows();
    }
?>
</rows>