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
<div id="dlc_bulk_panel_blank"></div>
{literal}
<script type="text/javascript">
    {/literal}{if isset($orderbox) && !empty($orderbox)}{literal}
        $(document).ready(function(){
            {/literal}{foreach $orderbox as $id_order}{literal}
                $("input[name='orderBox[]'][value={/literal}{$id_order|escape:'htmlall':'UTF-8'}{literal}]").prop("checked", true);
           {/literal}{/foreach}{literal}
        });
    {/literal}{/if}{literal}
</script>
{/literal}