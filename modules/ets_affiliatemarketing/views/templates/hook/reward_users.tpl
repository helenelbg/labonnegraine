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

<div class="eam-reward-user">
	<table class="table table-bordered eam-datatables">
		<thead>
			<tr>
				<th>
					#
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="id_customer" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="id_customer" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Customer' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="firstname" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="firstname" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Reward balance' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="reward_balance" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="reward_balance" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Loyalty rewards' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="loy_rewards" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="loy_rewards" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Sponsorship / Referral rewards' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="ref_rewards" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="ref_rewards" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Sponsored friends' mod='ets_affiliatemarketing'}
				</th>
				<th>
					{l s='Sponsored orders' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="ref_orders" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="ref_orders" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Affiliate rewards' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="aff_rewards" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="aff_rewards" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Affiliate orders' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="aff_orders" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="aff_orders" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th style="gfg">
					{l s='Withdrawals' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="total_withdraws" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="total_withdraws" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='Status' mod='ets_affiliatemarketing'}
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="ASC" data-col="user_status" data-sort="asc">{$_svg_asc nofilter}</a>
					<a href="javascript:void(0)" class="js-sort-user-reward" data-toggle="tooltip" data-placement="top" title="DESC" data-col="user_status" data-sort="desc">{$_svg_desc nofilter}</a>
				</th>
				<th>
					{l s='View details' mod='ets_affiliatemarketing'}
				</th>
			</tr>
		</thead>
		<tbody>
			{if $users.results}
				{foreach $users.results as $user}
					<tr>
						<td>
							{$user.id_customer|escape:'html':'UTF-8'}
						</td>
						<td class="text-left">
							<a href="" style="text-transform: capitalize;">
								{$user.username|escape:'html':'UTF-8'}
							</a>
						</td>
						<td class="text-center">{$user.reward_balance|escape:'html':'UTF-8'}</td>
						<td class="text-center">{$user.loy_rewards|escape:'html':'UTF-8'}</td>
						<td class="text-center">{$user.ref_rewards|escape:'html':'UTF-8'}</td>
						<td class="text-center {if isset($user.sponsors) && $user.sponsors}sub-table{/if}">
							{if isset($user.sponsors) && $user.sponsors}
							<table>
							{foreach $user.sponsors as $sponsor}
								<tr>
									<td>{l s='Level' mod='ets_affiliatemarketing'} {$sponsor.level|escape:'html':'UTF-8'}</td>
									<td>{$sponsor.total_sponsor|escape:'html':'UTF-8'}</td>
								</tr>
							{/foreach}
							</table>
							{else}
							-
							{/if}
						</td>
						<td class="text-center">{$user.ref_orders|escape:'html':'UTF-8'}</td>
						<td class="text-center">{$user.aff_rewards|escape:'html':'UTF-8'}</td>
						<td class="text-center">{$user.aff_orders|escape:'html':'UTF-8'}</td>
						<td class="text-center">{$user.total_withdraws|escape:'html':'UTF-8'}</td>
						<td class="text-center">{$user.user_status nofilter}</td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-action">{$_svg_search nofilter}</button>
						</td>
					</tr>
				{/foreach}
			{else}
			<tr>
				<td colspan="100%" class="text-center">{l s='No data found' mod='ets_affiliatemarketing'}</td>
			</tr>
			{/if}
		</tbody>
	</table>
	<div class="row">
		<div class="col-lg-6">
			<label>{l s='Show' mod='ets_affiliatemarketing'}</label>
			<select name="limit" class="field-inline">
				<option value="10">10</option>
				<option value="20">20</option>
				<option value="50">50</option>
			</select>
			<span>{l s='entries' mod='ets_affiliatemarketing'}</span>
		</div>
		<div class="col-lg-6">
			<p class="text-right">{l s='Total:' mod='ets_affiliatemarketing'} <strong>{$users.total_result|escape:'html':'UTF-8'}</strong> {l s='result(s) found' mod='ets_affiliatemarketing'}</p>
		</div>
	</div>
	{if $users.total_page > 1}
    <div class="eam-pagination">
        <ul>
            {for $page=1 to $users.total_page}
            <li>
                <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}" class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
            </li>
            {/for}
        </ul>
    </div>
    {/if}
	<div class="box-filter">
		<form id="eamFormFilterRewardUser" onsubmit="return false;">
			<div class="row">
				<div class="col-lg-4">
					<label>{l s='Search' mod='ets_affiliatemarketing'}</label>
					<input type="text" name="search" value="{if isset($query.search)}{$query.search|escape:'html':'UTF-8'}{/if}" class="field-inline" placeholder="{l s='Search for Customer name' mod='ets_affiliatemarketing'}">
				</div>
				<div class="col-lg-6">
					<div class="clearfix">
						<div class="float-left">
							<label>{l s='Column filter' mod='ets_affiliatemarketing'}</label>
							<select name="column" class="field-inline">
								<option value="">{l s='None' mod='ets_affiliatemarketing'}</option>
								<option value="reward_balance" {if isset($query.column) && $query.column == 'reward_balance'}selected="selected"{/if}>{l s='Reward balance' mod='ets_affiliatemarketing'}</option>
								<option value="loy_rewards" {if isset($query.column) && $query.column == 'loy_rewards'}selected="selected"{/if}>{l s='Loyalty rewards' mod='ets_affiliatemarketing'}</option>
								<option value="ref_rewards" {if isset($query.column) && $query.column == 'ref_rewards'}selected="selected"{/if}>{l s='Sponsorship / Referral rewards' mod='ets_affiliatemarketing'}</option>
								<option value="ref_orders" {if isset($query.column) && $query.column == 'ref_orders'}selected="selected"{/if}>{l s='Sponsored orders' mod='ets_affiliatemarketing'}</option>
								<option value="aff_rewards" {if isset($query.column) && $query.column == 'aff_rewards'}selected="selected"{/if}>{l s='Affiliate rewards' mod='ets_affiliatemarketing'}</option>
								<option value="aff_orders" {if isset($query.column) && $query.column == 'aff_orders'}selected="selected"{/if}>{l s='Affiliate orders' mod='ets_affiliatemarketing'}</option>
								<option value="total_withdraws" {if isset($query.column) && $query.column == 'total_withdraws'}selected="selected"{/if}>{l s='Withdrawals' mod='ets_affiliatemarketing'}</option>
							</select>
						</div>
						<div class="float-left ml-10">
							<label>{l s='From' mod='ets_affiliatemarketing'}</label>
							<input type="number" name="column_number_from" value="{if isset($query.column_number_from)}{$query.column_number_from|escape:'html':'UTF-8'}{/if}" class="form-control field-inline filter-input-number" placeholder="">
						</div>
						<div class="float-left ml-10">
							<label>{l s='To' mod='ets_affiliatemarketing'}</label>
							<input type="number" name="column_number_to" value="{if isset($query.column_number_to)}{$query.column_number_to|escape:'html':'UTF-8'}{/if}" class="form-control field-inline filter-input-number" placeholder="">
						</div>
					</div>
				</div>
				<div class="col-lg-1">
					<button type="submit" class="btn btn-default btn-block">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}</button>
				</div>
				<div class="col-lg-1">
					<button type="reset" class="btn btn-default btn-block">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}</button>
				</div>
			</div>
		</form>
		
	</div>
</div>