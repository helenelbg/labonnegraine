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
                    <a href='{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}&amp;restOnControl' class='btn btn-primary btn-lg'>
                        {* <i class="icon-save" ></i>*} {l s='Enregistrer et rester' mod='controlecommande'}
                    </a>
                    <a href='{$link->getAdminLink('AdminControleCommande')}&amp;saveControl&amp;id_order_control={$id_order_control}' class='btn btn-info btn-lg'>
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

            // setResponsive();
            $(document).ready(function()
            {
                $('input:first-child').focus();

                checkPrepared();

                $('.prepare_product').on('click', function()
                {
                    var id_order_control = $('#id_order_control').val();
                    var product_identifiants = $(this).attr('id').split("_");
                    var product_id = product_identifiants[0];
                    var product_attribute_id = product_identifiants[1];
                    var num_quantity = product_identifiants[2];

                    saveControlProduct(id_order_control, product_id, product_attribute_id);
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
                var barcode = $('#product_barcode').val();
                var id_order_control = $('#id_order_control').val();

                if (barcode.length > 0)
                {
                    $.ajax({
                        type: "POST",
                        url: "{$link->getAdminLink('AdminControleCommande')|addslashes}",
                        async: true,
                        dataType: "json",
                        data: {
                            ajax: "1",
                            token: "{getAdminToken tab='AdminControleCommande'}",
                            tab: "AdminControleCommande",
                            searchProduct: "1",
                            barcode: barcode,
                            id_order_control: id_order_control
                        },
                        success: function(res)
                        {
            {*                        console.log(res);*}
                            if (res.errors.length > 0)
                            {
                              var audioElement = document.createElement("audio");
                              audioElement.setAttribute("src", "https://dev.labonnegraine.com/modules/controlecommande/erreur4.mp3");
                              audioElement.setAttribute("autoplay:false", "autoplay");
                              audioElement.play();
                                $('#errors_container').html('<div class="alert alert-danger">' + res.errors[0] + '</div>');
                            }
                            else if (res.product.id_product > 0)
                            {
                                saveControlProduct(id_order_control, res.product.id_product, res.product.id_product_attribute);
                            }
                        }
                    });
                }
                $('#product_barcode').val('');

                return false;
            }

            function checkPrepared()
            {
              console.log('checkPrepared');
                var total_prepared = parseInt($('.total_prepared').first().text());
                var total_ordered = parseInt($('.total_ordered').first().text());
                window.console.log(total_prepared);
                window.console.log(total_ordered);
                if (total_ordered == total_prepared)
                {
                    $('#pdfIconOrder').show();
                    $('#delivery_address').show();
                    $('.total_prepared').removeClass('badge-primary').addClass('badge-success');
                }
            }

            function saveControlProduct(id_order_control, id_product, id_product_attribute)
            {
                $.ajax({
                    type: "POST",
                    url: "{$link->getAdminLink('AdminControleCommande')|addslashes}",
                    async: true,
                    dataType: "json",
                    data: {
                        ajax: "1",
                        token: "{getAdminToken tab='AdminControleCommande'}",
                        tab: "AdminControleCommande",
                        updateControl: "1",
                        id_order_control: id_order_control,
                        id_product: id_product,
                        id_product_attribute: id_product_attribute,
                    },
                    success: function(res)
                    {
            {*                    window.console.log(res.length);*}
              console.log('l1');
                        if (res.errors.length > 0)
                        {
                          console.log('l2');

                          var audioElement = document.createElement("audio");
                          audioElement.setAttribute("src", "https://dev.labonnegraine.com/modules/controlecommande/erreur4.mp3");
                          audioElement.setAttribute("autoplay:false", "autoplay");
                          audioElement.play();
                            $('#errors_container').html('<div class="alert alert-danger">' + res.errors[0] + '</div>');
                        }
                        else if (res.success.length > 0)
                        {
                          console.log('l3');
                            $('#errors_container').html('<div class="alert alert-success">' + res.success[0] + '</div>');
                            var div_to_move = $('#producttoprepare .product_' + id_product + '_' + id_product_attribute).first();
                            $('#collapseDetailsControl').append(div_to_move);
                            var total_prepared = parseInt($('.total_prepared').first().text()) + 1;
                            $('.total_prepared').text(total_prepared);

                            var total_ordered = parseInt($('.total_ordered').html());
                            console.log(total_ordered +'/'+total_prepared);
                            if (total_ordered == total_prepared)
                            {
                              console.log('complet ! ' + $('.etq_col').length);

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

                          console.log('l4');
                        $('#errors_container').show();
                    }
                });


            }
            function setResponsive()
            {
                var maxHeight = 0;
                $('.product_prepare_container').each(function()
                {
                    var height = $(this).height();

                    window.console.log(height);
                    window.console.log(maxHeight);

                    if (height > maxHeight)
                        maxHeight = height;

                });

                $('.product_prepare_container').css('height', maxHeight);
            }
        </script>
    {/if}
</div>
