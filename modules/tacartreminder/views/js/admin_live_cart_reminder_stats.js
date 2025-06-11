/**
 * Cart Reminder
 * 
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA <support@timactive.com>
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra AI
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
 * All action need for Stat tab
 * All data is loaded with json
 */

var data_stats = [];
var chart_line_summary = nv.models.lineChart()
        .margin({right: 100})
        .x(function(d) { return (d !== undefined ? d[0] : 0); })
		.y(function(d) { return (d !== undefined ? d[1] : 0); })
        .useInteractiveGuideline(true)    //Tooltips which show all data points. Very nice!
        .rightAlignYAxis(true)      //Let's move the y-axis to the right side.
        .transitionDuration(500)
        .clipEdge(true);
chart_line_summary.xAxis.tickFormat(function(d) { 
   		return d3.time.format('%d/%m/%y')(new Date(d*1000))
});

var chart_pie_summary = nv.models.pieChart()
      	.x(function(d) { return d.label })
      	.y(function(d) { return d.value })
      	.showLabels(true)     //Display pie labels
      	.labelThreshold(.05)  //Configure the minimum slice size for labels to show up
      	.labelType("value") //Configure what type of data to show in the label. Can be "key", "value" or "percent"
      	.donut(true)          //Turn on Donut mode. Makes pie chart look tasty!
      	.donutRatio(0.2);     //Configure how big you want the donut hole size to be.

$( document ).ready(function() {
	$(".list-group-item").click(function(){
		$(".ta-stat-content").hide();
		$(".list-group-item").removeClass('active');
		$(this).addClass('active');
		var target_content = $(this).data('target');
		$('*[data-stat-content="'+target_content+'"]').show();
	});
	$("#ta-calendar .datepicker").datepicker({
								prevText: '',
								nextText: '',
								dateFormat: 'yy-mm-dd'
	});
	nv.addGraph(function() {
	    return chart_line_summary;
	});
	nv.addGraph(function() {
	    return chart_pie_summary;
	});
	
	$('#submitDatePicker').click(function() {
		processStatData();		
	});

	$('.ta-stat-tab-toolbar dl').click(function() {
				$(this).parent().find('dl').removeClass('active');
				$(this).addClass('active');
				initSummaryChart();
	});

	$('.submitDateDay').on('click',function(e){
		e.preventDefault;
		setDayPeriod();
	});
	$('.submitDateMonth').on('click',function(e){
		e.preventDefault;
		setMonthPeriod()
	});
	$('.submitDateYear').on('click',function(e){
		e.preventDefault;
		setYearPeriod();
	});
	$('.submitDateDayPrev').on('click',function(e){
		e.preventDefault;
		setPreviousDayPeriod();
	});
	$('.submitDateMonthPrev').on('click',function(e){
		e.preventDefault;
		setPreviousMonthPeriod();
	});
	$('.submitDateYearPrev').on('click',function(e){
		e.preventDefault;
		setPreviousYearPeriod();
	});
	$('.submitDateMonth').trigger('click');
});

