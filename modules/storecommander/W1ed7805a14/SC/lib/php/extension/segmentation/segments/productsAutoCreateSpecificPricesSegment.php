<?php

class productsAutoCreateSpecificPricesSegment extends SegmentCustom
{
    public $name = 'Products : creating specific prices automatically';
    public $manually_add_in = 'Y';
    public $liste_hooks = array(
            'segmentAutoSqlQuery',
            'segmentAutoSqlQueryGrid',
            'segmentAutoConfig',
            'cronSegment',
        );



    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Time verifications have to be done (HH:MM):').'</strong><br/>
        <input type="text" id="playing_time" name="playing_time" value="'.(!empty($values['playing_time']) ? $values['playing_time'] : '').'" /> 
        <input type="button" id="reset_processed" value="'._l('Reset processed products').'" />
        <br/><br/>        
        <strong>'._l('Create specific price X days after the products creation date:').'</strong><br/>
        <input type="text" id="nb_days" name="nb_days" value="'.(!empty($values['nb_days']) ? $values['nb_days'] : '').'" />
        <br/>        
        <strong>'._l('Reduction percentage:').'</strong><br/>
        <input type="text" id="percent" name="percent" value="'.(!empty($values['percent']) ? $values['percent'] : '').'" />
        <br/>    
        <strong>'._l('Product quantity needs to be lower than:').'</strong><br/>
        <input type="text" id="quantity_min" name="quantity_min" value="'.(!empty($values['quantity_min']) ? $values['quantity_min'] : '').'" />        
        
