var lot_id = 0;



function afficher_lot(numero_lot)

{

  if(document.getElementById('info_lots_product_'+numero_lot).style.display=="table-row")

  {

   document.getElementById('info_lots_product_'+numero_lot).style.display = "none";

 }

 else

 {

   document.getElementById('info_lots_product_'+numero_lot).style.display = "table-row";

 }

}

function modif_form_lot(numero, lot)

{

  var pourcent_germ = $('#pourcent_germ'+numero).val();

  var fournis = $('#fournis'+numero).val();

  var lot_org = $('#lot_org'+numero).val();

  var date_appro = $('#date_appro'+numero).val();

  var date_germ = $('#date_germ'+numero).val();

  var lot_LBG = $('#lot_LBG'+numero).val();

  var comm = $('#comm'+numero).val();

  var test = "ok";

  var quantite = $('#quantite'+numero).val();

  if ($('#graine'+numero).prop('checked')) {
    var graine_gramme = "graine";
  }

  else{
    var graine_gramme = "gramme";
  }

  $.post(document.location.href,

  {

    "numero_lot" : lot,

    "pourcent_germ": pourcent_germ,

    "fournis": fournis,

    "lot_org": lot_org,

    "date_appro": date_appro,

    "date_germ": date_germ,

    "lot_LBG": lot_LBG,

    "comm":comm,

    "test_lot": test,

    "quantite" : quantite,

    "id_product":numero,

    "graine_gramme":graine_gramme,


  }, processResultEnvoi);

}





function submit_form_lot(numero)

{

  var pourcent_germ = $('#pourcent_germ'+numero).val();

  var fournis = $('#fournis'+numero).val();

  var lot_org = $('#lot_org'+numero).val();

  var date_appro = $('#date_appro'+numero).val();

  var date_germ = $('#date_germ'+numero).val();

  var lot_LBG = $('#lot_LBG'+numero).val();

  var comm = $('#comm'+numero).val();

  var quantite = $('#quantite'+numero).val();

  var test = "ok";

  if ($('#graine'+numero).prop('checked')) {
    var graine_gramme = "graine";
  }

  else{
    var graine_gramme = "gramme";
  }



  $.post(document.location.href,

  {

    "pourcent_germ": pourcent_germ,

    "fournis": fournis,

    "lot_org": lot_org,

    "date_appro": date_appro,

    "date_germ": date_germ,

    "lot_LBG": lot_LBG,

    "comm":comm,

    "test_lot": test,

    "quantite" : quantite,

    "id_product":numero,

    "graine_gramme":graine_gramme,

  }, processResultEnvoi);

}



function processResultEnvoi(data, textStatus)

