$( document ).ready(function() {
    if ( $('#addressInvoice').length > 0 )
    {
        $('#addressInvoice').append('<a class="btn btn-primary pull-left" href="http://localhost/etiquettes/index.php?id='+$('.header-toolbar .text-muted').html().replace("#", "")+'" target="_blank"><i class="icon-print"></i>Imprimer l\'étiquette</a>');
    }
    if ( $('.adminorders .title-content').length > 0 )
    {
        $('.adminorders .title-content').append('<a href="/admin123/index.php/sell/orders/new?customerId='+$('#customerInfo .text-muted').html().replace("#", "")+'&orderId='+$('.header-toolbar .text-muted').html().replace("#", "")+'" style="display: inline-block;padding: .25rem .5rem;line-height: 1;text-align: center;white-space: nowrap;vertical-align: baseline;background-color: #363a41;border-radius: 5px;font-weight: bold;font-size: 16px;margin-left: 10px;">Créer SAV</a>');
    }
});

function print_bls(bls)
{
    $.ajax({
        type: 'GET',
        url: '/admin123/ajax_print_etiquettes.php',
        data: {orders: bls},
        async: false,
        success: function (msg)
        {
            var exp = msg.split("-");
            for (var i = 0; i < exp.length; i++)
            {
                if (i == 0)
                {
                    window.open('http://localhost/etiquettes/index.php?id=' + exp[i]);
                }
                else
                {
                    sleep_aw(1.5);
                    window.open('http://localhost/etiquettes/index.php?id=' + exp[i]);
                }
            }
        }
    });
    sleep_aw(2);
    location.reload(true);
}

function sleep_aw(seconds){
    var waitUntil = new Date().getTime() + seconds*1000;
    while(new Date().getTime() < waitUntil) true;
}