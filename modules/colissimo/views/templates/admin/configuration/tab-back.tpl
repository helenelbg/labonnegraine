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

<form class="form-horizontal back-config"
      action="#"
      name="colissimo_bo_config_orders_form"
      id="colissimo_bo_config_orders_form"
      method="post"
      enctype="multipart/form-data">
  <div class="panel">
    <div class="panel-heading">
      <i class="icon-cogs"></i>
      {l s='Status and orders settings' mod='colissimo'}
    </div>
    <div class="row">
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='Enter your order preparation time' mod='colissimo'}">
            {l s='Preparation time' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <div class="input-group input fixed-width-xs">
            <input value="{$form_data['COLISSIMO_ORDER_PREPARATION_TIME']|intval}"
                   type="text"
                   name="COLISSIMO_ORDER_PREPARATION_TIME"
                   class="input  fixed-width-xs">
            <span class="input-group-addon">{l s='day(s)' mod='colissimo'}</span>
          </div>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-9 col-lg-offset-3">
        <p class="colissimo-subtitle">{l s='Label generation' mod='colissimo'}</p>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-lg-3" for="colissimo-generate-label-statuses[]">
          <span class="label-tooltip"
                data-html="false"
                data-toggle="tooltip"
                data-original-title="{l s='Tip : press + hold Ctrl then click on statuses to select several options quickly' mod='colissimo'}">
            {l s='Statuses that allow to generate labels' mod='colissimo'}
          </span>
      </label>

      <div class="col-lg-9">
        <div class="form-control-static row">
          <div class="col-xs-6">
            <select id="availableSwap"
                    class="availableSwap"
                    name="COLISSIMO_GENERATE_LABEL_STATUSES_available[]"
                    multiple="multiple">
              <optgroup label="{l s='Available statuses' mod='colissimo'}">
                {foreach $order_states as $id => $state}
                  {if !isset($form_data['COLISSIMO_GENERATE_LABEL_STATUSES'][$id])}
                    <option value="{$id|intval}">
                      {$state|escape:'htmlall':'UTF-8'}
                    </option>
                  {/if}
                {/foreach}
              </optgroup>
            </select>
            <a href="#" class="btn btn-default btn-block addSwap" id="addSwap">
              {l s='Add' mod='colissimo'} <i class="icon-arrow-right"></i>
            </a>
          </div>
          <div class="col-xs-6">
            <select id="selectedSwap" class="selectedSwap"
                    name="COLISSIMO_GENERATE_LABEL_STATUSES[]"
                    multiple="multiple">
              <optgroup label="{l s='Selected statuses' mod='colissimo'}">
                {foreach $order_states as $id => $state}
                  {if isset($form_data['COLISSIMO_GENERATE_LABEL_STATUSES'][$id])}
                    <option value="{$id|intval}">
                      {$state|escape:'htmlall':'UTF-8'}
                    </option>
                  {/if}
                {/foreach}
              </optgroup>
            </select>
            <a href="#" class="btn btn-default btn-block removeSwap" id="removeSwap">
              <i class="icon-arrow-left"></i> {l s='Remove' mod='colissimo'}
            </a>
          </div>
          <p class="help-block">
            <i class="icon icon-warning"></i> {l s='If no statuses are selected, all statuses will be considered as selected (wording...)' mod='colissimo'}
          </p>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='If you choose Yes, orders will change to Shipping in progress after the generation of the first label' mod='colissimo'}">
            {l s='Update order status after label generation' mod='colissimo'}
          </span>
      </label>
      <div class="col-lg-9 colissimo-use-shipping-in-progress">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_USE_SHIPPING_IN_PROGRESS"
                   id="COLISSIMO_USE_SHIPPING_IN_PROGRESS_on"
                   {if $form_data['COLISSIMO_USE_SHIPPING_IN_PROGRESS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_SHIPPING_IN_PROGRESS_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_USE_SHIPPING_IN_PROGRESS"
                   id="COLISSIMO_USE_SHIPPING_IN_PROGRESS_off"
                   {if !$form_data['COLISSIMO_USE_SHIPPING_IN_PROGRESS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_SHIPPING_IN_PROGRESS_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
      </div>
      <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='If you choose Yes, orders will change to Handled by carrier after the generation of the first deposit slip' mod='colissimo'}">
            {l s='Update order status after deposit slip generation' mod='colissimo'}
          </span>
      </label>
      <div class="col-lg-9 colissimo-use-handled-by-carrier">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_USE_HANDLED_BY_CARRIER"
                   id="COLISSIMO_USE_HANDLED_BY_CARRIER_on"
                   {if $form_data['COLISSIMO_USE_HANDLED_BY_CARRIER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_HANDLED_BY_CARRIER_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_USE_HANDLED_BY_CARRIER"
                   id="COLISSIMO_USE_HANDLED_BY_CARRIER_off"
                   {if !$form_data['COLISSIMO_USE_HANDLED_BY_CARRIER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_HANDLED_BY_CARRIER_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
      </div>
      <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
          <span>
            {l s='Send an email with the Non-Authenticated tracking page URL to customers after label generation' mod='colissimo'}
          </span>
      </label>
      <div class="col-lg-9 colissimo-enable-pna-mail">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_ENABLE_PNA_MAIL"
                   id="COLISSIMO_ENABLE_PNA_MAIL_on"
                   {if $form_data['COLISSIMO_ENABLE_PNA_MAIL']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_ENABLE_PNA_MAIL_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_ENABLE_PNA_MAIL"
                   id="COLISSIMO_ENABLE_PNA_MAIL_off"
                   {if !$form_data['COLISSIMO_ENABLE_PNA_MAIL']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_ENABLE_PNA_MAIL_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
      </div>
      <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
          <span>
            {l s='Display tracking number on the order page' mod='colissimo'}
          </span>
      </label>
      <div class="col-lg-9 colissimo-display-tracking-number">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_DISPLAY_TRACKING_NUMBER"
                   id="COLISSIMO_DISPLAY_TRACKING_NUMBER_on"
                   {if $form_data['COLISSIMO_DISPLAY_TRACKING_NUMBER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_DISPLAY_TRACKING_NUMBER_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_DISPLAY_TRACKING_NUMBER"
                   id="COLISSIMO_DISPLAY_TRACKING_NUMBER_off"
                   {if !$form_data['COLISSIMO_DISPLAY_TRACKING_NUMBER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_DISPLAY_TRACKING_NUMBER_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
       </div>
       <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
  </div>
  <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoBackConfigOrdersForm">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
      </button>
  </div>
</form>
<!-- Print settings -->
<form class="form-horizontal back-config"
      action="#"
      name="colissimo-bo-config-print-form"
      id="colissimo-bo-config-print-form"
      method="post"
      enctype="multipart/form-data">
  <div class="form-colissimo">
  <div class="panel">
    <div class="panel-heading">
       <i class="icon-cogs"></i>
       {l s='Print settings' mod='colissimo'}
    </div>   
    <div class="form-group">
      <label class="control-label col-lg-3 ">
          <span>
            {l s='Label printing mode' mod='colissimo'}
          </span>
      </label>
      <div class="col-lg-9 colissimo-generate-label-prestashop">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_GENERATE_LABEL_PRESTASHOP"
                   id="COLISSIMO_GENERATE_LABEL_PRESTASHOP_on"
                   {if $form_data['COLISSIMO_GENERATE_LABEL_PRESTASHOP']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_GENERATE_LABEL_PRESTASHOP_on">{l s='PrestaShop BO' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_GENERATE_LABEL_PRESTASHOP"
                   id="COLISSIMO_GENERATE_LABEL_PRESTASHOP_off"
                   {if !$form_data['COLISSIMO_GENERATE_LABEL_PRESTASHOP']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_GENERATE_LABEL_PRESTASHOP_off">{l s='Coliship' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
      </div>
      <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
    <div class="colissimo-label-generation-mode-inputs">
      <div class="form-group">
        <label class="control-label col-lg-3 ">
           <span class="label-tooltip"
              data-toggle="tooltip"
              data-html="true"
              data-original-title="{l s='You must have the printing kit' mod='colissimo'}">
              {l s='Postage mode in PrestaShop\'s BO' mod='colissimo'}
        </span>
        </label>
        <div class="col-lg-9 colissimo-postage-mode-manual">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_POSTAGE_MODE_MANUAL"
                   id="COLISSIMO_POSTAGE_MODE_MANUAL_on"
                   {if $form_data['COLISSIMO_POSTAGE_MODE_MANUAL']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_POSTAGE_MODE_MANUAL_on">
                {l s='Manual' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_POSTAGE_MODE_MANUAL"
                   id="COLISSIMO_POSTAGE_MODE_MANUAL_off"
                   {if !$form_data['COLISSIMO_POSTAGE_MODE_MANUAL']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_POSTAGE_MODE_MANUAL_off">{l s='Auto' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="form-group">
        <form class="form-horizontal"
            action="downloadKit"
            id="colissimo-form-kit"
            method="post"
            enctype="multipart/form-data">
            
            <label class="control-label col-lg-3 ">
                <span>{l s='Download Printing Kit' mod='colissimo'}</span>
            </label>
            <div class="col-lg-9">
                <button type="submit" class="btn btn-primary btn-download-kit" name="submitDownloadKit">
                    <i class="icon icon-download"></i>
                    {l s='Download zip' mod='colissimo'}
                </button>
            </div>
        </form>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>
            {l s='Use a thermal printer' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9 colissimo-use-thermal-printer">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_USE_THERMAL_PRINTER"
                   id="COLISSIMO_USE_THERMAL_PRINTER_on"
                   {if $form_data['COLISSIMO_USE_THERMAL_PRINTER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_THERMAL_PRINTER_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_USE_THERMAL_PRINTER"
                   id="COLISSIMO_USE_THERMAL_PRINTER_off"
                   {if !$form_data['COLISSIMO_USE_THERMAL_PRINTER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_THERMAL_PRINTER_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>{l s='Label format' mod='colissimo'}</span>
        </label>
        <div class="col-lg-9">
          <select name="COLISSIMO_LABEL_FORMAT" class=" fixed-width-xxl">
            {foreach $label_formats as $id => $name}
              <option {if $id == $form_data['COLISSIMO_LABEL_FORMAT']}selected="selected"{/if}
                      value="{$id|escape:'htmlall':'UTF-8'}">
                {$name|escape:'htmlall':'UTF-8'}
              </option>
            {/foreach}
          </select>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="colissimo-thermal-printer-inputs">
        <div class="form-group">
          <label class="control-label col-lg-3 ">
          <span>
            {l s='Printer port' mod='colissimo'}
          </span>
          </label>
          <div class="col-lg-9 colissimo-use-ethernet">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_USE_ETHERNET"
                   id="COLISSIMO_USE_ETHERNET_on"
                   {if $form_data['COLISSIMO_USE_ETHERNET']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_ETHERNET_on">{l s='Ethernet' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_USE_ETHERNET"
                   id="COLISSIMO_USE_ETHERNET_off"
                   {if !$form_data['COLISSIMO_USE_ETHERNET']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_ETHERNET_off">{l s='USB' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
          </div>
          <div class="col-lg-9 col-lg-offset-3"></div>
        </div>
        <div class="colissimo-thermal-printer-usb">
          <div class="form-group">
            <label class="control-label col-lg-3 ">
              <span>{l s='Protocole' mod='colissimo'}</span>
            </label>
            <div class="col-lg-9">
              <select name="COLISSIMO_USB_PROTOCOLE" class=" fixed-width-xxl">
                {foreach $usb_protocoles as $id => $name}
                  <option {if $id == $form_data['COLISSIMO_USB_PROTOCOLE']}selected="selected"{/if}
                          value="{$id|escape:'htmlall':'UTF-8'}">
                    {$name|escape:'htmlall':'UTF-8'}
                  </option>
                {/foreach}
              </select>
            </div>
            <div class="col-lg-9 col-lg-offset-3">
              <div x-ms-format-detection="none" class="help-block">
                {l s='If you do not know which value to chose, please refer to your printer user guide' mod='colissimo'}
                <span></span>
              </div>
            </div>
          </div>
        </div>
        <div class="colissimo-thermal-printer-ethernet">
          <div class="form-group">
            <label class="control-label col-lg-3 ">
              <span>{l s='Printer IP address' mod='colissimo'}</span>
            </label>
            <div class="col-lg-9">
              <div class="fixed-width-xxl">
                <input type="text"
                       value="{$form_data['COLISSIMO_PRINTER_IP_ADDR']|escape:'htmlall':'UTF-8'}"
                       name="COLISSIMO_PRINTER_IP_ADDR"
                       class="input  fixed-width-xxl">
              </div>
            </div>
            <div class="col-lg-9 col-lg-offset-3">
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
          <span>
            {l s='Display the order reference on the label or order id ' mod='colissimo'}
          </span>
      </label>
      <div class="col-lg-9 colissimo-enable-pna-mail">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_LABEL_DISPLAY_REFERENCE"
                   id="COLISSIMO_LABEL_DISPLAY_REFERENCE_on"
                   {if $form_data['COLISSIMO_LABEL_DISPLAY_REFERENCE']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_LABEL_DISPLAY_REFERENCE_on">{l s='REFERENCE' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_LABEL_DISPLAY_REFERENCE"
                   id="COLISSIMO_LABEL_DISPLAY_REFERENCE_off"
                   {if !$form_data['COLISSIMO_LABEL_DISPLAY_REFERENCE']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_LABEL_DISPLAY_REFERENCE_off">{l s='ID' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
      </div>
      <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
 </div>
  <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoPrintConfigForm">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
      </button>
  </div>
  </div>
</form>
<!-- /Print settings -->
<!-- International Shipping Settings -->
<form class="form-horizontal back-config"
      action="#"
      name="colissimo-bo-config-postage-form"
      id="colissimo-bo-config-postage-form"
      method="post"
      enctype="multipart/form-data">
  <div class="form-colissimo">
 <div class="panel">
   <div class="panel-heading">
        <i class="icon-cogs"></i>
        {l s='International shipping settings' mod='colissimo'}
    </div>
    <div class="row">
      <div class="col-lg-9 col-lg-offset-3">
        <p class="colissimo-subtitle">{l s='International shipments' mod='colissimo'}</p>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
        <span>{l s='Default Origin Country' mod='colissimo'}</span>
      </label>
      <div class="col-lg-9">
        <select name="COLISSIMO_DEFAULT_ORIGIN_COUNTRY" class=" fixed-width-xxl">
         <option value="0">{l s='-- Please select a country --' mod='colissimo'}</option>
         {foreach $countries as $id => $country}
           <option {if $id == $form_data['default_origin_country']}selected="selected"{/if}
                    value="{$country['id_country']|intval}">
             {$country['name']|escape:'htmlall':'UTF-8'}
           </option>
         {/foreach}
        </select>
      </div>
      <div class="col-lg-9 col-lg-offset-3"></div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
        <span class="label-tooltip"
              data-toggle="tooltip"
              data-html="true"
              data-original-title="{l s='(e.g. 620411)' mod='colissimo'}">
          {l s='Default HS Code of your products' mod='colissimo'}
        </span>
      </label>
      <div class="col-lg-9">
        <input type="text"
               name="COLISSIMO_DEFAULT_HS_CODE"
               value="{$form_data['COLISSIMO_DEFAULT_HS_CODE']|escape:'htmlall':'UTF-8'}"
               class="input fixed-width-xxl">
      </div>
      <div class="col-lg-9 col-lg-offset-3">
        <div x-ms-format-detection="none" class="help-block">
          {l s='For more information:' mod='colissimo'} https://douane.gouv.nc/le-tarif-douanier/nomenclature-tarifaire
          <span></span>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
        <span>{l s='EORI Number' mod='colissimo'}</span>
      </label>
      <div class="col-lg-9">
        <div class="fixed-width-xxl">
          <input type="text"
                 value="{$form_data['COLISSIMO_EORI_NUMBER']|escape:'htmlall':'UTF-8'}"
                 name="COLISSIMO_EORI_NUMBER"
                 class="input  fixed-width-xxl">
        </div>
      </div>
      <div class="col-lg-9 col-lg-offset-3">
        <div x-ms-format-detection="none" class="help-block">
          {l s='For more information:' mod='colissimo'}
          http://douane.gouv.fr/articles/a10901-numero-eori-economic-operator-registration-and-identification
          <span></span>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-lg-3 ">
        <span>{l s='UK EORI Number' mod='colissimo'}</span>
      </label>
      <div class="col-lg-9">
        <div class="fixed-width-xxl">
          <input type="text"
                 value="{$form_data['COLISSIMO_EORI_NUMBER_UK']|escape:'htmlall':'UTF-8'}"
                 name="COLISSIMO_EORI_NUMBER_UK"
                 class="input  fixed-width-xxl">
        </div>
      </div>
      <div class="col-lg-9 col-lg-offset-3">
        <div x-ms-format-detection="none" class="help-block">
          {l s='For more information:' mod='colissimo'}
          http://douane.gouv.fr/articles/a10901-numero-eori-economic-operator-registration-and-identification
          <span></span>
        </div>
      </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>
            {l s='Choice of carrier for the following international destinations (Italy, Austria, Germany and Luxembourg)' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_POSTAL_PARTNER"
                   id="COLISSIMO_POSTAL_PARTNER_on"
                   {if $form_data['COLISSIMO_POSTAL_PARTNER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_POSTAL_PARTNER_on">{l s='Part.postal' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_POSTAL_PARTNER"
                   id="COLISSIMO_POSTAL_PARTNER_off"
                   {if !$form_data['COLISSIMO_POSTAL_PARTNER']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_POSTAL_PARTNER_off">{l s='DPD' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
    </div>          
 </div>
  <div class="panel-footer">
    <button type="submit" class="btn btn-default btn-config-form pull-right" name="submitColissimoBackShippingForm">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
    </button>
 </div>
</div>
</form>
<!-- /International Shipping Settings -->
<!-- Postage Settings -->
<form class="form-horizontal back-config"
      action="#"
      name="colissimo_postage_config_form"
      id="colissimo-postage-config-form"
      method="post"
      enctype="multipart/form-data">
  <div class="panel">
    <div class="panel-heading">
      <i class="icon-cogs"></i>
      {l s='Postage settings' mod='colissimo'}
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>
            {l s='Use a default weight tare' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9 colissimo-use-weight-tare">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_USE_WEIGHT_TARE"
                   id="COLISSIMO_USE_WEIGHT_TARE_on"
                   {if $form_data['COLISSIMO_USE_WEIGHT_TARE']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_WEIGHT_TARE_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_USE_WEIGHT_TARE"
                   id="COLISSIMO_USE_WEIGHT_TARE_off"
                   {if !$form_data['COLISSIMO_USE_WEIGHT_TARE']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_WEIGHT_TARE_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="colissimo-default-tare-input"> 
        <div class="form-group">
          <label class="control-label col-lg-3 ">
            <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='Default weight tare' mod='colissimo'}">
            {l s='Default weight tare' mod='colissimo'}
            </span>
          </label>
          <div class="col-lg-9">
            <div class="input-group input fixed-width-xs">
              <input value="{$form_data['COLISSIMO_DEFAULT_WEIGHT_TARE']|floatval}"
                   type="text"
                   name="COLISSIMO_DEFAULT_WEIGHT_TARE"
                   class="input  fixed-width-sm">
              <span class="input-group-addon">{l s='kg' mod='colissimo'}</span>
            </div>
          </div>
          <div class="col-lg-9 col-lg-offset-3"></div>
        </div>
      </div> 
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='Ad Valorem Insurance' mod='colissimo'}">
            {l s='Insure shipments' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_INSURE_SHIPMENTS"
                   id="COLISSIMO_INSURE_SHIPMENTS_on"
                   {if $form_data['COLISSIMO_INSURE_SHIPMENTS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_INSURE_SHIPMENTS_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_INSURE_SHIPMENTS"
                   id="COLISSIMO_INSURE_SHIPMENTS_off"
                   {if !$form_data['COLISSIMO_INSURE_SHIPMENTS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_INSURE_SHIPMENTS_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
  </div>
  <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoBackPostageConfigForm">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
      </button>
  </div>
</form>
<!-- /Postage Settings -->    
<!-- shipments Settings -->   
<form class="form-horizontal back-config"
      action="#"
      name="colissimo_shipments_config_form"
      id="colissimo-shipments-config-form"
      method="post"
      enctype="multipart/form-data">
  <div class="panel">
    <div class="panel-heading">
      <i class="icon-cogs"></i>
      {l s='Return label settings' mod='colissimo'}
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>
            {l s='Enable Colissimo merchandises return' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9 colissimo-enable-return">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_ENABLE_RETURN"
                   id="COLISSIMO_ENABLE_RETURN_on"
                   {if $form_data['COLISSIMO_ENABLE_RETURN']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_ENABLE_RETURN_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_ENABLE_RETURN"
                   id="COLISSIMO_ENABLE_RETURN_off"
                   {if !$form_data['COLISSIMO_ENABLE_RETURN']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_ENABLE_RETURN_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="colissimo-enable-return-inputs">
        <div class="form-group">
          <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='Only scanned parcels will be charged' mod='colissimo'}">
            {l s='Print return labels for each shipment' mod='colissimo'}
          </span>
          </label>
          <div class="col-lg-9 colissimo-use-handled-by-carrier">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_AUTO_PRINT_RETURN_LABEL"
                   id="COLISSIMO_AUTO_PRINT_RETURN_LABEL_on"
                   {if $form_data['COLISSIMO_AUTO_PRINT_RETURN_LABEL']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_AUTO_PRINT_RETURN_LABEL_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_AUTO_PRINT_RETURN_LABEL"
                   id="COLISSIMO_AUTO_PRINT_RETURN_LABEL_off"
                   {if !$form_data['COLISSIMO_AUTO_PRINT_RETURN_LABEL']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_AUTO_PRINT_RETURN_LABEL_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
          </div>
          <div class="col-lg-9 col-lg-offset-3">
            <div x-ms-format-detection="none" class="help-block">
              <i class='icon icon-lightbulb'></i>
              {l s='Choose «Yes» if you want to include return label inside packages.' mod='colissimo'}
              <span></span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-lg-3">
            <span>
              {l s='Display return labels in customer\'s account' mod='colissimo'}
            </span>
          </label>
          <div class="col-lg-9 colissimo-display-return-label-customer">
            <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio"
                     value="1"
                     name="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER"
                     id="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER_on"
                     {if $form_data['COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER']|intval}checked="checked"{/if}>
              <label for="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER_on">{l s='Yes' mod='colissimo'}</label>
              <input type="radio"
                     value="0"
                     name="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER"
                     id="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER_off"
                     {if !$form_data['COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER']|intval}checked="checked"{/if}>
              <label for="COLISSIMO_DISPLAY_RETURN_LABEL_CUSTOMER_off">{l s='No' mod='colissimo'}</label>
              <a class="slide-button btn"></a>
            </span>
          </div>
          <div class="col-lg-9 col-lg-offset-3"></div>
        </div>
        <div class="colissimo-generate-label-customer-inputs">
          <div class="form-group">
            <label class="control-label col-lg-3 ">
          <span>
            {l s='Allow customers to generate their return label' mod='colissimo'}
          </span>
            </label>
            <div class="col-lg-9">
              <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio"
                       value="1"
                       name="COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER"
                       id="COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER_on"
                       {if $form_data['COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER']|intval}checked="checked"{/if}>
                <label for="COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER_on">{l s='Yes' mod='colissimo'}</label>
                <input type="radio"
                       value="0"
                       name="COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER"
                       id="COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER_off"
                       {if !$form_data['COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER']|intval}checked="checked"{/if}>
                <label for="COLISSIMO_GENERATE_RETURN_LABEL_CUSTOMER_off">{l s='No' mod='colissimo'}</label>
                <a class="slide-button btn"></a>
              </span>
            </div>
            <div class="col-lg-9 col-lg-offset-3">
              <div x-ms-format-detection="none" class="help-block">
                {l s='Customers will be able to generate and print return labels themselves' mod='colissimo'}
                <span></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-lg-3 ">
              <span>
                {l s='Enable mailbox return' mod='colissimo'}
              </span>
            </label>
            <div class="col-lg-9">
              <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio"
                       value="1"
                       name="COLISSIMO_ENABLE_MAILBOX_RETURN"
                       id="COLISSIMO_ENABLE_MAILBOX_RETURN_on"
                       {if $form_data['COLISSIMO_ENABLE_MAILBOX_RETURN']|intval}checked="checked"{/if}>
                <label for="COLISSIMO_ENABLE_MAILBOX_RETURN_on">{l s='Yes' mod='colissimo'}</label>
                <input type="radio"
                       value="0"
                       name="COLISSIMO_ENABLE_MAILBOX_RETURN"
                       id="COLISSIMO_ENABLE_MAILBOX_RETURN_off"
                       {if !$form_data['COLISSIMO_ENABLE_MAILBOX_RETURN']|intval}checked="checked"{/if}>
                <label for="COLISSIMO_ENABLE_MAILBOX_RETURN_off">{l s='No' mod='colissimo'}</label>
                <a class="slide-button btn"></a>
              </span>
            </div>
            <div class="col-lg-9 col-lg-offset-3"></div>
          </div>  
        </div>
      </div>
  </div>
  <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoShipmentsConfigForm">
        <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
      </button>
  </div>
</form>
<!-- /shipments Settings --> 