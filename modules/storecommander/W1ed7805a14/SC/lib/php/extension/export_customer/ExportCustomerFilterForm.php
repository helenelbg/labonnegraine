<?php

class ExportCustomerFilterForm
{
    public $customerRules;
    public $customerAddressRules;
    public $customerGroupRules;
    public $customerOrderRules;
    public $customerOtherListRules;
    public $customerNewsletterRules;

    private $id_lang;

    public function __construct($id_lang)
    {
        $this->id_lang = (int)$id_lang;
        $this->initCustomerRules();
        $this->initCustomerAddressRules();
        $this->initCustomerGroupRules();
        $this->initCustomerOrderRules();
        $this->initCustomerOtherListRules();
        $this->initCustomerNewsletterRules();
    }

    /**
     * @param $ruleName
     * @param $ruleInfos
     * @param $ruleDescriptors
     * @return string
     */
    public function displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors)
    {
        $html = '';
        // the the rule components by its name
        $rule = ExportCustomerFilter::getRuleByName($ruleName, $ruleDescriptors);
        if (!$rule) {
            ExportCustomerTools::addLog('CList edit : No rule ' . $ruleName . ' into the descriptors');
        }
        switch ($ruleInfos['type']) {
            case 'boolean':
                $html = $this->displayBooleanRule($ruleName, $ruleInfos['text'], $rule, $ruleInfos['choiceText1'], $ruleInfos['choiceText2']);
                break;
            case 'string':
                $html = $this->displayStringRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'date':
                $html = $this->displayDateRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'gender':
                $html = $this->displayGenderRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'number':
                $html = $this->displayNumberRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'group':
                $html = $this->displayGroupRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'order_state':
                $html = $this->displayOrderStateRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'product':
                $html = $this->displayProductRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'pdt_category':
                $html = $this->displayProductCategoryRule($ruleName, $ruleInfos['text'], $rule);
                break;
            case 'other_list':
                $html = $this->displayOtherListRule($ruleName, $ruleInfos['text'], $rule, $ruleInfos['editedList']);
                break;
            case 'lang':
                $html = $this->displayLangRule($ruleName, $ruleInfos['text'], $rule);
                break;
            default:
                ExportCustomerTools::addLog('CList edit : unable to display rule ' . $ruleName . ' with type ' . $ruleInfos['type']);
                return '';
        }
        return $html;
    }

    /**
     * Output a HTML <TR> to display a BOOLEAN field rule
     */
    private function displayBooleanRule($ruleName, $displayName, $rule, $choiceText1, $choiceText2)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $choice = 1;
        if ($rule) {
            $choice = (($rule[1] == '=' && $rule[2] == '1') || ($rule[1] == '<>' && $rule[2] == '0')) ? 1 : 2;
        }
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : boolean rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        $html = '
            <tr>
                <td class="field_label">
                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                    <input type="hidden" id="' . $ruleName . '_type" value="boolean" />
                </td>
                <td>
                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                        <input type="radio" value="1" id="' . $ruleName . '_choice_1" name="' . $ruleName . '_choice" ' . ($choice == 1 ? 'checked="checked"' : '') . ' /> <label class="sc_label2" for="' . $ruleName . '_choice_1">' . $choiceText1 . '</label>
                        <input type="radio" value="0" id="' . $ruleName . '_choice_2" name="' . $ruleName . '_choice" ' . ($choice == 2 ? 'checked="checked"' : '') . ' /> <label class="sc_label2" for="' . $ruleName . '_choice_2">' . $choiceText2 . '</label>
                    </div>
                </td>
                <td style="width:110px;">
                </td>
            </tr>';
        if ($ruleName == 'c_o_order_without') {
            $html .= '<tr><td style="text-align:center;" colspan="3"> - ' . _l('OR') . ' - </td></tr>';
        }
        return $html;
    }

    /**
     * Output a HTML <TR> to display an OTHER LIST field rule
     */
    private function displayOtherListRule($ruleName, $displayName, $rule, $editedList)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $operator = $rule ? $rule[1] : null;
        $value = $rule ? $rule[2] : '2';
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Other list rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        // load all other lists
        $allLists = ExportCustomerFilter::getAll($editedList->name);
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . '/> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="other_list" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">';
        if (count($allLists) > 0) {
            $html .= '
                                        <select id="' . $ruleName . '_operator">
                                            <option value="UNION" ' . ($operator == 'UNION' ? 'selected="selected"' : '') . '>' . _l('Union') . '</option>
                                            <option value="INTERSECTION" ' . ($operator == 'INTERSECTION' ? 'selected="selected"' : '') . '>' . _l('Intersection') . '</option>
                                            <option value="NOT IN" ' . ($operator == 'NOT IN' ? 'selected="selected"' : '') . '>' . _l('Not in') . '</option>
                                        </select>
                                        <select id="' . $ruleName . '_value" style="width:350px;">';
            foreach ($allLists as $list) {
                $html .= '
                                            <option value="' . $list[ExportCustomerFilter::$definition['primary']] . '" ' . ($value == $list[ExportCustomerFilter::$definition['primary']] ? 'selected="selected"' : '') . '>' . $list['name'] . '</option>';
            }
            $html .= '
                                        </select>';
        } else {
            $html .= '
                                    <p>' . _l('No other list to display') . '</p>';
        }
        $html .= '
                                    </div>
                                </td>
                                <td style="width:110px;">
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a STRING field rule
     */
    private function displayStringRule($ruleName, $displayName, $rule)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $operator = $rule ? $rule[1] : null;
        $value = $rule ? $rule[2] : '';
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : String rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="string" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <select id="' . $ruleName . '_operator">
                                            <option value="begin" ' . ($operator == 'begin' ? 'selected="selected"' : '') . '>' . _l('Begins with') . '</option>
                                            <option value="end" ' . ($operator == 'end' ? 'selected="selected"' : '') . '>' . _l('Ends with') . '</option>
                                            <option value="like" ' . ($operator == 'like' ? 'selected="selected"' : '') . '>' . _l('Contains') . '</option>
                                            <option value="equal" ' . ($operator == 'equal' ? 'selected="selected"' : '') . '>' . _l('Equals') . '</option>
                                            ';
        if ($ruleName == 'c_a_country') {
            $html .= '<option value="in" ' . ($operator == 'in' ? 'selected="selected"' : '') . '>' . _l('In the list (separated by a ",")') . '</option>';
        }
        $html .= '</select>
                                        <input type="text" size="33" id="' . $ruleName . '_value" value="' . $value . '" />
                                    </div>
                                </td>
                                <td style="width:130px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a DATE field rule
     */
    private function displayDateRule($ruleName, $displayName, $rule)
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
        $month = '1';
        $monthchoice = false;
        // values from rule if any
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Date rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
            // is it a range date ?
            $isRange = (count($rule) == 5);
            $isForMonth = (count($rule) == 3);
            if ($isForMonth) {
                $month = $rule[2];
                $monthchoice = true;
            } elseif (!$isRange) {
                $operator = $rule[1];
                $value = $rule[2];
                $unit = $rule[3];
            } else {
                $value1 = $rule[1];
                $operator1 = $rule[2];
                $operator2 = $rule[3];
                $value2 = $rule[4];
            }
        }
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="date" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <div>
                                            <input type="radio" value="norange" id="' . $ruleName . '_date_1" name="' . $ruleName . '_date" ' . (!$isRange ? 'checked="checked"' : '') . ' /> <label class="sc_label2" for="' . $ruleName . '_date_1"></label>
                                            <select id="' . $ruleName . '_1_operator">
                                                <option value="<=" ' . ($operator == '<=' ? 'selected="selected"' : '') . '>' . _l('Older than') . '</option>
                                                <option value=">=" ' . ($operator == '>=' ? 'selected="selected"' : '') . '>' . _l('Younger than') . '</option>
                                            </select>
                                            ' . _l('the last') . '
                                            <input type="text" size="5" maxlength="4" id="' . $ruleName . '_1_value" value="' . $value . '" />
                                            <select id="' . $ruleName . '_1_unit">
                                                <option value="DAY" ' . ($unit == 'DAY' ? 'selected="selected"' : '') . '>' . _l('Day(s)') . '</option>
                                                <option value="MONTH" ' . ($unit == 'MONTH' ? 'selected="selected"' : '') . '>' . _l('Month(s)') . '</option>
                                                <option value="YEAR" ' . ($unit == 'YEAR' ? 'selected="selected"' : '') . '>' . _l('Year(s)') . '</option>
                                            </select>
                                        </div>
                                        <div>
                                            <input type="radio" value="range" id="' . $ruleName . '_date_2" name="' . $ruleName . '_date" ' . ($isRange ? 'checked="checked"' : '') . ' /> <label class="sc_label2" for="' . $ruleName . '_date_2"></label>
                                            <select id="' . $ruleName . '_2_operator1">
                                                <option value="<=" ' . ($operator1 == '<=' ? 'selected="selected"' : '') . '>' . _l('Before') . '</option>
                                                <option value="=" ' . ($operator1 == '=' ? 'selected="selected"' : '') . '>' . _l('Equals') . '</option>
                                                <option value=">=" ' . ($operator1 == '>=' ? 'selected="selected"' : '') . '>' . _l('After') . '</option>
                                                <option value="<>" ' . ($operator1 == '<>' ? 'selected="selected"' : '') . '>' . _l('Different') . '</option>
                                            </select>
                                            <input type="text" size="13" maxlength="10" id="' . $ruleName . '_2_date_value1" value="' . $value1 . '" class="calendar"/>
                                            ' . _l('and') . '
                                            <select id="' . $ruleName . '_2_operator2">
                                                <option value="" ' . ($operator2 == '' ? 'selected="selected"' : '') . '></option>
                                                <option value="<=" ' . ($operator2 == '<=' ? 'selected="selected"' : '') . '>' . _l('Before') . '</option>
                                                <option value="=" ' . ($operator2 == '=' ? 'selected="selected"' : '') . '>' . _l('Equals') . '</option>
                                                <option value=">=" ' . ($operator2 == '>=' ? 'selected="selected"' : '') . '>' . _l('After') . '</option>
                                                <option value="<>" ' . ($operator2 == '<>' ? 'selected="selected"' : '') . '>' . _l('Different') . '</option>
                                            </select>
                                            <input type="text" size="13" maxlength="10"id="' . $ruleName . '_2_date_value2" value="' . $value2 . '" class="calendar"/>
                                        </div>
                                        <div>
                                            <input type="radio" value="monthchoice" id="' . $ruleName . '_date_3" name="' . $ruleName . '_date" ' . ($monthchoice ? 'checked="checked"' : '') . ' />
                                            <label class="sc_label3" for="' . $ruleName . '_date_3" style="float:none"></label>
                                            <select id="' . $ruleName . '_month">
                                                <option value="1" ' . ($month == '1' ? 'selected="selected"' : '') . '>' . _l('January') . '</option>
                                                <option value="2" ' . ($month == '2' ? 'selected="selected"' : '') . '>' . _l('February') . '</option>
                                                <option value="3" ' . ($month == '3' ? 'selected="selected"' : '') . '>' . _l('March') . '</option>
                                                <option value="4" ' . ($month == '4' ? 'selected="selected"' : '') . '>' . _l('April') . '</option>
                                                <option value="5" ' . ($month == '5' ? 'selected="selected"' : '') . '>' . _l('May') . '</option>
                                                <option value="6" ' . ($month == '6' ? 'selected="selected"' : '') . '>' . _l('June') . '</option>
                                                <option value="7" ' . ($month == '7' ? 'selected="selected"' : '') . '>' . _l('July') . '</option>
                                                <option value="8" ' . ($month == '8' ? 'selected="selected"' : '') . '>' . _l('August') . '</option>
                                                <option value="9" ' . ($month == '9' ? 'selected="selected"' : '') . '>' . _l('September') . '</option>
                                                <option value="10" ' . ($month == '10' ? 'selected="selected"' : '') . '>' . _l('October') . '</option>
                                                <option value="11" ' . ($month == '11' ? 'selected="selected"' : '') . '>' . _l('November') . '</option>
                                                <option value="12" ' . ($month == '12' ? 'selected="selected"' : '') . '>' . _l('December') . '</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <td style="width:110px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a NUMBER field rule
     */
    private function displayNumberRule($ruleName, $displayName, $rule)
    {
        // default values
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $value1 = $rule ? $rule[1] : '1';
        $operator1 = $rule ? $rule[2] : '>=';
        $operator2 = $rule ? $rule[3] : null;
        $value2 = $rule ? $rule[4] : '';
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Number rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="number" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <select id="' . $ruleName . '_operator1">
                                            <option value="<" ' . ($operator1 == '<' ? 'selected="selected"' : '') . '><</option>
                                            <option value="<=" ' . ($operator1 == '<=' ? 'selected="selected"' : '') . '><=</option>
                                            <option value="=" ' . ($operator1 == '=' ? 'selected="selected"' : '') . '>=</option>
                                            <option value=">=" ' . ($operator1 == '>=' ? 'selected="selected"' : '') . '>>=</option>
                                            <option value=">" ' . ($operator1 == '>' ? 'selected="selected"' : '') . '>></option>
                                            <option value="<>" ' . ($operator1 == '<>' ? 'selected="selected"' : '') . '>' . _l('Different') . '</option>
                                        </select>
                                        <input type="text" size="10" maxlength="7" id="' . $ruleName . '_value1" value="' . $value1 . '" />
                                        ' . _l('and') . '
                                        <select id="' . $ruleName . '_operator2">
                                            <option value="" ' . ($operator2 == '' ? 'selected="selected"' : '') . '></option>
                                            <option value="<" ' . ($operator2 == '<' ? 'selected="selected"' : '') . '><</option>
                                            <option value="<=" ' . ($operator2 == '<=' ? 'selected="selected"' : '') . '><=</option>
                                            <option value="=" ' . ($operator2 == '=' ? 'selected="selected"' : '') . '>=</option>
                                            <option value=">=" ' . ($operator2 == '>=' ? 'selected="selected"' : '') . '>>=</option>
                                            <option value=">" ' . ($operator2 == '>' ? 'selected="selected"' : '') . '>></option>
                                            <option value="<>" ' . ($operator2 == '<>' ? 'selected="selected"' : '') . '>' . _l('Different') . '</option>
                                        </select>
                                        <input type="text" size="13" maxlength="10"id="' . $ruleName . '_value2" value="' . $value2 . '" />
                                    </div>
                                </td>
                                <td style="width:130px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a GENDER field rule
     */
    private function displayGenderRule($ruleName, $displayName, $rule)
    {
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $operator = $rule ? $rule[1] : null;
        $value = $rule ? $rule[2] : '2';
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Gender rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="gender" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <select id="' . $ruleName . '_operator">
                                            <option value="=" ' . ($operator == '=' ? 'selected="selected"' : '') . '>' . _l('Equals') . '</option>
                                            <option value="<>" ' . ($operator == '<>' ? 'selected="selected"' : '') . '>' . _l('Different') . '</option>
                                        </select>
                                        <select id="' . $ruleName . '_value">
                                            <option value="1" ' . ($value == '1' ? 'selected="selected"' : '') . '>' . _l('Male') . '</option>
                                            <option value="2" ' . ($value == '2' ? 'selected="selected"' : '') . '>' . _l('Female') . '</option>
                                        </select>
                                    </div>
                                </td>
                                <td style="width:110px;">
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a Customer group field rule
     */
    private function displayGroupRule($ruleName, $displayName, $rule)
    {
        $cookie = Context::getContext()->cookie;
        // set values with rule components if any
        // NONE|gp1,gp2,...
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $values = $rule ? explode(',', $rule[1]) : array();
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Group rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        // load groups for current language
        $groups = Group::getGroups($cookie->id_lang);
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="group" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <table style="width:200px;">';
        foreach ($groups as $group) {
            $checked = in_array($group['id_group'], $values) ? true : false;
            $itemId = $ruleName . '_group_' . $group['id_group'];
            $html .= '

                                            <tr>
                                                <td style="width:20px; text-align:center;">
                                                    <input type="checkbox" value="' . $group['id_group'] . '" name="' . $ruleName . '_group" id="' . $itemId . '" ' . ($checked ? 'checked="checked"' : '') . ' />
                                                </td>
                                                <td><label for="' . $itemId . '" class="sc_label2">' . $group['name'] . '</label></td>
                                            </tr>';
        }
        $html .= '
                                        </table>
                                    </div>
                                </td>
                                <td style="width:130px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a Customer lang field rule
     */
    private function displayLangRule($ruleName, $displayName, $rule)
    {
        $cookie = Context::getContext()->cookie;
        // set values with rule components if any
        // NONE|gp1,gp2,...
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $values = $rule ? explode(',', $rule[1]) : array();
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Lang rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        // load languages
        $langs = Language::getLanguages(false);
        $html = '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="group" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <table style="width:200px;">';
        foreach ($langs as $lang) {
            $checked = in_array($lang['id_lang'], $values) ? true : false;
            $itemId = $ruleName . '_lang_' . $lang['id_lang'];
            $html .= '
                                            <tr>
                                                <td style="width:20px; text-align:center;">
                                                    <input type="checkbox" value="' . $lang['id_lang'] . '" name="' . $ruleName . '_group" id="' . $itemId . '" ' . ($checked ? 'checked="checked"' : '') . ' />
                                                </td>
                                                <td><label for="' . $itemId . '" class="sc_label2">' . $lang['name'] . '</label></td>
                                            </tr>';
        }
        $html .= '
                                        </table>
                                    </div>
                                </td>
                                <td style="width:130px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a Order state field rule
     */
    private function displayOrderStateRule($ruleName, $displayName, $rule)
    {
        $cookie = Context::getContext()->cookie;

        // set values with rule components if any
        // NONE|state1,state2,...
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $values = $rule ? explode(',', $rule[1]) : array();
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Order state rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        // load states for current language
        $states = OrderState::getOrderStates($cookie->id_lang);
        $html = '

                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="order_state" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <table class="order_state">';
        $html .= '
                                            <tr>
                                                <td style="width:20px; text-align:center;">
                                                    <input type="checkbox" value="1" id="check_all_states" />
                                                    <script>
                                                        $("#check_all_states:checkbox").change(function(){
                                                               if($(this).attr("checked"))
                                                            {
                                                                $(".check_states:checkbox").attr("checked","checked");
                                                                $("#check_all_states_text").html("' . _l('Uncheck all') . '");
                                                            }
                                                               else
                                                            {
                                                                $(".check_states:checkbox").removeAttr("checked");
                                                                   $("#check_all_states_text").html("' . _l('Check all') . '");
                                                            }
                                                        });
                                                        $(document).ready(function(){
                                                            var nb_all = $(".check_states").length;
                                                            var nb_checked = $(".check_states:checked").length;
                                                            if(nb_all==nb_checked)
                                                            {
                                                                $("#check_all_states:checkbox").attr("checked","checked");
                                                                $("#check_all_states_text").html("' . _l('Uncheck all') . '");
                                                            }
                                                        });
                                                    </script>
                                                </td>
                                                <td><label id="check_all_states_text" for="check_all_states" class="sc_label2">' . _l('Check all') . '</label></td>
                                            </tr>';
        foreach ($states as $state) {
            $checked = in_array($state['id_order_state'], $values) ? true : false;
            $itemId = $ruleName . '_state_' . $state['id_order_state'];
            $html .= '
                                            <tr style="background:' . $state['color'] . ';">
                                                <td style="width:20px; text-align:center;">
                                                    <input class="check_states" type="checkbox" value="' . $state['id_order_state'] . '" name="' . $ruleName . '_state" id="' . $itemId . '" ' . ($checked ? 'checked="checked"' : '') . ' />
                                                </td>
                                                <td><label for="' . $itemId . '" class="sclabel_bgcolored">' . $state['name'] . '</label></td>
                                            </tr>';
        }
        $html .= '
                                        </table>
                                    </div>
                                </td>
                                <td style="width:110px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a PRODUCT field rule
     */
    private function displayProductRule($ruleName, $displayName, $rule)
    {
        $cookie = Context::getContext()->cookie;
        ini_set("display_errors", "ON");
        $html = '';
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $value = $rule ? $rule[1] : '';
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Product rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        // load products if any are defined
        if (Tools::strlen($value) > 0) {
            // load the products names from these ids
            $query = 'SELECT p.id_product, pl.name FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product=pl.id_product
                WHERE p.id_product IN (' . pInSQL($value) . ') AND pl.id_lang=' . (int)($cookie->id_lang);
            $results = Db::getInstance()->executeS($query);
            if ($results) {
                $html .= '
                    <script> ';
                $i = 0;
                foreach ($results as $result) {
                    $html .= '
                        productList[' . $i . '] = {id:' . $result['id_product'] . ', name:"' . htmlspecialchars($result['name']) . '"};';
                    $i++;
                }
                $html .= '
                    </script>';
            }
        }
        $html .= '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="product" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <table>
                                            <tr>
                                                <td>
                                                    ' . _l('Search') . '
                                                    <input class="search_query" type="text" style="width:180px;" id="sc_product_search_query" value="" />
                                                    ' . _l('then add the product by clicking') . '
                                                    <input type="hidden" id="sc_product_search_id_to_add" value="0" />
                                                    <input type="hidden" id="sc_product_search_name_to_add" value="" />
                                                    <a href="" onclick="javascript:return addProduct();" title="' . _l('Add the product') . '"><img style="vertical-align:middle;" src="'._PS_ADMIN_IMG_.'duplicate.gif" /></a>
                                                    <div id="search_query_result"></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span id="' . $ruleName . '_spanlist" style="font-style:italic;"></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td style="width:130px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Output a HTML <TR> to display a PRODUCT CATEGORY field rule
     */
    private function displayProductCategoryRule($ruleName, $displayName, $rule)
    {
        $cookie = Context::getContext()->cookie;
        $html = '';
        // set values with rule components if any
        $checked = $rule ? true : false;
        $ruleOperator = $rule ? $rule[0] : null;
        $value = $rule ? $rule[1] : '';
        if ($rule) {
            ExportCustomerTools::addLog('CList edit : Product category rule ' . $ruleName . ' : ' . ExportCustomerTools::captureVarDump($rule));
        }
        // load products if any are defined
        if (Tools::strlen($value) > 0) {
            // load the category names from these ids
            $query = 'SELECT c.id_category, cl.name FROM ' . _DB_PREFIX_ . 'category c
                INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl ON c.id_category=cl.id_category
                WHERE c.id_category IN (' . pInSQL($value) . ') AND cl.id_lang=' . (int)($cookie->id_lang);
            $results = Db::getInstance()->executeS($query);
            if ($results) {
                $html .= '
                    <script> ';
                $i = 0;
                foreach ($results as $result) {
                    $html .= '
                        pdtCategoryList[' . $i . '] = {id:' . $result['id_category'] . ', name:"' . htmlspecialchars($result['name']) . '"};';
                    $i++;
                }
                $html .= '
                    </script>';
            }
        }
        $html .= '
                            <tr>
                                <td class="field_label">
                                    <input type="checkbox" id="' . $ruleName . '_activate" ' . ($checked ? 'checked="checked"' : '') . './> <label class="sc_label" for="' . $ruleName . '_activate">' . $displayName . '</label>
                                    <input type="hidden" id="' . $ruleName . '_type" value="pdt_category" />
                                </td>
                                <td>
                                    <div id="1_panel_' . $ruleName . '" style="display:none;">
                                        <table style="width:95%;">
                                            <tr>
                                                <td>
                                                    ' . _l('Select a category to add to the filter') . ' :
                                                    <a href="" onclick="javascript:return toggleCategorySelect();" title="' . _l('Show/hide the categories') . '"><img style="vertical-align:middle;" src="'._PS_ADMIN_IMG_.'duplicate.gif" /></a>
                                                    ';
        // load the category tree
        $html .= $this->loadCategoryTree();
        $html .= '
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span id="' . $ruleName . '_spanlist" style="font-style:italic;"></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    </div>
                                </td>
                                <td style="width:130px;">
                                    <div id="2_panel_' . $ruleName . '" style="display:none;">
                                        <input type="checkbox" id="' . $ruleName . '_reverse" ' . ($ruleOperator == 'NOT' ? 'checked="checked"' : '') . '/> <label class="sc_label2" for="' . $ruleName . '_reverse">' . _l('Reverse rule') . '</label>
                                    </div>
                                </td>
                            </tr>';
        return $html;
    }

    /**
     * Load the prduct category tree and display it as HTML
     */
    private function loadCategoryTree()
    {
        $cookie = Context::getContext()->cookie;
        $query = '
            SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
            FROM `' . _DB_PREFIX_ . 'category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = ' . (int)$cookie->id_lang . ')
            GROUP BY id_category
            ORDER BY `level_depth` ASC, c.`position` ASC';
        if (!$result = Db::getInstance()->executeS($query)) {
            return;
        }
        $resultParents = array();
        $resultIds = array();
        foreach ($result as &$row) {
            $resultParents[$row['id_parent']][] =& $row;
            $resultIds[$row['id_category']] =& $row;
        }
        $blockCategTree = $this->getTree($resultParents, $resultIds, 0);
        unset($resultParents);
        unset($resultIds);
        $isDhtml = (Configuration::get('BLOCK_CATEG_DHTML') == 1 ? true : false);
        // build the HTML
        $html = '<div id="c_o_p_cat_order_pdt_category_categoryTree" class="dhtmlxTree" setImagePath="lib/js/imgs/dhxtree_material/" style="width:250px; height:218px;overflow:auto;">
                <ul>';
        for ($i = 0; $i < count($blockCategTree['children']); $i++) {
            $child = $blockCategTree['children'][$i];
            $isLast = ($i == (count($blockCategTree['children']) - 1));
            $html .= $this->addCategoryHtml($child, $isLast);
        }
        $html .= '</ul>
            </div>
        <script>
        const orderProductTree = dhtmlXTreeFromHTML("c_o_p_cat_order_pdt_category_categoryTree");
        </script>';
        return $html;
    }

    /**
     * Display a category branch as HTML
     */
    private function addCategoryHtml($node)
    {
        $html = '<li><a href="" style="font-weight:bold;" onclick="javascript:return addCategory(' . $node['id'] . ', this);" title="' . _l('Click to add this category to filter') . '">' . $node['name'] . '</a>';
        if (count($node['children']) > 0)
        {
            // compute left margin according to depth
            $html .= '<ul>';
            for ($i = 0; $i < count($node['children']); $i++) {
                $child = $node['children'][$i];
                $html .= $this->addCategoryHtml($child);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    private function getTree($resultParents, $resultIds, $maxDepth, $id_category = 1, $currentDepth = 0)
    {
        $link = Context::getContext()->link;
        $children = array();
        if (isset($resultParents[$id_category]) and sizeof($resultParents[$id_category]) and ($maxDepth == 0 or $currentDepth < $maxDepth)) {
            foreach ($resultParents[$id_category] as $subcat) {
                $children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
            }
        }
        if (!isset($resultIds[$id_category])) {
            return false;
        }
        return array(
            'id' => $id_category,
            'link' => $link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
            'name' => $resultIds[$id_category]['name'],
            'desc' => $resultIds[$id_category]['description'],
            'depth' => $currentDepth,
            'children' => $children
        );
    }

    private function initCustomerRules()
    {
        $this->customerRules = array(
            'c_gender' => array('text' => _l('Gender'), 'type' => 'gender'),
            'c_first_name' => array('text' => _l('First name'), 'type' => 'string'),
            'c_last_name' => array('text' => _l('Last name'), 'type' => 'string'),
            'c_email' => array('text' => _l('Email'), 'type' => 'string'),
            'c_birthday_date' => array('text' => _l('Birthday date'), 'type' => 'date'),
            'c_news' => array('text' => _l('Newsletter'), 'type' => 'boolean', 'choiceText1' => _l('Have subscribed'), 'choiceText2' => _l('Have NOT subscribed')),
            'c_optin' => array('text' => _l('Third-party offers'), 'type' => 'boolean', 'choiceText1' => _l('ok to receive offers'), 'choiceText2' => _l('NOT ok to receive offers')),
            'c_active' => array('text' => _l('Active account'), 'type' => 'boolean', 'choiceText1' => _l('Enabled'), 'choiceText2' => _l('Disabled')),
            'c_deleted' => array('text' => _l('Deleted account'), 'type' => 'boolean', 'choiceText1' => _l('Deleted'), 'choiceText2' => _l('Not deleted')),
            'c_date_add' => array('text' => _l('Register date'), 'type' => 'date'),
            'c_lang' => array('text' => _l('Lang'), 'type' => 'lang')
        );
    }

    private function initCustomerAddressRules()
    {
        $this->customerAddressRules = array(
            'c_a_company' => array('text' => _l('Company'), 'type' => 'string'),
            'c_a_first_name' => array('text' => _l('First name'), 'type' => 'string'),
            'c_a_last_name' => array('text' => _l('Last name'), 'type' => 'string'),
            'c_a_address' => array('text' => _l('Address'), 'type' => 'string'),
            'c_a_postcode' => array('text' => _l('Postcode'), 'type' => 'string'),
            'c_a_city' => array('text' => _l('City'), 'type' => 'string'),
            'c_a_state' => array('text' => _l('State'), 'type' => 'string'),
            'c_a_country' => array('text' => _l('Country'), 'type' => 'string'),
            'c_a_phone' => array('text' => _l('Phone'), 'type' => 'string'),
            'c_a_mobilephone' => array('text' => _l('Phone mobile'), 'type' => 'string')
        );
    }

    private function initCustomerGroupRules()
    {
        $this->customerGroupRules = array(
            'c_g_group' => array('text' => _l('Group'), 'type' => 'group')
        );
    }

    private function initCustomerOrderRules()
    {
        $this->customerOrderRules = array(
            'c_o_order_without' => array('text' => _l('Without orders'), 'type' => 'boolean', 'choiceText1' => _l('Without orders'), 'choiceText2' => _l('With orders')),
            'c_o_order_date' => array('text' => _l('Orders date'), 'type' => 'date'),
            'c_o_p_order_product' => array('text' => _l('Product in orders'), 'type' => 'product'),
            'c_o_p_cat_order_pdt_category' => array('text' => _l('Product category in orders'), 'type' => 'pdt_category'),
            'c_o_order_agg_nb' => array('text' => _l('Number of orders'), 'type' => 'number'),
            'c_o_order_agg_sum' => array('text' => _l('Amount of orders'), 'type' => 'number'),
            'c_o_order_state' => array('text' => _l('Orders State'), 'type' => 'order_state')
        );
    }

    private function initCustomerOtherListRules()
    {
        $this->customerOtherListRules = array(
            'c_logic_list' => array('text' => _l('Other customers list'), 'type' => 'other_list')
        );
    }

    private function initCustomerNewsletterRules()
    {
        $this->customerNewsletterRules = array(
            'n_email' => array('text' => _l('Email'), 'type' => 'string'),
            'n_date_add' => array('text' => _l('Subscription date'), 'type' => 'date')
        );
    }
}
