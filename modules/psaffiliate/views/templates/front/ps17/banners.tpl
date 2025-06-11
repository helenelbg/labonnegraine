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
    <div id="banners">
        <div class="card">
            <div class="card-header">
                <h2 class="h3 m-t-sm m-b-sm">{l s='Banners' mod='psaffiliate'}</h2>
            </div>
            <div class="card-block">
                {if $hasBanners}
                    <div class="row">
                        {foreach from=$banners key=key item=banner}
                            <div class="col-sm-6 col-xs-12">
                                <div class="clearfix m-b">
                                    <h4 class="font-bold pull-sm-left m-t-sm">{$banner.title|escape:'htmlall':'UTF-8'}</h4>
                                    <div class="pull-sm-right">
                                        <button type="button" class="btn btn-sm btn-secondary btn-copy"
                                                data-clipboard-text="<a href=&quot;{$affiliate_link|escape:'htmlall':'UTF-8'}&quot; title=&quot;{$banner.title|escape:'htmlall':'UTF-8'}&quot;><img src=&quot;{$banner.image_link|escape:'htmlall':'UTF-8'}&quot; alt=&quot;{$banner.title|escape:'htmlall':'UTF-8'}&quot; title=&quot;{$banner.title|escape:'htmlall':'UTF-8'}&quot;/></a>">
                                            <i class="material-icons">content_copy</i> {l s='Copy code' mod='psaffiliate'}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary btn-copy"
                                                data-clipboard-text="{$banner.image_link|escape:'htmlall':'UTF-8'}"><i
                                                    class="material-icons">content_copy</i> {l s='Copy image source' mod='psaffiliate'}
                                        </button>
                                    </div>
                                </div>
                                {if $banner.image_link}
                                    <div class="card">
                                        <div class="card-block">
                                            <img class="img-fluid psaffiliate-banner"
                                                 src="{$banner.image_link|escape:'htmlall':'UTF-8'}"/>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                            {if $key%2 == 1}
                                <div class="clearfix"></div>
                            {/if}
                        {/foreach}
                    </div>
                {else}
                    <p>{l s='No banners available for now' mod='psaffiliate'}</p>
                {/if}
            </div>
        </div>
    </div>
{/block}