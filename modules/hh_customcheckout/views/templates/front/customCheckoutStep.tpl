{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
    <div class="custom-checkout-step">
        <form
                method="POST"
                action="{$urls.pages.order}"
                data-refresh-url="{url entity='order' params=['ajax' => 1, 'action' => 'customStep']}"
        >
            {* ZONE LIVRAISON DIFFERENCIEE *}
            <div class="step_colis">
                {block name='step_colis'}
                {include file='checkout/_partials/steps/colis.tpl' cart=$cart}
                {/block}
            </div>

            <footer class="form-footer clearfix">
                <input type="submit" name="submitCustomStep" value="Continuer"
                       class="btn btn-primary continue float-xs-right"/>
            </footer>
        </form>
    </div>
{/block}