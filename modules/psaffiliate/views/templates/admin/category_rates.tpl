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
<div id="container-category-rates">
    <div class="row">
        <div class="panel clearfix">
            <div class="alert alert-warning">{l s='Commission percent and value: -1 means that we will not take the rule into consideration, 0 means that we will give 0 commission.' mod='psaffiliate'}</div>
            <form method="POST" id="form_category_rates">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{l s='ID' mod='psaffiliate'}</th>
                        <th>{l s='Name' mod='psaffiliate'}</th>
                        <th>{l s='Commission percent' mod='psaffiliate'}</th>
                        <th>{l s='Commission value' mod='psaffiliate'}</th>
                        <th>{l s='Multiplier' mod='psaffiliate'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$categories item=category}
                        <tr>
                            <td>{$category.id_category|escape:'htmlall':'utf-8'}</td>
                            <td>{$category.name|escape:'htmlall':'utf-8'}</td>
                            <td>
                                <div class="input-group">
                                    <input name="rates_percent[{$category.id_category|escape:'htmlall':'utf-8'}]"
                                           value="{if is_numeric($category.rate_percent)}{$category.rate_percent|escape:'htmlall':'utf-8'|string_format:"%.2f"}{else}{$category.rate_percent|escape:'htmlall':'utf-8'}{/if}"
                                           type="text"/>
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input name="rates_value[{$category.id_category|escape:'htmlall':'utf-8'}]"
                                           value="{if is_numeric($category.rate_value)}{$category.rate_value|escape:'htmlall':'utf-8'|string_format:"%.2f"}{else}{$category.rate_value|escape:'htmlall':'utf-8'}{/if}"
                                           type="text"/>
                                    <span class="input-group-addon">{$currency_iso|escape:'htmlall':'utf-8'}</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input name="multiplier[{$category.id_category|escape:'htmlall':'utf-8'}]"
                                           value="{if is_numeric($category.multiplier)}{$category.multiplier|escape:'htmlall':'utf-8'|string_format:"%.2f"}{else}{$category.multiplier|escape:'htmlall':'utf-8'}{/if}"
                                           type="text"/>
                                    <span class="input-group-addon">*</span>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                <div class="panel-footer">
                    <button type="submit" value="1" id="configuration_form_submit_btn"
                            name="submitCategoryCommissionRates" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Save' mod='psaffiliate'}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>