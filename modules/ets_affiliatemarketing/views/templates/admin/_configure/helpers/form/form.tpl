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

{extends file="helpers/form/form.tpl"}
{block name="legend"}
    {if $field.title}
        {if $table == 'send'}
            <span class="gdpr_close_form">{l s='Close' mod='ets_affiliatemarketing'}</span>
        {/if}
    {/if}
{/block}
{block name="label"}
    {if isset($input.label)}
        <label class="control-label col-lg-3{if ((isset($input.required) && $input.required) || (isset($input.showrequired) && $input.showrequired)) && $input.type != 'radio'} required{/if}{if isset($input.fill) && $input.fill} eam-fill{/if}">
            {if isset($input.hint)}
            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
                            {foreach $input.hint as $hint}
                                {if is_array($hint)}
                                    {$hint.text|escape:'html':'UTF-8'}
                                {else}
                                    {$hint|escape:'html':'UTF-8'}
                                {/if}
                            {/foreach}
                        {else}
                            {$input.hint|escape:'html':'UTF-8'}
                        {/if}">
                {/if}
                {$input.label nofilter}
                {if isset($input.hint)}
                </span>
            {/if}
        </label>
    {/if}
{/block}
{block name="input_row"}
    {if isset($input.caption_before) && $input.caption_before}
        <h3 class="ets-am-caption-field">{$input.caption_before|escape:'html':'UTF-8'}</h3>
        {$smarty.block.parent}
    {elseif isset($input.divider_before) && $input.divider_before}
        <div class="divider-config"></div>
        {$smarty.block.parent}
    {elseif $input.name == 'ETS_AM_REF_ENABLED_MULTI_LEVEL'}
        {$smarty.block.parent}
        <div class="form-group">
            <button type="button"
                    class="btn btn-default btn-add-level" name="ETS_AM_ADD_LEVEL"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/></svg></i> {l s='Add level' mod='ets_affiliatemarketing'}</button>
        </div>
    {elseif $input.name == 'ETS_AM_LOYALTY_AMOUNT'}
        <div class="eam-input-loyalty-amount">
            {$smarty.block.parent}
        </div>
        {elseif $input.name == 'ETS_AM_LOYALTY_AMOUNT_PER'}
        <div class="eam-input-loyalty-per">
            {$smarty.block.parent}
        </div>
    {elseif $input.type == 'custom_discount'}
        <div class="form-group row form_{$input.name|lower|escape:'html':'UTF-8'}">
            <label class="control-label col-lg-3">
                {$input.label|escape:'html':'UTF-8'}
            </label>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" name="{$input.id|escape:'html':'UTF-8'}" value="{$fields_value[$input.id]|escape:'html':'UTF-8'}" />
                    </div>
                    <div class="col-lg-2">
                        <select name="{$input.items[0]|escape:'html':'UTF-8'}">
                            {foreach $input._currencies as $cur}
                                <option value="{$cur.id_currency|intval}" {if !$fields_value[$input.items[0]] && $input.default_currency == $cur.id_currency}selected="selected"{elseif $fields_value[$input.items[0]] == $cur.id_currency}selected="selected"{/if}>{$cur.iso_code|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select name="{$input.items[1]|escape:'html':'UTF-8'}">
                            <option value="0" {if $fields_value[$input.items[1]] == '0'} selected="selected"{/if}>Tax excluded</option>
                            <option value="1" {if $fields_value[$input.items[1]] == '1' } selected="selected"{/if}>Tax included</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <select name="{$input.items[2]|escape:'html':'UTF-8'}">
                            <option value="0" {if $fields_value[$input.items[2]] == '0'} selected="selected"{/if}>Shipping excluded</option>
                            <option value="1" {if $fields_value[$input.items[2]] == '1' } selected="selected"{/if}>Shipping included</option>
                        </select>
                    </div>
                </div>
                {if isset($input.desc)}
                    <p class="help-block">{$input.desc|escape:'html':'UTF-8'}</p>
                {/if}
            </div>
        </div>
    {elseif $input.type == 'custom_amount'}
        <div class="form-group row form_{$input.name|lower|escape:'html':'UTF-8'}">
            <label class="control-label col-lg-3">
                {$input.label|escape:'html':'UTF-8'}
            </label>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-3">
                        <input type="text" name="{$input.id|escape:'html':'UTF-8'}" value="{$fields_value[$input.id]|escape:'html':'UTF-8'}" />
                    </div>
                    <div class="col-lg-3">
                        <select name="{$input.items[0]|escape:'html':'UTF-8'}">
                            {foreach $input._currencies as $cur}
                                <option value="{$cur.id_currency|intval}" {if !$fields_value[$input.items[0]] && $input.default_currency == $cur.id_currency}selected="selected"{elseif $fields_value[$input.items[0]] == $cur.id_currency}selected="selected"{/if}>{$cur.iso_code|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <select name="{$input.items[1]|escape:'html':'UTF-8'}">
                            <option value="0" {if $fields_value[$input.items[1]] == '0'} selected="selected"{/if}>Tax excluded</option>
                            <option value="1" {if $fields_value[$input.items[1]] == '1' } selected="selected"{/if}>Tax included</option>
                        </select>
                    </div>
                </div>
                {if isset($input.desc)}
                    <p class="help-block">{$input.desc|escape:'html':'UTF-8'}</p>
                {/if}
            </div>
        </div>
    {elseif strpos($input.type, 'custom_discount_') !== false}
        {**}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="input_row"}
    {if $input.name=='ETS_AM_REF_OFFER_VOUCHER'}
        <h3>{l s='Customer\'s voucher code' mod='ets_affiliatemarketing'}</h3>
    {/if}
    {if $input.name=='ETS_AM_SELL_OFFER_VOUCHER'}
        <h3>{l s='Sponsor\'s voucher code' mod='ets_affiliatemarketing'}</h3>
    {/if}
    <div class="form-group form_{$input.name|lower|escape:'html':'UTF-8'}">
        {$smarty.block.parent}
    </div>
    {if $input.name=='ETS_AM_REF_MAX_INVITATION'}
        <div class="form-group ">
            <label class="control-label col-lg-4">{l s='Clear QR code cache' mod='ets_affiliatemarketing'} </label>
            <div class="col-lg-5">
                <button class="btn btn-default clear_qr_code_cache"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i> {l s='Clear' mod='ets_affiliatemarketing'}</button>
            </div>
        </div>
    {/if}
{/block}
{block name="input"}
    {if $input.type == 'ets_checkbox_group'}
        {assign 'checkboxs_group' $input.values}
        {if !empty($checkboxs_group)}
            {assign var="array_values" value=","|explode:$fields_value[$input.name]}
            <div class="row">
                <div class="{if $input.name == 'ETS_AM_WAITING_STATUS' ||  $input.name == 'ETS_AM_VALIDATED_STATUS' || $input.name == 'ETS_AM_CANCELED_STATUS' }col-lg-10{else}col-lg-12{/if}">
                    <table class="table table-bordered mb-0">
                        <tbody>
                        {foreach $checkboxs_group as $group}
                            {if isset($group.is_all) && $group.is_all}
                                <tr>
                                    <td class="w-10 border-r">
                                        <span class="title_box">
                                            <input class="mt-5" type="checkbox"
                                                   name="{$input.name|escape:'html':'UTF-8'}[]"
                                                   value="{$group.value nofilter}"
                                                   id="{$group.id nofilter}" {if $array_values && in_array($group.value, $array_values)} checked {elseif $fields_value[$input.name] == $group.value} checked{/if} {if isset($group.data_decide) && $group.data_decide}data-decide="{$group.data_decide nofilter}"{/if} />
                                        </span>
                                    </td>
                                    <td>
                                        <label class="mb-0" for="{$group.id nofilter}" style="width: 100%; font-weight: 400;">
                                            <span class="title_box">{$group.title|escape:'html':'UTF-8'}</span>
                                        </label>
                                    </td>
                                </tr>
                                {break}
                            {/if}
                        {/foreach}
                        {foreach $checkboxs_group as $group}
                            {if !isset($group.is_all) || !$group.is_all}
                                <tr>
                                    <td class="w-10 border-r">
                                        <span class="title_box">
                                            <input class="mt-5" type="checkbox"
                                                   name="{$input.name|escape:'html':'UTF-8'}[]"
                                                   value="{$group.value nofilter}"
                                                   id="{$group.id nofilter}" {if $array_values && in_array($group.value, $array_values)} checked {elseif $fields_value[$input.name] == $group.value} checked {/if} {if isset($group.data_decide) && $group.data_decide}data-decide="{$group.data_decide nofilter}"{/if} />
                                        </span>
                                    </td>
                                    <td>
                                        <label class="mb-0" class="mb-0" for="{$group.id nofilter}"
                                               style="width: 100%; font-weight: 400;">
                                            <span class="title_box">{$group.title nofilter}</span>
                                        </label>
                                    </td>
                                </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                {if $input.name == 'ETS_AM_WAITING_STATUS' || $input.name == 'ETS_AM_VALIDATED_STATUS' || $input.name == 'ETS_AM_CANCELED_STATUS'}
                    <div class="col-md-2">
                        <button type="button" class="btn btn-default js-eam-toggle-states-payment states-hide">
                            <i class="ets_svg">
                                <svg class="ets_svg_add" width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/></svg>
                                <svg class="ets_svg_minus" width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-1216q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h1216q40 0 68 28t28 68z"/></svg>
                            </i>
                        </button>
                    </div>
                {/if}
            </div>
        {/if}
    {elseif $input.type == 'ets_radio_group'}
        {assign 'radios_group' $input.values}
        {if !empty($radios_group)}
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-bordered mb-0">
                        <tbody>
                        {foreach $radios_group as $group}
                            {if isset($group.is_all) && $group.is_all}
                                <tr>
                                    <td class="w-10 border-r">
                                    <span class="title_box">
                                        <input class="mt-5" type="radio" name="{$input.name|escape:'html':'UTF-8'}"
                                               value="{$group.value nofilter}"
                                               id="{$group.id nofilter}" {if $fields_value[$input.name] == $group.value} checked {elseif !$fields_value[$input.name] && isset($group.default) && $group.default} checked {/if} {if isset($group.data_decide) && $group.data_decide} data-decide="{$group.data_decide nofilter}"{/if}>
                                    </span>
                                    </td>
                                    <td>
                                        <label class="mb-0" for="{$group.id nofilter}" style="width: 100%; font-weight: 400;">
                                            <span class="title_box">{$group.title nofilter}</span>
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
                                            <input class="mt-5" type="radio" name="{$input.name|escape:'html':'UTF-8'}"
                                                   value="{$group.value nofilter}" id="{$group.id nofilter}"
                                                    {if $fields_value[$input.name] == $group.value} checked
                                                    {elseif !$fields_value[$input.name] && isset($group.default) && $group.default} checked  {/if}
                                                    {if isset($group.data_decide) && $group.data_decide} data-decide="{$group.data_decide|escape:'html':'UTF-8'}"{/if}>
                                        </span>
                                    </td>
                                    <td>
                                        <label class="mb-0" for="{$group.id nofilter}" style="width: 100%; font-weight: 400;">
                                            <span class="title_box">{$group.title nofilter}</span>
                                        </label>
                                    </td>
                                </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
    {elseif $input.type =='ets_radio_group_tree'}
        {assign 'radios_group' $input.values}
        {assign var="array_values" value=","|explode:$fields_value[$input.name]}
        <div class="row">
            {foreach $radios_group as $group}
                {if (isset($group.options) && $group.options) || (isset($group.tree_html) && $group.tree_html) || (isset($group.tree_data) && $group.tree_data)}
                    <div class="col-lg-8">
                        <div class="radio">
                            <label>
                                <input type="radio" name="{$input.name|escape:'html':'UTF-8'}" class="has-tree-option"
                                       data-tree="#{$input.name|escape:'html':'UTF-8'}_TREE" value="{$group.value nofilter}"
                                       {if $fields_value[$input.name] && !in_array('ALL', $array_values)}checked{/if}>
                                {$group.title|escape:'html':'UTF-8'}
                            </label>
                        </div>
                        <div class="tree-options" id="{$input.name|escape:'html':'UTF-8'}_TREE" style="display: none;">
                            {if isset($group.options) && $group.options}
                                <table class="table table-bordered">
                                    {foreach $group.options as $option}
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" value="{$option.value nofilter}"
                                                       id="{$option.id|escape:'html':'UTF-8'}"
                                                       {if $array_values && in_array($option.value, $array_values)}checked{/if}>
                                            </td>
                                            <td>
                                                <label for="{$option.id|escape:'html':'UTF-8'}" style="width: 100%; font-weight: 400;">
                                                    {$option.title|escape:'html':'UTF-8'}
                                                </label>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </table>
                            {/if}
                            {if isset($group.tree_html) && $group.tree_html}
                                {$group.tree_html nofilter}
                            {/if}
                        </div>
                    </div>
                    {if isset($group.tree_data) && $group.tree_data}
                        <div>
                            {$group.tree_data->render() nofilter}
                        </div>
                    {/if}
                {else}
                    <div class="col-lg-4">
                        <div class="radio">
                            <label>
                                <input type="radio" name="{$input.name|escape:'html':'UTF-8'}" class="no-tree-option"
                                       data-tree="#{$input.name|escape:'html':'UTF-8'}_TREE" value="{$group.value|escape:'html':'UTF-8'}"
                                       {if $fields_value[$input.name] && in_array($group.value, $array_values)}checked{elseif !$fields_value[$input.name] && isset($group.default) && $group.default}checked{/if}>
                                {$group.title|escape:'html':'UTF-8'}
                            </label>
                        </div>
                    </div>
                {/if}
            {/foreach}
        </div>
    {elseif $input.type == 'text_search_prd'}
        <input class="ets_snw_search_ids" id="{$input.name|escape:'html':'UTF-8'}_SEARCH" data-target="{$input.name|escape:'html':'UTF-8'}" type="text"
               autocomplete="off" class="form-control"
               placeholder="{l s='Search by name, reference and ID' mod='ets_affiliatemarketing'}" value="">
        <input class="ets_snw_ids" type="hidden" name="{$input.name|escape:'html':'UTF-8'}"
               value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"/>
        <ul class="snw_products_added">
            {Module::getInstanceByName('ets_affiliatemarketing')->hookDisplaySnwProductList(['ids'=>$fields_value[$input.name]]) nofilter}
        </ul>
    {elseif $input.type == 'file' && isset($input.is_image) && $input.is_image}
        {$smarty.block.parent}
        {if $fields_value[$input.name] }
            <img src="{$path_banner|escape:'html':'UTF-8'}{$fields_value[$input.name] nofilter}?time={time()|escape:'html':'UTF-8'}" alt="{$fields_value[$input.name]|escape:'html':'UTF-8'}"
                 class="image-preview">
            <button type="button" class="eam-btn-delete-file js-eam-btn-delete-file" data-name="{$input.name|escape:'html':'UTF-8'}">
                <i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i></button>
        {/if}
    {elseif $input.type == 'categories_tree'}
		{$input.tree nofilter}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='description'}
    {if $input.type == 'file' && isset($input.is_image) && $input.is_image}
        {$smarty.block.parent}
        <p class="help-block">{l s='Available image type: jpg, png, gif, jpeg' mod='ets_affiliatemarketing'}. {l s='Limit' mod='ets_affiliatemarketing'} {Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|escape:'html':'UTF-8'}Mb</p>

    {elseif isset($input.desc) && !is_array($input.desc)}
        <p class="help-block">{$input.desc|replace:'[highlight]':'<code>'|replace:'[end_highlight]':'</code>' nofilter}</p>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}