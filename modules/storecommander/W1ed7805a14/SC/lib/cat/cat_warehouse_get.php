<?php

$id_lang = (int) Tools::getValue('id_lang');

    function getwarehouse()
    {
        global $sc_agent;
        $tree = array();

        $shop = (int) SCI::getSelectedShop();
        if ($shop == 0)
        {
            $shop = null;
        }

        $results = Warehouse::getWarehouses((!empty($shop) ? false : true), $shop);

        $icon = 'building.png';
        foreach ($results as $key => $warehouse)
        {
            $selected = '';
            echo ' <item '.$selected.
                                    ' id="'.$warehouse['id_warehouse'].'"'.
                                    ' text="'.str_replace('&', _l('and'), $warehouse['name']).'"'.
                                    ' im0="'.$icon.'"'.
                                    ' im1="'.$icon.'"'.
                                    ' im2="'.$icon.'">
                                    <itemtext><![CDATA['.$warehouse['name']."]]></itemtext>\n";
            echo '</item>'."\n";
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
    echo '<tree id="0">';
    getwarehouse();
    echo '</tree>';
