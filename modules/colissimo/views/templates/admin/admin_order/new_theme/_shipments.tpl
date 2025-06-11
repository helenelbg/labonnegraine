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
<div class="table-responsive col-lg-7 colissimo-shipments-products">
    <table class="table">
        <thead>
            <tr>
                <th><span class="title_box ">{l s='Product name' mod='colissimo'}</span></th>
                <th><span class="title_box ">{l s='Quantity Shipped' mod='colissimo'}</span></th>
                <th><span class="title_box ">{l s='Shipped' mod='colissimo'}</span></th>
            </tr>
        </thead>
        <tbody>
            {foreach $shipments as $shipment}
                {foreach $shipment.products as $product}
                    {foreach $order_details as $order}
                        {if $order['product_id'] == $product.id_product && $order['product_attribute_id'] == $product.id_product_attribute}
                            <tr class="product-line-row">
                                <td>{$order['product_name']|escape:'htmlall':'UTF-8'}</td>
                                <td><strong> {$product.quantity|intval} / {$order['product_quantity']|intval}</strong></td>
                                <td> <a target="_blank"
                                    title="{l s='Download label' mod='colissimo'}"
                                    href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'UTF-8'}&action=downloadLabel&id_label={$shipment.id_label|intval}">
                                    {$shipment.shipping_number|escape:'htmlall':'UTF-8'}
                                    </a>
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                {/foreach}
            {foreachelse}
                <tr>
                    <td class="list-empty hidden-print" colspan="6">
                        <div class="list-empty-msg">
                               {l s='You have not generated any labels yet.' mod='colissimo'}
                        </div>
                    </td>
                </tr>
            {/foreach}  
        </tbody>
    </table>
    <br/> <br/>
