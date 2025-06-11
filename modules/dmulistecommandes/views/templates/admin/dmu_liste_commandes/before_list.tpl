{**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL DREAM ME UP
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL DREAM ME UP is strictly forbidden.
*
*   .--.
*   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
*   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
*   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
*        w w w . d r e a m - m e - u p . f r       '
*
*  @author    Dream me up <prestashop@dream-me-up.fr>
*  @copyright 2007 - 2023 Dream me up
*  @license   All Rights Reserved
*}

{literal}
<script type="text/javascript">
     var dlc_oldversion = {/literal}{$dlc_oldversion|escape:"html":"UTF-8"}{literal};
     var adminOrders_token = '{/literal}{$adminOrders_token|escape:"html":"UTF-8"}{literal}';
     var adminDmuListeCommandes_token = '{/literal}{$adminDmuListeCommandes_token|escape:"html":"UTF-8"}{literal}';
     var dlc_status_on_line = {/literal}{$dlc_status_on_line|escape:"html":"UTF-8"}{literal};
     var dlc_show_buttons = {/literal}{$dlc_show_buttons|escape:"html":"UTF-8"}{literal};
     var dlc_warning_txt = '{/literal}{$dlc_warning_txt|escape:"html":"UTF-8"}{literal}';
     var dlc_status_change_txt = '{/literal}{$dlc_status_change_txt|escape:"html":"UTF-8"}{literal}';
     var dlc_views_list_txt = '{/literal}{$dlc_views_list_txt|escape:"html":"UTF-8"}{literal}';
     var dlc_tracking_txt = '{/literal}{$dlc_tracking_txt|escape:"html":"UTF-8"}{literal}';
     var dlc_from_txt = '{/literal}{$dlc_from_txt|escape:"html":"UTF-8"}{literal}';
     var dlc_to_txt = '{/literal}{$dlc_to_txt|escape:"html":"UTF-8"}{literal}';
     var dlc_postcode_from = '{/literal}{$dlc_postcode_from|escape:"html":"UTF-8"}{literal}';
     var dlc_postcode_to = '{/literal}{$dlc_postcode_to|escape:"html":"UTF-8"}{literal}';
     var reset = "{/literal}{l s='Reset' mod='dmulistecommandes'}{literal}";
     var dlc_views_list = [];
     {/literal}{foreach $views_list as $view}{literal}
         dlc_views_list.push("{/literal}{$view.id_vue|escape:"html":"UTF-8"}{literal}¤¤{/literal}{$view.name|escape:"html":"UTF-8"}{literal}");
     {/literal}{/foreach}{literal}

     $(document).ready(function() {
        {/literal}{if isset($special_searches) && !empty($special_searches)}{literal}
           {/literal}{foreach $special_searches as $id => $val}{literal}
                $('input[name=orderFilter_{/literal}{$id|escape:"html":"UTF-8"}{literal}]').val('{/literal}{$val|escape:"html":"UTF-8"}{literal}');
           {/literal}{/foreach}{literal}
           if ($('#submitFilterButtonorder').length && !$('button[name=submitResetorder]').length) {
               reset_button = '<button type="submit" name="submitResetorder" class="btn btn-warning">';
               reset_button += '<i class="icon-eraser"></i> '+reset+'</button>';
               $('#submitFilterButtonorder').parent().append(reset_button);
           }
        {/literal}{/if}{literal}
     });
     
 </script>
 {/literal}