<?php

$id_lang = (int) Tools::getValue('id_lang', 0);
$id_segment = (int) Tools::getValue('id_segment', 0);

$in_access = array();
$description = '';

if (!empty($id_segment))
{
    $segment = new ScSegment($id_segment);
    $description = $segment->description;
    $in_access = explode('-', $segment->access);
}
else
{
    exit('No segment');
}

$html_segment = SegmentHook::hookByIdSegment('segmentAutoConfig', $segment, array('id_lang' => $id_lang, 'values' => $segment->auto_params));
if (!empty($html_segment))
{
    $html_segment = '<form id="form_params">'.$html_segment.'</form>';
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
<div class="form">
    <div id="div_config" style="width: <?php if (!empty($html_segment))
{
    echo '48%;  padding-right: 2%; border-right: 1px solid #A4BED4';
}
else
{
    echo '99%';
} ?>; float:left;">
        <div style="width: 38%; float:left;">
            <strong><?php echo _l('Interface:'); ?></strong><br/>
            <select id="form_segment_access" multiple="multiple" style="width: 100%; height: 6em;">
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
        <div style="width: 60%; float: right;">
            <strong><?php echo _l('Description:'); ?></strong><br/>
            <textarea id="form_segment_description" wrap="off" style="width: 100%; height: 6em;"><?php echo $description; ?></textarea>
        </div>
        <div style="clear: both;"></div>
        <br/>
        <strong><?php echo _l('Segment type:'); ?></strong><br/>
        <select id="form_segment_auto_file" style="width: 100%;">
            <?php $type_segments = array();
            foreach (SegmentHook::$listFiles as $file)
            {
                if (file_exists(SC_SEGMENTS_DIR.$file))
                {
                    require_once SC_SEGMENTS_DIR.$file;
                    $class_name = str_replace('.php', '', $file);
                    $instance = new $class_name();
                    $type_segments[_l($instance->name)] = array('class_name' => $class_name, 'instance' => $instance);
                }
            }
            ksort($type_segments);
            foreach ($type_segments as $type_segment)
            {
                $class_name = $type_segment['class_name'];
                $instance = $type_segment['instance']; ?>
                <option value="<?php echo $class_name; ?>" <?php if ($segment->auto_file == $class_name)
                {
                    echo 'selected';
                } ?>><?php echo _l($instance->name); ?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <?php if (!empty($html_segment)) { ?>
    <div id="div_auto_file" style="width: 49%; float: right;">
        <?php $instance = new $segment->auto_file();
            echo '<strong style="font-size: 18px;">'._l($instance->name).'</strong><br/><br/>';

            echo $html_segment;
        ?>
    </div>
    <?php }
        else
        { ?>
    <div id="div_auto_file" style="width: 49%; float: right; display: none;"></div>
    <?php } ?>
</div>

<script>
function propertiesSave(){
    var description = $("#form_segment_description").val();
    
    var access = "-";
    $.each($("#form_segment_access option:selected"), function(num, element){
        var val = $(element).val();
        access = access + val + "-";
    });

    var use_filters = 0;
    if($("input[name=use_filters]:checked").val() === 'on') {
        use_filters = 1;
    }
    
    var auto_file = $("#form_segment_auto_file option:selected").val();

    var auto_params_values = $("#form_params").serializeArray();
    var auto_params_temp = {};
    var auto_params = new Array();
    $.each(auto_params_values, function (i, elem){
        if(auto_params_temp[elem.name]!=undefined)
            auto_params_temp[elem.name] = auto_params_temp[elem.name]+","+elem.value;
        else
            auto_params_temp[elem.name] = elem.value;
    });
    $.each(auto_params_temp, function (id, value){
        auto_params[auto_params.length] = {"name":id.replace("[]","") , "value":value};
    });

    if(auto_file!=undefined && auto_file!=null && auto_file!="")
    {
        $.post("index.php?ajax=1&act=all_win-segmentation_update&action=update_properties&id_segment=<?php echo $id_segment; ?>&id_lang=<?php echo $id_lang; ?>", {"access":access, "description":description, "auto_file":auto_file, "auto_params":auto_params, "use_filters":use_filters}, function(data){
            parent.displaySegment();
            parent.dhtmlx.message({text:'<?php echo _l('Properties have been updated.', 1); ?>',type:'succcess',expire:5000});
            $.post("index.php?ajax=1&act=all_win-segmentation_get_html",{id_segment:<?php echo $id_segment; ?>,id_lang:<?php echo $id_lang; ?>},function(data){
                if(data !== '' && data !== undefined) {
                    $('#div_auto_file').html(data).show();
                } else {
                    $('#div_auto_file').hide();
                    $('#div_config').attr('style','width:99%;');
                }
            });
        });
    }
}

$(document).ready(function(){

    $("#form_segment_auto_file").change(function(e){
        <?php if (empty($html_segment)) { ?>
        $("#div_config").css("width", "48%");
        $("#div_config").css("padding-right", "2%");
        $("#div_config").css("border-right", "1px solid #A4BED4");
        $("#div_auto_file").show();
        <?php } ?>
        $("#div_auto_file").html('<br/><br/><br/><center><strong><?php echo _l('To access the settings of this type of segment, please save!', 1); ?></strong></center>');


    });
});
</script>
</body>
</html>