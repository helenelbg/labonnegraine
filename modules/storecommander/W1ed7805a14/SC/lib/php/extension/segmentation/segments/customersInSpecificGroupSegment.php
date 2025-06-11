<?php

class customersInSpecificGroupSegment extends SegmentCustom
{
    public $name = 'Customers from specific group';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        global $id_lang;

        $html = '<strong>'._l('Customer groups').' :</strong><br/>';
        $html .= '<select id="id_group" style="width: 100%; height: 10em;" multiple="multiple">';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }
        $id_groups = array();
        if (!empty($values['id_groups']))
        {
            $id_groups = array_filter(explode('-', $values['id_groups']));
        }

        $sql = 'SELECT g.id_group, gl.name FROM '._DB_PREFIX_.'group g
                INNER JOIN '._DB_PREFIX_.'group_lang gl ON (g.id_group = gl.id_group AND gl.id_lang = '.(int) $id_lang.') '.
                (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'group_shop gs ON (gs.id_group = g.id_group AND gs.id_shop = '.(int) SCI::getSelectedShop().') ' : '' ) .'
                ORDER BY gl.name';
        $groups = Db::getInstance()->ExecuteS($sql);
        foreach ($groups as $group)
        {
            $html .= '<option value="'.$group['id_group'].'" '.(in_array($group['id_group'], $id_groups) ? 'selected' : '').'>'.$group['name'].'</option>';
        }
        $html .= '</select>
        <input type="hidden" name="id_groups" value="'.$values['id_groups'].'" />
                
        <script>
        $(document).ready(function(){
            $("#id_group").change(function(){
                var fields = "";
                $.each($("#id_group option:selected"), function(num, element){
                    var val = $(element).val();
                    fields = fields + val + "-";
                });
                $("input[name=id_groups]").val(fields);
            });
        });
        </script>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['id_groups']))
            {
                $array_id_groups = array_filter(explode('-', $auto_params['id_groups']));
                $ids = implode(',', $array_id_groups);

                $sql = 'SELECT DISTINCT c.id_customer
                FROM `'._DB_PREFIX_.'customer` c
                    INNER JOIN `'._DB_PREFIX_.'customer_group` cg ON (c.id_customer = cg.id_customer)
                    WHERE cg.id_group IN ('.pInSQL($ids).') '.
                    (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND c.id_shop='.SCI::getSelectedShop() : '') ;
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    $type = _l('Customer');
                    $element = new Customer($row['id_customer']);
                    $name = $element->firstname.' '.$element->lastname;
                    $infos = $element->email;
                    $array[] = array($type, $name, $infos, 'id' => 'customer_'.$row['id_customer'], 'id_display' => $row['id_customer']);
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

            if (!empty($auto_params['id_groups']))
            {
                $array_id_groups = array_filter(explode('-', $auto_params['id_groups']));
                $ids = implode(',', $array_id_groups);

                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.id_customer IN (SELECT DISTINCT c_seg.id_customer
                            FROM `'._DB_PREFIX_.'customer` c_seg
                            INNER JOIN `'._DB_PREFIX_.'customer_group` cg_seg ON (c_seg.id_customer = cg_seg.id_customer)
                            WHERE cg_seg.id_group IN ('.pInSQL($ids).'))';
            }
        }

        return $where;
    }
}
