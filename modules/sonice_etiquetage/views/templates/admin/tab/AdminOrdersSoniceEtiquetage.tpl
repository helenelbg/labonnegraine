{**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * @package   sonice_etiquetage
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @license   Commercial license
 *}

<!-- DATA -->
<input type="hidden" id="ps15x" value="{$ps15x|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_token_order_url" value="{$sne_token_order|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_last_session_used" value="{$sne_last_session_used|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_context_key" value="{$sne_context_key|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_print_type" value="{if isset($sne_config.output_print_type)}{$sne_config.output_print_type|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" id="sne_compact_mode" value="{$sne_config.compact_mode|default:0|intval}">

<!-- URL -->
<input type="hidden" id="sne_webservice_url" value="{$sne_webservice_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_pagination_url" value="{$sne_pagination_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_createsession_url" value="{$sne_createsession_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_usesession_url" value="{$sne_usesession_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_deletesession_url" value="{$sne_deletesession_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_deletelabel_url" value="{$sne_deletelabel_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_updatesession_url" value="{$sne_updatesession_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_getorderlist_url" value="{$sne_getorderlist_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_changesessionname_url" value="{$sne_changesessionname_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_generatelisting_url" value="{$sne_generatelisting_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_generatedeliveryslips_url" value="{$sne_generatedeliveryslips_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_generatetoday_url" value="{$sne_generatetoday_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_downloadcn23_url" value="{$sne_downloadcn23_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_modifyaddress_url" value="{$sne_modifyaddress_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_modifyweight_url" value="{$sne_modifyweight_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_getlabelexpedition_url" value="{$sne_getlabelexpedition_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_setorderassent_url" value="{$sne_setorderassent_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_fusionsession_url" value="{$sne_fusionsession_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_changeordercarrier_url" value="{$sne_changeordercarrier_url|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_print_documents" value="{$sne_print_documents|escape:'htmlall':'UTF-8'}">
<input type="hidden" id="sne_common_printserver" value="{$sne_common_printserver|escape:'htmlall':'UTF-8'}">

<!-- SUCCESS MSG -->
<input type="hidden" id="conf_rename" value="{l s='The session has been renamed successfully.' mod='sonice_etiquetage'}">
<input type="hidden" id="conf_fusion" value="{l s='The session has been fusionned successfully.' mod='sonice_etiquetage'}">

<!-- ERROR MSG -->
<input type="hidden" id="err_ajax_no_data_received" value="{l s='Ajax call did not send data back.' mod='sonice_etiquetage'}">
<input type="hidden" id="err_create_false" value="{l s='The request did not succeed to create the session.' mod='sonice_etiquetage'}">
<input type="hidden" id="err_delete_false" value="{l s='The request did not succeed to delete the session.' mod='sonice_etiquetage'}">
<input type="hidden" id="err_update_false" value="{l s='The request did not succeed to update the session.' mod='sonice_etiquetage'}">
<input type="hidden" id="err_exp_false" value="{l s='An error occured while updating label as "sent".' mod='sonice_etiquetage'}">
<input type="hidden" id="err_printer_not_set" value="{l s='Label printer has not been set.' mod='sonice_etiquetage'}">

<!-- WARNING MSG -->
<input type="hidden" id="warn_id_session_not_exists" value="{l s='The session ID was not returned by the Ajax call, some problems may occur.' mod='sonice_etiquetage'}">
<input type="hidden" id="warn_no_alias_received" value="{l s='The Ajax request did not return the session alias, some problems may occur.' mod='sonice_etiquetage'}">
<input type="hidden" id="warn_listing_no_selection" value="{l s='You need to select the orders you want to appear in the listing.' mod='sonice_etiquetage'}">
<input type="hidden" id="warn_infinite_loop" value="{l s='The system noticed an infinite loop and stop the process, please refresh the page.' mod='sonice_etiquetage'}">

