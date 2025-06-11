<?php echo '<style>
    .dhxform_base
    {
        display: flex;
        flex-direction: column;
        row-gap: 10px;
    }
    .fs_legend
    {
        text-decoration: underline;
        font-weight: bold !important;;
    }
    
</style>';
?>

<?php echo '<script type="text/javascript">'; ?>

wScclab.setText('SCC Lab');

dhxSCCLabLayout=wScclab.attachLayout("1C");
var cellSCCLab1 = dhxSCCLabLayout.cells('a');
cellSCCLab1.setText("<?php echo _l(''); ?>");
//cellSCCLab1.setWidth(300);

var sbAuth = cellSCCLab1.attachStatusBar({text:   "<?php echo 'ID '._l('shop').' : '.SCI::getSelectedShop(); ?>", height: 35});

var formStructure = [
    {type: "fieldset",  name: "authentication", label: "<?php echo _l('Authentication'); ?>", width:"400", list:[
        {type:"input", id:"scc_auth_login", name:"scc_auth_login", value:"", label:"<?php echo _l('User'); ?> : ", labelWidth: "100"},
        {type:"password", id:"scc_auth_password", name:"scc_auth_password", value:"", label:"<?php echo _l('Password'); ?> : ", labelWidth: "100"},
        {type:"button", id:"scc_auth_submit", name:"scc_auth_submit", value:"OK"}
    ]}
];

var dhxLabForm = cellSCCLab1.attachForm(formStructure);

