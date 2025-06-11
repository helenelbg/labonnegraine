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

<div class="wk-sub-product-variants">
  {foreach from=$groups key=id_attribute_group item=group}
    {if !empty($group.attributes)}
    <div class="clearfix wk-product-variants-item wk-bp-custom-margin-t{if $group.name == "Production"} hidden{/if}">
        <select
          class="form-control form-control-select wk_attr_onchange wk_attr_onchange_{$idproduct}_{$idsection} wk-bp-custom-margin-b"
          id="group_{$id_attribute_group}_{$idsection}_{$idproduct}"
          data-product-attribute="{$id_attribute_group}"
          data-id_section='{$idsection}' name="group[{$id_attribute_group}]_{$idsection}_{$idproduct}"  data-id_product = '{$idproduct}'>
          {foreach from=$group.attributes key=id_attribute item=group_attribute}
            <option value="{$id_attribute}" title="{$group_attribute.name}"{if $group_attribute.selected} selected="selected"{/if}>{$group_attribute.name}</option>
          {/foreach}
        </select>
    </div>
    {/if}
  {/foreach}
</div>