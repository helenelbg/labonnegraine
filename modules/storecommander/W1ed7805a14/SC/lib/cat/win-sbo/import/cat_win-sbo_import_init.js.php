<?php
if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$defaultDataImport = json_decode($shippingBoService->getConfigValue('defaultDataImport'));

// TODO 2 : afficher progression import
// TODO 2 : afficher resultat import
$lastCollectDate = $shippingBoService->getCollectProcess()->getStartDate();

$lastCollect = $lastCollectDate ? '('._l('last update', 1).' '.$shippingBoService->getLocaleDate($lastCollectDate, 'yyyy-MM-dd H:mm:ss').')' : false;

?>

<?php echo '<script>'; ?>
const wSboPanelImportLayout = window.parent.wSboPreview.attachLayout("1C");
/**
 * Shippingbo
 */
const wSboImportLayout_cell = wSboPanelImportLayout.cells('a');
wSboImportLayout_cell.setText("<?php echo _l('Import', 1); ?>");
wSboImportLayout_cell.cell.classList.add('service');


$.ajax({
    'url': 'index.php?ajax=1&act=cat_win-sbo_forms_import_form.json&process',
    'type': 'GET',
    'success': function (data) {

        wSboTabSyncLayout_form = wSboImportLayout_cell.attachForm(JSON.parse(data));
        wSboTabSyncLayout_form.attachEvent("onButtonClick", function (name, command) {
            if (name === "startImport") {
                wSboImportLayout_cell.progressOn();
                disableTabs();
                wSboTabSyncLayout_form.send("index.php?ajax=1&act=cat_win-sbo_process_import", "post", function (xml) {
                    // refresh active tab grid
                    wSboImportLayout_cell.progressOff();
                    let response = JSON.parse(xml.xmlDoc.response);
                    let type = response.state === true ? 'sc_success' : 'error';
                    wSboImportLayout_cell.attachHTMLString('<p class="message success"><?php echo _l('Success', 1); ?></p>');
                    enableTabs();
                    wSboTabClick(wSboTabBar.getActiveTab());
                    displayTree();
                });
            }
        });
    }
});

<?php echo '</script>'; ?>

