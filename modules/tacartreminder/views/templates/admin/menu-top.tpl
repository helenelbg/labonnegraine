{*
 * Cart Reminder
 * 
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra
 *    @version 1.0.0
 *    @license   Commercial license
 *
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _           
 * |_   _(_)          / _ \     | | (_)          
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____ 
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *                                              
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * Template the main menu present in all admin page
 *}
{if isset($currentIndex)}
    <script type="text/javascript">currentIndex = {$currentIndex};</script>
{/if}
{if isset($mt_error)}
    <div class="conf confirm ta-alert alert-danger">
        {$mt_error}
    </div>
{/if}
<script type="text/javascript">
$(document).ready(function(){
		var setting_url = '{$link->getAdminLink('AdminModules')|escape:'quotes':'UTF-8'}&configure=tacartreminder&tab_select=settings';
		var admin_controller_url = '{$link->getAdminLink('AdminLiveCartReminder')|escape:'quotes':'UTF-8'}';
		$('.ta-module-admin-nav li').live('click', function(){
				if($(this).data('tabSelect')=='settings')
					location.href = setting_url;
				else 
					location.href = admin_controller_url + '&tab_select=' + $(this).data('tabSelect');
		});
	});
</script>
<div class="ta-module-admin-nav">
  <nav>
    <ul>
      {if {$count_manual|intval} > 0}<li class="{if $ta_cr_tab_select=='manual'}tab-current {/if}apple" data-tab-select='manual'><a class="flaticon flaticon-support3"><span>{l s='Manual to do' mod='tacartreminder'}</span><div class="ta-badge ta-badge-warning nav-animate-badge">{$count_manual|intval}</div></a></li>{/if}
      <li class="{if $ta_cr_tab_select=='cart'}tab-current {/if}purple" data-tab-select='cart'><a class="flaticon flaticon-shopping11"><span>{l s='Cart' mod='tacartreminder'}</span></a></li>
      <li class="{if $ta_cr_tab_select=='running'}tab-current {/if}apple" data-tab-select='running'><a class="flaticon flaticon-chronometer10"><span>{l s='Running' mod='tacartreminder'}</span></a></li>
      <li class="{if $ta_cr_tab_select=='finished'}tab-current {/if}apple" data-tab-select='finished'><a class="flaticon flaticon-task"><span>{l s='Completed' mod='tacartreminder'}</span></a></li>
      <li class="{if $ta_cr_tab_select=='unsubscribes'}tab-current {/if}apple" data-tab-select='unsubscribes'><a class="flaticon flaticon-man403"><span>{l s='Unsubscribed' mod='tacartreminder'}</span></a></li>
      <li class="{if $ta_cr_tab_select=='stats'}tab-current {/if}apple" data-tab-select='stats'><a class="flaticon flaticon-ascendant6"><span>{l s='Statistics' mod='tacartreminder'}</span></a></li>
      <li class="{if $ta_cr_tab_select=='settings'}tab-current {/if}apple" data-tab-select='settings'><a class="flaticon flaticon-tools6"><span>{l s='Settings' mod='tacartreminder'}</span></a></li>
    </ul>
  </nav>
</div>