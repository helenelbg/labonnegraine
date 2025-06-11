{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code.
*
*  @author Active Design <office@activedesign.ro>
*  @copyright  2017-2018 Active Design
*  @license LICENSE.txt
*}
<form method="POST" id="datepicker_statistics" class="clearfix">
    <div class="col-md-4">
        <div class="row">
            <div class="col-xs-6">
                <div class="input-group">
                    <label class="input-group-addon">{l s='From' mod='psaffiliate'}</label>
                    <input name="datepickerFrom" id="datepickerFrom"
                           value="{if isset($datepickerFrom)}{$datepickerFrom|escape:'htmlall':'UTF-8'}{else}{"-90 days"|date_format:"%Y-%m-%d"}{/if}"
                           class="datepicker form-control" type="text">
                </div>
            </div>
            <div class="col-xs-6">
                <div class="input-group">
                    <label class="input-group-addon">{l s='To' mod='psaffiliate'}</label>
                    <input name="datepickerTo" id="datepickerTo"
                           value="{if isset($datepickerTo)}{$datepickerTo|escape:'htmlall':'UTF-8'}{else}{"now"|date_format:"%Y-%m-%d"}{/if}"
                           class="datepicker form-control" type="text">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <button type="submit" name="submitDatePicker" id="submitDatePicker" class="btn btn-default"><i
                        class="icon-filter"></i> {l s='Filter' mod='psaffiliate'}</button>
        </div>
    </div>
</form>
