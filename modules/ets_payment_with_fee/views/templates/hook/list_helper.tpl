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
<div class="panel ets-custom-payment-panel">
    <div class="panel-heading">{$title|escape:'html':'UTF-8'}
        <span class="panel-heading-action">
            {if !isset($show_add_new) || isset($show_add_new) && $show_add_new}
                <a class="list-toolbar-btn" href="{$link->getAdminLink('AdminPaymentFee',true)|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Set fee for other payment methods' mod='ets_payment_with_fee'}" class="label-tooltip" data-toggle="tooltip" title="{l s='Set fee for other payment methods' mod='ets_payment_with_fee'}">
                        <i class="icon icon-cogs"></i>
                    </span>
                </a>             
                <a class="list-toolbar-btn" href="{$currentIndex|escape:'html':'UTF-8'}&addnewPayment">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Add new' mod='ets_payment_with_fee'}" class="label-tooltip" data-toggle="tooltip" title="{l s='Add new' mod='ets_payment_with_fee'}">
                        <i class="process-icon-new"></i>
                    </span>
                </a>       
            {else}
                <a class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules', true)|escape:'html':'UTF-8'}&configure=ets_payment_with_fee&tab_module=payments_gateways&module_name=ets_payment_with_fee">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Add custom payment method with fee' mod='ets_payment_with_fee'}" class="label-tooltip" data-toggle="tooltip" title="{l s='Add custom payment method with fee' mod='ets_payment_with_fee'}">
                        <i class="process-icon-new"></i>
                    </span>
                </a>     
            {/if}
            {if isset($preview_link) && $preview_link}            
                <a target="_blank" class="list-toolbar-btn" href="{$preview_link|escape:'html':'UTF-8'}">
                    <span data-placement="top" data-html="true" data-original-title="{l s='Preview ' mod='ets_payment_with_fee'} ({$title|escape:'html':'UTF-8'})" class="label-tooltip" data-toggle="tooltip" title="">
                        <i style="margin-left: 5px;" class="icon-search"></i>
                    </span>
                </a>            
            {/if}
        </span>
    </div>
    {if $fields_list}
        <div class="table-responsive clearfix">
            <form method="post" action="{$currentIndex|escape:'html':'UTF-8'}&amp;list=true">
                <div class="table configuration">
                    <div class="table-row nodrag nodrop header_table">
                        {foreach from=$fields_list item='field' key='index'}
                            <div class="table-cell {$index|escape:'html':'UTF-8'}">
                                <span class="title_box">
                                    {$field.title|escape:'html':'UTF-8'}
                                    {if isset($field.sort) && $field.sort}
                                        <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;sort={$index|escape:'html':'UTF-8'}&amp;sort_type=desc&amp;list=true{$filter_params nofilter}" class="{if $sort==$index && $sort_type=='desc'}active{/if}"><i class="icon-caret-down"></i></a>
                                        <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;sort={$index|escape:'html':'UTF-8'}&amp;sort_type=asc&amp;list=true{$filter_params nofilter}" class="{if $sort==$index && $sort_type=='asc'}active{/if}"><i class="icon-caret-up"></i></a>
                                    {/if}
                                </span>
                            </div>                            
                        {/foreach}
                        {if $show_action}
                            <div class="table-cell" style="text-align: right;">{l s='Action' mod='ets_payment_with_fee'}</div>
                        {/if}
                    </div>
                    {if $show_toolbar}
                        <div class="table-row nodrag nodrop toolbar_table">
                            {foreach from=$fields_list item='field' key='index'}
                                <div class="table-cell {$index|escape:'html':'UTF-8'}">
                                    {if isset($field.filter) && $field.filter}
                                        {if $field.type=='text'}
                                            <input class="filter" name="{$index|escape:'html':'UTF-8'}" type="text" {if isset($field.width)}style="width: {$field.width|intval}px;"{/if} {if isset($field.active)}value="{$field.active|escape:'html':'UTF-8'}"{/if}/>
                                        {/if}
                                        {if $field.type=='select' || $field.type=='active'}
                                            <select  {if isset($field.width)}style="width: {$field.width|intval}px;"{/if}  name="{$index|escape:'html':'UTF-8'}">
                                                {if $index!='has_post'}
                                                    <option value=""> -- </option>
                                                {/if}
                                                {if isset($field.filter_list.list) && $field.filter_list.list}
                                                    {assign var='id_option' value=$field.filter_list.id_option}
                                                    {assign var='value' value=$field.filter_list.value}
                                                    {foreach from=$field.filter_list.list item='option'}
                                                        <option {if isset($field.active) && (($field.active!=='' && $field.active==$option.$id_option) || ($field.active=='' && $index=='has_post' && $option.$id_option==1 ))} selected="selected"{/if} value="{$option.$id_option|escape:'html':'UTF-8'}">{$option.$value|escape:'html':'UTF-8'}</option>
                                                    {/foreach}
                                                {/if}
                                            </select>                                            
                                        {/if}
                                        {if $field.type=='int'}
                                            <label for="{$index|escape:'html':'UTF-8'}_min"><input type="text" placeholder="{l s='Min' mod='ets_payment_with_fee'}" name="{$index|escape:'html':'UTF-8'}_min" value="{$field.active.min|escape:'html':'UTF-8'}" /></label>
                                            <label for="{$index|escape:'html':'UTF-8'}_max"><input type="text" placeholder="{l s='Max' mod='ets_payment_with_fee'}" name="{$index|escape:'html':'UTF-8'}_max" value="{$field.active.max|escape:'html':'UTF-8'}" /></label>
                                        {/if}
                                    {else}
                                       {l s=' -- ' mod='ets_payment_with_fee'}
                                    {/if}
                                </div>
                            {/foreach}
                            {if $show_action}
                                <div class="table-cell pm_action">
                                    <span class="pull-right">
                                        <input type="hidden" name="post_filter" value="yes" />
                                        {if $show_reset}<a  class="btn btn-warning"  href="{$currentIndex|escape:'html':'UTF-8'}&list=true"><i class="icon-eraser"></i> {l s='Reset' mod='ets_payment_with_fee'}</a>{/if}
                                        <button class="btn btn-default" name="ets_payment_submit_{$name|escape:'html':'UTF-8'}" id="ets_payment_submit_{$name|escape:'html':'UTF-8'}" type="submit">
                                            <i class="icon-search"></i> {l s='Filter' mod='ets_payment_with_fee'}
                                        </button>
                                    </span>
                                </div>
                            {/if}
                        </div>
                    {/if}
                    <div id="{$name|escape:'html':'UTF-8'}-list" class="ui-sortable ets_table">
                        {if $field_values}
                            {foreach from=$field_values item='row'}
                                <div id="{$name|escape:'html':'UTF-8'}-{$row[$identifier]|intval}" class="table-row">
                                    {foreach from=$fields_list item='field' key='key'}                                
                                        <div class="table-cell {$key|escape:'html':'UTF-8'} {if isset($sort)&& $sort==$key && isset($sort_type) && $sort_type=='asc' && isset($field.update_position) && $field.update_position}pointer dragHandle center{/if}">
                                            {if isset($field.rating_field) && $field.rating_field}
                                                {if isset($row.$key) && $row.$key > 0}
                                                    {for $i=1 to (int)$row.$key}
                                                        <div class="star star_on"></div>
                                                    {/for}
                                                    {if (int)$row.$key < 5}
                                                        {for $i=(int)$row.$key+1 to 5}
                                                            <div class="star"></div>
                                                        {/for}
                                                    {/if}
                                                {else}
                                                    {l s=' -- ' mod='ets_payment_with_fee'}
                                                {/if}
                                            {elseif $field.type != 'active'}
                                                {if isset($field.update_position) && $field.update_position}
                                                    <div class="dragGroup">
                                                    <span class="positions">
                                                {/if}
                                                    {if isset($row.$key) && !is_array($row.$key)}{if isset($field.strip_tag) && !$field.strip_tag}{$row.$key nofilter}{else}{$row.$key|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}{/if}{/if}
                                                    {if isset($row.$key) && is_array($row.$key) && isset($row.$key.image_field) && $row.$key.image_field}
                                                        <img style="{if isset($row.$key.height) && $row.$key.height}max-height: {$row.$key.height|intval}px;{/if}{if isset($row.$key.width) && $row.$key.width}max-width: {$row.$key.width|intval}px;{/if}" src="{$row.$key.img_url|escape:'html':'UTF-8'}" />
                                                    {/if}  
                                                {if isset($field.update_position) && $field.update_position}
                                                    </div>
                                                    </span>
                                                {/if}                                      
                                            {else}                                            
                                                {if isset($row.$key) && $row.$key}
                                                    <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&amp;change_enabled=0&amp;field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-enabled list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-check"></i></a>
                                                {else}
                                                    <a href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&amp;change_enabled=1&amp;field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-disabled  list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-remove"></i></a>
                                                {/if}
                                            {/if}
                                        </div>
                                    {/foreach}
                                    {if $show_action}
                                        <div class=" table-cell text-right pm_action">
                                            <div class="btn-group-action">
                                                <div class="btn-group pull-right">
                                                    {if in_array('edit',$actions)}
                                                        <a class="edit btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&edit{$name|escape:'html':'UTF-8'}=1&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="icon-pencil"></i> {if $name=='module'}{l s='Edit fee' mod='ets_payment_with_fee'}{else}{l s='Edit' mod='ets_payment_with_fee'}{/if}</a>
                                                    {/if}
                                                    {if in_array('view',$actions)}
                                                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                                            <i class="icon-caret-down"></i>&nbsp;
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            {if isset($row.view_url) && $row.view_url}
                                                                <li><a target="_blank" href="{$row.view_url|escape:'html':'UTF-8'}"><i class="icon-search-plus"></i> {if isset($row.view_text) && $row.view_text} {$row.view_text|escape:'html':'UTF-8'}{else} {l s='Preview' mod='ets_payment_with_fee'}{/if}</a></li>
                                                                <li class="divider"></li>
                                                            {/if}
                                                            <li><a onclick="return confirm('{l s='Do you want to delete this item?' mod='ets_payment_with_fee'}');" href="{$currentIndex|escape:'html':'UTF-8'}&amp;{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&del=yes"><i class="icon-trash"></i> {l s='Delete' mod='ets_payment_with_fee'}</a></li>
                                                        </ul>
                                                    {/if}
                                                </div>
                                            </div>
                                         </div>
                                    {/if}
                                </div>
                            {/foreach}
                        {else}
                            <div class="no_payment_method table-row">
                                <p>
                                    {if $show_reset}
                                        {l s='No data found' mod='ets_payment_with_fee'}
                                    {else}
                                        {l s='No custom payment methods available. Click on "Add new" button to add new custom payment methods.' mod='ets_payment_with_fee'}
                                    {/if}
                                </p>
                            </div>
                        {/if}
                    </div>
                </div>
            </form>
        </div>
    {/if}
</div>