function processStatData()
{
	var url = 'index.php?controller=AdminLiveCartReminder';
	var loader = $('#ta_form_loader_stat');
	loader.show();
	var $form_error = $('#stat_form_error');
	$.ajax({
	      type: "POST",
	      cache: false,
	      async: true,
		  cache: false,
		  url:	url,
		  dataType: 'json',
		  data: 'submitAction=getStats'+
		  	'&action=get_stats'+
		  	'&date-start='+$('#date-start').val() + 
		  	'&date-end='+$('#date-end').val() + 
		  	'&token=' + token +
		  	'&rand=' + new Date().getTime(),
	      success: function (data) {
	      	loader.hide();
	      	if (!data.has_error)
			{
			  data_stats = data;
			  console.log(data_stats);
			  refreshStatDisplay();
			}
			else
			{
				$form_error.find('ul').html('');
					$.each(data.errors, function(index, value) {
						$form_error.find('ul').append('<li class="ta-alert alert-danger">'+value+'</li>');
					});
					$form_error.slideDown('slow');
			}
		  }
		});
}
function refreshStatDisplay()
{
	/*Panel transformation*/
	var sum_total_sales_display = formatCurrency(parseFloat(data_stats['stats-order-summary'].total_sales), currency_format, currency_sign, currency_blank);
	var reminders_count = parseInt(data_stats['stats-order-summary'].count_cart_reminders);
	var reminders_order_count = parseInt(data_stats['stats-order-summary'].count_orders);
	var conversion_percent_display = '0&#37;';
	if(reminders_order_count > 0)
		conversion_percent_display = Math.round(((reminders_order_count/reminders_count) * 100)) + '&#37;';
	$('.ta-st-sum-total-sales').html(sum_total_sales_display);
	$('.ta-st-sum-conversion').html(conversion_percent_display);
	/*Panel mails*/
	var mails_sended = parseInt(data_stats['stats-mail-summary'].nb_send) || 0;
	var mails_open = parseInt(data_stats['stats-mail-summary'].nb_open) || 0;
	var mails_clicked = parseInt(data_stats['stats-mail-summary'].nb_click) || 0;
	$('.ta-st-sum-mails-sended').html(mails_sended);
	$('.ta-st-sum-mails-clicked').html(mails_clicked);
	$('.ta-st-sum-mails-open').html(mails_open);
	var table_line_mails = data_stats['table-line']['mail'];
	$('#ta-st-mail-line > tbody:last').html('');
	$.each(table_line_mails, function(i, mail_stat_line) {
		var tr_htm = '<tr><td>' + mail_stat_line.name + '</td>';
		tr_htm += '<td class="ta-number sended">' + mail_stat_line.nb_send + '</td>';
		tr_htm += '<td class="ta-number open">' + mail_stat_line.nb_open + '</td>';
		tr_htm += '<td class="ta-number clicked">' + mail_stat_line.nb_click + '</td></tr>';
		$('#ta-st-mail-line > tbody:last').append(tr_htm);
	});
	var table_line_rules = data_stats['table-line']['rule'];
	$('#ta-st-rule-line > tbody').html('');
	$.each(table_line_rules, function(i, rule_stat_line) {
		var conversion = 0;
		var nb_reminder = parseInt(rule_stat_line.cart_reminder_nb);
		var nb_order = parseInt(rule_stat_line.order_sum_nb);
		if(nb_order > 0)
			conversion  = (nb_order / nb_reminder) * 100;
		var tr_htm = '<tr><td>' + rule_stat_line.name + '</td>';
		tr_htm += '<td class="ta-number cart">' + rule_stat_line.cart_reminder_nb+ '</td>';
		tr_htm += '<td class="ta-number order_sum">' + rule_stat_line.order_sum_nb + '</td>';
		tr_htm += '<td class="ta-number conv">' + (Math.round(conversion*100))/100 + '&#37;</td>';
		tr_htm += '<td class="ta-number sale">' + formatCurrency(parseFloat(rule_stat_line.order_sum_paid), currency_format, currency_sign, currency_blank) + '</td></tr>';
		$('#ta-st-rule-line > tbody:last').append(tr_htm);
	});
	var table_line_employees = data_stats['table-line']['employee'];
	$('#ta-st-employee-line > tbody').html('');
	$.each(table_line_employees, function(i, employee_stat_line) {
		var conversion = 0;
		var nb_reminder = parseInt(employee_stat_line.cart_reminder_nb);
		var nb_order = parseInt(employee_stat_line.order_sum_nb);
		if(nb_order > 0)
			conversion  = (nb_order / nb_reminder) * 100;
		var tr_htm = '<tr><td>' + employee_stat_line.firstname + ' ' + employee_stat_line.lastname +'</td>';
		tr_htm += '<td class="ta-number cart">' + nb_reminder+ '</td>';
		tr_htm += '<td class="ta-number order_sum">' + nb_order + '</td>';
		tr_htm += '<td class="ta-number conv">' + (Math.round(conversion*100))/100 + '&#37;</td>';
		tr_htm += '<td class="ta-number sale">' + formatCurrency(parseFloat(employee_stat_line.order_sum_paid), currency_format, currency_sign, currency_blank) + '</td></tr>';
		$('#ta-st-employee-line > tbody:last').append(tr_htm);
	});
	initSummaryChart();
}

function initSummaryChart()
{
	var chart_select = $('.ta-stat-tab-toolbar dl.active').data('target');
	chart_line_summary.yAxis.tickFormat(d3.format('.f'));
	if (chart_select == 'sales')
		chart_line_summary.yAxis.tickFormat(function(d) {
			return formatCurrency(parseFloat(d), currency_format, currency_sign, currency_blank);
	});
	if(chart_select == 'conversion')
		d3.select('#ta-st-sum-stacked svg')
			.datum(data_stats['stats-trends'].conversion)
			.call(chart_line_summary);
	else
		d3.select('#ta-st-sum-stacked svg')
			.datum(data_stats['stats-trends'].order)
			.call(chart_line_summary);

	nv.utils.windowResize(chart_line_summary.update);

	d3.select("#ta-st-sum-pie svg")
        	.datum(data_stats['stats-trends'].conversion_pie)
        	.transition().duration(350)
        	.call(chart_pie_summary);
    nv.utils.windowResize(chart_pie_summary.update);
}

function setDayPeriod() {
	date = new Date();
	$("#date-start").val(date.format($("#date-start").data('date-format')));
	$("#date-end").val(date.format($("#date-end").data('date-format')));
	processStatData();
}

