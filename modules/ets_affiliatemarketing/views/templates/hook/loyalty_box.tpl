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
		{assign var='_svg_heart' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 1664q-26 0-44-18l-624-602q-10-8-27.5-26t-55.5-65.5-68-97.5-53.5-121-23.5-138q0-220 127-344t351-124q62 0 126.5 21.5t120 58 95.5 68.5 76 68q36-36 76-68t95.5-68.5 120-58 126.5-21.5q224 0 351 124t127 344q0 221-229 450l-623 600q-18 18-44 18z"/></svg></i>'}

		<a id="aem-loyalty-link" href="{$refUrl|escape:'html':'UTF-8'}" class="{if isset($is17) && $is17}col-lg-4 col-md-6 col-sm-6 col-xs-12 eam-box-featured{/if}">
    	  <span class="link-item">
    	    {$_svg_heart nofilter}
    	      {l s='Loyalty program' mod='ets_affiliatemarketing'}
              <p class="desc">{l s='Buy to get rewards' mod='ets_affiliatemarketing'}</p>
    	  </span>
    	</a>
{if !$is17}
    </li>
{/if}