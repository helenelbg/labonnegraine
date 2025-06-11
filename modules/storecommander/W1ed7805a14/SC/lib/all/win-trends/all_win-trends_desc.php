<?php
$html = sc_file_get_contents('http://api.storecommander.com/Trends/getProjectDesc/'.$user_lang_iso.'/');
?>
<body style="padding: 10px; margin: 0px;font-family:Arial,sans-serif">
<?php
if (!empty($html))
{
    echo $html;
}
?>
</body>
