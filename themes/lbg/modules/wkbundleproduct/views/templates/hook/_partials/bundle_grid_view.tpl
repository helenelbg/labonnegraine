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

{if isset($sections.product_detail)}
    <div class="item">
        <div class="pad15">
            {block name='bundle-sub-products'}
                {include file='module:wkbundleproduct/views/templates/hook/_partials/bundle-sub-product-grid.tpl' product=$sections.product_detail[0] sections=$sections idpsproduct=$idpsproduct themeType=$themeType}
            {/block}
        </div>
    </div>
{/if}

