<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_product = (int) Tools::getValue('id_product', 0);
$id_product_attribute = (int) Tools::getValue('id_product_attribute', 0);
$id_warehouse = (int) Tools::getValue('id_warehouse', SCI::getSelectedWarehouse());

if (!empty($id_product_attribute) && empty($id_product))
{
    $pa = new Combination($id_product_attribute);
    $id_product = $pa->id_product;
}

?>
<?php if (empty($id_product) || empty($id_warehouse)) { ?>
<?php echo '<script type="text/javascript">'; ?>
wStockMvt.hide();
<?php echo '</script>'; ?>
<?php exit(); }
?>
<?php echo '<script type="text/javascript">'; ?>
dhxlStockMvt=wStockMvt.attachLayout("2E");
dhxlStockMvt_w = dhxlStockMvt.cells('a');
dhxlStockMvt_w.hideHeader();
dhxlStockMvt_w.setHeight(90);
dhxlStockMvt.cells('b').hideHeader();

dhxlStockMvt_w.attachURL("index.php?ajax=1&act=cat_win-stockmvt_choose_form&id_product=<?php echo $id_product; ?>&id_lang="+SC_ID_LANG);

stockmvtChooseAdd();

function stockmvtChooseAdd()
{
    $.get("index.php?ajax=1&act=cat_win-stockmvt_add_init&subform=1&id_product=<?php echo $id_product; ?>&id_product_attribute=<?php echo $id_product_attribute; ?>&id_warehouse=<?php echo $id_warehouse; ?>&id_lang="+SC_ID_LANG,function(data){
            $('#jsExecute').html(data);
        });
    wStockMvt.show();
}
function stockmvtChooseRemove()
{
    $.get("index.php?ajax=1&act=cat_win-stockmvt_delete_init&subform=1&id_product=<?php echo $id_product; ?>&id_product_attribute=<?php echo $id_product_attribute; ?>&id_warehouse=<?php echo $id_warehouse; ?>&id_lang="+SC_ID_LANG,function(data){
            $('#jsExecute').html(data);
        });
    wStockMvt.show();
}

<?php echo '</script>'; ?>