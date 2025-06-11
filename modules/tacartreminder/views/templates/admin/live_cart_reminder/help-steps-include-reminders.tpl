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
 * Include in page help
 *}
<h3 class="link-tab-content" data-target="tab-content-reminders"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Reminders' mod='tacartreminder'}</h3>
<div id="tab-content-reminders" style="display:none">
		<h4>{l s='Legend' mod='tacartreminder'}</h4>
		<table>
			<tr>
				<td style="padding: 5px;">
					<span class="position_reminder_line " style="background-color:#79bd3c">2</span>
				</td>
				<td>
					{l s='"Green" indicates that the reminder has been performed' mod='tacartreminder'}
				</td>
			</tr>
			<tr>
				<td style="padding: 5px;">
					<span class="position_reminder_line pendinglaunch" style="background-color:orange">2</span>
				</td>
				<td>
					{l s='"Orange" current reminder; reminder will be processed or awaits employee completion' mod='tacartreminder'}
				</td>
			</tr>
			<tr>
				<td style="padding: 5px;">
					<span class="position_reminder_line" style="background-color:#999">2</span>
				</td>
				<td>
					{l s='"Grey" indicates that the reminder awaits completion of the previous reminder before being processed.' mod='tacartreminder'}
				</td>
			</tr>
			<tr>
				<td style="padding: 5px;">
					<span class="position_reminder_line pendingaccomplish" style="background-color:#FF0066">2</span>
				</td>
				<td>
					{l s='"Pink" manual reminder; contact the customer and indicate that the reminder is completed.' mod='tacartreminder'}
				</td>
			</tr>
		</table>
		<h4>{l s='Status & messages' mod='tacartreminder'}</h4>
		<table style="background:#fff;border:1px solid #CECECE;width:100%;color:#555">
			<tr style="border: 1px solid #ececec;">
				<td style="padding: 5px;background-color:orange;width:170px">
					<span class="flip-clock-wrapper" data-id-reminder="13" data-nb-second="3599">
					<span class="flip-clock-divider days"></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider hours"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider minutes"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">4</div></div><div class="down"><div class="shadow"></div><div class="inn">4</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">1</div></div><div class="down"><div class="shadow"></div><div class="inn">1</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">8</div></div><div class="down"><div class="shadow"></div><div class="inn">8</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider seconds"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip  play"><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">3</div></div><div class="down"><div class="shadow"></div><div class="inn">3</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip  play"><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">3</div></div><div class="down"><div class="shadow"></div><div class="inn">3</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul></span>
				</td>
				<td style="padding: 5px;">
					<p>{l s='Timer, in this example the reminder will be performed in 10 minutes.' mod='tacartreminder'}</p>
				</td>
			</tr>
			<tr style="border: 1px solid #ececec;">
				<td style="padding: 5px;background-color:orange;width:170px; color:#fff">
					{l s='Pending action' mod='tacartreminder'}
				</td>
				<td style="padding: 5px;">
					<p>{l s='Temporary status, waiting for the batch execution (cron), the batch is scheduled in the crontab.' mod='tacartreminder'}</p>
				</td>
			</tr>
			<tr style="border: 1px solid #ececec;">
				<td style="padding: 5px;background-color:#79bd3c;color:#fff">
					{l s='Completed' mod='tacartreminder'}
				</td>
				<td style="padding: 5px;">
					<p>{l s='The following applies to manual reminders, the reminder is considered completed (eg the customer was contacted by telephone).' mod='tacartreminder'}</ p>
				</td>
			</tr>
			<tr style="border: 1px solid #ececec;">
				<td style="padding: 5px;background-color:#79bd3c;color:#fff">
					{l s='Sent' mod='tacartreminder'}
				</td>
				<td style="padding: 5px;">
					{l s='The email was sent to the client.' mod='tacartreminder'}<br/>
					<i class="flaticon flaticon-eye46" style="color:#79bd3c;font-size:22px;" title="{l s='The email has been opened' mod='tacartreminder'}"></i>&nbsp; : {l s='Indicates that the email has been opened by the customer' mod='tacartreminder'}<br/>
					<i class="flaticon flaticon-left27" style="color:#79bd3c;font-size:22px;" title="{l s='The email has been clicked' mod='tacartreminder'}"></i>&nbsp; : {l s='Indicates that the link "complete order" was clicked by the customer' mod='tacartreminder'}
				</td>
			</tr>
			<tr style="border: 1px solid #ececec;">
				<td style="padding: 5px;background-color:#999;color:#fff">
					{l s='Waiting for the previous reminder' mod='tacartreminder'}
				</td>
				<td style="padding: 5px;">
					<p>{l s='The previous reminder should be executed or completed before it takes effect.' mod='tacartreminder'}</p>
				</td>
			</tr>
			<tr style="border: 1px solid #ececec;">
				<td style="padding: 5px;background-color:#FF0066;color:#fff">
					{l s='To do' mod='tacartreminder'}
				</td>
				<td style="padding: 5px;">
					<p>{l s='Manual reminder, you must contact the customer then indicate that the reminder has been completed.' mod='tacartreminder'}</p>
				</td>
			</tr>
		</table>
</div>