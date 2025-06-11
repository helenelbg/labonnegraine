<?php
    $submitUpdate = (int) Tools::getValue('submitUpdate');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="<?php echo SC_CSS_FONTAWESOME; ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('input[name="cgu_agreed"]').click(function(){
                let buttondisabled = $('#cgu_validator');
                if ($(this).is(':checked')) {
                    buttondisabled.removeAttr('disabled');
                } else {
                    buttondisabled.prop("disabled",true);
                }
            });

            $('form').on('submit', function() {
                $(this).find('input#cgu_validator').prop('disabled', true);
            });

            $('button#enable_autoupdate').click(function(){
                 $('#msg_autoupdate_status').addClass('loading');
                $.post('index.php?ajax=1&act=core_autoupdate',{action:$(this).data('action')},function(response){
                    let responseData = JSON.parse(response);
                    let msg = $('#msg_autoupdate_status');
                    if(responseData.state === 'error') {
                        msg.removeClass('loading enable disable').addClass('error');
                        return false;
                    }
                    msg.removeClass('loading error disable').addClass('enable');
                    $('button#enable_autoupdate').hide();
                    $('button#disable_autoupdate').show();
                });
               return false;
            });

            $('button#disable_autoupdate').click(function(){
                $('#msg_autoupdate_status').addClass('loading');
                $.post('index.php?ajax=1&act=core_autoupdate',{action:$(this).data('action')},function(response){
                    let responseData = JSON.parse(response);
                    let msg = $('#msg_autoupdate_status');
                    if(responseData.state === 'error') {
                        msg.removeClass('loading enable disable').addClass('error');
                        return false;
                    }
                    msg.removeClass('loading error enable').addClass('disable');
                    $('button#disable_autoupdate').hide();
                    $('button#enable_autoupdate').show();
                });
               return false;
            });
        });
    </script>
    <style>
        body{
            font-family:Arial,sans-serif;
            margin:5%;
        }
        h2{
            text-align: center;
        }
        h3{
            font-size: 1em;
        }
        .group_action{
            display: flex;
            flex-direction: column;
            row-gap: 13px;
        }
        .group_cgu,
        .group_auto_update{
            align-self: start;
        }

        .group_auto_update{
            width: 100%;
            position: relative;
            border: 1px solid #65717a;
        }
        .group_auto_update p{
            text-align: center;
            margin: 5px 5px 15px 5px;
        }
        .group_auto_update p.minus{
            font-size: 80%;
        }
        .group_auto_update p.error{
            background: red;
            color: #fff;
            padding: 5px 0;
            line-height: 2;
            margin: 10px auto;
            max-width: 80%;
        }
        .group_auto_update p.error a{
            color:#fff;
        }
        .group_auto_update p.error a:hover{
            text-decoration:none;
        }

        .group_auto_update::before{
            content: attr(data-badge);
            color: #fff;
            background-color: #156788;
            padding: 0.25em 0.8em 0.25em 0.5em;
            font-size: 83%;
            font-weight: 400;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            font-style: italic;
        }

        #cgu_detail{
            height: 300px;
            overflow-y: scroll;
            width: 100%;
            border:1px solid #65717a;
            display: block;
        }
        #cgu_validator[disabled],
        .action_autoupdate[disabled]{
            background-color: grey;
            color: #bbb7b7;
        }

        .action_autoupdate.success{
            background-color:#0ab169;
        }

        #cgu_validator,
        .action_autoupdate{
            font-family:Arial,sans-serif;
            background: #d70035;
            transition:all 0.2s ease-in-out;
            color: #fff;
            border: none;
            text-transform: uppercase;
            line-height: 26px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 3px;
            padding:5px 20px;
            align-self: end;
            position:relative;
            overflow: hidden;
            cursor: pointer;
        }

        .action_autoupdate:not(.success):not([disabled]):hover{
            background: #f32658;
        }

        #msg_autoupdate_status {
            font-weight: bold;
            font-style: italic;
            position:relative;
        }

        #msg_autoupdate_status:before {
            font-family: 'Font Awesome 5 Pro';
            display:inline-block;
            font-weight: 900;
            content:"\f110";
            font-style: normal;
            -webkit-animation: fa-spin 2s infinite linear;
            animation: fa-spin 2s infinite linear;
            opacity:0;
        }

        #msg_autoupdate_status.loading:before {
            opacity:100;
        }

        #msg_autoupdate_status:after {
            position: absolute;
            left:20px;
        }

        #msg_autoupdate_status.enable:after{
            content:attr(data-enable);
             color:#0ab169;
        }
        #msg_autoupdate_status.disable:after{
            content:attr(data-disable);
            color:#d70035;
        }
        #msg_autoupdate_status.error:after{
            padding:0 5px;
            content:attr(data-error);
            background:red;
            color:#fff;
        }

        #loading i{
            font-size:4em;
        }

        .last_confirmation{
            display: flex;
            justify-content: space-between;
        }
        .last_confirmation .minus{
            margin:0;
            align-self: center;
        }
    </style>
