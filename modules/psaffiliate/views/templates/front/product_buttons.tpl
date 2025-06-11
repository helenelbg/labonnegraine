{*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code.
*
*  @author Active Design <office@activedesign.ro>
*  @copyright  2017-2018 Active Design
*  @license LICENSE.txt
*}
<div class="form-group row psaffiliates-product-link ps16">
    <label class="control-label">{l s='Affiliate link:' mod='psaffiliate'}</label>
    <div class="">
        <div class="input-group">
            <input type="text" class="form-control" id="product_affiliate_link"
                   value="{$product_affiliate_link|escape:'html':'UTF-8'}" readonly>
            <span class="input-group-btn">
        <button type="button" class="btn btn-default btn-copy" data-clipboard-target="#product_affiliate_link"><i
                    class="icon-clipboard"></i></button>
      </span>
        </div>
        <p class="mt-1 text-right">{l s='Commision:' mod='psaffiliate'} {$product_commision}</p>
    </div>
</div>