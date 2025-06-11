function submit_form_select_category_fiches()
{
	var id_category = jQuery('#id_category').val();
	if(id_category != undefined)
	{
	    var adress = document.location.href;
	    var tab_adresse = adress.split("&id_cat");
	    adress = tab_adresse[0];            
	    document.categoriesForm.action = adress+"&id_category="+id_category;
            //alert(document.categoriesForm.action);
	}
	else
	{
		document.categoriesForm.action = document.location.href;
        }
	jQuery('#categoriesForm').submit();
}

function check_all_product_fiches()
{
    var cases = jQuery(".check_all_product_fiches").each(function()
    {
        jQuery(this).attr('checked', true);
    });         
}

function uncheck_all_product_fiches()
{
    var cases = jQuery(".check_all_product_fiches").each(function()
    {
        jQuery(this).attr('checked', false);
    });         
}