dhxLabForm.attachEvent("onButtonClick", function (id)
{
    if(id=='scc_auth_submit')
    {
        var u = $('[name=scc_auth_login]').val();
        var p = $('[name=scc_auth_password]').val();
        $.post("index.php?ajax=1&act=all_win-scclab_auth", {scc_auth_login:u,scc_auth_password:p},function(res) {
            if (res.success)
            { // AUTHENTICATION OK
                dhtmlx.message({text:'<?php echo _l('Authentication OK'); ?> !',type:'success',expire:3000});

                // RECUPERATION DATA API
                var id_scc_prefix_formated = res.data.id_scc_prefix.padStart(4, '0');
                var url_shop = res.data.url_shop;
                var id_shop = (typeof res.data.id_shop === "undefined" ) ? "1" : res.data.id_shop;

                //var shop_default = "<?php echo Configuration::get('PS_SHOP_DEFAULT'); ?>";
                //var id_shop = (typeof res.data.id_shop === "undefined" ) ? shop_default : res.data.id_shop;

                dhxSCCLabLayout=wScclab.attachLayout("2E");

                var cellSCCLab1 = dhxSCCLabLayout.cells('a');
                cellSCCLab1.setText("<?php echo _l('Create').' '._l('E-carte').' '._l('for').' '; ?>"+url_shop);

                var cellSCCLab2 = dhxSCCLabLayout.cells('b');
                cellSCCLab2.setText("<?php echo _l('E-carte'); ?>");

                var tbSCCLab = cellSCCLab2.attachToolbar();
                tbSCCLab.setIconset('awesome');
                tbSCCLab.addButton("exportcsv", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
                tbSCCLab.setItemToolTip("exportcsv",'<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
                tbSCCLab.addButton("exportcsvsemicolon", 0, "", "fa fa-file-csv", "fa fa-file-csv");
                tbSCCLab.setItemToolTip("exportcsvsemicolon",'<?php echo _l('Export grid to clipboard in CSV format with semicolon as delimiter.'); ?>');
                tbSCCLab.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
                tbSCCLab.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');
                tbSCCLab.attachEvent("onClick", function (id)
                {
                    if(id=='exportcsv')
                    {
                        displayQuickExportWindow(gridSCCLab,1);
                        wQuickExportWindow.bringToTop();
                        wQuickExportWindow.maximize();
                    }
                    if(id=='exportcsvsemicolon')
                    {
                        displayQuickExportWindow(gridSCCLab,1,null,null,false,";");
                        wQuickExportWindow.bringToTop();
                        wQuickExportWindow.maximize();
                    }
                    if(id=='refresh') DisplayCartRules();
                });

                var gridSCCLab=cellSCCLab2.attachGrid();

                gridSCCLab.enableSmartRendering(true);
                gridSCCLab.enableMultiselect(true);
                gridSCCLab.setImagePath("lib/js/imgs/");

                gridSCCLab.setHeader("ID <?php echo _l('Cart rule'); ?>,<?php echo _l('Code'); ?>,<?php echo _l('Amount'); ?>,<?php echo _l('Quantity'); ?>,<?php echo _l('Date from'); ?>,<?php echo _l('Date to'); ?>,ID <?php echo _l('shop'); ?>,ID SCC <?php echo _l('customer'); ?>");
                gridSCCLab.setInitWidths("100,400,100,100,250,250,0,0");
                gridSCCLab.setColAlign("right,left,right,right,left,left,left,left");
                gridSCCLab.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                gridSCCLab.setColSorting("int,str,int,int,str,str,int,str");
                gridSCCLab.attachHeader("#numeric_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,,");
                gridSCCLab.init();

                var currentlyProcessing=false;

                // FUNCTIONS
                function DisplayCartRules ()
                {
                    $.post("index.php?ajax=1&act=all_win-scclab_get_cart_rules&"+new Date().getTime(),{'cr_prefix':id_scc_prefix_formated,'cr_idshop':id_shop},function(data) {
                        if (data != '') {
                            gridSCCLab.parse(data);
                            var array_avail = JSON.parse(gridSCCLab.getUserData(0,'available'));
                            (array_avail[20]===undefined) ? $('[name=scc_nb_gift_cards_20_qty_avail]').val('0') : $('[name=scc_nb_gift_cards_20_qty_avail]').val(array_avail[20]);
                            (array_avail[30]===undefined) ? $('[name=scc_nb_gift_cards_30_qty_avail]').val('0') : $('[name=scc_nb_gift_cards_30_qty_avail]').val(array_avail[30]);
                            (array_avail[50]===undefined) ? $('[name=scc_nb_gift_cards_50_qty_avail]').val('0') : $('[name=scc_nb_gift_cards_50_qty_avail]').val(array_avail[50]);
                            (array_avail[100]===undefined) ? $('[name=scc_nb_gift_cards_100_qty_avail]').val('0') : $('[name=scc_nb_gift_cards_100_qty_avail]').val(array_avail[100]);

                            var nb = gridSCCLab.getRowsNum();
                            var ud_total = JSON.parse(gridSCCLab.getUserData(0,'total'));
                            var tot = (ud_total[0]['total'] == null) ? '0' : ud_total[0]['total'];
                            cellSCCLab2.setText("<?php echo _l('E-carte').' - '._l('Quantity').' : '; ?>"+nb+" - <?php echo _l('Total amount').' : '; ?>"+tot+' €');
                        }
                    });
                }

                function CalculateTotals ()
                {
                    $('[name=scc_nb_gift_cards_20_total]').val( Number($('[name=scc_nb_gift_cards_20_amount]').val()) * Number($('[name=scc_nb_gift_cards_20_qty]').val()) );
                    $('[name=scc_nb_gift_cards_30_total]').val( Number($('[name=scc_nb_gift_cards_30_amount]').val()) * Number($('[name=scc_nb_gift_cards_30_qty]').val()) );
                    $('[name=scc_nb_gift_cards_50_total]').val( Number($('[name=scc_nb_gift_cards_50_amount]').val()) * Number($('[name=scc_nb_gift_cards_50_qty]').val()) );
                    $('[name=scc_nb_gift_cards_100_total]').val( Number($('[name=scc_nb_gift_cards_100_amount]').val()) * Number($('[name=scc_nb_gift_cards_100_qty]').val()) );
                }

                function AfterCartRulesGeneration ()
                {
                    DisplayCartRules();
                    currentlyProcessing=false;
                    $('.btnSubmitGeneration')[0].firstChild.firstChild.innerHTML='<?php echo _l('Générer ')._l('E-cartes'); ?>';
                    $('.btnSubmitGeneration')[0].firstChild.firstChild.classList.remove("fa");
                    $('.btnSubmitGeneration')[0].firstChild.firstChild.classList.remove("fa-spin");
                    $('.btnSubmitGeneration')[0].firstChild.firstChild.classList.remove("fa-spinner");
                }

                async function sendAjaxRequestToGenerateCartRules(nbr20, nbr30, nbr50, nbr100, scc_prefix, idshop, urlshop)
                {
                    return new Promise((resolve, reject) =>
                    {
                        fetch("index.php?ajax=1&act=all_win-scclab_create_cart_rules",{
                            method: "POST",
                            body: JSON.stringify({
                                cr_qty_20: nbr20,
                                cr_qty_30: nbr30,
                                cr_qty_50: nbr50,
                                cr_qty_100: nbr100,
                                cr_id_scc: scc_prefix,
                                cr_id_shop: idshop,
                                cr_url_shop: urlshop
                            }),
                            headers: {
                                "Content-type": "application/json; charset=UTF-8"
                            }
                        })
                        .then((response) => {
                            resolve('all cartrules created');
                            return response.json();
                        }).then((data) =>
                        {
                            if(data['error']=='mail_not_sent')
                            {
                                dhtmlx.message({
                                    text: '<?php echo _l('Mail not sent ! Please send a manual CSV export.'); ?> !',
                                    type: 'error',
                                    expire: 5000
                                });
                            }
                        });
                    })
                }

                // FORM
                formStructure = [
                    {type: "fieldset",  name: "quantity", width:"80", list:[
                            {type: "label", label: "<?php echo _l('Quantity'); ?>", labelWidth: "auto", labelHeight: "30"},
                            {type:"input", id:"scc_nb_gift_cards_20_qty", name:"scc_nb_gift_cards_20_qty", value:"45", width:60},
                            {type:"input", id:"scc_nb_gift_cards_30_qty", name:"scc_nb_gift_cards_30_qty", value:"20", width:60},
                            {type:"input", id:"scc_nb_gift_cards_50_qty", name:"scc_nb_gift_cards_50_qty", value:"10", width:60},
                            {type:"input", id:"scc_nb_gift_cards_100_qty", name:"scc_nb_gift_cards_100_qty", value:"10", width:60}
                        ]}, { type:"newcolumn" },
                    {type: "fieldset",  name: "amount", width:"150", list:[
                            {type: "label", label: "<?php echo _l('Amount'); ?>", labelWidth: "auto", labelHeight: "30", offsetLeft:"20"},
                            {type:"input", id:"scc_nb_gift_cards_20_amount", name:"scc_nb_gift_cards_20_amount", value:"20", disabled:true, width:60, style:"text-align: right", label: "€", labelWidth: "20", position:"label-right"},
                            {type:"input", id:"scc_nb_gift_cards_30_amount", name:"scc_nb_gift_cards_30_amount", value:"30", disabled:true, width:60, style:"text-align: right", label: "€", labelWidth: "20", position:"label-right"},
                            {type:"input", id:"scc_nb_gift_cards_50_amount", name:"scc_nb_gift_cards_50_amount", value:"50", disabled:true, width:60, style:"text-align: right", label: "€", labelWidth: "20", position:"label-right"},
                            {type:"input", id:"scc_nb_gift_cards_100_amount", name:"scc_nb_gift_cards_100_amount", value:"100", disabled:true, width:60, style:"text-align: right", label: "€", labelWidth: "20", position:"label-right"}
                        ]}, { type:"newcolumn" },
                    {type: "fieldset",  name: "total", width:"180", list:[
                            {type: "label", label: "<?php echo _l('Total'); ?>", labelWidth: "auto", labelHeight: "30", offsetLeft:"20"},
                            {type:"input", id:"scc_nb_gift_cards_20_total", name:"scc_nb_gift_cards_20_total", value:"0", style:"text-align: center", disabled:true, width:80, label: "€", labelWidth: "20", position:"label-right"},
                            {type:"input", id:"scc_nb_gift_cards_30_total", name:"scc_nb_gift_cards_30_total", value:"0", style:"text-align: center", disabled:true, width:80, label: "€", labelWidth: "20", position:"label-right"},
                            {type:"input", id:"scc_nb_gift_cards_50_total", name:"scc_nb_gift_cards_50_total", value:"0", style:"text-align: center", disabled:true, width:80, label: "€", labelWidth: "20", position:"label-right"},
                            {type:"input", id:"scc_nb_gift_cards_100_total", name:"scc_nb_gift_cards_100_total", value:"0", style:"text-align: center", disabled:true, width:80, label: "€", labelWidth: "20", position:"label-right"}
                        ]}, { type:"newcolumn" },
                    {type: "fieldset",  name: "available", width:"180", list:[
                            {type: "label", label: "<?php echo _l('Quantity usable on the shop'); ?>", labelWidth: "auto", labelHeight: "30"},
                            {type:"input", id:"scc_nb_gift_cards_20_qty_avail", name:"scc_nb_gift_cards_20_qty_avail", value:"0", style:"text-align: center", disabled:true, width:60},
                            {type:"input", id:"scc_nb_gift_cards_30_qty_avail", name:"scc_nb_gift_cards_30_qty_avail", value:"0", style:"text-align: center", disabled:true, width:60},
                            {type:"input", id:"scc_nb_gift_cards_50_qty_avail", name:"scc_nb_gift_cards_50_qty_avail", value:"0", style:"text-align: center", disabled:true, width:60},
                            {type:"input", id:"scc_nb_gift_cards_100_qty_avail", name:"scc_nb_gift_cards_100_qty_avail", value:"0", style:"text-align: center", disabled:true, width:60}
                        ]},
                    {type:"button", id:"scc_ask_gift_cards_submit", name:"scc_ask_gift_cards_submit", value:"<?php echo _l('Générer ')._l('E-cartes'); ?>", className:"btnSubmitGeneration"}
                ];
                dhxLabForm = cellSCCLab1.attachForm(formStructure);

                DisplayCartRules();
                CalculateTotals();

                dhxLabForm.attachEvent("onChange", function (id) {
                    if (id=="scc_nb_gift_cards_20_qty" || id=="scc_nb_gift_cards_30_qty" || id=="scc_nb_gift_cards_50_qty" || id=="scc_nb_gift_cards_100_qty") CalculateTotals();
                });

                dhxLabForm.attachEvent("onButtonClick", function (id)
                {
                    if(id=='scc_ask_gift_cards_submit' && !currentlyProcessing) {

                        // BLOQUER SUBMIT BUTTON
                        currentlyProcessing = true;
                        $('.btnSubmitGeneration')[0].firstChild.firstChild.innerHTML='';
                        $('.btnSubmitGeneration')[0].firstChild.firstChild.classList.add("fa");
                        $('.btnSubmitGeneration')[0].firstChild.firstChild.classList.add("fa-spinner");
                        $('.btnSubmitGeneration')[0].firstChild.firstChild.classList.add("fa-spin");

                        var nbr_gift_card_20 = Number($('[name=scc_nb_gift_cards_20_qty]').val());
                        var nbr_gift_card_30 = Number($('[name=scc_nb_gift_cards_30_qty]').val());
                        var nbr_gift_card_50 = Number($('[name=scc_nb_gift_cards_50_qty]').val());
                        var nbr_gift_card_100 = Number($('[name=scc_nb_gift_cards_100_qty]').val());
                        if (Number.isInteger(nbr_gift_card_20) && Number.isInteger(nbr_gift_card_30) && Number.isInteger(nbr_gift_card_50) && Number.isInteger(nbr_gift_card_100))
                        {
                            // CREATION DE TOUS LES BONS 20€, 30€, 50€, 100€
                            sendAjaxRequestToGenerateCartRules(nbr_gift_card_20, nbr_gift_card_30, nbr_gift_card_50, nbr_gift_card_100, id_scc_prefix_formated, id_shop, url_shop)
                                .then(AfterCartRulesGeneration);
                        }
                        else
                        {
                            dhtmlx.message({text:'<?php echo _l('Format error (integers needed)'); ?> !',type:'error',expire:3000});
                        }
                    }
                })
            }
            else
            {
                dhtmlx.message({text:'<?php echo _l('Authentication failed'); ?> !',type:'error',expire:3000});
            }
        },'json')
    }
})
<?php echo '</script>'; ?>