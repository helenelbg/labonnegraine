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

{if !$is17}
    <li class=" eam-box-featured">
{/if}
	<a href="{$refUrl|escape:'html':'UTF-8'}" class="{if isset($is17) && $is17}col-lg-4 col-md-6 col-sm-6 col-xs-12 eam-box-featured{/if}">
	  <span class="link-item">
	    <i class="ets_svg">
			<svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1024q133 0 226.5 93.5t93.5 226.5-93.5 226.5-226.5 93.5-226.5-93.5-93.5-226.5q0-12 2-34l-360-180q-92 86-218 86-133 0-226.5-93.5t-93.5-226.5 93.5-226.5 226.5-93.5q126 0 218 86l360-180q-2-22-2-34 0-133 93.5-226.5t226.5-93.5 226.5 93.5 93.5 226.5-93.5 226.5-226.5 93.5q-126 0-218-86l-360 180q2 22 2 34t-2 34l360 180q92-86 218-86z"/></svg>
		</i>
	      {l s='Affiliate program' mod='ets_affiliatemarketing'}
          <p class="desc">{l s='Sell to get rewards' mod='ets_affiliatemarketing'}</p>
	  </span>
	</a>
{if !$is17}
    </li>
{/if}