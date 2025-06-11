function afficher_details_reassort(id_product)
{
  if(document.getElementById('reassort_product_'+id_product).style.display=="table-row")
  {
     document.getElementById('reassort_product_'+id_product).style.display = "none";
  }
  else
  {
     document.getElementById('reassort_product_'+id_product).style.display = "table-row";
  }
}


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