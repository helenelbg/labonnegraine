<?php

class productPackSegment extends SegmentCustom
{
    public $name = 'Products: Product packs';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>!!! '._l('Caution').' !!! '._l('Only from prestashop 1.6.1.14').'</strong>
        <br/><br/>
        <strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            $sql = "SELECT GROUP_CONCAT(DISTINCT(id_product) SEPARATOR  ',')
                        FROM "._DB_PREFIX_."product WHERE cache_is_pack='1' ".
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');
            $res = Db::getInstance()->getValue($sql);

            $advp_installed = SCI::moduleIsInstalled('pm_advancedpack');
            if ($advp_installed)
            {
                $sql = "SELECT GROUP_CONCAT(DISTINCT(pm.id_pack) SEPARATOR  ',') FROM "._DB_PREFIX_.'pm_advancedpack pm'.
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? ' LEFT JOIN '._DB_PREFIX_."product p ON (p.id_product = pm.id_pack) WHERE active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');
                $res_advpack = Db::getInstance()->getValue($sql);
                if (!empty($res_advpack))
                {
                    if ($res)
                    {
                        $res = implode(',', array($res, $res_advpack));
                    }
                    else
                    {
                        $res = $res_advpack;
                    }
                }
            }
            if (!empty($res))
            {
                $res = explode(',', $res);
                foreach ($res as $id_product)
                {
                    $type = _l('Product');
                    if (SCMS)
                    {
                        $element = new Product($id_product, true);
                    }
                    else
                    {
                        $element = new Product($id_product);
                    }
                    $name = $element->name[$params['id_lang']];
                    $infos = $element->reference;
                    $array[] = array($type, $name, $infos, 'id' => 'product_'.$id_product, 'id_display' => $id_product);
                }
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);

            $condition = "p.cache_is_pack='1'";
            $advp_installed = SCI::moduleIsInstalled('pm_advancedpack');
            if ($advp_installed)
            {
                $condition = "(p.cache_is_pack='1' OR p.id_product IN (SELECT id_pack FROM "._DB_PREFIX_.'pm_advancedpack))';
            }

            $where = (empty($params['no_operator']) ? 'AND' : '').' ( '.$condition.' '.
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
        }

        return $where;
    }
}
