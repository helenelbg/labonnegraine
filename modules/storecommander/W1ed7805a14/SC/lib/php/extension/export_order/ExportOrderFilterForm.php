<?php

class ExportOrderFilterForm
{
    public $orderRules;
    public $orderProductRules;
    public $orderTotalRules;
    public $customerRules;
    public $addressDeliveryRules;
    public $addressInvoiceRules;
    public $groupRules;
    private $id_lang;

    public function __construct($id_lang)
    {
        $this->id_lang = (int)$id_lang;
        $this->initOrderRules();
        $this->initOrderProductRules();
        $this->initOrderTotalRules();
        $this->initCustomerRules();
        $this->initAddressDeliveryRules();
        $this->initAddressInvoiceRules();
        $this->initGroupRules();
    }

    public function displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors)
    {
        $html = '';

        // common rules properties
        $displayName = $ruleInfos['text'];
        $fieldType = $ruleInfos['type'];

        // the the rule components by its name
        $rule = ExportOrderFilter::getRuleByName($ruleName, $ruleDescriptors);
        if (!$rule)
        {
            ExportOrderTools::addLog('CList edit : No rule '.$ruleName.' into the descriptors');
        }

        switch ($fieldType)
        {
            case 'boolean':
                $html = $this->displayBooleanRule($ruleName, $displayName, $rule, $ruleInfos['choice1'], $ruleInfos['choice2']);
                break;
            case 'string':
                $html = $this->displayStringRule($ruleName, $displayName, $rule);
                break;
            case 'date':
                $html = $this->displayDateRule($ruleName, $displayName, $rule);
                break;
            case 'number':
                $html = $this->displayNumberRule($ruleName, $displayName, $rule);
                break;
            case 'group':
                $html = $this->displayGroupRule($ruleName, $displayName, $rule);
                break;
            case 'sql_list':
            case 'list':
                $html = $this->displayListRule($ruleName, $displayName, $rule, $ruleInfos);
                break;
            case 'listmultiple':
                $html = $this->displayListRule($ruleName, $displayName, $rule, $ruleInfos, true);
                break;
            case 'order_state':
                $html = $this->displayOrderStateRule($ruleName, $displayName, $rule);
                break;
            case 'product':
                $html = $this->displayProductRule($ruleName, $displayName, $rule);
                break;
            case 'pdt_category':
                $html = $this->displayProductCategoryRule($ruleName, $displayName, $rule);
                break;
            default:
                ExportOrderTools::addLog('CList edit : unable to display rule '.$ruleName.' with type '.$fieldType);

                return '';
        }

        return $html;
    }

    /**
     * Output a HTML <TR> to display a BOOLEAN field rule.
     */
    public function displayBooleanRule($ruleName, $displayName, $rule, $choiceText1, $choiceText2)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $choice = 1;
        if ($rule)
        {
            $choice = (($rule[1] == '=' && $rule[2] == '1') || ($rule[1] == '<>' && $rule[2] == '0')) ? 1 : 2;
        }

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : boolean rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        $html = '
            <tr>
                <td class="field_label">
                    <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                    <input type="hidden" id="'.$ruleName.'_type" value="boolean" />
                </td>
                <td>
                    <div id="1_panel_'.$ruleName.'" style="display:none;">
                        <input type="radio" value="1" id="'.$ruleName.'_choice_1" name="'.$ruleName.'_choice" '.($choice == 1 ? 'checked="checked"' : '').' /> <label class="sclabel2" for="'.$ruleName.'_choice_1">'.$choiceText1.'</label>
                        <input type="radio" value="0" id="'.$ruleName.'_choice_2" name="'.$ruleName.'_choice" '.($choice == 2 ? 'checked="checked"' : '').' /> <label class="sclabel2" for="'.$ruleName.'_choice_2">'.$choiceText2.'</label>
                    </div>
                </td>
                <td style="width:110px;">
                </td>
            </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a STRING field rule.
     */
    public function displayStringRule($ruleName, $displayName, $rule)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $operator = $rule ? $rule[1] : null;
        $value = $rule ? $rule[2] : '';

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : String rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        $html = '
            <tr>
                <td class="field_label">
                    <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                    <input type="hidden" id="'.$ruleName.'_type" value="string" />
                </td>
                <td>
                    <div id="1_panel_'.$ruleName.'" style="display:none;">
                        <select id="'.$ruleName.'_operator">
                            <option value="begin" '.($operator == 'begin' ? 'selected="selected"' : '').'>'._l('Begins with').'</option>
                            <option value="end" '.($operator == 'end' ? 'selected="selected"' : '').'>'._l('Ends with').'</option>
                            <option value="like" '.($operator == 'like' ? 'selected="selected"' : '').'>'._l('Contains').'</option>
                            <option value="equal" '.($operator == 'equal' ? 'selected="selected"' : '').'>'._l('Equals').'</option>
                        </select>
                        <input type="text" size="33" id="'.$ruleName.'_value" value="'.$value.'" />
                    </div>
                </td>
                <td style="width:130px;">
                    <div id="2_panel_'.$ruleName.'" style="display:none;">
                        <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label>
                    </div>
                </td>
            </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a DATE field rule.
     */
    public function displayDateRule($ruleName, $displayName, $rule)
    {
        // default values
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $isRange = false;
        $operator = '<=';
        $value = '10';
        $unit = 'DAY';
        $value1 = '';
        $operator1 = '>=';
        $operator2 = '';
        $value2 = '';

        // values from rule if any
        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : Date rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));

            // is it a range date ?
            $isRange = (count($rule) == 5);
            if (!$isRange)
            {
                $operator = $rule[1];
                $value = $rule[2];
                $unit = $rule[3];
            }
            else
            {
                $value1 = $rule[1];
                $operator1 = $rule[2];
                $operator2 = $rule[3];
                $value2 = $rule[4];
            }
        }

        $html = '
                <tr>
                    <td class="field_label">
                        <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                        <input type="hidden" id="'.$ruleName.'_type" value="date" />
                    </td>
                    <td>
                        <div id="1_panel_'.$ruleName.'" style="display:none;">
                            <div>
                                <input type="radio" value="norange" id="'.$ruleName.'_date_1" name="'.$ruleName.'_date" '.(!$isRange ? 'checked="checked"' : '').' /> <label class="sclabel2" for="'.$ruleName.'_date_1"></label>
                                <select id="'.$ruleName.'_1_operator">
                                    <option value="<=" '.($operator == '<=' ? 'selected="selected"' : '').'>'._l('Older than').'</option>
                                    <option value=">=" '.($operator == '>=' ? 'selected="selected"' : '').'>'._l('Younger than').'</option>
                                </select>
                                '._l('the last').'
                                <input type="text" size="5" maxlength="4" id="'.$ruleName.'_1_value" value="'.$value.'" />
                                <select id="'.$ruleName.'_1_unit">
                                    <option value="DAY" '.($unit == 'DAY' ? 'selected="selected"' : '').'>'._l('Day(s)').'</option>
                                    <option value="MONTH" '.($unit == 'MONTH' ? 'selected="selected"' : '').'>'._l('Month(s)').'</option>
                                    <option value="YEAR" '.($unit == 'YEAR' ? 'selected="selected"' : '').'>'._l('Year(s)').'</option>
                                </select>
                            </div>
                            <div>
                                <input type="radio" value="range" id="'.$ruleName.'_date_2" name="'.$ruleName.'_date" '.($isRange ? 'checked="checked"' : '').' /> <label class="sclabel2" for="'.$ruleName.'_date_2"></label>
                                <select id="'.$ruleName.'_2_operator1">
                                    <option value="<=" '.($operator1 == '<=' ? 'selected="selected"' : '').'>'._l('Before').'</option>
                                    <option value="=" '.($operator1 == '=' ? 'selected="selected"' : '').'>'._l('Equals').'</option>
                                    <option value=">=" '.($operator1 == '>=' ? 'selected="selected"' : '').'>'._l('After').'</option>
                                    <option value="<>" '.($operator1 == '<>' ? 'selected="selected"' : '').'>'._l('Different').'</option>
                                </select>
                                <input type="text" size="13" maxlength="10" id="'.$ruleName.'_2_date_value1" value="'.$value1.'" class="calendar"/>
                                 '._l('and').' 
                                <select id="'.$ruleName.'_2_operator2">
                                    <option value="" '.($operator2 == '' ? 'selected="selected"' : '').'></option>
                                    <option value="<=" '.($operator2 == '<=' ? 'selected="selected"' : '').'>'._l('Before').'</option>
                                    <option value="=" '.($operator2 == '=' ? 'selected="selected"' : '').'>'._l('Equals').'</option>
                                    <option value=">=" '.($operator2 == '>=' ? 'selected="selected"' : '').'>'._l('After').'</option>
                                    <option value="<>" '.($operator2 == '<>' ? 'selected="selected"' : '').'>'._l('Different').'</option>
                                </select>
                                <input type="text" size="13" maxlength="10"id="'.$ruleName.'_2_date_value2" value="'.$value2.'" class="calendar"/>
                            </div>
                        </div>
                    </td>
                    <td style="width:110px;">
                        <div id="2_panel_'.$ruleName.'" style="display:none;">
                            <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label>
                        </div>
                    </td>
                </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a NUMBER field rule.
     */
    public function displayNumberRule($ruleName, $displayName, $rule)
    {
        // default values
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $value1 = $rule ? $rule[1] : '1';
        $operator1 = $rule ? $rule[2] : '>=';
        $operator2 = $rule ? $rule[3] : null;
        $value2 = $rule ? $rule[4] : '';

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : Number rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        $html = '
                <tr>
                    <td class="field_label">
                        <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                        <input type="hidden" id="'.$ruleName.'_type" value="number" />
                    </td>
                    <td>
                        <div id="1_panel_'.$ruleName.'" style="display:none;">
                            <select id="'.$ruleName.'_operator1">
                                <option value="<" '.($operator1 == '<' ? 'selected="selected"' : '').'><</option>
                                <option value="<=" '.($operator1 == '<=' ? 'selected="selected"' : '').'><=</option>
                                <option value="=" '.($operator1 == '=' ? 'selected="selected"' : '').'>=</option>
                                <option value=">=" '.($operator1 == '>=' ? 'selected="selected"' : '').'>>=</option>
                                <option value=">" '.($operator1 == '>' ? 'selected="selected"' : '').'>></option>
                                <option value="<>" '.($operator1 == '<>' ? 'selected="selected"' : '').'>'._l('Different').'</option>
                            </select>
                            <input type="text" size="10" maxlength="7" id="'.$ruleName.'_value1" value="'.$value1.'" />
                             '._l('and').' 
                            <select id="'.$ruleName.'_operator2">
                                <option value="" '.($operator2 == '' ? 'selected="selected"' : '').'></option>
                                <option value="<" '.($operator2 == '<' ? 'selected="selected"' : '').'><</option>
                                <option value="<=" '.($operator2 == '<=' ? 'selected="selected"' : '').'><=</option>
                                <option value="=" '.($operator2 == '=' ? 'selected="selected"' : '').'>=</option>
                                <option value=">=" '.($operator2 == '>=' ? 'selected="selected"' : '').'>>=</option>
                                <option value=">" '.($operator2 == '>' ? 'selected="selected"' : '').'>></option>
                                <option value="<>" '.($operator2 == '<>' ? 'selected="selected"' : '').'>'._l('Different').'</option>
                            </select>
                            <input type="text" size="13" maxlength="10"id="'.$ruleName.'_value2" value="'.$value2.'" />
                        </div>
                    </td>
                    <td style="width:130px;">
                        <div id="2_panel_'.$ruleName.'" style="display:none;">
                            <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label>
                        </div>
                    </td>
                </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a LIST field rule
     * ruleInfos contains two items for here :
     *         either
     *             'values' => $values is an array(id1=>value1, ..., idn=>valuen)
     *        or
     *            'sql' => the SQL query to get data source (same structure as 'values' --> 2 fields)
     *         'default' => default_id (optional).
     */
    public function displayListRule($ruleName, $displayName, $rule, $ruleInfos, $multiple = false)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $operator = $rule ? $rule[1] : null;

        // get values
        if (array_key_exists('values', $ruleInfos))
        {
            $values = $ruleInfos['values'];
        }
        elseif (array_key_exists('sql', $ruleInfos))
        {
            $values = $this->getValuesFromSql($ruleInfos['sql']);
        }

        // no selected value ?
        if (!$rule)
        {
            // is there a default value defined in rule infos ?
            if (array_key_exists('default', $ruleInfos))
            {
                $selectedValue = $ruleInfos['default'];
            }
            else
            {
                // so get the first id of values
                reset($values);                    // reset pointer in array to first item
                $selectedValue = key($values);     // get first id in values to display
            }
        }
        else
        {
            $selectedValue = $rule[2];
        }            // selected value

        $multipleValues = array();
        if ($multiple)
        {
            $temps = explode(',', $selectedValue);
            foreach ($temps as $temp)
            {
                $multipleValues[] = $temp;
            }
        }

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : List rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                                    <input type="hidden" id="'.$ruleName.'_type" value="list" />
                                </td>
                                <td>
                                    <div id="1_panel_'.$ruleName.'" style="display:none;">
                                        <select id="'.$ruleName.'_value" '.(($multiple) ? 'multiple' : '').'>';
        foreach ($values as $id => $value)
        {
            $html .= '
                                            <option value="'.$id.'" '.((!$multiple && $selectedValue == $id) || ($multiple && in_array($id, $multipleValues)) ? 'selected="selected"' : '').'>'.$value.'</option>';
        }
        $html .= '
                                        </select>
                                    </div>
                                </td>
                                <td style="width:110px;">
                                </td>
                            </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a Customer group field rule.
     */
    public function displayGroupRule($ruleName, $displayName, $rule)
    {
        // set values with rule components if any
        // NONE|gp1,gp2,...
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $values = $rule ? explode(',', $rule[1]) : array();

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : Group rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        // load groups for current language
        $groups = Group::getGroups($this->id_lang);

        $html = '
                <tr>
                    <td class="field_label">
                        <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                        <input type="hidden" id="'.$ruleName.'_type" value="group" />
                    </td>
                    <td>
                        <div id="1_panel_'.$ruleName.'" style="display:none;">
                            <table style="width:200px;">';
        foreach ($groups as $group)
        {
            $checked = in_array($group['id_group'], $values) ? true : false;
            $itemId = $ruleName.'_group_'.$group['id_group'];
            $html .= '
                                <tr>
                                    <td style="width:20px; text-align:center;">
                                        <input type="checkbox" value="'.$group['id_group'].'" name="'.$ruleName.'_group" id="'.$itemId.'" '.($checked ? 'checked="checked"' : '').' />
                                    </td>
                                    <td><label for="'.$itemId.'" class="sclabel2">'.$group['name'].'</label></td>
                                </tr>';
        }
        $html .= '
                        </table>
                    </div>
                </td>
                <td style="width:130px;">
                    <div id="2_panel_'.$ruleName.'" style="display:none;">
                        <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label>
                    </div>
                </td>
            </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a Order state field rule.
     */
    public function displayOrderStateRule($ruleName, $displayName, $rule)
    {
        // set values with rule components if any
        // NONE|state1,state2,...
        // always check this rule
//        $checked = $rule ? true : false;
        $checked = true;
        $ruleOperator = $rule ? $rule[0] : null;
        $values = $rule ? explode(',', $rule[1]) : array();

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : Order state rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        // load states for current language
        $states = OrderState::getOrderStates($this->id_lang);

        $html = '
                <tr>
                    <td class="field_label">
                        <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                        <input type="hidden" id="'.$ruleName.'_type" value="order_state" />
                    </td>
                    <td>
                        <div id="1_panel_'.$ruleName.'" style="display:none;">
                            <table style="width:400px;">
                                <tr>
                                    <td style="width:20px; text-align:center;">
                                        <input type="checkbox" value="1" id="check_all_states" />
                                        <script>
                                            $("#check_all_states:checkbox").change(function(){
                                                if($(this).attr("checked")) {
                                                    $(".o_order_state_state").attr("checked","checked");
                                                    $("#check_all_states_text").html("' . _l('Uncheck all') . '");
                                                } else {
                                                    $(".o_order_state_state").removeAttr("checked");
                                                    $("#check_all_states_text").html("' . _l('Check all') . '");
                                                }
                                            });
                                            $(document).ready(function(){
                                                if($(".o_order_state_state").length === $(".o_order_state_state:checked").length) {
                                                    $("#check_all_states:checkbox").attr("checked","checked");
                                                    $("#check_all_states_text").text("' . _l('Uncheck all') . '");
                                                }
                                            });
                                        </script>
                                    </td>
                                    <td><label id="check_all_states_text" for="check_all_states" class="sc_label2">' . _l('Check all') . '</label></td>
                                </tr>';
        foreach ($states as $state)
        {
            $checked = in_array($state['id_order_state'], $values) ? true : false;
            $itemId = $ruleName.'_state_'.$state['id_order_state'];
            $html .= '
                                <tr style="background:'.$state['color'].';">
                                    <td style="width:20px; text-align:center;">
                                        <input type="checkbox" value="'.$state['id_order_state'].'" name="'.$ruleName.'_state" class="'.$ruleName.'_state" id="'.$itemId.'" '.($checked ? 'checked="checked"' : '').' />
                                    </td>
                                    <td><label for="'.$itemId.'" class="sclabel_bgcolored">'.$state['name'].'</label></td>
                                </tr>';
        }
        $html .= '
                            </table>
                        </div>
                    </td>
                    <td style="width:130px;">
                        <div id="2_panel_'.$ruleName.'" style="display:none;">
                            <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label><sup>*</sup>
                        </div>
                    </td>
                </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a PRODUCT field rule.
     */
    public function displayProductRule($ruleName, $displayName, $rule)
    {
        $html = '';

        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $value = $rule ? $rule[1] : '';

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : Product rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        // load products if any are defined
        if (Tools::strlen($value) > 0)
        {
            // load the products names from these ids
            $query = 'SELECT p.id_product, pl.name FROM '._DB_PREFIX_.'product p
                INNER JOIN '._DB_PREFIX_.'product_lang pl ON p.id_product=pl.id_product
                WHERE p.id_product IN ('.$value.') AND pl.id_lang='.(int) $this->id_lang;
            $results = Db::getInstance()->executeS($query);
            if ($results)
            {
                $html .= '
                    <script> ';
                $i = 0;
                foreach ($results as $result)
                {
                    $html .= '
                        productList['.$i.'] = {id:'.$result['id_product'].', name:"'.htmlspecialchars($result['name']).'"};';
                    ++$i;
                }
                $html .= '
                    </script>';
            }
        }

        $html .= '
                <tr>
                    <td class="field_label">
                        <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                        <input type="hidden" id="'.$ruleName.'_type" value="product" />
                    </td>
                    <td>
                        <div id="1_panel_'.$ruleName.'" style="display:none;">
                            <table>
                                <tr>
                                    <td>
                                        '._l('Search').'
                                        <input class="search_query" type="text" style="width:200px;" id="scproduct_search_query" value="" />
                                         '._l('then add the product by clicking').' 
                                        <input type="hidden" id="scproduct_search_id_to_add" value="0" />
                                        <input type="hidden" id="scproduct_search_name_to_add" value="" />
                                        <a href="" onclick="return addProduct();" title="'._l('Add the product').'"><img style="vertical-align:middle;" src="'._PS_ADMIN_IMG_.'duplicate.gif" /></a>
                                        <div id="search_query_result"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span id="'.$ruleName.'_spanlist" style="font-style:italic;"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width:130px;">
                        <div id="2_panel_'.$ruleName.'" style="display:none;">
                            <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label>
                        </div>
                    </td>
                </tr>';

        return $html;
    }

    /**
     * Output a HTML <TR> to display a PRODUCT CATEGORY field rule.
     */
    public function displayProductCategoryRule($ruleName, $displayName, $rule)
    {
        $html = '';

        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $value = $rule ? $rule[1] : '';

        if ($rule)
        {
            ExportOrderTools::addLog('CList edit : Product category rule '.$ruleName.' : '.ExportOrderTools::captureVarDump($rule));
        }

        // load products if any are defined
        if (Tools::strlen($value) > 0)
        {
            // load the category names from these ids
            $query = 'SELECT c.id_category, cl.name FROM '._DB_PREFIX_.'category c
                INNER JOIN '._DB_PREFIX_.'category_lang cl ON c.id_category=cl.id_category
                WHERE c.id_category IN ('.$value.') AND cl.id_lang='.(int) $this->id_lang;
            $results = Db::getInstance()->executeS($query);
            if ($results)
            {
                $html .= '
                    <script> ';
                $i = 0;
                $exist = array();
                foreach ($results as $result)
                {
                    if (empty($exist[$result['id_category']]))
                    {
                        $html .= '
                            pdtCategoryList['.$i.'] = {id:'.$result['id_category'].', name:"'.htmlspecialchars($result['name']).'"};';
                        ++$i;
                        $exist[$result['id_category']] = $result['id_category'];
                    }
                }
                $html .= '
                    </script>';
            }
        }

        $html .= '
                <tr>
                    <td class="field_label">
                        <input type="checkbox" id="'.$ruleName.'_activate" '.($checked ? 'checked="checked"' : '').'/> <label class="sclabel" for="'.$ruleName.'_activate">'.$displayName.'</label>
                        <input type="hidden" id="'.$ruleName.'_type" value="pdt_category" />
                    </td>
                    <td>
                        <div id="1_panel_'.$ruleName.'" style="display:none;">
                            <table style="width:95%;">
                                <tr>
                                    <td>
                                        '._l('Select a category to add to the filter');
                                        // load the category tree
                                        $html .= $this->loadCategoryTree();
                                        $html .= '
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span id="'.$ruleName.'_spanlist" style="font-style:italic;"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        </div>
                    </td>
                    <td style="width:130px;">
                        <div id="2_panel_'.$ruleName.'" style="display:none;">
                            <input type="checkbox" id="'.$ruleName.'_reverse" '.($ruleOperator == 'NOT' ? 'checked="checked"' : '').'/> <label class="sclabel2 sclabel_reverse" for="'.$ruleName.'_reverse">'._l('Reverse rule').'</label>
                        </div>
                    </td>
                </tr>';

        return $html;
    }

    /**
     * Load the prduct category tree and display it as HTML.
     */
    public function loadCategoryTree()
    {
        $query = '
            SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
            FROM `'._DB_PREFIX_.'category` c
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int) $this->id_lang.')
            GROUP BY id_category
            ORDER BY `level_depth` ASC, '.(ExportOrderTools::isNewerPs14x() ? 'c.`position` ASC' : 'cl.`name` ASC');
        if (!$result = Db::getInstance()->executeS($query))
        {
            return;
        }

        $resultParents = array();
        $resultIds = array();
        foreach ($result as &$row)
        {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }

        $blockCategTree = $this->getTree($resultParents, $resultIds, 0);
        unset($resultParents);
        unset($resultIds);

        // build the HTML
        $html = '
            <div id="o_p_cat_order_pdt_category_category" class="dhtmlxTree" setImagePath="lib/js/imgs/dhxtree_material/"
    style="width:250px; height:218px;overflow:auto;">
                <ul>';
        for ($i = 0; $i < count($blockCategTree['children']); ++$i)
        {
            $child = $blockCategTree['children'][$i];
            $html .= $this->addCategoryHtml($child);
        }
        $html .= '
                </ul>
            </div>
        <script>
        const orderProductTree = dhtmlXTreeFromHTML("o_p_cat_order_pdt_category_category");
        </script>';

        return $html;
    }

    /**
     * Display a category branch as HTML.
     */
    public function addCategoryHtml($node)
    {
        $html = '
            <li><a href="" style="font-weight:bold;" onclick="return addCategory('.$node['id'].', this);" title="'._l('Click to add this category to filter').'">'.$node['name'].'</a>';
        if (count($node['children']) > 0)
        {
            // compute left margin according to depth
            $html .= '
                <ul>';
            for ($i = 0; $i < count($node['children']); ++$i)
            {
                $child = $node['children'][$i];
                $html .= $this->addCategoryHtml($child);
            }
            $html .= '
                </ul>';
        }
        $html .= '
            </li>';

        return $html;
    }

    public function getTree($resultParents, $resultIds, $maxDepth, $id_category = 1, $currentDepth = 0)
    {
        $link = Context::getContext()->link;

        $children = array();
        if (isset($resultParents[$id_category]) and sizeof($resultParents[$id_category]) and ($maxDepth == 0 or $currentDepth < $maxDepth))
        {
            foreach ($resultParents[$id_category] as $subcat)
            {
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
            }
        }
        if (!isset($resultIds[$id_category]))
        {
            return false;
        }

        return array('id' => $id_category,
            'link' => $link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
            'name' => $resultIds[$id_category]['name'],
            'desc' => $resultIds[$id_category]['description'],
            'depth' => $currentDepth,
            'children' => $children);
    }

    /**
     * Execute a SQL query to get two fields (id and value) and build back an array like
     * (id1=>value1, ..., idn=>valuen).
     */
    public function getValuesFromSql($sql)
    {
        $values = array();
        $results = Db::getInstance()->executeS($sql);
        if ($results)
        {
            foreach ($results as $result)
            {
                $values[$result['id']] = $result['value'];
            }
        }

        return $values;
    }

    private function initOrderRules()
    {
        $this->orderRules = array(
            'o_date_add' => array('text' => _l('Creation date'), 'type' => 'date'),
            'o_date_invoice' => array('text' => _l('Invoice date'), 'type' => 'date'),
            'o_date_delivery' => array('text' => _l('Delivery date'), 'type' => 'date'),
            'o_gift' => array('text' => _l('Is gift'), 'type' => 'boolean', 'choice1' => _l('Yes'), 'choice2' => _l('No')),
            'o_invoice' => array('text' => _l('Invoice number'), 'type' => 'string'),
            // Carriers list
            'o_ca_carrier' => array('text' => _l('Carrier'), 'type' => 'list', 'sql' => 'SELECT `id_carrier` as id, `name` as value FROM `'._DB_PREFIX_.'carrier` '.(ExportOrderTools::isPsVersionsNewer(_PS_VERSION_, '1.4.9') ? ' GROUP BY `id_reference` ' : '').' ORDER BY `id_carrier`'),
            'o_shipping_number' => array('text' => _l('Shipping number'), 'type' => 'string'),
            'os_date_add' => array('text' => _l('Order slip - Creation date'), 'type' => 'date'),
            'olh_date_add' => array('text' => _l('Status update'), 'type' => 'date'),
        );
    }

    private function initOrderProductRules()
    {
        $this->orderProductRules = array(
            'o_p_order_product' => array('text' => _l('Product in orders'), 'type' => 'product'),
            'o_p_cat_order_pdt_category' => array('text' => _l('Product category in orders'), 'type' => 'pdt_category'),
        );
    }

    private function initOrderTotalRules()
    {
        $this->orderTotalRules = array(
            'o_payment_mode' => array('text' => _l('Payment mode'), 'type' => 'listmultiple', 'sql' => 'SELECT `payment` as id, `payment` as value FROM `'._DB_PREFIX_.'orders` GROUP BY `payment`'),
            'o_total_real' => array('text' => _l('Total paid real'), 'type' => 'number'),
            'o_total_paid' => array('text' => _l('Total paid'), 'type' => 'number'),
            'o_total_discount' => array('text' => _l('Total discount'), 'type' => 'number'),
            'o_total_products_it' => array('text' => _l('Total products IT'), 'type' => 'number'),
            'o_total_products_et' => array('text' => _l('Total products ET'), 'type' => 'number'),
            'o_total_shipping' => array('text' => _l('Total shipping'), 'type' => 'number'),
            'o_cu_currency' => array('text' => _l('Currency'), 'type' => 'list', 'sql' => 'SELECT `id_currency` as id, `name` as value FROM `'._DB_PREFIX_.'currency`'),
        );
    }

    private function initCustomerRules()
    {
        $this->customerRules = array(
            'c_gender' => array('text' => _l('Gender'), 'type' => 'list', 'default' => '1', 'values' => array('1' => _l('Male'), '2' => _l('Female'))),
            'c_first_name' => array('text' => _l('First name'), 'type' => 'string'),
            'c_last_name' => array('text' => _l('Last name'), 'type' => 'string'),
            'c_birthday_date' => array('text' => _l('Birthday date'), 'type' => 'date'),
            'c_email' => array('text' => _l('Email'), 'type' => 'string'),
        );
    }

    private function initAddressDeliveryRules()
    {
        $this->addressDeliveryRules = array(
            'a_d_company' => array('text' => _l('Company'), 'type' => 'string'),
            'a_d_first_name' => array('text' => _l('Address first name'), 'type' => 'string'),
            'a_d_last_name' => array('text' => _l('Address last name'), 'type' => 'string'),
            'a_d_address' => array('text' => _l('Address'), 'type' => 'string'),
            'a_d_postcode' => array('text' => _l('Postcode'), 'type' => 'string'),
            'a_d_city' => array('text' => _l('City'), 'type' => 'string'),
            'a_d_state' => array('text' => _l('State'), 'type' => 'string'),
            'a_d_country' => array('text' => _l('Country'), 'type' => 'string'),
            'a_d_vat_number' => array('text' => _l('VAT customer num.'), 'type' => 'string'),
        );
    }

    private function initAddressInvoiceRules()
    {
        $this->addressInvoiceRules = array(
            'a_i_company' => array('text' => _l('Company'), 'type' => 'string'),
            'a_i_first_name' => array('text' => _l('Address first name'), 'type' => 'string'),
            'a_i_last_name' => array('text' => _l('Address last name'), 'type' => 'string'),
            'a_i_address' => array('text' => _l('Address'), 'type' => 'string'),
            'a_i_postcode' => array('text' => _l('Postcode'), 'type' => 'string'),
            'a_i_city' => array('text' => _l('City'), 'type' => 'string'),
            'a_i_state' => array('text' => _l('State'), 'type' => 'string'),
            'a_i_country' => array('text' => _l('Country'), 'type' => 'string'),
            'a_i_vat_number' => array('text' => _l('VAT customer num.'), 'type' => 'string'),
        );
    }

    private function initGroupRules()
    {
        $this->groupRules = array(
            'cg_customerGroup' => array('text' => _l('Customer group'), 'type' => 'group'),
        );
    }
}
