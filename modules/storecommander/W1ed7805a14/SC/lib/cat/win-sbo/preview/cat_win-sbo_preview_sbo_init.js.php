<?php
use Sc\Service\Shippingbo\ShippingboService;

if (!defined('STORE_COMMANDER'))
{
    exit;
}

$tabId = Tools::getValue('tabId', null);
$sboType = Tools::getValue('sboType', null);
$shippingBoService = ShippingboService::getInstance();
$labels = $shippingBoService->getStatusLabels();
$stats = $shippingBoService->getStatsRepository()->getAll();

$defaultTab = $tabId;
if (!$defaultTab or $defaultTab === 'undefined')
{
    $defaultTab = ($stats['sbo'][$sboType]['error'] > 0) ? 'error' : 'awaiting';
}

?>

<?php echo '<script>'; ?>
const stats = JSON.parse('<?php echo json_encode($stats); ?>');
const sboType = '<?php echo $sboType; ?>';
const resultsPerPage = '<?php echo $shippingBoService->getGridResultsPerPage(); ?>';
// ------------------------------------------------------------------------
// LAYOUT
// ------------------------------------------------------------------------
const wSboTabPreviewLayout = window.parent.wSboPreview.attachLayout("1C");
const wSboPanelPreviewSboData = wSboTabPreviewLayout.cells('a');
wSboPanelPreviewSboData.cell.classList.add('service', 'sbo_preview');



//if ($stats['sbo'][$sboType]['error'] > 0)

// TABBAR
var wSboPreviewTabConfig = {
    tabs: [

        {
            id: "error",
            text: '<span class="status status_error"><?php echo _l('%s '.$labels['error'], 1, [(int) $stats['sbo'][$sboType]['error']]); ?></span>',
            active: <?php echo $defaultTab === 'error' ? 'true' : 'false'; ?>,
            enabled: true,
            close: false
        },
        {
            id: "awaiting",
            text: '<span class="status status_<?php echo $stats['sbo'][$sboType]['awaiting'] === 0 ? 'success' : 'awaiting'; ?>"><?php echo _l('%s '.$labels['awaiting'], 1, [(int) $stats['sbo'][$sboType]['awaiting']]); ?></span>',
            active: <?php echo $defaultTab === 'awaiting' ? 'true' : 'false'; ?>,
            enabled: true,
            close: false
        },
        {
            id: "locked",
            text: '<span class="status status_locked"><?php echo _l('%s '.$labels['locked'], 1, [(int) $stats['sbo'][$sboType]['locked']]); ?></span>',
            active: <?php echo $defaultTab === 'locked' ? 'true' : 'false'; ?>,
            enabled: true,
            close: false
        }
    ]
};
wSboPanelPreviewSboData._tabbar = wSboPanelPreviewSboData.attachTabbar(wSboPreviewTabConfig);
wSboPanelPreviewSboData._refresh = function(){
    /* dashboard stats refresh */
    wSboTabClick(wSboTabBar.getActiveTab(), {"sync": false});
    /* preview */
    wSboPreviewOpenClick('grid', 'sbo','<?php echo $sboType; ?>',wSboPanelPreviewSboData._tabbar.getActiveTab());

}
wSboPanelPreviewSboData.setText('<span class="hdr_ps">PrestaShop</span> ⇒ <span class="hdr_sbo">Shippingbo</span> : <?php echo ucfirst(_l($sboType)); ?>');
wSboPanelPreviewSboData.showHeader(true);

// TOOLBAR
wSboPanelPreviewSboData._toolbar = wSboPanelPreviewSboData.attachToolbar();
wSboPanelPreviewSboData._toolbar.setIconset('awesome');
wSboPanelPreviewSboData._toolbar.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
wSboPanelPreviewSboData._toolbar.setItemToolTip('refresh', '<?php echo _l('Refresh grid'); ?>');
wSboPanelPreviewSboData._toolbar.addButton("select_all", 1000, "", "fa fa-bolt", "fa fa-bolt");
wSboPanelPreviewSboData._toolbar.setItemToolTip('select_all', '<?php echo _l('Refresh grid'); ?>');
wSboPanelPreviewSboData._toolbar.addButton("lock", 1000, "", "fad fa-lock-alt", "fad fa-lock-alt");
wSboPanelPreviewSboData._toolbar.setItemToolTip('lock', '<?php echo _l('Disable Shippingbo Synchronization for selection'); ?>');
wSboPanelPreviewSboData._toolbar.addButton("unlock", 1000, "", "fad fa-lock-open-alt", "fad fa-lock-open-alt");
wSboPanelPreviewSboData._toolbar.setItemToolTip('unlock', '<?php echo _l('Enable Shippingbo Synchronization for selection'); ?>');

