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

{if isset($data) && $data}
    <div class="row wk-bp-custom-padding-t">
        <div class="col-md-11">
            <strong>{if isset($section_name)}{$section_name}{/if}</strong>
        </div>
        <div class="col-md-1">
            {if !Configuration::get('WK_BUNDLE_PRODUCT_HIDE_PRODUCT_SELECTION')}
                <div class="wk_bundle_section_redirect" title="{l s='Go to this section' mod='wkbundleproduct'}" data-id="{$id_section}">
                        <a href="#{$id_section}">
                            <i class="material-icons wk_add_circle">
                                arrow_downward
                            </i>
                        </a>
                </div>
            {/if}
        </div>
    </div>
    <hr>
    {if $wk_multi_selection == 1}
        {foreach $data as $info}
            <div class="row">
                    <div class="col-md-2 wk_section_product_{$id_section}_{$info.id_product}">
                        <img src="{$info.image_link}" class="img-responsive img-thumbnail" alt ="{$info.product_name}">
                    </div>
                    <div class="col-md-9">
                        {$info.product_name}<br>
                        {if $info.attr_name}
                            {foreach $info.attr_name as $combName}
                                <strong>{$combName.group_name}</strong> : {$combName.attribute_name}<br>
                            {/foreach}
                        {/if}
                        <strong>{l s='Quantity' mod='wkbundleproduct'}</strong> : {$info.product_qty}
                    </div>
            </div>
            <hr>
        {/foreach}
    {else}
        <div class="row">
            <div class="col-md-2 wk_section_product_{$id_section}_{$data.id_product}">
                <img src="{$data.image_link}" class="img-responsive img-thumbnail" alt ="{$data.product_name}">
            </div>
            <div class="col-md-9">
                {$data.product_name}<br>
                {if $data.attr_name}
                    {foreach $data.attr_name as $combName}
                        <strong>{$combName.group_name}</strong> : {$combName.attribute_name}<br>
                    {/foreach}
                {/if}
                <strong>{l s='Quantity' mod='wkbundleproduct'}</strong> : {$data.product_qty}
            </div>
        </div>
    {/if}

    <div class="row">
        <div class="col-md-12 wk_section_price_info wk-bp-custom-margin-t">
            <strong>{l s='Section price : ' mod='wkbundleproduct'}</strong> {$section_price} <a href="javascript:void(0);" data-toggle="tooltip" title="{l s='This is the price of the section considered in the bundle price calculation.' mod='wkbundleproduct'}"><i class="material-icons" style="font-size:15px;vertical-align: initial;">info</i></a>
        </div>
    </div>
{else}
    <div class="row wk-bp-custom-padding-t">
        <div class="col-md-11">
            <strong>{if isset($section_name)}{$section_name}{/if}</strong>
            <hr>
            <p class="wk-bp-custom-margin-t">{l s='No products are selected from this section.' mod='wkbundleproduct'}</p>
            <hr>
        </div>
        <div class="col-md-1">
            {if !Configuration::get('WK_BUNDLE_PRODUCT_HIDE_PRODUCT_SELECTION')}
                <div class="wk_bundle_section_redirect" title="{l s='Go to this section' mod='wkbundleproduct'}" data-id= {$id_section}>
                    <a href="#{$id_section}">
                        <i class="material-icons wk_add_circle">
                            arrow_downward
                        </i>
                    </a>
                </div>
            {/if}
        </div>
    </div>
{/if}
