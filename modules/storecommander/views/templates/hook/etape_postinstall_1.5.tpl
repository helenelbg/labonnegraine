<script type="text/javascript">
    $(document).ready(function() {
        $(".loading").click(function() {
            $.loader({
                className:"blue-with-image-2",
                content:""
            });
        });
        $('#force_update_title').click(function() {
            if($(this).parent('.allow_push').hasClass('open')) {
                $(this).parent('.allow_push').removeClass('open');
            } else {
                $(this).parent('.allow_push').addClass('open');
            }
        });
    });
</script>
<div id="content" class="bootstrap" style="margin: 0px; padding: 0px; width: 700px; margin-top: 20px;">
    <section class="panel widget allow_push">
        <div>
            <img src="../modules/storecommander/views/img/logo.png"/><br/><br/>
            <p>{l s='Store Commnander is installed. You can launch the application from the Modules > Store Commander menu.' mod='storecommander'}</p><br/>
            <center>
                <a href="index.php?controller=AdminStoreCommander&token={$token|escape:'htmlall':'UTF-8'}" class="sc_bouton">{l s='Go to Store Commander' mod='storecommander'}</a>
            </center>
        </div>
    </section>
</div>
<div style="clear: both;"></div>

<div id="content" class="bootstrap" style="margin: 0px; padding: 0px; width: 700px; margin-top: 20px;">
{if isset($update_validation) && $update_validation}
    <div class="alert alert-success" role="alert">
        <div class="alert-text">
            <p>{l s='Successful StoreCommander update' mod='storecommander'}</p>
        </div>
    </div>
{else}
    <section class="panel widget allow_push">
        <div class="panel-heading" id="force_update_title">{l s='Advanced settings' mod='storecommander'}</div>
        <div id="force_update_field">
            <center>
                {l s='By clicking on this button to update, you\'re accepting' mod='storecommander'} <a href="https://www.storecommander.com/files/{$url_cgu}" target="_blank" style="text-decoration: underline;">{l s='Terms & Conditions' mod='storecommander'}</a>
                <br/><br/>
                <a href="{$currentUrl|escape:'htmlall':'UTF-8'}{$baseParams|escape:'htmlall':'UTF-8'}&sc_step=3&SCupdate=1" class="sc_bouton">{l s='Force Store Commander last update' mod='storecommander'}</a>
            </center>
        </div>
    </section>
{/if}
</div>
<div style="clear: both;"></div>