// STATUSBAR
wSboPanelPreviewSboData._statusbar = wSboPanelPreviewSboData.attachStatusBar({
    text: "<div id='sboPagingArea'></div>",
    paging: true
});



// ------------------------------------------------------------------------
// EVENTS
// ------------------------------------------------------------------------


/* affichage grid en fonction de l'onglet */
wSboPanelPreviewSboData._tabbar._displayGrid = function (tabId) {
    const currentTabContent = wSboPanelPreviewSboData._tabbar.tabs(tabId);
    currentTabContent._grid = currentTabContent.attachGrid({
        image_path:'lib/js/imgs/',
        multiselect: true,
        smart_rendering: false,
        header_menu: false
    });
    currentTabContent._grid.enableExcelKeyMap(true);
    currentTabContent._grid.i18n.paging = {
        results: "<?php echo _l('Results'); ?> ",
        records: "<?php echo _l('Results'); ?> <?php echo strtolower(_l('From')); ?> ",
        to: " <?php echo _l('to'); ?> ",
        page: "<?php echo _l('Page'); ?> ",
        perpage: "<?php echo _l('rows per page'); ?>",
        first: "<?php echo _l('To first Page'); ?>",
        previous: "<?php echo _l('Previous Page'); ?>",
        found: "<?php echo _l('Found records'); ?>",
        next: "<?php echo _l('Next Page'); ?>",
        last: "<?php echo _l('To last Page'); ?>",
        of: " <?php echo _l('of'); ?> ",
        notfound: "<?php echo _l('No Records Found'); ?>"
    };
    currentTabContent._grid.enablePaging(true, resultsPerPage, null, "sboPagingArea");
    currentTabContent._grid.setPagingSkin("toolbar");
    //currentTabContent._grid.init();
    /* gestion edition cellule */
    currentTabContent._grid.attachEvent("onEditCell", function (stage,rId,cInd,nValue,oValue) {
        let idxIdProduct=this.getColIndexById('id_product');
        let idxIdProductAttribute=this.getColIndexById('id_product_attribute');
        let idxIsLocked=this.getColIndexById('is_locked');
        let idxRef=this.getColIndexById('reference');
        if (stage==2 && nValue !== oValue)
        {
            let action = '';
            if (cInd === idxIsLocked) {
                action = 'is_locked';
            }
            if (cInd === idxRef) {
                action = 'reference';
            }
            $.post("index.php?ajax=1&act=cat_win-sbo_common_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),
                {
                    'id_product': this.cells(rId,idxIdProduct).getValue(),
                    'id_product_attribute': this.cells(rId,idxIdProductAttribute).getValue(),
                    'value': nValue,
                    'action': action
                },
                function(response)
                {
                    if(response.state === false){
                        dhtmlx.message({
                            text: response.extra.message,
                            type: 'error',
                            expire: 7000
                        });
                    }
                    if(typeof wSboPanelPreviewSboData._refresh === 'function')
                        wSboPanelPreviewSboData._refresh();



                });

        }
        return true;
    });

    // CONTEXT MENU
    var wSboPanelPreviewSboContextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="<?php echo _l('Update:'); ?> <?php echo _l('Yes'); ?>" id="update_yes"/>'+
        '<item text="<?php echo _l('Update:'); ?> <?php echo _l('No'); ?>" id="update_no"/>'+
        '</menu>';
    var wSboPanelPreviewSboContextMenu = new dhtmlXMenuObject();
    wSboPanelPreviewSboContextMenu.renderAsContextMenu();
    wSboPanelPreviewSboContextMenu.setIconset('awesome');
    wSboPanelPreviewSboContextMenu.attachEvent("onClick", function(itemId){
        const currentGrid= currentTabContent._grid;
        var idxSyncSbo = currentGrid.getColIndexById('is_locked');
        let syncSbo = '1';
        if(itemId === 'update_yes'){
            syncSbo = '0';
        }
        for(let rowId of currentGrid.getSelectedRowId().split(',').values()){
            currentGrid.cells(rowId,idxSyncSbo).setValue(syncSbo);
            currentGrid.callEvent("onEditCell",[2,rowId,idxSyncSbo,syncSbo]);
        }

    });
    wSboPanelPreviewSboContextMenu.loadStruct(wSboPanelPreviewSboContextMenuXML);
    currentTabContent._grid.enableContextMenu(wSboPanelPreviewSboContextMenu);
    currentTabContent._grid.attachEvent('onBeforeContextMenu',function(zoneId,ev){
        var idxSyncSbo = currentTabContent._grid.getColIndexById('is_locked');
        if(idxSyncSbo != ev){
            return false;
        }
        return true;
    });

    // LOAD DATA
    let params = new URLSearchParams({
        ajax: 1,
        id_lang: SC_ID_LANG,
        tabId: tabId,
        sboType: sboType,
        totalCount: stats['sbo'][sboType][tabId]
    });
    currentTabContent._grid.load('index.php?act=cat_win-sbo_preview_sbo_get&'+params.toString());

    //ajaxPostCalling(currentTabContent, currentTabContent.dataObj, 'index.php?act=cat_win-sbo_preview_sbo_get', {
    //    ajax: 1,
    //    id_lang: SC_ID_LANG,
    //    tabId: tabId,
    //    sboType: sboType,
    //    totalCount: stats['sbo'][sboType][tabId]
    //}, function (data) {
    //    currentTabContent.dataObj.parse(data);
    //});
};

