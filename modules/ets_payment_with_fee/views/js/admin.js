/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
$(document).ready(function(){
    $('.ets-custom-payment-tab-general').addClass('active');
    $('.config_tab_general').addClass('active');
    $('.confi_tab').click(function(){
        $('.ets-form-group').removeClass('active');
        $('.ets-custom-payment-tab-'+$(this).data('tab-id')).addClass('active');  
        $('.confi_tab').removeClass('active');
        $(this).addClass('active');             
    });
    displayFieldCustomFrom();
    $('#fee_type').change(function(){
        displayFieldCustomFrom();
    });
    var $myPayment = $("#payment-list");
    if($myPayment.length >0)
	$myPayment.sortable({
		opacity: 0.6,
		cursor: "move",
        handle: ".dragHandle",
		update: function() {
			var order = $(this).sortable("serialize") + "&action=updatePaymentOrdering";
             $.ajax({
    			type: 'POST',
    			headers: { "cache-control": "no-cache" },
    			url: '',
    			async: true,
    			cache: false,
    			dataType : "json",
    			data:order,
    			success: function(jsonData)
    			{
		              $.growl.notice({ message: jsonData.success });
                        var i=1;
                        $('.dragGroup span').each(function(){
                        $(this).html(i+(jsonData.page-1)*20);
                        i++;
                        });
                }
    		});						
        }
   });
   $('select[multiple="multiple"] option[value="0"]').each(function(){
         var id = $(this).parent().attr('id');
        if($(this).attr('selected')=='selected')
            $('#'+id+' option').attr('selected','selected');
    });
    $(document).on('click','select[multiple="multiple"] option',function(){
        var id = $(this).parent().attr('id');
        if($(this).attr('value')=='0')
        {
            if($(this).parent().val()!='' && $(this).parent().val().indexOf('0') ==0)
            {
                $('#'+id+' option').prop('selected',true);
            }
            else
            {
                $('#'+id+' option').prop('selected',false);
            }
        }
        $(this).parent().change();
   });
});
function hideOtherCurrency(id_currency)
{
    $('.currency-field').hide();
    $('.currency-'+id_currency).show();
}
function displayFieldCustomFrom()
{
    $('.form-group.custom').hide();
    $('.form-group.form-group.'+$('#fee_type').val()).show(); 
}