<?php
$id_project = Tools::getValue('id_project');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Store Commander</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="lib/css/style.css">
    <style>
        body {
            line-height: 27px;
            font-weight: normal;
            font-family: Tahoma;
            font-size: 12px;
            color: #000000;
        }
    </style>
    <script>
        parent.col_eSP_config.progressOff();
    </script>
    <?php require_once dirname(__FILE__).'/config_form/_header.php'; ?>
</head>
<body>
<?php if (!empty($id_project))
{
    $type = Tools::getValue('type');
    $id_lang = Tools::getValue('id_lang');

    $project = null;
    $headers = array();
    $posts = array();
    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
    $posts['LICENSE'] = '#';
    $posts['URLCALLING'] = '#';
    if (defined('IS_SUBS') && IS_SUBS == '1')
    {
        $posts['SUBSCRIPTION'] = '1';
    }
    $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
    if (!empty($ret['code']) && $ret['code'] == '200')
    {
        $project = $ret['project'];
    }

    if (!empty($project['status']))
    {
        if (file_exists(dirname(__FILE__).'/config_form/'.$type.'.php'))
        {
            require dirname(__FILE__).'/config_form/'.$type.'.php';
        }
    }
    else
    {
        require dirname(__FILE__).'/config_form/_configuring.php';
    }
}
else
{ ?>
    <br/><br/><br/>
<center><strong><?php echo _l('Select a project to configure it.'); ?></strong></center>
<?php } ?>
</body>
</html>