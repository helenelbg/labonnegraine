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
<div class="block ybc_block_search {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'}">
    <h4 class="title_blog title_block">{l s='Search in blog' mod='ybc_blog'}</h4>
    <div class="content_block block_content">
        <form action="{$action|escape:'html':'UTF-8'}" method="post">
            <input class="form-control" type="text" name="blog_search" placeholder="{l s='Type in key words...' mod='ybc_blog'}" value="{$search|escape:'html':'UTF-8'}" />
            <input class="button" type="submit" value="{l s='Search' mod='ybc_blog'}" />
            <span class="icon_search">
                <i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg>
</i>
            </span>
        </form>
    </div>
</div>