function setPreviousDayPeriod() {
	date = new Date();
	date = date.subDays(1);
	$("#date-start").val(date.format($("#date-start").data('date-format')));
	$("#date-end").val(date.format($("#date-end").data('date-format')));
	processStatData();
}

function setMonthPeriod() {
	date = new Date();
	$("#date-end").val(date.format($("#date-end").data('date-format')));
	date = new Date(date.setDate(1));
	$("#date-start").val(date.format($("#date-start").data('date-format')));
	processStatData();
}

function setPreviousMonthPeriod() {
	date = new Date();
	date = new Date(date.getFullYear(), date.getMonth(), 0);
	$("#date-end").val(date.format($("#date-end").data('date-format')));
	date = new Date(date.setDate(1));
	$("#date-start").val(date.format($("#date-start").data('date-format')));
	processStatData();	
}

function setYearPeriod() {
	date = new Date();
	$("#date-end").val(date.format($("#date-end").data('date-format')));
	date = new Date(date.getFullYear(), 0, 1);
	$("#date-start").val(date.format($("#date-start").data('date-format')));	
	processStatData();
}

function setPreviousYearPeriod() {
	date = new Date();
	date = new Date(date.getFullYear(), 11, 31);
	date = date.subYears(1);
	$("#date-end").val(date.format($("#date-end").data('date-format')));
	date = new Date(date.getFullYear(), 0, 1);
	$("#date-start").val(date.format($("#date-start").data('date-format')));
	$('#date-start').trigger('change');
	processStatData();
}




/*ADD FUNCTION TO DATE*/
Date.prototype.addDays = function(value) {
	this.setDate(this.getDate() + value);

	return this;
};

Date.prototype.addMonths = function(value) {
	var date = this.getDate();
	this.setMonth(this.getMonth() + value);

	if (this.getDate() < date) {
		this.setDate(0);
	}

	return this;
};

Date.prototype.addWeeks = function(value) {
	this.addDays(value * 7);

	return this;
};

Date.prototype.addYears = function(value) {
	var month = this.getMonth();
	this.setFullYear(this.getFullYear() + value);

	if (month < this.getMonth()) {
		this.setDate(0);
	}

	return this;
};

Date.parseDate = function(date, format) {
	if (format === undefined)
		format = 'Y-m-d';

	var formatSeparator = format.match(/[.\/\-\s].*?/);
	var formatParts     = format.split(/\W+/);
	var parts           = date.split(formatSeparator);
	var date            = new Date();

	if (parts.length === formatParts.length) {
		date.setHours(0);
		date.setMinutes(0);
		date.setSeconds(0);
		date.setMilliseconds(0);

		for (var i=0; i<=formatParts.length; i++) {
			switch(formatParts[i]) {
				case 'dd':
				case 'd':
				case 'j':
				date.setDate(parseInt(parts[i], 10)||1);
				break;

				case 'mm':
				case 'm':
				date.setMonth((parseInt(parts[i], 10)||1) - 1);
				break;

				case 'yy':
				case 'y':
				date.setFullYear(2000 + (parseInt(parts[i], 10)||1));
				break;

				case 'yyyy':
				case 'Y':
				date.setFullYear(parseInt(parts[i], 10)||1);
				break;
			}
		}
	}

	return date;
};

Date.prototype.subDays = function(value) {
	this.setDate(this.getDate() - value);

	return this;
};

Date.prototype.subMonths = function(value) {
	var date = this.getDate();
	this.setMonth(this.getMonth() - value);

	if (this.getDate() < date) {
		this.setDate(0);
	}

	return this;
};

Date.prototype.subWeeks = function(value) {
	this.subDays(value * 7);

	return this;
};

Date.prototype.subYears = function(value) {
	var month = this.getMonth();
	this.setFullYear(this.getFullYear() - value);

	if (month < this.getMonth()) {
		this.setDate(0);
	}

	return this;
};

Date.prototype.format = function(format) {
	if (format === undefined)
		return this.toString();

	var formatSeparator = format.match(/[.\/\-\s].*?/);
	var formatParts     = format.split(/\W+/);
	var result          = '';

	for (var i=0; i<=formatParts.length; i++) {
		switch(formatParts[i]) {
			case 'd':
			case 'j':
			result += this.getDate() + formatSeparator;
			break;

			case 'dd':
			result += (this.getDate() < 10 ? '0' : '')+this.getDate() + formatSeparator;
			break;

			case 'm':
			result += (this.getMonth() + 1) + formatSeparator;
			break;

			case 'mm':
			result += (this.getMonth() < 9 ? '0' : '')+(this.getMonth() + 1) + formatSeparator;
			break;

			case 'yy':
			case 'y':
			result += this.getFullYear() + formatSeparator;
			break;

			case 'yyyy':
			case 'Y':
			result += this.getFullYear() + formatSeparator;
			break;
		}
	}

	return result.slice(0, -1);
}
