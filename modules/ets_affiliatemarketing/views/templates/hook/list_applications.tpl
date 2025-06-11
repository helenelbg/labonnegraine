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
                        <th>{l s='ID' mod='ets_affiliatemarketing'}</th>
                        <th>{l s='Customer' mod='ets_affiliatemarketing'}</th>
                        <th>{l s='Program' mod='ets_affiliatemarketing'}</th>
                        <th class="text-center">{l s='Status' mod='ets_affiliatemarketing'}</th>
                        <th class="text-center">{l s='Date of request' mod='ets_affiliatemarketing'}</th>
                        <th class="text-center">{l s='Action' mod='ets_affiliatemarketing'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $results}
                        {foreach $results as $result}
                        <tr>
                            <td>{$result.id|escape:'html':'UTF-8'}</td>
                            <td>  
                                <a href="{$result.link|escape:'html':'UTF-8'}" title="{l s='View customer' mod='ets_affiliatemarketing'}">
                                    {if $result.firstname}
                                        {$result.firstname|escape:'html':'UTF-8'} {$result.lastname|escape:'html':'UTF-8'}
                                    {else}
                                        <span class="label warning-deleted">{l s='User deleted' mod='ets_affiliatemarketing'}</span>
                                    {/if}
                                </a>   
                            </td>
                            <td><span class="eam-text">{$result.program|escape:'html':'UTF-8'}</span></td>
                            <td class="text-center">
                                {if $result.status == -2}
                                    <label class="label label-danger">{l s='Stopped' mod='ets_affiliatemarketing'}</label>
                                {elseif $result.status == -1}
                                    <label class="label label-default">{l s='Declined' mod='ets_affiliatemarketing'}</label>
                                {elseif $result.status == 0}
                                    <label class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</label>
                                {else}
                                    <label class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</label>
                                {/if}
                            </td>
                            <td class="text-center">{dateFormat date=$result.date_added full=1}</td>
                            <td class="text-center">{if count($result.actions) > 1}
                                <div class="btn-group">
                                  <a href="{if isset($result.actions[0].href) && $result.actions[0].href }{$result.actions[0].href|escape:'html':'UTF-8'}{else}javascript:void(0){/if}" class="btn btn-default {$result.actions[0].class|escape:'html':'UTF-8'}" data-id="{$result.actions[0].id|escape:'html':'UTF-8'}" data-action="{$result.actions[0].action|escape:'html':'UTF-8'}"><i class="fa fa-{$result.actions[0].icon|escape:'html':'UTF-8'}"></i> {$result.actions[0].label|escape:'html':'UTF-8'}</a>
                                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    {foreach $result.actions as $key=>$action}
                                        {if $key > 0}
                                        <li><a href="javascript:void(0)" class="{$action.class|escape:'html':'UTF-8'}" data-id="{$action.id|escape:'html':'UTF-8'}" data-action="{$action.action|escape:'html':'UTF-8'}"><i class="fa fa-{$action.icon|escape:'html':'UTF-8'}" ></i> {$action.label|escape:'html':'UTF-8'}</a></li>
                                        {/if}
                                    {/foreach}
                                  </ul>
                                </div>
                                {else}
                                    <a href="javascript:void(0)" class="btn btn-default {$result.actions[0].class|escape:'html':'UTF-8'}" data-id="{$result.actions[0].id|escape:'html':'UTF-8'}" data-action="{$result.actions[0].action|escape:'html':'UTF-8'}"><i class="fa fa-{$result.actions[0].icon|escape:'html':'UTF-8'}"></i> {$result.actions[0].label|escape:'html':'UTF-8'}</a>
                                {/if}
                            </td>
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
                                <option value="10" {if $limit == 10} selected {/if}>10</option>
                                <option value="25" {if $limit == 25} selected {/if}>25</option>
                                <option value="50" {if $limit == 50} selected {/if}>50</option>
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
               
                <div class="stat-filter eam-box-filter">
                    <div class="row" style="margin-top: 15px;">
                        <div class="eam_select_filter">
                            <label>{l s='Status' mod='ets_affiliatemarketing'}</label>
                            <select name="status" class="field-inline">
                                <option value="all"  {if isset($params.status) && $params.status == 'all'} selected="selected" {/if}>{l s='All' mod='ets_affiliatemarketing'}</option>
                                <option value="1"  {if isset($params.status) && $params.status == '1'} selected="selected" {/if}>{l s='Approved' mod='ets_affiliatemarketing'}</option>
                                <option value="0"  {if isset($params.status) && $params.status == '0'} selected="selected" {/if}>{l s='Pending' mod='ets_affiliatemarketing'}</option>
                                <option value="-1"  {if isset($params.status) && $params.status == '-1'} selected="selected" {/if}>{l s='Declined' mod='ets_affiliatemarketing'}</option>
                            </select>
                        </div>
                        <div class="col-xs-6 col-md-4 col-lg-6 eam_time_range">
                            <div>
                                <label>{l s='Time range' mod='ets_affiliatemarketing'}</label>
                                <select name="type_date_filter" class="form-control field-inline">
                                    <option value="all_times" {if isset($params.type_date_filter) && $params.type_date_filter == 'all_times'}selected="selected"{/if}>{l s='All the time' mod='ets_affiliatemarketing'}</option>
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
                            <div class="form-group col-lg-6">
                                <button type="submit" class="btn btn-default btn-block">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}</button>
                            </div>
                            <div class="form-group col-lg-6">
                                <button type="button" class="btn btn-default btn-block js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{if $enable_email_approve_app == 1}
<div class="modal fade" id="modalReasonDeclineApp" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{l s='Send an message to customer via email?' mod='ets_affiliatemarketing'}</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <textarea name="reason" class="form-control" rows="5" placeholder="{l s='Messages' mod='ets_affiliatemarketing'}"></textarea>
        </div>
        <p><em>{l s='Give customer a reason why their application is declined. Leave this blank if you just want to decline the application without giving any reason' mod='ets_affiliatemarketing'}</em></p>
        <div class="form-group">
            <button type="button" id="submit_reason_decline" class="btn btn-default">{l s='Decline' mod='ets_affiliatemarketing'}</button>
        </div>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{/if}
{if $enable_email_decline_app == 1}
<div class="modal fade" id="modalReasonAproveApp" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{l s='Send an message to customer via email?' mod='ets_affiliatemarketing'}</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <textarea name="reason" class="form-control" rows="5" placeholder="{l s='Messages' mod='ets_affiliatemarketing'}"></textarea>
        </div>
            <p><em>{l s='Give customer a reason why their application is approved. Leave this blank if you just want to approve the application without giving any reason' mod='ets_affiliatemarketing'}</em></p>
            <div class="form-group">
                <button type="button" id="submit_reason_approve" class="btn btn-default">{l s='Approve' mod='ets_affiliatemarketing'}</button>
            </div>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
{/if}