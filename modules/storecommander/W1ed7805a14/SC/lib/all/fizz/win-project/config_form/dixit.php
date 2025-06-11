<?php

$params = (!empty($project['params']) ? json_decode($project['params'], true) : '');

$nb_pdt = 0;
if (!empty($project['list_items']) && $project['list_items'] != '-')
{
    $res = explode('-', trim($project['list_items'], '-'));
    $nb_pdt = count($res);
}

$possible_langs = array();
$headers = array();
$posts = array('iso' => Language::getIsoById((int) $id_lang));
$ret = sc_file_get_contents('http://api.storecommander.com/Dixit/getLangs/', 'POST', $posts, $headers);
if (!empty($ret))
{
    $ret = json_decode($ret, true);
    if (!empty($ret['code']) && $ret['code'] == '200')
    {
        $possible_langs = $ret['langs'];
    }
}

$valid_langs = array();
$langs = Language::getLanguages(false);
foreach ($langs as $lang)
{
    foreach ($possible_langs as $possible_lang)
    {
        if ($possible_lang['iso_code_2'] == $lang['iso_code'])
        {
            $valid_langs[$possible_lang['language_id']] = $possible_lang;
        }
    }
}

    $etape = 0;
    if (!empty($project))
    {
        if (in_array($project['status'], array('2', '1', '101')))
        {
            $etape = 1;
        }
        elseif (in_array($project['status'], array('3', '4', '103')))
        {
            $etape = 2;
        }
        elseif (in_array($project['status'], array('7', '8', '9', '10', '11', '12', '13', '107', '109', '110', '111', '112', '113')))
        {
            $etape = 3;
        }
    }
    ?>
    <div class="div_form steps actual_step_<?php echo $etape; ?>" style="width: 100%;">
        <div class="step step_1" style="width: 33%"><?php echo _l('Configure project'); ?></div>

        <div class="step step_2" style="width: 33%">
            <div class="arrow"></div>
            <?php echo _l('Request quote'); ?>
        </div>

        <div class="step step_3" style="width: 33%">
            <div class="arrow"></div>
            <?php echo _l('Start project'); ?>
        </div>
        <div style="clear: both"></div>
    </div>
    <br/>

    <?php if (in_array($project['status'], array('2', '3', '4', '7', '101', '103', '107', '113')))
    {
        if (in_array($project['status'], array('2', '3', '4', '101', '103'))) { ?>
            <button class="btn center big <?php echo $nb_pdt > 0 ? 'clickable' : 'lightgrey'; ?>" id="btn_tarif"><?php echo _l('Request quote'); ?></button>
            <br/><br/>
        <?php }
        elseif (in_array($project['status'], array('7'))) { ?>
            <center><strong><?php echo _l('Project cost:'); ?> <?php echo $project['amount']; ?> <img src="lib/img/fizz.png" alt="Fizz" title="Fizz" style="margin-bottom: -3px;" /></strong></center>
            <br/>
            <button class="btn center big <?php echo $project['amount'] > 0 ? 'clickable' : 'lightgrey'; ?>" id="btn_start"><?php echo _l('Use my Fizz & start translation'); ?></button>
            <br/><br/>
        <?php }
        elseif (in_array($project['status'], array('113'))) { ?>
            <button class="btn center big clickable" id="btn_restart"><?php echo _l('Re-start translation'); ?></button>
            <br/><br/>
        <?php }
        if (in_array($project['status'], array('2', '3', '4', '7', '101', '103'))) { ?>

        <div class="circle"><?php echo _l('Or'); ?></div>

        <span class="title"><?php echo _l('Continue to configure project'); ?></span>
        <?php } ?>
        <br/>
    <?php
    }
    elseif (in_array($project['status'], array('1'))) { ?>
        <span class="title"><?php echo _l('Configure project'); ?></span>

        <br/>
    <?php }
    elseif (in_array($project['status'], array('10', '111'))) { ?>
        <div class="message stripes" style="background-color: rgb(203, 227, 131);">
            <div>
                <img src="lib/img/fizz_tuto01.png" alt="" style="float: left; margin-right: 30px;height: 100px;" />
                <br style="line-height: 0.5em;" />
                <?php echo _l('Translation project in progress.'); ?><br/><?php echo _l('To know if it\'s finished, click on'); ?><br/><?php echo _l('Check if processed & import datas'); ?>
                <br style="clear:both;"/>
            </div>
        </div>

        <br/>
    <?php } ?>

    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Level'); ?>*
        </div>
        <select id="level">
            <option value="">-- <?php echo _l('Choose'); ?> --</option>
            <option value="normal" <?php if (!empty($params['level']) && $params['level'] == 'normal')
    {
        echo 'selected';
    } ?>><?php echo _l('Pro'); ?></option>
            <option value="advanced" <?php if (!empty($params['level']) && $params['level'] == 'advanced')
    {
        echo 'selected';
    } ?>><?php echo _l('Expert (with proofreading)'); ?></option>
        </select>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Source lang'); ?>*
        </div>
        <select id="lang_source">
            <option value="">-- <?php echo _l('Choose'); ?> --</option>
            <?php
            foreach ($valid_langs as $lang) { ?>
                <option value="<?php echo $lang['language_id'].'_'.$lang['iso_code_2']; ?>" <?php if (!empty($params['lang_source']) && $params['lang_source'] == $lang['language_id'].'_'.$lang['iso_code_2'])
            {
                echo 'selected';
            } ?>><?php echo $lang['content']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Translation lang'); ?>*
        </div>
        <select id="lang_translation">
            <option value="">-- <?php echo _l('Choose'); ?> --</option>
            <?php
            foreach ($valid_langs as $lang) { ?>
                <option value="<?php echo $lang['language_id'].'_'.$lang['iso_code_2']; ?>" <?php if (!empty($params['lang_translation']) && $params['lang_translation'] == $lang['language_id'].'_'.$lang['iso_code_2'])
            {
                echo 'selected';
            } ?>><?php echo $lang['content']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Fields to translate'); ?>*
        </div><br/>
        <?php
        $sources = array();
        if (!empty($params['source']))
        {
            $sources = explode(',', $params['source']);
        }
        foreach ($enabled_sources as $id => $name) { ?>
            <input type="checkbox" class="chk colspan fields_source" value="<?php echo $id; ?>" <?php if (in_array($id, $sources))
        {
            echo 'checked';
        } ?> style="" /> <?php echo $name; ?><br/>
        <?php } ?>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Instructions for translators'); ?>
        </div>
        <textarea id="comment"><?php echo !empty($params['comment']) ? $params['comment'] : ''; ?></textarea>
    </div>
    <br/>

    <div class="div_form" style="width: 800px;">
        <br/>
        <button style="float: right;" class="btn <?php echo in_array($project['status'], array('0', '1')) ? 'clickable' : (in_array($project['status'], array('2', '3', '4', '7')) ? 'darkgrey clickable' : 'lightgrey'); ?>" id="btn_save"><?php echo _l('Save'); ?></button>
    </div>

<script type="text/javascript">
    $("#btn_save.clickable").on( "click", function() {
        var errors = false;

        var val_level = $("#level").val();
        if(val_level==undefined || val_level==null || val_level=="" || val_level==0)
        {
            var msg = '<?php echo _l('You need to select a level.', 1); ?>';
            parent.dhtmlx.message({text:msg,type:"error",expire:10000});
            errors = true;
        }

        var val_lang_source = $("#lang_source").val();
        if(val_lang_source==undefined || val_lang_source==null || val_lang_source=="" || val_lang_source==0)
        {
            var msg = '<?php echo _l('You need to select a source lang.', 1); ?>';
            parent.dhtmlx.message({text:msg,type:"error",expire:10000});
            errors = true;
        }

        var val_lang_translation = $("#lang_translation").val();
        if(val_lang_translation==undefined || val_lang_translation==null || val_lang_translation=="" || val_lang_translation==0)
        {
            var msg = '<?php echo _l('You need to select a translation lang.', 1); ?>';
            parent.dhtmlx.message({text:msg,type:"error",expire:10000});
            errors = true;
        }

        var val_source = '';
        $( ".fields_source:checked" ).each(function( index ) {
            if(val_source!='')
                val_source += ",";
            val_source += $( this ).val();
        });
        if(val_source=="" || val_source==",")
        {
            var msg = '<?php echo _l('You need to enter source(s).', 1); ?>';
            parent.dhtmlx.message({text:msg,type:'error',expire:10000});
            errors = true;
        }

        var val_quality = $("#quality").val();
        if(val_quality==undefined || val_quality==null || val_quality=="" || val_quality==0)
            val_quality = 'good';

        var val_comment = $("#comment").val();

        if(!errors)
        {
            $.post('index.php?ajax=1&act=all_fizz_win-project_update',
                {
                    'id_project': "<?php echo $id_project; ?>",
                    'action': 'update',
                    'source':val_source,
                    'lang_source':val_lang_source,
                    'lang_translation':val_lang_translation,
                    'level':val_level,
                    'comment':val_comment
                },
                function(data){
                    var msg = '<?php echo _l('Project saved', 1); ?>';
                    parent.dhtmlx.message({text:msg,type:'success',expire:3000});
                    parent.displayProjects();
                });
        }
    });
</script>
<div class="clear"></div>

<script type="text/javascript">
    $("#btn_tarif.clickable").on( "click", function() {
        parent.setStatus('<?php echo $id_project; ?>', 'get_quote');
    });
    $("#btn_start.clickable").on( "click", function() {
        parent.setStatus('<?php echo $id_project; ?>', 'start');
    });
    $("#btn_restart.clickable").on( "click", function() {
        if(parent.eSP_started_processing!=undefined && parent.eSP_started_processing==false)
        {
            parent.eSP_started_id_project = "<?php echo $id_project; ?>";
            parent.eSP_started_startCalls();
        }
        else
        {
            var msg = '<?php echo _l('A project is already processing', 1); ?>';
            parent.dhtmlx.message({text:msg,expire:3000});
        }
    });
</script>