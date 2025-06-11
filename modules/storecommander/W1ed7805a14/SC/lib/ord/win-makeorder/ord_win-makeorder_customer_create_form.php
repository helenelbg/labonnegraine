<script type="text/javascript">
    let customer_form_params = [
        {
            type: "settings",
            offsetLeft: 20,
            position: "label-left",
            labelWidth: 120,
            inputWidth: 200,
            labelAlign: "left"
        },
        {
            type: "input",
            name: "firstname",
            label: "<?php echo _l('Firstname'); ?>",
            tooltip: "<?php echo _l('Invalid characters:'); ?> 0-9!<>,;?=+()@#",
            required: true
        },
        {
            type: "input",
            name: "lastname",
            label: "<?php echo _l('Lastname'); ?>",
            tooltip: "<?php echo _l('Invalid characters:'); ?> 0-9!<>,;?=+()@#",
            required: true
        },
        {
            type: "input",
            name: "email",
            label: "<?php echo _l('Email'); ?>",
            value: "",
            required: true
        },
        {
            type: "input",
            name: "passwd",
            label: "<?php echo _l('Password'); ?>",
            tooltip: "<?php echo _l('Password should be at least %s characters long.', null, array(Validate::PASSWORD_LENGTH)); ?>",
            required: true
        },
        {
            type: "input",
            name: "company",
            label: "<?php echo _l('Company'); ?>",
            hidden: <?php echo _s('CUS_USE_COMPANY_FIELDS') && SCI::getConfigurationValue('PS_B2B_ENABLE') ? 'false' : 'true'; ?>,
        },
        {
            type: "input",
            name: "siret",
            label: "<?php echo _l('SIRET'); ?>",
            hidden: <?php echo _s('CUS_USE_COMPANY_FIELDS') && SCI::getConfigurationValue('PS_B2B_ENABLE') ? 'false' : 'true'; ?>,
        },
        {
            type: "input",
            name: "ape",
            label: "<?php echo _l('APE'); ?>",
            hidden: <?php echo _s('CUS_USE_COMPANY_FIELDS') && SCI::getConfigurationValue('PS_B2B_ENABLE') ? 'false' : 'true'; ?>,
        },
        {
            type: "button",
            width: 315,
            name: "submit",
            value: "<?php echo '<i class=\"fa fa-plus-circle green\"></i> '._l('Create customer'); ?>"
        }
    ];
    let create_customer_form = wCreateNewCustomer.attachForm(customer_form_params);
    create_customer_form.enableLiveValidation(true);
    create_customer_form.attachEvent("onButtonClick", function (id) {
        let valid_form = create_customer_form.validate();
        if (valid_form) {
            if (id == 'submit') {
                $.post("index.php?ajax=1&act=ord_win-makeorder_customer_update&id_lang=" + SC_ID_LANG + "&" + new Date().getTime(), {
                    id_lang: SC_ID_LANG,
                    id_shop: makeOrder_shop,
                    customer_data: {
                        'firstname':create_customer_form.getItemValue('firstname'),
                        'lastname':create_customer_form.getItemValue('lastname'),
                        'email':create_customer_form.getItemValue('email'),
                        'passwd':create_customer_form.getItemValue('passwd'),
                        'company':create_customer_form.getItemValue('company'),
                        'siret':create_customer_form.getItemValue('siret'),
                        'ape':create_customer_form.getItemValue('ape'),
                    },
                }, function (data) {
                    if(data.includes('ERR:')) {
                        let msg = data.replace('ERR:','');
                        dhtmlx.message({text:msg,type:'error',expire:5000});
                    } else {
                        dhtmlx.message({text:'<?php echo _l('Customer added'); ?>',type:'success',expire:5000});
                        wCreateNewCustomer.close();
                        displayMOCustomers(data);
                    }
                });
            }
        }
    });
</script>