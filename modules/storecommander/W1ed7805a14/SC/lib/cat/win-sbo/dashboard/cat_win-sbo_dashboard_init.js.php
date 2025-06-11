<?php if (!defined('STORE_COMMANDER'))
{
    exit;
}
use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();
$sync = Tools::getValue('sync', 'false');
$activePreviewPlatform = Tools::getValue('platform', null);
$activePreviewSboType = Tools::getValue('sboType', null);

?>

<?php echo '<script>'; ?>

const wSboTabDashboard = wSboTabBar.tabs('dashboard');
const wSboTabDashboardGlobalLayout = wSboTabDashboard.attachLayout("1C");
const wSboTabDashboardMainLayout = wSboTabDashboardGlobalLayout.cells('a').attachLayout("1C");

/**
 * Stats
 */
const wSboTabDashboardSync_cell = wSboTabDashboardMainLayout.cells('a');
wSboTabDashboardSync_cell.hideHeader();
wSboTabDashboardSync_cell.fixSize(true, true);
wSboTabDashboardSync_cell.cell.classList.add('service');
wSboTabDashboardSync_cell.attachURL('index.php?ajax=1&act=cat_win-sbo_dashboard_get&sync=<?php echo $sync; ?>', true);



/* EVENTS */
wSboTabDashboardMainLayout.attachEvent("onContentLoaded", function () {

    // preview content toggles 'active' class on blocks
    const activePreviewPlatform = '<?php echo $activePreviewPlatform; ?>';
    const activePreviewSboType = '<?php echo $activePreviewSboType; ?>';
    if(activePreviewPlatform !== '' && activePreviewSboType !== ''){
        window.document.querySelector('.platform.'+activePreviewPlatform+' .'+activePreviewSboType).classList.add('active');
    }

    // SYNC PROCESS
    let progressDomSelector = '.sbo-dashboard .process';
    let url = "index.php?ajax=1&act=cat_win-sbo_process";
    // getting info of last sse interruption
    let sboSync = JSON.parse(window.localStorage.getItem('sbo_sync'));
    if(!!sboSync){
        if(sboSync.processId){
            url+='&start_process='+sboSync.processId;
        }
        if(sboSync.processIteration){
            url+='&start_iteration='+sboSync.processIteration;
        }
    }
    // refresh button event
    let refresh = document.querySelector(progressDomSelector+' .refresh');
    refresh.addEventListener("click", function(){startAsyncProcess(url,progressDomSelector)});
    // tab load
    if (new URLSearchParams(wSboTabDashboardSync_cell.conf.url_data.url).get('sync') === 'true') {
        startAsyncProcess(url,progressDomSelector);// TODO use Promise
    } else {
        fetchSboStats()
    }
});


function startAsyncProcess(url,progressDomSelector) {
    //let progressElement = document.querySelector(progressDomSelector);
    let progressElement = new SboProgressBar(progressDomSelector);
    window.document.querySelector('.sbo-dashboard').classList.add('sync_in_progress') ;
    wSbo._evtSource = new EventSource(url);
    // 1. écoute des événements envoyés sur le canal "sbo_sync"
    wSbo._evtSource.addEventListener("process_started", function (event) {
        // avancement progress bar
        let data = JSON.parse(event.data);
        window.localStorage.setItem('sbo_sync',JSON.stringify(data)); // save current to localStorage
        let positive = Math.round(data.stepProgress);
        let negative = 100-positive;
        progressElement.positive.style.width = positive+'%';
        progressElement.positiveText.textContent = positive+'%';
        progressElement.negative.style.width =negative+'%';
        progressElement.negativeText.textContent = positive+'%';
        progressElement.text.textContent = data.message;
    });
    // 2. écoute événement sur le canal "done"
    wSbo._evtSource.addEventListener("done", function () {
        // init progress bar
        progressElement.classList.remove('in_progress');
        progressElement.classList.add('success');
        progressElement.stepName.textContent = '';
        progressElement.positive.style.width = '0';
        progressElement.positiveText.textContent = '0%';
        progressElement.negative.style.width ='100%';
        progressElement.negativeText.textContent = '0%';
        progressElement.text.textContent = "<?php echo _l('Synchronization complete', 1); ?>";
        window.localStorage.removeItem('sbo_sync');
        // close eventSource
        wSbo._evtSource.close();
        wSbo._evtSource = null;
        // reactivate interactions on dashboard
        window.document.querySelector('.sbo-dashboard').classList.remove('sync_in_progress') ;
        fetchSboStats()
    });
}

function fetchSboStats(){
    $.ajax({
        'url': 'index.php?ajax=1&act=cat_win-sbo_dashboard_stats.json',
        'type': 'GET',
        'beforeSend':  function () {
            for (let placeholder of window.document.querySelectorAll('[data-placeholder]')){
                placeholder.classList.add('loading');
                placeholder.textContent='';
            }
        },
        'success': function (data) {
            for (let placeholder of window.document.querySelectorAll('[data-placeholder],[data-classplaceholder]')){
                let currentData = data.extra;
                if(placeholder.dataset.classplaceholder !== undefined){
                    placeholder.classList.add('status_'+placeholder.dataset.classplaceholder.split('.').reduce((o, k) => ( currentData = currentData[k]),  currentData));
                } else {
                    placeholder.classList.remove('loading');
                    placeholder.textContent = placeholder.dataset.placeholder.split('.').reduce((o, k) => ( currentData = currentData[k]),  currentData);
                }
            }
        }
    });
}

function SboProgressBar(wrapperSelector) {
    let progressElement = document.querySelector(wrapperSelector);
    progressElement.classList.remove('success');
    progressElement.classList.add('in_progress');
    if(progressElement.state !== true){ // check whether node has progress properties, so create it
        progressElement.icon =  progressElement.querySelector('.progress .icon');
        progressElement.stepName =  progressElement.querySelector('.progress .stepName');
        progressElement.text =  progressElement.querySelector('.progress .text');
        progressElement.positive =  progressElement.querySelector('.progress .positive');
        progressElement.positiveText =  progressElement.positive.querySelector('span');
        progressElement.negative =  progressElement.querySelector('.progress .negative');
        progressElement.negativeText =  progressElement.negative.querySelector('span');
        progressElement.state = true;
    }
    //progressElement.text.textContent = '';
    progressElement.text.textContent = progressElement.dataset.starttext;
    return progressElement;
}


<?php echo '</script>'; ?>