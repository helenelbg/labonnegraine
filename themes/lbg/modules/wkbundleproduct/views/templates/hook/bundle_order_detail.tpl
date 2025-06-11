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
{if !empty($order_info)}
    {if $is_admin == 1}
        <div class="panel" style="background-color: #fff;">
            {if $wk_ps_version >= '1.7.7.0'}
                <div class="card-header">
                    <h3 class="card-header-title">
                        {l s='This order contains following bundle\'s product(s), which is splited after order.' mod='wkbundleproduct'}
                    </h3>
                </div>
            {else}
                <div class="panel-heading">
                    <i class="icon-list"></i>
                    {l s='This order contains following bundle\'s product(s), which is splited after order.' mod='wkbundleproduct'}
                </div>
            {/if}
            <div class="{if $wk_ps_version >= '1.7.7.0'}card-body{else}panel-body{/if}">
                {foreach $order_info as $info}
                    {if $info.link == ''}
                        <p>{$info.name}</p>
                    {else}
                        <p><a href="{$info.link}" target="blank">{$info.name}</a></p>
                    {/if}
                {/foreach}
            </div>
        </div>
    {else}
        <div class="box">
            <h4>{l s='This order contains following bundle\'s product(s), which is splited after order.' mod='wkbundleproduct'}</h4>
            {foreach $order_info as $info}
                {if $info.link == ''}
                    <p>{$info.name}</p>
                {else}
                    <p><a href="{$info.link}" target="blank">{$info.name}</a></p>
                {/if}
            {/foreach}
        </div>
    {/if}
{/if}

