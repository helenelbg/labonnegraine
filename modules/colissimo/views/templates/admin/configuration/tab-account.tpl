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

<form class="form-horizontal"
      action="#"
      name="colissimo_account_config_form"
      id="colissimo-account-config-form"
      method="post"
      enctype="multipart/form-data">
  <div class="panel">
    <div class="panel-heading">
      <i class="icon-cogs"></i>
      {l s='Configuration' mod='colissimo'}
    </div>
    <div class="row">
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span class="label-tooltip"
                data-toggle="tooltip"
                data-html="true"
                data-original-title="{l s='Your account number is indicated on your contract' mod='colissimo'}">
            {l s='Enable logs' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg ">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_LOGS"
                   id="COLISSIMO_LOGS_on"
                   {if $form_data['COLISSIMO_LOGS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_LOGS_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_LOGS"
                   id="COLISSIMO_LOGS_off"
                   {if !$form_data['COLISSIMO_LOGS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_LOGS_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
      <div class="form-group">
        <div class="col-lg-9 col-lg-offset-3">
          <p class="alert-text">
            {l s='Logs files are stored in the logs directory of the module. Files are rotated every month.' mod='colissimo'}
            <br/>
            {l s='You can download the current file by clicking' mod='colissimo'}
            <a target="_blank"
               href="{$link->getAdminLink('AdminColissimoLogs')|escape:'htmlall':'UTF-8'}&action=downloadLogFile">{l s='here' mod='colissimo'}</a>.<br/>
          </p>
        </div>
      </div>
    </div>
    <div class="col-lg-offset-3 colissimo-box">
      <div class="panel">
        <div class="row">
          <p class="colissimo-box-title col-lg-offset-3">{l s='Colissimo Box credentials' mod='colissimo'}</p>
          <div class="form-group">
            <label class="control-label col-lg-3">
              <span class="label-tooltip"
                    data-toggle="tooltip"
                    data-html="true"
                    data-original-title="{l s='Your account number is indicated on your contract' mod='colissimo'}">
                {l s='Contract number' mod='colissimo'}
              </span>
            </label>
            <div class="col-lg-9">
              <div class="input-group fixed-width-xxl">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text"
                       name="COLISSIMO_ACCOUNT_LOGIN"
                       class="input fixed-width-xxl"
                       value="{$form_data['COLISSIMO_ACCOUNT_LOGIN']|escape:'htmlall':'UTF-8'}">
              </div>
            </div>
            <div class="col-lg-9 col-lg-offset-3"></div>
          </div>
          <div class="form-group">
            <label class="control-label col-lg-3">
              <span class="label-tooltip"
                    data-toggle="tooltip"
                    data-html="true"
                    data-original-title="{l s='The password was sent by email to the contract address' mod='colissimo'}">
                {l s='Password' mod='colissimo'}
              </span>
            </label>
            <div class="col-lg-9">
              <div class="input-group fixed-width-xxl">
                <span class="input-group-addon"><i class="icon-key"></i></span>
                <input type="password"
                       name="COLISSIMO_ACCOUNT_PASSWORD"
                       class="input fixed-width-xxl"
                       value="{$form_data['COLISSIMO_ACCOUNT_PASSWORD']|escape:'htmlall':'UTF-8'}">
              </div>
            </div>
            <div class="col-lg-9 col-lg-offset-3"></div>
          </div>
          <div class="form-group">
            <label class="control-label col-lg-3 ">
              <span class="label-tooltip"
                    data-toggle="tooltip"
                    data-html="true"
                    data-original-title="{l s='Check your product list on your contract' mod='colissimo'}">
                {l s='Account type' mod='colissimo'}
              </span>
            </label>
            <div class="col-lg-9">
              <div class="checkbox">
                <label>
                  <input type="checkbox"
                         name="COLISSIMO_ACCOUNT_TYPE[][]"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['FRANCE'])}checked="true"{/if}
                         value="FRANCE">
                  {l s='France (home, PUDO, return)' mod='colissimo'}
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox"
                         name="COLISSIMO_ACCOUNT_TYPE[][]"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['OM'])}checked="true"{/if}
                         value="OM">
                  {l s='Overseas (home)' mod='colissimo'}
                </label>
              </div>
              <div class="checkbox child">
                <label>
                  <input type="checkbox"
                         name="COLISSIMO_ACCOUNT_TYPE[][]"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['ECOOM'])}checked="true"{/if}
                         value="ECOOM">
                    {l s='Eco Overseas' mod='colissimo'}
                </label>
                <p class="help-block">
                   {l s='Offer available only for customers who drop off or collect directly from Colissimo platforms. Do not hesitate to contact your sales representative for more information.' mod='colissimo'}
                </p>
              </div>   
              <div class="checkbox">
                <label>
                  <input type="checkbox"
                         name="COLISSIMO_ACCOUNT_TYPE[][]"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['EUROPE'])}checked="true"{/if}
                         value="EUROPE">
                  {l s='Europe' mod='colissimo'}
                </label>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox"
                         name="COLISSIMO_ACCOUNT_TYPE[][]"
                         {if isset($form_data['COLISSIMO_ACCOUNT_TYPE']['WORLDWIDE'])}checked="true"{/if}
                         value="WORLDWIDE">
                  {l s='Worldwide' mod='colissimo'}
                </label>
              </div>
            </div>
            <div class="col-lg-9 col-lg-offset-3"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submitColissimoAccountConfigForm">
        <i class="process-icon-save"></i>
        {l s='Save' mod='colissimo'}
      </button>
    </div>
  </div>
