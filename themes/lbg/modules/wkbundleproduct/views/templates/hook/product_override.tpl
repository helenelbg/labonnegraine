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

{extends file=$extendFilePath}

{block name='page_content_container'}
  <section class="page-content" id="content">
    {block name='page_content'}
      {block name='product_flags'}
        <ul class="product-flags">
          {foreach from=$product.flags item=flag}
            <li class="product-flag {$flag.type}">{$flag.label}</li>
          {/foreach}
        </ul>
      {/block}

      {block name='product_cover_thumbnails'}
        {include file='catalog/_partials/product-cover-thumbnails.tpl'}
      {/block}
      <div class="scroll-box-arrows">
        <i class="material-icons left">&#xE314;</i>
        <i class="material-icons right">&#xE315;</i>
      </div>
    {/block}
  </section>
{/block}
{block name='product_customization'}
  {* {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations} *}
{/block}
