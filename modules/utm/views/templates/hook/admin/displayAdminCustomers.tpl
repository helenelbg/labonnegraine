<div class="col">
    <div class="card">
        <h3 class="card-header">{l s='UTM' d='Modules.utm'}</h3>  
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <th>
                            <span class="title_box">{l s='utm_source' d='Modules.utm'}</span>
                        </th>
                        <th>
                            <span class="title_box">{l s='utm_medium' d='Modules.utm'}</span>
                        </th>
                        <th>
                            <span class="title_box">{l s='utm_campaign' d='Modules.utm'}</span>
                        </th>
                        <th>
                            <span class="title_box">{l s='utm_expire' d='Modules.utm'}</span>
                        </th>
                    </thead>
                    <tbody>
                        <tr class="product-line-row">
                            <td>{$utm_source}</td>
                            <td>{$utm_medium}</td>
                            <td>{$utm_campaign}</td>
                            <td>{$utm_expire}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger" id="utmResetCustomer" value="{$idCustomer}">{l s='Supprimer les UTM' d='Admin.utm'}</button>
            </div>
        </div>
    </div>
</div>

{literal}
<script>
    $("#utmResetCustomer").on("click", function(){
        let idCustomer = $(this).val();

        if(!confirm("Voulez-vous vraiment supprimer les UTM de ce client ?"))
            return false;

        $.ajax({
            url: "/modules/utm/ajax/resetUtmCustomer.php",
            type: 'POST',
            data: 'idCustomer=' + idCustomer,
            dataType: 'json',
            timeout: 3000,
            success: function (data) {
                if(data === "OK"){
                    location.reload();
                }
            },
            error: function () {
                console.log("Probleme interne");
            }
        }); 

    });
</script>
{/literal}