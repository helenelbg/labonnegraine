<?php

$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
$scSalt = SCI::getConfigurationValue('SC_SALT');

if (!empty($post_action) && $post_action == 'do_check')
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT m.id_module, m.name, m.active,
                GROUP_CONCAT(ms.id_shop ORDER BY ms.id_shop ASC) AS modules_shop_ids,
                (SELECT GROUP_CONCAT(id_shop ORDER BY id_shop ASC) FROM '._DB_PREFIX_.'shop) AS shops_id
                FROM '._DB_PREFIX_.'module m
                LEFT JOIN '._DB_PREFIX_.'module_shop ms ON ms.id_module = m.id_module
                GROUP BY m.name
                HAVING m.active = 0 OR modules_shop_ids IS NULL OR modules_shop_ids != shops_id';
    }
    else
    {
        $sql = 'SELECT id_module,name
                FROM '._DB_PREFIX_.'module
                WHERE active = 0';
    }
    $moduleDatabase = Db::getInstance()->executeS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($moduleDatabase))
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var tbDisabledModules = dhxlSCExtCheck.tabbar.cells("<?php echo $cell_id; ?>").attachToolbar();
            tbDisabledModules.setIconset('awesome');
            tbDisabledModules.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbDisabledModules.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbDisabledModules.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbDisabledModules.setItemToolTip('delete','<?php echo _l('Uninstall and delete modules'); ?>');
            tbDisabledModules.attachEvent("onClick",
                function(id){
                    switch(id) {
                        case 'selectall':
                            gridDisabledModules.forEachRow(function(id) {
                                let is_enable = Number(gridDisabledModules.getUserData(id,'isEnable'));
                                if(is_enable !==1) {
                                    gridDisabledModules.selectRowById(Number(id),true);
                                }
                            });
                            break;
                        case 'delete':
                            if(confirm('<?php echo _l('Are you sure you want to uninstall and delete the selected items?', 1); ?>')) {
                                uninstallAndDeleteDisabledModules();
                            }
                            break;
                    }
                });

            var gridDisabledModules = dhxlSCExtCheck.tabbar.cells("<?php echo $cell_id; ?>").attachGrid();
            gridDisabledModules.setImagePath("lib/js/imgs/");
            gridDisabledModules.enableSmartRendering(true);
            gridDisabledModules.enableMultiselect(true);

            gridDisabledModules.setHeader("<?php echo _l('Module'); ?>,<?php echo _l('Active'); ?>");
            gridDisabledModules.setInitWidths("*,*");
            gridDisabledModules.setColAlign("left,left");
            gridDisabledModules.setColTypes("ro,ro");
            gridDisabledModules.setColSorting("str,str");
            gridDisabledModules.attachHeader("#text_filter,#text_filter");
            gridDisabledModules.init();

            let xml = '<rows>';
            <?php foreach ($moduleDatabase as $module)
        {
            $active = _l('No');
            $activeValue = 0;
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') &&
                $module['active'] == 1
                && !empty($module['modules_shop_ids'])
                && $module['modules_shop_ids'] != $module['shops_id'])
            {
                $list = array();
                foreach (explode(',', $module['shops_id']) as $id_shop)
                {
                    if (in_array($id_shop, explode(',', $module['modules_shop_ids'])))
                    {
                        $list[] = $id_shop;
                    }
                }
                if (!empty($list))
                {
                    $active = _l('Still enable on shops:%s', null, array(implode(',', $list)));
                    $activeValue = 1;
                }
            } ?>
                    xml = xml+'    <row id="<?php echo (int) $module['id_module']; ?>">';
                    xml = xml+'        <userdata name="isEnable"><?php echo (int) $activeValue; ?></userdata>';
                    xml = xml+'        <cell<?php echo $activeValue > 0 ? ' bgColor="EFEFEF"' : ''; ?>><![CDATA[<?php echo $module['name']; ?>]]></cell>';
                    xml = xml+'        <cell<?php echo $activeValue > 0 ? ' bgColor="EFEFEF"' : ''; ?>><![CDATA[<?php echo $active; ?>]]></cell>';
                    xml = xml+'    </row>';
                    <?php
        } ?>
            xml = xml+'</rows>';
            gridDisabledModules.parse(xml);

            gridDisabledModules.attachEvent("onBeforeSelect",function (rowId){
                let is_enable = Number(gridDisabledModules.getUserData(rowId,'isEnable'));
                return is_enable <= 0;
            });

            function uninstallAndDeleteDisabledModules()
            {

                let selectedModules = gridDisabledModules.getSelectedRowId();
                if(selectedModules)
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=<?php echo $current_control_filename; ?>&id_lang="+SC_ID_LANG, {
                        "action": 'uninstallAndDeleteModuleList',
                        "mlist": selectedModules
                    }, function(){
                        dhxlSCExtCheck.tabbar.tabs("<?php echo $cell_id; ?>").close();
                        dhxlSCExtCheck.gridChecks.selectRowById('<?php echo $current_control_filename; ?>');
                        doCheck(false);
                    });
                }
            }
        </script>
        <?php $content_js = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'grid',
        'content' => $content,
        'title' => _l('Disabled modules'),
        'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'uninstallAndDeleteModuleList')
{
    $mList = Tools::getValue('mlist');
    if (!empty($mList))
    {
        $mList = explode(',', $mList);
        foreach ($mList as $moduleId)
        {
            $moduleInst = Module::getInstanceById($moduleId);
            if ($moduleInst)
            {
                $path = (version_compare(_PS_VERSION_, '1.5.0.1', '>=') ? $moduleInst->getLocalPath() : _PS_MODULE_DIR_.$moduleInst->name);
                $uninstalled = $moduleInst->uninstall();
                if ($uninstalled)
                {
                    dirRemove($path);
                }
            }
            else
            {
                $sql = array(
                    'DELETE FROM `'._DB_PREFIX_.'module` WHERE `id_module` = '.(int) $moduleId,
                    'DELETE FROM `'._DB_PREFIX_.'module_group` WHERE `id_module` = '.(int) $moduleId,
                    'DELETE FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.(int) $moduleId,
                );
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $sql[] = 'DELETE FROM `'._DB_PREFIX_.'module_shop` WHERE `id_module` = '.(int) $moduleId;
                }
                foreach($sql as $rowSql)
                {
                    Db::getInstance()->execute($rowSql);
                }
            }
        }
    }
}
