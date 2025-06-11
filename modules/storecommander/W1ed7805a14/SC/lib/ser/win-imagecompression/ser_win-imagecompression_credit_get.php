<?php
$id_project = (int) Tools::getValue('id_project');
if (empty($id_project))
{
    exit('empty id_project');
}
$url_params = array(
    'LICENSE' => '#',
    'URLCALLING' => '#',
    'KEY' => 'gt789zef132kiy789u13v498ve15nhry98',
    'SC_UNIQUE_ID' => SCI::getConfigurationValue('SC_UNIQUE_ID'),
);
if (defined('IS_SUBS') && IS_SUBS == '1')
{
    $url_params['SUBSCRIPTION'] = 1;
}

$credit_amount = $fizz_amount = 0;

$lot_credit = makeCallToOurApi('Compression/Credit/Config', array(), $url_params);
if (empty($lot_credit))
{
    exit('#IMGC_01 - '._l('Please contact our support'));
}
$lot_credit_label_for_js = array();
foreach ($lot_credit as $key => $row)
{
    $lot_credit_label_for_js[(int) $key] = _l('Do you really want to convert %s Fizz into %s image credits?', null, array($key, $row['label']));
}
$lot_credit_label_for_js = json_encode($lot_credit_label_for_js);

$project = makeCallToOurApi('Fizz/Project/Get/'.$id_project, array(), $url_params);
if (empty($project) || $project['code'] != 200)
{
    exit('#IMGC_02 - '._l('Please contact our support'));
}
$project_params = json_decode($project['project']['params'], true);
$credit_amount = (int) $project_params['allowed_image_amount'];

$fizz_amount = (float) getWallet();

##submit form
$confirmation_conversion_message = null;
$error = false;
if (array_key_exists('submitConversionCredit', $_POST) && Tools::getValue('submitConversionCredit') == 1)
{
    $nb_fizz_to_decrement = (int) Tools::getValue('lot');
    $_POST = null;
    $compress_params = array(
        'fizz_to_decrement' => $nb_fizz_to_decrement,
        'lot_credit' => $lot_credit,
        'url_params' => $url_params,
        'project_params' => $project_params,
    );
    $conversion_result = CompressionImg::convertFizzToCredit($id_project, $compress_params, $fizz_amount, $credit_amount);
    $error = (bool) $conversion_result['error'];
    $confirmation_conversion_message = (string) $conversion_result['message'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script>
        $(document).ready(function () {
            $('#conversion_form label').click(function () {
                let identifier = $(this).attr('for');
                let input_for_value = $('#conversion_form #' + identifier);
                input_for_value.attr('checked', 'checked');
                let choice = Number(input_for_value.val());
                let label_for_js = <?php echo $lot_credit_label_for_js; ?>;
                let confirmation = confirm(label_for_js[choice]);
                if (!confirmation) {
                    return false;
                }
                $('#conversion_form').submit();
            });

            $('#buy_fizz').click(function () {
                top.dhxMenu.callEvent('onClick', ['eservices']);
                top.weServices.setModal(1);
                return false;
            });
        });
    </script>
    <style>
        #credit_get_content {
            font-family: Arial,sans-serif;
            font-size: 14px;
            color: #444;
            padding: 0 5px
        }
        #credit_choice {
            display: flex;
            flex-direction: column;
            margin: 10px 0;
        }

        #conversion_form button {
            cursor: pointer;
        }

        /* radio -> button */
        #credit_choice input[type="radio"] {
            opacity: 0;
            position: fixed;
            width: 0;
        }

        #credit_choice label {
            background-color: #f5f5f5;
            padding: 5px 11px;
            border: 1px solid #dfdfdf;
            color: #404040;
            border-radius: 3px;
            cursor: pointer;
            align-self: flex-start;
            margin: 5px auto;
        }

        #credit_choice input[type="radio"]:checked + label,
        #credit_choice label:hover {
            background-color: #cfdbe0;
            border-color: #39c;
        }

        #msg {
            padding: 20px;
            margin-bottom: 10px;
        }

        #msg.warning {
            border: 1px solid #f5c6cb;
            background: #f8d7da;
        }

        #msg.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .center {
            text-align: center;
        }

        #buy_fizz {
            position: relative;
        }

        #buy_fizz:before {
            content: "";
            background: url('lib/img/fizz.png') no-repeat left center;
            display: block;
            height: 16px;
            width: 16px;
            position: absolute;
            left: -20px;
        }
    </style>
</head>
<body>
<div id="credit_get_content">
    <p>
        <b><?php echo _l('Number of image credits left')._l(':'); ?></b> <?php echo $credit_amount; ?>
        <br>
        <b><?php echo _l('Number of Fizz')._l(':'); ?></b> <?php echo $fizz_amount; ?><br>
    </p>
    <p class="center">
        <a href="#" id="buy_fizz"><?php echo _l('Buy Fizz'); ?></a> -
        <a href="<?php echo getScExternalLink('support_image_compression'); ?>" target="_blank"><?php echo _l('How it working?'); ?></a>
    </p>
    <?php if ($confirmation_conversion_message) { ?>
        <p id="msg" class="<?php echo $error ? 'warning' : 'success'; ?>"><?php echo $confirmation_conversion_message; ?></p>
    <?php }
else
{ ?>
        <form id="conversion_form" target="_self" method="post" class="center">
            <input type="hidden" name="submitConversionCredit" value="1"/>
            <label><?php echo _l('Convert')._l(':'); ?></label>
            <div id="credit_choice">
                <?php foreach ($lot_credit as $fizz_need => $credit_label) { ?>
                    <input type="radio" name="lot" value="<?php echo (int) $fizz_need; ?>" id="lot<?php echo $fizz_need; ?>"/>
                    <label class="center" for="lot<?php echo $fizz_need; ?>"><?php echo $fizz_need.' '._l('Fizz into %s image credits', null, array($credit_label['label'])); ?></label>
                <?php } ?>
            </div>
        </form>
    <?php } ?>
</div>
</body>
</html>