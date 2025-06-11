<?php

class productsAllSegment extends SegmentCustom
{
    public $name = 'Products: All products';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        global $id_lang;

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

        $html .= '<br><br><strong>'._l('Operator:').'</strong><br/>
        <select id="operator" name="operator" style="width: 100%;">
            <option value="ALL" '.(!empty($values['operator']) && $values['operator'] == 'ALL' ? 'selected' : '').'>'._l('All categories').'</option>
            <option value="INCLUDE" '.(!empty($values['operator']) && $values['operator'] == 'INCLUDE' ? 'selected' : '').'>'._l('Include').'</option>
            <option value="EXCLUDE" '.(!empty($values['operator']) && $values['operator'] == 'EXCLUDE' ? 'selected' : '').'>'._l('Exclude').'</option>';
        $html .= '</select><br/><br/>';

        $id_categs = array();
        if (!empty($values['id_categs']))
        {
            $id_categs = array_filter(explode('-', $values['id_categs']));
        }

        $sql = 'SELECT DISTINCT c.id_category, cl.name FROM `'._DB_PREFIX_.'category` c
				INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang = '. $id_lang .(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND cl.id_shop='.(int) SCI::getSelectedShop() : '').')
				'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop='.(int) SCI::getSelectedShop().')' : '').'
				ORDER BY cl.name';
        $categs = Db::getInstance()->ExecuteS($sql);

        $list_categs = '';
        $list_categs_js = '';
        $num = 1;
        if (!empty($values['id_categs']))
        {
            $sql = "SELECT *
                    FROM `"._DB_PREFIX_."category` c
                    INNER JOIN `"._DB_PREFIX_."category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang=".$id_lang. (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND cl.id_shop='.(int) SCI::getSelectedShop() : '').") ".
                (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? "INNER JOIN `"._DB_PREFIX_."category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop=".(int) SCI::getSelectedShop().") " : "") ."
                    WHERE c.id_category IN (".pInSQL($values['id_categs']).")
                    ORDER BY cl.name";

            $res = Db::getInstance()->ExecuteS($sql);
            if (!empty($res))
            {
                foreach ($res as $row)
                {
                    $list_categs .= '<span>- <input type="hidden" name="id_categs[]" value="'.$row['id_category'].'" /> '.$row['name'].' <img src="lib/img/delete.gif" onclick="javascript: $(this).parent().remove();" title="'._l('Delete').'" style="cursor: pointer;" /><br/></span>';
                    $list_categs_js .= "\n".'$("#choose_categ").val('.$row['id_category'].');addCat();';
                    ++$num;
                }
            }
        }

        $html .= '<strong class="tohide" style="'. ((!empty($values['operator']) && $values['operator'] == 'ALL') ? 'display:none;' : '') .'">'._l('Add categories').':'.'</strong><br/>
        <input type="text" id="filter_categs" class="tohide" value="" placeholder="'._l('Filter by name or ID').'" style="'. ((!empty($values['operator']) && $values['operator'] == 'ALL') ? 'display:none;' : '') .'"/>
        <input type="button" id="reset_filter" class="tohide" value='._l("Reset filters").' style="'. ((!empty($values['operator']) && $values['operator'] == 'ALL') ? 'display:none;' : '') .'"/>
        <select id="choose_categ" class="tohide" style="width: 60%;float: left; '. ((!empty($values['operator']) && $values['operator'] == 'ALL') ? 'display:none;' : '') .'">';
        $html .= '<option value="">-</option>';

        $html .= $this->getLevelFromDBcustom(1);

        $html .= '</select>
        <input type="button" id="add_categ" class="tohide" value="'._l('Add').'" style="width: 38%; float:right; '. ((!empty($values['operator']) && $values['operator'] == 'ALL') ? 'display:none;' : '') .'" />
        <br/><br/><br/>
        <fieldset class="tohide" id="list_categs" style="'. ((!empty($values['operator']) && $values['operator'] == 'ALL') ? 'display:none;' : '') .'">
            <legend><strong>'._l('List of categories to use').':'.'</strong></legend>
            '/*.$list_categs*/.'
        </fieldset>

        <script>
            var num_categ = '.$num.'*1;
            $("#add_categ").on("click", function(){
                addCat();
            });
            function addCat()
            {
                var id = $("#choose_categ").val();
                var name = $( "#choose_categ option:selected" ).attr("name");
                if(id!="" && $("input:hidden[value="+id+"]").length < 1)
                {
                    $("#list_categs").append("<span>- <input type=\"hidden\" name=\"id_categs[]\" value=\""+id+"\" /> "+name+" <img src=\"lib/img/delete.gif\" onclick=\"javascript: $(this).parent().remove();\" title=\"'._l('Delete').'\" style=\"cursor: pointer;\" /><br/></span>");
                    num_categ++;
                }
                $("#choose_categ option[value=\"\"]").prop("selected", true);
            }
            '.$list_categs_js.'
            $("#choose_categ").val("");
            
            $(document).ready(function()
            {
                $("#id_categ").change(function(){
                    var fields = "";
                    $.each($("#id_categ option:selected"), function(num, element){
                        var val = $(element).val();
                        fields = fields + val + "-";
                    });
                    $("input[name=id_categs]").val(fields);
                });
                $("#operator").change(function(){
                    if ($("#operator").val() == "ALL"){
                        $.each($(".tohide"), function(num, element)
                        {
                            $(element).hide();
                        });
                    }
                    else
                    {
                        $.each($(".tohide"), function(num, element)
                        {
                            $(element).show();
                        });
                    }
                });
                $("#filter_categs").change(function(){
                    var first_to_selected = true;
                    var filter = $("#filter_categs").val().toLowerCase();
                    $.each($("#choose_categ option"), function(num, element){
                        var catid = $(element).val();
                        var catname = element.innerHTML.toLowerCase();
                        if (catname.includes(filter) || catid==filter)
                        {
                            $(element).show();
                            if (first_to_selected==true)
                            {
                                $("#choose_categ option[value="+catid+"]").prop("selected", true);
                                first_to_selected = false;
                            }
                        }
                        else
                        {
                            $(element).hide();
                        }
                    });
                });
                $("#reset_filter").click(function(){
                    if ($("#filter_categs").val() != "")
                    {
                        $("#filter_categs").val("");
                        $("#filter_categs").change();
                    }
                    $("#choose_categ option[value=\"\"]").prop("selected", true);
                });
            });
            $("#form_params").submit(function() {
              return false;
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

            $array_id_categs = array_filter(explode('-', $auto_params['id_categs']));
            $ids = implode(',', $array_id_categs);
            $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps.' : 'p.';

            $sql = 'SELECT DISTINCT p.id_product
            FROM '. _DB_PREFIX_ . 'product p' .
                (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON (p.id_product = ps.id_product and ps.id_shop = '.SCI::getSelectedShop().') ' : '').
                ((!empty($auto_params['operator']) && $auto_params['operator'] != 'ALL') ? ' INNER JOIN ' . _DB_PREFIX_ . 'category_product cp ON (p.id_product = cp.id_product) ' : '') .
                ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? " WHERE ".pSQL($alias)."active='" . ($auto_params['active_pdt'] == 'active' ? '1' : '0') . "'" : '') .
                ((!empty($auto_params['operator']) && $auto_params['operator'] != 'ALL' && !empty($array_id_categs)) ? (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? ' AND ' : ' WHERE ') . 'cp.id_category ' .($auto_params['operator'] == 'EXCLUDE' ? 'NOT' : ''). ' IN (' . $ids . ')' : '').
                ' ORDER BY p.id_product';
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row) {
                $type = _l('Product');
                if (SCMS) {
                    $element = new Product($row['id_product'], true);
                } else {
                    $element = new Product($row['id_product']);
                }
                $name = $element->name[$params['id_lang']];
                $infos = $element->reference;
                $array[] = array($type, $name, $infos, 'id' => 'product_' . $row['id_product'], 'id_display' => $row['id_product']);
            }
        }
        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        if (!empty($params['auto_params'])) {
            $auto_params = unserialize($params['auto_params']);
            $array_id_categs = array_filter(explode('-', $auto_params['id_categs']));
            $ids = implode(',', $array_id_categs);

            $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'prs.' : 'p.';

            //$where =(!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? ' ' . (empty($params['no_operator']) ? 'AND' : '') . " " . pSQL($alias) . "active='" . ($auto_params['active_pdt'] == 'active' ? '1' : '0') . "'" : '') .
            //        ((!empty($auto_params['operator']) && $auto_params['operator'] != 'ALL') ? (empty($params['no_operator']) ? ' AND' : ' ') . ' cp.id_category ' . ($auto_params['operator'] == 'EXCLUDE' ? 'NOT' : '') . ' IN (' . $ids . ')' : '' );

            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( (
                    p.id_product IN (SELECT DISTINCT p_seg.id_product
            FROM ' . _DB_PREFIX_ . 'product p_seg' .
                ((!empty($auto_params['operator']) && $auto_params['operator'] != 'ALL') ? ' INNER JOIN ' . _DB_PREFIX_ . 'category_product cp_seg ON (p_seg.id_product = cp_seg.id_product) ' : '') .
                ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? " WHERE ".pSQL($alias)."active='" . ($auto_params['active_pdt'] == 'active' ? '1' : '0') . "'" : '') .
                ((!empty($auto_params['operator']) && $auto_params['operator'] != 'ALL' && !empty($array_id_categs)) ? (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? ' AND ' : ' WHERE ') . 'cp_seg.id_category ' .($auto_params['operator'] == 'EXCLUDE' ? 'NOT' : ''). ' IN (' . $ids . ')' : '').' ) '
                .' ) )';

            return $where;
        }
    }

    public function getLevelFromDBcustom($parent_id, $values = array(), $niveau = 0, $prefix = '')
    {
        global $id_lang;

        $eservices_cat_id = SCI::getConfigurationValue('SC_ESERVICES_CATEGORY');

        $html = '';
        $sql = "SELECT DISTINCT * 
                    FROM `"._DB_PREFIX_."category` c
                    INNER JOIN `"._DB_PREFIX_."category_lang` cl ON (c.id_category = cl.id_category AND cl.id_lang=".$id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? " AND cl.id_shop =".SCI::getSelectedShop() : "")." ) ".
            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? "INNER JOIN `"._DB_PREFIX_."category_shop` cs ON (c.id_category = cs.id_category AND cs.id_shop = ".SCI::getSelectedShop().") " : "") ."
                    WHERE id_parent = ".(int)$parent_id.
            (($eservices_cat_id) ? " AND c.id_category != ".$eservices_cat_id : "")
            ." ORDER BY c.nleft, cl.name";

        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($niveau > 0)
            {
                $name = '|- '.$row['name'];
            }
            else
            {
                $name = $row['name'];
            }
            for ($i = 1; $i <= $niveau; ++$i)
            {
                $name = '&nbsp;&nbsp;&nbsp;'.$name;
            }

            $html .= '<option value="'.$row['id_category'].'" name="'.$prefix.'<strong>'.$row['name'].'</strong>'.'" '.($row['id_category'] == $values['id_category'] ? 'selected' : '').'>'.$name.'</option>';
            $html .= $this->getLevelFromDBcustom($row['id_category'], $values, ($niveau + 1), $prefix.$row['name'].' > ');
        }

        return $html;
    }

}
