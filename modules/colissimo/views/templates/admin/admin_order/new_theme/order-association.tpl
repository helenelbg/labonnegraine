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
  <div class="alert alert-info">
    <p>{l s='This order has not been placed using a Colissimo carrier.' mod='colissimo'}</p>
    <p>{l s='However, you can still associate it to Colissimo in order to generate label, track shipments etc...' mod='colissimo'}</p>
    <p>{l s='Choose between home delivery or pickup delivery...' mod='colissimo'}</p>
  </div>
  <div class="row">
    <a class="colissimo-service-selection btn btn-primary ml-3" href="#" data-id-order="{$id_order|intval}">
      <i class="icon icon-link"></i>
      {l s='Associate order to Colissimo' mod='colissimo'}
    </a>
  </div>
{/block}

{block name="customer_detail"}{/block}
{block name="labels_actions_top"}{/block}
{block name="colissimo_table"}{/block}
{block name="labels_actions_bottom"}{/block}

{block name="javascript_block"}
{literal}
  <script type="text/javascript">
      var newTheme = 1;
  </script>
{/literal}
  {$smarty.block.parent}
{/block}
