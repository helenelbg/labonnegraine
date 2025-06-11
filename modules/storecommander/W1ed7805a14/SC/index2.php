<?php
$sc_alerts = array();

require_once 'init_sc.php';


if (version_compare(_PS_VERSION_, '1.7.6.0', '>='))
{
    global $kernel;
    if (empty($kernel))
    {
        require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
        $env = _PS_MODE_DEV_ ? 'dev' : 'prod';
        $debug = _PS_MODE_DEV_ ? true : false;
        $kernel = new \AppKernel($env, $debug);
        $kernel->boot();
    }
}
if (version_compare(_PS_VERSION_, '1.7.5.0', '>=')
    && method_exists('Dispatcher', 'getInstance'))
{
    Dispatcher::getInstance();
}

$sc_alerts[] = SCI::fixMyPSAlert($sc_agent);

// mode ajax
if ($ajax)
{
    if (SC_TOOLS)
    {
        foreach ($sc_tools_list as $tool)
        {
            if ($tool != 'pmcachemanager')
            {
                if (file_exists(SC_TOOLS_DIR.$tool.'/hookStartAction.php'))
                {
                    require_once SC_TOOLS_DIR.$tool.'/hookStartAction.php';
                }
            }
        }
    }

    $action = Tools::getValue('act');
    $panel = Tools::getValue('p');
    $xml = Tools::getValue('x');
    if ($xml)
    {
        $action = $xml;
    }
    if ($panel)
    {
        $action = $panel;
    }
    if ($action)
    {
        // DHTMLX4 compatibility
        if (strpos($action, '_update') !== false && isset($_POST['ids']))
        {
            $str = str_replace('.', '_', Tools::getValue('ids')).'_';
            foreach ($_POST as $k => $val)
            {
                if (strpos($k, $str) !== false)
                {
                    $_POST[substr($k, strlen($str), 1000)] = $val;
                }
            }
        }

        if ($ajax == 1)
        {
            $act = explode('_', $action);
            $overridePrefix = '';
            if (file_exists(SC_TOOLS_DIR.'lib/'.$act[0].'/'.$act[1]))
            {
                $overridePrefix = SC_TOOLS_DIR;
            }
            ob_start('cleanXMLContent');
            if (file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[0].'_'.$act[1].'_tools.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[0].'_'.$act[1].'_tools.php';
            }
            if (file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.js.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.js.php';
            }
            elseif (file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.php';
            }
            elseif (file_exists($overridePrefix.'lib/'.$act[0].'/'.$action.'.js.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$action.'.js.php';
            }
            elseif (file_exists($overridePrefix.'lib/'.$act[0].'/'.$action.'.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$action.'.php';
            }
            elseif (isset($act[2]) && file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.js.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.js.php';
            }
            elseif (isset($act[2]) && file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.php'))
            {
                require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.php';
            }
            else
            {
                $overridePrefix = '';
                if (file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[0].'_'.$act[1].'_tools.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[0].'_'.$act[1].'_tools.php';
                }
                if (file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.js.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.js.php';
                }
                elseif (file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$action.'.php';
                }
                elseif (file_exists($overridePrefix.'lib/'.$act[0].'/'.$action.'.js.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$action.'.js.php';
                }
                elseif (file_exists($overridePrefix.'lib/'.$act[0].'/'.$action.'.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$action.'.php';
                }
                elseif (isset($act[2]) && file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.js.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.js.php';
                }
                elseif (isset($act[2]) && file_exists($overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.php'))
                {
                    require_once $overridePrefix.'lib/'.$act[0].'/'.$act[1].'/'.$act[2].'/'.$action.'.php';
                }
            }
            ob_end_flush();
        }
        elseif ($ajax == 2)
        {
            require_once SC_TOOLS_DIR.$action.'.php';
        }
        exit();
    }
    if ($panel)
    { // useless but kept for old extension compatibility
        if ($ajax == 1)
        {
            require_once 'lib/panel/'.$panel.'.php';
        }
        elseif ($ajax == 2)
        {
            require_once SC_TOOLS_DIR.$panel.'.php';
        }
        exit();
    }
    if ($xml)
    { // useless but kept for old extension compatibility
        if ($ajax == 1)
        {
            require_once 'lib/xml/'.$xml.'.php';
        }
        elseif ($ajax == 2)
        {
            require_once SC_TOOLS_DIR.$xml.'.php';
        }
        exit();
    }
}
else
{
    switch (true) {
        case (bool) (isset($_GET['page']) && $_GET['page'] == 'cms_tree'):
            $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'cms';
            $SC_SHOP_CMS_PAGECOUNT = Db::getInstance()->getValue($sql);
            break;
        case (bool) (isset($_GET['page']) && $_GET['page'] == 'man_tree'):
            $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'manufacturer';
            $SC_SHOP_MANUFACTURER_COUNT = Db::getInstance()->getValue($sql);
            break;
        case (bool) (isset($_GET['page']) && $_GET['page'] == 'sup_tree'):
            $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'supplier';
            $SC_SHOP_SUPPLIER_COUNT = Db::getInstance()->getValue($sql);
            break;
        default:
            $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'product';
            $SC_SHOP_PRODUCTSCOUNT = Db::getInstance()->getValue($sql);
    }
    $notepad_icon = '<i class=\"fa fa-sticky-note\"></i>';
    $NOTEPAD_BUTTON = ' - <a style=\"color:#737373;cursor:pointer;\" onClick=\"openNotepad($(this));return false;\">'.$notepad_icon.' '._l('Notepad').'</a>';

    // mode classic
    checkDB();
    require_once SC_DIR.'lib/php/maintenance.php';
    runMaintenance();

    require_once SC_DIR.'lib/php/menu.php';
    $page = Tools::getValue('page', 'cat_tree');
    $title = ''; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <?php
        switch ($page){
            case 'cat_tree':
                $title = _l('Catalog');
                break;
            case 'cus_tree':
                $title = _l('Customers');
                break;
            case 'ord_tree':
                $title = _l('Orders');
                break;
            case 'cusm_tree':
                $title = _l('Support');
                break;
            case 'cms_tree':
                $title = _l('CMS');
                break;
            case 'man_tree':
                $title = _l('Manufacturers');
                break;
            case 'sup_tree':
                $title = _l('Suppliers');
                break;
            default:
                $title = 'Store Commander';
                break;
        }
    $shop_name = Configuration::get('PS_SHOP_NAME');
    if (!empty($shop_name))
    {
        $title .= ' - '.$shop_name;
    } ?>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?php
    $favicon = _PS_IMG_DIR_.'favicon.ico';
    if (_s('APP_USE_SC_FAVICON'))
    {
        $favicon = SC_DIR.'lib/img/sc_only_red.png';
    }
    $favicon = 'data:image/png;base64,'.base64_encode(file_get_contents($favicon)); ?>
    <link rel="icon" href="<?php echo $favicon; ?>" />
    <link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo $favicon; ?>"/>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $favicon; ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSSDHTMLX; ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSS_FONTAWESOME; ?>"/>
    <link type="text/css" rel="stylesheet" href="lib/css/jquery.autocomplete.css"/>
    <script type="text/javascript" src="<?php echo SC_JSDHTMLX; ?>"></script>
    <script type="text/javascript" src="lib/js/message.js"></script>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript" src="lib/js/jquery.cokie.js"></script>
    <script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <script type="text/javascript" src="lib/js/jquery.autocomplete.js"></script>
    <script src="https://clippingmagic.com/api/v1/ClippingMagic.js" type="text/javascript"></script>
    <script type="text/javascript">
        ClippingMagic.initialize({apiId: <?php echo CutOut::getApiId(); ?>});
    </script>
</head>
<body>
<script type="text/javascript">
    const cookiePath = '<?php echo constant('COOKIE_PATH'); ?>';
    var snapUsage = {};
    dhtmlx.message.position="top";
    const SC_MODULE_FOLDER_NAME = '<?php echo SC_MODULE_FOLDER_NAME; ?>';
    var isIPAD = (navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/Android/i)) != null || navigator.maxTouchPoints > 1;
    var mustOpenBrowserTab = <?php echo _s('APP_FORCE_OPEN_BROWSER_TAB') ? 1 : 0; ?>;
    var lightNavigation = <?php echo _s('APP_LIGHT_NAVIGATION') ? 1 : 0; ?>;
    SC_ID_LANG =<?php echo $sc_agent->id_lang; ?>;
    SC_PAGE = '<?php echo $page; ?>';
    SCMS =<?php echo SCMS ? 1 : 0; ?>;
    ISO_LANG_LIST = <?php echo json_encode(array_column($languages, 'iso_code', 'id_lang')); ?>;
    var lang_setting_disable_notice = '<?php echo _l('Do you want to disable this notice ?', 1); ?>';
    var lang_setting_disable_tip = '<?php echo _l('Do you want to disable this tip ?', 1); ?>';
    var lang_disable_tips = '<?php echo _l('Do you want to disable tips ?', 1); ?>';
    var dhxWins = new dhtmlXWindows();
    dhtmlXWindowsCell.prototype.setIcon = function (icon) {
        this.wins.w[this._idd].hdr.firstChild.style.backgroundImage = "url(" + icon + ")";
        this.wins._winAdjustTitle(this._idd);
    };

    // Allow WysiWyg cell
    var last_selected_grid = null;
    function eXcell_wysiwyg(cell) {
        if (cell) {
            this.cell = cell;
            this.grid = this.cell.parentNode.grid;
            this.edit = function(){}
            this.setValue = function(val) {
                this.setCValue(val);
            }
            this.setLabel = function(val)
            {
                this.setCTxtValue(val);
            }
        }
    }
    eXcell_wysiwyg.prototype = new eXcell;

    var loader_gif = '<i class="fas fa-spinner fa-pulse"></i>';
    $(document).ready(function () {

        <?php require_once 'start-tips-notice.php'; ?>

        document.onselectstart = new Function("return false;");
    });

    <?php if (SCMS) { ?>
    $(window).on('focus', function() {
        checkIdShopBetweenBrowserTabs();
    });
    <?php } ?>

    <?php
        UISettings::loadJS($page); ?>

    var layoutStatusText = "";
    var updateQueueLimit = '<?php
    $nb = _s('APP_UPDATEQUEUE_LIMIT');
    if (empty($nb))
    {
        $nb = 20;
    }
    echo $nb; ?>';
    const winGridEditorTitle = "<?php echo _l('Interface customization'); ?>";
    const TbInterfaceCustomizationTextLink = "<?php echo _l('Interface customization'); ?>";
    const TbSettingsTextLink = "<?php echo _l('Settings'); ?>";
    var lang_confirmclose = '<?php echo _l('Multiple actions are currently running. Do you really want to close?', 1); ?>';
    var lang_queuetasks = '<?php echo _l('Tasks', 1); ?>';
    var lang_queuetaskswindow = '<?php echo _l('Tasks error logs', 1); ?>';
    var lang_queueerror_1 = '<?php echo _l('An error has occured:', 1); ?>';
    var lang_queueerror_2 = '<?php echo _l('Check the logs to see the modification that triggered the error as well as other that could not be applied', 1); ?>';
    var lang_queueerror_3 = '<?php echo _l('See logs', 1); ?>';
    var lang_queueerror_4 = '<?php echo _l('An error has occured when inserting', 1); ?>';
    var lang_settings = '<?php echo _l('Settings', 1); ?>';
    var lang_refresh_SC = '<?php echo _l('You need to refresh Store Commander to use the new settings.', 1); ?>';
    var lang_inactive_customer = '<?php echo _l('Inactive customer'); ?>';
    <?php
        $month_full = array(_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December'));
    $month_short = array(_l('Jan'), _l('Feb'), _l('Mar'), _l('Apr'), _l('May'), _l('Jun'), _l('Jul'), _l('Aug'), _l('Sep'), _l('Oct'), _l('Nov'), _l('Dec'));
    $day_full = array(_l('Sunday'), _l('Monday'), _l('Tuesday'), _l('Wednesday'), _l('Thursday'), _l('Friday'), _l('Saturday'));
    $day_short = array(_l('Sun.'), _l('Mon.'), _l('Tue.'), _l('Wed.'), _l('Thu.'), _l('Fri.'), _l('Sat.')); ?>
    var lang_calendar = {
        dateformat: "%Y-%m-%d",
        monthesFNames: ["<?php echo implode('","', $month_full); ?>"],
        monthesSNames: ["<?php echo implode('","', $month_short); ?>"],
        daysFNames: ["<?php echo implode('","', $day_full); ?>"],
        daysSNames: ["<?php echo implode('","', $day_short); ?>"],
        weekstart: 1
    }

    var weServicesProject = null;
    function loadWindoweServicesProject(filter)
    {
        var filter_url = "";
        if(filter!=undefined && filter!=null && filter!="" && filter!=0)
            filter_url = "&filter_type="+filter;

        if (!dhxWins.isWindow('weServicesProject'))
        {
            weServicesProject = dhxWins.createWindow('weServicesProject', 50, 50, $(window).width()-75, $(window).height()-75);
            weServicesProject.setModal(1);
            weServicesProject.maximize();
            weServicesProject.denyMove();
            weServicesProject.denyResize();
            weServicesProject.denyPark();
            weServicesProject.setText('<?php echo _l('e-Services', 1).' - '._l('Managing your projects', 1); ?>');
            $.get('index.php?ajax=1&act=all_fizz_win-project_init'+filter_url,function(data){
                $('#jsExecute').html(data);
            });
        }else{
            weServicesProject.show();
            if(filter!=undefined && filter!=null && filter!="" && filter!=0)
                eSP_filterByType(filter);
        }
    }

    function checkIdShopBetweenBrowserTabs()
    {
        let current_page = "<?php echo Tools::getValue('page', 'cat_tree'); ?>";
        var cookie_selected_shop = $.cookie('sc_shop_selected');
        var current_selected_shop = 0;
        switch(current_page) {
            case 'cat_tree':
                current_selected_shop = cat_shoptree.getSelectedItemId();
                break;
            case 'ord_tree':
                current_selected_shop = ord_shoptree.getSelectedItemId();
                break;
            case 'cus_tree':
                current_selected_shop = cus_shoptree.getSelectedItemId();
                break;
            case 'cusm_tree':
                current_selected_shop = cusm_shoptree.getSelectedItemId();
                break;
            case 'cms_tree':
                current_selected_shop = cms_shoptree.getSelectedItemId();
                break;
            case 'man_tree':
                current_selected_shop = man_shoptree.getSelectedItemId();
                break;
            case 'sup_tree':
                current_selected_shop = sup_shoptree.getSelectedItemId();
                break;
        }
        if(current_selected_shop > 0) {
            if(cookie_selected_shop !== current_selected_shop) {
                dhtmlx.message({text:"<?php echo _l('Not advised: another tab is open and enabled on a different shop. Reload the page.', 1); ?>",type:'error',expire:10000});
            }
        }
    }

    function displayWarningAllShopsSelected(){
        dhtmlx.message({text: '<?php echo _l('Warning : it\'s not recommended to select "All Shops"', 1); ?><br/><br/><a href="<?php echo getScExternalLink('store_tree_work'); ?>" target="_blank"><?php echo _l('More info'); ?></a>', type: 'error', expire: 10000});
    }

    function openNotepad(btn)
    {
        if (!dhxWins.isWindow('wNotePad'))
        {
            wNotePad = dhxWins.createWindow('wNotePad', btn[0].offsetLeft, $(window).height()-400, 300, 350);
            wNotePad.setText('<?php echo _l('Notepad', 1); ?>');
            $.get('index.php?ajax=1&act=all_win-employeenotepad_init',function(data){
                $('#jsExecute').html(data);
            });
            wNotePad.attachEvent("onClose", function(win){
                win.hide();
                return false;
            });
        }else{
            wNotePad.show();
            wNotePad.bringToTop();
        }
    }
</script>
<?php

switch ($page) {
    case 'cat_tree':
        include_once 'lib/cat/cat_tree.php';
        require_once 'lib/cat/cat_grid.php';
        require_once 'lib/cat/cat_prop.php';
        if (file_exists(SC_TOOLS_DIR.'lib/cat/quicksearch/cat_quicksearch.php'))
        {
            require_once SC_TOOLS_DIR.'lib/cat/quicksearch/cat_quicksearch.php';
        }
        else
        {
            require_once 'lib/cat/quicksearch/cat_quicksearch.php';
        }
        break;
    case 'ord_tree':
        include_once 'lib/ord/ord_tree.php';
        require_once 'lib/ord/ord_grid.php';
        require_once 'lib/ord/ord_prop.php';
        break;
    case 'cus_tree':
        include_once 'lib/cus/cus_tree.php';
        require_once 'lib/cus/cus_grid.php';
        require_once 'lib/cus/cus_prop.php';
        break;
    case 'cusm_tree':
        include_once 'lib/cusm/cusm_tree.php';
        require_once 'lib/cusm/cusm_grid.php';
        require_once 'lib/cusm/cusm_prop.php';
        break;
    case 'cms_tree':
        include_once 'lib/cms/cms_tree.php';
        require_once 'lib/cms/cms_grid.php';
        require_once 'lib/cms/cms_prop.php';
        break;
    case 'man_tree':
        include_once 'lib/man/man_tree.php';
        require_once 'lib/man/man_grid.php';
        require_once 'lib/man/man_prop.php';
        break;
    case 'sup_tree':
        include_once 'lib/sup/sup_tree.php';
        require_once 'lib/sup/sup_grid.php';
        require_once 'lib/sup/sup_prop.php';
        break;
}

    require_once 'lib/all/win-quickexport/all_win-quickexport_init.js.php';
    require_once 'lib/all/win-help/all_win-help_init.js.php'; ?>
<div id="jsExecute"></div>
<?php

if (KAI9DF4 == 1)
{
    require_once 'lib/core/core_trialtime.php';
}
    else
    {
        if ((defined('OPEN_UPDATE_WINDOW') && OPEN_UPDATE_WINDOW == '1') && _r('INT_HELP_SC_UPDATE'))
        {
            ?>
        <script type="application/javascript">
            if (!dhxWins.isWindow('wCoreUpdate'))
            {
                wCoreUpdate = dhxWins.createWindow('wCoreUpdate', 50, 50, 900, $(window).height()-75);
                wCoreUpdate.setText('<?php echo _l('Store Commander update', 1); ?>');
                wCoreUpdate.attachURL('index.php?ajax=1&act=core_update');
                wCoreUpdate.setModal(true);
            }else{
                wCoreUpdate.show();
            }
        </script>
        <?php
        }
    }
    $sc_alerts = array_filter($sc_alerts);
    if (!empty($sc_alerts) && count($sc_alerts) > 0) { ?>
<script type="application/javascript">
    $( document ).ready(function() {
        dhtmlx.message({text:'<?php echo str_replace("'", "\'", implode('<br/><br/>', $sc_alerts)); ?>',type:'error',expire:-1});
    });
</script>

<?php }

    $sql = 'SELECT count(pm.id_module) 
        FROM '._DB_PREFIX_.'module AS pm INNER JOIN '._DB_PREFIX_.'hook_module AS hm ON (pm.id_module = hm.id_module) INNER JOIN '._DB_PREFIX_."hook AS h ON (hm.id_hook = h.id_hook)
        WHERE h.name = 'actionProductUpdate' AND pm.active = 1 AND (LOWER(pm.name) = 'advsearch' OR LOWER(pm.name) = 'pm_advancedsearch4' OR LOWER(pm.name) = 'shoppingflux' OR LOWER(pm.name) = 'ebay' OR LOWER(pm.name) = 'mailchimpintegration') ";
    $res = Db::getInstance()->getValue($sql);

    if (_s('CAT_NOTICE_HOOKACTIONPRODUCTUPDATE') && $res >= 1)
    {
        $message = _l('Some modules installed on your shop are misconfigured which affects Store Commander performances.', 1).' <a href="'.getScExternalLink('support_error_grid_product_update').'" target="_blank">'._l('Read more', 1)."</a><br/><a href=\"javascript:disableThisNotice(\'CAT_NOTICE_HOOKACTIONPRODUCTUPDATE\');\">"._l('Disable this notice', 1).'</a>'; ?>
    <script>
        dhtmlx.message
        ({
            type: 'error',
            text: '<?php echo $message; ?>',
            expire: -1
        });

    </script>
    <?php
    }
    if (_s('CAT_NOTICE_PSVERSIONUPDATE') && (version_compare(_PS_VERSION_, '1.7.1.0', '>=') && version_compare(_PS_VERSION_, '1.7.2.3', '<=')))
    {
        $message = _l('Your Prestashop version need to be uptaded. We strongly advise you to upgrade.', 1).' <a href="http://build.prestashop.com/news/prestashop-1-7-2-4-maintenance-release/" target="_blank">'._l('Read more', 1)."</a><br/><a href=\"javascript:disableThisNotice(\'CAT_NOTICE_PSVERSIONUPDATE\');\">"._l('Disable this notice', 1).'</a>'; ?>
    <script>
        dhtmlx.message
        ({
            type: 'error',
            text: '<?php echo $message; ?>',
            expire: 10000
        });

    </script>
    <?php
    }
    include 'lib/all/win-trends/all_win-trends_loop.php';
    if (file_exists(SC_TOOLS_DIR.'lib/php/extension/intcom.php'))
    {
        include SC_TOOLS_DIR.'lib/php/extension/intcom.php';
    }
    else
    {
        include 'lib/php/extension/intcom.php';
    } ?>
</body>
</html>
<?php
} // end mode classic
