{*
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*}

<div id="custom_price_form" style="display:none">
    <div class="bootstrap">
        <div class="form-horizontal max-width-md">
            <h2>{l s='Customize flash sale rule' mod='flashsales'}</h2>
            <div class="well clearfix">
                <div class="form-group" data-toggle="has_reduction">
                    <label class="control-label col-lg-3" for="from">{l s='Period' mod='flashsales'}</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon">{l s='from' mod='flashsales'}</span>
                                    <input class="datepicker" type="text" name="custom_from" value="{$currentTab->getFieldValue($currentObject, 'from')|escape:'html':'UTF-8'}" style="text-align: center" id="custom_from" />
                                    <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <span class="input-group-addon">{l s='to' mod='flashsales'}</span>
                                    <input class="datepicker" type="text" name="custom_to" value="{$currentTab->getFieldValue($currentObject, 'to')|escape:'html':'UTF-8'}" style="text-align: center" id="custom_to" />
                                    <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="custom_errors" id="custom_period_errors">
                        {l s='The to date must be higher than from date' mod='flashsales'}
                    </div>
                </div>
                <div class="form-group" data-toggle="has_reduction">
                    <label class="control-label col-lg-3" for="reduction">{l s='Apply a discount of' mod='flashsales'}</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <input type="text" name="custom_reduction" id="custom_reduction" value="{$currentTab->getFieldValue($currentObject, 'reduction')|floatval}"/>
                            </div>
                            <div class="col-lg-6">
                                <select name="custom_reduction_type" id="custom_reduction_type">
                                    <option value="amount" {if $currentTab->getFieldValue($currentObject, 'reduction_type')|escape:'quotes':'UTF-8' == 'amount'} selected="selected"{/if}>{l s='Currency Units' mod='flashsales'}</option>
                                    <option value="percentage" {if $currentTab->getFieldValue($currentObject, 'reduction_type')|escape:'quotes':'UTF-8' == 'percentage'} selected="selected"{/if}>{l s='Percent' mod='flashsales'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="custom_errors" id="custom_price_errors">
                        {l s='Please select a valid integer reduction' mod='flashsales'}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <button type="button" id="validate_custom_price" class="btn btn-default chip" />
                        </i> {l s='Done' mod='flashsales'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
