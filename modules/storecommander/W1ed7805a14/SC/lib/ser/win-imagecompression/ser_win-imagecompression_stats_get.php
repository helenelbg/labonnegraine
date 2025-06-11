<?php
require_once dirname(__FILE__).'/ser_win-imagecompression_tools.php';
$stats = array(
    'last_scan' => '-',
    'total_count' => 0,
    'total_count_compressed' => 0,
    'total_percentage_compressed' => 0,
    'total_size' => 0,
    'total_size_compressed' => 0,
    'total_after_compression' => 0,
    'total_size_gain' => 0,
    'total_real_size_gain' => 0,
    'total_percentage_gain' => 0,
    'total_count_obsolete' => 0,
    'total_size_obsolete' => 0,
);
$last_stats = json_decode(SCI::getConfigurationValue('SC_IMAGECOMPRESSION_STATS'), true);
if (!empty($last_stats))
{
    foreach ($last_stats as $key => $stat)
    {
        if (array_key_exists($key, $stats))
        {
            $stats[$key] = $stat;
        }
    }
}
$iso_lang = Language::getIsoById($sc_agent->id_lang);
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        #stats_get_content {
            font-family:Arial,sans-serif;
            font-size:14px;
            color:#444;
            padding:0 5px
        }
    </style>
</head>
<body>
    <p id="stats_get_content">
        <b><?php echo _l('Last analyse date:'); ?></b> <?php echo !empty($stats['last_scan']) ? $stats['last_scan'] : '-'; ?><br><br>
        <b><?php echo _l('Number of images to compress:'); ?></b> <?php echo (int) $stats['total_count']; ?><br>
        <b><?php echo _l('Number of compressed images:'); ?></b> <?php echo (int) $stats['total_count_compressed']; ?><br>
        <b><?php echo _l('Percentage of compressed images:'); ?></b> <?php echo (float) $stats['total_percentage_compressed']; ?>%<br><br>

        <b><?php echo _l('Number of obsolete images:'); ?></b> <?php echo (int) $stats['total_count_obsolete']; ?><br>
        <b><?php echo _l('Obsolete image total size:'); ?></b> <?php echo sizeFormat($stats['total_size_obsolete'], 2); ?><br><br>

        <b><?php echo _l('Total size before compression:'); ?></b> <?php echo sizeFormat($stats['total_size'], 2); ?><br>
        <b><?php echo _l('Total size after compression:'); ?></b> <?php echo sizeFormat($stats['total_after_compression'], 2); ?><br>
        <b><?php echo _l('Reduced total size:'); ?></b> <?php echo sizeFormat($stats['total_size_gain'], 2); ?> - <?php echo (float) $stats['total_percentage_gain']; ?>%<br><br>

        <b><?php echo _l('Gain on real compressed images:'); ?></b> <?php echo (float) $stats['total_real_size_gain']; ?>%<br><br>
        <?php echo _l('Note: obsolete images found during the analysis will be deleted as they are found.'); ?> - <a href="<?php echo getScExternalLink('support_image_compression'); ?>" target="_blank"><?php echo _l('More informations'); ?></a><br><br>
        
        <?php echo _l('Statistics are updated every Tuesday or by launching file analysis (step 1).'); ?>
    </p>
</body>
</html>