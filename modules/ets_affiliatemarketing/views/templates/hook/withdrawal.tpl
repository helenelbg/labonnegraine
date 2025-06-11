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
{assign var='_svg_undo' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 896q0 156-61 298t-164 245-245 164-298 61q-172 0-327-72.5t-264-204.5q-7-10-6.5-22.5t8.5-20.5l137-138q10-9 25-9 16 2 23 12 73 95 179 147t225 52q104 0 198.5-40.5t163.5-109.5 109.5-163.5 40.5-198.5-40.5-198.5-109.5-163.5-163.5-109.5-198.5-40.5q-98 0-188 35.5t-160 101.5l137 138q31 30 14 69-17 40-59 40h-448q-26 0-45-19t-19-45v-448q0-42 40-59 39-17 69 14l130 129q107-101 244.5-156.5t284.5-55.5q156 0 298 61t245 164 164 245 61 298z"/></svg></i>'}
{assign var='_svg_search' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg></i>'}

<div class="ets-am-list-app eam-ox-auto">
    <div class="eam-minwidth-1000">
        <div class="table-responsive">
            <table class="table table-bordered eam-datatables">
                <thead>
                <tr>
                    {foreach $fields as $key=>$field}
                        <th class="{if $key == 'actions'}text-center{/if}">{$field.title|escape:'html':'UTF-8'}</th>
                    {/foreach}
                </tr>
                </thead>
                <tbody>
                    {if $results}
                        {foreach $results as $result}
                        <tr>
                            {foreach $fields as $key => $field}
                                {if $key == 'actions' && is_array($result[$key])}
                                    <td class="text-center">
                                        {if count($result[$key]) > 1}
                                            <div class="btn-group">
                                            {if isset($result[$key][0].link)}
                                                <a href="{$result[$key][0].link|escape:'html':'UTF-8'}" title="" class="btn btn-default"><i class="fa fa-{$result[$key][0].icon|escape:'html':'UTF-8'}"></i> {$result[$key][0].label|escape:'html':'UTF-8'}</a>
                                            {else}
                                              <button type="button" class="btn btn-default {$result[$key][0].class|escape:'html':'UTF-8'}" data-id="{$result[$key][0].id|escape:'html':'UTF-8'}" {if isset($result[$key][0].action)}data-action="{$result[$key][0].action|escape:'html':'UTF-8'}"{/if}><i class="fa fa-{$result[$key][0].icon|escape:'html':'UTF-8'}"></i> {$result[$key][0].label|escape:'html':'UTF-8'}</button>
                                            }
                                            {/if}
                                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                              </button>
                                              <ul class="dropdown-menu">
                                                {foreach $result[$key] as $k=>$v}
                                                    {if $k > 0}
                                                    <li><a href="javascript:void(0)" data-id="{$v.id|escape:'html':'UTF-8'}" class="{$v.class|escape:'html':'UTF-8'}" {if isset($v.action)}data-action="{$v.action|escape:'html':'UTF-8'}"{/if}><i class="fa fa-{$v.icon|escape:'html':'UTF-8'}"></i> {$v.label|escape:'html':'UTF-8'}</a></li>
                                                    {/if}
                                                {/foreach}
                                              </ul>
                                            </div>
                                        {elseif count($result[$key]) == 1}
                                            <button href="javascript:void(0)" class="btn btn-default {$result[$key][0].class|escape:'html':'UTF-8'}" {if isset($result[$key][0].action)}data-action="{$result[$key][0].action|escape:'html':'UTF-8'}"{/if} data-id="{$result[$key][0].id|escape:'html':'UTF-8'}"><i class="fa fa-{$result[$key][0].icon|escape:'html':'UTF-8'}"></i> {$result[$key][0].label|escape:'html':'UTF-8'}</button>
                                        {/if}
                                    </td>
                                {elseif $key == 'customer'}
                                    <td>
                                        <a href="{$link_customer|escape:'html':'UTF-8'}&configure=ets_affiliatemarketing&tabActive=reward_users&id_reward_users={$result.id_customer|escape:'html':'UTF-8'}&viewreward_users" title="{l s='View customer' mod='ets_affiliatemarketing'}">
                                            {if $result.firstname}
                                                {$result.firstname|escape:'html':'UTF-8'} {$result.lastname|escape:'html':'UTF-8'}
                                            {else}
                                                <span class="warning-deleted">{l s='User deleted' mod='ets_affiliatemarketing'} (ID: {$result.id_customer|escape:'html':'UTF-8'})</span>
                                            {/if}
                                        </a>
                                    </td>
                                 {elseif $key == 'status'}
                                    <td>
                                        {if $result.status == -1}
                                            <label class="label label-default">{l s='Declined' mod='ets_affiliatemarketing'}</label>
                                        {elseif $result.status == 0}
                                            <label class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</label>
                                        {else}
                                            <label class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</label>
                                        {/if}
                                    </td>
                                {else}
                                <td>{$result[$key] nofilter}</td>
                                {/if}
                            {/foreach}
                        </tr>
                        {/foreach}
                    {else}
                    <tr>
                        <td colspan="100%" style="text-align: center;">
                            {l s='No data found' mod='ets_affiliatemarketing'}
                        </td>
                    </tr>
                    {/if}
                </tbody>
            </table>
            <div class="eam-result-pagination">
                <div class="row">
                    <div class="col-lg-6 display-flex">
                        <div class="filter_limit">
                            <span>{l s='Show' mod='ets_affiliatemarketing'}</span>
                            <select name="limit" class="form-control input-select-limit">
                                <option value="30" {if $limit == 30} selected {/if}>30</option>
                                <option value="50" {if $limit == 50} selected {/if}>50</option>
                                <option value="100" {if $limit == 100} selected {/if}>100</option>
                            </select>
                            <span>{l s='entries' mod='ets_affiliatemarketing'}</span>
                        </div>
                        <span class="pull-right">{l s='Total: ' mod='ets_affiliatemarketing'}<strong>{$total_data|escape:'html':'UTF-8'}</strong> {l s='result(s) found' mod='ets_affiliatemarketing'}</span>
                    </div>
                    {if $total_page > 1}
                        <div class="col-lg-6"> 
                            <div class="eam-pagination">
                                <ul>
                                    {if $current_page > 1}
                                        <li class="{if $current_page==1} active {/if}">
                                            <a href="javascript:void(0)" data-page="1" class="js-eam-page-item">|<</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item"><</a>
                                        </li>
                                    {/if}
                                    {assign 'minRange' 1}
                                    {assign 'maxRange' $total_page}
                                    {if $total_page > 10}
                                        {if $current_page < ($total_page - 3)}
                                        {assign 'maxRange' $current_page + 2}
                                        {/if}
                                        {if $current_page > 3}
                                        {assign 'minRange' $current_page - 2}
                                        {/if}
                                    {/if}
                                    {if $minRange > 1}
                                    <li><span class="eam-page-3dot">...</span></li>
                                    {/if}
                                    {for $page=$minRange to $maxRange}
                                        <li class="{if $page == $current_page} active {/if}">
                                            <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}" class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                        </li>
                                    {/for}
                                    {if $maxRange < $total_page}
                                    <li><span class="eam-page-3dot">...</span></li>
                                    {/if}
                                    {if $current_page < $total_page}
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$current_page + 1|escape:'html':'UTF-8'}" class="js-eam-page-item"> > </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$total_page|escape:'html':'UTF-8'}" class="js-eam-page-item"> >| </a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                     {/if}
                </div>
            </div>
        </div>
       
        <div class="filter-datatable stats-data-reward">
            <form id="eamFormFilterHistoryReward">
                <div class="stat-filter col-lg-12">
                    <div class="clearfix" style="margin-top: 15px;">
                        <div class="filter-box-item">
                            <label>{l s='Status' mod='ets_affiliatemarketing'}</label>
                            <select name="status" class="form-group" style="display: inline-block; width: auto;">
                                <option value="">{l s='All' mod='ets_affiliatemarketing'}</option>
                                <option value="1"  {if isset($params.status) && $params.status == 1} selected="selected" {/if}>{l s='Approved' mod='ets_affiliatemarketing'}</option>
                                <option value="0"  {if isset($params.status) && $params.status == 0 && $params.status !== ''} selected="selected" {/if}>{l s='Pending' mod='ets_affiliatemarketing'}</option>
                                <option value="-1"  {if isset($params.status) && $params.status == -1} selected="selected" {/if}>{l s='Declined' mod='ets_affiliatemarketing'}</option>
                            </select>
                        </div>
                        <div class="filter-box-item">
                            <div class="filter_search">
                                <label>{l s='Customer' mod='ets_affiliatemarketing'}</label>
                                <input type="text" name="search" value="{$search|escape:'html':'UTF-8'}" class="form-control input-search input-search-suggestion" placeholder="{$search_placeholder|escape:'html':'UTF-8'}" autocomplete="off">
                                <div class="data-suggestion" data-type="withdraw"></div>
                                <input type="hidden" name="id_customer" value="{if isset($params.id_customer) && $params.id_customer}{$params.id_customer|escape:'html':'UTF-8'}{/if}">
                                {if isset($params.search) && $params.search}
                                    <span class="tag-query-search">{$params.search|escape:'html':'UTF-8'} <span class="remove-tag">{$_svg_close_icon nofilter}</span></span>
                                {/if}
                            </div>
                        </div>
                        <div class="filter-box-item">
                            <div>
                                <label>{l s='Time range' mod='ets_affiliatemarketing'}</label>
                                <select name="type_date_filter" class="form-control field-inline">
                                     <option value="all_times" {if (isset($params.type_date_filter) && $params.type_date_filter == 'all_times') || !isset($params.type_date_filter) || !$params.type_date_filter}selected="selected"{/if} >{l s='All the time' mod='ets_affiliatemarketing'}</option>
                                    <option value="this_month" {if isset($params.type_date_filter) && $params.type_date_filter == 'this_month'}selected="selected"{/if}>{l s="This month" mod='ets_affiliatemarketing'} - {date('m/Y') nofilter}</option>
                                    <option value="this_year" {if isset($params.type_date_filter) && $params.type_date_filter == 'this_year'}selected="selected"{/if}>{l s="This year" mod='ets_affiliatemarketing'} - {date('Y') nofilter}</option>
                                   
                                    <option value="time_ranger" {if isset($params.type_date_filter) && $params.type_date_filter == 'time_ranger'}selected="selected"{/if}>{l s='Time range' mod='ets_affiliatemarketing'}</option>
                                </select>
                                <div class="box-date-ranger">
                                    <input type="text" name="date_ranger" value="{if isset($params.date_from_reward) && $params.date_from_reward && isset($params.date_from_reward) && $params.date_from_reward}{date('Y/m/d', strtotime($params.date_from_reward)) nofilter} - {date('Y/m/d', strtotime($params.date_to_reward)) nofilter}{/if}" class="form-control eam_date_ranger_filter">
                                    <input type="hidden" name="date_from_reward" class="date_from_reward" value="{if isset($params.date_from_reward) && $params.date_from_reward}{date('Y-m-d', strtotime($params.date_from_reward)) nofilter}{else}{date('Y-m-01') nofilter}{/if}">
                                    <input type="hidden" name="date_to_reward" class="date_to_reward" value="{if isset($params.date_from_reward) && $params.date_from_reward}{date('Y-m-d', strtotime($params.date_to_reward)) nofilter}{else}{date('Y-m-t') nofilter}{/if}">
                                </div>
                            </div>
                        </div>
                        <div class="eam_action">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}</button>
                                <button type="button" class="btn btn-default js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
