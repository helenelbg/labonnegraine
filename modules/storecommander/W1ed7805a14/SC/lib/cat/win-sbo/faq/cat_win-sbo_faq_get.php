<?php
if (!defined('STORE_COMMANDER')) {
    exit;
}

$langDir = Language::getIsoById(Tools::getValue('id_lang'));
$content = makeCallToOurApi('content/blog/', [], ['iso_code' => 'fr', 'page_name' => 'sbo_faq']);

// open links in new tab
$re = "/(<a\\b[^<>]*href=['\"]?http[^<>]+)>/is";
$subst = "$1 target=\"_blank\">";
$content = preg_replace($re, $subst, stripslashes($content['content']));
if($content === ''){
    die;
}
?>

<div class="html_content">
    <?php echo utf8_decode($content); ?>
</div>