        <input type="hidden" id="already" name="already" value="'.(!empty($values['already']) ? $values['already'] : '').'" />    
        <input type="hidden" id="last_cron" name="last_cron" value="'.(!empty($values['last_cron']) ? $values['last_cron'] : '').'" />        
        <script>
        $(document).ready(function(){
            $("#reset_processed").click(function(){
                $.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"productsAutoCreateSpecificPricesSegment", "function":"_resetProcessed", "params": {"id_segment":"'.(int) $params['id_segment'].'"}},function(data){
                    parent.dhtmlx.message({text:"'._l('Processed products have been reseted.').'",type:"succcess",expire:5000});
                });
            });
        });
        </script>';

        return $html;
    }

    public static function _resetProcessed($params = array())
    {
        if (!empty($params['id_segment']))
        {
            $sql = 'SELECT *
            FROM '._DB_PREFIX_."sc_segment
            WHERE id_segment =".(int)$params['id_segment'];
            $segment = Db::getInstance()->ExecuteS($sql);
            $segment = $segment[0];
            $seg_params = unserialize($segment['auto_params']);
            $seg_params['already'] = '';
            $seg_params_seria = serialize($seg_params);
            $sql = 'UPDATE '._DB_PREFIX_."sc_segment SET auto_params = '".$seg_params_seria."' WHERE id_segment=".(int)$params['id_segment'];
            Db::getInstance()->Execute($sql);
        }
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['id_segment']))
        {
            $sql = 'SELECT *
                FROM '._DB_PREFIX_."sc_segment
                WHERE id_segment =".(int)$params['id_segment'];
            $segment = Db::getInstance()->ExecuteS($sql);
            $segment = $segment[0];
            $seg_params = unserialize($segment['auto_params']);
            if (empty($seg_params['already']))
            {
                $seg_params['already'] = '';
            }
            $already_tmp = explode(',', trim($seg_params['already'], ','));
            $already = array();
            foreach ($already_tmp as $tmp)
            {
                $already[$tmp] = $tmp;
            }
            $sql = 'SELECT *
                    FROM '._DB_PREFIX_."sc_segment_element
                    WHERE id_segment =".(int)$params['id_segment']."
                    ORDER BY type_element, id_element";

            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                if ($row['type_element'] == 'product')
                {
                    $type = _l('Product');
                    $element = new Product($row['id_element']);
                    $name = $element->name[$params['id_lang']];
                    $infos = '';
                    if (!empty($already[$row['id_element']]))
                    {
                        $infos .= '<span style="color: #326201">'._l('Processed').'</span>';
                    }
                    else
                    {
                        $sql = 'SELECT *
                        FROM '._DB_PREFIX_."specific_price
                        WHERE id_product = ".(int)$row['id_element'];
                        $exist = Db::getInstance()->ExecuteS($sql);
                        if (count($exist) > 1 || (count($exist) == 1 && $exist[0]['reduction_type'] != 'percentage'))
                        {
                            $infos .= '<span style="color: #ff0000">'._l('Error').'</span>';
                        }
                        else
                        {
                            $infos .= _l('No processed');
                        }
                    }
                    $array[] = array($type, $name, $infos, 'id' => $row['type_element'].'_'.$row['id_element'], 'id_display' => $row['id_element']);
                }
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['access']) && $params['access'] == 'catalog' && !empty($params['id_segment']))
        {
            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' p.id_product IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='product' AND id_segment=".(int)$params['id_segment'].")";
        }

        return $where;
    }

    public function _executeHook_cronSegment($name, $params = array())
    {
        $date_now = date('Y-m-d H:m');
        $hour_now = date('H:m');
        $sql = 'SELECT *
                FROM '._DB_PREFIX_."sc_segment
                WHERE auto_file = 'productsAutoCreateSpecificPricesSegment'";
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $segment)
        {
            $seg_params = unserialize($segment['auto_params']);
            $seg_params['nb_days'] = (int) $seg_params['nb_days'];
            if (!empty($seg_params['nb_days']) && !empty($seg_params['percent']) && !empty($seg_params['quantity_min'])
                && !empty($seg_params['playing_time']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $seg_params['playing_time']))
            {
                $playing_time = date('Y-m-d').' '.$seg_params['playing_time'];
                if (($hour_now >= $seg_params['playing_time']) && (empty($seg_params['last_cron']) || $seg_params['last_cron'] < $playing_time))
                {
                    if (empty($seg_params['already']))
                    {
                        $seg_params['already'] = '';
                    }
                    $already = str_replace(',', "','", trim($seg_params['already'], ','));
                    $sql = 'SELECT p.id_product
                        FROM '._DB_PREFIX_.'product p
                            INNER JOIN '._DB_PREFIX_.'sc_segment_element sc_se ON (sc_se.id_element = p.id_product)
                            INNER JOIN '._DB_PREFIX_."sc_segment sc_s ON (sc_se.id_segment = sc_s.id_segment)
                        WHERE sc_se.id_segment =".(int)$segment['id_segment']."
                            AND sc_se.type_element='product'
                            AND p.date_add <= (SELECT ADDDATE('".date('Y-m-d')." 00:00:00', INTERVAL -".(int)$seg_params['nb_days'].' DAY))
                            '.(!empty($already) ? " AND p.id_product NOT IN ('".pInSQL($already)."') " : '').'
                        GROUP BY p.id_product';
                    $products = Db::getInstance()->ExecuteS($sql);
                    foreach ($products as $product)
                    {
                        $sql = 'SELECT *
                            FROM '._DB_PREFIX_."specific_price
                            WHERE id_product = ".(int)$product['id_product'];
                        $exist = Db::getInstance()->ExecuteS($sql);
                        if (count($exist) == 0)
                        {
                            $specificPrice = new SpecificPrice();
                            $specificPrice->id_product = $product['id_product'];
                            if (SCMS)
                            {
                                $specificPrice->id_shop = 0;
                                $specificPrice->id_shop_group = 0;
                            }
                            $specificPrice->id_group = 0;
                            $specificPrice->id_customer = 0;
                            $specificPrice->price = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? -1 : 0);
                            $specificPrice->id_currency = 0;
                            $specificPrice->id_country = 0;
                            $specificPrice->from_quantity = 1;
                            $specificPrice->reduction = (float) (floatval($seg_params['percent']) / 100);
                            $specificPrice->reduction_type = 'percentage';
                            $specificPrice->from = '0000-00-00 00:00:00';
                            $specificPrice->to = '0000-00-00 00:00:00';
                            $specificPrice->add();

                            $seg_params['already'] .= $product['id_product'].',';
                        }
                        elseif (count($exist) == 1 && !empty($exist[0]['id_specific_price']) && $exist[0]['reduction_type'] == 'percentage')
                        {
                            $specificPrice = new SpecificPrice((int) $exist[0]['id_specific_price']);
                            $specificPrice->reduction = (float) (floatval($seg_params['percent']) / 100);
                            $specificPrice->save();

                            $seg_params['already'] .= $product['id_product'].',';
                        }
                    }

                    $seg_params['last_cron'] = $date_now;
                    $seg_params_seria = serialize($seg_params);
                    $sql = 'UPDATE '._DB_PREFIX_."sc_segment SET auto_params = '".$seg_params_seria."' WHERE id_segment=".(int)$segment['id_segment'];
                    Db::getInstance()->Execute($sql);
                }
            }
        }
    }
}
