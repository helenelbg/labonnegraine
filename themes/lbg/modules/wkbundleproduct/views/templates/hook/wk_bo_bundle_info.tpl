{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class="wk_list_bundle">
  <h2>
    {l s='List of bundle(s)' mod='wkbundleproduct'}
    <span class="help-box" data-toggle="popover" data-content="{l s='This product exists in following bundle(s)' mod='wkbundleproduct'}" data-original-title="" title=""></span>
  </h2>
</div>
{if $product_info}
    <ol type="1">
        {foreach $product_info as $info}
            <li> <a href='{$info.link|escape:'html':'UTF-8'}' target='blank'>{$info.name|escape:'html':'UTF-8'}</a></li>
        {/foreach}
    </ol>
{/if}
