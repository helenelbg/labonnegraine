<?php

if (!defined('STORE_COMMANDER')) {
    exit;
}

$productId = Tools::getValue('product_id', null)
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
<div class="service html_content">
    <div class="message success">
        <?php echo ucfirst(_l("combinations successfully created")); ?> ! :
    </div>
    <ul class="actions">
        <li>
            <button class="btn secondary" id="create_combinations" onclick="wConvertProductToCombination.close()">
                <i class="fal fa-times"></i>
                <?php echo ucfirst(_l('close window')); ?>
            </button>
        </li>
    </ul>
</div>
</body>
<script>
    function gotoCombinations(id_product) {
        cat_grid.refresh();
        // id_product
        displayCombinations();
    }
</script>
</html>