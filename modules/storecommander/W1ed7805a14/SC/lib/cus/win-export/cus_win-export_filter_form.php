<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportCustomers_ACTIVE') || (int)SC_ExportCustomers_ACTIVE !== 1) {
    exit;
}

$filterId = (int)Tools::getValue(ExportCustomerFilter::$definition['primary']);
$id_lang = (int)$sc_agent->id_lang;

$iso = Language::getIsoById($id_lang);
$link = new Link();
$url_js_dir = 'lib/js/jquery/';
$exportCustomerFilterObj = new ExportCustomerFilter($filterId);
$exportCustomerFilterFormObj = new ExportCustomerFilterForm($id_lang);

$ruleDescriptors = array();
$dynamicDefinition = ExportCustomerTools::getFieldValue($exportCustomerFilterObj, 'dynamic_definition');
if ($dynamicDefinition) {
    $dynamicDefinition = urldecode($dynamicDefinition);
    $parts = explode('__', $dynamicDefinition);
    // load all rules in a array with informations : type (rule or operator), operator, rule_name, rule_descriptor
    // Useful because some rules must be treated grouped like order rules : c_o_order_number & c_o_order_sum for example
    foreach ($parts as $rule) {
        // is this an operator ?
        if ($rule == 'AND' || $rule == 'OR') {
            $ruleDescriptors[] = array(
                'type' => 'operator',
                'operator' => $rule
            );
        } else // or a rule ?
        {
            $subparts = explode('=>', $rule);
            $ruleName = $subparts[0];
            $ruleDescriptors[] = array(
                'type' => 'rule',
                'name' => $ruleName,
                'descriptor' => explode('|', $subparts[1])
            );
        }
    }
    if ($exportCustomerFilterObj->id) {
        ExportCustomerTools::addLog('CList edit : id=' . $exportCustomerFilterObj->id . ' / Dynamic def splitted into ' . count($ruleDescriptors) . ' rule descriptors');
    }
}

if (version_compare(_PS_VERSION_, '1.7.6.0', '>=')) {
    $urlData = array(
        'ajax' => 1,
        'controller' => 'AdminProducts',
        'action' => 'productsList',
        'forceJson' => 1,
        'disableCombination' => 1,
        'limit' => 10,
        'token' => $sc_agent->getPSToken('AdminProducts')
    );
    $link_search = SC_PS_PATH_ADMIN_REL . 'index.php?' . http_build_query($urlData);
} else {
    $link_search = SC_PS_PATH_ADMIN_REL . 'ajax_products_list.php';
}

$panelCustomerRules = '';
foreach ($exportCustomerFilterFormObj->customerRules as $ruleKey => $ruleInfos) {
    $panelCustomerRules .= $exportCustomerFilterFormObj->displayFieldRule($ruleKey, $ruleInfos, $ruleDescriptors);
}

$panelCustomerAddressRules = '';
foreach ($exportCustomerFilterFormObj->customerAddressRules as $ruleKey => $ruleInfos) {
    $panelCustomerAddressRules .= $exportCustomerFilterFormObj->displayFieldRule($ruleKey, $ruleInfos, $ruleDescriptors);
}

$panelCustomerGroupRules = '';
foreach ($exportCustomerFilterFormObj->customerGroupRules as $ruleKey => $ruleInfos) {
    $panelCustomerGroupRules .= $exportCustomerFilterFormObj->displayFieldRule($ruleKey, $ruleInfos, $ruleDescriptors);
}

$panelCustomerOrderRules = '';
foreach ($exportCustomerFilterFormObj->customerOrderRules as $ruleKey => $ruleInfos) {
    $panelCustomerOrderRules .= $exportCustomerFilterFormObj->displayFieldRule($ruleKey, $ruleInfos, $ruleDescriptors);
}

$panelCustomerOtherListRules = '';
foreach ($exportCustomerFilterFormObj->customerOtherListRules as $ruleKey => $ruleInfos) {
    $ruleInfos['editedList'] = $exportCustomerFilterObj;
    $panelCustomerOtherListRules .= $exportCustomerFilterFormObj->displayFieldRule($ruleKey, $ruleInfos, $ruleDescriptors);
}

