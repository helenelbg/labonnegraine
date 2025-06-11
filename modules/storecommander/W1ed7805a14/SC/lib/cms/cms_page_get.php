<?php

    $id_lang = (int) Tools::getValue('id_lang');

    $view = Tools::getValue('view', 'grid_light');
    $grids = SCI::getGridViews('cms');
    sc_ext::readCustomCMSGridsConfigXML('gridConfig');

    $exportedCms = array();
    $cdata = (isset($_COOKIE['cg_cms_treegrid_col_'.$view]) ? $_COOKIE['cg_cms_treegrid_col_'.$view] : '');
    //check validity
    $check = explode(',', $cdata);
    foreach ($check as $c)
    {
        if ($c == 'undefined')
        {
            $cdata = '';
            break;
        }
    }
    if ($cdata != '')
    {
        $grids[$view] = $cdata;
    }

    $cols = explode(',', $grids[$view]);

    $colSettings = array();
    $colSettings = SCI::getGridFields('cms');
    sc_ext::readCustomCMSGridsConfigXML('colSettings');

    function getColSettingsAsXML()
    {
        global $cols,$colSettings,$view;

        $uiset = uisettings::getSetting('cms_grid_'.$view);
        $hidden = $sizes = array();
        if (!empty($uiset))
        {
            $tmp = explode('|', $uiset);
            $tmp = explode('-', $tmp[2]);
            foreach ($tmp as $v)
            {
                $s = explode(':', $v);
                $sizes[$s[0]] = $s[1];
            }
            $tmp = explode('|', $uiset);
            $tmp = explode('-', $tmp[0]);
            foreach ($tmp as $v)
            {
                $s = explode(':', $v);
                $hidden[$s[0]] = $s[1];
            }
        }

        $xml = '';

        foreach ($cols as $id => $col)
        {
            $xml .= '<column id="'.$col.'"'.(sc_array_key_exists('format', $colSettings[$col]) ?
                    ' format="'.$colSettings[$col]['format'].'"' : '').
                    ' width="'.(sc_array_key_exists($col, $sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
                    ' hidden="'.(sc_array_key_exists($col, $hidden) ? $hidden[$col] : 0).'"'.
                    ' align="'.$colSettings[$col]['align'].'" 
                    type="'.$colSettings[$col]['type'].'" 
                    sort="'.$colSettings[$col]['sort'].'" 
                    color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
            if (!empty($colSettings[$col]['options']))
            {
                foreach ($colSettings[$col]['options'] as $k => $v)
                {
                    $xml .= '<option value="'.str_replace('"', '\'', $k).'"><![CDATA['.$v.']]></option>';
                }
            }
            $xml .= '</column>'."\n";
        }

        return $xml;
    }

    function getFooterColSettings()
    {
        global $cols,$colSettings;

        $footer = '';
        foreach ($cols as $id => $col)
        {
            if (sc_array_key_exists($col, $colSettings) && sc_array_key_exists('footer', $colSettings[$col]))
            {
                $footer .= $colSettings[$col]['footer'].',';
            }
            else
            {
                $footer .= ',';
            }
        }

        return $footer;
    }

    function getFilterColSettings()
    {
        global $cols,$colSettings;

        $filters = '';
        foreach ($cols as $id => $col)
        {
            if ($colSettings[$col]['filter'] == 'na')
            {
                $colSettings[$col]['filter'] = '';
            }
            $filters .= $colSettings[$col]['filter'].',';
        }
        $filters = trim($filters, ',');

        return $filters;
    }

    function getPages($id_cms_category = null)
    {
        global $col,$id_lang,$cols,$colSettings;
        $sql = 'SELECT * ';
        sc_ext::readCustomCMSGridsConfigXML('SQLSelectDataSelect');
        $sql .= ' FROM '._DB_PREFIX_.'cms c ';
        if (SCMS && SCI::getSelectedShop() > 0)
        {
            $sql .= ' INNER JOIN '._DB_PREFIX_."cms_shop cs ON (c.id_cms = cs.id_cms AND cs.id_shop = '".(int) SCI::getSelectedShop()."') ";
            $sql .= ' INNER JOIN '._DB_PREFIX_."cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = '".(int) $id_lang."'".(version_compare(_PS_VERSION_, '1.6.0.12', '>=') ? " AND cl.id_shop = '".(int) SCI::getSelectedShop()."'" : '').')';
        }
        else
        {
            $sql .= ' INNER JOIN '._DB_PREFIX_."cms_lang cl ON (c.id_cms = cl.id_cms AND cl.id_lang = '".(int) $id_lang."') ";
        }
        sc_ext::readCustomCMSGridsConfigXML('SQLSelectDataLeftJoin');
        $sql .= " WHERE c.id_cms_category = '".(int) $id_cms_category."'
        ORDER BY c.position ASC";
        $res = Db::getInstance()->ExecuteS($sql);

        foreach ($res as $cmsRow)
        {
            echo '<row id="'.$cmsRow['id_cms'].'">';
            echo '  <userdata name="id_cms">'.(int) $cmsRow['id_cms'].'</userdata>';
            sc_ext::readCustomCMSGridsConfigXML('rowUserData', $cmsRow);
            foreach ($cols as $key => $col)
            {
                switch ($col){
                    case 'id':
                        echo '<cell>'.$cmsRow['id_cms'].'</cell>'; //  style=\"color:tan\"
                        break;
                    case 'meta_title':case 'meta_description':case 'meta_keywords':case 'link_rewrite':
                        echo '<cell><![CDATA['.$cmsRow[$col].']]></cell>';
                        break;
                    case 'content':
                        echo '<cell><![CDATA['.str_replace(array("\r", "\n"), '', $cmsRow[$col]).']]></cell>';
                        break;
                    default:
                        sc_ext::readCustomCMSGridsConfigXML('rowData');
                        if (sc_array_key_exists('buildDefaultValue', $colSettings[$col]) && $colSettings[$col]['buildDefaultValue'] != '')
                        {
                            if ($colSettings[$col]['buildDefaultValue'] == 'ID')
                            {
                                echo '<cell>ID'.$cmsRow['id_cms'].'</cell>';
                            }
                        }
                        else
                        {
                            if ($cmsRow[$col] == '' || $cmsRow[$col] === 0 || $cmsRow[$col] === 1)
                            { // opti perf is_numeric($cmsRow[$col]) ||
                                echo '<cell>'.$cmsRow[$col].'</cell>';
                            }
                            else
                            {
                                echo '<cell><![CDATA['.$cmsRow[$col].']]></cell>';
                            }
                        }
                }
            }
            echo "</row>\n";
        }
        if ($_GET['tree_mode'] == 'all')
        {
            getSubCategoriesPages($id_cms_category);
        }
    }

    function getSubCategoriesPages($parent_id)
    {
        $sql = 'SELECT c.id_cms_category FROM '._DB_PREFIX_.'cms_category c WHERE c.id_parent='.(int) $parent_id;
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            getPages($row['id_cms_category']);
            getSubCategoriesPages($row['id_cms_category']);
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
    echo '<rows><head>';
    echo getColSettingsAsXML();
    echo '<afterInit>
                        <call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
                        <call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call>
                    </afterInit>';
    echo '</head>';

    $uiset = uisettings::getSetting('cms_grid_'.$view);
    if (!empty($uiset))
    {
        $tmp = explode('|', $uiset);
        $uiset = '|'.$tmp[1].'||'.$tmp[3];
    }
    echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
    echo '<userdata name="LIMIT_SMARTRENDERING">'.(int) _s('CMS_PAGE_LIMIT_SMARTRENDERING').'</userdata>';
    sc_ext::readCustomCMSGridsConfigXML('gridUserData');
    echo "\n";
    getPages(Tools::getValue('idc'));

    echo '</rows>';
