<?php
$id_customer = Tools::getValue('id_customer');
$from_cus_prop = Tools::getValue('from_cus_prop', null);
if ($id_customer)
{
    $customer = new Customer((int) $id_customer);
    $countries = Country::getCountries($sc_agent->id_lang);
    $tmp = array();
    foreach ($countries as $country)
    {
        $tmp[] = array(
            'text' => $country['name'],
            'value' => $country['id_country'],
        );
    }
    $countries = json_encode($tmp);
}
else
{
    echo _l('Empty id_customer');
    exit;
}
?>
<script type="text/javascript">
    let address_form_params = [
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
            name: "none",
            label: "<?php echo _l('Current customer'); ?>",
            value: "<?php echo $customer->firstname.' '.$customer->lastname; ?>",
            disabled: true
        },
        {
            type: "input",
            name: "alias",
            label: "<?php echo _l('Alias'); ?>",
            tooltip: "<?php echo _l('Invalid characters:'); ?> <>,;?=+()@#",
            required: true
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
            name: "company",
            label: "<?php echo _l('Company'); ?>",
            tooltip: "<?php echo _l('Invalid characters:'); ?> 0-9!<>,;?=+()@#",
        },
        {
            type: "input",
            name: "vat",
            label: "<?php echo _l('VAT Number'); ?>",
        },
        {
            type: "input",
            name: "address1",
            label: "<?php echo _l('Address'); ?>",
            required: true
        },
        {
            type: "input",
            name: "address2",
            label: "<?php echo _l('Address(2)'); ?>",
        },
        {
            type: "input",
            name: "postcode",
            label: "<?php echo _l('Postcode'); ?>",
            required: true
        },
        {
            type: "input",
            name: "city",
            label: "<?php echo _l('City'); ?>",
            required: true
        },
        {
            type: "select",
            options:<?php echo $countries; ?>,
            name: "id_country",
            label: "<?php echo _l('Country'); ?>",
            required: true
        },
        {
            type: "input",
            name: "phone",
            label: "<?php echo _l('Phone'); ?>",
        },
        {
            type: "input",
            rows: 3,
            name: "other",
            label: "<?php echo _l('Other'); ?>",
            tooltip: "<?php echo _l('Invalid characters:'); ?> 0-9!<>,;?=+()@#",
        },
        {
            type: "button",
            width: 315,
            name: "submit",
            value: "<?php echo '<i class=\"fa fa-plus-circle green\"></i> '._l('Create address'); ?>"
        }
    ];
    let create_address_form = wCreateNewAddress.attachForm(address_form_params);
    create_address_form.enableLiveValidation(true);
    create_address_form.attachEvent("onButtonClick", function (id) {
        let valid_form = create_address_form.validate();
        if (valid_form) {
            if (id === 'submit') {
                this.send("index.php?ajax=1&act=ord_win-makeorder_address_update&action=add&id_customer=<?php echo $id_customer; ?>", "post", function (loader,response) {
                    if (response.includes('ERR:')) {
                        let msg = response.replace('ERR:', '');
                        dhtmlx.message({text: msg, type: 'error', expire: 5000});
                    } else {
                        dhtmlx.message({text: '<?php echo _l('Address added'); ?>', type: 'success', expire: 5000});
                        wCreateNewAddress.close();
                        <?php if ($from_cus_prop) { ?>
                        displayCustomerAddresses();
                        <?php }
else
{ ?>
                        displayMOaddresses();
                        <?php } ?>
                    }
                });
            }
        }
    });
</script>