$panelNewsletterRules = '';
foreach ($exportCustomerFilterFormObj->customerNewsletterRules as $ruleKey => $ruleInfos) {
    $panelNewsletterRules .= $exportCustomerFilterFormObj->displayFieldRule($ruleKey, $ruleInfos, $ruleDescriptors);
}

$field_value_static_definition = htmlentities(ExportCustomerTools::getFieldValue($exportCustomerFilterObj, 'static_definition'), ENT_COMPAT, 'UTF-8');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo SC_CSSDHTMLX; ?>"/>
    <link rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>"/>
    <link rel="stylesheet" href="<?php echo SC_CSS_FONTAWESOME; ?>"/>
    <script src="<?php echo SC_JQUERY; ?>"></script>
    <script src="<?php echo SC_JQUERY_UI; ?>"></script>
    <script src="<?php echo SC_JSDHTMLX; ?>"></script>
    <script src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 0.9em;
            width: auto;
            height: auto;
            margin: 8px;
        }

        #mainForm {
            padding-bottom: 20px;
        }

        #panelDynamic .panel {
            padding: 0 0 5px 0;
            margin-bottom: 20px;
        }

        #panelDynamic .panel .title {
            background: #999a9b;
            color: #fff;
            line-height: 2em;
            padding: 0 14px;
            font-size: 1.1em;
        }

        table.tableRule {
            width: 100%;
            border-spacing: 0 5px;
            border-collapse: separate;
        }

        table.tableRule tr {
            background: #efefef;
        }

        table.tableRule tr td {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        table.tableRule tr td.field_label {
            width: 250px;
        }


        table.tableRule tr td:first-child {
            padding-left: 7px;
        }

        #c_o_p_cat_order_pdt_category_categoryTree {
            border: 1px solid #000000;
            margin-top: 3px;
            padding: 2px;
            width: 100%;
        }

        #c_o_p_cat_order_pdt_category_categoryTree table tr,
        #c_o_p_cat_order_pdt_category_categoryTree table tr td,
        #c_o_p_cat_order_pdt_category_categoryTree table tr td:first-child {
            padding: 0;
        }

        label {
            width: 220px;
        }

        label.sclabel {
            font-weight: normal;
            float: none;
        }

        label.sclabel2 {
            font-weight: normal;
            float: none;
        }

        .order_state label.sclabel_bgcolored {
            color: #fff;
            text-shadow: 0px 0px 4px #000;
            padding-left: 10px
        }

        .order_state label.sclabel2 {
            color: #000;
        }

        .order_state label.sclabel2.sclabel_reverse {
            color: #585A69;
            text-shadow: 0 1px 0 #FFFFFF;
        }

        table.tableRule td {
            border: 0;
        }

        table.tableRule input[type=checkbox] {
            margin-top: 3px;
            margin-left: 2px;
        }

        #form_footer {
            display: flex;
            padding: 0 20px;
            justify-content: space-between;
        }

        #form_footer > span {
            color: red;
        }

        #form_footer > button[type=submit] {
            padding: 5px 10px;
        }
    </style>
    <script>
        const msg = [];

        msg["root"] = "<?php echo _l('Invalid definition of filter for field'); ?>";
        msg["c_first_name"] = "<?php echo _l('First name'); ?>";
        msg["c_last_name"] = "<?php echo _l('Last name'); ?>";
        msg["c_birthday_date"] = "<?php echo _l('Birthday date'); ?>";
        msg["c_email"] = "<?php echo _l('Email'); ?>";
        msg["c_date_add"] = "' .utf8_decode(ExportCustomerTools::prepareValueForJs(_l('Account creation date')));";
        msg["c_a_company"] = "<?php echo _l('Company'); ?>";
        msg["c_a_first_name"] = "<?php echo _l('Address first name'); ?>";
        msg["c_a_last_name"] = "<?php echo _l('Address last name'); ?>";
        msg["c_a_address"] = "<?php echo _l('Address'); ?>";
        msg["c_a_postcode"] = "<?php echo _l('Postcode'); ?>";
        msg["c_a_city"] = "<?php echo _l('City'); ?>";
        msg["c_a_state"] = "<?php echo _l('State'); ?>";
        msg["c_a_country"] = "<?php echo _l('Country'); ?>";
        msg["c_a_phone"] = "<?php echo _l('Phone'); ?>";
        msg["c_a_phone_mobile"] = "<?php echo _l('Phone mobile'); ?>";
        msg["c_g_group"] = "<?php echo _l('Group'); ?>";
        msg["c_o_order_without"] = "<?php echo _l('Without orders'); ?>";
        msg["c_o_order_state"] = "<?php echo _l('Order states'); ?>";
        msg["c_o_order_agg_sum"] = "<?php echo _l('Order total amount'); ?>";
        msg["c_o_order_agg_nb"] = "<?php echo _l('Nb of orders'); ?>";
        msg["c_o_order_date"] = "<?php echo _l('Orders in period'); ?>";
        msg["n_email"] = "<?php echo _l('Email'); ?>";
        msg["n_date_add"] = "<?php echo _l('Subscription date'); ?>";

        msg["state_should_be_checked"] = "<?php echo _l('To apply a filter on orders amount or number, you should also select the order states to include.'); ?>";
        msg["state_should_be_checked_for_pdt"] = "<?php echo _l('To apply a filter on orders products/category, you should also select the order states to include.'); ?>";
        msg["you_should_select_one_product"] = "<?php echo _l('No product has been selected for filter.'); ?>";
        msg["you_should_select_one_category"] = "<?php echo _l('No category has been selected for filter.'); ?>";

        msg["No product to add"] = "<?php echo _l('No product to add'); ?>";


        // to store client side information for product / category filters

        // Each element in these arrays are like : {id:id1, name:'name1'}

        var productList = [];

        var pdtCategoryList = [];

        // check for jquery version because of change between attr / prop at 1.6 version
        // ex: check 1.11 < 1.6
        let current_jquery = jQuery(document).jquery.split('.');
        var jqueryBefore16 = current_jquery[1] < 6; // ex 11 < 6


        /**

         * Create the final string that represents all rules defined by user

         */

        function createRuleDefinitionFromPanels() {

            var r = '';

            // general rules

            if ($('#include_customers').is(":checked"))

                r += 'c_all=>NONE|1';

            if ($('#include_newsletter').is(":checked"))

                r += (r.length > 0 ? '__AND__' : '') + 'n_all=>NONE|1';

            // panel rules

            $('div[id*="Rules"]:visible').find('input[type="checkbox"][id*="_activate"]:checked').each(function () {

                r += (r.length > 0 ? '__AND__' : '');

                r += createRuleDefinition($(this));

            });

            return r;

        }

        function createRuleDefinition(obj) {

            // extract rule name from id

            var checkId = obj.attr("id");

            var ruleName = checkId.substr(0, checkId.length - "_activate".length);

            // get type from associated hidden field

            var ruleType = $("#" + ruleName + "_type").attr("value");

            // console.log("add to whole rule " + checkId + " of type " + ruleType);

            // create the json for each rule type

            switch (ruleType) {

                case "boolean":

                    return createRuleDefinitionForBoolean(ruleName);

                case "string":

                    return createRuleDefinitionForString(ruleName);

                case "number":

                    return createRuleDefinitionForNumber(ruleName);

                case "date":

                    return createRuleDefinitionForDate(ruleName);

                case "gender":

                    return createRuleDefinitionForGender(ruleName);

                case "group":

                    return createRuleDefinitionForGroup(ruleName);

                case "order_state":

                    return createRuleDefinitionForOrderState(ruleName);

                case "product":

                    return createRuleDefinitionForPdtOrCat(ruleName);

                case "pdt_category":

                    return createRuleDefinitionForPdtOrCat(ruleName);

                case "other_list":

                    return createRuleDefinitionForOtherList(ruleName);

            }

            return '';

        }

        function createRuleDefinitionForOtherList(ruleName) {

            //c_logic_list=>NONE|UNION|2

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            r += $('#' + ruleName + '_operator').val() + '|';

            r += $('#' + ruleName + '_value').val();

            return r;

        }

        function createRuleDefinitionForBoolean(ruleName) {

            // c_news=>NONE|=|1

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            r += '=|';

            r += $('input[type="radio"][name*="' + ruleName + '_choice"]:checked').val();

            return r;

        }

        function createRuleDefinitionForString(ruleName) {

            // c_first_name=>NONE|LIKE|John

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            r += $('#' + ruleName + '_operator').val() + '|';

            r += $('#' + ruleName + '_value').val();

            return r;

        }

        function createRuleDefinitionForNumber(ruleName) {

            //c_o_order_agg_sum=>NONE|40|>=||

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            r += $('#' + ruleName + '_value1').val() + '|';

            r += $('#' + ruleName + '_operator1').val() + '|';

            r += $('#' + ruleName + '_operator2').val() + '|';

            r += $('#' + ruleName + '_value2').val();

            return r;

        }

        function createRuleDefinitionForDate(ruleName) {

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            if ($('input[type="radio"][name*="' + ruleName + '_date"]:checked').val() == "monthchoice") {
                //operator
                r += '=|';
                //value
                r += $('#' + ruleName + '_month').val();

            } else if ($('input[type="radio"][name*="' + ruleName + '_date"]:checked').val() == "norange") {

                // c_o_order_date=>NONE|>=|100|DAY

                r += $('#' + ruleName + '_1_operator').val() + '|';

                r += $('#' + ruleName + '_1_value').val() + '|';

                r += $('#' + ruleName + '_1_unit').val();

            } else {

                // c_birthday_date=>NONE|1996-04-21[<=|>|1975-01-01

                r += $('#' + ruleName + '_2_date_value1').val() + '|';

                r += $('#' + ruleName + '_2_operator1').val() + '|';

                r += $('#' + ruleName + '_2_operator2').val() + '|';

                r += $('#' + ruleName + '_2_date_value2').val();

            }

            return r;

        }

        function createRuleDefinitionForGender(ruleName) {

            //c_gender=>NONE|=|1

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            r += $('#' + ruleName + '_operator').val() + '|';

            r += $('#' + ruleName + '_value').val();

            return r;

        }

        function createRuleDefinitionForGroup(ruleName) {

            //c_g_group=>NONE|gp1,gp2,...

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            var nbCheck = 0;

            $('input[type="checkbox"][name="' + ruleName + '_group"]:checked').each(function () {

                r += $(this).attr("value") + ',';

                nbCheck++;

            });

            if (nbCheck > 0)

                // remove trailing comma

                r = removeTrailingChar(r, ',');

            else

                return '';

            return r;

        }

        function createRuleDefinitionForOrderState(ruleName) {

            //c_o_order_state=>NONE|gp1,gp2,...

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';

            var nbCheck = 0;

            $('input[type="checkbox"][name="' + ruleName + '_state"]:checked').each(function () {

                r += $(this).attr("value") + ',';

                nbCheck++;

            });

            if (nbCheck > 0)

                // remove trailing comma

                r = removeTrailingChar(r, ',');

            else

                return '';

            return r;

        }

        function createRuleDefinitionForPdtOrCat(ruleName) {

            // c_o_p_order_product=>NONE|7,8,10,11

            // c_o_p_cat_order_pdt_category=>NONE|7,8,10,11

            var r = ruleName + '=>';

            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';


            if (ruleName == 'c_o_p_order_product') {

                var text = "";

                $.each(productList, function (index, value) {

                    text += (index > 0 ? ',' : '') + value.id;

                });

            } else if (ruleName == 'c_o_p_cat_order_pdt_category') {

                var text = "";

                $.each(pdtCategoryList, function (index, value) {

                    text += (index > 0 ? ',' : '') + value.id;

                });

            }

            r += text;

            return r;

        }

        function removeTrailingChar(text, c) {

            if (text == null || text.length == 0)

                return text;

            if (text.substr(text.length - 1, 1) == c)

                return text.substr(0, text.length - 1);

            return text;

        }

        /**

         * Check the user choices for all rules. Display error messages.

         */

        function checkRules() {

            var check = true;

            // deal only with checked checkboxes of visible rule panels

            $('div[id*="Rules"]:visible').find('input[type="checkbox"][id*="_activate"]:checked').each(function () {

                var checkId = $(this).attr("id");

                var ruleName = checkId.substr(0, checkId.length - "_activate".length);

                // get type from associated hidden field

                var ruleType = $("#" + ruleName + "_type").attr("value");

                // hack for "c_o_order_agg_" rules

                if (ruleName.indexOf("c_o_order_agg_") >= 0)

                    ruleType = "order_agg";

                //console.log("check rule1 " + ruleName + " - " + checkId+ " of type " + ruleType);

                switch (ruleType) {

                    case "boolean":

                        // no check for boolean

                        break;
                        ;

                    case "string":

                        if (!checkRuleForString(ruleName)) {

                            check = false;

                            return false;

                        }

                        break;

                    case "number":

                        if (!checkRuleForNumber(ruleName)) {

                            check = false;

                            return false;

                        }

                        break;

                    case "date":

                        if (!checkRuleForDate(ruleName)) {

                            check = false;

                            return false;

                        }

                        break;

                    case "gender":

                        // no check for gender

                        break;

                    case "group":

                        if (!checkRuleForGroup(ruleName)) {

                            check = false;

                            return false;

                        }

                        break;

                    case "order_state":

                        if (!checkRuleForOrderState(ruleName)) {

                            check = false;

                            return false;

                        }

                        break;

                    case "order_agg":

                        // first check the number rule

                        if (!checkRuleForNumber(ruleName)) {

                            check = false;

                            return false;

                        }

                        // check if the state field is visible
                        if (!$('#c_o_order_state_activate').is(":checked") && $('#' + ruleName + '_value1').val() > 0) {

                            alert(msg["state_should_be_checked"]);

                            check = false;

                            return false;

                        }

                        break;

                    case "product":

                        // check if at least one product has been selected

                        if (productList.length == 0) {

                            alert(msg["you_should_select_one_product"]);

                            check = false;

                            return false;

                        }

                        // check if the state field is visible

                        if (!$('#c_o_order_state_activate').is(":checked")) {

                            alert(msg["state_should_be_checked_for_pdt"]);

                            check = false;

                            return false;

                        }

                        break;

                    case "pdt_category":

                        // check if at least one category has been selected

                        if (pdtCategoryList.length == 0) {

                            alert(msg["you_should_select_one_category"]);

                            check = false;

                            return false;

                        }

                        // check if the state field is visible

                        if (!$('#c_o_order_state_activate').is(":checked")) {

                            alert(msg["state_should_be_checked_for_pdt"]);

                            check = false;

                            return false;

                        }

                        break;

                }

            });

            return check;

        }

        function checkRuleForString(ruleName) {

            // be sure a value is set

            //console.log("check STRING field " + ruleName);

            if ($('#' + ruleName + '_value').val().length == 0) {

                alert(msg["root"] + " " + msg[ruleName]);

                return false;

            }

            return true;

        }

        function checkRuleForNumber(ruleName) {

            // be sure a value1 is set

            //console.log("check NUMBER field " + ruleName);

            if ($('#' + ruleName + '_value1').val().length == 0) {

                alert(msg["root"] + " " + msg[ruleName]);

                return false;

            }

            // be sure a value2 is set if an operator2 is set

            if ($('#' + ruleName + '_operator2').val() != '' && $('#' + ruleName + '_value2').val().length == 0) {

                alert(msg["root"] + " " + msg[ruleName]);

                return false;

            }

            return true;

        }

        function checkRuleForDate(ruleName) {

            // in case of unit date rule

            //console.log("check DATE field " + ruleName);

            if ($('input[type="radio"][name*="' + ruleName + '_date"]:checked').val() != "monthchoice") {
                if ($('input[type="radio"][name*="' + ruleName + '_date"]:checked').val() == "norange") {

                    // be sure a value is set

                    if ($('#' + ruleName + '_1_value').val().length == 0) {

                        alert(msg["root"] + " " + msg[ruleName]);

                        return false;

                    }

                } else {	// range date

                    // be sure a value1 is set

                    if ($('#' + ruleName + '_2_date_value1').val().length == 0) {

                        alert(msg["root"] + " " + msg[ruleName]);

                        return false;

                    }

                    // be sure a value2 is set if an operator2 is set

                    if ($('#' + ruleName + '_2_operator2').val() != '' && $('#' + ruleName + '_2_date_value2').val().length == 0) {

                        alert(msg["root"] + " " + msg[ruleName]);

                        return false;

                    }

                }
            }

            return true;

        }

        function checkRuleForGroup(ruleName) {

            //console.log("check GROUP field " + ruleName);

            var nbCheck = $('input[type="checkbox"][name="' + ruleName + '_group"]:checked').length;

            if (nbCheck == 0) {

                alert(msg["root"] + " " + msg[ruleName]);

                return false;

            }

            return true;

        }

        function checkRuleForOrderState(ruleName) {

            //console.log("check ORDER_STATE field " + ruleName);

            var nbCheck = $('input[type="checkbox"][name="' + ruleName + '_state"]:checked').length;

            if (nbCheck == 0) {

                alert(msg["root"] + " " + msg[ruleName]);

                return false;

            }

            return true;

        }

        // add a new product to the filter

        function addProduct() {

            var idToAdd = $('#sc_product_search_id_to_add').val();

            if (idToAdd == 0) {

                alert(msg["No product to add"]);

                return false;

            }

            var nameToAdd = $('#sc_product_search_name_to_add').val();

            // TODO : JSON encode the name

            nameToAdd = $('<div/>').text(nameToAdd).html();

            // append a new object to the global product filter array

            productList.push({id: idToAdd, name: nameToAdd});

            // empty the search and the toAdd fields

            $('#sc_product_search_query').val("");

            $('#sc_product_search_id_to_add').val(0);

            $('#sc_product_search_name_to_add').val("");

            updatePanels();

            return false;

        }

        // Remove the product with the input id

        function removeProduct(id) {

            var indexToRemove = -1;

            // look in the productList array to remove the object with the input id

            for (var i = 0; i < productList.length; i++) {

                // check for id

                if (productList[i].id == id) {

                    indexToRemove = i;

                    break;

                }

            }

            if (indexToRemove != -1) {

                productList.splice(indexToRemove, 1);

                updatePanels();

            }

        }

        // add a new category to the filter

        function addCategory(idToAdd, aTag) {

            nameToAdd = $(aTag).html();

            // TODO : JSON encode the name

            nameToAdd = $('<div/>').text(nameToAdd).html();

            // append a new object to the global category filter array

            pdtCategoryList[pdtCategoryList.length] = {id: idToAdd, name: nameToAdd};

            updatePanels();

            return false;

        }

        // Remove the category with the input id

        function removeCategory(id) {

            var indexToRemove = -1;

            // look in the pdtCategoryList array to remove the object with the input id

            for (var i = 0; i < pdtCategoryList.length; i++) {

                // check for id

                if (pdtCategoryList[i].id == id) {

                    indexToRemove = i;

                    break;

                }

            }

            if (indexToRemove != -1) {

                pdtCategoryList.splice(indexToRemove, 1);

                updatePanels();

            }

        }

        // toggle the visibility of the category tree div

        function toggleCategorySelect() {

            $("#c_o_p_cat_order_pdt_category_categoryTree").toggle(400);

            return false;

        }

        // bind event on activate / disactivate checkboxes changes

        function bindCheckboxesClick() {

            // on main checkboxes

            $('input[type="checkbox"][id*="include_"]').click(function (event) {
                updatePanels();

            });

            // on rule panels checkboxes

            $('input[type="checkbox"][id*="_activate"]').click(function (event) {

                updatePanels();

            });

            // on DATE rule panels radio buttons

            $('input[type="radio"][id*="_date"]').click(function (event) {
                updatePanels();

            });

        }

        // refresh all the panels to display
        function updatePanels() {
            // apply main checkboxes consequences
            $('input[type="checkbox"][id*="include_"]').each(function () {
                var checked = $(this).is(":checked");
                if ($(this).attr("id") == "include_customers")
                    if (checked)
                        $('div[id*="panelCustomer"]').show("fast");
                    else
                        $('div[id*="panelCustomer"]').hide("fast");
                else if ($(this).attr("id") == "include_newsletter")
                    if (checked)
                        $('div[id*="panelNewsletter"]').show("fast");
                    else
                        $('div[id*="panelNewsletter"]').hide("fast");
            });

            // apply panel checkboxes consequences
            $('input[type="checkbox"][id*="_activate"]').each(function () {
                // the checkbox checked state
                var checked = $(this).is(":checked");
                // get the id radix (without the suffix _activate)
                var checkId = $(this).attr("id");
                var radix = checkId.substr(0, checkId.length - "_activate".length);
                // show / hide the definition panels
                if (checked)
                    $('div[id*="panel_' + radix + '"]').show("fast");
                else
                    $('div[id*="panel_' + radix + '"]').hide("fast");
                $('[id*="' + radix + '"]').each(function () {
                    if ($(this).attr("id") == checkId)
                        return;
                    if (jqueryBefore16)
                        $(this).attr("disabled", (checked ? null : "disabled"));
                    else
                        $(this).prop("disabled", !checked);
                    // set the label for checkbox bold if checked
                    $('label[for="' + checkId + '"]').css("font-weight", checked ? "bold" : "normal");
                });
            });

            $('input[type="radio"][name*="_date"]').each(function () {
                var checked = $(this).is(":checked");
                // get the id radix (without the suffix _date)
                if (jqueryBefore16) {
                    $(this).parent().find($("input,select")).not($(this)).attr("disabled", checked ? null : "disabled");
                } else {
                    $(this).parent().find($("input,select")).not($(this)).prop("disabled", !checked);
                }
            });

            // fill in the product / category field according to current datas

            var pdtCategoryListValue = "";

            $.each(pdtCategoryList, function (index, value) {

                pdtCategoryListValue += (index > 0 ? ' <?php echo _l('OR'); ?> ' : '') + ' <a href="javascript:removeCategory(' + value.id + ');" title="<?php echo _l('Click to remove this product category from rule'); ?>">' + value.name + '</a>';

            });

            $('#c_o_p_cat_order_pdt_category_spanlist').html(pdtCategoryListValue);

            // remove duplicate entries
            productList = removeDuplicates(productList);

            var productListValue = "";

            $.each(productList, function (index, value) {

                productListValue += (index > 0 ? ' <?php echo _l('OR'); ?> ' : '') + ' <a href="javascript:removeProduct(' + value.id + ');" title="<?php echo _l('Click to remove this product from rule'); ?>">' + value.name + '</a>';

            });

            $('#c_o_p_order_product_spanlist').html(productListValue);

        }

        function removeDuplicates(array) {
            return array.filter((value, index) => {
                const _value = JSON.stringify(value);
                return index === array.findIndex(obj => {
                    return JSON.stringify(obj) === _value;
                });
            });
        }
    </script>
    <script>
        jQuery().ready(function () {

            // bind click event to all checbox items

            bindCheckboxesClick();

            // refresh all panels states

            updatePanels();

            // bind the form submit event

            $("#mainForm").submit(function (event) {

                // check the right to submit

                if (!checkRules()) {

                    event.preventDefault();

                    return false;

                }

                // get the json

                var ruleDefinition = createRuleDefinitionFromPanels();

                // put it into the hidden field for server submission

                //console.log(ruleDefinition);

                $("#dynamic_definition").val(encodeURIComponent(ruleDefinition));


                return true;

            });

            $("#sc_product_search_query").autocomplete({
                appendTo: "#search_query_result",
                classes: {
                    "ui-autocomplete": "autocomplete_window_export",
                },
                source: function (request, response) {
                    $.ajax({
                        url: '<?php echo $link_search; ?>',
                        minChars: 1,
                        max: 20,
                        width: 500,
                        cacheLength:0,
                        selectFirst: false,
                        scroll: false,
                        blockSubmit:true,
                        dataType: "json",
                        data: {
                            q: request.term
                        },
                        success: function (data) {
                            const formattedData = [];
                            data.forEach(function (item) {
                                formattedData.push({
                                    data: {
                                        id_product: item.id,
                                        pname: item.name
                                    },
                                    value: item.name
                                });
                            });
                            response(formattedData);
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    $('#sc_product_search_query').val(ui.item.data.pname);
                    $('#sc_product_search_id_to_add').val(ui.item.data.id_product);
                    $('#sc_product_search_name_to_add').val(ui.item.data.pname);
                },
            });

            // sans commmandes check
            disableCheckboxWithoutOrders();

            $('#c_o_order_without_activate, #c_o_order_without_choice_1, #c_o_order_without_choice_2').change(function () {
                disableCheckboxWithoutOrders();
            });

            function disableCheckboxWithoutOrders() {
                if ($('#c_o_order_without_type').is(':disabled')) {
                    $('#panelCustomerOrderRules input[type=checkbox]').not($('#c_o_order_without_activate')).removeAttr('disabled');
                } else {
                    if ($('#c_o_order_without_choice_1').is(':checked')) {
                        $('#panelCustomerOrderRules input[type=checkbox]').not($('#c_o_order_without_activate')).attr('disabled', 'disabled');
                    } else {
                        $('#panelCustomerOrderRules input[type=checkbox]').not($('#c_o_order_without_activate')).removeAttr('disabled');
                    }
                }
            }

        });

        function submitMainForm()
        {
            if (!checkRules()) {
                return false;
            }

            let ruleDefinition = createRuleDefinitionFromPanels();
            let dd_input = $("#dynamic_definition");
            dd_input.val(encodeURIComponent(ruleDefinition));

            $.post('index.php?ajax=1&act=cus_win-export_filter_update', {
                action: 'submit_filter_form',
                '<?php echo ExportCustomerFilter::$definition['primary']; ?>': $('#<?php echo ExportCustomerFilter::$definition['primary']; ?>').val(),
                dynamic_definition: dd_input.val(),
                static_definition: $("#static_definition").val()
            },function (response) {
                let dataResponse = JSON.parse(response);
                parent.dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
            });
            return false;
        }
    </script>
    <script>
        jQuery().ready(function () {
            let exportCalendar = new dhtmlXCalendarObject($('input.calendar'));
            exportCalendar.setDateFormat("%d/%m/%Y");
            exportCalendar.hideTime();
            dhtmlXCalendarObject.prototype.langData["<?php echo $iso; ?>"] = parent.lang_calendar;
            exportCalendar.loadUserLanguage("<?php echo $iso; ?>");
        });
    </script>
</head>
<body>
<form id="mainForm" action="/" method="post" enctype="multipart/form-data" class="width2" style="width:100%;">
    <input type="hidden" name="<?php echo ExportCustomerFilter::$definition['primary']; ?>" id="<?php echo ExportCustomerFilter::$definition['primary']; ?>" value="<?php echo $filterId; ?>"/>
    <input type="hidden" name="dynamic_definition" id="dynamic_definition" value=""/>

    <div id="panelStatic" style="display:none;">
        <label><?php echo _l('Static definition:'); ?> </label>
        <div class="margin-form">
            <input type="text" size="33" name="static_definition" id="static_definition" value="<?php echo $field_value_static_definition; ?>"/> <sup>*</sup>
        </div>
    </div>

    <div id="panelDynamic">
        <div class="panel">
            <input type="checkbox" size="33" id="include_customers" <?php echo(strpos($dynamicDefinition, 'c_') !== false ? 'checked="checked"' : ''); ?> />
            <label><?php echo _l('will include in list all customers and then, filter them by some properties you will define'); ?></label>
        </div>

        <div id="panelCustomerRules" class="panel">
            <div class="title"><?php echo _l('Filter on customers properties'); ?></div>
            <table class="tableRule"><?php echo $panelCustomerRules; ?></table>
        </div>

        <div id="panelCustomerAddressRules" class="panel">
            <div class="title"><?php echo _l('Filter on customers address properties'); ?></div>
            <table class="tableRule"><?php echo $panelCustomerAddressRules; ?></table>
        </div>

        <div id="panelCustomerGroupRules" class="panel">
            <div class="title"><?php echo _l('Filter on customers group properties'); ?></div>
            <table class="tableRule"><?php echo $panelCustomerGroupRules; ?></table>
        </div>

        <div id="panelCustomerOrderRules" class="panel">
            <div class="title"><?php echo _l('Filter on customers orders properties'); ?></div>
            <table class="tableRule"><?php echo $panelCustomerOrderRules; ?></table>
        </div>

        <div id="panelCustomerOtherListRules" class="panel">
            <div class="title"><?php echo _l('Filter with other Customers Lists'); ?></div>
            <table class="tableRule"><?php echo $panelCustomerOtherListRules; ?></table>
        </div>

        <div id="panelNewsletterRules" class="panel">
            <div class="title"><?php echo _l('Filter on Newsletters subscribers properties'); ?></div>
            <table class="tableRule"><?php echo $panelNewsletterRules; ?></table>
        </div>
    </div>
    <div>
        <p><i><?php echo _l('Warning : if you choose some order fields in export model, you must select order status in filters.'); ?></i></p>
    </div>
    <div id="form_footer">
        <span><sup>*</sup> <?php echo _l('Required field'); ?></span>
    </div>
</form>
</body>
</html>
