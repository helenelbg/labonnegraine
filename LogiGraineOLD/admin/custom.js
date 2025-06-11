$( document ).ready(function() {
    $( "#tabs" ).tabs();
    $( "input" ).checkboxradio();

    $( "select" ).selectmenu({
        change: function( event, data ) {
          affectationPda($(this).val(), $(this).attr('selector'));
          /*$(this).val('');
          $(this).selectmenu("refresh");*/
        }
       });
});

function checkOrder(element, nb)
{
    if ( nb == 0 )
    {
        $('input[name^='+element+']').prop("checked",true).change();
    }
    else if ( nb == -1 )
    {
        $('input[name^='+element+']').prop("checked",false).change();
    }
    else 
    {
        $('input[name^='+element+']').prop("checked",false).change();
        var cpt_x = 0;
        $('input[name^='+element+']').each(function( index ) {
            if ( cpt_x < 20 )
            {
                $(this).prop("checked",true).change();
                cpt_x++;
            }
        });
        /*for (var i = 1; i <= nb; i++)
        {
            $('input[name='+element+i+']').prop("checked",true).change();
        }*/
    }
}

function affectationPda(pda, element)
{
    let orders = [];
    $("input[name^='checkbox-"+element+"']:checked").each(function( index ) {
        orders.push($(this).val());
    });
    if ( pda > 0 || pda == -1 )
    {
        $.ajax({
            method: "POST",
            url: "/LogiGraine/admin/ajax_affectation_pda.php?",
            data: {orders: orders, id_pda: pda},
            success :function(data) {
                location.reload();
                //console.log(data);
        }
        });
    }
}