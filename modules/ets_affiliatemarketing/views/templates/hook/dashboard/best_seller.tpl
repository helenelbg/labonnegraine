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
{assign var='_svg_search' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg></i>'}

{assign var='_svg_question_circle_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
<table class="table table-bordered">
	<thead>
		<tr>
			<th class="text-left">{l s='Product image' mod='ets_affiliatemarketing'}</th>
			<th class="text-left">{l s='Product name' mod='ets_affiliatemarketing'}</th>
			<th class="text-center">{l s='Sales' mod='ets_affiliatemarketing'}
				<span data-toggle="tooltip" data-placement="top" title="{l s='The number of sold products' mod='ets_affiliatemarketing'}">
					{$_svg_question_circle_icon nofilter}
				</span>
			</th>
			<th class="text-center">{l s='Total cost' mod='ets_affiliatemarketing'}
				<span data-toggle="tooltip" data-placement="top" title="{l s='The total earning from a product' mod='ets_affiliatemarketing'}">
					{$_svg_question_circle_icon nofilter}
				</span>
			</th>
			<th class="text-center">{l s='Action' mod='ets_affiliatemarketing'}</th>
		</tr>
	</thead>
	<tbody>
		{if $data && $data.results}
		{foreach $data.results as $ord}
			<tr>
				<td class="text-left">
                    <a href="{$ord.link_product nofilter}" title="{l s='View product' mod='ets_affiliatemarketing'}" target="_blank" >
					   <img src="{$ord.product_image|escape:'html':'UTF-8'}" alt="{$ord.product_name|escape:'html':'UTF-8'}" style="height: 50px;" />
                    </a>
				</td>
				<td lass="text-left">
					<a href="{$ord.link_product nofilter}" title="{l s='View product' mod='ets_affiliatemarketing'}" target="_blank" >
						{$ord.product_name|escape:'html':'UTF-8'}
					</a>
				</td>
				<td class="text-center">
					{$ord.total_sold|escape:'html':'UTF-8'}
				</td>
				<td class="text-center">
					{$ord.sales|escape:'html':'UTF-8'}
				</td>
				<td class="text-right">
					<a target="_blank" href="{$ord.link_product|escape:'html':'UTF-8'}"  data-toggle="tooltip" data-placement="top" title="{l s='View product' mod='ets_affiliatemarketing'}" class="btn btn-default">
						{$_svg_search nofilter}</a></td>
			</tr>
		{/foreach}
		{else}
			<tr>
				<td colspan="100%" class="text-center">{l s='No data found' mod='ets_affiliatemarketing'}</td>
			</tr>
		{/if}
	</tbody>
</table>