</div>
<div class="table-responsive colissimo-new-theme colissimo-shipments">
  <table class="table">
    <thead>
    <tr>
      <th class="text-center">
        <span class="title_box "><i class="icon icon-info"></i> {l s='#' mod='colissimo'}</span></th>
      <th class="text-center" style="width: 45%">
        <span class="title_box "><i class="icon icon-barcode"></i> {l s='Shipment' mod='colissimo'}</span></th>
      <th class="text-center" style="width: 45%">
        <span class="title_box "><i class="icon icon-euro"></i> {l s='Return shipment' mod='colissimo'}</span></th>
      <th class="text-center">
        <span class="title_box "><i class="icon icon-file-pdf-o"></i> {l s='Customs Documents' mod='colissimo'}</span></th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$shipments key=$i item=$shipment name=shipments}
      <tr class="product-line-row">
        <td style="width: 10%">#{$smarty.foreach.shipments.iteration}</td>
        <td colspan="2" class="tracking-label-1">
          <div class="container">
            <div class="row text-center">
              <div class="col-6">
                <p>
                  <span class="font-weight-bold colissimo-shipment-number">{$shipment.shipping_number|escape:'htmlall':'UTF-8'}</span>
                  {if $shipment.status_text}
                    <span class="d-block">{$shipment.status_text|escape:'htmlall':'UTF-8'}</span>
                  {/if}
                  {if $shipment.status_upd}
                    <span class="text-muted colissimo-shipment-status">
                      ({l s='last update on' mod='colissimo'} {$shipment.status_upd|escape:'htmlall':'UTF-8'})
                    </span>
                  {/if}
                </p>
              </div>
              <div class="col-6">
                {if isset($shipment.id_return_label)}
                  <p>
                    <span class="font-weight-bold colissimo-shipment-number">{$shipment.return_shipping_number|escape:'htmlall':'UTF-8'}</span>
                  </p>
                {/if}
              </div>
            </div>

            <div class="row">
              <div class="col-6">
                <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                  <div class="col-xl-4 text-xl-right text-sm-center">{l s='Label' mod='colissimo'}</div>
                  <div class="col-xl-8 text-xl-left">
                    {if $shipment.is_downloadable}
                      <a target="_blank"
                         title="{l s='Download label' mod='colissimo'}"
                         class="icon-action"
                         href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'UTF-8'}&action=downloadLabel&id_label={$shipment.id_label|intval}">
                        <i class="material-icons">get_app</i>
                      </a>
                    {/if}
                    {if $shipment.is_printable_pdf}
                      <a class="icon-action"
                         title="{l s='Print label' mod='colissimo'}"
                         onclick="printJS({literal}{
                                 printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewLabel&id_label={/literal}{$shipment.id_label|intval}{literal}&token=' + tokenLabel,
                                 showModal: true,
                                 fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['base64']|escape:'html':'UTF-8'}{literal}`,
                                 modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                                 }{/literal});">
                        <i class="material-icons">print</i>
                      </a>
                    {else}
                      <a class="icon-action"
                         title="{l s='Print label' mod='colissimo'}"
                         onclick="printThermal('{$shipment['base64']|escape:'html':'UTF-8'}')">
                        <i class="material-icons">print</i>
                      </a>
                    {/if}
                    {if $shipment.is_deletable}
                      <a data-colissimo-label-id="{$shipment.id_label|intval}"
                         data-colissimo-label-number="{$shipment.shipping_number|escape:'htmlall':'UTF-8'}"
                         title="{l s='Delete label' mod='colissimo'}"
                         class="colissimo-delete-label icon-action icon-action-red"
                         href="#">
                        <i class="material-icons">delete_forever</i>
                      </a>
                    {/if}
                  </div>
                </div>
                <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                  <div class="col-xl-4 text-xl-right text-sm-center">{l s='CN23' mod='colissimo'}</div>
                  <div class="col-xl-8 text-xl-left">
                    {if isset($shipment.cn23) && $shipment.cn23}
                      <a target="_blank"
                         class="icon-action"
                         href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'UTF-8'}&action=downloadCN23&id_label={$shipment.id_label|intval}">
                        <i class="material-icons">get_app</i>
                      </a>
                      <a class="icon-action"
                         title="{l s='Print CN23' mod='colissimo'}"
                         onclick="printJS({literal}{
                                 printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewCN23&id_label={/literal}{$shipment.id_label|intval}{literal}&token=' + tokenLabel,
                                 showModal: true,
                                 fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['cn23_base64']|escape:'html':'UTF-8'}{literal}`,
                                 modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                                 }{/literal});">
                        <i class="material-icons">print</i>
                      </a>
                    {else}
                      <span class="ml-2">--</span>
                    {/if}
                  </div>
                </div>
                <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                  <div class="col-xl-4 text-xl-right text-sm-center">{l s='Deposit slip' mod='colissimo'}</div>
                  <div class="col-xl-8 text-xl-left">
                    {if isset($shipment.id_deposit_slip) && $shipment.id_deposit_slip}
                      <a target="_blank"
                         class="icon-action"
                         href="{$link->getAdminLink('AdminColissimoDepositSlip')|escape:'htmlall':'UTF-8'}&action=download&id_deposit_slip={$shipment.id_deposit_slip|intval}">
                        <i class="material-icons">get_app</i>
                      </a>
                    {else}
                      <span class="ml-2">--</span>
                    {/if}
                  </div>
                </div>
                <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                  <div class="col-xl-4 text-xl-right">{l s='Shipment insured' mod='colissimo'}</div>
                  <div class="col-xl-8 text-xl-left">
                    {if $shipment.insurance === '1'}
                      <i class="ml-1 material-icons icon-action-green">done</i>
                    {elseif $shipment.insurance === '0'}
                      <i class="ml-1 material-icons icon-action-red">clear</i>
                    {else}
                      --
                    {/if}
                  </div>
                </div>
              </div>
              <div class="col-6">
                {if isset($shipment.id_return_label)}
                  <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                    <div class="col-xl-4 text-xl-right">{l s='Return label' mod='colissimo'}</div>
                    <div class="col-xl-8 text-xl-left">
                      {if $shipment.return_is_downloadable}
                        <a target="_blank"
                           title="{l s='Download return label' mod='colissimo'}"
                           class="icon-action"
                           href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'UTF-8'}&action=downloadLabel&id_label={$shipment.id_return_label|intval}">
                          <i class="material-icons">get_app</i>
                        </a>
                        <a class="icon-action"
                           title="{l s='Print return label' mod='colissimo'}"
                           onclick="printJS({literal}{
                                   printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewLabel&id_label={/literal}{$shipment.id_return_label|intval}{literal}&token=' + tokenLabel,
                                   showModal: true,
                                   fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['return_base64']|escape:'html':'UTF-8'}{literal}`,
                                   modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                                   }{/literal});">
                          <i class="material-icons">print</i>
                        </a>
                        <a data-colissimo-return-label-id="{$shipment.id_return_label|intval}"
                           class="colissimo-mail-return-label icon-action"
                           title="{l s='Send by mail to customer' mod='colissimo'}"
                           href="#">
                          <i class="material-icons">mail_outline</i>
                        </a>
                      {/if}
                      {if $shipment.return_is_deletable}
                        <a data-colissimo-label-id="{$shipment.id_return_label|intval}"
                           data-colissimo-label-number="{$shipment.return_shipping_number|escape:'htmlall':'UTF-8'}"
                           title="{l s='Delete return label' mod='colissimo'}"
                           class="colissimo-delete-label icon-action icon-action-red"
                           href="#">
                          <i class="material-icons">delete_forever</i>
                        </a>
                      {/if}
                    </div>
                  </div>
                  <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                    <div class="col-xl-4 text-xl-right">{l s='Return CN23' mod='colissimo'}</div>
                    <div class="col-xl-8 text-xl-left">
                      {if isset($shipment.return_cn23) && $shipment.return_cn23}
                        <a target="_blank"
                           class="icon-action"
                           href="{$link->getAdminLink('AdminColissimoLabel')|escape:'htmlall':'UTF-8'}&action=downloadCN23&id_label={$shipment.id_return_label|intval}">
                          <i class="material-icons">get_app</i>
                        </a>
                        <a class="icon-action"
                           title="{l s='Print return CN23' mod='colissimo'}"
                           onclick="printJS({literal}{
                                   printable: baseAdminDir + 'index.php?controller=AdminColissimoLabel&action=viewCN23&id_label={/literal}{$shipment.id_return_label|intval}{literal}&token=' + tokenLabel,
                                   showModal: true,
                                   fallbackPrintable: `data:application/pdf;base64,{/literal}{$shipment['return_cn23_base64']|escape:'html':'UTF-8'}{literal}`,
                                   modalMessage: {/literal}'{l s='Preparing printing' mod='colissimo'}'{literal},
                                   }{/literal});">
                          <i class="material-icons">print</i>
                        </a>
                      {else}
                        <span class="ml-2">--</span>
                      {/if}
                    </div>
                  </div>
                  <div class="row align-items-center mb-sm-2 mb-xl-0 text-sm-center">
                    <div class="col-xl-4 text-xl-right">{l s='Return shipment insured' mod='colissimo'}</div>
                    <div class="col-xl-8 text-xl-left">
                      {if $shipment.return_insurance === '1'}
                        <i class="ml-1 material-icons icon-action-green">done</i>
                      {elseif $shipment.return_insurance === '0'}
                        <i class="ml-1 material-icons icon-action-red">clear</i>
                      {else}
                        --
                      {/if}
                    </div>
                  </div>
                {else}
                  <div class="row">
                    <div class="col-xl-12 text-center">
                      {if $shipment.return_available >= 1}
                        <button class="btn btn-primary colissimo-generate-return-label"
                                data-colissimo-label-id="{$shipment.id_label|intval}">
                          {l s='Generate' mod='colissimo'}
                        </button>
                      {/if}
                    </div>
                  </div>
                {/if}
              </div>
            </div>
          </div>
        </td>
        <td>
            {if $shipment.documents}
                {if isset($shipment.documents.CN23)}
                    <div><b>{l s='CN23' mod='colissimo'}</b> : {$shipment.documents.CN23|escape:'html':'UTF-8'} </div>
                {/if}        
                {if isset($shipment.documents.COMMERCIAL_INVOICE)}
                    <div><b>{l s='Invoice' mod='colissimo'}</b> : {$shipment.documents.COMMERCIAL_INVOICE|escape:'html':'UTF-8'} </div>
                {/if}
                {if isset($shipment.documents.OTHER)}
                    <div><b>{l s='Administrative Doc' mod='colissimo'}</b> : {$shipment.documents.OTHER|escape:'html':'UTF-8'} </div>
                {/if}
            {else}
                <div class="text-center"> -- </div>
            {/if}
        </td>
      </tr>
      {foreachelse}
      <tr>
        <td class="list-empty hidden-print" colspan="3">
          <div class="list-empty-msg">
            <i class="icon-warning-sign list-empty-icon"></i>
            {l s='You have not generated any labels yet.' mod='colissimo'}
          </div>
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  <div class="colissimo-modal-delete-label colissimo-modal-delete-label-{$id_colissimo_order|intval} modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {l s='Confirm label deleting' mod='colissimo'}
          </h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="colissimo-modal-order-id" value="{$id_colissimo_order|intval}"/>
          <input type="hidden" name="colissimo-modal-label-id" value=""/>
          <p class="alert alert-info">
            {l s='Documents like CN23 and return labels associated with this label will be deleted as well.' mod='colissimo'}
          </p>
          <p class="modal-confirm">
            {l s='Do you wan\'t to delete label' mod='colissimo'} <span id="colissimo-shipment-number"></span> ?
          </p>
          <div class="modal-actions">
            <button class="btn btn-danger" data-dismiss="modal">
              <i class="icon icon-times"></i>
              {l s='No' mod='colissimo'}
            </button>
            <button class="btn btn-primary colissimo-modal-confirm-delete-label">
              <i class="icon- icon-check"></i>
              {l s='Yes' mod='colissimo'}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="colissimo-modal-mail-label colissimo-modal-mail-label-{$id_colissimo_order|intval} modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            {l s='The return label is about to be generated' mod='colissimo'}
          </h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="colissimo-modal-order-id" value="{$id_colissimo_order|intval}"/>
          <input type="hidden" name="colissimo-modal-label-id" value=""/>
          <p class="modal-confirm">
            {l s='Do you want to send it by mail to your customer' mod='colissimo'} ?
          </p>
          <div class="modal-actions">
            <button class="btn btn-danger colissimo-modal-no-confirm-mail-label">
              <i class="icon icon-times"></i>
              {l s='No' mod='colissimo'}
            </button>
            <button class="btn btn-primary colissimo-modal-confirm-mail-label">
              <i class="icon- icon-check"></i>
              {l s='Yes' mod='colissimo'}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
