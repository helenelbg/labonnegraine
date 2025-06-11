<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportOrders_ACTIVE') || (int) SC_ExportOrders_ACTIVE !== 1)
{
    exit;
}

$filterId = (int) Tools::getValue(ExportOrderFilter::$definition['primary']);
$id_lang = (int) $sc_agent->id_lang;

$iso = Language::getIsoById($id_lang);
$link = new Link();
$exportOrderFilterObj = new ExportOrderFilter($filterId);
$exportOrderFilterFormObj = new ExportOrderFilterForm($id_lang);

$url_js_dir = 'lib/js/jquery/';
$ruleDescriptors = array();

if (version_compare(_PS_VERSION_, '1.7.6.0', '>='))
{
    $urlData = array(
        'ajax' => 1,
        'controller' => 'AdminProducts',
        'action' => 'productsList',
        'forceJson' => 1,
        'disableCombination' => 1,
        'limit' => 10,
        'token' => $sc_agent->getPSToken('AdminProducts')
    );
    $link_search = SC_PS_PATH_ADMIN_REL.'index.php?'.http_build_query($urlData);
}
else
{
    $link_search = SC_PS_PATH_ADMIN_REL.'ajax_products_list.php';
}

// has it a dynamic definition ?
$dynamicDefinition = ExportOrderTools::getFieldValue($exportOrderFilterObj, 'dynamic_definition');
if ($dynamicDefinition)
{
    $dynamicDefinition = urldecode($dynamicDefinition);
    // TODO : put this decomposition function in the ScCustomerList class (use also in ScCustomerList::analyzeRules
    $parts = explode('__', $dynamicDefinition);
    // load all rules in a array with informations : type (rule or operator), operator, rule_name, rule_descriptor
    // Useful because some rules must be treated grouped like order rules : c_o_order_number & c_o_order_sum for example
    foreach ($parts as $rule)
    {
        // is this an operator ?
        if ($rule == 'AND' || $rule == 'OR')
        {
            $ruleDescriptors[] = array('type' => 'operator', 'operator' => $rule);
        }
        else // or a rule ?
        {
            $subparts = explode('=>', $rule);
            $ruleName = $subparts[0];
            $ruleDescriptors[] = array('type' => 'rule', 'name' => $ruleName, 'descriptor' => explode('|', $subparts[1]));
        }
    }
    if ($exportOrderFilterObj->id)
    {
        ExportOrderTools::addLog('CList edit : id='.$exportOrderFilterObj->id.' / Dynamic def splitted into '.count($ruleDescriptors).' rule descriptors');
    }
}

$panelOrderStatesRules = $exportOrderFilterFormObj->displayFieldRule('o_order_state', array('text' => _l('Select status'), 'type' => 'order_state'), $ruleDescriptors);

