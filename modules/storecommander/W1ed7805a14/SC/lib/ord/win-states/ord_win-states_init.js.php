<?php

if (_r('MENU_ORD_MANAGE_STATUSES'))
{
    echo '<script type="text/javascript">';
    $idShop = SCI::getSelectedShop() === null ? $sc_agent->getDefaultShopID() : SCI::getSelectedShop();
    $shop = Shop::getShop($idShop);
    $shopName = $idShop === 0 ? _l('All shops') : $shop['name']; ?>

dhxlOrderStates = wOrderStates.attachLayout("1C");

dhxlOrderStates.cells('a').hideHeader();
orderstates_grid = dhxlOrderStates.cells('a').attachGrid();
orderstates_tb = dhxlOrderStates.cells('a').attachToolbar();
orderstatesListUrl = "index.php?ajax=1&act=ord_win-states_get&id_lang=" + SC_ID_LANG;
orderstatesUpdateUrl = "index.php?ajax=1&act=ord_win-states_update&id_lang=" + SC_ID_LANG;

//toolbar
orderstates_tb.setIconset('awesome');

orderstates_tb.addButton('orderstates_refresh', 100, '', 'fa fa-sync green', 'fa fa-sync green');
orderstates_tb.setItemToolTip('orderstates_refresh', '<?php echo _l('Refresh', 1); ?>');

//
<?php
if(SCMS)
{
    $orderStateShopLsit = array();
    if ($sc_agent->isSuperAdmin()){
        $orderStateShopLsit[] = array(
            'shop-0',
            'obj',
            _l('All shops'),
            ''
        );
    }
    foreach (Shop::getShops(false) as $key => $shop)
    {
        if ($sc_agent->hasAuthOnShop($shop['id_shop']))
        {
            $orderStateShopLsit[] = array(
                'shop-'.$shop['id_shop'],
                'obj',
                $shop['name'],
                ''
            );
        }
    }
?>
orderstates_tb.addButtonSelect("orderstates_shop_list", 100, '', <?php echo json_encode($orderStateShopLsit) ?>, "","",true,true);
orderstates_tb.setItemText('orderstates_shop_list', '<?php echo addslashes($shopName); ?>');
orderstates_tb.setItemToolTip('orderstates_shop_list','<?php echo _l('Shop'); ?>');
<?php } ?>



// buttons actions
        orderstates_tb.attachEvent("onClick", function (id) {

            if (id == "orderstates_refresh") {
                getAndDisplayOrderStatuses(orderstates_grid, orderstatesListUrl);
            }
            if( id.includes('shop-')){
                let selectedText = orderstates_tb.getListOptionText("orderstates_shop_list", orderstates_tb.getListOptionSelected("orderstates_shop_list"));
                orderstates_tb.setItemText('orderstates_shop_list', selectedText);
                getAndDisplayOrderStatuses(orderstates_grid, orderstatesListUrl);
            }
        })


        // edit event
        orderstates_grid.attachEvent("onEditCell", function onEditCellHideOrderStates(stage, rId, cInd, nValue, oValue) {
            idxHidden = orderstates_grid.getColIndexById('hidden');
            if (cInd == idxHidden) {
                if (stage == 2) {// valeur modifiée
                    if(orderstates_tb.getListOptionSelected("orderstates_shop_list")){
                        $selectedIdShop = orderstates_tb.getListOptionSelected("orderstates_shop_list").replace( /shop-([\d]+)/gm, `$1`)
                    } else {
                        $selectedIdShop = 0
                    }
                    $.post(orderstatesUpdateUrl, {
                        "value": orderstates_grid.cells(rId, idxHidden).getValue(),
                        "id_order_state": orderstates_grid.getSelectedRowId(),
                        "id_shop": $selectedIdShop
                    }, function (data) {
                    });
                }
            }
            return true;
        });

        // grid
        orderstates_grid._name = 'orderstates_grid';
        orderstates_grid.enableSmartRendering(false); // enable lazylood ?
        orderstates_grid.enableMultiselect(true);

        // UISettings
        orderstates_grid._uisettings_prefix = orderstates_grid._name;
        orderstates_grid._uisettings_name = orderstates_grid._uisettings_prefix;

        initGridUISettings(orderstates_grid); // default actions on  lists (column moving, sorting,etc.)

        // get and display datas
        function getAndDisplayOrderStatuses(gridObject, url) {
            if(orderstates_tb.getListOptionSelected("orderstates_shop_list")){
                url += "&id_shop="+orderstates_tb.getListOptionSelected("orderstates_shop_list").replace( /shop-([\d]+)/gm, `$1`);
            }
            gridObject.clearAll(true);
            gridObject.load(url, function () {
                // UISettings
                gridObject._rowsNum = gridObject.getRowsNum();
                loadGridUISettings(gridObject);
            });
        }

        getAndDisplayOrderStatuses(orderstates_grid, orderstatesListUrl);
    <?php echo '</script>'; ?>
<?php
} ?>
