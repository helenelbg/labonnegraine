<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_product = (int) Tools::getValue('id_product');
$id_warehouse = (int) SCI::getSelectedWarehouse();
?>
<style>
    #stocks_from_button {
        display:flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-around;
    }
    #stocks_from_button a {
        text-align:center;

    }
</style>
<div id="stocks_from_button">
    <a href="javascript: void(0);" onclick="window.parent.stockmvtChooseAdd()">
        <img src="lib/img/add_big.png" width="50" height="auto" alt="<?php echo _l('Add to stock'); ?>" title="<?php echo _l('Add to stock'); ?>" /><br/>
        <?php echo _l('Add to stock'); ?>
    </a>
    <div  style="font-family: Tahoma;font-size: 11px !important;color: black;text-decoration: none;">
        <?php echo _l('OR'); ?>
    </div>
    <a href="javascript: void(0);" onclick="window.parent.stockmvtChooseRemove()">
        <img src="lib/img/delete_big.png" width="50" height="auto" alt="<?php echo _l('Remove stock'); ?>" title="<?php echo _l('Remove stock'); ?>" /><br/>
        <?php echo _l('Remove stock'); ?>
    </a>
</div>