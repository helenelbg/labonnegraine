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
    <div id="campaign">
        {*{capture name=path}*}
        {*<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='My account' mod='psaffiliate'}</a>*}
        {*<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>*}
        {*<a href="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)|escape:'html':'UTF-8'}">{l s='My affiliate account' mod='psaffiliate'}</a>*}
        {*<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>*}
        {*<span>{if $id_campaign}{l s='Campaign #%s' mod='psaffiliate' sprintf=$id_campaign}{else}{l s='Create a campaign' mod='psaffiliate'}{/if}</span>*}
        {*{/capture}*}

        {if (!isset($hasErrorNoCampaignFound) || !$hasErrorNoCampaignFound) && (!isset($hasErrorNotYourCampaign) || !$hasErrorNotYourCampaign)}
            <div class="card">
                <div class="card-header">
                    <h2 class="h3 m-t-sm m-b-sm">
                        {if $id_campaign}
                            {l
                            s='Campaign "%camp%"'
                            sprintf=[
                            '%camp%' => $campaign.name
                            ]
                            d='Modules.Psaffiliate.Shop'
                            }
                        {else}
                            {l s='New campaign' mod='psaffiliate'}
                        {/if}</h2>
                </div>
                <div class="card-block">
                    {if isset($savedSuccess)}
                        {if $savedSuccess}
                            <div class="alert alert-success">{l s='Campaign saved successfully!' mod='psaffiliate'}</div>
                        {else}
                            <div class="alert alert-warning">{l s='Error! Could not save campaign, please try again later.' mod='psaffiliate'}</div>
                        {/if}
                    {/if}
                    {if $id_campaign}
                        <div class="form-group row">
                            <label class="col-sm-7 col-form-label label-my_affiliate_link">{l s='Campaign link:' mod='psaffiliate'}</label>
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="my_affiliate_link"
                                           value="{$campaign.link|escape:'html':'UTF-8'}" readonly>
                                    <span class="input-group-btn">
												<button type="button" class="btn btn-primary btn-sm btn-copy"
                                                        data-clipboard-target="#my_affiliate_link"><i
                                                            class="material-icons">filter_none</i></button>
											</span>
                                </div>
                            </div>
                        </div>
                    {/if}
                    <form method="POST" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3"
                                   for="name">{l s='Campaign name' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <input type="text" name="name" id="name"
                                       value="{$campaign.name|escape:'htmlall':'UTF-8'}" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3"
                                   for="description">{l s='Campaign description' mod='psaffiliate'}</label>
                            <div class="col-sm-7 col-md-6">
                                <textarea name="description" id="description" class="form-control"
                                          rows="6">{$campaign.description|escape:'htmlall':'UTF-8'}</textarea>
                            </div>
                        </div>
                        {if $id_campaign}
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Date created' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.date_created|date_format|escape:'html':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Date last earnings' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.date_lastactive|date_format|escape:'html':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Clicks' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.clicks|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Approved sales' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.sales|escape:'htmlall':'UTF-8'}
                                        /{$campaign.sales_total|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Total earnings for clicks' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.total_earnings_clicks_formatted|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Total earnings for sales' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.total_earnings_sales_formatted|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3">{l s='Total earnings' mod='psaffiliate'}</label>
                                <div class="col-sm-5 col-md-4">
                                    <div class="form-control-static">{$campaign.total_earnings_formatted|escape:'htmlall':'UTF-8'}</div>
                                </div>
                            </div>
                        {/if}
                        <input type="hidden" name="submitSaveCampaign" value="1"/>
                        <div class="row">
                            <div class="offset-sm-3 col-sm-5 col-md-4 m-t">
                                <button type="submit"
                                        class="btn btn-lg btn-success btn-block">{l s='Save' mod='psaffiliate'}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        {else}
            <div class="alert alert-warning">
                {if isset($hasErrorNoCampaignFound) && $hasErrorNoCampaignFound}
                    <p>{l s='Campaign not found' mod='psaffiliate'}</p>
                {elseif isset($hasErrorNotYourCampaign) && $hasErrorNotYourCampaign}
                    <p>{l s='This is not your campaign, nice try.' mod='psaffiliate'}</p>
                {/if}
            </div>
        {/if}
    </div>
{/block}