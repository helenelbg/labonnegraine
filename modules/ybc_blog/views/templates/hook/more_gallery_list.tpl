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
{if isset($blog_galleries)}
    {foreach from=$blog_galleries item='gallery'}
        <li class="col-xs-12 col-sm-4 col-lg-{12/$per_row|intval}">
            <a class="gallery_item"  {if $gallery.description} title="{strip_tags($gallery.description)|escape:'html':'UTF-8'}"{/if} rel="prettyPhotoGalleryPage[gallery]" href="{$gallery.image|escape:'html':'UTF-8'}">
                <img width="{Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_GALLERY_THUHMB_HEIGHT'|escape:'html':'UTF-8')}" src="{$gallery.thumb|escape:'html':'UTF-8'}" title="{$gallery.title|escape:'html':'UTF-8'}" alt="{$gallery.title|escape:'html':'UTF-8'}" />
            </a>
        </li>
    {/foreach}
{/if}