<!-- OTHER MSG -->
<input type="hidden" id="prompt_ta" value="{l s='Assurance value' mod='sonice_etiquetage'}">
<input type="hidden" id="confirm_delete_label" value="{l s='Do you want to delete this label ?' mod='sonice_etiquetage'}">
<input type="hidden" id="no_cn23_to_print" value="{l s='There is no CN23 to print in the current session.' mod='sonice_etiquetage'}">

<!-- PRINTER CONF -->
<input type="hidden" id="printer1_name" value="{if isset($sne_config.printer1)}{$sne_config.printer1|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" id="printer2_name" value="{if isset($sne_config.printer2)}{$sne_config.printer2|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" id="selected_printer" value="{if isset($sne_config.printing)}{$sne_config.printing|escape:'htmlall':'UTF-8'}{/if}">
<input type="hidden" id="merge_pdf" value="{if isset($sne_config.merge_pdf) && $sne_config.merge_pdf}1{else}0{/if}">

<!-- RESSOURCES -->
<input type="hidden" id="img_tick" value="{$sne_img|escape:'htmlall':'UTF-8'}tick.png">

{if isset($sne_config.legacy) && $sne_config.legacy}
    <!-- QZ -->
    <applet id="qz" code="qz.PrintApplet.class" archive="{$sne_module_url|escape:'htmlall':'UTF-8'}tools/applet/qz-print.jar" width="1" height="1">
        <param name="jnlp_href" value="{$sne_module_url|escape:'htmlall':'UTF-8'}tools/applet/qz-print_jnlp.jnlp">
        <param name="cache_option" value="plugin">
        <param name="disable_logging" value="false">
        <param name="initial_focus" value="false">
        <param name="codebase_lookup" value="false">
    </applet>
{/if}

<!-- TABS -->
<div id="sneTab">
    <ul id="menuTab">
        <li id="menu-session" class="menuTabButton selected"><span>&nbsp;<img src="{$sne_img|escape:'htmlall':'UTF-8'}report_stack.png" alt="package" />&nbsp;Session</span></li>
        <li id="menu-listing" class="menuTabButton"><span>&nbsp;<img src="{$sne_img|escape:'htmlall':'UTF-8'}to_do_list_cheked_all.png" alt="list" />&nbsp;{l s='Listing' mod='sonice_etiquetage'}</span></li>
        <li id="menu-orders" class="menuTabButton"><span>&nbsp;<img src="{$sne_img|escape:'htmlall':'UTF-8'}barcode.png" alt="package" />&nbsp;{l s='Orders' mod='sonice_etiquetage'}</span></li>
        <li id="menu-exp" class="menuTabButton"><span>&nbsp;<img src="{$sne_img|escape:'htmlall':'UTF-8'}package_go.png" alt="package" />&nbsp;{l s='Expedition' mod='sonice_etiquetage'}</span></li>
        <li id="menu-close" class="menuTabButton"><span>&nbsp;<img src="{$sne_img|escape:'htmlall':'UTF-8'}lock.png" alt="package" />&nbsp;{l s='Close session' mod='sonice_etiquetage'}</span></li>
    </ul>
</div>
<div class="clean">&nbsp;</div>
<fieldset id="tabs">
    <!-- SESSION -->
    {include file="$sne_module_path/views/templates/admin/tab/session.tpl"}
    
    <!-- LISTING -->
    {include file="$sne_module_path/views/templates/admin/tab/listing.tpl"}
    
    <!-- ORDERS -->
    {include file="$sne_module_path/views/templates/admin/tab/orders.tpl"}
    
    <!-- EXPEDITION -->
    {include file="$sne_module_path/views/templates/admin/tab/expedition.tpl"}
    
    <!-- CLOSE -->
    {include file="$sne_module_path/views/templates/admin/tab/close.tpl"}
</fieldset>
    
<iframe id="PDF_to_print" style="display:none;"></iframe>