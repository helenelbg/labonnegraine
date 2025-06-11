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
 * Page list all message and user can
 * filter all notification by employee or by notification system
 *}
<div id="content" class="bootstrap ta-to-bootstrap" style="margin: 5px;padding: 0;" >
<div class="ta-filter-tab-content">
	<ul class="ta-filter-tab" data-filter-content-id="fa-content-messages" data-filter-item-class="message-item">
		<li class="ta-filter-all active" data-filter-target=""><i class="flaticon-list30" ></i>{l s='All' mod='tacartreminder'} <span class="ta-badge ta-count-element">4</span></li>
		<li class="ta-filter-employee" data-filter-target="employee"><i class="flaticon-man457"></i>{l s='Employee' mod='tacartreminder'} <span class="ta-badge ta-count-element">4</span></li>
		<li class="ta-filter-system" data-filter-target="notification"><i class="flaticon-bell31"></i>{l s='Notification' mod='tacartreminder'} <span class="ta-badge  ta-count-element">4</span></li> 
	</ul>
</div>
<div class="row" style="background-color: #fff;" id="fa-content-messages">

{foreach from=$messages item=message name=blockMessages}
	{include file="./message-item.tpl" message=$message}
{/foreach}
</div>
</div>