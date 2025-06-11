<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $res = array();
    $sql = 'SELECT id_product, position
            FROM '._DB_PREFIX_.'image
            ORDER BY `id_product` ASC, `position` ASC';
    $images = Db::getInstance()->ExecuteS($sql);

    $key = 0;
    $id_last_product = 0;
    foreach ($images as $k => $image)
    {
        if ($id_last_product != $image['id_product'])
        {
            $id_last_product = $image['id_product'];
            $key = 1;
        }
        if ($key != $image['position'])
        {
            $res[$k]['id_product'] = $image['id_product'];
        }
        ++$key;
    }

    $content = '';
    $content_js = '';
    $results = 'OK';

    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbImagePositionOrder = dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_IMG_POSITIONS").attachToolbar();
            tbImagePositionOrder.setIconset('awesome');
            tbImagePositionOrder.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbImagePositionOrder.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbImagePositionOrder.addButton("reorderImagesPositions", 0, "", 'fad fa-tools green', 'fad fa-tools green');
            tbImagePositionOrder.setItemToolTip('reorderImagesPositions','<?php echo _l('Reorder images positions', 1); ?>');
            tbImagePositionOrder.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridImagePositionOrder.selectAll();
                        getGridStat_ImagePositionOrder();
                    }
                    if (id=='reorderImagesPositions')
                    {
                        reorderImagesPositions()
                    }
                });

            var gridImagePositionOrder = dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_IMG_POSITIONS").attachGrid();
            gridImagePositionOrder.setImagePath("lib/js/imgs/");
            gridImagePositionOrder.enableSmartRendering(false);
            gridImagePositionOrder.enableMultiselect(true);

            gridImagePositionOrder.setHeader("ID <?php echo _l('product'); ?>");
            gridImagePositionOrder.setInitWidths("100");
            gridImagePositionOrder.setColAlign("left");
            gridImagePositionOrder.setColTypes("ro");
            gridImagePositionOrder.attachHeader("#numeric_filter");
            gridImagePositionOrder.init();

            var xml = '<rows>';
            <?php foreach ($res as $i => $row)
        {
            ?>
            xml = xml+'   <row id="<?php echo $row['id_product']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridImagePositionOrder.parse(xml);

            sbImagePositionOrder=dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_IMG_POSITIONS").attachStatusBar();
            function getGridStat_ImagePositionOrder(){
                var filteredRows=gridImagePositionOrder.getRowsNum();
                var selectedRows=(gridImagePositionOrder.getSelectedRowId()?gridImagePositionOrder.getSelectedRowId().split(',').length:0);
                sbImagePositionOrder.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridImagePositionOrder.attachEvent("onFilterEnd", function(elements){
                getGridStat_ImagePositionOrder();
            });
            gridImagePositionOrder.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ImagePositionOrder();
            });
            getGridStat_ImagePositionOrder();

            function reorderImagesPositions()
            {
                var selectedImagePositions = gridImagePositionOrder.getSelectedRowId();
                if(selectedImagePositions==null || selectedImagePositions=="")
                    selectedImagePositions = 0;
                if(selectedImagePositions!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PRODUCT_IMG_POSITIONS&id_lang="+SC_ID_LANG, { "action": "reorder_image_position", "ids": selectedImagePositions}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PRODUCT_IMG_POSITIONS").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PRODUCT_IMG_POSITIONS');
                        doCheck(false);
                    });
                }
            }
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Image position'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'reorder_image_position')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id_product)
        {
            $sql = 'SELECT id_image
            FROM '._DB_PREFIX_.'image
            WHERE id_product = '.(int) $id_product.'
            ORDER BY `position` ASC, `id_image` ASC';
            $images = Db::getInstance()->ExecuteS($sql);

            $newPosition = 1;
            $sql = '';
            foreach ($images as $image)
            {
                dbExecuteForeignKeyOff('UPDATE '._DB_PREFIX_.'image
                        SET position = '.(int) $newPosition.'
                        WHERE id_image = '.(int) $image['id_image']);
                ++$newPosition;
            }
        }
    }
}
