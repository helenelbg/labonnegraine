<?php if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$startSync = filter_var(Tools::getValue('sync', false), FILTER_VALIDATE_BOOLEAN);

$sc_agent = SC_Agent::getInstance();
$pdo = Db::getInstance()->getLink();
$stats = $shippingBoService->getStatsRepository()->getAll();

$lastCollectText = ucfirst(_l('first start'));
$lastSyncedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $shippingBoService->getConfigValue('lastSyncedAt'), new DateTimeZone(SCI::getConfigurationValue('PS_TIMEZONE')));

$localizedDateTime = '';
if ($lastSyncedAt)
{
    $lastCollectText = _l('Last update of the dashboard', 1).'&nbsp;: ';
    $localizedDateTime = $shippingBoService->getLocaleDate($lastSyncedAt, 'dd/MM/yyyy H:mm');
    $lastCollectText .= $localizedDateTime;
}

?>

<div class="<?php echo ($startSync) ? 'sync' : ''; ?>">

    <div class="service html_content sbo-dashboard <?php echo ($startSync) ? 'sync_in_progress' : ''; ?>">
        <div class="sync_infos">
            <h2><?php echo $lastCollectText; ?></h2>

            <div class="process" data-starttext="<?php echo _l('starting'); ?>...">
                <button class="refresh secondary"><?php echo _l('Refresh'); ?></button>
                <div class="progress">
                    <?php include SC_DIR.'shared/Process/templates/progressBarTemplate.html.php'; ?>
                    <span class="icon"></span>
                    <span class="stepName"></span>
                    <span class="text"></span>
                </div>
            </div>

        </div>


        <div class="stats">
            <div class="sbo">
                <h2><img src="lib/img/shippingbo/logo-shippingbo-gray-blue.svg" alt="<?php echo _l('Shippingbo'); ?>">
                </h2>
                <ul>
                    <li><?php echo ucfirst(_l('products')); ?>&nbsp;:
                        <strong data-placeholder="sbo.products.all"></strong>
                    </li>
                    <li><?php echo ucfirst(_l('batches')); ?>&nbsp;:
                        <strong data-placeholder="sbo.batches.all"></strong>
                    </li>
                    <li><?php echo ucfirst(_l('packs')); ?>&nbsp;:
                        <strong data-placeholder="sbo.packs.all"></strong>
                    </li>
                </ul>

            </div>
            <div class="matching">
                <h2><?php echo ucfirst(_l('matching')); ?></h2>
                <ul>
                    <li>
                    <span class="status" data-classplaceholder="score.products_status">
                        <?php echo _l('Products'); ?>&nbsp;: <strong data-placeholder="score.products">
                        </strong>
                    </span>
                    </li>
                    <li>
                    <span class="status" data-classplaceholder="score.batches_status">
                        <?php echo _l('Batches'); ?>&nbsp;: <strong data-placeholder="score.batches"></strong>
                    </span>
                    </li>
                    <li>
                    <span class="status" data-classplaceholder="score.packs_status">
                        <?php echo _l('Packs'); ?>&nbsp;: <strong data-placeholder="score.packs"></strong>
                    </span>
                    </li>
                </ul>
            </div>
            <div id="prestashop">
                <h2><img src="lib/img/ps-logo-black.svg" alt="<?php echo _l('Prestashop'); ?>"></h2>
                <ul>
                    <li><?php echo ucfirst(_l('products')); ?>&nbsp;:
                        <strong data-placeholder="ps.products.all"></strong>
                        <span class="help" role="tooltip">
                            <span class="help-content">
                                <?php echo _l('Some %s may be excluded from Shippingbo synchronisation.', null, [_l('products')]); ?>
                                <a href="#" class="secondary sbo_open_view" data-preview="grid" data-sboType="products"
                                   data-platform="sbo" data-tabId="locked">
                                    <?php echo _l('View list'); ?>
                                </a>
                            </span>
                        </span>
                    </li>
                    <li><?php echo ucfirst(_l('batches')); ?>&nbsp;:
                        <strong data-placeholder="ps.batches.all"></strong>
                        <span class="help" role="tooltip">
                            <span class="help-content">
                                <?php echo _l('Some %s may be excluded from Shippingbo synchronisation.', null, [_l('batches')]); ?>
                                <a href="#" class="secondary sbo_open_view" data-preview="grid" data-sboType="batches"
                                   data-platform="sbo" data-tabId="locked">
                                    <?php echo _l('View list'); ?>
                                </a>
                            </span>
                        </span></li>
                    <li><?php echo ucfirst(_l('packs')); ?>&nbsp;:
                        <strong data-placeholder="ps.packs.all"></strong>
                        <span class="help" role="tooltip">
                            <span class="help-content">
                                <?php echo _l('Some %s may be excluded from Shippingbo synchronisation.', null, [_l('packs')]); ?>
                                <a href="#" class="secondary sbo_open_view" data-preview="grid" data-sboType="packs"
                                   data-platform="sbo" data-tabId="locked">
                                    <?php echo _l('View list'); ?>
                                </a>
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>


        <div class="platforms">
            <?php $platform = 'sbo'; ?>
            <div class="platform <?php echo $platform; ?>">
                <div class="platform-header">
                    <h2><?php echo ucfirst(_l('prestashop')).' ⇒ '.ucfirst(_l('shippingbo')); ?></h2>
                    <p class="intro">
                        <?php echo _l('Update data by importing files into <a href="#" class="%s">Shippingbo</a>', 0, ['sbo_open']); ?>
                    </p>
                </div>

                <div class="details">
                    <div class="products">
                        <?php
                        $sboType = 'products';
