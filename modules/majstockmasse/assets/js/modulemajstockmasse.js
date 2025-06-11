function submit_form_select_category()
{
	var id_category = $('#id_category').val();
	if(id_category != undefined)
	{
	    var adress = document.location.href;
	    var tab_adresse = adress.split("&id_cat");
	    adress = tab_adresse[0];

	    document.categoriesForm.action = adress+"&id_category="+id_category;
	}
	else
	{
		document.categoriesForm.action = document.location.href;
    }
	$('#categoriesForm').submit();
}


function date_to_datepicker(date)
{
	var date_new = "";
	var date_array = date.split('/');
	date_new = date_array[2]+"-"+date_array[1]+"-"+date_array[0];
	return date_new;
}

function modifier_qte_generale()
{
    var valeur_qte_generale = $('#input_qte_generale').val();
    //$('.input_qte_declinaisons').val(valeur_qte_generale);
    $('.input_qte_declinaisons').each(function()
    {
        $(this).val(valeur_qte_generale);
    })
}