$panelOrderGeneralRules = '';
foreach ($exportOrderFilterFormObj->orderRules as $ruleName => $ruleInfos)
{
    $panelOrderGeneralRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$panelOrderTotalRules = '';
foreach ($exportOrderFilterFormObj->orderTotalRules as $ruleName => $ruleInfos)
{
    $panelOrderTotalRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$panelOrderProductsRules = '';
foreach ($exportOrderFilterFormObj->orderProductRules as $ruleName => $ruleInfos)
{
    $panelOrderProductsRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$panelCustomerRules = '';
foreach ($exportOrderFilterFormObj->customerRules as $ruleName => $ruleInfos)
{
    $panelCustomerRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$panelDeliveryAddressRules = '';
foreach ($exportOrderFilterFormObj->addressDeliveryRules as $ruleName => $ruleInfos)
{
    $panelDeliveryAddressRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$panelInvoiceAddressRules = '';
foreach ($exportOrderFilterFormObj->addressInvoiceRules as $ruleName => $ruleInfos)
{
    $panelInvoiceAddressRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$panelCustomerGroupRules = '';
foreach ($exportOrderFilterFormObj->groupRules as $ruleName => $ruleInfos)
{
    $panelCustomerGroupRules .= $exportOrderFilterFormObj->displayFieldRule($ruleName, $ruleInfos, $ruleDescriptors);
}

$field_value_static_definition = htmlentities(ExportOrderTools::getFieldValue($exportOrderFilterObj, 'static_definition'), ENT_COMPAT, 'UTF-8');
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo SC_CSSDHTMLX; ?>" />
    <link rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>"/>
    <link rel="stylesheet" href="<?php echo SC_CSS_FONTAWESOME; ?>"/>
    <script src="<?php echo SC_JQUERY; ?>"></script>
    <script src="<?php echo SC_JQUERY_UI; ?>"></script>
    <script src="<?php echo SC_JSDHTMLX; ?>"></script>
    <script src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <style>
        body{
            font-family: Arial,sans-serif;
            font-size: 0.9em;
            width: auto;
            height: auto;
            margin: 8px;
        }

        #mainForm{
            padding-bottom: 20px;
        }

        #panelDynamic .panel{
            padding:0 0 5px 0;
            margin-bottom: 20px;
        }

        #panelDynamic .panel .title{
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

        table.tableRule tr{
            background:#efefef;
        }
        table.tableRule tr td
        {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        table.tableRule tr td.field_label{
            width: 250px;
        }


        table.tableRule tr td:first-child{
            padding-left: 7px;
        }


        #o_p_cat_order_pdt_category_category {
            border: 1px solid #000000;
            margin-top: 3px;
            padding: 2px;
            width: 100%;
        }
        #o_p_cat_order_pdt_category_category table tr,
        #o_p_cat_order_pdt_category_category table tr td,
        #o_p_cat_order_pdt_category_category table tr td:first-child{
            padding:0;
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

        #form_footer{
            display: flex;
            padding: 0 20px;
            justify-content: space-between;
        }

        #form_footer > span{
            color: red;
        }

        #form_footer > button[type=submit]{
            padding: 5px 10px;
        }
    </style>
    <script>
        const msg = []
        msg["root"] = "<?php echo _l('Invalid definition of filter for field'); ?>";

        msg["o_date_add"] = "<?php echo _l('Orders in period'); ?>";
        msg["o_invoice"] = "<?php echo _l('Orders invoice number'); ?>";
        msg["o_total_paid_real"] = "<?php echo _l('Orders total paid real'); ?>";
        msg["o_total_discount"] = "<?php echo _l('Orders total discount'); ?>";
        msg["o_total_paid"] = "<?php echo _l('Orders total paid'); ?>";
        msg["o_total_products"] = "<?php echo _l('Orders total products'); ?>";
        msg["o_total_products_wt"] = "<?php echo _l('Orders total products WT'); ?>";
        msg["o_total_shipping"] = "<?php echo _l('Orders total shipping'); ?>";

        msg["o_date_invoice"] = "<?php echo _l('Orders invoice date'); ?>";
        msg["o_date_delivery"] = "<?php echo _l('Orders delivery date'); ?>";
        msg["o_shipping_number"] = "<?php echo _l('Orders shipping number'); ?>";

        msg["c_first_name"] = "<?php echo _l('First name'); ?>";
        msg["c_last_name"] = "<?php echo _l('Last name'); ?>";
        msg["c_birthday_date"] = "<?php echo _l('Birthday date'); ?>";
        msg["c_email"] = "<?php echo _l('Email'); ?>";

        msg["a_d_company"] = "<?php echo _l('Delivery address : Company'); ?>";
        msg["a_d_first_name"] = "<?php echo _l('Delivery address : first name'); ?>";
        msg["a_d_last_name"] = "<?php echo _l('Delivery address : last name'); ?>";
        msg["a_d_address"] = "<?php echo _l('Delivery address : Address'); ?>";
        msg["a_d_postcode"] = "<?php echo _l('Delivery address : Postcode'); ?>";
        msg["a_d_city"] = "<?php echo _l('Delivery address : City'); ?>";
        msg["a_d_state"] = "<?php echo _l('Delivery address : State'); ?>";
        msg["a_d_country"] = "<?php echo _l('Delivery address : Country'); ?>";
        msg["a_d_vat_number"] = "<?php echo _l('Delivery address : customer VAT num.'); ?>";

        msg["a_i_company"] = "<?php echo _l('Invoice address : Company'); ?>";
        msg["a_i_first_name"] = "<?php echo _l('Invoice address : first name'); ?>";
        msg["a_i_last_name"] = "<?php echo _l('Invoice address : last name'); ?>";
        msg["a_i_address"] = "<?php echo _l('Invoice address : Address'); ?>";
        msg["a_i_postcode"] = "<?php echo _l('Invoice address : Postcode'); ?>";
        msg["a_i_city"] = "<?php echo _l('Invoice address : City'); ?>";
        msg["a_i_state"] = "<?php echo _l('Invoice address : State'); ?>";
        msg["a_i_country"] = "<?php echo _l('Invoice address : Country'); ?>";
        msg["a_i_vat_number"] = "<?php echo _l('Invoice address : customer VAT num.'); ?>";

        msg["cg_customerGroup"] = "<?php echo _l('Group'); ?>";

        msg["state_should_be_checked"] = "<?php echo _l('To apply any filter on orders, you should also select the order states to include.'); ?>";
        msg["o_order_state"] = "<?php echo _l('Order states selection'); ?>";
        msg["o_p_order_product"] = "<?php echo _l('Order products'); ?>";
        msg["o_p_cat_order_pdt_category"] = "<?php echo _l('Order product categories'); ?>";


        // to store client side information for product / category filters
        // Each element in these arrays are like : {id:id1, name:'name1'}
        var productList = [];
        var pdtCategoryList = [];
        // check for jquery version because of change between attr / prop at 1.6 version
        var jqueryBefore16 = jQuery(document).jquery < "1.6";

        /**
         * Create the final string that represents all rules defined by user
         */
        function createRuleDefinitionFromPanels() {
            var r = '';
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
                case "list":
                    return createRuleDefinitionForList(ruleName);
                case "group":
                    return createRuleDefinitionForGroup(ruleName);
                case "order_state":
                    return createRuleDefinitionForOrderState(ruleName);
                case "product":
                    return createRuleDefinitionForPdtOrCat(ruleName);
                case "pdt_category":
                    return createRuleDefinitionForPdtOrCat(ruleName);
            }
            return '';
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
            if ($('input[type="radio"][name*="' + ruleName + '_date"]:checked').val() == "norange") {
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

        function createRuleDefinitionForList(ruleName) {
            //c_gender=>NONE|=|1
            var r = ruleName + '=>';
            r += ($('#' + ruleName + '_reverse').is(":checked") ? "NOT" : "NONE") + '|';
            r += '=|';    // always =
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

            var text = "";
            if (ruleName == 'o_p_order_product') {
                $.each(productList, function (index, value) {
                    text += (index > 0 ? ',' : '') + value.id;
                });
            } else if (ruleName == 'o_p_cat_order_pdt_category') {
                $.each(pdtCategoryList, function (index, value) {
                    text += (index > 0 ? ',' : '') + value.id;
                });
            }
            // nothing selected, then return no rule
            if (text.length == 0)
                return '';

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

            // check if the state field is visible
            if (!$('#o_order_state_activate').is(":checked")) {
                alert(msg["state_should_be_checked"]);
                check = false;
                return false;
            }

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
                    case "product":
                        if (!checkRuleForProductOrCategory(ruleName)) {
                            check = false;
                            return false;
                        }
                        break;
                    case "pdt_category":
                        if (!checkRuleForProductOrCategory(ruleName)) {
                            check = false;
                            return false;
                        }
                        break;
                }
            });
            return check;
        }

        function checkRuleForProductOrCategory(ruleName) {
            // be sure a value is set
            //console.log("check Product field " + ruleName);
            if ($('#' + ruleName + '_spanlist').html().length == 0) {
                alert(msg["root"] + " " + msg[ruleName]);
                return false;
            }
            return true;
        }

        function checkRuleForString(ruleName) {
            // be sure a value is set
            //console.log("check STRING field " + ruleName);
            if ($('#' + ruleName + '_value').val().length == 0 && $('#' + ruleName + '_operator').val() != "equal") {
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
            if ($('input[type="radio"][name*="' + ruleName + '_date"]:checked').val() == "norange") {
                // be sure a value is set
                if ($('#' + ruleName + '_1_value').val().length == 0) {
                    alert(msg["root"] + " " + msg[ruleName]);
                    return false;
                }
            } else {    // range date
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
            var idToAdd = $('#scproduct_search_id_to_add').val();
            if (idToAdd == 0) {
                alert("<?php echo _l('No product to add'); ?>");
                return false;
            }
            var nameToAdd = $('#scproduct_search_name_to_add').val();
            // TODO : JSON encode the name
            nameToAdd = $('<div/>').text(nameToAdd).html();
            // append a new object to the global product filter array
            productList.push({id: idToAdd, name: nameToAdd});
            // empty the search and the toAdd fields
            $('#scproduct_search_query').val("");
            $('#scproduct_search_id_to_add').val(0);
            $('#scproduct_search_name_to_add').val("");
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

        // bind event on activate / disactivate checkboxes changes
        function bindCheckboxesClick() {
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
            // apply panel checkboxes consequences
            $('input[type="checkbox"][id*="_activate"]').each(function () {
                // the checkbox checked state
                var checked = $(this).is(":checked");
                // get the id radix (without the suffix _activate)
                var checkId = $(this).attr("id");
                var radix = checkId.substr(0, checkId.length - "_activate".length);
                // show / hide the definition panels
                if (checked)
                    $('div[id*="panel_' + radix + '"]').show();
                else
                    $('div[id*="panel_' + radix + '"]').hide();
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
            // apply internal Date panels choices consequences
            $('input[type="radio"][name*="_date"]').each(function () {
                var checked = $(this).is(":checked");
                // get the id radix (without the suffix _date)
                var checkName = $(this).attr("name");
                var checkId = $(this).attr("id");
                var radix = checkName.substr(0, checkName.length - "_date".length);
                var isDateRange = checkId.indexOf("date_2") > 0;
                $('[id*="' + radix + '_' + (isDateRange ? "2" : "1") + '"]').each(function () {
                    if (jqueryBefore16)
                        $(this).attr("disabled", checked ? null : "disabled");
                    else
                        $(this).prop("disabled", !checked);
                });
            });

            // fill in the product / category field according to current datas
            var pdtCategoryListValue = "";
            $.each(pdtCategoryList, function (index, value) {
                pdtCategoryListValue += (index > 0 ? ' <?php echo _l('OR'); ?> ' : '') + '&nbsp;<a href="javascript:removeCategory(' + value.id + ');" title="<?php echo _l('Click to remove this product category from rule'); ?>">' + value.name + '</a>&nbsp;';
            });
            $('#o_p_cat_order_pdt_category_spanlist').html(pdtCategoryListValue);
            // remove duplicate entries
            productList = removeDuplicates(productList);
            var productListValue = "";
            $.each(productList, function (index, value) {
                productListValue += (index > 0 ? ' <?php echo _l('OR'); ?> ' : '') + '&nbsp;<a href="javascript:removeProduct(' + value.id + ');" title="<?php echo _l('Click to remove this product from rule'); ?>">' + value.name + '</a>&nbsp;';
            });
            $('#o_p_order_product_spanlist').html(productListValue);
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
            // disable the order state checkbox (have to select at least one)
            $('#o_order_state_activate').attr('disabled', 'disabled');
            // bind click event to all checbox items
            bindCheckboxesClick();
            // refresh all panels states
            updatePanels();
            // bind the form submit event
            $("#scproduct_search_query").autocomplete({
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
                    $('#scproduct_search_query').val(ui.item.data.pname);
                    $('#scproduct_search_id_to_add').val(ui.item.data.id_product);
                    $('#scproduct_search_name_to_add').val(ui.item.data.pname);
                },
            });
        });

        function submitMainForm()
        {
            // check the right to submit
            if (!checkRules()) {
                return false;
            }
            // get the json
            let ruleDefinition = createRuleDefinitionFromPanels();
            let dd_input = $("#dynamic_definition");
            // put it into the hidden field for server submission
            // console.log(ruleDefinition);
            dd_input.val(ruleDefinition);

            $.post('index.php?ajax=1&act=ord_win-export_filter_update', {
                action: 'submit_filter_form',
                '<?php echo ExportOrderFilter::$definition['primary']; ?>': $('#<?php echo ExportOrderFilter::$definition['primary']; ?>').val(),
                dynamic_definition: dd_input.val(),
                static_definition: $("#static_definition").val()
            },function (response) {
                let dataResponse = JSON.parse(response);
                parent.dhtmlx.message({text: dataResponse.message, type: dataResponse.state, expire: 5000});
            });
            return false;
        }
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
            <input type="hidden" name="<?php echo ExportOrderFilter::$definition['primary']; ?>" id="<?php echo ExportOrderFilter::$definition['primary']; ?>" value="<?php echo $filterId; ?>"/>
            <input type="hidden" name="dynamic_definition" id="dynamic_definition" value=""/>
            <div id="panelStatic" style="display:none;">
                <label><?php echo _l('Static definition:'); ?> </label>
                <div class="margin-form">
                    <input type="text" size="33" name="static_definition" id="static_definition" value="<?php echo $field_value_static_definition; ?>"/> <sup>*</sup>
                </div>
            </div>

            <div id="panelDynamic">
                <div id="panelOrderStatesRules" class="panel">
                    <div class="title"><?php echo _l('You want to consider orders with a status of'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelOrderStatesRules; ?></table>
                </div>

                <div id="panelOrderGeneralRules" class="panel">
                    <div class="title"><?php echo _l('Filter on orders general properties'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelOrderGeneralRules; ?></table>
                </div>

                <div id="panelOrderTotalRules" class="panel">
                    <div class="title"><?php echo _l('Filter on orders payment properties'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelOrderTotalRules; ?></table>
                </div>

                <div id="panelOrderProductsRules" class="panel">
                    <div class="title"><?php echo _l('Filter on orders products'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelOrderProductsRules; ?></table>
                </div>

                <div id="panelCustomerRules" class="panel">
                    <div class="title"><?php echo _l('Filter on orders customers properties'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelCustomerRules; ?></table>
                </div>

                <div id="panelDeliveryAddressRules" class="panel">
                    <div class="title"><?php echo _l('Filter on orders delivery address properties'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelDeliveryAddressRules; ?></table>
                </div>

                <div id="panelInvoiceAddressRules" class="panel">
                    <div class="title"><?php echo _l('Filter on orders invoice address properties'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelInvoiceAddressRules; ?></table>
                </div>

                <div id="panelCustomerGroupRules" class="panel">
                    <div class="title"><?php echo _l('Filter on customers group properties'); ?></div>
                    <table class="tableRule order_state"><?php echo $panelCustomerGroupRules; ?></table>
                </div>

            </div>
            <div id="form_footer">
                <span><sup>*</sup> <?php echo _l('Required field'); ?></span>
            </div>
        </form>
</body>
</html>
