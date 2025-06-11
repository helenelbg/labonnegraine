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

<div class="modal-header">
  <h4 class="modal-title">{l s='Colissimo service selection' mod='colissimo'}</h4>
</div>
<div class="modal-body">
  <div class="form-group">
    <label class="control-label col-lg-3">{l s='Available Colissimo services' mod='colissimo'}</label>
    <div class="col-lg-9">
      <select class="col-lg-4" name="colissimo_service_to_associate" id="colissimo-service-to-associate">
        {foreach $colissimo_services as $id_service => $name}
          {if $id_service || $colissimo_widget_token}
            <option value="{$id_service|intval}">
              {$name|escape:'html':'UTF-8'}
            </option>
          {/if}
        {/foreach}
      </select>
    </div>
  </div>
  <div class="colissimo-pickup-point-selection">
    <button class="btn btn-primary col-lg-offset-3 colissimo-pickup-point-btn">
      {l s='Select a pickup point' mod='colissimo'}
    </button>
    <div class="pickup-point-selected">

    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="submit"
          name="submitColissimoValidateService"
          class="col-lg-offset-3 btn btn-primary colissimo-submit-update-service">
    <i class="icon icon-check"></i>
    {l s='Validate' mod='colissimo'}
  </button>
</div>

<script type="text/javascript">
  {literal}

  $('.admincolissimoaffranchissement .colissimo-form-update-service').off('submit').on('submit', function (e) {
      e.preventDefault();

      var $form = $(this);
      var insuranceText = "{/literal}{l s='For a shipment to a pickup point, the maximum amount of insurance is 1000 euros.' mod='colissimo'}{literal}";
      var data = {
          'controller': 'AdminColissimoAffranchissement',
          'ajax': 1,
          'token': tokenAffranchissement,
          'action': 'validateServiceUpdate'
      };

      data = $form.serialize() + '&' + $.param(data);
      $.ajax({
          type: 'POST',
          dataType: 'json',
          url: baseAdminDir + 'index.php',
          data: data
      }).fail(function (jqXHR, textStatus) {
          showErrorMessage(genericErrorMessage);
      }).done(function (data) {
          if (!data.error) {
              var row = $('tr.row-id-order-' + data.order.id_order);

              row.find('td.colissimo-delivery-addr').html(data.html_address);
              row.find('td.colissimo-service').html(data.html_service);
              row.find('td.colissimo-insurance').html(data.html_insurance);
              row.find('td.colissimo-ta').html(data.html_ftd);
              if (data.order.relais) {
                  row.find('td.colissimo-d150 input[type="checkbox"]').prop('disabled', true);
              } else {
                  row.find('td.colissimo-d150 input[type="checkbox"]').prop('disabled', false);
              }
              $('.colissimo-insurance-msg').empty();
              if (data.order.relais) {
                $('.colissimo-insurance-msg').html('<div class="alert alert-info">'+insuranceText+'</div>');
              }
          } else {
              showErrorMessage(data.message);
          }
      }).always(function (data) {
          $('.colissimo-back-widget').modal('hide');
      });

  });

  {/literal}
</script>