</form>
<div class="panel">
  <div class="panel-heading">
    <i class="icon-envelope"></i>
    {l s='Colissimo Address' mod='colissimo'}
  </div>
  <div class="clearfix">
    <div class="alert alert-info">
      {l s='Please enter your physical address for your returns in case of non-delivery (no CEDEX admitted)' mod='colissimo'}
    </div>
    <div class="row colissimo-return-address">
      <div class="form-group">
        <label class="control-label col-lg-3 ">
          <span>
            {l s='Use a different address for the reception of return shipement' mod='colissimo'}
          </span>
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg colissimo-set-return-address">
            <input type="radio"
                   value="1"
                   name="COLISSIMO_USE_RETURN_ADDRESS"
                   id="COLISSIMO_USE_RETURN_ADDRESS_on"
                   {if $form_data['COLISSIMO_USE_RETURN_ADDRESS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_RETURN_ADDRESS_on">{l s='Yes' mod='colissimo'}</label>
            <input type="radio"
                   value="0"
                   name="COLISSIMO_USE_RETURN_ADDRESS"
                   id="COLISSIMO_USE_RETURN_ADDRESS_off"
                   {if !$form_data['COLISSIMO_USE_RETURN_ADDRESS']|intval}checked="checked"{/if}>
            <label for="COLISSIMO_USE_RETURN_ADDRESS_off">{l s='No' mod='colissimo'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
        <div class="col-lg-9 col-lg-offset-3"></div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <form class="form-horizontal adr-form"
            action="#"
            name="colissimo_sender_config_form"
            id="colissimo-sender-config-form"
            method="post"
            enctype="multipart/form-data">
        <div class="panel">
          <div class="panel-heading">
            {l s='Shipping Address' mod='colissimo'}
          </div>
          <div class="row">
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Company' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-lg">
                  <span class="input-group-addon" id="sender-company_counter">35</span>
                  <input type="text"
                         autocomplete="organization"
                         id="sender-company"
                         name="sender_company"
                         class="input fixed-width-lg"
                         required="required"
                         value="{if $form_data['sender_company']}{$form_data['sender_company']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Lastname' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-lg">
                  <span class="input-group-addon" id="sender-lastname_counter">35</span>
                  <input type="text"
                         autocomplete="family-name"
                         id="sender-lastname"
                         name="sender_lastname"
                         class="input fixed-width-lg"
                         required="required"
                         value="{if $form_data['sender_lastname']}{$form_data['sender_lastname']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Firstname' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-lg">
                  <span class="input-group-addon" id="sender-firstname_counter">35</span>
                  <input type="text"
                         autocomplete="given-name"
                         id="sender-firstname"
                         name="sender_firstname"
                         class="input fixed-width-lg"
                         required="required"
                         value="{if $form_data['sender_firstname']}{$form_data['sender_firstname']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Address 1' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="sender-address1_counter">35</span>
                  <input type="text"
                         autocomplete="address-line1"
                         id="sender-address1"
                         name="sender_address1"
                         class="input  fixed-width-xxl"
                         required="required"
                         value="{if $form_data['sender_address1']}{$form_data['sender_address1']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3">
                <span>{l s='Address 2' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="sender-address2_counter">35</span>
                  <input type="text"
                         autocomplete="address-line2"
                         id="sender-address2"
                         name="sender_address2"
                         class="input  fixed-width-xxl"
                         value="{if $form_data['sender_address2']}{$form_data['sender_address2']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3">
                <span>{l s='Address 3' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="sender-address3_counter">35</span>
                  <input type="text"
                         autocomplete="address-line3"
                         id="sender-address3"
                         name="sender_address3"
                         class="input  fixed-width-xxl"
                         value="{if $form_data['sender_address3']}{$form_data['sender_address3']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3">
                <span>{l s='Address 4' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="sender-address4_counter">35</span>
                  <input type="text"
                         autocomplete="address-line4"
                         id="sender-address4"
                         name="sender_address4"
                         class="input  fixed-width-xxl"
                         value="{if $form_data['sender_address4']}{$form_data['sender_address4']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='City' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="sender-city_counter">30</span>
                  <input type="text"
                         autocomplete="address-level2"
                         id="sender-city"
                         name="sender_city"
                         class="input  fixed-width-xxl"
                         required="required"
                         value="{if $form_data['sender_city']}{$form_data['sender_city']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Zipcode' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="fixed-width-sm">
                  <input type="text"
                         autocomplete="postal-code"
                         name="sender_zipcode"
                         class="input  fixed-width-sm"
                         value="{if $form_data['sender_zipcode']}{$form_data['sender_zipcode']|escape:'htmlall':'UTF-8'}{/if}"
                         required="required">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group"><label class="control-label col-lg-3 required">
                <span>{l s='Country' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <select id="colissimo-country" name="sender_country" class="cls-chosen fixed-width-xl" style="display: none;">
                  {foreach from=$address_countries key=iso item=name}
                    <option value="{$iso|escape:'html':'UTF-8'}"
                            {if $form_data['sender_country'] == $iso }selected{/if}>
                      {$name|escape:'html':'UTF-8'}
                    </option>
                  {/foreach}
                </select>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="validate-phone">
              <div class="form-group">
                <label class="control-label col-lg-3 required"> {l s='Phone' mod='colissimo'}</label>
                <div class="col-lg-9">
                  <input type="text"
                         id="sender-phone"
                         name="sender_phone[main]"
                         class="input fixed-width-xl"
                         value="{if $form_data['sender_phone']}{$form_data['sender_phone']|escape:'htmlall':'UTF-8'}{/if}"
                         required="required">
                </div>
              </div>
              <img src="{$colissimo_img_path|escape:'html':'UTF-8'}icons/icon_valid.png"
                   class="colissimo-mobile-valid js-colissimo-mobile-valid"/>
              <img src="{$colissimo_img_path|escape:'html':'UTF-8'}icons/icon_invalid.png"
                   class="colissimo-mobile-invalid js-colissimo-mobile-invalid"/>
            </div>
            <input type="hidden" class="js-colissimo-is-mobile-valid" name="colissimo_is_mobile_valid" value=""/>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Email' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="sender-email_counter">80</span>
                  <input type="text"
                         autocomplete="email"
                         id="sender-email"
                         name="sender_email"
                         class="input  fixed-width-xxl"
                         required="required"
                         value="{if $form_data['sender_email']}{$form_data['sender_email']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="80"
                         maxlength="80">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
          </div>
          <div class="panel-footer">
            <button type="submit" class="btn btn-default pull-right" name="submitColissimoSenderAddressConfigForm">
              <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="col-md-6 col-sm-12" id="colissimo-return-address">
      <form class="form-horizontal adr-form"
            action="#"
            name="colissimo_return_config_form"
            id="colissimo-return-config-form"
            method="post"
            enctype="multipart/form-data">

        <div class="panel">
          <div class="panel-heading">
            {l s='Return Address' mod='colissimo'}
          </div>
          <div class="row">
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Company' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-lg">
                  <span class="input-group-addon" id="return-company_counter">35</span>
                  <input type="text"
                         autocomplete="organization"
                         id="return-company"
                         name="return_company"
                         class="input fixed-width-lg"
                         required="required"
                         value="{if $form_data['return_company']}{$form_data['return_company']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Lastname' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-lg">
                  <span class="input-group-addon" id="return-lastname_counter">35</span>
                  <input type="text"
                         autocomplete="family-name"
                         id="return-lastname"
                         name="return_lastname"
                         class="input fixed-width-lg"
                         required="required"
                         value="{if $form_data['return_lastname']}{$form_data['return_lastname']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Firstname' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-lg">
                  <span class="input-group-addon" id="return-firstname_counter">35</span>
                  <input type="text"
                         autocomplete="given-name"
                         id="return-firstname"
                         name="return_firstname"
                         class="input fixed-width-lg"
                         required="required"
                         value="{if $form_data['return_firstname']}{$form_data['return_firstname']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Address 1' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="return-address1_counter">35</span>
                  <input type="text"
                         autocomplete="address-line1"
                         id="return-address1"
                         name="return_address1"
                         class="input  fixed-width-xxl"
                         required="required"
                         value="{if $form_data['return_address1']}{$form_data['return_address1']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3">
                <span>{l s='Address 2' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="return-address2_counter">35</span>
                  <input type="text"
                         autocomplete="address-line2"
                         id="return-address2"
                         name="return_address2"
                         class="input  fixed-width-xxl"
                         value="{if $form_data['return_address2']}{$form_data['return_address2']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3">
                <span>{l s='Address 3' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="return-address3_counter">35</span>
                  <input type="text"
                         autocomplete="address-line3"
                         id="return-address3"
                         name="return_address3"
                         class="input  fixed-width-xxl"
                         value="{if $form_data['return_address3']}{$form_data['return_address3']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3">
                <span>{l s='Address 4' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="return-address4_counter">35</span>
                  <input type="text"
                         autocomplete="address-line4"
                         id="return-address4"
                         name="return_address4"
                         class="input  fixed-width-xxl"
                         value="{if $form_data['return_address4']}{$form_data['return_address4']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='City' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="return-city_counter">30</span>
                  <input type="text"
                         autocomplete="address-level2"
                         id="return-city"
                         name="return_city"
                         class="input  fixed-width-xxl"
                         required="required"
                         value="{if $form_data['return_city']}{$form_data['return_city']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="35"
                         maxlength="35">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Zipcode' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="fixed-width-sm">
                  <input type="text"
                         autocomplete="postal-code"
                         name="return_zipcode"
                         class="input  fixed-width-sm"
                         value="{if $form_data['return_zipcode']}{$form_data['return_zipcode']|escape:'htmlall':'UTF-8'}{/if}"
                         required="required">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="form-group"><label class="control-label col-lg-3 required">
                <span>{l s='Country' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <select name="return_country" class="cls-chosen fixed-width-lg" style="display: none;">
                  {foreach from=$address_countries key=iso item=name}
                    <option value="{$iso|escape:'html':'UTF-8'}"
                            {if $form_data['return_country'] == $iso }selected{/if}>
                      {$name|escape:'html':'UTF-8'}
                    </option>
                  {/foreach}
                </select>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
            <div class="validate-phone">
              <div class="form-group">
                <label class="control-label col-lg-3 required"> {l s='Phone' mod='colissimo'}</label>
                <div class="col-lg-9">
                  <input type="text"
                         id="return-phone"
                         name="return_phone[main]"
                         class="input fixed-width-xl"
                         value="{if $form_data['return_phone']}{$form_data['return_phone']|escape:'htmlall':'UTF-8'}{/if}"
                         required="required">
                </div>
              </div>
              <img src="{$colissimo_img_path|escape:'html':'UTF-8'}icons/icon_valid.png"
                   class="colissimo-mobile-valid js-colissimo-mobile-valid"/>
              <img src="{$colissimo_img_path|escape:'html':'UTF-8'}icons/icon_invalid.png"
                   class="colissimo-mobile-invalid js-colissimo-mobile-invalid"/>
            </div>
            <input type="hidden" class="js-colissimo-is-mobile-valid" name="colissimo_is_mobile_valid" value=""/>
            <div class="form-group">
              <label class="control-label col-lg-3 required">
                <span>{l s='Email' mod='colissimo'}</span>
              </label>
              <div class="col-lg-9">
                <div class="input-group input fixed-width-xxl">
                  <span class="input-group-addon" id="return-email_counter">80</span>
                  <input type="text"
                         autocomplete="email"
                         id="return-email"
                         name="return_email"
                         class="input  fixed-width-xxl"
                         required="required"
                         value="{if $form_data['return_email']}{$form_data['return_email']|escape:'htmlall':'UTF-8'}{/if}"
                         data-maxchar="80"
                         maxlength="80">
                </div>
              </div>
              <div class="col-lg-9 col-lg-offset-3"></div>
            </div>
          </div>
          <div class="panel-footer">
            <button type="submit" class="btn btn-default pull-right" name="submitColissimoReturnAddressConfigForm">
              <i class="process-icon-save"></i> {l s='Save' mod='colissimo'}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


{literal}
<script type="text/javascript">
    var genericErrorMessage = "{/literal}{l s='Webservice connexion could not be checked' mod='colissimo'}{literal}";
    var colissimoCredentialsToken = '{/literal}{getAdminToken tab='AdminColissimoTestCredentials'}{literal}';
    var addressErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
    var token = '{/literal}{getAdminToken tab='AdminModules'}{literal}';
    var colissimoSenderPhone = '{/literal}{if $form_data['sender_country']}{$form_data['sender_country']|escape:'htmlall':'UTF-8'}{/if}{literal}';
    var colissimoReturnPhone = '{/literal}{if $form_data['return_country']}{$form_data['return_country']|escape:'htmlall':'UTF-8'}{/if}{literal}';
    var onlyCountries = {/literal}{$address_countries|array_keys|json_encode}{literal};

    $(document).ready(function () {
        $(document).on('click', '#colissimo-check-credentials', function () {
            var colissimoCheckCredentials = $('#colissimo-check-credentials');

            testWSCredentials(colissimoCheckCredentials);
        });
    });

    $('select.cls-chosen').each(function(k, item){
        $(item).chosen({disable_search_threshold: 5, search_contains: true, width: '200px', });
    });
</script>
{/literal}
