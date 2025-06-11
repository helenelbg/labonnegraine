{**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 *}


<h3>
    {l s='Your order (#%s) on %s is complete.' sprintf=[$order_reference, $shop_name] mod='scalapay'}
</h3>

<p>
    <strong>{l s='Your payment was successfully accepted. ' mod='scalapay'}</strong>
</p>

<ul>
    <li>
        {l s='Amount Payed:' mod='scalapay'}
        <span class="price"><strong>{$total_payed}</strong></span>
    </li>
    <li>
        {l s='Scalapay order token:' mod='scalapay'}
        <strong>{$scalapay_order_token}</strong>
    </li>
</ul>

<p>
    {l s='An email has been sent to you with this information.' mod='scalapay'}
</p>

<p>
    {l s='For any questions or for further information, please contact our' mod='scalapay'}
    <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department' mod='scalapay'}</a>.
</p>
