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

{if isset($products) && $products}
{foreach from=$products item=prod}
    <script type="text/javascript">
    combs[{$prod.id_product|intval}] = new Object();
    {foreach from=$prod.combinations item=combination}
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}] = new Array();
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['id_product'] = {$prod.id_product|intval};
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['id_product_attribute'] = {$combination.id_product_attribute|intval};
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['attributes'] = "{$combination.attributes|escape:'html':'UTF-8'}";
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['price_tax_incl'] = {$combination.price_tax_incl|floatval};
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['formatted_price'] = "{$combination.formatted_price|escape:'html':'UTF-8'}";
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['stock'] = {$combination.stock|intval};
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['selected'] = {if $combination.id_product_attribute == 0}true{else}false{/if};
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['custom_reduction'] = false;
        combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['has_reduction'] = {if (isset($combination.reduction) && $combination.reduction > 0) || (isset($prod.combinations[0].reduction) && $prod.combinations[0].reduction > 0)}true{else}false{/if};
        {if isset($combination.custom_reduction) && $combination.custom_reduction}
            combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['reduction'] = "{$combination.reduction|floatval}";
            combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['reduction_type'] = "{$combination.reduction_type|escape:'html':'UTF-8'}";
            combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['from'] = "{$combination.from|escape:'html':'UTF-8'}";
            combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['to'] = "{$combination.to|escape:'html':'UTF-8'}";
            combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['custom_reduction'] = {if $combination.custom_reduction}true{else}false{/if};
            combs[{$prod.id_product|intval}][{$combination.id_product_attribute|intval}]['has_reduction'] = true;
        {/if}
    {/foreach}
    </script>

    <div class="productCard col-lg-4 col-md-6 item_product_{$prod.id_product|intval} {if $selected}selected-product{/if}">
        <div class="panel" data-panel="product">
            <div class="panel-heading">
                {$prod.name|truncate:40:'...'|escape:'html':'UTF-8'}
            </div>
            <div class="row panel-body">
                <div class="col-xs-4">
                    <img src="{$prod.image_link|escape:'html':'UTF-8'}" title="{$prod.name|escape:'html':'UTF-8'}" class="img-responsive" />
                </div>
                <div class="col-xs-8">
                    <div class="row">
                        <div class="col-xs-12">
                            <select name="id_combination">
                            {foreach from=$prod.combinations item=combination}
                                <option value="{$combination.id_product_attribute|intval}" {if $combination.id_product_attribute == 0}selected="selected"{/if}>{$combination.attributes|escape:'html':'UTF-8'}</option>
                            {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <h4>{l s='Information' mod='flashsales'}</h4>
                            <div class="formatted_information card-panel">
                                <div>
                                    <span class="strong">{l s='Reference' mod='flashsales'}:</span>
                                    <span class="reference">{$prod.reference|escape:'html':'UTF-8'}</span>
                                </div>
                                <div>
                                    <span class="strong">{l s='Stock' mod='flashsales'}:</span>
                                    <span class="stock">{$prod.combinations[0].stock|intval}</span>
                                </div>
                                <div>
                                    <span class="strong">{l s='Price' mod='flashsales'}:</span>
                                    <span class="price">{$prod.combinations[0].formatted_price|escape:'html':'UTF-8'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-reduction="" data-reduction-type="" data-from="" data-to="" class="row custom_price_content {if $selected}show{else}hide{/if}">
                        <div class="col-xs-12">
                            <h4>{l s='Apply a discount' mod='flashsales'}</h4>
                            <div class="material-switch">
                                <input 
                                    id="has_reduction" 
                                    name="has_reduction" 
                                    type="checkbox" 
                                    {if isset($prod.combinations[0].reduction) && $prod.combinations[0].reduction > 0}checked="checked"{/if}
                                />
                                <label data-toggle="has_reduction" class="label-info"></label>
                            </div>
                            <div class="formatted_reduction" style="display:{if isset($prod.combinations[0].reduction) && $prod.combinations[0].reduction > 0}block{else}none{/if};">
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
            <div class="panel-footer" data-product="{$prod.id_product|intval}">
                <input type="hidden" name="id_product" value="{$prod.id_product|intval}" />
                <a href="{Context::getContext()->link->getAdminLink('AdminProducts', true, ['id_product' => $prod.id_product|intval, 'updateproduct' => true])|addslashes}" class="btn btn-default fancybox"><i class="icon-search"></i> {l s='Details' mod='flashsales'}</a>
                <button type="button" class="card-action btn btn-default pull-right">
                    <div class="btn-remove"><i class="icon-refresh"></i> {l s='Remove' mod='flashsales'}</div>
                    <div class="btn-choose"><i class="icon-arrow-right"></i> {l s='Choose' mod='flashsales'}</div>
                </button>
            </div>
        </div>
    </div>
{/foreach}

<script type="text/javascript">
    $(document).ready(function() {
        Card.mergeCombinations(combs);
    });
</script>
{/if}
