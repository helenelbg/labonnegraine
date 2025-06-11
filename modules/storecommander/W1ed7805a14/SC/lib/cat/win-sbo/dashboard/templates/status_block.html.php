<?php
$labels = $shippingBoService->getStatusLabels();

if ($stats[$platform][$sboType][$status] != 0) {?>
<ul class="actions">
    <li class="name">
        <span class="status status_<?php echo $status; ?>"><?php echo $stats[$platform][$sboType][$status].' '._l('%s '.$labels[$status], null, [_l($sboType)]); ?></span>
    </li>
    <?php if ($status === 'awaiting' && $platform === 'sbo') { ?>
        <li class="download">
            <button class="dhxform_btn dhxform_btn_txt single secondary sbo_download"
                    data-download="<?php echo $sboType; ?>">
                <i class="fal fa-download"></i>
                <?php echo ucfirst(_l('download '.$sboType)); ?>
            </button>
            <?php if ($sboType === 'packs' && $platform === 'sbo') { ?>
                <button class="dhxform_btn dhxform_btn_txt single secondary sbo_download"
                        data-download="pack_components">
                    <i class="fal fa-download"></i>
                    <?php echo ucfirst(_l('download pack components')); ?>
                </button>
            <?php } ?>
            <span><?php echo _l('Shippingbo import ready !'); ?></span>
        </li>
    <?php } ?>

</ul>
<?php } ?>