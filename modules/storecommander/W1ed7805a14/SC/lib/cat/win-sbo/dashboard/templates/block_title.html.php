<?php
$sboTypes = $stats[$platform][$sboType];
$statusByPriority = ($sboTypes['awaiting'] != 0) ? 'awaiting' : '';
$statusByPriority = ($sboTypes['error'] != 0) ? 'error' : $statusByPriority;
?>
<h3 class="<?php echo $statusByPriority; ?>"><?php echo ucfirst(_l($sboType)); ?>
    <a href="#" class="sbo_open_view"
            data-preview="grid" data-sboType="<?php echo $sboType; ?>" data-platform="<?php echo $platform; ?>">
        <?php echo ucfirst(_l('view '.$sboType)); ?>
    </a>
</h3>