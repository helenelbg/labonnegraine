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

{block name="order_detail"}
{/block}

{block name="customer_detail"}
  {if $show_visibility_btn}
    <button name="submitShowColissimoOrder" type="submit" class="btn btn-outline-primary js-colissimo-show-hidden-order colissimo-show-hidden-order" data-colissimo-order-id="{$id_colissimo_order|intval}">
      <i class="icon icon-eye-open"></i>
      {l s='Make order visible in Postage list' mod='colissimo'}
    </button>
  {/if}
  <p class="colissimo-order-summary-title">{l s='Order summary' mod='colissimo'}</p>
  <div class="row">
    <div class="col-sm-4">
      <p class="colissimo-order-summary">
        <span><b>{l s='Total order (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.amount currency=$order_totals.id_currency}
        <br/>
        <span><b>{l s='Total shipping (tax incl.):' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$order_totals.shipping currency=$order_totals.id_currency}
        <br/>
        <span><b>{l s='Total weight:' mod='colissimo'}</b></span>&nbsp;{$order_totals.weight|floatval} {$order_totals.weight_unit|escape:'htmlall':'UTF-8'}
        {if isset($delivery_duty_paid)} <br/><span><b>{l s='Delivery duty paid:' mod='colissimo'}</b></span>&nbsp;{displayPrice price=$delivery_duty_paid currency=$order_totals.id_currency}{/if}
        <br/><br/>
        <a class="btn btn-default colissimo-order-page"
           target="_blank"
           href="{$link->getAdminLink('AdminOrders')|escape:'htmlall':'UTF-8'}&vieworder&id_order={$id_order|intval}">
          <i class="icon icon-external-link"></i> {l s='Go to order page' mod='colissimo'}
        </a>
      </p>
    </div>
    <div class="col-sm-4">
      <p class="colissimo-order-summary">
        <span><i class="icon icon-truck"></i>&nbsp;<b>{l s='Delivery address' mod='colissimo'}</b></span><br/>
        <span>
        {if $delivery_addr->company}
          {$delivery_addr->company|escape:'htmlall':'UTF-8'}
          <br/>
        {/if}
          {$delivery_addr->firstname|escape:'htmlall':'UTF-8'} {$delivery_addr->lastname|escape:'htmlall':'UTF-8'}<br/>
        {$delivery_addr->address1|escape:'htmlall':'UTF-8'}<br/>
        {if $delivery_addr->address2}
          {$delivery_addr->address2|escape:'htmlall':'UTF-8'}
          <br/>
        {/if}
          {$delivery_addr->postcode|escape:'htmlall':'UTF-8'} {$delivery_addr->city|escape:'htmlall':'UTF-8'}{if $delivery_addr->id_state}, {State::getNameById($delivery_addr->id_state)|escape:'htmlall':'UTF-8'}{/if}
        <br/>
        {$delivery_addr->country|escape:'htmlall':'UTF-8'}
        <br/>
        {if isset($colissimo_address->code_porte1) && $colissimo_address->code_porte1}<span>{l s='Door code 1' mod='colissimo'} : {$colissimo_address->code_porte1|escape:'htmlall':'UTF-8'}</span><br/>{/if}
        {if isset($colissimo_address->code_porte2) && $colissimo_address->code_porte2}<span>{l s='Door code 2' mod='colissimo'} : {$colissimo_address->code_porte2|escape:'htmlall':'UTF-8'}</span><br/>{/if}
        </span>
        {if $pickup_point_id}
          <br/>
          <span class="colissimo-pickup-point-number">
          {l s='Pickup point No.:' mod='colissimo'}
            {$pickup_point_id|escape:'htmlall':'UTF-8'}
        </span>
        {/if}
      </p>
    </div>
    <div class="col-sm-4">
      <p class="colissimo-order-summary">
        <span><i class="icon icon-user"></i>&nbsp;<b>{l s='Contact details' mod='colissimo'}</b></span><br/>
        <span>
        {$customer->firstname|escape:'htmlall':'UTF-8'} {$customer->lastname|escape:'htmlall':'UTF-8'}<br/>
        {l s='Phone:' mod='colissimo'} {$delivery_addr->phone|escape:'htmlall':'UTF-8'}<br/>
        {l s='Mobile:' mod='colissimo'} {$delivery_addr->phone_mobile|escape:'htmlall':'UTF-8'}<br/>
        <a href="mailto:{$customer->email|escape:'htmlall':'UTF-8'}">{$customer->email|escape:'htmlall':'UTF-8'}</a>
      </span>
      </p>
    </div>
  </div>
{/block}

{block name="labels_actions_top"}
  <div class="row">
    <div class="col-sm-12 colissimo-create-label {if $coliship_enabled}coliship-enabled{/if}">
      <form method="post" action="{$link->getAdminLink('AdminColissimoAffranchissement')|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="colissimo_orderBox[]" value="{$id_colissimo_order|intval}"/>
        <button name="submitProcessColissimoSelection" type="submit" class="btn btn-primary">
          <i class="icon icon-plus-circle"></i>
          {l s='Create a label' mod='colissimo'}
        </button>
      </form>
    </div>
  </div>
{/block}

{block name="colissimo_table"}
  <div id="block-colissimo-shipments" class="colissimo-shipments-{$id_colissimo_order|intval}">
    {include file="./_shipments.tpl"}
  </div>
{/block}

{block name="labels_actions_bottom"}
  {if $shipments}
    <p class="colissimo-update-order-tracking">
      <button class="btn btn-primary" data-colissimo-order-id="{$id_colissimo_order|intval}">
        <i class="icon icon-refresh"></i> {l s='Update tracking of this order' mod='colissimo'}
      </button>
    </p>
  {/if}
{/block}

{block name="javascript_block"}
{literal}
  <script type="text/javascript">
      var newTheme = 0;
  </script>
{/literal}
  {$smarty.block.parent}
{/block}
