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
<div class="panel ybc-blog-panel">
    <div class="panel-heading">{$title|escape:'html':'UTF-8'}
        {if isset($totalRecords) && $totalRecords}<span class="badge">{$totalRecords|intval}</span>{/if}
        <span class="panel-heading-action">
            {if in_array('add_new',$blog_config.YBC_BLOG_AUTHOR_PRIVILEGES) && $show_add_new}            
                <span class="add_new_post_blog">
                    <a href="{$link_addnew|escape:'html':'UTF-8'}" data-placement="top" data-html="true" data-original-title="{l s='Submit new post' mod='ybc_blog'}" class="label-tooltip" data-toggle="tooltip" title="">
        				<i class="process-icon-new"></i>
                        {l s='Submit new post' mod='ybc_blog'}
                    </a>
                    {if !empty($view_auth_post)}
                        <a href="{$view_auth_post|escape:'html':'UTF-8'}" data-placement="top" data-html="true" data-original-title="{l s='View' mod='ybc_blog'}" class="label-tooltip" data-toggle="tooltip" title="">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512"><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                        </a>
                    {/if}
                </span>            
            {/if}
        </span>
    </div>
    {if $fields_list}
        {if $totalPost}
            <div class="table-responsive clearfix list_post_by_customer">
                <form method="post" action="{$currentIndex|escape:'html':'UTF-8'}">
                    <table class="table configuration">
                        <thead>
                            <tr class="nodrag nodrop">
                                {foreach from=$fields_list item='field' key='index'}
                                    <th class="{$index|escape:'html':'UTF-8'}">
                                        <span class="title_box">
                                            {$field.title|escape:'html':'UTF-8'}
                                            {if isset($field.sort) && $field.sort}
                                                <a href="{$field.sort|escape:'html':'UTF-8'}{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='asc'} class="active"{/if}><i class="ets_svg caret-up">
                                                    <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg>
                                                </i></a>
                                                <a href="{$field.sort_desc|escape:'html':'UTF-8'}{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='desc'} class="active"{/if}>
                                                    <i class="ets_svg caret-down">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
                                                    </i></a>
                                            {/if}
                                        </span>
                                    </th>                            
                                {/foreach}
                                {if $show_action}
                                    <th style="text-align: right;">{l s='Action' mod='ybc_blog'}</th>
                                {/if}
                            </tr>
                            {if $show_toolbar}
                                <tr class="nodrag nodrop filter row_hover">
                                    {foreach from=$fields_list item='field' key='index'}
                                        <th class="{$index|escape:'html':'UTF-8'}">
                                            {if isset($field.filter) && $field.filter}
                                                {if $field.type=='text'}
                                                    <input class="filter" name="{$index|escape:'html':'UTF-8'}" type="text" {if isset($field.width)}style="width: {$field.width|intval}px;"{/if} {if isset($field.active)}value="{$field.active|escape:'html':'UTF-8'}"{/if}/>
                                                {/if}
                                                {if $field.type=='select' || $field.type=='active'}
                                                    <select  {if isset($field.width)}style="width: {$field.width|intval}px;"{/if}  name="{$index|escape:'html':'UTF-8'}">
                                                        <option value=""> -- </option>
                                                        {if isset($field.filter_list.list) && $field.filter_list.list}
                                                            {assign var='id_option' value=$field.filter_list.id_option}
                                                            {assign var='value' value=$field.filter_list.value}
                                                            {foreach from=$field.filter_list.list item='option'}
                                                                <option {if $field.active!=='' && $field.active==$option.$id_option} selected="selected" {/if} value="{$option.$id_option|escape:'html':'UTF-8'}">{$option.$value|escape:'html':'UTF-8'}</option>
                                                            {/foreach}
                                                        {/if}
                                                    </select>                                            
                                                {/if}
                                            {else}
                                               {l s=' -- ' mod='ybc_blog'}
                                            {/if}
                                        </th>
                                    {/foreach}
                                    {if $show_action}
                                        <th class="actions">
                                            <span class="pull-right">
                                                <input type="hidden" name="post_filter" value="yes" />
                                                {if $show_reset}<a  class="btn btn-warning"  href="{$currentIndex|escape:'html':'UTF-8'}"><i class="icon-eraser"></i> {l s='Reset' mod='ybc_blog'}</a>{/if}
                                                <button class="btn btn-default" name="ybc_submit_{$name|escape:'html':'UTF-8'}" id="ybc_submit_{$name|escape:'html':'UTF-8'}" type="submit">
                									<i class="ets_svg">
                                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                                                </i> {l s='Filter' mod='ybc_blog'}
                								</button>
                                            </span>
                                        </th>
                                    {/if}
                                </tr>
                            {/if}
                        </thead>
                        <tbody>
                            {if $field_values}
                                {foreach from=$field_values item='row'}
                                    <tr>
                                        {foreach from=$fields_list item='field' key='key'}
                                            <td class="pointer {$key|escape:'html':'UTF-8'}">
                                                {if isset($field.rating_field) && $field.rating_field}
                                                    {if isset($row.$key) && $row.$key > 0}
                                                        <div class="blog_star_admin" data-rate="{$row.$key|intval}">
                                                            {assign var='everage_rating' value=$row.$key}
                                                            {if $everage_rating == 1}★☆☆☆☆
                                                            {elseif  $everage_rating == 2}★★☆☆☆
                                                            {elseif  $everage_rating == 3}★★★☆☆
                                                            {elseif  $everage_rating == 4}★★★★☆
                                                            {elseif  $everage_rating == 5}★★★★★{/if}
                                                        </div>
                                                    {else}
                                                        {l s=' -- ' mod='ybc_blog'}
                                                    {/if}
                                                {elseif $field.type != 'active'}
                                                    {if isset($row.$key) && !is_array($row.$key)}{if isset($field.strip_tag) && !$field.strip_tag}{$row.$key nofilter}{else}{$row.$key|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}{/if}{/if}
                                                    {if isset($row.$key) && is_array($row.$key) && isset($row.$key.image_field) && $row.$key.image_field}
                                                        <a class="ybc_fancy" href="{$row.$key.img_url|escape:'html':'UTF-8'}"><img style="{if isset($row.$key.height) && $row.$key.height}max-height: {$row.$key.height|intval}px;{/if}{if isset($row.$key.width) && $row.$key.width}max-width: {$row.$key.width|intval}px;{/if}" src="{$row.$key.img_url|escape:'html':'UTF-8'}" /></a>
                                                    {/if}
                                                {else}
                                                    {if isset($row.$key) && $row.$key}
                                                        {if $row.$key==-1}
                                                            <span title="{l s='Pending' mod='ybc_blog'}">
                                                                <i class="ets_svg clock-o">
                                                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 544v448q0 14-9 23t-23 9h-320q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h224v-352q0-14 9-23t23-9h64q14 0 23 9t9 23zm416 352q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                                        </i>
                                                            </span>
                                                        {else}
                                                            {if $row.$key==2 && isset($row.datetime_active)}
                                                                <span title="{l s='Scheduled post time: ' mod='ybc_blog'}{$row.datetime_active|escape:'html':'UTF-8'}">
                                                                    <i class="ets_svg clock-o">
                                                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 544v448q0 14-9 23t-23 9h-320q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h224v-352q0-14 9-23t23-9h64q14 0 23 9t9 23zm416 352q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                                        </i><span style="color:black;">({$row.datetime_active|escape:'html':'UTF-8'})</span></span>
                                                            {else}
                                                                <span title="{l s='Published' mod='ybc_blog'}">
                                                                    <i class="ets_svg approved">
                                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                                                                    </i>
                                                                </span>
                                                            {/if}

                                                        {/if}
                                                    {else}
                                                        <span title="{l s='Unpublished' mod='ybc_blog'}">
                                                            <i class="ets_svg remove">
                                                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>
                                                        </i>
                                                        </span>
                                                    {/if}
                                                {/if}
                                            </td>
                                        {/foreach}
                                        {if $show_action}
                                            <td class="text-right">
                                                    <div class="btn-group-action">
                                                        <div class="btn-group pull-right">
                                                            <a href="{$row.view_url|escape:'html':'UTF-8'}" title="{if isset($row.view_text) && $row.view_text} {$row.view_text|escape:'html':'UTF-8'}{else} {l s='View post' mod='ybc_blog'}{/if}">
                                                                <i class="ets_svg">
                                                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1088 800v64q0 13-9.5 22.5t-22.5 9.5h-224v224q0 13-9.5 22.5t-22.5 9.5h-64q-13 0-22.5-9.5t-9.5-22.5v-224h-224q-13 0-22.5-9.5t-9.5-22.5v-64q0-13 9.5-22.5t22.5-9.5h224v-224q0-13 9.5-22.5t22.5-9.5h64q13 0 22.5 9.5t9.5 22.5v224h224q13 0 22.5 9.5t9.5 22.5zm128 32q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 53-37.5 90.5t-90.5 37.5q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
                                                                </i></a>
                                                            {if (isset($row.edit_url) && $row.edit_url)|| (isset($row.delete_url) && $row.delete_url)}
                                                                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle blog_svg">
                                                                    <i class="ets_svg caret-down">
                                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg>
                                                                    </i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    {if isset($row.edit_url) && $row.edit_url}
                                                                        <li><a class="" href="{$row.edit_url|escape:'html':'UTF-8'}">
                                                                                <i class="ets_svg">
                                                                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
                                                                                </i> {l s='Edit' mod='ybc_blog'}</a></li>
                                                                        <li class="divider"></li>
                                                                    {/if}
                                                                    {if isset($row.delete_url) && $row.delete_url}
                                                                        <li><a onclick="return confirm('{l s='Do you want to delete this item?' mod='ybc_blog'}');" class="" href="{$row.delete_url|escape:'html':'UTF-8'}"><i class="ets_svg"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                                                                </i> {l s='Delete' mod='ybc_blog'}</a></li>
                                                                    {/if}
                                                                </ul>
                                                            {/if}
                                                        </div>
                                                    </div>
                                             </td>
                                        {/if}
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="100%" style="text-align: center">{l s='No post found' mod='ybc_blog'}</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                    {if $paggination}
                        <div class="ybc_paggination" style="margin-top: 10px;">
                            {$paggination nofilter}
                        </div>
                    {/if}
                </form>
            </div>
        {else}
            <div class="alert alert-warning">
                {l s='You have not submitted any posts' mod='ybc_blog'}
            </div>
        {/if}
    {/if}
</div>