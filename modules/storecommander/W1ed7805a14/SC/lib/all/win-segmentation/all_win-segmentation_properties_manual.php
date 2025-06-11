<?php $id_lang = (int) Tools::getValue('id_lang', 0);
$id_segment = (int) Tools::getValue('id_segment', 0);

$in_access = array();
$description = '';

if (!empty($id_segment))
{
    $segment = new ScSegment($id_segment);
    $description = $segment->description;
    $in_access = explode('-', $segment->access);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Segmentation</title>

<script src="<?php echo SC_JQUERY; ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo SC_JSDHTMLX; ?>"></script>
<script type="text/javascript" src="lib/js/message.js"></script>

<style>
    body * {
        font-family: Arial,sans-serif;
        font-size: 13px !important;
    }
    body {
        color: #404040;
    }
    input, select, textarea {
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: none;
        color: #555;
        height: 26px;
        margin-top: 5px;
    }
    input[type=button] {
        background-color: #c8c8c8;
        color: #fff;
        cursor: pointer;
    }
    fieldset {
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: none;
        margin-top: 10px;
    }
</style>
</head>
<body>
<div style="width: 48%; float:left;">
    <strong><?php echo _l('Interface:'); ?></strong><br/>
    <select id="form_segment_access" multiple="multiple" style="width: 100%; height: 10em;">
        <option value="catalog" <?php if (in_array('catalog', $in_access))
{
    echo 'selected';
} ?>><?php echo _l('Catalog'); ?></option>
        <option value="orders" <?php if (in_array('orders', $in_access))
{
    echo 'selected';
} ?>><?php echo _l('Orders'); ?></option>
        <option value="customers" <?php if (in_array('customers', $in_access))
{
    echo 'selected';
} ?>><?php echo _l('Customers'); ?></option>
        <option value="customer_service" <?php if (in_array('customer_service', $in_access))
{
    echo 'selected';
} ?>><?php echo _l('Customer service'); ?></option>
    </select>

</div>
<div style="width: 50%; float: right;">
    <strong><?php echo _l('Description:'); ?></strong><br/>
    <textarea id="form_segment_description" style="width: 100%; height: 10em;"><?php echo $description; ?></textarea>
</div>

<script>
function propertiesSave(){
    var description = $("#form_segment_description").val();
    
    var access = "-";
    $.each($("#form_segment_access option:selected"), function(num, element){
        var val = $(element).val();
        access = access + val + "-";
    });
    
    $.post("index.php?ajax=1&act=all_win-segmentation_update&action=update_properties&id_segment=<?php echo $id_segment; ?>&id_lang=<?php echo $id_lang; ?>", {"access":access, "description":description}, function(data){
        parent.displaySegment();    

        parent.dhtmlx.message({text:'<?php echo _l('Properties have been updated.', 1); ?>',type:'succcess',expire:5000});
    });
}
</script>
</body>
</html>