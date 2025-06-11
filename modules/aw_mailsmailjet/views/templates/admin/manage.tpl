<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='Identifiants Mailjet' d='Modules.aw_mailsmailjet'}</h3>
    <form action="" method="post" class="form-horizontal">
        <div class="form-group">
			<label for="AW_MAILSMAILJET_LOGIN" class="col-lg-1 control-label">{l s='Clé API' d='Modules.aw_mailsmailjet'}</label>
			<div class="col-lg-3">
				<input type="text" name="AW_MAILSMAILJET_LOGIN" value="{$AW_MAILSMAILJET_LOGIN}" />
			</div>
		</div>
		<div class="form-group">
            <label for="AW_MAILSMAILJET_KEY" class="col-lg-1 control-label">{l s='Clé secrète' d='Modules.aw_mailsmailjet'}</label>
            <div class="col-lg-3">
                <input type="text" name="AW_MAILSMAILJET_KEY" value="{$AW_MAILSMAILJET_KEY}" />
            </div>
		</div>
        <div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right" name="submitLogin"><i class="process-icon-save"></i> {l s='Save' d='Admin.Actions'}</button>
		</div>
    </form>
</div>

{if isset($AW_MAILSMAILJET_TEMPLATE_MAILJET_LIST) && $AW_MAILSMAILJET_TEMPLATE_MAILJET_LIST != ''}
    <div class="panel aw_panel_template">
        <h3><i class="icon-cogs"></i> {l s='Les Templates' d='Modules.aw_mailsmailjet'}</h3>
        <form action="" method="post" class="form-horizontal">
            <div class="form-group col-lg-12">
                <label class="col-lg-12">{l s='Langue' d='Modules.aw_mailsmailjet'}</label>
                <select class="col-lg-3 aw_mailsmailjet_lang_id" name="AW_MAILSMAILJET_LANG_ID">
                    {foreach from=$AW_MAILSMAILJET_LANG_LIST key=lang_key item=lang_item}
                        {if $lang_item.name|substr:0:9 == 'Français'}
                            <option value="{$lang_item.id_lang}" selected>{$lang_item.name}</option>
                        {else}
                            <option value="{$lang_item.id_lang}">{$lang_item.name}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
            <div class="form-group col-lg-5 aw_mailsmailjet_template_mailjet_section">
                <label class="col-lg-12">{l s='Liste des templates Mailjet' d='Modules.aw_mailsmailjet'}</label>
                <select class="col-lg-6" name="AW_MAILSMAILJET_TEMPLATE_MAILJET_ID">
                    <option value="">--Veuillez choisir un template mailjet--</option>
                    {foreach from=$AW_MAILSMAILJET_TEMPLATE_MAILJET_LIST key=template_key item=template_item}
                        <option value="{$template_item.template_id}:{$template_item.template_name}">{$template_item.template_name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group col-lg-5 aw_mailsmailjet_template_prestashop_section">
                <label class="col-lg-12">{l s='Liste des templates Prestashop' d='Modules.aw_mailsmailjet'}</label>
                <select class="col-lg-6 aw_mailsmailjet_template_prestashop_id" name="AW_MAILSMAILJET_TEMPLATE_PRESTASHOP_ID">
                    <option value="">--Veuillez choisir un template prestashop--</option>
                    {foreach from=$AW_MAILSMAILJET_TEMPLATE_PRESTASHOP_LIST item=template_item}
                        <option value="{$template_item|substr:0:-4}">{$template_item|substr:0:-4}</option>
                    {/foreach}
                </select>
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-default pull-right" name="submitLinkTemplate"><i class="process-icon-save"></i> {l s='Save' d='Admin.Actions'}</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h3><i class="icon-cogs"></i> {l s='Les templates attribués' d='Modules.aw_mailsmailjet'}</h3>
        <form action="" method="post" class="form-horizontal">
            <div class="form-group col-lg-5">
                <label class="col-lg-12">{l s='Selection de la langue' d='Modules.aw_mailsmailjet'}</label>
                <select class="col-lg-6 aw_mailsmailjet_linked_template_lang_id" name="aw_mailsmailjet_linked_template_lang_id">
                    <option value="">--Veuillez choisir une langue--</option>
                    {foreach from=$AW_MAILSMAILJET_LANG_LIST key=lang_key item=lang_item}
                        <option value="{$lang_item.id_lang}">{$lang_item.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group linked_template_list_section">
                <div class="col-lg-12 linked_template_list">
                </div>  
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-default pull-right" name="submitDeleteLinkTemplate"><i class="process-icon-delete"></i> {l s='Supprimer' d='Admin.Actions'}</button>
            </div>
        </form>
    </div>
{/if}