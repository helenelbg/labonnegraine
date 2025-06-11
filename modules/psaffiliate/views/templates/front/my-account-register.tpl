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
<div id="myaffiliateaccount-register">
    {capture name=path}
        <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='psaffiliate'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
        <span>{l s='Become affiliate' mod='psaffiliate'}</span>
    {/capture}

    <h2>{l s='Become affiliate' mod='psaffiliate'}</h2>

    {include file="$tpl_dir./errors.tpl"}

    {if !isset($error) || $error != 'group_blocked'}
        <div class="myaffiliateaccount-register-form">
            <form class="clearfix col-md-8" method="POST">
                {if isset($submitted) && $submitted}
                    {if (isset($success) && !$success) || (isset($errors) && sizeof($errors))}
                        <div class="alert alert-warning">
                            {if isset($errors) && sizeof($errors)}
                                {foreach from=$errors item=error}
                                    <p>{$error|escape:'htmlall':'UTF-8'}</p>
                                {/foreach}
                            {else}
                                <p>{l s='An error occured. Please try again later.' mod='psaffiliate'}</p>
                            {/if}
                        </div>
                    {/if}
                {/if}
                <div class="form-group clearfix">
                    <label class="col-sm-4 text-right" for="name">{l s='Name' mod='psaffiliate'}</label>
                    <div class="col-sm-8">
                        <input type="text" name="name" id="id" value="{$customer_name|escape:'htmlall':'UTF-8'}"
                               disabled="disabled" class="form-control"/>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="col-sm-4 text-right" for="email">{l s='Email' mod='psaffiliate'}</label>
                    <div class="col-sm-8">
                        <input type="text" name="email" id="email" value="{$customer_email|escape:'htmlall':'UTF-8'}"
                               disabled="disabled" class="form-control"/>
                    </div>
                </div>
                {if $ask_for_website}
                    <div class="form-group clearfix">
                        <label class="col-sm-4 text-right" for="website">{l s='Website' mod='psaffiliate'}</label>
                        <div class="col-sm-8">
                            <input type="text" name="website" id="website" value="" class="form-control"/>
                        </div>
                    </div>
                {/if}
                {if $textarea_at_registration}
                    <div class="form-group clearfix">
                        <label class="col-sm-4 text-right"
                               for="textarea_registration">{if $textarea_at_registration_label}{$textarea_at_registration_label|escape:'htmlall':'UTF-8'}{else}{l s='How will you promote us?' mod='psaffiliate'}{/if}{if $textarea_at_registration_required}*{/if}</label>
                        <div class="col-sm-8">
                            <textarea name="textarea_registration" id="textarea_registration"
                                      class="form-control"{if $textarea_at_registration_required} required="required"{/if}></textarea>
                        </div>
                    </div>
                {/if}

                {if $custom_fields}
                    {foreach $custom_fields as $custom_field}
                        <div class="form-group clearfix">
                            <label class="col-sm-4 text-right"
                                   for="custom_field_{$custom_field.id_field|escape:'htmlall':'UTF-8'}">
                                {$custom_field.name|escape:'htmlall':'UTF-8'}{if $custom_field.required}*{/if}
                            </label>
                            <div class="col-sm-8">
                                {if $custom_field.type == 'textarea'}
                                    <textarea name="custom_field_{$custom_field.id_field|escape:'htmlall':'UTF-8'}"
                                              id="custom_field_{$custom_field.id_field|escape:'htmlall':'UTF-8'}"
                                              class="form-control"
                                              rows="4" {if $custom_field.required} required{/if}></textarea>
                                {else}
                                    <input type="text"
                                           name="custom_field_{$custom_field.id_field|escape:'htmlall':'UTF-8'}"
                                           id="custom_field_{$custom_field.id_field|escape:'htmlall':'UTF-8'}"
                                           class="form-control"
                                            {if $custom_field.required} required{/if}>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                {/if}

                {if $display_terms_checkbox && $terms_cms_link}
                    <div class="form-group clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <p>
                                <label><input type="checkbox" name="terms_and_conditions"
                                              id="psaff_terms_and_conditions">
                                    {l s='I accept the' mod='psaffiliate'} <a href="{$terms_cms_link}" target="_blank">
                                        {l s='terms and conditions' mod='psaffiliate'}</a>.</label>
                            </p>
                        </div>
                    </div>
                {/if}
                <input type="hidden" name="submitRegisterAffiliate" value="1"/>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8">
                        <button type="submit" id="psaff_register"
                                class="btn btn-default"{if $display_terms_checkbox && $terms_cms_link} disabled{/if}>
                            {if $affiliates_require_approval}{l s='Ask for approval' mod='psaffiliate'}{else}{l s='Register' mod='psaffiliate'}{/if}</button>
                    </div>
                </div>
            </form>
        </div>
    {/if}
</div>