<?php
if (!defined('STORE_COMMANDER'))
{
    exit;
}
use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$config = $shippingBoService->getConfig();
?>

<?php echo '<script>'; ?>


const wSboTabSettings = wSboTabBar.tabs('settings');
const wSboTabSettingsLayout = wSboTabSettings.attachLayout("2U");



const wSboTabMenuSettings_cell = wSboTabSettingsLayout.cells('a');
wSboTabMenuSettings_cell.setText("<?php echo _l('Settings', 1); ?>");
wSboTabMenuSettings_cell.setWidth(150);
wSboTabMenuSettings_cell.hideHeader();
wSboTabMenuSettings_cell.collapsable = false;
wSboTabMenuSettings_cell.cell.classList.add('service');


var SboTabMenuSettingsMenuStructure = '<ul class="sboSettingsMenu">' +
    '<li><a id="api" class="active" href="#" ><?php echo _l('API', 1); ?></a></li>'+
    '<li><a id="shops" href="#" ><?php echo _l('Shops', 1); ?></a></li>'+
    '<li><a id="import" href="#" ><?php echo _l('Imports', 1); ?></a></li>'+
    '<li><a id="export" href="#" ><?php echo _l('Exports', 1); ?></a></li>';

<?php if ($shippingBoService->getScAgent()->getIdProfile() == 1){ ?>
SboTabMenuSettingsMenuStructure += '<li><a id="advanced" href="#" ><?php echo _l('Advanced', 1); ?></a></li>'+
    '<li><a id="logs" href="#" ><?php echo _l('Debug', 1); ?></a></li>'+
    '</ul>';
;
<?php } ?>
const SboTabMenuSettingsMenu = wSboTabMenuSettings_cell.attachHTMLString(SboTabMenuSettingsMenuStructure);


let menuLinks = wSboTabMenuSettings_cell.cell.querySelectorAll('a');
//menuLinks.0.trigger

for (var i = 0; i < menuLinks.length; i++) {
    menuLinks[i].addEventListener('click', function(event) {
            event.preventDefault();
            event.target.parentNode.parentNode.querySelector('a.active').classList.remove('active');
            event.target.classList.add('active');
            $.ajax({
                'url': 'index.php?ajax=1&act=cat_win-sbo_forms_'+event.target.id+'_form.json',
                'type': 'GET',
                'success': function (data) {
                    displaySboSettingsForm(data,event.target.id );
                }
            });


    });
}
const wSboTabFormSettings_cell = wSboTabSettingsLayout.cells('b');
wSboTabFormSettings_cell.hideHeader();
wSboTabFormSettings_cell.cell.classList.add('service');


function displaySboSettingsForm(data, section) {
    const wSboSettingsLayout_form = wSboTabFormSettings_cell.attachForm(JSON.parse(data));
    //wSboSettingsLayout_form.adjustParentSize();
    wSboSettingsLayout_form.attachEvent("onButtonClick", function (name, command) {
        if (name == "save_"+section) {
            wSboTabFormSettings_cell.progressOn();
            wSboSettingsLayout_form.send("index.php?ajax=1&act=cat_win-sbo_settings_update&section="+section, "post", function (xml) {
                wSboTabFormSettings_cell.progressOff();
                let response = JSON.parse(xml.xmlDoc.response);
                let type = response.state === true ? 'sc_success' : 'error';
                let callback = response.extra.callback;
                window.localStorage.removeItem('sbo_sync');
                if(callback && callback.functionName){
                    if(typeof callback.functionName === 'function' || typeof callback.functionName === 'string') {
                       executeFunctionByName(callback.functionName, window, callback.params);
                    }
                }
                dhtmlx.message({
                    text: response.extra.message,
                    type: type,
                    expire: 7000
                });
            });
        }
    });
}

function executeFunctionByName(functionName, context , arguments ) {
    //var args = Array.prototype.slice.call(arguments, 2);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for(var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return context[func].apply(context, arguments);
}

// init
wSboTabMenuSettings_cell.cell.querySelector('a.active').click();
<?php echo '</script>'; ?>