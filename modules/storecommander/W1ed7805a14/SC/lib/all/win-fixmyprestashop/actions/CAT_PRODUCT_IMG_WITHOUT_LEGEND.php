<?php
if (!defined('STORE_COMMANDER')) { exit; }

$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $langs = array();
    $tmpl = Language::getLanguages(false);
    $langs = array_column($tmpl,'iso_code','id_lang');

    $tmps = SCI::getAllShops();
    $shops = array_column($tmps,'name','id_shop');

    $sql = "SELECT DISTINCT ishop.*, il.`id_lang`, il.`legend`, pl.`name`
            FROM `"._DB_PREFIX_."image_shop` ishop
            RIGHT JOIN `"._DB_PREFIX_."image_lang` il ON (ishop.`id_image`=il.`id_image` AND (il.`legend` = '' OR il.`legend` IS NULL))
            LEFT JOIN `"._DB_PREFIX_."product_lang` pl ON (ishop.`id_product`= pl.`id_product` AND pl.id_lang = il.id_lang)
            WHERE ishop.`id_product` > 0 
            AND ishop.`id_shop`=".SCI::getSelectedShop()."
            GROUP BY `ishop`.`id_product`,`ishop`.`id_image`,`ishop`.`id_shop`,il.id_lang 
            LIMIT 1500";
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($res) && count($res) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script>

            var tbProductImgWithoutLegend = dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_IMG_WITHOUT_LEGEND").attachToolbar();
            tbProductImgWithoutLegend.setIconset('awesome');
            tbProductImgWithoutLegend.addButton("exportcsv", 0, "", 'fad fa-file-csv green', 'fad fa-file-csv green');
            tbProductImgWithoutLegend.setItemToolTip("exportcsv","<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>");
            tbProductImgWithoutLegend.addButton("image_fill_legend", 0, "", 'fad fa-key yellow', 'fad fa-key yellow');
            tbProductImgWithoutLegend.setItemToolTip('image_fill_legend','<?php echo _l('Assign product name to selected image(s) legend', 1); ?>');
            tbProductImgWithoutLegend.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbProductImgWithoutLegend.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbProductImgWithoutLegend.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridProductImgWithoutLegend.selectAll();
                        getGridStat_ProductImgWithoutLegend();
                    }
                    if (id=='exportcsv')
                    {
                        displayQuickExportWindow(gridProductImgWithoutLegend, 1);
                    }
                    if (id=='image_fill_legend')
                    {
                        ImagesFillLegends()
                    }
                });

            var gridProductImgWithoutLegend = dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_IMG_WITHOUT_LEGEND").attachGrid();
            gridProductImgWithoutLegend.enableSmartRendering(false);
            gridProductImgWithoutLegend.enableMultiselect(true);

            gridProductImgWithoutLegend.setHeader("ID <?php echo _l('Product'); ?>,ID <?php echo _l('Image'); ?>,<?php echo _l('Language'); ?>,<?php echo _l('Shop'); ?>,<?php echo _l('Product name'); ?>");
            gridProductImgWithoutLegend.setInitWidths("60,60,60,120,100");
            gridProductImgWithoutLegend.setColAlign("right,right,right,right,right,");
            gridProductImgWithoutLegend.setColTypes("ro,ro,ro,ro,ro");
            gridProductImgWithoutLegend.setColSorting("int,int,str,str,str");
            gridProductImgWithoutLegend.attachHeader("#numeric_filter,#numeric_filter,#select_filter,#select_filter,#text_filter");
            gridProductImgWithoutLegend.init();

            var xml = '<rows>';
            <?php foreach ($res as $row)
            {
            ?>
            xml = xml+'   <row id="<?php echo $row['id_product'].'_'.$row['id_image'].'_'.$row['id_lang'].'_'.$row['id_shop']; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_product']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo $row['id_image']; ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($langs[$row['id_lang']]); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($shops[$row['id_shop']]); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo addslashes($row['name']); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
            } ?>
            xml = xml+'</rows>';
            gridProductImgWithoutLegend.parse(xml);

            sbProductImgWithoutLegend=dhxlSCExtCheck.tabbar.cells("table_CAT_PRODUCT_IMG_WITHOUT_LEGEND").attachStatusBar();
            function getGridStat_ProductImgWithoutLegend(){
                var filteredRows=gridProductImgWithoutLegend.getRowsNum();
                var selectedRows=(gridProductImgWithoutLegend.getSelectedRowId()?gridProductImgWithoutLegend.getSelectedRowId().split(',').length:0);
                sbProductImgWithoutLegend.setText('<?php echo count($res).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridProductImgWithoutLegend.attachEvent("onFilterEnd", function(elements){
                getGridStat_ProductImgWithoutLegend();
            });
            gridProductImgWithoutLegend.attachEvent("onSelectStateChanged", function(id){
                getGridStat_ProductImgWithoutLegend();
            });
            getGridStat_ProductImgWithoutLegend();

            function ImagesFillLegends()
            {
                var selectedProductImgWithoutLegends = gridProductImgWithoutLegend.getSelectedRowId();
                if(selectedProductImgWithoutLegends==null || selectedProductImgWithoutLegends=="")
                    selectedProductImgWithoutLegends = 0;
                if(selectedProductImgWithoutLegends!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PRODUCT_IMG_WITHOUT_LEGEND&id_lang="+SC_ID_LANG, { "action": "img_fill_legends", "ids": selectedProductImgWithoutLegends}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PRODUCT_IMG_WITHOUT_LEGEND").close();

                        dhxlSCExtCheck.gridChecks.selectRowById('CAT_PRODUCT_IMG_WITHOUT_LEGEND');
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
        'title' => _l('Image without legend'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'img_fill_legends')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_product, $id_image, $id_lang, $id_shop) = explode('_', $id);

            //$sql = 'SELECT * FROM `'._DB_PREFIX_."image` WHERE id_image='".(int) $id_image."' ";
            //$res = Db::getInstance()->ExecuteS($sql);

            $sql = 'UPDATE '._DB_PREFIX_.'image_lang il SET `legend` = 
                    (SELECT `name` FROM '._DB_PREFIX_.'product_lang pl WHERE pl.id_lang='.(int)$id_lang.' AND pl.id_product='.(int)$id_product.' AND pl.id_shop = '.(int)$id_shop.')
                    WHERE il.id_image='.(int)$id_image.' and il.id_lang= '.(int)$id_lang;
            $res = Db::getInstance()->Execute($sql);
        }
    }
}
