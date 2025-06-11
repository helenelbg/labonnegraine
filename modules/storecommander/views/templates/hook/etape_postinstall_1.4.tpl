<link type="text/css" rel="stylesheet" href="../modules/storecommander/views/css/admin.css" />
<script type="text/javascript" src="../modules/storecommander/views/js/loader/jquery.loader-min.js"></script>
<script type="text/javascript">
{literal}
    $(document).ready(function() {
        $(".loading").click(function() {
            $.loader({
                className:"blue-with-image-2",
                content:""
            });
        });
    });
{/literal}
</script>
<fieldset>
    <img src="../modules/storecommander/views/img/logo.png"/><br/><br/>
    <p>{l s='Store Commnander is installed. You can launch the application from the Modules > Store Commander menu.' mod='storecommander'}</p><br/>
    <a href="index.php?tab=AdminStoreCommander&token={$token|escape:'htmlall':'UTF-8'}" class="sc_bouton">{l s='Go to Store Commander' mod='storecommander'}</a>
</fieldset>
<fieldset style="margin-top: 20px;">
{if isset($update_validation) && $update_validation}
    <div class="conf">
        <img src="../img/admin/ok2.png" alt=""> {l s='Successful StoreCommander update' mod='storecommander'}
    </div>
{else}
    {l s='By clicking on this button to update, you\'re accepting' mod='storecommander'} <a href="https://www.storecommander.com/files/{$url_cgu}" target="_blank" style="text-decoration: underline;">{l s='Terms & Conditions' mod='storecommander'}</a>
    <br/><br/>
    <a href="{$currentUrl|escape:'htmlall':'UTF-8'}{$baseParams|escape:'htmlall':'UTF-8'}&sc_step=3&SCupdate=1" class="sc_bouton">{l s='Force Store Commander last update' mod='storecommander'}</a>
{/if}
</fieldset>