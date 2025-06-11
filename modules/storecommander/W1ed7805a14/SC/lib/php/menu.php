<?php

function createMenu()
{
    global $languages,$page,$sc_agent,$menuConfiguration,$user_lang_iso,$menu_js_action;
    $updateAvailable = checkSCVersion(false, false);
    $sc_module_infos = SCI::getScModulesInfos(); ?>
    var dhxMenu = dhxLayout.attachMenu();
    dhxMenu.setIconset('awesome');
    var XMLMenuData=''+
'<menu>'+
<?php if (_r('MEN_CAT_CATALOG')) { ?>
'<item id="catalog" text="<?php echo _l('Catalog', 1); ?>" img="fad fa-edit yellow" imgdis="fad fa-edit yellow">'+
    '<item id="cat_tree" text="<?php echo _l('Categories and products', 1); ?>" img="fad fa-folder-tree blue" imgdis="fad fa-folder-tree blue"/>'+
    '<item id="cat_grid" text="<?php echo _l('Products list', 1); ?>" img="fad fa-list-alt blue" imgdis="fad fa-list-alt blue"/>'+
    '<item id="cat_categories" text="<?php echo _l('Categories', 1); ?>" img="fad fa-folder-tree" imgdis="fad fa-folder-tree" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'>'+
        <?php if (_r('MEN_CAT_CATMANAGEMENT')) { ?>
        '<item id="cat_management" text="<?php echo _l('Categories management', 1); ?>" img="fa fa-cog yellow" imgdis="fa fa-cog yellow" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'/>'+
        <?php } if (_r('MEN_CAT_CATIMPORT_CSV')) { ?>
        '<item id="cat_catimport" text="<?php echo _l('CSV Import', 1); ?>" img="fad fa-sign-in green" imgdis="fad fa-sign-in green" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'/>'+
        <?php } if (_r('MEN_CAT_CATEXPORT_CSV')) { ?>
        '<item id="cat_catexport" text="<?php echo _l('CSV Export', 1); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'/>'+
        <?php } ?>
    '</item>'+
    <?php
    if (version_compare(_PS_VERSION_, '1.5.0.0', '<') && _r('MEN_CAT_DISCOUNT'))
    {
        ?>
    '<item id="cat_discount" text="<?php echo _l('Discounts', 1); ?>" img="fad fa-medal red" imgdis="fad fa-medal red">'+
        '<item id="cat_resetpricedropdates" text="<?php echo _l('Reset prices drop dates', 1); ?>" img="fad fa-medal red" imgdis="fad fa-medal red"/>'+
        '<item id="cat_resetpricedropreductions" text="<?php echo _l('Reset prices drop reductions', 1); ?>" img="fad fa-medal red" imgdis="fad fa-medal red"/>'+
        '<item id="cat_resetpricedrop" text="<?php echo _l('Delete all prices drop', 1); ?>" img="fad fa-medal red" imgdis="fad fa-medal red"/>'+
    '</item>'+
<?php
    }
    if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') || Combination::isFeatureActive()) && _r('MEN_CAT_ATTRIBUTES_GROUPS'))
    {
        ?>
    '<item id="cat_attribute" text="<?php echo _l('Attributes and groups', 1); ?>" img="fa fa-asterisk yellow" imgdis="fa fa-asterisk yellow">'+
        '<item id="cat_attributes" text="<?php echo _l('Attribute and group management', 1); ?>" img="fad fa-edit" imgdis="fad fa-edit"/>'+
        '<item id="cat_impexp_attr_translation" text="<?php echo _l('Export/Import translations', 1); ?>" img="fad fa-flag blue" imgdis="fad fa-flag blue"/>'+
    '</item>'+
<?php
    }
    if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') || Feature::isFeatureActive()) && _r('MEN_CAT_FEATURES'))
    {
        ?>
    '<item id="cat_feature" text="<?php echo _l('Features', 1); ?>" img="fa fa-eye" imgdis="fa fa-eye">'+
        '<item id="cat_features" text="<?php echo _l('Feature management', 1); ?>" img="fad fa-edit" imgdis="fad fa-edit"/>'+
        '<item id="cat_impexp_feat_translation" text="<?php echo _l('Export/Import translations', 1); ?>" img="fad fa-flag blue" imgdis="fad fa-flag blue"/>'+
    '</item>'+
<?php
    }
    if (_r('MEN_MAN_MANUFACTURERS')) { ?>
        '<item id="man" text="<?php echo _l('Manufacturers', 1); ?>" img="fad fa-list-alt blue" imgdis="fad fa-list-alt blue">'+
            '<item id="man_tree" text="<?php echo _l('Manufacturer management', 1); ?>" img="fad fa-edit" imgdis="fad fa-edit"/>'+
            '<item id="man_import" text="<?php echo _l('CSV Import', 1); ?>" img="fad fa-sign-in green" imgdis="fad fa-sign-in green"/>'+
        '</item>'+
<?php
    }
    if (_r('MEN_SUP_SUPPLIERS')) { ?>
        '<item id="sup" text="<?php echo _l('Suppliers', 1); ?>" img="fad fa-list-alt blue" imgdis="fad fa-list-alt blue">'+
            '<item id="sup_tree" text="<?php echo _l('Supplier management', 1); ?>" img="fad fa-edit" imgdis="fad fa-edit"/>'+
        '</item>'+
<?php
    }
    if (_r('MEN_CAT_SPECIFIC_PRICE'))
    {
        ?>
    '<item id="cat_specificprice" text="<?php echo _l('Specific prices', 1); ?>" img="fad fa-money-check-edit-alt" imgdis="fad fa-money-check-edit-alt" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+' />'+
    <?php
    }
    if (_r('MEN_CAT_IMPORT_CSV')) { ?>
    '<item id="cat_import" text="<?php echo _l('CSV Import', 1); ?>" img="fad fa-sign-in green" imgdis="fad fa-sign-in green"/>'+
    <?php }
    if (_r('MEN_CAT_EXPORT_CSV')) { ?>
    '<item id="cat_export" text="<?php echo _l('CSV Export', 1); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green"/>'+
    <?php } ?>
    <?php if (_r('MEN_CAT_TOOLS')) { ?>
    '<item id="cat_tools" text="<?php echo _l('Tools', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green">'+
        <?php if (_r('MEN_CAT_CHECK_FIX_CATEGORIES')) { ?>
        '<item id="cat_rebuildleveldepth" text="<?php echo _l('Check and fix categories', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
<?php
        }
        if (SCMS && _r('MEN_CAT_SYNCHRO_CATS_POSITIONS')) { ?>
        '<item id="cat_synchro_cats_positions" text="<?php echo _l('Synchronize the categories positions on multiple shops', 1); ?>" img="fad fa-sync" imgdis="fad fa-sync"></item>'+
<?php
        }
    if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
    {
        ?>
        '<item id="cat_rebuildlangfield" text="<?php echo _l('Check and fix language fields of products for all languages', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
        <?php if (_r('MEN_CAT_TOOLS_REBUILD_PRODUCT_PRICE')) { ?>
        '<item id="cat_rebuildproductprice" text="<?php echo _l('Set product prices to their default combination prices', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
        <?php } ?>
        '<item id="cat_fillcoverimage" text="<?php echo _l('Set a cover image for products without cover image', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
<?php
    }
    else
    {
        ?>
        <?php if (_r('MEN_CAT_TOOLS_REBUILD_PRODUCT_PRICE')) { ?>
        '<item id="cat_rebuildproductprice" text="<?php echo _l('Set product prices to their default combination prices', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
        <?php
        }
        if (_r('MEN_CAT_CALCULATE_TOTAL_STOCK_COMBI')) { ?>
        '<item id="cat_rebuildsumstock" text="<?php echo _l('Calculate total stock of products with combinations', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
<?php }
    }
?>
        <?php
        $as_version = SCI::getModuleVersion('pm_advancedsearch4');
        if (SCI::moduleIsInstalled('pm_advancedsearch4') && version_compare($as_version, '4.12.0', '>=') && defined('SC_AdvancedSearchSeo_ACTIVE') && SC_AdvancedSearchSeo_ACTIVE) { ?>
        '<item id="cat_advancedsearchseo" text="<?php echo _l('%s - SEO Pages', 1, array('Advanced Search')); ?>" img="fad fa-at" imgdis="fad fa-at"></item>'+
        <?php } ?>
    '</item>'+
    <?php } ?>
'</item>'+
<?php } ?>
<?php if (_r('MENU_ORD_ORDERS')) { ?>
'<item id="order" text="<?php echo _l('Orders', 1); ?>" img="fa fa-shopping-cart" imgdis="fa fa-shopping-cart">'+
    '<item id="ord_orders" text="<?php echo _l('Orders', 1); ?>" img="fad fa-list-alt blue" imgdis="fad fa-list-alt blue"></item>'+
<?php
    if (version_compare(_PS_VERSION_, '1.5.0.0', '<') && _r('MENU_ORD_EXPORTORDERS'))
    {
        if (Configuration::get('SC_EXPORTORDERS_INSTALLED'))
        {
            ?>
    '<item id="ord_exportorders" text="<?php echo _l('Export orders', 1); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green"></item>'+
<?php
        }
        else
        {
            ?>
    '<item id="teaser_exportorders" text="<?php echo _l('Export orders', 1); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green">'+
        '<item id="teaser_exportorders_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
    '</item>'+
<?php
        }
    }
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (_r('GRI_ORD_MAKEORDER_INTERFACE'))
        {
            ?>
        '<item id="ord_win_makeorder" text="<?php echo _l('Create an order', 1); ?>" img="fad fa-cart-plus" imgdis="fad fa-cart-plus"></item>'+
<?php
        }
        if (_r('MENU_ORD_DISCOUNT_VOUCHERS'))
        {
            ?>
            '<item id="ord_cartrules" text="<?php echo _l('Discount voucher', 1); ?>" img="fad fa-tags" imgdis="fad fa-tags"></item>'+
<?php
        }
        if (_r('MENU_ORD_MANAGE_STATUSES'))
        {
            ?>
        '<item id="ord_states" text="<?php echo _l('Manage order statuses', 1); ?>" img="fa fa-cog" imgdis="fa fa-cog"></item>'+
<?php
        }
    }
?>
        <?php if (KAI9DF4 != 1 && _r('MEN_TRENDS')) { ?>
            '<item id="trends_shop" text="<?php echo _l('Stats and trends', 1); ?>" img="fa fa-chart-area" imgdis="fa fa-chart-area"/>'+
        <?php } ?>
<?php
    if (SC_ExportOrders_ACTIVE == 1)
    {
        ?>
        '<item id="acc_quickaccounting<?php echo (empty($sc_module_infos['scquickaccounting']['present']) ? '_download' : (empty($sc_module_infos['scquickaccounting']['installed']) ? '_install' : '')); ?>" text="<?php echo _l('CSV Export', 1).(!empty($sc_module_infos['scquickaccounting']['message']) ? ' '.$sc_module_infos['scquickaccounting']['message'] : ''); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green"></item>'+
        <?php
    }
    else
    {
        ?>
        '<item id="teaser_quickaccounting" text="<?php echo _l('CSV Export', 1); ?>" img="fad fa-edit yellow" imgdis="fad fa-edit yellow">'+
            '<item id="teaser_quickaccounting_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
            '</item>'+
        <?php
    }
?>
'</item>'+
<?php } ?>
<?php if (_r('MENU_CUS_CUSTOMERS')) { ?>
'<item id="customer" text="<?php echo _l('Customers', 1); ?>" img="fa fa-user" imgdis="fa fa-user">'+
    '<item id="cus_customers" text="<?php echo _l('Customers', 1); ?>" img="fad fa-list-alt blue" imgdis="fad fa-list-alt blue"></item>'+
    <?php if (_r('GRI_CUSM_VIEW_CUSM')) { ?>
    '<item id="cusm_customersservice" text="<?php echo _l('Customer service', 1); ?>" img="fad fa-comment-lines" imgdis="fad fa-comment-lines"></item>'+
    <?php } ?>
    '<item id="cus_groupmanagement" text="<?php echo _l('Customer group', 1); ?>" img="fad fa-user-friends" imgdis="fad fa-user-friends"></item>'+
    <?php
    if (_r('MENU_CUS_IMPORTCUSTOMERS'))
    { ?>
    '<item id="cus_import" text="<?php echo _l('CSV Import', 1); ?>" img="fad fa-sign-in green" imgdis="fad fa-sign-in green"/>'+
    <?php
    }

    if (_r('MENU_CUS_EXPORTCUSTOMERS'))
    {
        if (SC_ExportCustomers_ACTIVE == 1)
        {    ?>
        '<item id="cus_export<?php echo (empty($sc_module_infos['scexportcustomers']['present']) ? '_download' : (empty($sc_module_infos['scexportcustomers']['installed']) ? '_install' : '')); ?>" text="<?php echo _l('CSV Export', 1).(!empty($sc_module_infos['scexportcustomers']['message']) ? ' '.$sc_module_infos['scexportcustomers']['message'] : ''); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green"/>'+
    <?php
        }
        else
        { ?>
        '<item id="teaser_cus_export" text="<?php echo _l('CSV Export', 1); ?>" img="fad fa-sign-out fa-flip-horizontal green" imgdis="fad fa-sign-out fa-flip-horizontal green">'+
            '<item id="teaser_cus_export_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"></item>'+
        '</item>'+
    <?php
        }
    }
    ?>
    '<item id="cus_tools" text="<?php echo _l('Tools', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green">'+
        '<item id="cus_tools_format" text="<?php echo _l('Set customers data to format', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"'+(SC_PAGE!='cus_tree'?' disabled="true"':'')+'>'+
            '<item id="cus_tools_format_capitalize" text="<?php echo _l('Firstname Lastname', 1); ?>" '+(SC_PAGE!='cus_tree'?' disabled="true"':'')+'></item>'+
            '<item id="cus_tools_format_uppercase" text="<?php echo _l('Firstname LASTNAME', 1); ?>" '+(SC_PAGE!='cus_tree'?' disabled="true"':'')+'></item>'+
        '</item>'+
    '</item>'+
'</item>'+
<?php } ?>
<?php if (_r('MENU_CMS_CMSPAGE')) { ?>
    '<item id="cms" text="<?php echo _l('CMS', 1); ?>" img="fad fa-edit" imgdis="fad fa-edit">'+
        '<item id="cms_tree" text="<?php echo _l('CMS page', 1); ?>" img="fad fa-list-alt blue" imgdis="fad fa-list-alt blue"></item>'+
        '</item>'+
<?php } ?>
<?php if (_r('MENU_MAR_MARKETING')) { ?>
'<item id="marketing" text="<?php echo _l('Marketing', 1); ?>" img="fad fa-money-bill-alt" imgdis="fad fa-money-bill-alt">'+
<?php
if (SC_Affiliation_ACTIVE)
{    ?>
    '<item id="affiliation<?php echo (empty($sc_module_infos['scaffiliation']['present']) ? '_download' : (empty($sc_module_infos['scaffiliation']['installed']) ? '_install' : ''));?>" text="<?php echo _l('Affiliation program', 1).(!empty($sc_module_infos['scaffiliation']['message']) ? ' '.$sc_module_infos['scaffiliation']['message'] : ''); ?>" img="fad fa-users-class yellow" imgdis="fad fa-users-class yellow"/>'+
<?php }
else
{
    ?>
    '<item id="teaser_affiliation" text="<?php echo _l('Affiliation', 1); ?>" img="fad fa-users-class yellow" imgdis="fad fa-users-class yellow">'+
        '<item id="teaser_affiliation_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
    '</item>'+
<?php
}
        if (_r('MEN_MAR_SEGMENTATION'))
        {
            if (SCSG)
            {
                ?>
    '<item id="mar_segmentation" text="<?php echo _l('Segmentation', 1); ?>" img="fad fa-chart-pie blue" imgdis="fad fa-chart-pie blue"></item>'+
<?php
            }
            else
            {
                ?>
    '<item id="teaser_segmentation" text="<?php echo _l('Segmentation', 1); ?>" img="fad fa-chart-pie blue" imgdis="fad fa-chart-pie blue">'+
        '<item id="teaser_segmentation_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
    '</item>'+
<?php
            }
        } ?>
<?php if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) { ?>
        '<item id="scc_lab" text="<?php echo _l('Lab', 1); ?>" img="fad fa-regular fa-flask" imgdis="fad fa-chart-pie blue">'+
        '</item>'+
<?php } ?>
<?php
        if (SC_CatalogPDF_ACTIVE == 1) { ?>
        '<item id="catalog_pdf_read<?php echo (empty($sc_module_infos['scpdfcatalog']['present']) ? '_download' : (empty($sc_module_infos['scpdfcatalog']['installed']) ? '_install' : ''));?>" text="<?php echo _l('Create your PDF Catalog', 1).(!empty($sc_module_infos['scpdfcatalog']['message']) ? ' '.$sc_module_infos['scpdfcatalog']['message'] : ''); ?>" img="fad fa-file-pdf blue" imgdis="fad fa-file-pdf blue"></item>'+
<?php }
        else
        { ?>
        '<item id="catalog_pdf" text="<?php echo _l('Create your PDF Catalog', 1); ?>" img="fad fa-file-pdf blue" imgdis="fad fa-file-pdf blue">'+
            '<item id="catalog_pdf_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
        '</item>'+
<?php }
        if (!defined('SUB6TYP2') || (defined('SUB6TYP2') && !in_array(SUB6TYP2, array(3, 4, 5, 7, 9, 10)))) {?>
        '<item id="sc_amazon" text="<?php echo _l('Check SC Amazon for Amazon module', 1); ?>" img="fad fa-badge-dollar" imgdis="fad fa-badge-dollar">'+
            '<item id="sc_amazon_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
        '</item>'+
        '<item id="sc_cdiscount" text="<?php echo _l('Check SC Cdiscount for Cdiscount module', 1); ?>" img="fad fa-badge-dollar" imgdis="fad fa-badge-dollar">'+
            '<item id="sc_cdiscount_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
        '</item>'+
        '<item id="sc_feedbiz" text="<?php echo _l('Check SC Feed.biz for Feed.biz Amazon/Cdiscount', 1); ?>" img="fad fa-badge-dollar" imgdis="fad fa-badge-dollar">'+
            '<item id="sc_feedbiz_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
        '</item>'+
<?php } ?>
'</item>'+
<?php } ?>
//'<item id="emailing" text="<?php echo _l('Emailing (Preview)', 1); ?>" img="fad fa-envelope blue" imgdis="fad fa-envelope blue"></item>'+
<?php if (_r('MEN_TOO_TOOLS')) { ?>
'<item id="config" text="<?php echo _l('Tools', 1); ?>" img="fa fa-cog" imgdis="fa fa-cog">'+
<?php
    if (_s('APP_COMPAT_EBAY'))
    {
        ?>    '<item id="config_ebay" text="eBay" img="lib/img/ebay.gif" imgdis="lib/img/ebay.gif"></item>'+ <?php
    }
    if (_r('MEN_TOO_EXTENSIONS'))
    {
        echo eval('?>'.$menuConfiguration['Tools'].'<?php ');
    }
?>
    <?php
    if (!_s('APP_DISABLE_CHANGE_HISTORY') && _r('MEN_TOO_HISTORY'))
    {
        ?>
        '<item id="cat_history" text="<?php echo _l('History', 1); ?>" img="fa fa-clock" imgdis="fa fa-clock"/>'+
    <?php
    }
    if (_r('MEN_TOO_SETTINGS')) { ?>
        '<item id="core_settings" text="<?php echo _l('Settings', 1); ?>" img="fad fa-sliders-v-square yellow" imgdis="fad fa-sliders-v-square yellow"/>'+
    <?php }
    if (_r('MEN_TOO_PERMISSIONS')) { ?>
        '<item id="permissions" text="<?php echo _l('Manage user permissions', 1); ?>" img="fad fa-user-shield green" imgdis="fad fa-user-shield green"/>'+
    <?php }
    if (_r('MEN_TOO_LANGUAGE')) { ?>
        '<item id="core_language" text="<?php echo _l('SC language', 1); ?>" img="fad fa-flag blue" imgdis="fad fa-flag blue">'+
            '<item id="core_language_" text="<?php echo _l('Use PrestaShop backoffice language', 1); ?>"></item>'+
            <?php
            $files = array_diff(scandir(SC_DIR.'lang'), array_merge(array('.', '..', 'index.php', 'index.htm', 'index.html', '.htaccess', 'php.ini')));
            foreach ($files as $file)
            {
                echo '\'<item id="core_language_'.str_replace('.php', '', $file).'" text="'.strtoupper(str_replace('.php', '', $file)).'"'.(str_replace('.php', '', $file) == $user_lang_iso ? ' img="fad fa-flag blue" imgdis="fad fa-flag blue"' : '').'></item>\'+';
            }
            ?>
            '<item id="core_languagehelp" text="<?php echo _l('Help us to translate Store Commander!', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"></item>'+
            '<item id="core_languageupdate" text="<?php echo _l('Update Store Commander translations', 1); ?>" img="fad fa-sync green" imgdis="fad fa-sync green"></item>'+
            '</item>'+
    <?php } ?>
        '<item id="core_queuelogs" text="<?php echo _l('Tasks error logs', 1); ?>" img="fa fa-bug red" imgdis="fa fa-bug red"/>'+
    <?php if (_r('MEN_TOO_SERVER')) { ?>
        '<item id="config_server" text="<?php echo _l('Server', 1); ?>" img="fad fa-server" imgdis="fad fa-server">'+
            '<item id="ser_emptysmartycache" text="<?php echo _l('Empty Smarty cache', 1); ?>" img="fa fa-eraser red" imgdis="fa fa-eraser red"></item>'+
            '<item id="ser_page404" text="<?php echo _l('Page not found 404', 1); ?>" img="fad fa-exclamation-triangle orange" imgdis="fad fa-exclamation-triangle orange"></item>'+
            '<item id="config_changehashdir" text="<?php echo _l('Change security key', 1); ?>" img="fad fa-exclamation-triangle orange" imgdis="fad fa-exclamation-triangle orange"></item>'+
        '</item>'+
    <?php } ?>
/*
    <?php
    if (_r('MEN_TOO_INSTALLATION')) { ?>
    '<item id="config_install" text="<?php echo _l('Installation', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green">'+
<?php if (SC_INSTALL_MODE == 0 && version_compare(_PS_VERSION_, '1.5.0.0', '<')) { ?>        '<item id="config_createquickaccess" text="<?php echo _l('Create link in the PrestaShop Quick access menu', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+<?php } ?>
        '<item id="config_createcsvimportsample" text="<?php echo _l('Create files example for CSV import', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
        '<item id="config_createcsvexportsample" text="<?php echo _l('Create script files example for CSV export', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"></item>'+
    '</item>'+
    <?php } ?>
    '<item id="core_labs" text="<?php echo _l('Laboratory', 1); ?>" img="fa fa-lightbulb-on yellow" imgdis="fa fa-lightbulb-on yellow">'+
        '<item id="core_labs_intro" text="<?php echo _l('The laboratory contains experimental tools. We need your comments before implementing these tools in Store Commander. Thanks!', 1); ?>" img="fa fa-lightbulb-on yellow" imgdis="fa fa-lightbulb-on yellow"></item>'+
    '</item>'+
*/
    <?php
    if (_r('MEN_TOO_GRIDSSETTINGS'))
    {
        if (!SC_GRIDSEDITOR_INSTALLED && !SC_GRIDSEDITOR_PRO_INSTALLED)
        {    ?>
    '<item id="teaser_gridseditor" text="<?php echo _l('Interface customization', 1); ?>" img="fad fa-ruler-triangle" imgdis="fad fa-ruler-triangle">'+
        '<item id="teaser_gridseditor_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"></item>'+
    '</item>'+
    <?php }
        else
        { ?>
    '<item id="win_grids_editor" text="<?php echo _l('Interface customization', 1); ?>" img="fad fa-ruler-triangle" imgdis="fad fa-ruler-triangle"/>'+
    <?php }
    }
        if (_r('MEN_MAR_SEGMENTATION'))
        {
            if (SCSG)
            {
                ?>
    '<item id="too_segmentation" text="<?php echo _l('Segmentation', 1); ?>" img="fad fa-chart-pie blue" imgdis="fad fa-chart-pie blue"></item>'+
<?php
            }
            else
            {
                ?>
    '<item id="too_teaser_segmentation" text="<?php echo _l('Segmentation', 1); ?>" img="fad fa-chart-pie blue" imgdis="fad fa-chart-pie blue">'+
        '<item id="too_teaser_segmentation_read" text="<?php echo _l('Read more', 1); ?>" img="fa fa-check green" imgdis="fa fa-check green"/>'+
    '</item>'+
<?php
            }
        } ?>
<?php
    if (_r('MEN_TOO_FIXMYPS'))
    {
        if (defined('SC_FixMyPrestashop_ACTIVE') && SC_FixMyPrestashop_ACTIVE == '1')
        {
            ?>
      '<item id="fixmyprestashop" text="FixMyPrestashop" img="fad fa-monitor-heart-rate" imgdis="fad fa-monitor-heart-rate"/>'+
<?php
        }
        else
        {
            ?>
        '<item id="teaser_fixmyps" text="<?php echo _l('FixMyPrestashop', 1); ?>" img="fad fa-monitor-heart-rate" imgdis="fad fa-monitor-heart-rate">'+
            '<item id="teaser_fixmyps_read" text="<?php echo _l('Read more', 1); ?>" img="fad fa-monitor-heart-rate" imgdis="fad fa-monitor-heart-rate"/>'+
        '</item>'+
    <?php
        }
    }
    if (_r('MEN_TOO_SHOP_CLEAN_OPTIMIZE'))
    {
        if (defined('SC_Terminator_ACTIVE') && SC_Terminator_ACTIVE == '1')
    {    ?>
    '<item id="win_terminator" text="<?php echo _l('Shop cleaning and optimization'); ?>" img="fad fa-tachometer-alt-fastest" imgdis="fad fa-tachometer-alt-fastest"/>'+
<?php }
        else
        {
            ?>
        '<item id="teaser_terminator" text="<?php echo _l('Shop cleaning and optimization', 1); ?>" img="fad fa-tachometer-alt-fastest" imgdis="fad fa-tachometer-alt-fastest">'+
            '<item id="teaser_terminator_read" text="<?php echo _l('Read more', 1); ?>" img="fad fa-tachometer-alt-fastest" imgdis="fad fa-tachometer-alt-fastest"/>'+
        '</item>'+
    <?php
        }
    }
    ?>
        '<item id="tools_clearcookies_all" text="<?php echo _l('Clear all interface preferences', 1); ?>" img="fad fa-tools green" imgdis="fad fa-tools green"/>'+
'</item>'+
<?php } ?>
<?php
    if (KAI9DF4 != 1 && _r('MEN_E_SERVICES_LINKS')) { ?>
'<item id="eservices_group" text="<?php echo _l('e-Services', 1); ?>" img="fa fa-gem red" imgdis="fa fa-gem red">'+
    '<item id="eservices" text="<?php echo _l('e-Services list', 1); ?>" img="fa fa-gem red" imgdis="fa fa-gem red"/>'+
    '<item id="eservices_project" text="<?php echo _l('Your projects', 1); ?>" img="fa fa-cubes" imgdis="fa fa-cubes"/>'+
'</item>'+
<?php } ?>
<?php if (_r('MENU_LIN_LINKS')) { ?>
'<item id="link" text="<?php echo _l('Links', 1); ?>" img="fa fa-bolt yellow" imgdis="fa fa-bolt yellow">'+
    <?php if (SCMS)
    {
        $sql_shop = 'SELECT id_shop, name
                    FROM '._DB_PREFIX_."shop
                    WHERE deleted != '1'";
        $shops = Db::getInstance()->ExecuteS($sql_shop);
        if (!empty($shops) && count($shops) > 1)
        {
            $protocol = (version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? Tools::getShopProtocol() : (SCI::getConfigurationValue('PS_SSL_ENABLED') ? 'https://' : 'http://'));
            $shopUrls = array(); ?>'<item id="link_psfront_shops" text="<?php echo _l('Your shops', 1); ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green">'+<?php
            foreach ($shops as $shop)
            {
                $url = Db::getInstance()->ExecuteS('SELECT *, CONCAT(domain, physical_uri, virtual_uri) AS url
                    FROM '._DB_PREFIX_.'shop_url
                    WHERE id_shop = '.(int) $shop['id_shop'].'
                        AND active = "1"
                    ORDER BY main DESC
                    LIMIT 1');
                if (!empty($url[0]['url']))
                {
                    $shopUrls[$shop['id_shop']] = $protocol.$url[0]['url'];
                    $name = str_replace('&', '+', $shop['name']);
                    $name = str_replace('"', "'", $name);
                    $name = str_replace("'", "\'", $name); ?>
                    '<item id="link_psfront_shop_<?php echo $shop['id_shop']; ?>" text="<?php echo $name; ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
                    <?php
                }
            } ?>'</item>'+<?php
        }
        elseif (!empty($shops) && count($shops) == 0)
        { ?>
        '<item id="link_psfront" text="<?php echo _l('Your Shop', 1); ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
        <?php }
    }
    else
    { ?>
    '<item id="link_psfront" text="<?php echo _l('Your Shop', 1); ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
    <?php } ?>
    '<item id="link_psbo" text="<?php echo _l('PrestaShop BackOffice', 1); ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
    '<item id="link_ps" text="<?php echo _l('Visit PrestaShop.com', 1); ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
    '<item id="link_pse" text="<?php echo _l('Visit StoreCommander.com', 1); ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+

    <?php if (_r('MEN_TOO_CUSTOM_LINKS')) { ?>
        '<item id="sepTools_2" type="separator"/>'+
        <?php
        $has_link = false;
        $link = _s('TOOLS_LINK_1');
        if (!empty($link) && strpos($link, ';') !== false)
        {
            list($name, $url) = explode(';', $link);
            $has_link = true; ?>
            '<item id="tools_links_1" text="<?php echo !empty($name) ? $name : $url; ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
        <?php
        }
        $link = _s('TOOLS_LINK_2');
        if (!empty($link) && strpos($link, ';') !== false)
        {
            list($name, $url) = explode(';', $link);
            $has_link = true; ?>
            '<item id="tools_links_2" text="<?php echo !empty($name) ? $name : $url; ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
        <?php
        }
        $link = _s('TOOLS_LINK_3');
        if (!empty($link) && strpos($link, ';') !== false)
        {
            list($name, $url) = explode(';', $link);
            $has_link = true; ?>
            '<item id="tools_links_3" text="<?php echo !empty($name) ? $name : $url; ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
        <?php
        }
        $link = _s('TOOLS_LINK_4');
        if (!empty($link) && strpos($link, ';') !== false)
        {
            list($name, $url) = explode(';', $link);
            $has_link = true; ?>
            '<item id="tools_links_4" text="<?php echo !empty($name) ? $name : $url; ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
        <?php
        }

        $link = _s('TOOLS_LINK_5');
        if (!empty($link) && strpos($link, ';') !== false)
        {
            list($name, $url) = explode(';', $link);
            $has_link = true; ?>
            '<item id="tools_links_5" text="<?php echo !empty($name) ? $name : $url; ?>" img="fad fa-external-link green" imgdis="fad fa-external-link green"></item>'+
        <?php
        } ?>
        '<item id="tools_links_manage" text="<?php echo _l('Manage custom links'); ?>" img="fad fa-edit yellow" imgdis="fad fa-edit yellow"></item>'+
    <?php } ?>
'</item>'+
<?php } ?>
<?php if (_r('MENU_HEL_HELP')) { ?>
'<item id="help" text="<?php echo _l('Help', 1); ?>" img="fad fa-question-circle blue" imgdis="fad fa-question-circle blue">'+
    '<item id="help_help" text="<?php echo _l('Documentation', 1); ?>" img="fad fa-question-circle blue" imgdis="fad fa-question-circle blue"></item>'+
    '<item id="help_bug" text="<?php echo _l('Send a comment, a bug, a request', 1); ?>" img="fa fa-bug red" imgdis="fa fa-bug red"></item>'+
    <?php if (_r('INT_HELP_SC_UPDATE')) { ?>
    '<item id="help_updates" text="<?php echo _l('Update history', 1); ?>" img="fad fa-mug-hot" imgdis="fad fa-mug-hot"></item>'+
    '<item id="version" text="<?php echo _l('Update Store Commander', 1).(SC_BETA ? ' BETA' : ''); ?>" img="fad fa-sync green" imgdis="fad fa-sync green"></item>'+
    <?php } ?>
    '<item id="--456" type ="separator"></item>'+
    <?php if (_r('MENU_HEL_SC_LICENCE')) { ?>
    '<item id="help_enterlicense" text="<?php echo _l('Register your license', 1); ?>" img="fad fa-key yellow" imgdis="fad fa-key yellow"></item>'+
    <?php } ?>
    //    '<item id="help_upgradesupport" text="<?php echo _l('Extend your support and automatic updates', 1); ?>" img="fa fa-magic yellow" imgdis="fa fa-magic yellow"></item>'+
'</item>'+
<?php } ?>
<?php if ($updateAvailable && _r('INT_HELP_SC_UPDATE')){ ?>
'<item id="newversion" text="<?php echo _l('UPDATE', 1); ?>" img="fa fa-heart red">'+
    '<item id="help_updates2" text="<?php echo _l('Update history', 1); ?>" img="fad fa-mug-hot" imgdis="fad fa-mug-hot"></item>'+
    '<item id="version2" text="<?php echo _l('Update Store Commander', 1).(SC_BETA ? ' BETA' : ''); ?>" img="fad fa-sync green" imgdis="fad fa-sync green"></item>'+
'</item>'+
<?php } ?>
'</menu>';

    dhxMenu.loadStruct(XMLMenuData);

    function onMenuClick(id, zoneId, casState){
        <?php echo $menu_js_action;

    $link = _s('TOOLS_LINK_1');
    if (!empty($link) && strpos($link, ';') !== false)
    {
        list($name, $url) = explode(';', $link);
        echo 'if(id=="tools_links_1"){window.open("'.$url.'");}';
    }
    $link = _s('TOOLS_LINK_2');
    if (!empty($link) && strpos($link, ';') !== false)
    {
        list($name, $url) = explode(';', $link);
        echo 'if(id=="tools_links_2"){window.open("'.$url.'");}';
    }
    $link = _s('TOOLS_LINK_3');
    if (!empty($link) && strpos($link, ';') !== false)
    {
        list($name, $url) = explode(';', $link);
        echo 'if(id=="tools_links_3"){window.open("'.$url.'");}';
    }
    $link = _s('TOOLS_LINK_4');
    if (!empty($link) && strpos($link, ';') !== false)
    {
        list($name, $url) = explode(';', $link);
        echo 'if(id=="tools_links_4"){window.open("'.$url.'");}';
    }
    $link = _s('TOOLS_LINK_5');
    if (!empty($link) && strpos($link, ';') !== false)
    {
        list($name, $url) = explode(';', $link);
        echo 'if(id=="tools_links_5"){window.open("'.$url.'");}';
    } ?>
    }

    function clearConfigCookie(object)
    {
        if (object=='all')
        {
            if(confirm('<?php echo _l('Are you sure that you want to resert the interface preferences?', 1); ?>'))
            {
                $.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'all', "data":""},function(data){
                alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.', 1); ?>');
            });
            }
        }
    }

    dhxMenu.attachEvent("onClick",onMenuClick);
<?php
}
