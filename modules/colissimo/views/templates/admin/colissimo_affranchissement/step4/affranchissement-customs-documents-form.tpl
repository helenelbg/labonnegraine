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

{if $orders}
  <a href="{$link->getAdminLink('AdminColissimoAffranchissement')|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
    <i class="icon icon-chevron-left"></i> {l s='Back to selection' mod='colissimo'}
  </a>
{/if}
<div id="colissimo-process-result" style="display:none;">
    <div class="alert alert-info" >
       {l s='All orders have been processed, please see details below.' mod='colissimo'}
    </div>
</div>
<div id="colissimo-process" style="display: none">
  <img src="{$data.img_path|escape:'html':'UTF-8'}loading.svg"/>
</div>

{if $orders}
  <form method="post" class="form-horizontal" id="colissimo-affranchissement-customs-documents"
        enctype="multipart/form-data">
    <div class="colissimo-customs-documents panel collapse in">
      <div>
        <table class="table colissimo-customs-documents-table">
          <thead>
           <tr>
            <th></th>
            <th><span class="title_box text-center">{l s='Reference' mod='colissimo'}</span></th>
            <th><span class="title_box text-center">{l s='ID' mod='colissimo'}</span></th>
            <th><span class="title_box text-center">{l s='Shipping Number' mod='colissimo'}</span></th>
            <th><span class="title_box text-center">{l s='Use Prestashop invoice' mod='colissimo'}</span></th>
            <th style="width: 25%"><span class="title_box text-center">{l s='Add my own invoice' mod='colissimo'}</span></th>
            <th style="width: 25%"><span class="title_box text-center">{l s='Administrative document if > 1000' mod='colissimo'}</span></th>
            <th><span class="title_box text-center">{l s='Result' mod='colissimo'}</span></th>
           </tr>
          </thead>
          <tbody>
          {foreach $orders as $key => $order}
            <tr class="row-id-order-{$order.id_order|intval}">
              <td>
                <input class="colissimo-order-selection"
                       type="checkbox"
                       checked="checked"
                       name="colissimo_order_{$key|intval}">
              </td>
              <td class="text-left pointer col-reference-plus">
                <i class="icon icon-plus-circle"></i> {$order.reference|escape:'htmlall':'UTF-8'}
              </td>
              <td class="text-center">
                {$order.id_order|intval} 
              </td>
              <td class="text-center">
                <a target="_blank"
                   title="{l s='Download label' mod='colissimo'}"
                   class="shipping-number"
                   href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'UTF-8'}&action=downloadLabel&id_label={$order.id_colissimo_label|intval}">
                   {$order.shipping_number|escape:'htmlall':'UTF-8'}
                </a>
              </td>
              <td class="text-center">
                 <input type="checkbox" name="colissimo_prestashop_invoice_{$key|intval}" {if !$order.has_invoice}disabled{/if}/>
              </td>
              <td>
                 <div class="inputfile-box">
                  <input type="file" id="invoice-file" class="inputfile" name="invoice_{$order.id_colissimo_label|intval}" onchange='uploadFile(this)'>
                  <label for="invoice-file">
                    <span id="invoice-{$order.id_colissimo_label|intval}" class="file-box"></span>
                    <span class="file-button">{l s='Select File' mod='colissimo'}</span>
                  </label>
                 </div>
              </td>
              {if $order.amount > 1000}
                <td>
                 <div class="inputfile-box">
                  <input type="file" id="doc-file" class="inputfile" name="administrative_doc_{$order.id_colissimo_label|intval}" onchange='uploadFile(this)'>
                  <label for="doc-file">
                    <span id="administrative-doc-{$order.id_colissimo_label|intval}" class="file-box"></span>
                    <span class="file-button">{l s='Select File' mod='colissimo'}</span>
                  </label>
                 </div>
                </td>
              {else}
                <td class="text-center">
                  --
                </td>
              {/if}
              <td class="text-center colissimo-custom-document-result colissimo-custom-document-result-{$order.id_colissimo_label|intval}">
                --
              </td>
            </tr>
            <input type="hidden" name="label_{$order.id_colissimo_order|intval}"  class="label_{$order.id_colissimo_order|intval}" value="{$order.id_colissimo_label|intval}"/>
          {/foreach}
          </tbody>
        </table>
      </div>
      <button id="submit-process-colissimo-customs-documents"
              name="submitProcessColissimoCustomsDocuments"
              class="btn btn-primary pull-right">
          <i class="process-icon- icon-refresh"></i> {l s='Send to Customs' mod='colissimo'}
      </button>
      <div class="clearfix"></div>
    </div>
  </form>
      
  <a href="{$link->getAdminLink('AdminColissimoAffranchissement')|escape:'htmlall':'UTF-8'}" class="btn btn-primary">
    <i class="icon icon-chevron-left"></i> {l s='Back to selection' mod='colissimo'}
  </a>
{else}
  <div class="alert alert-info">
    {l s='There is no shipments to process for now.' mod='colissimo'}
  </div>
{/if}


{literal}
<script type="text/javascript">
    var loaderPath = {/literal}'{$data.img_path|escape:'html':'UTF-8'}loading.svg'{literal};
    var noOrdersText = "{/literal}{l s='Please select at least one order.' mod='colissimo'}{literal}";
    var noDocText = "{/literal}{l s='Please add all documents.' mod='colissimo'}{literal}";
    var genericErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
    $(document).ready(function(){
        $('.colissimo-progress-bar .step-documents').css({'display':'inline'});
        $('.step.current').removeClass('current').addClass('complete');
        $('.step.incomplete').removeClass('incomplete').addClass('current');
        $('.step-line').addClass('complete');
        $('.step-documents .step.incomplete').removeClass('incomplete').addClass('current');
    });
    
    function uploadFile(target) {
        var element = ($(target).attr('name'));
        var id = element.split("_").join("-");;
	document.getElementById(id).innerHTML = target.files[0].name;
    }
    
</script>
{/literal}
