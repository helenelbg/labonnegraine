{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
  {include file='./_partials/sub-menu.tpl'}

  <div class="alert alert-info">
    <p>{l s='On this page, you can edit postage slip for orders that have not been shipped yet.' mod='colissimo'}</p>
    <p>{l s='Select orders for which you want to edit a postage slip:' mod='colissimo'}</p>
    <ul>
      <li>{l s='By ticking orders in the list' mod='colissimo'}</li>
      <li>{l s='By filling manually parcel numbers in the input below' mod='colissimo'}</li>
      <li>{l s='By scanning barcode of labels in the input below' mod='colissimo'}</li>
    </ul>
  </div>
  <form method="post" class="form-horizontal">
    <div class="row">
      <div class="panel">
        <div class="row space">
          <div class="col-md-4">
            <div class="colissimo-barcode">
              <span>{l s='Fill the parcel numbers or scan the labels barcode using a scanner' mod='colissimo'}</span><br/>
              <img src="{$data.img_path|escape:'htmlall':'UTF-8'}icons/icon-barcode.png"/>
              <div class="fixed-width-xl">
                <input type="text" name="colissimo_parcel_number_barcode"
                       id="colissimo-parcel-number-barcode"
                       maxlength="13"
                       class="input fixed-width-xl"/>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="pull-right js-colissimo-deposit-slip-actions colissimo-deposit-slip-actions">
              <span>{l s='Number of parcel(s) selected:' mod='colissimo'} </span>
              <span class="js-colissimo-count-selected">0</span>
              <button type="submit" class="btn btn-primary btn-print" id="submit-process-colissimo-deposit-slip">
                <i class="process-icon- icon-print"></i>
                {l s='Print a delivery slip' mod='colissimo'}
              </button>
              <div id="deposit-slip-result" style="display: none;"></div>
            </div>
          </div>
        </div>
        <p class="colissimo-shipments-waiting">{l s='Parcels of the day' mod='colissimo'}</p>
        <table id="parcelsOfToday">
          <thead>
          <tr>
            <th></th>
            <th>{l s='Order reference' mod='colissimo'}</th>
            <th>{l s='Order ID' mod='colissimo'}</th>
            <th>{l s='Tracking number' mod='colissimo'}</th>
            <th>{l s='Customer' mod='colissimo'}</th>
            <th>{l s='Order status' mod='colissimo'}</th>
            <th>Status background</th>
            <th>Status color</th>
            <th>{l s='Date of order' mod='colissimo'}</th>
            <th>{l s='Date of label creation' mod='colissimo'}</th>
          </tr>
          </thead>
        </table>
        <p class="colissimo-shipments-waiting">{l s='Older parcels' mod='colissimo'}</p>
        <table id="olderParcels">
          <thead>
          <tr>
            <th></th>
            <th>{l s='Order reference' mod='colissimo'}</th>
            <th>{l s='Order ID' mod='colissimo'}</th>
            <th>{l s='Tracking number' mod='colissimo'}</th>
            <th>{l s='Customer' mod='colissimo'}</th>
            <th>{l s='Order status' mod='colissimo'}</th>
            <th>Status background</th>
            <th>Status color</th>
            <th>{l s='Date of order' mod='colissimo'}</th>
            <th>{l s='Date of label creation' mod='colissimo'}</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </form>
</div>
{literal}
<script type="text/javascript">
    var noLabelSelectedText = "{/literal}{l s='Please select at least one label.' mod='colissimo'}{literal}";
    var noWaitingShipmentsText = "{/literal}{l s='No waiting shipments' mod='colissimo'}{literal}";
    var genericErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
    var successMessage = "{/literal}{l s='Deposit slip generated successfully.' mod='colissimo'}{literal}";
    var tableParcelsOfToday;
    var tableOlderParcels;

    $(document).ready(function () {
        var barcodeInput = $('#colissimo-parcel-number-barcode');
        var dataTablesOptions = {
            dom:
                "<'row'<'col-sm-12 dt-control-buttons'lB>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            language: {
                url: datatables_lang_file,
            },
            columns: [
                {
                    sortable: false,
                    className: 'select-checkbox',
                    render: function (data, type, row) {
                        return '';
                    }
                },
                {
                    data: 'reference',
                    render: function (data, type, row, meta) {
                        //return '<a target="_blank" href="' + orders_url + '&vieworder&id_order=' + row.id_order + '">' + data + ' <i class="icon icon-external-link"></a>';
                        return '<a target="_blank" href="' + row.url + '">' + data + ' <i class="icon icon-external-link"></a>';
                    }
                },
                {
                    data: 'id_order'
                },
                {
                    data: 'shipping_number',
                    className: 'strong'
                },
                {
                    data: 'customer',
                    className: 'text-upper'
                },
                {
                    data: 'state_name',
                    render: function (data, type, row, meta) {
                        return '<span class="label color_field" style="background-color: ' + row.state_bg + '; color: ' + row.state_color + '">' + data + '</span>';
                    }
                },
                {
                    data: 'state_bg',
                    visible: false
                },
                {
                    data: 'state_color',
                    visible: false
                },
                {
                    data: 'order_date_add'
                },
                {
                    data: 'label_date_add'
                }
            ],
            buttons: [
                'selectAll',
                'selectNone',
            ],
            select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            drawCallback: function (settings) {
                $('.dt-buttons button').addClass('btn btn-primary');
            },
            order: [[9, 'desc']]
        };

        tableParcelsOfToday = $('#parcelsOfToday').DataTable(Object.assign({}, dataTablesOptions, {
            ajax: {
                url: datatables_url,
                data: {
                    ajax: 1,
                    action: 'listParcelsOfToday'
                }
            },
            rowCallback: function (row, data) {
                tableParcelsOfToday.row(row).select();
            }
        }));
        tableOlderParcels = $('#olderParcels').DataTable(Object.assign({}, dataTablesOptions, {
            ajax: {
                url: datatables_url,
                data: {
                    ajax: 1,
                    action: 'listOlderParcels'
                }
            }
        }));

        barcodeInput.focus();
        barcodeInput.typeWatch({
            captureLength: 12,
            highlight: true,
            wait: 50,
            callback: function () {
                findLabel(tableParcelsOfToday, tableOlderParcels);
            }
        });

        $.each([tableParcelsOfToday, tableOlderParcels], function () {
            this.on('select deselect', function (e, dt, type, indexes) {
                var count1 = tableParcelsOfToday.rows({selected: true}).count();
                var count2 = tableOlderParcels.rows({selected: true}).count();

                $('.js-colissimo-count-selected').text(count1 + count2);
            });
        });
    });
</script>
{/literal}
