{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<style type="text/css">
    .ets-am-product-settings label.required:before {
        content: "*";
        color: red;
    }
</style>

<div class="ets-am-product-settings">
    <div class="row fields-setting">
        {if !empty($settings)}
            {foreach $settings as $index=>$setting}
                {if $setting}
                    <div class="{if $using_cart}col-md-12{else}col-md-6{/if}" {if $index == 'loyalty_reward' && $using_cart}style="display:none;"{/if}>
                        <div class="card {if $index == 'loyalty_reward'}loyalty_reward{elseif $index == 'aff_reward'}aff_reward{/if}"
                             data-type="{if $index == 'loyalty_reward'}loyalty_reward{elseif $index == 'aff_reward'}aff_reward{/if}">
                            <div class="card-header">
                                {if $index == 'loyalty_reward'}
                                    <h3 class="card-title">{l s='Loyalty program' mod='ets_affiliatemarketing'}</h3>
                                {elseif $index == 'aff_reward'}
                                    <h3 class="card-title">{l s='Affiliate program' mod='ets_affiliatemarketing'}</h3>
                                {/if}
                            </div>
                            <div class="card-body">
                                <div class="checkbox">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" data-toggle="switch" class="tiny"
                                                   name="{$index|escape:'html':'UTF-8'}_use_default" value="1"
                                                   {if $settings[$index].use_default == 1}checked="checked"{/if}>
                                            {l s='Use default setting' mod='ets_affiliatemarketing'}
                                        </label>
                                    </div>
                                </div>
                                {if !empty($setting)}
                                    {foreach $setting as $key=>$input}
                                        {if is_array($input) && $input.type == 'text'}
                                            <div class="form-group">
                                                <label {if $key != 'qty_min'} class="required" {/if}>{$input['label']|escape:'html':'UTF-8'}</label>
                                                {if isset($input.suffix) && $input.suffix}
                                                    <div class="input-group">
                                                        <input type="text" name="{$key|escape:'html':'UTF-8'}"
                                                               value="{if isset($input.value) && $input.value}{round($input.value, 2) nofilter}{elseif isset($input.default) && $input.default}{round($input.default, 2) nofilter}{/if}"
                                                               class="form-control {if isset($input.class) && $input.class}{$input.class|escape:'html':'UTF-8'}{/if}">
                                                        {if $is17}
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{$input.suffix|escape:'html':'UTF-8'}</span>
                                                        </div>
                                                        {else}
                                                        <span class="input-group-addon">{$input.suffix|escape:'html':'UTF-8'}</span>
                                                        {/if}
                                                    </div>
                                                {else}
                                                    <input type="text" name="{$key|escape:'html':'UTF-8'}"
                                                           value="{if isset($input.value) && $input.value}{$input.value|escape:'html':'UTF-8'}{elseif isset($input.default) && $input.default}{$input.default|escape:'html':'UTF-8'}{/if}"
                                                           class="form-control {if isset($input.class) && $input.class}{$input.class|escape:'html':'UTF-8'}{/if}">
                                                {/if}
                                            </div>
                                        {elseif is_array($input) && $input.type == 'ets_radio_group'}
                                            {assign 'radios_group' $input.values}
                                            {if !empty($radios_group)}
                                                <div class="form-group">
                                                    <label>{$input.label|escape:'html':'UTF-8'}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                {foreach $radios_group as $group}
                                                                    {if isset($group.is_all) && $group.is_all}
                                                                        <tr>
                                                                            <td class="w-10 border-r">
    										                                    <span class="title_box">
    										                                        <input type="radio"
                                                                                           name="{$key|escape:'html':'UTF-8'}"
                                                                                           value="{$group.value|escape:'html':'UTF-8'}"
                                                                                           id="{$group.id|escape:'html':'UTF-8'}" {if isset($input.value) && $input.value == $group.value} checked {elseif (!isset($input.value) || !$input.value) && isset($group.default) && $group.default} checked {/if} {if isset($group.data_decide) && $group.data_decide}data-decide="{$group.data_decide|escape:'html':'UTF-8'}"{/if}
                                                                                           class="{if isset($input.class) && $input.class}{$input.class|escape:'html':'UTF-8'}{/if}">
    										                                    </span>
                                                                            </td>
                                                                            <td>
                                                                                <label class="mb-0" for="{$group.id|escape:'html':'UTF-8'}"
                                                                                       style="width: 100%; font-weight: 400;">
                                                                                    <span class="title_box">{$group.title|escape:'html':'UTF-8'}</span>
                                                                                </label>
                                                                            </td>
                                                                        </tr>
                                                                        {break}
                                                                    {/if}
                                                                {/foreach}
                                                                {foreach $radios_group as $group}
                                                                    {if !isset($group.is_all)|| !$group.is_all}
                                                                        <tr>
                                                                            <td class="w-10 border-r">
    										                                        <span class="title_box">
    										                                            <input type="radio"
                                                                                               name="{$key|escape:'html':'UTF-8'}"
                                                                                               value="{$group.value|escape:'html':'UTF-8'}"
                                                                                               id="{$group.id|escape:'html':'UTF-8'}"
                                                                                                {if isset($input.value) && $input.value == $group.value} checked
                                                                                                {elseif (!isset($input.value) || !$input.value) && isset($group.default) && $group.default} checked  {/if}
                                                                                                {if isset($group.data_decide) && $group.data_decide} data-decide="{$group.data_decide|escape:'html':'UTF-8'}"{/if}
                                                                                               class="{if isset($input.class) && $input.class}{$input.class|escape:'html':'UTF-8'}{/if}">
    										                                        </span>
                                                                            </td>
                                                                            <td>
                                                                                <label class="mb-0" for="{$group.id|escape:'html':'UTF-8'}"
                                                                                       style="width: 100%; font-weight: 400;">
                                                                                    <span class="title_box">{$group.title|escape:'html':'UTF-8'}</span>
                                                                                </label>
                                                                            </td>
                                                                        </tr>
                                                                    {/if}
                                                                {/foreach}
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/if}

                                        {/if}
                                    {/foreach}

                                {/if}
                                <input type="hidden" name="id_product" value="{$id_product|escape:'html':'UTF-8'}">
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}
        {/if}
        <div class="col-md-12 px-15">
            <button type="button"
                    class="btn btn-primary js-ets-sm-save-setting-prd">{l s='Save tab settings' mod='ets_affiliatemarketing'}</button>
        </div>
    </div>
    <script type="text/javascript">
        var ets_am_msg_required = "{l s='This value field is invalid.' mod='ets_affiliatemarketing'}";
        var ets_am_link_ajax = "{$linkAjax nofilter}";
    </script>
    {if !$is17 }
    <script type="text/javascript" src="{$linkJs nofilter}"></script>
    {/if}
</div>