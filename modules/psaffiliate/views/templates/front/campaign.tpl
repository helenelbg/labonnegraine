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
<div id="campaign">
    {capture name=path}
        <a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='My account' mod='psaffiliate'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
        <a href="{$link->getModuleLink('psaffiliate', 'myaccount', array(), true)|escape:'html':'UTF-8'}">{l s='My affiliate account' mod='psaffiliate'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
        <span>{if $id_campaign}{l s='Campaign #%s' mod='psaffiliate' sprintf=$id_campaign}{else}{l s='Create a campaign' mod='psaffiliate'}{/if}</span>
    {/capture}

    {if (!isset($hasErrorNoCampaignFound) || !$hasErrorNoCampaignFound) && (!isset($hasErrorNotYourCampaign) || !$hasErrorNotYourCampaign)}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="h3 m-t-sm m-b-sm">{if $id_campaign}{l s='Campaign #%s' mod='psaffiliate' sprintf=$id_campaign}{else}{l s='New campaign' mod='psaffiliate'}{/if}</h2>
            </div>
            <div class="panel-body">
                {if isset($savedSuccess)}
                    {if $savedSuccess}
                        <div class="alert alert-success">{l s='Campaign saved successfully!' mod='psaffiliate'}</div>
                    {else}
                        <div class="alert alert-warning">{l s='Error! Could not save campaign, please try again later.' mod='psaffiliate'}</div>
                    {/if}
                {/if}
                {if $id_campaign}
                    <p class="text-center-xs text-right m-b-lg">
                        <strong>{l s='Campaign link:' mod='psaffiliate'}</strong> <a
                                href="{$campaign.link|escape:'htmlall':'UTF-8'}" target="_blank"
                                title="{$campaign.title|escape:'htmlall':'UTF-8'}">{$campaign.link|escape:'htmlall':'UTF-8'}</a>
                    </p>
                {/if}
                <form method="POST" class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="name">{l s='Campaign name' mod='psaffiliate'}</label>
                        <div class="col-sm-5 col-md-4">
                            <input type="text" name="name" id="name" value="{$campaign.name|escape:'htmlall':'UTF-8'}"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"
                               for="description">{l s='Campaign description' mod='psaffiliate'}</label>
                        <div class="col-sm-7 col-md-6">
                            <textarea name="description" id="description" class="form-control"
                                      rows="6">{$campaign.description|escape:'htmlall':'UTF-8'}</textarea>
                        </div>
                    </div>
                    {if $id_campaign}
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Date created' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{$campaign.date_created|date_format|escape:'html':'UTF-8'}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Date last earnings' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{$campaign.date_lastactive|date_format|escape:'html':'UTF-8'}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Clicks' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{$campaign.clicks|escape:'htmlall':'UTF-8'}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Approved sales' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{$campaign.sales|escape:'htmlall':'UTF-8'}
                                    /{$campaign.sales_total|escape:'htmlall':'UTF-8'}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Total earnings for clicks' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{convertPrice price=$campaign.total_earnings_clicks}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Total earnings for sales' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{convertPrice price=$campaign.total_earnings_sales}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3">{l s='Total earnings' mod='psaffiliate'}</label>
                            <div class="col-sm-5 col-md-4">
                                <div class="form-control-static">{convertPrice price=($campaign.total_earnings_clicks + $campaign.total_earnings_sales)}</div>
                            </div>
                        </div>
                    {/if}
                    <input type="hidden" name="submitSaveCampaign" value="1"/>
                    <div class="row">
                        <div class="col-sm-offset-3 col-sm-5 col-md-4 m-t">
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