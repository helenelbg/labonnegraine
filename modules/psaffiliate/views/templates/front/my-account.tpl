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
<li class="lnk_psaffiliate">
    
    {if $isAffiliate}
        <a href="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)|escape:'html':'UTF-8'}"
           title="{l s='My affiliate account' mod='psaffiliate'}">
            <i class="icon-group"></i>
            <span>{l s='My affiliate account' mod='psaffiliate'}</span>
        </a>
    {else}
        <a href="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)|escape:'html':'UTF-8'}"
           title="{l s='Become affiliate' mod='psaffiliate'}">
            <i class="icon-group"></i>
            <span>{l s='Become affiliate' mod='psaffiliate'}</span>
        </a>
    {/if}
</li>