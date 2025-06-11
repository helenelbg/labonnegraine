<div id="controlCommande">
    {if !isset($order)}
        <div class="panel panel-info">
            <div class="panel-heading">
                <i class="icon-barcode" ></i>{l s='Contrôle commande'  mod="controlecommande"}
            </div>
            <div class='panel-body'>
                <form method="POST" class="form-inline" id='searchOrder'>
                        <input type="text" name="order" placeholder="{l s='Code barre' mod='controlecommande'}" aria-describedby="scan_order" id="scan_order" class="scan_input">
                        <button onclick="$('#searchOrder').submit();" name='submitSearchOrder' value='{l s='Rechercher' mod='controlecommande'}'  class='btn btn-primary'>{l s='OK' mod='controlecommande'}</button>
                </form>
            </div>
            <div class="panel-footer">
                {*<div class='col-md-6 col-lg-6 col-sm-6'>
                    <a href="{$link->getAdminLink('AdminControleCommande')}&amp;showControls" class="btn btn-primary">
                        {l s='Voir les contrôles' mod="controlecommande"}
                    </a>
                </div>*}
                <div id="pdfToPrint" style='display:none;' class='col-md-6 col-lg-6 col-sm-6'>
                    {l s='Commande validée : ' mod='controlecommande'}
                    {if $printpdf && !empty($orderToPrint)}

                        {Order::printPDFIcons($orderToPrint->id)}
                    {/if}
                    {if $printproformat}
                       <a href="{$link->getAdminLink('AdminControleCommande')}&amp;generateProFormat&id_order={$orderToPrint->id}" target="_blank" class="btn btn-primary"  id="generate_proformat">{l s='Pro forma'  mod='controlecommande'}</a>
                    {/if}
                </div>
            </div>
        </div>
        <script>
            var printLabel = '{$printLabel}';
            var printproformat = '{$printproformat}';
            {if ($orderToPrint)}
            var orderToPrint = '{$orderToPrint->id}_{$orderToPrint->id_carrier}';
            {else}
            var orderToPrint = '';
            {/if}
                var messageNoLabel = "{l s='Aucune étiquette disponible' mod="controlecommande"}";
            {literal}
                $(document).ready(function()
                {
                    $('#scan_order').focus();
                    if(printproformat == '1' && $('#generate_proformat').length > 0 )
                    {
                     //   $('#generate_proformat')[0].click();
                        //var href_proformat = $('#generate_proformat')[0].attr('href');
                        //window.open(href_proformat);
                  //      window.console.log('click pro format');
                    }
                        window.console.log('printLabel: '+printLabel);
                            window.console.log('orderToPrint: '+orderToPrint);
                                window.console.log('length: '+$('#pdfToPrint #label_' + orderToPrint).length);
                    if (printLabel == '1' && $('#pdfToPrint #label_' + orderToPrint).length > 0)
                    {
                        $('#pdfToPrint').show();
                        if ($('#pdfToPrint #label_' + orderToPrint).length > 0)
                        {
                            window.console.log('printpdf');
                            $('#pdfToPrint #label_' + orderToPrint)[0].click();
                            //var href_label = $('#pdfToPrint #label_' + orderToPrint)[0].attr('href');
                            //var href_label = $('#pdfToPrint #label_' + orderToPrint)[0].attr('href');
                            //window.open(href_label);
                            window.console.log($('#pdfToPrint #label_' + orderToPrint));
                        }
                        else
                        {
                            $('#controlCommande').prepend('<div class="alert alert-warning">' + messageNoLabel + '</div>');
                        }
                    }
                });
            {/literal}
        </script>
        {if !empty($ordersToPrepare)}
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4>{l s='Commande à contrôler'  mod="controlecommande"} <span class="badge">{$ordersToPrepare|count}</span></h4>
                </div>
                <div class='panel-body'>
                    <table id="order_to_control" class="table table-hover table-responsive table-striped">
                        <thead>
                        <tr>
                            <th>{l s='ID'  mod="controlecommande"}</th>
                            <th>{l s='Référence'  mod="controlecommande"}</th>
                            <th>{l s='Transporteur'  mod="controlecommande"}</th>
                            <th>{l s='Client'  mod="controlecommande"}</th>
                            <th>{l s='Date'  mod="controlecommande"}</th >
                            <th>{l s='Actions'  mod="controlecommande"}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $ordersToPrepare as $order}
                            <tr id="order_{$order->id}">
                                <td>{$order->id}</td>
                                <td>{$order->reference}</td>
                                <td>{$order->carrier->name}</td>
                                <td>{$order->customer->lastname} {$order->customer->firstname} ({$order->customer->email})</td>
                                <td>{$order->date_add}</td>
                                <td>
                                  {*  <a class="btn btn-info btn-lg" href="{$link->getAdminLink('AdminOrders')}&amp;vieworder&amp;id_order={$order->id}" target="_blank">
                                        {l s='Voir'}
                                    </a>*}

                                    <a class="btn btn-primary btn-lg" href="{$link->getAdminLink('AdminControleCommande')}&amp;submitSearchOrder&amp;order={$order->id}">
                                        {l s='Contrôler'}
                                    </a>
                                    <span class="btn-list-order">

                                    </span>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <script>

