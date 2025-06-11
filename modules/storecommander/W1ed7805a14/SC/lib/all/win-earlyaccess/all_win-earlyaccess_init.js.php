<?php
if (!defined('STORE_COMMANDER')) { exit; }
echo '<script>'; ?>
wEarlyAccess._dimensions = wEarlyAccess.getDimension();
wEarlyAccess._formStructure = [
    {type: "settings", position: "label-top", inputWidth:wEarlyAccess._dimensions[0]-50},
    {
        type: "block",
        width: (wEarlyAccess._dimensions[0]-20),
        list: [
            {type: "input", name: 'email', label: 'Email', validate: "ValidEmail"},
            {type: "password", name: "pwd", label: "Password"},
            {type: "button", name: "submitAccess", value: "<?php echo _l('Connect', true); ?>"},
            {type: "button", name: "link", value: "<?php echo _l('Click here to reload %s', true, ['Store Commander']); ?>"}
        ]
    }
];
wEarlyAccess._form = wEarlyAccess.attachForm(wEarlyAccess._formStructure);
wEarlyAccess._form.hideItem('link');

wEarlyAccess._form.attachEvent("onButtonClick", function (name) {
    switch (name) {
        case 'link':
            location.reload();
            break;
        case 'submitAccess':
            if(!this.validate())
            {
                dhtmlx.message({text: 'Invalid data', type: 'error', expire: 5000});
                break;
            }

            wEarlyAccess.progressOn();
            $.post('index.php?ajax=1&act=all_win-earlyaccess_update',
            {
                '<?php echo generateToken(date('YmdH')); ?>' : 'token',
                email: this.getItemValue('email'),
                pwd: this.getItemValue('pwd')
            },
            function (response) {
                wEarlyAccess.progressOff();
                let responseData = JSON.parse(response);
                dhtmlx.message({text: responseData['message'], type: responseData['status'], expire: 10000});

                if(responseData['status'] === 'success')
                {
                    wEarlyAccess._form.hideItem('email');
                    wEarlyAccess._form.hideItem('pwd');
                    wEarlyAccess._form.hideItem('submitAccess');
                    wEarlyAccess._form.showItem('link');
                }
            });
            break;
    }
});
<?php echo '</script>'; ?>
