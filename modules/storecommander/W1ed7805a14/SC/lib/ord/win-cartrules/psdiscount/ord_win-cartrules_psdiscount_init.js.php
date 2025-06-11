// INITIALISATION TOOLBAR
ord_wincartrules_prop_tb.addListOption('ord_prop_subproperties', 'ord_prop_psdiscount', 1, "button", '<?php echo _l('Edit the discount voucher', 1); ?>', "fad fa-tags");
ord_wincartrules_prop_tb.attachEvent("onClick", function(id){
    if(id=="ord_prop_psdiscount")
    {
        hideCartRulesSubpropertiesItems();
        ord_wincartrules_prop_tb.setItemText('ord_prop_subproperties', '<?php echo _l('Edit the discount voucher', 1); ?>');
        ord_wincartrules_prop_tb.setItemImage('ord_prop_subproperties', 'fad fa-tags');
        actual_wincartrules_subproperties = "ord_prop_psdiscount";
        initCartRulesPsEdit();
    }
});

hideCartRulesSubpropertiesItems();

// FUNCTIONS
cartrules_grid.attachEvent("onRowSelect",function (id_cart_rule)
{
    if (!dhxlCartRules_prop.isCollapsed())
    {
        if(actual_wincartrules_subproperties == "ord_prop_psdiscount") {
            lastCartRuleSelected = id_cart_rule;
            dhxlCartRules_prop.progressOn();
            loadCartRulePsForm();
        }
    }
});

function initCartRulesPsEdit()
{
    ord_prop_psdiscount = dhxlCartRules.cells('b').attachLayout("1C");

    // GRID
    ord_prop_psdiscount.cells('a').hideHeader();
    loadCartRulePsForm();
}

function loadCartRulePsForm()
{
    if(lastCartRuleSelected!=undefined && lastCartRuleSelected!=null && lastCartRuleSelected!="" && lastCartRuleSelected!=0 && dhxlCartRules.cells('b').isCollapsed()==false)
    {
        ord_prop_psdiscount.cells('a').attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?controller=AdminCartRules&id_cart_rule="+lastCartRuleSelected+"&updatecart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules'); ?>");
        setTimeout(function() {
            dhxlCartRules_prop.progressOff();
        }, 1000);
    }
}