{

 document.formInventaire.submit();

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



function supprimer_lot(numero_lot)

{

 if(confirm('\312tes-vous s\373r de vouloir supprimer ce lot?'))

 {

  lot_id = numero_lot;

  var suppr_lot = "ok";

  $.post(document.location.href,

    { "suppr_lot":suppr_lot,

    "id_lot":numero_lot

  }, processResultsuppr);

  location.reload();

}

}



function processResultsuppr(data, textStatus)

{

	$("#lot_num_"+lot_id).remove();

}



function date_to_datepicker(date)

{

	var date_new = "";

	var date_array = date.split('/');

	date_new = date_array[2]+"-"+date_array[1]+"-"+date_array[0];

	return date_new;

}



function modifier_lot(numero_lot, numero_produit)

{

	$('#fournis'+numero_produit).val( $('#ref_fourn_lot_'+numero_lot).html() );

	$('#quantite'+numero_produit).val( $('#quantite_lot_'+numero_lot).html() );

	$('#date_appro'+numero_produit).val(date_to_datepicker( $('#date_appro_lot_'+numero_lot).html()) );

	$('#lot_org'+numero_produit).val( $('#num_lot_org_'+numero_lot).html() );

	$('#lot_LBG'+numero_produit).val( $('#num_lot_LBG_'+numero_lot).html() );

  $('#date_germ'+numero_produit).val( date_to_datepicker($('#date_germ_lot_'+numero_lot).html()) );

  $('#pourcent_germ'+numero_produit).val( $('#germ_lot_'+numero_lot).html().replace('%', '') );

  $('#comm'+numero_produit).val( $('#commentaire_lot_'+numero_lot).html().replace('<br>', '') );
   
   
    $('#date_germ_span'+numero_produit).html($('#date_germ_lot_'+numero_lot).html() );

  $('#pourcent_germ_span'+numero_produit).html( $('#germ_lot_'+numero_lot).html() );
  
  $('#comm_span'+numero_produit).html( $('#commentaire_lot_'+numero_lot).html() );
  /*$('#date_germ'+numero_produit).attr('disabled','disabled');
  $('#pourcent_germ'+numero_produit).attr('disabled','disabled');
  $('#comm'+numero_produit).attr('disabled','disabled');

  $('#date_germ'+numero_produit).css("border","none");
  $('#pourcent_germ'+numero_produit).css("border","none");
  $('#comm'+numero_produit).css("border","none");*/
  
  $('#date_germ'+numero_produit).hide();
  $('#pourcent_germ'+numero_produit).hide();
  $('#comm'+numero_produit).hide();
    
  $('#date_germ_span'+numero_produit).show();
  $('#pourcent_germ_span'+numero_produit).show();
  $('#comm_span'+numero_produit).show();  
    

  var graine_gramme = $('#graine_gramme_'+numero_lot).text();

  $('#'+graine_gramme+''+numero_produit).prop( "checked", true );


  $('#button_'+numero_produit).val("Modifier ce lot");

  document.getElementById('button_'+numero_produit).onclick=function () { modif_form_lot(numero_produit, numero_lot); };

  var position = $('#lot_produit'+numero_produit).offset().top;

  window.scrollTo(0,position);

}



function reset_lot_add(numero_produit)

{

	var today = new Date();

	var month = (1+today.getMonth()).toString().replace(/^(\d)$/,'0$1');

  var day = (today.getDate()).toString().replace(/^(\d)$/,'0$1');



  var date_courante = today.getFullYear()+"-"+month+"-"+day;

  $('#fournis'+numero_produit).val('');

  $('#date_appro'+numero_produit).val(date_courante) ;

  $('#lot_org'+numero_produit).val('');

  $('#lot_LBG'+numero_produit).val('' );

  $('#date_germ'+numero_produit).val(date_courante);

  $('#pourcent_germ'+numero_produit).val('');

  $('#comm'+numero_produit).val('');

  $('#button_'+numero_produit).val("Ajouter ce lot");

  $('#gramme'+numero_produit).prop( "checked", true );
  
  
  /*  $('#date_germ'+numero_produit).removeAttr('disabled');
  $('#pourcent_germ'+numero_produit).removeAttr('disabled');
  $('#comm'+numero_produit).removeAttr('disabled');

  $('#date_germ'+numero_produit).css("border","none");
  $('#pourcent_germ'+numero_produit).css("border","none");
  $('#comm'+numero_produit).css("border","none");*/
  
  
    
  $('#date_germ_span'+numero_produit).hide();
  $('#pourcent_germ_span'+numero_produit).hide();
  $('#comm_span'+numero_produit).hide();
  
  
  $('#date_germ'+numero_produit).show();
  $('#pourcent_germ'+numero_produit).show();
  $('#comm'+numero_produit).show();
  

  document.getElementById('button_'+numero_produit).onclick=function () { submit_form_lot(numero_produit); };

  //$('#button_'+numero_produit).onclick="aaa";



}



function voir_test(id_lot)

{

  $('#conteneur_lb_'+id_lot).css('display', 'flex');

  var mouse_is_inside;

  $('#conteneur_lb_'+id_lot+' .lightbox').hover(function(){

    mouse_is_inside=true;

  }, function(){

    mouse_is_inside=false;

  });

  $('.ui-datepicker').hover(function(){

    mouse_is_inside=true;

  }, function(){

    mouse_is_inside=false;

  });



  $("body").mouseup(function(){

    if(! mouse_is_inside) $('#conteneur_lb_'+id_lot).hide();

  });

}

function maj_graine_gramme(numero,valeur) 
{
       //var pourcent_germ = $('valeurgraine_gramme'+numero).val();  
}

$(document).ready(function(){


 $(".ligne_modif_ajout_lot .radio_graine_gramme").click(function(){
    // alert("test2");
    // $(this).parent(".ligne_modif_ajout_lot").children(".valeur_graine_gramme").val($(this).val());
    }
 );
  $(".icon_plus_moins").on('click', function(){

    var id = $(this).attr('id-attr');

    //$('.display_none').hide();

    $('.display_'+id).toggle();

    if ( $('#icon_plus_moins_'+id).hasClass('plus') )

    {

     $('#icon_plus_moins_'+id).html('<span class="icon-minus"></span>');

     $('#icon_plus_moins_'+id).removeClass('plus');

     $('#icon_plus_moins_'+id).addClass('moins');

   }

   else

   {

     $('#icon_plus_moins_'+id).html('<span class="icon-plus"></span>');

     $('#icon_plus_moins_'+id).removeClass('moins');

     $('#icon_plus_moins_'+id).addClass('plus');

   }

 });



  /* $(".icon_plus_moins").on('click', function(){

    var id = $(this).attr("id-attr");



    $('.display_'+id).toggle();

  }); */



  $('.ajout_test').on('click', function(){
      id_ajout_test=this.id.split("_");
    
     var id_lot =id_ajout_test[2];
     /** var id_lot = $('#id_lot').val(); *///

    var date_debut_test = $('#ligne_ajout_test_' + id_lot + ' .date_debut_test').val();

    var date_fin_test = $('#ligne_ajout_test_' + id_lot + ' .date_fin_test').val();

    var commentaire_test = $('#ligne_ajout_test_' + id_lot + ' #commentaire_test').val();
    alert(commentaire_test);

    var date_etape_1 = $('#ligne_ajout_test_' + id_lot + ' .date_etape_1').val();

    var resultat_etape_1 = $('#ligne_ajout_test_' + id_lot + ' #resultat_etape_1').val();

    var date_etape_2 = $('#ligne_ajout_test_' + id_lot + ' .date_etape_2').val();

    var resultat_etape_2 = $('#ligne_ajout_test_' + id_lot + ' #resultat_etape_2').val();

    var date_etape_3 = $('#ligne_ajout_test_' + id_lot + ' .date_etape_3').val();

    var resultat_etape_3 = $('#ligne_ajout_test_' + id_lot + ' #resultat_etape_3').val();
    
    var origine_test = $('#ligne_ajout_test_' + id_lot + ' #origine_test').val();

    alert(origine_test);
    $.ajax({

      type: "POST",

      url: '/modules/statsstocksinventaire/ajout_test.php',

      data: {id_lot: id_lot, date_debut_test: date_debut_test, date_fin_test: date_fin_test, commentaire_test: commentaire_test, date_etape_1: date_etape_1, resultat_etape_1: resultat_etape_1, date_etape_2: date_etape_2, resultat_etape_2: resultat_etape_2, date_etape_3: date_etape_3, resultat_etape_3: resultat_etape_3, origine_test: origine_test},

      success: function(echo){

        location.reload();

      },
        error: function() {
            alert('Error occured');
        }
      

    })

  })



  $('.envoi_modif').on('click', function(){

    var id = $(this).attr('id');

    var id_lot = $('#id_lot_'+id).val();

    var date_debut_test = $('#date_debut_semis_'+id).val();

    var date_fin_test = $('#date_fin_test_'+id).val();

    var commentaire = $('#commentaire_'+id).val();

    var date_etape_1 = $('#date_etape_1_'+id).val();

    var resultat_etape_1 = $('#resultat_etape_1_'+id).val();

    var date_etape_2 = $('#date_etape_2_'+id).val();

    var resultat_etape_2 = $('#resultat_etape_2_'+id).val();

    var date_etape_3 = $('#date_etape_3_'+id).val();

    var resultat_etape_3 = $('#resultat_etape_3_'+id).val();
    
    var origine_test = $('#origine_test_'+id).val();

    $.ajax({

      type: "POST",

      url: '/modules/statsstocksinventaire/modif_test.php',

      data: {id: id, id_lot: id_lot, date_debut_test: date_debut_test, date_fin_test: date_fin_test, commentaire_test: commentaire, date_etape_1: date_etape_1, resultat_etape_1: resultat_etape_1, date_etape_2: date_etape_2, resultat_etape_2: resultat_etape_2, date_etape_3: date_etape_3, resultat_etape_3: resultat_etape_3, origine_test: origine_test},

      success: function(echo){

        location.reload();

      }

    });

  });



  $('.titre_click_display').on('click', function(){

    $('.table_display').toggle();

  })



});





