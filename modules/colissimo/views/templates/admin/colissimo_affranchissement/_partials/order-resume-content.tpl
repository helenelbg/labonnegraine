{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="border-top">
  <div class="panel">
    <p class="colissimo-order-summary-title">{l s='Order summary' mod='colissimo'}</p>
    <p class="colissimo-order-summary">
      <span><b>{l s='Total order (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.amount currency=$order_totals.id_currency}
      <br/>
      <span><b>{l s='Total shipping (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.shipping currency=$order_totals.id_currency}
      <br/>
      <span><b>{l s='Total weight:' mod='colissimo'}</b></span>&nbsp;{$order_totals.weight|floatval} {$order_totals.weight_unit|escape:'htmlall':'UTF-8'}
      <br/>
      {if isset($delivery_duty_paid)}
        <span><b>{l s='Delivery duty paid:' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$delivery_duty_paid currency=$order_totals.id_currency}
      {/if}
    </p>
    {if isset($delivery_duty_paid) && $step == 2} 
      <span><strong>{l s='Label dimensions' mod='colissimo'} :</strong></span>
      <input type="text" class="label-dimensions fixed-width-sm" name="colissimo_label_length_{$id_colissimo_order|intval}" placeholder="{l s='Length' mod='colissimo'}" onchange="updateDDPInfo(this.name)"/>
      <input type="text" class="label-dimensions fixed-width-sm" name="colissimo_label_width_{$id_colissimo_order|intval}"  placeholder="{l s='Width' mod='colissimo'}"  onchange="updateDDPInfo(this.name)"/>
      <input type="text" class="label-dimensions fixed-width-sm" name="colissimo_label_height_{$id_colissimo_order|intval}" placeholder="{l s='Height' mod='colissimo'}" onchange="updateDDPInfo(this.name)" />
      <span class="label-articles-description"><strong>{l s='Generic articles description' mod='colissimo'} :</strong></span>
      <input type="text" class="input-articles-description" name="colissimo_articles_description_{$id_colissimo_order|intval}" onchange="updateDDPInfo(this.name)" />
    {/if}<br />
    {if isset($needCN23) && $step == 2}
        <span class="category-cn23"><strong>{l s='Shipment category' mod='colissimo'} :</strong></span>
        <select class="form-control category-cn23-select" name="colissimo_cn23_category_{$id_colissimo_order|intval}">
           <option value="3">{l s='Business shipment' mod='colissimo'}</option>
           <option value="1">{l s='Gift' mod='colissimo'}</option>
           <option value="2">{l s='Commercial sample' mod='colissimo'}</option>
           <option value="4">{l s='Document' mod='colissimo'}</option>
           <option value="5">{l s='Other' mod='colissimo'}</option>
           <option value="6">{l s='Merchandise return' mod='colissimo'}</option>
        </select>              
    {/if}
    
    <div class="table-responsive">
      <table class="table">
        <thead>
        <tr>
          {if $step == 2}
              <th><span class="title_box ">{l s='Quantity to ship' mod='colissimo'}</span></th>
          {/if}
          {if $step == 2 || $step == 4}
              <th><span class="title_box ">{l s='Quantity already shipped' mod='colissimo'}</span></th>
          {/if}
          <th><span class="title_box ">{l s='Product name' mod='colissimo'}</span></th>
          <th><span class="title_box ">{l s='Reference' mod='colissimo'}</span></th>
          <th><span class="title_box ">{l s='Quantity' mod='colissimo'}</span></th>
          <th>
            <span class="title_box ">{l s='Unit price' mod='colissimo'}</span>
            <small class="text-muted">{l s='(tax excl.)' mod='colissimo'}</small>
          </th>
          <th>
            <span class="title_box ">{l s='Unit weight' mod='colissimo'}</span>
            <small class="text-muted">{l s='(%s)' sprintf=$order_totals.weight_unit|escape:'htmlall':'UTF-8' mod='colissimo'}</small>
          </th>
        </tr>
        </thead>
        <tbody>
        {foreach $order_details as $order_detail}
          <tr class="product-line-row">
            {if $step == 2}
                <td><input type="text" class="input fixed-width-xs product-qunatity" name="colissimo_orderBox_{$id_colissimo_order|intval}_{$order_detail.product_id|intval}_{$order_detail.product_attribute_id|intval}" value="{$order_detail.product_quantity|intval}" onchange="checkQuantity(this.name, {$order_detail.product_quantity})"><span> / {$order_detail.product_quantity|intval} </span></td>
            {/if}
            {if $step == 2 || $step == 4}
                <td>{$products_shipped_qty[$order_detail.product_id][$order_detail.product_attribute_id]|intval}</td>

            {/if}
            <td>{$order_detail.product_name|escape:'htmlall':'UTF-8'}</td>
            <td>{$order_detail.product_reference|escape:'htmlall':'UTF-8'}</td>
            <td>{$order_detail.product_quantity|escape:'htmlall':'UTF-8'}</td>
            <td>{displayPrice price=$order_detail.unit_price_tax_excl|floatval currency=$order_totals.id_currency|intval}</td>
            <td>{$order_detail.product_weight|floatval}</td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
</div>
