<?php
if (!defined('STORE_COMMANDER'))
{
    exit;
}

use Sc\Service\Shippingbo\ShippingboService;

try
{
    $shippingboService = ShippingboService::getInstance();
    $config = $shippingboService->getConfig();

    $step = 1;
    $shippingBoApiValidConfig = $shippingboService->checkApiConfig();

    $shippingBoShopsValidConfig = $shippingboService->checkShopsConfig();
    $step = $shippingBoShopsValidConfig ? 2 : $step;

    $shippingBoImportValidConfig = $shippingBoApiValidConfig && $shippingBoShopsValidConfig && $shippingboService->checkConfig('defaultDataImport');

    $step = $shippingBoImportValidConfig ? 3 : $step;

    $shippingBoExportValidConfig = $shippingBoApiValidConfig && $shippingBoShopsValidConfig && $shippingboService->checkConfig('defaultDataExport');
    $step = $shippingBoExportValidConfig ? 4 : $step;

    $langDir = Language::getIsoById(Tools::getValue('id_lang'));
    $intro = sc_file_get_contents(__DIR__.'/content/'.$langDir.'/intro.html');
    $settingsOk = sc_file_get_contents(__DIR__.'/content/'.$langDir.'/settings_ok.html');
}
catch (Exception $e)
{
    $shippingboService->sendResponse($e->getMessage());
}
?>

<div class="html_content">
    <div id="firstStartBegin">
        <h2>
            <img class="shippingbo" src="lib/img/shippingbo/logo-shippingbo-gray-blue.svg" alt="<?php echo _l('Shippingbo'); ?>">
            <img class="sync" src="lib/img/sync.svg" alt="<?php echo _l('Sync'); ?>">
        <img  class="prestashop" src="lib/img/ps-logo-black.svg" alt="<?php echo _l('Prestashop'); ?>"></h2>

        <?php echo $intro; ?>

        <button id="first-start-go" class="dhxform_btn dhxform_btn_txt single primary"><?php echo _l('Start now!'); ?></button>

    </div>

    <div id="sboGuideSteps">
        <div class="form-step<?php echo $shippingBoApiValidConfig ? ' valid' : ''; ?>" id="api">
            <h2>1. <?php echo _l('Configure Shippingbo Access'); ?></h2>
            <div class="form-step-content" id="api_form" style="width:100%;"></div>
        </div>
        <div class="form-step<?php echo $shippingBoShopsValidConfig ? ' valid' : ''; ?>" id="shops">
            <h2>2. <?php echo _l('Shippingbo shop(s) preferences'); ?></h2>
            <div class="form-step-content" id="shops_form" style="width:100%;"></div>
        </div>
        <div class="form-step<?php echo $shippingBoImportValidConfig ? ' valid' : ''; ?>" id="import">
            <h2>3. <?php echo _l('Shippingbo import preferences'); ?></h2>
            <div class="form-step-content" id="import_form" style="width:100%;"></div>
        </div>
        <div class="form-step<?php echo $shippingBoExportValidConfig ? ' valid' : ''; ?>" id="export">
            <h2>4. <?php echo _l('Shippingbo export preferences'); ?></h2>
            <div class="form-step-content" id="export_form" style="width:100%;"></div>
        </div>


    </div>
    <div id="firstStartEnd">
        <img class="shippingbo" src="lib/img/shippingbo/checked.svg" alt="">
        <h2><?php echo ucfirst(_l("let's go")); ?> !!</h2>
        <p>
            <?php echo ucfirst(_l('Successful configuration of the Shippingbo/Prestashop connector')); ?>

        </p>
        <button class="dhxform_btn dhxform_btn_txt single"
                id="sbo_goto_dashboard"><?php echo ucfirst(_l('go to dashboard')); ?></button>
    </div>
</div>

<script>
    /* gestion du premier écran */
    document.getElementById('sboGuideSteps').classList.add('hide');
    document.getElementById('firstStartEnd').classList.add('hide');
    document.getElementById('first-start-go').addEventListener('click', function(){
        document.getElementById('firstStartBegin').classList.add('hide');
        document.getElementById('sboGuideSteps').classList.remove('hide');
    });



    displayFormSteps(<?php echo $step; ?>);


    function displayFormSteps(step){
        if(step === undefined){
            step = 1;
        }
        let currentFormStep = window.document.querySelector('#sboGuideSteps > .form-step:nth-child('+step+')');

        if(currentFormStep === null){
            document.getElementById('sboGuideSteps').classList.add('hide');
            document.getElementById('firstStartEnd').classList.remove('hide');
            document.getElementById("firstStartEnd").addEventListener("click", function () {
                $.get('index.php?ajax=1&act=cat_win-sbo_init', function (data) {
                    $('#jsExecute').html(data);
                });
            });
            return;
        }

        let sboSettingsForm = new dhtmlXForm(currentFormStep.id+'_form');

        sboSettingsForm.loadStruct('index.php?ajax=1&act=cat_win-sbo_forms_'+currentFormStep.id+'_form.json');
        sboSettingsForm.attachEvent("onButtonClick", function (name) {
            if (name === "save_"+currentFormStep.id) {
                sboSettingsForm.send("index.php?ajax=1&act=cat_win-sbo_settings_update&section="+currentFormStep.id, "post", function (xml) {
                    let response = JSON.parse(xml.xmlDoc.response);
                    let type = response.state === true ? 'sc_success' : 'error';
                    if (response.state === true) {
                        currentFormStep.classList.add('valid');
                        document.getElementById(currentFormStep.id+'_form').classList.add('hide');
                        /* permet de rouvrir le formulaire d'une étape validée */
                        document.getElementById(currentFormStep.id+'_form').previousElementSibling.addEventListener('click', function(e){
                            e.target.nextElementSibling.classList.toggle('hide');
                        });
                        displayFormSteps(step+1)
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


</script>





