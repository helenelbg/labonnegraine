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
 * Page dedicated for stat all data information is loaded with jquery by json
 * when this page is ready all call request in ajax and load all data
 *}
<div class="panel ta-form" id="ta-calendar">
		<div class="row">
			<div class="col-lg-6">
				<div class="btn-group">
						<button type="button" name="submitDateDay" class="btn btn-default submitDateDay{if isset($preselect_date_range) && $preselect_date_range == 'day'} active{/if}">
							{l s='Day' mod='tacartreminder'}
						</button>
						<button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth{if isset($preselect_date_range) && $preselect_date_range == 'month'} active{/if}">
							{l s='Month' mod='tacartreminder'}
						</button>
						<button type="button" name="submitDateYear" class="btn btn-default submitDateYear{if isset($preselect_date_range) && $preselect_date_range == 'year'} active{/if}">
							{l s='Year' mod='tacartreminder'}
						</button>
						<button type="button" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-day'} active{/if}">
							{l s='Day' mod='tacartreminder'}-1
						</button>
						<button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-month'} active{/if}">
							{l s='Month' mod='tacartreminder'}-1
						</button>
						<button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-year'} active{/if}">
							{l s='Year' mod='tacartreminder'}-1
						</button>
				  </div>				
			</div>
			<div class="col-lg-6">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-xs-6">
							<label for="date-start" class="control-label" style="float: left;margin-right: 5px;width:auto">
									{l s='From' mod='tacartreminder'}
								</label>
							<div class="input-group">
								<input  name="date-start" id="date-start" value="" class="datepicker  form-control" data-date-format="Y-mm-dd">
								<span class="input-group-addon">
									<i class="flaticon-calendar146"></i>
								</span>
							</div>
								<!--div class="input-group">
									<label class="input-group-addon">Du</label>
									<input type="text" name="date-start" id="date-start" value="" class="datepicker  form-control" data-date-format="Y-mm-dd">
								</div-->
							</div>
							<div class="col-xs-6">
								<label for="date-end" class="control-label" style="float: left;margin-right: 5px;width:auto">
									{l s='To' mod='tacartreminder'}
								</label>
								<div class="input-group">
								<input name="date-end" id="date-end" value="" class="datepicker  form-control" data-date-format="Y-mm-dd">
								<span class="input-group-addon">
									<i class="flaticon-calendar146"></i>
								</span>
								</div>
								<!--div class="input-group">
									<label class="input-group-addon">Au</label>
									<input type="text" name="date-end" id="date-end" value="" class="datepicker  form-control" data-date-format="Y-mm-dd">
								</div-->
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="row">
							<button type="button" name="submitDatePicker" id="submitDatePicker" class="btn btn-default">{l s='Filter' mod='tacartreminder'}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
</div>

<div class="row" >
	<span class="ta_form_loader"  id="ta_form_loader_stat" style="margin-left: auto;margin-right: auto;"></span>
	<div id="stat_form_error"  style="display: none; padding: 15px 25px">
		<ul class="alert-list"></ul>
	</div>
</div>
<div class="row">
<div class="sidebar navigation col-md-3">
	<nav class="list-group">
		<a class="list-group-item active" href="javascript:;" data-target="sale">{l s='Sales' mod='tacartreminder'}</a>
		<a class="list-group-item" href="javascript:;" data-target="mail">{l s='Emails' mod='tacartreminder'}</a>
		<a class="list-group-item" href="javascript:;" data-target="rule">{l s='Rules' mod='tacartreminder'}</a>
		<a class="list-group-item" href="javascript:;" data-target="employee">{l s='Employees' mod='tacartreminder'}</a>
	</nav>