//TOOLBAR
/* actions toolbar */
wSboPanelPreviewSboData._toolbar.attachEvent("onClick", function (buttonId) {
    switch (buttonId) {
        case 'refresh':
            wSboPanelPreviewSboData._refresh();
            break;
        case 'select_all':
            wSboPanelPreviewSboData._tabbar.tabs(wSboPanelPreviewSboData._tabbar.getActiveTab())._grid.selectAll();
            break;
        case 'lock':
            $.each(wSboPanelPreviewSboData._tabbar.tabs(wSboPanelPreviewSboData._tabbar.getActiveTab())._grid.getSelectedRowId().split(','), function(num, pId){
                var vars = {"property":"is_locked","value": 1,"rowId":pId};
                addMissingSboProductsInQueue("", "update", "", vars);
            });
            break;
        case 'unlock':
            $.each(wSboPanelPreviewSboData._tabbar.tabs(wSboPanelPreviewSboData._tabbar.getActiveTab())._grid.getSelectedRowId().split(','), function(num, pId){
                var vars = {"property":"is_locked", "value": 0,"rowId":pId};
                addMissingSboProductsInQueue("", "update", "", vars);
            });
            break;
    }
});

//TABBAR
/* actions onglets */
wSboPanelPreviewSboData._tabbar.attachEvent("onTabClick", function (tabId) {
    wSboPanelPreviewSboData._tabbar._displayGrid(tabId);
    return false;
});


// ------------------------------------------------------------------------
// INIT
// ------------------------------------------------------------------------
wSboPanelPreviewSboData._tabbar._displayGrid(wSboPanelPreviewSboData._tabbar.getActiveTab());


// ------------------------------------------------------------------------
// FUNCTIONS
// ------------------------------------------------------------------------
function addMissingSboProductsInQueue(rId, action, cIn, vars)
{
    wSboPanelPreviewSboData._tabbar.tabs(wSboPanelPreviewSboData._tabbar.getActiveTab()).progressOn();
    var params = {
        name: "cat_win-sbo_common_update_queue",
        row: rId,
        action: "update",
        params: {},
        callback: "callbackSboMissingProducts('"+rId+"','update','"+rId+"');"
    };
    // COLUMN VALUES
    params.params["id_lang"] = SC_ID_LANG;
    if(vars!=undefined && vars!=null && vars!="" && vars!=0)
    {
        $.each(vars, function(key, value){
            params.params[key] = value;
        });
    }
    // USER DATA
    params.params = JSON.stringify(params.params);
    addInUpdateQueue(params,wSboPanelPreviewSboData._tabbar.tabs(wSboPanelPreviewSboData._tabbar.getActiveTab()).dataObj);
}
//
// CALLBACK FUNCTION
function callbackSboMissingProducts(sid,action)
{
    if (action=='update')
    {
        var tabContent = wSboPanelPreviewSboData._tabbar.tabs(wSboPanelPreviewSboData._tabbar.getActiveTab());
        tabContent.dataObj.setRowTextNormal(sid);
        if(updateQueue.length === 0){
            wSboPanelPreviewSboData._refresh();
        }

    }
}


<?php echo '</script>'; ?>