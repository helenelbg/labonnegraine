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
{block name="input_row"}
    {if isset($input.global_field) && $input.global_field}
        </div>
        </div>
        <div id="fieldset_1" class="panel">
        <div class="panel-heading">
            <i class="icon-AdminAdmin"></i>
            {l s='Block Settings' mod='ets_crosssell'}: {$page_title|escape:'html':'UTF-8'}
        </div>
        <div class="form-wrapper">
    {/if}
     {if isset($input.first_field) && $input.first_field && isset($fields_position) && $fields_position}
        <ul id="field-positions" class="field-positions">
            {foreach from=$fields_position key='key' item='field_position'}
                {if isset($fields_value[$fields_postion_value[$key]])}
                    {assign var='value_text' value=$fields_value[$fields_postion_value[$key]]}
                {else}
                    {assign var='value_text' value=0}
                {/if}
                <li id="field_positions_{$field_position|escape:'html':'UTF-8'}">
                    <div>
                        <div class="title-field"> 
                            {if $control!='CUSTOM_PAGE'}
                                <span class="position_number" >
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg>
                                    <span>
                                        {$key+1|intval}
                                    </span>
                                </span>
                            {/if}
                            <span class="position_info">
                                {$_config_types[$field_position].title|escape:'html':'UTF-8'}
                                {if isset($_config_types[$field_position].desc)}
                                    <div class="desc">{$_config_types[$field_position].desc|escape:'html':'UTF-8'}</div>
                                {/if}
                            </span>
                        </div>
                        <div class="cross_field_config">
                            {if $control=='CUSTOM_PAGE'}
                                <code title="Click to copy">{literal}{hook{/literal} h='{$custom_hooks[$field_position]|escape:'html':'UTF-8'}'{literal}}{/literal}</code>
                            {/if}
                            <label class="ets_sc_switch{if $value_text} active{/if}">
                                <input class="ets_sc_field" type="checkbox" {if $value_text}checked ="checked"{/if} value="1" data-field="{$fields_postion_value[$key]|escape:'html':'UTF-8'}"/>
                                <span class="ets_sc_field_label on">{l s='On' mod='ets_crosssell'}</span>
                                <span class="ets_sc_field_label off">{l s='Off' mod='ets_crosssell'}</span>
                                <span class="ets_sc_field_circle"></span>
                            </label>
                            {if isset($_config_types[$field_position].setting)}
                                <div class="setting" data-setting="{$field_position|escape:'html':'UTF-8'}"><i class="icon-AdminAdmin"></i>{l s='Setting' mod='ets_crosssell'}</div>
                            {/if}
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
     {/if}   
     {if isset($input.begin_group) && $input.begin_group}
        <div class="ets-cs-form-group-field{if isset($input.form_group_class)} {$input.form_group_class|escape:'html':'UTF-8'}{/if}">
            <div class="popup_table"><div class="popup_tablecell">
            <div class="ets-cs-form-group-field-wapper">
            <span class="close-setting-field">{l s='Close' mod='ets_crosssell'}</span>
            <div class="setting-title" >{$input.title_group|escape:'html':'UTF-8'}</div>
            {if isset($input.hook_name) && $input.hook_name}
                <div class="form-group">
                    <div class="col-lg-12">
                        <p class="alert alert-info">{l s='Put' mod='ets_crosssell'} <code title="Click to copy">{literal}{hook{/literal} h='{$input.hook_name|escape:'html':'UTF-8'}'{literal}}{/literal}</code> {l s='on tpl file where you want to display the block' mod='ets_crosssell'}</p>
                    </div>
                </div>
            {/if}
     {/if}
     {$smarty.block.parent}
     {if isset($input.end_group) && $input.end_group}
            {if isset($input.warning) && $input.warning}    
                <div class="alert alert-warning">
                <strong>{$input.module_name|escape:'html':'UTF-8'}</strong> {$input.warning|escape:'html':'UTF-8'}
                </div>
            {/if}
            {if isset($input.info) && $input.info}    
                <div class="alert alert-info">
                 {$input.info nofilter}
                </div>
            {/if}
            <div class="popup_footer">
                <button class="module_form_cancel_btn_filed btn btn-default pull-left" type="button">
                    <i class="process-icon-cancel"></i>
                    {l s='Cancel' mod='ets_crosssell'}
                </button>
                <button class="module_form_submit_btn_filed btn btn-default pull-right" type="button">
                    <i class="process-icon-save"></i>
                    {l s='Save' mod='ets_crosssell'}
                </button>
            </div>
            </div>
         </div>
         </div>
        </div>
     {/if}
     {if $input.name=='ETS_CS_CACHE_LIFETIME'}
        <div class="form-group">
            <label class="control-label col-lg-3">
            </label>
            <div class="col-lg-6">
                <a href="#" class="ets_cs_clear_cache btn btn-default">
                    <i class="ets_cs_clear">
                        <svg width="16" height="20" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M960 1408l336-384h-768l-336 384h768zm1013-1077q15 34 9.5 71.5t-30.5 65.5l-896 1024q-38 44-96 44h-768q-38 0-69.5-20.5t-47.5-54.5q-15-34-9.5-71.5t30.5-65.5l896-1024q38-44 96-44h768q38 0 69.5 20.5t47.5 54.5z"/></svg>
                    </i>
                    <span class="a_text">{l s='Clear cache' mod='ets_crosssell'}</span>
                </a>
            </div>
        </div>
     {/if}
{/block}
{block name="input"}
    {if $input.type == 'search'}
        <div class="ets_cs_search_product_form">
            <input class="ets_cs_search_product" name="ets_cs_search_product" {if isset($input.placeholder)}placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} autocomplete="off" type="text" />
            <input class="ets_cs_product_ids" name="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" type="hidden" />
            <ul class="ets_cs_products" id="block_search_{$input.name|escape:'html':'UTF-8'}">
                {Module::getInstanceByName('ets_crosssell')->displaySearchProductList($fields_value[$input.name]) nofilter}
                <li class="ets_cs_product_loading"></li>
            </ul>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}