include 'templates/block_title.html.php';
$status = 'error';
include 'templates/status_block.html.php';
$status = 'awaiting';
include 'templates/status_block.html.php';
?>
                    </div>
                    <div class="batches">
                        <?php
$sboType = 'batches';
include 'templates/block_title.html.php';
$status = 'error';
include 'templates/status_block.html.php';
$status = 'awaiting';
include 'templates/status_block.html.php';
?>
                    </div>
                    <div class="packs">
                        <?php
$sboType = 'packs';
include 'templates/block_title.html.php';
$status = 'error';
include 'templates/status_block.html.php';
$status = 'awaiting';
include 'templates/status_block.html.php';
?>
                    </div>
                </div>
            </div>

            <?php $platform = 'ps'; ?>
            <div class="platform <?php echo $platform; ?>">
                <div class="platform-header">
                    <h2><?php echo ucfirst(_l('shippingbo')).' ⇒ '.ucfirst(_l('prestashop')); ?></h2>
                    <p class="intro">
                        <button class="dhxform_btn dhxform_btn_txt single secondary sbo_import">
                            <i class="fal fa-file-import"></i>
                            <?php echo _l('Import into Prestashop'); ?>
                        </button>
                    </p>
                </div>
                <div class="details">
                    <div class="products ">
                        <?php
$sboType = 'products';
include 'templates/block_title.html.php';
$status = 'error';
include 'templates/status_block.html.php';
$status = 'awaiting';
include 'templates/status_block.html.php';
?>
                    </div>
                    <div class="batches">
                        <?php
$sboType = 'batches';
include 'templates/block_title.html.php';
$status = 'error';
include 'templates/status_block.html.php';
$status = 'awaiting';
include 'templates/status_block.html.php';
?>
                    </div>
                    <div class="packs">
                        <?php
$sboType = 'packs';
include 'templates/block_title.html.php';
$status = 'error';
include 'templates/status_block.html.php';
$status = 'awaiting';
include 'templates/status_block.html.php';
?>
                    </div>
                </div>
            </div>


        </div>

        <script>

            $('.service:not(.sync_in_progress) .sbo_import').on('click', function () {
                if($(this).closest('.service.syncing').length === 0){
                    parent.window.wSboPreviewOpenClick('import');
                }
            })
            $('.service:not(.sync_in_progress) .sbo_open').on('click', function () {
                window.open('<?php echo ShippingboService::LINK_PRODUCT_URL; ?>',)
            })

            $('.service .sbo_open_view').on('click', function (e) {
                if($(this).closest('.service.sync_in_progress').length === 0){
                    /*gestion classe active sur blocks */
                    document.querySelectorAll('.platform .details > div').forEach(action => {
                        action.classList.remove('active');
                    })
                    document.querySelector('.platform.' + this.dataset.platform + ' .details > div.' + this.dataset.sbotype).classList.add('active');

                    /* open preview */
                    parent.window.wSboPreviewOpenClick(this.dataset.preview, this.dataset.platform, this.dataset.sbotype, this.dataset.tabid);
                }
            })

            $('.service:not(.sync_in_progress) button.sbo_download').on('click', function () {
                parent.window.wSboDownloadClick(this.dataset.download);
            })


        </script>
    </div>
