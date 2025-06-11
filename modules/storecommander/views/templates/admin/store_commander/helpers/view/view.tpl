{$html|escape:'htmlall':'UTF-8'}
{if !empty($sc_url)}
<fieldset><legend>Store Commander</legend>
    <label>{$sc_title|escape:'htmlall':'UTF-8'}</label>
    <div class="margin-form">
        <script>
            document.location="{$sc_url}";
        </script>
    </div>
</fieldset>
{/if}