$(document).ready(function()
{
    $('#order_to_control').tablesorter(
            {
                    widgets: ['filter', 'columns']

                });
});
                    </script>
                </div>
            </div>
        {else}
            {*<div class="alert alert-warning">{l s='Aucune commande'}</div>*}
        {/if}
    {else}
        <form method="POST" class="form-inline well col-md-12 panel panel-info" id="searchProducts" onsubmit="return false;">

            <div class="panel-heading">
                <div class="info_com">
                    <div class="sub_info_com">
                        <b>{l s='Commande :'  mod="controlecommande"} {$order->id} - {$order->reference} - {$carrier->name}</b>
                        <span class='badge badge-primary total_prepared' style="background-color: #72c279;">{$total_prepared}</span> / <span class='badge badge-danger total_ordered' style="background-color: #e08f95;">{$total_ordered}</span>
                        <div class="clear"></div>
                        <a class="btn btn-default" id="see_order" href="{$link->getAdminLink('AdminOrders')}&amp;vieworder&amp;id_order={$order->id}" target="_blank">Voir la commande</a>
                        <div class="poste_controle">
                        <input type="radio" id="poste_controle1" name="poste_controle" value="1" checked>
                        <label for="poste_controle">Contrôle 1</label>
                        </div>
                        <div class="poste_controle">
                        <input type="radio" id="poste_controle2" class="poste_controle" name="poste_controle" value="2">
                        <label for="poste_controle">Contrôle 2</label>
                        </div>
                    </div>
                    <div class="button_com">
                        {*if $printpdf}
                            <span id="pdfIconOrder" class="" style="display:none;">
                                {Order::printPDFIcons($order->id)}
                            </span>
                        {/if*}
                        <input type="hidden" name="id_order_control" value="{$id_order_control}" id="id_order_control"/>
                        <input type="hidden" name="id_order" value="{$order->id}" id="id_order"/>
                    </div>
                </div>
                <div class="block_scan">
                    <input type="text" name="product" id="product_barcode" placeholder="{l s='Code barre du produit' mod='controlecommande'}" aria-describedby="scan_product">
                    <button onclick="searchProduct();" name='submitLoadProduct' class='btn btn-primary'>{l s='OK' mod='controlecommande'}</button>
                </div>
            </div>

            <div class='panel-body'>
                <div id="errors_container"></div>
                <div id='producttoprepare'>
                    {foreach $products as $product}
                        {if $product.control_valid == 0}
                            {assign var="productToShow" value=$product.product_quantity-$product.quantity_prepared}
                            {for $nb_quantity=1 to $productToShow}
                                {include file='productView.tpl'}
                            {/for}
                        {/if}
                    {/foreach}
                </div>
                <div>
                    <button class="btn btn-primary" type="button" onclick="$('#collapseDetailsControl').slideToggle();">
                        {l s='Détail du contrôle'  mod='controlecommande'} - {l s='Total contrôlé / Total commandé   '  mod='controlecommande'} - 
                        <span class='badge badge-primary total_prepared' style="background-color: #72c279;">{$total_prepared}</span> / <span class='badge badge-danger total_ordered'>{$total_ordered}</span>
                    </button>
                    <div class="collapse" id="collapseDetailsControl">
                        {if $total_prepared > 0}
                            {foreach $products as $product}
                                {if $product.quantity_prepared > 0}
                                    {for $nb_quantity=1 to $product.quantity_prepared}
                                        {include file='productView.tpl'}
                                    {/for}
                                {/if}
                            {/foreach}
                        {/if}
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                  <div class="bg-warning" style="display:none" id="delivery_address">
                    <b>{$address_delivery->alias}</b><br/>
                    {if $address_delivery->company}
                        {$address_delivery->company},<br/> 
                    {/if}
                    {$address_delivery->lastname} {$address_delivery->firstname}<br/>
                    {if $address_delivery->phone}
                        {$address_delivery->phone}<br/>
                    {/if}
                    {if $address_delivery->phone_mobile}
                        {$address_delivery->phone_mobile}<br/>
                    {/if}
                    {$address_delivery->address1} {$address_delivery->address2}, {$address_delivery->postcode} {$address_delivery->city}
                </div>
                <div class="center-block">
{*                    <a href='{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}&amp;restOnControl' class='btn btn-primary btn-lg'>*}
                    <a onclick="saveAndRest()" class='btn btn-primary btn-lg'>
                        {* <i class="icon-save" ></i>*} {l s='Enregistrer et rester' mod='controlecommande'}
                    </a>
{*                    <a href='{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}' class='btn btn-info btn-lg'>*}
                    <a onclick="saveAndNext()" class='btn btn-info btn-lg'>
                        {* <i class="icon-save" ></i>*} {l s='Enregistrer et Suivant' mod='controlecommande'}
                    </a>

                    {if $carrier->id_reference == 348 || $carrier->id_reference == 142 || $carrier->id_reference == 189 || $carrier->id_reference == 190 || $carrier->id_reference == 191 || $carrier->id_reference == 192 || $carrier->id_reference == 112}
                        <input type="hidden" id="url_col"  value="{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}&amp;printLabel"/>
                        <input type="hidden" id="token_etiquetage" value="{$token_etiquetage}">
                        <a class='btn btn-success btn-lg etq_col'>
                            {* <i class="icon-save" ></i>*} {l s='Enregistrer et Etiquette (Col)' mod='controlecommande'}
                        </a>
                    {elseif $carrier->id_reference == 155 || $carrier->id_reference == 193}
                        <a class='btn btn-success btn-lg etq_dpd'>
                            {* <i class="icon-save" ></i>*} {l s='Enregistrer et Etiquette (DPD)' mod='controlecommande'}
                        </a>
                    {elseif $carrier->id_reference == 390}
                        <input type="hidden" id="url_col"  value="{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}&amp;printLabel"/>
                        <a class='btn btn-success btn-lg etq_cac'>
                            {* <i class="icon-save" ></i>*} {l s='Enregistrer et Etiquette (Click & Collect)' mod='controlecommande'}
                        </a>
                    {else}
                    {if $printpdf}
                        <a href='{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}&amp;printLabel' class='btn btn-success btn-lg'>
                            {* <i class="icon-save" ></i>*} {l s='Enregistrer et Etiquette' mod='controlecommande'}
                        </a>
                    {/if}
                    {/if}
                    <a href='{$link->getAdminLink('AdminControleCommande')}&amp;cancelControl&amp;id_order_control={$id_order_control}' class='btn btn-danger btn-lg '>
                        {* <i class="icon-rotate-left" ></i>*} {l s='Annuler' mod='controlecommande'}
                    </a>
                </div>
            </div>

        </form>
        <div id="errorColissimo"></div>
        <script>

            var eans = '{$eans}';
            eans = JSON.parse(eans);

            $(document).ready(function()
            {
                $('input:first-child').focus();

                $('.prepare_product').on('click', function()
                {
                    success_control($(this).parents(".product_prepare_container"));
                });

                $(".iframe").fancybox({
                    'width': '75%',
                    'height': '75%',
                    'autoScale': false,
                    'transitionIn': 'none',
                    'transitionOut': 'none',
                    'type': 'iframe'
                });
            });

            function searchProduct()
            {
                var item = $('#product_barcode');
                var barcode = item.val();

                if(barcode.length === 0){
                    return;
                }

                if(eans[barcode]){
                    var div_to_move = $('#producttoprepare .product_' + eans[barcode]);

                    if(div_to_move.length > 0){
                        success_control(div_to_move.first());
                    }else{
                        error_control("<b>Echec du contrôle</b><br>Nombre maximum pour ce produit atteint", $('#collapseDetailsControl .product_' + eans[barcode]).first());
                    }
                }else{
                    error_control("<b>Echec du contrôle</b><br>Ce produit n'est pas dans la commande", null, barcode);
                }

                item.val("");
                $('input:first-child').focus();
            }

            async function success_control(div_to_move){
                $('#collapseDetailsControl').append(div_to_move);

                var message = $('<div class="alert alert-success"><b>Produit contrôlé</b><br>' + div_to_move.find(".text-product-control").html() + '</div>');

                $('#errors_container').append(message);

                setTimeout(function(){
                    message.remove();
                }, 2000);

                var total_prepared = parseInt($('.total_prepared').first().text()) + 1;
                $('.total_prepared').text(total_prepared);

                var total_ordered = parseInt($('.total_ordered').html());

                if (total_ordered === total_prepared)
                {
                    $("body").append('<div class="loader_control" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #00000070; display: flex; align-items: center; justify-content: center; z-index: 9999;"><svg style="margin: auto; background: transparent; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" r="32" stroke-width="8" stroke="#ffffff" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">  <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"/></circle></svg></div>');

                    try{
                        var data = await saveControleToDB();
                    }catch (e) {
                        console.error(e);
                        alert("Impossible de sauvegarder les informations dans la BDD");
                        var data = null;
                    }

                    if(data !== null && data.errors.length === 0){
                        $(".loader_control").remove();

                        if ( $('.etq_col').length > 0 )
                        {
                            $('.etq_col').click();
                        }
                        else if ( $('.etq_dpd').length > 0 )
                        {
                            alert('Contrôle de commande ok. Vous pouvez imprimer votre étiquette DPD.');
                        }

                        $('#pdfIconOrder').slideToggle();
                        $('.total_prepared').removeClass('badge-primary').addClass('badge-success');
                    }
                }
            }

            async function error_control(msg, div = null, barcode = null){
                var audioElement = document.createElement("audio");

                audioElement.setAttribute("src", "https://dev.labonnegraine.com/modules/controlecommande/erreur4.mp3");
                audioElement.setAttribute("autoplay:false", "autoplay");
                audioElement.play();

                if(div === null){
                    var itemError = $('<div class="alert alert-danger">' + msg + '</div>');
                }else{
                    var itemError = $('<div class="alert alert-danger">' + msg + "<br>" + div.find(".text-product-control").html() + '</div>');
                }

                itemError.append('<button onclick="$(this).parent().remove()"><svg fill="#000000" height="10px" width="10px" version="1.1" id="Capa_1" viewBox="0 0 490 490" xml:space="preserve">                    <polygon points="456.851,0 245,212.564 33.149,0 0.708,32.337 212.669,245.004 0.708,457.678 33.149,490 245,277.443 456.851,490   489.292,457.678 277.331,245.004 489.292,32.337 "/></svg></button>');

                itemError.find("button").css({
                    "position": "absolute",
                    "top": "10px",
                    "right": "10px",
                    "border-radius": "100%",
                    "aspect-ratio": "1",
                    "width": "30px",
                    "background": "transparent",
                    "border": "solid 1px #f44336"
                })

                $('#errors_container').append(itemError);

                if(barcode !== null){
                    itemError.append(" : "+barcode);

                    try{
                        var data = await searchProductError(barcode);

                        if(data.errors.length > 0){
                            itemError.append("<br>Le produit n'existe pas");
                        }else{
                            itemError.append("<br>Le produit est : "+data.product);
                        }
                    }catch (e) {
                       console.error(e);
                    }
                }
            }

            function saveControleToDB(){

                var array_order_control = [];

                $("#collapseDetailsControl .product_prepare_container").each(function(){
                    var classe = $(this).attr("class");

                    classe = classe.replace("product_prepare_container", "");
                    classe = classe.replace(" ", "");
                    classe = classe.replace("product_", "");

                    classe = classe.split("_");

                    {literal}
                        array_order_control.push({id_product: classe[0], id_attr: classe[1]})
                    {/literal}
                });

                return $.ajax({
                    type: "POST",
                    url: "{$link->getAdminLink('AdminControleCommande')|addslashes}",
                    async: true,
                    dataType: "json",
                    data: {
                        ajax: "1",
                        token: "{getAdminToken tab='AdminControleCommande'}",
                        tab: "AdminControleCommande",
                        updateAllControl: "1",
                        id_order_control: $('#id_order_control').val(),
                        array_order_control: array_order_control
                    }
                });
            }

            function searchProductError(cb){
                return $.ajax({
                    type: "POST",
                    url: "{$link->getAdminLink('AdminControleCommande')|addslashes}",
                    async: true,
                    dataType: "json",
                    data: {
                        ajax: "1",
                        token: "{getAdminToken tab='AdminControleCommande'}",
                        tab: "AdminControleCommande",
                        searchProductError: "1",
                        barcode: cb
                    }
                });
            }

            async function saveAndRest(){
                $("body").append('<div class="loader_control" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #00000070; display: flex; align-items: center; justify-content: center; z-index: 9999;"><svg style="margin: auto; background: transparent; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" r="32" stroke-width="8" stroke="#ffffff" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">  <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"/></circle></svg></div>');

                try{
                    var data = await saveControleToDB();
                }catch (e) {
                    console.error(e);
                    alert("Impossible de sauvegarder les informations dans la BDD");
                    var data = null;
                }

                if(data !== null && data.errors.length === 0){
                    $(".loader_control").remove();
                    window.location.reload(true);
                }
            }

            async function saveAndNext(){
                $("body").append('<div class="loader_control" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #00000070; display: flex; align-items: center; justify-content: center; z-index: 9999;"><svg style="margin: auto; background: transparent; display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" r="32" stroke-width="8" stroke="#ffffff" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">  <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0 50 50;360 50 50"/></circle></svg></div>');

                try{
                    var data = await saveControleToDB();
                }catch (e) {
                    console.error(e);
                    alert("Impossible de sauvegarder les informations dans la BDD");
                    var data = null;
                }

                if(data !== null && data.errors.length === 0){
                    $(".loader_control").remove();
                    window.location.href = "/admin123/index.php?controller=AdminControleCommande";
                }
            }

        </script>
    {/if}
</div>