</div>
<div class="col-md-9">
<div class="panel ta-stat-content" data-stat-content="sale" display="none">
	<div class="panel-heading">
				{l s='Sales & conversion' mod='tacartreminder'}
	</div>
	<div class="ta-alert alert-info">
		<p>{l s='The following graphs represent :' mod='tacartreminder'}
		<ul>
			<li>{l s='Sales generated by cart reminders created during the selected period.' mod='tacartreminder'}</li>
			<li>{l s='The conversion rate is # of orders from a cart reminder/# of cart reminders for a selected period.' mod='tacartreminder'}</li>
		</ul>
		</p>
	</div>

	<div class="row">
		<div class="col-lg-12">
			
			
			<div class="col-lg-6 ta-st-summary">
				<div class="row">
					<div class="col-sm-6 ta-number-info-block">
						
							<h4>{l s='CA' mod='tacartreminder'}</h4>
							<span  class="ta-number ta-st-sum-total-sales"></span>
						
					</div>
					<div class="col-sm-6 ta-number-info-block">
						
							<h4>{l s='Conversion rate' mod='tacartreminder'}</h4>
							<span  class="ta-number ta-st-sum-conversion"></span>
						
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div id="ta-st-sum-pie">
					<svg style="width:320px;height:170px"></svg>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<section class="ta-stat-tab" >
				<div class="row ta-stat-tab-toolbar">
					<dl class="col-xs-6 col-lg-6 sales active" data-target='sales'>
						<dt>{l s='CA' mod='tacartreminder'}</dt>
						<dd class="data_value size_l"><span class="ta-st-sum-total-sales"></span></dd>
					</dl>
					<dl class="col-xs-6 col-lg-6 convertion-rate" data-target='conversion'>
						<dt>{l s='Conversion rate' mod='tacartreminder'}</dt>
						<dd class="data_value size_l"><span class="ta-st-sum-conversion"></span></dd>
					</dl>
				</div>
				<div id="ta-st-sum-stacked" class='chart with-transitions'>
					<svg style="height:250px;"></svg>
				</div>
				</section>
				
			</div>
		</div>
	</div>
</div>
<div class="panel ta-stat-content" data-stat-content="mail" style="display:none">
	<div class="panel-heading">
				{l s='Mails' mod='tacartreminder'}
	</div>
	<div class="row ta-st-summary">		
		<div class="col-lg-4 col-sm-6 ta-number-info-block">
				<h4>{l s='Sent' mod='tacartreminder'}</h4>
				<span  class="ta-number ta-st-sum-mails-sended"></span>
		</div>
		<div class="col-lg-4 col-sm-6 ta-number-info-block">
				<h4>{l s='Open' mod='tacartreminder'}</h4>
				<span  class="ta-number ta-st-sum-mails-open"></span>
		</div>
		<div class="col-lg-4 col-sm-6 ta-number-info-block">
				<h4>{l s='Clicked' mod='tacartreminder'}</h4>
				<span  class="ta-number ta-st-sum-mails-clicked"></span>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
		<table class="table ta-stat-table" id="ta-st-mail-line">
			<thead>
				<tr>
					<th class="text-center fixed-width-md">&nbsp;</th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Sended' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Opened' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Clicked' mod='tacartreminder'}</span></th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>		
		</div>
	</div>
</div>
<div class="panel ta-stat-content" data-stat-content="rule" style="display:none">
	<div class="panel-heading">
				{l s='Rules' mod='tacartreminder'}
	</div>
	<div class="ta-alert alert-info">
		<p>{l s='The following data lets you view the conversion rate for each rule.' mod='tacartreminder'}</p>
	</div>
	<div class="row">
		<div class="col-lg-12">
		<table class="table ta-stat-table" id="ta-st-rule-line">
			<thead>
				<tr>
					<th class="text-center fixed-width-md">&nbsp;</th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Cart' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Order' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Conversion' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Sales' mod='tacartreminder'}</span></th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>		
		</div>
	</div>
</div>
<div class="panel ta-stat-content" data-stat-content="employee" style="display:none">
	<div class="panel-heading">
		{l s='Employees' mod='tacartreminder'}
	</div>
	<div class="ta-alert alert-info">
		<p>{l s='The following data represents the number of reminders completed by each employee and their resulting sales.' mod='tacartreminder'}</p>
	</div>
	<div class="row">
		<div class="col-lg-12">
		<table class="table ta-stat-table" id="ta-st-employee-line">
			<thead>
				<tr>
					<th class="text-center fixed-width-md">&nbsp;</th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Completed' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Order' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Conversion' mod='tacartreminder'}</span></th>
					<th class="text-center fixed-width-md"><span class="title_box">{l s='Sale' mod='tacartreminder'}</span></th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
		</div>
	</div>
</div>
</div>
</div>
