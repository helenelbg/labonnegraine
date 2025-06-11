<?php

class productsWithoutAttachmentSegment extends SegmentCustom
{
    public $name = 'Products without attachment';
    public $liste_hooks = array(
            'segmentAutoConfig',
            'segmentAutoSqlQuery',
            'segmentAutoSqlQueryGrid',
        );

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $data_products = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
        }

        $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps.' : 'p.';

        $sql = 'SELECT p.id_product FROM `'._DB_PREFIX_.'product` p '.
            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop ='.(int) SCI::getSelectedShop().') ' : '').'
                    WHERE p.id_product NOT IN
                        (SELECT distinct patt.id_product FROM `'._DB_PREFIX_.'product_attachment` patt)'.
                    ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? " AND ".pSQL($alias)."active='".(($auto_params['active_pdt'] == 'active') ? '1' : '0'."'") : '').
                    ' ORDER BY p.id_product';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            $type = _l('Product');
            if (SCMS)
            {
                $element = new Product($row['id_product'], true);
            }
            else
            {
                $element = new Product($row['id_product']);
            }
            $name = $element->name[$params['id_lang']];
            $infos = $element->reference;
            $data_products[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
        }
        return $data_products;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['auto_params'])) {
            $auto_params = unserialize($params['auto_params']);
        }

        $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'prs.' : 'p.';

        $where=' '.(empty($params['no_operator']) ? 'AND' : '').' p.id_product NOT IN
            (SELECT distinct patt.id_product FROM `'._DB_PREFIX_.'product_attachment` patt)'.
            (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND ".pSQL($alias)."active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');

        return $where;
    }
}
