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

<div class="panel kpi-container">
    <div class="kpi-refresh">
        <button class="close refresh" type="button" onclick="refresh_kpis();">
            <i class="process-icon-refresh" style="font-size:1em"></i>
        </button>
    </div>
    <div class="row">
        {foreach from=$kpis item=kpi}
            <div class="col-sm-6 col-md-3 col-lg-2">
                {$kpi|cleanHtml nofilter}
            </div>
        {/foreach}
    </div>
</div>