</head>
<body>

<h2><?php echo _l('Update', 1); ?><br/><img src="lib/img/logo.png"/></h2>
<?php if (empty($submitUpdate) || $submitUpdate == 0)
{
    $isAllowedAutoupdate = false;
    $scUniqueId = SCI::getConfigurationValue('SC_UNIQUE_ID');
    if (SC_DEMO)
    {
        $autoUpdateInfo = array('code' => 400);
    }
    else
    {
        $autoUpdateInfo = makeDefaultCallToOurApi('externhall/autoupdate/get', array('unique-id' => $scUniqueId));
        $isAllowedAutoupdate = ($autoUpdateInfo['code'] == 200 && !empty($autoUpdateInfo['result']));
    } ?>
<div id="cgu">
    <h3><?php echo _l('Please accept our Terms & Conditions to update Store Commander'); ?></h3>
    <iframe id="cgu_detail" src="<?php echo CGU_EXTERNAL_URL; ?>"></iframe>
    <br/>
    <form action="" method="post">
        <input type="hidden" name="submitUpdate" value="1"/>
        <div class="group_action">
            <div class="group_cgu">
                <input type="checkbox" name="cgu_agreed"/> <?php echo _l('I accept Terms & Conditions'); ?>
            </div>
            <div class="group_auto_update" data-badge="<?php echo _l('Highly recommended'); ?>">
                <?php if ($autoUpdateInfo['code'] != 200) { ?>
                    <?php if (SC_DEMO) { ?>
                        <p class="error"><?php echo _l('Update option not available on a demo'); ?></p>
                    <?php } else { ?>
                        <p class="error"><?php echo _l('An error occured%s <b>%s</b>.<br>Please <a href="mailto:%s">contact our support</a>.', null, array(_l(':'), $autoUpdateInfo['result'], 'support@storecommander.com')); ?></p>
                    <?php } ?>
                <?php } ?>
                <p><?php echo _l('The automatic update of %s is:', null, array('Store Commander')); ?> <span id="msg_autoupdate_status" class="<?php echo $isAllowedAutoupdate ? 'enable' : 'disable'; ?>" data-enable="<?php echo _l('Enabled'); ?>" data-disable="<?php echo _l('Disabled'); ?>" data-error="<?php echo _l('Error'); ?>"></span></p>
                <p>
                    <button id="enable_autoupdate" class="action_autoupdate" data-action="enable"<?php echo($isAllowedAutoupdate ? ' style="display: none;"' : '').(SC_DEMO ? ' disabled' : ''); ?>><?php echo _l('Enable'); ?></button>
                    <button id="disable_autoupdate" class="action_autoupdate" data-action="disable"<?php echo(!$isAllowedAutoupdate ? ' style="display: none;"' : '').(SC_DEMO ? ' disabled' : ''); ?>><?php echo _l('Disable'); ?></button>
                </p>
                <p class="minus"><i><?php echo _l('Updates will be performed overnight.'); ?> <a href="<?php echo getScExternalLink('core_update_autoupdate'); ?>" target="_blank"><?php echo _l('More informations'); ?></a></i></p>
            </div>
            <div class="last_confirmation">
                <p class="minus"><i><?php echo _l('Date of last update')._l(':'); ?> <?php echo SCI::getConfigurationValue('SC_LAST_UPDATE'); ?></a></i></p>
                <button id="cgu_validator" type="submit" onclick="$('#cgu').hide();$('#loading').fadeIn();" disabled><?php echo _l('Update now'); ?></button>
            </div>
        </div>
    </form>
</div>
<center id="loading" style="display:none"><br/><br/><i class="fas fa-spinner fa-spin"></i></center>
<?php
}
    else
    { ?>

<div id="register">
    <?php
    if (SCI::getConfigurationValue('SC_LICENSE_KEY', '') == '')
    {
        exit(_l('You have to register your license key in the [Help > Register your license] menu to update Store Commander.'));
    }

    doScUpdate($user_lang_iso);

    echo _l('Update finished!').' '.'<a href="index.php" target="_top">'._l('Click here to refresh the application').'</a>';
    ?>
</div>
<?php } ?>
</body>
</html>
