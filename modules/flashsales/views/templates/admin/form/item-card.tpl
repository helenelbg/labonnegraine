{*
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*}

{if isset($items) && $items}
{foreach from=$items item=item}
    {if $key == 'product'}
        {$item.products}{* HTML, cannot escape *}
    {else}
    <div class="productCard col-lg-4 col-md-6 item_{$key|escape:'html':'UTF-8'}_{$item.id_item|intval} {if $selected}selected-product{/if}">
        <div class="panel" data-panel="{$key|escape:'quotes':'UTF-8'}">
            <div class="panel-heading">
                {$item.name|truncate:40:'...'|escape:'html':'UTF-8'}
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <img src="{$item.image_link|escape:'html':'UTF-8'}" title="{$item.name|escape:'html':'UTF-8'}" class="img-responsive" />
                </div>
                <div class="col-xs-8">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4>{l s='Information' mod='flashsales'}</h4>
                            <div class="formatted_information card-panel">
                                <div>
                                    <span class="strong">{l s='Products' mod='flashsales'}:</span>
                                    <span class="nb_products">{$item.nb_products|intval}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-reduction="" data-reduction-type="" data-from="" data-to="" class="row custom_price_content {if $selected}show{else}hide{/if}">
                        <div class="col-xs-12">
                            <h4>{l s='Reduction' mod='flashsales'}</h4>
                            <div class="formatted_reduction">
                                <div class="card-panel">
                                    <div>
                                        <span class="strong">{l s='Impact' mod='flashsales'}:</span>
                                        <span class="formatted_impact"></span>
                                    </div>
                                    <div>
                                        <span class="strong">{l s='Period' mod='flashsales'}:</span>
                                        {l s='from' mod='flashsales'} <span class="formatted_period_from"></span> {l s='to' mod='flashsales'} <span class="formatted_period_to"></span>
                                    </div>
                                </div>
                                
                                <button type="button" class="add-custom-price btn btn-default chip small primary mt-10">
                                    {l s='Customize' mod='flashsales'}
                                </button>

                                <button type="button" class="delete-custom-price btn btn-default chip small secondary mt-10">
                                    {l s='delete customization' mod='flashsales'}
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div id="{$key|strval}_products_{$item.id_item|intval}" class="list_products" style="display:none;">
                {$item.products}{* HTML, cannot escape *}
            </div>
            <div class="panel-footer" data-product="{$item.id_item|intval}">
                <input type="hidden" name="id_{$key|strval}" value="{$item.id_item|intval}" />
                <button type="button" class="expand-action btn btn-default pull-left">
                    <div class="btn-expand"><i class="icon-caret-down"></i> {l s='Expand' mod='flashsales'}</div>
                    <div class="btn-collapse"><i class="icon-caret-up"></i> {l s='Collapse' mod='flashsales'}</div>
                </button>
                <button type="button" class="card-action btn btn-default pull-right">
                    <div class="btn-remove"><i class="icon-refresh"></i> {l s='Remove' mod='flashsales'}</div>
                    <div class="btn-choose"><i class="icon-arrow-right"></i> {l s='Choose' mod='flashsales'}</div>
                </button>
            </div>
        </div>
    </div>
    {/if}
{/foreach}
{/if}
