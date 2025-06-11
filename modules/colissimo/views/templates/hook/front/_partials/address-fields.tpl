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

<p>
    <span>
        {l s='Please confirm these fields :' mod='colissimo'}<br>
    </span>
</p>
<div class="row">
    <label class="col-md-3 address-label">
        <span>{l s='Door porte 1' mod='colissimo'}</span>
    </label>
    <div class="col-md-6"><input type="text" class="fixed-width-md address-input" name="colissimo_code_porte1_{$reference|escape:'htmlall':'UTF-8'}" value="{$colissimo_address->code_porte1|escape:'htmlall':'UTF-8'}" maxlength="8"></div>
</div>
<div class="row">
    <label class="col-md-3 address-label">
        <span>{l s='Door porte 2' mod='colissimo'}</span>
    </label>
    <div <div class="col-md-6"><input type="text" class="fixed-width-md address-input" name="colissimo_code_porte2_{$reference|escape:'htmlall':'UTF-8'}" value="{$colissimo_address->code_porte2|escape:'htmlall':'UTF-8'}" maxlength="8"></div>
</div>


