<?php
use Sc\Service\Shippingbo\ShippingboService;

if (!defined('STORE_COMMANDER'))
{
    exit;
}

$sboType = Tools::getValue('sboType', null);
$shippingBoService = ShippingboService::getInstance();
$labels = $shippingBoService->getStatusLabels();
$stats = $shippingBoService->getStatsRepository()->getAll();
?>
?>

<?php echo '<script>'; ?>
const stats = JSON.parse('<?php echo json_encode($stats); ?>');
const sboType = '<?php echo $sboType; ?>';
const resultsPerPage = '<?php echo $shippingBoService->getGridResultsPerPage(); ?>';

// ------------------------------------------------------------------------
// LAYOUT
// ------------------------------------------------------------------------
const wSboTabPreviewLayout = window.parent.wSboPreview.attachLayout("1C");
const wSboPanelPreviewPsData = wSboTabPreviewLayout.cells('a');
wSboPanelPreviewPsData.cell.classList.add('service', 'sbo_preview');


// TABBAR
var wSboPreviewTabConfig = {
    tabs: [
        {
            id: "error",
            text: '<span class="status status_error"><?php echo _l('%s '.$labels['error'], 1, [(int) $stats['ps'][$sboType]['error']]); ?></span>',
            active: <?php echo ($stats['ps'][$sboType]['error'] > 0) ? 'true' : 'false'; ?>,
            enabled: true,
            close: false
        },
        {
            id: "awaiting",
            text: '<span class="status status_<?php echo $stats['ps'][$sboType]['awaiting'] === 0 ? 'success' : 'awaiting'; ?>"><?php echo _l('%s '.$labels['awaiting'], 1, [(int) $stats['ps'][$sboType]['awaiting']]); ?></span>',
            active:  <?php echo ($stats['ps'][$sboType]['error'] > 0) ? 'false' : 'true'; ?>,
            enabled: true,
            close: false
        }
    ]
};
wSboPanelPreviewPsData._tabbar = wSboPanelPreviewPsData.attachTabbar(wSboPreviewTabConfig);
wSboPanelPreviewPsData._refresh = function(){
    wSboTabClick(wSboTabBar.getActiveTab(), {"sync": false});
    wSboPreviewOpenClick('grid', 'ps','<?php echo $sboType; ?>',wSboPanelPreviewPsData._tabbar.getActiveTab());
}
wSboPanelPreviewPsData.setText('<span class="hdr_sbo">Shippingbo</span> ⇒ <span class="hdr_ps">PrestaShop</span> : <?php echo ucfirst(_l($sboType)); ?>');
wSboPanelPreviewPsData.showHeader(true);

// TOOLBAR
wSboPanelPreviewPsData._toolbar = wSboPanelPreviewPsData.attachToolbar();
wSboPanelPreviewPsData._toolbar.setIconset('awesome');
wSboPanelPreviewPsData._toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
wSboPanelPreviewPsData._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid'); ?>');
wSboPanelPreviewPsData._toolbar.addButton("sbo_product_link", 1000, "", "fas fa-external-link", "fas fa-external-link");
wSboPanelPreviewPsData._toolbar.setItemToolTip('sbo_product_link','<?php echo _l('Open selected product in Shippingbo', 1); ?>');


// STATUSBAR
wSboPanelPreviewPsData._statusbar = wSboPanelPreviewPsData.attachStatusBar({
    text: "<div id='sboPagingArea'></div>",
    paging: true
});



// ------------------------------------------------------------------------
// EVENTS
// ------------------------------------------------------------------------


/* affichage grid en fonction de l'onglet */
wSboPanelPreviewPsData._tabbar._displayGrid = function (tabId) {
    let loadUrl = 'index.php?ajax=1&act=cat_win-sbo_preview_ps_get';
    if(wSboPanelPreviewPsData._tabbar.tabs(tabId).dataObj === undefined){
        wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid = wSboPanelPreviewPsData._tabbar.tabs(tabId).attachGrid({
            image_path:'lib/js/imgs/',
            multiselect: true,
            smart_rendering: false,
            header_menu: true
        });
        wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid.enableExcelKeyMap(true);
        wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid.i18n.paging = {
            results: "<?php echo _l('Results'); ?> ",
            records: "<?php echo _l('Results'); ?> <?php echo strtolower(_l('From')); ?> ",
            to: " <?php echo _l('to'); ?> ",
            page: "<?php echo _l('Page'); ?> ",
            perpage: "<?php echo _l('rows per page'); ?>",
            first: "<?php echo _l('First Page'); ?>",
            previous: "<?php echo _l('Previous Page'); ?>",
            found: "<?php echo _l('Found records'); ?>",
            next: "<?php echo _l('Next Page'); ?>",
            last: "<?php echo _l('Last Page'); ?>",
            of: " <?php echo _l('of'); ?> ",
            notfound: "<?php echo _l('No Records Found'); ?>"
        };
        wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid.enablePaging(true, resultsPerPage, null, "sboPagingArea");
        wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid.setPagingSkin("toolbar");

        /* activation/desactivation lient vers SBO dans TOOLBAR */
        wSboPanelPreviewPsData._tabbar.tabs(tabId).dataObj.attachEvent("onRowSelect",function(id){
            if(wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid.getSelectedRowId() !== null){
                wSboPanelPreviewPsData._toolbar.enableItem('sbo_product_link');
            } else {
                wSboPanelPreviewPsData._toolbar.disableItem('sbo_product_link');
            }
        });


    }
    ajaxPostCalling(wSboPanelPreviewPsData._tabbar.tabs(tabId), wSboPanelPreviewPsData._tabbar.tabs(tabId)._grid, loadUrl, {
        ajax: 1,
        id_lang: SC_ID_LANG,
        sboType: sboType,
        tabId: tabId,
        totalCount: stats['ps'][sboType][tabId]
    }, function (data) {
        wSboPanelPreviewPsData._tabbar.tabs(tabId).dataObj.parse(data);
    });
};

//TOOLBAR
/* actions toolbar */
wSboPanelPreviewPsData._toolbar.attachEvent("onClick", function (buttonId) {
    switch (buttonId) {
        case 'refresh':
            wSboPanelPreviewPsData._tabbar._displayGrid(wSboPanelPreviewPsData._tabbar.getActiveTab());
            break;
        case 'sbo_product_link':
            let sboLinkPattern = '<?php echo ShippingboService::LINK_PRODUCT_URL_PATTERN; ?>';
            var grid = wSboPanelPreviewPsData._tabbar.tabs(wSboPanelPreviewPsData._tabbar.getActiveTab())._grid;
            let sboLink = sboLinkPattern.replace('{sbo_id_product}', grid.getUserData(grid.getSelectedRowId(), "id_sbo"));
            window.open(sboLink);
            break;
    }
});

//TABBAR
/* actions onglets */
wSboPanelPreviewPsData._tabbar.attachEvent("onTabClick", function (tabId) {
    wSboPanelPreviewPsData._tabbar._displayGrid(tabId);
    return false;
});

// ------------------------------------------------------------------------
// INIT
// ------------------------------------------------------------------------
wSboPanelPreviewPsData._tabbar._displayGrid(wSboPanelPreviewPsData._tabbar.getActiveTab());



<?php echo '</script>'; ?>