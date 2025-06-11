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
{if $previewFileName && $layoutName}
  <div class="etscs-preview-image-container">
    <img alt="{$layoutName|escape:'html':'UTF-8'} preview" class="img-preview-layout {$layoutName|escape:'html':'UTF-8'}" src="{$link->getMedialink("`$smarty.const._MODULE_DIR_`ets_crosssell/views/img/{$previewFileName}.png")|escape:'html':'UTF-8'}" />
  </div>
  <div class="etscs-preview-layout-img-zoom-container {$layoutName|escape:'html':'UTF-8'}">
    <img class="img-preview-layout-zoom" src="{$link->getMedialink("`$smarty.const._MODULE_DIR_`ets_crosssell/views/img/{$previewFileName}.png")|escape:'html':'UTF-8'}" alt="" style="">
    <div class="label-div">
      <span>
        {if (isset($label) && $label)}
          {$label|strip_tags|escape:'html':'UTF-8'}
        {else}
          {$layoutName|escape:'html':'UTF-8'} preview
        {/if}
      </span>
    </div>
  </div>
{/if}