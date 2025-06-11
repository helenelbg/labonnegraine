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
{extends file='page.tpl'}
{block name="page_content"}
    <div id="texts">
        {*{capture name=path}*}
        {*<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='psaffiliate'}</a>*}
        {*<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>*}
        {*<a href="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)|escape:'html':'UTF-8'}">{l s='My affiliate account' mod='psaffiliate'}</a>*}
        {*<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>*}
        {*<span>{l s='Texts' mod='psaffiliate'}</span>*}
        {*{/capture}*}
        <div class="card">
            <div class="card-header">
                <h2 class="h3 m-t-sm m-b-sm">{l s='Texts' mod='psaffiliate'}</h2>
            </div>
            <div class="card-block">
                {if $hasTexts}
                    <div class="row">
                        {foreach from=$texts key=key item=text}
                            <div class="col-sm-6 col-xs-12">
                                <div class="clearfix m-b">
                                    <h4 class="font-bold pull-md-left m-t-sm">{$text.title|escape:'htmlall':'UTF-8'}</h4>
                                    <div class="pull-md-right">
                                        <button type="button" class="btn btn-sm btn-secondary btn-copy"
                                                data-clipboard-text="{$text.text_parsed|@strip_tags|escape:'htmlall':'UTF-8'}">
                                            <i class="material-icons">content_copy</i> {l s='Copy text' mod='psaffiliate'}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary btn-copy"
                                                data-clipboard-text="{$text.text_parsed|escape:'htmlall':'UTF-8'}"><i
                                                    class="material-icons">content_copy</i> {l s='Copy HTML' mod='psaffiliate'}
                                        </button>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-block">
                                        {$text.text_parsed nofilter} {* HTML CODE *}
                                    </div>
                                </div>
                            </div>
                            {if $key%2 == 1}
                                <div class="clearfix"></div>
                            {/if}
                        {/foreach}
                    </div>
                {else}
                    <p>{l s='No texts available for now' mod='psaffiliate'}</p>
                {/if}
            </div>
        </div>
    </div>
{/block}